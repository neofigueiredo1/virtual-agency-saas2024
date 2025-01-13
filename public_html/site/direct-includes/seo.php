<?php
	/*Especifico para módulo de cases*/
	$case_cid = (isset($_GET['cid']))?$_GET['cid']:0;
	if(!is_numeric($case_cid)){$case_cid=0;}
	if($case_cid!=0)
	{
		$case_data = $m_cases->getClientes(true, $case_cid);
	}
	/* xx módulo de cases */

	$bodyClass = '';
	if($case_cid!=0 && isset($case_data)){
		if(is_array($case_data) && count($case_data)>0){

			$siteTitle        = Sis::config('CLI_TITULO') . ' - ' . Sis::desvar($pagina_data['titulo']) . ' - ' . $case_data[0]['nome'] . ' - ' . $case_data[0]['nome'];
			$pageDescription  = (Sis::desvar($case_data[0]['cas_descricao']) != '') ? Sis::desvar(substr(strip_tags($case_data[0]['cas_descricao']), 0, 300)) : Sis::config('CLI_DESCRICAO');
			$pagePalavraChave = (Sis::desvar($pagina_data['palavra_chave']) != '') ? Sis::desvar($pagina_data['palavra_chave']) : Sis::config('CLI_KEYWORDS');
			$bodyClass        .= ' interna ';
			$pageCanonical 	= Sis::config('CLI_URL') . Text::friendlyUrl($pagina_data['titulo']) ."/".$case_data[0]['case_idx']."/".Text::friendlyUrl($case_data[0]['nome']);

		}else{
			$siteTitle        = Sis::config('CLI_TITULO') . ' - ' . Sis::desvar($pagina_data['titulo']);
			$pageDescription  = (Sis::desvar($pagina_data['descricao']) != '') ? Sis::desvar($pagina_data['descricao']) : Sis::config('CLI_DESCRICAO');
			$pagePalavraChave = (Sis::desvar($pagina_data['palavra_chave']) != '') ? Sis::desvar($pagina_data['palavra_chave']) : Sis::config('CLI_KEYWORDS');
			$bodyClass        .= ' interna ';
			if ($pagina_data['titulo_mae'] != '') {
				$pageCanonical = Sis::config('CLI_URL') . Text::friendlyUrl($pagina_data['titulo_mae']) . '/' . Text::friendlyUrl($pagina_data['titulo']);
			}else{
				$pageCanonical = Sis::config('CLI_URL') . Text::friendlyUrl($pagina_data['titulo']);
			}
		}


	}else if($pagina != 0 && $pagina_data['codigo']!=0){

		$siteTitle        = Sis::config('CLI_TITULO') . ' - ' . Sis::desvar($pagina_data['titulo']);
		$pageDescription  = (Sis::desvar($pagina_data['descricao']) != '') ? Sis::desvar($pagina_data['descricao']) : Sis::config('CLI_DESCRICAO');
		$pagePalavraChave = (Sis::desvar($pagina_data['palavra_chave']) != '') ? Sis::desvar($pagina_data['palavra_chave']) : Sis::config('CLI_KEYWORDS');
		$bodyClass        .= ' interna ';
		if ($pagina_data['titulo_mae'] != '') {
			$pageCanonical = Sis::config('CLI_URL') . Text::toAscii($pagina_data['titulo_mae']) . '/' . Text::toAscii($pagina_data['titulo']);
		}else{
			$pageCanonical = Sis::config('CLI_URL') . Text::toAscii($pagina_data['titulo']);
		}

	}else{
		$siteTitle        = Sis::config('CLI_TITULO');
		$pageDescription  = Sis::config('CLI_DESCRICAO');
		$pagePalavraChave = Sis::config('CLI_KEYWORDS');
		$bodyClass        .= '';
		$pageCanonical    = Sis::config('CLI_URL');
	}

	if ($language !== '') {
		$bodyClass .= ' language_' . $language;
		if ($pagina_data['titulo_mae'] != '') {
			$pageCanonical = Sis::config('CLI_URL') . $language.'/' . Text::toAscii($pagina_data['titulo_mae']) . '/' . Text::toAscii($pagina_data['titulo']);
		}else{
			$pageCanonical = Sis::config('CLI_URL') . $language.'/' . Text::toAscii($pagina_data['titulo']);
		}
	}
	$siteImage = Sis::config('CLI_URL') . '/site/images/mosaico-branding-logomarca.png';
?>