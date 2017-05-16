<?

function rendeles_check_karoracentrum_new ($webshop_id, $webshop_host, $webshop_db, $webshop_user, $webshop_pwd) {

	global $conn_main;

	
	$result_max_id = mysql_query("select max(orig_id) from rendeles where webshop_id ='11'", $conn_main);
	list($max_id) = mysql_fetch_row($result_max_id);
	$result_rend_datum = mysql_query("select rend_datum, rend_ido from rendeles where webshop_id='11' and orig_id='$max_id'", $conn_main);
	list($max_datum, $max_ido) = mysql_fetch_row($result_rend_datum);
	
	$ch = curl_init($webshop_host.$max_datum."%20".$max_ido);
	
	echo $webshop_url."<br>";
	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch, CURLOPT_USERPWD, $webshop_user . ":" . $webshop_pwd);
	$html = curl_exec($ch);
	curl_close($ch);

	$html = str_replace("'", "", $html);
	$xml=simplexml_load_string($html);

	$trunc_kc_xml = mysql_query("truncate kc_xml", $conn_main);
	$result_kc_xml = mysql_query("insert into kc_xml (szoveg) values ('$html')", $conn_main);

	foreach ($xml -> ORDER as $rend) {
		if ($rend -> ORDERHEAD_CODE > 2917 ) {
			$temp_datum = substr($rend -> ORDERHEAD_TIMESTAMP, 0, 10);
			$temp_ido = substr($rend -> ORDERHEAD_TIMESTAMP, 11, 8);
			$temp_orig_id = $rend -> ORDERHEAD_CODE;
			$result_temp_update = mysql_query("insert into temp_rendeles (temp_webshop_id, temp_orig_id, temp_datum, temp_ido) values ('$webshop_id', '$temp_orig_id', '$temp_datum', '$temp_ido')", $conn_main);
		}
	}
}

function rendeles_download_karoracentrum_new ($orig_id) {

	global $conn_main;

	$result_kc_xml = mysql_query("select szoveg from kc_xml", $conn_main);
	list($html) = mysql_fetch_row($result_kc_xml);

	$xml=simplexml_load_string($html);

	foreach ($xml -> ORDER as $rend) {
		if($rend -> ORDERHEAD_CODE == $orig_id) {
			
			if($rend -> ORDERHEAD_PAYMENTMETHOD_CODE =='utanvetel') { $fizetes = 1; }
			elseif ($rend -> ORDERHEAD_PAYMENTMETHOD_CODE =='banki atutalas') {	$fizetes = 2; }
			elseif ($rend -> ORDERHEAD_PAYMENTMETHOD_CODE =='keszpenzes fizetes') {	$fizetes = 3; }
			elseif ($rend -> ORDERHEAD_PAYMENTMETHOD_CODE =='otp bankkartyas fizetes') {	$fizetes = 4; }
			
			if(substr($rend -> ORDERHEAD_SHIPPINGMETHOD_NAME, 0, 4) =='Szem') {
				$szallitas = 1;
			}
			else {
				$szallitas = 2;
			}
			
			newRend(11, 
			$orig_id, 
			substr($rend -> ORDERHEAD_TIMESTAMP, 0, 10), 
			substr($rend -> ORDERHEAD_TIMESTAMP, 11, 8), 
			$rend -> ORDERHEAD_PARTNER_CODE, 
			$rend -> ORDERHEAD_SHIPPING_PARTNER_NAME,
			$rend -> ORDERHEAD_SHIPPING_PARTNER_ZIP,
			$rend -> ORDERHEAD_SHIPPING_PARTNER_CITY,
			$rend -> ORDERHEAD_SHIPPING_PARTNER_ADDRESS,
			$rend -> ORDERHEAD_PARTNER_PHONE, 
			$rend -> ORDERHEAD_PARTNER_MAIL, 
			$rend -> ORDERHEAD_PARTNER_NAME, 
			$rend -> ORDERHEAD_PARTNER_ZIP, 
			$rend -> ORDERHEAD_PARTNER_CITY, 
			$rend -> ORDERHEAD_PARTNER_ADDRESS, 
			$szallitas, $fizetes, 0, 0, "", 1, '');

			$kosar_ido = ORDERHEAD_TIMESTAMP;
			$uzenet = $rend -> ORDERHEAD_PARTNER_COMMENT;

			$result_rend_id = mysql_query("select rend_id from rendeles where webshop_id = '11' and orig_id = '$orig_id'", $conn_main);
			list($rend_id) = mysql_fetch_row($result_rend_id);

			$result_uzenet_insert = mysql_query("insert into uzenet (uzenet_ido, rend_id, felh_id, uzenet_szoveg) values ('$kosar_ido.', $rend_id, '1', '$uzenet')", $conn_main);

			foreach ($rend -> ORDERITEM as $termek) {
				if(substr($termek -> ORDERITEM_PRODUCT_CODE, 0, 5) != 'ship_') {
				
					$termekid = $termek -> ORDERITEM_PRODUCT_CODE;
					$termek_nev = $termek -> ORDERITEM_PART_NO;
					$afa = $termek -> ORDERITEM_VAT_PERCENT;
					$egysegar = $termek -> ORDERITEM_PRICE;
					$egysegar = round(floatval($egysegar) * (1 + $afa / 100));
					$mennyiseg = $termek -> ORDERITEM_QTY;
					$termek_marka = '';

					$result_marka = mysql_query("select marka_nev, marka_alias, marka_id from marka", $conn_main);
					while(list($marka_nev, $marka_alias, $marka_id) = mysql_fetch_row($result_marka)) {
						if(stripos(" ".$termek -> ORDERITEM_NAME, $marka_nev) != false) $termek_marka = $marka_id;
						if(stripos(" ".$termek -> ORDERITEM_NAME, $marka_alias) != false) $termek_marka = $marka_id;
					}

					$query_termek_insert = "insert into termek (rend_id, termekid, termek_nev, marka_id, termek_egysegar, termek_db) values ('$rend_id', '$termekid', '$termek_nev', '$termek_marka', '$egysegar', '$mennyiseg')";

					$result_termek_insert = mysql_query($query_termek_insert, $conn_main);
					$termek_osszeg += ($egysegar * $mennyiseg);
				}
			}
			rendOsszesit($rend_id);
		}
	}

}


?>
