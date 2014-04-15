<?php

namespace CSanquer\FakeryGenerator\Test\Command;

use CSanquer\FakeryGenerator\Command\InfoCommand;
use CSanquer\Silex\Tools\ConsoleApplication;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * InfoCommandTest
 * 
 * @author Charles Sanquer <charles.sanquer@gmail.com>
 */
class InfoCommandTest extends AbstractCommandTestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        static::$configDir = __DIR__.'/fixtures/config';
    }
    
    /**
     * @dataProvider providerExecute
     */
    public function testExecute($args, $expectedOutput)
    {
        $application = new ConsoleApplication($this->silex, 'Fakery Generator Test Application', 'N/A');
        $application->add(new InfoCommand());
        
        $command = $application->find('fakery:info');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array_merge(
            [
                'command' => $command->getName(), 
            ],
            $args
        ));

        $this->assertRegExp('/'.$expectedOutput.'/', $commandTester->getDisplay());
        
//        $this->assertEquals($expectedOutput, $commandTester->getDisplay());
    }
    
    public function providerExecute()
    {
        return [
            // data set #0
            [
                [
                    'sections' => [],
                    '--locale' => 'fr_FR',
//                    '--filter-provider' => null,
//                    '--filter-locale' => null, 
                ],
                "Available Faker locales

+--------+----------------------+
| Locale | Language             |
+--------+----------------------+
| en_US  | anglais (États-Unis) |
| es_ES  | espagnol (Espagne)   |
| fr_FR  | français (France)    |
+--------+----------------------+

Available output file formats

csv
excel
yaml
xml
json
sql
php
perl
ruby
python

Available columns converters

lowercase
uppercase
capitalize
capitalize_words
absolute
as_bool
as_int
as_float
as_string
remove_accents
remove_accents_lowercase
remove_accents_uppercase
remove_accents_capitalize
remove_accents_capitalize_words

Available Faker Providers

Address 
  en_US 
    buildingNumber \/\/ '484'
    city \/\/ 'West Judge'
    country \/\/ 'Falkland Islands \(Malvinas\)'
    postcode \/\/ '17916'
    streetName \/\/ 'Keegan Trail'
  fr_FR 
    departmentName \/\/ 'Haut-Rhin'
    departmentNumber \/\/ '2B'
Company 
  fr_FR 
    siret\(sequential_digits = 2\) \/\/ '347 355 708 00224'
DateTime 
  en_US 
    dateTimeAD\(format = 'Y-m-d H:i:s', max = 'now'\) \/\/ '1800-04-29 20:38:49'
Person 
  en_US 
    firstName \/\/ 'Maynard'
    lastName \/\/ 'Zulauf'

",
            ],
            // data set #1
            [
                [
                    'sections' => ['locale'],
                    '--locale' => 'fr_FR',
                ],
                "Available Faker locales

+--------+----------------------+
| Locale | Language             |
+--------+----------------------+
| en_US  | anglais (États-Unis) |
| es_ES  | espagnol (Espagne)   |
| fr_FR  | français (France)    |
+--------+----------------------+

",
            ],
            // data set #2
            [
                [
                    'sections' => ['format'],
                ],
                "Available output file formats

csv
excel
yaml
xml
json
sql
php
perl
ruby
python

",
            ],
            // data set #3
            [
                [
                    'sections' => ['converter'],
                ],
                "Available columns converters

lowercase
uppercase
capitalize
capitalize_words
absolute
as_bool
as_int
as_float
as_string
remove_accents
remove_accents_lowercase
remove_accents_uppercase
remove_accents_capitalize
remove_accents_capitalize_words

",
            ],
            // data set #4
            [
                [
                    'sections' => ['method'],
//                    '--filter-provider' => null,
//                    '--filter-locale' => null, 
                ],
                "Available Faker Providers

Address 
  en_US 
    buildingNumber \/\/ '484'
    city \/\/ 'West Judge'
    country \/\/ 'Falkland Islands \(Malvinas\)'
    postcode \/\/ '17916'
    streetName \/\/ 'Keegan Trail'
  fr_FR 
    departmentName \/\/ 'Haut-Rhin'
    departmentNumber \/\/ '2B'
Company 
  fr_FR 
    siret\(sequential_digits = 2\) \/\/ '347 355 708 00224'
DateTime 
  en_US 
    dateTimeAD\(format = 'Y-m-d H:i:s', max = 'now'\) \/\/ '1800-04-29 20:38:49'
Person 
  en_US 
    firstName \/\/ 'Maynard'
    lastName \/\/ 'Zulauf'
",
            ],
            // data set #5
            [
                [
                    'sections' => ['method'],
                    '--filter-provider' => 'Address',
                    '--filter-locale' => 'fr_FR', 
                ],
                "Available Faker Providers

Address 
  en_US 
    buildingNumber \/\/ '484'
    city \/\/ 'West Judge'
    country \/\/ 'Falkland Islands \(Malvinas\)'
    postcode \/\/ '17916'
    streetName \/\/ 'Keegan Trail'
  fr_FR 
    departmentName \/\/ 'Haut-Rhin'
    departmentNumber \/\/ '2B'
",
            ],
            // data set #6
            [
                [
                    'sections' => ['provider'],
                ],
                "Available Faker Providers

Address
Company
DateTime
Person
",
            ],
        ];
    }
    
}
