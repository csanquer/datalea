<?php

namespace CSanquer\FakeryGenerator\Dump;

use CSanquer\FakeryGenerator\Config\ConfigSerializer;
use CSanquer\FakeryGenerator\Model\Config;
use Symfony\Component\Filesystem\Filesystem;

/**
 * DumpManagerInterface
 *
 * @author Charles Sanquer <charles.sanquer@gmail.com>
 */
interface DumpManagerInterface
{
    /**
     *
     * @return array
     */
    public static function getAvailableFormats();

    /**
     * 
     * @param Config $config
     * @param string   $outputDir
     * @param boolean $zipped
     * @param string $configFormat json, xml or all
     * 
     * @return array of filenames
     */
    public function dump(Config $config, $outputDir, $zipped = true,  $configFormat = 'json');
}
