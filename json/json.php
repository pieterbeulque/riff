<?php

/**
 * Easy API handling with JSON
 *
 * This source file is part of Riff, a stand-alone PHP library.
 *
 * @package     Riff
 * @subpackage  JSON
 *
 * @author      Pieter Beulque <pieterbeulque@gmail.com>
 * @since       0.1.1
 */

namespace Riff\JSON;

class JSON
{
    /**
     * The decoded JSON object or array (specify in constructor)
     *
     * @var object|array
     */
    private $decodedJSON;

    /**
     * The raw JSON output from the API
     *
     * @var string
     */
    private $rawJSON;

    /**
     * The API url
     *
     * @var string
     */
    private $url;

    /**
     * Call the URL and return the decoded information
     *
     * @var string $url     The API URL
     * @var bool $validate  Try and parse the JSON in a proper way (e.g. Flickr adds extra chars)
     */
    public function __construct($url, $validate = false)
    {
        $this->url = (string) $url;
        $this->rawJSON = trim(file_get_contents($this->url));

        if ($validate) {
            $firstBracket = strpos($this->rawJSON, '{');

            if ($firstBracket !== 0) {
                $this->rawJSON = substr($this->rawJSON, $firstBracket);
            }

            $lastBracket = strrpos($this->rawJSON, '}');

            if ($lastBracket !== (strlen($this->rawJSON) - 1)) {
                $count = strlen($this->rawJSON) - $lastBracket - 1;
                $this->rawJSON = substr($this->rawJSON, 0, -1 * $count);
            }
        }

        if (!$this->rawJSON) {
            throw new RiffException('Cannot connect to API');
        }
    }

    /**
     * Get decoded JSON in array or object
     *
     * @var bool $inArray   Array or object returned?
     * @return object|array
     */
    public function getDecoded($inArray = true)
    {
        $this->decodedJSON = json_decode($this->rawJSON, (bool) $inArray);

        return $this->decodedJSON;
    }

    /**
     * Get raw JSON
     *
     * @return object|array
     */
    public function getRaw()
    {
        return $this->rawJSON;
    }
}