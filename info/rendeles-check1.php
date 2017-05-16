<script type='text/javascript'>
function copyData($rend_id){
	var $rend_azon = 'check' + $rend_id;
	var formx = document.getElementById($rend_azon);
	formx.szamla_nev.value = formx.nev.value;
	formx.szamla_irsz.value = formx.irsz.value;
	formx.szamla_varos.value = formx.varos.value;
	formx.szamla_utca.value = formx.utca.value;
}
</script>


<?
$sor_tipus = 0;
$str_cond = array();
$str_h1 = 'Új rendelések ellenőrzése';
$filter_allapot = 0;
$filter_szo = '';
$filter_nagyker = 0;

//print_r($_GET);
//print_r($_POST);

if(isset($_POST['rend'])) {
	$query = "update rendeles set 
	nev = '".trim($_POST['nev'])."', 
	irsz = '".trim($_POST['irsz'])."', 
	varos = '".trim($_POST['varos'])."', 
	utca = '".trim($_POST['utca'])."', 
	telefon = '".trim($_POST['telefon'])."', 
	szamla_nev = '".trim($_POST['szamla_nev'])."', 
	szamla_irsz = '".trim($_POST['szamla_irsz'])."', 
	szamla_varos = '".trim($_POST['szamla_varos'])."', 
	szamla_utca = '".trim($_POST['szamla_utca'])."', 
	ellenorizve = 1 
	where rend_id = ".$_POST['rend'];

	mysql_query($query, $conn_main);

	$query = "update rendeles set allapot = 5 where allapot = 4 and rend_id = ".$_POST['rend'];
	mysql_query($query, $conn_main);
}

mysql_query("update rendeles set ellenorizve = 1 where szallitas = 1", $conn_main);

	echo "<h1>$str_h1</h1>\n";
	echo "<br style='line-height:10px;' />\n";

	echo "<table class='normal' cellspacing='0' cellpadding='0'>
		<tr><td class='id-head' rowspan='2'>ID</td>
			<td class='id-head' rowspan='2'>Dátum</td>
			<td class='normal-head' colspan='5'>Szállítás</td>
			<td class='id-head' rowspan='2'>COPY</td>
			<td class='szamla-head' colspan='4'>Számla</td>
			<td class='id-head' rowspan='2'>Mentés</td>
		</tr>
		<tr>
			<td class='normal-head'>Név</td>
			<td class='normal-head'>Irsz.</td>
			<td class='normal-head'>Város</td>
			<td class='normal-head'>Utca</td>
			<td class='normal-head'>Telefon</td>
			<td class='szamla-head'>Név</td>
			<td class='szamla-head'>Irsz.</td>
			<td class='szamla-head'>Város</td>
			<td class='szamla-head'>Utca</td>
		</tr>\n";

		$query_rend = "select rend_id, orig_id, rend_datum, nev, irsz, varos, utca, telefon, szamla_nev, szamla_irsz, szamla_varos, szamla_utca from rendeles where ellenorizve = 0 and (szallitas = 2 or szallitas = 3 or szallitas = 4)";
		$kezd = 0;
		if (isset($_GET['oldal'])) $kezd = ($_GET['oldal'] - 1) * $sorok_szama;
		$query_rend = $query_rend." order by rend_id desc limit $kezd, $sorok_szama";
		$query_numrows = "select count(*) from rendeles where ellenorizve = 0 and (szallitas = 2 or szallitas = 3 or szallitas = 4)";
		$result_numrows = mysql_query($query_numrows, $conn_main);
		list($numrows) = mysql_fetch_row($result_numrows);
		$result_rend = mysql_query($query_rend, $conn_main);
		while (list($rend_id, $orig_id, $rend_datum, $nev, $irsz, $varos, $utca, $telefon, $szamla_nev, $szamla_irsz, $szamla_varos, $szamla_utca)=mysql_fetch_row($result_rend)) {
			unset($termek_info);
			$result_dobbenetes = mysql_query("select count(rend_id) from uzenet where rend_id = $rend_id and upper(uzenet_szoveg) like '%BENETES%'", $conn_main);
			$dobbenetes = 0;
			$dobbenet_szoveg = "";
			list($dobbenetes) = mysql_fetch_row($result_dobbenetes);
			if($dobbenetes == 1) $dobbenet_szoveg = "  <span style='color:#f00;font-weight:bold;'>D</span>";


			echo "<tr class='row$sor_tipus'>
				<form id='check$rend_id' name='check$rend_id' action='".$_SERVER['REQUEST_URI']."' method='post'>
					<input type='hidden' name='rend' value='$rend_id'>
					<td class='cell$sor_tipus'><a href='?inf=rendeles&id=$rend_id' class='cell-link'>$rend_id</a><span style='margin-left:5px;'>$orig_id$dobbenet_szoveg</td>
					<td class='cell$sor_tipus'>$rend_datum</td>
					<td class='cell$sor_tipus'><input type='text' name ='nev' class='input-check' size='15' value='$nev'></td>
					<td class='cell$sor_tipus'><input type='text' name ='irsz' class='input-check' size='4' value='$irsz'></td>
					<td class='cell$sor_tipus'><input type='text' name ='varos' class='input-check' size='12' value='$varos'></td>
					<td class='cell$sor_tipus'><input type='text' name ='utca' class='input-check' size='30' value='$utca'></td>
					<td class='cell$sor_tipus'><input type='text' name ='telefon' size='11' class='input-check' value='$telefon'></td>
					<td class='cell$sor_tipus' style='text-align:center;'><a href='javascript:void();' onclick='copyData($rend_id);'  style='font-weight:900;color:#a00;font-size:13px;text-decoration:none;' title='Másol'>&gt;&gt;</a></td>
					<td class='cell$sor_tipus'><input type='text' name ='szamla_nev' class='input-check' size='15' value='$szamla_nev'></td>
					<td class='cell$sor_tipus'><input type='text' name ='szamla_irsz' size='4' class='input-check' value='$szamla_irsz'></td>
					<td class='cell$sor_tipus'><input type='text' name ='szamla_varos' class='input-check' size='12' value='$szamla_varos'></td>
					<td class='cell$sor_tipus'><input type='text' name ='szamla_utca' class='input-check' size='30' value='$szamla_utca'></td>
					<td class='cell$sor_tipus'><a href='javascript:void();' onclick='document.check$rend_id.submit()'  style='font-weight:bold;color:#00a;font-size:13px;text-decoration:none;' title='Módosít'>OK</a></td>
					</form>
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


