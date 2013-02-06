<?php

require_once 'PHPUnit/Autoload.php';

require_once '../riff.php';

use Riff\Cookie\Cookie;

class RiffCookieTest extends PHPUnit_Framework_TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testExistsFails()
    {
        $this->assertFalse(Cookie::exists('zimbabwe'));
    }

    /**
     * @runInSeparateProcess
     */
    public function testGetNonexistant()
    {
        $this->assertFalse(Cookie::get('zimbabwoooo'));
    }
}