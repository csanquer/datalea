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
class InfoMethodsCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('fakery:info:methods')
            ->setDescription('list available Faker methods')
            ->setHelp(<<<EOF
The <info>%command.name%</info> list available Faker methods :

  <info>%command.full_name%</info>

EOF
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->getApplication()->getSilexApplication();
        
        $fakerConfig = $app['fakery.faker.config'];
        $methods = $fakerConfig->getMethods(null, null, true);
        
        $output->writeln('Available Faker Providers');
        $output->writeln('');
        
        $previousLocale = null;
        $previousProvider = null;
        
        foreach ($methods as $method) {
            if ($method['provider'] != $previousProvider) {
                $previousProvider = $method['provider'];
                $output->writeln('<comment>'.$method['provider'].'</comment> ');
            }
            
            if ($method['culture'] != $previousLocale) {
                $previousLocale = $method['culture'];
                $output->writeln('  <info>'.$method['culture'].'</info> ');
            }
            $output->write('    '.$method['name']);
            
            if (count($method['arguments'])) {
                $output->write('(');
                $first = true;
                foreach ($method['arguments'] as $key => $value) {
                    if ($first) {
                        $first = false;
                    } else {
                        $output->write(', ');
                    }
                    
                    $output->write('<info>'.$key.'</info> = '.($value === '' || $value === null ? 'null' : $value));
                } 
                $output->write(')');
            }
            
            if ($method['example'] != '' && $method['example'] != null) {
                $output->write(' <comment>// '.$method['example'].'</comment>');
            }
            
            $output->writeln('');
        }
    }
}
