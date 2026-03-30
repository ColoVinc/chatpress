jQuery(function ($) {

    const $widget   = $('#chatpress-chat-widget');
    const $toggle   = $('#chatpress-chat-toggle');
    const $window   = $('#chatpress-chat-window');
    const $messages = $('#chatpress-chat-messages');
    const $input    = $('#chatpress-chat-input');
    const $send     = $('#chatpress-chat-send');

    // Toggle apertura/chiusura
    $toggle.on('click', function () {
        const isOpen = $window.is(':visible');
        $window.toggle(!isOpen);
        $('.chatpress-chat-icon').toggle(isOpen);
        $('.chatpress-chat-close').toggle(!isOpen);
        if (!isOpen) $input.focus();
    });

    // Suggerimenti rapidi
    $(document).on('click', '.chatpress-suggestion', function () {
        $input.val($(this).data('msg'));
        sendMessage();
    });

    // Invio con bottone
    $send.on('click', sendMessage);

    // Invio con Enter (Shift+Enter = nuova riga)
    $input.on('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    function sendMessage() {
        const msg = $input.val().trim();
        if (!msg) return;

        appendMessage(msg, 'user');
        $input.val('');

        const $loading = appendMessage('⏳ Sto elaborando...', 'loading');

        $.post(chatpress_chat.ajax_url, {
            action:  'chatpress_chat',
            nonce:   chatpress_chat.nonce,
            message: msg,
        })
        .done(function (res) {
            $loading.remove();
            if (res.success) {
                appendMessage(res.data.text, 'ai');
            } else {
                appendMessage('❌ ' + res.data, 'ai');
            }
        })
        .fail(function () {
            $loading.remove();
            appendMessage('❌ Errore di connessione.', 'ai');
        });
    }

    function appendMessage(text, type) {
        const $msg = $('<div>')
            .addClass('chatpress-chat-message chatpress-chat-message--' + type)
            .text(text);
        $messages.append($msg);
        $messages.scrollTop($messages[0].scrollHeight);
        return $msg;
    }

});