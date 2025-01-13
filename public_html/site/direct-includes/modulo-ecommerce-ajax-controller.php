<?php
include_once('config.php');

// Run CSRF check, on POST data, in exception mode, with a validity of 10 minutes, in one-time mode.
// try{
// 	NoCSRF::check('csrf_token',$_POST,true,60*10,false);
// }catch( Exception $e ){
// 	die("Não foi possível completar sua solicitação, certifique-se de que a página carregou corretamente.");
// }

$ac = isset($_GET['ac']) ? Text::clean($_GET['ac']) : '' ;
$ac = isset($_POST['ac']) ? Text::clean($_POST['ac']) : $ac ;

$pid = isset($_POST['pid']) ? Text::clean($_POST['pid']) : 0;
$vid = isset($_POST['vid']) ? Text::clean($_POST['vid']) : 0;

// $dadovalor = isset($_POST['dadovalor']) ? Text::clean($_POST['dadovalor']) : "";
// $produto_idx = isset($_POST['produto_idx']) ? Text::clean($_POST['produto_idx']) : 0;
// $produtos_idx = isset($_POST['produtos']) ? Text::clean($_POST['produtos']) : 0;

$ecomCadastro = new EcommerceCadastro();

switch ($ac) {

	case 'ecomCheckout':

		$ecomPagamento = new EcommercePagamento();
		$request = array(
			'cadastro_idx' => 0,
			'iugu_id' => 0,
			'iugu_payment_profile'=>'',
			'nome_completo' => isset($_POST['nome_completo']) ? Text::clean($_POST['nome_completo']) : "" ,
			'cpf_cnpj' => isset($_POST['cpf_cnpj']) ? Text::getOnlyNumber($_POST['cpf_cnpj']) : "" ,
			'telefone' => isset($_POST['telefone']) ? Text::clean($_POST['telefone']) : "" ,
			'forma_pagamento' => isset($_POST['forma_pagamento']) ? Text::clean($_POST['forma_pagamento']) : 0 ,
			'email' => isset($_POST['email']) ? Text::clean($_POST['email']) : "" ,
			'receber_boletim' => isset($_POST['receber_boletim']) ? (int)($_POST['receber_boletim']) : 0,
			'installmentQuantity' => isset($_POST['installmentQuantity']) ? Text::clean($_POST['installmentQuantity']) : 1,
			'paymentMethod' => isset($_POST['paymentMethod']) ? Text::clean($_POST['paymentMethod']) : "",
            'cd_hash' => isset($_POST['creditCardHash']) ? Text::clean($_POST['creditCardHash']) : "",
            'cd_brand' => isset($_POST['creditCardBrand']) ? Text::clean($_POST['creditCardBrand']) : "",
            'cd_number' => isset($_POST['creditCardNumber']) ? Text::clean($_POST['creditCardNumber']) : "",
            'cd_data' => isset($_POST['creditCardDtValid']) ? Text::clean($_POST['creditCardDtValid']) : "",
            'cd_holder_name' => isset($_POST['creditCardHolderName']) ? Text::clean($_POST['creditCardHolderName']) : ""
		);
		// 'data_nascimento' => isset($_POST['data_nascimento']) ? Text::clean($_POST['data_nascimento']) : "" ,
		// 'nome_informal' => isset($_POST['nome_informal']) ? Text::clean($_POST['nome_informal']) : "" ,
		// 'sexo' => isset($_POST['sexo']) ? (int)($_POST['sexo']) : 0 ,
		// 'frete' => isset($_POST['frete']) ? (int)($_POST['frete']) : 0 ,
		// 'observacoes' => isset($_POST['observacoes']) ? Text::clean($_POST['observacoes']) : "" ,
		// 'senha' => isset($_POST['senha']) ? Text::clean($_POST['senha']) : "",

		if (isset($_POST['cep'])) {
			if (trim($_POST['cep'])!='') {
				$request['cep'] = isset($_POST['cep']) ? Text::clean($_POST['cep']) : "" ;
				$request['estado'] = isset($_POST['estado']) ? Text::clean($_POST['estado']) : "" ;
				$request['cidade'] = isset($_POST['cidade']) ? Text::clean($_POST['cidade']) : "" ;
				$request['endereco'] = isset($_POST['endereco']) ? Text::clean($_POST['endereco']) : "" ;
				$request['numero'] = isset($_POST['numero']) ? Text::clean($_POST['numero']) : "" ;
				$request['complemento'] = isset($_POST['complemento']) ? Text::clean($_POST['complemento']) : "" ;
				$request['bairro'] = isset($_POST['bairro']) ? Text::clean($_POST['bairro']) : "" ;
			}
		}

		// print_r($_POST);
		// print_r($request);
		// exit();

		//Recupera os dados do produtor para seguir com a compra.
			$ecomCarrinho = new EcommerceCarrinho();
			$carrinho_itens = $ecomCarrinho->carListItens();
			$produtorID = (is_array($carrinho_itens)&&count($carrinho_itens)>0)?$carrinho_itens[0]['produtor_idx']:0;
			$produtorReg = $m_cadastro->getCadastro($produtorID);
			$request['produtor'] = $produtorReg[0];

		$newUser = !isset($_SESSION['plataforma_usuario']);

		$toProcess = true;

		$dados_retorno = json_decode('{"error":"0","message":"","pedido_codigo":"0","pagamento":""}');

		//valida o cpf
		$validaCPFCNPJ =  (strlen($request['cpf_cnpj'])>11) ? Sis::validaCNPJ($request['cpf_cnpj']) : Sis::validaCPF($request['cpf_cnpj']) ;
		if (!$validaCPFCNPJ){
			$dados_retorno->error = 1;
			$dados_retorno->message = "- O CPF/CNPJ informado é inválido </br>";
		}

		//valida a data de nascimento
		// if (trim($request['data_nascimento'])!="") {
		// 	if (!Date::isDate($request['data_nascimento'])){
		// 		$dados_retorno->error = 1;
		// 		$dados_retorno->message .= "- A data de nascimento informada é inválida </br>";
		// 	}
		// }

		if ($newUser) {
			
			//valida a senha
			// if (trim($request['senha'])=="") {
			// 	$dados_retorno->error = 1;
			// 	$dados_retorno->message .= "- Informe uma senha.</br>";
			// }elseif (strlen($request['senha'])<6) { 
			// 	$dados_retorno->error = 1;
			// 	$dados_retorno->message .= "- Informe uma senha com no mínimo 6 caracteres.</br>";
			// }

			//valida o email
			if (!Sis::isValidEmail($request['email'])) {
				$dados_retorno->error = 1;
				$dados_retorno->message .= "- Informe um email válido.</br>";
			}
		}else{
			$request['cadastro_idx'] = $_SESSION['plataforma_usuario']['id'];
			$request['email'] = $_SESSION['plataforma_usuario']['email'];
		}

		//valida o pagamento
		if (!$ecomPagamento->isValidMethod($request['forma_pagamento'])){
			$dados_retorno->error = 1;
			$dados_retorno->message .= "- M&eacute;todo de pagamento inexistente.</br>";
		}

		if ($dados_retorno->error==0) {

			//Atualiza os dados do cadastro.
			try {
				$m_cadastro->updateFromCheckout($request);//$nome_completo,$nome_informal,$data_nascimento,$sexo,$cpf,$telefone,$cep,$estado,$cidade,$endereco,$numero,$complemento,$bairro
				$request['cadastro_idx'] = $_SESSION['plataforma_usuario_buyer']['id'];
				$request['email'] = $_SESSION['plataforma_usuario_buyer']['email'];
			} catch (Exception $e) {
				$dados_retorno->error = 1;
				$dados_retorno->message .= $e->getMessage();
			}

			
			// print_r($_SESSION['plataforma_usuario_buyer']);
			// exit();


			//Processa e registra o pedido
			if ($dados_retorno->error==0) {
				try {
					
					$ecomPedido = new EcommercePedido();

	                // $ecomPagamento = new EcommercePagamento();
	                $ecomPagamento->setPagamentoId((int)$request['forma_pagamento']);
	                $ecomPagamento->registrarGateway('production');
	                //define as chaves de API do produtor do curso
	                $ecomPagamento->gateway->setApiKey($request['produtor']['iugu_split_live_api_token'],$request['produtor']['iugu_split_test_api_token']);

	                $iuguID = '';//$_SESSION['plataforma_usuario']['iugu_id'];
                    // Processa o registro do cliente no Gateway Iugu.
                    if (is_null($iuguID) || (int)$iuguID<=0) {//Não existe um ID Iugu para o cadastro.
                        try {
                            $iuguCustomer = $ecomPagamento->gateway->createCustomer($request);
                            $m_cadastro->setIuguID($_SESSION['plataforma_usuario_buyer']['id'],$iuguCustomer->id);
                            $iuguID = $iuguCustomer->id;
                            $_SESSION['plataforma_usuario_buyer']['iugu_id'] = $iuguCustomer->id;
                        } catch (Exception $e){
                            $dados_retorno->error=1;
                            $dados_retorno->message='Não foi possível registrar, verifique se os dados estão corretos e tente novamente, caso o problema persista entre em contato e informe os detalhes a seguir: <code>'.$e->getMessage().'<code>';
                        }
                    }
                    $request['iugu_id'] = $iuguID;


                    if ($dados_retorno->error==0) {
                    
	                    //Processa o cadastro/retorno do Perfil de Pagamento para o cartão.
	                    //Usado apenas em caso de assinatura.
	                    // try {
	                    //     $iuguPaymentProfile = $ecomPagamento->gateway->createPaymentProfile($request);
	                    //     $request['iugu_payment_profile'] = $iuguPaymentProfile;
	                    //     // $ecomPedido->cadastroCheckAndSaveCard($request); //Salva os dados do cartão... não usado nessa plataforma.
	                    // } catch (Exception $e){
	                    // 	die($e->getMessage());
	                    //     $dados_retorno->error=1;
	                    //     $dados_retorno->message='Não foi possível registrar, verifique se os dados de pagamento estão corretos e tente novamente, caso o problema persista entre em contato e informe os detalhes a seguir: <code>'.$e->getMessage().'<code>';
	                    // }

	                    $pedidoRetorno = $ecomPedido->pedidoProcessaFromCheckout($request);

						switch ( $request['forma_pagamento'] ) {
							case 1: //Iugu
								//Trata o retorno do processo de pagamento
								$dados_retorno->pagamento = $pedidoRetorno->pagamento_retorno->getTransactionData();

								if (
	                                (int)$dados_retorno->pagamento->code>0
	                            ){
	                                $dados_retorno->error = 1;
	                                $dados_retorno->message = "- Retorno da financeira: ".Text::to_utf8($dados_retorno->pagamento->message)." </br>";
	                            }else{
	                                if ($dados_retorno->pagamento->code=='paid') {
	                                    $paymentInfoComplement = '';
	                                }else{
										$dados_retorno->url_pagamento = method_exists($pedidoRetorno->pagamento_retorno,'getPaymentLink')?$pedidoRetorno->pagamento_retorno->getPaymentLink():"";
										$dados_retorno->tid = $pedidoRetorno->pagamento_retorno->getTransactionId();
	                                }
	                            }

							break;
						}
						
						$dados_retorno->pedido_codigo = $pedidoRetorno->pedido_codigo;

					}
					
				} catch (Exception $e) {
					$dados_retorno->error = 1;
					$dados_retorno->message .= $e->getMessage();
				}
			}



			// die("sucesso");
			// exit();

			//Processa e registra o pedido
			// if ($dados_retorno->error==0) {
			// 	try {
					
			// 		$ecomPedido = new EcommercePedido();
			// 		$pedidoRetorno = $ecomPedido->pedidoProcessaFromCheckout($request);
					
			// 		if ($request['forma_pagamento']!=0) {//Foi indicada uma forma de pagamento, possivelmente um gateway
			// 			//Trata o retorno do processo de pagamento
			// 			$dados_retorno->pagamento = (method_exists($pedidoRetorno->pagamento_retorno,'getTransactionData')) ? $pedidoRetorno->pagamento_retorno->getTransactionData() : $pedidoRetorno->pagamento_retorno;

			// 			// if($transactionData->status>2){
			// 			// 	$dados_retorno->url_pagamento = method_exists($pedidoRetorno->pagamento_retorno,'getPaymentLink')?$pedidoRetorno->pagamento_retorno->getPaymentLink():"";
			// 			// 	$dados_retorno->tid = $pedidoRetorno->pagamento_retorno->getTransactionCode();
			// 			// }
			// 		}else{
			// 			$dados_retorno->pagamento = $pedidoRetorno->pagamento_retorno;
			// 		}
			// 		$dados_retorno->pedido_codigo = $pedidoRetorno->pedido_codigo;
					
			// 	} catch (Exception $e) {
			// 		$dados_retorno->error = 1;
			// 		$dados_retorno->message .= $e->getMessage();
			// 	}
			// }

			if ($dados_retorno->error==0) {
				$dados_retorno->message = "Pedido registrado com sucesso!";
			}
		}
		ob_clean();
		echo json_encode($dados_retorno);
		exit();

		break;

	

	/**
	 * Verifica se a sessão do usuário está ativa
	 */
	case 'uIsOnline':
		// if($exe == 3){
		if(isset($_SESSION['plataforma_usuario']))
			if(is_array($_SESSION['plataforma_usuario']) && count($_SESSION['plataforma_usuario']) > 0)
				die("ok");
		die("nao");
		break;

	/**
	 * Atualiza o produtos nos favoritos do usuário
	 */
	case 'uFavUpdate':
		// if($exe == 4){
		$ecomCadastro->updateMyFavorites($produtos_idx);
		break;

	/**
	 * Exclui um favorito
	 */
	case 'uFavRemove':
		// if($exe == 5){
		$ecomCadastro->updateMyFavorites($produtos_idx, "delete");
		break;

	/**
	 * Avise-me
	 */
	case 'pSetAviseMe':
		// if($exe == 6){
		$nome 			= isset($_POST['nome']) ? Text::clean($_POST['nome']) : "";
		$email 			= isset($_POST['email']) ? Text::clean($_POST['email']) : "";
		$cadastro_idx 	= isset($_POST['cadastro_idx']) ? (int)Text::clean($_POST['cadastro_idx']) : 0;
		$produto_idx 	= isset($_POST['produto_idx']) ? (int)Text::clean($_POST['produto_idx']) : 0;

		if($cadastro_idx != 0 && isset($_SESSION['plataforma_usuario'])){
			if(is_array($_SESSION['plataforma_usuario']) && count($_SESSION['plataforma_usuario']) > 0){
				$ecomCadastro->aviseMeInsert(array('cadastro_idx' => $_SESSION['plataforma_usuario']['id'], 'produto_idx' => $produto_idx) );
			}else{
				echo "error";
				exit();
			}
		}else{
			$ecomCadastro->aviseMeInsert(array('nome' => $nome, 'email' => $email, 'produto_idx' => $produto_idx));
		}
		break;

	/**
	 * Inserindo avaliação do produto!
	 */
	case 'pCommentsAdd':
		// if($exe == 7){
		$voto 				= isset($_POST['voto']) ? (int)Text::clean($_POST['voto']) : 0;
		$produto_idx 		= isset($_POST['produto_idx']) ? (int)Text::clean($_POST['produto_idx']) : 0;
		$titulo_avaliacao 	= isset($_POST['titulo_avaliacao']) ? Text::clean($_POST['titulo_avaliacao']) : "";
		$conteudo_avaliacao = isset($_POST['conteudo_avaliacao']) ? Text::clean($_POST['conteudo_avaliacao']) : "";

		if($voto != 0 && $produto_idx != 0 && $titulo_avaliacao != "" && $conteudo_avaliacao != "" && isset($_SESSION['plataforma_usuario']) && !empty($_SESSION['plataforma_usuario'])){
			$array = array(
		            'produto_idx' 	=> $produto_idx,
		            'cadastro_idx' => (int)$_SESSION['plataforma_usuario']['id'],
		            'avaliacao' 	=> $voto,
		            'titulo' 		=> $titulo_avaliacao,
		            'comentario' 	=> $conteudo_avaliacao
			);
			$ecomCadastro->ratingInsert($array);
		}else{
			die("erro");
		}
		break;

	
	/**
	 * Carregando detalhes dos meus pedidos
	 */
	case 'pGetPedidoDetalhes':
		// if($exe == 10){
		if((int)$pid == 0){
			die("erro");
		}
		$ecomPedido = new EcommercePedido();
		$pedido = $ecomPedido->getPedidoDetalhes((int)$pid);
		if($pedido != "erro"){
			echo $pedido;
		}else{
			echo "erro";
		}
		exit();
		break;

	/**
	 * Carregando detalhes dos meus pedidos
	 */
	case 'pGetVendaDetalhes':
		if((int)$pid == 0){
			die("erro");
		}
		$ecomPedido = new EcommerceVendas();
		$pedido = $ecomPedido->getVendaDetalhes((int)$pid);
		if($pedido != "erro"){
			echo $pedido;
		}else{
			echo "erro";
		}
		exit();
		break;

	/**
	* Resgata as opções de parcelamento para a forma de pagamento com cartão
	*/
	case 'pGetPaymentInstallments':
		// if ($exe=322) {

		$ecomPagamento = new EcommercePagamento();
		$ecomCarrinho = new EcommerceCarrinho();

		//Verificação para a promoção de frete grátis
	    $frete_gratis = (int)Sis::config("ECOMMERCE-FRETE-DESCONTO-ATIVO");
	    $frete_gratis_valor_minimo = (float)Sis::config("ECOMMERCE-FRETE-DESCONTO-VALOR");
		
		//Frete grátis por região.
		$regiao_frete_gratis = (int)Sis::config("ECOMMERCE-FRETE-DESCONTO-REGIAO-ATIVO");
		$regiao_frete_gratis_regioes = Sis::config("ECOMMERCE-FRETE-DESCONTO-REGIAO-LOCAIS");
	    $regiao_frete_gratis_valor_minimo = (float)Sis::config("ECOMMERCE-FRETE-DESCONTO-REGIAO-VALOR-MINIMO-COMPRA");

		//Informação definida no processo de geração das opções de frete
		$regiao_endereco = isset($_SESSION['ECOM_FRETE_REGIAO_ENDERECO']) ? $_SESSION['ECOM_FRETE_REGIAO_ENDERECO'] : '';

		
		$valor_frete = 0;//Valor do frete obtido da integracao com os correios, (float)Sis::Config("ECOMMERCE-FRETE-VALOR");

		$car_subtotal = $ecomCarrinho->carGetTotal();
		$car_itens = $ecomCarrinho->carListItens();
		$carProdParcelamentoMax = 500000;
		foreach ($car_itens as $key => $produto) {
			if ((int)$produto['parcelamento_max']>0 && (int)$produto['parcelamento_max']<$carProdParcelamentoMax) {
				$carProdParcelamentoMax = (int)$produto['parcelamento_max'];
			}
		}
		if($carProdParcelamentoMax == 500000) {
			$carProdParcelamentoMax = 0;
		}


		if (isset($_SESSION['ECOM_FRETE_SELECIONADO'])) {
			if (is_array($_SESSION['ECOM_FRETE_SELECIONADO'])) {
				$valor_frete=$_SESSION['ECOM_FRETE_SELECIONADO']['retorno'][0]['valor'];
			}
		}
		
        if (trim($regiao_endereco)!="-"&&trim($regiao_endereco)!=""){
        	if($regiao_frete_gratis==1 && $car_subtotal>=$regiao_frete_gratis_valor_minimo && strpos($regiao_frete_gratis_regioes,$regiao_endereco)!==false){
	            $valor_frete=0;
	        }
        }
	    if($frete_gratis==1 && $car_subtotal>=$frete_gratis_valor_minimo){
            $valor_frete=0;
        }

        $desconto_valor = 0;
        if (isset($_SESSION['ecommerce_cupom'])) {
        	if ($_SESSION['ecommerce_cupom']['valor_minimo']>0) {

        		if ($car_subtotal>=$_SESSION['ecommerce_cupom']['valor_minimo']) {
        			if ($_SESSION['ecommerce_cupom']['tipo_desconto']==1){ //Percentual
						$desconto_valor =  $car_subtotal * ($_SESSION['ecommerce_cupom']['valor_desconto']/100);
					} else{
						$desconto_valor = (float)($_SESSION['ecommerce_cupom']['valor_desconto']);
					}
        		}

        	}else{

        		if ($_SESSION['ecommerce_cupom']['tipo_desconto']==1){ //Percentual
					$desconto_valor =  $car_subtotal * ($_SESSION['ecommerce_cupom']['valor_desconto']/100);
				} else{
					$desconto_valor = (float)($_SESSION['ecommerce_cupom']['valor_desconto']);
				}

        	}
        }
        //Aplica desonto pelo afiliado
        if (isset($_SESSION['platform_afiliado'])) {
        	if ((float)$_SESSION['platform_afiliado']['desconto_afiliado']>0){
            	$desconto_valor = $car_subtotal * ($_SESSION['platform_afiliado']['desconto_afiliado']/100);
            }
        }

        $valorTotalPedido = ($car_subtotal-$desconto_valor) + $valor_frete;

		$brand = isset($_POST['cbrand']) ? Text::clean($_POST['cbrand']) : "";
		$retorno="";
		if (trim($brand)!="") {
			//gera a lista de parcelamentos para o meio de pagamento
			$EcommerceGatewayParcelamentosStr = Sis::config("ECOMMERCE-GATEWAY-PARCELAMENTOS-IUGU");
			$EcommerceGatewayParcelamentos = explode(PHP_EOL,$EcommerceGatewayParcelamentosStr);
			
			$cBrand = "";
			$cParcelaSJ = 0;
			$cParcelaCJ = 0;
			$cParcelaValMin = 0;
			$cTaxa = 0;

			foreach ($EcommerceGatewayParcelamentos as $key => $brandParams) {
				$params = explode(",",$brandParams);
				$cBrand = isset($params[0])?$params[0]:'';
				$cParcelaSJ = isset($params[1])?$params[1]:0;
				$cParcelaCJ = isset($params[2])?$params[2]:0;
				$cParcelaValMin = isset($params[3])?$params[3]:0;
				$cTaxa = isset($params[4])?$params[4]:0;
				if ($brand==trim(strtolower($cBrand))) {
					break;
				}
			}

			if ($cBrand!=""&&($cParcelaSJ>0||$cParcelaCJ>0)) {
				$parcelas = ($cParcelaSJ>$cParcelaCJ) ? $cParcelaSJ : $cParcelaCJ;
				for ($i=1; $i <= $parcelas; $i++) { 
					$valor = $valorTotalPedido/$i;
					if ($valor<$cParcelaValMin){
						$parcelas = $i-1;
						break;
					}
				}
				if ($parcelas<1){
					$parcelas=1;
				}

				if ($carProdParcelamentoMax>0) {
					$parcelas=$carProdParcelamentoMax;
				}
				
				for ($i=1; $i <= $parcelas; $i++) {
					if ($i<=$cParcelaSJ) { //Sem Juros
						$valorParcela = $valorTotalPedido/$i;
						$retorno .= '<option value="'.$i.'" >'.$i.'x de R$ '. number_format($valorParcela,2,',','.') .' sem juros</option>';
					}else{//Com Juros
						$valorParcela = $ecomPagamento->calculaParcelaComJuros($valorTotalPedido,$i,$cTaxa);
						$retorno .= '<option value="'.$i.'" >'.$i.'x de R$ '. number_format($valorParcela,2,',','.') .' com juros* (R$ '. number_format(($valorParcela*$i),2,',','.') .')'.'</option>';
					}
				}

			}
		}
		ob_clean();
		echo $retorno;
		exit();

		break;


}