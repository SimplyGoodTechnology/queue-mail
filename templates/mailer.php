<?php
namespace SimplyGoodTech\QueueMail;

$renderer = function(Mailer $mailer, $mailerRenderer, $fromRenderer, $i) {
    $mailerType = $mailer->getType();
    ?>
    <tbody id="queue-mail-mailers-<?= $i ?>" class="queue-mail-mailer">
    <tr class="queue-mail-section">
        <th scope="row">
            <label><?= esc_html__('Mailer', 'queue-mail') ?></label>
        </th>
        <td>
            <div class="queue-mail-row" id="queue-mail-mailer-row-<?= $i ?>">
                <input type="hidden" name="mailers[]" value="<?= $i ?>">
                <?php foreach ($mailer::$types as $type => $label): ?>
                    <div class="queue-mail-col">
                        <label class="queue-mail-mailer<?= $mailerType === $type ? ' active' : '' ?>">
                            <img src="<?= plugin_dir_url(__DIR__) . 'images/' . $type . '.png' ?>">
                            <input type="radio" data-id="<?= $i ?>" name="mailer[<?= $i ?>]" value="<?= $type ?>" <?php checked($mailerType, $type) ?>> <?= esc_html__($label, 'queue-mail') ?>
                        </label>
                    </div>
                <?php endforeach ?>
            </div>
            <div class="queue-mail-mailer-sub-loader-<?= $i ?>" style="display: none;">
                <img src="/wp-admin/images/wpspin_light-2x.gif">
            </div>
            <div class="send-errors" style="display:none;">
                <label class="queue-mail-toggle">
                    <input type="radio" value="<?= $i ?>"
                           name="sendErrors" <?php checked($mailer->sendErrors) ?>>
                    <?= esc_html__('Use for error reporting', 'queue-mail') ?>
                </label>
                <p class="description">
                    <?= esc_html__('If checked, this mailer configuration will be used to send an error message if an email fails to send.', 'queue-mail') ?>
                </p>

            </div>
            <div>
                <button class="queue-mail-remove-mailer-btn button-secondary" type="button" data-id="<?= $i ?>">
                    <?= esc_html__('Remove Mailer', 'queue-mail') ?></button>
            </div>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?= esc_html__('Run In Background', 'queue-mail') ?></label>
        </th>
        <td>
            <label class="queue-mail-switch">
                <input type="checkbox" id="background-<?= $i ?>" name="background[<?= $i ?>]"
                       value="1" <?php checked($mailer->background) ?>>
                <span class="queue-mail-slider"></span>
            </label>
            <p class="description">
                <?= esc_html__('Switch this on if you want the page to return immediately and use a background script to send the email.', 'queue-mail') ?>
            </p>
        </td>
    </tr>
    </tbody>
    <?php
    $mailerRenderer($mailer, $i);
    ?>
    <tbody id="queue-mail-from-addresses-<?= $i ?>">
    <?php foreach ($mailer->fromAddresses as $j => $from) {
        $fromRenderer($from, $i, $j);
    } ?>
    <tr id="queue-mail-add-from-btn-row-<?= $i ?>">
        <th></th>
        <td>
            <div class="queue-mail-from-loader-<?= $i ?>" style="display: none;">
                <img src="/wp-admin/images/wpspin_light-2x.gif">
            </div>
            <button data-id="<?= $i ?>" type="button"
                    class="button-primary queue-mail-add-from-btn"><?= esc_html__('Add Another From Address', 'queue-mail') ?></button>
            <p class="description">
                <?= esc_html__('If you need to send emails from different From addresses, you can add multiple from addresses.', 'queue-mail') ?>
            </p>
        </td>
    </tr>
    </tbody>
    <?php
};