<?php
/**
 * Created by PhpStorm.
 * User: darry
 * Date: 9/07/2018
 * Time: 9:22 AM
 */

namespace SimplyGoodTech\QueueMail;

class Server
{
    public $mailer;
    public $fromAddresses = [];

    public function __construct()
    {
        $from = new From();
        $this->fromAddresses[] = $from;
    }

    public function loadFromPost($i)
    {
        $errors = '';

        $fromAddresses = isset($_POST['from'][$i]) ? (is_array($_POST['from'][$i]) ? $_POST['from'][$i] : []) : [];
        foreach ($fromAddresses as $j => $fromAddress) {
            $from = new From();
            $this->fromAddresses[] = $from;

            $from->email = isset($fromAddress[$i][$j]['email']) ? $fromAddress[$i][$j]['email'] : null;
            if ($from->email == '' || !filter_var($from->email, FILTER_VALIDATE_EMAIL)) {
                $errors .= __('Please enter a valid email address', 'queue-mail') . '. ';
            }
            $from->name = filter_var(isset($fromAddress[$i][$j]['name']) ? $fromAddress[$i][$j]['name'] : null, FILTER_SANITIZE_STRING);
            $from->forceEmail = isset($fromAddress[$i][$j]['forceEmail']) ? $fromAddress[$i][$j]['forceEmail'] === '1' : false;
            $from->forceName = isset($fromAddress[$i][$j]['forceName']) ? $fromAddress[$i][$j]['forceName'] === '1' : false;

            $from->defaultAuth = isset($fromAddress[$i][$j]['defaultAuth']) ? $fromAddress[$i][$j]['defaultAuth'] === '1' : false;
            $from->username = isset($fromAddress[$i][$j]['username']) ? $fromAddress[$i][$j]['username'] : null;
            $from->password = isset($fromAddress[$i][$j]['password']) ? $fromAddress[$i][$j]['password'] : null;

            if ($from->defaultAuth && !$from->username) {
                $errors .= __('Please enter the SMTP username', 'queue-mail') . '. ';
            }
            if ($from->defaultAuth && !$from->password) {
                $errors .= __('Please enter the SMTP password', 'queue-mail') . '. ';
            }
        }

        return $errors;
    }
}

class SMTPServer extends Server
{
    public $mailer = 'smtp';
    public $host;
    public $username;
    public $password;
    public $auth = true;
    public $ssl = 'tls';
    public $port = 587;
    public $autoTLS = true;


    public function loadFromPost($i)
    {
        $errors = parent::loadFromPost($i);

        $this->host = isset($_POST['host'][$i]) ? $_POST['host'][$i] : null;
        if ($this->host == '') {
            $errors .= __('Please enter the SMTP server hostname or IP address', 'queue-mail') . '. ';
        }

        $this->ssl = isset($_POST['ssl'][$i]) ? $_POST['ssl'][$i] : 'none';
        if (!in_array($this->ssl, ['tls', 'ssl', 'none'])) {
            $this->ssl = 'tls';
        }

        $this->port = isset($_POST['port'][$i]) ? intval($_POST['port'][$i]) : 0;
        $this->autoTLS = isset($_POST['autoTLS'][$i]) ? $_POST['autoTLS'][$i] === '1' : false;

        $this->auth = isset($_POST['auth'][$i]) ? $_POST['auth'][$i] === '1' : false;
        $this->username = isset($_POST['username'][$i]) ? $_POST['username'][$i] : null;
        $this->password = isset($_POST['password'][$i]) ? $_POST['password'][$i] : null;

        if ($this->auth && !$this->username) {
            $errors .= __('Please enter the SMTP username', 'queue-mail') . '. ';
        }
        if ($this->auth && !$this->password) {
            $errors .= __('Please enter the SMTP password', 'queue-mail') . '. ';
        }


        return $errors;
    }
}

class From
{
    public $email;
    public $name;
    public $forceEmail = true;
    public $forceName = true;
    public $username;
    public $password;
    public $defaultAuth;
}

class Settings
{
    public $servers = [];

    /**
     * Settings constructor.
     *
     * @param \stdClass $settings As an array of stdClass objects
     */
    public function __construct(\stdClass $settings = null)
    {
        if (!is_object($settings)) {
            return;
        }

        foreach ($settings->servers as $setting) {
            $server = self::mkServer($setting->mailer);
            $this->servers[] = $server;
            foreach ($setting as $k => $v) {
                if ($k === 'fromAddresses') {
                    foreach ($setting->fromAddresses as $fromAddress) {
                        $from = new From();
                        $server->fromAddresses[] = $from;
                        foreach ($fromAddress as $k1 => $v1) {
                            $from->$k1 = $v1;
                        }
                    }
                } else {
                    $server->$k = $v;
                }
            }
        }
    }

    public static function mkServer($mailer, $dieOnError = true)
    {
        $server = null;

        switch($mailer) {
            case 'smtp':
                $server = new SMTPServer();
                break;
            case 'php':
                $server = new Server();
                break;
            default:
                if ($dieOnError) {
                    die(__('Failed to find settings class for: ', 'queue-mail') . $mailer);
                }
                break;
        }

        return $server;
    }

    /**
     * Converts settings to a stdClass object.
     *
     * This is to prevent serialization errors if class names and/or the setting structure changes over time.
     * @return \stdClass
     */
    public function toStdClass()
    {
        $settings = new \stdClass();
        $settings->servers = [];

        foreach ($this->servers as $server) {
            $obj = new \stdClass();
            $settings->servers[] = $obj;
            foreach ($server as $k => $v) {
                if ($k === 'fromAddresses') {
                    $obj->$k = [];
                    foreach ($v as $fromAddress) {
                        $from = new \stdClass();
                        $obj->$k[] = $from;
                        foreach ($fromAddress as $k1 => $v1) {
                            $from->$k1 = $v1;
                        }
                    }
                } else {
                    $obj->$k = $v;
                }
            }
        }

        return $settings;
    }

    public function loadFromPost()
    {
        $errors = null;
        // TODO wordpress has some sanitizing functions etc.
        // TODO validate and load sanitized  $settigns from $_POST, If not valid don't save and send back error msg.
        error_log(print_r($_POST, true));

        $mailers = isset($_POST['mailers']) ? $_POST['mailers'] : [];
        if (!is_array($mailers) || count($mailers) === 0) {
            $errors .= __('Failed to find any mailers?', 'queue-mail');
        } else {

            foreach ($mailers as $i) {
                $mailer = isset($_POST['mailer'][$i]) ? $_POST['mailer'][$i] : null;
                $server = self::mkServer($mailer, $dieOnError = false);
                if ($server === null) {
                    $errors .= __('Unknown mailer: ', 'queue-mail') . $mailer . '. ';
                } else {
                    $this->servers[] = $server;
                    $errors .= $server->loadFromPost($i);
                }
            }
        }

        return $errors;
    }
}