<?php
/**
 * Created by PhpStorm.
 * User: darry
 * Date: 9/07/2018
 * Time: 9:22 AM
 */

namespace SimplyGoodTech\QueueMail;

class Mailer
{
    public static $types = ['smtp' => 'SMTP', 'php' => 'PHP mail()'];
    public $sendErrors = false;
    public $background = false;
    public $fromAddresses = [];

    private static $isDefaultEmailSet = false;
    private static $isDefaultNameSet = false;

    public function __construct()
    {
    }


    public static function mk($type)
    {
        $mailer = null;

        switch ($type) {
            case 'smtp':
                $mailer = new SMTPMailer();
                break;
            case 'php':
            default:
                $mailer = new PHPMailer();
                break;
        }

        return $mailer;
    }

    public function getType()
    {
        return strtolower(substr(basename(get_class($this)), 0, -6));
    }

    public function loadFromPost($i)
    {
        $isValid = true;
        $this->sendErrors = isset($_POST['sendErrors']) && $_POST['sendErrors'] == $i ? true : false;
        $this->background = isset($_POST['background'][$i]) && intval($_POST['background'][$i]) === 1 ? true : false;
        $fromAddresses = isset($_POST['from'][$i]) ? (is_array($_POST['from'][$i]) ? $_POST['from'][$i] : []) : [];

        //error_log(print_r($fromAddresses, true));
        foreach ($fromAddresses as $j => $fromAddress) {
            $from = new From();
            $this->fromAddresses[] = $from;

            $from->email = isset($fromAddress['email']) ? $fromAddress['email'] : null;
            if ($from->email == '' || !filter_var($from->email, FILTER_VALIDATE_EMAIL)) {
                $isValid = false;
            }
            $from->name = filter_var(isset($fromAddress['name']) ? $fromAddress['name'] : null, FILTER_SANITIZE_STRING);
            $from->forceEmail = isset($fromAddress['forceEmail']) ? $fromAddress['forceEmail'] === '1' : false;
            $from->forceName = isset($fromAddress['forceName']) ? $fromAddress['forceName'] === '1' : false;

            if (!self::$isDefaultEmailSet) {
                $from->isDefaultEmail = isset($fromAddress['isDefaultEmail']) ? $fromAddress['isDefaultEmail'] === '1' : false;
            }
            if ($from->isDefaultEmail) {
                self::$isDefaultEmailSet = true;
            }
            if (!self::$isDefaultNameSet) {
                $from->isDefaultName = isset($fromAddress['isDefaultName']) ? $fromAddress['isDefaultName'] === '1' : false;
            }
            if ($from->isDefaultName) {
                self::$isDefaultNameSet = true;
            }

            $from->auth = isset($fromAddress['auth']) ? $fromAddress['auth'] === '1' : false;
            $from->username = isset($fromAddress['username']) ? $fromAddress['username'] : null;
            $from->password = isset($fromAddress['password']) ? $fromAddress['password'] : null;

            if ($from->auth && !$from->username) {
                $isValid = false;
            }
            if ($from->auth && !$from->password) {
                $isValid = false;
            }
        }

        return $isValid;
    }
}

class PHPMailer extends Mailer
{
}

class SMTPMailer extends Mailer
{
    public $host;
    public $username;
    public $password;
    public $auth = true;
    public $ssl = 'tls';
    public $port = 587;
    public $autoTLS = true;


    public function loadFromPost($i)
    {
        $isValid = parent::loadFromPost($i);

        $this->host = isset($_POST['host'][$i]) ? $_POST['host'][$i] : null;
        if ($this->host == '') {
            $isValid = false;
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
            $isValid = false;
        }
        if ($this->auth && !$this->password) {
            $isValid = false;
        }

        return $isValid;
    }
}

class From
{
    public $email;
    public $name;
    public $forceEmail = false;
    public $forceName = false;
    public $username;
    public $password;
    public $auth = false;
    public $isDefaultEmail = false;
    public $isDefaultName = false;
}

class Settings
{
    public $version;
    public $mailers = [];
    public $sendErrorsTo;
    public $logging = true;
    public $logLevel = 'error';
    public $logPeriod = 12;

    private $errorMsg;
    private $vars = ['sendErrorsTo', 'logging', 'logLevel', 'logPeriod'];

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

        foreach ($this->vars as $var) {
            $this->$var = $settings->$var;
        }

        foreach ($settings->mailers as $obj) {
            $mailer = Mailer::mk($obj->type);
            $this->mailers[] = $mailer;
            foreach ($obj as $k => $v) {
                if ($k === 'fromAddresses') {
                    foreach ($obj->fromAddresses as $fromAddress) {
                        $from = new From();
                        $mailer->fromAddresses[] = $from;
                        foreach ($fromAddress as $k1 => $v1) {
                            $from->$k1 = $v1;
                        }
                    }
                } elseif ($k !== 'mailer') {
                    $mailer->$k = $v;
                }
            }
        }
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
        foreach ($this->vars as $var) {
            $settings->$var = $this->$var;
        }
        $settings->mailers = [];

        foreach ($this->mailers as $mailer) {
            $obj = new \stdClass();
            $obj->type = $mailer->getType();
            $settings->mailers[] = $obj;
            foreach ($mailer as $k => $v) {
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
        error_log(print_r($_POST, true));
        $this->errorMsg = null;
        $isValid = true;
        $mailers = isset($_POST['mailers']) ? $_POST['mailers'] : [];
        if (!is_array($mailers) || count($mailers) === 0) {
            $this->errorMsg .= __('Failed to find any mailers?', 'queue-mail');
            $isValid = false;
        } else {
            $this->logging = isset($_POST['logging']) && $_POST['logging'] === '1' ? true : false;
            $this->logLevel = isset($_POST['logLevel']) && $_POST['logLevel'] === 'all' ? 'all' : 'error';
            $this->logPeriod = isset($_POST['logPeriod']) ? floatval($_POST['logPeriod']) : 12;

            $this->sendErrorsTo = [];
            $sendErrorsTo = isset($_POST['sendErrorsTo']) ? explode(',', $_POST['sendErrorsTo']) : [];
            foreach ($sendErrorsTo as $address) {
                $address = trim($address);
                if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
                    $isValid = false;
                } else {
                    $this->sendErrorsTo[] = $address;
                }
            }
            foreach ($mailers as $i) {
                $mailer = isset($_POST['mailer'][$i]) ? $_POST['mailer'][$i] : null;
                $mailer = Mailer::mk($mailer);
                if ($mailer === null) {
                    $this->errorMsg .= __('Unknown mailer: ', 'queue-mail') . $mailer . '. ';
                    $isValid = false;
                } else {
                    $this->mailers[] = $mailer;
                    if (!$mailer->loadFromPost($i)) {
                        $isValid = false;
                    }
                }
            }
        }

        if (!$isValid) {
            $this->errorMsg .= __('There are errors, please see below.', 'queue-mail');
        }

        return $isValid;
    }

    public function getErrorMsg()
    {
        return $this->errorMsg;
    }
}