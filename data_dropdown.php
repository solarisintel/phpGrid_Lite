<?php
require_once ('phpGrid.php');
if (! session_id())
{
	session_start();
}
$data = '';
$tableName = $_GET['tableName'] or die('phpGrid fatal error: ULR parameter "tableName" for table name is not defined.');
$dataText = $_GET['dataText'] or die('phpGrid fatal error: ULR parameter "dataText" for data text field is not defined.');
$dataValue = $_GET['dataValue'] or die('phpGrid fatal error: ULR parameter "dataValue" for data value field is not defined.');
$addBlank = isset($_GET['addBlank']) ? true : false;
$db = new C_DataBase(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_NAME);
$result = $db->executeQuery("SELECT $dataText, $dataValue FROM $tableName");
$data = '"":"";';
while ($row = $db->FetchAssoc($result))
{
	$data .= '' . $row[$dataValue] . ':' . $row[$dataText];
	$data .= ';';
}
echo $data;
$db = null;
?>