<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * SiteGenie_Knowledge — gestisce la knowledge base (documenti + chunking + ricerca)
 */
class SiteGenie_Knowledge {

    /**
     * Salva un documento: lo spezza in chunk e li inserisce nel DB.
     */
    public static function add_document( string $name, string $content ): int {
        global $wpdb;
        $table = $wpdb->prefix . 'sitegenie_knowledge';

        // Rimuovi eventuale documento con lo stesso nome
        self::delete_document( $name );

        // Chunk per paragrafi (~500 caratteri ciascuno)
        $chunks = self::chunk_text( $content, 500 );
        $now    = current_time( 'mysql' );

        foreach ( $chunks as $i => $chunk ) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- custom table
            $wpdb->insert( $table, [
                'doc_name'    => sanitize_text_field( $name ),
                'chunk_index' => $i,
                'content'     => sanitize_textarea_field( $chunk ),
                'created_at'  => $now,
            ], [ '%s', '%d', '%s', '%s' ] );
        }

        return count( $chunks );
    }

    /**
     * Elimina tutti i chunk di un documento.
     */
    public static function delete_document( string $name ): void {
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- custom table
        $wpdb->delete( $wpdb->prefix . 'sitegenie_knowledge', [ 'doc_name' => $name ], [ '%s' ] );
    }

    /**
     * Lista documenti (raggruppati per nome).
     */
    public static function get_documents(): array {
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- custom table
        return $wpdb->get_results(
            "SELECT doc_name, COUNT(*) as chunks, MIN(created_at) as created_at
             FROM {$wpdb->prefix}sitegenie_knowledge
             GROUP BY doc_name
             ORDER BY created_at DESC",
            ARRAY_A
        );
    }

    /**
     * Cerca nei chunk usando FULLTEXT (con fallback LIKE).
     * Restituisce i chunk più rilevanti fino al limite di caratteri.
     */
    public static function search( string $query, int $max_chars = 1500 ): string {
        global $wpdb;
        $table = $wpdb->prefix . 'sitegenie_knowledge';

        // Prova FULLTEXT
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- custom table
        $results = $wpdb->get_results( $wpdb->prepare(
            "SELECT content, MATCH(content) AGAINST(%s IN NATURAL LANGUAGE MODE) AS score
             FROM {$table}
             WHERE MATCH(content) AGAINST(%s IN NATURAL LANGUAGE MODE)
             ORDER BY score DESC
             LIMIT 5",
            $query, $query
        ), ARRAY_A );

        // Fallback LIKE se FULLTEXT non trova nulla
        if ( empty( $results ) ) {
            $words = array_filter( explode( ' ', $query ) );
            if ( empty( $words ) ) return '';

            $like_clauses = [];
            $like_values  = [];
            foreach ( array_slice( $words, 0, 5 ) as $word ) {
                $like_clauses[] = 'content LIKE %s';
                $like_values[]  = '%' . $wpdb->esc_like( $word ) . '%';
            }

            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared -- custom table, dynamic WHERE
            $results = $wpdb->get_results( $wpdb->prepare(
                "SELECT content FROM {$table} WHERE " . implode( ' OR ', $like_clauses ) . " LIMIT 5",
                ...$like_values
            ), ARRAY_A );
        }

        if ( empty( $results ) ) return '';

        // Assembla fino al limite di caratteri
        $output = '';
        foreach ( $results as $row ) {
            if ( mb_strlen( $output ) + mb_strlen( $row['content'] ) > $max_chars ) break;
            $output .= $row['content'] . "\n\n";
        }

        return trim( $output );
    }

    /**
     * Indicizza un singolo post nella knowledge base.
     */
    public static function index_post( int $post_id ): void {
        $post = get_post( $post_id );
        $name = 'post:' . mb_strimwidth( get_the_title( $post_id ), 0, 15, '...' );

        if ( ! $post || $post->post_status !== 'publish' ) {
            self::delete_document( $name );
            return;
        }

        $text = wp_strip_all_tags( $post->post_content );
        if ( mb_strlen( $text ) < 50 ) return;

        $content = "Titolo: " . $post->post_title . "\n\n" . $text;
        self::add_document( $name, $content );
    }

    /**
     * Indicizza tutti i post pubblicati. Restituisce il numero di post indicizzati.
     */
    public static function index_all_posts(): int {
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- custom table
        $wpdb->query( "DELETE FROM {$wpdb->prefix}sitegenie_knowledge WHERE doc_name LIKE 'post:%'" );

        $posts = get_posts( [
            'post_type'      => array_merge( [ 'post', 'page' ], array_keys( get_post_types( [ '_builtin' => false, 'public' => true ] ) ) ),
            'post_status'    => 'publish',
            'posts_per_page' => -1,
        ] );

        $count = 0;
        foreach ( $posts as $post ) {
            $text = wp_strip_all_tags( $post->post_content );
            if ( mb_strlen( $text ) < 50 ) continue;

            $content = "Titolo: " . $post->post_title . "\n\n" . $text;
            self::add_document( 'post:' . mb_strimwidth( $post->post_title, 0, 15, '...' ), $content );
            $count++;
        }

        return $count;
    }

    /**
     * Conta i post attualmente indicizzati.
     */
    public static function count_indexed_posts(): int {
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- custom table
        return (int) $wpdb->get_var(
            "SELECT COUNT(DISTINCT doc_name) FROM {$wpdb->prefix}sitegenie_knowledge WHERE doc_name LIKE 'post:%'"
        );
    }

    /**
     * Spezza il testo in chunk per paragrafi, rispettando un limite di caratteri.
     */
    private static function chunk_text( string $text, int $max_len = 500 ): array {
        $paragraphs = preg_split( '/\n{2,}/', trim( $text ) );
        $chunks     = [];
        $buffer     = '';

        foreach ( $paragraphs as $para ) {
            $para = trim( $para );
            if ( $para === '' ) continue;

            if ( mb_strlen( $buffer ) + mb_strlen( $para ) + 2 > $max_len && $buffer !== '' ) {
                $chunks[] = trim( $buffer );
                $buffer   = '';
            }
            $buffer .= $para . "\n\n";
        }

        if ( trim( $buffer ) !== '' ) {
            $chunks[] = trim( $buffer );
        }

        // Se nessun paragrafo trovato, chunk per lunghezza fissa
        if ( empty( $chunks ) && $text !== '' ) {
            $chunks = str_split( $text, $max_len );
        }

        return $chunks;
    }
}
