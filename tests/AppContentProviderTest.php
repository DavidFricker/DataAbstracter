<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use DavidFricker\DataAbstracter\Example\Provider\AppContentProvider;

/**
 * @covers MySQLDatabaseWrapper
 */
final class AppContentProviderTest extends TestCase
{
    private static $AppContentProvider;

    public static function setupBeforeClass () {
        self::$AppContentProvider = AppContentProvider::init();
    }
    
    protected function tearDown()
    {
        self::$AppContentProvider->run('TRUNCATE TABLE `dataprovider`');
    }

    /*
        Test underlying table contract constraints by passing an unknown table name
     */
    public function testInsertIncorrectTableName()
    {
        $this->expectException(InvalidArgumentException::class);
        self::$AppContentProvider->insert('foo', []);
    }

    public function testInsertCorrectTableNameIncorrectCol()
    {
        $this->expectException(InvalidArgumentException::class);
        self::$AppContentProvider->insert('dataprovider', ['notacol' => 'hashcat']);
    }

    public function testInsertCorrectTableNameCorrectColIncorrectVal()
    {
        $this->expectException(InvalidArgumentException::class);
        self::$AppContentProvider->insert('dataprovider', ['username' => 456]);
    }

    public function testInsertCorrectTableNameCorrectColCorrectVal()
    {
        $result = self::$AppContentProvider->insert('dataprovider', ['username' => 'David']); 
        $this->assertEquals(
            1,
            $result
        );

        $result = self::$AppContentProvider->fetch('dataprovider', ['username']);
        $this->assertCount(
            1,
            $result
        );

        $this->assertArrayHasKey(0, $result);
    }

    public function testUpdateCorrect()
    {
        $result = self::$AppContentProvider->insert('dataprovider', ['username' => 'David']);
        $this->assertEquals(
            1,
            $result
        );

        $result = self::$AppContentProvider->update('dataprovider', ['username' => 'Updated']);
        $this->assertEquals(
            1,
            $result
        );

        $result = self::$AppContentProvider->fetch('dataprovider', ['username']);
        $this->assertCount(
            1,
            $result
        );

        $this->assertEquals(
            'Updated',
            $result[0]['username']
        );
    }

    /*
        Test exists
     */
    public function testExistsIncorrectTable()
    {
        $this->expectException(InvalidArgumentException::class);
        self::$AppContentProvider->exists('foo', 'username', 'David');
    }

    public function testExistsCorrectTableIncorrectData()
    {
        $this->expectException(InvalidArgumentException::class);
        self::$AppContentProvider->exists('foo', 'username', 345);
    }

    public function testExistsCorrectTableCorrectDataNoRecord()
    {
        $result = self::$AppContentProvider->exists('dataprovider', 'username', 'foo');
        $this->assertEquals(
            false,
            $result
        );
    }

    public function testExistsCorrectTableCorrectDataPresentRecord()
    {
        $result = self::$AppContentProvider->insert('dataprovider', ['username' => 'David']);
        $this->assertEquals(
            1,
            $result
        );

        $result = self::$AppContentProvider->exists('dataprovider', 'username', 'David');
        $this->assertEquals(
            true,
            $result
        );
    }

    /*
        Test fetch
     */
    public function testFecthCorrectWithLimit()
    {
        for ($i = 0; $i < 10; $i++) {
            $result = self::$AppContentProvider->insert('dataprovider', ['username' => 'David']);
            $this->assertEquals(
                $result,
                1
            );
        }

        $result = self::$AppContentProvider->fetch('dataprovider', ['username'], [], 4);
        $this->assertCount(
            4,
            $result
        );
    }

    public function testInsertCorrectTableNameCorrectInsertArray()
    {
        $result = self::$AppContentProvider->insert('dataprovider', ['username' => 'David']);
        $this->assertEquals(
            $result,
            1
        );

        $result = self::$AppContentProvider->fetch('dataprovider', ['username'], ['username' => 'David']);
         $this->assertCount(
            1,
            $result
        );
    }

    /*
        Test delete
     */
    public function testDeleteIncorrectTableName()
    {
        $this->expectException(InvalidArgumentException::class);
        $result = self::$AppContentProvider->delete('foo', []);
        $this->assertEquals(
            $result,
            false
        );
    }

    public function testDeleteCorrectTableNameWithLimit()
    {
        for ($i = 0; $i < 10; $i++) {
            $result = self::$AppContentProvider->insert('dataprovider', ['username' => 'David']);
            $this->assertEquals(
                $result,
                1
            );
        }

        $result = self::$AppContentProvider->delete('dataprovider',[], 6);
        $this->assertEquals(
            6,
            $result
        );

        $result = self::$AppContentProvider->fetch('dataprovider', ['username']);
        $this->assertCount(
            4,
            $result
        );
    }

    public function testDeleteCorrectTableNameWithWhere()
    {
        for ($i = 0; $i < 10; $i++) {
            if ($i % 2 == 0) {
                $Fname = 'David';
            } else {
                $Fname = 'NotDavid';
            }
            
            $result = self::$AppContentProvider->insert('dataprovider', ['username' => $Fname]);
            $this->assertEquals(
                1,
                $result
            );
        }

        $result = self::$AppContentProvider->delete('dataprovider', ['username' => $Fname]);
        $this->assertEquals(
            5,
            $result
        );

        $result = self::$AppContentProvider->fetch('dataprovider', ['username']);
        $this->assertCount(
            5,
            $result
        );
    }

    /*
        Test update
     */
    public function testUpdateIncorrectTableName()
    {
        $this->expectException(InvalidArgumentException::class);
        $result = self::$AppContentProvider->update('foo', ['username' => 'bar']);
        $this->assertEquals(
            false,
            $result
        );
    }

    public function testUpdateIncorrectColumnName()
    {
        $this->expectException(InvalidArgumentException::class);
        $result = self::$AppContentProvider->update('dataprovider', ['BarName' => 'first']);
        $this->assertEquals(
            false,
            $result
        );
    }

    public function testUpdateIncorrectWhereClause()
    {
        $result = self::$AppContentProvider->insert('dataprovider', ['username' => 'David']);
        $this->assertEquals(
            1,
            $result
        );

        $result = self::$AppContentProvider->update('dataprovider', ['username' => 'New name'], ['username' => 'David']);
        $this->assertEquals(
            1,
            $result
        );
    }
}
