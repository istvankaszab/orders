<?

function rendeles_check_legjobbajanlat ($webshop_id, $webshop_host, $webshop_db, $webshop_user, $webshop_pwd) {

	global $conn_main;
	
	// adatbazis csatlakozas
	if($conn=mysql_connect("$webshop_host","$webshop_user","$webshop_pwd")) {
		if ($db_select = mysql_select_db("$webshop_db",$conn)) { }
		else { echo "$db_name adatbázis csatlakozás sikertelen! <br/>"; }
	}
	else { echo "$db_host csatlakozás sikertelen! 1<br/>"; }
	
	$max_id = 0;
	
	$result_max_id = mysql_query("select max(orig_id) from rendeles where webshop_id ='8' or webshop_id ='9'", $conn_main);
	list($max_id) = mysql_fetch_row($result_max_id);
	
	if($max_id == 0) $max_id = 8780;
	
	$result_rendeles = mysql_query("select unid, datum from kar1_megrendeles where unid > '$max_id'", $conn);
	while (list($temp_orig_id, $temp_datum_ido)=mysql_fetch_row($result_rendeles)) {
		$temp_datum = trim(substr($temp_datum_ido, 0, stripos($temp_datum_ido, " ")+1));
		$temp_ido = trim(substr($temp_datum_ido, stripos($temp_datum_ido, " ")));

		$result_temp_update = mysql_query("insert into temp_rendeles (temp_webshop_id, temp_orig_id, temp_datum, temp_ido) values ('$webshop_id', '$temp_orig_id', '$temp_datum', '$temp_ido')", $conn_main);
	}
	mysql_close($conn);
}




function rendeles_download_legjobbajanlat ($orig_id) {

	global $conn_main;

	$result_webshop = mysql_query("select * from webshop where webshop_id = '8'", $conn_main);
	list($webshop_id, $webshop_nev, $webshop_host, $webshop_db, $webshop_user, $webshop_pwd, $webshop_tip)=mysql_fetch_row($result_webshop);

// adatbazis csatlakozas
		if($conn=mysql_connect("$webshop_host","$webshop_user","$webshop_pwd")) {
			if ($db_select = mysql_select_db("$webshop_db",$conn)) {
			}
			else { echo "$webshop_db adatbázis csatlakozás sikertelen! <br/>"; }
		}
		else { echo "$webshop_host csatlakozás sikertelen! 2<br/>";	}


		$result_rend=mysql_query("select unid, datum, userid, webshop, ertek, termek, uzenet, megjegyzes from kar1_megrendeles where unid = '$orig_id'", $conn);

		while (list($megr_unid, $megr_datum_ido, $megr_userid, $megr_webshop, $megr_ertek, $megr_termek, $megr_uzenet, $megr_megjegyzes)=mysql_fetch_row($result_rend)) {
			
			$megr_datum = trim(substr($megr_datum_ido, 0, stripos($megr_datum_ido, " ")+1));
			$megr_ido = trim(substr($megr_datum_ido, stripos($megr_datum_ido, " ")));
			
			if ($megr_webshop == 6) $webshop_id = 8;
			else $webshop_id = 9;
			
			$result_nev = mysql_query("select vnev, knev, email from kar1_user where unid = '$megr_userid'", $conn);
			list($vnev, $knev, $email) = mysql_fetch_row($result_nev);
			
//////////    RENDELES ALAPADATOK     //////////				

			$user_id = $webshop_db."_".$megr_userid;
			$nev = $vnev." ".$knev;
			$nev = fixEnc2($nev);
			$email = fixEnc2($email);
			$megr_uzenet = fixEnc2($megr_uzenet);
			$megr_megjegyzes = fixEnc2($megr_megjegyzes);
			$szamla_szall = preg_split( '/\r\n|\r|\n/', $megr_megjegyzes );
			preg_match('/(\d{4,4})/', $szamla_szall[0], $felh_irsz);
			$szamla_szall[0] = str_ireplace($felh_irsz[0], '', $szamla_szall[0]);
			preg_match('/(\d{4,4})/', $szamla_szall[2], $szamla_irsz);
			$szamla_szall[2] = str_ireplace($szamla_irsz[0], '', $szamla_szall[2]);
			$kapcstel = str_ireplace(' ', '', $szamla_szall[3]);
			$kapcstel = str_ireplace('-', '', $kapcstel);
			$kapcstel = str_ireplace('/', '', $kapcstel);
			$kapcstel = str_ireplace('+3620', '0620', $kapcstel);
			$kapcstel = str_ireplace('+3630', '0630', $kapcstel);
			$kapcstel = str_ireplace('+3670', '0670', $kapcstel);
			if(strlen($kapcstel) == 9) {
				$kapcstel = '06'.$kapcstel;
			}
			elseif (strlen($kapcstel) == 7) {
				$kapcstel = '061'.$kapcstel;
			}
				
			//kiszallitas modja es fizetes meghatarozasa
			//futar, utanvet
			
				if ($szamla_szall[4] == 'ac1763b0c46ec5e1' or $szamla_szall[4] == 'fiz-7-utanvet' or $szamla_szall[4] == 'fiz-7-utanvet-fr') {
					$szallitas = 2;
					$fizetes = 1;
				}
				//futar, eloreutalas
				elseif ($szamla_szall[4] == '9753d5d3f463e7d3' or $szamla_szall[4] == 'fiz-7-utalas' or $szamla_szall[4] == 'fiz-7-utalas-fr' or $szamla_szall[4] == '43b3e79f5b732c81') {
					$szallitas = 2;
					$fizetes = 2;
				}
				//szemelyes atvetel, keszpenz
				elseif ($szamla_szall[4] == 'fiz-7-szemelyes') {
					$szallitas = 1;
					$fizetes = 3;
				}
				//kulfold, eloreutalas
//				elseif ($kosar_kid == 7 or $kosar_kid == 12) {
//					$szallitas = 4;
//					$fizetes = 2;
//				}

				
				// osszeg, szallitasi dij nullara allitas
				$szallitasi_dij = 0;
				
				//ures a futar kod
				$futar_kod = "";
				// rendeles allapota: uj (1)
				$allapot = 1;
				
				newRend($webshop_id, $orig_id, $megr_datum, $megr_ido, $user_id, $nev, $felh_irsz[0], '', $szamla_szall[0], $kapcstel, $email, $szamla_szall[1], $szamla_irsz[0], '', $szamla_szall[2], $szallitas, $fizetes, $megr_ertek, $szallitasi_dij, $futar_kod, $allapot, '');

//				$query_rend_insert = "insert into rendeles (webshop_id, orig_id, rend_datum, rend_ido, nev, irsz, varos, utca, telefon, email, szamla_nev, szamla_irsz, szamla_varos, szamla_utca, szallitas, fizetes, termek_osszeg, szall_dij, futar_kod, allapot) values ('$webshop_id', '$orig_id', '$megr_datum', '$megr_ido', '$nev', '$felh_irsz[0]', '', '$szamla_szall[0]', '$kapcstel', '$email', '$szamla_szall[1]', '$szamla_irsz[0]', '', '$szamla_szall[2]', '$szallitas', '$fizetes', '$megr_ertek', '$szallitasi_dij', '$futar_kod', '$allapot')";
//				$result_rend_insert=mysql_query($query_rend_insert, $conn_main);
				
				//kideritjuk, mi a rend_id
				$result_rend_id = mysql_query("select rend_id from rendeles where webshop_id = '$webshop_id' and orig_id = '$orig_id'", $conn_main);
				list($rend_id) = mysql_fetch_row($result_rend_id);

				//megjegyzest beszurjuk
				$result_uzenet_insert = mysql_query("insert into uzenet (uzenet_ido, rend_id, felh_id, uzenet_szoveg) values ('$megr_datum_ido',$rend_id, '1', '$megr_uzenet')", $conn_main);

//////////    RENDELES ALAPADATOK END    //////////				


//////////    TERMEK ADATOK     //////////				

			$termek_osszeg = 0;

			$termek = preg_split( '/\r\n|\r|\n/', $megr_termek );

			for ($ciklus=0;$ciklus<count($termek);$ciklus++) {
				$termek_tul = explode("|", $termek[$ciklus]);

				//termek nev
				$result_termek_nev = mysql_query("select nev, cikkszam, katid from kar1_webaruhaz_termek_$termek_tul[4] where termekid = '$termek_tul[0]'", $conn);
				list($termek_nev, $termek_cikkszam, $katid) = mysql_fetch_row($result_termek_nev);

				//termekkod, ico
				$termek_nev_kieg = '';
				if ($termek_tul[4] == '7') {
					$query_termek_kod = "select ertek from kar1_webaruhaz_termektul_$termek_tul[4] where termekid = '$termek_tul[0]' and (megnevezes like 'TermÃ©kkÃ³d' or megnevezes like 'Termékkód')";
					$result_termek_kod = mysql_query($query_termek_kod, $conn);
					list($termek_kod)=mysql_fetch_row($result_termek_kod);
					if ($termek_nev_kieg =='' and $termek_kod !='') $termek_nev_kieg = $termek_kod;
				
					$query_termek_ico = "select ertek from kar1_webaruhaz_termektul_$termek_tul[4] where termekid = '$termek_tul[0]' and upper(megnevezes) like 'ICO'";
					$result_termek_ico = mysql_query($query_termek_ico, $conn);
					list($termek_ico)=mysql_fetch_row($result_termek_ico);
					
					if ($termek_ico !='') {
						if ($termek_nev_kieg =='') $termek_nev_kieg = $termek_ico;
						else $termek_nev_kieg = $termek_nev_kieg.", ".$termek_ico;
					}
					if ($termek_nev_kieg !='') $termek_nev_kieg = " ( ".$termek_nev_kieg." )";
				}
				$termek_nev = $termek_nev." ".$termek_cikkszam.$termek_nev_kieg;
				$termek_nev = fixEnc2($termek_nev);
				
				//marka_id
				$marka = trim(substr($termek_nev, 0, stripos($termek_nev, " ")+1));
				$result_marka = mysql_query("select marka_id from marka where upper(marka_nev) like upper('$marka')", $conn_main);
				list($marka_id) = mysql_fetch_row($result_marka);
				if(!$marka_id) {
					$marka = trim(substr($termek_cikkszam, 0, stripos($termek_cikkszam, " ")+1));
					$result_marka = mysql_query("select marka_id from marka where upper(marka_nev) like upper('$marka')", $conn_main);
					list($marka_id) = mysql_fetch_row($result_marka);
				}
				
				//termek_egysegar
				$egysegar = intval($termek_tul[2]);

				$termek_nev = fixEnc2($termek_nev);
				$termek_nev = str_ireplace($marka, "", $termek_nev);
				$termek_nev = trim($termek_nev);
				
					//beszuras adatbazisba
					$result_termek_insert = mysql_query("insert into termek (rend_id, termekid, termek_nev, marka_id, termek_egysegar, termek_db) values ('$rend_id', '$termek_tul[0]', '$termek_nev', '$marka_id', '$egysegar', '$termek_tul[3]')", $conn_main);
					$termek_osszeg += ($egysegar * $termek_tul[3]);

			}
			
			$query_aruhaz_update = "update rendeles set webshop_id = '$webshop_id' where rend_id = '$rend_id'";
			$result_aruhaz_update = mysql_query($query_aruhaz_update, $conn_main);

			rendOsszesit($rend_id);

//////////    TERMEK ADATOK END    //////////				

	}
	mysql_close($conn);
	
}


?>