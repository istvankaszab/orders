<?
//error_reporting(E_ALL);

//     rendeles admin adatbazis csatlakozas
$db_host_main='localhost';
$db_name_main='karorace_rendeles';
$db_user_main='username';
$db_passwd_main='password';

if($conn_main=mysql_connect("$db_host_main","$db_user_main","$db_passwd_main")) {
	if ($db_select_main = mysql_select_db("$db_name_main",$conn_main)) {
	}
	else {
		echo "$db_name_main adatbázis csatlakozás sikertelen! <br/>";
		$hiba = 1;
	} 
}
else {
	echo "$db_host_main csatlakozás sikertelen! <br/>";
}


$sorok_szama = 20;


?>
