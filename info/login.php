<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>


<body>

<?

$username = 'username';
$password = 'password';

$ch = curl_init("http://www.karoracentrum.hu/api/clearadmin.php?max_orders=3");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
$html = curl_exec($ch);
curl_close($ch);

$xml=simplexml_load_string($html);


print_r($xml);
echo "<br/><br/>***************<br/><br/>";

foreach ($xml -> ORDER as $rend) {
	echo "rendelés sorszáma: " . $rend -> ORDERHEAD_CODE . "<br/>";
	echo "rendelés időpontja: " . $rend -> ORDERHEAD_TIMESTAMP . "<br/>";
	echo "...";
	foreach ($rend -> ORDERITEM as $termek) {
		$egysegar = $termek -> ORDERITEM_PRICE;
		echo $egysegar."...<br>";
		$egysegar = round($egysegar * (1 + $afa / 100));
		echo $egysegar."...<br>";
	}

}

//	echo "rendelés sorszáma: " . $xml -> ORDER [1] -> ORDERHEAD_CODE . "<br/>";
//	echo "rendelés időpontja: " . $xml -> ORDER [1] -> ORDERHEAD_TIMESTAMP . "<br/>";


if(stripos(" "."Casio fx-82","casio") != false) echo "casio";


?>

</body>
</html>

