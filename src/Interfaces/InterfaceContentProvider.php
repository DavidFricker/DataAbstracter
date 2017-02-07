<?php
namespace DavidFricker\DataAbstracter\Interfaces;

// connection should be made in constructer
interface InterfaceContentProvider {
    public function insert($table, $data);
    public function delete($table, $where=false, $limit=false);
    public function update($table, $data, $where=false, $limit=false);
    public function fetch($table, $columns, $where=false, $limit=false);
    public function run($query);
}