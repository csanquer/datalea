<?php

namespace CSanquer\FakeryGenerator\Dump;

use CSanquer\FakeryGenerator\Model\Config;

/**
 * AbstractStreamDumper
 *
 * @author Charles Sanquer <charles.sanquer.gmail.com>
 */
abstract class AbstractStreamDumper extends AbstractDumper
{
    /**
     *
     * @var resource
     */
    protected $fileHandler;

    /**
     *
     * @var int
     */
    protected $indent = 0 ;

    /**
     *
     * @var bool
     */
    protected $first;

    public function initialize(Config $config, $directory, $filenameWithDate = false)
    {
        $this->setFilename($config, $directory, $filenameWithDate);
        $this->fileHandler = fopen($this->filename, 'w');

        $this->first = true;
        $this->indent = 4;
        
        $beginning = $this->getFileBeginning($config);
        if ($beginning !== null && $beginning !== '') {
            fwrite($this->fileHandler, $beginning);
        }
    }

    public function dumpRow(array $row = array())
    {
        fwrite($this->fileHandler, $this->dumpElement($row, null, 4, !$this->first));
        if ($this->first) {
            $this->first = false;
        }
    }
    
    abstract protected function getFileBeginning(Config $config);
    
    abstract protected function getFileEnding();

    abstract protected function dumpElement($value, $key = null, $indent = 0, $withComma = false);

    public function finalize()
    {
        $end = $this->getFileEnding();
        if ($end !== null && $end !== '') {
            fwrite($this->fileHandler, $end);
        }
        
        fclose($this->fileHandler);

        return $this->filename;
    }
}
