<?php

namespace CSanquer\FakeryGenerator\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for getting Faker available configuration 
 *
 * @author Charles Sanquer <charles.sanquer@gmail.com>
 *
 */
class FakerInfoCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('faker:info')
            ->setDescription('list available Faker configuration')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->getApplication()->getSilexApplication();
        
        $fakerConfig = $app['fakery.faker.config'];
//        $fakerConfig = new \CSanquer\FakeryGenerator\Config\FakerConfig;
        print_r($fakerConfig->getConfig());
        print_r($fakerConfig->getCultures());
        print_r($fakerConfig->getProviders());
        print_r($fakerConfig->getMethods());
        
    }
}
