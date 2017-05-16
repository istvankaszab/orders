<?

require "config.php";
require "functions.php";

include('copier-karoracentrum-new-151129.php');

//webshopok listája
$query_webshop = "select * from webshop";
$result_webshop = mysql_query($query_webshop);

$result_temp_delete = mysql_query("delete from temp_rendeles", $conn_main);

while (list($webshop_id, $webshop_nev, $webshop_host, $webshop_db, $webshop_user, $webshop_pwd, $webshop_tip)=mysql_fetch_row($result_webshop)) {
	if ($webshop_id==11) {
		rendeles_check_karoracentrum_new($webshop_id, $webshop_host, $webshop_db, $webshop_user, $webshop_pwd);
	}
}

$result_temp_rendeles = mysql_query("select temp_webshop_id, temp_orig_id from temp_rendeles order by temp_datum asc, temp_ido asc", $conn_main);

while (list($temp_webshop_id, $temp_orig_id)=mysql_fetch_row($result_temp_rendeles)) {
	if ($temp_webshop_id == 11) {
//			rendeles_download_karoracentrum_new($temp_orig_id);
	}
}

?>
