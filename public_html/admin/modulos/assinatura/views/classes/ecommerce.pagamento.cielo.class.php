<?php
	/**
	 * https://github.com/DeveloperCielo/API-3.0-PHP
	 * Local: /admin/library/vendor/Cielo-API-3.0
	 */

	if(!defined("DC"))
		define("DC",DIRECTORY_SEPARATOR);
	require_once $_SERVER['DOCUMENT_ROOT'].DC."admin".DC."library".DC."vendor".DC."Cielo-API-3.0".DC."vendor".DC."autoload.php";

	use Cielo\API30\Merchant;
	use Cielo\API30\Ecommerce\Environment;
	use Cielo\API30\Ecommerce\Sale;
	use Cielo\API30\Ecommerce\CieloEcommerce;
	use Cielo\API30\Ecommerce\Payment;
	use Cielo\API30\Ecommerce\CreditCard;
	use Cielo\API30\Ecommerce\Request\CieloRequestException;


	//Dados gerados pelo usuário "desenvolvimento@being.com.br" no portal de desenvolvimento da Cielo
	//https://desenvolvedores.cielo.com.br/api-portal/user
	define("CIELO_MERCHANT_ID_SANDBOX","f38ddfad-9b6a-45f4-92e3-523e511caf3d");
	define("CIELO_MERCHANT_KEY_SANDBOX","WCHHUAYZILWEEJKFWKCFSATVFDWOBFIFBDWJDXIU");

	//Dados de produção, cliente Conexo - institutoconexo.com.br
	define("CIELO_MERCHANT_ID","");
	define("CIELO_MERCHANT_KEY","");

	class EcommerceCieloPayment
	{

		private $transaction; //Objeto com todas as informações da transação.
		private $transactionCapture; //Informacoes do processo de captura da transação
		private $transactionError; //Registra o erro caso o mesmo exista;
		private $environmentPayment; // Configure o ambiente
		private $environment; // Configure o ambiente
		private $merchant; // Configure seu merchant
		private $lojistaDados; //Dados do Lojista
		private $ecomPagamento;
		private $basicData;

		function __construct($_enviroment='sandbox')
		{
			$this->environment = ($_enviroment!='production') ? 'sandbox' : 'production' ;
			$this->environmentPayment = ($_enviroment=='sandbox') ? Environment::sandbox() : Environment::production() ;
			$this->merchant = ($_enviroment=='sandbox') ? new Merchant(CIELO_MERCHANT_ID_SANDBOX, CIELO_MERCHANT_KEY_SANDBOX) : new Merchant(CIELO_MERCHANT_ID, CIELO_MERCHANT_KEY) ;

			$this->lojistaDados = array(
				'nome'=>'Conexo',
				'endereco'=> '',
				'numero'=> '',
				'bairro'=> '',
				'cep'=> '',
				'cidade'=> 'Fortaleza',
				'estado'=> 'CE',
				'complemento'=> '',
				'cnpj'=> ''
			);

			$this->basicData = array(
				'CardBrand'=>'',
				'InstallmentQuantity'=>'',
				'PaymentMethod'=>''
			);

		}

		public function setEcomPagamento($ecomPagamento){
			$this->ecomPagamento = $ecomPagamento;
		}

		/**
		 * Inicia a transação de pagamento com o GateWay da Cielo
		 * @param (Array) $item -> Itens do carrinho
		 * @param (Int) $codigoPedido -> Codigo da transação no banco de dados para notificações futuras
		 * @param (String) $transporte -> Nome do transporte. SEDEX / PAG / NOT_SPECIFIED
		 * @param (Array) $enderecoEnvio -> Array com endereço do cliente
		 * @param (Decimal) $frete -> Valor de cobrado no frete
		 * @param (Array) $cobranca -> Dados para a cobrança, cartão de crédito, débito ou boleto.
		 * @return (Self Object)
		 */
		public function makePayment(&$request)
		{
			$pedido = $request['pedido_resource']['pedido'];
			$pedidoItens = $request['pedido_resource']['pedido_itens'];
			$usuario = $request['pedido_resource']['usuario'];
			
			// Crie uma instância de Sale informando o ID do pedido na loja
			$sale = new Sale($request['pedido_resource']['pedido']['pedido_chave']);
			
			// Crie uma instância de Payment informando o valor do pagamento
			$valor_pedido = $request['pedido_resource']['pedido_total']-$request['pedido_resource']['pedido_desconto'];
			if ($valor_pedido<=0) {
				$valor_pedido=0;
			}
			$valor_pagamento = $valor_pedido;
			$valor_pagamento = number_format($valor_pagamento,2,"","");

			if ($valor_pagamento<=0) {
				throw new Exception("O valor para cobrança deve ser superior a ZERO", 1);
			}

			//Dados enviados no request
				// cieloCreditCardBrand
				// cieloCreditCardCCode
				// cieloCreditCardDtValid
				// cieloCreditCardHolderName
				// cieloCreditCardNumber
				// cieloInstallmentQuantity
				// cieloPaymentMethod

			$cardBrand = (isset($request['cieloCreditCardBrand'])) ? self::getBrandBySlug($request['cieloCreditCardBrand']) : '';

			$this->basicData['CardBrand'] = (isset($request['cieloCreditCardBrand'])) ? $request['cieloCreditCardBrand'] : '';
			$this->basicData['InstallmentQuantity'] = (isset($request['cieloInstallmentQuantity'])) ? $request['cieloInstallmentQuantity'] : '';
			$this->basicData['PaymentMethod'] = (isset($request['cieloPaymentMethod'])) ? $request['cieloPaymentMethod'] : '';

			// Crie uma instância de Customer informando o nome do cliente
			if (trim($request['cieloPaymentMethod'])=='boleto') {//Boleto

				preg_match_all('!\d+!', trim($usuario['cpf_cnpj']), $uMatches);
				$usuario_cpf_cnpj = implode("",$uMatches[0]);

				preg_match_all('!\d+!', trim($pedido['cep']), $pMatches);
				$pedido_cep = implode("",$pMatches[0]);

				$customer = $sale->customer($usuario['nome_completo'])
                  ->setIdentity($usuario_cpf_cnpj)
                  ->setIdentityType( (($usuario_cpf_cnpj>11)?'CNPJ':'CPF') )
                  ->address()->setZipCode($pedido_cep)
                             ->setCountry('BRA')
                             ->setState($pedido['estado'])
                             ->setCity($pedido['cidade'])
                             ->setDistrict($pedido['bairro'])
                             ->setStreet($pedido['endereco'])
                             ->setNumber($pedido['numero'])
                             ->setComplement($pedido['complemento']);

			}else{ //Cartões / Transferencia
				$payment = $sale->payment($valor_pagamento);
				$customer = $sale->customer($usuario['nome_completo']);
			}

			$ExpirationDate = explode("/",$request['cieloCreditCardDtValid']);
			$ExpirationMonth = trim($ExpirationDate[0]);
			$ExpirationYear = isset($ExpirationDate[1])?trim($ExpirationDate[1]):0;
			if (strlen($ExpirationYear)==2) {
				$ExpirationYear = "20".$ExpirationYear; //Complementa o ano com o milênio.
			}

			switch ($request['cieloPaymentMethod']) {
				case 'creditCard'://Pagamento com cartão
					// Crie uma instância de Credit Card utilizando os dados de teste
					// Esses dados estão disponíveis no manual de integração

					preg_match_all('!\d+!', trim($request['cieloCreditCardNumber']), $cMatches);
					$cNumber = implode("",$cMatches[0]);

					$cieloInstallmentQuantity = (isset($request['cieloInstallmentQuantity'])) ? (int)$request['cieloInstallmentQuantity'] : 0;
					$validInstallment = self::validateInstallmentQuantity($cieloInstallmentQuantity,trim($request['cieloCreditCardBrand']));

					

					$payment->setType(Payment::PAYMENTTYPE_CREDITCARD)
							->setCapture(true)
					        ->creditCard(trim($request['cieloCreditCardCCode']), $cardBrand)
					        ->setExpirationDate($ExpirationMonth."/".$ExpirationYear)
					        ->setCardNumber($cNumber)
					        ->setHolder($request['cieloCreditCardHolderName']);

					if ($cieloInstallmentQuantity>1 && $validInstallment){
						$payment->setInstallments($cieloInstallmentQuantity);
					}

					break;

				case 'debitCard'://Pagamento no débito

					preg_match_all('!\d+!', trim($request['cieloCreditCardNumber']), $cMatches);
					$cNumber = implode("",$cMatches[0]);

					// Defina a URL de retorno para que o cliente possa voltar para a loja
					// após a autenticação do cartão
					$payment->setReturnUrl('http://'.$_SERVER['HTTP_HOST'].'/pedido-concluido/?pcodigo='.$pedido['pedido_chave']);

					// Crie uma instância de Debit Card utilizando os dados de teste
					// esses dados estão disponíveis no manual de integração
					$cardBrandType = ($request['cieloCreditCardBrand']=='visa') ? CreditCard::VISA : ( ($request['cieloCreditCardBrand']=='mastercard')?CreditCard::MASTERCARD:null ) ; 
					$payment->debitCard(trim($request['cieloCreditCardCCode']), $cardBrandType)
					        ->setExpirationDate($ExpirationMonth."/".$ExpirationYear)
					        ->setCardNumber($cNumber)
					        ->setHolder($request['cieloCreditCardHolderName']);

					break;
				case 'boleto'://Pagamento no boleto

					// Sequencial para o numero do boleto.
					$boletoNumero = $this->ecomPagamento->getBoletoNumeroProx($pedido['pagamento_idx']);
					
					preg_match_all('!\d+!', trim($this->lojistaDados['cnpj']), $matches);
					$cedenteDocumento = implode("",$matches[0]);

					$cedenteEndereco = $this->lojistaDados['endereco'].', '.$this->lojistaDados['numero'].' / '.$this->lojistaDados['complemento'].' - '.$this->lojistaDados['cep'].' - '.$this->lojistaDados['bairro'].' - '.$this->lojistaDados['cidade'].'-'.$this->lojistaDados['estado'];

					// Crie uma instância de Payment informando o valor do pagamento
					$payment = $sale->payment($valor_pagamento)
					                ->setType(Payment::PAYMENTTYPE_BOLETO)
					                ->setAddress($cedenteEndereco)
					                ->setBoletoNumber($boletoNumero)
					                ->setAssignor($this->lojistaDados['nome'])
					                ->setDemonstrative('Compra online')
					                ->setIdentification($cedenteDocumento)
					                ->setInstructions('Nao receber apos o vencimento. Nao receber pagamento com cheque.');
					
					// if ((int)$usuario['boleto_prazo']>0) {
					// 	$payment->setExpirationDate(date('d/m/Y', strtotime('+'.(int)$usuario['boleto_prazo'].' day')));
					// }

					break;

				case 'transferencia'://Pagamento com transferencia online
					
					$payment = $sale->payment($valor_pagamento)
					                ->setType(Payment::PAYMENTTYPE_ELECTRONIC_TRANSFER);
					break;
			}
			
			// Crie o pagamento na Cielo
			try {
			    
			    // Configure o SDK com seu merchant e o ambiente apropriado para criar a venda.
			    $this->transaction = (new CieloEcommerce($this->merchant, $this->environmentPayment))->createSale($sale);

			    //BLOCO USADO PARA CAPTURA, REMOVER APÓS VALIDAÇÃO DESSA ETAPA PELO CLIENTE
			    //-------------------------------------------------------------------------
					// Com a venda criada na Cielo, já temos o ID do pagamento, TID e demais
					// dados retornados pela Cielo
					// $paymentId = $this->transaction->getPayment()->getPaymentId();
					// if(trim($request['cieloPaymentMethod'])=='creditCard') {// && $this->environment=='production'
					// // Com o ID do pagamento, podemos fazer sua captura, se ela não tiver sido capturada ainda
					// // $this->transaction = (new CieloEcommerce($this->merchant, $this->environmentPayment))->captureSale($paymentId, $valor_pagamento, 0);
					// }

			} catch (CieloRequestException $e) {
			    $this->transactionError = $e->getCieloError();
			}

			// echo "DEBUG";
			// var_dump($this->transaction);
			// var_dump($this->transaction->getPayment()->getTid());
			// exit();

			return $this;
		}

		/**
		 * Retorna os detalhes do tipo de pagamento escolhido, no caso de boleto é retornado o URL de impressão do documento.
		 * @return (String)
		 */
		public function getPaymentInfoResume()
		{
			if (is_null($this->transaction))
				return "Não há transação registrada para carregar os detalhes.";
			
			$returnInfo = "";
			$statusPayment = self::getStatusDescription((int)$this->transaction->getPayment()->getStatus());
			if (!is_null($this->transaction)) {
				switch ($this->transaction->getPayment()->getType()) {
					case 'CreditCard':
						$returnInfo .= "Cart&atilde;o de Cr&eacute;dito - " . self::getBrandByCode($this->transaction->getPayment()->getCreditCard()->getBrand())." <br/> <small>Situação da transação junto a financeira: <br/> ".$statusPayment->description."</small>" ;	
						break;
					case 'DebitCard':
						$returnInfo .= "Cart&atilde;o de D&eacute;bito - " . self::getBrandByCode($this->transaction->getPayment()->getDebitCard()->getBrand())." <br/> <small>Situação da transação junto a financeira: <br/> ".$statusPayment->description."</small>" ;
						break;
					case 'Boleto':
						$returnInfo .= "Boleto Banc&aacute;rio";
						break;
				}	
			}
			return $returnInfo;
		}

		/**
		 * Retorna os detalhes do tipo de pagamento escolhido, no caso de boleto é retornado o URL de impressão do documento.
		 * @return (String)
		 */
		public function getPaymentInfoComplete()
		{
			if (is_null($this->transaction))
				return "Não há transação registrada para carregar os detalhes.";

			$returnInfo = "";
			$statusPayment = self::getStatusDescription((int)$this->transaction->getPayment()->getStatus());
			if (!is_null($this->transaction)) {
				switch ($this->transaction->getPayment()->getType()) {
					case 'CreditCard':
						$returnInfo .= '
							<strong>Tipo / Bandeira:</strong> Cart&atilde;o de Cr&eacute;dito - ' . self::getBrandByCode($this->transaction->getPayment()->getCreditCard()->getBrand()) .' <br/>
							<strong>Valor da transação. (Ammount):</strong> '. number_format(($this->transaction->getPayment()->getAmount()/100),2,",",".") .' <br/>
							
							<strong>Parcelamento:</strong> '. $this->transaction->getPayment()->getInstallments() .'x <br/>

							<strong>Número da autorização, identico ao NSU. (ProofOfSale):</strong> '.$this->transaction->getPayment()->getProofOfSale().' <br/>
							<strong>Id da transação na adquirente. (Tid):</strong> '.$this->transaction->getPayment()->getTid().' <br/>
							<strong>Código de autorização. (AuthorizationCode) :</strong> '.$this->transaction->getPayment()->getAuthorizationCode().' <br/>
							<strong>Texto impresso na fatura bancaria comprador - Exclusivo para VISA/MASTER. (SoftDescriptor) :</strong> '.$this->transaction->getPayment()->getSoftDescriptor().' <br/>
							<strong>Campo Identificador do Pedido. (PaymentId) :</strong> '.$this->transaction->getPayment()->getPaymentId().' <br/>
							<strong>Status da Transação. (Status) :</strong> '.$this->transaction->getPayment()->getStatus().' // '.$statusPayment->description.' <br/>
							<strong>Código de retorno da Adquirência. (ReturnCode) :</strong> '.$this->transaction->getPayment()->getReturnCode().' <br/>
							<strong>Mensagem de retorno da Adquirência. (ReturnMessage) :</strong> '.$this->transaction->getPayment()->getReturnMessage().'<br/>' ;	
						break;
					case 'DebitCard':
						$returnInfo .= '
							<strong>Tipo / Bandeira:</strong> Cart&atilde;o de D&eacute;bito - ' . self::getBrandByCode($this->transaction->getPayment()->getDebitCard()->getBrand()) .' <br/>
							<strong>Valor da transação. (Ammount):</strong> '. number_format(($this->transaction->getPayment()->getAmount()/100),2,",",".") .' <br/>
							<strong>Número da autorização, identico ao NSU. (ProofOfSale):</strong> '.$this->transaction->getPayment()->getProofOfSale().' <br/>
							<strong>Id da transação na adquirente. (Tid):</strong> '.$this->transaction->getPayment()->getTid().' <br/>
							<strong>Código de autorização. (AuthorizationCode) :</strong> '.$this->transaction->getPayment()->getAuthorizationCode().' <br/>
							<strong>Texto impresso na fatura bancaria comprador - Exclusivo para VISA/MASTER. (SoftDescriptor) :</strong> '.$this->transaction->getPayment()->getSoftDescriptor().' <br/>
							<strong>Campo Identificador do Pedido. (PaymentId) :</strong> '.$this->transaction->getPayment()->getPaymentId().' <br/>
							<strong>Status da Transação. (Status) :</strong> '.$this->transaction->getPayment()->getStatus().' // '.$statusPayment->description.' <br/>
							<strong>Código de retorno da Adquirência. (ReturnCode) :</strong> '.$this->transaction->getPayment()->getReturnCode().' <br/>
							<strong>Mensagem de retorno da Adquirência. (ReturnMessage) :</strong> '.$this->transaction->getPayment()->getReturnMessage().'<br/>' ;	



						break;
					case 'Boleto':

						$PaymentId = $this->transaction->getPayment()->getPaymentId(); // 	Campo Identificador do Pedido. 	Guid 	36 	xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
						$Instructions = $this->transaction->getPayment()->getInstructions(); // 	Instruções do Boleto. 	Texto 	255 	Ex: Aceitar somente até a data de vencimento, após essa data juros de 1% dia.
						$ExpirationDate = $this->transaction->getPayment()->getExpirationDate(); // 	Data de expiração. 	Texto 	10 	2014-12-25
						$Url = $this->transaction->getPayment()->getUrl(); // 	Url do Boleto gerado. 	string 	256 	Ex:https://…/pagador/reenvia.asp/8464a692-b4bd-41e7-8003-1611a2b8ef2d
						$Number = $this->transaction->getPayment()->getNumber(); // 	“NossoNumero” gerado. 	Texto 	50 	Ex: 1000000012-8
						$BarCodeNumber = $this->transaction->getPayment()->getBarCodeNumber(); // 	Representação numérica do código de barras. 	Texto 	44 	Ex: 00091628800000157000494250100000001200656560
						$DigitableLine = $this->transaction->getPayment()->getDigitableLine(); // 	Linha digitável. 	Texto 	256 	Ex: 00090.49420 50100.000004 12006.565605 1 62880000015700
						$Assignor = $this->transaction->getPayment()->getAssignor(); // 	Nome do Cedente. 	Texto 	256 	Ex: Loja Teste
						$Address = $this->transaction->getPayment()->getAddress(); // 	Endereço do Cedente. 	Texto 	256 	Ex: Av. Teste, 160
						$Identification = $this->transaction->getPayment()->getIdentification(); // 	Documento de identificação do Cedente. 	Texto 	14 	CPF ou CNPJ do Cedente sem os caracteres especiais (., /, -)
						$Status = $this->transaction->getPayment()->getStatus(); // 	Status da Transação. 	Byte 	— 	1

						$returnInfo .= '
							<strong>Identificador do Pedido:</strong> '.$PaymentId.'<br/>
							<strong>Instruções do Boleto:</strong> '.$Instructions.'<br/>
							<strong>Data de expiração:</strong> '.$ExpirationDate.'<br/>
							<strong>Url do Boleto gerado:</strong> '.$Url.'<br/>
							<strong>“NossoNumero” gerado:</strong> '.$Number.'<br/>
							<strong>Representação numérica do código de barras:</strong> '.$BarCodeNumber.'<br/>
							<strong>Linha digitável:</strong> '.$DigitableLine.'<br/>
							<strong>Nome do Cedente:</strong> '.$Assignor.'<br/>
							<strong>Endereço do Cedente:</strong> '.$Address.'<br/>
							<strong>Documento de identificação do Cedente:</strong> '.$Identification.'<br/>
							<strong>Status da Transação:</strong> '.$Status.' // '.$statusPayment->description.'

						' ;	
						break;
				}	
			}
			return $returnInfo;
		}


		/**
		 * Retorna o objeto da transacao.
		 * @return (Object)
		 */
		public function getTransaction(){
			// if (!is_null($this->transactionCapture)){
			// 	return $this->transactionCapture;
			// }
			return $this->transaction;
		}

		public function getSerializedTransaction(){
			if (!is_null($this->transaction)) {
				return json_encode($this->transaction->jsonSerialize());
			}
			return "";
		}

		public function setSerializedTransaction($transaction){
			//Camel Case convertion
			$transactionArray = json_decode($transaction,true);
			if (!is_null($transactionArray)) {
				$transactionArrayFinal = ucfirstKeys($transactionArray);
				$transactionJsonString = json_encode($transactionArrayFinal);
				//Armazena o Objeto da Venda
				$this->transaction = Sale::fromJson($transactionJsonString);

				if ($this->transaction!=null) {
					if($this->transaction->getPayment()->getType()=='CreditCard') {
						$this->basicData['CardBrand'] = $this->transaction->getPayment()->getCreditCard()->getBrand();
						$this->basicData['InstallmentQuantity'] = $this->transaction->getPayment()->getInstallments();
					}elseif($this->transaction->getPayment()->getType()=='DebitCard') {
						$this->basicData['CardBrand'] = $this->transaction->getPayment()->getDebitCard()->getBrand();
					}
					$this->basicData['PaymentMethod'] = $this->transaction->getPayment()->getType();
				}

			}
		}

		public function getBasicData(){
			return $this->basicData;
		}

		/**
		 * Retorna o código da transacao
		 * @return (String)
		 */
		public function getTransactionCode(){
			// if (!is_null($this->transactionCapture)){
			// 	return $this->transactionCapture->getPayment()->getTid();
			// }
			if (!is_null($this->transaction)) {
				return $this->transaction->getPayment()->getTid();	
			}
			return 0;
		}

		/**
		 * Retorna o código da autorizacao da transacao
		 * @return (String)
		 */
		public function getTransactionAuthorizationCode(){
			// if (!is_null($this->transactionCapture)){
			// 	return $this->transactionCapture->getPayment()->getAuthorizationCode();
			// }
			return $this->transaction->getPayment()->getAuthorizationCode();
		}

		/**
		 * Retorna o código da transacao
		 * @return (String)
		 */
		public function getTransactionNumber(){
			return $this->transaction->getPayment()->getNumber();
		}

		/**
		 * Retorna o código da transacao
		 * @return (String)
		 */
		public function getPaymentMethod(){
			switch ($this->transaction->getPayment()->getType()) {
				case Payment::PAYMENTTYPE_CREDITCARD:
					return 'creditCard';
					break;
				case Payment::PAYMENTTYPE_DEBITCARD:
					return 'debitCard';
					break;
				case Payment::PAYMENTTYPE_ELECTRONIC_TRANSFER:
					return 'etf';
					break;
				case Payment::PAYMENTTYPE_BOLETO:
					return 'boleto';
					break;
				default:
					return '';
					break;
			}
		}

		/**
		 * Retorna a situação da transação 1=SUCESSO, 0=FALHA.
		 * @return (Int)
		 */
		public function getTransactionStatus(){
			// if (!is_null($this->transactionCapture)){
			// 	return $this->transactionCapture->getPayment()->getStatus();
			// }
			if (!is_null($this->transaction)) {
				return $this->transaction->getPayment()->getStatus();
			}
			return -1;
		}

		public function getStatusDescription($status){
			$status_return = json_decode('{"status":"","description":""}');
			switch ($status) {
				case 0:
				 	$status_return->status = 'NotFinished';
					$status_return->description = 'Aguardando atualização de status';
					break;
				case 1:
				 	$status_return->status = 'Authorized';
					$status_return->description = 'Pagamento apto a ser capturado ou definido como pago';
					break;
				case 2:
				 	$status_return->status = 'PaymentConfirmed';
					$status_return->description = 'Pagamento confirmado e finalizado';
					break;
				case 3:
				 	$status_return->status = 'Denied';
					$status_return->description = 'Pagamento negado por Autorizador';
					break;
				case 10:
				 	$status_return->status = 'Voided';
					$status_return->description = 'Pagamento cancelado';
					break;
				case 11:
				 	$status_return->status = 'Refunded';
					$status_return->description = 'Pagamento cancelado após 23:59 do dia de autorização';
					break;
				case 12:
				 	$status_return->status = 'Pending';
					$status_return->description = 'Aguardando Status de instituição financeira';
					break;
				case 13:
				 	$status_return->status = 'Aborted';
					$status_return->description = 'Pagamento cancelado por falha no processamento';
					break;
				case 20:
				 	$status_return->status = 'Scheduled';
					$status_return->description = 'Recorrência agendada';
					break;
			}
			return $status_return;
		}

		public function getStatusCardBinDescription($status){
			switch ((string)$status) {
				case "00":
				 	return 'Analise autorizada ';
					break;
				case "01":
				 	return 'Bandeira não suportada';
					break;
				case "02":
				 	return 'Cartão não suportado na consulta de bin';
					break;
				case "73":
				 	return 'Afiliação bloqueada';
					break;
			}
			return '';
		}


		public function getReturnCodeDescription($returnCode){
			$status_return = json_decode('{"returnStatus":"","returnMessage":""}');
			switch ($returnCode) {
				case '4':
				case '6':
					$status_return->returnStatus = "Autorizado";
					$status_return->returnMessage = "Operação realizada com sucesso";
					break;

				case '05':
					$status_return->returnStatus = "Não Autorizado";
					$status_return->returnMessage = "Não Autorizado";
					break;

				case '57':
					$status_return->returnStatus = "Não Autorizado";
					$status_return->returnMessage = "Cartão Expirado";
					break;

				case '78':
					$status_return->returnStatus = "Não Autorizado";
					$status_return->returnMessage = "Cartão Bloqueado";
					break;

				case '99':
					$status_return->returnStatus = "Não Autorizado";
					$status_return->returnMessage = "Time Out";
					break;

				case '77':
					$status_return->returnStatus = "Não Autorizado";
					$status_return->returnMessage = "Cartão Cancelado";
					break;

				case '70':
					$status_return->returnStatus = "Não Autorizado";
					$status_return->returnMessage = "Problemas com o Cartão de Crédito";
					break;

				case '99':
					$status_return->returnStatus = "Autorização Aleatória";
					$status_return->returnMessage = "Operation Successful / Time Out";
					break;

				default:
					# code...
					break;
			}
			return $status_return;
		}

		//Retorna o link de pagamento, Boleto, ETF
		public function getPaymentLink(){
			if (is_null($this->transaction)){
				return '';
			}
			switch ($this->transaction->getPayment()->getType()) {
				case Payment::PAYMENTTYPE_DEBITCARD:
				case Payment::PAYMENTTYPE_ELECTRONIC_TRANSFER:
					return $this->transaction->getPayment()->getAuthenticationUrl(); //Pagamento Débito
					break;
				case Payment::PAYMENTTYPE_BOLETO:
					return $this->transaction->getPayment()->getUrl();
					break;
				default:
					return '';
					break;
			}
		}
		//Retorna o link de pagamento, Boleto, ETF
		public function getBoletoNumero(){
			
			if (is_null($this->transaction)){
				return 0;
			}

			switch ($this->transaction->getPayment()->getType()) {
				case Payment::PAYMENTTYPE_BOLETO:
					/**
					* O gate retorna o número informado originalmente complementado com o controle interno deles,
					* ex.: se definirmos o número do boleto "Nosso Número" como sendo "123", no retorno vem "123-2".
					**/
					$boletoNumberRetorno = $this->transaction->getPayment()->getBoletoNumber();
					$boletoNumber = explode("-",$boletoNumberRetorno);
					return (int)$boletoNumber[0];
					break;
				default:
					return 0;
					break;
			}
		}

		

		// public function getTransactionSerialized(){
		// 	return serialize($this->transaction->getPayment());
		// }

		public function getBrandBySlug($slug){
			switch ($slug) {
				case 'visa' :
					return CreditCard::VISA;
					break;
				case 'mastercard' :
					return CreditCard::MASTERCARD;
					break;
				case 'americanexpress' :
					return CreditCard::AMEX;
					break;
				case 'elo' :
					return CreditCard::ELO;
					break;
				case 'dinersclub' :
					return CreditCard::DINERS;
					break;
				case 'discover' :
					return CreditCard::DISCOVER;
					break;
				case 'jcb' :
					return CreditCard::JCB;
					break;
				case 'aura' :
					return CreditCard::AURA;
					break;

			}
			return '';
		}

		public function getBrandByCode($brandCode){
			switch ($brandCode) {
				case CreditCard::VISA:
					return 'Visa' ;
					break;
				case CreditCard::MASTERCARD:
					return 'Mastercard' ;
					break;
				case CreditCard::AMEX:
					return 'American Express' ;
					break;
				case CreditCard::ELO:
					return 'Elo' ;
					break;
				case CreditCard::DINERS:
					return 'Diners Club' ;
					break;
				case CreditCard::DISCOVER:
					return 'Discover' ;
					break;
				case CreditCard::JCB:
					return 'JCB' ;
					break;
				case CreditCard::AURA:
					return 'Aura' ;
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

		public function getTransactionData()
		{
			$retornoTransaction = json_decode('{"code":"0","message":"","url_pagamento":"","tid":""}');
			if (!is_null($this->transactionError)) {
				$retornoTransaction->code = $this->transactionError->getCode();
				$retornoTransaction->message = $this->transactionError->getMessage();
			}else{
				$tStatus = self::getTransactionStatus();
				$tStatusInfo = self::getStatusDescription((int)$tStatus);
				$retornoTransaction->code = $tStatus;
				$retornoTransaction->message = $tStatusInfo->description;
				if ((int)$tStatus<=2) { //Códigos de sucesso 0,1 2, os demais são erro na transação e outras operacoes.
					$retornoTransaction->url_pagamento = self::getPaymentLink();
					$retornoTransaction->tid = self::getTransactionCode();	
				}
			}
			return $retornoTransaction;
		}

		public function getError(){
			if (!is_null($this->transactionError)) {
				return array('code'=>$this->transactionError->getCode(),'message'=>$this->transactionError->getMessage());
			}
			$tStatus = self::getTransactionStatus();
			if ((int)$tStatus>2) { //Códigos de sucesso 0,1 2, os demais são erro na transação e outras operacoes.
				$tStatusInfo = self::getStatusDescription((int)$tStatus);
				return array('code'=>$tStatus,'message'=>$tStatusInfo->description);
			}
			return array('code'=>0,'message'=>'');
		}


		public function validadeParams(&$request){
			
			$cieloCreditCardBrand = (isset($request['cieloCreditCardBrand'])) ? $request['cieloCreditCardBrand'] : '';
			$cieloCreditCardCCode = (isset($request['cieloCreditCardCCode'])) ? $request['cieloCreditCardCCode'] : '';
			$cieloCreditCardDtValid = (isset($request['cieloCreditCardDtValid'])) ? $request['cieloCreditCardDtValid'] : '';
			$cieloCreditCardHolderName = (isset($request['cieloCreditCardHolderName'])) ? $request['cieloCreditCardHolderName'] : '';
			$cieloCreditCardNumber = (isset($request['cieloCreditCardNumber'])) ? $request['cieloCreditCardNumber'] : '';
			$cieloInstallmentQuantity = (isset($request['cieloInstallmentQuantity'])) ? $request['cieloInstallmentQuantity'] : '';
			$cieloPaymentMethod = (isset($request['cieloPaymentMethod'])) ? $request['cieloPaymentMethod'] : '';

			$cardBrand = (isset($request['cieloCreditCardBrand'])) ? self::getBrandBySlug($request['cieloCreditCardBrand']) : '';

			preg_match_all('!\d+!', trim($cieloCreditCardNumber), $cMatches);
			$creditCardNumber = implode("",$cMatches[0]);
			
			switch ($cieloPaymentMethod) {
				case 'creditCard'://Valida para cartão

					//Consulta o BIN do cartão no gateway, apenas para ambiente de produção.
					if ($this->environment=='production') {
						try {
						    $cardBin = (new CieloEcommerce($this->merchant, $this->environmentPayment))->getCardBin(substr($creditCardNumber,0,6));
						} catch (CieloRequestException $e) {
							if (is_null($e->getCieloError())) {
								throw new Exception("Erro ao processar a validação do cartão. Erro não identificado.", 1);
							}else{
								throw new Exception("[".$e->getCieloError()->getCode()."] " . $e->getCieloError()->getMessage(), 1);
							}
						}

						if ($cardBin->Status == "01") {
							throw new Exception("Erro 01 - A bandeira do cartão não é suportada.", 1);
						}elseif ($cardBin->Status == "02") {
							throw new Exception("Erro 02 - Não foi possível validar seu cartão. O Cartão não é suportado na consulta.", 1);
						}elseif ($cardBin->Status == "72") {
							throw new Exception("Erro 72 - Não foi possível validar seu cartão. Tente novamente mais tarde, se o problema persistir entre em contato.", 1);
						}

						$cardBrandFromBin = strtolower($cardBin->Provider);

						if (trim($cardBin->CardType)=="Débito" || trim($cardBin->CardType)=="Debito") {
							throw new Exception("O cartão informado não é do tipo Crédito.", 1);
						}

						if ($cardBrandFromBin=='') {
							throw new Exception("Nenhuma bandeira identificada para o pagamento ou a bandeira do cartão não é atendida.", 1);
						}
					}

					$cieloInstallmentQuantity = (isset($request['cieloInstallmentQuantity'])) ? (int)$request['cieloInstallmentQuantity'] : 0;
					$validInstallment = self::validateInstallmentQuantity($cieloInstallmentQuantity,trim($request['cieloCreditCardBrand']));
					if (!$validInstallment) {
						throw new Exception("O número de parcelas não é consistente com o parcelamento possível para a bandeira informada.", 1);
					}
					//Demais dados preenchidos
					if(
						trim($cieloCreditCardBrand) == '' || 
						trim($cieloCreditCardCCode) == '' || 
						trim($cieloCreditCardDtValid) == '' || 
						trim($cieloCreditCardHolderName) == '' ||
						trim($cieloCreditCardNumber) == ''
					){
						throw new Exception("Certifique-se de que todas as informações de pagamento foram preenchidas.", 1);
					}

					//Valida a validade do cartão
					$validadeCheck = explode("/",$cieloCreditCardDtValid);
					$validadeCheckAno = isset($validadeCheck[1])?trim($validadeCheck[1]):"0000";
					$validadeCheckMes = isset($validadeCheck[0])?trim($validadeCheck[0]):"00"; 
					$validadeToday = date("Ym");
					if (strlen($validadeCheckAno)==2) {
						$validadeCheckAno = "20".$validadeCheckAno; //Inclui o milênio.
					}
					if (
						(int)($validadeCheckAno.$validadeCheckMes) < (int)$validadeToday || 
						!((int)$validadeCheckMes>0&&(int)$validadeCheckMes<13)	
					) {
						throw new Exception("A data de validade do cartão está expirada.", 1);
					}

					// var_dump($cardBin);
					// exit();

					break;

				case 'debitCard'://Valida no débito

					//Consulta o BIN do cartão no gateway, apenas para ambiente de produção.
					if ($this->environment=='production') {
						try {
						    $cardBin = (new CieloEcommerce($this->merchant, $this->environmentPayment))->getCardBin(substr($creditCardNumber,0,6));
						} catch (CieloRequestException $e) {
							if (is_null($e->getCieloError())) {
								throw new Exception("Erro ao processar a validação do cartão. Erro não identificado.", 1);
							}else{
								throw new Exception("[".$e->getCieloError()->getCode()."] " . $e->getCieloError()->getMessage(), 1);
							}
						}
						
						if ($cardBin->Status == "01") {
							throw new Exception("Erro 01 - A bandeira do cartão não é suportada.", 1);
						}elseif ($cardBin->Status == "02") {
							throw new Exception("Erro 02 - Não foi possível validar seu cartão. O Cartão não é suportado na consulta.", 1);
						}elseif ($cardBin->Status == "72") {
							throw new Exception("Erro 72 - Não foi possível validar seu cartão. Tente novamente mais tarde, se o problema persistir entre em contato.", 1);
						}

						$cardBrandFromBin = strtolower($cardBin->Provider);

						if (trim($cardBin->CardType)=="Crédito" || trim($cardBin->CardType)=="Credito") {
							throw new Exception("O cartão informado não é do tipo Débito.", 1);
						}

						if ($cardBrandFromBin=='' ||  ( strtolower($cardBrandFromBin)!='visa' && strtolower($cardBrandFromBin)!='mastercard' ) ) {
							throw new Exception("Nenhuma bandeira identificada para o pagamento ou a bandeira do cartão não é atendida.", 1);
						}
					}


					//Demais dados preenchidos
					if(
						trim($cieloCreditCardBrand) == '' || 
						trim($cieloCreditCardCCode) == '' || 
						trim($cieloCreditCardDtValid) == '' || 
						trim($cieloCreditCardHolderName) == '' ||
						trim($cieloCreditCardNumber) == ''
					){
						throw new Exception("Certifique-se de que todas as informações de pagamento foram preenchidas.", 1);
					}

					// var_dump($cardBin);
					// exit();

					break;
				case 'boleto'://Valida no boleto
				case 'transferencia'://Valida com transferencia online
					break;
				default:
					throw new Exception("Método de pagamento não identificado, certifique-se de ter selecionado dentre uma das opções válidas.", 1);
					break;
			}

		}

		/**
		 * Cancela uma transação no GateWay
		 * @param (FLOAT) $valor -> Valor para ser cancelado na venda
		 * @return (JSON Object)
		 */
		public function cancel($valor){

			//Estrutura padrão para esse retorno.
			$status_return = json_decode('{"Status":"","Message":"","Error":{"Code":"0","Message":""}}');

			if (is_null($this->transaction))
				throw new Exception("É necessário o objeto de transação para realizar a operação de cancelamento.", 1);

			try {
				$paymentId = $this->transaction->getPayment()->getPaymentId();
			} catch (Exception $e) {
				throw $e;
			}

			//Cancelamento apenas para cartão de crédito/débito.
			if ($this->transaction->getPayment()->getType() !='Boleto') {

				$OrderValorToCancel = number_format((float)$valor,2,"","");

				try {

					$sale = (new CieloEcommerce($this->merchant, $this->environmentPayment))->cancelSale($paymentId, $OrderValorToCancel);
					if ( $sale->getReturnCode()==9 || $sale->getReturnCode()==0 ){ //De acordo com a documentação o cancelamento foi sucesso.
						$status_return->Status = $sale->getStatus(); //10 Venda Cancelada - Não consta na documentação oficial.
					    $status_return->Message = $sale->getReturnMessage();
					}else{
						$status_return->Error->Code = $returnCT->Status->Code;
				    	$status_return->Error->Message = $returnCT->Status->Message;
					}

				} catch (CieloRequestException $e) {
				    // Em caso de erros de integração, podemos tratar o erro aqui.
				    // os códigos de erro estão todos disponíveis no manual de integração.
				    $error = $e->getCieloError();
				    $status_return->Error->Code = $error->getCode();
				    $status_return->Error->Message = $error->getMessage();
				}

			}

			return $status_return;

		}

		/**
		 * Obtem os dados da transação atualizados junto no GateWay
		 */
		public function updateFromOrigin()
		{
			$paymentId = $this->transaction->getPayment()->getPaymentId();
			$this->transaction = (new CieloEcommerce($this->merchant, $this->environmentPayment))->getSale($paymentId);
		}

	}



function ucfirstKeys($data)
{
	if (is_array($data)) {
		foreach ($data as $key => $value)
		{
			// Convert key
			$newKey = ucfirst($key);

			// Change key if needed
			if ($newKey != $key)
			{
				unset($data[$key]);
				$data[$newKey] = $value;
			}

			// Handle nested arrays
			if (is_array($value))
			{
				$data[$newKey] = ucfirstKeys($data[$newKey]);
			}
		}
	}
	return $data;
}