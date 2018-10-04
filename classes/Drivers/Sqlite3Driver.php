<?php
namespace Cz\PHPUnit\MockDibi\Drivers;

use Cz\PHPUnit\SQL,
    Dibi\Drivers,
    Dibi\NotImplementedException,
    Dibi\NotSupportedException,
    ReflectionProperty;

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
        $formatDate = isset($config['formatDate']) ? $config['formatDate'] : 'U';
        $formatDateTime = isset($config['formatDateTime']) ? $config['formatDateTime'] : 'U';
        $this->setDateTimeFormats($formatDate, $formatDateTime);
        // No calling parent constructor!
    }

    /**
     * @param  mixed  $savepoint
     */
    public function begin($savepoint = NULL)
    {
        $this->addExecutedQuery($savepoint ? "SAVEPOINT $savepoint" : 'BEGIN');
    }

    /**
     * @param  mixed  $savepoint
     */
    public function commit($savepoint = NULL)
    {
        $this->addExecutedQuery($savepoint ? "RELEASE SAVEPOINT $savepoint" : 'COMMIT');
    }

    /**
     * @param  mixed  $savepoint
     */
    public function rollback($savepoint = NULL)
    {
        $this->addExecutedQuery($savepoint ? "ROLLBACK TO SAVEPOINT $savepoint" : 'ROLLBACK');
    }

    /**
     * @param   string   $value
     * @param   integer  $pos
     * @return  string
     */
    public function escapeLike($value, $pos)
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
    private function escapeString($value)
    {
        return str_replace("'", "''", $value);
    }

    /**
     * @param   string  $value
     * @return  string
     */
    public function escapeText($value)
    {
        return "'".$this->escapeString($value)."'";
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

    /**
     * @throws  NotImplementedException
     */
    public function registerFunction($name, callable $callback, $numArgs = -1)
    {
        throw new NotImplementedException('No user-defined functions for mock DB connection');
    }

    /**
     * @throws  NotImplementedException
     */
    public function registerAggregateFunction($name, callable $rowCallback, callable $agrCallback, $numArgs = -1)
    {
        throw new NotImplementedException('No user-defined functions for mock DB connection');
    }

    /**
     * @param  string  $fmtDate
     * @param  string  $fmtDateTime
     */
    public function setDateTimeFormats($fmtDate, $fmtDateTime)
    {
        $propertyDate = new ReflectionProperty(Drivers\Sqlite3Driver::class, 'fmtDate');
        $propertyDate->setAccessible(TRUE);
        $propertyDate->setValue($this, $fmtDate);
        $propertyDateTime = new ReflectionProperty(Drivers\Sqlite3Driver::class, 'fmtDateTime');
        $propertyDateTime->setAccessible(TRUE);
        $propertyDateTime->setValue($this, $fmtDateTime);
    }
}
