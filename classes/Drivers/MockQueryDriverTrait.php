<?php
namespace Cz\PHPUnit\MockDibi\Drivers;

use Cz\PHPUnit\MockDB\Invocation\QueryInvocation,
    Cz\PHPUnit\MockDB\Invocation\QueryInvocationFactoryInterface,
    Cz\PHPUnit\MockDB\Mock,
    Cz\PHPUnit\SQL\DatabaseDriverTrait,
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
    use DatabaseDriverTrait;
    use MockQueryResultDriverTrait;

    /**
     * @var  integer
     */
    private $affectedRows;
    /**
     * @var  mixed
     */
    private $lastInsertId;
    /**
     * @var  QueryInvocationFactoryInterface
     */
    private $invocationFactory;
    /**
     * @var  Mock
     */
    private $mock;

    /**
     * @param   string  $sql
     * @return  ResultDriver|NULL
     */
    public function query($sql)
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
     * @param  string        $sql
     * @param  integer|NULL  $limit
     * @param  integer|NULL  $offset
     */
    abstract public function applyLimit( & $sql, $limit, $offset);

    /**
     * @return  integer|NULL
     */
    public function getAffectedRows()
    {
        return $this->affectedRows;
    }

    /**
     * @param  mixed  $value
     */
    public function setAffectedRows($value)
    {
        $this->affectedRows = $value;
    }

    /**
     * @param   mixed  $sequence
     * @return  mixed|FALSE
     */
    public function getInsertId($sequence = NULL)
    {
        return $this->lastInsertId ? : FALSE;
    }

    /**
     * @param  mixed  $value
     */
    public function setInsertId($value)
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
     * @return  Mock
     */
    public function getMockObject()
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
    abstract public function getReflector();

    /**
     * @param   string  $value
     * @return  string
     */
    abstract public function escapeText($value);

    /**
     * @param   string  $value
     * @return  string
     */
    abstract public function escapeBinary($value);

    /**
     * @param   string  $value
     * @return  string
     */
    abstract public function escapeIdentifier($value);

    /**
     * @param   boolean  $value
     * @return  string
     */
    abstract public function escapeBool($value);

    /**
     * @param   mixed  $value
     * @return  string
     */
    abstract public function escapeDate($value);

    /**
     * @param   mixed  $value
     * @return  string
     */
    abstract public function escapeDateTime($value);

    /**
     * @param   string   $value
     * @param   integer  $pos
     * @return  string
     */
    abstract public function escapeLike($value, $pos);
}
