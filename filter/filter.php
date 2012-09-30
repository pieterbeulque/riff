<?php

/**
 * Riff PHP Library
 *
 * This source file is part of Riff, a stand-alone PHP library.
 *
 * @package     Riff
 * @subpackage  RiffFilter
 *
 * @author      Pieter Beulque <pieterbeulque@gmail.com>
 * @since       0.1.1
 *
 */

/**
 * Static filter methods
 *
 * @package     Riff
 * @subpackage  RiffFilter
 *
 * @author      Pieter Beulque <pieterbeulque@gmail.com>
 * @since       0.1.1
 *
 */

/**
* 
*/
class RiffFilter
{
    
    public function __construct()
    {
        
    }

    /**
     * Is valid email?
     * 
     * @param string $string
     * @return bool
     */ 
    public static function isEmail($string)
    {
        return filter_var($string, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Is valid URL
     * 
     * @param string $string
     * @return bool
     */ 
    public static function isUrl($string)
    {
        // Strip off protocol
        $string = str_replace(array('http://', 'https://', '', $string);

        return filter_var($string, FILTER_VALIDATE_URL);

    }

    /**
     * Prepares a string for injection into MySQL queries
     * 
     * @param string $string
     * @return string
     */
    public static function sanitize($string)
    {
        return htmlspecialchars(stripslashes((string) $string));
    }

}