<?php
namespace SimplyGoodTech\QueueMail;

$renderer = function(From $from, $i, $j, $auth = true) {
    ?>
    <tr class="queue-mail-from-row queue-mail-section" id="queue-mail-from-container-<?= $i ?>_<?= $j ?>">
        <th scope="row">
            <label><?= esc_html__('From', 'queue-mail') ?></label>
        </th>
        <td>
            <div>
                <div class="queue-mail-row">
                    <div class="queue-mail-col">
                        <label for="fromEmail-<?= $i ?>_<?= $j ?>"><?= esc_html__('Email', 'queue-mail') ?>
                            *</label>
                        <input type="text" name="from[<?= $i ?>][<?= $j ?>][email]" id="fromEmail-<?= $i ?>_<?= $j ?>"
                               value="<?= esc_attr($from->email) ?>">
                        <?php // TODO only show this if there is only one from?>
                        <label class="queue-mail-toggle">
                            <input type="checkbox" value="1"
                                   name="from[<?= $i ?>][<?= $j ?>][forceEmail]" <?php checked($from->forceEmail) ?>>
                            <?= esc_html__('Force From Email', 'queue-mail') ?>
                        </label>
                        <p class="description">
                            <?= esc_html__('If checked, the From Email above will be used for all emails, ignoring values set by other plugins.', 'queue-mail') ?>
                        </p>
                    </div>
                    <div class="queue-mail-col">
                        <label for="fromName-<?= $i ?>_<?= $j ?>"><?= esc_html__('Name', 'queue-mail') ?></label>
                        <input type="text" name="from[<?= $i ?>][<?= $j ?>][name]" id="fromName-<?= $i ?>_<?= $j ?>"
                               value="<?= esc_attr($from->name) ?>">
                        <?php // TODO only show this if there is only one from?>
                        <label class="queue-mail-toggle">
                            <input type="checkbox" value="1"
                                   name="from[<?= $i ?>][<?= $j ?>][forceName]" <?php checked($from->forceName) ?>>
                            <?= esc_html__('Force From Name', 'queue-mail') ?>
                        </label>
                        <p class="description">
                            <?= esc_html__('If checked, the From Name above will be used for all emails, ignoring values set by other plugins.', 'queue-mail') ?>
                        </p>
                    </div>
                </div>
                <div class="queue-mail-row">
                    <div class="queue-mail-col has-auth-<?= $i ?>" <?= $auth ? '' : ' style="display:none;"' ?>>
                        <label class="queue-mail-toggle">
                            <input type="checkbox" id="defaultAuth-<?= $i ?>_<?= $j ?>" value="1"
                                   name="from[<?= $i ?>][<?= $j ?>][defaultAuth]" <?php checked($from->username == '') ?>>
                            <?= esc_html__('Use default SMTP Authentication (as above)', 'queue-mail') ?>
                        </label>
                    </div>
                    <div class="queue-mail-col">
                        <?php // TODO only show this if there is more than one from OR: Rethink
                        // TODO something like don't have Default From and when multiple From Force can only be set for one.
                        ?>
                        <label class="queue-mail-toggle">
                            <input type="radio"
                                   name="defaultFrom[<?= $i ?>]" <?php checked($from->default != null && $from->default === $from->email) ?>>
                            <?= esc_html__('Default From', 'queue-mail') ?>
                        </label>
                        <p class="description">
                            <?= esc_html__('If checked and have multiple from emails set, this will be the default From Email if the requested From Email is not found.', 'queue-mail') ?>
                        </p>
                    </div>
                </div>
                <div class="queue-mail-row has-auth-<?= $i ?>"
                     id="from-auth-<?= $i ?>_<?= $j ?>" <?= $from->username ? '' : ' style="display:none;"' ?>>
                    <div class="queue-mail-col">
                        <label for="username-<?= $i ?>_<?= $j ?>"><?= esc_html__('SMTP Username', 'queue-mail') ?></label>
                        <input type="text" name="from[<?= $i ?>][<?= $j ?>][username]" id="username-<?= $i ?>_<?= $j ?>"
                               value="<?= esc_attr($from->username) ?>"
                               placeholder="<?= esc_attr_e('leave blank if same as Email', 'queue-mail') ?>">
                    </div>
                    <div class="queue-mail-col">
                        <label for="password-<?= $i ?>_<?= $j ?>"><?= esc_html__('SMTP Password', 'queue-mail') ?></label>
                        <input type="password" name="from[<?= $i ?>][<?= $j ?>][password]" id="password-<?= $i ?>_<?= $j ?>"
                               value="<?= esc_attr($from->password) ?>">
                    </div>
                </div>
                <?php if ($j > 0): ?>
                    <div>
                        <button class="queue-mail-remove-from-btn" type="button" data-id="<?= $i ?>_<?= $j ?>"
                                class="button-primary"><?= esc_html__('Remove From Address', 'queue-mail') ?></button>
                    </div>
                <?php endif; ?>
            </div>
        </td>
    </tr>

    <?php
};