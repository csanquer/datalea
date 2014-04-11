<?php

namespace CSanquer\FakeryGenerator\Config;

use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Resource\FileResource;

/**
 * Fakerconfig
 *
 * @author Charles Sanquer <charles.sanquer@gmail.com>
 */
class FakerConfig
{
    CONST DEFAULT_LOCALE = 'en_US';

    protected $configDirectories;

    protected $configFiles;

    protected $cachePath;

    protected $config = [];

    /**
     * 
     * @param string $configDirectories
     * @param string $configFiles
     * @param string $cachePath
     * @param bool $debug
     */
    public function __construct($configDirectories, $configFiles, $cachePath, $debug = false)
    {
        $this->configDirectories = (array) $configDirectories;
        $this->configFiles = (array) $configFiles;
        $this->cachePath = $cachePath.'/faker_config.php';

        $configCache = new ConfigCache($this->cachePath, $debug);

        if (!$configCache->isFresh()) {
            $locator = new FileLocator($this->configDirectories);
            $loaderResolver = new LoaderResolver([new YamlLoader($locator)]);
            $delegatingLoader = new DelegatingLoader($loaderResolver);

            $resources = [];
            $config = [];
            foreach ($this->configFiles as $configFile) {
                $path = $locator->locate($configFile);
                $config = array_merge($config, $delegatingLoader->load($path));
                $resources[] = new FileResource($path);
            }

            $exportConfig = var_export($this->parseRawConfig(isset($config['faker']) ? $config['faker'] : []), true);
            $code = <<<PHP
<?php
return {$exportConfig};
PHP;

            $configCache->write($code, $resources);
        }

        if (file_exists($this->cachePath)) {
            $this->config = include $this->cachePath;
        }
    }

    protected function parseRawConfig(array $rawConfig)
    {
        $parsedConfig = [
            'locales' => [],
            'providers' => [],
            'methods' => [],
        ];

        if (isset($rawConfig['locales'])) {
            $parsedConfig['locales'] = array_unique($rawConfig['locales']);
            sort($parsedConfig['locales']);
        }

        if (isset($rawConfig['providers'])) {
            foreach ($rawConfig['providers'] as $locale => $providers) {
                foreach ($providers as $provider => $methods) {
                    $parsedConfig['providers'][] = $provider;
                    foreach ($methods as $method => $infos) {
                        $parsedConfig['methods'][$method] = [
                            'name' => $method,
                            'provider' => $provider,
                            'locale' => $locale,
                        ];

                        $parsedConfig['methods'][$method]['arguments'] = isset($infos['arguments']) ? $infos['arguments'] : [];
                        $parsedConfig['methods'][$method]['example'] = isset($infos['example']) ? $infos['example'] : null;
                    }
                }
            }
            $parsedConfig['providers'] = array_unique($parsedConfig['providers']);
            sort($parsedConfig['providers']);
            
            $parsedConfig['methods'] = $this->sortMethods($parsedConfig['methods']);
        }

        return $parsedConfig;
    }

    /**
     * 
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * 
     * @return array
     */
    public function getLocales()
    {
        return $this->config['locales'];
    }

    /**
     * 
     * @return array
     */
    public function getProviders()
    {
        return $this->config['providers'];
    }

    /**
     * 
     * @param string $name
     * 
     * @return array
     */
    public function getMethod($name)
    {
        return isset($this->config['methods'][$name]) ? $this->config['methods'][$name] : [];
    }

    /**
     * 
     * @param string $locale
     * @param string $provider
     * @return array
     */
    public function getMethods($locale = null, $provider = null)
    {
        $locales = array_unique([$locale, self::DEFAULT_LOCALE]);
        
        if (empty($locale) && empty($provider)) {
            $methods = $this->config['methods'];
        } else if (empty($locale)) {
            $methods = array_filter($this->config['methods'], function ($method) use ($provider) {
                return $method['provider'] == $provider;
            });
        } elseif (empty($provider)) {
            $methods = array_filter($this->config['methods'], function ($method) use ($locales) {
                return in_array($method['locale'], $locales);
            });
        } else {
            $methods = array_filter($this->config['methods'], function ($method) use ($locales, $provider) {
                return in_array($method['locale'], $locales) && $method['provider'] == $provider;
            });
        }
        
        return $methods;
    }
    
    /**
     * 
     * @param array $methods
     * 
     * @return array
     */
    protected function sortMethods($methods)
    {
        foreach ($methods as $method) {
            $locales[] = $method['locale'];
            $providers[] = $method['provider'];
            $names[] = $method['name'];
        }
        
        array_multisort($providers, SORT_ASC, SORT_REGULAR, $locales, SORT_ASC, SORT_REGULAR, $names, SORT_ASC, SORT_REGULAR, $methods);
        
        return $methods;
    }
}
