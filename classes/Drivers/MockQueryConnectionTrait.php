<?php
namespace Cz\PHPUnit\MockDibi\Drivers;

/**
 * MockQueryConnectionTrait
 * 
 * @author   czukowski
 * @license  MIT License
 */
trait MockQueryConnectionTrait
{
    public function __construct()
    {
        // Empty implementation to prevent checking for installed extensions.
    }

    public function __destruct()
    {
        // Empty implementation to prevent clearing non-existing resources.
    }

    public function connect(array & $config)
    {
        // Empty implementation to prevent connection to real database service.
    }

    public function disconnect(): void
    {
        // Empty implementation to prevent closing non-existant connection.
    }
}
