<?php
namespace SimplyGoodTech\QueueMail;

$renderer = function(Server $server, $mailerRenderer, $fromRenderer, $i) {
    ?>
    <tbody id="queue-mail-mailers-<?= $i ?>" class="queue-mail-server">
    <tr class="queue-mail-section">
        <th scope="row">
            <label><?= esc_html__('Mailer', 'queue-mail') ?></label>
        </th>
        <td>
            <div class="queue-mail-row" id="queue-mail-mailer-row-<?= $i ?>">
                <input type="hidden" name="mailers[]" value="<?= $i ?>">
                <?php foreach ($this->mailers as $mailer => $label): ?>
                    <div class="queue-mail-col">
                        <label class="queue-mail-mailer<?= $server->mailer === $mailer ? ' active' : '' ?>">
                            <img src="<?= plugin_dir_url(__DIR__) . 'images/' . $mailer . '.png' ?>">
                            <input type="radio" data-id="<?= $i ?>" name="mailer[<?= $i ?>]" value="<?= $mailer ?>" <?php checked($server->mailer, $mailer) ?>> <?= esc_html__($label, 'queue-mail') ?>
                        </label>
                    </div>
                <?php endforeach ?>
            </div>
            <div class="queue-mail-mailer-loader-<?= $i ?>" style="display: none;">
                <img src="/wp-admin/images/wpspin_light-2x.gif">
            </div>
            <div>
                <label class="queue-mail-toggle">
                    <input type="radio" value="<?= $i ?>"
                           name="sendErrors" <?php checked($server->sendErrors) ?>>
                    <?= esc_html__('Use for error reporting', 'queue-mail') ?>
                </label>
                <p class="description">
                    <?= esc_html__('If checked, this mailer configuration will be used to send an error message if an email fails to send.', 'queue-mail') ?>
                </p>

            </div>
            <p>
                TODO if more than one mailer show 'Report Error Mailer' option<br>
                TODO Add remove server button<br>
                TODO Add send in background option
            </p>
        </td>
    </tr>
    </tbody>
    <?php
    $mailerRenderer($server, $i);
    ?>
    <tbody id="queue-mail-from-addresses-<?= $i ?>">
    <?php foreach ($server->fromAddresses as $j => $from) {
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