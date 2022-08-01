<?php

namespace App\Services;

class Str
{
    public static function before($baseString, $needle): string
    {
        return strstr($baseString, $needle, true);
    }

    public static function between($baseString, $startingWord, $endingWord): string
    {
        $subtring_start = strpos($baseString, $startingWord);
        $subtring_start += strlen($startingWord);
        $size = strpos($baseString, $endingWord, $subtring_start) - $subtring_start;
        return substr($baseString, $subtring_start, $size);
    }

    public static function lastCharacters($string, $length): string
    {
        return substr($string, $length * -1);
    }
}
