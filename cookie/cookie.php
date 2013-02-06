<?php

/**
 * Static cookie methods
 *
 * This source file is part of Riff, a stand-alone PHP library.
 *
 * @package     Riff
 * @subpackage  Cookie
 *
 * @author      Pieter Beulque <pieterbeulque@gmail.com>
 * @since       0.1.1
 */

namespace Riff\Cookie;

class Cookie
{

    /**
     * Check if a cookie exists
     *
     * @param string name
     * @return bool
     */
    public static function exists($name)
    {
        return !empty($_COOKIE[$name]);
    }

    /**
     * Get a certain cookie
     *
     * @param string name
     * @return mixed        The value if set, else false
     */
    public static function get($name)
    {
        return (self::exists($name)) ? $_COOKIE[$name] : false;
    }

    /**
     * Set a cookie
     *
     * @param string name
     * @param mixed  value
     * @param int    duration
     */
    public static function set($name, $value, $duration = 3600)
    {
        setcookie($name, $value, (int) $duration);
    }
}