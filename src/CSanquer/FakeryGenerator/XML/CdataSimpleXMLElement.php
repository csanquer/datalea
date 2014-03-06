<?php

namespace CSanquer\FakeryGenerator\XML;

/**
 * CdataSimpleXMLElement
 *
 * @author Charles Sanquer <charles.sanquer@gmail.com>
 */
class CdataSimpleXMLElement extends \SimpleXMLElement
{
    /**
     * Add CDATA text in a node
     * @param string $cdata_text The CDATA value  to add
     */
    public function addCData($cdataText)
    {
        $node = dom_import_simplexml($this);
        $no = $node->ownerDocument;
        $node->appendChild($no->createCDATASection($cdataText));
    }

    /**
     * Create a child with CDATA value
     * @param string $name       The name of the child element to add.
     * @param string $cdata_text The CDATA value of the child element.
     *
     * @return CdataSimpleXMLElement child element
     */
    public function addChildCData($name, $cdataText)
    {
        $child = $this->addChild($name);
        $child->addCData($cdataText);

        return $child;
    }
}
