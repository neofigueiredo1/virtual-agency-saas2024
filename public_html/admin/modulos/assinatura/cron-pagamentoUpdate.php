<?php
/*
* PROCESSA EM BACKGROUND OS RETORNOS DE ATUALIZAÇÃO DE PAGAMENTO DOS GATEWAYS
*/

// ini_set('display_errors', 1);
// ini_set('log_errors', 1);

date_default_timezone_set('America/Fortaleza');
header('Content-Type: text/html; charset=utf-8');

$rootPath = dirname(dirname(dirname(dirname(__FILE__))));

define("BASE_PATH",$rootPath);
define("PASTA_CONTENT","sitecontent");
define("PASTA_USUARIO","user-files");
define("PASTA_DIRECTIN","admin");
define('DS', DIRECTORY_SEPARATOR);

require_once $rootPath.DS.PASTA_DIRECTIN.DS."library".DS."bootstrap.php";
//Carrega as variáveis de ambiente do sistema.
require_once($rootPath.DS.PASTA_DIRECTIN.DS."library".DS."database".DS."connect.class.php");
require_once($rootPath.DS.PASTA_DIRECTIN.DS."library".DS."database".DS."handle-sql.class.php");

$env = new EnvLoad();
$env->loadVars();
Connect::loadVarsFromEnv();

include_once("views/classes/ecommerce.pagamento.class.php");

include_once("pedido-model.php");
include_once("pedido-control.php");


$time_start = Date::getMicrotimeFloat();

$ecomPagamento = new EcommercePagamento();

$semaforo=null;
$filepath = BASE_PATH . DS . PASTA_CONTENT . DS . 'ecommerce';
$filename = $filepath.DS."ecom_monitorPagamentoUpdate.txt";

//Implementação de Controle de Concorrencia para evitar a execução do repetida do Serviço a partir do Cron
$semaforoSampleSource = '{"sinal":0,"tentativas":0,"notificacao":0,"error":0,"error_notifica":0}';
//CARREGA O SEMAFORO
	try {

		if (file_exists($filename)) { //arquivo existe
			$semaforoTxtFile = fopen($filename, "r");
			$semaforoTxt = fread($semaforoTxtFile,filesize($filename));
			fclose($semaforoTxtFile);
			$semaforo = (trim($semaforoTxt)!="") ? json_decode($semaforoTxt) : $semaforo;
			if (!$semaforo) {
				//Caso a estrutura do json esteja corrompida, é recriada.
				$semaforo = json_decode($semaforoSampleSource);
			}
		}else{ //arquivo nao existe
			$semaforoTxtFile = fopen($filename, "w");
			fwrite($semaforoTxtFile,$semaforoSampleSource);
			fclose($semaforoTxtFile);
			$semaforo = json_decode($semaforoSampleSource);
		}

	} catch (Exception $e) {
		exit();
	}


	$semaforo->sinal=0;//força a seguir


	if ($semaforo->error>5) {

		$semaforo->sinal=0;
		$semaforo->tentativas=0;	
		$semaforo->notificacao=0;

		if ($semaforo->error_notifica==0) {
			mail("desenvolvimento@being.com.br","ASSIM-MONITOR 001","Erro ao processar, o serviço mesmo vai ficar pausado a té que o problema seja analisado. Consultar o log de erros.");
			$semaforo->error_notifica=1;
		}

		$semaforoTxt = json_encode($semaforo);
		$semaforoTxtFile = fopen($filename, "w");
		fwrite($semaforoTxtFile,$semaforoTxt);
		fclose($semaforoTxtFile);

	}


	//VALIDA SE SINAL VERDE
	if ($semaforo->sinal>0) {
		
		//Outro Processo em andamento.
		//Registra a tentativa.
		$semaforo->tentativas = (int)$semaforo->tentativas + 1;
		if ((int)$semaforo->tentativas>10 && $semaforo->notificacao==0) {
			//Envia uma notificação por email
			mail("desenvolvimento@being.com.br","ASSIM-MONITOR 001","Atingiu ".$semaforo->tentativas." tentativas.");
			$semaforo->notificacao=1;
		}

		if ((int)$semaforo->tentativas>30 && $semaforo->notificacao==1) {
			$semaforo->sinal=0;
			$semaforo->tentativas=0;	
			$semaforo->notificacao=0;
		}

		//Registra os novos dados no arquivo txt
		$semaforoTxt = json_encode($semaforo);
		$semaforoTxtFile = fopen($filename, "w");
		fwrite($semaforoTxtFile,$semaforoTxt);
		fclose($semaforoTxtFile);
		exit();

	}else{//Sinal Verde

		$semaforo->sinal=1;
		$semaforoTxt = json_encode($semaforo);
		$semaforoTxtFile = fopen($filename, "w");
		fwrite($semaforoTxtFile,$semaforoTxt);
		fclose($semaforoTxtFile);

	}


#Só notifica se sucesso na operação anterior.
try {
	
	$hasTransactionForUpdate = $ecomPagamento->loadTransactionForUpdate();
	if ($hasTransactionForUpdate) {
		$ecomPagamento->updatefromOriginAndNotify();
		switch ($ecomPagamento->gateway->getTransactionStatusCode()) {
			case 'paid':
				//Processa a inscrição do usuario nos cursos adquiridos para o pedido da transacao.
				$m_pedido = new pedido();
	 			$m_pedido->pedidoCursoProcessainscritos($ecomPagamento->getPedidoId());
				break;
			case 'canceled':
			case 'refunded':
				//Cancelamento pela IUGU
				//Recupera os detalhes do pedido.
				$m_pedido = new pedido();
				$pedidoAtual = $m_pedido->pedidoListSelected($ecomPagamento->getPedidoId());
				$m_pedido->pedidoNotificaCancelamento($ecomPagamento->getPedidoId(),$pedidoAtual);
				//Processa a remoção da inscrição do usuario em relação ao curso comprado no pedido.
				$m_pedido->userUnsubscribe($pedidoAtual['pedido'][0]['pedido_idx'],$pedidoAtual['pedido_itens'][0]['curso_idx'],$pedidoAtual['pedido'][0]['cadastro_idx']);
				break;
		}
		
	}

} catch (Exception $e) {
	$semaforo->error = (int)$semaforo->error + 1;
	$messageError = "[".date("Y-m-d H:i:s")."] ".$e->getMessage();
	$operacaoProdutoNotifica=false;
	// die($messageError);
	// exit();
	error_log($messageError, 3, "ecom_pagamento_update_error.log");
}

$semaforo->sinal=0;
$semaforoTxt = json_encode($semaforo);
$semaforoTxtFile = fopen($filename, "w");
fwrite($semaforoTxtFile,$semaforoTxt);
fclose($semaforoTxtFile);

$time_end = Date::getMicrotimeFloat();
$time = $time_end - $time_start;
echo $time . " segundos";

