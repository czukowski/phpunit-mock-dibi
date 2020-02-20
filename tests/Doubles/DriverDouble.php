<?php
namespace Cz\PHPUnit\MockDibi\Doubles;

use Cz\PHPUnit\MockDibi\Drivers\DatabaseDriverInterface,
    Dibi\Driver;

/**
 * DriverDouble
 * 
 * @author   czukowski
 * @license  MIT License
 */
abstract class DriverDouble implements Driver, DatabaseDriverInterface
{
    // Test double to make up for PHPUnit inability to mock multiple interfaces.
}
