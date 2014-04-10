<?php

namespace CSanquer\FakeryGenerator\Dump;

use \CSanquer\FakeryGenerator\Model\Config;
use \Symfony\Component\Yaml\Yaml;

/**
 * YAMLDumper
 *
 * @author Charles Sanquer <charles.sanquer.gmail.com>
 */
class YAMLDumper extends AbstractDumper
{
    /**
     *
     * @var int
     */
    protected $line;

    /**
     *
     * @var array
     */
    protected $data;

    /**
     *
     * @var string
     */
    protected $collectionName;

    /**
     *
     * @var string
     */
    protected $itemName;

    public function initialize(Config $config, $directory)
    {
        $this->setFilename($config, $directory);
        $this->data = [];
        $this->collectionName = $config->getClassNameLastPart(true);
        $this->itemName = $config->getClassNameLastPart();
        $this->line = 1;
    }

    public function dumpRow(array $row = array())
    {
        $this->data[$this->itemName.'_'.$this->line] = $row;
        $this->line++;
    }

    public function finalize()
    {
        file_put_contents($this->filename, Yaml::dump([ $this->collectionName => $this->data], 6, 4));

        return $this->filename;
    }

    public function getExtension()
    {
        return 'yml';
    }
}
