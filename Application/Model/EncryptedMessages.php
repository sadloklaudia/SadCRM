<?php
namespace Application\Model;

use Ouzo\Controller;

class EncryptedMessages
{
    public static function decryptMessage(Controller $controller)
    {
        //return Encryption::decrypt(self::getRequestBody());
        return $controller->params;
    }

    public static function printEncrypted($message)
    {
        echo $message;
//        echo Encryption::encrypt($message);
    }

    protected static function getRequestBody()
    {
        return parse_str(file_get_contents('php://input'));
    }
}
