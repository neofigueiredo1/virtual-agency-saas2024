<?php

$DR = $_SERVER['DOCUMENT_ROOT'];
$DS = DIRECTORY_SEPARATOR;

require_once($DR.$DS.'admin'.$DS.'modulos'.$DS.'ecommerce'.$DS.'views'.$DS.'classes'.$DS.'ecommerce.pagamento.class.php');

class RestAPI
{

	/*
		POST 	Create
		GET 	Read
		PUT 	Update/Replace
		PATCH 	Update/Modify
		DELETE 	Delete
	*/

	const COMMANDS_GET = '[transactions][transactionsInstallments]';
	const COMMANDS_POST = '';
	const COMMANDS_PUT = '';
	const COMMANDS_DELETE = '';
	const PASSWORD = ''; //Usado na autenticação da integridade dos dados trafegados.
	const AuthorizationToken = 'c15d3087bea97579658f69c186841357d8eafb79'; //Usado na autenticação via cabeçalho.

	private $activeCommands;
	private $method;
	private $command;
	private $command_id;
	private $request_uri;
	private $requestParams;

	function __construct(){

		$http_authorization = isset($_SERVER['HTTP_AUTHORIZATION'])?$_SERVER['HTTP_AUTHORIZATION']:'';
		if(trim($http_authorization)==''){
			header('HTTP/1.0 403 Forbidden');
			exit();
		}else if(strtolower(trim($http_authorization)) != 'bearer '.RestAPI::AuthorizationToken){
			header('HTTP/1.0 403 Forbidden');
			exit();
		}
		
		// get the HTTP method, path and body of the request
		$this->method = $_SERVER['REQUEST_METHOD'];
		$REQUEST_URI = explode("?",$_SERVER['REQUEST_URI'])[0];
		$this->command = isset($_GET['command'])?str_replace("api/","",$_GET['command']): (isset($REQUEST_URI)?str_replace("/api/","",$REQUEST_URI):"") ;

		$commandData = explode("/",$this->command);
		if (count($commandData)>1) {
			$this->command = $commandData[0];
			$this->command_id = (int)$commandData[1];
		}

		$this->request_uri = explode('/', trim((isset($_SERVER['ORIG_PATH_INFO'])?$_SERVER['ORIG_PATH_INFO']:$_SERVER['SCRIPT_NAME']),'/'));
		$this->requestParams = json_decode(file_get_contents('php://input'),true);

		if( empty($this->requestParams) ) {
			$this->activeCommands = RestAPI::COMMANDS_GET;
			switch($this->method) {
				case 'post':
				case 'POST': //Create
					$this->requestParams = $_POST;
					$this->activeCommands = RestAPI::COMMANDS_POST;
					break;

				case 'get':
				case 'GET': //Read
					$this->activeCommands = RestAPI::COMMANDS_GET;
					$this->requestParams = $_GET;
					break;
			}
		}else{
			switch($this->method) {
				case 'post':
				case 'POST': //Create
					$this->activeCommands = RestAPI::COMMANDS_POST;
					break;

				case 'put':
				case 'PUT': //Update/Replace
					$this->activeCommands = RestAPI::COMMANDS_PUT;
					break;

				case 'delete':
				case 'DELETE': //Delete
					$this->activeCommands = RestAPI::COMMANDS_DELETE;
					break;
			}
		}

	}

	public function execute(){

		if (trim($this->command)==""){
			throw new Exception("Deve ser informado um comando.", 1);
		}

		if (strpos($this->activeCommands,"[".$this->command."]")===false){
			throw new Exception("O comando solicitado [". ($this->activeCommands) ."] não existe.", 1);
		}

		switch (strtolower($this->method.$this->command)) {

			case 'gettransactions':
				return self::getTransactions($this->requestParams);
				break;
				
			case 'gettransactionsinstallments':
				return self::getTransactionsInstallments($this->requestParams);
				break;

			default:
				throw new Exception("No command set", 1);
				break;
		}
	}

	/**
	* Recuperação de senha do usuário.
	* @param Array $params
	* @return String JSON.
	*/
	public function getTransactions($params)
	{
		$ecomPagamento = new EcommercePagamento();
		try {

			$transacoesList = $ecomPagamento->getPaymentTransactions();

			/**
			 * Retorno com os seguintes dados
				Código	
					Código do Cliente	
					Código da Assinatura	
					E-mail	
					Nome	
					CPF/CNPJ	
				Situação	
				Data de criação	
				Vencimento	
				Data da Ocorrência	
				Data do pagamento	
				Data do reembolso	
					Métodos de Pagamento Disponíveis	
				Paga com	
				Parcelas	
				Taxa	
				Comissão	
				Multa	
				Total	
					Adquirente	
				Refunded cents	
					Chave de unicidade

			Complementado com:
				produtor_id
				produtor_nome
				account_id
				curso_id
				curso_nome
			*/
			$dataFields = Array(
				'"Código"',
				'"Situação"',
				'"Data de criação"',
				'"Vencimento"',
				'"Data da Ocorrência"',
				'"Data do pagamento"',
				'"Data do reembolso"',
				'"Paga com"',
				'"Parcelas"',
				'"Taxa"',
				'"Comissão"',
				'"Multa"',
				'"Total"',
				'"Refunded cents"',
				'"Account ID"',
				'"Pedido Código"',
				'"Produtor Código"',
				'"Produtor Nome"',
				'"Curso Código"',
				'"Curso Nome"'
			);
			$dataFieldsHeader = implode(",",$dataFields);

			$transacoesDataContent = $dataFieldsHeader.PHP_EOL;

			if (is_array($transacoesList)&&count($transacoesList)>0) {

				foreach ($transacoesList as $key => $transacaoItem) {
					
					$ecomPagamento->setPagamentoId(1);
					$ecomPagamento->registrarGateway('sandbox');
					$ecomPagamento->gateway->setSerializedTransaction($transacaoItem['transacao_source']);
					$gatewayTransaction = $ecomPagamento->gateway->getTransaction();

					if (!is_null($gatewayTransaction)) {
						
						// print_r($gatewayTransaction);
						// var_dump($gatewayTransaction->status);
						// exit();

						
						$taxes_paid_cents = $gatewayTransaction->taxes_paid_cents;
						$taxes_paid_cents = (!is_null($taxes_paid_cents))?(float)(substr($taxes_paid_cents,0,strlen($taxes_paid_cents)-2).".".substr($taxes_paid_cents,-2)):0;

						$commission_cents = $gatewayTransaction->commission_cents;
						$commission_cents = (!is_null($commission_cents))?(float)(substr($commission_cents,0,strlen($commission_cents)-2).".".substr($commission_cents,-2)):0;

						$late_payment_fine_cents = $gatewayTransaction->late_payment_fine_cents;
						$late_payment_fine_cents = (!is_null($late_payment_fine_cents))?(float)(substr($late_payment_fine_cents,0,strlen($late_payment_fine_cents)-2).".".substr($late_payment_fine_cents,-2)):0;

						$total_cents = $gatewayTransaction->total_cents;
						$total_cents = (!is_null($total_cents))?(float)(substr($total_cents,0,strlen($total_cents)-2).".".substr($total_cents,-2)):0;

						$refunded_cents = $gatewayTransaction->refunded_cents;
						$refunded_cents = (!is_null($refunded_cents))?(float)(substr($refunded_cents,0,strlen($refunded_cents)-2).".".substr($refunded_cents,-2)):0;


						$dataFieldsData = Array(
							'"'.$transacaoItem['transacao_codigo'].'"', // 'Código'
							'"'.$gatewayTransaction->status.'"', // 'Situação'
							'"'.$gatewayTransaction->created_at_iso.'"', // 'Data de criação'
							'"'.$gatewayTransaction->due_date.'"', // 'Vencimento'
							'"'.$gatewayTransaction->occurrence_date.'"', // 'Data da Ocorrência'
							'"'.$gatewayTransaction->paid_at.'"', // 'Data do pagamento'
							'"'.$gatewayTransaction->refunded_at_iso.'"', // 'Data do reembolso'
							'"'.$gatewayTransaction->payment_method.'"', // 'Paga com'
							'"'.$gatewayTransaction->installments.'"', // 'Parcelas'
							'"'.$taxes_paid_cents.'"', // 'Taxa'
							'"'.$commission_cents.'"', // 'Comissão'
							'"'.$late_payment_fine_cents.'"', // 'Multa'
							'"'.$total_cents.'"', // 'Total'
							'"'.$refunded_cents.'"', // 'Refunded cents'
							'"'.$transacaoItem['account_id'].'"', // 'Account ID'
							'"'.$transacaoItem['pedido_idx'].'"', // 'Pedido Código'
							'"'.$transacaoItem['produtor_id'].'"', // 'Produtor Código'
							'"'.$transacaoItem['produtor_nome'].'"', // 'Produtor Nome'
							'"'.$transacaoItem['curso_idx'].'"', // 'Curso Código'
							'"'.$transacaoItem['curso_nome'].'"' // 'Curso Nome'
						);
						$dataFieldsRow = implode(",",$dataFieldsData);

						$transacoesDataContent .= $dataFieldsRow.PHP_EOL;

					}

				}

				ob_clean();
				echo $transacoesDataContent;
				exit();

				$response_body = array(
					'transacoes' => count($transacoesList)
				);

				return $response_body;
				
			}else{
				throw new Exception("Sem transações para a lista.", 1);
			}
		} catch (Exception $e) {
			die($e->getMessage());
			// throw $e;
		}
	}

	public function getTransactionsInstallments($params)
	{
		$ecomPagamento = new EcommercePagamento();
		try {

			$transacoesInstallmentsList = $ecomPagamento->getPaymentTransactionsInstallments();

			$dataFields = Array(
				'"Transacao Código"',
				'"id"',
				'"installment"',
				'"return_date"',
				'"status"',
				'"amount"',
				'"taxes"',
				'"executed_date"',
				'"advanced"',
				'"advance_fee"',
				'"commission"',
				'"amount_cents"',
				'"taxes_cents"',
				'"advance_fee_cents"',
				'"return_date_is"'
			);
			$dataFieldsHeader = implode(",",$dataFields);

			$transacoesDataContent = $dataFieldsHeader.PHP_EOL;

			if (is_array($transacoesInstallmentsList)&&count($transacoesInstallmentsList)>0) {

				foreach ($transacoesInstallmentsList as $key => $transacaoItem) {
					
					$ecomPagamento->setPagamentoId(1);
					$ecomPagamento->registrarGateway('sandbox');
					$ecomPagamento->gateway->setSerializedTransaction($transacaoItem['transacao_source']);
					$gatewayTransaction = $ecomPagamento->gateway->getTransaction();

					if (!is_null($gatewayTransaction)) {


						if ($gatewayTransaction->payment_method=='iugu_credit_card') {

							if (isset($gatewayTransaction->financial_return_dates)) {
								if (is_array($gatewayTransaction->financial_return_dates)) {
									foreach ($gatewayTransaction->financial_return_dates as $key2 => $f_return_date) {
										
										$amount_cents = $f_return_date->amount_cents;
										$amount_cents = (!is_null($amount_cents))?(float)(substr($amount_cents,0,strlen($amount_cents)-2).".".substr($amount_cents,-2)):0;

										$taxes_cents = $f_return_date->taxes_cents;
										$taxes_cents = (!is_null($taxes_cents))?(float)(substr($taxes_cents,0,strlen($taxes_cents)-2).".".substr($taxes_cents,-2)):0;

										$advance_fee_cents = $f_return_date->advance_fee_cents;
										$advance_fee_cents = (!is_null($advance_fee_cents))?(float)(substr($advance_fee_cents,0,strlen($advance_fee_cents)-2).".".substr($advance_fee_cents,-2)):0;

										$dataFieldsData = Array(
											'"'.$transacaoItem['transacao_codigo'].'"', // 'Código'
											'"'.$f_return_date->id.'"',
											'"'.$f_return_date->installment.'"',
											'"'.$f_return_date->return_date.'"',
											'"'.$f_return_date->status.'"',
											'"'.$f_return_date->amount.'"',
											'"'.$f_return_date->taxes.'"',
											'"'.$f_return_date->executed_date.'"',
											'"'.$f_return_date->advanced.'"',
											'"'.$f_return_date->advance_fee.'"',
											'"'.$f_return_date->commission.'"',
											'"'.$amount_cents.'"',
											'"'.$taxes_cents.'"',
											'"'.$advance_fee_cents.'"',
											'"'.$f_return_date->return_date_iso.'"'
										);
										$dataFieldsRow = implode(",",$dataFieldsData);

										$transacoesDataContent .= $dataFieldsRow.PHP_EOL;
									}
								}else{

									$total_cents = $gatewayTransaction->total_cents;
									$total_cents = (!is_null($total_cents))?(float)(substr($total_cents,0,strlen($total_cents)-2).".".substr($total_cents,-2)):0;

									$taxes_paid_cents = $gatewayTransaction->taxes_paid_cents;
									$taxes_paid_cents = (!is_null($taxes_paid_cents))?(float)(substr($taxes_paid_cents,0,strlen($taxes_paid_cents)-2).".".substr($taxes_paid_cents,-2)):0;

									$commission_cents = $gatewayTransaction->commission_cents;
									$commission_cents = (!is_null($commission_cents))?(float)(substr($commission_cents,0,strlen($commission_cents)-2).".".substr($commission_cents,-2)):0;

									$advance_fee_cents = $gatewayTransaction->advance_fee_cents;
									$advance_fee_cents = (!is_null($advance_fee_cents))?(float)(substr($advance_fee_cents,0,strlen($advance_fee_cents)-2).".".substr($advance_fee_cents,-2)):0;

									$installment = ((int)$gatewayTransaction->installment<=0)?1:(int)$gatewayTransaction->installment;

									$dataFieldsData = Array(
										'"'.$transacaoItem['transacao_codigo'].'"', // 'Código'
										'"'.$gatewayTransaction->id.'"',
										'"'.$installment.'"',
										'"'.$gatewayTransaction->due_date.'"',
										'"'.$gatewayTransaction->status.'"',
										'"'.$gatewayTransaction->total.'"',
										'"'.$gatewayTransaction->taxes_paid.'"',
										'"'.$gatewayTransaction->paid_at.'"',
										'""', //$gatewayTransaction->advanced
										'"0"', //$gatewayTransaction->advance_fee
										'"'.$commission_cents.'"',
										'"'.$total_cents.'"',
										'"'.$taxes_paid_cents.'"',
										'"'.$advance_fee_cents.'"',
										'"'.$gatewayTransaction->created_at_iso.'"'
									);
									$dataFieldsRow = implode(",",$dataFieldsData);

									$transacoesDataContent .= $dataFieldsRow.PHP_EOL;
									
								}
							}

						}else if (!is_null($gatewayTransaction->pix) || !is_null($gatewayTransaction->bank_slip)) { //Caso PIX
							
							$total_cents = $gatewayTransaction->total_cents;
							$total_cents = (!is_null($total_cents))?(float)(substr($total_cents,0,strlen($total_cents)-2).".".substr($total_cents,-2)):0;

							$taxes_paid_cents = $gatewayTransaction->taxes_paid_cents;
							$taxes_paid_cents = (!is_null($taxes_paid_cents))?(float)(substr($taxes_paid_cents,0,strlen($taxes_paid_cents)-2).".".substr($taxes_paid_cents,-2)):0;

							$commission_cents = $gatewayTransaction->commission_cents;
							$commission_cents = (!is_null($commission_cents))?(float)(substr($commission_cents,0,strlen($commission_cents)-2).".".substr($commission_cents,-2)):0;

							$advance_fee_cents = $gatewayTransaction->advance_fee_cents;
							$advance_fee_cents = (!is_null($advance_fee_cents))?(float)(substr($advance_fee_cents,0,strlen($advance_fee_cents)-2).".".substr($advance_fee_cents,-2)):0;

							$installment = ((int)$gatewayTransaction->installment<=0)?1:(int)$gatewayTransaction->installment;

							$dataFieldsData = Array(
								'"'.$transacaoItem['transacao_codigo'].'"', // 'Código'
								'"'.$gatewayTransaction->id.'"',
								'"'.$installment.'"',
								'"'.$gatewayTransaction->due_date.'"',
								'"'.$gatewayTransaction->status.'"',
								'"'.$gatewayTransaction->total.'"',
								'"'.$gatewayTransaction->taxes_paid.'"',
								'"'.$gatewayTransaction->paid_at.'"',
								'""', //$gatewayTransaction->advanced
								'"0"', //$gatewayTransaction->advance_fee
								'"'.$commission_cents.'"',
								'"'.$total_cents.'"',
								'"'.$taxes_paid_cents.'"',
								'"'.$advance_fee_cents.'"',
								'"'.$gatewayTransaction->created_at_iso.'"'
							);
							$dataFieldsRow = implode(",",$dataFieldsData);

							$transacoesDataContent .= $dataFieldsRow.PHP_EOL;

						}

						// print_r($gatewayTransaction);
						// var_dump($gatewayTransaction->status);
						// exit();

						

					}

				}

				ob_clean();
				echo $transacoesDataContent;
				exit();

				$response_body = array(
					'transacoes' => count($transacoesList)
				);

				return $response_body;
				
			}else{
				throw new Exception("Sem parcelamentos para a lista.", 1);
			}
		} catch (Exception $e) {
			die($e->getMessage());
			// throw $e;
		}
	}

	
	//Utilities

	public function api_getAPIHash($stringToHash){
		//Usado para gerar o hash de token
  		return hash('sha256',$stringToHash.RestAPI::PASSWORD);
	}

	public function api_testAPIHash($stringToCheck,$hash){
		//Usado para testar o hash de token e dados enviados
		$hashToCheck = hash('sha256',$stringToCheck.RestAPI::PASSWORD);
		// var_dump($stringToCheck);
		// var_dump(RestAPI::PASSWORD);
		// var_dump($hash)
		// var_dump($hashToCheck);

		return ($hashToCheck==$hash);
	}

	public function multi_implode($array, $glue){
		$ret = '';
		if (is_object($array)) {
			$array = (array) $array;
		}
	    if (is_array($array)) {
	    	foreach ($array as $item) {
		        if (is_array($item)) {
		            $ret .= self::multi_implode($item, $glue) . $glue;
		        } else {
		            $ret .= $item . $glue;
		        }
		    }
	    }else{
	    	$ret .= $array . $glue;
	    }
	    // $ret = substr($ret, 0, 0-strlen($glue));
	    return $ret;
	}

}