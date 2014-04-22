<?php

namespace CSanquer\FakeryGenerator\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ValidateCommand
 *
 * @author Charles Sanquer <charles.sanquer.gmail.com>
 */
class ValidateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('fakery:validate')
            ->setDescription('validate a fakery config file')
            ->setHelp(<<<EOF
The <info>%command.name%</info> validate a fakery config file :

  <info>%command.full_name% My_Entity_User_fakery_generator_config.json</info>

EOF
            )
            ->addArgument('config', InputArgument::REQUIRED, 'fakery generator configuration file')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->getApplication()->getSilex();
        
        $configFile = $input->getArgument('config');
        if (!file_exists($configFile)) {
            throw new \InvalidArgumentException('The config file '.$configFile.' does not exists.');
        }

        $configFileExtension = pathinfo($configFile, PATHINFO_EXTENSION);
        if (!in_array($configFileExtension, ['json', 'xml'])) {
            throw new \InvalidArgumentException('The config file '.$configFile.' must be a JSON or XML file.');
        }
        
        $serializer = $app['fakery.config_serializer'];
        $config = $serializer->load($configFile);
        $config->setFakerConfig($app['fakery.faker.config']);
        
        $errors = $app['validator']->validate($config);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $output->writeln($error->getPropertyPath().' : <error>'.$error->getMessage().'</error>');
            }
            $output->writeln('The config file is <error>not valid</error>');
            
            return 1;
        }
        
        $output->writeln('The config file is <info>valid</info>');
    }
}
