<?php
namespace SimplyGoodTech\QueueMail;


class PHPMailer extends Mailer
{
    public static $label = 'PHP mail()';
    /** @var \PHPMailer */
    protected static $phpMailer;

    public static function getIconUrl()
    {
        return plugin_dir_url(__DIR__) . 'images/php.png';
    }

    protected function initMailer()
    {
        self::$phpMailer->isMail();
    }

    /**
     * @param Message $message
     * @throws \phpmailerException
     */
    public function send(Message $message)
    {
        // (Re)create it, if it's gone missing
        if (!(self::$phpMailer instanceof PHPMailer)) {
            require_once ABSPATH . WPINC . '/class-phpmailer.php';
            require_once ABSPATH . WPINC . '/class-smtp.php';
            self::$phpMailer = new \PHPMailer(true);
        }

        $phpMailer = self::$phpMailer;

        // Empty out the values that may be set
        $phpMailer->ClearAllRecipients();
        $phpMailer->ClearAttachments();
        $phpMailer->ClearCustomHeaders();
        $phpMailer->ClearReplyTos();

        $phpMailer->Body= '';
        $phpMailer->AltBody= '';

        if ($this->debug) {
            $phpMailer->SMTPDebug = $this->debugLevel;
            $phpMailer->Debugoutput = $this->debugCallback;
        } else {
            $phpMailer->SMTPDebug = 0;
            $phpMailer->Debugoutput = 'html';
        }

        $phpMailer->setFrom($message->fromEmail, $message->fromName, false);

        // Set mail's subject and body
        $phpMailer->Subject = $message->subject;
        $phpMailer->Body = $message->body;

        foreach ($message->to as $address) {
            $phpMailer->addAddress($address[0], $address[1]);
        }
        foreach ($message->cc as $address) {
            $phpMailer->addCc($address[0], $address[1]);
        }
        foreach ($message->bcc as $address) {
            $phpMailer->addBcc($address[0], $address[1]);
        }
        foreach ($message->replyTo as $address) {
            $phpMailer->addReplyTo($address[0], $address[1]);
        }

        $this->initMailer();

        $phpMailer->ContentType = $message->contentType;

        // Set whether it's plaintext, depending on $content_type
        if ('text/html' === $message->contentType) {
            $phpMailer->isHTML(true);
        }

        $phpMailer->CharSet = $message->charSet;

        foreach ($message->customHeaders as $header) {
            $phpMailer->addCustomHeader($header);
        }

        foreach ($message->attachments as $attachment) {
            try {
                $phpMailer->addAttachment($attachment);
            } catch (\phpmailerException $e) {
                continue;
            }
        }

        do_action_ref_array('phpmailer_init', array(&$phpMailer));
        $phpMailer->send();
    }

}