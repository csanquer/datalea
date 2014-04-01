<?php

namespace CSanquer\FakeryGenerator\Dump;

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

/**
 * DumpManager
 *
 * @author Charles Sanquer <charles.sanquer@gmail.com>
 */
class DumpManager
{
    /**
     *
     * @var Config
     */
    protected $config;

    /**
     *
     * @var array
     */
    protected $fakeData;

    /**
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     *
     * @return array
     */
    public static function getAvailableFormats()
    {
        return [
            'csv' => 'CSV',
            'excel' => 'Excel',
            'yaml' => 'YAML',
            'xml' => 'XML',
            'json' => 'JSON',
            'sql' => 'SQL',
            'php' => 'PHP',
            'perl' => 'Perl',
            'ruby' => 'Ruby',
            'python' => 'Python',
        ];
    }

    /**
     *
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     *
     * @param Config $config
     *
     * @return Dumper
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     *
     * @param string   $tmpDir
     * @param DateTime $date
     *
     * @return string zip filename
     *
     * @throws RuntimeException
     */
    public function dump($tmpDir, $date = null)
    {
        $date = $date instanceof DateTime ? $date : new DateTime();

        $fs = new Filesystem();

        $workingDir = time().'_'.uniqid();
        $workingPath = $tmpDir.DS.$workingDir;

        if (!$fs->exists($workingPath)) {
            $fs->mkdir($workingPath, 0777);
        }

        if (!$this->config->hasSeed()) {
            $this->config->generateSeed();
        }

        $files = [];

        $files[] = $this->saveConfigAsXML($workingPath);

        foreach ($this->config->getFormats() as $format) {
            switch ($format) {
                case 'csv':
                    $files[] = $this->dumpCSV($workingPath);
                    break;
                case 'excel':
                    $files[] = $this->dumpExcel($workingPath);
                    break;
                case 'yaml':
                    $files[] = $this->dumpYAML($workingPath);
                    break;
                case 'xml':
                    $files[] = $this->dumpXML($workingPath);
                    break;
                case 'json':
                    $files[] = $this->dumpJSON($workingPath);
                    break;
                case 'sql':
                    $files[] = $this->dumpSQL($workingPath);
                    break;
                case 'php':
                    $files[] = $this->dumpPHP($workingPath);
                    break;
                case 'perl':
                    $files[] = $this->dumpPerl($workingPath);
                    break;
                case 'ruby':
                    $files[] = $this->dumpRuby($workingPath);
                    break;
                case 'python':
                    $files[] = $this->dumpPython($workingPath);
                    break;
            }
        }

        $zipname = $tmpDir.DS.'archive_'.$workingDir.'.zip';
        $zip = new ZipArchive();
        if ($zip->open($zipname, ZipArchive::CREATE)!==TRUE) {
            throw new RuntimeException;("cannot create zip archive $filename\n");
        }

        foreach ($files as $file) {
            $zip->addFile($file, 'datalea_'.$this->config->getClassNameLastPart().'_'.$date->format('Y-m-d_H-i-s').DS.basename($file));
        }
        $zip->close();

        return $zipname;
    }
}
