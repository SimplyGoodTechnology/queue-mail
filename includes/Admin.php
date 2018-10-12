<?php

namespace SimplyGoodTech\QueueMail;


// TODO if isAdmin() and there are no valid mailer configs then should show a warning on Dashboard etc
class Admin
{
    public $settingsSaved = false;
    /** @var QueueMail */
    public $queueMail;
    public $mailers;
    public $error;

    private $optionName;
    private $mailerRenderer;
    private $mailerRenders = [];
    private $formRenderer;

    public function __construct()
    {
        $this->optionName = Plugin::SLUG . '-settings';
        Mailer::$types = apply_filters('queue_mail_available_mailers', Plugin::MAILERS);

        add_action('admin_enqueue_scripts', function () {
            wp_enqueue_style(Plugin::SLUG . '-admin-css', plugin_dir_url(__DIR__) . 'css/admin.css', [], Plugin::VERSION);
            wp_enqueue_script(Plugin::SLUG . '-admin-js', plugin_dir_url(__DIR__) . 'js/admin.js', ['jquery', Plugin::SLUG . '-admin-parsley'], Plugin::VERSION, true);
            wp_enqueue_script(Plugin::SLUG . '-admin-parsley', plugin_dir_url(__DIR__) . 'js/parsley.min.js', ['jquery'], Plugin::VERSION, true);
            wp_localize_script(Plugin::SLUG . '-admin-js', 'queueMailAdminStrings', array(
                'confirmRemoveFromAddress' => esc_html__('Do you really want to remove this From Address?', 'queue-mail'),
                'confirmRemoveMailer' => esc_html__('Do you really want to remove this Mailer?', 'queue-mail'),
            ));
        });

        add_action('admin_menu', [$this, 'addSettingsPage']);

        add_action('wp_ajax_queue_mail_get_mailer_form', [$this, 'getMailerForm']);
        add_action('wp_ajax_queue_mail_get_mailer_sub_form', [$this, 'getMailerSubForm']);
        add_action('wp_ajax_queue_mail_get_from_form', [$this, 'getFromForm']);
        add_action('wp_ajax_queue_mail_send_test_email', [$this, 'sendTestEmail']);
    }

    public function sendTestEmail()
    {
        $to = isset($_GET['to']) ? $_GET['to'] : null;
        $from = isset($_GET['from']) ? $_GET['from'] : null;

        $queueMail = Plugin::getQueueMail();

        $failed = false;
        $log = '';
        $queueMail->setDebug(true, 4, function ($str, $level) use (&$log) {
            $log .= $str . "\n";
        });

        add_action('wp_mail_failed', function ($wp_error) use (&$log, &$failed) {
            $failed = true;
            error_log(print_r($wp_error, true));
        }, 10, 1);


        $queueMail->mail($to, __('Queue Mail Test Mesage', 'queue-mail'), __('Testing 1 2 3 ...', 'queue-mail'),
            $from !== null ? [$from] : '');
        if ($failed) {
            $log .= __('Message Failed to Send', 'queue-mail');
        } else {
            $log .= __('Message Successfully Sent', 'queue-mail');
        }

        $log = esc_html($log);

        wp_send_json(['failed' => $failed, 'log' => nl2br($log)]);
    }

    public function addSettingsPage()
    {
        $page = add_options_page(
            __('Queue Mail', 'queue-mail'),
            __('Queue Mail', 'queue-mail'),
            'manage_options',
            Plugin::SLUG,
            function () {
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
            $this->saveSettings();
        } else {
            $this->loadSettings();
        }

    }

    private function loadSettings()
    {
        error_log('loadSettings');
        //error_log('loadSettings');
        $this->queueMail = Plugin::getQueueMail();
        error_log(print_r($this->queueMail, true));
        if ($this->queueMail->version === null) {
            // TODO install
            $this->queueMail = new QueueMail();
        } else {
            // TODO compare VERSION with $settings->version if don't match then run update
        }
        //$this->queueMail = new QueueMail(); //TODO testing remove later
        if (count($this->queueMail->mailers) === 0) {
            $mailer = new SMTPMailer();
            $mailer->fromAddresses[] = new From();
            $this->queueMail->mailers[] = $mailer;
        }
    }

    private function saveSettings()
    {
        //error_log('saveSettings');
        check_admin_referer('queue_mail_option_page_save_settings_action');

        $this->queueMail = new QueueMail();
        $this->queueMail->version = Plugin::VERSION;

        $this->error = null;
        if (!$this->queueMail->loadFromPost()) {
            $this->error .= $this->queueMail->getErrorMsg();
        }
        $settings = $this->queueMail->toArray();

        $filterSettings = new \stdClass();
        $filterSettings->settings = $settings;
        $filterSettings->errors = null;
        if (!apply_filters('queue_mail_save_settings', $filterSettings)) {
            $this->error .= $filterSettings->errors;
        }

        if ($this->error) {
            return;
        }

        update_option($this->optionName, $settings);
        $this->settingsSaved = true;
    }

    // TODO this is broken as $mailerType should be templateFile according to line 194, but mailerType according to other useages ???
    public function getMailerRenderer($mailerType)
    {
        if (!isset($this->mailerRenders[$mailerType])) {
            $this->mailerRenders[$mailerType] = $this->getRenderer($mailerType);
        }
        return $this->mailerRenders[$mailerType];
    }

    private function getRenderer($file)
    {
        // Note to Self: using anonymous functions for templates to get local scope and to allow them to be used in loops.
        $renderer = null;
        if (is_file($file)) {
            include $file;
        }
        if ($renderer === null) {
            error_log('Failed to load template for ' . $file);
            die();
        }
        return $renderer;
    }

    public function getFromRenderer()
    {
        if ($this->formRenderer === null) {
            $this->formRenderer = $this->getRenderer(plugin_dir_path(__DIR__) . 'templates/from.php');
        }
        return $this->formRenderer;
    }

    public function getMailerBaseRenderer()
    {
        if ($this->mailerRenderer === null) {
            $this->mailerRenderer = $this->getRenderer(plugin_dir_path(__DIR__) . 'templates/mailer.php');
        }
        return $this->mailerRenderer;
    }

    public function renderMailer(Mailer $mailer, $i)
    {
        $this->getMailerBaseRenderer()($mailer, $this->getMailerRenderer($mailer->getSettingsTemplate()), $this->getFromRenderer(), $i);
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

        $mailerType = isset($_GET['mailer']) ? stripslashes($_GET['mailer']) : '';
        if (!in_array($mailerType, Mailer::$types)) {
            error_log('Unknown mailer type: ' . $mailerType);
            die();
        }
        $i = isset($_GET['id']) ? intval($_GET['id']) : 0;

        // TODO this is broken what is the real param for getMailerRenderer ???
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
}