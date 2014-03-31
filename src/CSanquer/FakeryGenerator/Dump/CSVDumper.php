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

    /**
     *
     * @var bool
     */
    protected $hasHeader;

    public function initialize(Config $config, $directory)
    {
        $this->setFilename($config, $directory);
        $this->csvWriter = new CsvWriter($config->getCsvDialect() ?: Dialect::createExcelDialect());
        $this->csvWriter->open($this->filename);
        $this->hasHeader = false;
//        $this->csvWriter->writeRow($config->getColumnNames(true));
    }

    public function dumpRow(array $row = array())
    {
        $flat = $this->convertRowAsFlat($row);

        if (!$this->hasHeader) {
            $this->csvWriter->writeRow(array_keys($flat));
            $this->hasHeader = true;
        }

        $this->csvWriter->writeRow($flat);
    }

    public function finalize()
    {
        $this->csvWriter->close();

        return $this->filename;
    }

    public function getExtension()
    {
        return 'csv';
    }
}
