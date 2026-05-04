<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap vcai-settings">

    <div class="vcai-header rounded-3 mb-4 d-flex align-items-center gap-3 p-4">
        <h1 class="text-white m-0 fs-4"><i class="fa-solid fa-puzzle-piece"></i> <?php esc_html_e( 'VColonna AI — Componenti', 'vcolonna-ai-assistant' ); ?></h1>
    </div>

    <div class="alert alert-info small mb-4" style="border-left:4px solid #0f3460;background:#f0f4ff;padding:12px 16px;border-radius:4px;">
        <i class="fa-solid fa-circle-info"></i>
        <?php esc_html_e( 'Per la generazione dei componenti si consiglia di utilizzare i modelli di Google Gemini, Anthropic Claude o OpenAI per ottenere risultati migliori. I modelli gratuiti (Groq) potrebbero generare componenti incompleti o con errori.', 'vcolonna-ai-assistant' ); ?>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-muted small m-0"><?php esc_html_e( 'Componenti generati dall\'AI per il tuo page builder. Chiedi nella chat di creare un componente.', 'vcolonna-ai-assistant' ); ?></p>
        <?php if ( ! empty( $components ) ) : ?>
            <button type="button" id="vcai-deactivate-all" class="btn btn-outline-danger btn-sm">
                <i class="fa-solid fa-power-off"></i> <?php esc_html_e( 'Disattiva Tutti', 'vcolonna-ai-assistant' ); ?>
            </button>
        <?php endif; ?>
    </div>

    <?php if ( empty( $components ) ) : ?>
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fa-solid fa-puzzle-piece" style="font-size:48px;color:#ccc;"></i>
                <p class="mt-3 text-muted"><?php esc_html_e( 'Nessun componente creato. Chiedi all\'AI nella chat di creare un componente per il tuo editor.', 'vcolonna-ai-assistant' ); ?></p>
                <p class="text-muted small"><?php esc_html_e( 'Esempio: "Crea un componente hero con titolo e immagine di sfondo per WPBakery"', 'vcolonna-ai-assistant' ); ?></p>
            </div>
        </div>
    <?php else : ?>
        <div class="card p-0">
            <div class="card-body p-0">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th><?php esc_html_e( 'Componente', 'vcolonna-ai-assistant' ); ?></th>
                            <th><?php esc_html_e( 'Editor', 'vcolonna-ai-assistant' ); ?></th>
                            <th><?php esc_html_e( 'Stato', 'vcolonna-ai-assistant' ); ?></th>
                            <th><?php esc_html_e( 'Data', 'vcolonna-ai-assistant' ); ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $components as $vcai_comp ) : ?>
                            <tr data-slug="<?php echo esc_attr( $vcai_comp['slug'] ); ?>">
                                <td>
                                    <strong><?php echo esc_html( $vcai_comp['name'] ); ?></strong>
                                    <br><code class="small"><?php echo esc_html( $vcai_comp['slug'] ); ?></code>
                                </td>
                                <td>
                                    <?php
                                    $vcai_editor_colors = [ 'wpbakery' => '#0073aa', 'elementor' => '#92003B', 'gutenberg' => '#000', 'divi' => '#7c3aed' ];
                                    $vcai_color = $vcai_editor_colors[ $vcai_comp['editor'] ] ?? '#666';
                                    ?>
                                    <span class="badge" style="background:<?php echo esc_attr( $vcai_color ); ?>;"><?php echo esc_html( ucfirst( $vcai_comp['editor'] ) ); ?></span>
                                </td>
                                <td>
                                    <?php if ( $vcai_comp['status'] === 'active' ) : ?>
                                        <span class="badge bg-success"><i class="fa-solid fa-check"></i> <?php esc_html_e( 'Attivo', 'vcolonna-ai-assistant' ); ?></span>
                                    <?php elseif ( $vcai_comp['status'] === 'error' ) : ?>
                                        <span class="badge bg-danger vcai-comp-error" style="cursor:pointer;" data-error="<?php echo esc_attr( $vcai_comp['error_message'] ); ?>"><i class="fa-solid fa-xmark"></i> <?php esc_html_e( 'Errore', 'vcolonna-ai-assistant' ); ?></span>
                                    <?php else : ?>
                                        <span class="badge bg-secondary"><i class="fa-solid fa-pause"></i> <?php esc_html_e( 'Disattivato', 'vcolonna-ai-assistant' ); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo esc_html( $vcai_comp['created_at'] ); ?></td>
                                <td>
                                    <?php if ( $vcai_comp['status'] === 'active' ) : ?>
                                        <button class="btn btn-outline-secondary btn-sm vcai-comp-toggle" data-slug="<?php echo esc_attr( $vcai_comp['slug'] ); ?>" data-status="inactive">
                                            <i class="fa-solid fa-pause"></i>
                                        </button>
                                    <?php else : ?>
                                        <button class="btn btn-outline-success btn-sm vcai-comp-toggle" data-slug="<?php echo esc_attr( $vcai_comp['slug'] ); ?>" data-status="active">
                                            <i class="fa-solid fa-play"></i>
                                        </button>
                                    <?php endif; ?>
                                    <button class="btn btn-outline-danger btn-sm vcai-comp-delete" data-slug="<?php echo esc_attr( $vcai_comp['slug'] ); ?>">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <p class="text-muted small mt-3">
            <i class="fa-solid fa-circle-info"></i>
            <?php esc_html_e( 'URL di emergenza per disattivare tutti i componenti:', 'vcolonna-ai-assistant' ); ?>
            <code><?php echo esc_url( home_url( '/?vcai_safe_mode=1' ) ); ?></code>
        </p>
    <?php endif; ?>
</div>
