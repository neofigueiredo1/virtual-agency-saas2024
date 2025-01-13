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

switch ($ac) {

    /**
    * Verifica o token de acesso da conta.
    */
    case 'ckToken':
        $accessTokenIsOk = $m_cadastro->checkAccessToken();
        if (!$accessTokenIsOk){
            die('forbided');
        }
        exit();
        
        break;

    

    /**
    * Cadastro na newsletter.
    */
    case 'newsRegister':
        // if($exe == 1){

        /**
        * Verifica os dados do formulário da Newsletter armazenando em um array
        */
        $array = array(
            'nome'    => isset($_POST['nome']) ? Text::clean($_POST['nome']) : '',
            'email'   => isset($_POST['email']) ? Text::clean($_POST['email']) : '',
            'status'  => 4,
            'area_interesse'=> isset($_POST['area_interesse']) ? Text::clean($_POST['area_interesse']) : '',
        );
        /**
        * Se o e-mail for válido, manda pra função que armazena os dados.
        */
        if($array['email'] != "" && Sis::isValidEmail($array['email']) && $array['nome'] != ""){
            try {
                $m_cadastro->newsletterSign($array);
                $dados_retorno->error = 0;
                $dados_retorno->message = "sucesso";
            } catch (Exception $e) {
                $dados_retorno->error = 1;
                $dados_retorno->message = $e->getMessage();
            }
        }else{
            //echo "E-mail inválido";
            $dados_retorno->error = 1;
            $dados_retorno->message = "E-mail inválido";
        }

        break;

    /**
    * Retorna as informacoes de endereço baseado no CEP.
    */
    case 'addressByCEP':
        // if($exe==2){

        $cep = (isset($_POST['cep'])) ? $_POST['cep'] : 0;
        $localUtil  = new LocalidadesUtil();
        $enderecoAchado = $localUtil->getAddressByCEP($cep);
        if ($enderecoAchado!==false) {
             echo $enderecoAchado->endereco."&".$enderecoAchado->bairro."&".$enderecoAchado->cidade."&".$enderecoAchado->uf;
        }else{
            echo "erro";
        }
        exit();

        //OLD//$data = file_get_contents('http://apps.widenet.com.br/busca-cep/api/cep/'.$cep.'.str');
        // $ch = curl_init('http://apps.widenet.com.br/busca-cep/api/cep/'.$cep.'.str');
        //         curl_setopt($ch, CURLOPT_HEADER, 0);
        //         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //         $data = curl_exec($ch);
        //         curl_close($ch);
        
        // var_dump($data);
        // if($data !== false){
        //     $stringArr = explode("&", $data);

        //     if(count($stringArr)>4){
        //         for ($i=1; $i < count($stringArr); $i++) {
        //             $stringArr2[] = explode("=", $stringArr[$i]);
        //         }
        //         $rua = ($stringArr2[0][0]=="address")?$stringArr2[0][1]:$stringArr2[4][1];
        //         $rua = explode("+-+", $rua);
        //         $rua = urldecode(str_replace("+", " ", $rua[0]));

        //         $estado = ($stringArr2[1][0]=="state")?$stringArr2[1][1]:$stringArr2[3][1];
        //         $estado = urldecode(str_replace("+", " ", $estado));

        //         $cidade = urldecode(str_replace("+", " ", $stringArr2[2][1]));

        //         $bairro = ($stringArr2[3][0]=="district")?$stringArr2[3][1]:$stringArr2[1][1];
        //         $bairro = urldecode(str_replace("+", " ", $bairro));
        //         echo $rua."&".$bairro."&".$cidade."&".$estado;
        //     }else{
        //         echo "erro";
        //     }

        // }else{
        //     echo "erro";
        // }
        // die();


        break;

    /**
    * Autentica o usuário no sistema.
    */
    case 'auth':
        try {
            $m_cadastro->login();
            $dados_retorno->error = 0;
            $dados_retorno->message = "sucesso";
        } catch (Exception $e) {
            $dados_retorno->error = 1;
            $dados_retorno->message = $e->getMessage();
        }
        break;

    /**
    * Recupera a senha.
    */
    case 'recoveryPass':
        try {
            $m_cadastro->geraCodigoNovaSenha();
            $dados_retorno->error = 0;
            $dados_retorno->message = "sucesso";
        } catch (Exception $e) {
            $dados_retorno->error = 1;
            $dados_retorno->message = $e->getMessage();
        }
        break;

    /**
    * Registra o novo cadastro.
    */
    case 'newRegister':
        try {
            $m_cadastro->cadastroInsertInicial();
            $dados_retorno->error = 0;
            $dados_retorno->message = "sucesso";
        } catch (Exception $e) {
            $dados_retorno->error = 1;
            $dados_retorno->message = $e->getMessage();
        }
        break;

    /**
    * Registra a nova senha através da recuperação de senha.
    */
    case 'recoveryPassSaveNew':
        try {
            $m_cadastro->salvaNovaSenha();
            $dados_retorno->error = 0;
            $dados_retorno->message = "sucesso";
        } catch (Exception $e) {
            $dados_retorno->error = 1;
            $dados_retorno->message = $e->getMessage();
        }
        break;
    
    /**
    * Confirma o cadastro.
    */
    case 'confirmRegister':
        try {
            $m_cadastro->confirmaCadastro();
            $dados_retorno->error = 0;
            $dados_retorno->message = "sucesso";
        } catch (Exception $e) {
            $dados_retorno->error = 1;
            $dados_retorno->message = $e->getMessage();
        }
        break;
    
    /**
    * Ativa a conta como Aluno da plataforma.
    */
    case 'activateAccount':
        
        if(!$m_cadastro->isLogged()) {
            exit(); 
        }
        if ($_SESSION['plataforma_usuario']['perfil']==1) {//Já é aluno CPOT.
            $dados_retorno->error = 1;
            $dados_retorno->message = 'Sua conta já está ativa como Aluno.';
        }
        
        $tid = isset($_POST['fatura']) ? Text::clean($_POST['fatura']) : '' ;
        $cpf_pagador = isset($_POST['cpf_pagador']) ? Text::getOnlyNumber($_POST['cpf_pagador']) : '' ;

        if (trim($tid)=='' || trim($cpf_pagador)=='') {
            $dados_retorno->error = 1;
            $dados_retorno->message = 'É requerido que seja informado o código da Fatura e o CPF do pagador.';
        }
        if ($dados_retorno->error==0) {
            try {
                $m_curso->activateAccountWithIugoPayment($tid,$cpf_pagador);
                $dados_retorno->message='Parabéns, sua conta foi ativada com sucesso. Seja bem-vindo ao curso CPOT!';
            } catch (Exception $e) {
                $dados_retorno->error = 1;
                $dados_retorno->message = $e->getMessage();
                if ($dados_retorno->message=='invoice: not found') {
                    $dados_retorno->message = 'A fatura informada não foi localizada. <br/> Por gentileza, certifique-se de que digitou seu identificador corretamente.';
                }
            }
        }

        break;


    case 'lpSaveData':
        
        if(!$m_cadastro->isLogged()) {
            exit();
        }

        $userData = $m_cadastro->getMyDataUserInfo();

        if ((int)$userData[0]['lp_active']==1) {
            try {
                $m_cadastro->updateLPData($_REQUEST);
            } catch (Exception $e) {
                $dados_retorno->error=1;
                $dados_retorno->message = $e->getMessage();
            }
        }
        break;

    case 'lpSaveUrl':
        
        if(!$m_cadastro->isLogged()) {
            exit();
        }

        $userData = $m_cadastro->getMyDataUserInfo();

        if ((int)$userData[0]['lp_active']==1) {
            try {
                $m_cadastro->updateLPURL($_REQUEST);
            } catch (Exception $e) {
                $dados_retorno->error=1;
                $dados_retorno->message = $e->getMessage();
            }
        }
        break;
}

if (ob_get_length()>0) ob_end_clean();
echo json_encode($dados_retorno);
exit();

?>