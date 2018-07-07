<?php
namespace SimplyGoodTech\QueueMail;
?>
<h1><?= __('Queue Mail', Plugin::SLUG) ?></h1>

<?php if ($this->settingsSaved) : ?>
    <div class="updated">
        <p><strong><?php _e('Settings Saved', Plugin::SLUG) ?></strong></p>
    </div>
<?php endif; ?>

<form method="POST" action="<?php echo admin_url('options-general.php?page=' . Plugin::SLUG) ?>">
    <?php wp_nonce_field('wpshout_option_page_example_action'); ?>
    <table class="form-table queue-mail-settings">
        <tbody>
        <?php foreach ($this->settings->servers as $i => $server): ?>
            <tr>
                <th scope="row">
                    <label for="host-<?= $i ?>"><?= esc_html_e('SMTP Server', Plugin::SLUG) ?></label>
                </th>
                <td>
                    <input type="text" name="host[]" id="host-<?= $i ?>" value="<?= esc_attr($server->host) ?>">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label><?= esc_html_e('Encryption', Plugin::SLUG) ?></label>
                </th>
                <td>
                    <label class="inline">
                        <input type="radio" id="ssl-tls-<?= $i ?>" name="ssl[]"
                               value="tls" <?php checked($server->ssl, 'tls') ?>> TLS
                    </label>
                    <label class="inline">
                        <input type="radio" id="ssl-ssl-<?= $i ?>" name="ssl[]"
                               value="ssl" <?php checked($server->ssl, 'ssl') ?>> SSL
                    </label>
                    <label class="inline">
                        <input type="radio" id="ssl-none-<?= $i ?>" name="ssl[]"
                               value="none" <?php checked($server->ssl, 'none') ?>> None
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="port-<?= $i ?>"><?= esc_html_e('SMTP Port', Plugin::SLUG) ?></label>
                </th>
                <td>
                    <input type="number" name="port[]" id="port-<?= $i ?>" value="<?= esc_attr($server->host) ?>">
                </td>
            </tr>
            <tr id="auto-ssl-row-<?= $i ?>" <?= $server->ssl === 'ssl' ? '' : ' style="display:none;"' ?>>
                <th scope="row">
                    <label><?= esc_html_e('Auto TLS', Plugin::SLUG) ?></label>
                </th>
                <td>
                    <label class="queue-mail-switch">
                        <input type="checkbox" name="autoTLS[]" value="1" <?php checked($server->autoTLS) ?>>
                        <span class="queue-mail-slider"></span>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label><?= esc_html_e('Authentication', Plugin::SLUG) ?></label>
                </th>
                <td>
                    <label class="queue-mail-switch">
                        <input type="checkbox" id="auth-<?= $i ?>" name="auth[]"
                               value="1" <?php checked($server->auth) ?>>
                        <span class="queue-mail-slider"></span>
                    </label>
                </td>
            </tr>
            <tr class="has-auth-<?= $i ?>" <?= $server->auth ? '' : ' style="display:none;"' ?>>
                <th scope="row">
                    <label for="username-<?= $i ?>"><?= esc_html_e('SMTP Username', Plugin::SLUG) ?></label>
                </th>
                <td>
                    <input type="text" name="username[]" id="username-<?= $i ?>"
                           value="<?= esc_attr($server->username) ?>">
                </td>
            </tr>
            <tr class="has-auth-<?= $i ?>" <?= $server->auth ? '' : ' style="display:none;"' ?>>
                <th scope="row">
                    <label for="password-<?= $i ?>"><?= esc_html_e('SMTP Password', Plugin::SLUG) ?></label>
                </th>
                <td>
                    <input type="password" name="password[]" id="password-<?= $i ?>"
                           value="<?= esc_attr($server->password) ?>">
                </td>
            </tr>
            <?php foreach ($server->users as $j => $user): ?>
                <tr class="queue-mail-from-row">
                    <th scope="row">
                        <label><?= esc_html_e('From', Plugin::SLUG) ?></label>
                    </th>
                    <td>
                        <div class="queue-mail-row">
                            <div class="queue-mail-col">
                                <label for="fromEmail-<?= $i ?>_<?= $j ?>"><?= esc_html_e('Email', Plugin::SLUG) ?>*</label>
                                <input type="text" name="user[fromEmail]" id="fromEmail-<?= $i ?>_<?= $j ?>"
                                       value="<?= esc_attr($user->fromEmail) ?>">
                                <label>
                                    <input type="checkbox" name="user[forceFrom]" <?php checked($user->forceFrom) ?>>
                                    <?= esc_html_e('Force From Email', Plugin::SLUG) ?>
                                </label>
                                <p class="description">
                                    <?= esc_html_e('If checked, the From Email above will be used for all emails, ignoring values set by other plugins.', Plugin::SLUG) ?>
                                </p>
                            </div>
                            <div class="queue-mail-col">
                                <label for="fromName-<?= $i ?>_<?= $j ?>"><?= esc_html_e('Name', Plugin::SLUG) ?></label>
                                <input type="text" name="fromName[]" id="fromName-<?= $i ?>_<?= $j ?>"
                                       value="<?= esc_attr($user->fromName) ?>">
                                <label>
                                    <input type="checkbox" name="user[forceName]" <?php checked($user->forceName) ?>>
                                    <?= esc_html_e('Force From Name', Plugin::SLUG) ?>
                                </label>
                                <p class="description">
                                <?= esc_html_e('If checked, the From Name above will be used for all emails, ignoring values set by other plugins.', Plugin::SLUG) ?>
                                </p>
                            </div>
                        </div>
                        <div class="queue-mail-row has-auth-<?= $i ?>" <?= $server->auth ? '' : ' style="display:none;"' ?>>
                            <label>
                                <input type="checkbox" id="defaultAuth-<?= $i ?>_<?= $j ?>" name="defaultAuth" <?php checked($user->username == '') ?>>
                                <?= esc_html_e('Use default SMTP Authentication (as above)', Plugin::SLUG) ?>
                            </label>
                            <br>
                            <br>
                        </div>
                        <div class="queue-mail-row has-auth-<?= $i ?>" id="from-<?= $i ?>_<?= $j ?>" <?= $user->username ? '' : ' style="display:none;"' ?>>
                            <div class="queue-mail-col">
                                <label for="username-<?= $i ?>_<?= $j ?>"><?= esc_html_e('SMTP Username', Plugin::SLUG) ?></label>
                                <input type="text" name="user[username]" id="username-<?= $i ?>_<?= $j ?>"
                                       value="<?= esc_attr($user->username) ?>" placeholder="<?= esc_attr_e('leave blank if same as Email', Plugin::SLUG) ?>">
                            </div>
                            <div class="queue-mail-col">
                                <label for="password-<?= $i ?>_<?= $j ?>"><?= esc_html_e('SMTP Password', Plugin::SLUG) ?></label>
                                <input type="password" name="user[password]" id="password-<?= $i ?>_<?= $j ?>"
                                       value="<?= esc_attr($user->password) ?>">
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <tr id="queue-mail-add-from-btn-row">
            <th></th>
            <td>
                <button id="queue-mail-add-from-btn" type="button" class="button-primary"><?= esc_html_e('Add Another From Address', Plugin::SLUG) ?></button>
                <p class="description">
                    <?= esc_html_e('If you need to send emails from different From addresses, you can add multiple from addresses.', Plugin::SLUG) ?>
                </p>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php
    submit_button();
    ?>
</form>
<script>
    (function ($) {
        // TODO move to a file once it's working OK
        $('input[name="ssl\[\]"]').change(function () {
            var ports = {tls: '587', ssl: '565', none: '25'};
            var v = $(this).val();
            var id = getId(this);

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

        $('#queue-mail-add-from-btn').click(function () {
            var $clone = $('.queue-mail-from-row').first().clone();
            // TODO reset inputs
            $clone.insertBefore('#queue-mail-add-from-btn-row');
        });

    }(jQuery));
</script>