<?php

namespace CSanquer\FakeryGenerator\Test\Dump;

use CSanquer\ColibriCsv\Dialect;
use CSanquer\FakeryGenerator\Dump\PythonDumper;
use CSanquer\FakeryGenerator\Model\Column;
use CSanquer\FakeryGenerator\Model\Config;
use CSanquer\FakeryGenerator\Model\Variable;

/**
 * PythonDumperTest
 *
 * @author Charles Sanquer <charles.sanquer.ext@francetv.fr>
 */
class PythonDumperTest extends DumperTestCase
{
    /**
     * @dataProvider providerDump
     */
    public function testDump(Config $config, $generatedValues, $expectedFile)
    {
        $dumper = new PythonDumper();
        $dumper->initialize($config, self::$cacheDir);

        foreach ($generatedValues as $row) {
            $dumper->dumpRow($row);
        }
        
        $filename = $dumper->finalize();
        $this->assertFileExists($filename);
        $this->assertEquals(basename($expectedFile), basename($filename));
        $this->assertFileEquals(self::$fixtures.'/'.$expectedFile, $filename);
    }

    public function providerDump()
    {
        $config1 = new Config();
        $config1
            ->setClassName('Entity\\User')
            ->setFakeNumber(3)
            ->setFormats(['python'])
            ->setLocale('en_US')
            ->setSeed(17846134)
            ->setCsvDialect(Dialect::createUnixDialect())
            ->setVariables([
                new Variable('firstname', 'firstName'),
                new Variable('lastname', 'lastName'),
                new Variable('emailDomain', 'freeEmailDomain'),
                new Variable('birthday', 'dateTimeThisCentury', ['Y-m-d']),
            ])
            ->setColumns([
                new Column('person', null, null, [
                    new Column('name', null, null, [
                        new Column('firstname', '%firstname%', 'capitalize'),
                        new Column('lastname', '%lastname%', 'capitalize'),
                            ]),
                    new Column('email', '%firstname%.%lastname%@%emailDomain%', 'lowercase'),
                        ]),
                new Column('birthday', '%birthday%'),
            ]);

        return [
            #data set #0
            [
                $config1,
                [
                    [
                        'person' => [
                            'name' => [
                                'firstname' => 'Adolph',
                                'lastname' => 'McCullough',
                            ],
                            'email' => 'adolph.mccullough@yahoo.com',
                        ],
                        'birthday' => '1994-05-30',
                    ],
                    [
                        'person' => [
                            'name' => [
                                'firstname' => 'Sebastian',
                                'lastname' => 'Harvey',
                            ],
                            'email' => 'sebastian.harvey@yahoo.com',
                        ],
                        'birthday' => '1927-10-02',
                    ],
                    [
                        'person' => [
                            'name' => [
                                'firstname' => 'Norris',
                                'lastname' => 'Douglas',
                            ],
                            'email' => 'norris.douglas@hotmail.com',
                        ],
                        'birthday' => '1994-08-12',
                    ],
                ],
                'PythonDumper/expected/Entity_User.py'
            ]
        ];
    }
}
