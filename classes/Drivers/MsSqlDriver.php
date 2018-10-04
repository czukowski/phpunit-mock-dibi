<?php
namespace Cz\PHPUnit\MockDibi\Drivers;

use Cz\PHPUnit\SQL,
    Dibi\Drivers;

/**
 * MsSqlDriver
 * 
 * @author   czukowski
 * @license  MIT License
 */
class MsSqlDriver extends Drivers\MsSqlDriver implements
    DatabaseDriverInterface,
    SQL\DatabaseDriverInterface
{
    use MockQueryConnectionTrait;
    use MockQueryDriverTrait;

    /**
     * @param   mixed  $sequence
     * @return  mixed
     */
    public function getInsertId($sequence)
    {
        $this->addExecutedQuery('SELECT @@IDENTITY');
        return $this->lastInsertId ? : FALSE;
    }
}
