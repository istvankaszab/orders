<h1>COPY</h1>
<?

include('./info/copier-karorauzlet.php');
include('./info/copier-szotargepcentrum.php');
include('./info/copier-karoracentrum.php');
include('./info/copier-legjobbajanlat.php');
include('./info/copier-karoracentrum-new.php');

//webshopok listája
$query_webshop = "select * from webshop";
$result_webshop = mysql_query($query_webshop);

$result_temp_delete = mysql_query("delete from temp_rendeles", $conn_main);

while (list($webshop_id, $webshop_nev, $webshop_host, $webshop_db, $webshop_user, $webshop_pwd, $webshop_tip)=mysql_fetch_row($result_webshop)) {
/*	if ($webshop_id==1) {
		rendeles_check_karorauzlet($webshop_id, $webshop_host, $webshop_db, $webshop_user, $webshop_pwd);
	}
	elseif ($webshop_id==2) {
		rendeles_check_karoracentrum($webshop_id, $webshop_host, $webshop_db, $webshop_user, $webshop_pwd);
	}
	elseif ($webshop_id==7) {
		rendeles_check_szotargepcentrum($webshop_id, $webshop_host, $webshop_db, $webshop_user, $webshop_pwd);
	}
	elseif ($webshop_id==8) {
		rendeles_check_legjobbajanlat($webshop_id, $webshop_host, $webshop_db, $webshop_user, $webshop_pwd);
	}
*/
	if ($webshop_id==11) {
		rendeles_check_karoracentrum_new($webshop_id, $webshop_host, $webshop_db, $webshop_user, $webshop_pwd);
	}

}

$result_temp_rendeles = mysql_query("select temp_webshop_id, temp_orig_id from temp_rendeles order by temp_datum asc, temp_ido asc", $conn_main);

while (list($temp_webshop_id, $temp_orig_id)=mysql_fetch_row($result_temp_rendeles)) {
/*	if ($temp_webshop_id == 1) {
			rendeles_download_karorauzlet($temp_orig_id);
	}
	if ($temp_webshop_id == 2) {
			rendeles_download_karoracentrum($temp_orig_id);
	}
	if ($temp_webshop_id == 7) {
			rendeles_download_szotargepcentrum($temp_orig_id);
	}
	if ($temp_webshop_id == 8) {
			rendeles_download_legjobbajanlat($temp_orig_id);
	}
*/
	if ($temp_webshop_id == 11) {
			rendeles_download_karoracentrum_new($temp_orig_id);
	}

}

?>
