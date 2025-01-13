<?php
class EcommerceVendas extends HandleSql
{

	protected $TB_PEDIDO;
	protected $TB_PEDIDO_STATUS;
	protected $TB_PEDIDO_ITENS;

	protected $TB_TRANSACAO;
	protected $TB_TRANSACAO_PARCELA;

	protected $TB_CADASTRO;
	protected $TB_CURSO;

	function __construct(){
		
		parent::__construct();
		
		$this->TB_TRANSACAO = self::getPrefix() . "_ecommerce_pedido_pagamento_transacao";
		$this->TB_TRANSACAO_PARCELA = self::getPrefix() . "_ecommerce_pedido_pagamento_transacao_parcela";

        $this->TB_PEDIDO = self::getPrefix() . "_ecommerce_pedido";
        $this->TB_PEDIDO_STATUS = self::getPrefix() . "_ecommerce_pedido_status";
        $this->TB_PEDIDO_ITENS = self::getPrefix() . "_ecommerce_pedido_itens";
        $this->TB_CADASTRO = self::getPrefix() . "_cadastro";
        $this->TB_CURSO = self::getPrefix() . "_curso";
	}

	/**
	* Processa a lista de vendas do usuario - perfil produtor
	* @return mixed => Se a consulta for realizada com sucesso, retorna um Array, caso contrário Bool(FALSE)
	*/
	public function getPedidos($filtros=array(),$pg=0,$regPorPagina=30)
	{
		
		$where = "";

		if (is_array($filtros)&&count($filtros)>0) {
			
			if (array_key_exists('periodo',$filtros) ) {
				$data_ini = isset($filtros['periodo']['ini'])?$filtros['periodo']['ini']." 00:00:00":date("Y-m-d 00:00:00",strtotime('-1 months'));
				$data_fim = isset($filtros['periodo']['fim'])?$filtros['periodo']['fim']." 23:59:59":date("Y-m-d 23:59:59");
				$where .= " And tbPed.data_cadastro BETWEEN '".$data_ini."' And '".$data_fim."' ";
			}

			if (array_key_exists('curso',$filtros) ) {
				if ((int)$filtros['curso']!=0) { 
					$where .= " And tbPedI.curso_idx='".$filtros['curso']."' ";
				}
			}

			if (array_key_exists('pagamento_status',$filtros) ) {
				if ((int)$filtros['pagamento_status']>0) {
					$where .= " And tbPed.status=".(int)$filtros['pagamento_status']." ";
				}
			}

		}

		// var_dump($where);

		$querySQL = "SELECT
				DISTINCT
				tbPed.pedido_idx,
				tbPed.data_cadastro,
				tbPed.desconto_valor,
				tbPedI.plataforma_comissao,
				tbCur.nome as curso_nome,
				tbPed.status,
				tbPed.pagamento_status,
				tbPStatus.nome as status_nome,
				(
					SELECT sum(item_valor) as total 
					FROM ".$this->TB_PEDIDO_ITENS." as tbPedI_a WHERE tbPedI_a.pedido_idx=tbPed.pedido_idx
				) as totalPedido,
				(
					SELECT sum( tbPedI_b.item_valor * (tbPedI_b.plataforma_comissao/100) ) as total 
					FROM ".$this->TB_PEDIDO_ITENS." as tbPedI_b WHERE tbPedI_b.pedido_idx=tbPed.pedido_idx
				) as totalComissaoPlataforma,
				tbTransa.taxas

			FROM ".$this->TB_PEDIDO." as tbPed
				LEFT JOIN ".$this->TB_TRANSACAO." as tbTransa ON tbPed.pedido_idx=tbTransa.pedido_idx
				INNER JOIN ".$this->TB_PEDIDO_ITENS." as tbPedI ON tbPed.pedido_idx=tbPedI.pedido_idx
				INNER JOIN ".$this->TB_CURSO." as tbCur ON tbCur.curso_idx=tbPedI.curso_idx
				INNER JOIN ".$this->TB_PEDIDO_STATUS." as tbPStatus ON tbPed.status=tbPStatus.status_idx
			WHERE
				tbCur.produtor_idx=".$_SESSION['plataforma_usuario']['id']."
				".$where."
			Order By tbPed.data_cadastro DESC ";

			// var_dump($querySQL);

		if ((int)$pg>0) {
			return parent::selectPage($querySQL,(int)$regPorPagina,(int)$pg);
		}else{
			return parent::select($querySQL);
		}
	}

	/**
	 * Seleciona os dados de um determinado pedido para a lista.
	 * @param Integer $pedido_idx => Identificador do pedido.
	 * @return mixed => Se a consulta for realizada com sucesso, retorna um Array, caso contrário Bool(FALSE)
	 */
	public function getVenda($pedido_idx)
	{
		return parent::select("SELECT
									tbPedido.*
	                         	FROM ". $this->TB_PEDIDO." as tbPedido
									INNER JOIN ".$this->TB_PEDIDO_ITENS." as tbPedI ON tbPedido.pedido_idx=tbPedI.pedido_idx
									INNER JOIN ".$this->TB_CURSO." as tbCur ON tbCur.curso_idx=tbPedI.curso_idx
									INNER JOIN ".$this->TB_PEDIDO_STATUS." as tbPStatus ON tbPedido.status=tbPStatus.status_idx
								Where
									tbPedido.pedido_idx=".$pedido_idx." 
									And tbCur.produtor_idx=".$_SESSION['plataforma_usuario']['id']."
	                         ");
	}

	public function getContagemPorPeriodo($periodo=array(),$campo,$having=true){
		
		$data_ini = isset($periodo['ini'])?$periodo['ini']." 00:00:00":date("Y-m-d 00:00:00",strtotime('-2 months'));
		$data_fim = isset($periodo['fim'])?$periodo['fim']." 23:59:59":date("Y-m-d 23:59:59");

		$cursoID = (isset($_SESSION['filtros_ecommerce_vendas_s']['curso']))?(int)$_SESSION['filtros_ecommerce_vendas_s']['curso']:0;
		$pedidoStatus = (isset($_SESSION['filtros_ecommerce_vendas_s']['pagamento_status']))?(int)$_SESSION['filtros_ecommerce_vendas_s']['pagamento_status']:0;

		$whereCurso = ($cursoID!=0)?" And tbPedI.curso_idx=".(int)$cursoID." ":"";
		$whereStatus = ((int)$pedidoStatus>0)? " And tbPed.status=".(int)$pedidoStatus." " : "";

		$SelectQuery = " ".$campo." ";
		$OrderBy = " ".$campo." ASC ";
		switch ($campo) {
			case 'data_cadastro':
				$SelectQuery = " DATE(tbPed.".$campo.") ";
				//$OrderBy = " ".$campo." DESC ";
				break;
		}
		$havingStr = ($having) ? " HAVING total>0 " : "" ; 
		$sqlQuery = "SELECT ".$SelectQuery." as dataRetorno, count(tbPed.pedido_idx) as total
					FROM ".$this->TB_PEDIDO." as tbPed
					INNER JOIN ".$this->TB_PEDIDO_ITENS." as tbPedI ON tbPed.pedido_idx=tbPedI.pedido_idx
					INNER JOIN ".$this->TB_CURSO." as tbCur ON tbCur.curso_idx=tbPedI.curso_idx
					WHERE tbCur.produtor_idx=".$_SESSION['plataforma_usuario']['id']." And tbPed.data_cadastro BETWEEN '".$data_ini."' And '".$data_fim."' ".$whereCurso.$whereStatus."
					GROUP BY dataRetorno ".$havingStr." Order By dataRetorno ASC ";
		return self::select($sqlQuery);
	}
	
	/**
	* Processa a lista completa das imagens de um determinado produto para o site.
	* @return mixed => Se a consulta for realizada com sucesso, retorna um Array, caso contrário Bool(FALSE)
	*/
	public function getVendasTotal($periodo=array()){

		$data_ini = isset($periodo['ini'])?$periodo['ini']." 00:00:00":date("Y-m-d 00:00:00",strtotime('-1 months'));
		$data_fim = isset($periodo['fim'])?$periodo['fim']." 23:59:59":date("Y-m-d 23:59:59");

		$cursoID = (isset($_SESSION['filtros_ecommerce_vendas_s']['curso']))?(int)$_SESSION['filtros_ecommerce_vendas_s']['curso']:0;
		$pedidoStatus = (isset($_SESSION['filtros_ecommerce_vendas_s']['pagamento_status']))?(int)$_SESSION['filtros_ecommerce_vendas_s']['pagamento_status']:0;

		$whereCurso = ($cursoID!=0)?" And tbPedI.curso_idx=".(int)$cursoID." ":"";
		$whereStatus = ((int)$pedidoStatus>0)? " And tbPed.status=".(int)$pedidoStatus." " : "";

		$whereCurso_a = ($cursoID!=0)?" And tbPedI_a.curso_idx=".(int)$cursoID." ":"";
		$whereStatus_a = ((int)$pedidoStatus>0)? " And tbPed_a.status=".(int)$pedidoStatus." " : "";

		$whereCurso_aa = ($cursoID!=0)?" And tbPedI_aa.curso_idx=".(int)$cursoID." ":"";
		$whereStatus_aa = ((int)$pedidoStatus>0)? " And tbPed_aa.status=".(int)$pedidoStatus." " : "";

		$whereCurso_ab = ($cursoID!=0)?" And tbPedI_ab.curso_idx=".(int)$cursoID." ":"";
		$whereStatus_ab = ((int)$pedidoStatus>0)? " And tbPed_ab.status=".(int)$pedidoStatus." " : "";

		$whereCurso_b = ($cursoID!=0)?" And tbPedI_b.curso_idx=".(int)$cursoID." ":"";
		$whereStatus_b = ((int)$pedidoStatus>0)? " And tbPed_b.status=".(int)$pedidoStatus." " : "";

		$whereCurso_c = ($cursoID!=0)?" And tbPedI_c.curso_idx=".(int)$cursoID." ":"";
		$whereStatus_c = ((int)$pedidoStatus>0)? " And tbPed_c.status=".(int)$pedidoStatus." " : "";

		$whereCurso_d = ($cursoID!=0)?" And tbPedI_d.curso_idx=".(int)$cursoID." ":"";
		$whereStatus_d = ((int)$pedidoStatus>0)? " And tbPed_d.status=".(int)$pedidoStatus." " : "";

		$whereCurso_e = ($cursoID!=0)?" And tbPedI_e.curso_idx=".(int)$cursoID." ":"";
		$whereStatus_e = ((int)$pedidoStatus>0)? " And tbPed_e.status=".(int)$pedidoStatus." " : "";

		$whereCurso_f = ($cursoID!=0)?" And tbPedI_f.curso_idx=".(int)$cursoID." ":"";
		$whereStatus_f = ((int)$pedidoStatus>0)? " And tbPed_f.status=".(int)$pedidoStatus." " : "";

		$whereCurso_g = ($cursoID!=0)?" And tbPedI_g.curso_idx=".(int)$cursoID." ":"";
		$whereStatus_g = ((int)$pedidoStatus>0)? " And tbPed_g.status=".(int)$pedidoStatus." " : "";

		$whereCurso_gx = ($cursoID!=0)?" And tbPedI_gx.curso_idx=".(int)$cursoID." ":"";
		$whereStatus_gx = ((int)$pedidoStatus>0)? " And tbPed_gx.status=".(int)$pedidoStatus." " : "";

		$whereCurso_h = ($cursoID!=0)?" And tbPedI_h.curso_idx=".(int)$cursoID." ":"";
		$whereStatus_h = ((int)$pedidoStatus>0)? " And tbPed_h.status=".(int)$pedidoStatus." " : "";

		$whereCurso_i = ($cursoID!=0)?" And tbPedI_i.curso_idx=".(int)$cursoID." ":"";
		$whereStatus_i = ((int)$pedidoStatus>0)? " And tbPed_i.status=".(int)$pedidoStatus." " : "";

		//Recupera o valor percentual de comissão a ser repassada.
		
		$queryStr = "SELECT count(tbPed.pedido_idx) as totalVendas
			,(
				SELECT sum(tbPedI_a.item_valor) as total
				FROM ".$this->TB_PEDIDO_ITENS." as tbPedI_a
				INNER JOIN ".$this->TB_PEDIDO." as tbPed_a ON tbPedI_a.pedido_idx=tbPed_a.pedido_idx
				INNER JOIN ".$this->TB_CURSO." as tbCur_a ON tbCur_a.curso_idx=tbPedI_a.curso_idx
				WHERE
					tbCur_a.produtor_idx=".$_SESSION['plataforma_usuario']['id']." And
					tbPed_a.data_cadastro BETWEEN '".$data_ini."' And '".$data_fim."'
					".$whereCurso_a.$whereStatus_a."

			) as valorTotalVendas
			,(
				SELECT sum(tbPed_gx.desconto_valor) as total
				FROM ".$this->TB_PEDIDO." as tbPed_gx
				INNER JOIN ".$this->TB_PEDIDO_ITENS." as tbPedI_gx ON tbPedI_gx.pedido_idx=tbPed_gx.pedido_idx
				INNER JOIN ".$this->TB_CURSO." as tbCur_gx ON tbCur_gx.curso_idx=tbPedI_gx.curso_idx
				WHERE 
					tbCur_gx.produtor_idx=".$_SESSION['plataforma_usuario']['id']." And
					tbPed_gx.data_cadastro BETWEEN '".$data_ini."' And '".$data_fim."'
					".$whereCurso_gx.$whereStatus_gx."

			) as valorTotalVendasDesconto
			,(
				SELECT sum(tbTrans_b.taxas) as total 
				FROM ".$this->TB_TRANSACAO." as tbTrans_b
				INNER JOIN ".$this->TB_PEDIDO." as tbPed_aa ON tbTrans_b.pedido_idx=tbPed_aa.pedido_idx
				INNER JOIN ".$this->TB_PEDIDO_ITENS." as tbPedI_aa ON tbPed_aa.pedido_idx = tbPedI_aa.pedido_idx
				INNER JOIN ".$this->TB_CURSO." as tbCur_ab ON tbCur_ab.curso_idx=tbPedI_aa.curso_idx
				WHERE 
					tbCur_ab.produtor_idx=".$_SESSION['plataforma_usuario']['id']." And
					tbPed_aa.pagamento_status=1 And tbPed_aa.status<>3 And
					tbPed_aa.data_cadastro BETWEEN '".$data_ini."' And '".$data_fim."'
					".$whereCurso_aa.$whereStatus_aa."
			) as valorTaxasFinan
			, (
				SELECT sum(tbTrans_b.comissao) as total 
				FROM ".$this->TB_TRANSACAO." as tbTrans_b
				INNER JOIN ".$this->TB_PEDIDO." as tbPed_ab ON tbTrans_b.pedido_idx=tbPed_ab.pedido_idx
				INNER JOIN ".$this->TB_PEDIDO_ITENS." as tbPedI_ab ON tbPed_ab.pedido_idx = tbPedI_ab.pedido_idx
				INNER JOIN ".$this->TB_CURSO." as tbCur_ab ON tbCur_ab.curso_idx=tbPedI_ab.curso_idx
				WHERE 
					tbCur_ab.produtor_idx=".$_SESSION['plataforma_usuario']['id']." And
					tbPed_ab.pagamento_status=1 And tbPed_ab.status<>3 And
					tbPed_ab.data_cadastro BETWEEN '".$data_ini."' And '".$data_fim."'
					".$whereCurso_ab.$whereStatus_ab."
			) as valorComissaoPlataforma
			, (
				SELECT sum(tbPedI_c.item_valor) as total_c
				FROM ".$this->TB_PEDIDO_ITENS." as tbPedI_c
				INNER JOIN ".$this->TB_PEDIDO." as tbPed_c ON tbPedI_c.pedido_idx=tbPed_c.pedido_idx
				INNER JOIN ".$this->TB_CURSO." as tbCur_c ON tbCur_c.curso_idx=tbPedI_c.curso_idx
				WHERE 
					tbCur_c.produtor_idx=".$_SESSION['plataforma_usuario']['id']." And
					tbPed_c.pagamento_status=1 And
					tbPed_c.status<>3 And
					tbPed_c.data_cadastro BETWEEN '".$data_ini."' And '".$data_fim."'
					".$whereCurso_c.$whereStatus_c."

			) as valorTotalVendasPago
			,(
				SELECT sum(tbPedI_d.item_valor) as total 
				FROM ".$this->TB_PEDIDO_ITENS." as tbPedI_d
				INNER JOIN ".$this->TB_PEDIDO." as tbPed_d ON tbPedI_d.pedido_idx=tbPed_d.pedido_idx
				INNER JOIN ".$this->TB_CURSO." as tbCur_d ON tbCur_d.curso_idx=tbPedI_d.curso_idx
				WHERE 
					tbCur_d.produtor_idx=".$_SESSION['plataforma_usuario']['id']." And
					tbPed_d.pagamento_status=0 And tbPed_d.status<>3 And
					tbPed_d.data_cadastro BETWEEN '".$data_ini."' And '".$data_fim."'
					".$whereCurso_d.$whereStatus_d."

			) as valorTotalVendasAguardando
			, (
				SELECT 
					SUM(tbTrsPar_i.valor) as totalPrevisto 
				FROM ".$this->TB_TRANSACAO_PARCELA." as tbTrsPar_i
					INNER JOIN ".$this->TB_TRANSACAO." as tbTrs_i ON tbTrs_i.transacao_idx = tbTrsPar_i.transacao_idx
					INNER JOIN ".$this->TB_PEDIDO." as tbPed_i ON tbTrs_i.pedido_idx=tbPed_i.pedido_idx
					INNER JOIN ".$this->TB_PEDIDO_ITENS." as tbPedItem_i ON tbPed_i.pedido_idx=tbPedItem_i.pedido_idx
					INNER JOIN ".$this->TB_CURSO." as tbCur_i ON tbPedItem_i.curso_idx=tbCur_i.curso_idx
				WHERE 
					tbTrsPar_i.data_prevista > Now()
					And tbTrs_i.transacao_status = 'paid'
					And tbCur_i.produtor_idx=".$_SESSION['plataforma_usuario']['id']."
					".$whereCurso_i.$whereStatus_i."

			) as valorTotalVendasAguardandoParcelamentos
			,(
				SELECT sum(tbPedI_e.item_valor) as total
				FROM ".$this->TB_PEDIDO_ITENS." as tbPedI_e
				INNER JOIN ".$this->TB_PEDIDO." as tbPed_e ON tbPedI_e.pedido_idx=tbPed_e.pedido_idx
				INNER JOIN ".$this->TB_CURSO." as tbCur_e ON tbCur_e.curso_idx=tbPedI_e.curso_idx
				WHERE 
					tbCur_e.produtor_idx=".$_SESSION['plataforma_usuario']['id']." And
					tbPed_e.status=3 And
					tbPed_e.data_cadastro BETWEEN '".$data_ini."' And '".$data_fim."'
					".$whereCurso_e.$whereStatus_e."

			) as valorTotalVendasCancelada
			,(
				SELECT count(tbPed_f.pedido_idx) as total
				FROM ".$this->TB_PEDIDO." as tbPed_f
				INNER JOIN ".$this->TB_PEDIDO_ITENS." as tbPedI_f ON tbPedI_f.pedido_idx=tbPed_f.pedido_idx
				INNER JOIN ".$this->TB_CURSO." as tbCur_f ON tbCur_f.curso_idx=tbPedI_f.curso_idx
				WHERE 
					tbCur_f.produtor_idx=".$_SESSION['plataforma_usuario']['id']." And
					tbPed_f.pagamento_status=1 And
					tbPed_f.status<>3 And
					tbPed_f.data_cadastro BETWEEN '".$data_ini."' And '".$data_fim."'
					".$whereCurso_f.$whereStatus_f."

			) as totalVendasPago
			,(
				SELECT count(tbPed_g.pedido_idx) as total
				FROM ".$this->TB_PEDIDO." as tbPed_g
				INNER JOIN ".$this->TB_PEDIDO_ITENS." as tbPedI_g ON tbPedI_g.pedido_idx=tbPed_g.pedido_idx
				INNER JOIN ".$this->TB_CURSO." as tbCur_g ON tbCur_g.curso_idx=tbPedI_g.curso_idx
				WHERE 
					tbCur_g.produtor_idx=".$_SESSION['plataforma_usuario']['id']." And
					tbPed_g.pagamento_status=0 And tbPed_g.status<>3 And
					tbPed_g.data_cadastro BETWEEN '".$data_ini."' And '".$data_fim."'
					".$whereCurso_g.$whereStatus_g."

			) as totalVendasAguardando
			,(
				SELECT count(tbPed_h.pedido_idx) as total
				FROM ".$this->TB_PEDIDO." as tbPed_h
				INNER JOIN ".$this->TB_PEDIDO_ITENS." as tbPedI_h ON tbPedI_h.pedido_idx=tbPed_h.pedido_idx
				INNER JOIN ".$this->TB_CURSO." as tbCur_h ON tbCur_h.curso_idx=tbPedI_h.curso_idx
				WHERE 
					tbCur_h.produtor_idx=".$_SESSION['plataforma_usuario']['id']." And
					tbPed_h.status=3 And
					tbPed_h.data_cadastro BETWEEN '".$data_ini."' And '".$data_fim."'
					".$whereCurso_h.$whereStatus_h."

			) as totalVendasCancelada
			
			FROM ".$this->TB_PEDIDO." as tbPed
			INNER JOIN ".$this->TB_PEDIDO_ITENS." as tbPedI ON tbPed.pedido_idx=tbPedI.pedido_idx
			INNER JOIN ".$this->TB_CURSO." as tbCur ON tbCur.curso_idx=tbPedI.curso_idx
			WHERE 
				tbCur.produtor_idx=".$_SESSION['plataforma_usuario']['id']." And
				tbPed.data_cadastro BETWEEN '".$data_ini."' And '".$data_fim."'
				".$whereCurso.$whereStatus."
		";

		// var_dump($queryStr);
		// exit();

		return parent::select($queryStr);
	}

	/**
	 * Processa os valores futuros baseados nos parcelamentos de pagamentos com cartão 
	 */
	public function getVencimentosFuturos()
	{
		$strSQL = "SELECT 
				YEAR(tbTrsPar.data_prevista) as ano, 
			    MONTH(tbTrsPar.data_prevista) as mes, 
				SUM(tbTrsPar.valor) as totalPrevisto 
			FROM ".$this->TB_TRANSACAO_PARCELA." as tbTrsPar
				INNER JOIN ".$this->TB_TRANSACAO." as tbTrs ON tbTrs.transacao_idx = tbTrsPar.transacao_idx
				INNER JOIN ".$this->TB_PEDIDO." as tbPed ON tbTrs.pedido_idx=tbPed.pedido_idx
				INNER JOIN ".$this->TB_PEDIDO_ITENS." as tbPedItem ON tbPed.pedido_idx=tbPedItem.pedido_idx
				INNER JOIN ".$this->TB_CURSO." as tbCur ON tbPedItem.curso_idx=tbCur.curso_idx
			WHERE tbCur.produtor_idx = ". (int)$_SESSION['plataforma_usuario']['id'] ." 
			And tbTrsPar.data_prevista > Now()
			And tbTrs.transacao_status = 'paid'
			GROUP BY YEAR(tbTrsPar.data_prevista), MONTH(tbTrsPar.data_prevista)
			ORDER BY ano,mes ASC
		";
		return self::select($strSQL);
	}

	/**
	* Método que retona o conteúdo do pedido para a área de "meus pedidos".
	*/
	public function getVendaDetalhes($pedido_idx)
	{
		$pedido = self::getVenda($pedido_idx);

		$retornoM = "";
		if(is_array($pedido) && count($pedido) > 0)
		{
			
			$pedido = $pedido[0];
			
			$ecomPagamento = new EcommercePagamento();
			$ecomPagamento->setPagamentoId($pedido['pagamento_idx']);
			$ecomPagamento->registrarGateway('production');
			$ecomPagamento->setPedidoId($pedido['pedido_idx']);
			$ecomPagamento->loadTransaction();
			
			

			$infoOfPayment = "<strong>Iugu</strong><br />";
			$infoOfPayment .= $ecomPagamento->gateway->getPaymentInfoCompleteSeller();

            $valor_subtotal_itens = 0;
			$retornoM = '
				<div class="card rounded-10" >
					<div class="card-body">
						<h4>Detalhes da transação com a financeira.</h4>
						<div>
							'.$infoOfPayment.'
						</div>
					</div>
				</div>
			';


				return $retornoM;
		}else{
			return "erro";
		}
	}
	
}
