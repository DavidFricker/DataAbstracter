<?php
namespace \DavidFricker\DataAbstracter;

class DBWrapper extends PDO implements InterfaceDatabaseWrapper {
    private $dsn = '';
    private $username = '';
    private $password = DB_PASSWORD;

    private $options = array(
	            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
	            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
	            PDO::ATTR_PERSISTENT => true
            	);

    public function __construct() {
        try {
            parent::__construct($this->$dsn, $this->$username, $this->$password, $this->$options);
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

    public function run($query) {
	if(!isValidTable($table)) {
    		return false;
    	}
    }

    private function isValidTable($table) {
    	return false;
    }
}