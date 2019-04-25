<?php
namespace Cz\PHPUnit\MockDibi;

use Cz\PHPUnit\MockDB,
    Dibi\Connection;

/**
 * Mock
 * 
 * @author   czukowski
 * @license  MIT License
 */
class Mock extends MockDB\Mock
{
    /**
     * @var  Connection
     */
    private $connection;
    /**
     * @var  InvocationMocker
     */
    private $invocationMocker;

    /**
     * @param  Connection  $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return  InvocationMocker
     */
    public function getInvocationMocker(): MockDB\InvocationMocker
    {
        if ($this->invocationMocker === NULL) {
            $this->invocationMocker = new InvocationMocker($this->connection);
        }
        return $this->invocationMocker;
    }

    /**
     * @return  void
     */
    public function unsetInvocationMocker(): void
    {
        $this->invocationMocker = NULL;
    }
}
