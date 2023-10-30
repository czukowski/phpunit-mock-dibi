<?php
namespace Cz\PHPUnit\MockDibi\Drivers;

use Cz\PHPUnit\SQL,
    Dibi\Drivers,
    PDO,
    PDOStatement;

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
        $config = [
            'resource' => new PdoConnectionDouble($this->driverName),
        ];
        parent::__construct($config);
    }

    /**
     * @param  mixed  $savepoint
     */
    public function begin(string $savepoint = NULL): void
    {
        // Empty implementation.
    }

    /**
     * @param  mixed  $savepoint
     */
    public function commit(string $savepoint = NULL): void
    {
        // Empty implementation.
    }

    /**
     * @param  mixed  $savepoint
     */
    public function rollback(string $savepoint = NULL): void
    {
        // Empty implementation.
    }

    /**
     * @param   mixed  $resultSet
     * @return  PdoResult
     */
    public function createResultSet($resultSet)
    {
        // In order to avoid having to create `PDOStatement` double, do not call the
        // overridden `createResultDriver` method and create the result driver here.
        return new PdoResult($resultSet, $this->driverName);
    }

    /**
     * @throws  PdoResult
     */
    public function createResultDriver(/*PDOStatement*/ $result): Drivers\PdoResult
    {
        return new PdoResult($result, $this->driverName);
    }

    /**
     * @return  NULL
     */
    public function getResource(): ?PDO
    {
        // Do not throw exception here, method called by `Dibi\Connection` destructor.
        return NULL;
    }
}
