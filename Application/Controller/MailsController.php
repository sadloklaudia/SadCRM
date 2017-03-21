<?php
namespace Application\Controller;

use Application\Model\GoogleMailSender;
use Application\Model\GoogleMailSendException;
use Ouzo\Controller;
use Ouzo\Utilities\Json;

class MailsController extends Controller
{
    public function init()
    {
        $this->header('Content-Type: application/json');
    }

    function singleMail()
    {
        $mailSender = new GoogleMailSender();

        $mailSender->setSubject($this->params['subject']);
        $mailSender->setMessage($this->params['message']);

        foreach (explode(',', $this->params['recipients']) as $recipient) {
            $mailSender->addRecipient($recipient);
        }

        try {
            $mailSender->send();
            echo Json::encode([
                'success' => true
            ]);
        } catch (GoogleMailSendException $exception) {
            echo Json::encode([
                'success' => false,
                'message' => $exception->getMessage()
            ]);
        }
    }
}
