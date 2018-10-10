<?php

namespace SimplyGoodTech\QueueMail;

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
    private static $isDefaultEmailSet = false;
    private static $isDefaultNameSet = false;
    private $errors = [];

    public function __construct(array $settings = null)
    {
        if (!is_array($settings)) {
            return;
        }

        foreach ($settings as $k => $v) {
            $this->$k = $v;
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getMessage($k)
    {
        static $messages = null;

        if ($messages === null) {
            $messages = [
                'emailError' => __('Please enter a valid email address', 'queue-mail'),
                'usernameError' => __('Please enter a username', 'queue-mail'),
                'passwordError' => __('Please enter a password', 'queue-mail'),
            ];
        }

        return isset($messages[$k]) ? $messages[$k] : null;
    }

    public function toArray()
    {
        $settings = [];
        try {
            $reflect = new \ReflectionClass($this);
            $props = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC);
        } catch (\Exception $e) {
            return $settings;
        }

        foreach ($props as $prop) {
            $var = $prop->name;
            $settings[$var] = $this->$var;
        }

        return $settings;
    }

    public function loadFromPost($fromAddress)
    {
        $isValid = true;
        $this->errors = [];


        $this->email = isset($fromAddress['email']) ? $fromAddress['email'] : null;
        if ($this->email == '' || !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $isValid = false;
            $this->errors['email'] = $this->getMessage('emailError');
        }
        $this->name = filter_var(isset($fromAddress['name']) ? $fromAddress['name'] : null, FILTER_SANITIZE_STRING);
        $this->forceEmail = isset($fromAddress['forceEmail']) ? $fromAddress['forceEmail'] === '1' : false;
        $this->forceName = isset($fromAddress['forceName']) ? $fromAddress['forceName'] === '1' : false;

        if (!self::$isDefaultEmailSet) {
            $this->isDefaultEmail = isset($fromAddress['isDefaultEmail']) ? $fromAddress['isDefaultEmail'] === '1' : false;
        }
        if ($this->isDefaultEmail) {
            self::$isDefaultEmailSet = true;
        }
        if (!self::$isDefaultNameSet) {
            $this->isDefaultName = isset($fromAddress['isDefaultName']) ? $fromAddress['isDefaultName'] === '1' : false;
        }
        if ($this->isDefaultName) {
            self::$isDefaultNameSet = true;
        }

        $this->auth = isset($fromAddress['auth']) ? $fromAddress['auth'] === '1' : false;
        $this->username = isset($fromAddress['username']) ? $fromAddress['username'] : null;
        $this->password = isset($fromAddress['password']) ? $fromAddress['password'] : null;

        if ($this->auth && !$this->username) {
            $isValid = false;
            $this->errors['username'] = $this->getMessage('usernameError');
        }
        if ($this->auth && !$this->password) {
            $isValid = false;
            $this->errors['password'] = $this->getMessage('passwordError');
        }

        return $isValid;
    }
}