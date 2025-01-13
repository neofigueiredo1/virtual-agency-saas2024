<?php
	class pedido_model extends HandleSql {
		
		
		protected $TB_PEDIDO;
		protected $TB_PEDIDO_ITENS;
		protected $TB_PEDIDO_ENDERECO;
		protected $TB_PEDIDO_STATUS;

		protected $TB_CUPOM;

		protected $TB_PAGAMENTO;
		
		protected $TB_CURSO;
		protected $TB_CURSO_INSCRITOS;
		
		protected $TB_CADASTRO;
		protected $TB_REPASSE;
		protected $TB_REPASSE_ITENS;

		function __construct(){

			parent::__construct();

			$this->TB_PEDIDO = self::getPrefix() . "_ecommerce_pedido";
			$this->TB_PEDIDO_ITENS = self::getPrefix() . "_ecommerce_pedido_itens";
			$this->TB_PEDIDO_ENDERECO = self::getPrefix() . "_ecommerce_pedido_endereco";
			$this->TB_PEDIDO_STATUS = self::getPrefix() . "_ecommerce_pedido_status";

			$this->TB_CUPOM = self::getPrefix() . "_ecommerce_cupom";
			
			$this->TB_PAGAMENTO = self::getPrefix() . "_ecommerce_pagamento";

			$this->TB_CURSO = self::getPrefix() . "_curso";
			$this->TB_CURSO_INSCRITOS = self::getPrefix() . "_curso_inscritos";

			$this->TB_CADASTRO = self::getPrefix() . "_cadastro";
			$this->TB_REPASSE = self::getPrefix() . "_ecommerce_repasse";
			$this->TB_REPASSE_ITENS = self::getPrefix() . "_ecommerce_repasse_itens";
			
		}



		public function mPedidosListAll($id=0, $filtros="", $paginaAtual=0, $registroPagina=0){
			$sqlWhere 	= "";
	      	$innerJoin 	= "";

	      if(is_array($filtros) && count($filtros) > 0){
	      	$sqlWhere .= "Where pedd.pedido_idx <> 0 ";

	      	if(array_key_exists("situacao", $filtros)){
	         	$sqlWhere .= " AND pedd.status=".$filtros['situacao']." ";
	         }
	         if(array_key_exists("cliente", $filtros)){
	         	$sqlWhere .= " AND pedd.cadastro_idx=".$filtros['cliente']." ";
	         }
	         if(array_key_exists("data_de", $filtros)){
	         	$sqlWhere .= " AND pedd.data_cadastro >= '".$filtros['data_de']. " 00:00:00 ' ";
	         }
		      if(array_key_exists("data_ate", $filtros)){
	         	$sqlWhere .= " AND pedd.data_cadastro <= '".$filtros['data_ate']. " 23:59:59 ' ";
	         }
	         if(array_key_exists("palavra_chave", $filtros)){
	         	$sqlWhere .= " AND ( 	pedd.pedido_chave Like '%".$filtros['palavra_chave']."%'
	         	                     	OR pedd.observacoes Like '%".$filtros['palavra_chave']."%'
	         	                     	OR Cadt.nome_completo Like '%".$filtros['palavra_chave']."%'
	         	                     	OR Cadt.email Like '%".$filtros['palavra_chave']."%'
	         	                     	OR Cadt.cpf_cnpj Like '%".$filtros['palavra_chave']."%'
	         	                    	)";
	         }
	      }

	      $sqlQuery = "SELECT pedd.*, Cadt.nome_completo, Cadt.email, Pstatus.nome as status_nome FROM " . $this->TB_PEDIDO ." as pedd
	      Inner Join " . $this->TB_CADASTRO ." as Cadt On Cadt.cadastro_idx=pedd.cadastro_idx
	      Left Join " . $this->TB_PEDIDO_STATUS ." as Pstatus On Pstatus.status_idx=pedd.status
	      ".$sqlWhere." Order By data_cadastro DESC";

	      if($registroPagina==0){
	      	return parent::select($sqlQuery);
	      }else{
	      	return parent::selectPage($sqlQuery, $registroPagina, $paginaAtual);
	      }
		}

		public function mListUsersWithPedido($id=0){
			$sqlWhere = "";
			if($id!=0){
				$sqlWhere = "and Cadt.cadastro_idx = ".$id;
			}
			return parent::select("SELECT DISTINCT Cadt.cadastro_idx, Cadt.nome_completo FROM " . $this->TB_CADASTRO ." as Cadt
			                      Inner Join " . $this->TB_PEDIDO ." as pedd On Cadt.cadastro_idx=pedd.cadastro_idx
			                      ".$sqlWhere."
			                      Order By Cadt.nome_completo ASC");
		}

		public function mListSituacaoWithPedido($id=0){
			$sqlWhere = "";
			if($id!=0){
				$sqlWhere = " And Stat.status_idx = ".$id;
			}
			return parent::select("SELECT DISTINCT Stat.status_idx, Stat.nome FROM " . $this->TB_PEDIDO_STATUS ." as Stat
			                      Inner Join " . $this->TB_PEDIDO ." as pedd On Stat.status_idx=pedd.status
			                      ".$sqlWhere."
			                      Order By Stat.nome ASC");
		}
		
	}
?>
