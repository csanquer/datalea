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
    
    public function initialize(Config $config, $directory)
    {
        $this->setFilename($config, $directory);
        $this->excel = new \PHPExcel();
        $sheet = $this->excel->getActiveSheet();
        $sheet->setTitle($config->getClassNameLastPart());
        
        $this->col = 0;
        $this->line = 1;
        
        $header = $config->getColumnNames(true);
        foreach ($header as $key) {
            $sheet->setCellValueByColumnAndRow($this->col, $this->line, $key);
            $sheet->getColumnDimensionByColumn($this->col)->setAutoSize(true);
            $this->col++;
        }
    }
    
    public function dumpRow(array $row = array())
    {
        $sheet = $this->excel->getActiveSheet();
        $this->col = 0;
        $this->line++;
        $flat = $this->convertRowAsFlat($row);
        
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
