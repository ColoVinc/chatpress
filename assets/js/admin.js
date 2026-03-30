jQuery(function ($) {

    // Test connessione API
    $('#chatpress-test-api').on('click', function () {
        const $btn = $(this);
        const $result = $('#chatpress-test-result');

        $btn.prop('disabled', true).text("Test in corso...");
        $result.removeClass('success error').text('');

        $.post(chatpress.ajax_url, {
            action: 'chatpress_test_api',
            nonce: chatpress.nonce,
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
            $btn.prop('disabled', false).text('Testa connessione');
        });
    });
});