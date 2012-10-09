<?php

/**
 * Riff PHP Library
 *
 * This source file is part of Riff, a stand-alone PHP library.
 *
 * @package     Riff
 * @subpackage  RiffJSON
 *
 * @author      Pieter Beulque <pieterbeulque@gmail.com>
 * @since       0.1.1
 *
 */

/**
 * Allows for easy API calling via JSON in PHP
 *
 * @package     Riff
 * @subpackage  RiffJSON
 *
 * @author      Pieter Beulque <pieterbeulque@gmail.com>
 * @since       0.1.1
 *
 */

class RiffJSON
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
     */ 
    public function __construct($url, $inArray = false)
    {
        $this->url = (string) $url;
        $this->rawJSON = file_get_contents($this->url);

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
    public function getDecoded($inArray = false)
    {
        $this->decodedJSON = json_decode($this->rawJSON, (bool) $inArray);

        return $this->decodedJSON;
    }
}