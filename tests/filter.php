<?php

require_once 'PHPUnit/Autoload.php';

require_once '../riff.php';

class RiffFilterTest extends PHPUnit_Framework_TestCase
{
    public function testCamelCaseToHyphen()
    {
        $expected = 'this-is-camel-cased';
        $result = RiffFilter::camelCaseToHyphen('ThisIsCamelCased');
        $this->assertEquals($expected, $result);
    }
}