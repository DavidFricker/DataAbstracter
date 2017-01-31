<?php


namespace DavidFricker\DataAbstracter;


// declare final class?
class ContentContract {
    const DATA_TYPE_INT = 'int';
    const DATA_TYPE_TEXT = 'text';
    const DATA_TYPE_DATETIME = 'datetime';
    
    const TABLE = [
        'users' => 'table_user'
    ];

    const SCHEMA = [
        'users' => [
            'id' => 'customerid',
            'email' => 'email',
            'password' => 'pass'
        ]
    ];

    const TYPE = [
         'users' => [
            'id' => self::DATA_TYPE_INT,
            'email' => self::DATA_TYPE_TEXT,
            'password' => self::DATA_TYPE_TEXT
        ]
    ];
}

var_dump(ContentContract::TABLE['users'], ContentContract::SCHEMA['users']['id'], ContentContract::TYPE['users']['id']);
