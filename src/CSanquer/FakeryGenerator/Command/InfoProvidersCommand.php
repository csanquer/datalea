<?php

namespace CSanquer\FakeryGenerator\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 * @author Charles Sanquer <charles.sanquer@gmail.com>
 *
 */
class InfoProvidersCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('fakery:info:providers')
            ->setDescription('list available Faker providers')
            ->setHelp(<<<EOF
The <info>%command.name%</info> list available Faker providers :

  <info>%command.full_name%</info>

EOF
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->getApplication()->getSilexApplication();
        
        $fakerConfig = $app['fakery.faker.config'];
        
        $output->writeln('Available Faker Providers');
        $output->writeln('');
        
        foreach ($fakerConfig->getProviders() as $provider) {
            $output->writeln('<info>'.$provider.'</info>');
        }
    }
}
