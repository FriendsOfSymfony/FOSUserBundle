<?php

namespace Bundle\FOS\UserBundle\Util;

class String
{
    public static function strtolower($str)
    {
        static $function = null;
        if (null === $function) {
            $function = extension_loaded('mb_string') ? 'mb_strtolower' : 'strtolower';
        }

        return $function($str);
    }

    public static function isEmail($str)
    {
        return 0 < preg_match('/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i', $str);
    }

    private final function __construct() {}
}
