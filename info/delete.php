<h1>DELETE</h1>
<?

$result_delete = mysql_query("delete  FROM termek where rend_id not in (select rend_id from rendeles)", $conn_main);
$result_delete = mysql_query("delete  FROM uzenet where rend_id not in (select rend_id from rendeles)", $conn_main);

echo "Kész<br>";


?>
