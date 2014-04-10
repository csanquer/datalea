<?php

namespace CSanquer\FakeryGenerator\Command;

use \CSanquer\ColibriCsv\Dialect;
use \CSanquer\FakeryGenerator\Config\ConfigSerializer;
use \CSanquer\FakeryGenerator\Dump\DumpManager;
use \CSanquer\FakeryGenerator\Model\Column;
use \CSanquer\FakeryGenerator\Model\Config;
use \CSanquer\FakeryGenerator\Model\Variable;
use \Symfony\Component\Console\Command\Command;
use \Symfony\Component\Console\Input\InputArgument;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Output\OutputInterface;

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
            ->addArgument($name, InputArgument::OPTIONAL, $description, $default)
            ->addOption($name, $shortcut, InputOption::VALUE_OPTIONAL, $description, $default)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->getApplication()->getSilexApplication();
        
//        $fakerConfig = $app['fakery.faker.config'];
//        $fakerConfig = new \CSanquer\FakeryGenerator\Config\FakerConfig;
//        print_r($fakerConfig->getConfig());
//        print_r($fakerConfig->getCultures());
//        print_r($fakerConfig->getProviders());
//        print_r($fakerConfig->getMethods());
        
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
            ])
            ->setColumns([
                new Column('firstname', '%firstname%', 'capitalize'),
                new Column('lastname', '%lastname%', 'capitalize'),
                new Column('email', '%firstname%.%lastname%@%emailDomain%', 'lowercase'),
                new Column('birthday', '%birthday%'),
            ]);
        
        $serializer = $app['fakery.config_serializer'];
//        $serializer = new ConfigSerializer();
        
        $serializer->dump($config, $dir, $format);
    }
}
