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

});
