<?php

namespace CSanquer\FakeryGenerator\Test\Dump;

use Symfony\Component\Filesystem\Filesystem;

/**
 * DumperTestCase
 *
 * @author Charles Sanquer <charles.sanquer.ext@francetv.fr>
 */
abstract class DumperTestCase extends \PHPUnit_Framework_TestCase
{
    protected static $fixtures;
    
    protected static $cacheDir;

    public static function setUpBeforeClass()
    {
        self::$fixtures = __DIR__.'/fixtures/';
        self::$cacheDir = __DIR__.'/tmp';
    }

    protected function setUp()
    {
        $fs = new Filesystem();
        if ($fs->exists(self::$cacheDir)) {
            $fs->remove(self::$cacheDir);
        }
//        $fs->mkdir(self::$cacheDir);
    }
}
