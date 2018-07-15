<?php
namespace SimplyGoodTech\QueueMail;

$renderer = function(SMTPServer $server, $i) {
    ?>
    <tbody class="queue-mail-smtp-<?= $i ?> queue-mail-mailer-<?= $i ?>">
    <tr>
        <th scope="row">
            <label for="host-<?= $i ?>"><?= esc_html__('SMTP Server', 'queue-mail') ?></label>
        </th>
        <td>
            <input type="text" name="host[<?= $i ?>]" id="host-<?= $i ?>" value="<?= esc_attr($server->host) ?>">
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?= esc_html__('Encryption', 'queue-mail') ?></label>
        </th>
        <td>
            <label class="inline">
                <input type="radio" id="ssl-tls-<?= $i ?>" name="ssl[<?= $i ?>]"
                       value="tls" <?php checked($server->ssl, 'tls') ?>> TLS
            </label>
            <label class="inline">
                <input type="radio" id="ssl-ssl-<?= $i ?>" name="ssl[<?= $i ?>]"
                       value="ssl" <?php checked($server->ssl, 'ssl') ?>> SSL
            </label>
            <label class="inline">
                <input type="radio" id="ssl-none-<?= $i ?>" name="ssl[<?= $i ?>]"
                       value="none" <?php checked($server->ssl, 'none') ?>> None
            </label>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label for="port-<?= $i ?>"><?= esc_html__('SMTP Port', 'queue-mail') ?></label>
        </th>
        <td>
            <input type="number" name="port[<?= $i ?>]" id="port-<?= $i ?>" value="<?= esc_attr($server->port) ?>">
        </td>
    </tr>
    <tr id="auto-ssl-row-<?= $i ?>" <?= $server->ssl === 'ssl' ? '' : ' style="display:none;"' ?>>
        <th scope="row">
            <label><?= esc_html__('Auto TLS', 'queue-mail') ?></label>
        </th>
        <td>
            <label class="queue-mail-switch">
                <input type="checkbox" name="autoTLS[<?= $i ?>]" value="1" <?php checked($server->autoTLS) ?>>
                <span class="queue-mail-slider"></span>
            </label>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?= esc_html__('Authentication', 'queue-mail') ?></label>
        </th>
        <td>
            <label class="queue-mail-switch">
                <input type="checkbox" id="auth-<?= $i ?>" name="auth[<?= $i ?>]"
                       value="1" <?php checked($server->auth) ?>>
                <span class="queue-mail-slider"></span>
            </label>
        </td>
    </tr>
    <tr class="has-auth-<?= $i ?>" <?= $server->auth ? '' : ' style="display:none;"' ?>>
        <th scope="row">
            <label for="username-<?= $i ?>"><?= esc_html__('SMTP Username', 'queue-mail') ?></label>
        </th>
        <td>
            <input type="text" name="username[<?= $i ?>]" id="username-<?= $i ?>"
                   value="<?= esc_attr($server->username) ?>">
        </td>
    </tr>
    <tr class="has-auth-<?= $i ?>" <?= $server->auth ? '' : ' style="display:none;"' ?>>
        <th scope="row">
            <label for="password-<?= $i ?>"><?= esc_html__('SMTP Password', 'queue-mail') ?></label>
        </th>
        <td>
            <input type="password" name="password[<?= $i ?>]" id="password-<?= $i ?>"
                   value="<?= esc_attr($server->password) ?>">
        </td>
    </tr>
    </tbody>
    <?php
};