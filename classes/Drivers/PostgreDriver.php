<?php
namespace Cz\PHPUnit\MockDibi\Drivers;

use Cz\PHPUnit\SQL,
    Dibi\Drivers,
    Dibi\NotImplementedException;

/**
 * PostgreDriver
 * 
 * @author   czukowski
 * @license  MIT License
 */
class PostgreDriver extends Drivers\PostgreDriver implements
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
     * @param   mixed  $sequence
     * @return  mixed
     */
    public function getInsertId($sequence)
    {
        $this->addExecutedQuery($sequence === NULL ? 'SELECT LASTVAL()' : "SELECT CURRVAL('$sequence')");
        return $this->lastInsertId ? : FALSE;
    }

    /**
     * @param  mixed  $savepoint
     */
    public function begin($savepoint = NULL)
    {
        $this->inTransaction = TRUE;
        $this->addExecutedQuery($savepoint ? "SAVEPOINT $savepoint" : 'START TRANSACTION');
    }

    /**
     * @param  mixed  $savepoint
     */
    public function commit($savepoint = NULL)
    {
        $this->inTransaction = FALSE;
        $this->addExecutedQuery($savepoint ? "RELEASE SAVEPOINT $savepoint" : 'COMMIT');
    }

    /**
     * @param  mixed  $savepoint
     */
    public function rollback($savepoint = NULL)
    {
        $this->inTransaction = FALSE;
        $this->addExecutedQuery($savepoint ? "ROLLBACK TO SAVEPOINT $savepoint" : 'ROLLBACK');
    }

    /**
     * @return  boolean
     */
    public function inTransaction()
    {
        return $this->inTransaction;
    }

    /**
     * A replacement for `pg_escape_bytea`.
     * 
     * @param   string  $value
     * @return  string
     */
    public function escapeBinary($value)
    {
        return PostgreEscapingHelper::escapeBinary($value);
    }

    /**
     * Adapted version of the parent class' method to avoid calls to `pg_escape_string`.
     * 
     * @param   string  $value
     * @param   string  $pos
     * @return  string
     */
    public function escapeLike($value, $pos)
    {
        return ($pos <= 0 ? "'%" : "'")
            .strtr($value, ['%' => '\\%', '_' => '\\_', '\\' => '\\\\'])
            .($pos >= 0 ? "%'" : "'");
    }

    /**
     * A cheap and dirty replacement for `pg_escape_string`.
     * 
     * @param   string  $value
     * @return  string
     */
    public function escapeText($value)
    {
        return PostgreEscapingHelper::escapeText($value);
    }

    /**
     * A replacement for `pg_unescape_bytea`.
     * 
     * @param   string  $value
     * @return  string
     */
    public function unescapeBinary($value)
    {
        return PostgreEscapingHelper::unescapeBinary($value);
    }

    /**
     * @throws  NotImplementedException
     */
    public function ping()
    {
        throw new NotImplementedException('No pinging mock DB connection');
    }

    /**
     * @throws  NotImplementedException
     */
    public function getTables()
    {
        throw new NotImplementedException('Parent class method uses extention functions');
    }

    /**
     * @param   string  $table
     * @throws  NotImplementedException
     */
    public function getColumns($table)
    {
        throw new NotImplementedException('Parent class method uses extention functions');
    }
}
