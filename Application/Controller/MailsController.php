<?php
namespace Application\Controller;

use Ouzo\Controller;
use Ouzo\Utilities\Json;
use PHPMailer;

class MailsController extends Controller
{
    public function init()
    {
        $this->header('Content-Type: application/json');
    }

    function singleMail()
    {
        $mailer = new PHPMailer();
        $mailer->IsSMTP(); // enable SMTP
        $mailer->SMTPDebug = 1;
        $mailer->SMTPAuth = true;
        $mailer->SMTPSecure = 'ssl'; // REQUIRED for Gmail
        $mailer->Host = "smtp.gmail.com";
        $mailer->Port = 465; // or 587
        $mailer->IsHTML(true);
        $mailer->Username = "hosting.strony.php@gmail.com";
        $mailer->Password = "76a5ba50d31";

        $mailer->SetFrom("hosting.strony.php@gmail.com");
        $mailer->Subject = "Test";
        $mailer->Body = "hello";
        $mailer->AddAddress("wilkowski.kontakt@gmail.com");

        if ($mailer->Send()) {
            echo Json::encode([
                'success' => true
            ]);
        } else {
            echo Json::encode([
                'success' => false,
                'message' => $mailer->ErrorInfo
            ]);
        }
    }
}
