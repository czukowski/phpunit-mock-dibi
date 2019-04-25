<?php
namespace Cz\PHPUnit\MockDibi\Builder;

use Cz\PHPUnit\MockDibi\Constraint\EqualsSQLQueriesConstraint,
    Cz\PHPUnit\MockDB\Builder\InvocationMocker as OriginalInvocationMocker,
    Cz\PHPUnit\MockDB\Matcher\RecordedInvocation,
    Cz\PHPUnit\MockDB\Stub\MatcherCollection,
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
     * @param  MatcherCollection   $collection
     * @param  RecordedInvocation  $invocationMatcher
     * @param  Connection          $connection
     */
    public function __construct(
        MatcherCollection $collection,
        RecordedInvocation $invocationMatcher,
        Connection $connection
    ) {
        parent::__construct($collection, $invocationMatcher);
        $this->connection = $connection;
    }

    /**
     * @param   Constraint|string  $constraint
     * @return  $this
     */
    public function query($constraint): parent
    {
        if (is_string($constraint)) {
            $constraint = new EqualsSQLQueriesConstraint($this->connection, $constraint);
        }
        return parent::query($constraint);
    }
}
