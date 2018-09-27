Dibi mocking helpers for PHPUnit
================================

[![Build Status](https://travis-ci.org/czukowski/phpunit-mock-dibi.svg?branch=dibi-4)](https://travis-ci.org/czukowski/phpunit-mock-dibi)

A mock-object library for database queries testing, without having to initialize in-memory
database from fixtures. Rather, every query executed by a tested code can be set to return
a pre-defined result set, affected rows count or last insert ID. All with a familiar interface
similar to PHPUnit Mock Objects.

This is an adapted version of [czukowski/phpunit-mock-db] package for uses of [Dibi - smart database
layer for PHP][dibi] by David Grudl. Dibi is a database connection library that includes a powerful
query builder and supports a variety of relational database systems.

Installation
------------

Pick your version! Version numbering follows Dibi version numbers, so for a given Dibi N.x
version, the installation command would look like this:

```sh
composer require --dev czukowski/phpunit-mock-dibi "~N.0"
```

Dibi versions starting from 3.0 using PHP 7 are supported.

Usage
-----

Use `Cz\PHPUnit\MockDibi\MockTrait` trait in a test case class, this will enable methods for
creating database mock instances. Just call `createDatabaseMock` method passing a `Dibi\Connection`
instance that uses one of the mock drivers provided in this package, and a mock object will be added
to it and registered to the test case class.

There is a handy factory class for mock Dibi drivers in `Cz\PHPUnit\MockDibi\Drivers\DriversFactory`
so you don't have to create driver instances yourself (see its source code for methods available).

### Example how to create mock Dibi Connection:

```php
$dibi = new Dibi\Connection([
    'driver' => $factory->createMySqlDriver()  // or whatever other driver you may be needing.
]);
```

### Examples how to set up expected queries and mock results:

Return a pre-defined result set on _any_ database query:

```php
$this->createDatabaseMock($dibi)
    ->expects($this->any())
    ->willReturnResultSet([
        ['id' => 1, 'name' => 'foo'],
        ['id' => 2, 'name' => 'bar'],
    ]);
```

Return a pre-defined result set on _any_ database query and expect it to be executed exactly once:

```php
$this->createDatabaseMock($dibi)
    ->expects($this->once())
    ->willReturnResultSet([
        ['id' => 1, 'name' => 'foo'],
        ['id' => 2, 'name' => 'bar'],
    ]);
```

Return a pre-defined result set on each specific database query, expecting each query to be executed
exactly once:

_Note_: the order in which the query expectations are being set up doesn't have to be same as the order
in which the queries will be executed.

_Also note_: the whitespaces will be ignored in query constraints, so they can be loaded from well-formatted
files, which could be especially useful for long and complex queries.

_Also note_: different Dibi drivers may use different characters to quote identifiers, eg. backticks for
MySQL or square brackets for MS SQL Server, etc. Query constraints will use Dibi Translator to transform
queries so there's no need to take extra care to have identifiers quotes matching the currently used driver.

```php
$mock = $this->createDatabaseMock($dibi);
$mock->expects($this->once())
    ->query('SELECT * FROM `t1`')
    ->willReturnResultSet([['id' => 1, 'name' => 'foo']]);
$mock->expects($this->once())
    ->query('SELECT * FROM `t2`')
    ->willReturnResultSet([['id' => 2, 'name' => 'bar']]);
```

Expect mixed queries, some at specific invocations (note: SELECT query is set to return an empty
result set):

```php
$mock = $this->createDatabaseMock($dibi);
$mock->expects($this->at(1))
    ->query('INSERT INTO `t1` VALUES (1, "foo")')
    ->willSetLastInsertId(1);
$mock->expects($this->at(2))
    ->query('INSERT INTO `t1` VALUES (2, "bar")')
    ->willSetLastInsertId(2);
$mock->expects($this->once())
    ->query('SELECT * FROM `t1`')
    ->willReturnResultSet([]);
```

Expect same query executed exactly three times and return different last insert IDs on each
consecutive call:

```php
$this->createDatabaseMock($dibi)
    ->expects($this->exactly(3))
    ->query('INSERT INTO `t1` VALUES ("a", "b", "c")')
    ->willSetLastInsertId(1, 2, 3);
```

Return affected rows count:

```php
$this->createDatabaseMock($dibi)
    ->expects($this->exactly(2))
    ->query('UPDATE `t1` SET `foo` = "bar" WHERE `id` = 1')
    ->willSetAffectedRows(1);
```

Match SQL query using PHPUnit constraint (note: whitespace will not be ignored when using default
PHPUnit constraints):

```php
$this->createDatabaseMock($dibi)
    ->expects($this->once())
    ->query($this->stringStartsWith('SELECT'))
    ->willReturnResultSet([['id' => 1, 'name' => 'foo']]);
```

Set up different outcomes on consecutive calls for INSERT queries using a consecutive calls stub
builder:

```php
$this->createDatabaseMock($dibi)
    ->expects($this->exactly(4))
    ->query($this->stringStartsWith('INSERT'))
    ->onConsecutiveCalls()
    ->willSetLastInsertId(1)
    ->willSetLastInsertId(2)
    ->willThrowException(new RuntimeException('Deadlock'))
    ->willSetLastInsertId(3);
```

Although not normally needed, it is possible to set up custom callbacks to handle database queries
(callbacks don't have to return anything):

```php
$mock = $this->createDatabaseMock($dibi);
$mock->expects($this->any())
    ->query($this->stringStartsWith('INSERT'))
    ->willInvokeCallback(function ($invocation) {
        $invocation->setLastInsertId(1);
    });
$mock->expects($this->any())
    ->query($this->stringStartsWith('UPDATE'))
    ->willInvokeCallback(function ($invocation) {
        $invocation->setAffectedRows(0);
    });
$mock->expects($this->any())
    ->query($this->stringStartsWith('SELECT'))
    ->willInvokeCallback(function ($invocation) {
        $invocation->setResultSet([]);
    });
```

By default, mock object is set to throw an exception if an unknown (unmatched) query is executed,
but this can be disabled:

```php
$mock = $this->createDatabaseMock($dibi);
$mock->setRequireMatch(FALSE);
```

License
-------

This work is released under the MIT License. See LICENSE.md for details.

 [czukowski/phpunit-mock-db]: https://packagist.org/packages/czukowski/phpunit-mock-db
 [dibi]: https://github.com/dg/dibi
