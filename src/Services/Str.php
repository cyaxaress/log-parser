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

    public static function getRoute(string $line): string
    {
        return Str::between($line, "\"", "\"");
    }

    public static function getMethod($line)
    {
        $route = self::getRoute($line);
        $routeParts = explode(" ", $route);

        return $routeParts[0];
    }

    public static function getUri($line)
    {
        $route = self::getRoute($line);
        $routeParts = explode(" ", $route);

        return $routeParts[1];
    }

    public static function getDate(string $line): \DateTime
    {
        $str = Str::before(Str::between($line, "[", "]"), " +");
        return \DateTime::createFromFormat("d/M/Y:H:i:s", $str);
    }

    public static function getStatusCode(string $line): string
    {
        return Str::lastCharacters($line, 3);
    }

    public static function getServiceName(string $line): string
    {
        return Str::before($line, " - -");
    }

}
