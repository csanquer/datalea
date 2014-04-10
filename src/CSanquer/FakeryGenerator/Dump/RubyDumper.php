<?php

namespace CSanquer\FakeryGenerator\Dump;

use CSanquer\FakeryGenerator\Model\Config;

/**
 * RubyDumper
 *
 * @author Charles Sanquer <charles.sanquer.gmail.com>
 */
class RubyDumper extends AbstractStreamDumper
{
    protected function getFileBeginning(Config $config)
    {
        return $config->getClassNameLastPart(true).' = {'."\n";
    }

    protected function getFileEnding()
    {
        return "\n}\n";
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

    public function getExtension()
    {
        return 'rb';
    }
}
