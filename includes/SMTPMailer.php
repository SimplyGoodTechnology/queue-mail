<?php
namespace SimplyGoodTech\QueueMail;


class SMTPMailer extends PHPMailer
{
    public static $label = 'SMTP';
    public $host;
    public $username;
    public $password;
    public $auth = true;
    public $ssl = 'tls';
    public $port = 587;
    public $autoTLS = true;

    public static function getIconUrl()
    {
        return plugin_dir_url(__DIR__) . 'images/smtp.png';
    }

    protected function initMailer()
    {
        self::$phpMailer->isSMTP();
        self::$phpMailer->Host = $this->host;
        self::$phpMailer->port = $this->port;
        if ($this->auth) {
            self::$phpMailer->SMTPAuth = true;
            self::$phpMailer->Username = $this->username;
            self::$phpMailer->Password = $this->password;
        }

        self::$phpMailer->Host = $this->host;
        self::$phpMailer->Host = $this->host;
        self::$phpMailer->SMTPSecure = $this->ssl === 'none' ? null : $this->ssl;

        // TODO add auto TLS option
    }

    public function getText($k)
    {
        static $messages = null;

        if ($messages === null) {
            $messages = [
                'hostError' => _('Please enter the SMTP server hostname or IP address', 'queue-mail'),
                'usernameError' => __('Please enter a username', 'queue-mail'),
                'passwordError' => __('Please enter a password', 'queue-mail'),
            ];
        }

        return isset($messages[$k]) ? $messages[$k] : null;
    }

    public function loadFromPost($i)
    {
        $isValid = parent::loadFromPost($i);

        $this->host = isset($_POST['host'][$i]) ? $_POST['host'][$i] : null;
        if ($this->host == '') {
            $isValid = false;
            $this->errors['host'] = $this->getText('hostError');
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
            $this->errors['username'] = $this->getText('usernameError');
        }
        if ($this->auth && !$this->password) {
            $isValid = false;
            $this->errors['password'] = $this->getText('passwordError');
        }

        return $isValid;
    }


}
