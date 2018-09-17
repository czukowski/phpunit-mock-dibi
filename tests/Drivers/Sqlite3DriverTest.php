<?php
namespace Cz\PHPUnit\MockDibi\Drivers;

use DateTime,
    Dibi\DateTime as DibiDateTime,
    Dibi\NotImplementedException,
    Dibi\NotSupportedException;

/**
 * Sqlite3DriverTest
 * 
 * @author   czukowski
 * @license  MIT License
 */
class Sqlite3DriverTest extends Testcase
{
    /**
     * @dataProvider  provideBegin
     */
    public function testBegin($savepoint, $expected)
    {
        $object = $this->createObject();
        $object->begin($savepoint);
        $this->assertExecutedQuery($object, $expected);
    }

    public function provideBegin()
    {
        return [
            [NULL, 'BEGIN'],
            ['s1', 'SAVEPOINT s1'],
        ];
    }

    /**
     * @dataProvider  provideCommit
     */
    public function testCommit($savepoint, $expected)
    {
        $object = $this->createObject();
        $object->commit($savepoint);
        $this->assertExecutedQuery($object, $expected);
    }

    public function provideCommit()
    {
        return [
            [NULL, 'COMMIT'],
            ['s1', 'RELEASE SAVEPOINT s1'],
        ];
    }

    /**
     * @dataProvider  provideRollback
     */
    public function testRollback($savepoint, $expected)
    {
        $object = $this->createObject();
        $object->rollback($savepoint);
        $this->assertExecutedQuery($object, $expected);
    }

    public function provideRollback()
    {
        return [
            [NULL, 'ROLLBACK'],
            ['s1', 'ROLLBACK TO SAVEPOINT s1'],
        ];
    }

    /**
     * Also testing `fmtDate` property.
     * 
     * @dataProvider  provideEscapeDate
     */
    public function testEscapeDate($format, $value, $expected)
    {
        $object = $this->createObject($format);
        $actual = $object->escapeDate($value);
        $this->assertSame($expected, $actual);
    }

    public function provideEscapeDate()
    {
        return [
            ['Y-m-d', 1525932234, '2018-05-10'],
            ['Y-m-d', '2018-05-10 08:18:53', '2018-05-10'],
            ['Y-m-d', new DateTime('2018-05-10 00:00:00'), '2018-05-10'],
            ['Y-m-d', new DibiDateTime('2018-05-10 23:59:59'), '2018-05-10'],
        ];
    }

    /**
     * Also testing `fmtDateTime` property.
     * 
     * @dataProvider  provideEscapeDateTime
     */
    public function testEscapeDateTime($format, $value, $expected)
    {
        $object = $this->createObject('U', $format);
        $actual = $object->escapeDateTime($value);
        $this->assertSame($expected, $actual);
    }

    public function provideEscapeDateTime()
    {
        return [
            ['Y-m-d H:i:s', 1525932234, '2018-05-10 06:03:54'],
            ['Y-m-d H:i:s', '2018-05-10 08:18:53', '2018-05-10 08:18:53'],
            ['Y-m-d H:i:s', new DateTime('2018-05-10 00:00:00'), '2018-05-10 00:00:00'],
            ['Y-m-d H:i:s', new DibiDateTime('2018-05-10 23:59:59'), '2018-05-10 23:59:59'],
        ];
    }

    /**
     * @dataProvider  provideEscapeLike
     */
    public function testEscapeLike($value, $pos, $expected)
    {
        $object = $this->createObject();
        $actual = $object->escapeLike($value, $pos);
        $this->assertSame($expected, $actual);
    }

    public function provideEscapeLike()
    {
        return [
            ["va_'\\%e", -1, "'%va\_''\\\\\%e' ESCAPE '\'"],
            ["va_'\\%e", 0, "'%va\_''\\\\\%e%' ESCAPE '\'"],
            ["va_'\\%e", 1, "'va\_''\\\\\%e%' ESCAPE '\'"],
        ];
    }

    /**
     * @dataProvider  provideEscapeText
     */
    public function testEscapeText($value, $expected)
    {
        $object = $this->createObject();
        $actual = $object->escapeText($value);
        $this->assertSame($expected, $actual);
    }

    public function provideEscapeText()
    {
        return [
            ["value", "'value'"],
            ["va'ue", "'va''ue'"],
        ];
    }

    /**
     * @dataProvider  provideUnsupportedMethods
     */
    public function testUnsupportedMethods($method, $arguments)
    {
        $object = $this->createObject();
        $this->expectException(NotSupportedException::class);
        call_user_func_array([$object, $method], $arguments);
    }

    public function provideUnsupportedMethods()
    {
        return [
            ['getRowCount', []],
            ['seek', [0]],
        ];
    }

    /**
     * @dataProvider  provideUnimplementedMethods
     */
    public function testUnimplementedMethods($method, $arguments)
    {
        $object = $this->createObject();
        $this->expectException(NotImplementedException::class);
        call_user_func_array([$object, $method], $arguments);
    }

    public function provideUnimplementedMethods()
    {
        return [
            ['registerFunction', ['', function () {}]],
            ['registerAggregateFunction', ['', function () {}, function () {}]],
        ];
    }

    /**
     * @return  Sqlite3Driver
     */
    private function createObject(string $formatDate = 'U', string $formatDateTime = 'U')
    {
        return $this->getDriversFactory()
            ->createSqlite3Driver($formatDate, $formatDateTime);
    }
}
