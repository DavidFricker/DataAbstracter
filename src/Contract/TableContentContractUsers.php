<?php

namespace DavidFricker\DataAbstracter\Contract;

// declare final class?
class TableContentContractUsers 
{
    const TABLE_NAME = 'customers';

    const COL_ID = 'CustomerID';
    const COL_EMAIL = 'email';
    const COL_PASSWORD = 'password';

    const SCHEMA = [
        self::COL_ID => ContentContract::DATA_TYPE_INT,
        self::COL_EMAIL => ContentContract::DATA_TYPE_TEXT,
        self::COL_PASSWORD => ContentContract::DATA_TYPE_TEXT,
    ];
}
