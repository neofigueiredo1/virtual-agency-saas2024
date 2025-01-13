<?php
if (!isset($_SESSION['plataforma_usuario'])) {
    ob_clean();
    header("Location: /login-cadastro");
    exit();
}

// if ((int)$_SESSION['plataforma_usuario']['perfil']!=1) {
//     ob_clean();
//     header("Location: /minha-conta");
//     exit();
// }

    global $m_cadastro,$subAccountData;

    
    //Recupera o resumo da subconta na IUGU
    $produtorReg = $m_cadastro->getCadastro($_SESSION['plataforma_usuario']['id']);

    $ecomPagamento = new EcommercePagamento();

    $ecomPagamento->setPagamentoId(1);
    $ecomPagamento->setAccountId($produtorReg[0]['iugu_split_account_id']); //Identifica a subconta da transacao
    $ecomPagamento->registrarGateway('sandbox');
    $ecomPagamento->setTransacaoPerfil('bill');
    $ecomPagamento->gateway->setApiKey($produtorReg[0]['iugu_split_live_api_token'],$produtorReg[0]['iugu_split_test_api_token']);
    
    $subAccountData = $ecomPagamento->gateway->getAccountData($produtorReg[0]['iugu_split_account_id']);

?>