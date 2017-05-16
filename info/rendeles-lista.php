<link rel="stylesheet" href="css/jquery-ui.css" />
<script src="js/jquery-1.8.3.js"></script>
<script src="js/jquery-ui.js"></script>
<script src="js/jquery.ui.datepicker-hu.js"></script>

<script type="text/javascript">
$(function() {
	$( "#datum1" ).datepicker({
		dateFormat: 'yy-mm-dd',
		firstDay: 1,
		regional: 'hu'
	});
});

$(function() {
	$( "#datum2" ).datepicker({
		dateFormat: 'yy-mm-dd',
		firstDay: 1,
		regional: 'hu'
	});
});


function checkDates() {
	var formx = document.getElementById("rendelesek");
	var ma = new Date();
	strMa = ma.toLocaleDateString();
	
	if(formx.datum2.value > strMa)  formx.datum2.value = strMa;
	if(formx.datum1.value > strMa)  formx.datum1.value = strMa;
	if(formx.datum2.value != '' && (formx.datum1.value > formx.datum2.value)) formx.datum1.value = formx.datum2.value;
	if(formx.datum1.value != '') formx.datum1.name = 'datum1';
	if(formx.datum2.value != '') formx.datum2.name = 'datum2';
	
}
</script>

<?
$sor_tipus = 0;
$str_cond = array();
$str_h1 = 'Rendelések';
$filter_allapot = 0;
$filter_szo = '';
$filter_nagyker = 0;

//print_r($_GET);
//print_r($_POST);

foreach($_POST as $rend_allapot=>$rend_allapot_value) {
	if(stristr($rend_allapot, 'allapot')) {
		$rend_allapot_id = str_ireplace('allapot', '', $rend_allapot);
		$result_allapot = mysql_query("update rendeles set allapot = $rend_allapot_value where rend_id = $rend_allapot_id", $conn_main);
		unset($_POST[$rend_allapot]);
	}
}

if (isset($_GET['osszes'])) $str_h1 = 'Minden rendelés, lezártak is';
else $str_h1 = 'Aktív rendelések';

if(isset($_GET['spec']) and $_GET['spec'] ==1) {
	if ($_GET['allapot'] == 4) $str_h1 = 'Beszerezhető rendelések';
	else if ($_GET['allapot'] == 3) $str_h1 = 'Átutalásra váró rendelések';
	if ($_GET['problemas'] == 1) $str_h1 = 'Problémás rendelések';
	if ($_GET['elfelejtett'] == 1) $str_h1 = 'Elfelejtett rendelések';
}

if(isset($_GET['allapot'])) {
	$filter_allapot = $_GET['allapot'];
}
if(isset($_GET['keresoszo'])) {
	$filter_szo = $_GET['keresoszo'];
}
if(isset($_GET['nagyker'])) {
	$filter_nagyker = $_GET['nagyker'];
}

	$result_allapot = mysql_query("select allapot_id, allapot_nev from allapot order by allapot_id asc", $conn_main);
	while ($allapot[] = mysql_fetch_array($result_allapot));

	$result_webshop_lista = mysql_query("select webshop_id, webshop_nev from webshop", $conn_main);
	while ($webshop_lista[] = mysql_fetch_array($result_webshop_lista));

	$result_nagyker = mysql_query("select nagyker_id, nagyker_nev from nagyker", $conn_main);
	while ($nagyker[] = mysql_fetch_array($result_nagyker));

	echo "<h1>$str_h1</h1>\n";

/*
	$url_pattern = "/\&oldal=(\d*)/";
	$url_subject = $_SERVER['REQUEST_URI'];
	$url_replaced = preg_replace($url_pattern, '', $url_subject);
	echo $_SERVER['REQUEST_URI']."<br>";
	echo $url_replaced."<br/>";
	echo "<form id='rendelesek' action='".$url_replaced."' method='get'>
*/

	echo "<form id='rendelesek' action='".$_SERVER['REQUEST_URI']."' method='get'>
		<input type='hidden' name='inf' value='".$_GET['inf']."'>";
	if (isset($_GET['spec']) and $_GET['spec'] == '1') echo "<input type='hidden' name='spec' value='1'>";
	if (isset($_GET['problemas']) and $_GET['problemas'] == '1') echo "<input type='hidden' name='problemas' value='1'>";
	if (isset($_GET['elfelejtett']) and $_GET['elfelejtett'] == '1') echo "<input type='hidden' name='elfelejtett' value='1'>";
	echo "<table cellspacing='0' cellpadding='0'>
			<tr>
				<td></td>";
	if (!isset($_GET['spec'])) echo "<td>Állapot</td>";
/*2015.07.06.
				echo "<td>Nagyker</td>";
*/
				echo "<td>Keresőszó</td>
				<td colspan ='2'>Rendelés feladás dátuma</td>
			</tr>
			<tr>
				<td><input type='submit' style='color:#00a;font-size:13px;border:none;background:#fff;cursor:pointer;' value='Szűrés' onclick='checkDates()'>&nbsp;&nbsp;</td>";
	if (isset($_GET['spec'])) {
		echo "<input type='hidden' name='allapot' value='$filter_allapot'>";
	}
	else {
		echo "<td><select name='allapot'>
				<option "; if($filter_allapot == 0) echo "selected='selected' "; echo "value='0'> - összes - </option>";
		foreach ($allapot as $all) {
			if($all[0]) {
				echo "<option "; if($all[0] == $filter_allapot) echo "selected='selected' "; echo "value='".$all[0]."'>".$all[1]."</option>";
			}
		}
	}
	echo "</select>&nbsp;&nbsp;</td>";
/*2015.07.06.
	echo "<td><select name='nagyker'>
				<option "; if($filter_nagyker == 0) echo "selected='selected' "; echo "value='0'> - összes - </option>";
	foreach ($nagyker as $nk) {
		if($nk[0]) {
			echo "<option "; if($nk[0] == $filter_nagyker) echo "selected='selected' "; echo "value='".$nk[0]."'>".$nk[1]."</option>";
		}
	}
	echo "</select>&nbsp;&nbsp;</td>";
*/

/*
	echo "<td><select name='webshop'>
				<option "; if($filter_webshop == 0) echo "selected='selected' "; echo "value='0'> - összes - </option>";
			foreach ($webshop_lista as $ws) {
				if($ws[0]) {
					echo "<option "; if($ws[0] == $filter_webshop) echo "selected='selected' "; echo "value='".$ws[0]."'>".$ws[1]."</option>";
				}
			}
			echo "</select></td>";
*/			
			echo "<td><input type='text' size='20' name='keresoszo' value='$filter_szo'>&nbsp;&nbsp;</td>";
			echo "<td><input type='text' id='datum1' size='8' onchange='checkDates()' ";
			if (isset($_GET['datum1'])) echo "value='".$_GET['datum1']."'";
			echo "/>&nbsp;-&nbsp;</td>";
			echo "<td><input type='text' id='datum2' size='8' onchange='checkDates()' ";
			if (isset($_GET['datum2'])) echo "value='".$_GET['datum2']."'";
			echo "/>&nbsp;&nbsp;</td>";
			if (isset($_GET['elfelejtett']) and $_GET['elfelejtett'] == '1') {
				if(!isset($_GET['felejt_nap']) or !(is_numeric($_GET['felejt_nap']))) $_GET['felejt_nap'] = 5;
				echo "<td>&nbsp;Ennél régebben volt módosítva:
				<input type='text' size='1' name='felejt_nap' value='".$_GET['felejt_nap']."'> nap&nbsp;&nbsp;</td>";
			}
			if (!isset($_GET['spec'])) {
				echo "<td>&nbsp;<input type='checkbox' name='osszes' value='1'";
				if ($_GET['osszes']=='1') echo " checked='checked'";
				echo ">Lezártak is</td>";
			}
//			if(isset($_GET['oldal'])) echo "<input type='hidden' name='oldal' value='".$_GET['oldal']."'";
			echo "</tr></table></form><br style='line-height:15px;' />\n";

	echo "<table class='normal' cellspacing='0' cellpadding='0'>
		<tr>
			<td class='normal-head'>ID</td>";
	if (!isset($_GET['spec'])) echo "<td class='normal-head'>Állapot</td>";
	echo "<td class='normal-head'>Dátum</td>
			<td class='normal-head'>Szállítás/fiz.</td>
			<td class='normal-head'>Név</td>
			<td class='normal-head'>Cím</td>
			<td class='normal-head'>Termék</td>
		</tr>\n";

		$query_rend = "select rend_id, webshop_id, orig_id, rend_datum, rend_ido, nev, irsz, varos, utca, termek_osszeg, szallitas, fizetes, szall_dij, allapot from rendeles";
		$feltetel ='';
		if ($filter_allapot != 0) {
			if (count($str_cond) > 0) {
				array_push($str_cond, " and ");
			}
			array_push($str_cond, "allapot='$filter_allapot'");
		}

/*2015.07.06.
		if ($filter_nagyker != 0) {
			if (count($str_cond) > 0) {
				array_push($str_cond, " and ");
			}
			array_push($str_cond, "rend_id in (select rend_id from termek where marka_id in (select marka_id from marka where nagyker_id='$filter_nagyker'))");
		}
*/
		if (!isset($_GET['osszes'])) {
			if (count($str_cond) > 0) {
				array_push($str_cond, " and ");
			}
			array_push($str_cond, "allapot in (select allapot_id from allapot where allapot_aktiv = '1')");
		}
		if($filter_szo !='') {
			if (count($str_cond) > 0) {
				array_push($str_cond, " and ");
			}
			array_push($str_cond, "(rend_id like '%$filter_szo%' 
			or orig_id like '%$filter_szo%' 
			or nev like '%$filter_szo%' 
			or varos like '%$filter_szo%' 
			or utca like '%$filter_szo%' 
			or email like '%$filter_szo%' 
			or szamla_nev like '%$filter_szo%' 
			or szamla_varos like '%$filter_szo%' 
			or szamla_utca like '%$filter_szo%' )");
//			or rend_id in (select rend_id from uzenet where uzenet_szoveg like '%$filter_szo%') 
//			or rend_id in (select rend_id from termek where termek_nev like '%$filter_szo%'))");
//			array_push($str_cond, "(rend_id like '%$filter_szo%' or orig_id like '%$filter_szo%' or nev like '%$filter_szo%')");
		}
		
		if ($_GET['elfelejtett'] == 1) {
			if (count($str_cond) > 0) {
				array_push($str_cond, " and ");
			}
			$felejt_nap = 5;
			array_push($str_cond, "mod_ido < '".date("Y-m-d", time() - ($_GET['felejt_nap'] * 86400))." 00:00:00'");
		}
		
		if (isset($_GET['datum1'])) {
			if (count($str_cond) > 0) {
				array_push($str_cond, " and ");
			}
			array_push($str_cond, "rend_datum >= '".$_GET['datum1']."'");
		}

		if (isset($_GET['datum2'])) {
			if (count($str_cond) > 0) {
				array_push($str_cond, " and ");
			}
			array_push($str_cond, "rend_datum <= '".$_GET['datum2']."'");
		}
		
		if ($_GET['problemas'] == 1) {
			if (count($str_cond) > 0) {
				array_push($str_cond, " and ");
			}
			array_push($str_cond, "problema = '1'");
		}
		if (count($str_cond) > 0) {
			array_push($str_cond, " and ");
		}
		array_push($str_cond, "ellenorizve = '1'");

		if (count($str_cond) > 0) {
			$feltetel = $feltetel." where ";
			foreach($str_cond as $item_cond) {
				$feltetel = $feltetel.$item_cond;
			}
		}
		$kezd = 0;
		if (isset($_GET['oldal'])) $kezd = ($_GET['oldal'] - 1) * $sorok_szama;
		$query_rend = $query_rend.$feltetel." order by rend_id desc limit $kezd, $sorok_szama";
//echo $query_rend;
		$query_numrows = "select count(*) from rendeles".$feltetel;
		$result_numrows = mysql_query($query_numrows, $conn_main);
		list($numrows) = mysql_fetch_row($result_numrows);
		$result_rend = mysql_query($query_rend, $conn_main);
		while (list($rend_id, $webshop_id, $orig_id, $rend_datum, $rend_ido, $nev, $irsz, $varos, $utca, $termek_osszeg, $szallitas, $fizetes, $szall_dij, $allapot_id)=mysql_fetch_row($result_rend)) {
			unset($termek_info);
			$result_webshop = mysql_query("select webshop_nev from webshop where webshop_id = $webshop_id", $conn_main);
			list($webshop_nev) = mysql_fetch_row($result_webshop);

//			$result_allapot = mysql_query("select allapot_nev from allapot where allapot_id = $allapot_id", $conn_main);
//			list($allapot) = mysql_fetch_row($result_allapot);

			$result_termek = mysql_query("select termek_nev, termek_db from termek where rend_id = $rend_id", $conn_main);
			while ($termek_info[] = mysql_fetch_array($result_termek));

			$result_szallitas = mysql_query("select szallitas_nev from szallitas where szallitas_id = $szallitas", $conn_main);
			list($szall) = mysql_fetch_row($result_szallitas);

			$result_fizetes = mysql_query("select fizetes_nev from fizetes where fizetes_id = $fizetes", $conn_main);
			list($fiz) = mysql_fetch_row($result_fizetes);

			echo "<tr class='row$sor_tipus'>
					<td class='cell$sor_tipus'><a href='?inf=rendeles&id=$rend_id' class='cell-link'>$rend_id</a><span style='margin-left:15px;;'>$orig_id</td>";
	if (!isset($_GET['spec'])) {
//		echo "<td class='cell$sor_tipus'>".str_ireplace(" ", "&nbsp;", $allapot)."</td>";
		echo "<td class='cell$sor_tipus'><form name='status$rend_id' action='".$_SERVER['REQUEST_URI']."' method='post'>
		<select name='allapot$rend_id' onchange='document.status$rend_id.submit()'>";
	
		foreach ($allapot as $all) {
			if($all[0]) {
				echo "<option ";
				if($all[0] == $allapot_id) echo "selected='selected' ";
				echo "value='".$all[0]."'>".$all[1]."</option>";
			}
		}
	echo "</select></form></td>";
	}

	
	
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
	echo "</table>";

//lapozas

	$oldalak = ceil($numrows / $sorok_szama);
	echo "<div style='margin: 5px;text-align:left;width:100%;'>
	A szűkítési feltételeknek megfelel: <b>$numrows</b> rendelés.
	</div>";
	
	echo "<div style='margin: 5px;text-align:center;width:100%;'>";
	
	if($oldalak > 1) {
		$thisurl = $_SERVER['REQUEST_URI'];
		
		if(!stristr($thisurl, "&oldal=")) $thisurl = $thisurl."&oldal=1";

		$refurl = preg_replace("/&oldal=([0-9])*/","&oldal=1",$thisurl);
		echo "<a href ='".$refurl."' class='oldalszam' style='margin-right:15px;'><<</a>";
		if(!isset($_GET['oldal'])) $_GET['oldal'] = 1;
		
		if($_GET['oldal'] > 1) {
			$refurl = preg_replace("/&oldal=([0-9])*/","&oldal=".($_GET['oldal']-1),$thisurl);
			echo "<a href ='".$refurl."' class='oldalszam' style='margin-right:15px;'><</a>";
		}
		for($ciklus = 1;$ciklus <= $oldalak;$ciklus++) {
			if($_GET['oldal'] == $ciklus) {
				echo " <span style='font-size:150%;color:#000;border:0px 10px;font-weight:bold;'>".$ciklus."</span>";
			}
			else {
				$refurl = preg_replace("/&oldal=([0-9])*/","&oldal=".$ciklus,$thisurl);
				echo " <a href ='".$refurl."' class='oldalszam'>$ciklus</a>";
			}
		}
		if($_GET['oldal'] < $oldalak) {
			$refurl = preg_replace("/&oldal=([0-9])*/","&oldal=".($_GET['oldal']+1),$thisurl);
			echo "<a href ='".$refurl."' class='oldalszam' style='margin-left:15px;'>></a>";
		}
		$refurl = preg_replace("/&oldal=([0-9])*/","&oldal=".$oldalak,$thisurl);
		echo "<a href ='".$refurl."' class='oldalszam' style='margin-left:15px;'>>></a>";
	}
	echo "</div>";

?>


