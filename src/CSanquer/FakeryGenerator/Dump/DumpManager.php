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
        return array(
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
        );
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
     * @return string
     */
    public function saveConfigAsXML($dir)
    {
        $name = $this->config->getClassName(true).'_datalea_config';

        $root = new CdataSimpleXMLElement('<?xml version=\'1.0\' encoding=\'utf-8\'?><datalea/>');

        $root->addAttribute('classname', $this->config->getClassName());
        $root->addAttribute('locale', $this->config->getLocale());
        $root->addAttribute('seed', $this->config->getSeed());
        $root->addAttribute('fakenumber', $this->config->getFakeNumber());

        $formatsElt = $root->addChild('formats');
        foreach ($this->config->getFormats() as $format) {
            $formatElt = $formatsElt->addChild('format', $format);
        }

        $csvFormat = $this->config->getCsvFormat();
        if ($csvFormat && $this->config->hasFormat('csv')) {
            $formatOptionsElt = $root->addChild('formatOptions');
            $csvElt = $formatOptionsElt->addChild('csv');
            $csvElt->addChildCData('delimiter', $csvFormat->getDelimiter());
            $csvElt->addChildCData('enclosure', $csvFormat->getEnclosure());
            $csvElt->addChild('encoding', $csvFormat->getEncoding());
            $csvElt->addChild('eol', $csvFormat->getEol());
            $csvElt->addChildCData('escape', $csvFormat->getEscape());
        }

        $variablesElt = $root->addChild('variables');
        foreach ($this->config->getVariableConfigs() as $variableConfig) {
            $variableElt = $variablesElt->addChild('variable');
            $variableElt->addAttribute('name', $variableConfig->getName());
            $variableElt->addChild('method', $variableConfig->getFakerMethod());
            $variableElt->addChildCData('argument1', $variableConfig->getFakerMethodArg1());
            $variableElt->addChildCData('argument2', $variableConfig->getFakerMethodArg2());
            $variableElt->addChildCData('argument3', $variableConfig->getFakerMethodArg3());
        }

        $columnsElt = $root->addChild('columns');
        foreach ($this->config->getColumnConfigs() as $columnConfig) {
            $columnElt = $columnsElt->addChild('column');
            $columnElt->addAttribute('name', $columnConfig->getName());
            $columnElt->addAttribute('unique', $columnConfig->getUnique());
            $columnElt->addChildCData('value', $columnConfig->getValue());
            $columnElt->addChild('convert', $columnConfig->getConvertMethod());
        }

        $file = $dir.DS.$name.'.xml';

        $rootDom = dom_import_simplexml($root);
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $rootDom = $dom->importNode($rootDom, true);
        $rootDom = $dom->appendChild($rootDom);

        $dom->save($file);

        return $file;
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

        $files = array();

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
