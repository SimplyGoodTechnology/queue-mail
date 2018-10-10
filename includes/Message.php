<?php

namespace SimplyGoodTech\QueueMail;


class Message
{
    public $fromEmail;
    public $fromName;
    public $to = [];
    public $cc = [];
    public $bcc = [];
    public $replyTo = [];
    public $subject;
    public $body;
    public $altBody;
    public $isHtml = false;
    public $contentType;
    public $charSet;
    public $attachments = [];
    public $customHeaders = [];

    public function __construct($to, $subject, $message, $headers = '', $attachments = [])
    {
        // The below is a modified version of the WordPress wp_mail() function.
        $atts = apply_filters('wp_mail', compact('to', 'subject', 'message', 'headers', 'attachments'));

        if (isset($atts['to'])) {
            $to = $atts['to'];
        }

        if (!is_array($to)) {
            $to = explode(',', $to);
        }

        if (isset($atts['subject'])) {
            $subject = $atts['subject'];
        }

        if (isset($atts['message'])) {
            $message = $atts['message'];
        }

        if (isset($atts['headers'])) {
            $headers = $atts['headers'];
        }

        if (isset($atts['attachments'])) {
            $attachments = $atts['attachments'];
        }

        if (!is_array($attachments)) {
            $attachments = explode("\n", str_replace("\r\n", "\n", $attachments));
        }

        // Headers
        $cc = $bcc = $reply_to = array();

        if (empty($headers)) {
            $headers = array();
        } else {
            if (!is_array($headers)) {
                // Explode the headers out, so this function can take both
                // string headers and an array of headers.
                $tempheaders = explode("\n", str_replace("\r\n", "\n", $headers));
            } else {
                $tempheaders = $headers;
            }
            $headers = array();

            // If it's actually got contents
            if (!empty($tempheaders)) {
                // Iterate through the raw headers
                foreach ((array)$tempheaders as $header) {
                    if (strpos($header, ':') === false) {
                        if (false !== stripos($header, 'boundary=')) {
                            $parts = preg_split('/boundary=/i', trim($header));
                            $boundary = trim(str_replace(array("'", '"'), '', $parts[1]));
                        }
                        continue;
                    }
                    // Explode them out
                    list($name, $content) = explode(':', trim($header), 2);

                    // Cleanup crew
                    $name = trim($name);
                    $content = trim($content);

                    switch (strtolower($name)) {
                        // Mainly for legacy -- process a From: header if it's there
                        case 'from':
                            $bracket_pos = strpos($content, '<');
                            if ($bracket_pos !== false) {
                                // Text before the bracketed email is the "From" name.
                                if ($bracket_pos > 0) {
                                    $from_name = substr($content, 0, $bracket_pos - 1);
                                    $from_name = str_replace('"', '', $from_name);
                                    $from_name = trim($from_name);
                                }

                                $from_email = substr($content, $bracket_pos + 1);
                                $from_email = str_replace('>', '', $from_email);
                                $from_email = trim($from_email);

                                // Avoid setting an empty $from_email.
                            } elseif ('' !== trim($content)) {
                                $from_email = trim($content);
                            }
                            break;
                        case 'content-type':
                            if (strpos($content, ';') !== false) {
                                list($type, $charset_content) = explode(';', $content);
                                $content_type = trim($type);
                                if (false !== stripos($charset_content, 'charset=')) {
                                    $charset = trim(str_replace(array('charset=', '"'), '', $charset_content));
                                } elseif (false !== stripos($charset_content, 'boundary=')) {
                                    $boundary = trim(str_replace(array('BOUNDARY=', 'boundary=', '"'), '', $charset_content));
                                    $charset = '';
                                }

                                // Avoid setting an empty $content_type.
                            } elseif ('' !== trim($content)) {
                                $content_type = trim($content);
                            }
                            break;
                        case 'cc':
                            $cc = array_merge((array)$cc, explode(',', $content));
                            break;
                        case 'bcc':
                            $bcc = array_merge((array)$bcc, explode(',', $content));
                            break;
                        case 'reply-to':
                            $reply_to = array_merge((array)$reply_to, explode(',', $content));
                            break;
                        default:
                            // Add it to our grand headers array
                            $headers[trim($name)] = trim($content);
                            break;
                    }
                }
            }
        }

        // From email and name
        // If we don't have a name from the input headers
        if (!isset($from_name))
            $from_name = 'WordPress';

        /* If we don't have an email from the input headers default to wordpress@$sitename
         * Some hosts will block outgoing mail from this address if it doesn't exist but
         * there's no easy alternative. Defaulting to admin_email might appear to be another
         * option but some hosts may refuse to relay mail from an unknown domain. See
         * https://core.trac.wordpress.org/ticket/5007.
         */

        if (!isset($from_email)) {
            // Get the site domain and get rid of www.
            $sitename = strtolower($_SERVER['SERVER_NAME']);
            if (substr($sitename, 0, 4) == 'www.') {
                $sitename = substr($sitename, 4);
            }

            $from_email = 'wordpress@' . $sitename;
        }

        $from_email = apply_filters('wp_mail_from', $from_email);
        $from_name = apply_filters('wp_mail_from_name', $from_name);

        $this->fromEmail = $from_email;
        $this->fromName = $from_name;
        $this->subject = $subject;
        $this->body = $message;

        // Set destination addresses, using appropriate methods for handling addresses
        $address_headers = compact('to', 'cc', 'bcc', 'reply_to');

        foreach ($address_headers as $address_header => $addresses) {
            if (empty($addresses)) {
                continue;
            }

            foreach ((array)$addresses as $address) {
                    // Break $recipient into name and address parts if in the format "Foo <bar@baz.com>"
                    $recipient_name = '';

                    if (preg_match('/(.*)<(.+)>/', $address, $matches)) {
                        if (count($matches) == 3) {
                            $recipient_name = $matches[1];
                            $address = $matches[2];
                        }
                    }

                    switch ($address_header) {
                        case 'to':
                            $this->to[] = [$address, $recipient_name];
                            break;
                        case 'cc':
                            $this->cc[] = [$address, $recipient_name];
                            break;
                        case 'bcc':
                            $this->bcc[] = [$address, $recipient_name];
                            break;
                        case 'reply_to':
                            $this->replyTo[] = [$address, $recipient_name];
                            break;
                    }
            }
        }

        // Set Content-Type and charset
        // If we don't have a content-type from the input headers
        if (!isset($content_type)) {
            $content_type = 'text/plain';
        }

        $this->contentType = apply_filters('wp_mail_content_type', $content_type);

        // If we don't have a charset from the input headers
        if (!isset($charset)) {
            $charset = get_bloginfo('charset');
        }

        $this->charSet = apply_filters('wp_mail_charset', $charset);

        // Set custom headers
        if (!empty($headers)) {
            foreach ((array)$headers as $name => $content) {
                $this->customHeaders[] = sprintf('%1$s: %2$s', $name, $content);
            }

            if (false !== stripos($content_type, 'multipart') && !empty($boundary)) {
                $this->customHeaders[] = sprintf("Content-Type: %s;\n\t boundary=\"%s\"", $content_type, $boundary);
            }
        }

        if (!empty($attachments)) {
            foreach ($attachments as $attachment) {
                $this->attachments[] = $attachment;
            }
        }
    }
}