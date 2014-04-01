<?php

namespace CSanquer\FakeryGenerator\Test\Dump;

use CSanquer\ColibriCsv\Dialect;
use CSanquer\FakeryGenerator\Dump\XMLDumper;
use CSanquer\FakeryGenerator\Model\Column;
use CSanquer\FakeryGenerator\Model\Config;
use CSanquer\FakeryGenerator\Model\Variable;
use Faker\Factory;

/**
 * XMLDumperTest
 *
 * @author Charles Sanquer <charles.sanquer.ext@francetv.fr>
 */
class XMLDumperTest extends DumperTestCase
{
    /**
     * @dataProvider providerDump
     */
    public function testDump(Config $config, $generatedValues, $expectedFile)
    {
//        $faker = Factory::create($config->getLocale());

        $dumper = new XMLDumper();
        $dumper->initialize($config, self::$cacheDir);

//        $result = [];
        foreach ($generatedValues as $row) {
            $dumper->dumpRow($row);
        }
        
//        for ($i = 0; $i < count($generatedValues); $i++) {
//            $values = [];
//            $config->generateVariableValues($faker, $values);
//            $row = $config->generateColumnValues($values);
//            $result[] = $row;
//            $dumper->dumpRow($row);
//        }

//        var_export($result);

        $filename = $dumper->finalize();
//        var_dump($filename);
//        var_dump(file_get_contents($filename));
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
            ->setFormats(['xml'])
            ->setLocale('en_US')
            ->setSeed(17846134)
            ->setCsvDialect(Dialect::createUnixDialect())
            ->setVariables([
                new Variable('firstname', 'firstName'),
                new Variable('lastname', 'lastName'),
                new Variable('emailDomain', 'freeEmailDomain'),
                new Variable('birthday', 'dateTimeThisCentury', ['Y-m-d']),
                new Variable('site', 'url'),
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
                new Column('personal_site', '%site%?foo=<&bar=>'),
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
                        'personal_site' => 'http://www.rohan.com/?foo=<&bar=>',
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
                        'personal_site' => 'http://glover.com/?foo=<&bar=>',
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
                        'personal_site' => 'http://brown.com/?foo=<&bar=>',
                    ],
                ],
                'XMLDumper/expected/Entity_User.xml'
            ]
        ];
    }
}
