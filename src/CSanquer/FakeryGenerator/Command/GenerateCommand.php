<?php

namespace CSanquer\FakeryGenerator\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * GenerateCommand
 *
 * @author Charles Sanquer <charles.sanquer.gmail.com>
 */
class GenerateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('fakery:generate')
            ->setDescription('generate random data files from a fakery config file')
            ->setHelp(<<<EOF
The <info>%command.name%</info> generate random data files from a fakery config file :

  <info>%command.full_name% My_Entity_User_fakery_generator_config.json</info>

To generate files in another directory :

  <info>%command.full_name% My_Entity_User_fakery_generator_config.json my_directory</info>

To generate files and not compress them as a zip archive:

  <info>%command.full_name% --no-zip My_Entity_User_fakery_generator_config.json</info>
                    
To override config fake number :

  <info>%command.full_name% --number=2000 My_Entity_User_fakery_generator_config.json</info>

EOF
            )
            ->addArgument('config', InputArgument::REQUIRED, 'fakery generator configuration file')
            ->addArgument('output-dir', InputArgument::OPTIONAL, 'directory where to dump the configuration file', 'dump_'.uniqid().'_'.time())
            ->addOption('no-zip', 'o', InputOption::VALUE_NONE, 'do not zip generated files')
            ->addOption('number', 'u', InputOption::VALUE_REQUIRED, 'override fake number rows to generate')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->getApplication()->getSilex();
        
        $configFile = $input->getArgument('config');
        if (!file_exists($configFile)) {
            throw new \InvalidArgumentException('The config file '.$configFile.' does not exists.');
        }

        $output->writeln('Loading <comment>'.$configFile.'</comment> ...');
        
        $noZip = $input->getOption('no-zip');
        $fakeNumber = $input->getOption('number');
        
        $serializer = $app['fakery.config_serializer'];
        $config = $serializer->load($configFile);
        
        $outputDir = $input->getArgument('output-dir');
        if (file_exists($outputDir) && !is_dir($outputDir)) {
            $outputDir = 'dump_'.uniqid().'_'.time();
        }
        
        if (is_numeric($fakeNumber)) {
            $config->setFakeNumber($fakeNumber);
            
        }
        
        $output->writeln('Generating <comment>'.$config->getFakeNumber().'</comment> rows ...');
        
        $dumpManager = $app['fakery.dumper_manager'];
        $files = $dumpManager->dump($config, $outputDir, !$noZip, 'now');
        foreach ($files as $file) {
            $output->writeln('<info>'.$file.'</info> generated');
        }
        
    }
}
