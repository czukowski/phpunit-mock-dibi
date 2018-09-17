<?php
namespace Cz\PHPUnit\MockDibi\Drivers;

use Dibi\Drivers;

/**
 * FirebirdResult
 * 
 * @author   czukowski
 * @license  MIT License
 */
class FirebirdResult extends Drivers\FirebirdResult
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
     * @throws  NotSupportedException
     */
    public function seek(int $row): bool
    {
        // Parent class will throw exception.
        return parent::seek($row);
    }
}
