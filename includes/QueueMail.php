<?php

namespace SimplyGoodTech\QueueMail;

class QueueMail
{
    public $version;
    public $mailers = [];
    public $sendErrorsTo;
    public $logging = true;
    public $logLevel = 'error';
    public $logPeriod = 12;
    private $errors = [];
    private $debug = false;
    private $debugLevel = 0;
    private $debugCallback;

    private $errorMsg;

    /**
     * Settings constructor.
     *
     * @param array $settings As an array of stdClass objects
     */
    public function __construct(array $settings = null)
    {
        if (!is_array($settings)) {
            return;
        }

        foreach ($settings as $k => $v) {
            if ($k === 'mailers') {
                continue;
            }
            $this->$k = $v;
        }

        foreach ($settings['mailers'] as $mailerSettings) {
            $mailer = $this->mkMailer($mailerSettings['type'], $mailerSettings);
            if ($mailer !== null) {
                $this->mailers[] = $mailer;
            }
        }
    }

    public function setDebug($debug, $level = 4, $callback = null)
    {
        $this->debug = $debug === true ? true : false;
        $this->debugLevel = $level;
        $this->debugCallback = $callback;
    }

    private function mkMailer($class, $settings = null)
    {
        $mailer = null;

        if (in_array($class, Plugin::MAILERS)) {
            $mailer = new $class($settings);
        }

        return $mailer;
    }

    public function mail($to, $subject, $messageBody, $headers = '', $attachments = [])
    {
        // TODO admin form is broken when you add a new mailer, plus lots of other stuff, also need to add $sendErrors option

        $message = new Message($to, $subject, $messageBody, $headers, $attachments);
        $n = count($this->mailers);
        /** @var Mailer $m */
        /** @var Mailer $mailer */
        /** @var Mailer $errorMailer */
        $defaultFromAddress = $defaultFromName = $errorMailer = $defaultMailer = $mailer = null;
        if ($n === 0) {
            $mailer = new PHPMailer();
        } elseif ($n === 1) {
            $mailer = $this->mailers[0];
            if ($mailer->sendErrors) {
                $errorMailer = $mailer;
            }
        } else {
            foreach ($this->mailers as $m) {
                if ($errorMailer === null && $m->sendErrors) {
                    $errorMailer = $m;
                }
                /** @var From $fromAddress */
                foreach ($m->fromAddresses as $fromAddress) {
                    if ($mailer === null && $fromAddress->email === $message->fromEmail) {
                        $mailer = $m;
                    }

                    // TODO is this correct or should we look this up from the choosen mailer?
                    if ($fromAddress->isDefaultEmail) {
                        $defaultFromAddress = $fromAddress->email;
                    }
                    if ($fromAddress->isDefaultName) {
                        $defaultFromName = $fromAddress->name;
                    }
                }

                if ($m->default) {
                    $defaultMailer = $m;
                }
            }
        }

        if ($mailer === null && $defaultMailer !== null) {
            $mailer = $defaultMailer;
        }

        // TODO Force From Email/Name can only apply if there is only one mailer OR for the default mailer
        // TODO need to check force from settings, etc and reset from if required
        // If from has username and password then these should overide the mailer's

        if ($errorMailer === null) {
            $errorMailer = new PHPMailer();
        }

        if ($this->logging || $mailer->background) {
            // TODO log message
        }

        if ($this->debug) {
            $mailer->setDebug(true, $this->debugLevel, $this->debugCallback);
        }

        if ($mailer->background) {
            // TODO implement background sending code
        } else {
            $sent = false;
            try {
                if ($this->debug && $this->debugLevel > 0) {
                    $debugMsg = __('Sending message with Mailer: ', 'queue-mail') . get_class($mailer);
                    if ($this->debugCallback) {
                        $cb = $this->debugCallback;
                        $cb($debugMsg, 1);
                    } else {
                        echo $debugMsg;
                    }
                }
                $mailer->send($message);
                $sent = true;
            } catch (\Exception $e) {
                $mail_error_data = compact('to', 'subject', 'message', 'headers', 'attachments');
                $mail_error_data['phpmailer_exception_code'] = $e->getCode();

                // TODO if $errorMailer and send error messages address then send error report

                do_action('wp_mail_failed', new \WP_Error('wp_mail_failed', $e->getMessage(), $mail_error_data));
            }

            if ($this->logging) {
                // TODO update message status
            }
        }
    }

    /**
     * Converts settings to an array.
     *
     * This is to prevent serialization errors if class names and/or the setting structure changes over time.
     * @return array
     */
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
            if ($var === 'mailers') {
                continue;
            }
            $settings[$var] = $this->$var;
        }
        $settings['mailers'] = [];

        foreach ($this->mailers as $mailer) {
            $settings['mailers'][] = $mailer->toArray();
        }

        return $settings;
    }

    public function loadFromPost()
    {
        $this->errors = [];
        //error_log(print_r($_POST, true));
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
                $mailer = isset($_POST['mailer'][$i]) ? str_replace("\\\\", "\\", $_POST['mailer'][$i]) : null;
                error_log($mailer);
                if ($mailer !== null) {
                    $mailer = $this->mkMailer($mailer);
                }

                if ($mailer === null) {
                    $this->errorMsg .= __('Unknown mailer: ', 'queue-mail') . $mailer . '. ';
                    $isValid = false;
                } else {
                    $this->mailers[] = $mailer;
                    if (!$mailer->loadFromPost($i)) {
                        $isValid = false;
                        $this->errors['mailer'][$i] = $mailer->getErrors();
                    }
                }
            }
        }

        if (!$isValid) {
            $this->errorMsg .= __('There are errors: ', 'queue-mail');
        }

        return $isValid;
    }

    public function getErrorMsg()
    {
        return $this->errorMsg . $this->errorsToString($this->errors);
    }

    private function errorsToString($errors)
    {
        $msg = '';
        foreach ($errors as $error) {
            if (is_array($error)) {
                $msg .= $this->errorsToString($error);
            } else {
                $msg .= $error;
            }
        }

        return $msg;
    }
}