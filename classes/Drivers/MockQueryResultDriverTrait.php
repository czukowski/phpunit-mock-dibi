<?php
namespace Cz\PHPUnit\MockDibi\Drivers;

use Dibi\NotSupportedException;

/**
 * MockQueryResultDriverTrait
 * 
 * Mock implementation of `Dibi\ResultDriver` interface. Mock drivers may use it as a base while
 * extending the actual driver class and override some of the methods where needed.
 * 
 * @author   czukowski
 * @license  MIT License
 */
trait MockQueryResultDriverTrait
{
    /**
     * @var  mixed
     */
    private $cursor;
    /**
     * @var  mixed
     */
    private $resultSet;

    /**
     * @param  mixed  $resultSet
     */
    public function __construct($resultSet)
    {
        $this->setResultResource($resultSet);
    }

    public function __destruct()
    {
        // Empty implementation to prevent clearing non-existing resources.
    }

    /**
     * @return  integer
     */
    public function getRowCount(): int
    {
        return $this->resultSet !== NULL
            ? count($this->resultSet)
            : 0;
    }

    /**
     * @param   integer  $row
     * @return  boolean
     */
    public function seek(int $row): bool
    {
        if (isset($this->resultSet[$row])) {
            $this->cursor = $row;
            return TRUE;
        }
        $this->cursor = NULL;
        return FALSE;
    }

    /**
     * @param   bool  $assoc
     * @return  array|NULL
     */
    public function fetch(bool $assoc): ?array
    {
        if ($this->cursor === NULL) {
            $this->cursor = 0;
        }
        if (isset($this->resultSet[$this->cursor])) {
            $row = $this->resultSet[$this->cursor];
            $this->cursor++;
            return $assoc ? $row : array_values($row);
        }
        return NULL;
    }

    /**
     * @return  void
     */
    public function free(): void
    {
        $this->cursor = NULL;
        $this->resultSet = NULL;
    }

    /**
     * @throws  NotSupportedException
     */
    public function getResultColumns(): array
    {
        throw new NotSupportedException('Native data types cannot be determined for mock DB connection');
    }

    /**
     * @return  mixed
     */
    public function getResultResource()
    {
        return $this->resultSet;
    }

    /**
     * @param  mixed  $resultSet
     */
    public function setResultResource($resultSet)
    {
        $this->resultSet = $resultSet;
    }

    /**
     * @return  integer|NULL
     */
    public function getCursor()
    {
        return $this->cursor;
    }

    /**
     * @param  mixed  $value
     */
    public function setCursor($value)
    {
        $this->cursor = $value;
    }

    /**
     * @param   string  $value
     * @return  string
     */
    abstract public function unescapeBinary(string $value): string;
}
