<?php
require_once("config.php");


if (!$m_cadastro->isLogged()){
	header("Location:/");
	exit();
}


$midiaId = isset($_GET['mId'])?(int)$_GET['mId']:0;
if ($midiaId==0){
	header("Location:/");
	exit();	
}

$cursoModulo = new CursoModulo();

//Recupera o arquivo de midia
$midiaReg = $cursoModulo->midiaGetMidia($midiaId);
if (!(isset($midiaReg)&&count($midiaReg)>0)) {
	die("<script>alert('O arquivo de mídia solicitado não existe!');history.back();</script>");
	exit();
}

$file = $_SERVER['DOCUMENT_ROOT'].DS."sitecontent".DS."curso".DS."midia".DS."arquivos".DS.$midiaReg[0]['arquivo'];

if (trim($midiaReg[0]['arquivo']) && !file_exists($file)) {
	die("<script>alert('O arquivo de mídia solicitado não existe!');history.back();</script>");
	exit();
}

if ((int)$midiaReg[0]['restrito']==1 && (int)$_SESSION['plataforma_usuario']['perfil']==0) { //o arquivo é restrito e o usuario não é aluno.
	die("<script>alert('O arquivo de mídia é restrito a Alunos CPOT!');history.back();</script>");
	exit();
}

//Registra o log de Download.
$cursoModulo->mudiaLogAdd($midiaId,'download');

ob_clean();
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$ext = pathinfo($dados[0]['arquivo_midia'], PATHINFO_EXTENSION);
$basename = pathinfo( $file , PATHINFO_BASENAME);

header("Content-type: application/".$ext);
header('Content-length: '.filesize($file));
header("Content-Disposition: attachment; filename=\"$basename\"");
ob_clean();
flush();

readfile($file);
exit;