<?

	if(isset($_POST["ugyvitel"])) {
		mysql_query("update ugyvitel set ugyvitel_aktiv = 0");
		mysql_query("update ugyvitel set ugyvitel_aktiv = 1 where ugyvitel_id = ".$_POST["ugyvitel"]);
	}

	$result_ugyvitel = mysql_query("select * from ugyvitel", $conn_main);
	while ($ugyvitel[] = mysql_fetch_array($result_ugyvitel));

?>

<h1>Ügyviteli szoftver beállítása</h1>


<form id="form_ugyvitel" action="<?echo $_SERVER['REQUEST_URI'];?>" method="post">

<select name="ugyvitel">
<?	
	foreach ($ugyvitel as $ugyv) {
		if($ugyv[0]) {
			echo "<option "; if($ugyv[2] == 1) echo "selected='selected' "; echo "value='".$ugyv[0]."'>".$ugyv[1]."</option>";
		}
	}
?>
</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" value="OK">

</form>
