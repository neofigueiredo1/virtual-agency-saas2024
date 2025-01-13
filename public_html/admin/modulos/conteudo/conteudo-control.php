<?php
/**
 * Classe de controle de módulo
 *
 * @package Conteudo
 **/
class conteudo extends conteudo_model{

	public $MODULO_CODIGO   = "10001";
	public $MODULO_AREA     = "Página";

	public $pasta_modulo            = "";
	public $pasta_modulo_images     = "";
	public $pasta_modulo_images_p   = "";
	public $pasta_modulo_images_m   = "";
	public $pasta_modulo_images_g   = "";

	function __construct(){
		parent::__construct();
	}

	/**
	 * Retorna as informações para exibir na dashboard do sistema, até 2 linhas
	 */
	public function dashInfo()
	{
		$num_total=0;
		$num_online=0;

		$array = array('orderby' => 'Order By pagina_idx');
		$paginaAll = parent::sqlCRUD($array, 'pagina_idx', $this->TB_PAGINA, '', 'S', 0, 0);
		$num_total = (is_array($paginaAll)&&count($paginaAll)>0)?count($paginaAll):0;
		$array = array('status' => 1);
		$paginasOnLine = parent::sqlCRUD($array, '', $this->TB_PAGINA, '', 'S', 0, 0);
		$num_online = (is_array($paginasOnLine)&&count($paginasOnLine)>0)?count($paginasOnLine):0;
		$retorno = ($num_total>0)?" ".$num_online." pagina(s) publicada(s) de<br> ".$num_total." pagina(s) cadastradas":"Nenhuma página publicada!";
		return $retorno;
	}

}

?>