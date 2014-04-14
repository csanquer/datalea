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
     * @dataProvider providerDump
     */
    public function testDump($config, $zipped, $configFormat, $expectedOutput, $expectedFiles, $expectedFilesInZip)
    {
        $output = new BufferedOutput();
        $progress = new ProgressHelper();
        $outputDir = static::$cacheDir.'/dump';
        $stopwatch = new Stopwatch();
        
        $stopwatch->openSection();
        $files = $this->dumpManager->dump($config, $outputDir, $zipped, $configFormat, $stopwatch, $output, $progress);
        $stopwatch->stopSection('generate-test');
        
        $events = $stopwatch->getSectionEvents('generate-test');
        
        $keys = ['dumping_config', 'initializing_files', 'generating_rows', 'finalizing_files', 'compressing_files'];
        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $events);
        }
        
        $outputContent = $output->fetch();

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
        
        $this->assertEquals($expectedOutput, $outputContent);
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
                "Dumping Configuration as JSON ...
Dumping Configuration as XML ...
Initializing files ...
Formats : php, json, xml, yaml, csv, sql, excel, perl, ruby, python
Generating 10 rows ...
\r  1/10 [==>-------------------------]  10%\r  2/10 [=====>----------------------]  20%\r  3/10 [========>-------------------]  30%\r  4/10 [===========>----------------]  40%\r  5/10 [==============>-------------]  50%\r  6/10 [================>-----------]  60%\r  7/10 [===================>--------]  70%\r  8/10 [======================>-----]  80%\r  9/10 [=========================>--]  90%\r 10/10 [============================] 100%
Finalizing files ...
\r  1/10 [==>-------------------------]  10%\r  2/10 [=====>----------------------]  20%\r  3/10 [========>-------------------]  30%\r  4/10 [===========>----------------]  40%\r  5/10 [==============>-------------]  50%\r  6/10 [================>-----------]  60%\r  7/10 [===================>--------]  70%\r  8/10 [======================>-----]  80%\r  9/10 [=========================>--]  90%\r 10/10 [============================] 100%
Compressing files into zip ...
",
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

    protected function unzip($file)
    {
        $zippedFiles = [];

        $zip = new \ZipArchive();
        $zip->open($file);
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $f = $zip->statIndex($i);
            if (isset($f['name'])) {
                $zippedFiles[] = $f['name'];
            }
        }
        $zip->close();

        sort($zippedFiles);

        return $zippedFiles;
    }
}
