<?php

require_once 'PHPUnit/Autoload.php';

require_once '../riff.php';

class RiffFilterTest extends PHPUnit_Framework_TestCase
{
    public function testCamelCaseToHyphen()
    {
        $expected = 'this-is-hyphened';
        $result = RiffFilter::camelCaseToHyphen('ThisIsHyphened');
        $this->assertEquals($expected, $result);
    }

    public function testHyphenToCamelCase()
    {
        $expected = 'thisIsHyphened';
        $result = RiffFilter::hyphenToCamelCase('this-is-hyphened');
        $this->assertEquals($expected, $result);
    }

    public function testDetectsWrongEmail()
    {
        $isEmail = RiffFilter::isEmail('not@nem.ail.');
        $this->assertEquals(false, $isEmail);
    }

    public function testDetectsCorrectEmail()
    {
        $isEmail = RiffFilter::isEmail('test@test.com');
        $this->assertEquals('test@test.com', $isEmail);
    }

    public function testUrlise()
    {
        $clean = 'societe-liberale';
        $result = RiffFilter::urlise('Société Liberale!');
        $this->assertEquals($clean, $result);
    }
}