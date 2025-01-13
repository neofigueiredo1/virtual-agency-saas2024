<?php
class EcommercePedido extends HandleSql
{
	private $TB_PEDIDO;
	private $TB_PEDIDO_ITENS;
	private $TB_PEDIDO_ITENS_COPRODUTOR;
	private $TB_PEDIDO_ENDERECO;

	private $TB_PAGAMENTO;
	private $TB_PEDIDO_STATUS;

	private $TB_CURSO;
	private $TB_CURSO_COPRODUTOR;

	private $TB_CADASTRO;

	function __construct(){
		parent::__construct();
		$this->TB_PEDIDO = $this->DB_PREFIX . "_ecommerce_pedido";
		$this->TB_PEDIDO_ITENS = $this->DB_PREFIX . "_ecommerce_pedido_itens";
		$this->TB_PEDIDO_ITENS_COPRODUTOR = $this->DB_PREFIX . "_ecommerce_pedido_itens_coprodutor";
		$this->TB_PEDIDO_ENDERECO = $this->DB_PREFIX . "_ecommerce_pedido_endereco";
		
		$this->TB_PAGAMENTO = $this->DB_PREFIX . "_ecommerce_pagamento";
		$this->TB_PEDIDO_STATUS = $this->DB_PREFIX . "_ecommerce_pedido_status";
		$this->TB_CURSO = $this->DB_PREFIX . "_curso";
		$this->TB_CADASTRO = $this->DB_PREFIX . "_cadastro";
	}

	/**
	* Processa e fecha o pedido.
	* @return string @codigo_pedido => Retorna o código do pedido que foi registrado para o usuário
	*/
	public function pedidoProcessaFromCheckout(&$request)
	{

		set_time_limit(60*5);
		
		if (!isset($_SESSION['ecommerce_cart'])){
			throw new Exception("O seu carrinho está vazio.", 1);
		}

		// $observacoes_cliente = strip_tags($request['observacoes']);
        // $observacoes_cliente = Text::clean($observacoes_cliente);
        // $observacoes_cliente = substr($observacoes_cliente, 0,500);

		$forma_pagamento = (int)$request['forma_pagamento'];

		$retornoPedido = json_decode('{"pedido_codigo":"","pagamento_retorno":""}');

		$ecomCarrinho = new EcommerceCarrinho();

		$ecomCupom = new EcommerceCupom();
		$ecomPagamento = new EcommercePagamento();
		$cCursos = new cCursos();

		if (!$ecomPagamento->isValidMethod($forma_pagamento)) {
			throw new Exception("M&eacute;todo de pagamento n&atilde;o identificado", 1);
		}
		$ecomPagamento->setPagamentoId($forma_pagamento);
		$ecomPagamento->setAccountId($request['produtor']['iugu_split_account_id']); //Identifica a subconta da transacao
		$ecomPagamento->registrarGateway('sandbox');
		$ecomPagamento->setPedidoIdSufix('HOMOLOGA');
		$ecomPagamento->setTransacaoPerfil('bill');


		//Cria a lista de itens do carrinho e faz uma ultima validação antes de prosseguir.
		$carrinho_itens = $ecomCarrinho->carListItens();
		$pedido_valor_total = $ecomCarrinho->carGetTotal();
		$carProdParcelamentoMax = 500000;
		foreach ($carrinho_itens as $key => $curso) {
			if ((int)$curso['parcelamento_max']>0 && (int)$curso['parcelamento_max']<$carProdParcelamentoMax) {
				$carProdParcelamentoMax = (int)$curso['parcelamento_max'];
			}
		}
		if($carProdParcelamentoMax == 500000) {
			$carProdParcelamentoMax = 12;//Valor determinado na plataforma IUGU
		}

		if ((int)$request['installmentQuantity'] > $carProdParcelamentoMax) {
			throw new Exception("O total de parcelas desejado excede o limite para os itens de compa. Um dos cursos selecionados só pode ser parcelado em no máximo ".$carProdParcelamentoMax."x.", 1);
		}
		try {
			if (!is_null($ecomPagamento->gateway)) {
				//define as chaves de API do produtor do curso.
        		$ecomPagamento->gateway->setApiKey($request['produtor']['iugu_split_live_api_token'],$request['produtor']['iugu_split_test_api_token']);
        		//Valida os parametros de pagamento.
				$ecomPagamento->gateway->validadeParams($request);
			}
		}catch (Exception $e) {
			throw $e;
		}

		// var_dump($request);
		// exit();

		// Regra que permite boleto apenas para determinados clientes.
		// if ((int)$_SESSION['plataforma_usuario']['boleto']==0 && trim($request['cieloPaymentMethod'])=='boleto') {
		// 	throw new Exception("M&eacute;todo de pagamento n&atilde;o identificado", 1);
		// }

		//Verificação para o desconto em BOLETO
		$pagamento_com_boleto = ($forma_pagamento==2)?1:0;
		$pagamento_com_boleto_desconto = 0;
	    $boleto_desconto = (int)Sis::config("ECOMMERCE-BOLETO-DESCONTO-ATIVO");
	    $boleto_desconto_percentual = (int)Sis::config("ECOMMERCE-BOLETO-DESCONTO-PERCENTUAL");
	    if ($boleto_desconto_percentual>100)
	    	$boleto_desconto_percentual=100;

	    //Valor de desconto no pedido
	    	$desconto_valor = 0;
	    	$pedido_total_enable_disccount = $pedido_valor_total;

	    //Afiliado e desconto
		    $afiliadoData = false;
		    $afiliado_idx = 0;
			$afiliado_comissao = 0;
			$afiliado_desconto = 0;

		    if (isset($_SESSION['platform_afiliado'])) {
		    	
		    	//anula o desconto com cupom caso o afiliado seja indicado e tenha desconto vinculado
				if ((int)$_SESSION['platform_afiliado']['desconto_afiliado']>0) {
					unset($_SESSION['ecommerce_cupom']);
			    }
			    $afiliadoData = $_SESSION['platform_afiliado'];
			    
			    $afiliado_idx = $afiliadoData['cadastro_idx'];
				$afiliado_comissao = ((float)$afiliadoData['afiliado_comissao']>0)?(float)$afiliadoData['afiliado_comissao']:(float)$afiliadoData['curso_afiliado_comissao'];
				$afiliado_desconto = (float)$afiliadoData['desconto_afiliado'];

				$desconto_valor = (float)((float)$pedido_total_enable_disccount * ($afiliado_desconto/100));

			}



		//Cupom de desconto
			$desconto_percent = 0;
			$aplicarDesconto=false;

			if(isset($_SESSION['ecommerce_cupom'])){
				$aplicarDesconto=true;
				
				// if ((int)$_SESSION['ecommerce_cupom']['cumulativo']==0) {//Não é cumulativo, é aplicado apenas em itens sem oferta.
	        	// 	$pedido_total_enable_disccount = $pedido_valor_total_no_offer;
	        	// }

				if ($_SESSION['ecommerce_cupom']['valor_minimo']>0) {
					if ($pedido_total_enable_disccount<$_SESSION['ecommerce_cupom']['valor_minimo']) {
						$aplicarDesconto=false;
					}
				}
				if ($aplicarDesconto) {
					if ($_SESSION['ecommerce_cupom']['tipo_desconto']==1) { //desconto percentual
						if ($_SESSION['ecommerce_cupom']['valor_desconto']>0 && $_SESSION['ecommerce_cupom']['valor_desconto']<=100) {
							$desconto_valor = (float)((float)$pedido_total_enable_disccount * ($_SESSION['ecommerce_cupom']['valor_desconto']/100));
							$desconto_percent = $_SESSION['ecommerce_cupom']['valor_desconto'];
						}
					}
					if ($_SESSION['ecommerce_cupom']['tipo_desconto']==0) { //desconto em valor
						$desconto_valor = $_SESSION['ecommerce_cupom']['valor_desconto'];
						if ($desconto_valor>$pedido_total_enable_disccount) {
							$desconto_valor = $pedido_total_enable_disccount;
						}
						$desconto_percent = (100/$pedido_total_enable_disccount) * $desconto_valor;
					}
				}
			}

        //Desconto boleto
		if($pagamento_com_boleto==1 && $boleto_desconto==1){

			//Desconto para forma de pagamento BOLETO.
			//Regra de negócio, o desconto só é ativo quando não for especificado CUPOM de desconto.
			$pagamento_com_boleto_desconto = $boleto_desconto_percentual*($pedido_valor_total/100);
		}

		/*Cria o registro do pedido e guarda o código*/
	    $array = array(
			'status' => 1,
			'pagamento_status' => 0,
			'pagamento_idx' => $forma_pagamento,
			'cadastro_idx' => $_SESSION['plataforma_usuario_buyer']['id'],
			'pagamento_com_boleto' => $pagamento_com_boleto,
			'pagamento_com_boleto_desconto_valor' => $pagamento_com_boleto_desconto,
			'cupom_idx' => 0,
			'cupom_tipo' => 0,
			'cupom_valor' => 0,
			'desconto_valor' => $desconto_valor,
			'afiliado_idx' => $afiliado_idx,
			'afiliado_comissao' => $afiliado_comissao,
			'afiliado_desconto' => $afiliado_desconto
	    );

	    if (isset($_SESSION['ecommerce_cupom']) && $aplicarDesconto) {
			$array['cupom_idx'] = $_SESSION['ecommerce_cupom']['cupom_idx'];
			$array['cupom_tipo'] = $_SESSION['ecommerce_cupom']['tipo_desconto'];
			$array['cupom_valor'] = $_SESSION['ecommerce_cupom']['valor_desconto'];
			$array['desconto_valor'] = str_replace(",",".",$desconto_valor);

			//Valida o uso do cupom.
			try {
				$cursoId = $ecomCarrinho->carGetCurso();
				$cupomData = $ecomCupom->getCupom($_SESSION['ecommerce_cupom']['codigo'],$_SESSION['plataforma_usuario_buyer']['id'],$cursoId);
			} catch (Exception $e) {
				throw $e;
			}
		}
		
		$codigo = parent::sqlCRUD($array, '',  $this->TB_PEDIDO, '', 'I', 0, 0);
		$codigo_pedido = str_pad($codigo, 9, "0", STR_PAD_LEFT); //codigo 99 => codigo_pedido 000000099
		/*Atualiza a chave do pedido*/
		$array = array(
			'pedido_idx' => $codigo,
			'pedido_chave' => $codigo_pedido
		);
		$pedido_update = parent::sqlCRUD($array, '',  $this->TB_PEDIDO, '', 'U', 0, 0);
		$retornoPedido->pedido_codigo = $codigo;
		$ecomPagamento->setPedidoId($codigo);

		if (isset($_SESSION['ecommerce_cupom']) && $aplicarDesconto) {
			//registra o uso do cupom já validado.
			try {
				$ecomCupom->registraUso($cupomData,$_SESSION['plataforma_usuario_buyer']['id'],$codigo);
			} catch (Exception $e) {
				// print_r($e->getMessage());
				// do nothing
			}
		}

		//Recupera os dados do usuário logado.
		$array_user = array( 'cadastro_idx' => $_SESSION['plataforma_usuario_buyer']['id']);
		$usuario = parent::sqlCRUD($array_user, '',  $this->TB_CADASTRO, '', 'S', 0, 0);

		/*Registra o endereco de entrega*/
		$array_endereco = array(
             'pedido_idx' => $codigo,
             'endereco' => Text::clean($request['endereco']),
             'numero' => Text::clean($request['numero']),
             'complemento' => Text::clean($request['complemento']),
             'bairro' => Text::clean($request['bairro']),
             'cep' => Text::clean($request['cep']),
             'cidade' => Text::clean($request['cidade']),
             'estado' => Text::clean($request['estado'])
         );

		$endereco_insere = parent::sqlCRUD($array_endereco, '',  $this->TB_PEDIDO_ENDERECO, '', 'I', 0, 0);

		/*Registra os itens do pedido*/
        $valor_total = 0;
        $cCoProdurores = null;

		if (is_array($carrinho_itens)&&count($carrinho_itens)>0) {
			$itens_cart = array();
			$iCount = 0;
             foreach ($carrinho_itens as $key => $item) {

				$car_item_session = $ecomCarrinho->carItemExisteSession(round($item['curso_idx']));
				
				$valor = (is_object($car_item_session)) ? $car_item_session->{'valor'} : 0 ;
                $valor_total += $valor;

                /*Cria o registro do item do pedido e guarda o código*/
		     	$array_item = array(
					'pedido_idx' => $codigo,
					'curso_idx' => $item['curso_idx'],
					'curso_em_oferta' => $item['em_oferta'],
					'plataforma_comissao' => $item['plataforma_comissao'],
					'item_valor' => $valor
                 );
                //$itens_cart[] .= $array_item;
				
				$retorno_item_insert = parent::sqlCRUD($array_item, '',  $this->TB_PEDIDO_ITENS, '', 'I', 0, 0);

				//Obtem os coprodutores do curso
                $cCoProdurores = $cCursos->getCoprodutores($item['curso_idx']);
                if (is_array($cCoProdurores)&&count($cCoProdurores)>0){
                	foreach ($cCoProdurores as $ckey_prod => $coprodutor) {
                		/*Cria o registro do item e coproduto*/
				     	$array_item_c = array(
							'item_idx' => $retorno_item_insert,
							'cadastro_idx' => $coprodutor['cadastro_idx'],
							'curso_idx' => $coprodutor['curso_idx'],
							'comissao' => $coprodutor['comissao_percentual'],
							'pedido_idx' => $codigo
		                 );
		                $coprod_item_insert = parent::sqlCRUD($array_item_c, '',  $this->TB_PEDIDO_ITENS_COPRODUTOR, '', 'I', 0, 0);
                	}
                }
                //Inclui no item os coprodutores
               	$array_item['coprodutores'] = $cCoProdurores;
                
                array_push($itens_cart,$array_item);

				$carrinho_itens[$iCount]['valor'] = $valor;
				$iCount++;

			}
		}

		/*Envia mensagem ao cliente / operador*/
		$pedido = parent::select("SELECT tbPedido.*,
		                         tbPedidoEnd.endereco,
		                         tbPedidoEnd.numero,
		                         tbPedidoEnd.bairro,
		                         tbPedidoEnd.cep,
		                         tbPedidoEnd.cidade,
		                         tbPedidoEnd.estado,
		                         tbPedidoEnd.complemento,
		                         tbFormaPg.nome as forma_pagamento_nome FROM ". $this->TB_PEDIDO." as tbPedido
										INNER JOIN ". $this->TB_PEDIDO_ENDERECO." as tbPedidoEnd ON tbPedidoEnd.pedido_idx = tbPedido.pedido_idx
										LEFT JOIN  ". $this->TB_PAGAMENTO." as tbFormaPg ON tbFormaPg.pagamento_idx = tbPedido.pagamento_idx
										Where tbPedido.pedido_idx=".$codigo."
	                         ");

		$sqlItensPedido = "
							SELECT DISTINCT tbPedidoItens.*,tbCur.nome as pnome, tbCur.codigo as pcodigo,tbCur.imagem as pimage,
							tbCur.plataforma_comissao,tbCur.facebook_pixel_id,tbCur.google_tag_manager_id,tbCur.parcelamento_max,
							tbProdutor.iugu_split_account_id,tbProdutor.iugu_split_live_api_token,tbProdutor.iugu_split_test_api_token
									FROM ". $this->TB_PEDIDO_ITENS." as tbPedidoItens
									INNER JOIN ". $this->TB_CURSO." as tbCur ON tbCur.curso_idx = tbPedidoItens.curso_idx
									INNER JOIN ". $this->TB_CADASTRO." as tbProdutor ON tbCur.produtor_idx = tbProdutor.cadastro_idx
									Where tbPedidoItens.pedido_idx=".$codigo."
								";
		$pedido_itens = parent::select($sqlItensPedido);

		$request['pedido_resource'] = array(
			'pedido'=>$pedido[0],
			'pedido_itens'=>$pedido_itens,
			'coprodutores' => $cCoProdurores,
			'afiliado' => $afiliadoData,
			'usuario'=>$usuario[0],
			'pedido_total'=>$valor_total,
			'pedido_desconto'=>$desconto_valor,
		);

		$_SESSION['ECOM_PEDIDO_RESOURCE'] = serialize($request['pedido_resource']);

		
		//Preapra para atualiza o status do pedido
		$processaUpdate = false;
		$arraypUpdate = array('pedido_idx' => $codigo);
		$pagamentoErroMensagem="";
		//Inicia o processo de pagamento com o gateway
		try {
			if (!is_null($ecomPagamento->gateway)) {
				//Pagamento com gateway
				if ($forma_pagamento==1) {
					
					$retornoPedido->pagamento_retorno = $ecomPagamento->gateway->makePayment($request);
					$ecomPagamento->transactionPersist();

					$transactionReturn = $retornoPedido->pagamento_retorno->getTransactionData();
					if ($transactionReturn->code===1) {
						$pagamentoErroMensagem = $transactionReturn->message;
					}else{
						if (trim($retornoPedido->pagamento_retorno->getTransactionStatusCode())=='paid') { // 1=Pagamento apto a ser capturado ou definido como pago, 2=Pagamento confirmado e finalizado
							$arraypUpdate['pagamento_status'] = 1; //Status de pagamento confirmado.
							$arraypUpdate['status'] = 2; //Status de pagamento confirmado.
							$processaUpdate=true;
							$pagamentoComSucesso=true;
						}elseif($retornoPedido->pagamento_retorno->getTransactionStatusCode()!='paid'){ //Aguardando atualização de status
							$pagamentoComSucesso=true;
						}else{
							//Trata o retorno do processo de pagamento
							$transactionReturn = $retornoPedido->pagamento_retorno->getTransactionData();
							$pagamentoErroMensagem = $transactionReturn->message;
						}

					}
				}

			}else{
				$retornoPedido->pagamento_retorno = json_decode('{"code":"1","message":"","url_pagamento":"","tid":""}');
			}
		} catch (Exception $e) {
			throw $e;
		}

		//NOVO TRATAMENTO
		//Em caso de pagamento sem sucesso, todos as quantidades são retornadas e o pedidos e itens antes registrados são eliminados.
		if (!$pagamentoComSucesso) {

			//Deleta o pedido, itens e registros relacionados.
			parent::delete("DELETE FROM " . $this->TB_PEDIDO . " WHERE pedido_idx=".(int)$codigo." ");
			parent::delete("DELETE FROM " . $this->TB_PEDIDO_ITENS . " WHERE pedido_idx=".(int)$codigo." ");
			parent::delete("DELETE FROM " . $this->TB_PEDIDO_ITENS_COPRODUTOR . " WHERE pedido_idx=".(int)$codigo." ");
			$ecomPagamento->removeTransaction();
			$ecomCupom->cancelaUso((int)$codigo);

			//Da o retorno com a mensagem de erro.
			throw new Exception("Seu pagamento não foi realizado.<br/>Retorno da financeira: ".$pagamentoErroMensagem.".<br/><br/>Tente novamente ou escolha uma forma de pagamento diferente.", 1);
			exit();

		}

		// var_dump($retornoPedido);
		// exit();

		/*Encerra a sessão do carrinho e outras*/
		unset($_SESSION['ecommerce_cupom']); //Sessão do cupom de desconto.
		$ecomCarrinho->carFinaliza(); //Deve estar comentado para as finalidades de validação.
		
		//Atualiza o pedido com os detalhes.
		if ($processaUpdate){
			parent::sqlCRUD($arraypUpdate, '',  $this->TB_PEDIDO, '', 'U', 0, 0);
		}

		//Envia a mensagem com os detalhes do pedido para o e-mail do cliente.
		try {
			self::pedidoFechadoClienteMensagem($pedido,$pedido_itens,$usuario,$forma_pagamento);
		} catch (Exception $e) {
			//DoNothing
		}

		sleep(3);

		try {
			self::pedidoFechadoProdutorMensagem($pedido,$pedido_itens,$usuario,$forma_pagamento);
		} catch (Exception $e) {
			//DoNothing
		}

        return $retornoPedido;

	}

	public function pedidoFechadoClienteMensagem($pedido,$pedido_itens,$usuario,$forma_pagamento)
	{
		$pedido = $pedido[0];
		$usuario = $usuario[0];

		$valor_subtotal_itens = 0;
			
		$message_body = '
			<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" /><title></title></head><body>
				<div style="margin: 10px auto; width:800px; background:#efeeee; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #333333; text-decoration: none;"  >
					
					<div style="padding:20px 0px; display:flex; align-items:flex-end; justify-content:space-between; background-color:#fff;" >
						<img src="https://'.$_SERVER["HTTP_HOST"].'/'.Sis::config("CLILOGO").'" /> 
						<div><span style="font-size:20px;margin-left: 50px;" >Resumo do Pedido</span> &nbsp; em: '. date("d\/m\/Y \&\a\g\\r\a\\v\\e\;\s H:i:s") .'</div>
					</div>

					<table width="800" border="0" cellpadding="0" cellspacing="0" align="center" style="padding:0px 0px; border-radius: 5px;margin-top: 5px;" >
						<tbody>
							<tr>
								<td bgcolor="#ffffff" style="padding: 10px 20px; border-radius: 3px;" >
									<table width="100%" border="0" cellpadding="12" cellspacing="0" align="center" >
										<tbody>
											<tr>
												<td width="50%" style="padding-left: 0px;">
													<p style="font-size: 24px; color:#3580b5;" >
														Nº do pedido: <span style="color:#565656; font-size: 24px;" >
														'. $pedido['pedido_chave'] .'
														</span>
													</p>
												</td>
												<td width="50%" align="right" style="padding-right:0px;" >
													<span>'. date("d\/m\/Y \&\a\g\\r\a\\v\\e\;\s H:i:s") .'</span>
												</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
							<tr>
							<td bgcolor="#ffffff" style="padding: 20px; border-radius: 3px; border-top: 1px solid #eee; font-size: 16px;">
								<p>Olá, '.$usuario['nome_completo'].'!</p>
								<p> Obrigado por ter efetuado sua compra em nossa plataforma on-line.<br/>
									Abaixo  seguem os detalhes sobre sua compra<br>
									Verifique o andamento da sua compra e inscrições pela plataforma: <a href="https://'.$_SERVER['HTTP_HOST'].'" >'.$_SERVER['HTTP_HOST'].'</a></p>
							</td>
							</tr>
							<tr>
								<td bgcolor="#efeeee" style="padding: 10px 20px; border-radius: 3px 3px 0px 0px;">
									<b style="font-size: 17px;">Seus Dados</b>
								</td>
							</tr>
							<tr>
								<td bgcolor="#ffffff" style="padding: 10px 20px; border-radius: 0px 0px 3px 3px;">
									Nome: <span>'.$usuario['nome_completo'].'</span><br>
									CPF/CNPJ: <span>'.$usuario['cpf_cnpj'].'</span><br>
									E-mail: <span>'.$usuario['email'].'</span><br>
								</td>
							</tr>
							<tr>
								<td bgcolor="#efeeee" style="padding: 10px 20px; border-radius: 3px 3px 0px 0px;">
									<b style="font-size: 17px;">Meio de Pagamento Escolhido</b>
								</td>
							</tr>
							<tr>
								<td bgcolor="#ffffff" style="padding: 10px 20px; border-radius: 0px 0px 3px 3px;">
									'.$pedido["forma_pagamento_nome"].'
								</td>
							</tr>
							
							<tr>
								<td bgcolor="#efeeee" style="padding: 10px 20px; border-radius: 3px 3px 0px 0px;" >
									<b style="font-size: 17px;">Itens do seu pedido</b>
								</td>
							</tr>
							<tr>
								<td bgcolor="#ffffff" style="padding: 10px 20px; border-radius: 0px 0px 3px 3px;">
									<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="padding: 20px 10px 25px;" >
										<tbody>
											<tr>
												<td></td>
												<td><b>Item:</b></td>
												<td><b>Valor:</b></td>
												<td style="text-align: right; padding-right: 25px;"><b>Subtotal:</b></td>
											</tr>
				
				';
				foreach ($pedido_itens as $key => $item){

					$subtotal_item = $item["item_valor"];
					$valor_subtotal_itens += $subtotal_item;

					$message_body .= '
											<tr>
												<td>'.	((!is_null($item['pimage']) && trim($item['pimage'])!="Null")?'<img src="https://'.$_SERVER["HTTP_HOST"].'/sitecontent/curso/curso/images/'.$item['pimage'].'" width="100" />':'')	 .'</td>
												<td><strong>'.$item["pnome"].'</strong></td>
												<td width="100" >R$ '.number_format($item["item_valor"], 2, ',', '.').'</td>
												<td align="right" width="100" >R$ '.number_format($subtotal_item, 2, ',', '.').'</td>
											</tr>
									';
				}

				$message_body .= '
										</tbody>
									</table>
								</td>
							</tr>
							<tr>
								<td bgcolor="#efeeee" style="padding: 10px 20px; border-radius: 3px 3px 0px 0px;" >
									<b style="font-size: 17px;">Valores à pagar</b>
								</td>
							</tr>
							<tr>
								<td bgcolor="#ffffff" >
									<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="padding: 20px 10px 25px;" >
										<tbody>
											<tr>
												<td bgcolor="#ffffff" style="padding: 10px 20px 12px;">
													<div style="float: left; font-size: 15px;">Sub total: </div>
													<div style="float: right;">R$ '.number_format($valor_subtotal_itens, 2, ',', '.').'</div>
												</td>
											</tr>
				';

				//Verifica o desconto no pedido.
				$desconto_valor=0;
				$boleto_desconto_valor=0;
				if(isset($pedido["cupom_idx"])){
					if ($pedido["cupom_idx"]!=0) {
						$desconto_valor = $pedido["desconto_valor"];
						$message_body .= '<tr><td bgcolor="#ffffff" style="padding: 10px 20px 12px;">
							<div style="float: left; font-size: 15px;">Desconto: </div>
							<div style="float: right;">R$ -'.number_format($desconto_valor, 2, ',', '.').'</div>
							</td></tr>';
					}
				}
				if(isset($pedido["afiliado_idx"])){
					if ($pedido["afiliado_idx"]!=0 && $pedido["desconto_valor"]!=0) {
						$desconto_valor = $pedido["desconto_valor"];
						$message_body .= '<tr><td bgcolor="#ffffff" style="padding: 10px 20px 12px;">
							<div style="float: left; font-size: 15px;">Desconto (afiliado): </div>
							<div style="float: right;">R$ -'.number_format($desconto_valor, 2, ',', '.').'</div>
							</td></tr>';
					}
				}

				if(isset($pedido["pagamento_com_boleto"]) && isset($pedido["pagamento_com_boleto_desconto_valor"])){

					if ($pedido["pagamento_com_boleto"]==1 && $pedido["pagamento_com_boleto_desconto_valor"]>0) {
						$boleto_desconto_valor = $pedido["pagamento_com_boleto_desconto_valor"];
						$message_body .= '<tr><td bgcolor="#ffffff" style="padding: 10px 20px 12px;">
							 
							<div style="float: left; font-size: 15px;">Desconto: </div>
							<div style="float: right;">R$ -'.number_format($boleto_desconto_valor, 2, ',', '.').'</div>
							</td></tr>';
					}

				}
				// if ($pedido["cupom_idx"]!=0) {
				// 	$desconto_valor = $pedido["desconto_valor"];
				// 	$message_body .= '<tr><td bgcolor="#ffffff" style="padding: 10px 20px 12px;">Desconto: </td><td>R$ -'.number_format($desconto_valor, 2, ',', '.').'</td></tr>';
				// }elseif ($pedido["pagamento_com_boleto"]==1 && $pedido["pagamento_com_boleto_desconto_valor"]>0) {
				// 	$boleto_desconto_valor = $pedido["pagamento_com_boleto_desconto_valor"];
				// 	$message_body .= '<tr><td bgcolor="#ffffff" style="padding: 10px 20px 12px;">
						 
				// 		<div style="float: left; font-size: 15px;">Desconto: </div>
				// 		<div style="float: right;">R$ -'.number_format($boleto_desconto_valor, 2, ',', '.').'</div>
				// 		</td></tr>';
				// }

			$message_body .= '
									  <tr>
										<td bgcolor="#ffffff" style="border-top: 1px solid #e7e7e7; padding: 10px 20px 20px;" >
											<div class="link" style="float: left; font-size: 23px;" >Total</div>
											<div class="link" style="float: right; font-size: 23px;" >R$ '.number_format((($valor_subtotal_itens-$desconto_valor))-$boleto_desconto_valor, 2, ',', '.').'
											</div>
											</td>
										</tr>
									</tbody>
								</table>

							</td>
						</tr>
						<tr>
							<td bgcolor="#ffffff" style="padding: 15px 20px 40px;" >
								<div style="font-size: 13px;" >
									Esta é uma mensagem automática, não é necessário responder. <br/>
									Em caso de dúvidas, críticas e/ou sugestões, escreva para 
									<a href="mailto:'.Sis::Config("ECOMMERCE_EMAIL_ATENDIMENTO").'" >'.Sis::Config("ECOMMERCE_EMAIL_ATENDIMENTO").'</a>
								</div>
							</td>
						</tr>
					</tbody>
				</table>

			</div></body></html>
			
			';

             // ob_clean();
             // die($message_body);
             // exit();

			// Descomentar envio de e-mail
			$m_ecommerce = new ecommerce_views();
			$m_ecommerce->sendMailECommerce("", "", $usuario['email'], $usuario['nome_informal'], "Resumo do Pedido - Nº " . $pedido['pedido_chave'], $message_body);
	}

	public function pedidoFechadoProdutorMensagem($pedido,$pedido_itens,$usuario,$forma_pagamento)
	{
		$pedido = $pedido[0];
		$usuario = $usuario[0];

		$ecomCadastro = new EcommerceCadastro();
		$cursoProdutor = $ecomCadastro->getProdutorByCursoID($pedido_itens[0]['curso_idx']);

		$valor_subtotal_itens = 0;
			
		$message_body = '
			<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" /><title></title></head><body>
				<div style="margin: 10px auto; width:800px; background:#efeeee; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #333333; text-decoration: none;"  >
					
					<div style="padding:20px 0px; display:flex; align-items:flex-end; justify-content:space-between; background-color:#fff;" >
						<img src="https://'.$_SERVER["HTTP_HOST"].'/'.Sis::config("CLILOGO").'" /> 
						<div><span style="font-size:20px;margin-left: 50px;" >Resumo do Pedido</span> &nbsp; em: '. date("d\/m\/Y \&\a\g\\r\a\\v\\e\;\s H:i:s") .'</div>
					</div>

					<table width="800" border="0" cellpadding="0" cellspacing="0" align="center" style="padding:0px 0px; border-radius: 5px;margin-top: 5px;" >
						<tbody>
							<tr>
								<td bgcolor="#ffffff" style="padding: 20px; border-radius: 3px; border-top: 1px solid #eee; font-size: 16px;">
									<p>Olá, <b>'.$cursoProdutor[0]['nome_completo'].'</b>!</p>
									<p> Uma nova venda foi registrada na plataforma.<br/>
										Abaixo seguem mais detalhes sobre a compra<br>
										Verifique o andamento da compra e inscrição pela plataforma: 
										<a href="https://'.$_SERVER['HTTP_HOST'].'" >'.$_SERVER['HTTP_HOST'].'</a></p>
							</td>
							</tr>
							<tr>
								<td bgcolor="#efeeee" style="padding: 10px 20px; border-radius: 3px 3px 0px 0px;">
									<b style="font-size: 17px;">Dados do Aluno</b>
								</td>
							</tr>
							<tr>
								<td bgcolor="#ffffff" style="padding: 10px 20px; border-radius: 0px 0px 3px 3px;">
									Nome: <span>'.$usuario['nome_completo'].'</span><br>
									E-mail: <span>'.$usuario['email'].'</span><br>
									Telefone: <span>'.$usuario['telefone_resid'].'</span><br>
								</td>
							</tr>
							<tr>
								<td bgcolor="#efeeee" style="padding: 10px 20px; border-radius: 3px 3px 0px 0px;">
									<b style="font-size: 17px;">Meio de Pagamento Escolhido</b>
								</td>
							</tr>
							<tr>
								<td bgcolor="#ffffff" style="padding: 10px 20px; border-radius: 0px 0px 3px 3px;">
									'.$pedido["forma_pagamento_nome"].'
								</td>
							</tr>
							
							<tr>
								<td bgcolor="#efeeee" style="padding: 10px 20px; border-radius: 3px 3px 0px 0px;" >
									<b style="font-size: 17px;">Curso</b>
								</td>
							</tr>
							<tr>
								<td bgcolor="#ffffff" style="padding: 10px 20px; border-radius: 0px 0px 3px 3px;">
									<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="padding: 20px 10px 25px;" >
										<tbody>
											<tr>
												<td></td>
												<td><b>Item:</b></td>
												<td><b>Valor:</b></td>
												<td style="text-align: right; padding-right: 25px;"><b>Subtotal:</b></td>
											</tr>
				
				';
				foreach ($pedido_itens as $key => $item){

					$subtotal_item = $item["item_valor"];
					$valor_subtotal_itens += $subtotal_item;

					$message_body .= '
											<tr>
												<td>'.	((!is_null($item['pimage']) && trim($item['pimage'])!="Null")?'<img src="https://'.$_SERVER["HTTP_HOST"].'/sitecontent/curso/curso/images/'.$item['pimage'].'" width="100" />':'')	 .'</td>
												<td><strong>'.$item["pnome"].'</strong></td>
												<td width="100" >R$ '.number_format($item["item_valor"], 2, ',', '.').'</td>
												<td align="right" width="100" >R$ '.number_format($subtotal_item, 2, ',', '.').'</td>
											</tr>
									';
				}

				$message_body .= '
										</tbody>
									</table>
								</td>
							</tr>
							<tr>
								<td bgcolor="#efeeee" style="padding: 10px 20px; border-radius: 3px 3px 0px 0px;" >
									<b style="font-size: 17px;">Valores à pagar pelo aluno</b>
								</td>
							</tr>
							<tr>
								<td bgcolor="#ffffff" >
									<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="padding: 20px 10px 25px;" >
										<tbody>
											<tr>
												<td bgcolor="#ffffff" style="padding: 10px 20px 12px;">
													<div style="float: left; font-size: 15px;">Sub total: </div>
													<div style="float: right;">R$ '.number_format($valor_subtotal_itens, 2, ',', '.').'</div>
												</td>
											</tr>
				';

				//Verifica o desconto no pedido.
				$desconto_valor=0;
				$boleto_desconto_valor=0;
				if(isset($pedido["cupom_idx"])){
					if ($pedido["cupom_idx"]!=0) {
						$desconto_valor = $pedido["desconto_valor"];
						$message_body .= '<tr><td bgcolor="#ffffff" style="padding: 10px 20px 12px;">Desconto: </td><td>R$ -'.number_format($desconto_valor, 2, ',', '.').'</td></tr>';
					}
				}
				if(isset($pedido["afiliado_idx"])){
					if ($pedido["afiliado_idx"]!=0&&$pedido["desconto_valor"]!=0) {
						$desconto_valor = $pedido["desconto_valor"];
						$message_body .= '<tr><td bgcolor="#ffffff" style="padding: 10px 20px 12px;">Desconto (afiliado): </td><td>R$ -'.number_format($desconto_valor, 2, ',', '.').'</td></tr>';
					}
				}

				if(isset($pedido["pagamento_com_boleto"]) && isset($pedido["pagamento_com_boleto_desconto_valor"])){
					if ($pedido["pagamento_com_boleto"]==1 && $pedido["pagamento_com_boleto_desconto_valor"]>0) {
						$boleto_desconto_valor = $pedido["pagamento_com_boleto_desconto_valor"];
						$message_body .= '<tr><td bgcolor="#ffffff" style="padding: 10px 20px 12px;">
							 
							<div style="float: left; font-size: 15px;">Desconto: </div>
							<div style="float: right;">R$ -'.number_format($boleto_desconto_valor, 2, ',', '.').'</div>
							</td></tr>';
					}
				}

				// if ($pedido["cupom_idx"]!=0) {
				// 	$desconto_valor = $pedido["desconto_valor"];
				// 	$message_body .= '<tr><td bgcolor="#ffffff" style="padding: 10px 20px 12px;">Desconto: </td><td>R$ -'.number_format($desconto_valor, 2, ',', '.').'</td></tr>';
				// }elseif ($pedido["pagamento_com_boleto"]==1 && $pedido["pagamento_com_boleto_desconto_valor"]>0) {
				// 	$boleto_desconto_valor = $pedido["pagamento_com_boleto_desconto_valor"];
				// 	$message_body .= '<tr><td bgcolor="#ffffff" style="padding: 10px 20px 12px;">
						 
				// 		<div style="float: left; font-size: 15px;">Desconto: </div>
				// 		<div style="float: right;">R$ -'.number_format($boleto_desconto_valor, 2, ',', '.').'</div>
				// 		</td></tr>';
				// }

			$message_body .= '
									  <tr>
										<td bgcolor="#ffffff" style="border-top: 1px solid #e7e7e7; padding: 10px 20px 20px;" >
											<div class="link" style="float: left; font-size: 23px;" >Total</div>
											<div class="link" style="float: right; font-size: 23px;" >R$ '.number_format((($valor_subtotal_itens-$desconto_valor))-$boleto_desconto_valor, 2, ',', '.').'
											</div>
											</td>
										</tr>
									</tbody>
								</table>

							</td>
						</tr>
						<tr>
							<td bgcolor="#ffffff" style="padding: 15px 20px 40px;" >
								<div style="font-size: 13px;" >
									Esta é uma mensagem automática, não é necessário responder. <br/>
									Em caso de dúvidas, críticas e/ou sugestões, escreva para 
									<a href="mailto:'.Sis::Config("ECOMMERCE_EMAIL_ATENDIMENTO").'" >'.Sis::Config("ECOMMERCE_EMAIL_ATENDIMENTO").'</a>
								</div>
							</td>
						</tr>
					</tbody>
				</table>

			</div></body></html>
			';

        // Descomentar envio de e-mail
		$m_ecommerce = new ecommerce_views();
		$m_ecommerce->sendMailECommerce("", "", $cursoProdutor[0]['email'], $cursoProdutor[0]['nome_informal'], "Nova venda pela plataforma", $message_body);
	}

	public function getPedidoFinalizado($pedido_codigo)
	{
		$pedido_codigo = (is_numeric(trim($pedido_codigo)))?round($pedido_codigo):0;
		return parent::select("SELECT tbPedido.pedido_idx FROM ". $this->TB_PEDIDO." as tbPedido Where tbPedido.status=0 And tbPedido.pedido_idx=".$pedido_codigo." And cadastro_idx=".$_SESSION['plataforma_usuario']['id']." ");
	}

	/**
	* Método que retona o conteúdo do pedido para a área de "meus pedidos".
	*/
	public function getPedidoDetalhes($pedido_idx)
	{
		$pedido = self::getPedido($pedido_idx);

		$retornoM = "";
		if(is_array($pedido) && count($pedido) > 0)
		{
			
			$pedido = $pedido[0];
			
			$ecomPagamento = new EcommercePagamento();
			$ecomPagamento->setPagamentoId($pedido['pagamento_idx']);
			$ecomPagamento->registrarGateway('production');
			$ecomPagamento->setPedidoId($pedido['pedido_idx']);
			$ecomPagamento->loadTransaction();
			
			$pedido_itens = self::getPedidoItens($pedido_idx);

			 $infoOfPayment = "<strong>".$pedido["forma_pagamento_nome"]."</strong><br />";
             $infoOfPayment .= $ecomPagamento->gateway->getPaymentInfoResume();

             $codigoRastreamento = "";
             $pedidoObservacoes = (trim($pedido['observacoes'])!="") ? nl2br($pedido['observacoes']) : 'Sem observações';
             // if (trim($pedido['frete_codigo_rastreamento'])!="" && !is_null($pedido['frete_codigo_rastreamento'])) {
             // 	$codigoRastreamento = '<h4 class="text-success" ><small>O c&oacute;digo de rastreamento &eacute;:</small> &nbsp; '.$pedido['frete_codigo_rastreamento'].'</h4><a href="'.Sis::config("ECOMMERCE_CORREIOS_LINK_RASTREAMENTO").$pedido['frete_codigo_rastreamento'].'" target="_blank" >Consulte no site dos Correios.</a>';
             // }

			
			$valor_subtotal_itens = 0;
			$retornoM = '
				<div class="card ecom-pedido-detalhes rounded-10" >
					<div class="card-body">
						<div class="row">
							<div class="col-md-6" >
								<h4>Meio de Pagamento Escolhido</h4>
								<div>
									'.$infoOfPayment.'
								</div>
							</div>
							
							<div class="clear"></div>
						</div>
						<hr>
						<h4 class="cor-cinza">Itens do seu pedido</h4>
						<div class="table-responsive">
						<table class="table w100" >
							<tr><th>#</th><th>Item:</th><th >Valor:</th></tr>
			';
				foreach ($pedido_itens as $key => $item){

					$valor_subtotal_itens += $item["item_valor"];
					
					$retornoM .= '
									<tr>
										<td width="150" >'.	((!is_null($item['pimage']) && trim($item['pimage'])!="Null")?'<img style="width:120px;max-width:100%;" src="http://'.$_SERVER["HTTP_HOST"].'/sitecontent/curso/curso/images/'.$item['pimage'].'" />':'')	 .'</td>
										<td ><strong>'.$item["pnome"].'</strong></td>
										<td nowrap width="100" >R$ '.number_format($item["item_valor"], 2, ',', '.').'</td>
									</tr>
									';

				}

			$retornoM .= '
						</table>
						</div>
						<div class="table-responsive">
						<table class="table pull-right fs-16" >
							<tr><td colspan="2" ><h4 class="compra__titulo-detalhes">Valores a Pagar</h4></td></tr>
							<tr><td width="200" class="fs-18 roboto-bold cor-cinza" >Subtotal:</td><td width="100" align="right" class="fs-18 roboto-bold cor-cinza-claro">R$ '.number_format($valor_subtotal_itens, 2, ',', '.').'</td></tr>';
			//Verifica o desconto no pedido.
			$desconto_valor=0;
			if ($pedido["cupom_idx"]!=0) {
				$desconto_valor = $pedido["desconto_valor"];
				$retornoM .= '<tr><td width="100" class="fs-18 roboto-bold cor-cinza" >Desconto:</td><td width="100" align="right" class="fs-18 roboto-bold cor-cinza-claro">R$ -'.number_format($desconto_valor, 2, ',', '.').'</td></tr>';
			}

			if ($pedido["afiliado_idx"]!=0&&$pedido["desconto_valor"]>0) {
				$desconto_valor = $pedido["desconto_valor"];
				$retornoM .= '<tr><td width="100" class="fs-18 roboto-bold cor-cinza" >Desconto (afiliado):</td><td width="100" align="right" class="fs-18 roboto-bold cor-cinza-claro">R$ -'.number_format($desconto_valor, 2, ',', '.').'</td></tr>';
			}

			//Verifica o desconto no pedido.
			$boleto_desconto_valor=0;
			if ($pedido["pagamento_com_boleto"]==1 && $pedido["pagamento_com_boleto_desconto_valor"]>0) {
				$boleto_desconto_valor = $pedido["pagamento_com_boleto_desconto_valor"];
				$retornoM .= '<tr><td width="100" class="fs-18 roboto-bold cor-cinza" >Desconto: <small>(pagamento com boleto)</small></td><td width="100" align="right" class="fs-18 roboto-bold cor-cinza-claro">R$ -'.number_format($boleto_desconto_valor, 2, ',', '.').'</td></tr>';
			}					

			$retornoM .= '
							<tr><td><strong><h4 class="compra__total-pedido" >Total:</h4></td><td align="right" ><h2 class="compra__total-pedido" >R$ '.number_format((($valor_subtotal_itens-$desconto_valor)-$boleto_desconto_valor), 2, ',', '.').'</h2></strong></td></tr>
						</table>
						</div>
					</div>
				</div>
			';


				return $retornoM;
		}else{
			return "erro";
		}
	}

	public function getPedidoFinalizadoAndUpdate($trns_code)
	{
		$trns_code = Text::clean($trns_code);
		$informacoes = SearchTransactionByCode::main($trns_code);
		$pedido_details = parent::update("UPDATE " .  $this->TB_PEDIDO_PGSEGURO . " SET
		                                 status='".$informacoes['status']->getValue()."',
		                                 type='".$informacoes['type']->getValue()."',
		                                 transaction='".$trns_code."',
		                                 type='".$informacoes['type']->getValue()."',
		                                 pm_type='".$informacoes['pm_type']->getValue()."',
		                                 pm_code='".$informacoes['pm_code']->getValue()."',
		                                 grossAmount='".$informacoes['grossAmount']."',
		                                 discountAmount='".$informacoes['discountAmount']."',
		                                 feeAmount='".$informacoes['feeAmount']."',
		                                 netAmount='".$informacoes['netAmount']."',
		                                 extraAmount='".$informacoes['extraAmount']."',
		                                 installmentCount='".$informacoes['installmentCount']."',
		                                 data_processo='".$informacoes['data_processo']."'
		                                 WHERE reference='".$informacoes['reference']."'");
		return $informacoes['reference'];
	}

	/**
	 * Seleciona os pedidos do usuário em sessão.
	 * De acordo com os parâmetros passados na Url, ele traz pedidos de status diferentes.
	 * @param Integer $status => Status do pedidos. Ex.: Enviado, Cancelado.
	 * @return mixed => Se a consulta for realizada com sucesso, retorna um Array, caso contrário Bool(FALSE)
	 */
	public function getPedidos($registrosPorPagina,$paginaAtual)
	{
		$queryStr = "SELECT tbPedido.*,tbFormaPg.nome as forma_pagamento_nome, tbPedidoStatus.nome as status_nome,
						(SELECT count(tbPedidoItensA.item_idx) as totalItens FROM " . $this->TB_PEDIDO_ITENS. " as tbPedidoItensA Where tbPedidoItensA.pedido_idx=tbPedido.pedido_idx ) as itens_total_quantidade,
						(SELECT SUM(tbPedidoItensB.item_valor) as totalItensValor FROM " . $this->TB_PEDIDO_ITENS. " as tbPedidoItensB Where tbPedidoItensB.pedido_idx=tbPedido.pedido_idx ) as itens_total_valor
						FROM ". $this->TB_PEDIDO." as tbPedido
										LEFT JOIN ". $this->TB_PEDIDO_STATUS ." as tbPedidoStatus On tbPedidoStatus.status_idx=tbPedido.status
										LEFT JOIN ". $this->TB_PAGAMENTO ." as tbFormaPg ON tbFormaPg.pagamento_idx = tbPedido.pagamento_idx
										Where tbPedido.cadastro_idx=".$_SESSION['plataforma_usuario']['id']." Order By data_cadastro DESC";

		return parent::selectPage($queryStr,$registrosPorPagina,$paginaAtual);
	}

	/**
	 * Seleciona os dados de um determinado pedido para a lista.
	 * @param Integer $pedido_idx => Identificador do pedido.
	 * @return mixed => Se a consulta for realizada com sucesso, retorna um Array, caso contrário Bool(FALSE)
	 */
	public function getPedido($pedido_idx)
	{
		return parent::select("SELECT tbPedido.*,
		                         tbPedidoEnd.endereco,
		                         tbPedidoEnd.numero,
		                         tbPedidoEnd.bairro,
		                         tbPedidoEnd.cep,
		                         tbPedidoEnd.cidade,
		                         tbPedidoEnd.estado,
		                         tbPedidoEnd.complemento,
		                         tbFormaPg.nome as forma_pagamento_nome FROM ". $this->TB_PEDIDO." as tbPedido
										INNER JOIN ". $this->TB_PEDIDO_ENDERECO." as tbPedidoEnd ON tbPedidoEnd.pedido_idx = tbPedido.pedido_idx
										LEFT JOIN  ". $this->TB_PAGAMENTO." as tbFormaPg ON tbFormaPg.pagamento_idx = tbPedido.pagamento_idx
										Where tbPedido.pedido_idx=".$pedido_idx." And tbPedido.cadastro_idx=".$_SESSION['plataforma_usuario']['id']."
	                         ");
	}

	/**
	 * Seleciona os dados de um determinado pedido para a lista.
	 * @param Integer $pedido_idx => Identificador do pedido.
	 * @return mixed => Se a consulta for realizada com sucesso, retorna um Array, caso contrário Bool(FALSE)
	 */
	public function getPedidoSingle($pedido_idx)
	{
		return parent::select("SELECT tbPedido.*,
		                         tbPedidoEnd.endereco,
		                         tbPedidoEnd.numero,
		                         tbPedidoEnd.bairro,
		                         tbPedidoEnd.cep,
		                         tbPedidoEnd.cidade,
		                         tbPedidoEnd.estado,
		                         tbPedidoEnd.complemento,
		                         tbFormaPg.nome as forma_pagamento_nome FROM ". $this->TB_PEDIDO." as tbPedido
										INNER JOIN ". $this->TB_PEDIDO_ENDERECO." as tbPedidoEnd ON tbPedidoEnd.pedido_idx = tbPedido.pedido_idx
										LEFT JOIN  ". $this->TB_PAGAMENTO." as tbFormaPg ON tbFormaPg.pagamento_idx = tbPedido.pagamento_idx
										Where tbPedido.pedido_idx=".$pedido_idx."
	                         ");
	}

	/**
	 * Seleciona os itens de um determinado pedido para a lista.
	 * @param Integer $pedido_idx => Identificador do pedido.
	 * @return mixed => Se a consulta for realizada com sucesso, retorna um Array, caso contrário Bool(FALSE)
	 */
	public function getPedidoItens($pedido_idx)
	{
		$sqlQuery = "SELECT tbPedidoItens.*,tbCur.nome as pnome,tbCur.imagem as pimage
					FROM ". $this->TB_PEDIDO_ITENS." as tbPedidoItens
					INNER JOIN ". $this->TB_CURSO." as tbCur ON tbCur.curso_idx = tbPedidoItens.curso_idx
					Where tbPedidoItens.pedido_idx=".$pedido_idx."
					";

		/* MANTIDO TEMPORARIAMENTE, PODE SER REMOVIDO DEPOIS QUE O SITE FOR HOMOLOGADO.

		$sqlQuery = "SELECT DISTINCT tbPedidoItens.*,tbCur.nome as pnome, tbCurVar.variacao_idx, tbCurVar.nome as pvnome,
                          	(Select imagem from ".$this->TB_CURSO_ARTPRINT_MOCKUP." Where artprint_idx=tbPedidoItens.artprint_idx And curso_idx=tbCur.curso_idx LIMIT 0,1) as pimage, ArtPrint.nome as artprint_nome, ArtPrint.cadastro_idx as artista_idx, Artista.nome_informal as artista_nome
									FROM ". $this->TB_PEDIDO_ITENS." as tbPedidoItens
									INNER JOIN ". $this->TB_CURSO." as tbCur ON tbCur.curso_idx = tbPedidoItens.curso_idx
									INNER JOIN ".$this->TB_CURSO_ARTPRINT." as ProdArtPrint ON ProdArtPrint.curso_idx = tbCur.curso_idx
									INNER JOIN ".$this->TB_ARTPRINT." as ArtPrint ON ArtPrint.artprint_idx = ProdArtPrint.artprint_idx
									INNER JOIN ".$this->TB_CADASTRO." as Artista ON Artista.cadastro_idx = ArtPrint.cadastro_idx
									LEFT JOIN  ". $this->TB_CURSO_VAR." as tbCurVar ON tbCurVar.variacao_idx = tbPedidoItens.curso_item_idx
									Where tbPedidoItens.pedido_idx=".$pedido_idx."";
									*/

		return parent::select($sqlQuery);
	}

	/**
	 * Seleciona os status dos pedidos.
	 * @return mixed => Se a consulta for realizada com sucesso, retorna um Array, caso contrário Bool(FALSE)
	 */
	public function getPedidoStatus($id=0){
         $WhereStatus = ($id!=0)?" Where status_idx=".((int)$id):"";
		return parent::select("SELECT * FROM ".  $this->TB_PEDIDO_STATUS." ".$WhereStatus." Order By status_idx ASC");
	}

	


}