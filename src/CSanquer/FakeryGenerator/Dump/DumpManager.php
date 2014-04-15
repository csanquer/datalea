<?php

namespace CSanquer\FakeryGenerator\Dump;

use CSanquer\FakeryGenerator\Config\ConfigSerializer;
use CSanquer\FakeryGenerator\Model\Config;
use Symfony\Component\Filesystem\Filesystem;

/**
 * DumpManager
 *
 * @author Charles Sanquer <charles.sanquer@gmail.com>
 */
class DumpManager
{
    /**
     *
     * @var ConfigSerializer
     */
    protected $configSerializer;

    /**
     *
     * @param ConfigSerializer $configSerializer
     */
    public function __construct(ConfigSerializer $configSerializer)
    {
        $this->configSerializer = $configSerializer;
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
     * @param string $format
     * @return DumperInterface
     */
    protected function dumperFactory($format)
    {
        $dumper = null;
        switch ($format) {
            case 'csv':
                $dumper = new CSVDumper();
                break;
            case 'excel':
                $dumper = new ExcelDumper();
                break;
            case 'yaml':
                $dumper = new YAMLDumper();
                break;
            case 'xml':
                $dumper = new XMLDumper();
                break;
            case 'json':
                $dumper = new JSONDumper();
                break;
            case 'sql':
                if (extension_loaded('PDO') && extension_loaded('pdo_sqlite')) {
                    $dumper = new SQLDumper();
                }
                break;
            case 'php':
                $dumper = new PHPDumper();
                break;
            case 'perl':
                $dumper = new PerlDumper();
                break;
            case 'ruby':
                $dumper = new RubyDumper();
                break;
            case 'python':
                $dumper = new PythonDumper();
                break;
        }
        
        return $dumper;
    }
    
    /**
     * 
     * @param Config $config
     * @param string   $outputDir
     * @param boolean $zipped
     * @param string $configFormat json, xml or all
     * 
     * @return array of filenames
     */
    public function dump(
        Config $config,
        $outputDir,
        $zipped = true, 
        $configFormat = 'json'
    ) {
        $fs = new Filesystem();
        if (!$fs->exists($outputDir)) {
            $fs->mkdir($outputDir, 0777);
        }

        $outputDir = realpath($outputDir);
        
        // dump config files
        $files = $this->createConfigFiles(
            $config, 
            $outputDir, 
            $configFormat  == 'all' ? ['json', 'xml'] : (array) $configFormat
        );
        
        // dump random data files
        $dumpers = $this->createDumpers($config, $outputDir, $zipped);
        if (count($dumpers)) {
            $this->dumpRowsToAllFiles($config, $dumpers);
            $files = array_merge($files, $this->finalizeAllDumps($dumpers));
        }
        
        // zip files if required
        if ($zipped) {
            $files = $this->compressFiles($config, $files, $outputDir, $fs);
        }
        
        return $files;
    }

    protected function createConfigFiles(Config $config, $outputDir, array $formats)
    {
        // dump current config
        $files = [];
        foreach ($formats as $format) {
            $files['config_'.$format] = $this->dumpConfigFile($config, $outputDir, $format);
        }
        
        return $files;
    }
    
    protected function dumpConfigFile(Config $config, $outputDir, $format = 'json')
    {
        return $this->configSerializer->dump($config, $outputDir, $format);
    }
    
    protected function createDumpers(Config $config, $outputDir, $zipped)
    {
        // create and initialize dumpers
        $dumpers = [];
        $formats = $config->getFormats();
        if (count($formats)) {
            foreach ($formats as $format) {
                $dumper = $this->dumperFactory($format);
                if ($dumper instanceof DumperInterface) {
                    $dumper->initialize($config, $outputDir, !$zipped);
                    $dumpers[$format] = $dumper;
                }
            }
        }
        
        return $dumpers;
    }
    
    protected function dumpRowsToAllFiles(Config $config, array $dumpers)
    {
        // generate random data and write row by row
        $faker = $config->createFaker();
        for ($index = 1; $index <= $config->getFakeNumber(); $index++) {
            $this->generateAndDumpRows($faker, $config, $dumpers);
        }
    }

    protected function generateAndDumpRows(\Faker\Generator $faker, Config $config, array $dumpers)
    {
        //generate 1 row
        $values = [];
        $config->generateVariableValues($faker, $values);
        $data = $config->generateColumnValues($values);

        //dump the row to each file
        foreach ($dumpers as $dumper) {
            if ($dumper instanceof DumperInterface) {
                $dumper->dumpRow($data);
            }
        }
    }
    
    protected function finalizeAllDumps(array $dumpers)
    {
        // finalize and save dumped files
        foreach ($dumpers as $dumper) {
            $files[$dumper->getExtension()] = $this->finalizeDump($dumper);
        }

        return $files;
    }
    
    protected function finalizeDump(DumperInterface $dumper)
    {
        return $dumper->finalize();
    }
    
    protected function compressFiles(Config $config, array $files, $outputDir, Filesystem $fs)
    {
        $zipfile = $this->zip(
            'fakery_'.$config->getClassNameLastPart().'_'.date('Y-m-d_H-i-s'),
            $files,
            $outputDir
        );

        if (!empty($zipfile)) {
            $fs->remove($files);
            $files = ['zip' => $zipfile];
        }
        
        return $files;
    }
    
    /**
     * zip files into a zip archive
     * 
     * @param string $basename
     * @param array $files
     * @param string $outputDir
     * @return string
     */
    protected function zip($basename, $files, $outputDir) 
    {
        $zipname = $outputDir.DIRECTORY_SEPARATOR.$basename.'.zip';
        $zip = new \ZipArchive();
        if ($zip->open($zipname, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            foreach ($files as $file) {
                $zip->addFile($file, $basename.DIRECTORY_SEPARATOR.basename($file));
            }
            $zip->close();
        }
        
        return file_exists($zipname) ? $zipname : null;
    }
}
