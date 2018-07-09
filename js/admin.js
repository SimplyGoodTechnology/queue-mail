(function ($) {
    $('input[name="mailer\[\]"]').change(function () {
        var id = $(this).attr('data-id'),
            mailerType = $(this).val(),
            $mailers = $('.queue-mail-mailer-' + id),
            $settings = $('.queue-mail-' + mailerType + '-' + id),
            $loader = $('.queue-mail-mailer-loader-' + id);

        $('#queue-mail-mailer-row-' + id + ' label').removeClass('active');
        $(this).parent().addClass('active');

        $mailers.hide();

        if ($settings.length === 0) {
            $loader.show();
            $.get(ajaxurl, {action: 'queue_mail_get_mailer_settings', mailer: mailerType, id: id}, function (response) {
                $loader.hide();
                $('#queue-mail-mailers-' + id).after(response);
            });
        } else {
            $settings.show();
        }

        if (mailerType === 'smtp' && $('#auth-' + id).prop('checked')) {
            $('.has-auth-' + id).show();
        } else {
            $('.has-auth-' + id).hide();
        }
    });

    $('input[name="ssl\[\]"]').change(function () {
        var ports = {tls: '587', ssl: '565', none: '25'}, v = $(this).val(), id = getId(this);

        $('#port-' + id).val(ports[v]);

        if (v === 'ssl') {
            $('#auto-ssl-row-' + id).show();
        } else {
            $('#auto-ssl-row-' + id).hide();
        }
    });

    function getId(el) {
        var split = $(el).attr('id').split('-');
        return split[split.length - 1];
    }

    $('input[name="auth\[\]"]').change(function () {
        var id = getId(this);
        if ($(this).prop('checked')) {
            $('.has-auth-' + id).show();
        } else {
            $('.has-auth-' + id).hide();
        }
    });

    $('input[name="defaultAuth"]').change(function () {
        var id = getId(this);
        if ($(this).prop('checked')) {
            $('#from-' + id).hide();
        } else {
            $('#from-' + id).show();
        }
    });

    $('.queue-mail-add-from-btn').click(function () {
        var id = $(this).attr('data-id');

        $loader = $('.queue-mail-from-loader-' + id);
        $loader.show();
        // TODO need to set auth based on value of auth-id checkbox
        $.get(ajaxurl, {action: 'queue_mail_get_from_settings', i: id, j: $('queue-mail-from-addresses-' + id + ' .queue-mail-from-row').length},
            function (response) {
            $loader.hide();
            $('#queue-mail-add-from-btn-row-' + id).before(response);

        });
    });

    $('.queue-mail-remove-from-btn').click(function () {
        // TODO remove from
    });

}(jQuery));