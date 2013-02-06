<?php

/**
 * Easy HTTP header setting
 *
 * This source file is part of Riff, a stand-alone PHP library.
 *
 * @package     Riff
 * @subpackage  HTTP
 *
 * @author      Pieter Beulque <pieterbeulque@gmail.com>
 * @since       0.2.1
 */

namespace Riff\HTTP;

class HTTP
{
    /**
     * Most frequent HTTP response codes
     *
     * @var array
     */
    private $codes = array(
        200 => 'OK',
        201 => 'Created',
        301 => 'Moved Permanently',
        302 => 'Found',
        304 => 'Not Modified',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        410 => 'Gone',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable'
        );

    public function __construct()
    {

    }

    /**
     * Alias for headers_sent()
     */
    public static function headersSent()
    {
        return headers_sent();
    }

    /**
     * Do a HTTP redirect
     *
     * @param string $url           The destination
     * @param int[optional] $code   The status code
     */
    public static function redirect($url, $code = 302)
    {
        self::setHeadersByCode((int) $code);
        self::setHeaders('Location: ' . (string) $url);
        exit;
    }

    /**
     * Set one or multiple headers by full string
     *
     * @param array|string $headers      The headers to send to the browser
     */
    public static function setHeaders($headers)
    {
        if (self::headersSent()) throw new Exception("Headers were already sent");

        foreach ((array) $headers as $header) {
            header((string) $header);
        }
    }

    /**
     * Set one or multiple headers by status code
     *
     * @param int $code        The status code to send to the browser
     */
    public static function setHeadersByCode($code)
    {
        $code = (int) $code;

        if (!isset(self::$codes[$code])) throw new Exception("Invalid status code");

        self::setHeaders('HTTP/1.1 ' . $code . ' ' . $codes[$code]);
    }
}