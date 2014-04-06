<?php

namespace CSanquer\FakeryGenerator\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
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
        
        $config = new \CSanquer\FakeryGenerator\Model\Config();
        
    }
}
