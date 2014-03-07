<?php

namespace CSanquer\FakeryGenerator\Dump;

use CSanquer\ColibriCsv\CsvWriter;
use CSanquer\ColibriCsv\Dialect;
use CSanquer\FakeryGenerator\Model\Config;

/**
 * CSVDumper
 *
 * @author Charles Sanquer <charles.sanquer.gmail.com>
 */
class CSVDumper extends AbstractDumper
{
    /**
     *
     * @var CsvWriter 
     */
    protected $csvWriter;
    
    public function initialize(Config $config, $directory)
    {
        $this->setFilename($config, $directory);
        $this->csvWriter = new CsvWriter($config->getCsvDialect() ?: Dialect::createExcelDialect());
        $this->csvWriter->open($this->filename);
    }
    
    public function dumpRow(array $row = array())
    {
        $this->csvWriter->writeRow($row);
    }

    public function finalize()
    {
        $this->csvWriter->close();
        
        return $this->filename;
    }

    protected function getExtension()
    {
        return 'csv';
    }
}
