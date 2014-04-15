<?php

namespace CSanquer\FakeryGenerator\Test\Dump;

use CSanquer\FakeryGenerator\Config\ConfigSerializer;
use CSanquer\FakeryGenerator\Dump\DumpManager;
use CSanquer\FakeryGenerator\Model\Column;
use CSanquer\FakeryGenerator\Model\Config;
use CSanquer\FakeryGenerator\Model\Variable;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Stopwatch\Stopwatch;

class AbstractDumpManagerTestCase extends DumperTestCase
{

    /**
     * @var DumpManager
     */
    protected $dumpManager;

    protected function unzip($file)
    {
        $zippedFiles = [];

        $zip = new \ZipArchive();
        $zip->open($file);
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $f = $zip->statIndex($i);
            if (isset($f['name'])) {
                $zippedFiles[] = $f['name'];
            }
        }
        $zip->close();

        sort($zippedFiles);

        return $zippedFiles;
    }
}
