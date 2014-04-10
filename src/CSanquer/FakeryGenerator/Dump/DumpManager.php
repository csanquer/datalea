<?php

namespace CSanquer\FakeryGenerator\Dump;

use CSanquer\FakeryGenerator\Config\ConfigSerializer;
use CSanquer\FakeryGenerator\Model\Config;
use Symfony\Component\Filesystem\Filesystem;

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
                $dumper = new SQLDumper();
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
     * @param boolean $zipped
     * @param string   $outputDir
     * @param \DateTime $date
     *
     * @return array of filenames
     */
    public function dump(Config $config, $outputDir, $zipped = true, $date = 'now')
    {
        $date = $date instanceof \DateTime ? $date : new \DateTime($date);

        $fs = new Filesystem();

        if (!$fs->exists($outputDir)) {
            $fs->mkdir($outputDir, 0777);
        }

        $outputDir = realpath($outputDir);
        
        $dumpers = [];
        foreach ($config->getFormats() as $format) {
            $dumper = $this->dumperFactory($format);
            if ($dumper instanceof DumperInterface) {
                $dumper->initialize($config, $outputDir);
                $dumpers[$format] = $dumper;
            }
        }
        
        $faker = $config->createFaker();
        
        for ($index = 1; $index <= $config->getFakeNumber(); $index++) {
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
        
        $files = [];
        foreach ($dumpers as $dumper) {
            if ($dumper instanceof DumperInterface) {
                $files[$dumper->getExtension()] = $dumper->finalize();
            }
        }
        
        $files['config_json'] = $this->configSerializer->dump($config, $outputDir, 'json');
        $files['config_xml'] = $this->configSerializer->dump($config, $outputDir, 'xml');
        
        if ($zipped) {
            $zipfile = $this->zip('fakery_'.$config->getClassNameLastPart().'_'.$date->format('Y-m-d_H-i-s'), $files, $outputDir);
            
            $fs->remove($files);
            $files = ['zip' => $zipfile];
        }
        
        return $files;
    }
    
    public function zip($basename, $files, $outputDir) 
    {
        $zipname = $outputDir.DS.$basename.'.zip';
        $zip = new \ZipArchive();
        if ($zip->open($zipname, \ZipArchive::CREATE)!==TRUE) {
            throw new \RuntimeException;("cannot create zip archive $zipname\n");
        }

        foreach ($files as $file) {
            $zip->addFile($file, $basename.DS.basename($file));
        }
        $zip->close();

        return $zipname;
    }
}
