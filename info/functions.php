<?

function fixEnc2($in_str) {
	$cur_encoding = mb_detect_encoding($in_str);
	if($cur_encoding == "UTF-8" && mb_check_encoding($in_str,"UTF-8"))
		return $in_str;
	else
		return utf8_encode($in_str);
} 

function newRend ($webshop_id, $orig_id, $rend_datum, $rend_ido, $user_id, $nev, $irsz, $varos, $utca, $telefon, $email, $szamla_nev, $szamla_irsz, $szamla_varos, $szamla_utca, $szallitas, $fizetes, $termek_osszeg, $szall_dij, $futar_kod, $allapot, $session_id) {

	global $conn_main;

	$nev = fixEnc2($nev);
	$user_id = fixEnc2($user_id);
	$irsz = fixEnc2($irsz);
	$varos = fixEnc2($varos);
	$utca = fixEnc2($utca);
	$telefon = fixEnc2($telefon);
	$email = fixEnc2($email);
	$szamla_nev = fixEnc2($szamla_nev);
	$szamla_irsz = fixEnc2($szamla_irsz);
	$szamla_varos = fixEnc2($szamla_varos);
	$szamla_utca = fixEnc2($szamla_utca);
	if ($fizetes == 2) $allapot = 3; else $allapot = 4;
	if($szallitas == 1) {
		$ellenorizve = 1;
		if($allapot ==4) $allapot = 5;
	}	
	else $ellenorizve = 0;

	$query_rend_insert = "insert into rendeles (webshop_id, orig_id, rend_datum, rend_ido, user_id, nev, irsz, varos, utca, telefon, email, szamla_nev, szamla_irsz, szamla_varos, szamla_utca, szallitas, fizetes, termek_osszeg, szall_dij, futar_kod, allapot, session_id, mod_ido, ellenorizve) values ('$webshop_id', '$orig_id', '$rend_datum', '$rend_ido', '$user_id', '$nev', '$irsz', '$varos', '$utca', '$telefon', '$email', '$szamla_nev', '$szamla_irsz', '$szamla_varos', '$szamla_utca', '$szallitas', '$fizetes', '$termek_osszeg', '$szall_dij', '$futar_kod', '$allapot', '$session_id', '".$rend_datum." ".$rend_ido."', '$ellenorizve')";

//echo $query_rend_insert;

	$result_rend_insert=mysql_query($query_rend_insert, $conn_main);

}

function newRend1 ($webshop_id, $orig_id, $rend_datum, $rend_ido, $user_id, $nev, $irsz, $varos, $utca, $telefon, $email, $szamla_nev, $szamla_irsz, $szamla_varos, $szamla_utca, $szallitas, $fizetes, $termek_osszeg, $szall_dij, $futar_kod, $allapot, $session_id) {

	global $conn_main;

	$nev = fixEnc2($nev);
	$user_id = fixEnc2($user_id);
	$irsz = fixEnc2($irsz);
	$varos = fixEnc2($varos);
	$utca = fixEnc2($utca);
	$telefon = fixEnc2($telefon);
	$email = fixEnc2($email);
	$szamla_nev = fixEnc2($szamla_nev);
	$szamla_irsz = fixEnc2($szamla_irsz);
	$szamla_varos = fixEnc2($szamla_varos);
	$szamla_utca = fixEnc2($szamla_utca);
	if ($fizetes == 2) $allapot = 3; else $allapot = 4;
	if($szallitas == 1) {
		$ellenorizve = 1;
		if($allapot ==4) $allapot = 5;
	}	
	else $ellenorizve = 0;

	$query_rend_insert = "insert into rendeles (webshop_id, orig_id, rend_datum, rend_ido, user_id, nev, irsz, varos, utca, telefon, email, szamla_nev, szamla_irsz, szamla_varos, szamla_utca, szallitas, fizetes, termek_osszeg, szall_dij, futar_kod, allapot, session_id, mod_ido, ellenorizve) values ('$webshop_id', '$orig_id', '$rend_datum', '$rend_ido', '$user_id', '$nev', '$irsz', '$varos', '$utca', '$telefon', '$email', '$szamla_nev', '$szamla_irsz', '$szamla_varos', '$szamla_utca', '$szallitas', '$fizetes', '$termek_osszeg', '$szall_dij', '$futar_kod', '$allapot', '$session_id', '".$rend_datum." ".$rend_ido."', '$ellenorizve')";

//0624
echo $query_rend_insert;

//0624	$result_rend_insert=mysql_query($query_rend_insert, $conn_main);

}

function telefonFormat($telefon) {
	$telefon = str_ireplace(' ', '', $telefon);
	$telefon = str_ireplace('-', '', $telefon);
	$telefon = str_ireplace('/', '', $telefon);
	$telefon = str_ireplace('+3620', '0620', $telefon);
	$telefon = str_ireplace('+3630', '0630', $telefon);
	$telefon = str_ireplace('+3670', '0670', $telefon);
	if(strlen($telefon) == 9) {
		$telefon = '06'.$telefon;
	}
	elseif (strlen($telefon) == 7) {
		$telefon = '061'.$telefon;
	}
	return $telefon;

}

function rendOsszesit($rend_id) {

	global $conn_main;

	$result_termek_osszeg = mysql_query("select sum(termek_egysegar * termek_db) as termek_sum from termek where rend_id='$rend_id'", $conn_main);
	$termek_sum = mysql_fetch_row($result_termek_osszeg);

	$upd_termek_osszeg = $termek_sum[0];
	$upd_szallitasi_dij = 0;
	
	$result_szallitas = mysql_query("select szallitas, fizetes, webshop_id from rendeles where rend_id='$rend_id'", $conn_main);
	list($upd_szallitas, $upd_fizetes, $upd_webshop_id) = mysql_fetch_row($result_szallitas);
	
	if($upd_szallitas != 1) {
		$result_aruhaz_szall = mysql_query("select webshop_ingyenes_szall, webshop_eloreutalas, webshop_utanvet, webshop_kulfold from webshop where webshop_id = '$upd_webshop_id'", $conn_main);
		list($webshop_ingyenes_szall, $webshop_eloreutalas, $webshop_utanvet, $webshop_kulfold) = mysql_fetch_row($result_aruhaz_szall);
		if ($upd_termek_osszeg >= $webshop_ingyenes_szall) {
			$upd_szallitasi_dij = 0;
		}
		else {
			if($upd_szallitas == 2 and $upd_fizetes == 1) {
				$upd_szallitasi_dij = $webshop_utanvet;
			}
			elseif ($upd_szallitas == 2 and $upd_fizetes == 2) {
				$upd_szallitasi_dij = $webshop_eloreutalas;
			}
			elseif ($upd_szallitas == 4 and $upd_fizetes == 2) {
				$upd_szallitasi_dij = $webshop_kulfold;
			}
			elseif ($upd_szallitas == 2 and $upd_fizetes == 4) {
				$upd_szallitasi_dij = $webshop_eloreutalas;
			}
		}
	}
	
	$mod_ido = date("Y.m.d H:i:s", time());
	$query_rendeles_update = "update rendeles set termek_osszeg = '$upd_termek_osszeg', szall_dij = '$upd_szallitasi_dij', mod_ido = '$mod_ido' where rend_id = '$rend_id'";
	$result_rendeles_update = mysql_query($query_rendeles_update, $conn_main);
	
}

function newMod ($rend_id, $felh_id, $mod_tipus, $mod_szoveg) {
	global $conn_main;
	
	$mod_ido = date("Y.m.d H:i:s", time());
	$query_mod = "insert into modositas (rend_id, felh_id, mod_ido, mod_tipus, mod_szoveg) values ('$rend_id', '$felh_id', '$mod_ido', '$mod_tipus', '$mod_szoveg')";
	$result_mod = mysql_query($query_mod, $conn_main);
}

?>