<?php
	
class EcommerceBoletoPayment
{

		private $transaction; //Objeto com todas as informações da transação.
		private $transactionCapture; //Informacoes do processo de captura da transação
		private $transactionError; //Registra o erro caso o mesmo exista;
		private $environment; // Configure o ambiente
		private $lojistaDados; //Dados do Lojista
		private $pagamento;
		private $basicData;

		function __construct($_enviroment='sandbox')
		{
			$this->environment = ($_enviroment=='sandbox') ? 'sandbox' : 'production' ;
		
			$this->lojistaDados = array(
				'nome'=>'',
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
				'PaymentMethod'=>'Boleto'
			);

		}

		public function setPagamento($pagamento){
			$this->pagamento = $pagamento;
		}

		public function makePayment(&$request)
		{
			$usuario = $request['pedido_resource']['usuario'];
			$boletoLojaInstallmentQuantity = (isset($request['boletoLojaInstallmentQuantity'])) ? (int)$request['boletoLojaInstallmentQuantity'] : 1;
			// $boletoLojaPrazo = (int)$usuario['boleto_prazo']; //Não aplicado
			// if ($boletoLojaPrazo<2) {
			$boletoLojaPrazo=2;
			// }
			$this->transaction = json_decode('{"parcelamento":"'.$boletoLojaInstallmentQuantity.'","prazo":"'.$boletoLojaPrazo.'"}');
			return $this;
		}

		/**
		 * Retorna os detalhes do tipo de pagamento escolhido, no caso de boleto é retornado o URL de impressão do documento.
		 * @return (String)
		 */
		public function getPaymentInfoResume()
		{
			$returnInfo = "Boleto Banc&aacute;rio";
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

			$returnInfo = '
						<strong>Tipo:</strong> Boleto Banc&aacute;rio <br/>
						<strong>Prazo de pagamento:</strong> '.$this->transaction->prazo.' dias &uacute;teis. <br/>
						' ;	
						// <strong>Parcelamento:</strong> '. $this->transaction->parcelamento .'x <br/> Não aplicado
			return $returnInfo;
		}

		public function getBasicData(){
			return $this->basicData;
		}

		public function getError(){
			return array('code'=>0,'message'=>'');
		}


		/**
		 * Retorna o código da transacao
		 * @return (String)
		 */
		public function getPaymentMethod(){
			return 'boleto';
		}

		/**
		 * Retorna o código da transacao
		 * @return (String)
		 */
		public function getTransactionCode(){
			return 0;
		}

		/**
		 * Retorna o código da autorizacao da transacao
		 * @return (String)
		 */
		public function getBoletoNumero(){
			return 0;
		}

		/**
		 * Retorna o código da transacao
		 * @return (String)
		 */
		public function getTransactionNumber(){
			return 0;
		}

		/**
		 * Retorna a situação da transação 1=SUCESSO, 0=FALHA.
		 * @return (Int)
		 */
		public function getTransactionStatus(){
			return 0;
		}

		public function getSerializedTransaction(){
			if (!is_null($this->transaction)) {
				return json_encode($this->transaction);
			}
			return "";
		}

		public function setSerializedTransaction($transaction){
			//Camel Case convertion
			try {
				$transaction = json_decode($transaction);
				if ($transaction!=null){
					$this->transaction = $transaction;
				}
			} catch (Exception $e) {
				//Do Nothing	
			}
		}

		public function validadeParams(&$request){
			//NÃO SE APLICA
		}

		public function validateInstallmentQuantity($quantity){
			//NÃO SE APLICA
		}

		

}
