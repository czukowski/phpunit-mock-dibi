<?php
namespace Cz\PHPUnit\MockDibi\Drivers;

use DateTime,
    DateTimeImmutable,
    DateTimeZone,
    Dibi\DateTime as DibiDateTime,
    Dibi\NotImplementedException;

/**
 * SqliteDriverTest
 * 
 * @author   czukowski
 * @license  MIT License
 */
class SqliteDriverTest extends Testcase
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
            ['Y-m-d', new DibiDateTime(1525932234, new DateTimeZone('UTC')), '2018-05-10'],
            ['Y-m-d', new DateTime('2018-05-10 08:18:53'), '2018-05-10'],
            ['Y-m-d', new DateTimeImmutable('2018-05-10 00:00:00'), '2018-05-10'],
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
        $previousTimeZone = ini_set('date.timezone', 'UTC');
        $object = $this->createObject('U', $format);
        $actual = $object->escapeDateTime($value);
        $this->assertSame($expected, $actual);
        ini_set('date.timezone', $previousTimeZone);
    }

    public function provideEscapeDateTime()
    {
        return [
            ['Y-m-d H:i:s', new DibiDateTime(1525932234, new DateTimeZone('UTC')), '2018-05-10 06:03:54'],
            ['Y-m-d H:i:s', new DateTimeImmutable('2018-05-10 08:18:53'), '2018-05-10 08:18:53'],
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
     * @dataProvider  provideReturnNullMethods
     */
    public function testReturnNullMethods($methodName, $arguments)
    {
        $object = $this->createObject();
        $actual = call_user_func_array([$object, $methodName], $arguments);
        $this->assertNull($actual);
    }

    public function provideReturnNullMethods()
    {
        return [
            ['getResource', []],
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
     * @return  SqliteDriver
     */
    private function createObject(string $formatDate = 'U', string $formatDateTime = 'U')
    {
        return $this->getDriversFactory()
            ->createSqliteDriver($formatDate, $formatDateTime);
    }
}
