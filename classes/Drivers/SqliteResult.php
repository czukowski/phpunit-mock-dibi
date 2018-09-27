<?php
namespace Cz\PHPUnit\MockDibi\Drivers;

use Dibi\Drivers,
    Dibi\NotSupportedException,
    SQLite3Result as NativeSQLiteResult;

/**
 * SqliteResult
 * 
 * @author   czukowski
 * @license  MIT License
 */
class SqliteResult extends Drivers\SqliteResult
{
    use MockQueryResultDriverTrait;

    /**
     * @throws  NotSupportedException
     */
    public function getResultResource(): NativeSQLiteResult
    {
        throw new NotSupportedException('Native result resource unavailable for mock DB connection');
    }

    /**
     * @throws  NotSupportedException
     */
    public function getRowCount(): int
    {
        // Parent class will throw exception.
        return parent::getRowCount();
    }

    /**
     * @throws  NotSupportedException
     */
    public function seek(int $row): bool
    {
        // Parent class will throw exception.
        return parent::seek($row);
    }
}
