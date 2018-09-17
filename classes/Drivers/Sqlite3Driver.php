<?php
namespace Cz\PHPUnit\MockDibi\Drivers;

use Cz\PHPUnit\SQL,
    Dibi\Drivers,
    Dibi\NotImplementedException,
    ReflectionProperty,
    SQLite3,
    SQLite3Result as NativeSqlite3DResult;

/**
 * Sqlite3Driver
 * 
 * @author   czukowski
 * @license  MIT License
 */
class Sqlite3Driver extends Drivers\Sqlite3Driver implements
    DatabaseDriverInterface,
    SQL\DatabaseDriverInterface
{
    use MockQueryConnectionTrait;
    use MockQueryDriverTrait;

    /**
     * @param  array  $config
     */
    public function __construct(array $config = [])
    {
        $formatDate = $config['formatDate'] ?? 'U';
        $formatDateTime = $config['formatDateTime'] ?? 'U';
        $this->setDateTimeFormats($formatDate, $formatDateTime);
        // No calling parent constructor!
    }

    /**
     * @param  mixed  $savepoint
     */
    public function begin(string $savepoint = NULL): void
    {
        $this->addExecutedQuery($savepoint ? "SAVEPOINT $savepoint" : 'BEGIN');
    }

    /**
     * @param  mixed  $savepoint
     */
    public function commit(string $savepoint = NULL): void
    {
        $this->addExecutedQuery($savepoint ? "RELEASE SAVEPOINT $savepoint" : 'COMMIT');
    }

    /**
     * @param  mixed  $savepoint
     */
    public function rollback(string $savepoint = NULL): void
    {
        $this->addExecutedQuery($savepoint ? "ROLLBACK TO SAVEPOINT $savepoint" : 'ROLLBACK');
    }

    /**
     * @param   mixed  $resultSet
     * @return  Sqlite3Result
     */
    public function createResultSet($resultSet)
    {
        return new Sqlite3Result($resultSet);
    }

    /**
     * @param   NativeSqlite3DResult  $result
     * @return  Sqlite3Result
     */
    public function createResultDriver(NativeSqlite3DResult $result): Drivers\Sqlite3Result
    {
        return new Sqlite3Result($result);
    }

    /**
     * @param   string   $value
     * @param   integer  $pos
     * @return  string
     */
    public function escapeLike(string $value, int $pos): string
    {
        return ($pos <= 0 ? "'%" : "'")
            .addcslashes($this->escapeString($value), '%_\\')
            .($pos >= 0 ? "%'" : "'")
            . " ESCAPE '\\'";
    }

    /**
     * Cheap and dirty replacement for `SQLite3::escapeString`.
     * 
     * @param   string  $value
     * @return  string
     */
    private function escapeString(string $value): string
    {
        return str_replace("'", "''", $value);
    }

    /**
     * @param   string  $value
     * @return  string
     */
    public function escapeText(string $value): string
    {
        return "'".$this->escapeString($value)."'";
    }

    /**
     * @return  NULL
     */
    public function getResource(): ?SQLite3
    {
        // Do not throw exception here, method called by `Dibi\Connection` destructor.
        return NULL;
    }

    /**
     * @throws  NotImplementedException
     */
    public function registerFunction(string $name, callable $callback, int $numArgs = -1): void
    {
        throw new NotImplementedException('No user-defined functions for mock DB connection');
    }

    /**
     * @throws  NotImplementedException
     */
    public function registerAggregateFunction(string $name, callable $rowCallback, callable $agrCallback, int $numArgs = -1): void
    {
        throw new NotImplementedException('No user-defined functions for mock DB connection');
    }

    /**
     * @param  string  $fmtDate
     * @param  string  $fmtDateTime
     */
    public function setDateTimeFormats(string $fmtDate, string $fmtDateTime): void
    {
        $propertyDate = new ReflectionProperty(Drivers\Sqlite3Driver::class, 'fmtDate');
        $propertyDate->setAccessible(TRUE);
        $propertyDate->setValue($this, $fmtDate);
        $propertyDateTime = new ReflectionProperty(Drivers\Sqlite3Driver::class, 'fmtDateTime');
        $propertyDateTime->setAccessible(TRUE);
        $propertyDateTime->setValue($this, $fmtDateTime);
    }
}
