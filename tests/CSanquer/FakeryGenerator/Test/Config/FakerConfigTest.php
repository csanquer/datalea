<?php

namespace CSanquer\FakeryGenerator\Test\Config;

use CSanquer\FakeryGenerator\Config\FakerConfig;

/**
 * FakerConfigTest
 * 
 * @author Charles Sanquer <charles.sanquer@gmail.com>
 */
class FakerConfigTest extends \PHPUnit_Framework_TestCase
{
    protected static $fixtures;
    
    protected static $cacheDir;

    /**
     * @var FakerConfig
     */
    protected $config;

    public static function setUpBeforeClass()
    {
        self::$fixtures = __DIR__.'/fixtures/';
        self::$cacheDir = __DIR__.'/tmp';
    }
    
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $fs = new \Symfony\Component\Filesystem\Filesystem();
        if ($fs->exists(self::$cacheDir)) {
            $fs->remove(self::$cacheDir);
        }
        $fs->mkdir(self::$cacheDir);
        
        $this->config = new FakerConfig(self::$fixtures, 'faker.yml', self::$cacheDir, false);
    }

    public function testGetConfig()
    {
        $expected = array(
            'cultures' => array(
                'en_US',
                'es_ES',
                'fr_FR',
            ),
            'providers' => array(
                'departmentName' => array(
                    'name' => '',
                    'provider' => '',
                    'culture' => '',
                    'arguments' => array(),
                    'example' => '',
                ),
                'departmentNumber' => array(
                    'name' => '',
                    'provider' => '',
                    'culture' => '',
                    'arguments' => array(),
                    'example' => '',
                    
                ),
                'firstName' => array(
                    'name' => '',
                    'provider' => '',
                    'culture' => '',
                    'arguments' => array(),
                    'example' => '',
                ),
                'lastName' => array(
                    'name' => 'lastName',
                    'provider' => 'Person',
                    'culture' => 'en_US',
                    'arguments' => array(),
                    'example' => '\'Zulauf\'',
                ),
                'buildingNumber' => array(
                    'name' => '',
                    'provider' => '',
                    'culture' => '',
                    'arguments' => array(),
                    'example' => '',
                ),
                'city' => array(
                    'name' => '',
                    'provider' => '',
                    'culture' => '',
                    'arguments' => array(),
                    'example' => '',
                ),
                'streetName' => array(
                    'name' => '',
                    'provider' => '',
                    'culture' => '',
                    'arguments' => array(),
                    'example' => '',
                ),
                'postcode' => array(
                    'name' => '',
                    'provider' => '',
                    'culture' => '',
                    'arguments' => array(),
                    'example' => '',
                ),
                'country' => array(
                    'name' => '',
                    'provider' => '',
                    'culture' => '',
                    'arguments' => array(),
                    'example' => '',
                ),
            ),
        );
        $this->assertEquals($expected, $this->config->getConfig());
    }
}
