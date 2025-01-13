<?php
global $vendasLista,$pg,$pagina,$pagina_uri,$ecomVendas,$cursosLista;
// $ecomPagamento = new EcommercePagamento();
// $ecomPagamento->setPagamentoId(1);
// $ecomPagamento->registrarGateway('sandbox');
?>
<?php if (is_array($cursosLista)&&count($cursosLista)>0): ?>
    <hr/>
    <div class="card mt-3" >
        
        <table class="table table-hover table-striped table_list" style="font-size:15px;"  >
            <thead>
                <tr>
                    <th>#</th>
                    <th>Cod. <br/> Pedido</th>
                    <th>Situação</th>
                    <th>Curso</th>
                    <th>Valor</th>
                    <th nowrap >Taxas da <br/> Financeira</th>
                    <th nowrap >Comissão da <br/> Plataforma</th>
                    <th>Saldo</th>
                    <th>Data</th>
                    <th>Detalhes</th>
                </tr>
            </thead>
            <?php
            foreach ($vendasLista as $i => $venda):
            $count = $i + 1;
            
                $statusPago = "Aguardando";
                $statusClass = "table-default";
                $taxa_iugu = (float)$venda['taxas'];

                switch ($venda['status']) {
                    case 2:
                        if ($venda['pagamento_status']) {
                            $statusClass = "success table-success";
                            $statusPago = "Pago";
                        }
                        break;
                    case 3:
                        $statusClass = "danger table-danger";
                        break;
                }
            ?>
            <tr class="<?php echo $statusClass; ?>" >
                <td>#<?php echo ($i+1); ?></td>
                <td><?php echo $venda["pedido_idx"]; ?></td>
                <td nowrap="" ><?php echo $venda["status_nome"]; ?><br><small>Pagamento: <?php echo $statusPago; ?></small></td>
                <td>#<?php echo $venda["curso_nome"]; ?></td>
                <td nowrap="">R$ <?php echo number_format($venda["totalPedido"]-$venda["desconto_valor"],2,",","."); ?></td>
                <td>
                    R$ <?php echo number_format($taxa_iugu,2,",","."); ?>
                </td>
                <td><?php echo $venda["plataforma_comissao"]; ?>%<br/>
                    <small>R$ <?php echo number_format($venda["totalComissaoPlataforma"],2,",","."); ?></small>
                </td>
                <td nowrap="" style="font-size:14px;"  >R$ <?php echo number_format($venda["totalPedido"]-$venda["desconto_valor"]-$venda["totalComissaoPlataforma"]-$taxa_iugu,2,",","."); ?></td>
                <td>
                    <?php echo date("d/m/Y H:i:s",strtotime($venda["data_cadastro"])); ?>
                </td>
                <td nowrap="" >
                    
                    <a title="Ocultar detalhes" href="javascript:;" onclick="javascript:ocultaVenda(<?php echo $venda['pedido_idx']; ?>);"
                      class="compra__flag-culta-mostra vendaDetalheHide<?php echo $venda['pedido_idx']; ?>" style="display:none;"
                    >Ocultar<i class="fas fa-chevron-up fs-12 ml-2"></i>
                    </a>
                    <a title="Detalhes" href="javascript:;" onclick="javascript:exibeVenda(<?php echo $venda['pedido_idx']; ?>);"
                      class="compra__flag-culta-mostra vendaDetalheShow<?php echo $venda['pedido_idx']; ?>"
                    > Exibir <i class="fas fa-chevron-down fs-12 ml-2"></i>
                    </a>

                </td>
            </tr>
            <tr>
                <td style="padding:0px;" colspan="9" ><div class="venda-detalhes" data-venda="<?php echo $venda['pedido_idx']; ?>" style="width:100%;" >
                  
                  <div id="box-venda-detalhe-<?php echo $venda['pedido_idx']; ?>" class="box-venda-detalhe box-venda-detalhe-temp loader p-4"  style="display: none;">
                    <div id="loader-<?php echo $venda['pedido_idx']; ?>" class="text-center" >
                      <img src="/assets/images/preloader.gif" alt="" width="100" /><br/>Aguarde, estamos carregando os detalhes da sua venda.
                    </div>
                    <div class="conteudo" ></div>
                  </div>
                
              </div></td>
            </tr>
            <?php endforeach ?>
        </table>
        <?php if(false): //(int)$vendasLista->totalPaginas > 1 ?>
        <div class="card-footer d-flex justify-content-center py-0" >
            <nav aria-label="Page navigation example" >
                <ul class="pagination">
                    <li class="page-item <?php echo ($pg<=1)?"disabled":""; ?>"><a class="page-link" href="<?php echo $pagina_uri .'/'. (($pg-1>0)?$pg-1:1) ; ?>"><span class="fa fa-chevron-left" ></span></a>
                </li>
                <li class="page-item" > p&aacute;gina <?php echo $pg; ?> de <?php echo $vendasLista->totalPaginas; ?></li>
                <li class="page-item <?php echo ($pg>=$vendasLista->{'totalPaginas'})?"disabled":""; ?>"><a class="page-link" href="<?php echo $pagina_uri .'/'. (($pg+1<$vendasLista->{'totalPaginas'})?$pg+1:$vendasLista->{'totalPaginas'}) ; ?>"><span class="fa fa-chevron-right" ></span></a></li>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
    <!-- /Paginação -->
    </div>    
<?php endif ?>