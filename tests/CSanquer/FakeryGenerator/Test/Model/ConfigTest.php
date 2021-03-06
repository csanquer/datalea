<?php

namespace CSanquer\FakeryGenerator\Test\Model;

use \CSanquer\ColibriCsv\Dialect;
use \CSanquer\FakeryGenerator\Config\FakerConfig;
use \CSanquer\FakeryGenerator\Model\Column;
use \CSanquer\FakeryGenerator\Model\Config;
use \CSanquer\FakeryGenerator\Model\Variable;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-03-05 at 12:29:44.
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var FakerConfig
     */
    protected $fakerConfig;
    
    protected static $fixtures;
    protected static $cacheDir;

    public static function setUpBeforeClass()
    {
        self::$fixtures = __DIR__.'/../Config/fixtures/';
        self::$cacheDir = __DIR__.'/../Config/tmp';
    }
    
    protected function setUp()
    {
        $fs = new Filesystem();
        if ($fs->exists(self::$cacheDir)) {
            $fs->remove(self::$cacheDir);
        }

        $this->fakerConfig = new FakerConfig(self::$fixtures, 'faker.yml', self::$cacheDir, false);
        $this->config = new Config();
    }

    public function testConstruct()
    {
        $config = new Config();
        
        $this->assertInternalType('int', $config->getSeed());
        $this->assertEquals(FakerConfig::DEFAULT_LOCALE, $config->getLocale());
        $this->assertEmpty($config->getFormats());
        $this->assertEmpty($config->getVariables());
        $this->assertEmpty($config->getColumns());
        $this->assertInstanceOf('\\CSanquer\\ColibriCsv\\Dialect', $config->getCsvDialect());
        $this->assertNull($config->getFakerConfig());
        
        
        $config2 = new Config($this->fakerConfig);
        
        $this->assertInternalType('int', $config2->getSeed());
        $this->assertEquals(FakerConfig::DEFAULT_LOCALE, $config2->getLocale());
        $this->assertEmpty($config2->getFormats());
        $this->assertEmpty($config2->getVariables());
        $this->assertEmpty($config2->getColumns());
        $this->assertInstanceOf('\\CSanquer\\ColibriCsv\\Dialect', $config2->getCsvDialect());
        $this->assertInstanceOf('\\CSanquer\\FakeryGenerator\\Config\\FakerConfig', $config2->getFakerConfig());
    }
    
    /**
     * @covers CSanquer\FakeryGenerator\Model\Config::getFakerConfig
     * @covers CSanquer\FakeryGenerator\Model\Config::setFakerConfig
     * @covers CSanquer\FakeryGenerator\Model\Config::updateVariableFakerConfig
     */
    public function testGetSetFakerConfig()
    {
        $variable = new Variable('firstname', 'firstname');
        $this->config->addVariable($variable);
        
        $this->assertNull($this->config->getFakerConfig());
        $this->assertInstanceOf('\\CSanquer\\FakeryGenerator\\Model\\Config', $this->config->setFakerConfig($this->fakerConfig));
        $this->assertInstanceOf('\\CSanquer\\FakeryGenerator\\Config\\FakerConfig', $this->config->getFakerConfig());
        
        $this->assertSame($this->config->getFakerConfig(), $variable->getFakerConfig());
    }
    
    /**
     * @covers CSanquer\FakeryGenerator\Model\Config::getLocale
     * @covers CSanquer\FakeryGenerator\Model\Config::setLocale
     */
    public function testGetSetLocale()
    {
        $this->assertEquals(FakerConfig::DEFAULT_LOCALE, $this->config->getLocale());
        $this->assertInstanceOf('\\CSanquer\\FakeryGenerator\\Model\\Config', $this->config->setLocale('fr_FR'));
        $this->assertEquals('fr_FR', $this->config->getLocale());
    }

    /**
     * @covers CSanquer\FakeryGenerator\Model\Config::getMaxTimestamp
     * @covers CSanquer\FakeryGenerator\Model\Config::setMaxTimestamp
     * @covers CSanquer\FakeryGenerator\Model\Config::updateVariableMaxTimestamp
     * @dataProvider providerGetSetMaxTimestamp
     */
    public function testGetSetMaxTimestamp($maxTimestamp, $expected)
    {
        $dateInit = $this->config->getMaxTimestamp();
        $this->assertInstanceOf('\\DateTime', $dateInit);
        $this->assertEquals(time(), $dateInit->format('U'), 'The maximum timestamp is not the current timestamp', 30);
        
        $this->assertInstanceOf('\\CSanquer\\FakeryGenerator\\Model\\Config', $this->config->setMaxTimestamp($maxTimestamp));
        $date = $this->config->getMaxTimestamp();
        $this->assertInstanceOf('\\DateTime', $date);
        if ($expected === null) {
            $this->assertEquals(time(), $date->format('U'), 'The maximum timestamp is not the current timestamp', 30);
        } else {
            $this->assertEquals($expected, $date);
        }
    }
    
    public function providerGetSetMaxTimestamp() 
    {
        return [
            [null, null],
            ['now', null],
            ['2014-01-01 12:30:45', new \DateTime('2014-01-01 12:30:45')],
            [new \DateTime('2014-01-01 12:30:45'), new \DateTime('2014-01-01 12:30:45')],
        ];
    }
    
    /**
     * @covers CSanquer\FakeryGenerator\Model\Config::updateVariableMaxTimestamp
     */
    public function testUpdateVariableMaxTimestamp()
    {
        $variable = new Variable('birthday', 'date', [], false, false, '2000-01-01 08:00:00');
        $this->assertEquals(new \DateTime('2000-01-01 08:00:00'), $variable->getMaxTimestamp());
        
        $this->config->addVariable($variable);
        
        $this->config->setMaxTimestamp('2014-06-30 00:00:00');
        $this->config->updateVariableMaxTimestamp();
        
        $this->assertEquals(new \DateTime('2014-06-30 00:00:00'), $variable->getMaxTimestamp());
    }
    
    /**
     * @covers CSanquer\FakeryGenerator\Model\Config::setSeed
     * @covers CSanquer\FakeryGenerator\Model\Config::getSeed
     * @covers CSanquer\FakeryGenerator\Model\Config::generateSeed
     */
    public function testHasGetSetGenerateSeed()
    {
        $this->assertInstanceOf('\\CSanquer\\FakeryGenerator\\Model\\Config', $this->config->generateSeed());
        $this->assertInternalType('int', $this->config->getSeed());
        $this->assertInstanceOf('\\CSanquer\\FakeryGenerator\\Model\\Config', $this->config->setSeed('2354'));
        $this->assertSame(2354, $this->config->getSeed());
    }

    /**
     * @covers CSanquer\FakeryGenerator\Model\Config::createFaker
     */
    public function testCreateFaker()
    {
        $faker = $this->config->createFaker();
        $this->assertInstanceOf('\\Faker\\Generator', $faker);
    }
    
    /**
     * @covers CSanquer\FakeryGenerator\Model\Config::getClassName
     * @covers CSanquer\FakeryGenerator\Model\Config::setClassName
     * @covers CSanquer\FakeryGenerator\Model\Config::getClassNameLastPart
     */
    public function testGetSetClassName()
    {
        $this->assertNull($this->config->getClassName());
        $this->assertInstanceOf('\\CSanquer\\FakeryGenerator\\Model\\Config', $this->config->setClassName('Entity\\User-'));
        $this->assertEquals('Entity\\User', $this->config->getClassName());
        $this->assertEquals('Entity_User', $this->config->getClassName(true));
        $this->assertEquals('User', $this->config->getClassNameLastPart());
    }

    /**
     * @covers CSanquer\FakeryGenerator\Model\Config::addFormat
     * @covers CSanquer\FakeryGenerator\Model\Config::hasFormat
     * @covers CSanquer\FakeryGenerator\Model\Config::getFormats
     * @covers CSanquer\FakeryGenerator\Model\Config::removeFormat
     */
    public function testAddHasRemoveFormat()
    {
        $this->assertFalse($this->config->hasFormat('php'));
        $this->assertInternalType('int', $this->config->getSeed());
        $this->assertInstanceOf('\\CSanquer\\FakeryGenerator\\Model\\Config', $this->config->addFormat('php'));
        $this->assertTrue($this->config->hasFormat('php'));
        $this->assertEquals(['php'], $this->config->getFormats());
        $this->assertTrue($this->config->removeFormat('php'));
        $this->assertFalse($this->config->hasFormat('php'));
        $this->assertFalse($this->config->removeFormat('json'));
    }

    /**
     * @covers CSanquer\FakeryGenerator\Model\Config::getFormats
     * @covers CSanquer\FakeryGenerator\Model\Config::setFormats
     */
    public function testGetSetFormats()
    {
        $this->assertInstanceOf('\\CSanquer\\FakeryGenerator\\Model\\Config', $this->config->setFormats(['php', 'json']));
        $this->assertSame(['php', 'json'], $this->config->getFormats());
    }

    /**
     * @covers CSanquer\FakeryGenerator\Model\Config::getFakeNumber
     * @covers CSanquer\FakeryGenerator\Model\Config::setFakeNumber
     */
    public function testGetSetFakeNumber()
    {
        $this->assertInstanceOf('\\CSanquer\\FakeryGenerator\\Model\\Config', $this->config->setFakeNumber('100'));
        $this->assertSame(100, $this->config->getFakeNumber());
    }
    
    /**
     * @covers CSanquer\FakeryGenerator\Model\Config::getCsvDialect
     * @covers CSanquer\FakeryGenerator\Model\Config::setCsvDialect
     */
    public function testGetSetsvDialect()
    {
        $dialect = new Dialect(['delimiter' => ',']);
        
        $this->assertInstanceOf('\\CSanquer\\FakeryGenerator\\Model\\Config', $this->config->setCsvDialect($dialect));
        $this->assertSame($dialect, $this->config->getCsvDialect());
    }

    /**
     * @covers CSanquer\FakeryGenerator\Model\Config::getColumns
     * @covers CSanquer\FakeryGenerator\Model\Config::setColumns
     * @covers CSanquer\FakeryGenerator\Model\Config::countColumns
     */
    public function testGetSetColumns()
    {
        $column = new Column('firstname', '%firstname%');
        $this->assertEquals(0, $this->config->countColumns());
        $this->assertInstanceOf('\\CSanquer\\FakeryGenerator\\Model\\Config', $this->config->setColumns([$column]));
        $this->assertSame(['firstname' => $column], $this->config->getColumns());
        $this->assertEquals(1, $this->config->countColumns());
    }

    /**
     * @covers CSanquer\FakeryGenerator\Model\Config::addColumn
     * @covers CSanquer\FakeryGenerator\Model\Config::getColumn
     * @covers CSanquer\FakeryGenerator\Model\Config::removeColumn
     * @covers CSanquer\FakeryGenerator\Model\Config::countColumns
     */
    public function testAddGetRemoveColumn()
    {
        $column = new Column('firstname', '%firstname%');
        $this->assertInstanceOf('\\CSanquer\\FakeryGenerator\\Model\\Config', $this->config->addColumn($column));
        $this->assertSame($column, $this->config->getColumn('firstname'));
        $this->assertEquals(1, $this->config->countColumns());
        $this->assertTrue($this->config->removeColumn($column));
        $this->assertEquals(0, $this->config->countColumns());
        $this->assertNull($this->config->getColumn('firstname'));
        $this->assertFalse($this->config->removeColumn($column));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The column must have a name.
     */
    public function testAddColumnWithEmptyName()
    {
        $this->config->addColumn(new Column(null));
    }
    
    /**
     * @covers CSanquer\FakeryGenerator\Model\Config::getVariables
     * @covers CSanquer\FakeryGenerator\Model\Config::setVariables
     * @covers CSanquer\FakeryGenerator\Model\Config::countVariables
     */
    public function testGetSetVariables()
    {
        $variable = new Variable('firstname', 'firstname');
        $this->assertEquals(0, $this->config->countVariables());
        $this->assertInstanceOf('\\CSanquer\\FakeryGenerator\\Model\\Config', $this->config->setVariables([$variable]));
        $this->assertSame(['firstname' => $variable], $this->config->getVariables());
        $this->assertEquals(1, $this->config->countVariables());
    }

    /**
     * @covers CSanquer\FakeryGenerator\Model\Config::addVariable
     * @covers CSanquer\FakeryGenerator\Model\Config::getVariable
     * @covers CSanquer\FakeryGenerator\Model\Config::removeVariable
     * @covers CSanquer\FakeryGenerator\Model\Config::countVariables
     */
    public function testAddGetRemoveVariable()
    {
        $this->config->setFakerConfig($this->fakerConfig);
        
        $variable = new Variable('firstname', 'firstname');
        $this->assertInstanceOf('\\CSanquer\\FakeryGenerator\\Model\\Config', $this->config->addVariable($variable));
        $this->assertSame($variable, $this->config->getVariable('firstname'));
        $this->assertSame($this->config->getFakerConfig(), $this->config->getVariable('firstname')->getFakerConfig());
        
        $this->assertEquals(1, $this->config->countVariables());
        $this->assertTrue($this->config->removeVariable($variable));
        $this->assertEquals(0, $this->config->countVariables());
        $this->assertNull($this->config->getVariable('firstname'));
        $this->assertFalse($this->config->removeVariable($variable));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The variable must have a name.
     */
    public function testAddVariableWithEmptyName()
    {
        $this->config->addVariable(new Variable(null));
    }
    
    /**
     * @covers CSanquer\FakeryGenerator\Model\Config::createDefaultColumns
     */
    public function testCreateDefaultColumns()
    {
        $this->config->addVariable(new Variable('firstname', 'firstname'));
        $this->config->createDefaultColumns();
        $this->assertEquals(['firstname' => new Column('firstname', '%firstname%')], $this->config->getColumns());
    }
    
    public function testGetColumnNames()
    {
        $this->config->setColumns([
            new Column('person', null, null, [
                new Column('name', null, null, [
                    new Column('firstname', '%firstname%', 'capitalize'),
                    new Column('lastname', '%lastname%', 'capitalize'),
                ]),
                new Column('email', '%firstname%.%lastname%@%emailDomain%', 'lowercase'),
            ]),
            new Column('birthday', '%birthday%'),
        ]);
        
        $this->assertEquals([
            'person' => [
                'name' => [
                    'firstname' => 'firstname',
                    'lastname' => 'lastname',
                ],
                'email' => 'email',
            ],
            'birthday' => 'birthday',
        ], $this->config->getColumnNames(false));
        
        $this->assertEquals([
            'person-name-firstname' => 'person-name-firstname',
            'person-name-lastname' => 'person-name-lastname',
            'person-email' => 'person-email',
            'birthday' => 'birthday',
        ], $this->config->getColumnNames(true));
    }
    
    /**
     * @covers CSanquer\FakeryGenerator\Model\Config::generateVariableValues
     */
    public function testGenerateVariableValues()
    {
        $faker = \Faker\Factory::create('en_US');
        
        $this->config->setVariables([
            new Variable('firstname', 'firstName'),
            new Variable('lastname', 'lastName'),
            new Variable('emailDomain', 'freeEmailDomain'),
            new Variable('birthday', 'dateTimeThisCentury', ['Y-m-d']),
        ]);
        
        $values = [];
        $this->config->generateVariableValues($faker, $values);
        $this->assertArrayHasKey('firstname', $values);
        $this->assertArrayHasKey('lastname', $values);
        $this->assertArrayHasKey('emailDomain', $values);
        $this->assertArrayHasKey('birthday', $values);
        
        foreach ($values as $value) {
            $this->assertArrayHasKey('flat', $value);
            $this->assertArrayHasKey('raw', $value);
            $this->assertNotEmpty($value['raw']);
            $this->assertNotEmpty($value['flat']);
        }
    }
    
    /**
     * @covers CSanquer\FakeryGenerator\Model\Config::generateColumnValues
     */
    public function testGenerateColumnValues()
    {
        $values =  [
            'firstname' => [
              'raw' => 'Allene',
              'flat' => 'Allene',
            ],
            'lastname' => [
              'raw' => 'McGlynn',
              'flat' => 'McGlynn',
            ],
            'emailDomain' => [
              'raw' => 'yahoo.com',
              'flat' => 'yahoo.com',
            ],
            'birthday' => [
              'raw' =>  new \DateTime('2000-10-13 20:30:58', new \DateTimeZone('Europe/Paris')),
              'flat' => '2000-10-13',
            ],
          ];
        
        $this->config->setColumns([
            new Column('name', null, null, [
                new Column('firstname', '%firstname%', 'capitalize'),
                new Column('lastname', '%lastname%', 'capitalize'),
            ]),
            new Column('email', '%firstname%.%lastname%@%emailDomain%', 'lowercase'),
            new Column('birthday', '%birthday%'),
        ]);
        
        $columnValues = $this->config->generateColumnValues($values);
        $this->assertEquals([
            'name' => [
                'firstname' => 'Allene',
                'lastname' => 'McGlynn',
            ],
            'email' => 'allene.mcglynn@yahoo.com',
            'birthday' => '2000-10-13',
        ], $columnValues);
    }
}
