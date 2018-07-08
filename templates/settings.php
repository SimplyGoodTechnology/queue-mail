<?php
namespace SimplyGoodTech\QueueMail;
?>
<h1><?= __('Queue Mail', Plugin::SLUG) ?></h1>
<img src="<?= plugin_dir_url(__DIR__) . 'images/bunny.png' ?>" height=30">

<?php if ($this->settingsSaved) : ?>
    <div class="updated">
        <p><strong><?php _e('Settings Saved', Plugin::SLUG) ?></strong></p>
    </div>
<?php endif; ?>

<form method="POST" action="<?php echo admin_url('options-general.php?page=' . Plugin::SLUG) ?>">
    <?php wp_nonce_field('wpshout_option_page_example_action'); ?>
    <table class="form-table queue-mail-settings">

        <?php foreach ($this->settings->servers as $i => $server): ?>
        <tbody id="queue-mail-mailers-<?= $i ?>">
            <tr class="queue-mail-section">
                <th scope="row">
                    <label><?= esc_html_e('Mailer', Plugin::SLUG) ?></label>
                </th>
                <td>
                    <div class="queue-mail-row" id="queue-mail-mailer-row-<?= $i ?>">
                        <?php foreach ($this->mailers as $mailer => $label): ?>
                        <div class="queue-mail-col">
                            <label class="queue-mail-mailer<?= $server->mailer === $mailer ? ' active' : '' ?>">
                                <img src="<?= plugin_dir_url(__DIR__) . 'images/' . $mailer . '.png' ?>">
                                <input type="radio" data-id="<?= $i ?>" name="mailer[]" value="<?= $mailer ?>" <?php checked($server->mailer, $mailer) ?>> <?= esc_html_e($label, Plugin::SLUG) ?>
                            </label>
                        </div>
                        <?php endforeach ?>
                    </div>
                    <div class="queue-mail-mailer-loader-<?= $i ?>" style="display: none;">
                        <img src="/wp-admin/images/wpspin_light-2x.gif">
                    </div>
                    <p>
                        TODO if more than one mailer show 'Report Error Mailer' option
                    </p>
                </td>
            </tr>
        </tbody>
            <?php
            $serverSettingsTemplate = plugin_dir_path(__DIR__) . 'templates/' . $server->mailer . '.php';
            if (is_file($serverSettingsTemplate)) {
                include $serverSettingsTemplate;
            }
            ?>
        <tbody>
            <?php foreach ($server->users as $j => $user): ?>
                <tr class="queue-mail-from-row queue-mail-section">
                    <th scope="row">
                        <label><?= esc_html_e('From', Plugin::SLUG) ?></label>
                    </th>
                    <td>
                        <div class="queue-mail-from-container" id="queue-mail-from-container-<?= $i ?>_<?= $j ?>">
                            <div class="queue-mail-row">
                                <div class="queue-mail-col">
                                    <label for="fromEmail-<?= $i ?>_<?= $j ?>"><?= esc_html_e('Email', Plugin::SLUG) ?>
                                        *</label>
                                    <input type="text" name="user[fromEmail]" id="fromEmail-<?= $i ?>_<?= $j ?>"
                                           value="<?= esc_attr($user->fromEmail) ?>">
                                    <?php // TODO only show this if there is only one from?>
                                    <label class="queue-mail-toggle">
                                        <input type="checkbox"
                                               name="user[forceFrom]" <?php checked($user->forceFrom) ?>>
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
                                    <?php // TODO only show this if there is only one from?>
                                    <label class="queue-mail-toggle">
                                        <input type="checkbox"
                                               name="user[forceName]" <?php checked($user->forceName) ?>>
                                        <?= esc_html_e('Force From Name', Plugin::SLUG) ?>
                                    </label>
                                    <p class="description">
                                        <?= esc_html_e('If checked, the From Name above will be used for all emails, ignoring values set by other plugins.', Plugin::SLUG) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="queue-mail-row">
                                <div class="queue-mail-col has-auth-<?= $i ?>" <?= $server->auth ? '' : ' style="display:none;"' ?>>
                                    <label class="queue-mail-toggle">
                                        <input type="checkbox" id="defaultAuth-<?= $i ?>_<?= $j ?>"
                                               name="defaultAuth" <?php checked($user->username == '') ?>>
                                        <?= esc_html_e('Use default SMTP Authentication (as above)', Plugin::SLUG) ?>
                                    </label>
                                </div>
                                <div class="queue-mail-col">
                                    <?php // TODO only show this if there is more than one from?>
                                    <label class="queue-mail-toggle">
                                        <input type="radio"
                                               name="defaultFrom[<?= $i ?>]" <?php checked($server->defaultFrom != null && $server->defaultFrom === $user->fromEmail) ?>>
                                        <?= esc_html_e('Default From', Plugin::SLUG) ?>
                                    </label>
                                    <p class="description">
                                        <?= esc_html_e('If checked and have multiple from emails set, this will be the default From Email if the requested From Email is not found.', Plugin::SLUG) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="queue-mail-row has-auth-<?= $i ?>"
                                 id="from-<?= $i ?>_<?= $j ?>" <?= $user->username ? '' : ' style="display:none;"' ?>>
                                <div class="queue-mail-col">
                                    <label for="username-<?= $i ?>_<?= $j ?>"><?= esc_html_e('SMTP Username', Plugin::SLUG) ?></label>
                                    <input type="text" name="user[username]" id="username-<?= $i ?>_<?= $j ?>"
                                           value="<?= esc_attr($user->username) ?>"
                                           placeholder="<?= esc_attr_e('leave blank if same as Email', Plugin::SLUG) ?>">
                                </div>
                                <div class="queue-mail-col">
                                    <label for="password-<?= $i ?>_<?= $j ?>"><?= esc_html_e('SMTP Password', Plugin::SLUG) ?></label>
                                    <input type="password" name="user[password]" id="password-<?= $i ?>_<?= $j ?>"
                                           value="<?= esc_attr($user->password) ?>">
                                </div>
                            </div>
                            <?php if ($i > 0): ?>
                            <div>
                                <button class="queue-mail-remove-from-btn" type="button" data-id="<?= $i ?>_<?= $j ?>"
                                        class="button-primary"><?= esc_html_e('Remove From Address', Plugin::SLUG) ?></button>
                            </div>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr id="queue-mail-add-from-btn-row">
                <th></th>
                <td>
                    <button id="queue-mail-add-from-btn" type="button"
                            class="button-primary"><?= esc_html_e('Add Another From Address', Plugin::SLUG) ?></button>
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