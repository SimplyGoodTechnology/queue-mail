(function ($) {

    window.Parsley
        .addValidator('multipleEmail', {
            requirementType: 'string',
            validateString: function(value, requirement) {
                return $('<input type="email" multiple>').val(value)[0].checkValidity();
            }
        });

    window.Parsley.on('field:error', function() {
        // This global callback will be called for any field that fails validation.
        console.log('Validation failed for: ', this.$element);
    });

    $('#queue-mail-form-loader').hide();
    $('#queue-mail-admin-form').show().parsley();

    $('#queue-mail-logging').change(function() {
        $('#queue-mail-logging-settings').find('input.logging-setting').prop('disabled', !$(this).prop('checked'));
    });

    for (var i = 0; i < $('.queue-mail-mailer').length; i++) {
        attachMailerHandlers(i);
    }

    function attachMailerHandlers(i) {
        hideIfOne();

        function hideIfOne() {
            var $sendErrors = $('.send-errors');
            if ($sendErrors.length > 1) {
                $sendErrors.show();
            } else {
                $sendErrors.hide();
            }

            var $removeBtns = $('.queue-mail-remove-mailer-btn');
            if ($removeBtns.length > 1) {
                $removeBtns.show();
            } else {
                $removeBtns.hide();
            }
        }

        $('#queue-mail-mailers-' + i ).find('.queue-mail-remove-mailer-btn').click(function () {
            if (confirm(queueMailAdminStrings.confirmRemoveMailer)) {
                var id = $(this).attr('data-id');
                $('#queue-mail-mailers-' + id).remove();
                $('.queue-mail-mailer-' + id).remove();
                $('#queue-mail-from-addresses-' + id).remove();
                hideIfOne();
            }
        });

        $('input[name="mailer\[' + i + '\]"]').change(function () {
            var id = $(this).attr('data-id'),
                mailerType = $(this).val(),
                $mailers = $('.queue-mail-mailer-' + id),
                $settings = $('.queue-mail-' + mailerType + '-' + id),
                $loader = $('.queue-mail-mailer-sub-loader-' + id);

            $('#queue-mail-mailer-row-' + id + ' label').removeClass('active');
            $(this).parent().addClass('active');

            $mailers.hide();
            $mailers.find('input').prop('disabled',  true);

            if ($settings.length === 0) {
                $loader.show();
                $.get(ajaxurl, {action: 'queue_mail_get_mailer_sub_form', mailer: mailerType, id: id}, function (response) {
                    $loader.hide();
                    $('#queue-mail-mailers-' + id).after(response);
                });
            } else {
                $settings.find('input').prop('disabled',  false);
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

            if (v === 'ssl' || v === 'none') {
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
        toggleDefaultEmail();

        $('#queue-mail-from-container-' + i + '_' + j).find('.queue-mail-remove-from-btn').click(function () {
            if (confirm(queueMailAdminStrings.confirmRemoveFromAddress)) {
                var id = $(this).attr('data-id');
                $('#queue-mail-from-container-' + id).remove();
                toggleDefaultEmail();
            }
        });

        function toggleDefaultEmail() {
            var n = $('.queue-mail-from-row').length, $checked;
            if (n > 1) {
                $checked = $('.queue-mail-force-email:checked');
                if ($checked.length > 0) {
                    $checked.closest('.queue-mail-from-row').find('.queue-mail-is-default-email').prop('checked', true);
                }
                $checked = $('.queue-mail-force-name:checked');
                if ($checked.length > 0) {
                    $checked.closest('.queue-mail-from-row').find('.queue-mail-is-default-name').prop('checked', true);
                }
                $('.queue-mail-force-email-container').hide();
                $('.queue-mail-default-email-container').show();
            } else {
                $checked = $('.queue-mail-default-email:checked');
                if ($checked.length > 0) {
                    $checked.closest('.queue-mail-from-row').find('.queue-mail-is-force-email').prop('checked', true);
                }
                $checked = $('.queue-mail-default-name:checked');
                if ($checked.length > 0) {
                    $checked.closest('.queue-mail-from-row').find('.queue-mail-is-force-name').prop('checked', true);
                }
                $('.queue-mail-default-email-container').hide();
                $('.queue-mail-force-email-container').show();
            }
        }

        $('input[name="from\[' + i + '\]\[' + j + '\]\[auth\]"]').change(function () {
            var id = getId(this);
            if ($(this).prop('checked')) {
                $('#from-auth-' + id).show();
            } else {
                $('#from-auth-' + id).hide();
            }
        });

        $('input[name="from\[' + i + '\]\[' + j + '\]\[isDefaultEmail\]"]').change(function () {
            if ($(this).prop('checked')) {
                var self = this;
                $('.queue-mail-is-default-email').each(function () {
                    if (this !== self) {
                        $(this).prop('checked', false);
                    }
                });
            }
        });
        $('input[name="from\[' + i + '\]\[' + j + '\]\[isDefaultName\]"]').change(function () {
            if ($(this).prop('checked')) {
                var self = this;
                $('.queue-mail-is-default-name').each(function () {
                    if (this !== self) {
                        $(this).prop('checked', false);
                    }
                });
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
        var j = $('#queue-mail-from-addresses-' + id + ' .queue-mail-from-row').length;

        $.get(ajaxurl, {action: 'queue_mail_get_from_form', i: id, j: j},
            function (response) {
                $loader.hide();
                $('#queue-mail-add-from-btn-row-' + id).before(response);
                attachFromHandlers(id, j);
            });
    });

    $('.queue-mail-add-mailer-btn').click(function () {
        var id = $(this).attr('data-id');

        $loader = $('.queue-mail-mailer-loader-' + id);
        $loader.show();

        var i = $('.queue-mail-mailer').length;
        $.get(ajaxurl, {action: 'queue_mail_get_mailer_form', i: i},
            function (response) {
                $loader.hide();
                $('#queue-mail-add-mailer-btn-container').before(response);
                attachMailerHandlers(i);
            });
    });

}(jQuery));