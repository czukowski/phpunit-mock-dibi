<?php
namespace Cz\PHPUnit\MockDibi\Drivers;

use Cz\PHPUnit\SQL,
    Dibi\Drivers;

/**
 * MySqlDriver
 * 
 * @author   czukowski
 * @license  MIT License
 */
class MySqlDriver extends Drivers\MySqlDriver implements
    DatabaseDriverInterface,
    SQL\DatabaseDriverInterface
{
    use MockQueryConnectionTrait;
    use MockQueryDriverTrait;
    use MySqlDriverTrait;
}
