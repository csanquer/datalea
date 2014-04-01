<?php

namespace CSanquer\FakeryGenerator\Dump;

use CSanquer\FakeryGenerator\Model\Config;

/**
 * PerlDumper
 *
 * @author Charles Sanquer <charles.sanquer.gmail.com>
 */
class PerlDumper extends AbstractStreamDumper
{
    public function initialize(Config $config, $directory)
    {
        parent::initialize($config, $directory);
        $name = $config->getClassName(true);

        fwrite(
            $this->fileHandler,
            'my %'.$name.' = ('."\n"
        );
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
        fwrite($this->fileHandler, "\n);\n");

        return parent::finalize();
    }

    public function getExtension()
    {
        return 'pl';
    }
}
