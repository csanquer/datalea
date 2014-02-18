<?php

namespace CSanquer\FakeryGenerator\Test\Model;

use CSanquer\FakeryGenerator\Model\VariableConfig;

/**
 * VariableConfigTest
 *
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-02-13 at 08:23:50.
 * 
 * @author Charles Sanquer <charles.sanquer@gmail.com>
 */
class VariableConfigTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var VariableConfig
     */
    protected $variableConfig;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->variableConfig = new VariableConfig();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

    /**
     * @covers CSanquer\FakeryGenerator\Model\VariableConfig::getName
     * @covers CSanquer\FakeryGenerator\Model\VariableConfig::getVarName
     * @covers CSanquer\FakeryGenerator\Model\VariableConfig::setName
     * 
     * @dataProvider providerGetSetName
     */
    public function testGetSetName($name, $expected)
    {
        $this->assertInstanceOf('\\CSanquer\\FakeryGenerator\\Model\\VariableConfig', $this->variableConfig->setName($name));
        $this->assertEquals($expected, $this->variableConfig->getName());
        $this->assertEquals('%'.$expected.'%', $this->variableConfig->getVarName());
    }

    public function providerGetSetName()
    {
        return array(
            array('firstname', 'firstname'),
            array('^$fi;:rstname%;', 'firstname'),
        );
    }
    
    /**
     * @covers CSanquer\FakeryGenerator\Model\VariableConfig::getMethod
     * @covers CSanquer\FakeryGenerator\Model\VariableConfig::setMethod
     * @todo   Implement testGetMethod().
     */
    public function testGetSetMethod()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers CSanquer\FakeryGenerator\Model\VariableConfig::getMethodArguments
     * @covers CSanquer\FakeryGenerator\Model\VariableConfig::setMethodArguments
     * @covers CSanquer\FakeryGenerator\Model\VariableConfig::hasMethodArguments
     * @todo   Implement testGetMethodArguments().
     */
    public function testGetMethodArguments()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers CSanquer\FakeryGenerator\Model\VariableConfig::getMethodArgument
     * @covers CSanquer\FakeryGenerator\Model\VariableConfig::hasMethodArgument
     * @covers CSanquer\FakeryGenerator\Model\VariableConfig::addMethodArgument
     * @covers CSanquer\FakeryGenerator\Model\VariableConfig::setMethodArgument
     * 
     * @todo   Implement testGetMethodArgument().
     */
    public function testGetMethodArgument()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers CSanquer\FakeryGenerator\Model\VariableConfig::isUnique
     * @covers CSanquer\FakeryGenerator\Model\VariableConfig::setUnique
     * @todo   Implement testIsUnique().
     */
    public function testIsSetUnique()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

}
