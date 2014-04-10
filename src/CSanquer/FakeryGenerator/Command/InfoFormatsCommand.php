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
class InfoFormatsCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('fakery:info:formats')
            ->setDescription('list available output file formats')
            ->setHelp(<<<EOF
The <info>%command.name%</info> list available output file formats :

  <info>%command.full_name%</info>

EOF
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->getApplication()->getSilex();
        
        $output->writeln('Available output file formats');
        $output->writeln('');
        
        foreach (\CSanquer\FakeryGenerator\Dump\DumpManager::getAvailableFormats() as $format => $label) {
            $output->writeln('<info>'.$format.'</info>');
        }
    }
}
