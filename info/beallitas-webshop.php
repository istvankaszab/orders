<h1>Webshop beállítás</h1>

<?

$query_webshop = "select * from webshop";
$result_webshop = mysql_query($query_webshop);

echo "<table class='normal' cellspacing='0' cellpadding='0'><tr><td class='normal-head'>Sorszám</td><td class='normal-head'>Webshop név</td><td class='normal-head'>Host</td><td class='normal-head'>Adatbázis</td><td class='normal-head'>DB user</td><td class='normal-head'>DB password</td><td class='normal-head'>Webshop típusa</td></tr>";

while (list($webshop_id, $webshop_nev, $webshop_host, $webshop_db, $webshop_user, $webshop_pwd, $webshop_tip)=mysql_fetch_row($result_webshop)) {
	echo "<tr><td class='normal-body'>$webshop_id</td><td class='normal-body'>$webshop_nev</td><td class='normal-body'>$webshop_host</td><td class='normal-body'>$webshop_db</td><td class='normal-body'>$webshop_user</td><td class='normal-body'>$webshop_pwd</td><td class='normal-body'>$webshop_tip</td></tr>";
}

echo '</table>';




?>
