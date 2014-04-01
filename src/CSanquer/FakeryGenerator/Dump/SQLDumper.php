<?php
namespace CSanquer\FakeryGenerator\Dump;

use CSanquer\FakeryGenerator\Model\Config;

/**
 * SQLDumper
 *
 * @author Charles Sanquer <charles.sanquer.gmail.com>
 */
class SQLDumper extends AbstractDumper
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
        return 'sql';
    }
}
