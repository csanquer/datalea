<?php

namespace CSanquer\FakeryGenerator\Dump;

use CSanquer\FakeryGenerator\Model\Config;

/**
 * DumperInterface
 *
 * @author Charles Sanquer <charles.sanquer@gmail.com>
 */
interface DumperInterface
{
    /**
     * initialize the dumper
     * 
     * @param \CSanquer\FakeryGenerator\Model\Config $config
     * @param string $directory
     */
    public function initialize(Config $config, $directory);

    /**
     * 
     * @param array $row
     */
    public function dumpRow(array $row = array());

    /**
     * finalize the dump process, save the dumped file and return its path
     * 
     * @return filepath
     */
    public function finalize();

    /**
     * @return file extension
     */
    public function getExtension();
}
