<?php

	/**
	*
	*/
	class depoimento_views extends HandleSql
	{

		function __construct()
		{
			parent::__construct();
			$this->TB_DEPOIMENTO = self::getPrefix() . "_depoimento";
		}

		public function getView($nome="")
		{
			if(file_exists('admin/modulos/depoimento/views/' . $nome . '.php')){
				require( $nome . '.php');
			}else{
				echo 'View não encontrada';
			}
		}

		public function listDepoimentos()
		{
			$gQuery = "SELECT tbdep.* FROM ". $this->TB_DEPOIMENTO." as tbdep WHERE tbdep.status=1 ORDER BY tbdep.ranking DESC";
			try {
				return parent::select($gQuery);
			 } catch (Exception $e) {
			  var_dump($e->getMessage());
			  

			 }
		}

	}

?>