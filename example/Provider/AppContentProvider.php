<?php

namespace DavidFricker\DataAbstracter\Example\Provider;

use DavidFricker\DataAbstracter\Interfaces\InterfaceContentProvider;
use DavidFricker\DataAbstracter\Adapter\MySQLDatabaseWrapper;
use DavidFricker\DataAbstracter\Example\Contract\ContentContract;

/**
 * A wrapper around any given database adapter
 *
 * Provides an abstraction and error checking with the use of table content contracts
 */
class AppContentProvider implements InterfaceContentProvider 
{
    private $dsn = 'mysql:host=127.0.0.1;port=3306;dbname=myDatabase';
    private $username = 'root';
    private $password = '';
    private $db_adapter;
    private $ContentContract;

    private $options = [
                //\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_PERSISTENT => true,
                \PDO::ATTR_EMULATE_PREPARES => false
            ];

    /**
     * Create/fetch an instance of the class
     *
     * This class is singleton, this method is the only path to get an instance of this class.
     * 
     * @return AppContentProvider an instance of the AppContentProvider class
     */
     public static function init() {
        static $instance = null;

        if ($instance === null) {
            $instance = new AppContentProvider();
        }

        return $instance;
    }

    /**
     * private __construct to stop instaces being created leading to more than one instance of this class
     */
    private function __construct() {
        $this->db_adapter = new MySQLDatabaseWrapper($this->dsn, $this->username, $this->password, $this->options);
        $this->ContentContract = new ContentContract();
    }

    /**
     * Remove one or more rows
     *
     * As this is intended to be a simple helper function the only 'glue' to hold together the where clauses is 'AND' more complex delete statements should be performed using run()
     * 
     * @param  string $table name of table in the database
     * @param  array  $where optional, key:value pairs - column and expected value to filter by 
     * @param  boolean $limit optional, integer describing the amount of matching rows to delete
     * @return mixed see return value of run
     */
    public function delete($table, $where=false, $limit=false) {
        if (!$this->validateTableAndCol($table, array_keys($where))) {
            throw new \InvalidArgumentException ('Table or coloumn information is incorrect acorrding to the contract');
        }

        if ($where && !$this->validateBind($table, $where)) {
            throw new \InvalidArgumentException ('Input data is incorrect acorrding to the contract');
        }

        $result = $this->db_adapter->delete($table, $where, $limit);
        if (!$result) {
            throw new \InvalidArgumentException ($this->db_adapter->getLastError());
        }   

        return $result;
    }

    /**
     * Update one or more rows
     *
     * As this is intended to be a simple helper function the only 'glue' to hold together the where clauses is 'AND' more complex update statements should be performed using run()
     * 
     * @param  string $table name of table in the database
     * @param  array $data  key:value pairs, key is the column and value is the new value for each affected row
     * @param  array $where optional, key:value pairs - column and expected value to filter by 
     * @param  boolean $limit optional, integer describing the amount of matching rows to update
     * @return mixed see return value of run
     */
    public function update($table, $data, $where=false, $limit=false) {
        if (!$this->validateTableAndCol($table, array_keys($data))) {
            throw new \InvalidArgumentException ('Table or coloumn information is incorrect acorrding to the contract');
        }

        if ($data && !$this->validateBind($table, $data)) {
            throw new \InvalidArgumentException ('Input data is incorrect acorrding to the contract');
        }

        $result = $this->db_adapter->update($table, $data, $where, $limit);
        if ($result === false) {
            throw new \InvalidArgumentException ($this->db_adapter->getLastError());
        }   

        return $result;   
    }

    /**
     * Pull one or more rows
     *
     * As this is intended to be a simple helper function the only 'glue' to hold together the where clauses is 'AND' more complex update statements should be performed using run()
     * IMPORTANT: Ensure the $columns variable does not contain user input as it is inserted as-is into the statement - vulnerable to SQL injection 
     * 
     * @param  string $table name of table in the database
     * @param  array $data  key:value pairs, key is the column and value is the new value for each affected row
     * @param  array $where optional, key:value pairs - column and expected value to filter by 
     * @param  boolean $limit optional, integer describing the amount of matching rows to fetch
     * @return mixed see return value of run
     */
    public function fetch($table, $columns, $where=false, $limit=false) {
        if (!$this->validateTableAndCol($table, $columns) ) { //|| !$this->validateTableAndCol($table, array_keys($where))
            throw new \InvalidArgumentException ('Table or coloumn information is incorrect acorrding to the contract');
        }

        if ($where && !$this->validateBind($table, $where)) {
            throw new \InvalidArgumentException ('Input data is incorrect acorrding to the contract');
        }

        $result = $this->db_adapter->fetch($table, $columns, $where, $limit);
        if ($result === false) {
            throw new \InvalidArgumentException ($this->db_adapter->getLastError());
        }   

        return $result;
    }

    /**
     * Create a new row
     * 
     * @param  string $table name of table in the database
     * @param  array $data  key:value pairs, key is the column and value is the value to set for the new row
     * @return mixed see return value of run
     */
    public function insert($table, $data) {
        if (!$this->validateTableAndCol($table, array_keys($data))) {
            throw new \InvalidArgumentException ('Table or coloumn information is incorrect acorrding to the contract');
        }

        if ($data && !$this->validateBind($table, $data)) {
            throw new \InvalidArgumentException ('Input data is incorrect acorrding to the contract');
        }

        $result = $this->db_adapter->insert($table, $data);
        if (!$result) {
            throw new \InvalidArgumentException ($this->db_adapter->getLastError());
        }   

        return $result;
    }

    public function exists($table, $column, $value) {
        $result = $this->fetch($table, [$column], [$column => $value], 1);
        return ($result && count($result) == 1);
    }
    
    /**
     * Execute any SQL query
     *
     * To ensure your query is safe from first order SQL injection attacks pass all values via the $bind array
     * 
     * @param  string $query MySQL query
     * @param  [type] $bind  key:value pairs where the key is a bind identifier and value is to be inserted at that location
     * @return mixed see example
     * @example depending on the type of input query the returned result can be an affected row count or a result set, the type of which is specified in the options passed to the constructor, defaulting to an assoc array
     * @example $query = 'SELECT * FROM table_name WHERE col_id = :BindColID'; $bind = [':BindColID' => 12];
     */
    public function run($query, $bind=[]) {
        return $this->db_adapter->run($query, $bind);
    }

    /**
     * Fetches the Row ID for the last inserted record
     * 
     * @return int Returns the integer ID of the last inserted row
     */
    public function getLastInsertID() {
        return $this->db_adapter->getLastInsertID();
    }

    /**
     * Fetch the number of rows returned from previous query
     * 
     * @return int affected row count of last query
     */
    public function rowCount() {
        return $this->db_adapter->rowCount();
    }

    /**
     * Validate WHERE binding for SQL statement
     *
     * Checks the type of supplied values for each column against the content contract
     * 
     * @param  string $table table name, used to fetch the content contract
     * @param  array $where [description]
     * @return boolean true if where clause is valid
     */
    private function validateWhereClause($table, $where) {
        foreach ($where as $column => $value) {
            if (!$this->isInputValid($table, $column, $value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check value against given filter
     *
     * Checks the type of the supplied value against the validate method supplied in the content contract for the table
     * 
     * @param  string  $table  table name, used to fetch the content contract
     * @param  [type]  $column [description]
     * @param  [type]  $value  [description]
     * @return boolean true if input is valid for the given column
     */
    public function isValidInput ($table, $column, $value) {
        $table_object = $this->ContentContract->tables[$table];
        $column_type_validator = $table_object::SCHEMA[$column];

        return $this->ContentContract->validators[$column_type_validator]($value);
    }

    /**
     * Validates table exists in database
     *
     * Uses the master table content contract to check for the tables existence
     * 
     * @param  string  $table table name to validate
     * @return boolean true if the table name is found in the contract
     */
    public function isValidTable ($table) {
        return isset($this->ContentContract->tables[$table]);
    }

    /**
     * Checks a column exists in a given table
     *
     * Uses the specific table content contract to check for the columns existence
     * Assumes a isValidTable() call with be made prior so it may skip it and save many redundant if statements
     * 
     * @param  string  $table  table name, used to fetch the content contract
     * @param  [type]  $column [description]
     * @return boolean true if the column is found in the table contract
     */
    public function isValidColumn ($table, $column) {
        $table_object = ($this->ContentContract->tables[$table]);
        $schema = $table_object::SCHEMA;
        return isset($schema[$column]);
    }

    /**
     * Wrapper function to validate a table and columns
     *
     * @param  string $table   table name, used to fetch the content contract
     * @param  array $columns [description]
     * @return boolean true if the table and column names are correct
     */
    private function validateTableAndCol ($table, $columns) {
        if (!$this->isValidTable($table)) {
            return false;
        }
        
        foreach ($columns as $column) {
            if (!$this->isValidColumn($table, $column)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate a set of columns and values
     *
     * Wraps isValidInput to check an array of column and values against the table content contracts
     * 
     * @param  string $table table name, used to fetch the content contract
     * @param  array $data  [description]
     * @return boolean true if the input values in $data are valid for their given columns
     */
    private function validateBind($table, $data) {
        foreach ($data as $column => $value) {
            if (!$this->isValidInput($table, $column, $value)) {
                return false;
            }
        }
        
        return true;
    }
}
