<?php
namespace SimplyGoodTech\QueueMail;

$renderer = function(SMTPMailer $mailer, $i) {
    ?>
    <tbody class="queue-mail-smtp-<?= $i ?> queue-mail-mailer-<?= $i ?>">
    <tr>
        <th scope="row">
            <label for="host-<?= $i ?>"><?= esc_html__('SMTP Server', 'queue-mail') ?></label>
        </th>
        <td>
            <input type="text" required name="host[<?= $i ?>]" id="host-<?= $i ?>" value="<?= esc_attr($mailer->host) ?>"
                   data-parsley-error-message="<?= esc_attr__('Please enter the SMTP server hostname or IP address', 'queue-mail') ?>">
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?= esc_html__('Encryption', 'queue-mail') ?></label>
        </th>
        <td>
            <label class="inline">
                <input type="radio" id="ssl-tls-<?= $i ?>" name="ssl[<?= $i ?>]"
                       value="tls" <?php checked($mailer->ssl, 'tls') ?>> TLS
            </label>
            <label class="inline">
                <input type="radio" id="ssl-ssl-<?= $i ?>" name="ssl[<?= $i ?>]"
                       value="ssl" <?php checked($mailer->ssl, 'ssl') ?>> SSL
            </label>
            <label class="inline">
                <input type="radio" id="ssl-none-<?= $i ?>" name="ssl[<?= $i ?>]"
                       value="none" <?php checked($mailer->ssl, 'none') ?>> None
            </label>
            <p class="description">
                <?= esc_html__('For most servers TLS is the recommended option. If your SMTP provider offers both SSL and TLS options, using TLS is recommended.', 'queue-mail') ?>
            </p>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label for="port-<?= $i ?>"><?= esc_html__('SMTP Port', 'queue-mail') ?></label>
        </th>
        <td>
            <input type="number" name="port[<?= $i ?>]" id="port-<?= $i ?>" value="<?= esc_attr($mailer->port) ?>">
        </td>
    </tr>
    <tr id="auto-ssl-row-<?= $i ?>" <?= $mailer->ssl === 'ssl' ? '' : ' style="display:none;"' ?>>
        <th scope="row">
            <label><?= esc_html__('Auto TLS', 'queue-mail') ?></label>
        </th>
        <td>
            <label class="queue-mail-switch">
                <input type="checkbox" name="autoTLS[<?= $i ?>]" value="1" <?php checked($mailer->autoTLS) ?>>
                <span class="queue-mail-slider"></span>
            </label>
            <p class="description">
                <?= esc_html__('By default TLS encryption is automatically used if the server supports it, which is recommended. In some cases this can cause issues and may need to be disabled.', 'queue-mail') ?>
            </p>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?= esc_html__('Authentication', 'queue-mail') ?></label>
        </th>
        <td>
            <label class="queue-mail-switch">
                <input type="checkbox" id="auth-<?= $i ?>" name="auth[<?= $i ?>]"
                       value="1" <?php checked($mailer->auth) ?>>
                <span class="queue-mail-slider"></span>
            </label>
        </td>
    </tr>
    <tr class="has-auth-<?= $i ?>" <?= $mailer->auth ? '' : ' style="display:none;"' ?>>
        <th scope="row">
            <label for="username-<?= $i ?>"><?= esc_html__('SMTP Username', 'queue-mail') ?></label>
        </th>
        <td>
            <input type="text" name="username[<?= $i ?>]" id="username-<?= $i ?>"
                   data-parsley-error-message="<?= esc_attr__('Please enter the SMTP username', 'queue-mail') ?>"
                   value="<?= esc_attr($mailer->username) ?>">
        </td>
    </tr>
    <tr class="has-auth-<?= $i ?>" <?= $mailer->auth ? '' : ' style="display:none;"' ?>>
        <th scope="row">
            <label for="password-<?= $i ?>"><?= esc_html__('SMTP Password', 'queue-mail') ?></label>
        </th>
        <td>
            <input type="password" name="password[<?= $i ?>]" id="password-<?= $i ?>"
                   data-parsley-error-message="<?= esc_attr__('Please enter the SMTP password', 'queue-mail') ?>"
                   value="<?= esc_attr($mailer->password) ?>">
        </td>
    </tr>
    </tbody>
    <?php
};