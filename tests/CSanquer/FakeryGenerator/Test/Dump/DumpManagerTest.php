<?php

namespace CSanquer\FakeryGenerator\Test\Dump;

use CSanquer\FakeryGenerator\Config\ConfigSerializer;
use CSanquer\FakeryGenerator\Dump\DumpManager;

class DumpManagerTest extends DumperTestCase
{
    /**
     * @var DumpManager
     */
    protected $dumpManager;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        
        $this->dumpManager = new DumpManager(new ConfigSerializer(
            self::$cacheDir.'/serializer', 
            __DIR__.'/../../../../../src/CSanquer/FakeryGenerator/Resources/Config', 
            true
        ));
    }

    /**
     * @covers CSanquer\FakeryGenerator\Dump\DumpManager::getAvailableFormats
     */
    public function testGetAvailableFormats()
    {
        $this->assertEquals([
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
        ], DumpManager::getAvailableFormats());
    }

    /**
     * @covers CSanquer\FakeryGenerator\Dump\DumpManager::dump
     * @todo   Implement testDump().
     */
    public function testDump()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

}
