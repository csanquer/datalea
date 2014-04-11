<?php

namespace CSanquer\FakeryGenerator\Test\Dump;

use CSanquer\ColibriCsv\Dialect;
use \CSanquer\FakeryGenerator\Dump\ExcelDumper;
use CSanquer\FakeryGenerator\Model\Column;
use CSanquer\FakeryGenerator\Model\Config;
use CSanquer\FakeryGenerator\Model\Variable;

/**
 * ExcelDumperTest
 *
 * @author Charles Sanquer <charles.sanquer.ext@francetv.fr>
 */
class ExcelDumperTest extends DumperTestCase
{
    /**
     * @dataProvider providerDump
     */
    public function testDump(Config $config, $withDate, $generatedValues, $expectedFile, $expected)
    {
        $dumper = new ExcelDumper();
        $dumper->initialize($config, self::$cacheDir, $withDate);

        foreach ($generatedValues as $row) {
            $dumper->dumpRow($row);
        }
        $filename = $dumper->finalize();
        
        $this->assertFileExists($filename);
        $ext = 'xlsx';
        $this->assertRegExp('/'.basename($expectedFile,'.'.$ext).'_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}.'.$ext.'/', basename($filename));
        
        $reader = new \PHPExcel_Reader_Excel2007();
        $excel = $reader->load(self::$fixtures.'/'.$expectedFile);
        
        $data = $excel->getActiveSheet()->toArray();
        $this->assertEquals($expected, $data);
    }

    public function providerDump()
    {
        $config1 = new Config();
        $config1
            ->setClassName('Entity\\User')
            ->setFakeNumber(3)
            ->setFormats(['excel'])
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
                true,
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
                'ExcelDumper/expected/Entity_User.xlsx',
                [
                    [
                        'person-name-firstname',
                        'person-name-lastname',
                        'person-email',
                        'birthday',
                    ],
                    [
                        'Adolph',
                        'McCullough',
                        'adolph.mccullough@yahoo.com',
                        '1994-05-30',
                    ],
                    [
                        'Sebastian',
                        'Harvey',
                        'sebastian.harvey@yahoo.com',
                        '1927-10-02',
                    ],
                    [
                        'Norris',
                        'Douglas',
                        'norris.douglas@hotmail.com',
                        '1994-08-12',
                    ],
                ],
            ]
        ];
    }
}
