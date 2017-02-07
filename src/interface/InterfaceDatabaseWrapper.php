<?php
namespace DavidFricker\DataAbstracter\Interface;

// connection should be made in constructer
interface InterfaceDatabaseWrapper {
    // must return bool or the result of run
    public function insert($table, $data);
    // must return bool or the result of run
    public function delete($table, $where, $limit);
    // must return bool or the result of run
    public function update($table, $data, $where, $limit);
    // must return bool or the result of run
    public function fetch($table, $columns, $where, $limit);
    // must return bool, assoc array, count
    public function run($query);
}