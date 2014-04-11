<?php

namespace CSanquer\FakeryGenerator\Dump;

use CSanquer\FakeryGenerator\Model\Config;

/**
 * ExcelDumper
 *
 * @author Charles Sanquer <charles.sanquer.gmail.com>
 */
class ExcelDumper extends AbstractDumper
{
    /**
     *
     * @var int
     */
    protected $col;

    /**
     *
     * @var int
     */
    protected $line;

    /**
     *
     * @var \PHPExcel
     */
    protected $excel;

    /**
     *
     * @var bool
     */
    protected $hasHeader;

    public function initialize(Config $config, $directory, $filenameWithDate = false)
    {
        $this->setFilename($config, $directory, $filenameWithDate);
        $this->excel = new \PHPExcel();
        $sheet = $this->excel->getActiveSheet();
        $sheet->setTitle($config->getClassNameLastPart());

        $this->col = 0;
        $this->line = 0;
    }

    public function dumpRow(array $row = array())
    {
        $sheet = $this->excel->getActiveSheet();

        $flat = $this->convertRowAsFlat($row);

        if (!$this->hasHeader) {
            $this->col = 0;
            $this->line++;
            foreach (array_keys($flat) as $key) {
                $sheet->setCellValueByColumnAndRow($this->col, $this->line, $key);
                $sheet->getColumnDimensionByColumn($this->col)->setAutoSize(true);
                $this->col++;
            }

            $this->hasHeader = true;
        }

        $this->col = 0;
        $this->line++;
        foreach ($flat as $value) {
            $sheet->setCellValueByColumnAndRow($this->col, $this->line, $value);
            $this->col++;
        }
    }

    public function finalize()
    {
        $writer = new \PHPExcel_Writer_Excel2007($this->excel);
        $writer->setPreCalculateFormulas(false);
        $writer->save($this->filename);

        return $this->filename;
    }

    public function getExtension()
    {
        return 'xlsx';
    }
}
