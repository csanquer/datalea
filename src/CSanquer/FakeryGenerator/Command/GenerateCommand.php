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
The <info>%command.name%</info> generate random data files from a fakery config file + an updated copy of the configuration file :

  <info>%command.full_name% My_Entity_User_fakery_generator_config.json</info>

To generate files in another directory :

  <info>%command.full_name% My_Entity_User_fakery_generator_config.json my_directory</info>

To generate files and not compress them as a zip archive:

  <info>%command.full_name% --no-zip My_Entity_User_fakery_generator_config.json</info>
                    
To override config fake number :

  <info>%command.full_name% --number=2000 My_Entity_User_fakery_generator_config.json</info>
                    
To change copyied configuration file output format :
                    
  - auto : same format than input config file
  - all : all formats
  - json
  - xml

  <info>%command.full_name% --config-format=auto My_Entity_User_fakery_generator_config.json</info>    
  
EOF
            )
            ->addArgument('config', InputArgument::REQUIRED, 'fakery generator configuration file')
            ->addArgument('output-dir', InputArgument::OPTIONAL, 'directory where to dump the configuration file', 'dump_'.uniqid().'_'.time())
            ->addOption('no-zip', 'o', InputOption::VALUE_NONE, 'do not zip generated files')
            ->addOption('number', 'u', InputOption::VALUE_REQUIRED, 'override fake number rows to generate')
            ->addOption('config-format', 'f', InputOption::VALUE_REQUIRED, 'dump a new configuration file in the following format: json, xml, all, auto', 'auto')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $stopwatch = new \Symfony\Component\Stopwatch\Stopwatch();
        
        $stopwatch->start('generate');
        
        $app = $this->getApplication()->getSilex();
        
        $configFile = $input->getArgument('config');
        if (!file_exists($configFile)) {
            throw new \InvalidArgumentException('The config file '.$configFile.' does not exists.');
        }

        $configFileExtension = pathinfo($configFile, PATHINFO_EXTENSION);
        if (!in_array($configFileExtension, ['json', 'xml'])) {
            throw new \InvalidArgumentException('The config file '.$configFile.' must be a JSON or XML file.');
        }
        
        $outputConfigFormat = $input->getOption('config-format');
        if (!in_array($outputConfigFormat, ['json', 'xml', 'all', 'auto'])) {
            throw new \InvalidArgumentException('The output config file format '.$outputConfigFormat.' is not allowed.');
        }
        
        if ($outputConfigFormat == 'auto') {
            $outputConfigFormat = $configFileExtension;
        }
        
        $output->writeln('Loading <info>'.$configFile.'</info> ...');
        
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
        
        $dumpManager = $app['fakery.dumper_manager'];
        $files = $dumpManager->dump($config, $outputDir, !$noZip, $outputConfigFormat, $output, $this->getHelperSet());
        $event = $stopwatch->stop('generate');
        
        foreach ($files as $file) {
            $output->writeln('<info>'.$file.'</info> generated');
        }
        
        $this->formatStopwatchEvent($event, $output);
    }
    
    protected function formatStopwatchEvent(\Symfony\Component\Stopwatch\StopwatchEvent $event, OutputInterface $output) 
    {
        $output->writeln('');
        $output->writeln('Duration : <comment>'.($event->getDuration()/1000).'</comment> s');
        $output->writeln('Memory usage : <comment>'.round($event->getMemory()/(1024*1024), 5).'</comment> Mb');
    }
}
