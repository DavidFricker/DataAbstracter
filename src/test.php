<?php
include 'InterfaceDatabaseWrapper.php';
include 'MySQLDatabaseWrapper.php';
include 'ContentContract.php';

use DavidFricker\DataAbstracter\InterfaceDatabaseWrapper;
use DavidFricker\DataAbstracter\MySQLDatabaseWrapper;
use DavidFricker\DataAbstracter\ContentContract;

$DBWrapper = new MySQLDatabaseWrapper();
/*
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

*/

$result = $DBWrapper->fetch(
	ContentContract::TABLE['customers'], 
	'Email',
	false,
	5);
var_dump($result);