<?php
/**
 * Classe de modelo do módulo
 *
 * @package Conteudo
 **/
class conteudo_model extends HandleSql{

	public $TB_PAGINA;

	function __construct(){
		parent::__construct();
		$this->TB_PAGINA = self::getPrefix() . "_conteudo_pagina";
	}

}

?>