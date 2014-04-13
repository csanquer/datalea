<?php

namespace CSanquer\FakeryGenerator\Dump;

use CSanquer\FakeryGenerator\Config\ConfigSerializer;
use CSanquer\FakeryGenerator\Model\Config;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Output\OutputInterface;
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
     * @param boolean $zipped
     * @param string   $outputDir
     * @param string $configFormat json, xml or all
     * @param OutputInterface $output default = null
     * @param HelperSet $helperSet default = null
     * 
     * @return array of filenames
     */
    public function dump(Config $config, $outputDir, $zipped = true, $configFormat = 'json', OutputInterface $output = null, HelperSet $helperSet = null)
    {
        $fs = new Filesystem();

        if (!$fs->exists($outputDir)) {
            $fs->mkdir($outputDir, 0777);
        }

        $outputDir = realpath($outputDir);
        
        $files = [];
        
        // dump current config
        if ($configFormat == 'json' || $configFormat == 'all') {
            if ($output) {
                $output->writeln('Dumping Configuration as <comment>JSON</comment> ...');
            }
            
            $files['config_json'] = $this->configSerializer->dump($config, $outputDir, 'json');
        }
        
        if ($configFormat == 'xml' || $configFormat == 'all') {
            if ($output) {
                $output->writeln('Dumping Configuration as <comment>XML</comment> ...');
            }
            $files['config_xml'] = $this->configSerializer->dump($config, $outputDir, 'xml');
        }
        
        // create and initilize dumpers
        $dumpers = [];
        
        $formats = $config->getFormats();
        if (count($formats)) {
            if ($output) {
                $output->writeln('Initializing files ...');
            }
            
            foreach ($formats as $format) {
                $dumper = $this->dumperFactory($format);
                if ($dumper instanceof DumperInterface) {
                    $dumper->initialize($config, $outputDir, !$zipped);
                    $dumpers[$format] = $dumper;
                }
            }
            
            if ($output) {
                $output->writeln('Formats : <comment>'.implode('</comment>, <comment>', array_keys($dumpers)).'</comment>');
            }
        }
        
        if (count($dumpers)) {
            // generate random data and write row by row
            $faker = $config->createFaker();

            if ($helperSet && $output) {
                $progress = $helperSet->get('progress');
                $progress->start($output, $config->getFakeNumber());
                $progress->setRedrawFrequency(floor($config->getFakeNumber()/100));
                $progress->setBarCharacter('<comment>=</comment>');
            }

            if ($output) {
                $output->writeln('Generating <info>'.$config->getFakeNumber().'</info> rows ...');
            }

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

                if ($helperSet && $output) {
                    $progress->advance();
                }
            }

            if ($helperSet && $output) {
                $progress->finish();
            }

            if ($output) {
                $output->writeln('Finalizing files ...');
            }

            // finalize and save dumped files
            foreach ($dumpers as $dumper) {
                if ($dumper instanceof DumperInterface) {
                    $files[$dumper->getExtension()] = $dumper->finalize();
                }
            }
        }
        
        // zip files if required
        if ($zipped) {
            if ($output) {
                $output->writeln('Compressing files into zip ...');
            }
            $zipfile = $this->zip('fakery_'.$config->getClassNameLastPart().'_'.date('Y-m-d_H-i-s'), $files, $outputDir);
            $fs->remove($files);
            $files = ['zip' => $zipfile];
        }
        
        return $files;
    }
    
    protected function zip($basename, $files, $outputDir) 
    {
        $zipname = $outputDir.DIRECTORY_SEPARATOR.$basename.'.zip';
        $zip = new \ZipArchive();
        if ($zip->open($zipname, \ZipArchive::CREATE)!==TRUE) {
            throw new \RuntimeException("cannot create zip archive $zipname\n");
        }

        foreach ($files as $file) {
            $zip->addFile($file, $basename.DIRECTORY_SEPARATOR.basename($file));
        }
        $zip->close();

        return $zipname;
    }
}
