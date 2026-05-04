<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div class="vcai-metabox">

    <div class="vcai-mb-tabs">
        <button type="button" class="vcai-tab active" data-tab="content">✍️ <?php esc_html_e( 'Contenuto', 'vc-colonna-ai-assistant' ); ?></button>
        <button type="button" class="vcai-tab" data-tab="seo">🔍 <?php esc_html_e( 'SEO', 'vc-colonna-ai-assistant' ); ?></button>
    </div>

    <!-- TAB CONTENUTO -->
    <div class="vcai-tab-content active" id="vcai-tab-content">
        <div class="vcai-mb-field">
            <label><?php esc_html_e( 'Keywords (opzionale)', 'vc-colonna-ai-assistant' ); ?></label>
            <input type="text" id="vcai-keywords" placeholder="<?php esc_attr_e( 'es. scarpe running, sport...', 'vc-colonna-ai-assistant' ); ?>" />
        </div>
        <button type="button" id="vcai-generate-content" class="button button-primary vcai-btn-full">
            ✨ <?php esc_html_e( 'Genera Bozza Articolo', 'vc-colonna-ai-assistant' ); ?>
        </button>
        <div id="vcai-content-result" class="vcai-result" style="display:none;">
            <div class="vcai-result-text"></div>
            <button type="button" class="button vcai-insert-content">⬆️ <?php esc_html_e( 'Inserisci nell\'editor', 'vc-colonna-ai-assistant' ); ?></button>
            <button type="button" class="button vcai-copy-content">📋 <?php esc_html_e( 'Copia testo', 'vc-colonna-ai-assistant' ); ?></button>
        </div>
    </div>

    <!-- TAB SEO -->
    <div class="vcai-tab-content" id="vcai-tab-seo">
        <p class="description"><?php esc_html_e( 'Genera meta title, description ed excerpt basati sul contenuto del post.', 'vc-colonna-ai-assistant' ); ?></p>
        <button type="button" id="vcai-generate-seo" class="button button-primary vcai-btn-full">
            🔍 <?php esc_html_e( 'Genera Meta SEO', 'vc-colonna-ai-assistant' ); ?>
        </button>
        <div id="vcai-seo-result" class="vcai-result" style="display:none;">
            <div class="vcai-seo-field">
                <label><strong>Meta Title</strong> <span class="vcai-char-count"></span></label>
                <input type="text" id="vcai-meta-title" class="widefat" />
            </div>
            <div class="vcai-seo-field">
                <label><strong>Meta Description</strong> <span class="vcai-char-count"></span></label>
                <textarea id="vcai-meta-description" class="widefat" rows="3"></textarea>
            </div>
            <div class="vcai-seo-field">
                <label><strong>Excerpt</strong></label>
                <textarea id="vcai-excerpt" class="widefat" rows="2"></textarea>
                <button type="button" class="button vcai-insert-excerpt">⬆️ <?php esc_html_e( 'Inserisci Excerpt', 'vc-colonna-ai-assistant' ); ?></button>
            </div>
        </div>
    </div>

    <div id="vcai-loading" class="vcai-loading" style="display:none;">
        <span class="spinner is-active"></span> <?php esc_html_e( 'L\'AI sta elaborando...', 'vc-colonna-ai-assistant' ); ?>
    </div>

    <div id="vcai-error" class="vcai-error notice notice-error" style="display:none;"></div>

</div>
