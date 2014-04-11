<?php

namespace CSanquer\FakeryGenerator\Command;

use \Symfony\Component\Console\Command\Command;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Output\OutputInterface;

/**
 *
 * @author Charles Sanquer <charles.sanquer@gmail.com>
 *
 */
class InfoLocalesCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('fakery:info:locales')
            ->setDescription('list available Faker locales (locale code and language name with country)')
            ->setHelp(<<<EOF
The <info>%command.name%</info> list available Faker locales :

  <info>%command.full_name%</info>

To change language display translation set the <info>locale</info> option :

  <info>%command.full_name% --locale=fr_FR</info>

EOF
            )
            ->addOption('locale', 'l', InputOption::VALUE_REQUIRED, 'locale used to display language name', \Locale::getDefault())
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->getApplication()->getSilex();
        
        $fakerConfig = $app['fakery.faker.config'];
        
        if ($input->getOption('locale')) {
            \Locale::setDefault($input->getOption('locale'));
        }
        
        $output->writeln('Available Faker locales');
        $output->writeln('');
        
        foreach ($fakerConfig->getLocales() as $locale) {
            $output->writeln('<info>'.$locale.'</info> : '.\Locale::getDisplayName($locale));
        }
    }
}
