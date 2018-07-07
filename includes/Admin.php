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
    public $settings;
    private $optionName;

    public function __construct()
    {
        $this->optionName = Plugin::SLUG . '-settings';
        add_action('admin_menu', [$this, 'addSettingsPage']);
    }

    public function addSettingsPage()
    {
        wp_enqueue_style(Plugin::SLUG . '-admin', plugin_dir_url(__DIR__) . '/css/admin.css', [], Plugin::VERSION);

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
            $server->host = null;
            $server->ssl = null;
            $server->autoTLS = true;
            $server->auth = true;

            $settings->servers[] = $server;

            $user = new \stdClass();
            $user->username = null;
            $user->password = null;
            $user->fromEmail = null;
            $user->fromName = null;
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