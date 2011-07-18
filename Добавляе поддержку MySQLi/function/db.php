<?php
if (!defined("FUNC_FILE")) die("Illegal File Access");

switch($dbtype) {

	case "mysqli":
	include("function/db/mysqli.php");
	break;

	case "mysql":
	include("function/db/mysql.php");
	break;
	
	case "sqlite":
	include("function/db/sqlite.php");
	break;
	
	case "postgres":
	include("function/db/postgres7.php");
	break;
	
	case "mssql":
	include("function/db/mssql.php");
	break;
	
	case "oracle":
	include("function/db/oracle.php");
	break;
	
	case "msaccess":
	include("function/db/msaccess.php");
	break;
	
	case "mssql-odbc":
	include("function/db/mssql-odbc.php");
	break;
}

$db = new sql_db($dbhost, $dbuname, $dbpass, $dbname, false, $dbcode);
if (!$db->db_connect_id) get_exit(""._SQLERROR."", 0);

if ($dbcode && $dbtype == "mysql") {
	mysql_query("SET NAMES '".$dbcode."'");
	mysql_query("SET CHARACTER SET '".$dbcode."'");
}
?>