<?php

namespace CSanquer\FakeryGenerator\Command;

use CSanquer\ColibriCsv\Dialect;
use CSanquer\FakeryGenerator\Dump\DumpManager;
use CSanquer\FakeryGenerator\Model\Column;
use CSanquer\FakeryGenerator\Model\Config;
use CSanquer\FakeryGenerator\Model\Variable;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for generating a fakery configuration file example
 *
 * @author Charles Sanquer <charles.sanquer@gmail.com>
 *
 */
class ConfigExampleCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('fakery:config:example')
            ->setDescription('dump a fakery generator configuration file example')
            ->setHelp(<<<EOF
The <info>%command.name%</info> dump an configuration example file :

  <info>%command.full_name%</info>

To dump the example file in another directory :

  <info>%command.full_name% my_directory</info>
                    
To dump the example file in <info>XML</info> format :

  <info>%command.full_name% --format=xml</info>

EOF
            )
            ->addArgument('output-dir', InputArgument::OPTIONAL, 'directory where to dump the configuration file', '.')
            ->addOption('format', 'f', InputOption::VALUE_REQUIRED, 'config file format (json or xml)', 'json')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->getApplication()->getSilex();
        
        $outputDir = realpath(is_dir($input->getArgument('output-dir')) ? $input->getArgument('output-dir') : '.');

        $format = $input->getOption('format');
        if (!in_array($format, array('json', 'xml'))) {
            throw new \InvalidArgumentException('The format '.$format.' is not allowed.');
        }
        
        $config = new Config();
        $config
            ->setClassName('Entity\\User')
            ->setFakeNumber(100)
            ->setFormats(array_keys(DumpManager::getAvailableFormats()))
            ->setLocale('en_US')
            ->setSeed(17846134)
            ->setCsvDialect(Dialect::createExcelDialect())
            ->setVariables([
                new Variable('firstname', 'firstName'),
                new Variable('lastname', 'lastName'),
                new Variable('emailDomain', 'freeEmailDomain'),
                new Variable('birthday', 'dateTimeBetween', ['Y-m-d', '1970-01-01', '2014-01-01']),
                new Variable('phonehome', 'phoneNumber'),
                new Variable('phonework', 'phoneNumber'),
                new Variable('phonemobile', 'phoneNumber'),
                new Variable('street1', 'streetAddress'),
                new Variable('city1', 'city'),
                new Variable('postalcode1', 'postcode'),
                new Variable('country1', 'country'),
                new Variable('street2', 'streetAddress'),
                new Variable('city2', 'city'),
                new Variable('postalcode2', 'postcode'),
                new Variable('country2', 'country'),
            ])
            ->setColumns([
                new Column('firstname', '%firstname%', 'capitalize'),
                new Column('lastname', '%lastname%', 'capitalize'),
                new Column('email', '%firstname%.%lastname%@%emailDomain%', 'lowercase'),
                new Column('birthday', '%birthday%'),
                new Column('address', null, null, [
                    new Column('home', null, null, [
                        new Column('street', '%street1%', 'capitalize'),
                        new Column('city', '%city1%', 'capitalize'),
                        new Column('postalcode', '%postalcode1%'),
                        new Column('country', '%country1%', 'capitalize'),
                    ]),
                    new Column('work', null, null, [
                        new Column('street', '%street2%', 'capitalize'),
                        new Column('city', '%city2%', 'capitalize'),
                        new Column('postalcode', '%postalcode2%'),
                        new Column('country', '%country2%', 'capitalize'),
                    ]),
                ]),
                new Column('phone', null, null, [
                    new Column('home', '%phonehome%'),
                    new Column('mobile', '%phonemobile%'),
                ]),
            ]);
        
        $serializer = $app['fakery.config_serializer'];
        
        $configFile = $serializer->dump($config, $outputDir, $format);
        $output->writeln('Example Config file dumped to <info>'.$configFile).'</info>';
    }
}
