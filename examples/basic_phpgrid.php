<?php
require_once("../conf.php");      
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>A Basic PHP Datagrid</title>
</head>
<body > 

<?php
//$dg = new C_DataGrid("SELECT * FROM Orders", "orderNumber", "Orders");

$dg = new C_DataGrid("SELECT * FROM Orders");

$dg -> set_dimension(1500, 600); 

$dg -> set_col_align("comments", "left");  // left, center, right
$dg -> set_col_width("comments", "1200");

$dg -> display();
?>

</body>
</html>