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
    CONST DEFAULT_CULTURE = 'en_US';
    
    protected $configDirectories;
    
    protected $configFiles;
    
    protected $cachePath;

    protected $config = array();
    
    public function __construct($configDirectories, $configFiles, $cachePath, $debug = false)
    {
        $this->configDirectories = (array) $configDirectories;
        $this->configFiles = (array) $configFiles;
        $this->cachePath = $cachePath.'/faker_config.php';
        
        $configCache = new ConfigCache($this->cachePath, $debug);

        if (!$configCache->isFresh()) {
            $locator = new FileLocator($this->configDirectories);
            $loaderResolver = new LoaderResolver(array(new YamlLoader($locator)));
            $delegatingLoader = new DelegatingLoader($loaderResolver);
            
            $resources = array();
            $config = array();
            foreach ($this->configFiles as $configFile) {
                $path = $locator->locate($configFile);
                $config = array_merge($config, $delegatingLoader->load($path));
                $resources[] = new FileResource($path);
            }

            $exportConfig = var_export($this->parseRawConfig(isset($config['faker']) ? $config['faker'] : array()), true);
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
        $parsedConfig = array(
            'cultures' => array(),
            'providers' => array(),
            'methods' => array(),
        );
        
        if (isset($rawConfig['cultures'])) {
            $parsedConfig['cultures'] = array_unique($rawConfig['cultures']);
            sort($parsedConfig['cultures']);
        }
        
        if (isset($rawConfig['providers'])) {
            foreach ($rawConfig['providers'] as $culture => $providers) {
                foreach ($providers as $provider => $methods) {
                    $parsedConfig['providers'][] = $provider;
                    foreach ($methods as $method => $infos) {
                        $parsedConfig['methods'][$method] = array(
                            'name' => $method,
                            'provider' => $provider,
                            'culture' => $culture,
                        );

                        $parsedConfig['methods'][$method]['arguments'] = isset($infos['arguments']) ? $infos['arguments'] : array();
                        $parsedConfig['methods'][$method]['example'] = isset($infos['example']) ? $infos['example'] : null;
                    }
                }
            }
            $parsedConfig['providers'] = array_unique($parsedConfig['providers']);
            sort($parsedConfig['providers']);
        }
        
        return $parsedConfig;
    }
    
    public function getConfig()
    {
        return $this->config;
    }
    
    public function getCultures()
    {
        return $this->config['cultures'];
    }
    
    public function getProviders()
    {
        return $this->config['providers'];
    }
    
    public function getMethod($name) 
    {
        return isset($this->config['methods'][$name]) ? $this->config['methods'][$name] : array();
    }
    
    public function getMethods($culture = null, $provider = null) 
    {
        if (empty($culture) && empty($provider)) {
            return $this->config['methods'];
        }
        
        if (empty($culture)) {
            return array_filter($this->config['methods'], function($method) use ($provider) {
                return $method['provider'] == $provider;
            });
        }
        
        $cultures = array_unique(array($culture, self::DEFAULT_CULTURE));
        
        if (empty($provider)) {
            return array_filter($this->config['methods'], function($method) use ($cultures) {
                return in_array($method['culture'], $cultures);
            });
        }
        
        return array_filter($this->config['methods'], function($method) use ($cultures, $provider) {
            return in_array($method['culture'], $cultures) && $method['provider'] == $provider;
        });
    }
}
