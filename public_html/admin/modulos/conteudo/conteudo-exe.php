<?php
	require_once "../../config.php";

	$mod = 'conteudo';
	$pag = 'pagina';

	require_once "pagina-model.php";
	require_once "pagina-control.php";

	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

	$exe 			= (isset($_GET['exe'])) ? $_GET['exe'] : 0;
	$tbl 			= (isset($_GET['tbl'])) ? $_GET['tbl'] : 0;

	$descricao 	= (isset($_GET['descricao'])) ? $_GET['descricao'] : "";
	$tags 		= (isset($_GET['tags'])) ? $_GET['tags'] : "";
	$id 			= (isset($_GET['id'])) ? $_GET['id'] : 0;

	$handleSql = new HandleSql;
	$TB_PAGE = $handleSql->getPrefix()."_conteudo_pagina";
	

	//Vefica se existe uma página com o msm nome
	if($exe==10)
	{
		$titulo 	= (isset($_GET['titulo'])) ? $_GET['titulo'] : "";
		$url 		= (isset($_GET['url'])) ? $_GET['url'] : "";
		$pg_id 	= (isset($_GET['id'])) ? $_GET['id'] : "0";

		Text::personalizeUrl(0, $pg_id, $titulo, $url, $TB_PAGE);
		exit();
	}

	if($exe==11)
	{
		$titulo 	= "";
		$url 		= (isset($_GET['url'])) ? $_GET['url'] : "";
		$edit		= (isset($_GET['edit'])) ? $_GET['edit'] : 0;

		Text::personalizeUrl(1, $edit, $titulo, $url, $TB_PAGE);
		exit();
	}

	//Salva a descricvao de uma imagem do lugar.
	if($exe==42)
	{
		$lserial = (isset($_GET['lserial'])) ? $_GET['lserial'] : "";
		if($lserial!="")
		{
			

			$lserial = explode("@@",$lserial);
			$ranking = 0;
			foreach($lserial as $ls)
			{
				$ranking++;
				$_elementos = explode("=",$ls);
				$pagina = trim(str_replace("]","",str_replace("list[","",$_elementos[0])));
				$pagina_mae = trim($_elementos[1]);
				if((string)($pagina_mae)=="null")
				{
					$pagina_mae=0;
				}
				$handleSql->update("Update ".$TB_PAGE." Set indice=". round($ranking) .", pagina_mae=". round($pagina_mae) . " Where pagina_idx=" . round($pagina) . " ");
			}
			$m_pagina = new pagina();
			$m_pagina->urlRewriteUpdate();
		}
		ob_clean();
		exit();
	}

?>
