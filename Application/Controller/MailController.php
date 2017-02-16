<?php
namespace Application\Controller;

use Ouzo\Controller;

class MailController extends Controller
{
    function sendMail()
    {

        $to = 'sadloklaudia@gamil.com';
        $subject = 'the subject';
        $message = 'hello';
        $headers = 'From: webmaster@example.com' . "\r\n" .
            'Reply-To: webmaster@example.com' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        mail($to, $subject, $message, "");

    }
}