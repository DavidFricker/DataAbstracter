<?php
namespace \DavidFricker\DataAbstracter\ContentProvider;

// connection should be made in constructer
class AppContentProvider implements InterfaceContentProvider {
    private $dsn = 'mysql:host=localhost;port=3306;dbname=apisaas';
    private $username = 'root';
    private $password = '';
    private static $db_adapter;

    private $options = array(
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_EMULATE_PREPARES => false
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
    }

    /*
        NB: As this is intented to be a simple helper function the only 'glue' to hold togther the where clasues is 'AND' 
        more complex update statments should be performed using run()
     */
    public function delete($table, $where, $limit) {
        if (!$this->validateTableAndCol($table, array_keys($where))) {
            return false;
        }

        $result = $this->db_adapter->delete($table, $where, $limit);
        if (!$result) {
            // throw exception
            $this->db_adapter->getLastError();
        }
    }

    /*
        NB: As this is intented to be a simple helper function the only 'glue' to hold togther the where clasues is 'AND' 
        more complex update statments should be performed using run()
     */
    public function update($table, $data, $where, $limit=false) {
        if (!$this->validateTableAndCol($table, array_keys($where))) {
            return false;
        }
       
    }

    /*
        Important: Ensure the $columns var does not contain user input as it is inserted as-is into the statement - vulnrable to SQL injection 
        NB: As this is intented to be a simple helper function the only 'glue' to hold togther the where clasues is 'AND' 
        more complex update statments should be performed using run()
     */
    public function fetch($table, $columns, $where=false, $limit=false) {
        if (!$this->validateTableAndCol($table, array_keys($where))) {
            return false;
        }

        
    }

    public function insert($table, $data) {
        if (!$this->validateTableAndCol($table, array_keys($where))) {
            return false;
        }      
    }
    
    public function run($query, $bind=[]) {
        
    }

    public function getLastInsertID() {
        
    }

    public function rowCount() {
        return $this->handle->rowCount();
    }

    private function validateTableAndCol($table, $columns) {
        if(!$this->isValidTable($table)) {
            return false;
        }
        
        foreach ($columns as $column) {
            if (!$this->isValidColumn($table, $column)) {
                return false;
            }
        }
    }

    private function validateWhereClause($table, $where) {
        foreach ($where as $column => $value) {
            if (!$this->isInputValid($table, $column, $value)) {
                return false;
            }
        }

        return true;
    }

    public function isInputValid($table, $column, $value) {
        $table_object = $ContentContract->tables[$table]
        $column_type_validator = $table_object::TYPE[$column];

        return $column_type_validator($value);
    }

    public function isValidTable($table) {
        $tables = $ContentContract->tables;
        return isset($temp[$table]);
    }

    // assumes a isValidTable() call with be made prioir so it may skip it and save many redudnant if statements
    public function isValidColumn($table, $column) {
        $table_object = $ContentContract->tables[$table]
        return defined(get_class($table).'::COL_'.strtoupper($column))
    }
}