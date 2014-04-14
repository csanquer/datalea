<?php

namespace CSanquer\FakeryGenerator\Helper;

/**
 * Memory usage helper
 * 
 * @author Charles Sanquer <charles.sanquer@gmail.com>
 */
class Memory
{
    /**
     * 
     * @param int|float $size
     * @param int $decimals default = 3
     * @param string $baseUnit default = 'B' , Bytes
     * @return string
     */
    public static function convert($size, $decimals = 3, $baseUnit = 'B')
    {
        $unit = array('', 'K', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y');
        $exponent =  floor(log($size, 1024));
        return round($size / pow(1024, $exponent), (int) $decimals).' '.$unit[$exponent].$baseUnit;
    }
}
