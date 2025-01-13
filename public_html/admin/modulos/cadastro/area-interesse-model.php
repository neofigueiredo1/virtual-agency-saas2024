<?php
/**
 * Classe de gerenciamento de dados de áreas de interesse dos cadastros
 *
 * @package area_interesse_m
 **/
class area_interesse_m extends HandleSql{
    /**
     * Nome da tabela de menus
     *
     **/
    public $TB_CADASTRO;
    public $TB_CADASTRO_AREA;

    public function __construct() {
    	parent::__construct();
    	$this->TB_CADASTRO = self::getPrefix() . "_cadastro";
    	$this->TB_CADASTRO_AREA = self::getPrefix() . "_cadastro_interesse";

    }

}