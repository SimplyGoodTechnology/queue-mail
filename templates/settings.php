<?php
namespace SimplyGoodTech\QueueMail;
?>
<h1><?= __('Queue Mail', 'queue-mail') ?></h1>
<p>TODO logging<br>
    Errors; All<br>
    Keep logs for: ever: X months

</p>
<?php if ($this->settingsSaved) : ?>
    <div class="notice  notice-success is-dismissible">
        <p><strong><?= esc_html__('Settings Saved', 'queue-mail') ?></strong></p>
    </div>
<?php endif; ?>
<?php if ($this->errors !== null) : ?>
    <div class="notice notice-error is-dismissible">
        <p><strong><?= $this->errors ?></strong></p>
    </div>
<?php endif; ?>

<form method="POST" action="<?php echo admin_url('options-general.php?page=' . Plugin::SLUG) ?>">
    <?php wp_nonce_field('queue_mail_option_page_save_settings_action'); ?>
    <table class="form-table queue-mail-settings">
        <tbody>
        <tr class="queue-mail-section">
            <th scope="row">Send Errors To</th>
            <td>
        <input type="text" name="sendErrorsTo" value="<?= esc_attr($this->settings->sendErrorsTo) ?>">
                <p class="description">
                    <?= esc_html__('If an email fails to send, an error message will be send to this address. 
                    If you need to send to more than one email address, separate the addresses with a semicolon.
                    If you leave this blank, errors will be sent to the wordpress admin email address.
                    ', 'queue-mail') ?>
                </p>
            </td>
        </tr>
        </tbody>
        <?php
        $serverRenderer = $this->getServerRenderer();
        $fromRenderer = $this->getFromRenderer();
        foreach ($this->settings->servers as $i => $server) {
            $mailerRenderer = $this->getMailerRenderer($server->mailer);
            $serverRenderer($server, $mailerRenderer, $fromRenderer, $i);
        }
        ?>
        <tbody id="queue-mail-add-server-btn-container">
        <tr class="queue-mail-section">
            <th scope="row"></th>
            <td>
                <div class="queue-mail-server-loader-<?= $i ?>" style="display: none;">
                    <img src="/wp-admin/images/wpspin_light-2x.gif">
                </div>
                <button data-id="<?= $i ?>" type="button"
                        class="button-primary queue-mail-add-server-btn"><?= esc_html__('Add Another Server', 'queue-mail') ?></button>
                <p class="description">
                    <?= esc_html__('If you need to send emails from different Server configurations, you can add multiple servers.', 'queue-mail') ?>
                </p>
            </td>
        </tr>
        </tbody>
    </table>

    <?php
    submit_button();
    ?>
</form>
