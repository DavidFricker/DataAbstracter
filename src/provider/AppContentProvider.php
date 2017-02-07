<?php
namespace DavidFricker\DataAbstracter\Provider;

use DavidFricker\DataAbstracter\Interfaces\InterfaceContentProvider;
use DavidFricker\DataAbstracter\Adapter\MySQLDatabaseWrapper;
use DavidFricker\DataAbstracter\Contract\ContentContract;

// connection should be made in constructer
class AppContentProvider implements InterfaceContentProvider 
{
    private $dsn = 'mysql:host=localhost;port=3306;dbname=apisaas';
    private $username = 'root';
    private $password = '';
    private static $db_adapter;
    private $ContentContract;

    private $options = array(
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_PERSISTENT => true,
                \PDO::ATTR_EMULATE_PREPARES => false
            );

     public static function init() {
        static $instance = null;

        if ($instance === null) {
            $instance = new AppContentProvider();
        }

        return $instance;
    }

    /**
     * Set a private __construct to stop instaces being created leading to more than one instance of this class
     */
    private function __construct() {
        $this->db_adapter = new MySQLDatabaseWrapper($this->dsn, $this->username, $this->password, $this->options);
        $this->ContentContract = new ContentContract();
    }

    /*
        NB: As this is intented to be a simple helper function the only 'glue' to hold togther the where clasues is 'AND' 
        more complex update statments should be performed using run()
        DO NOT use a client supplied number for limit as it is vulnerable to SQL injection
     */
    public function delete($table, $where=false, $limit=false) {
        var_dump($table, $where);
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

    /*
        NB: As this is intented to be a simple helper function the only 'glue' to hold togther the where clasues is 'AND' 
        more complex update statments should be performed using run()
     */
    public function update($table, $data, $where=false, $limit=false) {
        if (!$this->validateTableAndCol($table, array_keys($data))) {
            throw new \InvalidArgumentException ('Table or coloumn information is incorrect acorrding to the contract');
        }

        if ($data && !$this->validateBind($table, $data)) {
            throw new \InvalidArgumentException ('Input data is incorrect acorrding to the contract');
        }

        $result = $this->db_adapter->update($table, $data, $where, $limit);
        if (!$result) {
            throw new \InvalidArgumentException ($this->db_adapter->getLastError());
        }   

        return $result;   
    }

    /*
        Important: Ensure the $columns var does not contain user input as it is inserted as-is into the statement - vulnrable to SQL injection 
        NB: As this is intented to be a simple helper function the only 'glue' to hold togther the where clasues is 'AND' 
        more complex update statments should be performed using run()
     */
    public function fetch($table, $columns, $where=false, $limit=false) {
        if (!$this->validateTableAndCol($table, $columns) ) { //|| !$this->validateTableAndCol($table, array_keys($where))
            throw new \InvalidArgumentException ('Table or coloumn information is incorrect acorrding to the contract');
        }

        if ($where && !$this->validateBind($table, $where)) {
            throw new \InvalidArgumentException ('Input data is incorrect acorrding to the contract');
        }

        $result = $this->db_adapter->fetch($table, $columns, $where, $limit);
        if (!$result) {
            throw new \InvalidArgumentException ($this->db_adapter->getLastError());
        }   

        return $result;
    }

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
    
    public function run($query, $bind=[]) {
        return $this->db_adapter->run($query, $bind);
    }

    public function getLastInsertID() {
        return $this->db_adapter->getLastInsertID();
    }

    public function rowCount() {
        return $this->db_adapter->rowCount();
    }

    private function validateWhereClause($table, $where) {
        foreach ($where as $column => $value) {
            if (!$this->isInputValid($table, $column, $value)) {
                return false;
            }
        }

        return true;
    }

    public function isValidInput ($table, $column, $value) {
        $table_object = $this->ContentContract->tables[$table];
        $column_type_validator = $table_object::SCHEMA[$column];

        return $this->ContentContract->validators[$column_type_validator]($value);
    }

    public function isValidTable ($table) {
        return isset($this->ContentContract->tables[$table]);
    }

    // assumes a isValidTable() call with be made prioir so it may skip it and save many redudnant if statements
    public function isValidColumn ($table, $column) {
        $table_object = ($this->ContentContract->tables[$table]);
        $schema = $table_object::SCHEMA;
        return isset($schema[$column]);
    }

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

    private function validateBind($table, $data) {
        foreach ($data as $column => $value) {
            if (!$this->isValidInput($table, $column, $value)) {
                return false;
            }
        }
        
        return true;
    }
}
