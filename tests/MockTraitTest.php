<?php
namespace Cz\PHPUnit\MockDibi;

use Cz\PHPUnit\MockDibi\Drivers\DatabaseDriverInterface,
    Cz\PHPUnit\MockDB\MockObject\MockWrapper,
    Dibi\Connection,
    Dibi\Driver,
    LogicException,
    Exception;

/**
 * MockTraitTest
 * 
 * @author   czukowski
 * @license  MIT License
 */
class MockTraitTest extends Testcase
{
    /**
     * @dataProvider  provideCreateDatabaseMock
     */
    public function testCreateDatabaseMock($driverType, $expectedException)
    {
        $dibi = new Connection([
            'driver' => $this->createDibiDriver($driverType),
        ]);
        $mockObject = NULL;
        $object = $this->createObject($expectedException instanceof Exception, $mockObject);

        $this->expectExceptionFromArgument($expectedException);
        $actual = $object->createDatabaseMock($dibi);
        $this->assertSame($actual, $dibi->getDriver()->getMockObject());
        $this->assertSame($actual, $mockObject);
    }

    public function provideCreateDatabaseMock()
    {
        return [
            ['base', new LogicException],
            ['mock', NULL],
        ];
    }

    /**
     * @param   string  $type
     * @return  Driver
     */
    private function createDibiDriver($type)
    {
        switch ($type) {
            case 'base':
                return $this->createMock(Driver::class);
            case 'mock':
                $setMockObject = NULL;
                $object = $this->createMock([Driver::class, DatabaseDriverInterface::class]);
                $object->expects($this->once())
                    ->method('setMockObject')
                    ->willReturnCallback(function ($object) use ( & $setMockObject) {
                        $setMockObject = $object;
                    });
                $object->expects($this->once())
                    ->method('getMockObject')
                    ->willReturnCallback(function () use ( & $setMockObject) {
                        return $setMockObject;
                    });
                return $object;
        }
    }

    /**
     * @param   boolean  $expectException
     * @param   NULL     $registerMockObject
     * @return  MockTrait
     */
    private function createObject($expectException, & $registerMockObject)
    {
        $methods = ['registerMockObject'];
        $object = $this->getMockForTrait(MockTrait::class, [], '', TRUE, TRUE, TRUE, $methods);
        $object->expects($expectException ? $this->never() : $this->once())
            ->method('registerMockObject')
            ->with($this->callback(
                function ($mockObject) use ( & $registerMockObject) {
                    $this->assertInstanceOf(MockWrapper::class, $mockObject);
                    $mock = $this->getObjectAttribute($mockObject, 'object');
                    $this->assertInstanceOf(Mock::class, $mock);
                    $registerMockObject = $mock;
                    return TRUE;
                }
            ));
        return $object;
    }
}
