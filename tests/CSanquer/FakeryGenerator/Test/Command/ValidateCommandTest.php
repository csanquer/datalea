<?php

namespace CSanquer\FakeryGenerator\Test\Command;

use CSanquer\FakeryGenerator\Command\ValidateCommand;
use CSanquer\Silex\Tools\ConsoleApplication;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * ValidateCommandTest
 * 
 * @author Charles Sanquer <charles.sanquer@gmail.com>
 */
class ValidateCommandTest extends AbstractCommandTestCase
{
    /**
     * @dataProvider providerExecute
     */
    public function testExecute($configFile, $expectedOutput, $expectedStatusCode, $exception = null, $exceptionMessage = null)
    {
        if ($exception) {
            $this->setExpectedException($exception, $exceptionMessage);
        }
        
        $application = new ConsoleApplication($this->silex, 'Fakery Generator Test Application', 'N/A');
        $application->add(new ValidateCommand());
        
        $command = $application->find('fakery:validate');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'command' => $command->getName(), 
                'config' => static::$fixtures.$configFile,
            ]
        );
        
        $this->assertEquals($expectedStatusCode, $commandTester->getStatusCode());
        $this->assertRegExp('/'.$expectedOutput.'/', $commandTester->getDisplay());
    }
    
    public function providerExecute()
    {
        return [
            // data set #0
            [
                'Entity_User_fakery_generator_config_2014-04-14_10-57-17.json',
                "The config file is valid",
                0
            ],
            // data set #1
            [
                'not_valid_config.json',
                "locale : This locale 'to_TO' is not available in Faker.
variables\[firstname\].method : This method 'foobar' is not available in Faker.
The config file is not valid",
                1
            ],
            // data set #3
            [
                'config.yml',
                "foobar",
                1,
                '\\InvalidArgumentException',
                'must be a JSON or XML file.',
            ],
            // data set #4
            [
                'foobar.json',
                "foobar",
                1,
                '\\InvalidArgumentException',
                'does not exists.',
            ],
        ];
    }
    
}
