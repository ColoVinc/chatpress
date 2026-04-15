<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * SiteGenie_History — gestisce conversazioni e messaggi nel DB
 */
class SiteGenie_History {

    /**
     * Crea una nuova conversazione e restituisce l'ID
     */
    public static function create_conversation( int $user_id, string $first_message ): int {
        global $wpdb;
        $title = mb_strimwidth( $first_message, 0, 100, '...' );
        $now   = current_time( 'mysql' );

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- custom table
        $wpdb->insert(
            $wpdb->prefix . 'sitegenie_conversations',
            [ 'user_id' => $user_id, 'title' => $title, 'created_at' => $now, 'updated_at' => $now ],
            [ '%d', '%s', '%s', '%s' ]
        );

        return (int) $wpdb->insert_id;
    }

    /**
     * Salva un messaggio in una conversazione
     */
    public static function save_message( int $conversation_id, string $role, string $content ): void {
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- custom table
        $wpdb->insert(
            $wpdb->prefix . 'sitegenie_messages',
            [ 'conversation_id' => $conversation_id, 'role' => $role, 'content' => $content, 'created_at' => current_time( 'mysql' ) ],
            [ '%d', '%s', '%s', '%s' ]
        );
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- custom table, write operation
        $wpdb->update(
            $wpdb->prefix . 'sitegenie_conversations',
            [ 'updated_at' => current_time( 'mysql' ) ],
            [ 'id' => $conversation_id ],
            [ '%s' ],
            [ '%d' ]
        );
    }

    /**
     * Lista conversazioni di un utente (più recenti prima)
     */
    public static function get_conversations( int $user_id, int $limit = 50 ): array {
        global $wpdb;

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- custom table, dynamic data
        return $wpdb->get_results( $wpdb->prepare(
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table names
            "SELECT c.*, COUNT(m.id) as message_count
             FROM {$wpdb->prefix}sitegenie_conversations c
             LEFT JOIN {$wpdb->prefix}sitegenie_messages m ON m.conversation_id = c.id
             WHERE c.user_id = %d
             GROUP BY c.id
             ORDER BY c.updated_at DESC
             LIMIT %d",
            $user_id,
            $limit
        ), ARRAY_A );
    }

    /**
     * Messaggi di una conversazione
     */
    public static function get_messages( int $conversation_id, int $user_id ): array {
        global $wpdb;

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- custom table
        $owner = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM {$wpdb->prefix}sitegenie_conversations WHERE id = %d", $conversation_id ) );
        if ( (int) $owner !== $user_id ) return [];

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- custom table
        return $wpdb->get_results( $wpdb->prepare(
            "SELECT role, content, created_at FROM {$wpdb->prefix}sitegenie_messages WHERE conversation_id = %d ORDER BY created_at ASC",
            $conversation_id
        ), ARRAY_A );
    }

    /**
     * Elimina una conversazione e i suoi messaggi
     */
    public static function delete_conversation( int $conversation_id, int $user_id ): bool {
        global $wpdb;

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- custom table
        $owner = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM {$wpdb->prefix}sitegenie_conversations WHERE id = %d", $conversation_id ) );
        if ( (int) $owner !== $user_id ) return false;

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- custom table, delete operation
        $wpdb->delete( $wpdb->prefix . 'sitegenie_messages', [ 'conversation_id' => $conversation_id ], [ '%d' ] );
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- custom table, delete operation
        $wpdb->delete( $wpdb->prefix . 'sitegenie_conversations', [ 'id' => $conversation_id ], [ '%d' ] );
        return true;
    }

    /**
     * Elimina conversazioni più vecchie di X giorni (per tutti gli utenti)
     */
    public static function delete_older_than( int $days ): int {
        global $wpdb;

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- custom table
        $ids = $wpdb->get_col( $wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}sitegenie_conversations WHERE updated_at < DATE_SUB(NOW(), INTERVAL %d DAY)",
            $days
        ) );

        if ( empty( $ids ) ) return 0;

        $ids = array_map( 'absint', $ids );

        foreach ( $ids as $id ) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- custom table, delete operation
            $wpdb->delete( $wpdb->prefix . 'sitegenie_messages', [ 'conversation_id' => $id ], [ '%d' ] );
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- custom table, delete operation
            $wpdb->delete( $wpdb->prefix . 'sitegenie_conversations', [ 'id' => $id ], [ '%d' ] );
        }

        return count( $ids );
    }
}
