<?php
namespace Cz\PHPUnit\MockDibi\Drivers;

use Dibi\Drivers,
    Dibi\NotImplementedException,
    Dibi\NotSupportedException;

/**
 * OracleResult
 *
 * @author   czukowski
 * @license  MIT License
 */
class OracleResult extends Drivers\OracleResult
{
    use MockQueryResultDriverTrait;

    /**
     * @throws  NotSupportedException
     */
    public function getRowCount(): int
    {
        // Parent class will throw exception.
        return parent::getRowCount();
    }

    /**
     * @throws  NotImplementedException
     */
    public function seek(int $row): bool
    {
        // Parent class will throw exception.
        return parent::seek($row);
    }
}
