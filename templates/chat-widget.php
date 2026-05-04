<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div id="vcai-chat-widget" class="vcai-chat-widget">

    <button id="vcai-chat-toggle" class="vcai-chat-toggle" title="<?php esc_attr_e( 'VColonna AI', 'vc-colonna-ai-assistant' ); ?>">
        <span class="vcai-chat-icon"><i class="fa-solid fa-robot"></i></span>
        <span class="vcai-chat-close" style="display:none;"><i class="fa-solid fa-x"></i></span>
    </button>

    <div id="vcai-chat-window" class="vcai-chat-window" style="display:none;">

        <div class="vcai-chat-header">
            <span class="vcai-chat-header-title"><i class="fa-solid fa-robot"></i> VColonna AI</span>
            <div class="vcai-chat-header-actions">
                <button id="vcai-history-btn" class="vcai-header-btn" title="<?php esc_attr_e( 'Cronologia', 'vc-colonna-ai-assistant' ); ?>">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </button>
                <button id="vcai-new-chat-btn" class="vcai-header-btn" title="<?php esc_attr_e( 'Nuova chat', 'vc-colonna-ai-assistant' ); ?>">
                    <i class="fa-solid fa-plus"></i>
                </button>
            </div>
        </div>

        <div id="vcai-history-panel" class="vcai-history-panel" style="display:none;">
            <div class="vcai-history-header">
                <span><?php esc_html_e( 'Cronologia', 'vc-colonna-ai-assistant' ); ?></span>
                <button id="vcai-history-back" class="vcai-header-btn"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div id="vcai-history-list" class="vcai-history-list"></div>
        </div>

        <div id="vcai-chat-main">
            <div id="vcai-chat-messages" class="vcai-chat-messages">
                <div class="vcai-chat-message vcai-chat-message--ai">
                    <?php esc_html_e( 'Ciao! Sono il tuo assistente AI. Come posso aiutarti oggi?', 'vc-colonna-ai-assistant' ); ?>
                </div>
            </div>

            <div class="vcai-chat-suggestions">
                <button class="vcai-suggestion" data-msg="<?php esc_attr_e( 'Dammi 5 idee per articoli del blog', 'vc-colonna-ai-assistant' ); ?>"><i class="fa-solid fa-lightbulb"></i> <?php esc_html_e( 'Idee articoli', 'vc-colonna-ai-assistant' ); ?></button>
                <button class="vcai-suggestion" data-msg="<?php esc_attr_e( 'Come posso migliorare la SEO del sito?', 'vc-colonna-ai-assistant' ); ?>"><i class="fa-solid fa-magnifying-glass"></i> <?php esc_html_e( 'Consigli SEO', 'vc-colonna-ai-assistant' ); ?></button>
                <button class="vcai-suggestion" data-msg="<?php esc_attr_e( 'Scrivi un post breve su un argomento a mia scelta', 'vc-colonna-ai-assistant' ); ?>"><i class="fa-solid fa-pen"></i> <?php esc_html_e( 'Scrivi un post', 'vc-colonna-ai-assistant' ); ?></button>
            </div>

            <div class="vcai-chat-input-wrap">
                <textarea id="vcai-chat-input" class="vcai-chat-textarea" placeholder="<?php esc_attr_e( 'Scrivi un messaggio...', 'vc-colonna-ai-assistant' ); ?>" rows="2"></textarea>
                <button id="vcai-chat-send" class="vcai-btn-send" title="<?php esc_attr_e( 'Invia', 'vc-colonna-ai-assistant' ); ?>">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </div>
        </div>

    </div>
</div>
