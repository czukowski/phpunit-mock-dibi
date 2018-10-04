<?php
namespace Cz\PHPUnit\MockDibi\Constraint;

use Cz\PHPUnit\SQL,
    Dibi\Connection;

/**
 * EqualsSQLQueriesConstraint
 * 
 * @author   czukowski
 * @license  MIT License
 */
class EqualsSQLQueriesConstraint extends SQL\EqualsSQLQueriesConstraint
{
    /**
     * @param  Connection  $connection
     */
    public function __construct(
        Connection $connection,
        $value,
        $delta = 0.0,
        $maxDepth = 10,
        $canonicalize = FALSE,
        $ignoreCase = FALSE
    ) {
        parent::__construct(
            array_map(
                function ($query) use ($connection) {
                    return $connection->translate($query);
                },
                $this->toArray($value)
            ),
            $delta,
            $maxDepth,
            $canonicalize,
            $ignoreCase
        );
    }
}
