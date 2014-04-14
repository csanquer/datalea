<?php

namespace CSanquer\FakeryGenerator\Test\Command;

use CSanquer\FakeryGenerator\Command\GenerateCommand;
use CSanquer\Silex\Tools\ConsoleApplication;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * GenerateCommandTest
 * 
 * @author Charles Sanquer <charles.sanquer@gmail.com>
 */
class GenerateCommandTest extends AbstractCommandTestCase
{
    public function testExecute()
    {
        $application = new ConsoleApplication($this->silex, 'Fakery Generator Test Application', 'N/A');
        $application->add(new GenerateCommand());

        $rows = 2;
        $nozip = false;
        $configFormat = 'auto';
        
        $command = $application->find('fakery:generate');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(), 
            'config' => self::$fixtures.'Entity_User_fakery_generator_config_2014-04-14_10-57-17.json',
//            'output-dir' => self::$dumpDir,
            '--no-zip' => $nozip,
            '--number' => $rows,
            '--config-format' => $configFormat,
        ));

        $this->assertRegExp('/Generating '.$rows.' rows/', $commandTester->getDisplay());
        
        $outputPattern = self::$dumpDir.'/.*fakery_User_.+\.zip generated';
        $this->assertRegExp('#'.$outputPattern.'#', $commandTester->getDisplay());
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage must be a JSON or XML file.
     */
    public function testExecuteFailedYmlConfig()
    {
        $application = new ConsoleApplication($this->silex, 'Fakery Generator Test Application', 'N/A');
        $application->add(new GenerateCommand());

        $command = $application->find('fakery:generate');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(), 
            'config' => self::$fixtures.'config.yml',
        ));
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage does not exists.
     */
    public function testExecuteFailedConfigNotExists()
    {
        $application = new ConsoleApplication($this->silex, 'Fakery Generator Test Application', 'N/A');
        $application->add(new GenerateCommand());

        $command = $application->find('fakery:generate');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(), 
            'config' => self::$fixtures.'foobar.json',
        ));
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage is not a valid Fakery generator config file.
     */
    public function testExecuteFailedConfigNotValid()
    {
        $application = new ConsoleApplication($this->silex, 'Fakery Generator Test Application', 'N/A');
        $application->add(new GenerateCommand());

        $command = $application->find('fakery:generate');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(), 
            'config' => self::$fixtures.'noconfig.json',
        ));
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The output config file format foobar is not allowed.
     */
    public function testExecuteFailedConfigFormatNotAllowed()
    {
        $application = new ConsoleApplication($this->silex, 'Fakery Generator Test Application', 'N/A');
        $application->add(new GenerateCommand());

        $command = $application->find('fakery:generate');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(), 
            'config' => self::$fixtures.'Entity_User_fakery_generator_config_2014-04-14_10-57-17.json',
            '--config-format' => 'foobar',
        ));
    }
}
