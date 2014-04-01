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
    /**
     *
     * @var bool
     */
    protected $hasHeader;
    
    /**
     *
     * @var resource
     */
    protected $fileHandler;

    /**
     *
     * @var bool
     */
    private $first;
    
    public function initialize(Config $config, $directory)
    {
        $this->setFilename($config, $directory);
        $this->fileHandler = fopen($this->filename, 'w');
        $this->hasHeader = false;
        $this->first = true;
        fwrite(
            $this->fileHandler, 
            '# This is a fix for InnoDB in MySQL >= 4.1.x'."\n".
            '# It "suspends judgement" for fkey relationships until are tables are set.'."\n".
            'SET FOREIGN_KEY_CHECKS = 0;'."\n\n".
            'INSERT INTO `'.$config->getClassName(true).'` '
        );
        
    }
    
    public function dumpRow(array $row = array())
    {
        $content = '';
        $flat = $this->convertRowAsFlat($row);

        if (!$this->hasHeader) {
            $content .= '(`'.implode('`, `', array_keys($flat)).'`) VALUES'."\n";
            $this->hasHeader = true;
        }
        
        if ($this->first) {
            $this->first = false;
        } else {
            $content .= ','."\n";
        }
        $content .= '(\''.implode('\', \'', $flat).'\')';
        
        fwrite($this->fileHandler, $content);
    }

    public function finalize()
    {
        fwrite(
            $this->fileHandler, 
            ';'."\n\n".'# This restores the fkey checks, after having unset them earlier'."\n".
            'SET FOREIGN_KEY_CHECKS = 1;'."\n");
        
        fclose($this->fileHandler);
        
        return $this->filename;
    }

    public function getExtension()
    {
        return 'sql';
    }
}
