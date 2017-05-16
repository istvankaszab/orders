<?
ob_start();

session_start();

require "info/config.php";
require "info/functions.php";

$uri=$_SERVER['REQUEST_URI'];
$rest=substr($uri, 1);
$pos = strpos($rest, '/');

if(isset($_GET['inf'])) $inf= $_GET['inf']; else header('Location: '.$_SERVER['REQUEST_URI'].'?inf=rendeles-lista');
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Order management - Karóra Centrum Kft.</title>
	<link rel="shortcut icon" type="image/png" href="images/favicon.png" />        
	<link rel="stylesheet" type="text/css" href="css/style.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="css/print.css" media="print" />
       
        
</head>
<body>
<div class="menu">
	<a href="?inf=rendeles-lista">Home</a>
	<a href="?inf=rendeles-check">Check new orders</a>
	<a href="?inf=rendeles">New order</a>
	<a href="?inf=beszerzes-lista">Purchase list</a>
	<a href="?inf=futar-lista">Parcel list</a>
	<a href="?inf=futar-szamla-lista">Invoicing parcels</a>
	<a href="?inf=szamla-lista">Invoicing</a>
	<a href="?inf=rendeles-lezart">Completed orders</a>
	<a href="?inf=rendeles-lista&spec=1&problemas=1" style="color:#d00;">Problems</a>
	<a href="?inf=rendeles-lista&elfelejtett=1">Forgotten orders</a>
</div>

<?

$a=$inf.".html";
$b=$inf.".php";
if (file_exists('./info/'.$a)) {
	include('./info/'.$a);
}
else if (file_exists('./info/'.$b)) {
		include('./info/'.$b);
}
else {
		include('./info/nyito.php');
}


?>        

</body>
</html>
