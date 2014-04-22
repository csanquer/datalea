<?php

namespace CSanquer\FakeryGenerator\Test\Dump;

use CSanquer\FakeryGenerator\Config\ConfigSerializer;
use CSanquer\FakeryGenerator\Dump\DumpManager;
use CSanquer\FakeryGenerator\Model\Column;
use CSanquer\FakeryGenerator\Model\Config;
use CSanquer\FakeryGenerator\Model\Variable;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Stopwatch\Stopwatch;

class DumpManagerTest extends AbstractDumpManagerTestCase
{
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->dumpManager = new DumpManager(new ConfigSerializer(
            self::$cacheDir.'/serializer', __DIR__.'/../../../../../src/CSanquer/FakeryGenerator/Resources/Config', true
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
     * @covers CSanquer\FakeryGenerator\Dump\DumpManager::getAvailableFormatsForValidation
     */
    public function testGetAvailableFormatsForValidation()
    {
        $this->assertEquals([
            'csv',
            'excel',
            'yaml',
            'xml',
            'json',
            'sql',
            'php',
            'perl',
            'ruby',
            'python',
            ], DumpManager::getAvailableFormatsForValidation());
    }

    /**
     * @dataProvider providerDump
     */
    public function testDump($config, $zipped, $configFormat, $expectedFiles, $expectedFilesInZip)
    {
        $outputDir = static::$cacheDir.'/dump';
        
        $files = $this->dumpManager->dump($config, $outputDir, $zipped, $configFormat);

        $this->assertCount(count($expectedFiles), $files);
        foreach ($expectedFiles as $format => $pattern) {
            $this->assertRegExp('#'.$outputDir.'/'.$pattern.'#', $files[$format]);
        }

        if ($zipped && count($expectedFilesInZip)) {
            $zippedFiles = $this->unzip($files['zip']);
            sort($expectedFilesInZip);
            foreach ($expectedFilesInZip as $format => $pattern) {
                $this->assertRegExp('#'.$pattern.'#', $zippedFiles[$format]);
            }
        }
    }

    public function providerDump()
    {
        $config1 = new Config();
        $config1->setMaxTimestamp('2014-01-01T12:30:45+0100');
        $config1->setClassName('Entity\\User');
        $config1->setFakeNumber(10);
        $config1->setFormats(['php', 'json', 'xml', 'yaml', 'csv', 'sql', 'excel', 'perl', 'ruby', 'python']);
        $config1->setSeed(51);
        $config1->setLocale('fr_FR');
        $config1->setVariables([
            new Variable('firstname', 'firstName', [], false, false),
            new Variable('lastname', 'lastName', [], false, false),
            new Variable('birthday', 'dateTimeThisCentury', ['d/m/Y'], false, 0.5),
            new Variable('email', 'safeEmail', [], true, false),
        ]);

        $config1->setColumns([
            new Column('person', null, null, [
                new Column('name', null, null, [
                    new Column('firstname', '%firstname%', 'capitalize'),
                    new Column('lastname', '%lastname%', 'capitalize'),
                    ]),
                new Column('birthday', '%birthday%'),
                ]),
            new Column('email', '%email%'),
        ]);

        return [
            // data set #0
            [
                $config1,
                true,
                'all',
                [
                    'zip' => 'fakery_User_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}\.zip',
                ],
                [
                    'fakery_User_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}/Entity_User.csv',
                    'fakery_User_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}/Entity_User.json',
                    'fakery_User_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}/Entity_User.php',
                    'fakery_User_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}/Entity_User.pl',
                    'fakery_User_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}/Entity_User.py',
                    'fakery_User_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}/Entity_User.rb',
                    'fakery_User_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}/Entity_User.sql',
                    'fakery_User_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}/Entity_User.xlsx',
                    'fakery_User_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}/Entity_User.xml',
                    'fakery_User_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}/Entity_User.yml',
                    'fakery_User_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}/Entity_User_fakery_generator_config_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}.json',
                    'fakery_User_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}/Entity_User_fakery_generator_config_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}.xml',
                ],
            ]
        ];
    }
}
