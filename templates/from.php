<?php
namespace SimplyGoodTech\QueueMail;

$renderer = function(From $from, $i, $j) {
    ?>
    <tr class="queue-mail-from-row" id="queue-mail-from-container-<?= $i ?>_<?= $j ?>">
        <th scope="row">
            <label><?= esc_html__('From', 'queue-mail') ?></label>
        </th>
        <td>
            <div>
                <div class="queue-mail-row">
                    <div class="queue-mail-col">
                        <label for="fromEmail-<?= $i ?>_<?= $j ?>"><?= esc_html__('Email', 'queue-mail') ?>
                            *</label>
                        <input type="email" name="from[<?= $i ?>][<?= $j ?>][email]" id="fromEmail-<?= $i ?>_<?= $j ?>"
                               value="<?= esc_attr($from->email) ?>">
                        <div class="queue-mail-force-email-container">
                        <label class="queue-mail-toggle">
                            <input type="checkbox" value="1" class="queue-mail-force-email"
                                   name="from[<?= $i ?>][<?= $j ?>][forceEmail]" <?php checked($from->forceEmail) ?>>
                            <?= esc_html__('Force From Email', 'queue-mail') ?>
                        </label>
                        <p class="description">
                            <?= esc_html__('If checked, the From Email above will be used for all emails, ignoring values set by other plugins.', 'queue-mail') ?>
                        </p>
                        </div>
                    </div>
                    <div class="queue-mail-col">
                        <label for="fromName-<?= $i ?>_<?= $j ?>"><?= esc_html__('Name', 'queue-mail') ?></label>
                        <input type="text" name="from[<?= $i ?>][<?= $j ?>][name]" id="fromName-<?= $i ?>_<?= $j ?>"
                               value="<?= esc_attr($from->name) ?>">
                        <div class="queue-mail-force-email-container">
                        <label class="queue-mail-toggle">
                            <input type="checkbox" value="1" class="queue-mail-force-name"
                                   name="from[<?= $i ?>][<?= $j ?>][forceName]" <?php checked($from->forceName) ?>>
                            <?= esc_html__('Force From Name', 'queue-mail') ?>
                        </label>
                        <p class="description">
                            <?= esc_html__('If checked, the From Name above will be used for all emails, ignoring values set by other plugins.', 'queue-mail') ?>
                        </p>
                        </div>
                    </div>
                </div>
                <div class="queue-mail-row queue-mail-default-email-container">
                    <div class="queue-mail-col">
                        <label class="queue-mail-toggle">
                            <input type="checkbox" value="1" class="queue-mail-is-default-email"
                                   name="from[<?= $i ?>][<?= $j ?>][isDefaultEmail]" <?php checked($from->isDefaultEmail) ?>>
                            <?= esc_html__('Default From Email', 'queue-mail') ?>
                        </label>
                        <p class="description">
                            <?= esc_html__('If checked and have multiple from emails set, this will be the default From Email if the requested From Email is not found.', 'queue-mail') ?>
                        </p>
                    </div>
                    <div class="queue-mail-col">
                        <label class="queue-mail-toggle">
                            <input type="checkbox" value="1" class="queue-mail-is-default-name"
                                   name="from[<?= $i ?>][<?= $j ?>][isDefaultName]" <?php checked($from->isDefaultName) ?>>
                            <?= esc_html__('Default From Name', 'queue-mail') ?>
                        </label>
                        <p class="description">
                            <?= esc_html__('If checked and have multiple from emails set, this will be the default From Name if the requested From Email is not found.', 'queue-mail') ?>
                        </p>
                    </div>
                </div>
                <div>
                    <label style="font-weight: bold"><?= esc_html__('Authentication', 'queue-mail') ?>: </label>
                    <label class="queue-mail-switch">
                        <<input type="checkbox" id="defaultAuth-<?= $i ?>_<?= $j ?>" value="1"
                                name="from[<?= $i ?>][<?= $j ?>][auth]" <?php checked($from->auth) ?>>
                        <span class="queue-mail-slider"></span>
                    </label>
                    <p class="description" style="margin-bottom: 5px;">
                    <label><?= esc_html__('Is authentication required for this email address?', 'queue-mail') ?></label>
                    </p>
                </div>
                <div class="queue-mail-row has-from-auth-<?= $i ?>"
                     id="from-auth-<?= $i ?>_<?= $j ?>" <?= $from->auth ? '' : ' style="display:none;"' ?>>
                    <div class="queue-mail-col">
                        <label for="username-<?= $i ?>_<?= $j ?>"><?= esc_html__('Username', 'queue-mail') ?></label>
                        <input type="text" name="from[<?= $i ?>][<?= $j ?>][username]" id="username-<?= $i ?>_<?= $j ?>"
                               data-parsley-error-message="<?= esc_attr($from->getMessage('usernameError')) ?>"
                               value="<?= esc_attr($from->username) ?>"
                               placeholder="<?= esc_attr_e('leave blank if same as Email', 'queue-mail') ?>">
                    </div>
                    <div class="queue-mail-col">
                        <label for="password-<?= $i ?>_<?= $j ?>"><?= esc_html__('Password', 'queue-mail') ?></label>
                        <input type="password" name="from[<?= $i ?>][<?= $j ?>][password]" id="password-<?= $i ?>_<?= $j ?>"
                               data-parsley-error-message="<?= esc_attr__($from->getMessage('passwordError'), 'queue-mail') ?>"
                               value="<?= esc_attr($from->password) ?>">
                    </div>
                </div>
                    <div>
                        <button class="queue-mail-remove-from-btn button-secondary" type="button" data-id="<?= $i ?>_<?= $j ?>">
                            <?= esc_html__('Remove From Address', 'queue-mail') ?></button>
                    </div>
            </div>
        </td>
    </tr>

    <?php
};