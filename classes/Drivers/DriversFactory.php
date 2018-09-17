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
     * @return  MsSqlDriver
     */
    public function createMsSqlDriver()
    {
        return new MsSqlDriver;
    }

    /**
     * @return  MySqlDriver
     */
    public function createMySqlDriver()
    {
        return new MySqlDriver;
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
     * @param   string  $fmtDate
     * @param   string  $fmtDateTime
     * @return  Sqlite3Driver
     */
    public function createSqlite3Driver(string $fmtDate = 'U', string $fmtDateTime = 'U')
    {
        $driver = new Sqlite3Driver;
        $driver->setDateTimeFormats($fmtDate, $fmtDateTime);
        return $driver;
    }

    /**
     * @return  SqlsrvDriver
     */
    public function createSqlsrvDriver()
    {
        return new SqlsrvDriver;
    }
}
