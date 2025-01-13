<?php
include_once('config.php');

//Padrão de retorno para as requisições.
$dados_retorno = json_decode('{"error":"0","message":"","resource":"null"}');

// Run CSRF check, on POST data, in exception mode, with a validity of 10 minutes, in one-time mode.
// try{
//     NoCSRF::check('csrf_token',$_POST,true,60*10,false);
// }catch( Exception $e ){
//     var_dump($e->getMessage());
//     exit();
//     $dados_retorno->message = "Não foi possível completar sua solicitação, certifique-se de que a página carregou corretamente.";
//     if (ob_get_length()>0) ob_end_clean();
//     echo json_encode($dados_retorno);
//     exit();
// }

$ac = isset($_GET['ac']) ? Text::clean($_GET['ac']) : '' ;
$ac = isset($_POST['ac']) ? Text::clean($_POST['ac']) : $ac ;

switch($ac) {
    /**
    * Grava a resposta de uma questão do simulado.
    */
    case 'simuladoSaveAnswer':
       
        $simulado_id = isset($_POST['simulado_id']) ? (int)$_POST['simulado_id'] : 0 ;
        $questao_id = isset($_POST['questao_id']) ? (int)$_POST['questao_id'] : 0 ;
        $opcao_id = isset($_POST['opcao_id']) ? (int)$_POST['opcao_id'] : 0 ;
        $opcao_res = isset($_POST['opcao_res']) ? $_POST['opcao_res'] : 0 ;

        try {
            $m_simulado->respostaSalvaOpcao($simulado_id,$questao_id,$opcao_id,$opcao_res);
            $dados_retorno->error = 0;
            $dados_retorno->message = "sucesso";
        } catch (Exception $e) {
            $dados_retorno->error = 1;
            $dados_retorno->message = $e->getMessage();
        }

        break;
        
}

if (ob_get_length()>0) ob_end_clean();
echo json_encode($dados_retorno);
exit();

?>