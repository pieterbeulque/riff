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

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'exception' . DIRECTORY_SEPARATOR . 'exception.php';

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
        $class = str_replace('riff', '', strtolower($class));

        // Ofcourse we cannot assume that everything will be named conveniently
        $exceptions = array();
        $exceptions['riff'] = 'riff';
        $exceptions['query'] = 'database/query';

        $path = dirname(realpath(__FILE__));

        // If the class was not in the exceptions, check if it exists and include it
        if (!isset($exceptions[$class])) {
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
        // Only dump values if we're in debug mode
        if (!RIFF_DEBUG) return false;

        ob_start();
        echo '<pre style="background:#eee;border:1px solid #ccc;width:50%;max-width:640px;padding:20px 30px;margin:0 auto;color:#333;font:14px/22px Monaco,monospace">';
        var_dump($var);
        echo '</pre>';
        $dump = ob_get_clean();

        echo $dump;

        if ($die) die();
    }
}