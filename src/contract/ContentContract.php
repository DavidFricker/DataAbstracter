<?php
namespace DavidFricker\DataAbstracter\Contract;

error_reporting(E_ALL);
ini_set('display_errors', 1);
echo 1;
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
        $this->tables[TableContractUsers::TABLE_NAME] = new TableContractUsers();
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

// declare final class?
class TableContractUsers {
    const TABLE_NAME = 'users';

    const COL_ID = 'customerid';
    const COL_EMAIL = 'customerid';
    const COL_PASSWORD = 'customerid';

    const TYPE = [
        self::COL_ID => ContentContract::DATA_TYPE_INT,
        self::COL_EMAIL => ContentContract::DATA_TYPE_TEXT,
        self::COL_PASSWORD => ContentContract::DATA_TYPE_TEXT,
    ];
}

$ContentContract = new ContentContract();

var_dump(TableContractUsers::COL_ID, $ContentContract->tables[TableContractUsers::TABLE_NAME]);


