<?php
namespace SimplyGoodTech\QueueMail;

// Note to Self: using anonymous functions to get local scope.
// TODO move note to main Admin class when finished
$serverRenderer = function(Server $server, $fromRenderer, $i) {
    ?>
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
    $this->getMailerRenderer($server->mailer)($server, $i);
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
                class="button-primary queue-mail-add-from-btn"><?= esc_html_e('Add Another From Address', Plugin::SLUG) ?></button>
        <p class="description">
            <?= esc_html_e('If you need to send emails from different From addresses, you can add multiple from addresses.', Plugin::SLUG) ?>
        </p>
    </td>
</tr>
</tbody>
    <?php
};
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
        <?php
        $fromRenderer = $this->getFromRenderer();
        foreach ($this->settings->servers as $i => $server) {
            $serverRenderer($server, $fromRenderer, $i);
        }
        ?>
        <tr class="queue-mail-section" id="queue-mail-add-server-btn-row-<?= $i ?>">
            <th scope="row"></th>
            <td>
                <div class="queue-mail-server-loader-<?= $i ?>" style="display: none;">
                    <img src="/wp-admin/images/wpspin_light-2x.gif">
                </div>
                <button data-id="<?= $i ?>" type="button"
                        class="button-primary queue-mail-add-server-btn"><?= esc_html_e('Add Another Server', Plugin::SLUG) ?></button>
                <p class="description">
                    <?= esc_html_e('If you need to send emails from different Server configurations, you can add multiple servers.', Plugin::SLUG) ?>
                </p>
            </td>
        </tr>
    </table>

    <?php
    submit_button();
    ?>
</form>
