<?php

namespace CSanquer\FakeryGenerator\Dump;

use CSanquer\FakeryGenerator\Model\Config;

/**
 * DumperInterface
 *
 * @author Charles Sanquer <charles.sanquer@gmail.com>
 */
interface DumperInterface
{
    public function initialize(Config $config, $directory);

    public function dumpRow(array $row = array());

    public function finalize();

    public function getExtension();
}
