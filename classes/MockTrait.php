<?php
namespace Cz\PHPUnit\MockDibi;

use Cz\PHPUnit\MockDB\DatabaseDriverInterface,
    Cz\PHPUnit\MockDB\MockObject\MockWrapper,
    Dibi\Connection,
    LogicException,
    PHPUnit\Framework\MockObject\MockObject;

/**
 * MockTrait
 * 
 * @author   czukowski
 * @license  MIT License
 */
trait MockTrait
{
    /**
     * @param   Connection  $dibi
     * @return  Mock
     * @throws  LogicException
     */
    public function createDatabaseMock(Connection $dibi)
    {
        if ( ! $dibi->getDriver() instanceof DatabaseDriverInterface) {
            throw new LogicException('Dibi connection driver must implement `DatabaseDriverInterface`');
        }
        $mock = new Mock($dibi);
        $dibi->getDriver()
            ->setMockObject($mock);
        $this->registerMockObject(new MockWrapper($mock));
        return $mock;
    }

    /**
     * @param  MockObject  $mockObject
     */
    abstract public function registerMockObject(MockObject $mockObject): void;
}
