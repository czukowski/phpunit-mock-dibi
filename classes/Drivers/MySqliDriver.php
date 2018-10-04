<?php
namespace Cz\PHPUnit\MockDibi\Drivers;

use Cz\PHPUnit\SQL,
    Dibi\Drivers;

/**
 * MySqliDriver
 * 
 * @author   czukowski
 * @license  MIT License
 */
class MySqliDriver extends Drivers\MySqliDriver implements
    DatabaseDriverInterface,
    SQL\DatabaseDriverInterface
{
    use MockQueryConnectionTrait;
    use MockQueryDriverTrait;
    use MySqlDriverTrait;
}
