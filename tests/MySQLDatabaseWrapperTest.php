<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use DavidFricker\DataAbstracter\Adapter\MySQLDatabaseWrapper;

/**
 * @covers MySQLDatabaseWrapper
 */
final class MySQLDatabaseWrapperTest extends TestCase
{
    private static $db_connection;

    public static function setupBeforeClass () {
        self::$db_connection = new MySQLDatabaseWrapper('mysql:host=127.0.0.1;port=3306;dbname=myDatabase','root','');
    }

    /**
     * @expectedException PDOException
     */
    public function testEmptyConstructorException()
    {
        new MySQLDatabaseWrapper('','','','');
    }

    /**
     * @expectedException PDOException
     */
    public function testEmptyIncorrectConstructorException()
    {
        new MySQLDatabaseWrapper('mysql:host=127.0.0.1;port=3306;dbname=myDatabase','','','');
    }

    /*
        Test Insert
     */
    public function testInsertIncorrectTableName()
    {
        $result = self::$db_connection->insert('foo', []);
        $this->assertEquals(
            $result,
            false
        );
    }

    public function testInsertCorrectTableNameNoInsertArray()
    {
        $result = self::$db_connection->insert('phpunit', 'not-an-array');
        $this->assertEquals(
            $result,
            false
        );
    }

    public function testInsertCorrectTableNameCorrectInsertArray()
    {
        $result = self::$db_connection->insert('phpunit', ['FirstName' => 'David']);
        $this->assertEquals(
            $result,
            1
        );

        $result = self::$db_connection->fetch('phpunit', ['FirstName'], ['FirstName' => 'David']);
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
        $result = self::$db_connection->delete('foo', []);
        $this->assertEquals(
            $result,
            false
        );
    }

    public function testDeleteCorrectTableName()
    {
        $result = self::$db_connection->insert('phpunit', ['FirstName' => 'David']);
        $this->assertEquals(
            $result,
            1
        );

        $result = self::$db_connection->delete('phpunit');
        $this->assertEquals(
            1,
            $result
        );

        $result = self::$db_connection->fetch('phpunit', ['FirstName'], ['FirstName' => 'David']);
        $this->assertCount(
            0,
            $result
        );
    }

    public function testDeleteCorrectTableNameWithLimit()
    {
        for ($i = 0; $i < 10; $i++) {
            $result = self::$db_connection->insert('phpunit', ['FirstName' => 'David']);
            $this->assertEquals(
                $result,
                1
            );
        }

        $result = self::$db_connection->delete('phpunit',[], 6);
        $this->assertEquals(
            6,
            $result
        );

        $result = self::$db_connection->fetch('phpunit', ['FirstName']);
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
            
            $result = self::$db_connection->insert('phpunit', ['FirstName' => $Fname]);
            $this->assertEquals(
                1,
                $result
            );
        }

        $result = self::$db_connection->delete('phpunit', ['FirstName' => $Fname]);
        $this->assertEquals(
            5,
            $result
        );

        $result = self::$db_connection->fetch('phpunit', ['FirstName']);
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
        $result = self::$db_connection->update('foo', ['FirstName' => 'bar']);
        $this->assertEquals(
            false,
            $result
        );
    }

    public function testUpdateIncorrectColumnName()
    {
        $result = self::$db_connection->update('phpunit', ['BarName' => 'first']);
        $this->assertEquals(
            false,
            $result
        );
    }

    public function testUpdateIncorrectWhereClause()
    {
        $result = self::$db_connection->update('phpunit', ['FirstName' => 'David'], ['BarName' => 'first']);
        $this->assertEquals(
            false,
            $result
        );
    }

    public function testUpdateCorrect()
    {
        $result = self::$db_connection->insert('phpunit', ['FirstName' => 'David']);
        $this->assertEquals(
            1,
            $result
        );

        $result = self::$db_connection->update('phpunit', ['FirstName' => 'Updated']);
        $this->assertEquals(
            1,
            $result
        );

        $result = self::$db_connection->fetch('phpunit', ['FirstName']);
        $this->assertCount(
            1,
            $result
        );

        $this->assertEquals(
            'Updated',
            $result[0]['FirstName']
        );
    }

    /*
        Test fetch
     */
    public function testFecthCorrectWithLimit()
    {
        for ($i = 0; $i < 10; $i++) {
            $result = self::$db_connection->insert('phpunit', ['FirstName' => 'David']);
            $this->assertEquals(
                $result,
                1
            );
        }

        $result = self::$db_connection->fetch('phpunit', ['FirstName'], [], 4);
        $this->assertCount(
            4,
            $result
        );
    }
    
    /*
        Test getLastInsertID
     */
    public function testGetLastInsertID()
    {
        self::$db_connection->insert('phpunit', ['FirstName' => 'David']);
        $result = self::$db_connection->getLastInsertID();
        
        $this->assertEquals(
            1,
            $result
            
        );
    }

    public function testRowCount()
    {
        $result = self::$db_connection->insert('phpunit', ['FirstName' => 'David']);
        
        $this->assertEquals(
            1,
            $result
        );
    }

    protected function tearDown()
    {
        (self::$db_connection)->run('TRUNCATE TABLE `phpunit`');
    }
}
