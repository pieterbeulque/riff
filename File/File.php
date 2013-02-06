<?php

/**
 * Easy file handling
 *
 * This source file is part of Riff, a stand-alone PHP library.
 *
 * @package     Riff
 * @subpackage  File
 *
 * @author      Pieter Beulque <pieterbeulque@gmail.com>
 * @since       0.2.1
 */

namespace Riff\File;

class File
{
    /**
     * The content
     *
     * @var string
     */
    private $content;

    /**
     * The path to the file
     *
     * @var string
     */
    private $path;

    /**
     * Creates a reference to a file (it doesn't have to exist)
     *
     * @param string $path          The path to the file
     * @param bool[optional] $read  Read the file or not
     */
    public function __construct($path, $read = true)
    {
        $this->path = (string) $path;

        if ($read) $this->read();
    }

    /**
     * Change the file mode
     *
     * @param int $mode     The mode e.g. 0755 (octal!)
     */
    public function chmod($mode)
    {
        @chmod($this->path, (int) $mode);
    }

    /**
     * Checks if a file exists
     *
     * @param string $path
     * @return bool
     */
    public static function exists($path)
    {
        return (@file_exists((string) $path) && is_file((string) $path));
    }

    /**
     * Gets the stored content
     *
     * @return string
     */
    public function getContent()
    {
        if (!isset($this->content)) $this->read();

        return $this->content;
    }

    /**
     * Reads the file and stores the content
     */
    private function read()
    {
        if (!self::exists($this->path)) throw new Exception("File does not exist");

        $this->content = (string) file_get_contents($this->path);
    }

    /**
     * Sets the content of the current file
     *
     * @param string $content           The new content
     * @param bool[optional] $append    Append or overwrite?
     */
    public function setContent($content, $append = false)
    {
        $flags = ($append) ? FILE_APPEND : 0;

        file_put_contents($this->path, $content, $flags);

        $this->read();
    }
}