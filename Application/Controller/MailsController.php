<?php
namespace Application\Controller;

use Ouzo\Controller;

class MailsController extends Controller
{
    public function init()
    {
        $this->header('Content-Type: application/json');
    }

    function singleMail()
    {
        $to = 'wilkowski.kontakt@gamil.com';
        $subject = 'the subject';
        $message = 'hello';
        $headers = 'From: webmaster@example.com' . "\r\n" .
            'Reply-To: webmaster@example.com' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        mail($to, $subject, $message, $headers);
    }
}
