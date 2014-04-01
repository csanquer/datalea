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

    public function initialize(Config $config, $directory)
    {
        $this->setFilename($config, $directory);
        $this->fileHandler = fopen($this->filename, 'w');

        $this->first = true;
        $this->indent = 4;
    }

    public function dumpRow(array $row = array())
    {
        fwrite($this->fileHandler, $this->dumpElement($row, null, 4, !$this->first));
        if ($this->first) {
            $this->first = false;
        }
    }

    public function finalize()
    {
        fclose($this->fileHandler);

        return $this->filename;
    }
}
