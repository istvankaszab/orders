
<script type="text/javascript" src="js/rendeles.js"></script>

<?

//print_r($_GET);
//print_r($_POST);

$modositas_ok = 0;

if (isset($_POST['megjegyzes']) and $_POST['megjegyzes'] == 'uj') {
	$result_uzenet_insert = mysql_query("insert into uzenet (uzenet_ido, rend_id, felh_id, uzenet_szoveg) values ('".date("Y-m-d H:i:s")."', '".$_GET['id']."', '".$_POST['user']."', '".$_POST['megjegyzes_szoveg']."')", $conn_main);
	rendOsszesit($_GET['id']);
	newMod($_GET['id'], $_POST['user'], "Új üzenet", $_POST['megjegyzes_szoveg']);
}

if (isset($_POST['muvelet'])) {
	if ($_POST['muvelet'] == 'modosit') {
		$result_termek_update = mysql_query("update termek set termek_egysegar=".$_POST['egysegar'].", termek_db=".$_POST['darab']." where tid=".$_POST['tid'], $conn_main);
	}
	else if ($_POST['muvelet'] == 'torol') {
		$result_termek_mod = mysql_query("select marka_nev, termek_nev, termek_egysegar, termek_db from termek, marka where tid=".$_POST['tid']." and termek.marka_id=marka.marka_id", $conn_main);
		while($termek_mod[] = mysql_fetch_array($result_termek_mod));
		$arrayMod = $termek_mod[0];
		$strMod = $arrayMod['marka_nev'].' '.$arrayMod['termek_nev'].' ('.$arrayMod['termek_egysegar'].' Ft, '.$arrayMod['termek_db'].' db) ';
		newMod($_GET['id'], 5, "Termék törölve", $strMod);

		$result_termek_update = mysql_query("delete from termek where tid=".$_POST['tid'], $conn_main);
	}
	else if ($_POST['muvelet'] == 'uj') {
		$result_termek_insert = mysql_query("insert into termek (rend_id, termekid, termek_nev, marka_id, termek_egysegar, termek_db) values ('".$_GET['id']."', '0', '".$_POST['nev']."', '".$_POST['marka']."', '".$_POST['egysegar']."', '".$_POST['darab']."')", $conn_main);
	}
	
	rendOsszesit($_GET['id']);
	$modositas_ok = 1;
}

if (isset($_POST['save'])) {
	if (isset($_GET['id'])) {
		if ($_POST['problema']=='on') $rend_problema=1; else $rend_problema=0;
		if ($_POST['futar_ok']=='on') $rend_futar_ok=1; else $rend_futar_ok=0;
		if ($_POST['szamla_ok']=='on') $rend_szamla_ok=1; else $rend_szamla_ok=0;
		if ($_POST['ellenorizve']=='on') $ellenorizve=1; else $ellenorizve=0;
		$result_update = mysql_query("update rendeles set
										  nev='".trim($_POST['nev'])."',
										  email='".trim($_POST['email'])."',
										  telefon='".trim(telefonFormat($_POST['telefon']))."',
										  irsz='".trim($_POST['irsz'])."',
										  varos='".trim($_POST['varos'])."',
										  utca='".trim($_POST['utca'])."',
										  szamla_nev='".trim($_POST['szamla_nev'])."',
										  szamla_irsz='".trim($_POST['szamla_irsz'])."',
										  szamla_varos='".trim($_POST['szamla_varos'])."',
										  szamla_utca='".trim($_POST['szamla_utca'])."',
										  szallitas='".$_POST['szallitas']."',
										  fizetes='".$_POST['fizetes']."',
										  allapot='".$_POST['allapot']."',
										  futar_kod='".trim($_POST['futar_kod'])."',
										  problema='".$rend_problema."',
										  futar_ok='".$rend_futar_ok."',
										  szamla_ok='".$rend_szamla_ok."', 
										  ellenorizve='".$ellenorizve."' 
										  where rend_id = '".$_POST['rend_id']."'", $conn_main);
//	echo "<script type='text/javascript'>alert('A ".$_POST['rend_id']." rendelés elmentve.');</script>";

		$result_numuzenet = mysql_query("select count(*) from uzenet where felh_id=13 and rend_id = '".$_POST['rend_id']."'", $conn_main);
		list($numuzenet) = mysql_fetch_row($result_numuzenet);
	
		if($numuzenet > 0) {	
			$result_uzenet = mysql_query("update uzenet set uzenet_szoveg = '".trim($_POST['uzenet'])."' where felh_id=13 and rend_id = '".$_POST['rend_id']."'", $conn_main);
		}
		else {
			$result_uzenet = mysql_query("insert into uzenet (uzenet_ido, rend_id, felh_id, uzenet_szoveg) values ('".date("Y-m-d H:i:s")."', '".$_POST['rend_id']."', 13, '".trim($_POST['uzenet'])."')", $conn_main);
		}
		
		rendOsszesit($_GET['id']);
	}
	else {
		newRend($_POST['webshop'], 0, date("Y-m-d"), date("H:i:s"), '', trim($_POST['nev']), trim($_POST['irsz']), trim($_POST['varos']), trim($_POST['utca']), trim(telefonFormat($_POST['telefon'])), trim($_POST['email']), trim($_POST['szamla_nev']), trim($_POST['szamla_irsz']), trim($_POST['szamla_varos']), trim($_POST['szamla_utca']), $_POST['szallitas'], $_POST['fizetes'], 0, 0, '', $_POST['allapot'], session_id());
		$result_newrend = mysql_query("select rend_id from rendeles where session_id='".session_id()."'", $conn_main);
		list($new_rend_id) = mysql_fetch_row($result_newrend);
		
		$result_del_sessionid = mysql_query("update rendeles set session_id='', user_id='rendeles_$new_rend_id' where rend_id='$new_rend_id'", $conn_main);
		$result_uzenet_insert = mysql_query("insert into uzenet (rend_id, felh_id, uzenet_szoveg) values ($new_rend_id, '1', '".trim($_POST['uzenet'])."')", $conn_main);
		if(isset($_POST['hirlevel']) and $_POST['hirlevel']=='on') {
			require "info/hirlevel-feliratkozas.php";
			hirlevelFeliratkozas(trim($_POST['nev']), trim($_POST['email']));
		}
		

//		echo "<script type='text/javascript'>alert('A ".$new_rend_id." rendelés elmentve.');</script>";
//		header('Location: '.$_SERVER['REQUEST_URI'].'&id='.$new_rend_id);
		echo "<script type='text/javascript'>
		//<!--
		window.location ='".$_SERVER['REQUEST_URI']."&id=".$new_rend_id."';
		//-->
		</script>";

	}

	$modositas_ok = 1;
}


if (isset($_GET['id'])) {

	$rend_id = $_GET['id'];
	$result_rend = mysql_query("select * from rendeles where rend_id = '$rend_id'", $conn_main);
	$rend = mysql_fetch_array($result_rend);
	$result_webshop = mysql_query("select webshop_nev from webshop where webshop_id ='".$rend['webshop_id']."'", $conn_main);
	list($webshop_nev) = mysql_fetch_row($result_webshop);
	
	$result_uzenet = mysql_query("select uzenet_szoveg from uzenet where rend_id ='".$rend['rend_id']."' and felh_id = 13", $conn_main);
	list($uzenet) = mysql_fetch_row($result_uzenet);

	$result_termek = mysql_query("select marka_nev, termek_nev, termek_egysegar, termek_db, tid from termek, marka where rend_id='".$rend['rend_id']."' and termek.marka_id=marka.marka_id", $conn_main);
	while ($termek[] = mysql_fetch_array($result_termek));

	$result_megjegyzes = mysql_query("select * from uzenet, felhasznalok where rend_id='".$rend['rend_id']."' and felhasznalok.felh_id = uzenet.felh_id", $conn_main);
	while ($megjegyzes[] = mysql_fetch_array($result_megjegyzes));

	$result_mods = mysql_query("select * from modositas where rend_id ='".$_GET['id']."'", $conn_main);
	while ($modositas[] = mysql_fetch_array($result_mods));
}

	$result_szallitas = mysql_query("select szallitas_id, szallitas_nev from szallitas", $conn_main);
	while ($szallitas[] = mysql_fetch_array($result_szallitas));

	$result_webshop_lista = mysql_query("select webshop_id, webshop_nev from webshop", $conn_main);
	while ($webshop_lista[] = mysql_fetch_array($result_webshop_lista));

	$result_fizetes = mysql_query("select fizetes_id, fizetes_nev from fizetes", $conn_main);
	while ($fizetes[] = mysql_fetch_array($result_fizetes));

	$result_allapot = mysql_query("select allapot_id, allapot_nev from allapot", $conn_main);
	while ($allapot[] = mysql_fetch_array($result_allapot));

	$result_markalista = mysql_query("select marka_id, marka_nev from marka order by marka_nev asc", $conn_main);
	while ($markalista[] = mysql_fetch_array($result_markalista));

	$result_userlista = mysql_query("select felh_id, felh_nev from felhasznalok where felh_aktiv=1 order by felh_nev asc", $conn_main);
	while ($userlista[] = mysql_fetch_array($result_userlista));

	$str_h1 = 'Rendelés ';
	if (isset($_GET['id'])) $str_h1 = $str_h1.$rend['rend_id']." ".$rend['nev']; else $str_h1 = $str_h1."felvétele";
	
	echo "<h1>$str_h1</h1>";
	echo "<table cellpadding='0' cellspacing='0'><tr><td>
	<form id='rendeles' action='".$_SERVER['REQUEST_URI']."' method='post'><input type='hidden' name='save'>
	<table cellpadding='0' cellspacing='0'>
	<tr>
		<td class='normal-head' colspan='2'>Alapadatok</td>
	</tr>
	<tr class='rend-row'>
		<td><b>Rendelés ID</b></td>";
//	echo"<td>".$rend['rend_id']."<a href='javascript:void()' onclick='csspopup(".'"modPopup"'.");' style='margin-left:15px;'>történet</a></td><input type='hidden' name='rend_id' value='".$rend['rend_id']."'>
	echo "<td>".$rend['rend_id']."</td><input type='hidden' name='rend_id' value='".$rend['rend_id']."'>
	</tr>
	<tr class='rend-row'>
		<td><b>Webáruház</b></td>
		<td>";
		if (isset($_GET['id'])) {
			echo $webshop_nev;
		}
		else {
			echo "<select name='webshop'>";
			foreach ($webshop_lista as $ws) {
				if($ws[0]) {
					echo "<option "; if($ws[0] == 'karorauzlet.hu') echo "selected='selected' "; echo "value='".$ws[0]."'>".$ws[1]."</option>";
				}
			}
			echo "</select>";
		}
	echo "</td>
	</tr>
	<tr class='rend-row'>
		<td><b>Eredeti rendelés ID</b></td>
		<td>".$rend['orig_id']."</td>
	</tr>
	<tr class='rend-row'>
		<td><b>Rendelés ideje</b></td>
		<td>".$rend['rend_datum']." ".$rend['rend_ido']."</td>
	</tr>
	<tr class='rend-row'>
		<td><b>Módosítás ideje</b></td>
		<td>".$rend['mod_ido']."</td>
	</tr>
	<tr class='rend-row'>
		<td><b>Csomag</b></td>
		<td><input type='checkbox' name='futar_ok'";
		if($rend['futar_ok']==1) echo " checked='checked'";
		echo "> Futár import kész<br/><input type='checkbox' name='szamla_ok'";
		if($rend['szamla_ok']==1) echo " checked='checked'";
		echo "> Számla kész</td>
	</tr>";
/*	
	echo "<tr class='rend-row'>
		<td><b>DPD kód</b></td>
		<td><input type='text' name='futar_kod' value='".$rend['futar_kod']."'>";
	if($rend['futar_kod'] != '') echo " <a href='https://tracking.dpd.de/cgi-bin/delistrack?pknr=".$rend['futar_kod']."&typ=1&lang=hu"."' target='_blank'>DPD</a>";
	echo "</td>
	</tr>";
*/
	echo "<tr class='rend-row'>
		<td><b>Rendelés állapota</b></td>
		<td><select name='allapot'>";
	foreach ($allapot as $all) {
		if($all[0]) {
			echo "<option "; if($all[0] == $rend['allapot']) echo "selected='selected' "; echo "value='".$all[0]."'>".$all[1]."</option>";
		}
	}
	echo "</select>
		</td>
	</tr>
	<tr class='rend-row'>";
	if($rend['problema']==1) {
		echo "<td style='background:#f55;'><b>Problémás</b></td>
		<td style='background:#f55;'><input type='checkbox' name='problema' checked='checked' onclick='checkProblema()'></td>";
	}
	else {
		echo "<td><b>Problémás</b></td>
		<td><input type='checkbox' name='problema' onclick='checkProblema()'></td>";
	}
	echo "</tr>
	<tr class='rend-row'>
		<td><b>Vevő</b></td>
		<td><input type='text' size='25' name='nev' value='".$rend['nev']."'></td>
	</tr>
	<tr class='rend-row'>
		<td><b>Email</b></td>
		<td><input type='text' size='25' name='email' value='".$rend['email']."'></td>
	</tr>";
	if(!isset($_GET['id'])) {
		echo "<tr class='rend-row'>
		<td><b>Hírlevélre<br/>feliratkozik</b></td>
		<td><input type='checkbox' name='hirlevel'></td>
		</tr>";
	}
	echo "<tr class='rend-row'>
		<td><b>Telefon</b></td>
		<td><input type='text' size='25' name='telefon' value='".$rend['telefon']."'></td>
	</tr>
	<tr class='rend-row'>
		<td><b>Szállítás</b></td>
		<td>&nbsp;</td>
	</tr>
	<tr class='rend-row'>
		<td style='padding-left:30px;'>irányítószám</td>
		<td><input type='text' size='7' name='irsz' value='".$rend['irsz']."' onchange='setVaros(".'"irsz", "varos"'.")'></td>
	</tr>
	<tr class='rend-row'>
		<td style='padding-left:30px;'>település</td>
		<td><input type='text' size='25' name='varos' value='".$rend['varos']."'></td>
	</tr>
	<tr class='rend-row'>
		<td style='padding-left:30px;'>cím</td>
		<td><input type='text' size='25' name='utca' value='".$rend['utca']."'></td>
	</tr>
	<tr class='rend-row'>
		<td><b>Számla</b></td>
		<td>&nbsp;</td>
	</tr>
	<tr class='rend-row'>
		<td style='padding-left:30px;'>név</td>
		<td><input type='text' size='25' name='szamla_nev' value='".$rend['szamla_nev']."'>";
		if ($rend['szamla_nev']=='') echo "&nbsp;<a href='javascript:copyNev()' style='text-decoration:none;float:right;'>copy</a>";
		echo "</td>
	</tr>
	<tr class='rend-row'>
		<td style='padding-left:30px;'>irányítószám</td>
		<td><input type='text' size='7' name='szamla_irsz' value='".$rend['szamla_irsz']."' onchange='setVaros(".'"szamla_irsz", "szamla_varos"'.")'>";
		if ($rend['szamla_irsz']=='') echo "&nbsp;<a href='javascript:copyIrsz()' style='text-decoration:none;float:right;'>copy</a>";
		echo "</td>
	</tr>
	<tr class='rend-row'>
		<td style='padding-left:30px;'>település</td>
		<td><input type='text' size='25' name='szamla_varos' value='".$rend['szamla_varos']."'>";
		if ($rend['szamla_varos']=='') echo "&nbsp;<a href='javascript:copyVaros()' style='text-decoration:none;float:right;'>copy</a>";
		echo "</td>
	</tr>
	<tr class='rend-row'>
		<td style='padding-left:30px;'>cím</td>
		<td><input type='text' size='25' name='szamla_utca' value='".$rend['szamla_utca']."'>";
		if ($rend['szamla_utca']=='') echo "&nbsp;<a href='javascript:copyUtca()' style='text-decoration:none;float:right;'>copy</a>";
		echo "</td>
	</tr>
	<tr class='rend-row'>
		<td><b>Szállítás módja</b></td>
		<td><select name='szallitas'>";
	foreach ($szallitas as $sz) {
		if($sz[0]) {
			echo "<option "; if($sz[0] == $rend['szallitas']) echo "selected='selected' "; echo "value='".$sz[0]."'>".$sz[1]."</option>";
		}
	}
	echo "</select>
		</td>
	</tr>
	<tr class='rend-row'>
		<td><b>Fizetés módja</b></td>
		<td><select name='fizetes'>";
	foreach ($fizetes as $fiz) {
		if($fiz[0]) {
			if (!$rend['fizetes']) $rend['fizetes'] = 3;
			echo "<option "; if($fiz[0] == $rend['fizetes']) echo "selected='selected' "; echo "value='".$fiz[0]."'>".$fiz[1]."</option>";
		}
	}
	echo "</select>
		</td>
	</tr>
	<tr class='rend-row'>
		<td><b>Üzenet a futárnak</b></td>
		<td><textarea rows='4' cols='25' name='uzenet'>".$uzenet."</textarea></td>
	</tr>";
	echo "<tr class='rend-row'>
		<td><b>Ellenőrizve</b></td>
		<td><input type='checkbox' name='ellenorizve'";
	if($rend['ellenorizve']==1) echo " checked='checked'";
	echo "></td>
	</tr>";
	echo "<tr>
	<td>&nbsp;</td>
	<td style='text-align:right;padding:15px 20px;'><input type='submit' value='Mentés'></td>
	</tr>
	</table>
	</form>	
	</td><td style='width:20px;'>&nbsp;</td>";

if (isset($_GET['id'])) {
	
	echo "<td class='upper'><table cellpadding='0' cellspacing='0'>
	<tr>
		<td colspan='6' class='normal-head' style='background:#d60;'>Termékek</td>
	</tr>
	<tr><td class='termek-head'>Márka</td><td class='termek-head'>Név</td><td class='termek-head'>Egységár</td><td class='termek-head'>db</td><td class='termek-head'>Érték</td><td class='termek-head'>Módosít</td></tr>";
	
	foreach ($termek as $ter) {
		if($ter[0]) {
			echo "<tr class='row-termek'><form id='termek_".$ter[4]."' action='".$_SERVER['REQUEST_URI']."' method='post'><input type='hidden' name='muvelet' value=''>
			<td class='cell-termek1'>".$ter[0]."</td>
			<td class='cell-termek1' style='max-width:150px;'>".$ter[1]."<input type='hidden' name='tnev' value='".$ter[1]."' ><input type='hidden' name='tid' value='".$ter[4]."' ></td>
			<td class='cell-termek2'><input type='text' size='5' name='egysegar' value='".$ter[2]."' style='text-align:right;'></td>
			<td class='cell-termek2'><input type='text' size='1' name='darab' value='".$ter[3]."' style='text-align:right;'></td>
			<td class='cell-termek2'>".number_format(($ter[2]*$ter[3]), 0, '.', ' ')."&nbsp;Ft</td>
			<td class='cell-termek1'>
			<a href='javascript:void()' onclick='modTer(".$ter[4].")' style='font-weight:bold;color:#00a;font-size:13px;text-decoration:none;' title='Módosít'>OK</a>
			&nbsp;&nbsp;&nbsp;
			<a href='javascript:void()' onclick='delTer(".$ter[4].")' style='font-weight:bold;color:#f00;font-size:13px;text-decoration:none;' title='Töröl'>X</a>
			</td>
			</form></tr>";
		}
	}


	echo "<tr>
			<form id='termek_new' action='".$_SERVER['REQUEST_URI']."' method='post'>
				<input type='hidden' name='muvelet' value='uj'>
			<td class='cell-termek1'>
				<select name='marka' style='width:80px'>
				<option selected='selected' value=''></option>";
	foreach ($markalista as $mlist) {
		if($mlist[0]) {
			echo "<option value='".$mlist[0]."'>".$mlist[1]."</option>";
		}
	}
	echo "</select>
			</td>
			<td class='cell-termek1'><input type='text' name='nev' size='10' value=''></td>
			<td class='cell-termek1'><input type='text' name='egysegar' size='5' value='' onchange='szamolErtek()'></td>
			<td class='cell-termek1'><input type='text' name='darab' size='1' value='' onchange='szamolErtek()'></td>
			<td class='cell-termek2'><span id='newter_ertek' style='font-size:13px;'></span></td>
			<td class='cell-termek1'><a href='javascript:void()' onclick='modTer(".'"new"'.")' style='font-weight:bold;color:#0a0;font-size:20px;text-decoration:none;' title='Hozzáad'>+</a></td>
		</form>
		</tr>";


	echo "<tr><td colspan='6'>&nbsp;<br style='line-height:10px;'></td></tr>
		<tr>
		<td colspan='5' style='text-align:right;padding:3px 10px;'>Termék összesen</td>
		<td class='cell-termek2'>".number_format($rend['termek_osszeg'], 0, '.', ' ')."&nbsp;Ft</td>
		</tr>
		<tr>
		<td colspan='5' style='text-align:right;padding:3px 10px;'>Szállítási díj</td>
		<td class='cell-termek2'>".number_format($rend['szall_dij'], 0, '.', ' ')."&nbsp;Ft</td>
		</tr>
		<tr>
		<td colspan='2'></td>
		<td><span style='font-size:80%;'>".($rend['termek_osszeg']+$rend['szall_dij'])."</span></td>
		<td colspan='2' style='text-align:right;padding:3px 10px;'><b>Összesen</b></td>
		<td class='cell-termek2'><b>".number_format(($rend['termek_osszeg']+$rend['szall_dij']), 0, '.', ' ')." Ft</b></td>
		</tr>
	";
	echo "<tr><td colspan='6' style='height:50px;'><br/></td></tr><tr>
	<td class='upper' colspan='6'><table cellpadding='0' cellspacing='0' width='100%'>
	<tr>
		<td colspan='3' class='normal-head' style='background:#374;'>Megjegyzések</td>
	</tr>
	<tr><td class='termek-head'>Írta</td><td class='termek-head'>Időpont</td><td class='termek-head'>Szöveg</td></tr>";

	foreach ($megjegyzes as $megj) {
		if($megj[0] and $megj[4]!='' and $megj[3]!=13) {
			echo "<tr class='row-termek'><form id='termek_".$megj[0]."' action='".$_SERVER['REQUEST_URI']."' method='post'><input type='hidden' name='muvelet' value=''>
			<td class='cell-termek1'>".$megj[6]."</td>
			<td class='cell-termek1' style='max-width:150px;'>".$megj[1]."</td>
			<td class='cell-termek1' style='max-width:500px;'>".$megj[4]."</td>
			</form></tr>";
		}
	}
	echo "<tr>
			<form id='megjegyzes_new' action='".$_SERVER['REQUEST_URI']."' method='post'>
				<input type='hidden' name='megjegyzes' value='uj'>
			<td class='cell-termek1' colspan='2'>
				<select name='user' style='width:80px'>
				<option selected='selected' value=''></option>";
	foreach ($userlista as $ulist) {
		if($ulist[0] and $ulist[0]>1 and $ulist[0]!=13) {
			echo "<option value='".$ulist[0]."'>".$ulist[1]."</option>";
		}
	}
	echo "</select>
			</td>
			<td class='cell-termek1'>
			<textarea rows='4' cols='30' id='megjegyzes_szoveg' name='megjegyzes_szoveg'></textarea>
			<a href='javascript:void()' onclick='newMegj()' style='font-weight:bold;color:#0a0;font-size:20px;text-decoration:none;' title='Hozzáad'>+</a></td>
		</form>
		</tr>";
	
	echo "</table></td>";

}

	echo "</tr></table>";

?>
<!-- <a href="javascript:void()" onclick="csspopup('rendPopup')">confirm</a> -->
<style type="text/css">
#blanket {
   background-color:#000;
   opacity: 0.75;
   position:absolute;
   z-index: 9001; 
   top:0px;
   left:0px;
   width:100%;
}
#rendPopup {
	position:absolute;
	background:#fff;
	border: solid #000 1px;
	font-family:arial, helvetica, sans-serif;
	width:350px;
	z-index: 9002;
	line-height:normal;
	padding:20px;
}
</style>

<div id="blanket" style="display:none;"></div>
<div id="rendPopup" style="display:none;" >
	<form id="popupForm">
Ki vagy?	<select name="user">
			<option selected='selected' value=''></option>
<?
	foreach ($userlista as $ulist) {
		if($ulist[0] and $ulist[0]>1) {
			echo "<option value='".$ulist[0]."'>".$ulist[1]."</option>";
		}
	}
?>
		</select>
		<a href="javascript:void()" onclick="csspopup('rendPopup');">becsuk</a>
	</form>

</div>

<style type="text/css">
#modPopup {
	position:absolute;
	background:#fff;
	border: solid #000 1px;
	font-family:arial, helvetica, sans-serif;
	z-index: 9002;
	line-height:normal;
	padding:20px;
}
</style>

<div id="modPopup" style="display:none;" >
	<h1>Rendelés története</h1>
	<table cellspacing='0' cellpadding='0'>
		<tr>
			<td class='normal-head'>Felhasználó</td>
			<td class='normal-head'>Időpont</td>
			<td class='normal-head' colspan='2'>Módosítás</td>
		</tr>
<?
	
/*
	foreach ($modositas as $mod) {
		if($mod[0]) {
			echo "	<tr class='rend-row'>
			<td>";
			foreach ($userlista as $ulist) {
				if($ulist[0] == $mod['felh_id']) {
					echo $ulist[1];
				}
			}
			echo "</td>
			<td>".$mod['mod_ido']."</td>
			<td>".$mod['mod_tipus'].":&nbsp;</td>
			<td><pre style='font-family:arial, helvetica, sans-serif;'>".$mod['mod_szoveg']."</pre></td>
			</tr>";
		}
	}
*/
if($modositas_ok ==1) echo "<script type='text/javascript'>alert('Sikeres módosítás')</script>";

?>		
	</table>
	<a href="javascript:void()" onclick="csspopup('modPopup');" style='margin-top:15px;float:right;'>becsuk</a>
</div>
