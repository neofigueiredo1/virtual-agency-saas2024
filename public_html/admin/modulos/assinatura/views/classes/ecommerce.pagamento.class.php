<?php

require_once('ecommerce.pagamento.iugu.class.php');

class EcommercePagamento extends HandleSql
{
    private $TB_PAGAMENTO;
    private $TB_PEDIDO;
    private $TB_PEDIDO_ITENS;
    private $TB_TRANSACAO;
    private $TB_TRANSACAO_PARCELA;

    private $TB_CADASTRO;
    private $TB_CURSO;

    public $gateway;

    private $accountId;

    private $pedidoId;
    private $pagamentoId;
    private $transactionCode;

    private $transacaoIdx;
    private $transacaoData;
    private $transacaoPerfil;
    private $pedidoIdSufix;

	function __construct(){
        parent::__construct();
        $this->TB_PAGAMENTO = self::getPrefix() . "_ecommerce_pagamento";
        $this->TB_PEDIDO = self::getPrefix() . "_ecommerce_pedido";
        $this->TB_PEDIDO_ITENS = self::getPrefix() . "_ecommerce_pedido_itens";
        $this->TB_CADASTRO = self::getPrefix() . "_cadastro";
        $this->TB_TRANSACAO = self::getPrefix() . "_ecommerce_pedido_pagamento_transacao";
        $this->TB_TRANSACAO_PARCELA = self::getPrefix() . "_ecommerce_pedido_pagamento_transacao_parcela";
        
        $this->TB_CADASTRO = self::getPrefix() . "_cadastro";
        $this->TB_CURSO = self::getPrefix() . "_curso";
        
	}

    public function setAccountId($accountId){
        $this->accountId = $accountId;
    }
    public function getAccountId(){
        return $this->accountId;
    }
    public function getAccountDataById(){
        return self::select("SELECT tbCad.* FROM ".$this->TB_CADASTRO." as tbCad Where iugu_split_account_id Like '".$this->accountId."' ");
    }

    public function setPedidoId($pedidoId){
        $this->pedidoId = $pedidoId;
    }
    public function getPedidoId(){
        return $this->pedidoId;
    }
    
    public function setTransactionCode($transactionCode){
        $this->transactionCode = $transactionCode;
    }
    public function getTransactionCode(){
        return $this->transactionCode;
    }

    public function setPagamentoId($pagamentoId){
        $this->pagamentoId = $pagamentoId;
    }
    public function setPedidoIdSufix($pedidoIdSufix){
        $this->pedidoIdSufix = $pedidoIdSufix;
    }
    public function getPedidoIdSufix(){
        return $this->pedidoIdSufix;
    }
    public function setTransacaoIdx($transacaoIdx){
        $this->transacaoIdx = $transacaoIdx;
    }
    public function getTransacaoIdx(){
        return $this->transacaoIdx;
    }
    public function setTransacaoPerfil($_perfil){
        $this->transacaoPerfil=$_perfil;
    }
    public function getTransacaoPerfil(){
        return $this->transacaoPerfil;
    }
    public function getTransacaoPerfilDescription($perfil)
    {
        $perfilDescricao = $perfil;
        switch ($perfil) {
            case 'bill':
                $perfilDescricao = 'Fatura';
                break;
            case 'subscription':
                $perfilDescricao = 'Assinatura';
                break;
        }
        return $perfilDescricao;
    }

    public function registrarGateway($environment){
        // $environment = 'sandbox'; //Força o ambiente de teste
        $environment = 'production'; //Força o ambiente de produção
        switch ($this->pagamentoId) {
            case 1: //Iugu
                $this->gateway = new EcommerceIuguPayment($environment);
                $this->gateway->setPagamento($this);
            break;
        }
    }

    public function gatewaySetCredentials(){
        //Cadastro com as credenciais definidas no banco.
        $userAccountData = self::getAccountDataById();
        $this->gateway->setCredentialsFromAccount($userAccountData);
    }

    public function registrarGatewayBySlug($environment,$slug){
        switch (trim($slug)) {
            case 'iugu': //IUGU
                $this->pagamentoId = 1;
            break;
        }
        if ((int)$this->pagamentoId!=0) {
            self::registrarGateway($environment);
        }
    }

    public function transactionPersist()
    {

        $transaction = $this->gateway->getTransaction();

        $parcelamento = $transaction->installments;
        $parcelamento = (!is_null($parcelamento))?(int)$parcelamento:1;

        $taxas = $transaction->taxes_paid_cents;    
        $taxas = (!is_null($taxas))?(float)(substr($taxas,0,strlen($taxas)-2).".".substr($taxas,-2)):0;

        $valor = $transaction->total_cents;    
        $valor = (!is_null($valor))?(float)(substr($valor,0,strlen($valor)-2).".".substr($valor,-2)):0;

        $comissao = $transaction->commission_cents;
        $comissao = (!is_null($comissao))?(float)(substr($comissao,0,strlen($comissao)-2).".".substr($comissao,-2)):0;

        $dadosTransacao = [
            'pedido_idx' => $this->pedidoId,
            'pagamento_idx' => $this->pagamentoId,
            'account_id' => $this->accountId,
            'comissao' => $comissao,
            'taxas' => $taxas,
            'metodo_pagamento' => $this->gateway->getPaymentMethod(),
            'parcelamento' => $parcelamento,
            'valor' => $valor,
            'transacao_source' => $this->gateway->getSerializedTransaction(),
            'transacao_status' => $this->gateway->getTransactionStatusCode(), //Retorna um status de 1=SUCESSO ou 0=FALHA
            'transacao_codigo' => $this->gateway->getTransactionId(),
            'transacao_boleto_numero' => $this->gateway->getBoletoNumero(),
            'update_from_origin' => 1,
            'data_processo' => date('Y-m-d')
        ];
        $transacaoId = parent::sqlCRUD($dadosTransacao, '', $this->TB_TRANSACAO, '', 'I', 0, 0);

        //Cria os registros de parcelamentos da transação caso cartão
        if ($this->gateway->getPaymentMethod()=='credit_card') {
            for ($i=1; $i <= $parcelamento; $i++) { 
                $dadosParcela = [
                    'transacao_idx' => $transacaoId,
                    'parcela' => $i,
                    'status' => 'pending',
                    'valor' => $valor,
                    'taxas' => 0,
                    'comissao_plataforma' => 0,
                ];
                parent::sqlCRUD($dadosParcela, '', $this->TB_TRANSACAO_PARCELA, '', 'I', 0, 0);
            }
        }

    }

    public function transactionUpdate()
    {
        $transaction = $this->gateway->getTransaction();


        $parcelamento = $transaction->installments;
        $parcelamento = (!is_null($parcelamento))?(int)$parcelamento:1;

        $taxas = $transaction->taxes_paid_cents;    
        $taxas = (!is_null($taxas))?(float)(substr($taxas,0,strlen($taxas)-2).".".substr($taxas,-2)):0;
        
        $valor = $transaction->total_cents;    
        $valor = (!is_null($valor))?(float)(substr($valor,0,strlen($valor)-2).".".substr($valor,-2)):0;

        $comissao = $transaction->commission_cents;
        $comissao = (!is_null($comissao))?(float)(substr($comissao,0,strlen($comissao)-2).".".substr($comissao,-2)):0;

        //Atualiza a transação no banco
        parent::update("UPDATE " . $this->TB_TRANSACAO . " SET
            transacao_source = '" . $this->gateway->getSerializedTransaction() . "',
            transacao_status = '" . $this->gateway->getTransactionStatusCode() . "',
            comissao = ".$comissao.",
            taxas = ".$taxas.",
            metodo_pagamento = '".$this->gateway->getPaymentMethod()."',
            parcelamento = ".$parcelamento.",
            valor = ".$valor.",
            update_from_origin=0
            WHERE pedido_idx=".(int)$this->pedidoId." And pagamento_idx=".(int)$this->pagamentoId." And transacao_codigo='" . $this->gateway->getTransactionId() . "' ");

        //Atualiza o status do pedido em caso de pagamento
        if ($this->gateway->getTransactionStatusCode()=='paid') { //Transação definida como pago.
            parent::update("UPDATE " . $this->TB_PEDIDO . " SET
            status=2,
            pagamento_status=1
            WHERE pedido_idx=".(int)$this->pedidoId." ");
        }
        //Atualiza o status do pedido em caso de cancelamento
        if ($this->gateway->getTransactionStatusCode()=='refunded') { //Transação definida como cancelada e pagamento devolvido.
            parent::update("UPDATE " . $this->TB_PEDIDO . " SET
            status=3,
            pagamento_status=2
            WHERE pedido_idx=".(int)$this->pedidoId." ");
        }
        //Atualiza o status do pedido em caso de cancelamento
        if ($this->gateway->getTransactionStatusCode()=='canceled') { //Transação definida como cancelada sem pagamento.
            parent::update("UPDATE " . $this->TB_PEDIDO . " SET
            status=3,
            pagamento_status=0
            WHERE pedido_idx=".(int)$this->pedidoId." ");
        }

    }

    public function loadTransaction()
    {

        $sqlDataQuery = array(
            'pedido_idx' => $this->pedidoId
        );
        
        if ((int)$this->transacaoIdx>0) {
            $sqlDataQuery['transacao_idx']=(int)$this->transacaoIdx;
        }

        if ($this->transacaoPerfil != '' && !is_null($this->transacaoPerfil)) {
            $sqlDataQuery['perfil'] = $this->transacaoPerfil;
        }

        $transaction = parent::sqlCRUD($sqlDataQuery, '', $this->TB_TRANSACAO, '', 'S', 0, 0);
        if (is_array($transaction)&&count($transaction)>0) {
            $this->transacaoPerfil = $transaction[0]['perfil'];
            $this->accountId = $transaction[0]['account_id'];
            $this->transacaoIdx = $transaction[0]['transacao_idx'];
            $this->transactionCode = $transaction[0]['transacao_codigo'];
            $this->gateway->setSerializedTransaction($transaction[0]['transacao_source']);
        }

    }

    public function transactionCancel(){
        try {
            $transactionCancelReturn = $this->gateway->cancel();
            //Atualiza o registro da transação de acordo com o cancelamento
            if($transactionCancelReturn->Error->Code==0) {
                parent::update("UPDATE " . $this->TB_TRANSACAO . " SET status='".$transactionCancelReturn->Status."' WHERE transacao_idx=" . (int)$this->transacaoIdx . " ");
                return true;
            }else{
                throw new Exception("Cod.:".$transactionCancelReturn->Error->Code." M.:".$transactionCancelReturn->Error->Message." ", 1);
            }
        }catch (Exception $e) {
            throw $e;
        }
    }

    public function loadTransactionForUpdate()
    { 
        $sqlQuery = "SELECT pagamento_idx,pedido_idx,account_id,transacao_source,transacao_codigo FROM ".$this->TB_TRANSACAO." WHERE update_from_origin=1 LIMIT 0,1 ";
        $transaction = parent::select($sqlQuery);
        if (is_array($transaction)&&count($transaction)>0) {
            $this->pagamentoId = $transaction[0]['pagamento_idx'];
            $this->pedidoId = $transaction[0]['pedido_idx'];
            $this->accountId = $transaction[0]['account_id'];
            $this->transactionCode = $transaction[0]['transacao_codigo'];
            self::registrarGateway('production');
            $this->gateway->setSerializedTransaction($transaction[0]['transacao_source']);
            return true;
        }
        return false;
    }

    public function loadTransactionById($tid)
    {
        $transaction = parent::select(array('pagamento_idx' => $this->pagamentoId, 'transacao_codigo'=>trim($tid)), '', $this->TB_TRANSACAO, '', 'S', 0, 0);
        if (is_array($transaction)&&count($transaction)>0) {
            $this->pedidoId = $transaction[0]['pedido_idx'];
            $this->accountId = $transaction[0]['account_id'];
            $this->transactionCode = $transaction[0]['transacao_codigo'];
            $this->gateway->setSerializedTransaction($transaction[0]['transacao_source']);
        }
    }

    public function removeTransaction()
    {
        parent::sqlCRUD(array('pedido_idx' => $this->pedidoId), '', $this->TB_TRANSACAO, '', 'D', 0, 0);
    }

    public function getBoletoNumeroProx($pagamento)
    {
        $boletoNumeroReg = parent::select("SELECT transacao_boleto_numero FROM " . $this->TB_TRANSACAO . " Where pagamento_idx=".(int)$pagamento." Order By transacao_boleto_numero DESC LIMIT 0,1 ");
        $boletoNumero=0;
        if (is_array($boletoNumeroReg)&&count($boletoNumeroReg)>0)
            $boletoNumero = (int)$boletoNumeroReg[0]['transacao_boleto_numero'];
        $boletoNumero++;

        return $boletoNumero;
    }

    public function getPaymentMethod()
    {
        return $this->gateway->getPaymentMethod();
    }

    public function getBasicData()
    {
        return $this->gateway->getBasicData();
    }

    /**
    * Verifica se o método de pagamento indicado está registrado no sistema.
    * @param $pagamento int -> código do pagamento
    * @return boolean
    */
    public function isValidMethod($pagamento){
        $pagamentoCheck = parent::select("SELECT pagamento_idx FROM " . $this->TB_PAGAMENTO . " WHERE pagamento_idx=".(int)$pagamento." ");
        return (is_array($pagamentoCheck)&&count($pagamentoCheck)>0);
    }

    public function calculaParcelaComJuros($valorTotal,$parcelas,$taxa)
    {
        // $taxa = $taxa/100.00;
        // $valParcela = $valorTotal * $taxa * pow((1+$taxa),$parcelas)/(pow((1+$taxa),$parcelas)-1);
        // return $valParcela;

        $taxa = $taxa/100;
        $valParcela = $valorTotal * pow((1 + $taxa), $parcelas);
        return ($valParcela/$parcelas);
    }

    public function updatefromOrigin(){
        //Define as credenciais no gateway
        self::gatewaySetCredentials();
        //Obtem os dados atualizados direto do gataway
        $this->gateway->updateFromOrigin();
        //Atualiza a transação no banco.
        self::transactionUpdate();
        //Atualiza os parcelamentos
        self::updateInstallments();
    }

    public function updatefromOriginAndNotify(){
        try{

            //Atualiza a transação.
            self::updatefromOrigin();

            // try {//Trata o envio do email de notificação ao operador.
                
            //     // $codigo_assinatura = str_pad($this->pedidoId, 9, "0", STR_PAD_LEFT);
            //     // $subject = "Atualização na assinatura - Nº " . $codigo_assinatura;

            //     // $message_body = Sis::returnMessageBody($subject);
            //     // $HTMLSource = $this->gateway->getPaymentInfoComplete();
            //     // $message_body = str_replace("[HTML_DADOS]", $HTMLSource, $message_body);

            //     // ob_clean();
            //     // echo $message_body;
            //     // exit();

            //     //Notifica o operador do site sobre a atualização.
            //     // $retorno = Sis::sendMail("", "", "", "", $subject, $message_body);
            //     // var_dump($retorno);

            // } catch (Exception $e) {
            //     //Do Nothing
            //     // die($e->getMessage());
            // }


        } catch (Exception $e) {
            throw $e;
        }
    }

    public function webhookNotification(){
        //Processa a requisição de notificação e retorna o ID da transação.
        $tid = $this->gateway->webhookNotification();
        //Marca o flag de atualização pelo serviço do Cron ou quando os detalhes do assinatura for acessado.
        if ($tid!==false) {
            $sqlQuery = "UPDATE ".$this->TB_TRANSACAO." SET update_from_origin=1 WHERE pagamento_idx=".(int)$this->pagamentoId." And transacao_codigo='".addslashes($tid)."' ";
            parent::update($sqlQuery);
        }
    }

    //Exclusivo para o perfil "Subscriptions"
    public function subscriptionGetRegisterBills()
    {
        $bills = $this->gateway->subscriptionGetAllBills();
        $perfil = 'bill';
        foreach ($bills as $key => $bill) {
            //Consulta o registro da transação.
            $checkBillExists = self::select("SELECT transacao_idx FROM $this->TB_TRANSACAO WHERE transacao_codigo like '".$bill['tid']."' And pedido_idx=".(int)$this->pedidoId." ");
            if (! (is_array($checkBillExists)&&count($checkBillExists)>0) ) {
                //Transacão da fatura gerada não existe no banco, e registramos ela.
                $dadosTransacao = [
                    'pedido_idx' => $this->pedidoId,
                    'pagamento_idx' => $this->pagamentoId,
                    'transacao_source' => $bill['transacao_source'],
                    'transacao_status' => $bill['status'], //Retorna a situacao da assinatura
                    'transacao_codigo' => $bill['tid'],
                    'perfil' => $perfil,
                    'data_processo' => date('Y-m-d')
                ];
                parent::sqlCRUD($dadosTransacao, '', $this->TB_TRANSACAO, '', 'I', 0, 0);
            }
        }
        
        
    }

    //Atualiza os parcelamentos da transacao carregada
    public function updateInstallments()
    {
        $gatewayTransaction = $this->gateway->getTransaction();

        if (!is_null($gatewayTransaction)) {
            if ($gatewayTransaction->payment_method=='iugu_credit_card') {
                
                if (isset($gatewayTransaction->financial_return_dates)) {
                    if (is_array($gatewayTransaction->financial_return_dates)) {
                        foreach ($gatewayTransaction->financial_return_dates as $key => $f_return_date) {
                            
                            $amount_cents = $f_return_date->amount_cents;
                            $amount_cents = (!is_null($amount_cents))?(float)(substr($amount_cents,0,strlen($amount_cents)-2).".".substr($amount_cents,-2)):0;

                            $taxes_cents = $f_return_date->taxes_cents;
                            $taxes_cents = (!is_null($taxes_cents))?(float)(substr($taxes_cents,0,strlen($taxes_cents)-2).".".substr($taxes_cents,-2)):0;

                            $return_date = (is_null($f_return_date->return_date))?0:date('Y-m-d H:i:s',strtotime($f_return_date->return_date));
                            $executed_date = (is_null($f_return_date->executed_date_iso))?$return_date:date('Y-m-d H:i:s',strtotime($f_return_date->executed_date_iso));


                            //Consulta se a parcela ja existe no banco para inserir ou atualizar a mesma.
                            $parcelaSearch = Array(
                                'transacao_idx' => $this->transacaoIdx,
                                'parcela' => (int)$f_return_date->installment
                            );
                            $parcelaReg = parent::sqlCRUD($parcelaSearch, '', $this->TB_TRANSACAO_PARCELA, '', 'S', 0, 0);
                            if (is_array($parcelaReg)&&count($parcelaReg)>0) {//parcela existe - atualiza
                                
                                $parcelaData = Array(
                                    'parcela_id' => $parcelaReg[0]['parcela_id'],
                                    'status' => $f_return_date->status,
                                    'valor' => $amount_cents,
                                    'taxas' => ($taxes_cents + (float)$f_return_date->advance_fee),
                                    'comissao_plataforma' => $f_return_date->commission,
                                    'data_pagamento' => $executed_date,
                                    'data_prevista' => $return_date

                                );
                                parent::sqlCRUD($parcelaData, '', $this->TB_TRANSACAO_PARCELA, '', 'U', 0, 0);

                            }else{// não existe - cria

                                $parcelaData = Array(
                                    'transacao_idx' => $this->transacaoIdx,
                                    'parcela' => (int)$f_return_date->installment,
                                    'status' => $f_return_date->status,
                                    'valor' => $amount_cents,
                                    'taxas' => ($taxes_cents + (float)$f_return_date->advance_fee) ,
                                    'comissao_plataforma' => $f_return_date->commission,
                                    'data_pagamento' => $executed_date,
                                    'data_prevista' => $return_date

                                );
                                parent::sqlCRUD($parcelaData, '', $this->TB_TRANSACAO_PARCELA, '', 'I', 0, 0);

                            }


                        }
                    }
                }

            }
        }
    }


    /**
     * Métodos para retornos da API.
     * */

    //USADO NA API - Obtem a lista completa de transacoes da plataforma, incluindo dados do pedido, curso e produtor
    public function getPaymentTransactions()
    {
        $strSQL = "
            SELECT tbTrs.*, tbCur.curso_idx, tbCur.nome as curso_nome, tbCad.cadastro_idx as produtor_id, tbCad.nome_completo as produtor_nome
            FROM ".$this->TB_TRANSACAO." as tbTrs
            INNER JOIN ".$this->TB_PEDIDO." as tbPed ON tbTrs.pedido_idx=tbPed.pedido_idx
            INNER JOIN ".$this->TB_PEDIDO_ITENS." as tbPedItem ON tbPed.pedido_idx=tbPedItem.pedido_idx
            INNER JOIN ".$this->TB_CURSO." as tbCur ON tbPedItem.curso_idx=tbCur.curso_idx
            INNER JOIN ".$this->TB_CADASTRO." as tbCad ON tbCur.produtor_idx=tbCad.cadastro_idx
        ";
        $transactionsData = self::select($strSQL);
        return $transactionsData;
    }

    //USADO NA API - Obtem a lista completa de transacoes com parcelamento registrados
    public function getPaymentTransactionsInstallments()
    {
        $strSQL = "
            SELECT tbTrs.*
            FROM ".$this->TB_TRANSACAO." as tbTrs
            INNER JOIN ".$this->TB_PEDIDO." as tbPed ON tbTrs.pedido_idx=tbPed.pedido_idx
            INNER JOIN ".$this->TB_PEDIDO_ITENS." as tbPedItem ON tbPed.pedido_idx=tbPedItem.pedido_idx
            INNER JOIN ".$this->TB_CURSO." as tbCur ON tbPedItem.curso_idx=tbCur.curso_idx
            INNER JOIN ".$this->TB_CADASTRO." as tbCad ON tbCur.produtor_idx=tbCad.cadastro_idx
        ";
        // WHERE tbTrs.parcelamento > 1
        $transactionsData = self::select($strSQL);
        return $transactionsData;
    }



}