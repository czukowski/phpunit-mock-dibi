<?php
namespace Cz\PHPUnit\MockDibi\Drivers;

use Cz\PHPUnit\MockDibi\Doubles\MockDouble,
    Cz\PHPUnit\MockDB\Invocation\QueryInvocation,
    Cz\PHPUnit\MockDB\Invocation\QueryInvocationFactoryInterface,
    Dibi\ResultDriver,
    ReflectionMethod;

/**
 * MockQueryDriverTraitTest
 * 
 * @author   czukowski
 * @license  MIT License
 */
class MockQueryDriverTraitTest extends Testcase
{
    /**
     * @dataProvider  provideQuery
     */
    public function testQuery($sql, $affectedRows, $lastInsertId, $resultSet, $expected)
    {
        $invocation = $this->createQueryInvocation($sql, $affectedRows, $lastInsertId, $resultSet);
        $mock = $this->createMockDouble();
        $object = $this->createObject([
            'createInvocation', 'createResultDriver', 'setAffectedRows',
            'setInsertId', 'setResultSet',
        ]);
        $this->setupQuery($object, $mock, $invocation, $affectedRows, $lastInsertId, $resultSet, $expected);

        $actual = $object->query($sql);
        $this->assertSame($expected, $actual);
        $this->assertSame($invocation, $mock->invoked);
        $this->assertEquals([$sql], $object->getExecutedQueries());
    }

    private function setupQuery($object, $mock, $invocation, $affectedRows, $lastInsertId, $resultSet, $expected)
    {
        $object->expects($this->once())
            ->method('createInvocation')
            ->willReturn($invocation);
        $object->expects($expected ? $this->once() : $this->never())
            ->method('createResultDriver')
            ->willReturn($expected);
        $object->expects($this->once())
            ->method('setAffectedRows')
            ->with($affectedRows);
        $object->expects($this->once())
            ->method('setInsertId')
            ->with($lastInsertId);
        $object->expects($this->once())
            ->method('setResultSet')
            ->with($resultSet);
        $object->setMockObject($mock);
    }

    public function provideQuery()
    {
        return [
            [
                'SELECT * FROM `t1`',
                NULL, NULL, [['id' => 100]],
                $this->createMock(ResultDriver::class),
            ],
            [
                'SELECT * FROM `t2`',
                NULL, 10, [['id' => 100]],
                $this->createMock(ResultDriver::class),
            ],
            [
                'SELECT * FROM `t3`',
                1, 10, [['id' => 100]],
                $this->createMock(ResultDriver::class),
            ],
            [
                'INSERT INTO `t4` VALUES ("foo", "bar")',
                NULL, 10, NULL,
                NULL,
            ],
            [
                'INSERT INTO `t5` VALUES ("foo", "bar")',
                1, 10, NULL,
                NULL,
            ],
            [
                'UPDATE `t6` SET `foo` = "bar"',
                1, NULL, NULL,
                NULL,
            ],
            [
                'COMMIT',
                NULL, NULL, NULL,
                NULL,
            ],
        ];
    }

    /**
     * @dataProvider  provideCreateInvocation
     */
    public function testCreateInvocation($sql, $requireMatch, $expected)
    {
        $trimmed = trim($sql);
        $invocationFactory = $this->createMock(QueryInvocationFactoryInterface::class);
        $invocationFactoryMocker = $invocationFactory->expects($expected ? $this->once() : $this->never())
            ->method('createInvocation');
        if ($expected) {
            $invocationFactoryMocker->with($trimmed)
                ->willReturn($expected ?? $this->anything());
        }
        $mock = $this->createMockDouble($requireMatch);
        $object = $this->createObject();
        $object->setMockObject($mock);
        $object->setQueryInvocationFactory($invocationFactory);

        $createInvocation = new ReflectionMethod($object, 'createInvocation');
        $createInvocation->setAccessible(TRUE);
        $actual = $createInvocation->invoke($object, $sql);
        if ($expected) {
            $this->assertSame($expected, $actual);
        }
        $this->assertInstanceOf(QueryInvocation::class, $actual);
        $this->assertSame($trimmed, $actual->getQuery());
    }

    public function provideCreateInvocation()
    {
        return [
            [
                'SELECT * FROM `t1`',
                TRUE,
                NULL,
            ],
            [
                ' SELECT * FROM `t2` ',
                TRUE,
                NULL,
            ],
            [
                'SELECT * FROM `t1`',
                FALSE,
                $this->createQueryInvocation('SELECT * FROM `t1`'),
            ],
            [
                ' SELECT * FROM `t2` ',
                FALSE,
                $this->createQueryInvocation('SELECT * FROM `t2`'),
            ],
        ];
    }

    /**
     * @dataProvider  provideAffectedRows
     */
    public function testAffectedRows($count)
    {
        $object = $this->createObject();
        $this->assertNull($object->getAffectedRows());
        $object->setAffectedRows($count);
        $this->assertSame($count, $object->getAffectedRows());
    }

    public function provideAffectedRows()
    {
        return [
            [0],
            [NULL],
            [100],
        ];
    }

    /**
     * @dataProvider  provideInsertId
     */
    public function testInsertId($value, $expected)
    {
        $object = $this->createObject();
        $this->assertNull($object->getInsertId(NULL));
        $object->setInsertId($value);
        $this->assertSame($expected, $object->getInsertId(NULL));
    }

    public function provideInsertId()
    {
        return [
            [0, NULL],
            [NULL, NULL],
            [100, 100],
        ];
    }

    /**
     * @dataProvider  provideResultSet
     */
    public function testResultSet($resultSet)
    {
        $object = $this->createObject();
        $this->assertNull($object->getResultSet());
        $object->setResultSet($resultSet);
        $this->assertSame($resultSet, $object->getResultSet());
    }

    public function provideResultSet()
    {
        return [
            [[]],
            [NULL],
            [['id' => 1], ['id' => 2], ['id' => 3]],
        ];
    }

    /**
     * @param   boolean  $requireMatch
     * @return  MockDouble
     */
    private function createMockDouble($requireMatch = TRUE)
    {
        $mock = new MockDouble;
        $mock->setRequireMatch($requireMatch);
        return $mock;
    }

    /**
     * @param   array  $methods
     * @return  MockQueryTrait
     */
    private function createObject(array $methods = [])
    {
        return $this->getMockForTrait(MockQueryDriverTrait::class, [], '', TRUE, TRUE, TRUE, $methods);
    }

    /**
     * @param   string  $sql
     * @param   mixed   $affectedRows
     * @param   mixed   $lastInsertId
     * @param   mixed   $resultSet
     * @return  QueryInvocation
     */
    private function createQueryInvocation($sql, $affectedRows = NULL, $lastInsertId = NULL, $resultSet = NULL)
    {
        $invocation = new QueryInvocation($sql);
        if ($affectedRows !== NULL) {
            $invocation->setAffectedRows($affectedRows);
        }
        if ($lastInsertId !== NULL) {
            $invocation->setLastInsertId($lastInsertId);
        }
        if ($resultSet !== NULL) {
            $invocation->setResultSet($resultSet);
        }
        return $invocation;
    }
}
