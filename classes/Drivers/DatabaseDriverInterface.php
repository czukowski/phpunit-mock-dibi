<?php
namespace Cz\PHPUnit\MockDibi\Drivers;

use Cz\PHPUnit\MockDB\DatabaseDriverInterface as OriginalDatabaseDriverInterface,
    Cz\PHPUnit\MockDB\Mock;

/**
 * DatabaseDriverInterface
 * 
 * @author   czukowski
 * @license  MIT License
 */
interface DatabaseDriverInterface extends OriginalDatabaseDriverInterface
{
    /**
     * @return  Mock
     */
    function getMockObject();
}
