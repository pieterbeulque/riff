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

    public function urlise($string)
    {

        $string = mb_strtolower($string, 'UTF-8');

        $accentedVowels = array('é', 'è', 'ë', 'á', 'à', 'ä', 'ú', 'ù', 'ü', 'í', 'ì', 'ï', 'ó', 'ò', 'ö');
        $plainVowels    = array('e', 'e', 'e', 'a', 'a', 'a', 'u', 'u', 'u', 'i', 'i', 'i', 'o', 'o', 'o');

        $string = str_replace('"', ' ', $string);
        $string = str_replace(' ', '-', $string);

        if (urldecode($string) == $string) {
            $string = urlencode($string);
        }

        // convert "--" to "-"
        $string = preg_replace('/\-+/', '-', $string);

        return trim($string, '-');
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
        $string = str_replace(array('http://', 'https://'), '', $string);

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