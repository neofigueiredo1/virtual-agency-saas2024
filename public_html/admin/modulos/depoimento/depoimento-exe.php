<?php
	require_once "../../config.php";

	// VERIFICANDO A PERMISSÃO
	if (!Sis::checkPerm('10010-2') && !Sis::checkPerm('10010-4'))
	{
	    exit();
	}

	$mod = 'depoimento';
	$pag = 'depoimento';

	require_once "depoimento-model.php";
	require_once "depoimento-control.php";

	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

	$exe = (isset($_GET['exe'])) ? $_GET['exe'] : 0;
	$tbl = (isset($_GET['tbl'])) ? $_GET['tbl'] : 0;
	$id = (isset($_GET['id'])) ? (int)$_GET['id'] : 0;

	$sqlQuery = new HandleSql;
	$tbPrefix	= $sqlQuery->getPrefix();
	$TB_DEPOIMENTO  			= $tbPrefix."_depoimento";

	//update o ranking de acordo com a tabela de registros.
	if($exe==1)
	{
		$sqlQuery = new HandleSql;
		$ordem = $_GET["ordem"];
		$aOrdem = explode(",",$ordem);
		$rnk = (count($aOrdem)*10)+10;
		for($i=0;$i<count($aOrdem);$i++)
		{
			$res = $sqlQuery->update("UPDATE ".$TB_DEPOIMENTO_IMAGE." SET ranking=".round($rnk)." WHERE image_idx=".round($aOrdem[$i]));
			$rnk -= 10;
		}
		ob_clean();
		echo "ok";
		exit();
	}

	//Exclui a imagem.
	if($exe==2)
	{
		$sqlQuery = new HandleSql;

		$res = $sqlQuery->select("SELECT imagem FROM ".$TB_DEPOIMENTO_IMAGE." WHERE image_idx=".round($id));

		if(isset($res) && $res!=false ){

			$arquivo_midia = $res[0]['imagem'];

			//Lista as imagens enviadas
			//Verifica as pastas do sistema
			$BaseDir = realpath("..".DS."..".DS."..".DS."");

			$SisDir = constant("PASTA_CONTENT");
			$LocalDir = $BaseDir.DS.$SisDir;

			//Pasta do módulo
			$ModuloDir = "depoimento";
			$LocalModuloDir = $LocalDir.DS.$ModuloDir;

			//Pasta de imagens do módulo
			$ModuloImgDir = "images";
			$LocalModuloImgDir = $LocalModuloDir.DS.$ModuloImgDir;

			if(file_exists($LocalModuloImgDir.DS."p".DS.$arquivo_midia)){ unlink($LocalModuloImgDir.DS."p".DS.$arquivo_midia); }
			if(file_exists($LocalModuloImgDir.DS."m".DS.$arquivo_midia)){ unlink($LocalModuloImgDir.DS."m".DS.$arquivo_midia); }
			if(file_exists($LocalModuloImgDir.DS."g".DS.$arquivo_midia)){ unlink($LocalModuloImgDir.DS."g".DS.$arquivo_midia); }

			$res = $sqlQuery->delete("DELETE FROM ".$TB_DEPOIMENTO_IMAGE." WHERE image_idx=".round($id));

		}

		ob_clean();
		echo "ok";
		exit();
	}

	 if($exe==3)
    {
        $sqlQuery = new HandleSql;
        $ordem = $_GET["ordem"];
        $aOrdem = explode(",",$ordem);
        $rnk = (count($aOrdem)*10)+10;
        for($i=0;$i<count($aOrdem);$i++)
        {
            $res = $sqlQuery->update("UPDATE ".$TB_DEPOIMENTO." SET ranking=".round($rnk)." WHERE depoimento_idx=".round($aOrdem[$i]));
            $rnk -= 10;
        }
        ob_clean();
        echo "ok";
        exit();
    }

?>