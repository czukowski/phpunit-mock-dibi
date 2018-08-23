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
    public function testConnect($config, $expected)
    {
        $object = $this->createObject();
        $object->connect($config);

        $version = $this->getReflectionProperty(OriginalSqlsrvDriver::class, 'version');
        $this->assertSame($expected, $version->getValue($object));
        $connection = $this->getReflectionProperty(OriginalSqlsrvDriver::class, 'connection');
        $this->assertNull($connection->getValue($object));
    }

    public function provideConnect()
    {
        return [
            [[], '11'],
            [['version' => '12'], '12'],
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
            [NULL, NULL, FALSE],
            [0, NULL, FALSE],
            [1, NULL, 1],
            [NULL, 's1', FALSE],
            [0, 's1', FALSE],
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
        $config = [];
        $object = $this->createObject();
        $object->connect($config);
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
     * @return  SqlsrvDriver
     */
    private function createObject()
    {
        return $this->getDriversFactory()
            ->createSqlsrvDriver();
    }
}
