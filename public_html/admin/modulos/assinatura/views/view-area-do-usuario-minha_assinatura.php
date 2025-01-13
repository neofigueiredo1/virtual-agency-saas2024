<?php

    global $pedidos,$pg,$pagina,$pagina_uri,$ecomPedido;

    if(is_array($pedidos->{'resultado'}) && count($pedidos->{'resultado'}) > 0){?>
      <?php
      echo '<div class="row justify-content-end d-none d-lg-flex mx-lg-0 mx-md-4">
                  <div class="col-2">
                      <h2 class="compras__title">Data</h2>
                  </div>
                  <div class="col-3">
                      <h2 class="compras__title">Posição</h2>
                  </div>
                  <div class="col-2">
                      <h2 class="compras__title">Detalhes</h2>
                  </div>
              </div><div class="row d-flex d-lg-none">
                  <div class="col-md-12">
                          <h2 class="compras__title text-left">Dados do seu pedido</h2>
                          <hr class="my-4">
                  </div>
              </div>';

      // $requestUrlArr = explode("/", $_SERVER['REQUEST_URI']);
      // if(is_numeric(end($requestUrlArr))){ array_pop($requestUrlArr); }
      // $requestUrl = implode("/", $requestUrlArr);

      foreach ($pedidos->{'resultado'} as $key => $pedido) {
        
        $ped_status = $pedido['status_nome'];
        if(trim($pedido['status']) == 4){
          $ped_status = "Confirmado";
        }else if(trim($pedido['status']) == 0){
          $ped_status = "Aguardando";
        }
        else if(trim($pedido['status']) == 5){
          $ped_status = "Cancelado";
        }
        $paga_status = ($pedido['pagamento_status']==1) ? "Pago" : "Pagamento não identificado.";
        
        $desconto_valor=0;
        if ($pedido["cupom_idx"]!=0 || $pedido["afiliado_desconto"]!=0) {
          $desconto_valor = $pedido["desconto_valor"];
        }

        //Verifica o desconto no pedido.
        $boleto_desconto_valor=0;
        if ($pedido["pagamento_com_boleto"]==1 && $pedido["pagamento_com_boleto_desconto_valor"]>0) {
          $boleto_desconto_valor = $pedido["pagamento_com_boleto_desconto_valor"];
        }

      ?>
      <div class="compra <?php echo ($pedido['status']==4) ? "enviado" : ""; ?>" >
        <div class="row mx-0 align-items-top" data-toggle="collapse" data-target="#detalhes-pedido<?php echo $pedido['pedido_idx']; ?>" >
            <!-- Infos do Pedido -->
            <form action="/seu-carrinho" method="POST" name="repetir_pedido_<?php echo $pedido['pedido_idx']; ?>" >
              <input type="hidden" name="caction" value="4" />
              <input type="hidden" name="pedidoId" value="<?php echo $pedido['pedido_idx']; ?>" />
            </form>
            <div class="col-lg-5">
                <h3 class="compra__nome text-lg-left text-center">Pedido: <strong><?php echo $pedido['pedido_chave']; ?></strong></h3>
                <p class="compra__info text-lg-left text-center"><?php echo $pedido['itens_total_quantidade']; ?> item(ns) no pedido</p>
                <p class="compra__info text-lg-left text-center"><strong>Total: R$ <?php echo number_format(($pedido['itens_total_valor']-$desconto_valor) - $boleto_desconto_valor,2,",","."); ?></strong></p>
            </div>
            <div class="col-lg-2 text-center px-lg-2 px-0">
                <p class="mb-0 my-lg-0 my-2"><?php echo date("d/m/Y",strtotime($pedido['data_cadastro'])); ?></p>
            </div>
            <div class="col-lg-3 text-center px-lg-2 px-0">
              <span class="status-color <?php echo Text::friendlyUrl($ped_status);?>"></span><h3 class="d-inline-flex compra__status"><?php echo $ped_status; ?></h3>
              <br/><small><?php echo $paga_status; ?></small>
            </div>
            <div class="col-lg-2 text-center px-lg-2 px-0 d-flex mt-lg-0 mt-2 justify-content-center">
                <a title="Ocultar detalhes" href="javascript:;" onclick="javascript:ocultaMeuPedido(<?php echo $pedido['pedido_idx']; ?>);"
                  class="compra__flag-culta-mostra pedidoDetalheHide<?php echo $pedido['pedido_idx']; ?>" style="display:none;"
                >Ocultar<i class="fas fa-chevron-up fs-12 ml-2"></i>
                </a>
                <a title="Detalhes" href="javascript:;" onclick="javascript:exibeMeuPedido(<?php echo $pedido['pedido_idx']; ?>);"
                  class="compra__flag-culta-mostra pedidoDetalheShow<?php echo $pedido['pedido_idx']; ?>"
                > Exibir <i class="fas fa-chevron-down fs-12 ml-2"></i>
                </a>
            </div>
        </div> <!-- Infos do Pedido -->

        
          <div class="pedido-detalhes" data-pedido="<?php echo $pedido['pedido_idx']; ?>" style="width:100%;" >
              
              <div id="box-pedido-detalhe-<?php echo $pedido['pedido_idx']; ?>" class="box-pedido-detalhe box-pedido-detalhe-temp loader"  style="display: none;">
                <div id="loader-<?php echo $pedido['pedido_idx']; ?>" class="text-center" >
                  <img src="/assets/images/preloader.gif" alt="" width="150" /><br/>Aguarde, estamos carregando os detalhes do seu pedido.
                </div>
                <div class="conteudo" style="margin-top:25px;" >

                </div>
              </div>
            
          </div>
        </div>

      <?php
      }
      // echo '
      //     </tbody>
      //   </table></small>';
    }else{
      echo
      "
        <div class='alert alert-warning' style='display: block;'>
          Você ainda não possui nenhum pedido. <br />Navegue por nossa lista de produtos <a href='/lojas'>clicando aqui</a>
          <i class='fa fa-exclamation' ></i>
        </div>
      ";

    }

?>

<style>
  .usuario.interna .minhas-compras .compras-unit{
    padding: 25px 20px;
  }
</style>

<?php if($pedidos->{'totalPaginas'}>1): // ?>
  <nav>
    <div class="pagination-footer">
    <ul class="pagination text-default pagination-lg">
      <li class="<?php echo ($pg<=1)?"disabled":""; ?>" ><a href="<?php echo $pagina_uri ; ?>"  <?php echo ($pg<=1)?"disabled":""; ?> ><i class="glyphicon glyphicon-fast-backward"></i></a></li>
      <li class="<?php echo ($pg<=1)?"disabled":""; ?>" ><a href="<?php echo $pagina_uri .'/'. (($pg-1>0)?$pg-1:1) ; ?>" ><i class="glyphicon glyphicon-chevron-left"></i></a></li>
      <li class="<?php echo ($pg>=$solicitacoesLista->{'totalPaginas'})?"disabled":""; ?>"><a href="<?php echo $pagina_uri .'/'. (($pg+1<$solicitacoesLista->{'totalPaginas'})?$pg+1:$solicitacoesLista->{'totalPaginas'}) ; ?>" ><i class="glyphicon glyphicon-chevron-right"></i></a></li>
      <li class="<?php echo ($pg>=$solicitacoesLista->{'totalPaginas'})?"disabled":""; ?>"><a href="<?php echo $pagina_uri .'/'. $solicitacoesLista->{'totalPaginas'}; ?>" ><i class="glyphicon glyphicon-step-forward"></i></a></li>
    </ul>
    </div>
  </nav>
  <?php endif; ?>
  <!-- /Paginação -->

