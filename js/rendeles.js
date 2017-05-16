
function copyNev(){
	var formx = document.getElementById('rendeles');
	formx.szamla_nev.value = formx.nev.value;
}

function copyIrsz(){
	var formx = document.getElementById('rendeles');
	formx.szamla_irsz.value = formx.irsz.value;
}

function copyVaros(){
	var formx = document.getElementById('rendeles');
	formx.szamla_varos.value = formx.varos.value;
}

function copyUtca(){
	var formx = document.getElementById('rendeles');
	formx.szamla_utca.value = formx.utca.value;
}

function delTer(tid) {
	var formTer = document.getElementById('termek_'+tid);
	var ter = formTer.tnev.value;
	if (confirm('Biztos, hogy törlöd a(z) '+ter+' terméket?')) {
		formTer.muvelet.value = 'torol';
		formTer.submit();
	}
}

function modTer(tid) {
	var formTer = document.getElementById('termek_'+tid);
	var strHiany = '';
	
	formTer.nev.value = formTer.nev.value.replace(/^\s+|\s+$/g, '');
	
	if (tid=='new') {
		formTer.muvelet.value = 'uj';
	}
	else formTer.muvelet.value = 'modosit';
	
	if (formTer.marka.value == 0) strHiany += 'Hiányzó adat: márka!\n';
	if (formTer.nev.value == '') strHiany += 'Hiányzó adat: termék név!\n';
	if (formTer.egysegar.value == 0 || formTer.egysegar.value == '') {
		strHiany += 'Hiányzó adat: termék egységár!\n';
	}
	else if (isNaN(formTer.egysegar.value)) strHiany += 'Hibás adat: termék egységár csak számokból állhat!\n';
	if (formTer.darab.value == 0 || formTer.darab.value == '') {
		strHiany += 'Hiányzó adat: termék mennyiség!\n';
	}
	else if (isNaN(formTer.darab.value)) strHiany += 'Hibás adat: termék mennyiség csak számokból állhat!\n';
	
	
	if (strHiany == '') formTer.submit();
	else alert (strHiany);
	
}

function szamolErtek() {
	var formNewTer = document.getElementById('termek_new');
	var y = String(Number(formNewTer.egysegar.value) * Number(formNewTer.darab.value));
	var strErtek ='';
	var z = 0;
	for (z = y.length-3;z>-4;z-=3) strErtek = y.substring(z, z + 3) + ' ' + strErtek;
	document.getElementById('newter_ertek').innerHTML = strErtek+"Ft";
}

function setVaros(irsz, varos) {
	var formx = document.getElementById('rendeles');
	var x = '';
	if (irsz == 'irsz') {
		x = formx.irsz.value;
		if (x.substring(0,1) == '1') formx.varos.value = 'Budapest';
	}
	else if (irsz == 'szamla_irsz') {
		x = formx.szamla_irsz.value;
		if (x.substring(0,1) == '1') formx.szamla_varos.value = 'Budapest';
	}
	
}

function newMegj() {
	var formx = document.getElementById('megjegyzes_new');
	var megjx = document.getElementById('megjegyzes_szoveg');
	var megjt = megjx.value;
	
	megjt = megjt.replace(/^\s+|\s+$/g, '');
	if (formx.user.value == '') {
		alert ('ÁLLJ! Nevezze meg magát!');
	}
	else {
		if (megjt == '') {
			alert ('Hát valami üzenetet ossz meg a többiekkel!');
		}
		else {
			formx.submit();
		}
	}
}

function checkProblema() {
	var formx = document.getElementById('rendeles');

	if (formx.problema.checked == true) {
		var biztos = confirm("Biztos, hogy problémás ez a rendelés?");
		if (biztos == false) {
			formx.problema.checked = false;
		}
	}
}

function toggle(div_id) {
	var el = document.getElementById(div_id);
	if ( el.style.display == 'none' ) {	el.style.display = 'block';}
	else {el.style.display = 'none';}
}

function blanket_size(popUpDivVar) {
	if (typeof window.innerWidth != 'undefined') {
		viewportheight = window.innerHeight;
	} else {
		viewportheight = document.documentElement.clientHeight;
	}
	if ((viewportheight > document.body.parentNode.scrollHeight) && (viewportheight > document.body.parentNode.clientHeight)) {
		blanket_height = viewportheight;
	} else {
		if (document.body.parentNode.clientHeight > document.body.parentNode.scrollHeight) {
			blanket_height = document.body.parentNode.clientHeight;
		} else {
			blanket_height = document.body.parentNode.scrollHeight;
		}
	}

	var blanket = document.getElementById('blanket');
	blanket.style.height = blanket_height + 'px';
	var popUpDiv = document.getElementById(popUpDivVar);
	var popUpDiv_height = document.defaultView.getComputedStyle(popUpDiv,null).getPropertyValue('height');
	popUpDiv_height = popUpDiv_height.replace('px', '');
	popUpDiv_top = Math.floor((blanket_height-Number(popUpDiv_height))/2);
	popUpDiv.style.top = String(popUpDiv_top) + 'px';
	if(popUpDivVar == 'modPopup') popUpDiv.style.top = '50px';
	
}

function window_pos(popUpDivVar) {
	if (typeof window.innerWidth != 'undefined') {
		viewportwidth = window.innerHeight;
	} else {
		viewportwidth = document.documentElement.clientHeight;
	}
	if ((viewportwidth > document.body.parentNode.scrollWidth) && (viewportwidth > document.body.parentNode.clientWidth)) {
		window_width = viewportwidth;
	} else {
		if (document.body.parentNode.clientWidth > document.body.parentNode.scrollWidth) {
			window_width = document.body.parentNode.clientWidth;
		} else {
			window_width = document.body.parentNode.scrollWidth;
		}
	}
	var popUpDiv = document.getElementById(popUpDivVar);
	var popUpDiv_width = document.defaultView.getComputedStyle(popUpDiv,null).getPropertyValue('width');
	popUpDiv_width = popUpDiv_width.replace('px', '');
	popUpDiv_left = Math.floor((window_width-Number(popUpDiv_width))/2);
	popUpDiv.style.left = String(popUpDiv_left) + 'px';
	if(popUpDivVar == 'modPopup') popUpDiv.style.left = '150px';
}

function csspopup(windowname) {
	blanket_size(windowname);
	window_pos(windowname);
	toggle('blanket');
	toggle(windowname);
}
