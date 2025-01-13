<?php
  
    require_once("views/classes/ecommerce.pagamento.class.php");

    if(!Sis::checkPerm($directIn->MODULO_CODIGO.'-2')){
        Sis::setAlert('Você não tem acesso à este recurso!', 1, '/admin/');
    }

    $pid   = isset($_GET['pid']) ? (int)$_GET['pid'] : "";
    $pgs   = isset($_GET['pgs']) ? (int)$_GET['pgs'] : "";
    $ps   = isset($_GET['ps']) ? (int)$_GET['ps'] : "";

    $status = isset($_POST['status']) ? (int)$_POST['status'] : "";
    $p_action = isset($_POST['p_action']) ? (int)$_POST['p_action'] : 0;

   switch(round($p_action)){
      case 1 ://Atualiza o status do pedido
         $directIn->pedidoConfirmaPagamento($pid, $status);
         break;

      case 2 : //Salvar observações
         $directIn->pedidoSalvaObservacoes($pid);
         break;

      case 3 ://Salvar os dados da transação de acordo com o código TID
         $tid = isset($_POST['tid']) ? Text::clean($_POST['tid']) : "";
         $retorno = $directIn->setPedidoTransactionUpdate($tid);
         if (!$retorno) {
             Sis::setAlert('Não foi possível obter os dados da transação informada!', 1);
         }
         break;

      case 4 : //Salvar o codifo de rastreamento
         $codrast = isset($_POST['codrast']) ? Text::clean($_POST['codrast']) : "";
         if(trim($codrast)!="") {
             $directIn->pedidoSalvaCodigoRastreamento($pid,$codrast);
         }else{
             Sis::setAlert('O código de rastreamento deve ser informado!', 4, "?mod=" . $_GET['mod'] . "&pag=" . $_GET['pag'] . "&act=".$_GET['act']."&pid=".$_GET['pid']);
         }
         break;

      case 6 : //Registra os valores de repasse para os produtores
         $directIn->autorizarSolicitacaoDeRepasse($pid);
         break;
   }

    if ((int)$pid==0) {
      Sis::setAlert('O pedido informado não existe!', 4);
      exit();
    }

    $pedido_source = $directIn->pedidoListSelected($pid);

    if(!(is_array($pedido_source) && count($pedido_source) > 0))
    {
      Sis::setAlert('O pedido informado não existe!', 4);
      exit();
    }

    $pedido = $pedido_source['pedido'];
    $pedido_itens = $pedido_source['pedido_itens'];
    $cadastro = $pedido_source['cadastro'];

    $pagamentoInfo="";
    $ecomPagamento = new EcommercePagamento();
    try {
      
      $ecomPagamento->setPagamentoId($pedido[0]['pagamento_idx']);
      $ecomPagamento->registrarGateway('sandbox');
      $ecomPagamento->setPedidoId($pid);
      $ecomPagamento->loadTransaction();
      $ecomPagamento->updateInstallments();
      $pagamentoInfo = "<pre><code>".$ecomPagamento->gateway->getPaymentInfoComplete()."</code></pre>";

      
    } catch (Exception $e) {

      $pagamentoInfo = "Não foi possível obter os detalhes da transação. <br> <small>".$e->getMessage()."</small> ";

    }

?>

<ol class="breadcrumb">
   <li><a href="?mod=<?php echo $mod; ?>&pag=pedido" >E-commerce</a></li>
   <li><a href="?mod=<?php echo $mod; ?>&pag=pedido" >Pedidos</a></li>
   <li>Detalhes</li>
</ol>

<?php require_once("ecommerce-menu.php"); ?>

<hr />

<div class="alert alert-danger" id="error-box" style="display:none;"><i class="fa fa-exclamation-triangle"></i>&nbsp;&nbsp;Preencha todos os campos corretamente!</div>

<?php

         if(is_array($pedido)&&count($pedido)>0)
         {
            $pedido = $pedido[0];

            $valor_subtotal_itens = 0;
            // $valor_frete = $pedido["frete_valor"];
?>
         <div class="panel">
            <h1 style="margin:0px;padding:0px;"><?php echo $pedido['pedido_chave'] ?> &nbsp; <small>em <?php echo Date("d",strtotime($pedido['data_cadastro']))." de ".Date::getMonth(Date("m",strtotime($pedido['data_cadastro'])))." / ".Date("Y",strtotime($pedido['data_cadastro'])) ?></small></h1>
         </div>

         <div class="row">
            <!-- Dados do cliente -->
            
            <div class="col-md-6">
               <div class="panel panel-info">
                  <div class="panel-heading" >
                     <h3 class="panel-title">Detalhes do pedido</h3>
                  </div>
                  <div class="panel-body detalhes_pedido">
                     <span class='destaca' >Número do pedido:</span> <?php echo $pedido['pedido_chave'] ?><br>
                     <span class='destaca' >Data do pedido:</span> &nbsp; em <?php echo Date("d",strtotime($pedido['data_cadastro']))." de ".Date::getMonth(Date("m",strtotime($pedido['data_cadastro'])))." / ".Date("Y",strtotime($pedido['data_cadastro'])) ?><br>
                     <span class='destaca' >Situação do pedido: </span> <?php echo ($pedido['status']==0)?"Aguardando":$pedido['status_nome']; ?><br>
                     
                     <span class='destaca' >Gateway de pagamento: </span> <?php echo $pedido['forma_pagamento_nome']; ?><br/>
                     <span class='destaca' >Situação do pagamento:</span> 
                     <?php 
                        switch ($pedido['pagamento_status']) {
                           case 0:
                              echo "Aguardando confirmação";
                              break;
                           case 1:
                              echo "Pago";
                              break;
                           case 2:
                              echo "Reembolsada";
                              break;
                        }
                     ?>
                     <hr />
                     <a href="javascript:;" onclick="javascript:$('#gatewayDetalhes').slideToggle('fast');" ><strong>Visualizar os detalhes da transa&ccedil;&atilde;o no gateway.</strong></a>
                     <div id="gatewayDetalhes" style='padding:10px;background-color:#f0f0f0;margin-bottom:10px;display:none;' ><?php echo $pagamentoInfo; ?></div>
                     
                  </div>
               </div>
            </div>

            <div class="col-md-6">
               <div class="panel panel-default">
                  <div class="panel-heading" >
                     <h3 class="panel-title">Dados do cliente</h3>
                  </div>
                  <div class="panel-body">
                     <?php
                        $cadastro = $cadastro[0];
                        echo "
                        <h3 class='panel-title' >".$cadastro['nome_completo']."</h3>
                        ".$cadastro['cpf_cnpj']."<br>
                        ".$cadastro['email']."<br>
                        <a href='/admin/?mod=cadastro&pag=cadastro&act=view&id=".$cadastro['cadastro_idx']."' target='_blank' >mais informações</a>
                        <br><br>
                        <h3 class='panel-title' >Endereço de cadastro</h3>
                        ". $cadastro['endereco'] .", ". $cadastro['numero'] ." - ". $cadastro['complemento'] ."<br>
                        ". $cadastro['cep'] ." - ". $cadastro['bairro'] ." - ". $cadastro['cidade'] ."-". $cadastro['estado'] ."
                        ";
                     ?>
                  </div>
               </div>
            </div>

         </div><!-- /row -->

         
   <div class="panel panel-info">
      <div class="panel-heading" >
         <h3 class="panel-title">Cursos do pedido</h3>
      </div>
      <div class="panel-body">

         <table class="table table-striped">
            <thead>
               <tr>
                  <th width="50">#</th>
                  <th>Curso</th>
                  <th width="100" >Valor</th>
                  <th width="150" >Sub-total</th>
               </tr>
            </thead>
            <tbody>
               <?php
                  foreach ($pedido_itens as $key => $item) {

                    $subtotal_item = $item["item_valor"];
                    $valor_subtotal_itens += $subtotal_item;

                        echo '
                        <tr>
                           <td>'. ((!is_null($item['pimage']) && trim($item['pimage'])!="Null")?'<img src="http://'.$_SERVER["HTTP_HOST"].'/sitecontent/curso/curso/images/'.$item['pimage'].'" width="100" />':'')  .'</td>
                           <td>'.$item["pnome"].'</td>
                           <td width="100" >R$ '.number_format($item["item_valor"], 2, ',', '.').'</td>
                           <td width="100" >R$ '.number_format($subtotal_item, 2, ',', '.').'</td>
                        </tr>
                        ';
                  }
               ?>
            </tbody>
         </table>

         <table class="table table-striped pull-right" style="width:350px;" >
            <tbody>
               <tr>
                  <th width="200" >Sub-total</th><td width="150" >R$ <?php echo number_format($valor_subtotal_itens, 2, ',', '.'); ?></td>
               </tr>
               <?php 
               $desconto_valor = 0;
               if ($pedido['cupom_idx']!=0):
                  $desconto_valor = $pedido['desconto_valor'];
               ?>
                  <tr>
                     <th>Desconto</th><td>R$ -<?php echo number_format($desconto_valor, 2, ',', '.'); ?></td>
                  </tr>
               <?php endif ?>
               <?php 
               if ($pedido['afiliado_idx']!=0&&$pedido['desconto_valor']>0):
                  $desconto_valor = $pedido['desconto_valor'];
               ?>
                  <tr>
                     <th>Desconto (afiliado)</th><td>R$ -<?php echo number_format($desconto_valor, 2, ',', '.'); ?></td>
                  </tr>
               <?php endif ?>
               <?php 
               $boleto_desconto_valor = 0;
               if ($pedido["pagamento_com_boleto"]==1 && $pedido["pagamento_com_boleto_desconto_valor"]>0): 
                  $boleto_desconto_valor = $pedido['pagamento_com_boleto_desconto_valor'];
               ?>
                <tr>
                  <th>Desconto<br>(pagamento com boleto)</th><td>R$ -<?php echo number_format($boleto_desconto_valor, 2, ',', '.'); ?></td>
                </tr>
               <?php endif ?>
               <tr>
                  <th><h3>Total</h3></th><td><h3>R$ <?php echo number_format((($valor_subtotal_itens-$desconto_valor)-$boleto_desconto_valor), 2, ',', '.'); ?></h3></td>
               </tr>
            </tbody>
         </table>

         <?php if ($pedido['cupom_idx']!=0): 
            $cupom = $directIn->pedidoCupom($pedido['cupom_idx']);
         ?>
         <h3>Desconto através de cupom</h3>
         Código: <?php echo $cupom[0]['codigo']; ?><br />
         Tipo: <?php echo ($cupom[0]['tipo_desconto']==1)?'Percentual':'Valor'; ?><br />
         Valor: <?php echo ($cupom[0]['tipo_desconto']==1)?$cupom[0]['valor_desconto'] . '%':'R$' . number_format($cupom[0]['valor_desconto'],2); ?>
        <?php endif ?>

        <?php if ($pedido['afiliado_idx']!=0): ?>
           <h3>Venda através de afiliado</h3>
            Afiliado: <?php echo $pedido['afiliado_nome']; ?><br/>
            Codigo: <?php echo $pedido['afiliado_codigo']; ?><br/>
            Comissão de afiliado: <?php echo (float)$pedido['afiliado_comissao']; ?>%<br/>
           <?php if ((float)$pedido['afiliado_desconto']!=0 && $pedido['desconto_valor']!=0): ?>
               Desconto de afiliado: <?php echo (float)$pedido['afiliado_desconto']; ?>%
           <?php endif ?>
        <?php endif ?>
        
      </div>
   </div>



   <div class="row">
      <div class="col-md-6">

         <div class="panel panel-success" >
            <div class="panel-heading" >
               <h3 class="panel-title">Observações para o cliente</h3>
            </div>
            <div class="panel-body">
               <form action="<?php echo Sis::currPageUrl(); ?>" method="POST" enctype="multipart/form-data"name="form_dados" >
                  <input type="hidden" name="p_action" value="2">
                  <textarea name="observacoes" class="form-control pull-left" id="" style="width:100%; height:115px; margin-right:15px; margin-bottom:10px;" rows="5" ><?php echo $pedido['observacoes']; ?></textarea><Br />
                  <button type="submit" class="btn btn-primary pull-right" >Salvar</button>
               </form>
            </div>
         </div>
      </div>

      <div class="col-md-6">
         <div class="panel panel-warning">
            <div class="panel-heading" >
               <h3 class="panel-title">Ações</h3>
            </div>
            <div class="panel-body">
               
               <div id="mensagem_alerta" class="alert" style="display:none" >
                  <img src="/admin/library/images/ajax-spinner-3.gif" alt="" />
                  <i class="fa fa-check-circle pull-left" style='font-size:38px; display:block;width:32px;height:32px;display:none' ></i>
                  <div class="text" ></div>
               </div>

               <div class="clear-fix"></div>
               <!-- <hr /> -->

               <form action="" method="post" class="pull-left" >
                  <input type="hidden" name="p_action" value="1" >
                  <?php if($pedido['pagamento_status'] == 1){ ?>
                     <button type="submit" class="btn btn-primary btn-sm" disabled >Pagamento confirmado</button> &nbsp;
                  <?php }else{ ?>
                     <input type="hidden" name="status" value="2" >
                     <button type="submit" class="btn btn-primary btn-sm">Pagamento confirmado</button> &nbsp;
                  <?php } ?>
               </form>

               <?php if(false):?>
                  <form action="" method="post" class="pull-left">
                     <input type="hidden" name="p_action" value="1">
                     <?php if($pedido['status'] < 3 || $pedido['status'] == 6){ ?>
                        <input type="hidden" name="status" value="7">
                        <button type="submit" class="btn btn-primary btn-sm">Faturado na loja</button> &nbsp;
                     <?php }else{ ?>
                        <button type="submit" class="btn btn-primary btn-sm" disabled>Faturado na loja</button> &nbsp;
                     <?php } ?>
                  </form>
                  <form action="" method="post" class="pull-left">
                     <input type="hidden" name="p_action" value="1">
                     <?php if($pedido['status'] >= 3 && $pedido['status'] <= 5){ ?>
                        <button type="submit" class="btn btn-primary btn-sm" disabled>Enviado para entrega</button>
                     <?php }else{ ?>
                        <input type="hidden" name="status" value="3">
                        <button type="submit" class="btn btn-primary btn-sm">Enviado para entrega</button>
                     <?php } ?>
                  </form>
               <?php endif;?>

               <div class="clear-fix" ></div>
               <hr />
               <div class="clear-fix" ></div>

               <form action="" method="post" class="pull-left">
                  <input type="hidden" name="p_action" value="1">
                  <?php if($pedido['status'] == 2 || $pedido['status'] == 3){ ?>
                     <button type="submit" class="btn btn-success btn-lg" disabled >Confirmar pedido</button> &nbsp;
                  <?php }else{ ?>
                     <input type="hidden" name="status" value="4">
                     <button type="submit" class="btn btn-success btn-lg">Confirmar pedido</button> &nbsp;
                  <?php } ?>
               </form>

               <form action="" method="post" class="pull-left">
                  <input type="hidden" name="p_action" value="1">
                  <?php if($pedido['status'] == 3){ ?>
                     <button type="submit" class="btn btn-danger btn-lg" disabled >Cancelar pedido</button>
                  <?php }else{ ?>
                     <input type="hidden" name="status" value="3">
                     <button type="submit" class="btn btn-danger btn-lg">Cancelar pedido</button>
                  <?php } ?>
               </form>

               <div class="clear-fix"></div>
                <hr />
                <div class="clear-fix"></div>
                <?php if (false): ?>
                  <?php if ((int)$pedido['has_repasse']==0): ?>
                     <form action="" method="post" class="pull-left" >
                       <input type="hidden" name="p_action" value="6" >
                       <button type="submit" class="btn btn-success btn-lg" > <i class="fa fa-money" ></i> &nbsp; Criar registros de repasse do(s) produtore(s)</button>
                     </form>
                   <?php else: ?>
                     <form action="" method="post" class="pull-left" >
                       <button type="submit" class="btn btn-success btn-lg" disabled style="text-align: left;" >
                         <i class="fa fa-money" ></i> &nbsp; Registros de repasse já criados
                         <br/><small>Consulte a lista de repasses para mais informações.</small>
                       </button>
                     </form>
                   <?php endif ?>
                <?php endif ?>
                
            </div>
         </div>
      </div>
   </div><!-- /row -->

   <div class="clear"></div>

   <script>

      function  checkHost(callback){
         $("#mensagem_alerta .fa").removeClass('fa-times-circle');
         $("#mensagem_alerta .fa").addClass('fa-check-circle');
         $("#resumo_operacao").slideUp('fast');
         $("#mensagem_alerta img").css("display","block");
         $("#mensagem_alerta .fa").css("display","none");
         $("#n_progress").slideDown();
         $("#mensagem_alerta .text").html("Verificando conexão com o host local...");
         $("#mensagem_alerta").removeClass();
         $("#mensagem_alerta").addClass("alert alert-info");
         $("#mensagem_alerta").slideDown();

         $.ajax({
            type: 'POST',
            url: "/admin/modulos/ecommerce/sinc-exe.php?act=1",
            data: {},
            success: function(data){
               console.log(data);
               if(data=="1"){//Success
                  $("#mensagem_alerta img").css("display","none");
                  $("#mensagem_alerta .fa").css("display","block");
                  $("#mensagem_alerta").removeClass();
                  $("#mensagem_alerta").addClass("alert alert-success");
                  $("#mensagem_alerta .text").html("Host local está online! &nbsp;&nbsp; <small>Preparando informações para sincronização.</small>");
                  setTimeout(function(){
                   callback();
                  },3000);
               }else{ //Error
                  $("#mensagem_alerta").removeClass();
                  $("#mensagem_alerta").addClass("alert alert-danger");
                  $("#mensagem_alerta .text").html("<strong>Erro!</div><br>"+data);
               };
            },
            error: function(jqXHR,textStatus,errorThrown){
               console.log(jqXHR);
               console.log(textStatus);
               console.log(errorThrown);
               $("#mensagem_alerta img").css("display","none");
               $("#mensagem_alerta .fa").removeClass('fa-check-circle');
               $("#mensagem_alerta .fa").addClass('fa-times-circle');
               $("#mensagem_alerta .fa").css("display","block");
               $("#mensagem_alerta").removeClass();
               $("#mensagem_alerta").addClass("alert alert-danger");
               $("#mensagem_alerta .text").html("<strong>Erro!</div><br>"+errorThrown);
            }
         });
      }
      function  makeActionSalvaPedidoSync(){
         $("#mensagem_alerta .text").html("Processando solicitação, aguarde...");
         $("#mensagem_alerta .fa").removeClass('fa-times-circle');
         $("#mensagem_alerta .fa").addClass('fa-check-circle');
         $("#mensagem_alerta img").css("display","block");
         $("#mensagem_alerta .fa").css("display","none");
         $("#mensagem_alerta").removeClass();
         $("#mensagem_alerta").addClass("alert alert-info");
         $("#mensagem_alerta").slideDown();
         $.ajax({
            type: 'POST',
            url: "/admin/modulos/ecommerce/sinc-exe.php?act=5&pid=<?php echo $pedido['pedido_idx']; ?>",
            data: {},
            success: function(data){
               if (data!="falha") {
                  setTimeout(function(){
                     $("#mensagem_alerta img").css("display","none");
                     $("#mensagem_alerta .fa").css("display","block");
                     $("#mensagem_alerta").removeClass();
                     $("#mensagem_alerta").addClass("alert alert-success");
                     $("#mensagem_alerta .text").html("Dados registrados com sucesso!");
                     $("#mensagem_alerta").delay(5000).slideUp();
                     $("#resumo_operacao .panel-body").html(data);
                     $("#resumo_operacao").slideDown('slow');
                  },2000);
               }else{
                  $("#mensagem_alerta img").css("display","none");
                  $("#mensagem_alerta .fa").removeClass('fa-check-circle');
                  $("#mensagem_alerta .fa").addClass('fa-times-circle');
                  $("#mensagem_alerta").removeClass();
                  $("#mensagem_alerta").addClass("alert alert-danger");
                  $("#mensagem_alerta .text").html("Erro ao sincronizar os dados!");
                  $("#resumo_operacao .panel-body").html();
                  $("#resumo_operacao").slideDown('slow');
               };
            },
            error: function(jqXHR,textStatus,errorThrown){
               console.log(jqXHR);
               console.log(textStatus);
               console.log(errorThrown);
               $("#mensagem_alerta img").css("display","none");
               $("#mensagem_alerta .fa").removeClass('fa-check-circle');
               $("#mensagem_alerta .fa").addClass('fa-times-circle');
               $("#mensagem_alerta .fa").css("display","block");
               $("#mensagem_alerta").removeClass();
               $("#mensagem_alerta").addClass("alert alert-danger");
               $("#mensagem_alerta .text").html("<strong>Erro!</div><br>"+errorThrown);
            }
         });
      }

   </script>

<?php
   }
?>