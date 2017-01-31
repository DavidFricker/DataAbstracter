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

    public function insert($table, $data) {
    	if(!isValidTable($table)) {
    		return false;
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

    }

    private function isValidTable($table) {
    	return false;
    }
}