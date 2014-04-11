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
    /**
     *
     * @var string
     */
    protected $filename;

    /**
     * 
     * @param \CSanquer\FakeryGenerator\Model\Config $config
     * @param string $directory
     * @param bool $filenameWithDate
     */
    protected function setFilename(Config $config, $directory, $filenameWithDate = false)
    {
        $fs = new Filesystem();
        if (!$fs->exists($directory)) {
            $fs->mkdir($directory);
        }

        $this->filename = realpath($directory).DIRECTORY_SEPARATOR.
            $config->getClassName(true).($filenameWithDate ? '_'.date('Y-m-d_H-i-s') : '').
            '.'.$this->getExtension();
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
