jQuery(function ($) {

    const $toggle   = $('#sitegenie-chat-toggle');
    const $window   = $('#sitegenie-chat-window');
    const $messages = $('#sitegenie-chat-messages');
    const $input    = $('#sitegenie-chat-input');
    const $send     = $('#sitegenie-chat-send');
    const $main     = $('#sitegenie-chat-main');
    const $histPanel = $('#sitegenie-history-panel');
    const $histList  = $('#sitegenie-history-list');

    const STORAGE_KEY_MESSAGES = 'sitegenie_messages';
    const STORAGE_KEY_SESSION  = 'sitegenie_session';
    const STORAGE_KEY_CONV_ID  = 'sitegenie_conv_id';

    let currentConversationId = 0;

    // Session check
    var savedSession = sessionStorage.getItem(STORAGE_KEY_SESSION);
    if (savedSession && savedSession !== sitegenie_chat.session_id) {
        sessionStorage.removeItem(STORAGE_KEY_MESSAGES);
        sessionStorage.removeItem(STORAGE_KEY_CONV_ID);
    }
    sessionStorage.setItem(STORAGE_KEY_SESSION, sitegenie_chat.session_id);

    const toolLabels = {
        create_post:            '✅ Post creato',
        update_post:            '✏️ Post aggiornato',
        delete_post:            '🗑️ Post eliminato',
        get_posts:              '📋 Post recuperati',
        get_media:              '🖼️ Media recuperati',
        get_categories:         '🗂️ Categorie recuperate',
        get_site_info:          '🌐 Info sito recuperate',
        get_custom_post_types:  '📦 CPT recuperati',
        create_custom_post:     '✅ CPT creato',
        update_custom_post:     '✏️ CPT aggiornato',
    };

    restoreSession();

    // ── Toggle ────────────────────────────────────────────────────
    $toggle.on('click', function () {
        const isOpen = $window.is(':visible');
        $window.toggle(!isOpen);
        $('.sitegenie-chat-icon').toggle(isOpen);
        $('.sitegenie-chat-close').toggle(!isOpen);
        if (!isOpen) { $input.focus(); scrollToBottom(); }
    });

    // ── Suggerimenti ──────────────────────────────────────────────
    $(document).on('click', '.sitegenie-suggestion', function () {
        $input.val($(this).data('msg'));
        sendMessage();
    });

    // ── Invio ─────────────────────────────────────────────────────
    $send.on('click', sendMessage);
    $input.on('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
    });

    // ── Nuova chat ────────────────────────────────────────────────
    $('#sitegenie-new-chat-btn').on('click', function () {
        currentConversationId = 0;
        sessionStorage.removeItem(STORAGE_KEY_MESSAGES);
        sessionStorage.removeItem(STORAGE_KEY_CONV_ID);
        $messages.empty();
        appendMessage('Ciao! Sono il tuo assistente AI. Come posso aiutarti oggi?', 'ai');
        $('.sitegenie-chat-suggestions').show();
        $histPanel.hide();
        $main.show();
    });

    // ── Cronologia ────────────────────────────────────────────────
    $('#sitegenie-history-btn').on('click', function () {
        $main.hide();
        $histPanel.show();
        loadConversations();
    });

    $('#sitegenie-history-back').on('click', function () {
        $histPanel.hide();
        $main.show();
    });

    // Click su conversazione
    $(document).on('click', '.sitegenie-conv-item', function () {
        var convId = $(this).data('id');
        loadConversation(convId);
    });

    // Elimina conversazione
    $(document).on('click', '.sitegenie-conv-delete', function (e) {
        e.stopPropagation();
        var convId = $(this).closest('.sitegenie-conv-item').data('id');
        if (!confirm('Eliminare questa conversazione?')) return;

        $.post(sitegenie_chat.ajax_url, {
            action: 'sitegenie_delete_conversation',
            nonce: sitegenie_chat.nonce,
            conversation_id: convId,
        }).done(function () {
            if (currentConversationId === convId) {
                $('#sitegenie-new-chat-btn').click();
            }
            loadConversations();
        });
    });

    function loadConversations() {
        $histList.html('<div class="text-center p-3 text-muted small"><i class="fa-solid fa-spinner fa-spin"></i></div>');
        $.post(sitegenie_chat.ajax_url, {
            action: 'sitegenie_get_conversations',
            nonce: sitegenie_chat.nonce,
        }).done(function (res) {
            if (!res.success || !res.data.length) {
                $histList.html('<div class="text-center p-3 text-muted small">Nessuna conversazione.</div>');
                return;
            }
            var html = '';
            res.data.forEach(function (c) {
                var active = (c.id == currentConversationId) ? ' sitegenie-conv-active' : '';
                html += '<div class="sitegenie-conv-item' + active + '" data-id="' + c.id + '">'
                    + '<div class="sitegenie-conv-title">' + escHtml(c.title) + '</div>'
                    + '<div class="d-flex justify-content-between align-items-center">'
                    + '<small class="text-muted">' + c.message_count + ' msg</small>'
                    + '<button class="sitegenie-conv-delete btn btn-sm p-0 border-0 text-muted" title="Elimina"><i class="fa-solid fa-trash-can fa-xs"></i></button>'
                    + '</div></div>';
            });
            $histList.html(html);
        });
    }

    function loadConversation(convId) {
        $.post(sitegenie_chat.ajax_url, {
            action: 'sitegenie_load_conversation',
            nonce: sitegenie_chat.nonce,
            conversation_id: convId,
        }).done(function (res) {
            if (!res.success) return;

            currentConversationId = convId;
            $messages.empty();
            $('.sitegenie-chat-suggestions').hide();

            res.data.messages.forEach(function (m) {
                appendMessage(m.content, m.role === 'user' ? 'user' : 'ai');
            });

            sessionStorage.setItem(STORAGE_KEY_CONV_ID, convId);
            saveVisibleMessages();

            $histPanel.hide();
            $main.show();
            scrollToBottom();
        });
    }

    // ── Invio messaggio ───────────────────────────────────────────
    function sendMessage() {
        var msg = $input.val().trim();
        if (!msg) return;

        $('.sitegenie-chat-suggestions').hide();
        appendMessage(msg, 'user');
        $input.val('').prop('disabled', true);
        $send.prop('disabled', true);

        var $loading = appendMessage('⏳ Elaborazione in corso...', 'loading');

        $.post(sitegenie_chat.ajax_url, {
            action: 'sitegenie_chat',
            nonce: sitegenie_chat.nonce,
            message: msg,
            conversation_id: currentConversationId,
        })
        .done(function (res) {
            $loading.remove();
            if (res.success) {
                var data = res.data;
                currentConversationId = data.conversation_id || currentConversationId;

                if (data.action_taken && data.action_taken.tool) {
                    var label = toolLabels[data.action_taken.tool] || '⚡ Azione eseguita';
                    var result = data.action_taken.result || {};
                    var extraHtml = '';
                    if ((data.action_taken.tool === 'create_post' || data.action_taken.tool === 'create_custom_post') && result.edit_url) {
                        extraHtml = ' — <a href="' + result.edit_url + '" target="_blank">Apri nell\'editor</a>';
                    }
                    appendBadge(label + extraHtml);
                }

                appendMessage(data.text, 'ai');
                saveSession();
            } else {
                appendMessage('⚠️ ' + res.data, 'ai');
            }
        })
        .fail(function () {
            $loading.remove();
            appendMessage('❌ Errore di connessione.', 'ai');
        })
        .always(function () {
            $input.prop('disabled', false);
            $send.prop('disabled', false);
            $input.focus();
        });
    }

    // ── Helpers ───────────────────────────────────────────────────
    function appendMessage(text, type) {
        var $msg = $('<div>').addClass('sitegenie-chat-message sitegenie-chat-message--' + type).text(text);
        $messages.append($msg);
        scrollToBottom();
        return $msg;
    }

    function appendBadge(html) {
        var $badge = $('<div class="sitegenie-action-badge">').html(html);
        $messages.append($badge);
        scrollToBottom();
    }

    function scrollToBottom() {
        $messages.scrollTop($messages[0].scrollHeight);
    }

    function escHtml(str) {
        return $('<span>').text(str).html();
    }

    // ── Persistenza sessionStorage ────────────────────────────────
    function saveSession() {
        try {
            sessionStorage.setItem(STORAGE_KEY_CONV_ID, currentConversationId);
            saveVisibleMessages();
        } catch (e) {}
    }

    function saveVisibleMessages() {
        var ordered = [];
        $messages.children().each(function () {
            var $el = $(this);
            if ($el.hasClass('sitegenie-chat-message--user'))     ordered.push({ type: 'user', text: $el.text() });
            else if ($el.hasClass('sitegenie-chat-message--ai'))  ordered.push({ type: 'ai', text: $el.text() });
            else if ($el.hasClass('sitegenie-action-badge'))      ordered.push({ type: 'badge', html: $el.html() });
        });
        sessionStorage.setItem(STORAGE_KEY_MESSAGES, JSON.stringify(ordered));
    }

    function restoreSession() {
        try {
            var msgs   = sessionStorage.getItem(STORAGE_KEY_MESSAGES);
            var convId = sessionStorage.getItem(STORAGE_KEY_CONV_ID);
            if (!msgs) return;

            currentConversationId = parseInt(convId) || 0;
            var parsed = JSON.parse(msgs);
            if (!parsed.length) return;

            $messages.empty();
            $('.sitegenie-chat-suggestions').hide();
            parsed.forEach(function (m) {
                if (m.type === 'badge') appendBadge(m.html);
                else appendMessage(m.text, m.type);
            });
        } catch (e) {}
    }

});
