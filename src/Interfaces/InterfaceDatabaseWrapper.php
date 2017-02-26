<?php

namespace DavidFricker\DataAbstracter\Interfaces;

/**
 * Defines a common interface that the database adapters should implement 
 * This is done in the effort that content providers can switch between adapters with minimal effort
 */
interface InterfaceDatabaseWrapper {
    /**
     * Create a new row
     * Must return bool or the result of run
     * 
     * @param  string $table name of table in the database
     * @param  array $data  key:value pairs, key is the column and value is the value to set for the new row
     * @return mixed see return value of run
     */
    public function insert($table, $data);

    /**
     * Remove one or more rows
     *
     * As this is intended to be a simple helper function the only 'glue' to hold together the where clauses is 'AND' more complex delete statements should be performed using run()
     * Must return bool or the result of run
     * 
     * @param  string $table name of table in the database
     * @param  array  $where optional, key:value pairs - column and expected value to filter by 
     * @param  boolean $limit optional, integer describing the amount of matching rows to delete
     * @return mixed see return value of run
     */
    public function delete($table, $where, $limit);

    /**
     * Update one or more rows
     *
     * As this is intended to be a simple helper function the only 'glue' to hold together the where clauses is 'AND' more complex update statements should be performed using run()
     * Must return bool or the result of run
     * 
     * @param  string $table name of table in the database
     * @param  array $data  key:value pairs, key is the column and value is the new value for each affected row
     * @param  array $where optional, key:value pairs - column and expected value to filter by 
     * @param  boolean $limit optional, integer describing the amount of matching rows to update
     * @return mixed see return value of run
     */
    public function update($table, $data, $where, $limit);

    /**
     * Pull one or more rows
     *
     * As this is intended to be a simple helper function the only 'glue' to hold together the where clauses is 'AND' more complex update statements should be performed using run()
     * IMPORTANT: Ensure the $columns variable does not contain user input as it is inserted as-is into the statement - vulnerable to SQL injection 
     * Must return bool or the result of run
     * 
     * @param  string $table name of table in the database
     * @param  array $data  key:value pairs, key is the column and value is the new value for each affected row
     * @param  array $where optional, key:value pairs - column and expected value to filter by 
     * @param  boolean $limit optional, integer describing the amount of matching rows to fetch
     * @return mixed see return value of run
     */
    public function fetch($table, $columns, $where, $limit);

    /**
     * Execute any SQL query
     *
     * To ensure your query is safe from first order SQL injection attacks pass all values via the $bind array
     * Must return bool, assoc array, count
     * 
     * @param  string $query MySQL query
     * @param  [type] $bind  key:value pairs where the key is a bind identifier and value is to be inserted at that location
     * @return mixed see example
     * @example depending on the type of input query the returned result can be an affected row count or a result set, the type of which is specified in the options passed to the constructor, defaulting to an assoc array
     * @example $query = 'SELECT * FROM table_name WHERE col_id = :BindColID'; $bind = [':BindColID' => 12];
     */
    public function run($query);
}
