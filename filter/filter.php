<?php

/**
 * Static filter methods
 *
 * This source file is part of Riff, a stand-alone PHP library.
 *
 * @package     Riff
 * @subpackage  Filter
 *
 * @author      Pieter Beulque <pieterbeulque@gmail.com>
 * @since       0.1.1
 */

namespace Riff\Filter;

class Filter
{
    public function __construct()
    {

    }

    /**
     * Converts a camelCased string (searchAll) to a hyphen-separated string (search-all)
     *
     * @param string $string
     * @return string
     */
    public static function camelCaseToHyphen($string)
    {
        return trim(strtolower(preg_replace('/([A-Z])/', '-$1', $string)), '-');
    }

    /**
     * Converts a hyphen-separated string (search-all) to a camelCased string (searchAll)
     *
     * @param string $string
     * @param bool $lowercase   if true, lowercase the string first, if false, keep the string as is
     * @return string
     */
    public static function hyphenToCamelCase($string, $lowercase = true)
    {
        $string = ($lowercase) ? strtolower($string) : $string;
        return lcfirst(implode('', array_map('ucfirst', explode('-', $string))));
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

    /**
     * Cleans a string for the URL, removes weird signs and letters and returns a URL-friendly string
     *
     * @param string $string
     * @return string
     */
    public static function urlise($string)
    {
        $string = mb_strtolower($string, 'UTF-8');

        $accentedVowels = array('é','è','ë','á','à','ä','ú','ù','ü','í','ì','ï','ó','ò','ö');
        $plainVowels    = array('e','e','e','a','a','a','u','u','u','i','i','i','o','o','o');
        $string = str_replace($accentedVowels, $plainVowels, $string);

        $reservedCharacters = array('/','?',':','@','#','[',']','!','$','&','\'','(',')','*','+',',',';','=','"');
        $string = str_replace($reservedCharacters, ' ', $string);

        $string = str_replace(' ', '-', $string);

        if (urldecode($string) == $string) {
            $string = urlencode($string);
        }

        // convert "--" to "-"
        $string = preg_replace('/\-+/', '-', $string);

        return trim($string, '-');
    }
}