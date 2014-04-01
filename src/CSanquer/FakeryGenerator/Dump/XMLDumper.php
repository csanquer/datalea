<?php

namespace CSanquer\FakeryGenerator\Dump;

use CSanquer\FakeryGenerator\Model\Config;

/**
 * XMLDumper
 *
 * @author Charles Sanquer <charles.sanquer.gmail.com>
 */
class XMLDumper extends AbstractDumper
{
    /**
     *
     * @var \XMLWriter
     */
    protected $xml;

    /**
     *
     * @var string
     */
    protected $elementName;

    public function initialize(Config $config, $directory)
    {
        $this->setFilename($config, $directory);

        $this->xml = new \XMLWriter();
        $this->xml->openUri($this->filename);
        $this->xml->startDocument('1.0', 'utf-8');

        $this->xml->setIndent(true);
        $this->xml->setIndentString('    ');

        $rootName = strtolower(str_ireplace('_', '', $config->getClassName(true)));
        $this->xml->startElement($rootName);

        $this->elementName = strtolower($config->getClassNameLastPart());
    }

    public function dumpRow(array $row = array())
    {
        $this->dumpElement($this->elementName, $row);
    }

    protected function dumpElement($key, $value)
    {
        $this->xml->startElement(strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $key)));

        if (is_array($value)) {
            foreach ($value as $subKey => $subValue) {
                $this->dumpElement($subKey, $subValue);
            }
        } else {
            //need CDATA
            if (preg_match('/[<>&]/', $value)) {
                $this->xml->writeCdata($value);
            } else {
                $this->xml->text($value);
            }
        }

        $this->xml->endElement();
    }

    public function finalize()
    {
        $this->xml->endElement(); //root element
        $this->xml->endDocument();
        $this->xml->flush();

        return $this->filename;

        /**
        $fakeData = $this->getFakeData();

        $name = $this->config->getClassName(true);

        $elementRootName = strtolower(str_ireplace('_', '', $name));
        $elementName = strtolower($this->config->getClassNameLastPart());

        $root = new \CSanquer\FakeryGenerator\XML\CdataSimpleXMLElement('<?xml version=\'1.0\' encoding=\'utf-8\'?><'.$elementRootName.'s/>');

        foreach ($fakeData as $items) {
            $element = $root->addChild($elementName);
            foreach ($items as $column => $value) {
                $element->addChild(strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $column)), $value);
            }
        }

        $file = $dir.DS.$name.'.xml';

        $rootDom = dom_import_simplexml($root);
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $rootDom = $dom->importNode($rootDom, true);
        $rootDom = $dom->appendChild($rootDom);

        $dom->save($file);

        return $file;
        /**/

    }

    public function getExtension()
    {
        return 'xml';
    }
}
