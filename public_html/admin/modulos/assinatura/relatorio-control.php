<?php

	require_once('pedido-model.php');
	require_once('pedido-control.php');

	$m_pedido = new pedido();

	class relatorio extends relatorio_model{

		public $MODULO_CODIGO 		= "10033";
		public $MODULO_AREA   		= "Curso - Relatorios";
		public $mod, $pag;


		public function __construct(){

			parent::__construct();
			
			global $act;
			$this->mod = "ecommerce";
			$this->pag = "relatorio";
			$this->act = $act;

			$basedir = BASE_PATH.DS.PASTA_CONTENT;

			$this->pasta_modulo = $basedir.DS.$this->mod.DS;
			$this->pasta_modulo_pag = $this->pasta_modulo.$this->pag.DS;
			$this->pasta_modulo_images =   $this->pasta_modulo_pag."images".DS;

			if(!is_dir($this->pasta_modulo)){
			   mkdir($this->pasta_modulo);
			}
			if(!is_dir($this->pasta_modulo_pag)){
			   mkdir($this->pasta_modulo_pag);
			}
			if(!is_dir($this->pasta_modulo_images)){
			   mkdir($this->pasta_modulo_images);
			}
		}

		public function getAllProdutores($filtros=""){
			if (is_array($filtros) && count($filtros) > 0) {
				$array = $filtros;
			}

			$array['orderby'] = 'ORDER BY data_cadastro DESC';

			$dados = parent::listAllPurchaseByProdutor($array);

			return $dados;
		}

		public function getPedidos($filtros="") {

			if (is_array($filtros) && count($filtros) > 0) {
				$array = $filtros;
			}

			$array['orderby'] = 'ORDER BY data_cadastro DESC';

			$dados = parent::listAllPurchase($array);

			return $dados;
		}

		public function getAllPurchaseByCurso($filtros=""){

			if (is_array($filtros) && count($filtros) > 0) {
				$array = $filtros;
			}

			$array['orderby'] = 'ORDER BY data_cadastro DESC';

			$dados = parent::listAllPurchaseByCurso($array);

			return $dados;
		}
		
    }
?>