<?php
namespace Cz\PHPUnit\MockDibi\Drivers;

use Cz\PHPUnit\MockDibi\MockTrait,
    Cz\PHPUnit\MockDB\DatabaseDriverInterface,
    Cz\PHPUnit\MockDB\Invocation,
    Dibi\Driver,
    Dibi\Connection,
    Dibi\NotSupportedException,
    Dibi\Result,
    PHPUnit\Framework\Constraint\Constraint,
    PHPUnit\Framework\Exception as FrameworkException,
    RuntimeException,
    Throwable;

// Backward compatibility with older PHPUnit versions.
if ( ! class_exists(Constraint::class) && class_exists(\PHPUnit_Framework_Constraint::class)) {
    class_alias(\PHPUnit_Framework_Constraint::class, Constraint::class);
}

/**
 * DriversIntegrationTest
 * 
 * Will test different mocking scenarios for each supported mock driver.
 * 
 * @author   czukowski
 * @license  MIT License
 */
class DriversIntegrationTest extends Testcase
{
    use MockTrait;

    /**
     * Expect any query producing a result set, zero or more times. Invoke a query once.
     * 
     * @dataProvider  provideMatchSingleSelectInvocation
     */
    public function testMatchAnyQueryAnyInvocationCount(Driver $driver, string $query, array $expected)
    {
        $dibi = $this->createDibiConnection($driver);
        $this->createDatabaseMock($dibi)
            ->expects($this->any())
            ->willReturnResultSet($expected);
        $actual = $dibi->query($query)
            ->setRowClass(NULL)
            ->fetchAll();
        $this->assertSame($expected, $actual);
    }

    /**
     * Expect any single query producing a result set. Invoke once.
     * 
     * @dataProvider  provideMatchSingleSelectInvocation
     */
    public function testMatchAnyQuerySingleInvocation(Driver $driver, string $query, array $expected)
    {
        $dibi = $this->createDibiConnection($driver);
        $this->createDatabaseMock($dibi)
            ->expects($this->once())
            ->willReturnResultSet($expected);
        $actual = $dibi->query($query)
            ->setRowClass(NULL)
            ->fetchAll();
        $this->assertSame($expected, $actual);
    }

    /**
     * Expect any query producing a result set, zero or more times. Do not invoke any.
     * 
     * @dataProvider  provideMatchSingleSelectInvocation
     */
    public function testMatchAnyQueryAnyInvocationCountNoneInvoked(Driver $driver, $_, array $expected)
    {
        $dibi = $this->createDibiConnection($driver);
        $this->createDatabaseMock($dibi)
            ->expects($this->any())
            ->willReturnResultSet($expected);
    }

    public function provideMatchSingleSelectInvocation()
    {
        return $this->createDriversDataProvider(
            'SELECT * FROM `t`',
            [['foo' => 'bar']]
        );
    }

    /**
     * Expect two different queries producing result sets, each executed once, regardless of the order.
     * Invoke both once, starting with the 2nd query.
     * 
     * @dataProvider  provideMatchTwoSelectInvocations
     */
    public function testMatchWithQueryMatchersOnceEach(
        Driver $driver,
        string $query1,
        array $expected1,
        string $query2,
        array $expected2
    ) {
        $dibi = $this->createDibiConnection($driver);
        $mock = $this->createDatabaseMock($dibi);
        $mock->expects($this->once())
            ->query($query1)
            ->willReturnResultSet($expected1);
        $mock->expects($this->once())
            ->query($query2)
            ->willReturnResultSet($expected2);

        // Invoke in reverse order, it should succeed.
        $actual2 = $dibi->query($query2)
            ->setRowClass(NULL)
            ->fetchAll();
        $this->assertSame($expected2, $actual2);
        $actual1 = $dibi->query($query1)
            ->setRowClass(NULL)
            ->fetchAll();
        $this->assertSame($expected1, $actual1);
    }

    public function provideMatchTwoSelectInvocations()
    {
        return $this->createDriversDataProvider(
            'SELECT * FROM `t1`',
            [['foo' => 'bar']],
            'SELECT * FROM `t2`',
            [['no' => 'way']]
         );
    }

    /**
     * Expect two queries producing insert IDs executed as 2nd and 3rd queries, and also
     * a query producing a result set executed once at any position. Invoke select query,
     * then both insert queries in the correct order.
     * 
     * @dataProvider  provideMatchMixedQueriesWithQueryMatchersOnceEach
     */
    public function testMatchMixedQueriesWithQueryMatchersOnceEach(
        Driver $driver,
        string $query1,
        array $expected1,
        string $query2,
        int $expected2,
        string $query3,
        int $expected3
    ) {
        $dibi = $this->createDibiConnection($driver);
        $mock = $this->createDatabaseMock($dibi);
        $mock->expects($this->at(1))
            ->query($query2)
            ->willSetLastInsertId($expected2);
        $mock->expects($this->at(2))
            ->query($query3)
            ->willSetLastInsertId($expected3);
        $mock->expects($this->once())
            ->query($query1)
            ->willReturnResultSet($expected1);

        $actual1 = $dibi->query($query1)
            ->setRowClass(NULL)
            ->fetchAll();
        $this->assertSame($expected1, $actual1);

        $dibi->query($query2);
        $actual2 = $this->invokeAndSkipNotSupportedException(
            function () use ($dibi) {
                return $dibi->getInsertId();
            }
        );
        $this->assertSame($expected2, $actual2);

        $dibi->query($query3);
        $actual3 = $dibi->getInsertId();
        $this->assertSame($expected3, $actual3);
    }

    public function provideMatchMixedQueriesWithQueryMatchersOnceEach()
    {
        return $this->createDriversDataProvider(
            'SELECT * FROM `t1`',
            [['foo' => 'bar']],
            'INSERT INTO `t1` VALUES (1, 2, 3)',
            1,
            'INSERT INTO `t1` VALUES (1, 2, 3)',
            2
        );
    }

    /**
     * Expect a series of consequent queries producing insert IDs. Invoke all of then in order.
     * 
     * @dataProvider  provideMatchWithQueryMatchersWithConsecutiveCalls
     */
    public function testMatchWithQueryMatchersWithConsecutiveCalls(
        Driver $driver,
        string $query,
        array $expecteds
    ) {
        $dibi = $this->createDibiConnection($driver);
        $this->createDatabaseMock($dibi)
            ->expects($this->exactly(count($expecteds)))
            ->query($query)
            ->willSetLastInsertId(...$expecteds);

        foreach ($expecteds as $expected) {
            $dibi->query($query);
            $actual = $this->invokeAndSkipNotSupportedException(
                function () use ($dibi) {
                    return $dibi->getInsertId();
                }
            );
            $this->assertSame($expected, $actual);
        }
    }

    public function provideMatchWithQueryMatchersWithConsecutiveCalls()
    {
        return $this->createDriversDataProvider(
            'INSERT INTO `t1` VALUES ("a", "b", "c")',
            [1, 2, 3]
        );
    }

    /**
     * Expect a query producing a number of affected rows to be executed repeatedly
     * exactly specified amount of times. Invoke as specified.
     * 
     * @dataProvider  provideMatchWithQueryMatcherAssertAffectedRows
     */
    public function testMatchWithQueryMatcherAssertAffectedRows(
        Driver $driver,
        int $expectTimes,
        string $query,
        int $expected
    ) {
        $dibi = $this->createDibiConnection($driver);
        $this->createDatabaseMock($dibi)
            ->expects($this->exactly($expectTimes))
            ->query($query)
            ->willSetAffectedRows($expected);

        for ($i = 0; $i < $expectTimes; $i++) {
            $actual = $dibi->query($query);
            $this->assertSame($expected, $actual);
        }
    }

    public function provideMatchWithQueryMatcherAssertAffectedRows()
    {
        return $this->createDriversDataProvider(
            2,
            'UPDATE `t1` SET `foo` = "bar"',
            1
        );
    }

    /**
     * Expect a query producing a result set executed once, while being validated against
     * a specified native PHPUnit constraint. Invoke passed query once.
     * 
     * @dataProvider  provideMatchQueryWithPHPUnitConstraint
     */
    public function testMatchQueryWithPHPUnitConstraint(
        Driver $driver,
        Constraint $constraint,
        string $query,
        array $expected
    ) {
        $dibi = $this->createDibiConnection($driver);
        $this->createDatabaseMock($dibi)
            ->expects($this->once())
            ->query($constraint)
            ->willReturnResultSet($expected);

        $actual = $dibi->query($query)
            ->setRowClass(NULL)
            ->fetchAll();
        $this->assertSame($expected, $actual);
    }

    public function provideMatchQueryWithPHPUnitConstraint()
    {
        return $this->createDriversDataProvider(
            $this->stringStartsWith('SELECT'),
            'SELECT * FROM `t`',
            [['foo' => 'bar']]
        );
    }

    /**
     * Expect a query producing a series of consequent queries producing insert IDs using consecutive calls
     * builder in a way that the 1st and 2nd invocations succeed, 3rd invocation throws an exception and the
     * 4th invocation succeed again. Invoke a passed query four times and check the correct result each time.
     * 
     * @dataProvider  provideMatchWithQueryMatchersWithConsecutiveCallsBuilder
     */
    public function testMatchWithQueryMatchersWithConsecutiveCallsBuilder(
        Driver $driver,
        string $query,
        Throwable $exception,
        array $expecteds
    ) {
        $queue = $expecteds;
        $dibi = $this->createDibiConnection($driver);
        $this->createDatabaseMock($dibi)
            ->expects($this->atLeast(count($expecteds) + 1))
            ->query($query)
            ->onConsecutiveCalls()
            ->willSetLastInsertId(array_shift($queue))
            ->willSetLastInsertId(array_shift($queue))
            ->willThrowException($exception)
            ->willSetLastInsertId(array_shift($queue));

        $dibi->query($query);
        $actual0 = $this->invokeAndSkipNotSupportedException(
            function () use ($dibi) {
                return $dibi->getInsertId();
            }
        );
        $this->assertSame($expecteds[0], $actual0);

        $dibi->query($query);
        $actual1 = $dibi->getInsertId();
        $this->assertSame($expecteds[1], $actual1);

        try {
            $dibi->query($query);
            $this->fail('Expected exception');
        }
        catch (Throwable $e) {
            if ($e instanceof FrameworkException) {
                throw $e;
            }
            $this->assertInstanceOf(get_class($exception), $e);
        }

        $dibi->query($query);
        $actual2 = $dibi->getInsertId();
        $this->assertSame($expecteds[2], $actual2);
    }

    public function provideMatchWithQueryMatchersWithConsecutiveCallsBuilder()
    {
        return $this->createDriversDataProvider(
            'INSERT INTO `t1` VALUES ("a", "b", "c")',
            new RuntimeException('Deadlock'),
            [1, 2, 3]
        );
    }

    /**
     * Expect a single query and set up passed callback functions to create query result.
     * Invoke once and check for the correct result.
     * 
     * @dataProvider  provideMatchWithQueryMatchersWithCustomCallbackHandlers
     */
    public function testMatchWithQueryMatchersWithCustomCallbackHandlers(
        Driver $driver,
        Constraint $constraint,
        callable $callback,
        string $query,
        callable $assert
    ) {
        $dibi = $this->createDibiConnection($driver);
        $this->createDatabaseMock($dibi)
            ->expects($this->once())
            ->query($constraint)
            ->willInvokeCallback($callback);

        $actual = $dibi->query($query);
        $assert($dibi, $actual);
    }

    public function provideMatchWithQueryMatchersWithCustomCallbackHandlers()
    {
        return array_merge(
            array_values(
                $this->createDriversDataProvider(
                    $this->stringStartsWith('INSERT'),
                    function (Invocation $invocation) {
                        $invocation->setLastInsertId(1);
                    },
                    'INSERT INTO `t1` VALUES ("foo")',
                    function (Connection $dibi) {
                        $actual = $this->invokeAndSkipNotSupportedException(
                            function () use ($dibi) {
                                return $dibi->getInsertId();
                            }
                        );
                        $this->assertSame(1, $actual);
                    }
                )
            ),
            array_values(
                $this->createDriversDataProvider(
                    $this->stringStartsWith('UPDATE'),
                    function (Invocation $invocation) {
                        $invocation->setAffectedRows(0);
                    },
                    'UPDATE `t1` SET `name` = "foo" WHERE `name` = "bar"',
                    function (Connection $dibi) {
                        $actual = $dibi->getAffectedRows();
                        $this->assertSame(0, $actual);
                    }
                )
            ),
            array_values(
                $this->createDriversDataProvider(
                    $this->stringStartsWith('SELECT'),
                    function (Invocation $invocation) {
                        $invocation->setResultSet([['name' => 'foo']]);
                    },
                    'SELECT * FROM `t`',
                    function (Connection $_, $result) {
                        $this->assertInstanceOf(Result::class, $result);
                        $actual = $result->setRowClass(NULL)
                            ->fetchAll();
                        $this->assertSame([['name' => 'foo']], $actual);
                    }
                )
            )
        );
    }

    /**
     * @param   DatabaseDriverInterface  $driver
     * @return  Connection
     */
    protected function createDibiConnection(DatabaseDriverInterface $driver)
    {
        return new Connection(['driver' => $driver]);
    }

    /**
     * Some drivers may not support some features that other drivers do, such as auto incrementing IDs,
     * but it's not the reason to fail the integration test.
     * 
     * @param   callable  $function
     * @param   array     $arguments
     * @return  mixed
     */
    private function invokeAndSkipNotSupportedException(callable $function, array $arguments = [])
    {
        try {
            return call_user_func_array($function, $arguments);
        }
        catch (NotSupportedException $e) {
            $this->markTestSkipped($e->getMessage());
        }
    }
}
