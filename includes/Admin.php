<?php
/**
 * Created by PhpStorm.
 * User: darry
 * Date: 7/07/2018
 * Time: 7:38 AM
 */

namespace SimplyGoodTech\QueueMail;


class Admin
{
    public $settingsSaved = false;
    public $mailers = ['smtp' => 'SMTP', 'php' => 'PHP mail()'];
    public $settings;
    private $optionName;

    public function __construct()
    {
        $this->optionName = Plugin::SLUG . '-settings';

        add_action( 'admin_enqueue_scripts', function() {
            wp_enqueue_style(Plugin::SLUG . '-admin-css', plugin_dir_url(__DIR__) . 'css/admin.css', [], Plugin::VERSION);
            wp_enqueue_script(Plugin::SLUG . '-admin-js', plugin_dir_url(__DIR__) . 'js/admin.js', ['jquery'], Plugin::VERSION, true);
        });

        add_action('admin_menu', [$this, 'addSettingsPage']);

        add_action( 'wp_ajax_queue_mail_get_mailer_settings', [$this, 'getMailerSettings']);
    }

    public function getMailerSettings()
    {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized user');
        }

        $mailer = isset($_GET['mailer']) && isset($this->mailers[$_GET['mailer']]) ? $_GET['mailer'] : '';
        $i = isset($_GET['id']) ? intval($_GET['id']) : 0;

        $serverSettingsTemplate = plugin_dir_path(__DIR__) . 'templates/' . $mailer . '.php';
        if (is_file($serverSettingsTemplate)) {
            include $serverSettingsTemplate;
        }

        wp_die();
    }

    public function addSettingsPage()
    {
        $page = add_options_page(
            __('Queue Mail', Plugin::SLUG),
            __('Queue Mail', Plugin::SLUG),
            'manage_options',
            Plugin::SLUG,
            function() {
                $this->loadSettings();
                include plugin_dir_path(__DIR__) . '/templates/settings.php';
            }
        );

        add_action('load-' . $page, [$this, 'initSettingsPage']);
    }


    public function initSettingsPage()
    {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized user');
        }

        if (isset($_POST['submit'])) {
            error_log(print_r($_POST, true));
            //$this->saveSettings();
        }

    }

    private function loadSettings()
    {
        $settings = get_option($this->optionName);
        if (!$settings) {
            $settings = new \stdClass();
        }

        if (!isset($settings->servers)) {
            $settings->servers = [];
        }

        if (count($settings->servers) === 0) {
            $server = new \stdClass();
            $server->mailer = 'smtp';
            $server->host = null;
            $server->ssl = 'tls';
            $server->port = 587;
            $server->autoTLS = true;
            $server->auth = true;
            $server->username = null;
            $server->password = null;
            $server->defaultFrom = null;

            $settings->servers[] = $server;

            $user = new \stdClass();
            $user->username = null;
            $user->password = null;
            $user->fromEmail = null;
            $user->forceFrom = true;
            $user->fromName = null;
            $user->forceName = true;
            $user->setReplyTo = false;

            $server->users = [];
            $server->users[] = $user;
        }

        $this->settings = $settings;
    }

    private function saveSettings()
    {
        check_admin_referer('wpshout_option_page_example_action');
        // TODO wordpress has some sanitizing functions etc.
        // TODO show wordpress saved alert
        $settings = new \stdClass();
        $settings->test = $_POST['awesome_text'];
        update_option($this->optionName, $settings);
        $this->settingsSaved = true;
    }
}