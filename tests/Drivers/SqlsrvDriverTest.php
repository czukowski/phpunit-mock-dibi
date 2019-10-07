<?php
namespace Cz\PHPUnit\MockDibi\Drivers;

use Dibi\Drivers\SqlsrvDriver as OriginalSqlsrvDriver;

/**
 * SqlsrvDriverTest
 * 
 * @author   czukowski
 * @license  MIT License
 */
class SqlsrvDriverTest extends Testcase
{
    /**
     * @dataProvider  provideConnect
     */
    public function testConnect($arguments, $expected)
    {
        $object = $this->createObject($arguments);

        $versionProperty = $this->getReflectionProperty(OriginalSqlsrvDriver::class, 'version');
        $this->assertSame($expected, $versionProperty->getValue($object));
        $connection = $this->getReflectionProperty(OriginalSqlsrvDriver::class, 'connection');
        $this->assertNull($connection->getValue($object));
    }

    public function provideConnect()
    {
        return [
            [[], '11'],
            [['12'], '12'],
        ];
    }

    /**
     * @dataProvider  provideGetInsertId
     */
    public function testGetInsertId($lastInsertId, $sequence, $expected)
    {
        $object = $this->createObject();
        $object->setInsertId($lastInsertId);
        $actual = $object->getInsertId($sequence);
        $this->assertSame($expected, $actual);
        $queries = $object->getExecutedQueries();
        $this->assertCount(1, $queries);
        $this->assertSame('SELECT SCOPE_IDENTITY()', reset($queries));
    }

    public function provideGetInsertId()
    {
        return [
            [NULL, NULL, NULL],
            [0, NULL, NULL],
            [1, NULL, 1],
            [NULL, 's1', NULL],
            [0, 's1', NULL],
            [1, 's1', 1],
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
     * @dataProvider  provideApplyLimit
     */
    public function testApplyLimit($sql, $limit, $offset, $expected)
    {
        // Test to make sure version related NotSupportedException is not triggered.
        $object = $this->createObject([]);
        $object->applyLimit($sql, $limit, $offset);
        $this->assertSame($expected, $sql);
    }

    public function provideApplyLimit()
    {
        return [
            [
                'SELECT * FROM t1',
                10,
                10,
                'SELECT * FROM t1 OFFSET 10 ROWS FETCH NEXT 10 ROWS ONLY',
            ],
        ];
    }

    /**
     * @param   array  $arguments
     * @return  SqlsrvDriver
     */
    private function createObject(array $arguments = [])
    {
        return $this->getDriversFactory()
            ->createSqlsrvDriver(...$arguments);
    }
}
