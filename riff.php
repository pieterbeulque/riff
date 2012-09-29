<?php

/**
 * Riff PHP Library
 *
 * This source file is part of Riff, a stand-alone PHP library.
 *
 * @package     Riff
 * @subpackage  Riff
 *
 * @author      Pieter Beulque <pieterbeulque@gmail.com>
 * @since       0.1.1
 *
 */

/**
 * The base class for Riff that handles autoloading and generic functions 
 *
 * @package     Riff
 * @subpackage  Riff
 *
 * @author      Pieter Beulque <pieterbeulque@gmail.com>
 * @since       0.1.1
 *
 */

// Used charset
if (!defined('RIFF_CHARSET')) define('RIFF_CHARSET', 'utf-8');

// Debug mode or production mode
if (!defined('RIFF_DEBUG')) define('RIFF_DEBUG', true);

// Add the autoloader handle
spl_autoload_register(array('Riff', 'autoload'));

class Riff
{

    public function __construct()
    {

    }

    public static function autoload($class)
    {
        // Riff has a convenient way of naming classes/files
        // We can assume that a class called RiffException
        // can be found in exception/exception.php

        // We can erase the 'riff' part of the class
        $class = ltrim(strtolower($class), 'riff');

        // Ofcourse we cannot assume that everything will be named correctly
        $exceptions = array();
        $exceptions['riff'] = 'riff';

        $path = dirname(realpath(__FILE__));

        // If the class was not in the exceptions, check if it exists and include it
        if (!in_array($class, $exceptions)) {
            $file = $path . '/' . $class . '/' . $class . '.php';
            if (file_exists($file)) require_once $file;
        } else {
            require_once $path . '/' . $exceptions[$class] . '.php';
        }

    }

    /**
     * A nicer way to dump some variables in a readable way
     * 
     * @param mixed $var            Variable to dump
     * @param bool[optional] $die   Quit execution after dumping?
     */
    public static function dump($var, $die = false)
    {

        ob_start();
        echo '<pre style="font-family: ">';
        var_dump($var);
        echo '</pre>';
        $dump = ob_get_clean();

        echo $dump;

        if ($die) die();
    }

}