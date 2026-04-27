<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div id="jeenie-chat-widget" class="jeenie-chat-widget">

    <button id="jeenie-chat-toggle" class="jeenie-chat-toggle" title="<?php esc_attr_e( 'Jeenie AI', 'jeenie' ); ?>">
        <span class="jeenie-chat-icon"><i class="fa-solid fa-robot"></i></span>
        <span class="jeenie-chat-close" style="display:none;"><i class="fa-solid fa-x"></i></span>
    </button>

    <div id="jeenie-chat-window" class="jeenie-chat-window" style="display:none;">

        <div class="jeenie-chat-header">
            <span class="jeenie-chat-header-title"><i class="fa-solid fa-robot"></i> Jeenie</span>
            <div class="jeenie-chat-header-actions">
                <button id="jeenie-history-btn" class="jeenie-header-btn" title="<?php esc_attr_e( 'Cronologia', 'jeenie' ); ?>">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </button>
                <button id="jeenie-new-chat-btn" class="jeenie-header-btn" title="<?php esc_attr_e( 'Nuova chat', 'jeenie' ); ?>">
                    <i class="fa-solid fa-plus"></i>
                </button>
            </div>
        </div>

        <div id="jeenie-history-panel" class="jeenie-history-panel" style="display:none;">
            <div class="jeenie-history-header">
                <span><?php esc_html_e( 'Cronologia', 'jeenie' ); ?></span>
                <button id="jeenie-history-back" class="jeenie-header-btn"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div id="jeenie-history-list" class="jeenie-history-list"></div>
        </div>

        <div id="jeenie-chat-main">
            <div id="jeenie-chat-messages" class="jeenie-chat-messages">
                <div class="jeenie-chat-message jeenie-chat-message--ai">
                    <?php esc_html_e( 'Ciao! Sono il tuo assistente AI. Come posso aiutarti oggi?', 'jeenie' ); ?>
                </div>
            </div>

            <div class="jeenie-chat-suggestions">
                <button class="jeenie-suggestion" data-msg="<?php esc_attr_e( 'Dammi 5 idee per articoli del blog', 'jeenie' ); ?>"><i class="fa-solid fa-lightbulb"></i> <?php esc_html_e( 'Idee articoli', 'jeenie' ); ?></button>
                <button class="jeenie-suggestion" data-msg="<?php esc_attr_e( 'Come posso migliorare la SEO del sito?', 'jeenie' ); ?>"><i class="fa-solid fa-magnifying-glass"></i> <?php esc_html_e( 'Consigli SEO', 'jeenie' ); ?></button>
                <button class="jeenie-suggestion" data-msg="<?php esc_attr_e( 'Scrivi un post breve su un argomento a mia scelta', 'jeenie' ); ?>"><i class="fa-solid fa-pen"></i> <?php esc_html_e( 'Scrivi un post', 'jeenie' ); ?></button>
            </div>

            <div class="jeenie-chat-input-wrap">
                <textarea id="jeenie-chat-input" class="jeenie-chat-textarea" placeholder="<?php esc_attr_e( 'Scrivi un messaggio...', 'jeenie' ); ?>" rows="2"></textarea>
                <button id="jeenie-chat-send" class="jeenie-btn-send" title="<?php esc_attr_e( 'Invia', 'jeenie' ); ?>">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </div>
        </div>

    </div>
</div>
