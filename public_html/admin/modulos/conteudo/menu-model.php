<?php
/**
 * Classe de gerenciamento de dados da página de menus no módulo de Conteudo
 *
 * @package Conteudo
 **/
class conteudo_menu_model extends HandleSql {

	/**
	 * Nome da tabela de menus
	 *
	 **/

	public $TB_PAGINA;
	public $TB_MENU_PAGINAS;
	public $TB_MENU;

	function __construct(){
		parent::__construct();
		$this->TB_PAGINA = self::getPrefix() . "_conteudo_pagina";
		$this->TB_MENU_PAGINAS = self::getPrefix() . "_conteudo_menu_paginas";
		$this->TB_MENU = self::getPrefix() . "_conteudo_menu";
	}

	/**
	 * Retorna as páginas que estão no menu atual
	 *
	 * @return void
	 * @author
	 **/
	function getPagesOnMenuM($mn_id){
		$res = parent::select("SELECT Pagina.*,MenuPagina.nome as AltTitle
		                      FROM ". $this->TB_PAGINA." as Pagina
		                      INNER JOIN ".  $this->TB_MENU_PAGINAS." AS MenuPagina ON MenuPagina.pagina_idx=Pagina.pagina_idx
		                      WHERE MenuPagina.menu_idx=".round($mn_id)." ORDER BY MenuPagina.ranking DESC");

		if(count($res) >= 1){
			return $res;
		}else{
			return false;
		}
	}


	/**
	 * Retorna as páginas que NÃO estão no menu atual
	 *
	 * @return bool
	 * @author
	 **/
	function getPagesOutMenuM($menu_idx){

		$res = parent::select("SELECT DISTINCT Pagina.pagina_idx, Pagina.titulo
				FROM " .  $this->TB_PAGINA ." as Pagina
				LEFT JOIN " .  $this->TB_MENU_PAGINAS ." AS MenuPagina ON MenuPagina.pagina_idx=Pagina.pagina_idx
				WHERE (MenuPagina.pagina_idx Is Null Or MenuPagina.menu_idx<>".round($menu_idx).")
				AND Pagina.pagina_idx NOT IN (SELECT DISTINCT pagina_idx FROM " .  $this->TB_MENU_PAGINAS ." Where menu_idx=".round($menu_idx).") ");

		if(is_array($res) && count($res) > 0){
			return $res;
		}else{
			return false;
		}
	}

}