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

if( ! function_exists( 'wp_mail' ) ) {
    function wp_mail($to, $subject, $message, $headers = '', $attachments = [])
    {
        $GLOBALS['QueueMail']::getQueueMail()->mail($to, $subject, $message, $headers, $attachments);
    }
}

if (!class_exists('Plugin')) {
    class Plugin
    {
        const SLUG = 'queue-mail';
        const VERSION = '1.0';
        const MAILERS = ['SimplyGoodTech\QueueMail\SMTPMailer', 'SimplyGoodTech\QueueMail\PHPMailer'];
        public $admin;

        public function __construct()
        {
            spl_autoload_register(function($class) {
                if (substr($class, 0, 24) === 'SimplyGoodTech\QueueMail') {
                    $basename = substr($class, 25);
                    include __DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . $basename . '.php';
                }
            });

            register_uninstall_hook(__FILE__, 'Plugin::uninstall');
            // TODO set up activation hook as, but check as it's broken in multisite and is not always called on upgrade

            if (is_admin()) {
                $this->admin = new Admin();
            }
        }

        public static function getQueueMail()
        {
            return new QueueMail(get_option(Plugin::SLUG . '-settings'));
        }

        public static function uninstall()
        {
            error_log('uninstalling ....');
            // TODO remove options and drop tables, make sure to check if multisite and do all sites.
        }

    }
}

add_action('plugins_loaded', function () {
    $GLOBALS['QueueMail'] = new Plugin();
}, 100, 0);