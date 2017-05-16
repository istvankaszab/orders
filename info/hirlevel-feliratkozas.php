<?

function hirlevelFeliratkozas($nev, $email) {
/////     webgalamb feliratkozás
// kötelezõ adatok
	$data['f_42'] = $nev;
	$data['subscr'] = $email;

	$url = 'http://karoracentrum.chr.hu//subscriber.php?g=14';
	$data['g'] = '14';

	$data['sub'] = 'Feliratkozás'; // nem szabad módosítani
	$data = str_replace('&amp;','&', X_http_build_query($data));
	do_post_request($url, $data);	
}

/* alapfüggvények */
function X_http_build_query($data,$prefix=null,$sep='',$key='') {
	$ret = array();
	foreach((array)$data as $k => $v) {
		$k = urlencode($k);
		if(is_int($k) && $prefix != null) {
			$k = $prefix.$k;
		}
		if(!empty($key)) {
			$k = $key."[".$k."]";
		}

		if(is_array($v) || is_object($v)) {
			array_push($ret,http_build_query($v,"",$sep,$k));
		} else {
			array_push($ret,$k."=".urlencode($v));
		}
	}

	if(empty($sep)) {
		$sep = ini_get("arg_separator.output");
	}

	return implode($sep, $ret);
}

function do_post_request($url, $data, $optional_headers = null) {
	$start = strpos($url,'//')+2;
	$end = strpos($url,'/',$start);
	$host = substr($url, $start, $end-$start);
	$domain = substr($url,$end);
	$fp = fsockopen($host, 80);
	if(!$fp) return null;
	fputs ($fp,"POST $domain HTTP/1.1\n");
	fputs ($fp,"Host: $host\n");
	if ($optional_headers) {
		fputs($fp, $optional_headers);
	}

	fputs ($fp,"Content-type: application/x-www-form-urlencoded\n");
	fputs ($fp,"Content-length: ".strlen($data)."\n\n");
	fputs ($fp,"$data\n\n");

	$response = "";
	/*while(!feof($fp)) {
		$response .= fgets($fp, 1024);
	}*/
	fclose ($fp);
	return $response;
}   

?>