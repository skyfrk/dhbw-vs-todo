<?php

namespace App\Services;

use Interop\Container\ContainerInterface;
use PHPMailer\PHPMailer\PHPMailer;

class EmailService
{
    private $settings;

    public function __construct(array $settings) 
    {
        $this->settings = $settings;
    }

    public function sendMail(string $to, string $toName, string $subject, string $body)
    {
        $mailer = new PHPMailer(false);
        $mailer->SMTPDebug = $this->settings['smtpDebug'];
        $mailer->isSMTP();
        $mailer->Host = $this->settings['host'];
        $mailer->SMTPAuth = true;
        $mailer->Username = $this->settings['user'];
        $mailer->Password = $this->settings['password'];
        $mailer->SMTPSecure = 'tls';
        $mailer->Port = $this->settings['port'];
    
        $mailer->setFrom($this->settings['fromMail'], $this->settings['fromName']);
        $mailer->addAddress($to, $toName);
        $mailer->isHTML(true);
        $mailer->Subject = $subject;
        $mailer->Body = $body;
    
        return $mailer->send();
    }
}