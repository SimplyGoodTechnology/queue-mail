<?php
namespace SimplyGoodTech\QueueMail;
?>
<h1><?= __('Queue Mail', 'queue-mail') ?></h1>
<?php if ($this->settingsSaved) : ?>
    <div class="notice  notice-success is-dismissible">
        <p><strong><?= esc_html__('Settings Saved', 'queue-mail') ?></strong></p>
    </div>
<?php endif; ?>
<?php if ($this->error !== null) : ?>
    <div class="notice notice-error is-dismissible">
        <p><strong><?= $this->error ?></strong></p>
    </div>
<?php endif; ?>
<noscript>
    <?= esc_html__('This form requires javascript to work. Please enable javascript and reload the page.', 'queue-mail') ?>
</noscript>
<div id="queue-mail-form-loader">
    <br>
    <br>
    <img src="/wp-admin/images/wpspin_light-2x.gif"> <br>
    <?= esc_html__('Loading the settings form ...', 'queue-mail') ?>
</div>
<form id="queue-mail-admin-form" method="POST" action="<?= admin_url('options-general.php?page=' . Plugin::SLUG) ?>"
      style="display:none">
    <?php wp_nonce_field('queue_mail_option_page_save_settings_action'); ?>
    <table class="form-table queue-mail-settings">
        <tbody>
        <tr class="queue-mail-section">
            <th scope="row"><label><?= esc_html__('Logging', 'queue-mail') ?></label></th>
            <td id="queue-mail-logging-settings">
                <div class="queue-mail-row">
                    <div class="queue-mail-col" style="width: 100px;">
                        <label class="queue-mail-switch">
                            <input type="checkbox" name="logging" id="queue-mail-logging"
                                   value="1" <?php checked($this->settings->logging) ?>>
                            <span class="queue-mail-slider"></span>
                        </label>
                    </div>
                    <div class="queue-mail-col" style="width: 300px;">
                        <label class="inline">
                            <input type="radio" name="logLevel" class="logging-setting"
                                   value="error" <?php checked($this->settings->logLevel, 'error') ?>>
                            <?= esc_html__('Log errors only', 'queue-mail') ?>
                        </label>
                        <label class="inline">
                            <input type="radio" name="logLevel"class="logging-setting"
                                   value="all" <?php checked($this->settings->logLevel, 'all') ?>>
                            <?= esc_html__('Log everthing', 'queue-mail') ?>
                        </label>
                    </div>
                    <div class="queue-mail-col">
                        <label class="inline-input"
                               style="margin-top: -6px;"><?= esc_html__('Keep logs for', 'queue-mail') ?>
                            <input type="number" name="logPeriod" class="logging-setting"
                                   value="<?= esc_attr($this->settings->logPeriod) ?>"> <?= esc_html__('Months', 'queue-mail') ?>
                        </label>
                    </div>
                </div>
                <div class="queue-mail-row">

                </div>
                <p class="description">
                    <?= esc_html__('Log email message submissions, with logging enabled you can resend any logged email.', 'queue-mail') ?>
                </p>
            </td>
        </tr>
        <tr class="queue-mail-section">
            <th scope="row"><label><?= esc_html__('Send Errors To', 'queue-mail') ?></label></th>
            <td>
                <input type="text" name="sendErrorsTo"
                       value="<?= esc_attr(@implode(', ', $this->settings->sendErrorsTo)) ?>"
                       data-parsley-multiple-email
                       placeholder="admin@example.com, dev@example.com">
                <p class="description">
                    <?= esc_html__('If an email fails to send, an error message will be send to this address. 
                    If you need to send to more than one email address, separate the addresses with a comma.
                    If you leave this blank, errors will be sent to the wordpress admin email address.
                    ', 'queue-mail') ?>
                </p>
            </td>
        </tr>
        </tbody>
        <?php
        foreach ($this->settings->mailers as $i => $mailer) {
            $this->renderMailer($mailer, $i);
        }
        ?>
        <tbody id="queue-mail-add-mailer-btn-container">
        <tr class="queue-mail-section">
            <th scope="row"></th>
            <td>
                <div class="queue-mail-mailer-loader-<?= $i ?>" style="display: none;">
                    <img src="/wp-admin/images/wpspin_light-2x.gif">
                </div>
                <button data-id="<?= $i ?>" type="button"
                        class="button-primary queue-mail-add-mailer-btn"><?= esc_html__('Add Another Mailer', 'queue-mail') ?></button>
                <p class="description">
                    <?= esc_html__('If you need to send emails from different Mailer configurations, you can add multiple mailers.', 'queue-mail') ?>
                </p>
            </td>
        </tr>
        </tbody>
    </table>

    <?php
    submit_button();
    ?>
</form>
