<?php

namespace CSanquer\FakeryGenerator\Test\Command;

use CSanquer\FakeryGenerator\Command\ConfigExampleCommand;
use CSanquer\Silex\Tools\ConsoleApplication;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * ConfigExampleCommandTest
 * 
 * @author Charles Sanquer <charles.sanquer@gmail.com>
 */
class ConfigExampleCommandTest extends AbstractCommandTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $fs = new Filesystem();
        $fs->mkdir(static::$dumpDir);
    }
    
    /**
     * @dataProvider providerExecute
     */
    public function testExecute($format, $expectedException = null, $expectedExceptionMessage = null)
    {
        if (!empty($expectedException)) {
            $this->setExpectedException($expectedException, $expectedExceptionMessage);
        }
        
        $application = new ConsoleApplication($this->silex, 'Fakery Generator Test Application', 'N/A');
        $application->add(new ConfigExampleCommand());
        
        $command = $application->find('fakery:config:example');
        $commandTester = new CommandTester($command);
        
        $commandTester->execute(
            [
                'command' => $command->getName(), 
                'output-dir' => static::$dumpDir,
                '--format' => $format,
            ]
        );

        $filePattern = 'Entity_User_fakery_generator_config_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}\.'.$format;
        $outputPattern = '#Example Config file dumped to '.preg_quote(static::$dumpDir, '#').'/'.$filePattern.'#';
        $this->assertRegExp($outputPattern, $commandTester->getDisplay());
        
        $finder = new Finder();
        
        $finder
            ->name('/'.$filePattern.'/')
            ->files()
            ->in(static::$dumpDir);
        
        $this->assertCount(1, $finder);
    }
    
    public function providerExecute()
    {
        return [
            // data set #0
            [
                'json',
            ],
            // data set #1
            [
                'xml',
            ],
            // data set #2
            [
                'foo',
                '\\InvalidArgumentException',
                'The format foo is not allowed.',
            ],
        ];
    }
    
}
