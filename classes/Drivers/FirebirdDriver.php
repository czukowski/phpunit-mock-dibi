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
    private $inTransaction = FALSE;

    /**
     * @param  string|NULL  $savepoint
     */
    public function begin(string $savepoint = NULL): void
    {
        $this->checkSavepointValue($savepoint);
        $this->inTransaction = TRUE;
    }

    /**
     * @param  string|NULL  $savepoint
     */
    public function commit(string $savepoint = NULL): void
    {
        $this->checkSavepointValue($savepoint);
        $this->inTransaction = FALSE;
    }

    /**
     * @param  string|NULL  $savepoint
     */
    public function rollback(string $savepoint = NULL): void
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
    public function inTransaction(): bool
    {
        return $this->inTransaction;
    }

    /**
     * @return  FirebirdResult
     */
    public function createResultDriver($resultSet): Drivers\FirebirdResult
    {
        return new FirebirdResult($resultSet);
    }
}
