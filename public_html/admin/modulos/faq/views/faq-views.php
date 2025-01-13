<?php


class faq_views extends HandleSql
{
	public $TB_FAQ;
	public $TB_FAQ_ITEM;

	function __construct(){
		
		parent::__construct();

		$this->TB_FAQ = self::getPrefix() . "_faq";
		$this->TB_FAQ_ITEM = self::getPrefix() . "_faq_item";
	}

	/**
	 * Function que retorna um arquivo da pasta /views do módulo
	 * @param string $nome -> Nome da view a ser exibida
	 * @return void
	 */
	public function getView($nome="")
	{
		if(file_exists('admin/modulos/faq/views/' . $nome . '.php')){
			require($nome.'.php');
		}else{
			echo 'View não encontrada';
		}
	}

	public function getFaqGrupos(){
		$array = array('status'=>1,'orderby' => 'ORDER BY ranking DESC');
		return $this->sqlCRUD($array, '',$this->TB_FAQ, '', 'S', 0, 0);
	}
	public function getFaqItens($fid=0){
		$array = array('faq_idx'=>(int)$fid,'status'=>1,'orderby' => 'ORDER BY ranking DESC');
		return $this->sqlCRUD($array, '',$this->TB_FAQ_ITEM, '', 'S', 0, 0);
	}

} // End class
?>