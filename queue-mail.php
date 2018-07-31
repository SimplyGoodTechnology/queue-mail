<?php
/*
Plugin Name:  Queue Mail
Plugin URI:   https://github.com/SimplyGoodTechnology/queue-mail
Description:  Overrides wp_mail() to allow custom SMTP email settings
Version:      1.0
Author:       Simply Good Technology
Author URI:   https://github.com/SimplyGoodTechnology
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 */

namespace SimplyGoodTech\QueueMail;

if (!class_exists('Plugin')) {
    class Plugin
    {
        // TODO uninstall hook - remove settings
        const SLUG = 'queue-mail';
        const VERSION = '1.0';
        public $admin;

        public function __construct()
        {
            spl_autoload_register(function($class) {
                if (substr($class, 0, 24) === 'SimplyGoodTech\QueueMail') {
                    $basename = substr($class, 25);
                    if (in_array($basename, ['From', 'Mailer', 'SMTPMailer', 'PHPMailer'])) {
                        include __DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'Settings.php';
                    } else {
                        include __DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . $basename . '.php';
                    }
                }
            });

            register_uninstall_hook(__FILE__, 'Plugin::uninstall');
            // TODO don't use activation hook as it's broken in multisite and is not always called on upgrade

            // TODO compare VERSION with $settings->version if don't match then run install - may only in admin and/or check every time
            // TODO settings are loaded.

            if (is_admin()) {
                $this->admin = new Admin();
            }
        }

        public static function uninstall()
        {
            error_log('uninstalling ....');
            // TODO remove options and drop tables
        }

    }
}

add_action('plugins_loaded', function () {
    $GLOBALS['QueueMail'] = new Plugin();
}, 100, 0);