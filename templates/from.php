<?php
namespace SimplyGoodTech\QueueMail;

$renderer = function(From $from, $i, $j, $auth = true) {
    ?>
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
                               value="<?= esc_attr($from->email) ?>">
                        <?php // TODO only show this if there is only one from?>
                        <label class="queue-mail-toggle">
                            <input type="checkbox"
                                   name="user[forceName]" <?php checked($from->forceName) ?>>
                            <?= esc_html_e('Force From Email', Plugin::SLUG) ?>
                        </label>
                        <p class="description">
                            <?= esc_html_e('If checked, the From Email above will be used for all emails, ignoring values set by other plugins.', Plugin::SLUG) ?>
                        </p>
                    </div>
                    <div class="queue-mail-col">
                        <label for="fromName-<?= $i ?>_<?= $j ?>"><?= esc_html_e('Name', Plugin::SLUG) ?></label>
                        <input type="text" name="fromName[]" id="fromName-<?= $i ?>_<?= $j ?>"
                               value="<?= esc_attr($from->name) ?>">
                        <?php // TODO only show this if there is only one from?>
                        <label class="queue-mail-toggle">
                            <input type="checkbox"
                                   name="user[forceName]" <?php checked($from->forceName) ?>>
                            <?= esc_html_e('Force From Name', Plugin::SLUG) ?>
                        </label>
                        <p class="description">
                            <?= esc_html_e('If checked, the From Name above will be used for all emails, ignoring values set by other plugins.', Plugin::SLUG) ?>
                        </p>
                    </div>
                </div>
                <div class="queue-mail-row">
                    <div class="queue-mail-col has-auth-<?= $i ?>" <?= $auth ? '' : ' style="display:none;"' ?>>
                        <label class="queue-mail-toggle">
                            <input type="checkbox" id="defaultAuth-<?= $i ?>_<?= $j ?>"
                                   name="defaultAuth" <?php checked($from->username == '') ?>>
                            <?= esc_html_e('Use default SMTP Authentication (as above)', Plugin::SLUG) ?>
                        </label>
                    </div>
                    <div class="queue-mail-col">
                        <?php // TODO only show this if there is more than one from OR: Rethink
                        // TODO something like don't have Default From and when multiple From Force can only be set for one.
                        ?>
                        <label class="queue-mail-toggle">
                            <input type="radio"
                                   name="defaultFrom[<?= $i ?>]" <?php checked($from->default != null && $from->default === $from->email) ?>>
                            <?= esc_html_e('Default From', Plugin::SLUG) ?>
                        </label>
                        <p class="description">
                            <?= esc_html_e('If checked and have multiple from emails set, this will be the default From Email if the requested From Email is not found.', Plugin::SLUG) ?>
                        </p>
                    </div>
                </div>
                <div class="queue-mail-row has-auth-<?= $i ?>"
                     id="from-<?= $i ?>_<?= $j ?>" <?= $from->username ? '' : ' style="display:none;"' ?>>
                    <div class="queue-mail-col">
                        <label for="username-<?= $i ?>_<?= $j ?>"><?= esc_html_e('SMTP Username', Plugin::SLUG) ?></label>
                        <input type="text" name="user[username]" id="username-<?= $i ?>_<?= $j ?>"
                               value="<?= esc_attr($from->username) ?>"
                               placeholder="<?= esc_attr_e('leave blank if same as Email', Plugin::SLUG) ?>">
                    </div>
                    <div class="queue-mail-col">
                        <label for="password-<?= $i ?>_<?= $j ?>"><?= esc_html_e('SMTP Password', Plugin::SLUG) ?></label>
                        <input type="password" name="user[password]" id="password-<?= $i ?>_<?= $j ?>"
                               value="<?= esc_attr($from->password) ?>">
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

    <?php
};