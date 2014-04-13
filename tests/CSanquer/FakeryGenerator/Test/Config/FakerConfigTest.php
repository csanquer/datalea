<?php

namespace CSanquer\FakeryGenerator\Test\Config;

use CSanquer\FakeryGenerator\Config\FakerConfig;
use Symfony\Component\Filesystem\Filesystem;

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
        $fs = new Filesystem();
        if ($fs->exists(self::$cacheDir)) {
            $fs->remove(self::$cacheDir);
        }

        $this->config = new FakerConfig(self::$fixtures, 'faker.yml', self::$cacheDir, false);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testMissingConfigFile()
    {
        $fs = new Filesystem();
        if ($fs->exists(self::$cacheDir)) {
            $fs->remove(self::$cacheDir);
        }
        $fs->mkdir(self::$cacheDir);

        $this->config = new FakerConfig(self::$fixtures, 'foobar.yml', self::$cacheDir, false);
    }

    /**
     * @covers \CSanquer\FakeryGenerator\Config\FakerConfig::getConfig
     */
    public function testGetConfig()
    {
        $expected = [
            'locales' => [
                'en_US',
                'es_ES',
                'fr_FR',
            ],
            'providers' => [
                'Address',
                'Company',
                'Person',
            ],
            'methods' => [
                'departmentName' => [
                    'name' => 'departmentName',
                    'provider' => 'Address',
                    'locale' => 'fr_FR',
                    'arguments' => [],
                    'example' => '\'Haut-Rhin\'',
                ],
                'departmentNumber' => [
                    'name' => 'departmentNumber',
                    'provider' => 'Address',
                    'locale' => 'fr_FR',
                    'arguments' => [],
                    'example' => '\'2B\'',
                ],
                'siret' => [
                    'name' => 'siret',
                    'provider' => 'Company',
                    'locale' => 'fr_FR',
                    'arguments' => ['sequential_digits' => 2],
                    'example' => '\'347 355 708 00224\''
                ],
                'firstName' => [
                    'name' => 'firstName',
                    'provider' => 'Person',
                    'locale' => 'en_US',
                    'arguments' => [],
                    'example' => '\'Maynard\'',
                ],
                'lastName' => [
                    'name' => 'lastName',
                    'provider' => 'Person',
                    'locale' => 'en_US',
                    'arguments' => [],
                    'example' => '\'Zulauf\'',
                ],
                'buildingNumber' => [
                    'name' => 'buildingNumber',
                    'provider' => 'Address',
                    'locale' => 'en_US',
                    'arguments' => [],
                    'example' => '\'484\'',
                ],
                'city' => [
                    'name' => 'city',
                    'provider' => 'Address',
                    'locale' => 'en_US',
                    'arguments' => [],
                    'example' => '\'West Judge\'',
                ],
                'streetName' => [
                    'name' => 'streetName',
                    'provider' => 'Address',
                    'locale' => 'en_US',
                    'arguments' => [],
                    'example' => '\'Keegan Trail\'',
                ],
                'postcode' => [
                    'name' => 'postcode',
                    'provider' => 'Address',
                    'locale' => 'en_US',
                    'arguments' => [],
                    'example' => '\'17916\'',
                ],
                'country' => [
                    'name' => 'country',
                    'provider' => 'Address',
                    'locale' => 'en_US',
                    'arguments' => [],
                    'example' => '\'Falkland Islands (Malvinas)\'',
                ],
            ],
        ];
        $this->assertEquals($expected, $this->config->getConfig());
    }

    /**
     * @covers \CSanquer\FakeryGenerator\Config\FakerConfig::getLocales
     */
    public function testGetLocales()
    {
        $expected = [
            'en_US',
            'es_ES',
            'fr_FR',
        ];

        $this->assertEquals($expected, $this->config->getLocales());
    }

    /**
     * @covers \CSanquer\FakeryGenerator\Config\FakerConfig::getProviders
     */
    public function testGetProviders()
    {
        $expected = [
            'Address',
            'Company',
            'Person',
        ];

        $this->assertEquals($expected, $this->config->getProviders());
    }

    /**
     * @covers \CSanquer\FakeryGenerator\Config\FakerConfig::getMethods
     * @dataProvider providerGetMethods
     */
    public function testGetMethods($locale, $provider, $expected)
    {
        $this->assertSame($expected, $this->config->getMethods($locale, $provider));
    }

    public function providerGetMethods()
    {
        return [
            //data set #0
            [
                null,
                null,
                [
                    'buildingNumber' => [
                        'name' => 'buildingNumber',
                        'provider' => 'Address',
                        'locale' => 'en_US',
                        'arguments' => [],
                        'example' => '\'484\'',
                    ],
                    'city' => [
                        'name' => 'city',
                        'provider' => 'Address',
                        'locale' => 'en_US',
                        'arguments' => [],
                        'example' => '\'West Judge\'',
                    ],
                    'country' => [
                        'name' => 'country',
                        'provider' => 'Address',
                        'locale' => 'en_US',
                        'arguments' => [],
                        'example' => '\'Falkland Islands (Malvinas)\'',
                    ],
                    'postcode' => [
                        'name' => 'postcode',
                        'provider' => 'Address',
                        'locale' => 'en_US',
                        'arguments' => [],
                        'example' => '\'17916\'',
                    ],
                    'streetName' => [
                        'name' => 'streetName',
                        'provider' => 'Address',
                        'locale' => 'en_US',
                        'arguments' => [],
                        'example' => '\'Keegan Trail\'',
                    ],
                    'departmentName' => [
                        'name' => 'departmentName',
                        'provider' => 'Address',
                        'locale' => 'fr_FR',
                        'arguments' => [],
                        'example' => '\'Haut-Rhin\'',
                    ],
                    'departmentNumber' => [
                        'name' => 'departmentNumber',
                        'provider' => 'Address',
                        'locale' => 'fr_FR',
                        'arguments' => [],
                        'example' => '\'2B\'',
                    ],
                    'siret' => [
                        'name' => 'siret',
                        'provider' => 'Company',
                        'locale' => 'fr_FR',
                        'arguments' => ['sequential_digits' => 2],
                        'example' => '\'347 355 708 00224\''
                    ],
                    'firstName' => [
                        'name' => 'firstName',
                        'provider' => 'Person',
                        'locale' => 'en_US',
                        'arguments' => [],
                        'example' => '\'Maynard\'',
                    ],
                    'lastName' => [
                        'name' => 'lastName',
                        'provider' => 'Person',
                        'locale' => 'en_US',
                        'arguments' => [],
                        'example' => '\'Zulauf\'',
                    ],
                ],
            ],
            //data set #1
            [
                'en_US',
                '',
                [
                    'buildingNumber' => [
                        'name' => 'buildingNumber',
                        'provider' => 'Address',
                        'locale' => 'en_US',
                        'arguments' => [],
                        'example' => '\'484\'',
                    ],
                    'city' => [
                        'name' => 'city',
                        'provider' => 'Address',
                        'locale' => 'en_US',
                        'arguments' => [],
                        'example' => '\'West Judge\'',
                    ],
                    'country' => [
                        'name' => 'country',
                        'provider' => 'Address',
                        'locale' => 'en_US',
                        'arguments' => [],
                        'example' => '\'Falkland Islands (Malvinas)\'',
                    ],
                    'postcode' => [
                        'name' => 'postcode',
                        'provider' => 'Address',
                        'locale' => 'en_US',
                        'arguments' => [],
                        'example' => '\'17916\'',
                    ],
                    'streetName' => [
                        'name' => 'streetName',
                        'provider' => 'Address',
                        'locale' => 'en_US',
                        'arguments' => [],
                        'example' => '\'Keegan Trail\'',
                    ],
                    'firstName' => [
                        'name' => 'firstName',
                        'provider' => 'Person',
                        'locale' => 'en_US',
                        'arguments' => [],
                        'example' => '\'Maynard\'',
                    ],
                    'lastName' => [
                        'name' => 'lastName',
                        'provider' => 'Person',
                        'locale' => 'en_US',
                        'arguments' => [],
                        'example' => '\'Zulauf\'',
                    ],
                ]
            ],
            //data set #2
            [
                'en_US',
                'Person',
                [
                    'firstName' => [
                        'name' => 'firstName',
                        'provider' => 'Person',
                        'locale' => 'en_US',
                        'arguments' => [],
                        'example' => '\'Maynard\'',
                    ],
                    'lastName' => [
                        'name' => 'lastName',
                        'provider' => 'Person',
                        'locale' => 'en_US',
                        'arguments' => [],
                        'example' => '\'Zulauf\'',
                    ],
                ],
            ],
            //data set #3
            [
                'fr_FR',
                'Address',
                [
                    'buildingNumber' => [
                        'name' => 'buildingNumber',
                        'provider' => 'Address',
                        'locale' => 'en_US',
                        'arguments' => [],
                        'example' => '\'484\'',
                    ],
                    'city' => [
                        'name' => 'city',
                        'provider' => 'Address',
                        'locale' => 'en_US',
                        'arguments' => [],
                        'example' => '\'West Judge\'',
                    ],
                    'country' => [
                        'name' => 'country',
                        'provider' => 'Address',
                        'locale' => 'en_US',
                        'arguments' => [],
                        'example' => '\'Falkland Islands (Malvinas)\'',
                    ],
                    'postcode' => [
                        'name' => 'postcode',
                        'provider' => 'Address',
                        'locale' => 'en_US',
                        'arguments' => [],
                        'example' => '\'17916\'',
                    ],
                    'streetName' => [
                        'name' => 'streetName',
                        'provider' => 'Address',
                        'locale' => 'en_US',
                        'arguments' => [],
                        'example' => '\'Keegan Trail\'',
                    ],
                    'departmentName' => [
                        'name' => 'departmentName',
                        'provider' => 'Address',
                        'locale' => 'fr_FR',
                        'arguments' => [],
                        'example' => '\'Haut-Rhin\'',
                    ],
                    'departmentNumber' => [
                        'name' => 'departmentNumber',
                        'provider' => 'Address',
                        'locale' => 'fr_FR',
                        'arguments' => [],
                        'example' => '\'2B\'',
                    ],
                ],
            ],
            //data set #4
            [
                null,
                'Address',
                [
                    'buildingNumber' => [
                        'name' => 'buildingNumber',
                        'provider' => 'Address',
                        'locale' => 'en_US',
                        'arguments' => [],
                        'example' => '\'484\'',
                    ],
                    'city' => [
                        'name' => 'city',
                        'provider' => 'Address',
                        'locale' => 'en_US',
                        'arguments' => [],
                        'example' => '\'West Judge\'',
                    ],
                    'country' => [
                        'name' => 'country',
                        'provider' => 'Address',
                        'locale' => 'en_US',
                        'arguments' => [],
                        'example' => '\'Falkland Islands (Malvinas)\'',
                    ],
                    'postcode' => [
                        'name' => 'postcode',
                        'provider' => 'Address',
                        'locale' => 'en_US',
                        'arguments' => [],
                        'example' => '\'17916\'',
                    ],
                    'streetName' => [
                        'name' => 'streetName',
                        'provider' => 'Address',
                        'locale' => 'en_US',
                        'arguments' => [],
                        'example' => '\'Keegan Trail\'',
                    ],
                    'departmentName' => [
                        'name' => 'departmentName',
                        'provider' => 'Address',
                        'locale' => 'fr_FR',
                        'arguments' => [],
                        'example' => '\'Haut-Rhin\'',
                    ],
                    'departmentNumber' => [
                        'name' => 'departmentNumber',
                        'provider' => 'Address',
                        'locale' => 'fr_FR',
                        'arguments' => [],
                        'example' => '\'2B\'',
                    ],
                ],
            ],
        ];
    }

    /**
     * @covers \CSanquer\FakeryGenerator\Config\FakerConfig::getMethod
     * @dataProvider providerGetMethod
     */
    public function testGetMethod($name, $expected)
    {
        $this->assertEquals($expected, $this->config->getMethod($name));
    }

    public function providerGetMethod()
    {
        return [
            //data set #0
            [
                'firstName',
                [
                    'name' => 'firstName',
                    'provider' => 'Person',
                    'locale' => 'en_US',
                    'arguments' => [],
                    'example' => '\'Maynard\'',
                ],
            ],
            //data set #1
            [
                'foobar',
                [],
            ],
        ];
    }
}
