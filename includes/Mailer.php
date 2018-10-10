<?php
namespace SimplyGoodTech\QueueMail;


class Mailer
{
    public static $types = [];
    public static $label;
    public $sendErrors = false;
    public $default = false;
    public $background = false;
    public $fromAddresses = [];

    protected $errors = [];
    protected $debug = false;
    protected $debugLevel = 0;
    protected $debugCallback;

    public function __construct(array $settings = null)
    {
        if (!is_array($settings)) {
            return;
        }

        foreach ($settings as $k => $v) {
            if ($k === 'fromAddresses') {
                foreach ($settings[$k] as $fromAddress) {
                    $this->fromAddresses[] = new From($fromAddress);
                }
            } elseif ($k !== 'type') {
                $this->$k = $v;
            }
        }
    }

    public function getText($k)
    {
      return null;
    }

    public function setDebug($debug, $level = 4, $callback = null)
    {
        $this->debug = $debug === true ? true : false;
        $this->debugLevel = $level;
        $this->debugCallback = $callback;
    }

    public function getErrors()
    {
        return $this->errors;
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

        $settings['type'] = get_class($this);
        foreach ($props as $prop) {
            if ($prop->isStatic()) {
                continue;
            }
            error_log(print_r($prop, true));
            $var = $prop->name;
            if ($var === 'fromAddresses') {
                $settings[$var] = [];
                /** @var From $fromAddress */
                foreach ($this->fromAddresses as $fromAddress) {
                    $settings[$var][] = $fromAddress->toArray();
                }
            } else {
                $settings[$var] = $this->$var;
            }
        }

        return $settings;
    }

    public static function getIconUrl()
    {
        return '';
    }

    public function getSettingsTemplate()
    {
        return plugin_dir_path(__DIR__) . 'templates/' . strtolower(substr(basename(get_class($this)), 0, -6)) . '.php';
    }

    public function loadFromPost($i)
    {
        $this->errors = [];
        $isValid = true;
        $this->sendErrors = isset($_POST['sendErrors']) && $_POST['sendErrors'] == $i ? true : false;
        $this->background = isset($_POST['background'][$i]) && intval($_POST['background'][$i]) === 1 ? true : false;
        $this->default = isset($_POST['default']) && $_POST['default'] === $i ? true : false;
        $fromAddresses = isset($_POST['from'][$i]) ? (is_array($_POST['from'][$i]) ? $_POST['from'][$i] : []) : [];

        foreach ($fromAddresses as $j => $fromAddress) {
            $from = new From();
            $this->fromAddresses[] = $from;
            if (!$from->loadFromPost($fromAddress)) {
                $isValid = false;
                $this->errors['fromAddress'][$j] = $from->getErrors();
            }
        }

        return $isValid;
    }
}