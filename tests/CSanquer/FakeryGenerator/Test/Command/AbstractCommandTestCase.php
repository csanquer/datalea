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
        self::$fixtures = __DIR__.'/fixtures/';
        self::$cacheDir = __DIR__.'/tmp';
        self::$dumpDir = __DIR__.'/dump';
    }

    protected function setUp()
    {
        $this->previousCurrentDir = getcwd();
        chdir(__DIR__);
        
        $fs = new Filesystem();
        if ($fs->exists(self::$cacheDir)) {
            $fs->remove(self::$cacheDir);
        }
        
        if ($fs->exists(self::$dumpDir)) {
            $fs->remove(self::$dumpDir);
        }
        
        $app =  new Application();
        //custom providers and services
        $app['fakery.faker.config'] = $app->share(function ($app) {
            return new FakerConfig(
                __DIR__.'/../../../../../src/CSanquer/FakeryGenerator/Resources/Config', 
                'faker.yml',
                self::$cacheDir.'/cache/', 
                true
            );
        });

        $app['fakery.config_serializer'] = $app->share(function ($app) {
            return new ConfigSerializer(
                self::$cacheDir.'/cache/', 
                __DIR__.'/../../../../../src/CSanquer/FakeryGenerator/Resources/Config', 
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
