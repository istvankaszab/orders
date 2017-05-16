<h1>Nagyker beállítás</h1>

<?

if (isset($_POST['nagyker']) and isset($_POST['nagyker_nev'])) {
	echo "1";
	$result_nagyker_update = mysql_query("update nagyker set nagyker_nev = $_POST['nagyker_nev] where nagyker_id = $_POST['nagyker']");
	header('Location: /');
}

if (!isset($_GET["nagyker"])) {
	$query_nagyker = "select * from nagyker order by nagyker_id";
	$result_nagyker = mysql_query($query_nagyker);
	
	echo "<table class='normal' cellspacing='0' cellpadding='0'><tr><td class='normal-head'>Sorszám</td><td class='normal-head'>Nagyker név</td><td class='normal-head'>Módosítás</td></tr>";
	
	while (list($nagyker_id, $nagyker_nev)=mysql_fetch_row($result_nagyker)) {
		echo "<tr><td class='normal-body'>$nagyker_id</td><td class='normal-body'>$nagyker_nev</td><td class='normal-body'><a href='?inf=beallitas-nagyker&nagyker=$nagyker_id'><img src='images/edit.png' /></a></td></tr>";
	}

	echo '</table>';

}
else {
	echo "a";
	$query_nagyker = "select * from nagyker where nagyker_id = $_GET['nagyker']";
	$result_nagyker = mysql_query($query_nagyker);
	
	echo "<table class='normal' cellspacing='0' cellpadding='0'><tr><td class='normal-head'>Sorszám</td><td class='normal-head'>Nagyker név</td><td class='normal-head'>Módosítás</td></tr>";
	
	while (list($nagyker_id, $nagyker_nev)=mysql_fetch_row($result_nagyker)) {
		echo "<form method='post' action='?inf=beallitas-nagyker&nagyker=<?$nagyker_id?>&nagyker_nev=<?$nagyker_nev?>' >
			<input type='text' value='<?$nagyker_nev?>'>
			<input type='submit' title='Mentés'><input type='reset'>
		</form>";

	}

	echo '</table>';
	
}


?>
