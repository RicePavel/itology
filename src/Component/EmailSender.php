<?php

namespace App\Component;

class EmailSender {
    
    private $fromEmail;
    private $smtpHost;
    private $smtpPort;
    private $smtpUsername;
    private $smtpPassword;
    
    public function __construct($fromEmail, $smtpHost, $smtpPort, $smtpUsername, $smtpPassword) {
        $this->fromEmail = $fromEmail;
        $this->smtpHost = $smtpHost;
        $this->smtpPort = $smtpPort;
        $this->smtpUsername = $smtpUsername;
        $this->smtpPassword = $smtpPassword;
    }
    
    public function sendEmail($to, $body, $subject, $bodyContentType = "text/html") {
        $transport = new \Swift_SmtpTransport($this->smtpHost, $this->smtpPort);
        $transport->setUsername($this->smtpUsername);
        $transport->setPassword($this->smtpPassword);
        $swift = new \Swift_Mailer($transport);
        
        $message = new \Swift_Message($subject);
        $message->setFrom($this->fromEmail);
        $message->setBody($body, $bodyContentType);
        $message->setTo($to);
        
        $failures = "";
        return $swift->send($message, $failures);
    }
    
}

