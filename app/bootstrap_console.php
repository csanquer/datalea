<?php

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

use CSanquer\FakeryGenerator\Command\ConfigExampleCommand;
use CSanquer\FakeryGenerator\Command\InfoConvertersCommand;
use CSanquer\FakeryGenerator\Command\InfoFormatsCommand;
use CSanquer\FakeryGenerator\Command\InfoLocalesCommand;
use CSanquer\FakeryGenerator\Command\InfoMethodsCommand;
use CSanquer\FakeryGenerator\Command\InfoProvidersCommand;
use CSanquer\FakeryGenerator\Command\GenerateCommand;
use CSanquer\Silex\Tools\Command\AsseticDumpCommand;
use CSanquer\Silex\Tools\Command\CacheClearCommand;
use CSanquer\Silex\Tools\Command\ServerRunCommand;
use CSanquer\Silex\Tools\ConsoleApplication;

$console = new ConsoleApplication($app, 'Fakery Generator Application', 'N/A');

// register commands to the application
//$console
//    ->register('my-command')
//    ->setDefinition(array(
//        // new InputOption('some-option', null, InputOption::VALUE_NONE, 'Some help'),
//    ))
//    ->setDescription('My command description')
//    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
//        // do something
//    })
//;

// or add your existing commands to the application
//$console->add(new MyCommand());
   
$console->add(new CacheClearCommand());
$console->add(new AsseticDumpCommand());
$console->add(new ServerRunCommand());
$console->add(new InfoLocalesCommand());
$console->add(new InfoProvidersCommand());
$console->add(new InfoMethodsCommand());
$console->add(new InfoConvertersCommand());
$console->add(new InfoFormatsCommand());
$console->add(new ConfigExampleCommand());
$console->add(new GenerateCommand());

return $console;
