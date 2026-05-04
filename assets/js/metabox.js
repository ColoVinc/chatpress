jQuery(function ($) {

    // Gestione tab
    $('.vcai-tab').on('click', function () {
        const tab = $(this).data('tab');
        $('.vcai-tab').removeClass('active');
        $('.vcai-tab-content').removeClass('active');
        $(this).addClass('active');
        $('#vcai-tab-' + tab).addClass('active');
    });

    function showLoading()  { $('#vcai-loading').show(); $('#vcai-error').hide(); }
    function hideLoading()  { $('#vcai-loading').hide(); }
    function showError(msg) { $('#vcai-error').text(msg).show(); }

    // GENERA CONTENUTO
    $('#vcai-generate-content').on('click', function () {
        const title    = $('#title').val() || $('input[name="post_title"]').val() || '';
        const keywords = $('#vcai-keywords').val();

        if (!title) { showError('Inserisci prima il titolo del post.'); return; }

        showLoading();
        $('#vcai-content-result').hide();

        $.post(vcai.ajax_url, {
            action:   'vcai_generate_content',
            nonce:    vcai.nonce,
            title:    title,
            keywords: keywords,
            type:     $('#post_type').val() || 'post',
        })
        .done(function (res) {
            hideLoading();
            if (res.success) {
                $('#vcai-content-result .vcai-result-text').text(res.data.text);
                $('#vcai-content-result').show();
            } else {
                showError(res.data);
            }
        })
        .fail(function () { hideLoading(); showError('Errore di connessione.'); });
    });

    // COPIA CONTENUTO GENERATO
    $(document).on('click', '.vcai-copy-content', function () {
        var text = $('#vcai-content-result .vcai-result-text').text();
        if (!text) return;

        // Copia con fallback per HTTP
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text);
        } else {
            var $tmp = $('<textarea>').val(text).css({ position: 'fixed', opacity: 0 }).appendTo('body');
            $tmp[0].select();
            document.execCommand('copy');
            $tmp.remove();
        }

        // Toast notifica in alto a destra
        var $toast = $('<div>')
            .text('✅ Testo copiato!')
            .appendTo('body')
            .attr('style',
                'position:fixed;top:32px;right:20px;background:#1a1a2e;color:#fff;' +
                'padding:10px 20px;border-radius:6px;font-size:13px;z-index:999999;' +
                'opacity:0;transition:opacity 0.3s;pointer-events:none;'
            );
        setTimeout(function () { $toast.css('opacity', 1); }, 10);
        setTimeout(function () { $toast.css('opacity', 0); setTimeout(function () { $toast.remove(); }, 300); }, 2000);
    });

    // INSERISCI CONTENUTO NELL'EDITOR
    $(document).on('click', '.vcai-insert-content', function () {
        const text = $('#vcai-content-result .vcai-result-text').text();
        if (!text) return;

        // Editor classico (TinyMCE) — controlla per primo
        if (typeof tinyMCE !== 'undefined' && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden()) {
            tinyMCE.activeEditor.execCommand('mceInsertContent', false, text.replace(/\n/g, '<br>'));
        } else if ($('#content').length) {
            // Modalità testo dell'editor classico
            var $content = $('#content');
            $content.val($content.val() + '\n' + text);
        } else if (typeof wp !== 'undefined' && wp.data && wp.data.dispatch && wp.blocks) {
            // Gutenberg
            var blocks = wp.blocks.rawHandler({ HTML: '<p>' + text.replace(/\n/g, '</p><p>') + '</p>' });
            wp.data.dispatch('core/block-editor').insertBlocks(blocks);
        }
    });

    // GENERA SEO
    $('#vcai-generate-seo').on('click', function () {
        const title   = $('#title').val() || '';
        const content = typeof wp !== 'undefined' && wp.data
            ? (wp.data.select('core/block-editor').getBlocks().map(b => b.attributes.content || '').join(' '))
            : (tinyMCE && tinyMCE.activeEditor ? tinyMCE.activeEditor.getContent({ format: 'text' }) : '');

        showLoading();
        $('#vcai-seo-result').hide();

        $.post(vcai.ajax_url, {
            action:  'vcai_generate_seo',
            nonce:   vcai.nonce,
            title:   title,
            content: content.substring(0, 1000),
        })
        .done(function (res) {
            hideLoading();
            if (res.success) {
                const d = res.data;
                $('#vcai-meta-title').val(d.meta_title || '');
                $('#vcai-meta-description').val(d.meta_description || '');
                $('#vcai-excerpt').val(d.excerpt || '');
                updateCharCount('#vcai-meta-title', 60);
                updateCharCount('#vcai-meta-description', 155);
                $('#vcai-seo-result').show();
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
        const $count = $el.closest('.vcai-seo-field').find('.vcai-char-count');
        const color = len > max ? '#d63638' : (len > max * 0.85 ? '#dba617' : '#00a32a');
        $count.text(len + '/' + max + ' caratteri').css('color', color);
    }

    $('#vcai-meta-title').on('input', function () { updateCharCount('#vcai-meta-title', 60); });
    $('#vcai-meta-description').on('input', function () { updateCharCount('#vcai-meta-description', 155); });

    // Inserisci excerpt
    $(document).on('click', '.vcai-insert-excerpt', function () {
        const text = $('#vcai-excerpt').val();
        if (text && $('#excerpt').length) {
            $('#excerpt').val(text);
        }
    });

});
