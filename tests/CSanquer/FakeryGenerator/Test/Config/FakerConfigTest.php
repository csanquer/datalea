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
                'Address',
                'Company',
                'Person',
            ),
            'methods' => array(
                'departmentName' => array(
                    'name' => 'departmentName',
                    'provider' => 'Address',
                    'culture' => 'fr_FR',
                    'arguments' => array(),
                    'example' => '\'Haut-Rhin\'',
                ),
                'departmentNumber' => array(
                    'name' => 'departmentNumber',
                    'provider' => 'Address',
                    'culture' => 'fr_FR',
                    'arguments' => array(),
                    'example' => '\'2B\'',
                    
                ),
                'siret' => array(
                    'name' => 'siret',
                    'provider' => 'Company',
                    'culture' => 'fr_FR',
                    'arguments' => array('sequential_digits' => 2),
                    'example' => '\'347 355 708 00224\''
                ),
                'firstName' => array(
                    'name' => 'firstName',
                    'provider' => 'Person',
                    'culture' => 'en_US',
                    'arguments' => array(),
                    'example' => '\'Maynard\'',
                ),
                'lastName' => array(
                    'name' => 'lastName',
                    'provider' => 'Person',
                    'culture' => 'en_US',
                    'arguments' => array(),
                    'example' => '\'Zulauf\'',
                ),
                'buildingNumber' => array(
                    'name' => 'buildingNumber',
                    'provider' => 'Address',
                    'culture' => 'en_US',
                    'arguments' => array(),
                    'example' => '\'484\'',
                ),
                'city' => array(
                    'name' => 'city',
                    'provider' => 'Address',
                    'culture' => 'en_US',
                    'arguments' => array(),
                    'example' => '\'West Judge\'',
                ),
                'streetName' => array(
                    'name' => 'streetName',
                    'provider' => 'Address',
                    'culture' => 'en_US',
                    'arguments' => array(),
                    'example' => '\'Keegan Trail\'',
                ),
                'postcode' => array(
                    'name' => 'postcode',
                    'provider' => 'Address',
                    'culture' => 'en_US',
                    'arguments' => array(),
                    'example' => '\'17916\'',
                ),
                'country' => array(
                    'name' => 'country',
                    'provider' => 'Address',
                    'culture' => 'en_US',
                    'arguments' => array(),
                    'example' => '\'Falkland Islands (Malvinas)\'',
                ),
            ),
        );
        $this->assertEquals($expected, $this->config->getConfig());
    }
    
    public function testGetAvailableCulture()
    {
        $expected = array(
            'en_US',
            'es_ES',
            'fr_FR',
        );
        
        $this->assertEquals($expected, $this->config->getAvailableCultures());
    }
    
    public function testGetAvailableProviders()
    {
        $expected = array(
            'Address',
                'Company',
                'Person',
        );
        
        $this->assertEquals($expected, $this->config->getAvailableProviders());
    }
    
    public function testGetAvailableMethods()
    {
        $expected = array(
            'departmentName' => array(
                'name' => 'departmentName',
                'provider' => 'Address',
                'culture' => 'fr_FR',
                'arguments' => array(),
                'example' => '\'Haut-Rhin\'',
            ),
            'departmentNumber' => array(
                'name' => 'departmentNumber',
                'provider' => 'Address',
                'culture' => 'fr_FR',
                'arguments' => array(),
                'example' => '\'2B\'',

            ),
            'siret' => array(
                'name' => 'siret',
                'provider' => 'Company',
                'culture' => 'fr_FR',
                'arguments' => array('sequential_digits' => 2),
                'example' => '\'347 355 708 00224\''
            ),
            'firstName' => array(
                'name' => 'firstName',
                'provider' => 'Person',
                'culture' => 'en_US',
                'arguments' => array(),
                'example' => '\'Maynard\'',
            ),
            'lastName' => array(
                'name' => 'lastName',
                'provider' => 'Person',
                'culture' => 'en_US',
                'arguments' => array(),
                'example' => '\'Zulauf\'',
            ),
            'buildingNumber' => array(
                'name' => 'buildingNumber',
                'provider' => 'Address',
                'culture' => 'en_US',
                'arguments' => array(),
                'example' => '\'484\'',
            ),
            'city' => array(
                'name' => 'city',
                'provider' => 'Address',
                'culture' => 'en_US',
                'arguments' => array(),
                'example' => '\'West Judge\'',
            ),
            'streetName' => array(
                'name' => 'streetName',
                'provider' => 'Address',
                'culture' => 'en_US',
                'arguments' => array(),
                'example' => '\'Keegan Trail\'',
            ),
            'postcode' => array(
                'name' => 'postcode',
                'provider' => 'Address',
                'culture' => 'en_US',
                'arguments' => array(),
                'example' => '\'17916\'',
            ),
            'country' => array(
                'name' => 'country',
                'provider' => 'Address',
                'culture' => 'en_US',
                'arguments' => array(),
                'example' => '\'Falkland Islands (Malvinas)\'',
            ),
        );
        
        $this->assertEquals($expected, $this->config->getAvailableMethods());
    }
}
