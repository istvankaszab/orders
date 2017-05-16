<?

function rendeles_check_karoracentrum ($webshop_id, $webshop_host, $webshop_db, $webshop_user, $webshop_pwd) {

	global $conn_main;
	
	// adatbazis csatlakozas
	if($conn=mysql_connect("$webshop_host","$webshop_user","$webshop_pwd")) {
		if ($db_select = mysql_select_db("$webshop_db",$conn)) { }
		else { echo "$db_name adatbázis csatlakozás sikertelen! <br/>"; }
	}
	else { echo "$db_host csatlakozás sikertelen! 1<br/>"; }
	
	$result_max_id = mysql_query("select max(orig_id) from rendeles where webshop_id ='2' or webshop_id ='6'", $conn_main);
	list($max_id) = mysql_fetch_row($result_max_id);
	$result_rendeles = mysql_query("select kosar.megrendelt_sorsz, kosar.datum, kosar.ido from kosar where kosar.megrendelt_sorsz > '$max_id'", $conn);
	while (list($temp_orig_id, $temp_datum, $temp_ido)=mysql_fetch_row($result_rendeles)) {
		if ($temp_ido ='00:00:00') $temp_ido = idate("H").':'.idate("i").':'.idate("s");
		$result_temp_update = mysql_query("insert into temp_rendeles (temp_webshop_id, temp_orig_id, temp_datum, temp_ido) values ('$webshop_id', '$temp_orig_id', '$temp_datum', '$temp_ido')", $conn_main);
	}
	mysql_close($conn);
}




function rendeles_download_karoracentrum ($orig_id) {

	global $conn_main;

	$result_webshop = mysql_query("select * from webshop where webshop_id = '2'", $conn_main);
	list($webshop_id, $webshop_nev, $webshop_host, $webshop_db, $webshop_user, $webshop_pwd, $webshop_tip)=mysql_fetch_row($result_webshop);

// adatbazis csatlakozas
		if($conn=mysql_connect("$webshop_host","$webshop_user","$webshop_pwd")) {
			if ($db_select = mysql_select_db("$webshop_db",$conn)) {
			}
			else { echo "$db_name adatbázis csatlakozás sikertelen! <br/>"; }
		}
		else { echo "$db_host csatlakozás sikertelen! 2<br/>";	}


		$result_rend=mysql_query("select kosar_id, datum, kid, ido, megrendelt_sorsz, megjegyzes from kosar where megrendelt_sorsz='$orig_id'", $conn);
		while (list($kosar_id, $kosar_datum, $kosar_kid, $kosar_ido, $kosar_megrendelt_sorsz, $kosar_megjegyzes)=mysql_fetch_row($result_rend)) {

			if ($kosar_ido ='00:00:00') $kosar_ido = idate("H").':'.idate("i").':'.idate("s");
			
			$result_felh = mysql_query("select fid from cookies where cookieid = '$kosar_id'", $conn);
			list($fid) = mysql_fetch_row($result_felh);
			
			$result_kiszall=mysql_query("select telepules, irsz, utca, kapcsvnev, kapcsknev, kapcstel from kiszallitas where fid = '$fid'", $conn);
			list($telepules, $irsz, $utca, $kapcsvnev, $kapcsknev, $kapcstel)=mysql_fetch_row($result_kiszall);

//////////    RENDELES ALAPADATOK     //////////				
			
			$result_felh=mysql_query("select vnev, knev, telepules, irsz, utca, email, tel from felhasznalok where fid = '$fid'", $conn);
			$felh = mysql_fetch_array($result_felh);
			if($kapcsvnev == "" and $kapcsknev == "") {
				$kapcsvnev = $felh[0];
				$kapcsknev = $felh[1];
			}
			if($telepules=="" or $utca=="") {
				$telepules = $felh[2];
				$irsz = $felh[3];
				$utca = $felh[4];
			}
			if($kapcstel=="") $kapcstel = $felh[6];
			$email = $felh[5];

			//kodolas beallitasa
			//kiszallitasi adatok
			$user_id = $webshop_db."_".$fid;
			$telepules = fixEnc2($telepules);
			$irsz = fixEnc2($irsz);
			$utca = fixEnc2($utca);
			$kapcsnev = $kapcsvnev." ".$kapcsknev;
			$kapcsnev = fixEnc2($kapcsnev);
			$email = fixEnc2($email);
			$kosar_megjegyzes = fixEnc2($kosar_megjegyzes);
			$kapcstel = fixEnc2($kapcstel);
			$kapcstel = str_ireplace(' ', '', $kapcstel);
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

			$result_szamla=mysql_query("select telepules, irsz, utca, nev from szamlazas where fid = '$fid'", $conn);
			list($szamla_telepules, $szamla_irsz, $szamla_utca, $szamla_nev)=mysql_fetch_row($result_szamla);
			//szamlazasi adatok
			$szamla_telepules = fixEnc2($szamla_telepules);
			$szamla_irsz = fixEnc2($szamla_irsz);
			$szamla_utca = fixEnc2($szamla_utca);
			$szamla_nev = fixEnc2($szamla_nev);
				
			//kiszallitas modja es fizetes meghatarozasa
			//futar, utanvet
				if ($kosar_kid == 5 or $kosar_kid == 9) {
					$szallitas = 2;
					$fizetes = 1;
				}
				//futar, eloreutalas
				elseif ($kosar_kid == 6 or $kosar_kid == 10) {
					$szallitas = 2;
					$fizetes = 2;
				}
				//szemelyes atvetel, keszpenz
				elseif ($kosar_kid == 8 or $kosar_kid == 11) {
					$szallitas = 1;
					$fizetes = 3;
				}
				//kulfold, eloreutalas
				elseif ($kosar_kid == 7 or $kosar_kid == 12) {
					$szallitas = 4;
					$fizetes = 2;
				}
				
				// osszeg, szallitasi dij nullara allitas
				$termek_osszeg = 0;
				$szallitasi_dij = 0;
				
				//ures a futar kod
				$futar_kod = "";
				// rendeles allapota: uj (1)
				$allapot = 1;
				
				newRend($webshop_id, $orig_id, $kosar_datum, $kosar_ido, $user_id, $kapcsnev, $irsz, $telepules, $utca, $kapcstel, $email, $szamla_nev, $szamla_irsz, $szamla_telepules, $szamla_utca, $szallitas, $fizetes, $termek_osszeg, $szallitasi_dij, $futar_kod, $allapot, '');

//				$query_rend_insert = "insert into rendeles (webshop_id, orig_id, rend_datum, rend_ido, nev, irsz, varos, utca, telefon, email, szamla_nev, szamla_irsz, szamla_varos, szamla_utca, szallitas, fizetes, termek_osszeg, szall_dij, futar_kod, allapot) values ('$webshop_id', '$orig_id', '$kosar_datum', '$kosar_ido', '$kapcsnev', '$irsz', '$telepules', '$utca', '$kapcstel', '$email', '$szamla_nev', '$szamla_irsz', '$szamla_telepules', '$szamla_utca', '$szallitas', '$fizetes', '$termek_osszeg', '$szallitasi_dij', '$futar_kod', '$allapot')";
//				$result_rend_insert=mysql_query($query_rend_insert, $conn_main);

//////////    RENDELES ALAPADATOK END    //////////				


//////////    TERMEK ADATOK     //////////				
				
				//kideritjuk, mi a rend_id
				$result_rend_id = mysql_query("select rend_id from rendeles where webshop_id = '$webshop_id' and orig_id = '$orig_id'", $conn_main);
				list($rend_id) = mysql_fetch_row($result_rend_id);

				//megjegyzest beszurjuk
				$result_uzenet_insert = mysql_query("insert into uzenet (uzenet_ido, rend_id, felh_id, uzenet_szoveg) values ('".$kosar_datum." ".$kosar_ido."', $rend_id, '1', '$kosar_megjegyzes')", $conn_main);

				//kideritjuk, mi a kosar azonositoja
//				$query_kosar = "select kosar_id from kosar where megrendelt_sorsz = '$orig_id'";
//				$result_kosar = mysql_query($query_kosar, $conn);
//				list($kosar_id) = mysql_fetch_row($result_kosar);
				
				//lekérdezzuk a kosar tartalmat
				$result_termek = mysql_query("select termekid, egysegar, mennyiseg from kosar_tartalom where kosar_id ='$kosar_id'", $conn);
				$termek_osszeg = 0;

				while (list($termekid, $egysegar, $mennyiseg)=mysql_fetch_row($result_termek)) {

//echo "<br>$termekid | $egysegar | $mennyiseg<br>";

					$result_termek_tip = mysql_query("select termekid from termek_4 where termekid = '$termekid'", $conn);
					$num_rows = mysql_num_rows($result_termek_tip);
					if ($num_rows > 0) {
						$webshop_id = 6;
						$termek_tip = '_4';
					}
					else {
						$result_termek_tip = mysql_query("select termekid from termek_1 where termekid = '$termekid'", $conn);
						$num_rows = mysql_num_rows($result_termek_tip);
						if ($num_rows > 0) {
							$webshop_id = 2;
							$termek_tip = '_1';
						}
					}


					$result_tcsid = mysql_query("select min(tcsid) from termektermekcsoport$termek_tip where termekid = '$termekid'", $conn);
					list($tcsid) = mysql_fetch_row($result_tcsid);
					
					$result_tcsid = mysql_query("select szuloid from termekcsoport$termek_tip where tcsid = '$tcsid'", $conn);
					list($szuloid) = mysql_fetch_row($result_tcsid);
					if ($szuloid != 1) {
						$tcsid = $szuloid;
						$result_tcsid = mysql_query("select szuloid from termekcsoport$termek_tip where tcsid = '$tcsid'", $conn);
						list($szuloid) = mysql_fetch_row($result_tcsid);
					}
					
					$result_tcsnev = mysql_query("select nev from termekcsoport$termek_tip where tcsid = '$tcsid'", $conn);
					list($tcsnev) = mysql_fetch_row($result_tcsnev);

					$tcsnev = fixEnc2($tcsnev);
					$tcsnev = str_ireplace("számológép", "", $tcsnev);
					$tcsnev = str_ireplace("órák", "", $tcsnev);
					$marka = trim($tcsnev);
					$result_marka = mysql_query("select marka_id from marka where upper(marka_nev) like upper('$marka')", $conn_main);
					list($marka_id) = mysql_fetch_row($result_marka);
					
					$result_termek_nev = mysql_query("select nev from termek$termek_tip where termekid = '$termekid'", $conn);
					list($termek_nev) = mysql_fetch_row($result_termek_nev);

					$termek_nev = fixEnc2($termek_nev);
					$termek_nev = str_ireplace($marka, "", $termek_nev);
					$termek_nev = trim($termek_nev);
					
					//beszuras adatbazisba
					$query_termek_insert = "insert into termek (rend_id, termekid, termek_nev, marka_id, termek_egysegar, termek_db) values ('$rend_id', '$termekid', '$termek_nev', '$marka_id', '$egysegar', '$mennyiseg')";

//echo $query_termek_insert;					

					$result_termek_insert = mysql_query($query_termek_insert, $conn_main);
					$termek_osszeg += ($egysegar * $mennyiseg);
				}

				$query_aruhaz_update = "update rendeles set webshop_id = '$webshop_id' where rend_id = '$rend_id'";
				$result_aruhaz_update = mysql_query($query_aruhaz_update, $conn_main);

				rendOsszesit($rend_id);
				
//////////    TERMEK ADATOK END    //////////				

		}
		mysql_close($conn);
	
}


?>