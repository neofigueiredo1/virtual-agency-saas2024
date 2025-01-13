<?php
	class produto_model extends HandleSql{

		//Constantes de nome do banco do módulo
		protected $TB_PRODUTO;
		protected $TB_PRODUTO_IMAGEM;
		protected $TB_PRODUTO_VARIACAO;
		protected $TB_PRODUTO_COMENTARIO;
		protected $TB_PRDIDO_ITENS;

		protected $TB_PRODUTO_CATEGORIA;

		protected $TB_PRODUTO_DADO;
		protected $TB_PRODUTO_VAR_DADO;
		protected $TB_VAR_DADO;
		protected $TB_VAR_VALOR;

		protected $TB_FABRICANTE;

		protected $TB_AVISEME;
		protected $TB_CADASTRO;

		public function __construct(){

			parent::__construct();

			$this->TB_PRODUTO = self::getPrefix() . "_ecommerce_produto";
			$this->TB_PRODUTO_IMAGEM = self::getPrefix() . "_ecommerce_produto_imagem";
			$this->TB_PRODUTO_VARIACAO = self::getPrefix() . "_ecommerce_produto_variacao";
			$this->TB_PRODUTO_COMENTARIO = self::getPrefix() . "_ecommerce_produto_comentario";
			$this->TB_PRDIDO_ITENS = self::getPrefix() . "_ecommerce_pedido_itens";

			$this->TB_PRODUTO_DADO = self::getPrefix() . "_ecommerce_produto_opcao";
			$this->TB_PRODUTO_VAR_DADO = self::getPrefix() . "_ecommerce_produto_variacao_opcao";
			$this->TB_VAR_DADO = self::getPrefix() . "_ecommerce_produto_opcao_dado";
			$this->TB_VAR_VALOR = self::getPrefix() . "_ecommerce_produto_opcao_valor";

			$this->TB_PRODUTO_CATEGORIA = self::getPrefix() . "_ecommerce_produto_categoria";
			$this->TB_FABRICANTE = self::getPrefix() . "_ecommerce_fabricante";

			$this->TB_AVISEME = self::getPrefix() . "_ecommerce_aviseme";
			$this->TB_CADASTRO = self::getPrefix() . "_cadastro";
			

		}


		public function listAllM($matrix, $page=0, $pageNumRows=0){

	      $sqlOrderBy = " Order By prod.nome ASC ";
	      $sqlWhere 	= "";
	      $and 			= "";
	      $innerJoin 	= "";
	      $campos = "";

	      if(is_array($matrix) && count($matrix) > 1){
	         $sqlWhere = (count($matrix) <= 1) ? "" : " Where prod.produto_idx <> 0 ";
	         
	         if(array_key_exists("categoria", $matrix)){
	         	$innerJoin = "Inner join ". $this->TB_PRODUTO_CATEGORIA ." as cat on cat.categoria_idx=prod.categoria_idx ";
	         	$sqlWhere .= " AND cat.categoria_idx=".round($matrix['categoria']);
	         }

	         if(array_key_exists("status", $matrix)){
	         	$sqlWhere .= " AND prod.status=".round($matrix['status']);
	         }
	         
	         if(array_key_exists("fabricante", $matrix)){
	         	$innerJoin = "Inner join ". $this->TB_FABRICANTE ." as fab on fab.fabricante_idx=prod.fabricante_idx ";
	         	$sqlWhere .= " AND fab.fabricante_idx=".round($matrix['fabricante']);
	         }
	         
	         if(array_key_exists("destaque", $matrix)){
	         	$sqlWhere .= " AND prod.destaque=".(int)$matrix['destaque'];
	         }

	         if(array_key_exists("oferta", $matrix)){
	         	$sqlWhere .= " AND prod.em_oferta=".(int)$matrix['oferta'];
	         }

	         if(array_key_exists("withImage", $matrix)){
	         	$campos .= ",(SELECT imagem FROM ". $this->TB_PRODUTO_IMAGEM ." Where produto_idx=prod.produto_idx Order By ranking DESC Limit 0,1 ) as imagem ";
	         }

	         if(array_key_exists("imagem", $matrix)){
	         	if ((int)$matrix['imagem']==1) {
	         		$innerJoin .= " Inner Join ". $this->TB_PRODUTO_IMAGEM ." as pImg ON pImg.produto_idx=prod.produto_idx ";
	         		$sqlWhere .= " And pImg.imagem<>'' And pImg.imagem<>'Null' ";
	         	}else{
		         	$innerJoin .= " Left Join ". $this->TB_PRODUTO_IMAGEM ." as pImg ON pImg.produto_idx=prod.produto_idx ";
		         	$sqlWhere .= " And (pImg.imagem IS NULL Or pImg.imagem='' Or pImg.imagem='Null') ";
	         	}
	        
	         }

	         if(array_key_exists("estoque", $matrix)){
	         	switch ((int)$matrix['estoque']) {
	         		case 1:
	         			$sqlWhere .= " And prod.quantidade>0 ";
	         			break;
	         		case 2:
	         			$sqlWhere .= " And (prod.quantidade=0 Or prod.quantidade IS NULL) ";
	         			break;
	         		case 3:
	         			$sqlWhere .= " And (prod.quantidade>0 And prod.quantidade<=2) ";
	         			break;
	         	}
	         }

	         if(array_key_exists("valor", $matrix)){
	         	if ((int)$matrix['valor']==1) {
	         		$sqlWhere .= " And prod.valor>0 ";
	         	}else{
	         		$sqlWhere .= " And (prod.valor=0 Or prod.valor IS NULL) ";
	         	}
	         }

	         if(array_key_exists("palavra_chave", $matrix)){
				$nomeSemAcento = str_replace("-", " ",Text::toAscii(Text::clean($matrix['palavra_chave'])));
	         	$sqlWhere .= " AND ( 	LOWER(prod.nome_noaccent)   Like LOWER('%".$nomeSemAcento."%')
	                                    OR LOWER(prod.nome)  		Like LOWER('%".$matrix['palavra_chave']."%')
	                                    OR prod.quantidade  		Like '%".$matrix['palavra_chave']."%'
	         	                     	OR prod.em_oferta_valor     Like '%".$matrix['palavra_chave']."%'
	         	                     	OR prod.nacionalidade	    Like '%".$matrix['palavra_chave']."%'
	         	                     	OR prod.valor	    		Like '%".$matrix['palavra_chave']."%'
	         	                     	OR prod.pdv_id      		Like '%".$matrix['palavra_chave']."%'
	         	                     	OR prod.descricao_curta	    Like '%".$matrix['palavra_chave']."%'
										 )";
										 

	         }
	         if(array_key_exists("avaliados", $matrix)){
	         	if($matrix['avaliados'] == 2){
		         	$sqlWhere .= " And prod.produto_idx NOT IN ( SELECT produto_idx FROM ". $this->TB_PRODUTO_COMENTARIO ." ) ";
	         	}else if($matrix['avaliados'] == 1){
	         		$innerJoin = " Inner join ". $this->TB_PRODUTO_COMENTARIO ." as comm on comm.produto_idx=prod.produto_idx ";
	         	}else if($matrix['avaliados'] == 3){
	         		$innerJoin = " Inner join ". $this->TB_PRODUTO_COMENTARIO ." as comm on comm.produto_idx=prod.produto_idx ";
		         	$sqlWhere .= " AND comm.status=0";
	         	}
	         }
	      }
	      $sqlQuery = "SELECT Distinct prod.* ".$campos." FROM " . $this->TB_PRODUTO ." as prod ".$innerJoin." ".$sqlWhere. " ".$sqlOrderBy;


	      // var_dump($sqlQuery);
	      // exit();

	      if($page==0)
	      {
	         return self::select($sqlQuery);
	      }else{
	         return self::selectPage($sqlQuery,$pageNumRows,$page);
	      }
		}
	}
?>