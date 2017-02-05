<?php
namespace DavidFricker\DataAbstracter;

use \PDO;

/*
    
 */
/**
  * A wrapper around a DB driver to expose a uniform interface
  *
  * Bassically an abstraction over the complexity of the PDO class, but by design this could wrap any strctured storage mechanism 
  * A database engine adapter
  *
  * @param string $myArgument With a *description* of this argument, these may also
  *    span multiple lines.
  *
  * @return void
  */
class MySQLDatabaseWrapper extends \PDO implements InterfaceDatabaseWrapper {
    private $dsn = 'mysql:host=localhost;port=3306;dbname=apisaas';
    private $username = 'root';
    private $password = '';
    private $handle;
    private $error_str = '';

    //http://stackoverflow.com/questions/134099/are-pdo-prepared-statements-sufficient-to-prevent-sql-injection
    //ensure the data is sent in diff packets
    private $options = array(
	            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
	            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
	            PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_EMULATE_PREPARES => false
            );

    public function __construct() {
        try {
            parent::__construct($this->dsn, $this->username, $this->password, $this->options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
        }
    }

    /*
        NB: As this is intented to be a simple helper function the only 'glue' to hold togther the where clasues is 'AND' 
        more complex update statments should be performed using run()
     */
    public function delete($table, $where, $limit) {
        if(!$this->isValidTable($table)) {
    		return false;
    	}
        
        $sql= '';
        if ($limit && is_int($limit)) { 
            $sql .= ' LIMIT '. $limit; 
        }

        if (!is_array($where) || empty($where)) {
            $sql = 'DELETE FROM `' . $table . '`'.$sql;
            return $this->run($sql, []);
        }

        $bind_values = array_values($where);
        $sql = 'DELETE FROM  `' . $table . '` WHERE ' . $this->prepareBinding($where, ' AND ').$sql;

        return $this->run($sql, $bind_values);
    }

    /*
        NB: As this is intented to be a simple helper function the only 'glue' to hold togther the where clasues is 'AND' 
        more complex update statments should be performed using run()
     */
    public function update($table, $data, $where, $limit=false) {
        if (!$this->isValidTable($table)) {
    		return false;
    	}

        if (!is_array($data) || empty($data)) {
            return false;
        }

        $bind_values = array_values($data);
        $sql = 'UPDATE `' . $table . '` SET ' . $this->prepareBinding($data, ', ');

        if (is_array($where) && !empty($where)) {
            $sql .= ' WHERE ' . $this->prepareBinding($where, ' AND ');
            $bind_values = array_merge($bind_values, array_values($where));
        }

        if ($limit && is_int($limit)) { 
            $sql .= ' LIMIT '. $limit; 
        }

        return $this->run($sql, $bind_values);
    }

    /*
        Important: Ensure the $columns var does not contain user input as it is inserted as-is into the statement - vulnrable to SQL injection 
        NB: As this is intented to be a simple helper function the only 'glue' to hold togther the where clasues is 'AND' 
        more complex update statments should be performed using run()
     */
    public function fetch($table, $columns, $where=false, $limit=false) {
	    if (!$this->isValidTable($table)) {
            return false;
        }

        if (empty($columns)) {
            return false;
        }

        $bind_values = [];
        $sql = 'SELECT '.$columns.' FROM `' . $table . '`';

        if (is_array($where) && !empty($where)) {
            $sql .= ' WHERE ' . $this->prepareBinding($where, ' AND ');
            $bind_values = array_merge($bind_values, array_values($where));
        }

        if ($limit && is_int($limit)) { 
            $sql .= ' LIMIT '. $limit; 
        }

        return $this->run($sql, $bind_values);
    }

    public function insert($table, $data) {
        if(!$this->isValidTable($table)) {
            return false;
        }

        if (!is_array($data)) {
            return false;
        } 
        
        $fragment_sql = $this->prepareBinding($data, ', ');
        $bind_values = array_values($data);
        $query = 'INSERT `'. $table.'` SET ' . $fragment_sql;

        return $this->run($query, $bind_values);
    }
    
    public function run($query, $bind=[]) {
        try {
            $this->handle = $this->prepare($query);
            $this->handle->execute($bind);

            // check what the query begins with
            if (preg_match('/^(select|describe|pragma)/i', $query)) {
                return $this->handle->fetchAll();
            }

            if (preg_match('/^(delete|insert|update)/i', $query)) {
                return $this->handle->rowCount();
            }
            
            return true;
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function getLastInsertID() {
        if ($a=$this->run('SELECT LAST_INSERT_ID() as ID')) {
            return $a[0]['ID'];
        }else{
            return FALSE;
        }
    }

    public function rowCount() {
        return $this->handle->rowCount();
    }

    public function getLastError() {
        return $this->error;
    }

    private function isValidTable($table) {
        $temp = ContentContract::TABLE;
        return isset($temp[$table]);
        //return null !== ContentContract::TABLE[$table];
    }

    public function isValidColumn($table, $column) {
        if (!isValidTable($table)) {
            return false;
        }

        if (null !== ContentContract::SCHEMA[$table][$column]) {
            return false;
        }

        return true;
    }

    // https://www.youtube.com/watch?v=JGf6TP6hZXc
    private function prepareBinding($rickross, $glue) {
        $chunks = [];
        foreach (array_keys($rickross) as $column) { 
            $chunks[] = '`' . $column . '` = ?'; 
        }

        return implode($glue, $chunks);
    }
}