<?php

namespace CSanquer\FakeryGenerator\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use CSanquer\FakeryGenerator\Config\ConfigSerializer;
use CSanquer\FakeryGenerator\Config\FakerConfig;
use CSanquer\FakeryGenerator\Dump\ConsoleDumpManager;
use CSanquer\FakeryGenerator\Dump\DumpManager;

/**
 * FakeryGeneratorProvider
 *
 * @author Charles Sanquer <charles.sanquer@gmail.com>
 */
class FakeryGeneratorProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['fakery.faker.config'] = $app->share(function ($app) {
            return new FakerConfig(
                $app['root_dir'].'/src/CSanquer/FakeryGenerator/Resources/Config',
                'faker.yml',
                $app['cache_dir'],
                $app['debug']
            );
        });

        $app['fakery.config_serializer'] = $app->share(function ($app) {
            return new ConfigSerializer(
                $app['cache_dir'], 
                $app['root_dir'].'/src/CSanquer/FakeryGenerator/Resources/Config',
                $app['debug']
            );
        });

        $app['fakery.dumper_manager'] = $app->share(function ($app) {
            return new DumpManager($app['fakery.config_serializer']);
        });

        $app['fakery.console_dumper_manager'] = $app->share(function ($app) {
            return new ConsoleDumpManager($app['fakery.config_serializer']);
        });
    }

    public function boot(Application $app)
    {
        
    }
}