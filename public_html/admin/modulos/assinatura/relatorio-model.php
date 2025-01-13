<?php
class relatorio_model extends HandleSql{

	public $TB_PEDIDO;
	public $TB_PEDIDO_ITENS;
	public $TB_CADASTRO;

	
	public function __construct(){
		parent::__construct();

		$this->TB_PEDIDO = self::getPrefix() . "_ecommerce_pedido";
		$this->TB_PEDIDO_STATUS = self::getPrefix() . "_ecommerce_pedido_status";
		$this->TB_CURSO = self::getPrefix() . "_curso";
		$this->TB_PEDIDO_ITENS = self::getPrefix() . "_ecommerce_pedido_itens";
		$this->TB_CADASTRO = self::getPrefix() . "_cadastro";

	}

	/**
	 * Processa a lista de todos os usuários cadastrados
	 * @return array
	 */
	public function listAllPurchaseByProdutor($matrix){

		$sqlWhere = "";
		if(is_array($matrix) && count($matrix) > 0){
			if(array_key_exists("status_pagamento", $matrix)){
				$sqlWhere .= " AND tbPedido.status=".(int)$matrix['status_pagamento']. " ";
			}
			if(array_key_exists("data_de", $matrix)){
				$sqlWhere .= " AND tbPedido.data_cadastro >= '".$matrix['data_de']. " 00:00:00 '";
			}
			if(array_key_exists("data_ate", $matrix)){
				$sqlWhere .= " AND tbPedido.data_cadastro <= '".$matrix['data_ate']. " 23:59:59 '";
			}
			
			// if(array_key_exists("palavra_chave", $matrix)){
			// 	$sqlWhere .= " AND (
			// 						tbCadastro.nome_completo Like '%".$matrix['palavra_chave']."%'
			// 					)";
			// }

		}
		$strSQL = "SELECT DISTINCT tbCadastro.nome_completo as nome_produtor, tbCadastro.email, tbCadastro.cadastro_idx as produtor_id,
			(
				SELECT 
					count(tbPedido.pedido_idx) AS total
				FROM ". $this->TB_PEDIDO." as tbPedido
				INNER JOIN ". $this->TB_PEDIDO_ITENS." as tbPedidoItens ON tbPedidoItens.pedido_idx = tbPedido.pedido_idx
				INNER JOIN ". $this->TB_CURSO." as tbC ON tbPedidoItens.curso_idx = tbC.curso_idx
				WHERE tbC.produtor_idx = produtor_id ".$sqlWhere."
			) as pedidos,
			(
				SELECT 
					(SUM(tbPedidoItens.item_valor)-tbPedido.desconto_valor) AS receita
				FROM ". $this->TB_PEDIDO." as tbPedido
				INNER JOIN ". $this->TB_PEDIDO_ITENS." as tbPedidoItens ON tbPedidoItens.pedido_idx = tbPedido.pedido_idx
				INNER JOIN ". $this->TB_CURSO." as tbC ON tbPedidoItens.curso_idx = tbC.curso_idx
				WHERE tbC.produtor_idx = produtor_id ".$sqlWhere."
			) as receita
			FROM ". $this->TB_CADASTRO ." as tbCadastro
			
			INNER JOIN ". $this->TB_CURSO." as tbC2 ON tbC2.produtor_idx = tbCadastro.cadastro_idx
			INNER JOIN ". $this->TB_PEDIDO_ITENS." as tbPedidoItens2 ON tbPedidoItens2.curso_idx = tbC2.curso_idx

			WHERE tbCadastro.perfil=1
			ORDER BY pedidos DESC ";

		return self::select($strSQL);

	}

	/**
	 * Processa uma lista de compras por dia
	 * @return array
	 */
	public function listAllPurchase($matrix){
		$actualDate = date ("Y-m-d 00:00:00");
		$dayofweek = date('w', strtotime($actualDate));
		$startDate = "";
		$endDate = "";
		
		switch ($dayofweek) {
			case 1:
				//Segunda
				$startDate = $actualDate;
				$endDate = date('Y-m-d 23:59:59',strtotime('+1 Sunday'));
				break;
			case 2:
				//Terça
				$startDate = date('Y-m-d 00:00:00',strtotime('-1 Monday'));
				$endDate = date('Y-m-d 23:59:59',strtotime('+1 Sunday'));
				break;
			case 3:
				//Quarta
				$startDate = date('Y-m-d 00:00:00',strtotime('-1 Monday'));
				$endDate = date('Y-m-d 23:59:59',strtotime('+1 Sunday'));
				break;
			case 4:
				//Quinta
				$startDate = date('Y-m-d 00:00:00',strtotime('-1 Monday'));
				$endDate = date('Y-m-d 23:59:59',strtotime('+1 Sunday'));
				break;
			case 5:
				//Sexta
				$startDate = date('Y-m-d 00:00:00',strtotime('-1 Monday'));
				$endDate = date('Y-m-d 23:59:59',strtotime('+1 Sunday'));
				break;
			case 6:
				//Sabado
				$startDate = date('Y-m-d 00:00:00',strtotime('-1 Monday'));
				$endDate = date('Y-m-d 23:59:59',strtotime('+1 Sunday'));
				break;
			case 7:
				//Domingo
				$startDate = date('Y-m-d 00:00:00',strtotime('-1 Monday'));
				$endDate = $actualDate;
				break;
			default:
				$startDate = "";
				$endDate = "";
		};
		

		$sqlOrderBy = " ORDER BY tb.data_cadastro DESC ";
		$sqlWhere 	= " WHERE tb.data_cadastro >= '".$startDate. " ' AND tb.data_cadastro <= '".$endDate. " '";

		if(is_array($matrix) && count($matrix) > 0){
			if(array_key_exists("data_de", $matrix)){
				$sqlWhere = "WHERE tb.item_idx <> 0 ";
			}
			if(array_key_exists("data_de", $matrix)){
				$sqlWhere .= " AND tb.data_cadastro >= '".$matrix['data_de']. " 00:00:00 '";
			}
		   if(array_key_exists("data_ate", $matrix)){
			   $sqlWhere .= " AND tb.data_cadastro <= '".$matrix['data_ate']. " 23:59:59 '";
			}
			if(array_key_exists("status_pagamento", $matrix)){
				$sqlWhere .= " AND tbPedido.pagamento_status=".(int)$matrix['status_pagamento'];
			}
		}
		
		return self::select(
			"SELECT CAST(tb.data_cadastro AS date) AS dia,
					COUNT(CAST(tb.data_cadastro AS date)) AS pedidos,
					SUM(item_valor) AS receita,
					AVG(item_valor) AS ticket_medio
					
			FROM ". $this->TB_PEDIDO_ITENS." as tb
			INNER JOIN ".$this->TB_PEDIDO." as tbPedido ON tb.pedido_idx = tbPedido.pedido_idx
			".$sqlWhere."
			GROUP BY CAST(tb.data_cadastro AS date)
			HAVING COUNT(CAST(tb.data_cadastro AS date)) > 0
			ORDER BY dia ASC "
		);
	}

	/**
	 * Processa uma lista que seleciona os pedidos por produto
	 * @return array
	 */
	public function listAllPurchaseByCurso($matrix){
		$sqlWhere = "";
		if(is_array($matrix) && count($matrix) > 0){
			$sqlWhere = (count($matrix) <= 1) ? "" : " WHERE tb.curso_idx <> 0 ";
			if(array_key_exists("data_de", $matrix)){
				$sqlWhere .= " AND tbPedido.data_cadastro >= '".$matrix['data_de']. " 00:00:00 '";
			}
			if(array_key_exists("data_ate", $matrix)){
				$sqlWhere .= " AND tbPedido.data_cadastro <= '".$matrix['data_ate']. " 23:59:59 '";
			}
			if(array_key_exists("palavra_chave", $matrix)){
				$sqlWhere .= " AND (
									tb.nome Like '%".$matrix['palavra_chave']."%'
								)";
			}
		}

		return self::select(
			"SELECT COUNT(tbPedidoItens.curso_idx) AS pedidos,
					SUM(item_valor) AS receita,
					tb.nome

			FROM ". $this->TB_CURSO." as tb
			INNER JOIN ". $this->TB_PEDIDO_ITENS." as tbPedidoItens ON tbPedidoItens.curso_idx = tb.curso_idx
			INNER JOIN ". $this->TB_PEDIDO." as tbPedido ON tbPedido.pedido_idx = tbPedidoItens.pedido_idx
			".$sqlWhere."
			GROUP BY tbPedidoItens.curso_idx
			HAVING COUNT(tbPedidoItens.curso_idx) > 0
			ORDER BY pedidos DESC "
		);
	}
}
?>