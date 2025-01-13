<?php
require_once('../site/direct-includes/config.php');

//Estrutura da URI
//telemedicina/webhook/origem
$uriParts = explode("?",$pagina_uri);
$uriParts2 = explode("/",$uriParts[0]);
$origem = isset($uriParts2[2])?trim($uriParts2[2]):'';

switch ($origem) {
    case 'iugu': //Gateway de pagamento
        try{
            $ecomPagamento = new EcommercePagamento();
            $ecomPagamento->registrarGatewayBySlug('production',$origem);
            $ecomPagamento->webhookNotification($_REQUEST);
        }catch (Exception $e){
            error_log($origem . ": ".$e->getMessage(), 3, "webhook-iugu-errors.log");
        }
        break;
    default:
        // code...
        break;
}

?>