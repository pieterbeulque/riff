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

class Riff
{

    public function __construct()
    {

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
