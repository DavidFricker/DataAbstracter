<?php
namespace DavidFricker\DataAbstracter;

use \PDO;

/*
    Bassically an abstraction over the complexity of the PDO class, but by design this could wrap any strctured storage mechanism 
 */
class DBWrapper extends \PDO implements InterfaceDatabaseWrapper {
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

    public function delete($table, $where, $limit) {
        if(!isValidTable($table)) {
    		return false;
    	}
    }

    public function update($table, $data, $where, $limit) {
        if(!isValidTable($table)) {
    		return false;
    	}
    }

    public function fetch($table, $columns, $where, $limit, $order) {
	   if(!isValidTable($table)) {
    		return false;
    	}
    }

    // https://www.youtube.com/watch?v=JGf6TP6hZXc
    private function prepareBinding($rickross, $glue) {
        $chunks = [];
        foreach (array_keys($rickross) as $column) { 
            $chunks[] = '`' . $column . '` = ?'; 
        }

        return implode($glue, $chunks);
    }

    public function insert($table, $data) {
        if(!$this->isValidTable($table)) {
            return false;
        }

        if (!is_array($data)) {
            return false;
        } 
        
        $bind_string = $this->prepareBinding($data, ', ');
        $bind_values = array_values($data);
        $query = 'INSERT `'. $table.'` SET ' . $bind_string;

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

    private function isValidTable($table) {
        $temp = ContentContract::TABLE;
        return isset($temp[$table]);
        return null !== ContentContract::TABLE[$table];
    }

    private function isValidColumn($table, $column) {
        if (!isValidTable($table)) {
            return false;
        }

        if (null !== ContentContract::SCHEMA[$table][$column]) {
            return false;
        }

        return true;
    }

    public function getLastInsertID() {
        if($a=$this->run('SELECT LAST_INSERT_ID() as ID'))
        {
            return $a[0]['ID'];
        }else{
            return FALSE;
        }
    }

    public function rowCount() {
        return $this->dbh->rowCount();
    }

    public function getLastError() {
        return $this->error;
    }
}