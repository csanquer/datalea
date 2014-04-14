<?php

namespace CSanquer\FakeryGenerator\Test\Helper;

use CSanquer\FakeryGenerator\Helper\Memory;

class MemoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers CSanquer\FakeryGenerator\Helper\Memory::convert
     * @dataProvider providerConvert
     */
    public function testConvert($size, $decimals, $baseUnit, $expected)
    {
        $this->assertEquals($expected, Memory::convert($size, $decimals, $baseUnit));
    }
    
    public function providerConvert()
    {
        return [
            ['1054', 3, 'B', '1.029 KB'],
            ['1215465', 3, 'o', '1.159 Mo'],
            ['1215465', 1, 'B', '1.2 MB'],
        ];
    }
}
