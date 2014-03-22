<?php

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

use CSanquer\Silex\Tools\ConsoleApplication;

$console = new ConsoleApplication($app, __DIR__.'/..', 'Twig Front Dev Application', 'n/a', 'app');

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
   
$console->add(new CSanquer\Silex\Tools\Command\CacheClearCommand());
$console->add(new CSanquer\Silex\Tools\Command\AsseticDumpCommand());
$console->add(new CSanquer\Silex\Tools\Command\ServerRunCommand());

return $console;
