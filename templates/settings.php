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
                    <label for="host_<?= $i ?>"><?= esc_html_e('SMTP Server', Plugin::SLUG) ?></label>
                </th>
                <td>
                    <input type="text" name="host[]" id="host_<?= $i ?>" value="<?= esc_attr($server->host) ?>">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label><?= esc_html_e('Encryption', Plugin::SLUG) ?></label>
                </th>
                <td>
                    <label class="inline">
                        <input type="radio" id="ssl_<?= $i ?>" name="ssl[]"
                               value="tls" <?php checked($server->ssl, 'tls') ?>> TLS
                    </label>
                    <label class="inline">
                        <input type="radio" id="ssl_<?= $i ?>" name="ssl[]"
                               value="ssl" <?php checked($server->ssl, 'ssl') ?>> SSL
                    </label>
                    <label class="inline">
                        <input type="radio" id="ssl_<?= $i ?>" name="ssl[]"
                               value="none" <?php checked($server->ssl, 'none') ?>> None
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="port<?= $i ?>"><?= esc_html_e('SMTP Port', Plugin::SLUG) ?></label>
                </th>
                <td>
                    <input type="number" name="port[]" id="port_<?= $i ?>" value="<?= esc_attr($server->host) ?>">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label><?= esc_html_e('Auto TLS', Plugin::SLUG) ?></label>
                </th>
                <td>
                    <label class="switch">
                        <input type="checkbox" name="autoTLS[]" value="1" <?php checked($server->autoTLS) ?>>
                        <span class="slider round"></span>
                    </label>
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
            var ports = {tls: '587', ssl: '565', none: ''};
            var split = $(this).attr('id').split('_');
            var id = split[1];
            $('#port_' + id).val(ports[$(this).val()]);


        });
    }(jQuery));
</script>