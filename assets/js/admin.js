jQuery(function ($) {

    // Toggle sezioni provider
    function toggleProvider() {
        var provider = $('#sitegenie-provider-select').val();
        $('.sitegenie-provider-section').hide();
        $('#sitegenie-provider-' + provider).show();
    }
    $('#sitegenie-provider-select').on('change', toggleProvider);
    toggleProvider();

    // Test connessione API
    $('#sitegenie-test-api').on('click', function () {
        const $btn    = $(this);
        const $result = $('#sitegenie-test-result');

        $btn.prop('disabled', true).text('⏳ Test in corso...');
        $result.removeClass('success error').text('');

        $.post(sitegenie.ajax_url, {
            action: 'sitegenie_test_api',
            nonce:  sitegenie.nonce,
        })
        .done(function (res) {
            if (res.success) {
                $result.addClass('success').text('✅ ' + res.data);
            } else {
                $result.addClass('error').text('❌ ' + res.data);
            }
        })
        .fail(function () {
            $result.addClass('error').text('❌ Errore di connessione.');
        })
        .always(function () {
            $btn.prop('disabled', false).text('🔌 Testa Connessione');
        });
    });

    // Svuota log
    $('#sitegenie-clear-logs').on('click', function () {
        if ( ! confirm( 'Sei sicuro di voler svuotare tutti i log? L\'operazione non è reversibile.' ) ) return;

        const $btn = $(this);
        $btn.prop('disabled', true).text('⏳ Svuotamento...');

        $.post(sitegenie.ajax_url, {
            action: 'sitegenie_clear_logs',
            nonce:  sitegenie.nonce,
        })
        .done(function (res) {
            if (res.success) {
                location.reload();
            } else {
                alert('Errore: ' + res.data);
                $btn.prop('disabled', false).text('🗑️ Svuota Log');
            }
        })
        .fail(function () {
            alert('Errore di connessione.');
            $btn.prop('disabled', false).text('🗑️ Svuota Log');
        });
    });


    // ── Knowledge Base ───────────────────────────────────────────

    // Carica file .txt nel textarea
    $('#sitegenie-kb-file').on('change', function () {
        var file = this.files[0];
        if (!file) return;
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#sitegenie-kb-content').val(e.target.result);
            if (!$('#sitegenie-kb-name').val()) {
                $('#sitegenie-kb-name').val(file.name.replace(/\.txt$/i, ''));
            }
        };
        reader.readAsText(file);
    });

    // Upload documento
    $('#sitegenie-kb-upload').on('click', function () {
        var name    = $('#sitegenie-kb-name').val().trim();
        var content = $('#sitegenie-kb-content').val().trim();
        if (!name || !content) { $('#sitegenie-kb-result').show().text('⚠️ Nome e contenuto obbligatori.'); return; }

        var $btn = $(this);
        $btn.prop('disabled', true).text('⏳ Salvataggio...');

        $.post(sitegenie.ajax_url, {
            action: 'sitegenie_upload_knowledge',
            nonce: sitegenie.nonce,
            doc_name: name,
            doc_content: content,
        }).done(function (res) {
            if (res.success) {
                $('#sitegenie-kb-result').show().css('color', '#00a32a').text('✅ ' + res.data.message);
                setTimeout(function () { location.reload(); }, 1000);
            } else {
                $('#sitegenie-kb-result').show().css('color', '#d63638').text('❌ ' + res.data);
            }
        }).fail(function () {
            $('#sitegenie-kb-result').show().css('color', '#d63638').text('❌ Errore di connessione.');
        }).always(function () {
            $btn.prop('disabled', false).html('<i class="fa-solid fa-plus"></i> Salva Documento');
        });
    });

    // Elimina documento
    $(document).on('click', '.sitegenie-kb-delete', function () {
        var name = $(this).data('name');
        if (!confirm('Eliminare il documento "' + name + '"?')) return;

        $.post(sitegenie.ajax_url, {
            action: 'sitegenie_delete_knowledge',
            nonce: sitegenie.nonce,
            doc_name: name,
        }).done(function (res) {
            if (res.success) location.reload();
            else alert('Errore: ' + res.data);
        });
    });

    // Indicizza tutti i post (RAG)
    $('#sitegenie-index-posts').on('click', function () {
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Indicizzazione...');

        $.post(sitegenie.ajax_url, {
            action: 'sitegenie_index_posts',
            nonce: sitegenie.nonce,
        }).done(function (res) {
            if (res.success) {
                $('#sitegenie-index-result').show().css('color', '#00a32a').text('✅ ' + res.data.message);
                setTimeout(function () { location.reload(); }, 1500);
            } else {
                $('#sitegenie-index-result').show().css('color', '#d63638').text('❌ ' + res.data);
            }
        }).fail(function () {
            $('#sitegenie-index-result').show().css('color', '#d63638').text('❌ Errore di connessione.');
        }).always(function () {
            $btn.prop('disabled', false).html('<i class="fa-solid fa-arrows-rotate"></i> Indicizza tutti i post');
        });
    });
});
