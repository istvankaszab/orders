<?

$a = "Budapest, Akácfa utca 123. 1030";


preg_match('/(\d{4,4})/', $a, $b);

$a = str_ireplace($b[0], '', $a);


echo "irányítószám: ".($b[0])."<br>cím: ".$a;





?>
