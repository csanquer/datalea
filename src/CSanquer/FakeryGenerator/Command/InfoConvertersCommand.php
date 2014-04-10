<?php

namespace CSanquer\FakeryGenerator\Command;

use CSanquer\FakeryGenerator\Helper\Converter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 * @author Charles Sanquer <charles.sanquer@gmail.com>
 *
 */
class InfoConvertersCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('fakery:info:converters')
            ->setDescription('list available columns converters')
            ->setHelp(<<<EOF
The <info>%command.name%</info> list available columns converters :

  <info>%command.full_name%</info>

EOF
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->getApplication()->getSilex();
        
        $output->writeln('Available columns converters');
        $output->writeln('');
        
        foreach (Converter::getAvailableConvertMethods() as $converter) {
            $output->writeln('<info>'.$converter.'</info>');
        }
    }
}
