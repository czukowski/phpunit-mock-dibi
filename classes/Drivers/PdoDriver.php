<?php
namespace Cz\PHPUnit\MockDibi\Drivers;

use Cz\PHPUnit\SQL,
    Dibi\Drivers;

/**
 * PdoDriver
 * 
 * @author   czukowski
 * @license  MIT License
 */
class PdoDriver extends Drivers\PdoDriver implements
    DatabaseDriverInterface,
    SQL\DatabaseDriverInterface
{
    use MockQueryConnectionTrait;
    use MockQueryDriverTrait;

    /**
     * @var  string
     */
    private $driverName;

    /**
     * @param  string  $driverName
     */
    public function __construct($driverName)
    {
        $this->driverName = $driverName;
        // No calling parent constructor, it would fail if PDO extension is not enabled.
    }

    /**
     * @param  array  $config
     */
    public function connect(array & $config)
    {
        $config['resource'] = new PdoConnectionDouble($this->driverName);
        parent::connect($config);
    }

    /**
     * @param  mixed  $savepoint
     */
    public function begin($savepoint = NULL)
    {
        // Empty implementation.
    }

    /**
     * @param  mixed  $savepoint
     */
    public function commit($savepoint = NULL)
    {
        // Empty implementation.
    }

    /**
     * @param  mixed  $savepoint
     */
    public function rollback($savepoint = NULL)
    {
        // Empty implementation.
    }
}
