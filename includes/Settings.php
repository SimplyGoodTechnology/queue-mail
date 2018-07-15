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
    public function __construct()
    {
        $from = new From();
        $this->fromAddresses[] = $from;
    }

    public $mailer;
    public $host;
    public $auth;
    public $username;
    public $password;
    public $fromAddresses = [];
}

class SMTPServer extends Server
{
    public $mailer = 'smtp';
    public $auth = true;
    public $ssl = 'tls';
    public $port = 587;
    public $autoTLS = true;
}

class From
{
    public $email;
    public $name;
    public $forceEmail = true;
    public $forceName = true;
    public $username;
    public $password;
    public $default;
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
            $settings['servers'] = $obj;
            foreach ($server as $k => $v) {
                if ($k === 'fromAddresses') {
                    $obj->$k = [];
                    foreach ($v as $fromAddress) {
                        $from = new \stdClass();
                        $from->$k[] = $from;
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
                    // TODO $errors .= $server->loadFromPost();
                }
            }
        }

        return $errors;
    }
}