<?php

namespace Utilities\Common;

/**
 * Hotmail Class
 *
 * @link    https://github.com/utilities-php/common
 * @author  Shahrad Elahi (https://github.com/shahradelahi)
 * @license https://github.com/utilities-php/common/blob/master/LICENSE (MIT License)
 * @version v1.0
 */
class Hotmail
{

    /**
     * @var string
     */
    private string $template;

    /**
     * @var string
     */
    private string $headers;

    /**
     * @var array
     */
    private array $variables;

    /**
     * Hotmail constructor.
     *
     * @param string $name (e.g. "Trading Bucket")
     * @param string $domain (e.g. hotmail.com)
     */
    public function __construct(string $name, string $domain)
    {
        $this->variables = [];
        $this->setHeaders($name, $domain); // Initialize the template
    }

    /**
     * @param string $name The name of the sender
     * @param string $domain The domain of the email (ex: example.com)
     * @return void
     */
    private function setHeaders(string $name, string $domain): void
    {
        $this->headers = "Sender: no-reply@$domain\r\n";
        $this->headers .= "From: $name <no-reply@$domain>\r\n";
        $this->headers .= "REPLY-TO: $name <no-reply@$domain>\r\n";
        $this->headers .= "MIME-Version: 1.0\r\n";
        $this->headers .= "Content-Type: text/html; charset=\"UTF-8\"\r\n";
        $this->headers .= "X-Mailer: PHP/" . PHP_VERSION;
    }

    /**
     * Set variables for the template
     *
     * @param array $variables
     * @return void
     */
    public function setVariables(array $variables): void
    {
        $this->variables = $variables;
    }

    /**
     * Add a row to the variables array
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    public function addVariable(string $key, string $value): void
    {
        $this->variables[$key] = $value;
    }

    /**
     * Variables in the template are in the form of <!--{{variable_name}}-->
     *
     * @param string $template
     * @return void
     */
    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    /**
     * Convert an array template to HTML
     *
     * @param array $messages (e.g. ["<h3>Email Title</h3>","<p>Example message</p>"]])
     * @param bool $lineBreaker By setting this to true, it will add a "<br>" between each message
     * @return string
     */
    public function arrayShaped(array $messages, bool $lineBreaker = false): string
    {
        $result = "";
        foreach ($messages as $message) {
            $result .= $message;
            if ($lineBreaker) {
                $result .= "<br>";
            }
        }
        return $result;
    }

    /**
     * @param string $recipient
     * @param string $subject
     * @return bool
     */
    public function send(string $recipient, string $subject): bool
    {
        return mail($recipient, $subject, $this->requireMessage(), $this->headers);
    }

    /**
     * It will use $variables and will replace the template with the data
     *
     * @return string
     */
    private function requireMessage(): string
    {
        $message = $this->template;
        foreach ($this->variables as $key => $value) {
            $message = str_replace("<!--{{" . $key . "}}-->", $value, $message);
        }
        return $message;
    }

}