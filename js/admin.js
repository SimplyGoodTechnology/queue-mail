(function ($) {
    for (var i = 0; i < $('.queue-mail-server').length; i++) {
        attachServerHandlers(i);
    }

    function attachServerHandlers(i) {
        $('input[name="mailer\[' + i + '\]"]').change(function () {
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
                $.get(ajaxurl, {action: 'queue_mail_get_mailer_form', mailer: mailerType, id: id}, function (response) {
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

        $('input[name="ssl\[' + i + '\]"]').change(function () {
            var ports = {tls: '587', ssl: '565', none: '25'}, v = $(this).val(), id = getId(this);

            $('#port-' + id).val(ports[v]);

            if (v === 'ssl') {
                $('#auto-ssl-row-' + id).show();
            } else {
                $('#auto-ssl-row-' + id).hide();
            }
        });

        $('input[name="auth\[' + i + '\]"]').change(function () {
            var id = getId(this);
            if ($(this).prop('checked')) {
                $('.has-auth-' + id).show();
            } else {
                $('.has-auth-' + id).hide();
            }
        });

        for (var j = 0; j < $('#queue-mail-from-addresses-' + i + ' .queue-mail-from-row').length; j++) {
            attachFromHandlers(i, j);
        }
    }

    function attachFromHandlers(i, j) {
        $('#queue-mail-from-container-' + i + '_' + j).find('.queue-mail-remove-from-btn').click(function () {
            if (confirm(queueMailAdminStrings.confirmRemoveFromAddress)) {
                var id = $(this).attr('data-id');
                $('#queue-mail-from-container-' + id).remove();
            }
        });


        $('input[name="from\[' + i + '\]\[' + j + '\]\[defaultAuth\]"]').change(function () {
            var id = getId(this);
            if ($(this).prop('checked')) {
                $('#from-auth-' + id).hide();
            } else {
                $('#from-auth-' + id).show();
            }
        });
    }

    function getId(el) {
        var split = $(el).attr('id').split('-');
        return split[split.length - 1];
    }

    $('.queue-mail-add-from-btn').click(function () {
        var id = $(this).attr('data-id');

        $loader = $('.queue-mail-from-loader-' + id);
        $loader.show();
        var auth = $('#auth-' + id).prop('checked') ? 1 : 0;
        var j = $('#queue-mail-from-addresses-' + id + ' .queue-mail-from-row').length;

        $.get(ajaxurl, {action: 'queue_mail_get_from_form', auth: auth, i: id, j: j},
            function (response) {
                $loader.hide();
                $('#queue-mail-add-from-btn-row-' + id).before(response);
                attachFromHandlers(id, j);
            });
    });

    $('.queue-mail-add-server-btn').click(function () {
        var id = $(this).attr('data-id');

        $loader = $('.queue-mail-server-loader-' + id);
        $loader.show();

        var i = $('.queue-mail-server').length;
        $.get(ajaxurl, {action: 'queue_mail_get_server_form', i: i},
            function (response) {
                $loader.hide();
                $('#queue-mail-add-server-btn-container').before(response);
                attachServerHandlers(i);
            });
    });

}(jQuery));