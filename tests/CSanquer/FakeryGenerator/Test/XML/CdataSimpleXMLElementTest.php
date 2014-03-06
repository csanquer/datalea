<?php

namespace CSanquer\FakeryGenerator\Test\XML;

use CSanquer\FakeryGenerator\XML\CdataSimpleXMLElement;

class CdataSimpleXMLElementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CdataSimpleXMLElement
     */
    protected $elt;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $xmlstr = <<<XML
<?xml version='1.0' standalone='yes'?>
<config>
</config>
XML;
        
        $this->elt = new CdataSimpleXMLElement($xmlstr);
    }

    /**
     * @covers CSanquer\FakeryGenerator\XML\CdataSimpleXMLElement::addCData
     */
    public function testAddCData()
    {
        $this->elt->addCData('foobar');
        $this->assertXmlStringEqualsXmlString('<config><![CDATA[foobar]]></config>',$this->elt->asXML());
    }

    /**
     * @covers CSanquer\FakeryGenerator\XML\CdataSimpleXMLElement::addChildCData
     */
    public function testAddChildCData()
    {
        $this->elt->addChildCData('test','foobar');
        $this->assertXmlStringEqualsXmlString('<config><test><![CDATA[foobar]]></test></config>',$this->elt->asXML());
    }
}
