<?php

	if(!defined("DC"))
		define("DC",DIRECTORY_SEPARATOR);
	$rootBase = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
	require_once $rootBase.DC."admin".DC."library".DC."vendor".DC."iugu-php".DC."vendor".DC."autoload.php";
	
	class EcommerceIUGUPayment
	{

		/*!IMPORTANTE
		* TID para a ser identificado usando o ID da Fatura na IUGU.
		* Relacionado a Fatura(Bill) teremos as cobranças(Charge) relacionadas.
		* Nos retornos pelo webhook as cobranças tem a identificação da sua fatura.
		* https://github.com/iugu/iugu-php
		* https://dev.iugu.com/docs
		*/

		private $transaction; //Objeto com todas as informações da transação.
		private $transactionError; //Registra o erro caso o mesmo exista;
		private $environment; // Configure o ambiente
		
		private $api_master_account_id;
		private $api_key;
		private $api_key_production;
		private $api_key_sandbox;
		
		private $lojistaDados; //Dados do Lojista
		private $pagamento;
		private $basicData;

		public $metodosDePagamento;

		function __construct($_enviroment='sandbox')
		{
			$this->environment = $_enviroment;

			//Identificador da conta Mestre na IUGU - Instituto Conexo
			$this->api_master_account_id = 'F7E3A7E9DC1443E28F999F0FF0063292';

			//Dados de acesso a API IUGU - Instituto Conexo
			$this->api_key_sandbox = 'A3026A2C572CB573F39119271893E84DD6FD75060C827567369AE6D134D9BDEC';
			$this->api_key_production = '2C7B1714EFCC958C40DD6F2B1C2D2234F81EBE208B8AC6EEBC9035650F4869D8';
			
			$this->api_key = (trim($this->environment)=='production')?$this->api_key_production:$this->api_key_sandbox;

			$this->lojistaDados = array(
				'nome'=>'Conexo',
				'endereco'=> '',
				'numero'=> '',
				'bairro'=> '',
				'cep'=> '',
				'cidade'=> 'Fortaleza',
				'estado'=> 'CE',
				'complemento'=> '',
				'cnpj'=> '00.000.000/0000-00'
			);

			$this->basicData = array(//validar o uso desse objeto
				'CardBrand'=>'',
				'BoletoUrl'=>'',
				'PaymentMethod'=>'credit_card'
			);

			$this->transactionError = json_decode('{"code":"0","message":""}');
		}


		/**
		* @todo CONFIRMAR USO DO OBJETO PAGAMENTO NESSE CONTEXTO
		*/
		public function setApiKey($api_key_production,$api_key_sandbox){
			$this->api_key = (trim($this->environment)=='production')?$api_key_production:$api_key_sandbox;
		}

		/**
		* @todo CONFIRMAR USO DO OBJETO PAGAMENTO NESSE CONTEXTO
		*/
		public function setCredentialsFromAccount($dataAccount){
			$account_id = $this->pagamento->getAccountId();
			if ($account_id != $this->api_master_account_id){
				//A transacao não foi gerada pela conta principal e as chaves de acesso são redefinicas para a subconta.
				$api_key_production = $dataAccount[0]['iugu_split_live_api_token'];
				$api_key_sandbox = $dataAccount[0]['iugu_split_test_api_token'];
				self::setApiKey($api_key_production,$api_key_sandbox);
			}
		}

		public function getAccountData($accountId)
		{
			try {
				Iugu::setApiKey($this->api_key);
				$account = Iugu_Account::fetch($accountId);
				return $account;
			}catch (Exception $e) {
				throw $e;
			}
		}
		
		/**
		* @todo CONFIRMAR USO DO OBJETO PAGAMENTO NESSE CONTEXTO
		*/
		public function setPagamento($pagamento){
			$this->pagamento = $pagamento;
		}

		/**
		 * Cria um cliente na IUGU.
		 * @param (Array) $request -> Dados da requisição
		 * @return (Self Object)
		 */
		public function createCustomer($params)
		{

			//Antes de criar verifica a existencia do mesmo, se ele existir é retornado o registro e criada uma associacao do ID no sistema com a IUGU.
			try {
				$customer = self::findCustomer($params);
			} catch (Exception $e) {
				throw $e;
			}
			// echo "createCustomer:";
			// var_dump($customer);
			// exit();

			$hasCustomer = false;
			if (is_object($customer)) {
				if ($customer->total()>0) {
					$hasCustomer = true;
					$results = $customer->results();
					return $results[0];
				}
			}

			if (!$hasCustomer){

				$telefoneFixoPrefix = '';
				$telefoneFixoNumero = '';
				if (trim($params['telefone'])!=""&&strlen(trim($params['telefone']))>6) {
					$telefoneArr = explode(")",$params['telefone']);
					$telefoneFixoPrefix  = Text::getOnlyNumber($telefoneArr[0]);
					$telefoneFixoNumero  = Text::getOnlyNumber($telefoneArr[1]);
				}

				// 'zip_code' => $params['cep'],
				// 'number' => $params['numero'],
				// 'street' => $params['endereco'],
				// 'city' => $params['cidade'],
				// 'state' => trim(strtoupper($params['estado'])),
				// 'district' => $params['bairro'],
				// 'complement' => $params['complemento'],
				$customerData = Array(
					'email' => $params['email'],
					'name' => $params['nome_completo'],
					'phone' => $telefoneFixoNumero,
					'phone_prefix' => $telefoneFixoPrefix,
					'cpf_cnpj' => trim($params['cpf_cnpj']),
					'custom_variables' => Array(
						Array(
							'name'=>'plataforma_id',
							'value'=>$params['cadastro_idx']
						)
					)
				);
				//Enviado apenas caso pagamento com Boleto
				if (isset($params['cep'])) {
					$customerData['zip_code'] = $params['cep'];
					$customerData['number'] = $params['numero'];
					$customerData['street'] = $params['endereco'];
					$customerData['city'] = $params['cidade'];
					$customerData['state'] = trim(strtoupper($params['estado']));
					$customerData['district'] = $params['bairro'];
					$customerData['complement'] = $params['complemento'];
				}

				// var_dump($this->api_key);
				// exit();

				try {
					
					Iugu::setApiKey($this->api_key);
					$customer = Iugu_Customer::create($customerData);
					return $customer;

				} catch (Exception $e) {
					throw $e;
				}

			}
		}

		public function findCustomer($params)
		{
			$paramKey = trim(Text::getOnlyNumber($params['cpf_cnpj']));
			$queryData = [
				'query' => $paramKey
			];

			try {
				Iugu::setApiKey($this->api_key);
				$customer = Iugu_Customer::search($queryData);
				return $customer;
			}catch (Exception $e) {
				throw $e;
			}
		}

		/**
		 * @todo obsoleto, remover no final da implementacao
		*/
		public function updateCustomerSysID($params)
		{
			// $customerData = [
			// 	'code' => $params['cadastro_idx']
			// ];
			
			// try {
				
			// 	$customerService = new IUGU\Customer($this->envAuth);
			// 	$customerService->update($params['iugu_id'],$customerData);

			// } catch (IUGU\Exceptions\ValidationException $e) {
				
			// 	$mensagem="";
			// 	foreach ($e->getErrors() as $key => $error) {
			// 		$mensagem .= ' - ['.$error->id.'] '.$error->parameter.', '.$error->message;
			// 	}
			// 	throw new Exception("Algumas informações precisam ser revisadas: ".$mensagem, 1);
				
			// } catch (Exception $e) {
			// 	throw $e;
			// }
		}

		/**
		 * Cria um perfil de pagamento na IUGU.
		 * @param (Array) $request -> Dados da requisição
		 * @return (Self Object)
		 */
		public function createPaymentProfile($params)
		{
			//Valida os dados do cartão
			try {
				self::validadeParams($params);
			} catch (Exception $e) {
				throw $e;
			}

			//Antes de criar verifica a existencia do mesmo, 
			//se ele existir é retornado o registro e criada uma associacao do ID no sistema com a IUGU.
			// try {
			// 	$paymentProfile = self::findPaymentProfile($params);
			// } catch (Exception $e) {
			// 	throw $e;
			// }
			// if (is_array($paymentProfile)&&count($paymentProfile)>0) {
				
			// 	//Retorna os dados do cliente existente
			// 	return $paymentProfile[0];

			// }else{

				$paymentProfileData = Array(
					'customer_id'=>$params['iugu_id'],
					'description' => 'Instituto Conexo',
					'token' => $params['cd_hash'],
					'set_as_default' => 'true'
				);

				try {
					
					Iugu::setApiKey($this->api_key);
					Iugu_PaymentMethod::url(Array('customer_id'=>$params['iugu_id']));
					$paymentProfile = Iugu_PaymentMethod::create($paymentProfileData);
					return $paymentProfile;

				} catch (Exception $e) {
					throw $e;
				}

			// }// if else que verifica se ja existe, caso é obsoleto.
		}

		public function findPaymentProfile($params)
		{
			$ExpirationDate = explode("/",$params['cd_data']);
			$ExpirationMonth = trim($ExpirationDate[0]);
			$ExpirationYear = isset($ExpirationDate[1])?trim($ExpirationDate[1]):0;
			if (strlen($ExpirationYear)==2) {
				$ExpirationYear = "20".$ExpirationYear; //Complementa o ano com o milênio.
			}
			$paramKey = ' registry_code='.trim(Text::getOnlyNumber($params['cpf_cnpj'])).' ';
			if (trim($params['origem'])!='') { //Origem de idioma, não usa o cpf
				$paramKey = ' customer_id='.trim($params['iugu_id']).' ';
			}
			$queryData = [
				'query' => $paramKey.' type=PaymentProfile::CreditCard card_number_last_four='.substr(Text::clean($params['cd_number']), -4).' card_number_first_six='.substr(Text::clean(str_replace(" ","",$params['cd_number'])),0,6).' card_expiration='.$ExpirationMonth.'/'.$ExpirationYear.' status=active '
			];

			try {
				$paymentProfileService = new IUGU\PaymentProfile($this->envAuth);
				$paymentProfile = $paymentProfileService->all($queryData);
				return $paymentProfile;
			} catch (IUGU\Exceptions\ValidationException $e) {
				
				$mensagem="";
				foreach ($e->getErrors() as $key => $error) {
					$mensagem .= ' - ['.$error->id.'] '.$error->parameter.', '.$error->message;
				}
				throw new Exception("Algumas informações precisam ser revisadas: ".$mensagem, 1);
				
			} catch (Exception $e) {
				throw $e;
			}
		}

		public function findPaymentMethods()
		{
			// $queryData = [
			// 	'query' => ''
			// ];

			// try {
			// 	$paymentMethodService = new IUGU\PaymentMethod($this->envAuth);
			// 	$paymentMethod = $paymentMethodService->all($queryData);
			// 	return $paymentMethod;
			// } catch (IUGU\Exceptions\ValidationException $e) {
				
			// 	$mensagem="";
			// 	foreach ($e->getErrors() as $key => $error) {
			// 		$mensagem .= ' - ['.$error->id.'] '.$error->parameter.', '.$error->message;
			// 	}
			// 	throw new Exception("Algumas informações precisam ser revisadas: ".$mensagem, 1);
				
			// } catch (Exception $e) {
			// 	throw $e;
			// }
		}

		/**
		 * Inicia a transação de pagamento com o GateWay
		 * Modelo implementado: https://dev.iugu.com/reference/criar-fatura
		 * Com split de pagamento, debito das taxas na conta mestre.
		 *
		 * @param (Array) $request -> Dados da requisição
		 * @return (Self Object)
		 */
		public function makePayment($request)
		{
			// var_dump($request);
			// exit();

			$paymentMethod = (isset($request['paymentMethod'])) ? $request['paymentMethod'] : false;
			if ($paymentMethod===false) {
				throw new Exception("Forma de pagamento não identificada.", 1);
			}

			//Temporário para validação
			// $this->transaction = json_decode('{"id":"0","status":"paid"}');
			// return $this;

			$afiliado = $request['pedido_resource']['afiliado'];
			$coProdutores = $request['pedido_resource']['coprodutores'];
			$usuario = $request['pedido_resource']['usuario'];
			$pedido = $request['pedido_resource']['pedido'];
			$pedidoItens = $request['pedido_resource']['pedido_itens'];

			$installmentQuantity = (isset($request['installmentQuantity'])) ? (int)$request['installmentQuantity'] : 1;
			$validInstallment = self::validateInstallmentQuantity($installmentQuantity,trim($request['cd_brand']));

			//Dados da Fatura

			//Informações de contas e percentuais do Split
			$masteraccount_id = $this->api_master_account_id;
			$masteraccount_percent = (float)Sis::config("PLATAFORMA-COMISSAO-PERCENTUAL"); //Comissão Global da plataforma
			if (!is_numeric($masteraccount_percent) && $masteraccount_percent<=0) {
				$masteraccount_percent=1; //Define 1% padrão de comissão.
			}

			//Dados
			$subaccount_id = "";
			$subaccount_percent = "";
			// $subaccount_api_key_live = "";
			// $subaccount_api_key_test = "";

			$maximoParcela = "";

			$invoiceItens = Array();
			foreach ($pedidoItens as $key => $items) {
				
				$item = Array(
					"description"=>$items['pnome'],
					"quantity"=>1,
					"price_cents"=>(int)number_format((float)$items['item_valor'],2,"","")
				);

				if (is_numeric($items['plataforma_comissao']) && (float)$items['plataforma_comissao']>0 ) {
					$masteraccount_percent = (float)$items['plataforma_comissao'];
				}

				$subaccount_id = $items['iugu_split_account_id']; //Id da subconta na IUGU referente ao produto em questão.
				// $subaccount_api_key_live = $items['iugu_split_live_api_token'];
				// $subaccount_api_key_test = $items['iugu_split_test_api_token'];

				$subaccount_percent = 100 - $masteraccount_percent;
				$maximoParcela = (int)$items['parcelamento_max'];

				array_push($invoiceItens,$item);
			}

			$accountsSplit = Array();

			//MasterAccount - Plataforma Conexo
			$masterAccount = Array(
				"recipient_account_id"=>$this->api_master_account_id,
				"percent"=>$masteraccount_percent
			);
			array_push($accountsSplit,$masterAccount);

			//CoProdutores do Curso
			if (is_array($coProdutores)&&count($coProdutores)>0) {
				foreach ($coProdutores as $key => $coProdutor) {
					$coProdutorAccount = Array(
						"recipient_account_id"=>$coProdutor['iugu_split_account_id'],
						"percent"=>(float)$coProdutor['comissao_percentual']
					);
					array_push($accountsSplit,$coProdutorAccount);
				}
			}
			
			//Afiliado do Curso
			if (is_array($afiliado)&&count($afiliado)>0) {
				$afiliado_comissao = ((float)$afiliado['afiliado_comissao']>0)?(float)$afiliado['afiliado_comissao']:(float)$afiliado['curso_afiliado_comissao'];
				if ($afiliado_comissao>0) {
					$afiliadoAccount = Array(
						"recipient_account_id"=>$afiliado['iugu_split_account_id'],
						"percent"=>$afiliado_comissao
					);
					array_push($accountsSplit,$afiliadoAccount);
				}
			}

			// $subaccount_api_key = (trim($this->environment)=='production')?$subaccount_api_key_live:$subaccount_api_key_test;
			// var_dump($subaccount_api_key);

			$dataInvoice = Array(
				"customer_id"=>$request['iugu_id'],
				"email"=>$usuario['email'],
				"due_date"=>date("Y-m-d"),
				"order_id" => $this->pagamento->getPedidoIdSufix() . $pedido['pedido_idx'],
				"external_reference" => $pedido['pedido_idx'],
				"payable_with"=>Array(),
				"splits"=>$accountsSplit,
				"items"=>$invoiceItens,
				"discount_cents" => (int)number_format((float)$request['pedido_resource']['pedido_desconto'],2,"","")
			);
			// Split Data - Remover apos entrar em producao
			// Array(
			// 	Array(
			// 		"recipient_account_id"=>$this->api_master_account_id,
			// 		"percent"=>$masteraccount_percent
			// 		// "recipient_account_id"=>$subaccount_id, 
			// 		// "percent"=>$subaccount_percent
			// 	)
			// )

    		
    		if ($maximoParcela>0){
				$dataInvoice['max_installments_value'] = (int)$maximoParcela;
			}

			switch ($paymentMethod) {
		    	case 'creditCard':
		    		$dataInvoice['payable_with'] = Array("credit_card");
		    		break;
		    	case 'pix':
		    		$dataInvoice['payable_with'] = Array("pix");
		    		$dataInvoice['payer'] = Array(
						"address" => Array(
							"street" => $usuario['endereco'], 
							"number" => $usuario['numero'], 
							"district" => $usuario['bairro'], 
							"city" => $usuario['cidade'], 
							"state" => $usuario['estado'], 
							"zip_code" => $usuario['cep'], 
							"complement" => $usuario['complemento'] 
						), 
						"cpf_cnpj" => Text::getOnlyNumber($usuario['cpf_cnpj']), 
						"name" => $usuario['nome_completo'],
						"phone" => Text::getOnlyNumber($usuario['telefone_resid'])
					);

		    		break;
		    	case 'boleto':
		    		$dataInvoice['payable_with'] = Array("bank_slip");
		    		$dataInvoice['payer'] = Array(
						"address" => Array(
							"street" => $usuario['endereco'], 
							"number" => $usuario['numero'], 
							"district" => $usuario['bairro'], 
							"city" => $usuario['cidade'], 
							"state" => $usuario['estado'], 
							"zip_code" => $usuario['cep'], 
							"complement" => $usuario['complemento'] 
						), 
						"cpf_cnpj" => Text::getOnlyNumber($usuario['cpf_cnpj']), 
						"name" => $usuario['nome_completo'],
						"phone" => Text::getOnlyNumber($usuario['telefone_resid'])
					);

		    		break;
		    }

			// var_dump($dataInvoice);
			// exit();

			// var_dump($this->api_key);
			// exit();
		    
			//Realiza o pagamento através de uma fatura avulsa.
			try {
				
				Iugu::setApiKey($this->api_key);
				// Iugu::setApiKey($subaccount_api_key);
				$objInvoice = Iugu_Invoice::create($dataInvoice);

				if (is_object($objInvoice)) {
					
					if ( isset($objInvoice->errors) ){ //Falha ao criar.
						$errorReport = "";
						if (is_array($objInvoice->errors)) {
							// code...
							foreach ($objInvoice->errors as $key => $error) {

								$errorReport .= " Erro [".$key." => ";

								foreach ($error as $key2 => $description) {
									$errorReport .= " #".($key2+1).". ".$description;
								}
								$errorReport .= " ] " . PHP_EOL;
							}
						}else{
							$errorReport = (string)$objInvoice->errors;
						}
						$this->transactionError->code = 1;
		    			$this->transactionError->message = $errorReport;

					}else{//Criou com sucesso.
						
						$this->transaction = $objInvoice;
						
						//Já cria e processa a criação cobrança avulsa e relaciona a Fatura criada.
						if ($paymentMethod=='creditCard'){
							self::makePaymentChargeOnly($request,$objInvoice->id);
						}
					}

				}else{
					$this->transactionError->code = 1;
	    			$this->transactionError->message = 'Sem retorno da financeira.';
				}
				
				// var_dump($this->transactionError);
				// var_dump($this->transaction);
				// exit();
				
			} catch (Exception $e) {
				
				// echo "Falha";
				// die($e->getMessage());
				// exit();

				$this->transactionError->code = 1;
			    $this->transactionError->message = $e->getMessage();

			}
			
			return $this;
			

		}

		/**
		 * Inicia a transação de pagamento com o GateWay
		 * Modelo implementado: https://dev.iugu.com/reference/cobranca-direta
		 *
		 * @param (Array) $request -> Dados da requisição
		 * @return (Self Object)
		 */
		public function makePaymentChargeOnly($request,$invoiceID=null)
		{
			// var_dump($request);
			// exit();

			$paymentMethod = (isset($request['paymentMethod'])) ? $request['paymentMethod'] : false;
			if ($paymentMethod===false) {
				throw new Exception("Forma de pagamento não identificada.", 1);
			}

			//Temporário para validação
			// $this->transaction = json_decode('{"id":"0","status":"paid"}');
			// return $this;

			$usuario = $request['pedido_resource']['usuario'];
			$pedido = $request['pedido_resource']['pedido'];
			$pedidoItens = $request['pedido_resource']['pedido_itens'];

			$installmentQuantity = (isset($request['installmentQuantity'])) ? (int)$request['installmentQuantity'] : 1;
			$validInstallment = self::validateInstallmentQuantity($installmentQuantity,trim($request['cd_brand']));

			$userPayer = Array(
				"address" => Array(
					"street" => $usuario['endereco'], 
					"number" => $usuario['numero'], 
					"district" => $usuario['bairro'], 
					"city" => $usuario['cidade'], 
					"state" => $usuario['estado'], 
					"zip_code" => $usuario['cep'], 
					"complement" => $usuario['complemento'] 
				), 
				"cpf_cnpj" => Text::getOnlyNumber($usuario['cpf_cnpj']), 
				"name" => $usuario['nome_completo'],
				"phone" => Text::getOnlyNumber($usuario['telefone_resid'])
			);

			$invoiceItens = Array();
			foreach ($pedidoItens as $key => $items) {
				$item = Array(
					"description"=>$items['pnome'],
					"quantity"=>1,
					"price_cents"=>(int)number_format((float)$items['item_valor'],2,"","")
				);
				array_push($invoiceItens,$item);
			}
			
			//Cria uma assinatura.
			$chargeData = Array(
				"payer" => $userPayer,
		        "email" =>$usuario['email'],
		        "items" => $invoiceItens,
		        "discount_cents" => (int)number_format((float)$request['pedido_resource']['pedido_desconto'],2,".",""),
		        "order_id" => $pedido['pedido_idx'],
		        "external_reference" => $pedido['pedido_idx']
		    );

		    if (!is_null($invoiceID)) {//processa com fatura já criada
		    	$chargeData = Array(
					"invoice_id" => $invoiceID,
					"customer_id" => $request['iugu_id'],
			        "payer" => $userPayer
			    );
		    }

		    switch ($paymentMethod) {
		    	case 'creditCard':
		    		$chargeData['token'] = $request['cd_hash'];
		    		if ($installmentQuantity>1 && $validInstallment){
						$chargeData['months'] = (int)$installmentQuantity;
					}
		    		break;
		    	case 'boleto':
		    		$chargeData['method'] = 'bank_slip';
		    		break;
		    }
			
			//Realiza o pagamento através de uma fatura avulsa.
			try {
				
				Iugu::setApiKey($this->api_key);
				$objCharge = Iugu_Charge::create($chargeData);

				if (is_object($objCharge)) {
					
					if ( isset($objCharge->errors) ){ //Falha ao criar.
						$errorReport = "";
						if (is_array($objCharge->errors)) {
							// code...
							foreach ($objCharge->errors as $key => $error) {

								$errorReport .= " Erro [".$key." => ";

								foreach ($error as $key2 => $description) {
									$errorReport .= " #".($key2+1).". ".$description;
								}
								$errorReport .= " ] " . PHP_EOL;
							}
						}else{
							$errorReport = (string)$objCharge->errors;
						}
						$this->transactionError->code = 1;
		    			$this->transactionError->message = $errorReport;

					}else{//Criou com sucesso.
						
						$objInvoice = Iugu_Invoice::fetch($objCharge->invoice_id);
						// var_dump($objInvoice);
						if ($objInvoice!=null) {
							$this->transaction = $objInvoice;
						}
					}

				}else{
					$this->transactionError->code = 1;
	    			$this->transactionError->message = 'Sem retorno da financeira.';
				}
				
				// var_dump($this->transactionError);
				// var_dump($this->transaction);
				// exit();
				
			} catch (Exception $e) {
				
				// echo "Falha";
				// die($e->getMessage());
				// exit();

				$this->transactionError->code = 1;
			    $this->transactionError->message = $e->getMessage();

			}
			
			return $this;
		}


		public function makeSubscription($request)
		{
			// Não se aplica.
			// exit();

			$assinaPedido = $request['assinatura_resource']['assinatura'];
			$assinaProduto = $request['assinatura_resource']['assinatura_itens'][0];
			
			//Cria uma assinatura.
			$subscriptionData = Array(
			    "plan_identifier" => $assinaProduto['pcodigo'],
			    "customer_id" => $request['iugu_id'],
			    // "expires_at"=>date("Y-m-d"), //Data da primeira cobrança, as próximas datas de cobrança dependem do "intervalo" do plano vinculado
			    "only_on_charge_success" => true, //Apenas Cria a Assinatura se a Cobrança for bem sucedida. Não enviar "expires_at".
			    "ignore_due_email" => true,
			    "payable_with" => Array(
			        "credit_card"
			    ),
			    "credits_based" => false, //para assinaturas baseadas em crédito
			    "price_cents" => 0, //para assinaturas baseadas em crédito
			    "credits_cycle" => 0, //para assinaturas baseadas em crédito
			    "credits_min" => 0, //para assinaturas baseadas em crédito
			    "subitems" => Array(
			        Array(
			            "description" => $assinaProduto['pnome'],
			            "price_cents" => (int)number_format((float)$assinaProduto['item_valor'],2,".",""),
			            "quantity" => $assinaProduto['item_quantidade'],
			            "recurrent" => true
			        )
			    ),
			    "custom_variables"=>Array(
			        Array(
			            "name" => "plataforma_assinatura_id",
			            "value" => $assinaPedido['assinatura_idx']
			        )
			    ),
			    "two_step"=>false, //Não se aplica
			    "suspend_on_invoice_expired"=>true, //Quando uma assinatura tem uma fatura expirada ela fica com situacao de suspensa.
			    "only_charge_on_due_date"=>false //Somente efetua a cobrança do primeiro ciclo no dia do vencimento.
			);

			// var_dump($subscriptionData);
			
			
			//Realiza o pagamento através de uma fatura avulsa.
			try {
				
				Iugu::setApiKey($this->api_key);
				$subscription = Iugu_Subscription::create($subscriptionData);

				// echo "Sucesso";
				// var_dump($subscription);
				
				if (is_object($subscription)) {
					
					if ( is_array($subscription->errors) ) { //Falha ao criar.
						$errorReport = "";
						foreach ($subscription->errors as $key => $error) {

							$errorReport .= " Erro [".$key." => ";

							foreach ($error as $key2 => $description) {
								$errorReport .= " #".($key2+1).". ".$description;
							}
							$errorReport .= " ] " . PHP_EOL;
						}
						$this->transactionError->code = 1;
		    			$this->transactionError->message = $errorReport;

					}else{//Criou com sucesso.
						$this->transaction = $subscription;
					}
				}
				
				// var_dump($this->transactionError);
				// var_dump($this->transaction);
				// exit();
				
			} catch (Exception $e) {
				
				// echo "Falha";
				// die($e->getMessage());
				// exit();

				$this->transactionError->code = 1;
			    $this->transactionError->message = $e->getMessage();

			}
			
			return $this;
		}

		/**
		 * Cancela uma transação no GateWay
		 * @param (Array) $request -> Dados da requisição
		 * @return (JSON Object)
		 */
		public function cancel($params=array()){

			if (!isset($this->transaction->id))
				throw new Exception("Essa transa&ccedil;&atilde;o n&atilde;o possui um Identificador para processar o cancelamento.", 1);
			
			Iugu::setApiKey($this->api_key);
			try {
				
				$invoice = Iugu_Invoice::fetch($this->transaction->id);
				$retornoCancel = $invoice->cancel();
				return $retornoCancel;
				
			} catch (IuguObjectNotFound $e) {
				// die($e->getMessage());
				throw $e;
			} catch (Exception $e) {
				// die($e->getMessage());
				throw $e;
			}

			return false;

		}


		/**
		 * Cancela uma transação no GateWay
		 * @param (Array) $request -> Dados da requisição
		 * @return (JSON Object)
		 */
		public function refund($params=array()){

			if (!isset($this->transaction->id))
				throw new Exception("Essa transa&ccedil;&atilde;o n&atilde;o possui um Identificador para processar o cancelamento.", 1);
			
			Iugu::setApiKey($this->api_key);
			try {
				
				$invoice = Iugu_Invoice::fetch($this->transaction->id);
				$retornoRefound = $invoice->refund();
				return $retornoRefound;
				
			} catch (IuguObjectNotFound $e) {
				// die($e->getMessage());
				throw $e;
			} catch (Exception $e) {
				// die($e->getMessage());
				throw $e;
			}

			return false;

		}



		//Realiza a ativacao de uma assinatita.
		public function subscriptionActivate()
		{
			Iugu::setApiKey($this->api_key);
			try {
				$subscription = Iugu_Subscription::fetch($this->transaction->id);
				$subscription->activate();
			} catch (IuguObjectNotFound $e) {
				// die($e->getMessage());
				throw $e;
			} catch (Exception $e) {
				// die($e->getMessage());
				throw $e;		
			}
		}

		public function subscriptionSuspend()
		{
			Iugu::setApiKey($this->api_key);
			try {
				$subscription = Iugu_Subscription::fetch($this->transaction->id);
				$subscription->suspend();
			} catch (IuguObjectNotFound $e) {
				// die($e->getMessage());
				throw $e;
			} catch (Exception $e) {
				// die($e->getMessage());
				throw $e;		
			}
		}

		/**
		 * Retorna os detalhes do tipo de pagamento escolhido, no caso de boleto é retornado o URL de impressão do documento.
		 * @return (String)
		 */
		public function getPaymentInfoResume()
		{
			$returnInfo = "";
			if (!is_null($this->transaction)) {
				switch (self::getPaymentMethod()) {
					case 'credit_card':
						$returnInfo .= "Cart&atilde;o de Cr&eacute;dito" ;	
						break;
					case 'bank_slip':
						$returnInfo .= "Boleto bancário" ;
						break;
					case 'pix':
						$returnInfo .= "PIX" ;
						break;
				}
				if (isset($this->transaction->secure_url)) {
					$returnInfo .= '<br/><a href="'.$this->transaction->secure_url.'" target="_blank"  >Detalhes da sua fatura.</a>';
				}
			}

			return $returnInfo;
		}

		/**
		 * Retorna os detalhes do tipo de pagamento escolhido, no caso de boleto é retornado o URL de impressão do documento.
		 * @return (String)
		 */
		public function getPaymentInfoComplete(){

			$retornoDados = "";
			if (is_null($this->transaction)) {
				$retornoDados = "Sem dados da transação.";	
			}

			// if (property_exists($this->transaction,'TransactionInfo')) {
			// 	$responseMessage = $this->transaction->TransactionInfo->ResponseMessage;
			// 	$responseMessage = str_replace("u00","\u00",$responseMessage);
			// 	$responseMessage = Text::reformatCSVString($responseMessage);
			// 	$retornoDados .= '
			// 		<strong>Transa&ccedil;&atilde;o</strong><br>
			// 		<i class="text-info" >Acquirer</i>: '.$this->transaction->TransactionInfo->Acquirer.' <br/>
			// 		<i class="text-info" >TransactionId</i>: '.$this->transaction->TransactionInfo->TransactionId.' <br/>
			// 		<i class="text-info" >Complement</i>: '.$this->transaction->TransactionInfo->Complement.' <br/>
			// 		<i class="text-info" >Additional Complement</i>: '.$this->transaction->TransactionInfo->AdditionalComplement.' <br/>
			// 		<i class="text-info" >Response Code</i>: '.$this->transaction->TransactionInfo->ResponseCode.' <br/>
			// 		<i class="text-info" >Response Message</i>: '. $responseMessage .' <br/>
			// 		<i class="text-info" >Transaction Status</i>: '. $this->transaction->TransactionInfo->TransactionStatus .' <br/>
			// 		<i class="text-info" >Transaction Date</i>: '.$this->transaction->TransactionInfo->TransactionDate.' <br/>
			// 		<br/>
			// 	';	
			// }
			// if (property_exists($this->transaction,'Status')) {
			// 	$retornoDados .= '
			// 		<strong>Situa&ccedil;&atilde;o</strong><br>
			// 		<i class="text-info" >Code</i>: ' . $this->transaction->Status->Code . ' <br/>
			// 		<i class="text-info" >Messsage</i>: ' . $this->transaction->Status->Message . ' <br/>
			// 	';	
			// }

			// return self::getSerializedPrettyTransaction();
			return var_export($this->transaction,true);

		}

		/**
		 * Retorna os detalhes do tipo de pagamento escolhido, no caso de boleto é retornado o URL de impressão do documento.
		 * @return (String)
		 */
		public function getPaymentInfoCompleteSeller(){

			$retornoDados = "";
			if (is_null($this->transaction)) {
				return "Sem dados da transação.<br>";	
			}

			switch (self::getPaymentMethod()) {
				case 'credit_card':
					$retornoDados .= "Método de pagamento: Cart&atilde;o de Cr&eacute;dito <br/>" ;	
					break;
				case 'bank_slip':
					$retornoDados .= "Método de pagamento: Boleto bancário <br/>" ;
					break;
				case 'pix':
					$retornoDados .= "Método de pagamento: PIX <br/>" ;
					break;
			}

			$pgSituacao = self::getStatusDescription(self::getTransactionStatusCode());
			$retornoDados .= "Situação da fatura: ".$pgSituacao." <br/>" ;
			$retornoDados .= "Valor pago: ".$this->transaction->total_paid." <br/>" ;
			$retornoDados .= "Taxas Iugu: ".$this->transaction->taxes_paid." <br/>" ;
			$retornoDados .= "Comissão paga: ".$this->transaction->commission." <br/>" ;

			$total = (float)(substr($this->transaction->total_paid_cents,0,strlen($this->transaction->total_paid_cents)-2).'.'.substr($this->transaction->total_paid_cents,-2));
			
			$taxes = (float)(substr($this->transaction->taxes_paid_cents,0,strlen($this->transaction->taxes_paid_cents)-2).'.'.substr($this->transaction->taxes_paid_cents,-2));
			
			$commission = (float)(substr($this->transaction->commission_cents,0,strlen($this->transaction->commission_cents)-2).'.'.substr($this->transaction->commission_cents,-2));

			$repasseLiquido = $total - $taxes - $commission;

			$retornoDados .= "Liquido para repasse: R$ ".number_format($repasseLiquido,2,",",".")." <br/>" ;

			if (self::getPaymentMethod() == 'credit_card') {
				if ($this->transaction->installments>1) {
					
				}
			}

			
			return $retornoDados;

		}

		/**
		 * Retorna o objeto da transacao.
		 * @return (Object)
		 */
		public function getTransaction(){
			if (!is_null($this->transaction))
				return $this->transaction;
			return false;
		}

		public function getSerializedPrettyTransaction(){
			return json_encode($this->transaction, JSON_OBJECT_AS_ARRAY);
		}

		public function getSerializedTransaction(){
			if (!is_null($this->transaction)) {
				return serialize($this->transaction);
			}
			return "";
		}

		public function setSerializedTransaction($transaction){
			if(trim($transaction)!=""){
				try {
					$this->transaction = unserialize($transaction);
				} catch (Exception $e) {
					//Do Nothing
				}
			}
		}


		// public function getSerializedTransaction(){
		// 	return json_encode($this->transaction);
		// }

		// public function setSerializedTransaction($transaction){
		// 	//Camel Case convertion
		// 	$this->transaction = json_decode($transaction);
		// }

		public function getBasicData(){
			return $this->basicData;
		}

		/**
		 * Retorna o código da transacao
		 * @return (String)
		 */
		public function getTransactionId(){
			if (!is_null($this->transaction))
				return $this->transaction->id;
			return 0;
		}

		/**
		 * Retorna o código da autorizacao da transacao
		 * @return (String)
		 */
		public function getTransactionAuthorizationCode(){
			//O gateway Accesstage não retorna um código de autorização.
			return 0;
		}

		/**
		 * Retorna o número da transacao
		 * @return (String)
		 */
		public function getTransactionNumber(){
			//O gateway Accesstage não retorna um número da transação.
			return 0;
		}

		/**
		 * Retorna o código da transacao
		 * @return (String)
		 */
		public function getPaymentMethod(){
			if (is_null($this->transaction))
				return '';

			if (isset($this->transaction->credit_card_tid)) {
				if (!is_null($this->transaction->credit_card_tid)) {
					return 'credit_card';
				}
			}
			if(isset($this->transaction->bank_slip)) {
				if(!is_null($this->transaction->bank_slip)) {
					return 'bank_slip';
				}
			}
			if(isset($this->transaction->pix)) {
				if(!is_null($this->transaction->pix)) {
					return 'pix';
				}
			}
			return '';
		}

		// /**
		//  * Retorna a situação da transação 1=SUCESSO, 0=FALHA.
		//  * @return (Int)
		//  */
		// public function getTransactionStatus(){
		// 	if (is_null($this->transaction))
		// 		return null;
		// 	//verifica se a transação é do tipo assinatura, caso contrário é fatura avulsa.
		// 	if(property_exists($this->transaction, 'status')) {
		// 		return $this->transaction->status;
		// 	}else if(property_exists($this->transaction, 'subscription')) {
		// 		if (!is_null($this->transaction->subscription)) {
		// 			return $this->transaction->subscription->status;
		// 		}
		// 	}
		// 	return null;
		// }

		/**
		 * Retorna o códido de status da transação (fatura).
		 * @return (Int)
		 */
		public function getTransactionStatusCode(){
			
			if (is_null($this->transaction))
				return null;
			
			// $transactionStatus='';
			// if ($this->pagamento->getTransacaoPerfil()=='subscription') {
			// 	$statusSuspended = ($this->transaction->suspended)?'suspended':'not_suspended';
			// 	$statusActive = ($this->transaction->active)?'active':'inactive';
			// 	$transactionStatus = $statusSuspended.'_'.$statusActive;
			// }else{
			// }

			$transactionStatus = $this->transaction->status;
			return $transactionStatus;

		}

		public function getTransactionStatusMessage(){
			if (is_null($this->transaction))
				return null;
			$statusCode  = self::getTransactionStatusCode();
			return self::getStatusDescription($statusCode);
		}

		public function getStatusDescription($statusCode){
			switch (trim($statusCode)) {
				case 'suspended_active':
					$statusMessage = "Suspensa e Ativa";
					break;
				case 'not_suspended_active':
					$statusMessage = "Ativa";
					break;
				case 'suspended_inactive':
					$statusMessage = "Suspensa e Inativa";
					break;
				case 'not_suspended_inactive':
					$statusMessage = "Inativa";
					break;
				case 'pending':
					//pendente,	a fatura ainda não foi paga
					$statusMessage = "Pendente";
					break;
				case 'paid':
					//paga,	pix realizado, boleto compensado ou transação capturada no cartão de crédito
					$statusMessage = "Paga";
					break;
				case 'canceled':
					//cancelada, fatura cancelada e não pode ser paga. Pode ter sido cancelada manualmente pelo painel ou comando via API, ou ainda, devido a uma falha de cobrança no cartão
					$statusMessage = "Cancelada";
					break;
				case 'in_analysis':
					//em análise, recurso da iugu que acrescenta uma etapa a mais ao pagamento da sua fatura entre o (pending) e o (paid). Recurso opcional; você pode ativar/desativar a qualquer momento. (Válido para transação em duas etapas)
					$statusMessage = "Em análise";
					break;
				case 'draft':
					//rascunho,	a fatura ainda não foi gerada, apenas os dados foram salvos
					$statusMessage = "Rascunho";
					break;
				case 'partially_paid':
					//parcialmente paga,	quando um boleto não é pago em seu valor total, ou o valor de multa/juros não foi pago
					$statusMessage = "Parcialmente paga";
					break;
				case 'refunded':
					//reembolsada, quando você devolve o valor ao cliente por desistência ou por algum erro. Status válido penas para pagamentos com cartão de crédito e PIX.
					$statusMessage = "Reembolsada";
					break;
				case 'expired':
					//expirada,	a cobrança atingiu o tempo limite após o vencimento e expirou. Não pode ser paga
					$statusMessage = "Expirada";
					break;
				case 'in_protest':
					//em protesto, quando uma fatura já paga recebe uma notificação de não reconhecimento da compra (apenas para pagamentos por cartão de crédito)
					$statusMessage = "Em protesto";
					break;
				case 'chargeback':
					//contestada, quando o status (in_protest) é estornado para o cliente, pois ganhou a disputa do chargeback. Significa que sua fatura foi contestada e nesta etapa, já não poderá recorrer
					$statusMessage = "Contestada";
					break;
				default:
					$statusMessage=$statusCode;
					break;
			}
			return $statusMessage;
		}

		//Retorna o link de pagamento, Boleto, ETF
		public function getPaymentLink(){
			if (is_null($this->transaction)){
				return '';
			}
			switch(self::getPaymentMethod()) {
				case 'bank_slip':
					if (isset($this->transaction->secure_url)) {
						return $this->transaction->secure_url;
					}
					break;
			}
			return '';
		}

		//Retorna o link de pagamento, Boleto, ETF
		public function getPixData(){
			if (is_null($this->transaction)){
				return '';
			}
			if (self::getPaymentMethod() == 'pix'){
				return $this->transaction->pix;
			}
			return false;
		}

		//Retorna o link de pagamento, Boleto, ETF
		public function getBoletoNumero(){
			//Não se aplica
			return 0;
		}


		public function getTransactionSerialized(){
			return json_encode($this->transaction);
		}

		//VALIDAR NECESSIDADE
		public function getBrandBySlug($slug){
			switch ($slug) {
				case 'visa' :
					return 'VISA';
					break;
				case 'mastercard' :
					return 'MASTERCARD';
					break;
				case 'americanexpress' :
				case 'amex' :
					return 'AMEX';
					break;
				case 'elo' :
					return 'ELO';
					break;
				case 'dinersclub' :
					return 'DINERS';
					break;
				case 'discover' :
					return 'DISCOVER';
					break;
				case 'jcb' :
					return 'JCB';
					break;
				case 'aura' :
					return 'AURA';
					break;
				case 'hipercard' :
					return 'HIPERCARD';
					break;

			}
			return '';
		}

		public function getBrandByCode($brandCode){
			switch ($brandCode) {
				case 'visa':
					return 'Visa' ;
					break;
				case 'mastercard':
					return 'Mastercard' ;
					break;
				case 'american_express':
					return 'American Express' ;
					break;
				case 'elo':
					return 'Elo' ;
					break;
				case 'diners':
					return 'Diners Club' ;
					break;
				case 'discover':
					return 'Discover' ;
					break;
				case 'jcb':
					return 'JCB' ;
					break;
				case 'aura':
					return 'Aura' ;
					break;
				case 'hiper':
				case 'hipercard':
					return 'Hipercard' ;
					break;
			}
			return '';
		}


		public function validateInstallmentQuantity($quantity,$cardBrand){
			//gera a lista de parcelamentos para o meio de pagamento
			$EcommerceGatewayParcelamentosStr = Sis::config("ECOMMERCE-GATEWAY-PARCELAMENTOS-IUGU");
			$EcommerceGatewayParcelamentos = explode(PHP_EOL,$EcommerceGatewayParcelamentosStr);
			$cParcelaSJ = 0;
			$cParcelaCJ = 0;
			foreach ($EcommerceGatewayParcelamentos as $key => $brandParams) {
				$params = explode(",",$brandParams);
				$cBrand = isset($params[0])?$params[0]:'';
				$cParcelaSJ = isset($params[1])?$params[1]:0;
				$cParcelaCJ = isset($params[2])?$params[2]:0;
				if ($cardBrand==trim(strtolower($cBrand))) {
					break;
				}
			}
			$maximoParcela = max($cParcelaCJ,$cParcelaSJ);
			return $quantity<=$maximoParcela;
		}


		public function subscriptionGetAllBills()
		{
			$bills = [];
			if (is_array($this->transaction->recent_invoices)&&count($this->transaction->recent_invoices)>0) {
				foreach ($this->transaction->recent_invoices as $key => $invoice) {
					$bill = array(
						'tid' => $invoice->id,
						'status' => $invoice->status,
						'data'=> $invoice->due_date,
						'source' => serialize($invoice)
					);
					array_push($bills,$bill);
				}
			}
			return $bills;
		}

		public function billGetDueDate()
		{
			if (!is_null($this->transaction))
				return 	$this->transaction->due_date;
			return '';
		}

		public function getTransactionData()
		{
			$retornoTransaction = json_decode('{"code":"0","message":"","tid":""}');
			if ((int)$this->transactionError->code>0) {
				$retornoTransaction->code = $this->transactionError->code;
				$retornoTransaction->message = $this->transactionError->message;
			}else{
				$retornoTransaction->code = self::getTransactionStatusCode();
				$retornoTransaction->message = self::getTransactionStatusMessage();
				$retornoTransaction->tid = self::getTransactionId();
			}
			return $retornoTransaction;
		}

		public function getError(){
			if (!is_null($this->transactionError)) {
				return array('code'=>$this->transactionError->code,'message'=>$this->transactionError->message);
			}
			$tStatus = self::getTransactionStatusCode();
			if ($tStatus=='not_suspended_active') { //Códigos de sucesso not_suspended_active, os demais a assinatura esta suspenda ou inativa.
				$tStatusInfo = self::getTransactionStatusMessage();
				return array('code'=>$tStatus,'message'=>$tStatusInfo);
			}
			return array('code'=>0,'message'=>'');
		}

		public function validadeParams(&$request){

			if ($request['paymentMethod']=='creditCard') {

				$creditCardHash = (isset($request['cd_hash'])) ? $request['cd_hash'] : '';

				$creditCardBrand = (isset($request['creditCardBrand'])) ? $request['creditCardBrand'] : '';
				// $creditCardCCode = (isset($request['cd_secure'])) ? $request['cd_secure'] : '';
				// $creditCardDtValid = (isset($request['cd_data'])) ? $request['cd_data'] : '';
				// $creditCardHolderName = (isset($request['cd_holder_name'])) ? $request['cd_holder_name'] : '';
				// $creditCardNumber = (isset($request['cd_number'])) ? $request['cd_number'] : '';
				
				// $cardBrand = self::getBrandBySlug($creditCardBrand);
				
				if ($creditCardHash=='') {
					throw new Exception("Não é possível continuar, certifique-se de que as informacões de pagamento estão corretas.", 1);
				}

				$installmentQuantity = (isset($request['installmentQuantity'])) ? (int)$request['installmentQuantity'] : 0;
				$validInstallment = self::validateInstallmentQuantity($installmentQuantity,trim($creditCardBrand));
				if (!$validInstallment) {
					throw new Exception("O número de parcelas não é consistente com o parcelamento possível para a bandeira informada.", 1);
				}

				// //Demais dados preenchidos
				// if(
				// 	trim($creditCardBrand) == '' || 
				// 	trim($creditCardCCode) == '' || 
				// 	trim($creditCardDtValid) == '' || 
				// 	trim($creditCardHolderName) == '' ||
				// 	trim($creditCardNumber) == ''
				// ){
				// 	throw new Exception("Certifique-se de que todas as informações de pagamento foram preenchidas.", 1);
				// }
				
				// //Valida a validade do cartão
				// $validadeCheck = explode("/",$creditCardDtValid);
				// $validadeCheckAno = isset($validadeCheck[1])?trim($validadeCheck[1]):"0000";
				// $validadeCheckMes = isset($validadeCheck[0])?trim($validadeCheck[0]):"00"; 
				// $validadeToday = date("Ym");
				// if (strlen($validadeCheckAno)==2) {
				// 	$validadeCheckAno = "20".$validadeCheckAno; //Inclui o milênio.
				// }
				// if (
				// 	(int)($validadeCheckAno.$validadeCheckMes) < (int)$validadeToday || 
				// 	!((int)$validadeCheckMes>0&&(int)$validadeCheckMes<13)	
				// ) {
				// 	throw new Exception("A data de validade do cartão está expirada.", 1);
				// }
				
			}

		}

		/**
		 * Obtem os dados da transação atualizados junto no GateWay
		 */
		public function updateFromOrigin()
		{
			$paymentId = $this->pagamento->getTransactionCode();
			Iugu::setApiKey($this->api_key);
			if ($this->pagamento->getTransacaoPerfil()=='subscription') {
				try {
					$resultSearch = Iugu_Subscription::fetch($paymentId);
					if ($resultSearch!=null) {
						if ($resultSearch->id == $paymentId) {
							$this->transaction = $resultSearch;
						}
					}
				} catch (IuguObjectNotFound $e) {
					// die($e->getMessage());
					// throw $e;
				} catch (Exception $e) {
					// die($e->getMessage());
					// throw $e;		
				}
			}else{
				try {
					$resultSearch = Iugu_Invoice::fetch($paymentId);
					if ($resultSearch!=null) {
						if ($resultSearch->id == $paymentId) {
							$this->transaction = $resultSearch;
						}
					}
				} catch (IuguObjectNotFound $e) {
					// die($e->getMessage());
					// throw $e;
				} catch (Exception $e) {
					// die($e->getMessage());
					// throw $e;		
				}
			}
		}

		public function webhookNotification(){

			$forma_pagamento = 'iugu';
			//As chamadas são enviadas com content-type application/x-www-form-urlencoded através do POST HTTP
   			try {
   				$event = isset($_POST['event'])?trim($_POST['event']):null;
   				$data = isset($_POST['data'])?$_POST['data']:null;
   				if (!is_null($event) && !is_null($data) && is_array($data)){
					if (strpos($event,'invoice.')!==false) { //qualquer evento de fatura
						return $data['id'];
					}
					if (strpos($event,'subscription.')!==false) { //qualquer evento de assinatura
						//returna o ID da transacao(cobranca) para marcar o flag de atualizacao.
						return $data['id'];
					}
				}
   			} catch (Exception $e) {
   				error_log($forma_pagamento . ": ".$e->getMessage(), 3, "webhook-errors.log");
   			}
   			return false;

		}

	}
