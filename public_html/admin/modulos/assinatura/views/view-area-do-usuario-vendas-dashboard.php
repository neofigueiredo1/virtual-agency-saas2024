<?php 
global $ini_totais,$vendas,$pagamentoStatusText,$cursoId,$cursoText,$mensagem,$msgSession,$subAccountData,$cursosLista,$vencimentosFuturos;

// var_dump($vendas);
// exit();

$periodo = $_SESSION['filtros_ecommerce_vendas_s']['periodo'];
$pagamentoStatus = $_SESSION['filtros_ecommerce_vendas_s']['pagamento_status'];

$filtros_ecommerce_vendas_s = isset($_SESSION['filtros_ecommerce_vendas_s'])?$_SESSION['filtros_ecommerce_vendas_s']:array();

?>

<?php if (is_array($cursosLista)&&count($cursosLista)>0): ?>
	


	<?php
	   if (is_array($msgSession)) {
			$tipo="warning";
			switch($msgSession['tipo'])
			{
				case 1 : $tipo="warning";  $icone = "<i class='fa fa-exclamation-triangle'></i>&nbsp;&nbsp;";
				break;
				case 2 : $tipo="info";     $icone = "<i class='fa fa-info-circle'></i>&nbsp;&nbsp;";
				break;
				case 3 : $tipo="success";  $icone = "<i class='fa fa-check-circle-o'></i>&nbsp;&nbsp;";
				break;
				case 4 : $tipo="danger";   $icone = "<i class='fa fa-ban'></i>&nbsp;&nbsp;";
				break;
			}
	   	?>
	      <div style="display:block;" class="alert alert-<?php echo $tipo; ?>" >
	         <button onclick="javascript: $(this).parent().slideUp('fast');" type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
	         <?php echo $icone.str_replace("[EXIBE]","",$msgSession['mensagem']); ?>
	      </div>
	      <?php
	   }

	   //print_r($ini_totais);

		// balance => Saldo
		// balance_available_for_withdraw => saldo_disponível_para_retirada
		// commission_balance => saldo_comissão
		
		// balance_in_protest => balance_in_protest
		// protected_balance => saldo_protegido
		// payable_balance => saldo_a pagar
		// receivable_balance => saldo_a receber

		// volume_last_month => volume_último_mês
		// volume_this_month => volume_este_mês
		// taxes_paid_last_month => impostos_pagos_último_mês
		// taxes_paid_this_month => impostos_pagos_este_mês

	?>

	<div class="d-md-flex justify-content-between align-items-center mb-3" >
		<div class="my-3" >

			Filtros atuais&nbsp;
			<span class="badge badge-info">
				Per&iacute;odo
				de <?php echo date("d/m/Y",strtotime($periodo['ini'])); ?> 
				à <?php echo date("d/m/Y",strtotime($periodo['fim'])); ?>
			</span>
			<span class="badge badge-info">
				Situa&ccedil;&atilde;o das vendas: <?php echo $pagamentoStatusText; ?>
			</span>
			
		</div>

		<a href="javascript:;" class="btn btn-outline-azul w-auto btn-sm py-0 px-4 py-1 h-auto ml-md-3" style="font-size: 13px;" onclick="javascript:$('#vendas_filtros_form').slideToggle('fast');" >Visualizar os filtros de pesquisa</a>

	</div>

	<section id="vendas_filtros_form" style="display:none;" >
        <form  action="<?php echo Sis::currPageUrl(); ?>" method="post" enctype="multipart/form-data" class="form_limpa" name="form_limpa" >
          <input type="hidden" name="goFilter" value="1">
        </form>

        <div class="card mb-3" >
            <div class="card-header">
                <h5 class="card-title m-0">
                    Filtrar vendas
                </h5>
            </div>
            <div class="card-body">

                <form  action="<?php echo Sis::currPageUrl(); ?>" method="post" class="form_solicitacoes_filtro" name="form_solicitacoes_filtro">
                    <input type="hidden" name="goFilter" value="2">
          			<input type="hidden" name="por_periodo" value="1" />
          			<input type="hidden" name="curso_nome" value="" />
                    <div class="row">
                         <div class="col-md" >
                         	<label> Por situa&ccedil;&atilde;o </label><br/>
                            <select class="form-control h-auto fs-14" name="status" >
                                <option value="0" >Todos</option>
                                <option value="1" <?php echo ( (int)$pagamentoStatus==1 )?"selected":""; ?> >Pendentes</option>
                                <option value="2" <?php echo ( (int)$pagamentoStatus==2 )?"selected":""; ?> >Pagos</option>
                                <option value="3" <?php echo ( (int)$pagamentoStatus==3 )?"selected":""; ?> >Cancelados</option>
                            </select>
                        </div>
                        <div class="col-md" >
                        	<label> Por curso </label><br/>
                            <select class="form-control h-auto fs-14" name="curso"
                            	onchange="JavaScript:this.form.curso_nome.value=this.options[this.selectedIndex].text;" >
                                <option value="0" >Todos</option>
                                <?php foreach ($cursosLista  as $key => $_curso):
                                	$selected = ( (int)$_curso['curso_idx']==(int)$cursoId )?"selected":"";
                                ?>
                                	<option value="<?php echo $_curso['curso_idx'] ?>" <?php echo $selected; ?> ><?php echo $_curso['nome'] ?></option>	
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="col-md" >
                            
                                <label> Por período </label><br/>
                                <div class="periodo pull-left" style="display:block;margin:0px 0px 0px 0px;" >
                                    &nbsp; de <input type="date" class="form-control fs-14 px-2 h-auto mb-1"  id="data_de" name="data_de" style="display:inline-block;vertical-align:middle;width:150px;" placeholder="Data de:" value="<?php echo date("Y-m-d",strtotime($periodo['ini'])) ?>" >
                                    <br/>
                                    &nbsp;até <input type="date" class="form-control fs-14 px-2 h-auto"  id="data_ate" name="data_ate" style="display:inline-block;vertical-align:middle;width:150px;" placeholder="Data até:" value="<?php echo date("Y-m-d",strtotime($periodo['fim'])) ?>" >
                                </div>
                            
                        </div>
                        <div class="col-md text-right" >
                            <input type="submit" value="Pesquisar" class="btn btn-danger h-auto btn-sm fs-14 px-5 d-inline-block" data-loading-text="Carregando..." />
                        </div>
                    </div>
                </form>

                <div class="<?php echo (is_array($filtros_ecommerce_vendas_s)&&count($filtros_ecommerce_vendas_s)>0) ? '' : 's-hidden'; ?> list-group search-result" >
                    <?php echo (isset($_SESSION['filtros_ecommerce_vendas_s_texto']) && $_SESSION['filtros_ecommerce_vendas_s_texto']!="") ? $_SESSION['filtros_ecommerce_vendas_s_texto'] : ""; ?>
                </div>

            </div>

        </div>

    </section>


    <section class="bg-info text-white text-center fs-34 mb-3 " style="border-radius:16px;" >
		<div class="row">
			<div class="col p-3">
				<?php echo $cursoText; ?>
			</div>
		</div>
	</section>

	<section>
	    <!--Grid row-->
	    <div class="row">
	        
	        <?php if (false): ?>
		        <!--Grid column-->
				<div class="col-md-3 mb-2 px-1">
					<!-- Card -->
					<div class="card bg-info branco h-100">
						<div class="card-body">
							<p class="text-uppercase small mb-2"><strong>Total de vendas</strong></p>
							<h5 class="font-weight-bold fs-32 text-nowrap mb-0" >
								<?php echo $ini_totais[0]['totalVendas']; ?>
							</h5>
							<small>
								Total de vendas para os filtros atuais.
							</small>
						</div>
					</div>
					<!-- Card -->
				</div>
				<!--Grid column-->
	        <?php endif ?>

	         <!--Grid column-->
			<div class="col-md-3 mb-2"  >
				<!-- Card -->
				<div class="card bg-info branco h-100" style="border-radius:16px;" >
					<div class="card-body d-flex align-items-center justify-content-center" >
						
						<div class="text-center" >
							<p class="text-uppercase small mb-2 fs-22" ><strong>Valor Líquido</strong></p>
							<h5 class="font-weight-bold fs-32 text-nowrap mb-0" >
								R$ <?php
								$valor_liquido_result = ((float)$ini_totais[0]['valorTotalVendasPago']-(float)$ini_totais[0]['valorTotalVendasDesconto']-(float)$ini_totais[0]['valorComissaoPlataforma']-(float)$ini_totais[0]['valorTaxasFinan'])+(float)$ini_totais[0]['valorTotalVendasAguardando']+(float)$ini_totais[0]['valorTotalVendasAguardandoParcelamentos'];
								if ($valor_liquido_result<0){
									$valor_liquido_result=0;
								}
								echo number_format($valor_liquido_result,2,',','.'); ?>
							</h5>
							<!-- <small>
								Total líquido para os filtros atuais.
							</small> -->
						</div>

					</div>
				</div>
				<!-- Card -->
			</div>
			<!--Grid column-->

			<!--Grid column-->
			<div class="col-md-3 mb-2">
				<!-- Card -->
				<div class="card bg-info branco h-100" style="border-radius:16px;" >
					<div class="card-body d-flex align-items-center justify-content-center">
						<div class="text-center" >
							<p class="text-uppercase small mb-2 fs-22"><strong>Valor recebido</strong></p>
							<h5 class="font-weight-bold fs-32 text-nowrap mb-0" >
								R$ <?php
									$valorTotalVendasPagoFinal = ((float)$ini_totais[0]['valorTotalVendasPago']<=0)?0: (float)$ini_totais[0]['valorTotalVendasPago']-(float)$ini_totais[0]['valorTotalVendasDesconto']-(float)$ini_totais[0]['valorComissaoPlataforma']-(float)$ini_totais[0]['valorTaxasFinan'];
									echo number_format($valorTotalVendasPagoFinal,2,',','.'); 
								?>
							</h5>
							<!-- <small>
								<?php echo $ini_totais[0]['totalVendasPago'] ?> Pedido(s) pagos
							</small> -->
						</div>
					</div>
				</div>
				<!-- Card -->
			</div>
			<!--Grid column-->

			<!--Grid column-->
	        <div class="col-md-3 mb-2" >
	            <!-- Card -->
	            <div class="card bg-info branco h-100" style="border-radius:16px;" >
						<div class="card-body d-flex align-items-center justify-content-center">
							<div class="text-center" >
								<p class="small mb-2 fs-22" style="line-height:100%;" >
									<strong class="text-uppercase" >Valor receber</strong><br>
									<small class="fs-14" >taxas ainda não aplicadas</small>
								</p>
								<h5 class="font-weight-bold fs-32 text-nowrap m-0" >
									R$ <?php echo number_format($ini_totais[0]['valorTotalVendasAguardando']+$ini_totais[0]['valorTotalVendasAguardandoParcelamentos'],2,',','.'); ?>
								</h5>
								<!-- <small>
									<?php echo $ini_totais[0]['totalVendasAguardando'] ?> Pedido(s) a receber
								</small> -->
							</div>
						</div>
	            </div>
	            <!-- Card -->
	        </div>
	        <!--Grid column-->

	        <!--Grid column-->
	        <div class="col-md-3 mb-2" >
	            <!-- Card -->
	            <div class="card bg-success branco h-100" style="border-radius:16px;" >
						<div class="card-body">
							<!-- <p class="text-uppercase small mb-0">
								<strong>Vencimentos futuros</strong>
							</p> -->
							<?php if (is_array($vencimentosFuturos)&&count($vencimentosFuturos)>0): ?>
								<div class="lista_vencimentos s_a_vencer_items owl-carousel owl-theme" style="width:200px;" >
									<?php foreach ($vencimentosFuturos as $key => $vencimentoFuturo): ?>
									<div class="font-weight-bold fs-32 text-nowrap m-0 text-center" >
										<div class="fs-22" style="line-height:100%" >
											Valores do mês
											<div class="fs-14" style="font-weight:normal;" >
												<?php echo Date::getMonth($vencimentoFuturo['mes']) ?>/<?php echo $vencimentoFuturo['ano'] ?>
											</div>
										</div>
										R$ <?php echo number_format($vencimentoFuturo['totalPrevisto'],2,',','.'); ?>
									</div>
									<?php endforeach ?>
								</div>
							<?php else: ?>
								<h5 class="font-weight-bold fs-32 text-nowrap m-0" >
									R$ 0,00
								</h5>
							<?php endif ?>
							<!-- <small>
								Parcelamentos de cart&atilde;o a vencer.
							</small> -->
						</div>
	            </div>
	            <!-- Card -->
	        </div>
	        <!--Grid column-->

	    </div>
	    <!--Grid row-->
	</section>

	<?php if (false): ?>
		<section>
			<h5 class="my-3" >
				Informa&ccedil;&otilde;es gerais das vendas<br/>
				<small>As informa&ccedil;&otilde;es a seguir n&atilde;o tem os descontos relacioados a comissao da plataforma e taxas da financeira.</small>
			</h5>

		    <!--Grid row-->
		    <div class="row">
		        <!--Grid column-->
		        <div class="col-md mb-2 px-1" >
		            <!-- Card -->
		            <div class="card">
							<div class="card-body">
								<div id="totalVendasChart" style="width: 100%; height: 200px;" ></div>
								<p class="text-uppercase text-muted small mb-2">
									<strong>Todos</strong>
								</p>
								<h5 class="font-weight-bold fs-32 text-nowrap m-0" >
									R$ <?php echo number_format($ini_totais[0]['valorTotalVendas'],2,',','.'); ?> 
								</h5>
								<small>
									<?php echo $ini_totais[0]['totalVendas'] ?> Pedido(s)
								</small>
							</div>
		            </div>
		            <!-- Card -->
		        </div>
		        <!--Grid column-->
		        <!--Grid column-->
		        <div class="col-md mb-2 px-1" >
		            <!-- Card -->
		            <div class="card">
							<div class="card-body">
								<div id="totalVendasPagoChart" style="width: 100%; height: 200px;"></div>
								<p class="text-uppercase text-muted small mb-2">
									<strong>Pagos</strong>
								</p>
								<h5 class="font-weight-bold fs-32 text-nowrap m-0" >
									R$ <?php echo number_format($ini_totais[0]['valorTotalVendasPago'],2,',','.'); ?> 
								</h5>
								<small>
									<?php echo $ini_totais[0]['totalVendasPago'] ?> Pedido(s)
								</small>
							</div>
		            </div>
		            <!-- Card -->
		        </div>
		        <!--Grid column-->
		        <!--Grid column-->
		        <div class="col-md mb-2 px-1">
		            <!-- Card -->
		            <div class="card">
		                <div class="card-body">
		                	  <div id="totalVendasAguardandoChart" style="width: 100%; height: 200px;"></div>
		                    <p class="text-uppercase text-muted small mb-2"><strong>Pendente de pagamento</strong></p>
		                    <h5 class="font-weight-bold fs-32 text-nowrap mb-0">
		                    		R$ <?php echo number_format($ini_totais[0]['valorTotalVendasAguardando'],2,",","."); ?>
		                    </h5>
		                    <small><?php echo $ini_totais[0]['totalVendasAguardando']; ?> Pedidos</small>
		                </div>
		            </div>
		            <!-- Card -->
		        </div>
		        <!--Grid column-->
					<!--Grid column-->
					<div class="col-md mb-2 px-1">
						<!-- Card -->
						<div class="card">
							<div class="card-body">
								<div id="totalVendasCanceladaChart" style="width: 100%; height: 200px;"></div>
								<p class="text-uppercase text-muted small mb-2"><strong>Cancelado</strong></p>
								<h5 class="font-weight-bold fs-32 text-nowrap mb-0">
								R$ <?php echo number_format($ini_totais[0]['valorTotalVendasCancelada'],2,",","."); ?>
								</h5>
								<small>
									<?php echo $ini_totais[0]['totalVendasCancelada']; ?> Pedidos(s)
								</small>
							</div>
						</div>
						<!-- Card -->
					</div>
					<!--Grid column-->

		    </div>
		    <!--Grid row-->
		</section>
	<?php endif ?>

	<div class="panel panel-default mb-2 " >
		<div class="list-group search-form" >
			<div class="list-group-item " >
				<div id="chart_datasPeriodo" ></div>
			</div>
		</div>
	</div>

	<?php if (is_array($vencimentosFuturos)&&count($vencimentosFuturos)>0): ?>
		<div class="panel panel-default mb-2" >
			<div class="list-group search-form" >
				<div class="list-group-item " >
					<div id="chart_datasFuturas" ></div>
				</div>
			</div>
		</div>
	<?php endif; ?>
	
	

	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<script>
	  	google.charts.load('current', {packages: ['corechart', 'line'], language: 'pt-BR'});
		google.charts.setOnLoadCallback(drawGraphics);

		function drawGraphics() {

			/*_datasPeriodo*/
		      var data_datasPeriodo = new google.visualization.DataTable();
			      data_datasPeriodo.addColumn('date', 'Data');
			      data_datasPeriodo.addColumn('number', 'Pedidos');

				  data_datasPeriodo.addRows([<?php 
			      	foreach ($vendas as $key => $aData) {
			      		$virgula = (($key>0)?",":"");
			      		if ($key==0 && strtotime($periodo['ini']) != strtotime($aData['dataRetorno']) ){
			      			 echo "[new Date(".date("Y",strtotime($periodo['ini'])).", ".(date("m",strtotime($periodo['ini']))-1).", ".date("d",strtotime($periodo['ini']))."), 0],";
			      		}
			      		echo $virgula . "[new Date(".date("Y",strtotime($aData['dataRetorno'])).", ".(date("m",strtotime($aData['dataRetorno']))-1).", ".date("d",strtotime($aData['dataRetorno']))."), ".$aData['total']."]";
			      	}
			      	if (strtotime($periodo['fim']) != strtotime($vendas[count($vendas)-1]['dataRetorno']) ) {
						echo ",[new Date(".date("Y",strtotime($periodo['fim'])).", ".(date("m",strtotime($periodo['fim']))-1).", ".date("d",strtotime($periodo['fim']))."), 0],";
			      	}
			      	?>]);

				      var options_datasPeriodo = {
				        hAxis: {
				          title: 'Período'
				        },
				        vAxis: {
				          title: 'Pedidos por dia'
				        }
				      };
		      		var chart_datasPeriodo = new google.visualization.LineChart(document.getElementById('chart_datasPeriodo'));
		      			chart_datasPeriodo.draw(data_datasPeriodo, options_datasPeriodo);

		      	;

		    }
	</script>
	
	<?php if (is_array($vencimentosFuturos)&&count($vencimentosFuturos)>0): ?>
		<script>
		  	google.charts.load('current', {packages:['corechart'], language:'pt-BR'});
			google.charts.setOnLoadCallback(drawGraphics);

			function drawGraphics() {

				var data = google.visualization.arrayToDataTable([
					['Data', 'Valor (R$)', { role: 'annotation' }]
					<?php foreach ($vencimentosFuturos as $key => $vencimentoFuturo): ?>
						,['<?php echo Date::getMonth($vencimentoFuturo['mes']) ?>/<?php echo $vencimentoFuturo['ano'] ?>', <?php echo number_format($vencimentoFuturo['totalPrevisto'],2,'.',''); ?>, 'R$ <?php echo number_format($vencimentoFuturo['totalPrevisto'],2,'.',''); ?>' ]
					<?php endforeach ?>
		        ]);

				var options = {
					title: 'Vencimentos Futuros',
					curveType: 'function',
					legend: { position: 'bottom' }
				};

		        var chart = new google.visualization.BarChart(document.getElementById('chart_datasFuturas'));
		        	chart.draw(data, options);

		    }
		</script>
	<?php endif; ?>

	<script type="text/javascript">
		google.charts.load("current", {packages:["corechart"]});
		google.charts.setOnLoadCallback(drawChartA);
		function drawChartA() {
	  
		  var options = {
			title: '',
			pieHole: 0.6,
			legend: 'none',
			pieSliceText: 'none',
			tooltip: { trigger: 'none' },
			slices: {
				0: { color: '#006699' },
				1: { color: '#eeeeee' }
			}
		  };


		  /*totalVendasChart*/
		     var data = google.visualization.arrayToDataTable([
				['Vendas','Vendas'],
				['',100],
				['',0]
		     ]);
		     var chart = new google.visualization.PieChart(document.getElementById('totalVendasChart'));
		 		chart.draw(data, options);


		 /*totalRepasseGeralChart*/
		 	var data = google.visualization.arrayToDataTable([
				['Pagos','Pagos'],
				['',<?php echo (100/($ini_totais[0]['totalVendas'])) * $ini_totais[0]['totalVendasPago']; ?>],
				['',<?php echo (100 - (100/($ini_totais[0]['totalVendas'])) * $ini_totais[0]['totalVendasPago']); ?>]
		     ]);
		     var chart = new google.visualization.PieChart(document.getElementById('totalVendasPagoChart'));
		 		chart.draw(data, options);


		 /*totalVendasAguardandoChart*/
		 	var data = google.visualization.arrayToDataTable([
				['Aguardando','Aguardando'],
				['',<?php echo (100/($ini_totais[0]['totalVendas'])) * $ini_totais[0]['totalVendasAguardando']; ?>],
				['',<?php echo (100 - (100/($ini_totais[0]['totalVendas'])) * $ini_totais[0]['totalVendasAguardando']) ; ?>]
		     ]);
		     var chart = new google.visualization.PieChart(document.getElementById('totalVendasAguardandoChart'));
		 		chart.draw(data, options);


		 /*totalVendasCanceladaChart*/
		 	var data = google.visualization.arrayToDataTable([
					['Aguardando','Aguardando'],
					['',<?php echo (100/($ini_totais[0]['totalVendas'])) * $ini_totais[0]['totalVendasCancelada']; ?>],
					['',<?php echo (100 - (100/($ini_totais[0]['totalVendas'])) * $ini_totais[0]['totalVendasCancelada']) ; ?>]
				]);
				var chart = new google.visualization.PieChart(document.getElementById('totalVendasCanceladaChart'));
					chart.draw(data, options);

		}
	</script>


<?php else: ?>


	<section class="wrapper wrapper-1260 p-0" >
		<div class="cotainer" >

			<a href="/minha-conta/cadastro-de-produtos-novo">
				<figure class="d-none d-md-block" >
					<img src="/assets/images/novos-produtores-desktop.png" border="0" class="img-fluid" />
				</figure>
				<figure class="d-bock d-md-none" >
					<img src="/assets/images/novos-produtores-mobile.png" border="0" class="img-fluid" />
				</figure>
			</a>

		</div>
	</section>


<?php endif ?>