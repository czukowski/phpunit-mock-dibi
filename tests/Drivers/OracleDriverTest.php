<?php
namespace Cz\PHPUnit\MockDibi\Drivers;

/**
 * OracleDriverTest
 * 
 * @author   czukowski
 * @license  MIT License
 */
class OracleDriverTest extends Testcase
{
    /**
     * @dataProvider  provideGetInsertId
     */
    public function testGetInsertId($lastInsertId, $sequence, $expectedInsertId, $expectedQuery)
    {
        $object = $this->createObject();
        $object->setInsertId($lastInsertId);
        $actual = $object->getInsertId($sequence);
        $this->assertSame($expectedInsertId, $actual);
        $this->assertExecutedQuery($object, $expectedQuery);
    }

    public function provideGetInsertId()
    {
        return [
            [NULL, 's1', NULL, "SELECT s1.CURRVAL AS ID FROM DUAL"],
            [0, 's1', NULL, "SELECT s1.CURRVAL AS ID FROM DUAL"],
            [1, 's1', 1, "SELECT s1.CURRVAL AS ID FROM DUAL"],
        ];
    }

    /**
     * @dataProvider  provideEmptyMethods
     */
    public function testEmptyMethods($methodName)
    {
        $object = $this->createObject();
        $actual = call_user_func([$object, $methodName]);
        $this->assertNull($actual);
    }

    public function provideEmptyMethods()
    {
        return [
            ['begin'],
            ['commit'],
            ['rollback'],
        ];
    }

    /**
     * Also testing `nativeDate` property.
     * 
     * @dataProvider  provideEscapeDate
     */
    public function testEscapeDate($nativeDate, $value, $expected)
    {
        $object = $this->createObject($nativeDate);
        $actual = $object->escapeDate($value);
        $this->assertSame($expected, $actual);
    }

    public function provideEscapeDate()
    {
        return [
            [FALSE, 1537163164, '1537163164'],
            [TRUE, 1537163164, "to_date('2018-09-17', 'YYYY-mm-dd')"],
        ];
    }

    /**
     * @param   boolean  $nativeDate
     * @return  OdbcDriver
     */
    private function createObject(bool $nativeDate = TRUE)
    {
        return $this->getDriversFactory()
            ->createOracleDriver($nativeDate);
    }
}
