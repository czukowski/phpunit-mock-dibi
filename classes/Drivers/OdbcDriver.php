<?php
namespace Cz\PHPUnit\MockDibi\Drivers;

use Cz\PHPUnit\SQL,
    Dibi\Drivers,
    Dibi\NotImplementedException;

/**
 * OdbcDriver
 * 
 * @author   czukowski
 * @license  MIT License
 */
class OdbcDriver extends Drivers\OdbcDriver implements
    DatabaseDriverInterface,
    SQL\DatabaseDriverInterface
{
    use MockQueryConnectionTrait;
    use MockQueryDriverTrait;

    /**
     * @var  boolean
     */
    private $inTransaction;

    /**
     * @param  mixed  $savepoint
     */
    public function begin($savepoint = NULL)
    {
        $this->inTransaction = TRUE;
    }

    /**
     * @param  mixed  $savepoint
     */
    public function commit($savepoint = NULL)
    {
        $this->inTransaction = FALSE;
    }

    /**
     * @param  mixed  $savepoint
     */
    public function rollback($savepoint = NULL)
    {
        $this->inTransaction = FALSE;
    }

    /**
     * @return  boolean
     */
    public function inTransaction()
    {
        return $this->inTransaction;
    }

    /**
     * @throws  NotSupportedException
     */
    public function getInsertId($sequence)
    {
        // Parent class will throw exception.
        return parent::getInsertId($sequence);
    }

    /**
     * @throws  NotImplementedException
     */
    public function getTables()
    {
        $this->throwReflectorNotImplementedException();
    }

    /**
     * @param   string  $table
     * @throws  NotImplementedException
     */
    public function getColumns($table)
    {
        $this->throwReflectorNotImplementedException();
    }

    /**
     * @throws  NotImplementedException
     */
    private function throwReflectorNotImplementedException()
    {
        throw new NotImplementedException('No schema info for mock DB connection');
    }
}
