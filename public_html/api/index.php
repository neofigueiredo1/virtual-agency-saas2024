<?php
  require_once('../site/direct-includes/config.php');
  require_once('../admin/library/classes/restapi.class.php');
  $retorno = json_decode('{
                  "response_message": "success",
                  "response_body": null,
                  "response_status": "00",
                  "response_date": "'.date("d/m/Y H:i:s").'"
                }');
  $restAPI = new RestAPI();
  try{
    $retorno->response_body = $restAPI->execute();
  }catch (Exception $e){
    $retorno->response_message = "Falha ao processar.";
    $retorno->response_body = json_decode('{"error_message":"'.$e->getMessage().'"}');
    $retorno->response_status = "01";
  }
  header('Content-Type: application/json');
  echo json_encode($retorno);