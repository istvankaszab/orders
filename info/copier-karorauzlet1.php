<?

function rendeles_check_karorauzlet ($webshop_id, $webshop_host, $webshop_db, $webshop_user, $webshop_pwd) {

	global $conn_main;
	
	// db connection
	if($conn=mysql_connect("$webshop_host","$webshop_user","$webshop_pwd")) {
		if ($db_select = mysql_select_db("$webshop_db",$conn)) {
			$query_max_id ="select max(orig_id) from rendeles where webshop_id ='1' or webshop_id ='4' or webshop_id ='5'";
			$result_max_id = mysql_query($query_max_id, $conn_main);
			list($max_id) = mysql_fetch_row($result_max_id);
			
			$max_id = '14558';
			
			$result_rendeles = mysql_query("select kosar.megrendelt_sorsz, kosar.datum, kosar.ido from kosar where kosar.megrendelt_sorsz > '$max_id' order by kosar.megrendelt_sorsz desc", $conn);
			while (list($temp_orig_id, $temp_datum, $temp_ido)=mysql_fetch_row($result_rendeles)) {
				$result_temp_update = mysql_query("insert into temp_rendeles (temp_webshop_id, temp_orig_id, temp_datum, temp_ido) values ('$webshop_id', '$temp_orig_id', '$temp_datum', '$temp_ido')", $conn_main);
			}
		}
		else { echo "$webshop_db adatbázis csatlakozás sikertelen! <br/>"; }
		mysql_close($conn);
	}
	else {
		echo mysql_error($conn)."<br>";
		echo "$webshop_host csatlakozás sikertelen! 1<br/>";
	}
	
}

function rendeles_download_karorauzlet ($orig_id) {

	global $conn_main;

	$result_webshop = mysql_query("select * from webshop where webshop_id = '1'", $conn_main);
	list($webshop_id, $webshop_nev, $webshop_host, $webshop_db, $webshop_user, $webshop_pwd, $webshop_tip)=mysql_fetch_row($result_webshop);

// db connection
		if($conn=mysql_connect("$webshop_host","$webshop_user","$webshop_pwd")) {
			if ($db_select = mysql_select_db("$webshop_db",$conn)) {
			}
			else { echo "$webshop_db adatbázis csatlakozás sikertelen! <br/>"; }
		}
		else { echo "$webshop_host csatlakozás sikertelen! 2<br/>";	}

$query_rend="select email, datum, kiszallitas_modja, ido, megrendelt_sorsz, megjegyzes from kosar where megrendelt_sorsz='$orig_id'";
		$result_rend=mysql_query($query_rend, $conn);
		while (list($kosar_email, $kosar_datum, $kosar_kiszallitas_modja, $kosar_ido, $kosar_megrendelt_sorsz, $kosar_megjegyzes)=mysql_fetch_row($result_rend)) {
			$result_felh=mysql_query("select felhasznalo_id, nev, cim, tel, email, szamla_nev, szamla_cim from felhasznalok where felhasznalok.email = '$kosar_email'");
			while (list($user_id, $felh_nev, $felh_cim, $felh_tel, $felh_email, $szamla_nev, $szamla_cim)=mysql_fetch_row($result_felh)) {

//////////    ORDER PARAMETERS     //////////				

				// set encoding
				$user_id = $webshop_db."_".$user_id;
				$felh_nev = fixEnc2($felh_nev);
				$felh_cim = fixEnc2($felh_cim);
				$felh_email = fixEnc2($felh_email);
				$kosar_megjegyzes = fixEnc2($kosar_megjegyzes);
				$felh_tel = fixEnc2($felh_tel);
				$felh_tel = str_ireplace(' ', '', $felh_tel);
				$felh_tel = str_ireplace('-', '', $felh_tel);
				$felh_tel = str_ireplace('/', '', $felh_tel);
				$felh_tel = str_ireplace('+3620', '0620', $felh_tel);
				$felh_tel = str_ireplace('+3630', '0630', $felh_tel);
				$felh_tel = str_ireplace('+3670', '0670', $felh_tel);
				if(strlen($felh_tel) == 9) {
					$felh_tel = '06'.$felh_tel;
				}
				elseif (strlen($felh_tel) == 7) {
					$felh_tel = '061'.$felh_tel;
				}
				$szamla_nev = fixEnc2($szamla_nev);
				$szamla_cim = fixEnc2($szamla_cim);
				$kosar_megjegyzes = fixEnc2($kosar_megjegyzes);
				
				// retrieve zipcode
				preg_match('/(\d{4,4})/', $felh_cim, $felh_irsz);
				$felh_cim = str_ireplace($felh_irsz[0], '', $felh_cim);
				$varos = '';
				if (substr($felh_irsz[0], 0, 1) == '1') {
					$varos = 'Budapest';
					$felh_cim = str_ireplace('Budapest', '', $felh_cim);
				}
				$felh_cim = trim($felh_cim);
				preg_match('/(\d{4,4})/', $szamla_cim, $szamla_irsz);
				$szamla_cim = str_ireplace($szamla_irsz, '', $szamla_cim);
				$szamla_varos = '';
				if (substr($felh_irsz[0], 0, 1) == '1') {
					$szamla_varos = 'Budapest';
					$szamla_cim = str_ireplace('Budapest', '', $szamla_cim);
				}
				$szamla_cim = trim($szamla_cim);
				
				// delivery and payment

				// parcel, cash
				if ($kosar_kiszallitas_modja == 1) {
					$szallitas = 2;
					$fizetes = 1;
				}
				// parcel, transfer
				elseif ($kosar_kiszallitas_modja == 2) {
					$szallitas = 2;
					$fizetes = 2;
				}
				// in store, cash
				elseif ($kosar_kiszallitas_modja == 3) {
					$szallitas = 1;
					$fizetes = 3;
				}
				// abroad, transfer
				elseif ($kosar_kiszallitas_modja == 4) {
					$szallitas = 4;
					$fizetes = 2;
				}
				
				// set total, postage to zero
				$termek_osszeg = 0;
				$szallitasi_dij = 0;
				
				// parcel code empty
				$futar_kod = "";
				// order status: new (1)
				$allapot = 1;
				
				newRend1($webshop_id, $orig_id, $kosar_datum, $kosar_ido, $user_id, $felh_nev, $felh_irsz[0], $varos, $felh_cim, $felh_tel, $felh_email, $szamla_nev, $szamla_irsz[0], $szamla_varos, $szamla_cim, $szallitas, $fizetes, $termek_osszeg, $szallitasi_dij, $futar_kod, $allapot, '');

//////////    ORDER PARAMETERS END    //////////				


//////////    ITEM DATA     //////////				
				
				// retrieve oreder id
				$query_rend_id = "select rend_id from rendeles where webshop_id = '$webshop_id' and orig_id = '$orig_id'";
				$result_rend_id = mysql_query($query_rend_id, $conn_main);
				list($rend_id) = mysql_fetch_row($result_rend_id);
				
				// insert comment
				$query_uzenet_insert = "insert into uzenet (uzenet_ido, rend_id, felh_id, uzenet_szoveg) values ('".$kosar_datum." ".$kosar_ido."', $rend_id, '1', '$kosar_megjegyzes')";
				$result_uzenet_insert = mysql_query($query_uzenet_insert, $conn_main);

				//oredr id in webshop
				$query_kosar = "select kosar_id from kosar where megrendelt_sorsz = '$orig_id'";
				$result_kosar = mysql_query($query_kosar, $conn);
				list($kosar_id) = mysql_fetch_row($result_kosar);
				
				// order content
				$query_termek = "select termekid, egysegar, penznem, mennyiseg from kosar_tartalom where kosar_id ='$kosar_id'";
				$result_termek = mysql_query($query_termek, $conn);
				$termek_osszeg = 0;

				while (list($termekid, $egysegar, $penznem, $mennyiseg)=mysql_fetch_row($result_termek)) {

					$query_termek_adatok = "select nev, tcsid, focsoportnev, csoportnev from termek where termekid = '$termekid'";
					$result_termek_adatok = mysql_query($query_termek_adatok, $conn);
					list($termek_nev, $tcsid, $focsoportnev, $csoportnev) = mysql_fetch_row($result_termek_adatok);
					
					// retrieve brand
					$result_marka = mysql_query("select marka_nev, marka_alias, marka_id from marka", $conn_main);
					while(list($marka_nev, $marka_alias, $marka_azon) = mysql_fetch_row($result_marka)) {
						if(stripos(" ".$focsoportnev, $marka_nev) != false) $marka_id = $marka_azon;
						elseif(stripos(" ".$csoportnev, $marka_nev) != false) $marka_id = $marka_azon;
						elseif(stripos(" ".$termek_nev, $marka_nev) != false) $marka_id = $marka_azon;
					}
					$termek_nev = fixEnc2($termek_nev);
					$termek_nev = str_ireplace($result_marka, "", $termek_nev);
					$termek_nev = trim($termek_nev);

					// insert oredered item into db
					$query_termek_insert = "insert into termek (rend_id, termekid, termek_nev, marka_id, termek_egysegar, termek_db) values ('$rend_id', '$termekid', '$termek_nev', '$marka_id', '$egysegar', '$mennyiseg')";
					$result_termek_insert = mysql_query($query_termek_insert, $conn_main);
					$termek_osszeg += ($egysegar * $mennyiseg);
					
					// which webshop
					$query_aruhaz = "select aruhaz from termekcsoport where tcsid = '$tcsid'";
					$result_aruhaz = mysql_query($query_aruhaz, $conn);
					list($aruhaz) = mysql_fetch_row($result_aruhaz);
					if($aruhaz == 'karora') {
						$webshop_id = 1;						
					}
					else if($aruhaz =='szamologep') {
						$webshop_id = 4;
					}
					else if($aruhaz =='szotargep') {
						$webshop_id = 5;
					}

				}
				
				$query_aruhaz_update = "update rendeles set webshop_id = '$webshop_id' where rend_id = '$rend_id'";
				$result_aruhaz_update = mysql_query($query_aruhaz_update, $conn_main);

				rendOsszesit($rend_id);

//////////    ITEM DATA END    //////////				

			}
		}
		mysql_close($conn);
	
}


?>