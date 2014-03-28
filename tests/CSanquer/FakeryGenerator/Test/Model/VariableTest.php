<?php

namespace CSanquer\FakeryGenerator\Test\Model;

use CSanquer\FakeryGenerator\Model\Variable;

/**
 * VariableTest
 *
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-02-13 at 08:23:50.
 * 
 * @author Charles Sanquer <charles.sanquer@gmail.com>
 */
class VariableTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Variable
     */
    protected $variable;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->variable = new Variable();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

    public function testConstruct()
    {
        $variable = new Variable('firstname', 'firstName', [1], true, true);
        $this->assertEquals('firstname', $variable->getName());
        $this->assertEquals('firstName', $variable->getMethod());
        $this->assertEquals([1], $variable->getMethodArguments());
        $this->assertEquals(true, $variable->isUnique());
        $this->assertEquals(0.5, $variable->getOptional());
    }
    
    /**
     * @covers CSanquer\FakeryGenerator\Model\Variable::getName
     * @covers CSanquer\FakeryGenerator\Model\Variable::getVarName
     * @covers CSanquer\FakeryGenerator\Model\Variable::setName
     * 
     * @dataProvider providerGetSetName
     */
    public function testGetSetName($name, $expected)
    {
        $this->assertInstanceOf('\\CSanquer\\FakeryGenerator\\Model\\Variable', $this->variable->setName($name));
        $this->assertEquals($expected, $this->variable->getName());
        $this->assertEquals('%'.$expected.'%', $this->variable->getVarName());
    }

    public function providerGetSetName()
    {
        return [
            ['firstname', 'firstname'],
            ['^$fi;:rstname%;', 'firstname'],
        ];
    }
    
    /**
     * @covers CSanquer\FakeryGenerator\Model\Variable::getMethod
     * @covers CSanquer\FakeryGenerator\Model\Variable::setMethod
     */
    public function testGetSetMethod()
    {
        $this->assertInstanceOf('\\CSanquer\\FakeryGenerator\\Model\\Variable', $this->variable->setMethod('firstName'));
        $this->assertEquals('firstName', $this->variable->getMethod());
    }

    /**
     * @covers CSanquer\FakeryGenerator\Model\Variable::getMethodArguments
     * @covers CSanquer\FakeryGenerator\Model\Variable::setMethodArguments
     * @covers CSanquer\FakeryGenerator\Model\Variable::hasMethodArguments
     */
    public function testGetMethodArguments()
    {
        $this->assertFalse($this->variable->hasMethodArguments());
        $this->assertInstanceOf('\\CSanquer\\FakeryGenerator\\Model\\Variable', $this->variable->setMethodArguments([1,2]));
        $this->assertTrue($this->variable->hasMethodArguments());
        $this->assertEquals([1,2], $this->variable->getMethodArguments());
    }

    /**
     * @covers CSanquer\FakeryGenerator\Model\Variable::getMethodArgument
     * @covers CSanquer\FakeryGenerator\Model\Variable::hasMethodArgument
     * @covers CSanquer\FakeryGenerator\Model\Variable::addMethodArgument
     * @covers CSanquer\FakeryGenerator\Model\Variable::setMethodArgument
     */
    public function testGetMethodArgument()
    {
        $this->assertFalse($this->variable->hasMethodArgument(0));
        $this->assertInstanceOf('\\CSanquer\\FakeryGenerator\\Model\\Variable', $this->variable->setMethodArgument(0, 1));
        $this->assertTrue($this->variable->hasMethodArguments(0));
        $this->assertEquals(1, $this->variable->getMethodArgument(0));
        $this->assertFalse($this->variable->hasMethodArgument(1));
        $this->assertInstanceOf('\\CSanquer\\FakeryGenerator\\Model\\Variable', $this->variable->addMethodArgument('5'));
        $this->assertTrue($this->variable->hasMethodArguments(0));
        $this->assertTrue($this->variable->hasMethodArguments(1));
        $this->assertEquals(1, $this->variable->getMethodArgument(0));
        $this->assertEquals('5', $this->variable->getMethodArgument(1));
    }

    /**
     * @covers CSanquer\FakeryGenerator\Model\Variable::isUnique
     * @covers CSanquer\FakeryGenerator\Model\Variable::setUnique
     * @dataProvider providerIsSetUnique
     */
    public function testIsSetUnique($unique, $expected)
    {
        $this->assertInstanceOf('\\CSanquer\\FakeryGenerator\\Model\\Variable', $this->variable->setUnique($unique));
        $isUnique = $this->variable->isUnique();
        $this->assertInternalType('bool', $isUnique);
        $this->assertEquals($expected, $isUnique);
    }
    
    public function providerIsSetUnique() 
    {
        return [
            [0, false],
            [1, true],
            [null, false],
            ['', false],
            [true, true],
            [false, false],
        ];
    }
    
    /**
     * @covers CSanquer\FakeryGenerator\Model\Variable::getOptional
     * @covers CSanquer\FakeryGenerator\Model\Variable::setOptional
     * @dataProvider providerIsSetOptional
     */
    public function testIsSetOptional($unique, $expected, $type)
    {
        $this->assertInstanceOf('\\CSanquer\\FakeryGenerator\\Model\\Variable', $this->variable->setOptional($unique));
        $isOptional = $this->variable->getOptional();
        $this->assertInternalType($type, $isOptional);
        $this->assertEquals($expected, $isOptional);
    }
    
    public function providerIsSetOptional() 
    {
        return [
            [0, 0.0, 'float'],
            [1, 1.0, 'float'],
            ['50.5', 1.0, 'float'],
            [null, null, 'null'],
            ['', null, 'null'],
            [true, 0.5, 'float'],
            [false, null, 'null'],
        ];
    }

    /**
     * @covers CSanquer\FakeryGenerator\Model\Variable::generateValue
     * @covers CSanquer\FakeryGenerator\Model\Variable::generate
     * @covers CSanquer\FakeryGenerator\Model\Variable::replaceVariables
     * @dataProvider providerGenerateValue
     */
    public function testGenerateValue(
        $expected,
        $faker,
        $values,
        $name, 
        $method, 
        $methodArguments, 
        $variables = [],
        $optional = false, 
        $unique = false, 
        $force = false, 
        $useIncrement = false, 
        $resetIncrement = false
    ) 
    {
        $this->variable->setName($name);
        $this->variable->setMethod($method);
        $this->variable->setMethodArguments($methodArguments);
        $this->variable->setOptional($optional);
        $this->variable->setUnique($unique);
        
        $this->variable->generateValue($faker, $values, $variables, $force, $useIncrement, $resetIncrement);
        
        $this->assertArrayHasKey($name, $values);
        $this->assertArrayHasKey('raw', $values[$name]);
        $this->assertArrayHasKey('flat', $values[$name]);
        
        foreach ($expected as $key => $rules) {
            $this->assertArrayHasKey($key, $values);
            
            if (!empty($rules['raw_type'])) {
                $this->assertInternalType($rules['raw_type'], $values[$key]['raw'], 'raw type is not valid for variable '.$key);
            }

            if (!empty($rules['raw_class'])) {
                $this->assertInstanceOf($rules['raw_class'], $values[$key]['raw'], 'raw class is not valid for variable '.$key);
            }
            
            if (isset($rules['raw_count']) && !is_null($rules['raw_count'])) {
                $this->assertCount($rules['raw_count'], $values[$key]['raw'], 'raw count is not valid for variable '.$key);
            }

            if (!empty($rules['flat_pattern'])) {
                $this->assertRegExp($rules['flat_pattern'], (string) $values[$key]['flat'], 'flat value does not match for variable '.$key);
            }
            
            if (isset($rules['flat_length']) && !is_null($rules['flat_length'])) {
                $this->assertEquals($rules['flat_length'], strlen($values[$key]['flat']), 'flat value length is not valid for variable '.$key);
            }
        }
    }
    
    public function providerGenerateValue() 
    {
        $defaultFaker = \Faker\Factory::create('en_US');
        
        $fakerFixDigit = new \Faker\Generator();
        $fakerFixDigit->addProvider(new FixDigitProvider());
        
        $fakerOptional = new \Faker\Generator();
        $fakerOptional->addProvider(new \Faker\Provider\Base($fakerOptional));
        $fakerOptional->addProvider(new \ArrayObject([1]));
        
        return [
            // data set #0 simple, no argument
            [
                // expected rules
                [
                    'name_prefix' => [
                        'raw_type' => 'string',
                        'raw_class' => null,
                        'flat_pattern' => '/^(Mr\.|Mrs\.|Ms\.|Miss|Dr\.)$/',
                    ],
                ], 
                $defaultFaker, // faker 
                // values
                [],  
                'name_prefix', // name 
                'prefix', // method 
                // methodArguments 
                [], 
                // variables
                [],  
                false, // optional 
                false, // unique 
                false, // force 
                false, // useIncrement 
                false, // resetIncrement 
            ],
            // data set #1 with argument
            [
                // expected rules
                [
                    'letter' => [
                        'raw_type' => 'string',
                        'raw_class' => null,
                        'flat_pattern' => '/^[abc]$/',
                    ],
                ], 
                $defaultFaker, // faker 
                // values
                [],  
                'letter', // name 
                'randomElement', // method 
                // methodArguments 
                ['a,b,c'], 
                // variables
                [
                ],  
                false, // optional 
                false, // unique 
                false, // force 
                false, // useIncrement 
                false, // resetIncrement 
            ],
            // data set #2 with a variable as argument
            [
                // expected rules
                [
                    'random_digit' => [
                        'raw_type' => 'int',
                        'raw_class' => null,
                        'flat_pattern' => '/^\d$/',
                    ],
                    'random_number' => [
                        'raw_type' => 'int',
                        'raw_class' => null,
                        'flat_pattern' => '/^\d*$/',
                    ],
                ], 
                $defaultFaker, // faker 
                // values
                [],  
                'random_number', // name 
                'randomNumber', // method 
                // methodArguments 
                ['%random_digit%'], 
                // variables
                [
                    'random_digit' => new Variable('random_digit', 'randomDigit'),
                ],  
                false, // optional 
                false, // unique 
                false, // force 
                false, // useIncrement 
                false, // resetIncrement 
            ],
            // data set #3 non existent faker method
            [
                // expected rules
                [
                    'empty_var' => [
                        'raw_type' => 'null',
                        'raw_class' => null,
                        'flat_pattern' => '/^$/',
                    ],
                ], 
                $defaultFaker, // faker 
                // values
                [],  
                'empty_var', // name 
                'foobar', // method 
                // methodArguments 
                [], 
                // variables
                [
                ],  
                false, // optional 
                false, // unique 
                false, // force 
                false, // useIncrement 
                false, // resetIncrement 
            ],
            // data set #4 words
            [
                // expected rules
                [
                    'wording' => [
                        'raw_type' => 'array',
                        'raw_count' => 4,
                        'flat_pattern' => '/^\S+\s\S+\s\S+\s\S+$/',
                    ],
                ], 
                $defaultFaker, // faker 
                // values
                [],  
                'wording', // name 
                'words', // method 
                // methodArguments 
                [4], 
                // variables
                [
                ],  
                false, // optional 
                false, // unique 
                false, // force 
                false, // useIncrement 
                false, // resetIncrement 
            ],
            // data set #5 rgb
            [
                // expected rules
                [
                    'rgb' => [
                        'raw_type' => 'array',
                        'raw_count' => 3,
                        'flat_pattern' => '/^\d+,\d+,\d+$/',
                    ],
                ], 
                $defaultFaker, // faker 
                // values
                [],  
                'rgb', // name 
                'rgbColorAsArray', // method 
                // methodArguments 
                [], 
                // variables
                [
                ],  
                false, // optional 
                false, // unique 
                false, // force 
                false, // useIncrement 
                false, // resetIncrement 
            ],
            // data set #6 creditCardDetails
            [
                // expected rules
                [
                    'credit_card_details' => [
                        'raw_type' => 'array',
                        'raw_count' => 4,
                        'flat_pattern' => '/^.+,.+,.+,.+$/',
                    ],
                ], 
                $defaultFaker, // faker 
                // values
                [],  
                'credit_card_details', // name 
                'creditCardDetails', // method 
                // methodArguments 
                [], 
                // variables
                [
                ],  
                false, // optional 
                false, // unique 
                false, // force 
                false, // useIncrement 
                false, // resetIncrement 
            ],
            // data set #7 sentences
            [
                // expected rules
                [
                    'sentences' => [
                        'raw_type' => 'array',
                        'raw_count' => 4,
                        'flat_pattern' => '/^.+\n.+\n.+\n.+$/',
                    ],
                ], 
                $defaultFaker, // faker 
                // values
                [],  
                'sentences', // name 
                'sentences', // method 
                // methodArguments 
                [4], 
                // variables
                [
                ],  
                false, // optional 
                false, // unique 
                false, // force 
                false, // useIncrement 
                false, // resetIncrement 
            ],
            // data set #8 sentences
            [
                // expected rules
                [
                    'paragraphs' => [
                        'raw_type' => 'array',
                        'raw_count' => 4,
                        'flat_pattern' => '/^.+\n.+\n.+\n.+$/',
                    ],
                ], 
                $defaultFaker, // faker 
                // values
                [],  
                'paragraphs', // name 
                'paragraphs', // method 
                // methodArguments 
                [4], 
                // variables
                [
                ],  
                false, // optional 
                false, // unique 
                false, // force 
                false, // useIncrement 
                false, // resetIncrement 
            ],
            // data set #9 datetime
            [
                // expected rules
                [
                    'date' => [
                        'raw_class' => '\DateTime',
                        'flat_pattern' => '/^\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}:\d{2}$/',
                    ],
                ], 
                $defaultFaker, // faker 
                // values
                [],  
                'date', // name 
                'dateTime', // method 
                // methodArguments 
                ['d/m/Y H:i:s'], 
                // variables
                [
                ],  
                false, // optional 
                false, // unique 
                false, // force 
                false, // useIncrement 
                false, // resetIncrement 
            ],
            // data set #10 number with increment
            [
                // expected rules
                [
                    'number' => [
                        'raw_type' => 'int',
                        'flat_pattern' => '/^6$/',
                    ],
                ], 
                $fakerFixDigit, // faker 
                // values
                [],  
                'number', // name 
                'fixDigit', // method 
                // methodArguments 
                [5], 
                // variables
                [
                ],  
                false, // optional 
                false, // unique 
                false, // force 
                true, // useIncrement 
                true, // resetIncrement 
            ],
            // data set #11 optional 0 %
            [
                // expected rules
                [
                    'number' => [
                        'raw_type' => 'null',
                        'flat_pattern' => '/^$/',
                    ],
                ], 
                $fakerOptional, // faker 
                // values
                [],  
                'number', // name 
                'count', // method 
                // methodArguments 
                [], 
                // variables
                [
                ],  
                0, // optional 
                false, // unique 
                false, // force 
                false, // useIncrement 
                false, // resetIncrement 
            ],
            // data set #12 optional 100 %
            [
                // expected rules
                [
                    'number' => [
                        'raw_type' => 'int',
                        'flat_pattern' => '/1/',
                    ],
                ], 
                $fakerOptional, // faker 
                // values
                [],  
                'number', // name 
                'count', // method 
                // methodArguments 
                [], 
                // variables
                [
                ],  
                1, // optional 
                false, // unique 
                false, // force 
                false, // useIncrement 
                false, // resetIncrement 
            ],
        ];
    }
    
    public function testGenerateValueOptional() 
    {
        $faker = \Faker\Factory::create('en_US');
        
        $values = [];
        $variables = [];
                
        $name = 'foo';
        
        $this->variable->setName($name);
        $this->variable->setMethod('randomDigit');
        $this->variable->setMethodArguments([]);
        $this->variable->setOptional(0.5);
        $this->variable->setUnique(false);
        
        $rawValues = [];
        
        for ($i=0; $i < 20; $i++) {
            $this->variable->generateValue($faker, $values, $variables);
            $this->assertArrayHasKey($name, $values);
            $this->assertArrayHasKey('raw', $values[$name]);
            $this->assertArrayHasKey('flat', $values[$name]);
            
            $rawValues[] = $values[$name]['raw'];
            unset($values[$name]);
        }
        sort($rawValues);
        
        $this->assertContains(null, $rawValues);
    }
    
    public function testGenerateValueUnique() 
    {
        $faker = \Faker\Factory::create('en_US');
        
        $values = [];
        $variables = [];
                
        $name = 'foo';
        
        $this->variable->setName($name);
        $this->variable->setMethod('randomDigit');
        $this->variable->setMethodArguments([]);
        $this->variable->setOptional(null);
        $this->variable->setUnique(true);
        
        $rawValues = [];
        
        for ($i=0; $i < 10; $i++) {
            $this->variable->generateValue($faker, $values, $variables);
            $this->assertArrayHasKey($name, $values);
            $this->assertArrayHasKey('raw', $values[$name]);
            $this->assertArrayHasKey('flat', $values[$name]);
            
            $rawValues[] = $values[$name]['raw'];
            unset($values[$name]);
        }
        sort($rawValues);
        
        $this->assertEquals([0, 1, 2, 3, 4, 5, 6, 7, 8, 9], $rawValues);
    }
}

class FixDigitProvider 
{
    public function fixDigit($num = 0)
    {
        return is_numeric($num) ? $num : 0;
    }
}
