<?php
class conteudo_views extends HandleSql{

	private $pagina;
	function __construct() {
		parent::__construct();
		global $pagina;
		$this->pagina = $pagina;
	}

	public function get_page($pagina=0,$language='')
	{
		$codigo        = $pagina;
		$codigo_mae    = 0;
		$titulo        = "";
		$titulo_mae    = "";
		$titulo_seo    = "";
		$conteudo      = "";
		$descricao     = "";
		$extra         = "";
		$link_externo  = "";
		$alvo_link     = "";
		$palavra_chave = "";

		$language_field = ($language !== '') ? '_'. $language : '';

		//$mysql_sys = new HandleSql;
		$sql_pagina = self::select("Select tbPag.* From ".$this->DB_PREFIX."_conteudo_pagina as tbPag Where tbPag.status=1 And tbPag.pagina_idx=".round($pagina)." ");

		if($sql_pagina && is_array($sql_pagina))
		{
			$sql_pagina_mae = self::select("Select titulo,pagina_idx From ".$this->DB_PREFIX."_conteudo_pagina Where status=1 And pagina_idx=".round($sql_pagina[0]['pagina_mae'])." ");
			if($sql_pagina_mae && is_array($sql_pagina_mae)){
				$titulo_mae = $sql_pagina_mae[0]['titulo'];
				$codigo_mae = $sql_pagina_mae[0]['pagina_idx'];
			}
			$titulo        = $sql_pagina[0]['titulo' . $language_field];
			$titulo_seo    = $sql_pagina[0]['titulo_seo' . $language_field];
			$conteudo      = $sql_pagina[0]['conteudo' . $language_field];
			$descricao     = $sql_pagina[0]['descricao' . $language_field];
			$extra         = $sql_pagina[0]['extra'];
			$link_externo  = $sql_pagina[0]['link_externo'];
			$alvo_link     = $sql_pagina[0]['alvo_link'];
			$palavra_chave = $sql_pagina[0]['palavra_chave'];

		}else{
			if($pagina!=0){
				$codigo="404";
			}else{
				$codigo=0;
			}
		}

		return array(
			'codigo' 		=> $codigo,
			'codigo_mae' 	=> $codigo_mae,
			'titulo' 		=> $titulo,
			'titulo_mae'	=> $titulo_mae,
			'titulo_seo'	=> $titulo_seo,
			'conteudo' 		=> $conteudo,
			'descricao' 	=> $descricao,
			'extra' 		=> $extra,
			'link_externo' 	=> $link_externo,
			'alvo_link' 	=> $alvo_link,
			'palavra_chave' => $palavra_chave
		);
	}

	public function get_menufilho($pagina=0,$titulo="Mais informações")
	{
		$data_menu = "";

		//$mysql_sys = new HandleSql;
		$sql_paginas = self::select("Select titulo,pagina_idx From ".$this->DB_PREFIX."_conteudo_pagina Where status=1 And pagina_mae=".round($pagina)." Order By indice ASC, titulo ASC ");

		if($sql_paginas && is_array($sql_paginas))
		{
			$data_menu .= ' <div class="caixa_menu_pagina">
                    <div class="titulo_menu_pagina"><span>' . $titulo . '</span></div><!-- .titulo_menu_pagina -->

                    <div class="itens_menu_pagina">
                        <ul>';
			$counter=0;
			foreach($sql_paginas as $sql_pagina){
				if($counter>0)
				{
					$data_menu .= '<li class="separa_itens_menu_pagina" ><img src="site/images/spacer.gif" width="100%" height="1" border="0" /></li>';
				}
				$data_menu .= '<li><a href="?pagina='.$sql_pagina['pagina_idx'].'">'.$sql_pagina['titulo'].'</a></li>';
				$counter++;
			}
			$data_menu .= '</ul>
                    </div><!-- .itens_menu_pagina -->
                </div><!-- .caixa_menu_pagina -->';
		}

		return $data_menu;

	}

	public function get_menu($menu=0,$lista=false,$separador=false,$submenu=false,$inicio=false,$inicio_side='',$inicio_txt="In&iacute;cio", $language = '')
	{
		$language_field = ($language !== '') ? '_' . $language : '';
		$data_menu = "";

		//$mysql_sys = new HandleSql;
		$sql_menu = self::select("Select nome From ".$this->DB_PREFIX."_conteudo_menu Where menu_idx=".round($menu)." ");
		$sql_paginas = self::select("Select Pagina.*,MenuPagina.nome as AltTitle From ".$this->DB_PREFIX."_conteudo_pagina as Pagina Inner Join ".$this->DB_PREFIX."_conteudo_menu_paginas as MenuPagina ON MenuPagina.pagina_idx=Pagina.pagina_idx Where Pagina.status=1 And MenuPagina.menu_idx=".round($menu)." Order By MenuPagina.ranking DESC,Pagina.indice ASC,Pagina.titulo ASC ");

		$nome_menu = "";
		if($sql_menu && is_array($sql_menu))
		{
			$nome_menu = strtolower(Text::normalize($sql_menu[0]['nome']));
		}

		if($sql_paginas && is_array($sql_paginas))
		{
			// $data_menu .= '<div id="cmd-'.str_replace("_","-",$nome_menu).'" class="cmd_'.$nome_menu.' cmd_menu ">';
			$data_menu .= ($lista) ? '<ul id="lista-'.str_replace("_","-",$nome_menu).'" class="lista_'.$nome_menu.' lista_menu menu navbar-nav " >' : '';
			$counter=0;
			$counter_reg=0;

			if($inicio && $inicio_side==='l' && isset($_GET['pagina']))
			{
				$data_menu .= ($lista) ? '<li id="lista-item-'.$counter.'" class="nav-item lista_item lista_item_'.$counter.'" >' : '';
				$data_menu .= '<a class="a_princ" href="/" >'.$inicio_txt.'</a>'; //<div id="cmd-item-'.$counter.'" class="cmd_item cmd_item_'.$counter.'" ></div>
				$data_menu .= ($lista) ? '</li>' : "";
				$counter++;
			}
			foreach($sql_paginas as $sql_pagina)
			{
				$first=($counter_reg==0)? "first" : "";
				$last=($counter_reg==(count($sql_paginas)-1))? "last" : "";
				if($counter==0){ $first="first"; }

				if(($counter>0 && $separador))
				{
					$data_menu .= ($lista) ? '<li id="lista-item-'.$counter.'" class="separador li_separador lista_item">' : '';
					$data_menu .= '<div class="div_separador"><img src="/site/images/spacer.gif" ></div>';
					$data_menu .= ($lista) ? '</li>' : '';
					$counter++;
				}
				$data_menu .= ($lista) ? '<li id="lista-item-'.$counter.'" class="lista_item nav-item lista_item_'.$counter.' '.$first.$last.' odd" >' : '';

				$sql_pagina_mae = self::select("Select titulo From ".$this->DB_PREFIX."_conteudo_pagina Where status=1 And pagina_idx=".round($sql_pagina['pagina_mae'])." ");

				if ($language !== '') {
					if($sql_pagina_mae && is_array($sql_pagina_mae)){
						$url_mae = '/' . Text::friendlyUrl($sql_pagina_mae[0]['titulo_en']);
					}else{
						$url_mae = '/';
					}
					$menu_url      = '/en' . $url_mae . Text::friendlyUrl($sql_pagina['titulo_en']);
				}else{
					if($sql_pagina_mae && is_array($sql_pagina_mae)){
						$url_mae = '/' . Text::friendlyUrl($sql_pagina_mae[0]['titulo']) . '/';
					}else{
						$url_mae = '/';
					}
					$menu_url      = $url_mae . Text::friendlyUrl($sql_pagina['titulo']);
				}

				$menu_url_alvo = '_self';
				if($sql_pagina['link_externo']!=''){
					$menu_url = $sql_pagina['link_externo'];
					$menu_url_alvo = $sql_pagina['alvo_link'];
				}


				if ($this->pagina == $sql_pagina['pagina_idx']) {
					$active = ' active ';
				}else{
					$active = '';
				}

				$titulo = $sql_pagina['AltTitle'];
				if(is_null($titulo) || trim($titulo)==""){
					$titulo = trim($sql_pagina['titulo' . $language_field]);
				}

				$data_menu .= '<a id="m-'.$menu.'-link-'.Text::friendlyUrl($sql_pagina['titulo']).'" class="nav-link'. $active .'" href="'.$menu_url.'" title="'.$sql_pagina['titulo'].'"  target="'.$menu_url_alvo.'" >'.$titulo.'</a>';
				
				// var_dump($data_menu);
				
				// if($submenu) {
				// 	$data_menu .= '<div id="navbarDropdown" class="cmd_item cmd_item_'.$counter.' cmd_item_id_'.$sql_pagina['pagina_idx'].' '.$first.$last.' odd ' . $active . '" ><a class="dropdown-toggle data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" a_princ_m transition nav-link" role="button" href="'.$menu_url.'" title="'.$sql_pagina['titulo'].'" target="'.$menu_url_alvo.'" >'.$titulo.'</a></div>';
				// }
				

				if($submenu){

					//Traz o submenu do link
					$sql_paginas_filho = self::select("Select * From ".$this->DB_PREFIX."_conteudo_pagina Where status=1 And pagina_mae=" . round($sql_pagina['pagina_idx']) . " Order By indice ASC,titulo ASC ");
					if (count($sql_paginas_filho) > 0) {
						$data_menu .= '<ul class="menu_dropdown dropdown-menu transition">';

						for ($i=0; $i < count($sql_paginas_filho); $i++) {
							//if ($i !== 0) $data_menu .= '<li class="separa_sub_menu"><hr></li>';
							if ($language_field != '') {
								$url_fil = $menu_url . '/' . Text::friendlyUrl($sql_paginas_filho[$i]['titulo_en']);
							}else{
								$url_fil = $menu_url . '/' . Text::friendlyUrl($sql_paginas_filho[$i]['titulo']);
							}
							$url_fil_alvo="_self";
							// if($sql_paginas_filho[$i]['link_externo']!=''){
							// 	$url_fil = $sql_paginas_filho[$i]['link_externo'];
							// 	$url_fil_alvo = $sql_paginas_filho[$i]['alvo_link'];
							// }

							$data_menu .= '<li><a class="dropdown-item" href="' . $url_fil . '" target="'.$url_fil_alvo.'" >' . $sql_paginas_filho[$i]['titulo' . $language_field] . '</a></li>';
						}
						$data_menu .= '</ul>';
					}


				}

				$data_menu .= ($lista) ? '</li>' : '';

				$counter++;
				$counter_reg++;
			}
			if($inicio && $inicio_side==='r' && isset($_GET['pagina']))
			{
				if($separador)
				{
					$data_menu .= ($lista) ? '<li id="lista-item-'.$counter.'" class="separador li_separador lista_item">' : '';
					$data_menu .= '<div class="div_separador"><img src="/site/images/spacer.gif" ></div>';
					$data_menu .= ($lista) ? '</li>' : '';
					$counter++;
				}
				$data_menu .= ($lista) ? '<li id="lista-item-'.$counter.'" class="lista_item lista_item_'.$counter.'" >' : '';
				$data_menu .= '<div id="cmd-item-'.$counter.'" class="cmd_item cmd_item_'.$counter.'" ><a href="/">'.$inicio_txt.'</a></div>';
				$data_menu .= ($lista) ? '</li>' : '';
			}
			$data_menu .= ($lista) ? '</ul>' : '';
		}

		return $data_menu;

	}

}
?>