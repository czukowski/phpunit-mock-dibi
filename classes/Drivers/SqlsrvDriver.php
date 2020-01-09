<?php
namespace Cz\PHPUnit\MockDibi\Drivers;

use Cz\PHPUnit\SQL,
    Dibi\Drivers,
    ReflectionProperty;

/**
 * SqlsrvDriver
 * 
 * @author   czukowski
 * @license  MIT License
 */
class SqlsrvDriver extends Drivers\SqlsrvDriver implements
    DatabaseDriverInterface,
    SQL\DatabaseDriverInterface
{
    use MockQueryConnectionTrait;
    use MockQueryDriverTrait;

    private const DEFAULT_VERSION = '11';

    /**
     * @param  array  $config
     */
    public function connect(array & $config)
    {
        $version = isset($config['version']) ? $config['version'] : self::DEFAULT_VERSION;
        $this->setVersion($version);
    }

    /**
     * @param   mixed  $sequence
     * @return  mixed
     */
    public function getInsertId($sequence)
    {
        $this->addExecutedQuery('SELECT SCOPE_IDENTITY()');
        return $this->lastInsertId ? : FALSE;
    }

    /**
     * @param  mixed  $savepoint
     */
    public function begin($savepoint = NULL)
    {
        // Do nothing.
    }

    /**
     * @param  mixed  $savepoint
     */
    public function commit($savepoint = NULL)
    {
        // Do nothing.
    }

    /**
     * @param  mixed  $savepoint
     */
    public function rollback($savepoint = NULL)
    {
        // Do nothing.
    }

    /**
     * @param  string  $value
     */
    public function setVersion($value)
    {
        $version = new ReflectionProperty(Drivers\SqlsrvDriver::class, 'version');
        $version->setAccessible(TRUE);
        $version->setValue($this, $value);
    }
}
