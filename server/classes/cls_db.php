<?php
if (! session_id())
{
	session_start();
}
class C_Database
{
	public $hostName;
	public $userName;
	public $password;
	public $databaseName;
	public $tableName;
	public $link;
	public $dbType;
	public $db;
	public $result;
//	public function __construct ($host, $user, $pass, $dbName, $db_type = "mysqli")
	public function __construct ($host, $user, $pass, $dbName, $db_type = "postgres")
	{
		$this->hostName = $host;
		$this->userName = $user;
		$this->password = $pass;
		$this->databaseName = $dbName;
		$this->dbType = $db_type;
		$this->Connect();
	}
	public function Connect()
	{
		switch ($this->dbType)
		{
			case "access":
				$this->db = & ADONewConnection($this->dbType);
				$dsn = "Driver={Microsoft Access Driver (*.mdb)};Dbq=" .
				 $this->databaseName . ";Uid=" .
				 $this->userName . ";Pwd=" .
				 $this->password . ";";
				$this->db->Connect($dsn);
				break;
			case "odbc_mssql":
				$this->db = & ADONewConnection($this->dbType);
				$dsn = "Driver={SQL Server};Server=" .
				$this->hostName . ";Database=" .
				$this->databaseName . ";";
				$this->db->Connect($dsn, 
				$this->userName, $this->password);
				break;
			case "postgres":
			        error_log('cls_db called type=postgres');
				$this->db = ADONewConnection($this->dbType);
				$this->db->Connect($this->hostName, $this->userName, $this->password, $this->databaseName);
				if (!($this->db->isConnected())) {
				    die("Error: Could not connect to the database");
				}
				break;
			case "db2":
				$this->db = & ADONewConnection($this->dbType);
				$dsn = "driver={IBM db2 odbc DRIVER};Database=" .
				 $this->databaseName . ";hostname=" .
				 $this->hostName .
				 ";port=50000;protocol=TCPIP;uid=" .
				 $this->userName . "; pwd=" .
				 $this->password;
				$this->db->Connect($dsn);
				break;
			case "ibase":
				$this->db = &ADONewConnection($this->dbType);
				$this->db->PConnect(
				$this->hostName . $this->databaseName, 
				$this->userName, $this->password);
				break;
			case "oci8":
				$ret = $this->db->Connect(false, 
				$this->userName, $this->password);
				if (! ret)
				{
					$this->db->Connect($this->hostName, 
					$this->userName, $this->password, 
					$this->databaseName);
				}
				break;
			case "sqlit":
				break;
			default:
			        error_log('cls_db called default is postgres');
				$this->db = ADONewConnection($this->dbType);
				$this->db->Connect($this->hostName, $this->userName, $this->password, $this->databaseName);
				if (!($this->db->isConnected())) {
				    die("Error: Could not connect to the database");
				}
				break;
			        //error_log('cls_db called default is mysqli');
				//$this->db = ADONewConnection($this->dbType);
				//$this->db->PConnect($this->hostName, 
				//$this->userName, $this->password, 
				//$this->databaseName) or
				// die("Error: Could not connect to the database");
				//break;
		}
	}
	public function executeQuery ($sqlQuery)
	{
		$this->db->SetFetchMode(ADODB_FETCH_BOTH);
		$result = $this->db->Execute($sqlQuery) or
		 die(
		"Error: Could not execute query $sqlQuery in executeQuery()");
		$this->result = $result;
		return $result;
	}
	public function selectLimit ($sqlQuery, $size, $offset)
	{
		$result = $this->db->SelectLimit($sqlQuery, 
		$size, $offset) or
		 die(
		"Error: Could not execute query $sqlQuery in selectLimit()");
		$this->result = $result;
		return $result;
	}
	public function selectLimitArray ($sqlQuery, $size, $offset)
	{
		$result = $this->selectLimit(
		$sqlQuery, $size, 
		$offset);
		$resultArray = $result->GetArray();
		$this->result = $resultArray;
		return $resultArray;
	}
	public function FetchNum (&$result)
	{
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		if (! $result->EOF)
		{
			$rs = $result->fields;
			$result->MoveNext();
			return $rs;
		}
	}
	public function FetchBoth (&$result)
	{
		$ADODB_FETCH_MODE = ADODB_FETCH_BOTH;
		if (! $result->EOF)
		{
			$rs = $result->fields;
			$result->MoveNext();
			return $rs;
		}
	}
	public function FetchAssoc (&$result)
	{
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		if (! $result->EOF)
		{
			$rs = $result->fields;
			$result->MoveNext();
			return $rs;
		}
	}
	public function ExecuteFetchNum ($sqlQuery)
	{
		$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
		$result = $this->db->Execute($sqlQuery) or
		 die(
		"Error: Could not execute query $sqlQuery");
		if (! $result->EOF)
		{
			$rs = $result->fields;
			$result->MoveNext();
			return $rs;
		}
	}
	public function ExecuteFetchBoth ($sqlQuery)
	{
		$ADODB_FETCH_MODE = ADODB_FETCH_BOTH;
		$result = $this->db->Execute($sqlQuery) or
		 die(
		"Error: Could not execute query $sqlQuery");
		if (! $result->EOF)
		{
			$rs = $result->fields;
			$result->MoveNext();
			return $rs;
		}
	}
	public function GetRecordCount ($result)
	{
		return $result->RecordCount();
	}
	public function num_fields ($result)
	{
		return $result->FieldCount();
	}
	public function field_name ($result, $index)
	{
		$filedObj = new ADOFieldObject();
		$filedObj = $result->FetchField($index);
		return isset($filedObj->name) ? $filedObj->name : "";
	}
	public function field_type($result, $index)
	{
		$filedObj = new ADOFieldObject();
		$filedObj = $result->FetchField($index);
		return isset($filedObj->type) ? $filedObj->type : "";
	}
	public function meta_type ($result, $index)
	{
		$filedObj = new ADOFieldObject();
		$filedObj = $result->FetchField($index);
		$type = $result->MetaType($filedObj->type, 
		$filedObj->max_length);
		return $type;
	}
	public function getMetaColumn ($table, 	$fieldName)
	{
		$arr = array();
		$arr = $this->db->MetaColumns($table);
		$filedObj = new ADOFieldObject();
		if (isset($arr[strtoupper($fieldName)]))
		{
			$filedObj = $arr[strtoupper(
			$fieldName)];
			return $filedObj;
		}
		else
		{
			return false;
		}
	}
	public function getFieldIndexByName ($result, $field_name)
	{
		$fieldCount = $this->num_fields($result);
		$i = 0;
		for ($i = 0; $i < $fieldCount; $i ++)
		{
			if ($field_name == $this->field_name($result, $i)) return $i;
		}
		return - 1;
	}
	public function FieldMaxLength ($result, $index)
	{
		$filedObj = new ADOFieldObject();
		$filedObj = $result->FetchField($index);
		return isset($filedObj->max_length) ? $filedObj->max_length : "";
	}
}
?>