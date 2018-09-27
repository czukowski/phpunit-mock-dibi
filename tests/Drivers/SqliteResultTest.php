<?php
namespace Cz\PHPUnit\MockDibi\Drivers;

use Dibi\NotSupportedException;

/**
 * SqliteResultTest
 * 
 * @author   czukowski
 * @license  MIT License
 */
class SqliteResultTest extends Testcase
{
    /**
     * @dataProvider  provideUnsupportedMethods
     */
    public function testUnsupportedMethods($method, $arguments)
    {
        $object = $this->createObject();
        $this->expectException(NotSupportedException::class);
        call_user_func_array([$object, $method], $arguments);
    }

    public function provideUnsupportedMethods()
    {
        return [
            ['getResultResource', []],
            ['getRowCount', []],
            ['seek', [0]],
        ];
    }

    /**
     * @return  SqliteResult
     */
    private function createObject()
    {
        return new SqliteResult(NULL);
    }
}
