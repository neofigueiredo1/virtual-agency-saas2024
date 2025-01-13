<?php
global $ecomCarrinho,$car_session,$car_itens;
global $car_subtotal,$pedido_itens_total,$desconto_valor;
?>

<?php if(is_array($car_session->{'cursos'})&&count($car_session->{'cursos'})>0): ?>
	
	<section class="checkout container carrinho-checkout" >

		<?php if (isset($_SESSION['ecommerce_cupom_error'])): ?>
			<div id="alertMessage" class="alert alert-info text-center mt-20" >
				<a href="javascript:$('#alertMessage').slideUp();" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
				<?php echo $_SESSION['ecommerce_cupom_error']; ?>
			</div>
		<?php
			unset($_SESSION['ecommerce_cupom_error']);
		 endif 
		?>

		<?php if (isset($_SESSION['platform_filiado_error'])): ?>
			<div id="alertMessage" class="alert alert-info text-center mt-20" >
				<a href="javascript:$('#alertMessage').slideUp();" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
				<?php echo $_SESSION['platform_filiado_error']; ?>
			</div>
		<?php
			unset($_SESSION['platform_filiado_error']);
		 endif 
		?>

		
		<?php if (count($car_session->{'cursos'})>1): ?>
	    <div class="alert alert-warning" >
	    	S&oacute; ser&aacute; poss&iacute;vel parcelar pela quantidade de vezes que o curso que tenha a menor quantidade de parcelas dispon&iacute;veis.
	    </div>	
	    <?php endif ?>

		<div class="checkout__infos">
			<div class="container" >
				<div class="row info-titles d-block d-md-none"> 
	                <div class="col-md-12"> 
	                    <h2 class="info-title m-0 px-0">Cursos Adicionados</h2> 
	                </div> 
	            </div>
	            <div class="row info-titles d-md-flex d-none">
	                <div class="col-md-8">
	                    <h2 class="info-title">Curso</h2>
	                </div>
	                <div class="col-md-2">
	                    <h2 class="info-title text-center">Subtotal</h2>
	                </div>
	            </div>

	            <?php
				if (is_array($car_itens)&&count($car_itens)>0): ?>
					<?php
					$car_subtotal = 0;
					$counter = 0;
					foreach ($car_itens as $key => $car_item):
						$car_item_session = $ecomCarrinho->carItemExisteSession(round($car_item['curso_idx']));
						$valor = (is_object($car_item_session)) ? $car_item_session->{'valor'} : 0 ;
						$car_subtotal += $valor;
					?>

					<form action="/seu-carrinho" name="frmEditCar<?php echo $counter; ?>" method="post" >
						<input type="hidden" name="cursoId" value="<?php echo $car_item['curso_idx'] ?>" />
						<input type="hidden" name="vid" value="<?php echo $variacao_idx; ?>" />
						<input type="hidden" name="caction" value="2" />
						<div class="row checkout-produto">
		                    <div class="col-md-8 d-flex align-items-center">
		                        <a class="imagem-produto-carrinho" href="/cursos/<?php echo Text::friendlyUrl($car_item['nome']); ?>" >
		                            <figure>
	                                <?php if (!is_null($car_item['imagem'])&&trim($car_item['imagem'])!=""): ?>
		                                <img class="checkout-produto__image" src="/sitecontent/curso/curso/images/<?php echo $car_item['imagem'] ?>" alt="<?php echo $car_item['nome'] ?>" />
									<?php else: ?>
									    <img class="checkout-produto__image" src="/assets/images/product-no-image.png" alt="<?php echo $car_item['nome'] ?>">
									<?php endif ?>
		                            </figure>
		                        </a>
		                        <h3 class="checkout-produto__name" >
		                        	<?php echo $car_item['nome'] ?>
		                        </h3>
		                    </div>
		                    <div class="col-md-2 d-flex align-items-center justify-content-center">
		                        <h3 class="checkout-produto__value text-center">R$ <?php echo number_format($valor, 2, "," , "."); ?></h3>
							</div>
							<div class="col-md-2 text-center align-middle my-auto">
		                    	 <a href="#" class="checkout__remover" title="Remover do carrinho"
				                        onclick="javascript:document.frmEditCar<?php echo $counter; ?>.caction.value=3;document.frmEditCar<?php echo $counter; ?>.submit();" 
				                        	><i class="fas fa-times-circle mr-2"></i>remover</a>
		                    </div>
		                </div>
					</form>
					<?php
					$counter++;
					endforeach ?>
				<?php endif ?>
						<div class="row">
							
						
						<div class="col-md-8 col-12 d-flex justify-content-center justify-content-md-start cupom-desconto mb-5 pl-0" >
							<?php
							$afiliadoDesconto=false;
							if (isset($_SESSION['platform_afiliado'])) {
								if ((int)$_SESSION['platform_afiliado']['desconto_afiliado']>0) {
									$afiliadoDesconto=true;
								}
							}
							if (!$afiliadoDesconto): ?>
								<form accept="/seu-carrinho" name="form_voucher" id="form_voucher" method="post" class="form_voucher w-100 checkout-produto">
									
									<div>
										<?php if (!isset($_SESSION['ecommerce_cupom'])): ?>
											
											<input type="hidden" name="cpAc" value="1" />

											<div class="voucher-submit d-flex flex-wrap align-items-end">
												
												<div class="cupom-input rounded-8 bg-cinza-5 px-4 w-100">
													<div class="a-cupom-text text-uppercase d-flex align-items-center  fs-18 mb-3"> 
														<i class="fas fa-tags vermelho mr-2"></i>
														<span>Cupom de desconto</span>
													</div>
													<div class="row">
														<div class="col-sm-8 col-12 form-group mb-sm-0 pr-sm-0">
															<input name="voucher" id="voucher" class="form-control form-group mb-0 fs-14" placeholder="Digite o código" data-required="true" type="text" style="height:52px;" >
														</div>
														<div class="col-sm-4 col-12">
															<button type="button" onclick="javascript:Util.checkFormRequire(document.form_voucher, '#error-box-voucher');" class="btn btn-danger fs-16" style="border-radius:8px;" >APLICAR</button>
														</div>
													</div>
													<div class="alert alert-danger fs-14 mt-2 mx-md-0 mx-3 mb-0" style="display: none;" id="error-box-voucher" >
														Informe o c&oacute;digo do voucher!
													</div>
													<div class="clear-fix"></div>
												</div>
												
											</div>

										<?php else: ?>
													
											<input type="hidden" name="cpAc" value="2" />
											
											<div class="a-cupom-text display-inline text-upper subtitulo-t5 px-4 mb-md-0 mb-3" >
												
												<div class="a-cupom-text text-uppercase d-flex align-items-center source-sp-regular fs-18">
													<i class="fas fa-tags vermelho mr-2"></i>
													Seu cupom: &nbsp; <span class=""><?php echo $_SESSION['ecommerce_cupom']['codigo'] ?></span>
													<a href="javascript:;" class="btn green" title="Não usar esse cupom" 
														style="width:auto; padding:5px 10px; display:inline-block; vertical-align:middle; margin-left:15px;" 
														onclick="document.form_voucher.submit();"
													><i class="fa fa-times" ></i></a>
												</div>
												<small>
													<?php if ($_SESSION['ecommerce_cupom']['tipo_desconto']==1): //Percentual ?>
														Desconto de <?php echo $_SESSION['ecommerce_cupom']['valor_desconto'] ?>% no seu curso!
													<?php else: ?>
														Desconto de R$ <?php echo number_format($_SESSION['ecommerce_cupom']['valor_desconto'],2,",",".") ?> no seu curso!
													<?php endif ?>
													<?php if ($_SESSION['ecommerce_cupom']['valor_minimo']>0 && $car_subtotal<$_SESSION['ecommerce_cupom']['valor_minimo']): ?>
														<div class="text-danger" >Desconto apenas para cursos a partir de R$ <?php echo number_format($_SESSION['ecommerce_cupom']['valor_minimo'],2,",",".") ?> </div>
													<?php endif ?>
													<?php if ((int)$_SESSION['ecommerce_cupom']['cumulativo']==0): ?>
														<div class="text-danger" >Desconto apenas para cursos que não estejam em oferta.</div>
													<?php endif ?>
												</small>
											</div>
													
										<?php endif ?>
									</div>
							
								</form>

							<?php endif ?>

							<form accept="/seu-carrinho" name="form_afiliado" id="form_afiliado" method="post" class="form_afiliado w-100 checkout-produto ml-2">
								<div>
								<?php if (!isset($_SESSION['platform_afiliado'])): ?>
									<input type="hidden" name="afilAc" value="1" />

									<div class="afiliado-submit d-flex flex-wrap align-items-end">
										
										<div class="cupom-input rounded-8 bg-cinza-5 px-4 w-100">
											

											<div class="a-cupom-text text-uppercase d-flex align-items-center  fs-18 mb-3"> 
												<i class="fas fa-user-tag vermelho mr-2"></i>
												<span>Afiliado</span>
											</div>
											<div class="row">
												<div class="col-sm-8 col-12 form-group mb-sm-0 pr-sm-0">
													<input name="afiliadoCode" id="afiliadoCode" class="form-control form-group mb-0 fs-14" placeholder="Digite o código" data-required="true" type="text"
														style="height:52px;"
													>
												</div>
												<div class="col-sm-4 col-12">
													<button type="button" onclick="javascript:Util.checkFormRequire(document.form_afiliado, '#error-box-afiliado');" class="btn btn-danger fs-16"
														style="border-radius:8px;" 
													>APLICAR</button>
												</div>
											</div>
											<div class="clear-fix"></div>
											<div class="alert alert-danger fs-14 mt-2 mx-md-0 mx-3 mb-0" style="display: none;" id="error-box-afiliado" >
												Informe o c&oacute;digo do afiliado!
											</div>
										</div>
										
									</div>
								<?php else: ?>
									

										<input type="hidden" name="afilAc" value="2" />		
										<div class="a-cupom-text display-inline text-upper subtitulo-t5 px-4 mb-md-0 mb-3" >
											
											<div class="a-cupom-text text-uppercase d-flex align-items-center justify-content-between source-sp-regular fs-18">
												<div>
													<small><i class="fas fa-user-tag"></i> Afiliado:</small><br>
													<?php echo $_SESSION['platform_afiliado']['nome_completo'] ?>
													<small>(Cod.: <?php echo $_SESSION['platform_afiliado']['afiliado_codigo'] ?>)</small>
												</div>
												<a href="javascript:;" class="btn green" title="Não comprar com esse afiliado" 
													style="width:auto; padding:5px 10px; display:inline-block; vertical-align:middle; margin-left:15px;" 
													onclick="document.form_afiliado.submit();"
												><i class="fa fa-times" ></i></a>
											</div>
											<?php if ($_SESSION['platform_afiliado']['desconto_afiliado']>0): //Percentual ?>
											<small>
												Desconto de <?php echo $_SESSION['platform_afiliado']['desconto_afiliado'] ?>% no seu curso!
												<div class="text-danger" >Desconto apenas para cursos que não estejam em oferta.</div>
											</small>
											<?php endif ?>
										</div>

								<?php endif ?>
									
								</div>
							</form>
						</div>

						<?php
						$pedido_itens_total = $car_subtotal;

			            $desconto_valor = 0;
			            
			            //Aplica desconto por cupm
			            if (isset($_SESSION['ecommerce_cupom'])) {
			            	
			            	if ($_SESSION['ecommerce_cupom']['valor_minimo']>0) {
			            		
			            		if ($car_subtotal>=$_SESSION['ecommerce_cupom']['valor_minimo']) {
			            			if ($_SESSION['ecommerce_cupom']['tipo_desconto']==1){ //Percentual
										$desconto_valor = $car_subtotal * ($_SESSION['ecommerce_cupom']['valor_desconto']/100);
									}else{
										$desconto_valor = (float)($_SESSION['ecommerce_cupom']['valor_desconto']);
										if ((int)$_SESSION['ecommerce_cupom']['cumulativo']==0) {//Não é cumulativo, é aplicado apenas em itens sem oferta.
						            		$desconto_valor = ($desconto_valor>$car_subtotal_no_offer)?$car_subtotal_no_offer:$desconto_valor;
						            	}
									}
			            		}

			            	}else{

			            		if ($_SESSION['ecommerce_cupom']['tipo_desconto']==1){ //Percentual
									$desconto_valor = $car_subtotal * ($_SESSION['ecommerce_cupom']['valor_desconto']/100);
								} else{
									$desconto_valor = (float)($_SESSION['ecommerce_cupom']['valor_desconto']);
									if ((int)$_SESSION['ecommerce_cupom']['cumulativo']==0) {//Não é cumulativo, é aplicado apenas em itens sem oferta.
					            		$desconto_valor = ($desconto_valor>$car_subtotal_no_offer)?$car_subtotal_no_offer:$desconto_valor;
					            	}
								}

			            	}
			            }
			            //Aplica desonto pelo afiliado
			            if (isset($_SESSION['platform_afiliado'])) {
			            	if ((float)$_SESSION['platform_afiliado']['desconto_afiliado']>0){
				            	$desconto_valor = $car_subtotal * ($_SESSION['platform_afiliado']['desconto_afiliado']/100);
				            }
			            }
						?>

						
						<div class="col-md-4 col-12" >
							<?php 
							if ($desconto_valor>0): 

								$pedido_itens_total = $pedido_itens_total-$desconto_valor;
								if ($pedido_itens_total<=0) {
									$pedido_itens_total=0;
								}
							?>
								<div class="row" >
									<div class="order-resume col-md-12 justify-content-end align-items-end d-flex" >
										<h2 class="order-resume__title cinza" >Desconto</h2>
										<h3 class="order-resume__value cinza" >- R$ <?php echo number_format($desconto_valor, 2, "," , "."); ?></h3>
									</div>
								</div>
							<?php endif ?>

						    <div class="row">
			                    <div class="order-resume col-md-12 justify-content-end align-items-end d-flex">
			                        <?php $pedido_subtotal = number_format($pedido_itens_total, 2,",","."); ?>
									<h2 class="order-resume__title total" >Total</h2>
									<h3 class="order-resume__value total-valor" ><?php echo "R$ " .$pedido_subtotal;?></h3>
			                    </div>
			                </div>
			            </div>


	               </div>
			</div>
		</div>

	</section>

<?php else: ?>
	<br /><br />
	<h2 class="checkout__carrinho-vazio" >Seu carrinho está vazio!</h2>
<?php endif ?>

