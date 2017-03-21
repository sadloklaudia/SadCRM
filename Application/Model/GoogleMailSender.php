<?php
namespace Application\Model;

use Ouzo\Logger\Logger;
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
        $mailer->IsSMTP(); // enable SMTP
        $mailer->SMTPDebug = 1;
        $mailer->SMTPAuth = true;
        $mailer->SMTPSecure = 'ssl'; // REQUIRED for Gmail
        $mailer->Host = 'smtp.gmail.com';
        $mailer->Port = 465; // or 587
        $mailer->IsHTML(true);
        $mailer->Username = self::GMAIL_SMTP_USERNAME;
        $mailer->Password = self::GMAIL_SMTP_PASSWORD;

        $mailer->SetFrom(self::GMAIL_SMTP_USERNAME);

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
        $this->mailer->AddAddress($email);
    }

    public function send()
    {
        if (!($this->sendMessage())) {
            throw new GoogleMailSendException($this->mailer->ErrorInfo);
        }
    }

    protected function sendMessage()
    {
        ob_start();
        $result = $this->mailer->Send();
        $logs = ob_get_clean();
        Logger::getLogger(__CLASS__)->info($logs);
        return $result;
    }
}
