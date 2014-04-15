<?php

namespace CSanquer\FakeryGenerator\Command;

use CSanquer\FakeryGenerator\Config\FakerConfig;
use CSanquer\FakeryGenerator\Dump\DumpManager;
use CSanquer\FakeryGenerator\Helper\Converter;
use Doctrine\Common\Inflector\Inflector;
use Silex\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 * @author Charles Sanquer <charles.sanquer@gmail.com>
 *
 */
class InfoCommand extends Command
{
    /**
     *
     * @var array
     */
    protected static $availableSections = [
        'locale',
        'format',
        'converter',
        'provider',
        'method'
    ];
    
    /**
     *
     * @var array
     */
    protected static $defaultSections = [
        'locale',
        'format',
        'converter',
        'method'
    ];
    
    protected function configure()
    {
        $this
            ->setName('fakery:info')
            ->setDescription('list available Fakery generator configuration sections')
            ->setHelp(<<<EOF
The <info>%command.name%</info> list all available Fakery generator configuration sections :

  <info>%command.full_name%</info>

The <info>%command.name%</info> list available Faker locales :

  <info>%command.full_name%</info> locale

To change language display translation set the <info>locale</info> option :

  <info>%command.full_name% --locale=fr_FR</info> locale
                    
The <info>%command.name%</info> list available output file formats :

  <info>%command.full_name%</info> format
                    
The <info>%command.name%</info> list available columns converters :

  <info>%command.full_name%</info> converter
                    
The <info>%command.name%</info> list available Faker providers :

  <info>%command.full_name%</info> provider
                    
The <info>%command.name%</info> list available Faker methods :

  <info>%command.full_name%</info> method
                    
EOF
            )
            ->addArgument('sections', InputArgument::IS_ARRAY, 'List of sections to display : '.implode(', ', static::$availableSections), static::$defaultSections)
            ->addOption('locale', 'l', InputOption::VALUE_REQUIRED, 'locale used to display language name', \Locale::getDefault())
            ->addOption('filter-provider', 'p', InputOption::VALUE_REQUIRED, 'filter methods on provider')
            ->addOption('filter-locale', 'o', InputOption::VALUE_REQUIRED, 'filter methods on locale')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $selectedSections = $input->getArgument('sections');
        $sections= [];
        foreach (static::$availableSections as $section) {
            if (in_array($section, $selectedSections) || in_array(Inflector::pluralize($section), $selectedSections)) {
                $sections[] = $section;
            }
        }

        if (empty($sections)) {
            $sections = static::$defaultSections;
        }
        
        foreach ($sections as $section) {
            switch ($section) {
                case 'locale':
                    $this->displayLocale($input, $output);
                    break;
                case 'format':
                    $this->displayFormat($input, $output);
                    break;
                case 'converter':
                    $this->displayConverter($input, $output);
                    break;
                case 'provider':
                    $this->displayProvider($input, $output);
                    break;
                case 'method':
                    $this->displayMethod($input, $output);
                    break;
            }
        }
    }
    
    /**
     * 
     * @return Application
     */
    protected function getSilex()
    {
        return $this->getApplication()->getSilex();
    }
    
    /**
     * 
     * @return FakerConfig
     */
    protected function getFakerConfig()
    {
        $app = $this->getSilex();
        
        return isset($app['fakery.faker.config']) ? $app['fakery.faker.config'] : null;
    }
    
    protected function displayLocale(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('locale')) {
            \Locale::setDefault($input->getOption('locale'));
        }
        
        $output->writeln('Available Faker locales');
        $output->writeln('');
        
        $locales = [];
        foreach ($this->getFakerConfig()->getLocales() as $locale) {
            $locales[] = [$locale, \Locale::getDisplayName($locale)];
        }
        
        $table = $this->getHelperSet()->get('table');
        $table->setCellRowFormat('<comment>%s</comment>');
        $table
            ->setHeaders(array('Locale', 'Language'))
            ->setRows($locales)
        ;
        $table->render($output);
        
        $output->writeln('');
    }
    
    protected function displayFormat(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Available output file formats');
        $output->writeln('');
        
        foreach (DumpManager::getAvailableFormats() as $format => $label) {
            $output->writeln('<info>'.$format.'</info>');
        }
        
        $output->writeln('');
    }
    
    protected function displayConverter(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Available columns converters');
        $output->writeln('');
        
        foreach (Converter::getAvailableConvertMethods() as $converter) {
            $output->writeln('<info>'.$converter.'</info>');
        }
        
        $output->writeln('');
    }
    
    protected function displayProvider(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Available Faker Providers');
        $output->writeln('');
        
        foreach ($this->getFakerConfig()->getProviders() as $provider) {
            $output->writeln('<info>'.$provider.'</info>');
        }
        
        $output->writeln('');
    }
    
    protected function displayMethod(InputInterface $input, OutputInterface $output)
    {
        $filterLocale = $input->getOption('filter-locale');
        $filterProvider = ucfirst(strtolower($input->getOption('filter-provider')));
        $methods = $this->getFakerConfig()->getMethods($filterLocale, $filterProvider, true);
        
        $output->writeln('Available Faker Providers');
        $output->writeln('');
        
        $previousLocale = null;
        $previousProvider = null;
        
        foreach ($methods as $method) {
            if ($method['provider'] != $previousProvider) {
                $previousProvider = $method['provider'];
                $output->writeln('<comment>'.$method['provider'].'</comment> ');
                $previousLocale = null;
            }
            
            if ($method['locale'] != $previousLocale) {
                $previousLocale = $method['locale'];
                $output->writeln('  <info>'.$method['locale'].'</info> ');
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
        $output->writeln('');
    }
}
