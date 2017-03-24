<?php
namespace Application\Model;

class SaltGenerator
{
    public static function getSalt()
    {
        return self::getRandomWord(10);
    }

    private static function getRandomWord($length)
    {
        $letters = array_merge(range('a', 'z'), range('A', 'Z'));
        shuffle($letters);
        return substr(implode($letters), 0, $length);
    }
}
