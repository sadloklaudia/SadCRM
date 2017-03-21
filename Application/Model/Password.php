<?php
namespace Application\Model;

class Password
{
    public static function hash($password, $salt)
    {
        return sha1($password . $salt);
    }
}
