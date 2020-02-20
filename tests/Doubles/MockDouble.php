<?php
namespace Cz\PHPUnit\MockDibi\Doubles;

use Cz\PHPUnit\MockDB\Invocation,
    Cz\PHPUnit\MockDB\Mock;

/**
 * MockDouble
 * 
 * @author   czukowski
 * @license  MIT License
 */
class MockDouble extends Mock
{
    /**
     * @var  Invocation
     */
    public $invoked;

    /**
     * Note: no strict type check here.
     * 
     * @param   Invocation  $invocation
     * @param   array       $parameters
     * @return  Invocation
     */
    public function invoke($invocation, array $parameters = []): Invocation
    {
        $this->invoked = $invocation;
        return $invocation;
    }
}
