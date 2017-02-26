<?php

namespace DavidFricker\DataAbstracter\Example\Contract;

class ContentContract {
    const DATA_TYPE_INT = 'int';
    const DATA_TYPE_TEXT = 'text';
    const DATA_TYPE_DATETIME = 'datetime';
    const FORMAT_DATETIME = 'Y-m-d H:i:s';
    
    // holds pairs of the table alias and contract
    public $tables = [];
    public $validators = [];

    /**
     * Set a private __construct to stop instaces being created leading to more than one instance of this class
     */
    public function __construct() {
        $this->registerValidators();
        $this->registerTables();
    }

    private function registerTables() {
        $this->tables[DataProvider::TABLE_NAME] = new DataProvider();
    }

    private function registerValidators() {
        $this->validators[self::DATA_TYPE_INT] = function($input) {
            return is_int($input, true);
        };

        $this->validators[self::DATA_TYPE_TEXT] = function($input) {
            return !is_int($input);
        };

        $this->validators[self::DATA_TYPE_DATETIME] = function($input) {
            return true;
        };
    }
}
