<?php
namespace Cz\PHPUnit\MockDibi\Drivers;

/**
 * DriversFactory
 * 
 * @author   czukowski
 * @license  MIT License
 */
class DriversFactory
{
    /**
     * @return  FirebirdDriver
     */
    public function createFirebirdDriver()
    {
        return new FirebirdDriver;
    }

    /**
     * @return  MySqliDriver
     */
    public function createMySqliDriver()
    {
        return new MySqliDriver;
    }

    /**
     * @return  OdbcDriver
     */
    public function createOdbcDriver()
    {
        return new OdbcDriver;
    }

    /**
     * @param   boolean  $nativeDate
     * @return  OracleDriver
     */
    public function createOracleDriver(bool $nativeDate = TRUE)
    {
        return new OracleDriver([
            'nativeDate' => $nativeDate,
        ]);
    }

    /**
     * @param   string  $driverName
     * @return  PdoDriver
     */
    public function createPdoDriver($driverName)
    {
        return new PdoDriver($driverName);
    }

    /**
     * @return  PdoDriver
     */
    public function createPdoDblibDriver()
    {
        return $this->createPdoDriver('dblib');
    }

    /**
     * @return  PdoDriver
     */
    public function createPdoMysqlDriver()
    {
        return $this->createPdoDriver('mysql');
    }

    /**
     * @return  PdoDriver
     */
    public function createPdoMssqlDriver()
    {
        return $this->createPdoDriver('mssql');
    }

    /**
     * @return  PdoDriver
     */
    public function createPdoOciDriver()
    {
        return $this->createPdoDriver('oci');
    }

    /**
     * @return  PdoDriver
     */
    public function createPdoOdbcDriver()
    {
        return $this->createPdoDriver('odbc');
    }

    /**
     * @return  PdoDriver
     */
    public function createPdoPgsqlDriver()
    {
        return $this->createPdoDriver('pgsql');
    }

    /**
     * @return  PdoDriver
     */
    public function createPdoSqliteDriver()
    {
        return $this->createPdoDriver('sqlite');
    }

    /**
     * @return  PdoDriver
     */
    public function createPdoSqlsrvDriver()
    {
        return $this->createPdoDriver('sqlsrv');
    }

    /**
     * @return  PostgreDriver
     */
    public function createPostgreDriver()
    {
        return new PostgreDriver;
    }

    /**
     * @param   string  $formatDate
     * @param   string  $formatDateTime
     * @return  SqliteDriver
     */
    public function createSqliteDriver(string $formatDate = 'U', string $formatDateTime = 'U')
    {
        return new SqliteDriver([
            'formatDate' => $formatDate,
            'formatDateTime' => $formatDateTime,
        ]);
    }

    /**
     * @param   string  $formatDate
     * @param   string  $formatDateTime
     * @return  Sqlite3Driver
     */
    public function createSqlite3Driver(string $formatDate = 'U', string $formatDateTime = 'U')
    {
        return new Sqlite3Driver([
            'formatDate' => $formatDate,
            'formatDateTime' => $formatDateTime,
        ]);
    }

    /**
     * @return  SqlsrvDriver
     */
    public function createSqlsrvDriver(string $version = '11')
    {
        return new SqlsrvDriver([
            'version' => $version,
        ]);
    }
}
