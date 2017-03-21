<?php
namespace Application\Model;

use Exception;

class GoogleMailSendException extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
