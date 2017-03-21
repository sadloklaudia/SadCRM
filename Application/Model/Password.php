<?php
namespace Model;

class Password
{
    public static function hash($password)
    {
        return sha1($password);
    }
}
