<?php
namespace Application\Model;

use PHPMailer;

class GoogleMailSender
{
    const GMAIL_SMTP_USERNAME = 'hosting.strony.php@gmail.com';
    const GMAIL_SMTP_PASSWORD = '76a5ba50d31';

    /** @var PHPMailer */
    private $mailer;

    public function __construct()
    {
        $this->mailer = $this->createMailer();
    }

    protected function createMailer()
    {
        $mailer = new PHPMailer();
        $mailer->isSMTP(); // enable SMTP
        $mailer->SMTPDebug = 1;
        $mailer->SMTPAuth = true;
        $mailer->SMTPSecure = 'ssl'; // REQUIRED for Gmail
        $mailer->Host = 'smtp.gmail.com';
        $mailer->Port = 465; // or 587
        $mailer->isHTML(true);
        $mailer->Username = self::GMAIL_SMTP_USERNAME;
        $mailer->Password = self::GMAIL_SMTP_PASSWORD;

        return $mailer;
    }

    public function setSubject($subject)
    {
        $this->mailer->Subject = $subject;
    }

    public function setMessage($message)
    {
        $this->mailer->Body = $message;
    }

    public function addRecipient($email)
    {
        OutputBuffer::logOutput("Adding recipient", function () use ($email) {
            $this->mailer->AddAddress($email);
        });
    }

    public function send()
    {
        if (!($this->sendMessage())) {
            throw new GoogleMailSendException($this->mailer->ErrorInfo);
        }
    }

    protected function sendMessage()
    {
        return OutputBuffer::logOutput("Send e-mail message", function () {
            return $this->mailer->Send();
        });
    }
}
