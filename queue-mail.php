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
                    $basename = substr($class, 24);
                    if ($basename === '\From') {
                        include __DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'Settings.php';
                    } else {
                        include __DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . $basename . '.php';
                    }
                }
            });

            if (is_admin()) {
                $this->admin = new Admin();
            }
        }

    }
}

add_action('plugins_loaded', function () {
    $GLOBALS['QueueMail'] = new Plugin();
}, 100, 0);