<?php

namespace CSanquer\FakeryGenerator\Dump;

use CSanquer\FakeryGenerator\Model\Config;

/**
 * PHPDumper
 *
 * @author Charles Sanquer <charles.sanquer.gmail.com>
 */
class PHPDumper extends AbstractDumper
{
    public function initialize(Config $config, $directory)
    {

    }
    
    public function dumpRow(array $row = array())
    {

    }

    public function finalize()
    {

    }

    public function getExtension()
    {
        return 'php';
    }
}
