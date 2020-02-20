<?php
namespace Cz\PHPUnit\MockDibi;

use Cz\PHPUnit\MockDibi\Builder\InvocationMocker as InvocationMockerBuilder,
    Cz\PHPUnit\MockDB\Builder\InvocationMocker as MockDBInvocationMockerBuilder,
    Cz\PHPUnit\MockDB\InvocationMocker as OriginalInvocationMocker,
    Cz\PHPUnit\MockDB\Matcher\RecordedInvocation,
    Dibi\Connection;

/**
 * InvocationMocker
 * 
 * @author   czukowski
 * @license  MIT License
 */
class InvocationMocker extends OriginalInvocationMocker
{
    /**
     * @var  Connection
     */
    private $connection;

    /**
     * @param  Connection  $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param   RecordedInvocation  $matcher
     * @return  InvocationMockerBuilder
     */
    public function expects(RecordedInvocation $matcher): MockDBInvocationMockerBuilder
    {
        return new InvocationMockerBuilder($this, $matcher, $this->connection);
    }
}
