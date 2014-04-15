<?php

namespace CSanquer\FakeryGenerator\Test\Command;

use CSanquer\FakeryGenerator\Command\GenerateCommand;
use CSanquer\FakeryGenerator\Config\ConfigSerializer;
use CSanquer\FakeryGenerator\Config\FakerConfig;
use CSanquer\FakeryGenerator\Dump\DumpManager;
use CSanquer\Silex\Tools\ConsoleApplication;
use Silex\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

/**
 * AbstractCommandTestCase
 * 
 * @author Charles Sanquer <charles.sanquer@gmail.com>
 */
class AbstractCommandTestCase extends \PHPUnit_Framework_TestCase
{
    protected static $configDir;
    protected static $fixtures;
    protected static $cacheDir;
    protected static $dumpDir;
    protected $previousCurrentDir;

    /**
     *
     * @var Application 
     */
    protected $silex;
    
    public static function setUpBeforeClass()
    {
        static::$fixtures = __DIR__.'/fixtures/';
        static::$cacheDir = __DIR__.'/tmp';
        static::$dumpDir = __DIR__.'/dump';
        static::$configDir = __DIR__.'/../../../../../src/CSanquer/FakeryGenerator/Resources/Config';
    }

    protected function setUp()
    {
        $this->previousCurrentDir = getcwd();
        chdir(__DIR__);
        
        $fs = new Filesystem();
        if ($fs->exists(static::$cacheDir)) {
            $fs->remove(static::$cacheDir);
        }
        
        if ($fs->exists(static::$dumpDir)) {
            $fs->remove(static::$dumpDir);
        }
        
        $app =  new Application();
        //custom providers and services
        $app['fakery.faker.config'] = $app->share(function ($app) {
            return new FakerConfig(
                static::$configDir, 
                'faker.yml',
                static::$cacheDir.'/cache/', 
                true
            );
        });

        $app['fakery.config_serializer'] = $app->share(function ($app) {
            return new ConfigSerializer(
                static::$cacheDir.'/cache/', 
                static::$configDir, 
                true
            );
        });

        $app['fakery.dumper_manager'] = $app->share(function ($app) {
            return new DumpManager($app['fakery.config_serializer']);
        });
        
        $this->silex = $app;
    }
    
    protected function tearDown()
    {
        chdir($this->previousCurrentDir);
    }
}
