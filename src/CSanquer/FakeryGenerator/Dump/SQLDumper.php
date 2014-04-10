<?php
namespace CSanquer\FakeryGenerator\Dump;

use CSanquer\FakeryGenerator\Model\Config;

/**
 * SQLDumper
 *
 * @author Charles Sanquer <charles.sanquer.gmail.com>
 */
class SQLDumper extends AbstractStreamDumper
{
    /**
     *
     * @var bool
     */
    protected $hasHeader;

    /**
     *
     * @var \PDO
     */
    protected $pdo;
    
    protected function getFileBeginning(Config $config)
    {
        return '# This is a fix for InnoDB in MySQL >= 4.1.x'."\n".
            '# It "suspends judgement" for fkey relationships until are tables are set.'."\n".
            'SET FOREIGN_KEY_CHECKS = 0;'."\n\n".
            'INSERT INTO `'.$config->getClassName(true).'` ';
    }

    protected function getFileEnding()
    {
        return ';'."\n\n".'# This restores the fkey checks, after having unset them earlier'."\n".
            'SET FOREIGN_KEY_CHECKS = 1;'."\n";
    }
    
    public function initialize(Config $config, $directory)
    {
        $this->hasHeader = false;
        parent::initialize($config, $directory);
        
        $this->pdo = new \PDO('sqlite::memory:');
    }

    protected function dumpElement($value, $key = null, $indent = 0, $withComma = false) 
    {
        $content = '';
        
        $rawFlat = $this->convertRowAsFlat($value);
        
        $flat = [];
        foreach ($rawFlat as $key => $value) {
            $flat[$key] = $this->pdo->quote($value);
        }
        
        if (!$this->hasHeader) {
            $content .= '(`'.implode('`, `', array_keys($flat)).'`) VALUES'."\n";
            $this->hasHeader = true;
        }

        if ($withComma) {
            $content .= ",\n";
        }
        
        $content .= '('.implode(', ', $flat).')';
        
        return $content;
    }
    
    public function getExtension()
    {
        return 'sql';
    }
}
