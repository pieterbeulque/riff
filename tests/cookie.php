<?php

require_once 'PHPUnit/Autoload.php';

require_once '../riff.php';

class RiffCookieTest extends PHPUnit_Framework_TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testExistsFails()
    {
        $this->assertFalse(RiffCookie::exists('zimbabwe'));
    }

    /**
     * @runInSeparateProcess
     */
    public function testGetNonexistant()
    {
        $this->assertFalse(RiffCookie::get('zimbabwoooo'));
    }
}