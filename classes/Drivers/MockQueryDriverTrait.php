<?php
namespace Cz\PHPUnit\MockDibi\Drivers;

use Cz\PHPUnit\MockDB\Invocation\QueryInvocation,
    Cz\PHPUnit\MockDB\Invocation\QueryInvocationFactoryInterface,
    Cz\PHPUnit\MockDB\Mock,
    Dibi\Reflector,
    Dibi\ResultDriver;

/**
 * MockQueryDriverTrait
 * 
 * Mock implementation of `Dibi\Driver` interface. Mock drivers may use it as a base while
 * extending the actual driver class and override some of the methods where needed.
 * 
 * @author   czukowski
 * @license  MIT License
 */
trait MockQueryDriverTrait
{
    /**
     * @var  integer
     */
    private $affectedRows;
    /**
     * @var  mixed
     */
    private $lastInsertId;
    /**
     * @var  mixed
     */
    private $resultSet;
    /**
     * @var  QueryInvocationFactoryInterface
     */
    private $invocationFactory;
    /**
     * @var  Mock
     */
    private $mock;
    /**
     * @var  array
     */
    private $queries = [];

    /**
     * @param   string  $sql
     * @return  ResultDriver|NULL
     */
    public function query(string $sql): ?ResultDriver
    {
        $this->addExecutedQuery($sql);

        $invocation = $this->mock->invoke($this->createInvocation($sql));
        $this->setAffectedRows($invocation->getAffectedRows());
        $this->setInsertId($invocation->getLastInsertId());
        $this->setResultSet($invocation->getResultSet());

        if ($invocation->getResultSet() !== NULL) {
            return $this->createResultSet($invocation->getResultSet());
        }
        return NULL;
    }

    /**
     * @param   string  $sql
     * @return  QueryInvocation
     */
    protected function createInvocation($sql)
    {
        $trimmed = trim($sql);
        return $this->mock->getRequireMatch()
            ? new QueryInvocation($trimmed)
            : $this->invocationFactory->createInvocation($trimmed);
    }

    /**
     * @param   mixed  $resultSet
     * @return  ResultDriver
     */
    public function createResultSet($resultSet)
    {
        return $this->createResultDriver($resultSet);
    }

    /**
     * @param   mixed  $resultSet
     * @return  ResultDriver
     */
    abstract public function createResultDriver($resultSet);

    /**
     * @param  string        $sql
     * @param  integer|NULL  $limit
     * @param  integer|NULL  $offset
     */
    abstract public function applyLimit(string & $sql, ?int $limit, ?int $offset): void;

    /**
     * @return  integer|NULL
     */
    public function getAffectedRows(): ?int
    {
        return $this->affectedRows;
    }

    /**
     * @param  integer|NULL  $value
     */
    public function setAffectedRows(?int $value)
    {
        $this->affectedRows = $value;
    }

    /**
     * @param   string|NULL  $sequence
     * @return  integer|NULL
     */
    public function getInsertId(?string $sequence): ?int
    {
        return $this->lastInsertId ? : NULL;
    }

    /**
     * @param  integer|NULL  $value
     */
    public function setInsertId(?int $value)
    {
        $this->lastInsertId = $value;
    }

    /**
     * @return  array|NULL
     */
    public function getResultSet()
    {
        return $this->resultSet;
    }

    /**
     * @param  array|NULL  $resultSet
     */
    public function setResultSet($resultSet)
    {
        $this->resultSet = $resultSet;
    }

    /**
     * @param  string  $sql
     */
    private function addExecutedQuery($sql)
    {
        $this->queries[] = $sql;
    }

    /**
     * Implements `Cz\PHPUnit\SQL\DatabaseDriverInterface`
     * 
     * @return  array
     */
    public function getExecutedQueries(): array
    {
        return $this->queries;
    }

    /**
     * @return  Mock
     */
    public function getMockObject(): Mock
    {
        return $this->mock;
    }

    /**
     * Implements `Cz\PHPUnit\MockDB\DatabaseDriverInterface`
     * 
     * @param  Mock  $mock
     */
    public function setMockObject(Mock $mock)
    {
        $this->mock = $mock;
    }

    /**
     * @return  QueryInvocationFactoryInterface
     */
    public function getQueryInvocationFactory()
    {
        return $this->invocationFactory;
    }

    /**
     * @param  QueryInvocationFactoryInterface  $instance
     */
    public function setQueryInvocationFactory(QueryInvocationFactoryInterface $instance)
    {
        $this->invocationFactory = $instance;
    }

    /**
     * @return  void
     */
    public function getResource()
    {
        // Do not return anything.
    }

    /**
     * @return  Reflector
     */
    abstract public function getReflector(): Reflector;

    /**
     * @param   string  $value
     * @return  string
     */
    abstract public function escapeText(string $value): string;

    /**
     * @param   string  $value
     * @return  string
     */
    abstract public function escapeBinary(string $value): string;

    /**
     * @param   string  $value
     * @return  string
     */
    abstract public function escapeIdentifier(string $value): string;

    /**
     * @param   boolean  $value
     * @return  string
     */
    abstract public function escapeBool(bool $value): string;

    /**
     * @param   mixed  $value
     * @return  string
     */
    abstract public function escapeDate($value): string;

    /**
     * @param   mixed  $value
     * @return  string
     */
    abstract public function escapeDateTime($value): string;

    /**
     * @param   string   $value
     * @param   integer  $pos
     * @return  string
     */
    abstract public function escapeLike(string $value, int $pos): string;
}
