<?php

namespace CSanquer\FakeryGenerator\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use CSanquer\FakeryGenerator\Config\ConfigSerializer;
use CSanquer\FakeryGenerator\Config\FakerConfig;
use CSanquer\FakeryGenerator\Dump\ConsoleDumpManager;
use CSanquer\FakeryGenerator\Dump\DumpManager;

/**
 * FakeryGeneratorProviderTest
 *
 * @author Charles Sanquer <charles.sanquer@gmail.com>
 */
class FakeryGeneratorProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testRegister()
    {
        $app = new Application();
        
        $app['root_dir'] = __DIR__.'/../../../../..';
        $app['cache_dir'] = __DIR__.'/tmp';
        $app['debug'] = true;
        
        $app->register(new FakeryGeneratorProvider());
        $app->boot();
        
        $this->assertInstanceOf('\\CSanquer\\FakeryGenerator\\Config\\FakerConfig', $app['fakery.faker.config']);
        $this->assertInstanceOf('\\CSanquer\\FakeryGenerator\\Config\\ConfigSerializer', $app['fakery.config_serializer']);
        $this->assertInstanceOf('\\CSanquer\\FakeryGenerator\\Dump\\DumpManager', $app['fakery.dumper_manager']);
        $this->assertInstanceOf('\\CSanquer\\FakeryGenerator\\Dump\\ConsoleDumpManager', $app['fakery.console_dumper_manager']);
    }
}