<?php

namespace CSanquer\FakeryGenerator\Dump;

use CSanquer\FakeryGenerator\Model\Config;

/**
 * PerlDumper
 *
 * @author Charles Sanquer <charles.sanquer.gmail.com>
 */
class PerlDumper extends AbstractDumper
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
    private $first;
    
    public function initialize(Config $config, $directory)
    {
        $this->setFilename($config, $directory);
        $this->fileHandler = fopen($this->filename, 'w');
        
        $name = $config->getClassName(true);
        
        $this->first = true;
        $this->indent = 4;
        
        fwrite(
            $this->fileHandler, 
            'my %'.$name.' = ('."\n"
        );
    }
    
    public function dumpRow(array $row = array())
    {
        fwrite($this->fileHandler, $this->dumpElement($row, null, 4, !$this->first));
        if ($this->first) {
            $this->first = false;
        }
    }

    protected function dumpElement($value, $key = null, $indent = 0, $withComma = false)
    {
        $result = '';
        
        if ($withComma) {
            $result .= ",\n";
        }
        
        $indentStr = str_repeat(' ', $indent);
        $keyStr = '';
        if ($key !== '' && $key !== null) {
            $keyStr = (is_int($key) ? $key : '\''.$key.'\'').' => ';
        }
        
        $result .= $indentStr.$keyStr;
        
        if (is_array($value)) {
            $result .= "{\n";
            $first = true;
            foreach ($value as $subKey => $subValue) {
                $result .= $this->dumpElement($subValue, $subKey, $indent+$this->indent, !$first);
                if ($first) {
                    $first = false;
                }
            }
            $result .= "\n".$indentStr.'}';
        } else {
            $result .= '\''.((string) $value).'\'';
        }
        
        return $result;
    }
    
    public function finalize()
    {
        fwrite(
            $this->fileHandler, 
            "\n);\n");
        
        fclose($this->fileHandler);
        
        return $this->filename;
        
//                $format = <<<DUMP
//my %%%s = (
//%s
//);
//
//DUMP;
//        $fakeData = $this->getFakeData();
//
//        $indent = 2;
//        $indentChar = ' ';
//
//        $values = '';
//        foreach ($fakeData as $item) {
//            $values .= str_repeat($indentChar, $indent).'{'."\n";
//            foreach ($item as $key => $value) {
//                $values .= str_repeat($indentChar, $indent*2).'\''.$key.'\' => \''.$value.'\','."\n";
//            }
//            $values .= str_repeat($indentChar, $indent).'},'."\n";
//        }
//
//        $file = $dir.DS.$this->config->getClassName(true).'.pl';
//        file_put_contents($file, sprintf($format, $this->config->getClassNameLastPart(), $values));
//
//        return $file;
    }

    public function getExtension()
    {
        return 'pl';
    }
}
