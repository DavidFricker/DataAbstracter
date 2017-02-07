<?php
include '..\src\interfaces\InterfaceDatabaseWrapper.php';
include '..\src\interfaces\InterfaceContentProvider.php';
include '..\src\adapter\MySQLDatabaseWrapper.php';
include '..\src\contract\ContentContract.php';
include '..\src\contract\TableContentContractUsers.php';
include '..\src\provider\AppContentProvider.php';

use DavidFricker\DataAbstracter\Interfaces\InterfaceDatabaseWrapper;
use DavidFricker\DataAbstracter\Adapter\MySQLDatabaseWrapper;
use DavidFricker\DataAbstracter\Contract\ContentContract;
use DavidFricker\DataAbstracter\Contract\TableContentContractUsers;
use DavidFricker\DataAbstracter\Provider\AppContentProvider;

$AppContentProvider = AppContentProvider::init();

//var_dump($AppContentProvider->isValidInput(TableContentContractUsers::TABLE_NAME, TableContentContractUsers::COL_ID, '1'));

$R=$AppContentProvider->insert(TableContentContractUsers::TABLE_NAME, [TableContentContractUsers::COL_EMAIL => 'test@example.com']);
$R=$AppContentProvider->getLastInsertID();
#$R=$AppContentProvider->delete(TableContentContractUsers::TABLE_NAME, [TableContentContractUsers::COL_ID => $R], 1);
#$R=$AppContentProvider->fetch(TableContentContractUsers::TABLE_NAME, [TableContentContractUsers::COL_EMAIL], [TableContentContractUsers::COL_ID => $R], 1);
$AppContentProvider->update(TableContentContractUsers::TABLE_NAME, [TableContentContractUsers::COL_EMAIL => 'UPDATE@example.com'], [TableContentContractUsers::COL_ID => $R], 1);
$R=$AppContentProvider->fetch(TableContentContractUsers::TABLE_NAME, [TableContentContractUsers::COL_EMAIL], [TableContentContractUsers::COL_ID => $R], 1);
var_dump($R);

/*
$DBWrapper = new MySQLDatabaseWrapper();

$result = $DBWrapper->insert(ContentContract::TABLE['customers'], [ContentContract::SCHEMA['customers']['email'] => 'dbf@dbf.com']);
if ($result === false) {
	echo 'Error: '.$DBWrapper->getLastError();
}

$InsertID = $DBWrapper->getLastInsertID();

$result = $DBWrapper->update(
	ContentContract::TABLE['customers'], 
	[
		ContentContract::SCHEMA['customers']['first_name'] => 'David'
	], 
	[
		ContentContract::SCHEMA['customers']['email'] => 'gsdfg'
	]);
var_dump($result);

$result = $DBWrapper->delete(
	ContentContract::TABLE['customers'], 
	[
		ContentContract::SCHEMA['customers']['email'] => 'dbf@dbf.com'
	], 
	2);
var_dump($result);



$result = $DBWrapper->fetch(
	ContentContract::TABLE['customers'], 
	'Email',
	false,
	5);
var_dump($result,$DBWrapper->rowCount());*/