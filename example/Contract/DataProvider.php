<?php

namespace DavidFricker\DataAbstracter\Example\Contract;

class DataProvider
{
	const TABLE_NAME = 'dataprovider';

	const COL_ID = 'ID';
	const COL_USERNAME = 'username';
	const COL_ACCOUNT_STATUS = 'active';
	const COL_DATE = 'Date';

	const SCHEMA = [
		self::COL_ID => ContentContract::DATA_TYPE_INT,
		self::COL_USERNAME => ContentContract::DATA_TYPE_TEXT,
		self::COL_ACCOUNT_STATUS => ContentContract::DATA_TYPE_INT,
		self::COL_DATE => ContentContract::DATA_TYPE_DATETIME
	];
}

