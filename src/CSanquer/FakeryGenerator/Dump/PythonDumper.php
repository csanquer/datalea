<?php

namespace CSanquer\FakeryGenerator\Dump;

use CSanquer\FakeryGenerator\Model\Config;

/**
 * PythonDumper
 *
 * @author Charles Sanquer <charles.sanquer.gmail.com>
 */
class PythonDumper extends AbstractDumper
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
            $name.' = ['."\n"
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
            $keyStr = (is_int($key) ? $key : '\''.$key.'\'').': ';
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
            "\n]\n");
        
        fclose($this->fileHandler);
        
        return $this->filename;
        
//                $format = <<<DUMP
//%s = [
//%s
//]
//
//DUMP;
//        $fakeData = $this->getFakeData();
//
//        $indent = 2;
//        $indentChar = ' ';
//
//        $values = '';
//        $first1 = true;
//        foreach ($fakeData as $item) {
//            if ($first1) {
//                $first1 = false;
//            } else {
//                $values .= ','."\n";
//            }
//            $values .= str_repeat($indentChar, $indent).'{';
//
//            $first2 = true;
//            foreach ($item as $key => $value) {
//                if ($first2) {
//                    $first2 = false;
//                    $values .= "\n";
//                } else {
//                    $values .= ','."\n";
//                }
//                $values .= str_repeat($indentChar, $indent*2).'\''.$key.'\': \''.$value.'\'';
//            }
//
//            $values .= "\n".str_repeat($indentChar, $indent).'}';
//        }
//
//        $file = $dir.DS.$this->config->getClassName(true).'.py';
//        file_put_contents($file, sprintf($format, $this->config->getClassNameLastPart(), $values));
//
//        return $file;
    }

    public function getExtension()
    {
        return 'py';
    }
}
