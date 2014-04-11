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

    public function initialize(Config $config, $directory, $filenameWithDate = false)
    {
        $this->setFilename($config, $directory, $filenameWithDate);

        $this->xml = new \XMLWriter();
        $this->xml->openUri($this->filename);
        $this->xml->startDocument('1.0', 'utf-8');

        $this->xml->setIndent(true);
        $this->xml->setIndentString('    ');

        $this->xml->writeComment('list of '.$config->getClassName());
        $this->elementName = strtolower($config->getClassNameLastPart());
        $this->xml->startElement(strtolower($config->getClassNameLastPart(true)));
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
    }

    public function getExtension()
    {
        return 'xml';
    }
}
