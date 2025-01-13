<?php include "../../../../site/config.php";
$idb  = isset($_GET['b']) ? $_GET['b'] : 0;
$hRef = isset($_GET['hRef']) ? $_GET['hRef'] : '';

if ($hRef != '') {
	$http_referer = urldecode($hRef);
}else{
	$http_referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
}

$remote_addr  = $_SERVER['REMOTE_ADDR'];

$mysqli = new manipula_sql();
$sqlBanner = $mysqli->seleciona("SELECT url FROM apl_banner WHERE banner_idx=" . $idb . "");

if (count($sqlBanner) > 0) {
	if ($sqlBanner[0]['url'] == '' || $sqlBanner[0]['url'] == '#') {
		ob_clean();
		die(sis::redirecionar("http://" . $_SERVER["HTTP_HOST"] . "/", 0.01));

	}else{
		$mysqli->insere("INSERT INTO apl_banner_data(banner_idx,tipo,http_referer,remote_addr) VALUES('" . $idb . "',1,'" . $http_referer . "','" . $remote_addr . "')");
		die(sis::redirecionar($sqlBanner[0]['url'], 0.01));
	}
}else{
	ob_clean();
	die(sis::redirecionar("http://" . $_SERVER["HTTP_HOST"] . "/", 0.01));
}
