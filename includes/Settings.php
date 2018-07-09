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

    // TODO add note to self about doing this to prevent serialization errors if classnames change in settings etc
    public function __construct($settings = [])
    {
        // TODO init from $settings
    }

    public static function mkServer($mailer)
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
                die('Failed to find settings class for ' . $mailer);
                break;
        }

        return $server;
    }

    public function toArray()
    {
        // TODO convert $this to an array
        $settings = [];

        return $settings;
    }
}