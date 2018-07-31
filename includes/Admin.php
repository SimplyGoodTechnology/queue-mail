<?php
/**
 * Created by PhpStorm.
 * User: darry
 * Date: 7/07/2018
 * Time: 7:38 AM
 */

namespace SimplyGoodTech\QueueMail;


// TODO if isAdmin() and there are no valid mailer configs then should show a warning on Dashboard etc
class Admin
{
    public $settingsSaved = false;
    /** @var Settings */
    public $settings;
    public $error;

    private $optionName;
    private $mailerRenderer;
    private $mailerRenders = [];
    private $formRenderer;

    public function __construct()
    {
        $this->optionName = Plugin::SLUG . '-settings';

        add_action('admin_enqueue_scripts', function () {
            wp_enqueue_style(Plugin::SLUG . '-admin-css', plugin_dir_url(__DIR__) . 'css/admin.css', [], Plugin::VERSION);
            wp_enqueue_script(Plugin::SLUG . '-admin-js', plugin_dir_url(__DIR__) . 'js/admin.js', ['jquery', Plugin::SLUG . '-admin-parsley'], Plugin::VERSION, true);
            wp_enqueue_script(Plugin::SLUG . '-admin-parsley', plugin_dir_url(__DIR__) . 'js/parsley.min.js', ['jquery'], Plugin::VERSION, true);
            wp_localize_script(Plugin::SLUG . '-admin-js', 'queueMailAdminStrings', array(
                'confirmRemoveFromAddress' => esc_html__('Do you really want to remove this From Address?', 'queue-mail'),
                'confirmRemoveMailer' => esc_html__('Do you really want to remove this Mailer?', 'queue-mail'),
            ));
        });

        // TODO consider renaming addSettingsPage to addSettingsMenu
        add_action('admin_menu', [$this, 'addSettingsPage']);

        add_action('wp_ajax_queue_mail_get_mailer_form', [$this, 'getMailerForm']);
        add_action('wp_ajax_queue_mail_get_mailer_sub_form', [$this, 'getMailerSubForm']);
        add_action('wp_ajax_queue_mail_get_from_form', [$this, 'getFromForm']);
    }

    public function getMailerRenderer($mailerType)
    {
        if (!isset($this->mailerRenders[$mailerType])) {
            $this->mailerRenders[$mailerType] = $this->getRenderer($mailerType);
        }
        return $this->mailerRenders[$mailerType];
    }

    private function getRenderer($template)
    {
        // Note to Self: using anonymous functions for templates to get local scope and to allow them to be used in loops.
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

    public function getMailerBaseRenderer()
    {
        if ($this->mailerRenderer === null) {
            $this->mailerRenderer = $this->getRenderer('mailer');
        }
        return $this->mailerRenderer;
    }

    public function renderMailer(Mailer $mailer, $i)
    {
        $this->getMailerBaseRenderer()($mailer, $this->getMailerRenderer($mailer->getType()), $this->getFromRenderer(), $i);
    }

    public function getMailerForm()
    {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized user');
        }

        $i = isset($_GET['i']) ? intval($_GET['i']) : 0;

        $mailer = new SMTPMailer();
        $mailer->fromAddresses[] = new From();

        $this->renderMailer($mailer, $i);

        wp_die();
    }

    public function getMailerSubForm()
    {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized user');
        }

        $mailerType = isset($_GET['mailer']) && isset(Mailer::$types[$_GET['mailer']]) ? $_GET['mailer'] : '';
        $i = isset($_GET['id']) ? intval($_GET['id']) : 0;

        $this->getMailerRenderer($mailerType)(Mailer::mk($mailerType), $i);

        wp_die();
    }

    public function getFromForm()
    {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized user');
        }

        $i = isset($_GET['i']) ? intval($_GET['i']) : 0;
        $j = isset($_GET['j']) ? intval($_GET['j']) : 0;

        $this->getFromRenderer()(new From(), $i, $j);

        wp_die();
    }


    public function addSettingsPage()
    {
        $page = add_options_page(
            __('Queue Mail', 'queue-mail'),
            __('Queue Mail', 'queue-mail'),
            'manage_options',
            Plugin::SLUG,
            function () {
                if (!$this->error) {
                    $this->loadSettings();
                }
                include plugin_dir_path(__DIR__) . '/templates/settings.php';
            }
        );

        add_action('load-' . $page, [$this, 'initSettingsPage']);
    }


    public function initSettingsPage()
    {
        //error_log('initSettingsPage');
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized user');
        }

        if (isset($_POST['submit'])) {
            $this->saveSettings();
        }

    }

    private function loadSettings()
    {
        //error_log('loadSettings');
        $this->settings = new Settings(get_option($this->optionName));
        //$this->settings = new Settings();
        if (count($this->settings->mailers) === 0) {
            $mailer = new SMTPMailer();
            $mailer->fromAddresses[] = new From();
            $this->settings->mailers[] = $mailer;
        }
    }

    private function saveSettings()
    {
        //error_log('saveSettings');
        check_admin_referer('queue_mail_option_page_save_settings_action');

        $this->settings = new Settings();
        if (!$this->settings->loadFromPost()) {
            $this->error = $this->settings->getErrorMsg();
            // TODO if have errors then trigger a parsley validation check;
            return;
        }

        update_option($this->optionName, $this->settings->toStdClass());
        $this->settingsSaved = true;
    }
}