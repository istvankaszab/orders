<?

  /*
    Ügylet import teszt PHP fájl, v1.0 (2012-06-25)
    Copyright (c) 2012, AXEL PROFESSIONAL SOFTWARES Kft., Minden jog fenntartva.

    www.axel-szamlazo-program.hu

    ***********************************************

    Engedélyezett a forráskód és bináris formában történő felhasználás és terjesztés, módosítással vagy anélkül,
    amennyiben a következő feltételek teljesülnek:

    - A forráskód terjesztésekor meg kell őrizni a fenti szerzői jogi megjegyzést, ezt a feltétellistát és a következő nyilatkozatot.
    - Bináris formában történő terjesztéskor tovább kell adni a fenti szerzői jogi megjegyzést, ezt a feltétellistát és a következő
      nyilatkozatot a dokumentációban, illetve a csomaggal részét képező egyéb anyagokban.
    - Ezt a szoftvert a szerzői jog tulajdonosai és a hozzájárulók úgy biztosítják, "ahogy van", és semmilyen nyílt vagy burkolt
      garancia - beleértve, de nem erre korlátozva az eladhatóságot vagy egy adott célra való alkalmatosságot – nem érvényesíthető.
      A szerzői jog tulajdonosai és a hozzájárulók semmilyen esetben sem vonhatók felelősségre a szoftver használatából eredő semmilyen
      közvetlen, közvetett, véletlenszerű, különleges, példaadó vagy szükségszerű károkért (beleértve, de nem erre korlátozva a
      helyettesítő termékek vagy szolgáltatások beszerzését, üzemkiesést, adatvesztést, elmaradt hasznot vagy üzletmenet megszakadását),
      bárhogy is következett be, valamint a felelősség bármilyen elméletével – akár szerződésben, akár okozott kárban (beleértve a
      hanyagságot és egyebet), akkor is, ha az ilyen kár lehetőségére felhívták a figyelmet.

    ***********************************************

    Részleteket, további információk az AXEL PRO Használati Útmutató "Webáruház és integráció" témaköre alatt találhatóak, példa XML-el, stb.

    ***********************************************

    Tesztelés módja:
    -----------------------------

    1.) Másolja ki ezen oldal szövegét és töltse fel saját tárhelyére egy tetszőleges fájlnévvel, php kiterjesztéssel. (pl.: ugylet-import.php)
    2.) Az ugyelet-import-mintasql.sql fájl segítségével importálja be a MySQL adattáblákat.
    3.) Írja át a MySQL beállításokat ($db_hostname, $db_username, $db_password, $db_database változók).
    4.) Az AXEL PRO szoftverben a Beállítások főmenüben kattintson a Webáruház gombon, majd a "Webáruház beállítások" menüponton és állítsa be az ugylet-import.php webcímét.
    5.) Indítsa el az adatcserét a Webáruház gombon, majd a "Webáruház kapcsolódás és adatcsere" menüponton kattintva.
    Működő xml demó a következő címről érhető el: http://www.axel-szamlazo-program.hu/ugylet-import
  */

require "config.php";
require "functions.php";
  
  
  //PHP teljes hibajelentés bekapcsolása, hogy minden hiba kiderüljön
  error_reporting(E_ALL);

  //UTF-8 XML fejléc küldése a böngészőnek
  header("Content-type: text/xml; charset=utf-8");
  //XML fejléc kiírása
  print("<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\" ?>");
  print("<AXELPRO_IMP_TRANS VERSION=\"1.1\">");

/*
  //MySQL kapcsolódási adatok
  $db_hostname = 'localhost';
  $db_username = 'username';
  $db_password = 'password';
  $db_database = 'database';

  //MySQL adatbázis kapcsolódás
  mysql_connect($db_hostname, $db_username, $db_password);
  //hiba esetén kiírjuk a hibát
  if (mysql_errno() > 0) die("Adatbazis kapcsolodasi hiba: ".mysql_error());

  //MySQL adatbázis kiválasztása
  mysql_select_db($db_database);
  //hiba esetén kiírjuk a hibát
  if (mysql_errno() > 0) die("Adatbazis tabla kivalasztasi hiba: ".mysql_error());


  //MySQL UTF-8 beállítása
  mysql_query("SET NAMES 'UTF8'");
  mysql_query("SET CHARACTER SET 'UTF8'");
  mysql_query("SET COLLATION_CONNECTION = 'utf8_general_ci'");
*/

  //MySQL lekérdezés, ügyletek keresése amik még nem voltak szinkronizálva
  $query_szamla = mysql_query("SELECT * FROM rendeles WHERE szamla_ok = 0 and szamlazhato = 1", $conn_main);
  //hiba esetén kiírjuk a hibát
  if (mysql_errno() > 0) die("Adatbazis lekerdezesi hiba: ".mysql_error());

//ügylet adatainak feldolgozása
while ($ugylet = mysql_fetch_assoc($query_szamla)) {
	//MySQL lekérdezés, ügylet tételek keresése amik még nem voltak szinkronizálva
	$query_termek = mysql_query("SELECT * FROM termek WHERE rend_id = ".mysql_real_escape_string($ugylet['rend_id']), $conn_main);
	if (mysql_errno() > 0) die("Adatbazis lekerdezesi hiba: ".mysql_error());

/*	
	$ugylet = str_replace("&", "", $ugylet);
	$ugylet = str_replace("<", "(", $ugylet);
	$ugylet = str_replace(">", ")", $ugylet);
	$ugylet = str_replace('"', "", $ugylet);
	$ugylet = str_replace("'", "", $ugylet);

print $ugylet;
*/	

	//hány darab szinkronizálandó ügylet tétel van?
	$count = mysql_num_rows($query_termek);
	//legalább egy szinkronizálandó ügylet tétel van
	if ($count > 0) {
		//XML ügylet elejének kiírása
		print("<TRANS>");
		//XML ügylet adatai elejének kiírása
		print("<TRANS_HEAD>");

		$most = date("Y.m.d. H:i:s");
		$datum_most = date("Y.m.d.");
		$datum_nyolc = date('Y.m.d', strtotime('+8 day'));
		$fizetes = $ugylet['fizetes'];
		$result_fizetes = mysql_query("select fizetes_nev from fizetes where fizetes_id = $fizetes", $conn_main);
		list($fiz) = mysql_fetch_row($result_fizetes);

		
		//IMG_TYPE = 3: bejövő megrendelés
		print("<IMG_TYPE>3</IMG_TYPE>");
		print("<IMG_DATETIME>{$most}</IMG_DATETIME>");
		print("<IMG_FULFILMENT_DATE>{$datum_most}</IMG_FULFILMENT_DATE>");
		print("<IMG_DEADLINE_DATE>{$datum_nyolc}</IMG_DEADLINE_DATE>");
		print("<IMG_CUSTOMER_NAME>{$ugylet['szamla_nev']}</IMG_CUSTOMER_NAME>");
		print("<IMG_CUSTOMER_ADDRESS>{$ugylet['szamla_irsz']} {$ugylet['szamla_varos']}, {$ugylet['szamla_utca']}</IMG_CUSTOMER_ADDRESS>");
		print("<IMG_CUSTOMER_OTHER></IMG_CUSTOMER_OTHER>");
		print("<IMG_POST_NAME>{$ugylet['nev']}</IMG_POST_NAME>");
		print("<IMG_POST_ADDRESS>{$ugylet['irsz']} {$ugylet['varos']}, {$ugylet['utca']}</IMG_POST_ADDRESS>");
		print("<IMG_PAY_NAME>{$fiz}</IMG_PAY_NAME>");
		print("<IMG_CURR>HUF</IMG_CURR>");
		print("<IMG_RATE>1</IMG_RATE>");
		print("<IMG_PRICE_TYPE>1</IMG_PRICE_TYPE>");
		print("<IMG_DISCOUNT>0</IMG_DISCOUNT>");
		print("<IMG_COPIES>2</IMG_COPIES>");
		print("<IMG_COMMENT>A számla a garancia érvényesítéséhez szükséges, kérjük őrizze meg! Rendelés azonosító: {$ugylet['orig_id']}/{$ugylet['rend_id']}</IMG_COMMENT>");
		print("<IMG_IS_MOVE>1</IMG_IS_MOVE>");
		print("<IMG_IS_PAID>1</IMG_IS_PAID>");
		print("<IMG_LANG>0</IMG_LANG>");
		print("<IMG_OTHER></IMG_OTHER>");
		print("<IMG_STORNO>0</IMG_STORNO>");
		print("<IMG_IS_ADVANCE>0</IMG_IS_ADVANCE>");
		print("<IMG_IS_CORRECTION>0</IMG_IS_CORRECTION>");
		print("<IMG_ENVELOPE>0</IMG_ENVELOPE>");
		print("<IMG_COMPANY_PLUS>1</IMG_COMPANY_PLUS>");

		//XML ügylet adatai végének kiírása
		print("</TRANS_HEAD>");

		//XML ügylet tételek elejének kiírása
		print("<TRANS_ITEMS>");
		
		$tetel_sorsz = 0;
		
		//ügylet tételeinek feldolgozása
		while ($tetel = mysql_fetch_assoc($query_termek)) {
			$tetel_sorsz += 1;
			
			$result_marka = mysql_query("select marka_nev from marka where marka_id = {$tetel['marka_id']}", $conn_main);
			list($marka) = mysql_fetch_row($result_marka);

			
			$egysegar = $tetel['termek_egysegar']/1.27;
			//XML ügylet tétel elejének kiírása
			print("<TRANS_ITEM>");

			print("<ITM_NAME>{$marka} {$tetel['termek_nev']}</ITM_NAME>");
			print("<ITM_PRICE_PRICE>{$egysegar}</ITM_PRICE_PRICE>");
			print("<ITM_PRICE_DISCOUNT>0</ITM_PRICE_DISCOUNT>");
			print("<ITM_PRICE_ORIG>{$egysegar}</ITM_PRICE_ORIG>");
			print("<ITM_PRICE_VAT_SHORT>27%</ITM_PRICE_VAT_SHORT>");
			print("<ITM_DATETIME>{$datum_most}</ITM_DATETIME>");
			print("<ITM_AMOUNT>{$tetel['termek_db']}</ITM_AMOUNT>");
			print("<ITM_UNIT>db</ITM_UNIT>");
			print("<ITM_VTSZSZJ></ITM_VTSZSZJ>");
			print("<ITM_ORD>{$tetel_sorsz}</ITM_ORD>");
			print("<ITM_COMMENT></ITM_COMMENT>");

			//XML ügylet tétel végének kiírása
			print("</TRANS_ITEM>");

/*
			//MySQL ügylet tétel szinkronizálva, adott ügylet tétel frissítése a táblában
			$sql_ugylet_tetel_szink = "UPDATE mintatetelek SET szinkronizalva = 1 WHERE id = "
			mysql_real_escape_string($tetel['rend_id']);
			//MySQL ügylet tétel frissítés futtatása
			mysql_query($sql_ugylet_tetel_szink);
*/
		}
		
		$tetel_sorsz += 1;
		$egysegar = $ugylet['szall_dij']/1.27;
		//postakoltseg kiirasa
		print("<TRANS_ITEM>");
		print("<ITM_NAME>Postaköltség</ITM_NAME>");
		print("<ITM_PRICE_PRICE>{$egysegar}</ITM_PRICE_PRICE>");
		print("<ITM_PRICE_DISCOUNT>0</ITM_PRICE_DISCOUNT>");
		print("<ITM_PRICE_ORIG>{$egysegar}</ITM_PRICE_ORIG>");
		print("<ITM_PRICE_VAT_SHORT>27%</ITM_PRICE_VAT_SHORT>");
		print("<ITM_DATETIME>{$datum_most}</ITM_DATETIME>");
		print("<ITM_AMOUNT>1</ITM_AMOUNT>");
		print("<ITM_UNIT>db</ITM_UNIT>");
		print("<ITM_VTSZSZJ></ITM_VTSZSZJ>");
		print("<ITM_ORD>{$tetel_sorsz}</ITM_ORD>");
		print("<ITM_COMMENT></ITM_COMMENT>");
		print("</TRANS_ITEM>");
		

		//XML ügylet tételek végének kiírása
		print("</TRANS_ITEMS>");

		//XML ügylet végének kiírása
		print("</TRANS>");

		//MySQL ügylet szinkronizálva, így az adott ügylet frissítése a táblában
		$sql_ugylet_szink = "UPDATE rendeles SET szamla_ok = 1, szamlazhato = 0 WHERE rend_id = ".mysql_real_escape_string($ugylet['rend_id']);
		//MySQL ügylet frissítés futtatása
		mysql_query($sql_ugylet_szink, $conn_main);
		
		mysql_query("update rendeles set aktiv = 0, allapot = 8 WHERE szamla_ok = 1 and futar_ok = 1 and rend_id = ".mysql_real_escape_string($ugylet['rend_id']), $conn_main);
	}
}

	mysql_free_result($query_szamla); //MySQL erőforrás felszabadítása
	mysql_close(); //MySQL adatkapcsolat megszüntetése

	//XML végének kiírása
	print("</AXELPRO_IMP_TRANS>");
	//kimeneti buffer ürítése a böngésző felé
	flush();
	//PHP szkript futtatás vége
	die();

?>
