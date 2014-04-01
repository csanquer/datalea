<?php

namespace CSanquer\FakeryGenerator\Dump;

use CSanquer\FakeryGenerator\Model\Config;

/**
 * PHPDumper
 *
 * @author Charles Sanquer <charles.sanquer.gmail.com>
 */
class PHPDumper extends AbstractStreamDumper
{
    protected function getFileBeginning(Config $config)
    {
        return "<?php\n\n".
            '$'.$config->getClassName(true).' = array('."\n";
    }

    protected function getFileEnding()
    {
        return "\n);\n";
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
            $result .= "array(\n";
            $first = true;
            foreach ($value as $subKey => $subValue) {
                $result .= $this->dumpElement($subValue, $subKey, $indent+$this->indent, !$first);
                if ($first) {
                    $first = false;
                }
            }
            $result .= "\n".$indentStr.')';
        } else {
            $result .= var_export($value, true);
        }

        return $result;
    }

    public function getExtension()
    {
        return 'php';
    }
}
