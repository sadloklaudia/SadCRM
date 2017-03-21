<?php
namespace Application\Controller;

use Application\Model\GoogleMailSender;
use Application\Model\GoogleMailSendException;
use Ouzo\Controller;
use Ouzo\Utilities\Functions;
use Ouzo\Utilities\Json;
use Ouzo\Utilities\Strings;

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

        $this->forEachRecipient(function ($recipient) use ($mailSender) {
            $mailSender->addRecipient($recipient);
        });

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

    private function forEachRecipient(callable $callback)
    {
        $elements = preg_split('/[\ \n\,]+/', $this->params['recipients']);

        foreach ($elements as $recipient) {
            $recipient = Strings::trimToNull($recipient);
            if (Strings::isNotBlank($recipient)) {
                Functions::call($callback, $recipient);
            }
        }
    }
}
