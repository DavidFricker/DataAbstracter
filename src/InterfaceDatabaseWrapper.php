<?php
namespace \DavidFricker\DataAbstracter;

// connection should be made in constructer
interface InterfaceDatabaseWrapper {
    public function insert($table, $data);
    public function delete($table, $where, $limit);
    public function update($table, $data, $where, $limit);
    public function fetch($table, $columns, $where, $limit, $order);
    public function run($query);
}