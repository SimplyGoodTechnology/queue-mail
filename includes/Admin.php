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
    // TODO consider moving to Settings class
    public $mailers = ['smtp' => 'SMTP', 'php' => 'PHP mail()'];
    public $settings;

    private $optionName;
    private $mailerRenders = [];
    private $formRenderer;

    public function __construct()
    {
        $this->optionName = Plugin::SLUG . '-settings';

        add_action( 'admin_enqueue_scripts', function() {
            wp_enqueue_style(Plugin::SLUG . '-admin-css', plugin_dir_url(__DIR__) . 'css/admin.css', [], Plugin::VERSION);
            wp_enqueue_script(Plugin::SLUG . '-admin-js', plugin_dir_url(__DIR__) . 'js/admin.js', ['jquery'], Plugin::VERSION, true);
        });

        add_action('admin_menu', [$this, 'addSettingsPage']);

        add_action( 'wp_ajax_queue_mail_get_mailer_form', [$this, 'getMailerForm']);
        add_action( 'wp_ajax_queue_mail_get_from_form', [$this, 'getFromForm']);
    }

    public function getMailerRenderer($mailer)
    {
        if (!isset($this->mailerRenders[$mailer])) {
            $this->mailerRenders[$mailer] = $this->getRenderer($mailer);
        }
        return $this->mailerRenders[$mailer];
    }

    private function getRenderer($template)
    {
        $renderer = null;
        $file = plugin_dir_path(__DIR__) . 'templates/' . $template . '.php';
        if (is_file($file)) {
            include $file;
        }
        if ($renderer === null) {
            die('Failed to load template for ' . $template);
        }
        return $renderer;
    }

    public function getFromRenderer()
    {
        if ($this->formRenderer === null) {
            $this->formRenderer = $this->getRenderer('from');
        }
        return $this->formRenderer;
    }

    public function getMailerForm()
    {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized user');
        }

        $mailer = isset($_GET['mailer']) && isset($this->mailers[$_GET['mailer']]) ? $_GET['mailer'] : '';
        $i = isset($_GET['id']) ? intval($_GET['id']) : 0;

        $this->getMailerRenderer($mailer)(Settings::mkServer($mailer), $i);

        wp_die();
    }

    public function getFromForm()
    {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized user');
        }

        $i = isset($_GET['i']) ? intval($_GET['i']) : 0;
        $j = isset($_GET['j']) ? intval($_GET['j']) : 0;
        $auth = isset($_GET['auth']) ? intval($_GET['auth']) : 0;

        $this->getFromRenderer()(new From(), $i, $j, $auth === 1 ? true: false);

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
        $this->settings = new Settings(get_option($this->optionName));
        if (count($this->settings->servers) === 0) {
            $this->settings->servers[] = new SMTPServer();
        }
    }

    private function saveSettings()
    {
        check_admin_referer('wpshout_option_page_example_action');
        // TODO wordpress has some sanitizing functions etc.
        // TODO show wordpress saved alert
        $settings = new Settings();
        // TODO validate and load sanitized  $settigns from $_POST, If not valid don't save and send back error msg.

        update_option($this->optionName, $settings->toArray());
        $this->settingsSaved = true;
    }
}