<link rel="stylesheet" href="css/jquery-ui.css" />
<script src="js/jquery-1.8.3.js"></script>
<script src="js/jquery-ui.js"></script>


<?
$sor_tipus = 0;
$str_cond = array();

//print_r($_GET);
//print_r($_POST);

foreach(array_keys($_POST) as $csomag) {
	$csomag_id = str_ireplace('mehet', '', $csomag);
	$result_csomag = mysql_query("select nev from rendeles where rend_id = '$csomag_id'", $conn_main);
	while ($csomag_sor[] = mysql_fetch_array($result_csomag));
	if(sizeof($csomag_sor[0])>0) { $van_csomag = 1; break;}
}

if($van_csomag > 0) {
	unset($csomag_sor);
	ob_end_clean();
	ob_start();
	

	header('Content-Type: text/csv');
	if(isset($_POST['dpd'])) {
		header('Content-Disposition: attachement; filename="DPD-import-'.date("Ymd-His").'.csv"');
	}
	elseif(isset($_POST['gls'])) {
		header('Content-Disposition: attachement; filename="GLS-import-'.date("Ymd-His").'.csv"');
	}
	header('Pragma: no-cache');
	header("Expires: 0");

	foreach(array_keys($_POST) as $csomag) {
		if(substr($csomag, 0, 5) == 'mehet') {
			$csomag_id = str_ireplace('mehet', '', $csomag);
			$result_csomag = mysql_query("select nev, irsz, varos, utca, telefon, email, termek_osszeg+szall_dij as utanvet, fizetes, rend_id, orig_id from rendeles where rend_id = '$csomag_id'", $conn_main);
			list($nev, $irsz, $varos, $utca, $telefon, $email, $utanvet, $fizetes, $rend_id, $orig_id)=mysql_fetch_row($result_csomag);
			$result_uzenet = mysql_query("select uzenet_szoveg from uzenet where rend_id = '$csomag_id' and felh_id = 13", $conn_main);
			list($uzenet) = mysql_fetch_row($result_uzenet);
			$uzenet = str_replace("\n"," ",$uzenet);
			$uzenet = str_replace("\r"," ",$uzenet);
			
			$fizetendo = '';
			$penznem='';
			if($fizetes == '1') {
				$dpd_header = "D-CODEX;;$utanvet;";
				$fizetendo = $utanvet;
				$penznem="HUF";
			}
			elseif($fizetes == '2') {
				$dpd_header = "D;;;";
				$fizetendo = '';
				$penznem='';
			}
			
			if(isset($_POST['dpd'])) {
				print_r($dpd_header.str_replace(";",",",$orig_id)."/".str_replace(";",",",$rend_id).";;;".str_replace(";",",",$nev).";;".str_replace(";",",",$utca).";;H;".str_replace(";",",",$irsz).";".str_replace(";",",",$varos).";".str_replace(";",",",$telefon).";;;".str_replace(";",",",$uzenet)."\r\n");
			}
			elseif(isset($_POST['gls'])) {
				print_r($fizetendo.";".$penznem.";".str_replace(";",",",$orig_id)."/".str_replace(";",",",$rend_id).";;;".str_replace(";",",",$nev).";".str_replace(";",",",$utca).";Magyarország;".str_replace(";",",",$irsz).";".str_replace(";",",",$varos).";".str_replace(";",",",$telefon).";".str_replace(";",",",$email).";".str_replace(";",",",$uzenet).";FSS()\r\n");
			}
		
		
			$result_futar_ok = mysql_query("update rendeles set futar_ok = 1 where rend_id=$csomag_id", $conn_main);
			mysql_query("update rendeles set aktiv = 0, allapot = 8 WHERE szamla_ok = 1 and futar_ok = 1 and rend_id = $csomag_id", $conn_main);
		}
	}
	
	ob_end_flush();
	die();
}


	echo "<h1>Futár importfájl generálás</h1>\n";

	$query_rend = "select rend_id, webshop_id, orig_id, rend_datum, rend_ido, nev, irsz, varos, utca, termek_osszeg, szallitas, fizetes, szall_dij, allapot from rendeles where futar_ok=0 and (allapot='6' or allapot='5') and (szallitas='2' or szallitas='4')";

	$query_numrows = "select count(*) from rendeles where futar_ok=0 and (allapot='6' or allapot='5') and (szallitas='2' or szallitas='4')";
	$result_numrows = mysql_query($query_numrows, $conn_main);
	list($numrows) = mysql_fetch_row($result_numrows);

	echo "<form name='futar' action='".$_SERVER['REQUEST_URI']."' method='post'>";
	echo "<div style='margin: 5px;text-align:left;width:100%;'>
	Futárnak feladható: <b>$numrows</b> rendelés.";
	echo"<div style='display:inline;margin-left:100px;'><input type='submit' name='dpd' value='DPD fájl'></div><div style='display:inline;margin-left:25px;'><input type='submit' name='gls' value='GLS fájl'></div>";
	echo "</div>";

	echo "<table class='normal' cellspacing='0' cellpadding='0'>
		<tr>
			<td class='normal-head'>Mehet</td>
			<td class='normal-head'>ID</td>";
	echo "<td class='normal-head'>Dátum</td>
			<td class='normal-head'>Szállítás/fiz.</td>
			<td class='normal-head'>Név</td>
			<td class='normal-head'>Cím</td>
			<td class='normal-head'>Termék</td>
		</tr>\n";

		$query_rend = $query_rend.$feltetel." order by rend_id desc";
//echo $query_rend;
		$result_rend = mysql_query($query_rend, $conn_main);
		while (list($rend_id, $webshop_id, $orig_id, $rend_datum, $rend_ido, $nev, $irsz, $varos, $utca, $termek_osszeg, $szallitas, $fizetes, $szall_dij, $allapot_id)=mysql_fetch_row($result_rend)) {
			unset($termek_info);

			$result_termek = mysql_query("select termek_nev, termek_db from termek where rend_id = $rend_id", $conn_main);
			while ($termek_info[] = mysql_fetch_array($result_termek));

			$result_szallitas = mysql_query("select szallitas_nev from szallitas where szallitas_id = $szallitas", $conn_main);
			list($szall) = mysql_fetch_row($result_szallitas);

			$result_fizetes = mysql_query("select fizetes_nev from fizetes where fizetes_id = $fizetes", $conn_main);
			list($fiz) = mysql_fetch_row($result_fizetes);


			echo "<tr class='row$sor_tipus'>
					<td class='cell$sor_tipus'><input type='checkbox' name='mehet$rend_id'></td>
					<td class='cell$sor_tipus'><a href='?inf=rendeles&id=$rend_id' class='cell-link'>$rend_id</a><span style='margin-left:15px;;'>$orig_id</td>";
			echo "<td class='cell$sor_tipus'>$rend_datum&nbsp;$rend_ido</td>
					<td class='cell$sor_tipus'>$szall, $fiz</td>
					<td class='cell$sor_tipus' style='max-width:150px;'>$nev</td>
					<td class='cell$sor_tipus'>$irsz $varos $utca</td>
					<td class='cell$sor_tipus' style='max-width:350px;'>";
					foreach ($termek_info as $ti) {
						if ($ti[0] !='') echo $ti[0]." (".$ti[1]." db), ";
					}
					echo number_format(($termek_osszeg+$szall_dij), 0, '.', ' ')." Ft</td>
				</tr>\n";
			$sor_tipus = ($sor_tipus+1)%2;
		}
	echo "</table>\n</form>";

?>


