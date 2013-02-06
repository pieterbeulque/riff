<?php

/**
 * The standard exception used by Riff
 *
 * This source file is part of Riff, a stand-alone PHP library.
 *
 * @package     Riff
 * @subpackage  Exception
 *
 * @author      Pieter Beulque <pieterbeulque@gmail.com>
 * @since       0.1.1
 */

namespace Riff\Exception;

class Exception extends \Exception
{
    /**
     * Class constructor
     *
     * @param string $message       Error message
     * @param int[optional] $code   Error code, can be used to categorise exceptions
     */
    public function __construct($message, $code = 0)
    {
        parent::__construct((string) $message, (int) $code);
    }
}

// Set the exception handler
set_exception_handler('Riff\Exception\exceptionHandler');

/**
 * Prints an easy on the eye error log to the web browser,
 * makes it more fun to debug your applications
 *
 * @param Exception $e
 */
function exceptionHandler ($e)
{
    // Do some checks so we won't crash when we use these variables
    if (!isset($_SERVER['SERVER_NAME'])) $_SERVER['SERVER_NAME'] = '';
    if (!isset($_SERVER['REQUEST_METHOD'])) $_SERVER['REQUEST_METHOD'] = '';
    if (!isset($_SERVER['REQUEST_URI'])) $_SERVER['REQUEST_URI'] = '';

    $traceStack = (array) $e->getTrace();

    // Start the HTML document
    $output  = '<!doctype html>
                <html>
                    <head><title>Uncaught exception</title></head>
                    <body style="background-color: #fefefe; color: #555; font-family: Helvetica Neue, Arial; font-size: 14px; line-height: 1.3">
                        <table width="50%" style="background-color: #eee; border: 1px solid #ccc; padding: 0 20px; max-width: 680px; margin: 20px auto">
                            <tr>
                                <td><h1>Uncaught exception: ' . $e->getMessage() . '</h1></td>
                            </tr>
                            <tr>
                                <td>
                                    <h2>Error information</h2>
                                    <p>An error occured when trying to&nbsp;access&nbsp;(' . $_SERVER['REQUEST_METHOD'] .')&nbsp;<strong>http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] . '</strong>.
                                    <p>The error was thrown in <strong>' . $e->getFile() . '</strong> on&nbsp;line&nbsp;<strong>' . $e->getLine() . '</strong>.</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <h2>Trace</h2>';

    // Add the trace to the output, if there is any
    if (count($traceStack) > 0) {

        $output .= '<ol>';

        foreach($traceStack as $trace) {
            $output .= '<li>Function <strong>' . $trace['function'] . '</strong> in file <strong>' . $trace['file'] . '</strong> on&nbsp;line&nbsp;<strong>' .$trace['line'] . '</strong></li>';
        }

        $output .= '</ol></td></tr>';

    } else {

        $output .= '<p>No trace information</p>';

    }

    $output .= '</td></tr>';

    // Add the global variables to the output
    $superglobals = array('GET', 'POST', 'COOKIE', 'FILES');
    if (session_status() == PHP_SESSION_ACTIVE) $superglobals[] = 'SESSION';

    $output .= '<tr><td><h2>Superglobals</h2>';

    foreach ($superglobals as $superglobal) {

        $output .= '<h3>' . $superglobal . '</h3>';

        if (count($GLOBALS['_' . $superglobal]) > 0) {
            $output .= '<dl>';

            foreach ($GLOBALS['_' . $superglobal] as $key => $value) {
                $output .= '<dt><strong>' . $key . '</strong></dt><dd>' . $value . '</dd>';
            }

            $output .= '</dl>';
        } else {
            $output .= '<p>No ' . $superglobal . ' variables</p>';
        }

    }

    die($output);
}