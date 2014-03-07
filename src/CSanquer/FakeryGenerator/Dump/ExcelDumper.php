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
    protected $row;
    
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
        $this->row = 1;
        
    }
    
    public function dumpRow(array $row = array())
    {
        $sheet = $this->excel->getActiveSheet();
        $this->col = 0;
        $this->row++;
        foreach ($row as $value) {
            $sheet->setCellValueByColumnAndRow($this->col, $this->row, $value);
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
