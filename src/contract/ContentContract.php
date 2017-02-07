<?php

namespace DavidFricker\DataAbstracter\Contract;

use DavidFricker\DataAbstracter\Contract\TableContentContractUsers;

// declare final class?
class ContentContract {
    const DATA_TYPE_INT = 'int';
    const DATA_TYPE_TEXT = 'text';
    const DATA_TYPE_DATETIME = 'datetime';
    
    // holds pairs of the table alias and 
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
        $this->tables[TableContentContractUsers::TABLE_NAME] = new TableContentContractUsers();
    }

    private function registerValidators() {
        $this->validators[self::DATA_TYPE_INT] = function($input) {
            return is_int($input);
        };

        $this->validators[self::DATA_TYPE_TEXT] = function($input) {
            return true;
        };

        $this->validators[self::DATA_TYPE_DATETIME] = function($input) {
            return true;
        };
    }
}





//var_dump(TableContentContractUsers::COL_ID, $ContentContract->tables[TableContentContractUsers::TABLE_NAME]);


/*

// declare final class?
class ContentContract {
    const DATA_TYPE_INT = 'int';
    const DATA_TYPE_TEXT = 'text';
    const DATA_TYPE_DATETIME = 'datetime';
    
    const TABLE = [
        'customers' => 'customers'
    ];

    const SCHEMA = [
        'customers' => [
            'id' => 'customerid',
            'email' => 'email',
            'password' => 'pass',
            'first_name' => 'fname',
        ]
    ];

    const TYPE = [
         'customers' => [
            'id' => self::DATA_TYPE_INT,
            'email' => self::DATA_TYPE_TEXT,
            'password' => self::DATA_TYPE_TEXT,
            'first_name' => self::DATA_TYPE_TEXT,
        ]
    ];

    static $VALIDATOR = [
         'customers' => [
            'id' => function($input){
                return is_int($input);
            },
            'email' => function($input){
                return true;
            },
            'password' => function($input){
                return true;
            },
            'first_name' => function($input){
                return true;
            },
        ]
    ];
}

var_dump(ContentContract::TABLE['customers'], ContentContract::SCHEMA['customers']['id'], ContentContract::TYPE['customers']['id']);
*/