<?php

require_once 'PHPUnit/Autoload.php';

require_once '../riff.php';

use Riff\Filter\Filter;

class RiffFilterTest extends PHPUnit_Framework_TestCase
{
    public function testCamelCaseToHyphen()
    {
        $expected = 'this-is-hyphened';
        $result = Filter::camelCaseToHyphen('ThisIsHyphened');
        $this->assertEquals($expected, $result);
    }

    public function testHyphenToCamelCase()
    {
        $expected = 'thisIsHyphened';
        $result = Filter::hyphenToCamelCase('this-is-hyphened');
        $this->assertEquals($expected, $result);
    }

    public function testDetectsWrongEmail()
    {
        $isEmail = Filter::isEmail('not@nem.ail.');
        $this->assertFalse($isEmail);
    }

    public function testDetectsCorrectEmail()
    {
        $isEmail = Filter::isEmail('test@test.com');
        $this->assertEquals('test@test.com', $isEmail);
    }

    public function testUrlise()
    {
        $clean = 'societe-liberale';
        $result = Filter::urlise('Société Liberale!');
        $this->assertEquals($clean, $result);
    }
}