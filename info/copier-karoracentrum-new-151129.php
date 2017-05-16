<?

function rendeles_check_karoracentrum_new ($webshop_id, $webshop_host, $webshop_db, $webshop_user, $webshop_pwd) {

	global $conn_main;

	$result_datum = mysql_query("select max(rend_datum) from rendeles where webshop_id='11'", $conn_main);
	list($max_datum) = mysql_fetch_row($result_datum);
	$result_ido = mysql_query("select max(rend_ido) from rendeles where webshop_id='11' and rend_datum ='$max_datum'", $conn_main);
	list($max_ido) = mysql_fetch_row($result_ido);
	
	
	

	$ch = curl_init($webshop_host."2015-01-01%20".$max_ido);
	
	echo $webshop_host."2015-01-01%20".$max_ido."<br>";
	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch, CURLOPT_USERPWD, $webshop_user . ":" . $webshop_pwd);
	$html = curl_exec($ch);
	curl_close($ch);

	$html = str_replace("'", "", $html);
	$xml=simplexml_load_string($html);

	foreach ($xml -> ORDER as $rend) {
		$qry_id = "select orig_id from rendeles where webshop_id ='11' and orig_id ='".$rend -> ORDERHEAD_CODE."'";
		echo $qry_id."<br>";
		$result_id = mysql_query($qry_id, $conn_main);
		if(!$result_id) {
			echo "new: ".$rend -> ORDERHEAD_CODE."<br>";
		}
		if ($rend -> ORDERHEAD_CODE > 2917 ) {
			$temp_datum = substr($rend -> ORDERHEAD_TIMESTAMP, 0, 10);
			$temp_ido = substr($rend -> ORDERHEAD_TIMESTAMP, 11, 8);
			$temp_orig_id = $rend -> ORDERHEAD_CODE;
		}
	}
}


?>
