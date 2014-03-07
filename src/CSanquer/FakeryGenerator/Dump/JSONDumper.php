<?php

namespace CSanquer\FakeryGenerator\Dump;

use CSanquer\FakeryGenerator\Model\Config;

/**
 * JSONDumper
 *
 * @author Charles Sanquer <charles.sanquer.gmail.com>
 */
class JSONDumper extends AbstractDumper
{
    protected $data = [];
    
    public function initialize(Config $config, $directory)
    {
        $this->setFilename($config, $directory);
    }
    
    public function dumpRow(array $row = array())
    {
        $this->data[] = $row;
    }

    public function finalize()
    {
        file_put_contents($this->filename, json_encode($this->data, JSON_PRETTY_PRINT));
        
        return $this->filename;
    }

    public function getExtension()
    {
        return 'json';
    }
}
