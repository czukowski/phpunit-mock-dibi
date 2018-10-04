<?php
namespace Cz\PHPUnit\MockDibi\Drivers;

use Cz\PHPUnit\SQL,
    Dibi\Drivers,
    Dibi\NotSupportedException;

/**
 * FirebirdDriver
 * 
 * @author   czukowski
 * @license  MIT License
 */
class FirebirdDriver extends Drivers\FirebirdDriver implements
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
        $this->checkSavepointValue($savepoint);
        $this->inTransaction = TRUE;
    }

    /**
     * @param  mixed  $savepoint
     */
    public function commit($savepoint = NULL)
    {
        $this->checkSavepointValue($savepoint);
        $this->inTransaction = FALSE;
    }

    /**
     * @param  mixed  $savepoint
     */
    public function rollback($savepoint = NULL)
    {
        $this->checkSavepointValue($savepoint);
        $this->inTransaction = FALSE;
    }

    /**
     * @param   mixed  $savepoint
     * @throws  NotSupportedException
     */
    private function checkSavepointValue($savepoint)
    {
        if ($savepoint !== NULL) {
            throw new NotSupportedException('Savepoints are not supported in Firebird/Interbase.');
        }
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
    public function getRowCount()
    {
        // Parent class will throw exception.
        return parent::getRowCount();
    }

    /**
     * @throws  NotSupportedException
     */
    public function seek($row)
    {
        // Parent class will throw exception.
        return parent::seek($row);
    }
}
