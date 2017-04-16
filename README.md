# DataAbstracter
This package is a wrapper around the PDO DBMS driver. It enables easy paramterisation of queries with one function call, aditionally it expoess CRUD operations.

## Install
Using composer
`composer require DavidFricker/DataAbstracter`

## Usage
```php
use DavidFricker\DataAbstracter\Adapter\MySQLDatabaseWrapper ;

$MySQLDatabaseWrapper = new MySQLDatabaseWrapper($dsn, $username, $password);
```

## API
### delete()
Remove one or more rows. As this is intended to be a simple helper function the only 'glue' to hold together the where clauses is 'AND' more complex delete statements should be performed using `run()`. 
#### Example
```php
$where = ['col_name' => 'expected_value'];
$limit = 10;
$MySQLDatabaseWrapper->delete('table_name', $where, $limit);
```
### update()
Update one or more rows. This is intended to be a simple helper function the only 'glue' to hold together the where clauses is 'AND' more complex update statements should be performed using `run()`.
#### Example
```php
$data = ['col_name' => 'new_value'];
$where = ['col_name' => 'expected_value'];
$limit = 10;
$MySQLDatabaseWrapper->update('table_name', $data, $where, $limit);
```
### fetch()
Pull one or more rows. As this is intended to be a simple helper function the only 'glue' to hold together the where clauses is 'AND' more complex update statements should be performed using `run()`. IMPORTANT: Ensure the $columns variable does not contain user input as it is inserted as-is into the statement and as so creates an SQL injection vulnerable. 
#### Example
```php
$columns = ['col_name_1', 'col_name_2'];
$where = ['col_name' => 'expected_value'];
$limit = 10;
$MySQLDatabaseWrapper->fetch('table_name', $columns, $where, $limit);
```
### insert()
Create a new row.
#### Example
```php
$data = ['col_name_1' => 'new_value', 'col_name_2' => 'new_value'];
$MySQLDatabaseWrapper->insert('table_name', $data);
```
### run()
Execute any SQL query. To ensure your query is safe from SQL injection attacks pass all values via the `$bind` array.
#### Example
```php
$new_record = ['col_name_1' => 'new_value', 'col_name_2' => 'new_value'];
$MySQLDatabaseWrapper->insert('table_name', $new_record);
```
### rowCount()
Fetch the number of rows returned from the last query performed.
#### Example
```php
$new_record = ['col_name_1' => 'new_value', 'col_name_2' => 'new_value'];
$MySQLDatabaseWrapper->insert('table_name', $new_record);
```
## Bugs
Please report bugs by creating an issue and supply code to replicate the issue. Code contributions are always welcome.

## Licence 
Released under the MIT licenses.
