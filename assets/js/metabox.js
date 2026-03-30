jQuery(function ($) {

    // Gestione tab
    $('.chatpress-tab').on('click', function () {
        const tab = $(this).data('tab');
        $('.chatpress-tab').removeClass('active');
        $('.chatpress-tab-content').removeClass('active');
        $(this).addClass('active');
        $('#chatpress-tab-' + tab).addClass('active');
    });

    function showLoading()  { $('#chatpress-loading').show(); $('#chatpress-error').hide(); }
    function hideLoading()  { $('#chatpress-loading').hide(); }
    function showError(msg) { $('#chatpress-error').text(msg).show(); }

    // GENERA CONTENUTO
    $('#chatpress-generate-content').on('click', function () {
        const title    = $('#title').val() || $('input[name="post_title"]').val() || '';
        const keywords = $('#chatpress-keywords').val();

        if (!title) { showError('Inserisci prima il titolo del post.'); return; }

        showLoading();
        $('#chatpress-content-result').hide();

        $.post(chatpress.ajax_url, {
            action:   'chatpress_generate_content',
            nonce:    chatpress.nonce,
            title:    title,
            keywords: keywords,
            type:     $('#post_type').val() || 'post',
        })
        .done(function (res) {
            hideLoading();
            if (res.success) {
                $('#chatpress-content-result .chatpress-result-text').text(res.data.text);
                $('#chatpress-content-result').show();
            } else {
                showError(res.data);
            }
        })
        .fail(function () { hideLoading(); showError('Errore di connessione.'); });
    });

    // INSERISCI CONTENUTO NELL'EDITOR
    $(document).on('click', '.chatpress-insert-content', function () {
        const text = $('#chatpress-content-result .chatpress-result-text').text();
        if (!text) return;

        // Compatibile sia con editor classico che Gutenberg
        if (typeof wp !== 'undefined' && wp.data && wp.data.dispatch) {
            // Gutenberg
            const blocks = wp.blocks.rawHandler({ HTML: '<p>' + text.replace(/\n/g, '</p><p>') + '</p>' });
            wp.data.dispatch('core/block-editor').insertBlocks(blocks);
        } else if (typeof tinyMCE !== 'undefined' && tinyMCE.activeEditor) {
            // Editor classico
            tinyMCE.activeEditor.execCommand('mceInsertContent', false, text.replace(/\n/g, '<br>'));
        }
    });

    // GENERA SEO
    $('#chatpress-generate-seo').on('click', function () {
        const title   = $('#title').val() || '';
        const content = typeof wp !== 'undefined' && wp.data
            ? (wp.data.select('core/block-editor').getBlocks().map(b => b.attributes.content || '').join(' '))
            : (tinyMCE && tinyMCE.activeEditor ? tinyMCE.activeEditor.getContent({ format: 'text' }) : '');

        showLoading();
        $('#chatpress-seo-result').hide();

        $.post(chatpress.ajax_url, {
            action:  'chatpress_generate_seo',
            nonce:   chatpress.nonce,
            title:   title,
            content: content.substring(0, 1000),
        })
        .done(function (res) {
            hideLoading();
            if (res.success) {
                const d = res.data;
                $('#chatpress-meta-title').val(d.meta_title || '');
                $('#chatpress-meta-description').val(d.meta_description || '');
                $('#chatpress-excerpt').val(d.excerpt || '');
                updateCharCount('#chatpress-meta-title', 60);
                updateCharCount('#chatpress-meta-description', 155);
                $('#chatpress-seo-result').show();
            } else {
                showError(res.data);
            }
        })
        .fail(function () { hideLoading(); showError('Errore di connessione.'); });
    });

    // Contatore caratteri SEO
    function updateCharCount(selector, max) {
        const $el = $(selector);
        const len = $el.val().length;
        const $count = $el.closest('.chatpress-seo-field').find('.chatpress-char-count');
        const color = len > max ? '#d63638' : (len > max * 0.85 ? '#dba617' : '#00a32a');
        $count.text(len + '/' + max + ' caratteri').css('color', color);
    }

    $('#chatpress-meta-title').on('input', function () { updateCharCount('#chatpress-meta-title', 60); });
    $('#chatpress-meta-description').on('input', function () { updateCharCount('#chatpress-meta-description', 155); });

    // Inserisci excerpt
    $(document).on('click', '.chatpress-insert-excerpt', function () {
        const text = $('#chatpress-excerpt').val();
        if (text && $('#excerpt').length) {
            $('#excerpt').val(text);
        }
    });

});