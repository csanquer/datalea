<?php

namespace CSanquer\FakeryGenerator\Dump;

use CSanquer\FakeryGenerator\Model\Config;
use Symfony\Component\Filesystem\Filesystem;

/**
 * AbstractDumper
 *
 * @author Charles Sanquer <charles.sanquer.gmail.com>
 */
abstract class AbstractDumper implements DumperInterface
{
    protected $filename;

    protected function setFilename(Config $config, $directory)
    {
        $fs = new Filesystem();
        if (!$fs->exists($directory)) {
            $fs->mkdir($directory);
        }

        $this->filename = realpath($directory).DIRECTORY_SEPARATOR.$config->getClassName(true).'.'.$this->getExtension();
    }

    protected function convertRowAsFlat(array $row = array())
    {
        $flat = [];
        foreach ($row as $key => $value) {
            if (is_array($value)) {
                $tmp = $this->convertRowAsFlat($value);
                foreach ($tmp as $key2 => $value2) {
                    $flat[$key.'-'.$key2] = $value2;
                }
            } else {
                $flat[$key] = $value;
            }
        }

        return $flat;
    }
}
