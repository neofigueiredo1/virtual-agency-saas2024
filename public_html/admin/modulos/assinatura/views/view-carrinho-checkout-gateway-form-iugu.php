<?php
global $valor_frete,$frete_gratis,$frete_gratis_valor_minimo,$car_subtotal,$desconto_valor,$car_itens;
global $boleto_desconto, $boleto_desconto_percentual,$m_cadastro,$dados_formulario;

//Recupera os dados do produtor para seguir com a compra.
	$produtorID = (is_array($car_itens)&&count($car_itens)>0)?$car_itens[0]['produtor_idx']:0;
	$produtorReg = $m_cadastro->getCadastro($produtorID);

$autorizaBoleto = 1;

?>
<script type="text/javascript" async src="https://js.iugu.com/v2"></script>
<script>
	
	window.addEventListener('load',function(){
		Iugu.setAccountID("<?php echo $produtorReg[0]['iugu_split_account_id']; ?>");
		Iugu.setTestMode(true);
	});

	var objIugu = {
		data:{
			ajaxInProcess:false,
			validBrands:['visa','mastercard','amex','dinersclub','discover','jcb','elo','aura'],
			payment:null,
			card_hash:null
		},
		setCardHash:function(data){

			if (data.errors) {
				alert("Erro ao salvar o cartão: " + JSON.stringify(data.errors));
			} else {
				console.log(data.id);
				objIugu.data.card_hash = data.id;
				data_checkout.creditCardHash = data.id;
				document.form_checkout.card_hash.value = data.id;
			}

		},
		getCardHash:function(){
			return objIugu.data.card_hash;
		},
		isReady:function(){
			
			if (objIugu.data.payment!=null){
				if (objIugu.data.payment.paymentMethod!="creditCard"){
					return true;
				}
			}
			if (objIugu.data.card_hash!=null) {
				document.form_checkout.card_hash.value = objIugu.data.card_hash;
				return true;
			}
			return false;

		},
		/*Valida os dados preenchidos.*/
		checkoutData:function(theMessageArea){
			
			form = document.form_checkout;

			var paymentMethod = $('.payment_option:checked').val();
			var ccNum = $('.iugu_cc_card_num').val();
			var ccExp = $('.iugu_cc_exp_date').val();
			var ccExpArr = ccExp.split("/");
			var ccCCV = $('.iugu_cc_card_code').val();
			var ccNameHolder = $('.iugu_cc_holder_name').val();
			
			if (paymentMethod=="creditCard" || paymentMethod=="debitCard") {

				var ccType = Iugu.utils.getBrandByCreditCardNumber(ccNum);

				var daddosValidos = true;
				var mensagemErro = "";

				var validCcNum = Iugu.utils.validateCreditCardNumber(ccNum);
				var validCcExp = (ccExpArr.length==2) ? $.payment.validateCardExpiry(ccExpArr[0],ccExpArr[1]) : false;
				var validCcCVC = Iugu.utils.validateCVV(ccCCV,ccType);
				var ccInstallment = $('.iugu_installments').val();

				var card = {};
			        card.card_holder_name = ccNameHolder;
			        card.card_expiration_date = ccExpArr[0] + '/' + ccExpArr[1];
			        card.card_number = ccNum;
			        card.card_cvv = ccCCV;

				if (!validCcNum){
					daddosValidos = false;
					mensagemErro += "- O Número do cartão não é válido.<br/>";
					$('.iugu_cc_card_num').addClass("error");
				}
				if (!validCcExp){
					daddosValidos = false;
					mensagemErro += "- Informe a validade do cartão.<br/>";
					$('.iugu_cc_exp_date').addClass("error");
				}
				if (!validCcCVC){
					daddosValidos = false;
					mensagemErro += "- O Código do cartão é inválido.<br/>";
					$('.iugu_cc_card_code').addClass("error");
				}
				if (ccNameHolder.trim()==""){
					daddosValidos = false;
					mensagemErro += "- Informe o nome do portador do cartão.<br/>";
					$('.iugu_cc_holder_name').addClass("error");
				}
				if (isNaN(ccInstallment)){
					daddosValidos = false;
					mensagemErro += "- É necessário selecionar o número de parcelas da compra.<br/>";
				}
				
			}else{

				objIugu.data.payment = {
					paymentMethod:paymentMethod
				};
				data_checkout = Object.assign(data_checkout, objIugu.data.payment);
				return true;

			}

			if (daddosValidos){
				
				console.log(data_checkout);

				Iugu.createPaymentToken(document.form_checkout, objIugu.setCardHash);

				objIugu.data.payment = {
					paymentMethod:paymentMethod,
					creditCardNumber:ccNum.substr(-4,4),
					creditCardDtValid:ccExp,
					creditCardBrand:ccType,
					creditCardHolderName:ccNameHolder,
					creditCardHash:objIugu.data.card_hash,
					installmentQuantity:ccInstallment
				};
				data_checkout = Object.assign(data_checkout, objIugu.data.payment);
				
				return true;

			}else{

				$('html, body').animate({ scrollTop: ($(form).offset().top-150)}, 'slow');
				console.log(theMessageArea);
				$(theMessageArea).removeAttr("style");
				$(theMessageArea).html(mensagemErro);
				$(theMessageArea).stop().slideDown('fast',function(){
					setTimeout(function(){
						$(theMessageArea).slideUp('fast');
					},35000);
				});
				return false;

			}

		},
		getBrandByCreditCardNumber:function(){
			let cardNumber = $('input.cc-num').val();
			let cardBrand = Iugu.utils.getBrandByCreditCardNumber(cardNumber);
			for (var i = 0; i < objIugu.data.validBrands.length; i++) {
				$('input.cc-num').removeClass('card-'+objIugu.data.validBrands[i]);
			}
			if (cardBrand!==false){
				if (objIugu.data.validBrands.indexOf(cardBrand)!=-1){
					$('input.cc-num').addClass('card-'+cardBrand);
					document.form_checkout.card_brand.value = cardBrand;
				}
			}
		},
		loadInstalments:function(){
			let cardNumber = $('input.cc-num').val();
			let cardBrand = Iugu.utils.getBrandByCreditCardNumber(cardNumber);
			for (var i = 0; i < objIugu.data.validBrands.length; i++) {
				$('input.cc-num').removeClass('card-'+objIugu.data.validBrands[i]);
			}
			
			if (cardBrand!==false && objIugu.data.validBrands.indexOf(cardBrand)!=-1){
				$('input.cc-num').addClass('card-'+cardBrand);

				if (!objIugu.data.ajaxInProcess) {
					
					objIugu.data.ajaxInProcess=true;
					$.ajax({
			            type:'POST',
			            url:'/site/direct-includes/modulo-ecommerce-ajax-controller.php',
			            data:{ cbrand:cardBrand,ac:'pGetPaymentInstallments' ,exe:322 }
			        }).done(function(data){
			        	objIugu.data.ajaxInProcess=true;
			        	if (data.trim()!="") {
							$('.iugu_installments').html(data);
							$('.installments-info').slideUp('fast');
							$('.iugu_installments').slideDown('fast');
			        	}else{
							$('.iugu_installments').html("");
							$('.installments-info').slideDown('fast');
							$('.iugu_installments').slideUp('fast');
			        	}
			        }).always(function(){
			        	objIugu.data.ajaxInProcess=false;
			        });
				}

			}else{
				$('.iugu_installments').html("");
				$('.installments-info').slideDown('fast');
				$('.iugu_installments').slideUp('fast');
			}
		},
		init:function(){}
	};

</script>
<style>
	.cc-num{ background:transparent no-repeat right center !important; background-size:auto50%; }
	.cc-num.card-amex{ background-image:url(/assets/images/cc-AMEX.jpg) !important; }
	.cc-num.card-visa{ background-image:url(/assets/images/cc-VISA.jpg) !important; }
	.cc-num.card-mastercard{ background-image:url(/assets/images/cc-MASTERCARD.jpg) !important; }
	.cc-num.card-elo{ background-image:url(/assets/images/cc-ELO.jpg) !important; }
	.cc-num.card-aura{ background-image:url(/assets/images/cc-AURA.jpg) !important; }
	.cc-num.card-dinersclub{ background-image:url(/assets/images/cc-DINERS.jpg) !important; }
	.cc-num.card-jcb{ background-image:url(/assets/images/cc-JCB.jpg) !important; }
	.cc-num.card-discover{ background-image:url(/assets/images/cc-DISCOVER.jpg) !important; }
</style>

<input type="hidden" name="pagamento_id" id="pagamento_id" value="1" />
<input type="hidden" name="paymentMethod" id="paymentMethod" value="" />
<input type="hidden" name="card_brand" value="" />
<input type="hidden" name="card_hash" value="" />

<div class="meios-pagamento">
	<div class="row" >
		<div class="col-sm-5 order-0 order-sm-1 text-right" >
			<span class="checkout__total">
				Total:
				<small>
					<span class="card-pedido-subtotal-valor" >
						R$ <?php echo number_format(($car_subtotal-$desconto_valor), 2, "," , "."); ?>
					</span>
				</small>
			</span>
        </div>
        <div class="col-sm-7 order-1 order-sm-0 text-left" >
            
            <div class="ginfo g-iugu" style="height:auto;" >
        		
        		<figure class="d-inline-block align-top" >
	                <img src="/assets/images/gateway-logo-iugu.png" width="100" alt="Meio de Pagamento" />
	            </figure>
	            &nbsp;
	            <div class="d-inline-block align-top" >
	            	<small>Bandeiras aceitas:</small><br>
		            <figure class="d-inline-block ico-cc-brand debitIn" ><img width="30" src="/assets/images/cc-VISA.jpg" alt="VISA" ></figure>
		            <figure class="d-inline-block ico-cc-brand debitIn" ><img width="30" src="/assets/images/cc-MASTERCARD.jpg" alt="MASTERCARD" ></figure>
		            <figure class="d-inline-block ico-cc-brand " ><img width="30" src="/assets/images/cc-ELO.jpg" alt="ELO" ></figure>
		            <figure class="d-inline-block ico-cc-brand " ><img width="30" src="/assets/images/cc-AMEX.jpg" alt="AMEX" ></figure>
		            <figure class="d-inline-block ico-cc-brand " ><img width="30" src="/assets/images/cc-DINERS.jpg" alt="DINERS" ></figure>
		            <figure class="d-inline-block ico-cc-brand " ><img width="30" src="/assets/images/cc-DISCOVER.jpg" alt="DISCOVER" ></figure>
		            <figure class="d-inline-block ico-cc-brand " ><img width="30" src="/assets/images/cc-JCB.jpg" alt="JCB" ></figure>
		            <figure class="d-inline-block ico-cc-brand " ><img width="30" src="/assets/images/cc-AURA.jpg" alt="AURA" ></figure>
	            </div>
	            <div class="iugu-elo-debit-w alert alert-warning" style="display:none;" ></div>

        	</div>
        	<div class="ginfo g-boletoprint" style="display:none;" >
        		<figure class="d-inline-block align-top" >
	                <img src="/assets/images/gateway-logo-iugu.png" width="100" alt="Meio de Pagamento" />
	            </figure>
	            &nbsp;
        		<figure class="d-inline-block align-top" >
	                <img src="/assets/images/gateway-logo-boletoprint.jpg" alt="Meio de Pagamento" />
	            </figure>
	            &nbsp;
        	</div>
        	<div class="ginfo g-pixprint" style="display:none;" >
        		<figure class="d-inline-block align-top" >
	                <img src="/assets/images/gateway-logo-iugu.png" width="100" alt="Meio de Pagamento" />
	            </figure>
	            &nbsp;
        		<figure class="d-inline-block align-top" >
	                <img src="/assets/images/gateway-logo-pixprint.png" alt="Meio de Pagamento" height="40" />
	            </figure>
	            &nbsp;
        	</div>

        </div>
	</div>
	<hr>
    <div class="row login" >
        
        <div class="col-md-3" >

    	    <div class="form-group" >
    	    	
                <div class="form-check d-flex align-items-center mb-3">
                    <input class="form-check-input mt-0 payment_option" type="radio" name="payment_option" data-payment-id="1" id="cartao-credito" value="creditCard" checked
						onclick="javascript:$('#pagamento_id').val(1);
						$('.installments-option').fadeIn();
						$('.payment-form').slideUp('fast');
						$('.iugu-cartao').stop().slideDown('fast');
						$('.ico-cc-brand').stop().animate({opacity:1},500);
						$('.ginfo').stop().slideUp('fast');
						$('.g-iugu').stop().slideDown('fast');
						"
                    >
                    <figure class="m-0 mr-3">
                        <img src="/assets/images/credito-icon.png" alt="Cartão de Crédito" class="mx-auto">
                    </figure>
                    <label class="form-check-label" for="cartao-credito">
                        Cart&atilde;o
                    </label>
                </div>

            <?php if (false): ?>
                <div class="form-check d-flex align-items-center mb-3">
                    <input class="form-check-input mt-0 payment_option" type="radio" name="payment_option" data-payment-id="1" id="debito-online" value="debitCard" 
                    	onclick="javascript:$('#pagamento_id').val(1);
                    	$('.installments-option').fadeOut();
                    	$('.payment-form').slideUp('fast');
                    	$('.iugu-cartao').stop().slideDown('fast');
                    	$('.ico-cc-brand').animate({opacity:0.25},500);
                    	$('.debitIn').stop().animate({opacity:1},500);
                    	$('.ginfo').stop().slideUp('fast');
						$('.g-iugu').stop().slideDown('fast');
                    	"
                	>
                    <figure class="m-0 mr-3">
                        <img src="/assets/images/debito-icon.png" alt="Débito Online" class="mx-auto">
                    </figure>
                    <label class="form-check-label" for="debito-online">
                        Cartão de Débito
                    </label>
                </div>
			<?php endif; ?>

            <?php if ($autorizaBoleto==1): ?>
                <div class="form-check d-flex align-items-center mb-3">
                    <input class="form-check-input mt-0 payment_option" type="radio" name="payment_option" data-payment-id="1" id="boleto" value="boleto"
                    	onclick="javascript:$('#pagamento_id').val(1);
                    	$('.payment-form').slideUp('fast');
                    	$('.iugu-boleto').slideDown('fast');
                    	$('.ginfo').stop().slideUp('fast');
						$('.g-boletoprint').stop().slideDown('fast');
                    	"
                	>
                    <figure class="m-0 mr-3">
                        <img src="/assets/images/boleto-icon.png" alt="Boleto" class="mx-auto">
                    </figure>
                    <label class="form-check-label" for="boleto">
                        Boleto
                    </label>
                </div>
        	<?php endif; ?>

        		<div class="form-check d-flex align-items-center mb-3">
                    <input class="form-check-input mt-0 payment_option" type="radio" name="payment_option" data-payment-id="1" id="pix" value="pix"
                    	onclick="javascript:$('#pagamento_id').val(1);
                    	$('.payment-form').slideUp('fast');
                    	$('.iugu-pix').slideDown('fast');
                    	$('.ginfo').stop().slideUp('fast');
						$('.g-pixprint').stop().slideDown('fast');
                    	"
                	>
                    <figure class="m-0 mr-3">
                        <img src="/assets/images/pix-icon.png" alt="Pix" class="mx-auto" width="36" >
                    </figure>
                    <label class="form-check-label" for="pix" >
	                	PIX
	                </label>
                </div>

                <?php if (false): ?>
                <div class="form-check d-flex align-items-center mb-3">
                    <input class="form-check-input mt-0 payment_option" type="radio" name="payment_option" id="transferencia" value="transferencia"
                    	onclick="javascript:$('.payment-form').slideUp('fast');$('.iugu-transferencia').slideDown('fast');"
                	>
                    <figure class="m-0 mr-3">
                        <img src="/assets/images/transference-icon.png" alt="transferencia" class="mx-auto" >
                    </figure>
                    <label class="form-check-label" for="transferencia">
                        Transfer&ecirc;ncia OnLine
                    </label>
                </div>
                <?php endif ?>
            </div>
            
        </div>
        <div class="col-md-9" >

        	<div class="payment-form iugu-pix" style="display:none;" >
				O seu PIX Copia e Cola será gerado ap&oacute;s &quot;Finalizar o Pedido&quot;.<br/><br/>
				Se voc&ecirc; n&atilde;o visualizar QRCode e o link de copia e cola do seu pix, entre em contato.
			</div>

        	<?php if ($autorizaBoleto==1): ?>
        	<div class="payment-form iugu-boleto" style="display:none;" >

        		
                <strong>Informe seu endereço para emissão da cobrança</strong>
                <div class="row p-0 m-0">
                    <div class="col-md-4 px-1 mb-2 form-group">
                        <label for="cep" class="checkout-cadastro__label" >CEP <span class="just-numbers fs-12 cinza-claro font-italic">(Somente números)</span></label>
                        <input type="text" class="form-control form--custom mask_cep cep" id="cep" value="<?php echo $dados_formulario['cep']; ?>"
                            onkeyup="javascript:if(this.value.length==9){ckCompletaEnderecoPorCEP(this.form);};getAndSetFreteInformation(this.form);"
                            onblur="javascript:if(this.value.length==9){ckCompletaEnderecoPorCEP(this.form);};getAndSetFreteInformation(this.form);"
                        >
                    </div>
                    <div class="col-md-8 px-1 mb-2 form-group">
                        <label class="checkout-cadastro__label">Endereço</label>
                        <input type="text" name="endereco" class="form-control form--custom " value="<?php echo $dados_formulario['endereco']; ?>" >
                    </div>
                    <div class="col-md-3 px-1 mb-2 form-group">
                        <label class="checkout-cadastro__label">Número</label>
                        <input type="text" name="numero" id="numero" class="form-control form--custom " value="<?php echo $dados_formulario['numero']; ?>" >
                    </div>
                    <div class="col-md-4 px-1 mb-2 form-group">
                        <label class="checkout-cadastro__label">Complemento</label>
                        <input type="text" name="complemento" class="form-control form--custom " value="<?php echo $dados_formulario['complemento']; ?>" >
                    </div>
                    <div class="col-md-5 px-1 mb-2 form-group">
                        <label class="checkout-cadastro__label">Bairro</label>
                        <input type="text" name="bairro" class="form-control form--custom " value="<?php echo $dados_formulario['bairro']; ?>" >
                    </div>
                    <div class="col-md-7 px-1 mb-2 form-group">
                        <label class="checkout-cadastro__label">Cidade</label>
                        <input type="text" name="cidade" class="form-control form--custom " value="<?php echo $dados_formulario['cidade']; ?>">
                    </div>
                    <div class="col-md-5 px-1 mb-2 form-group">
                        <label class="checkout-cadastro__label">Estado</label>
                        <select id="estado" name="estado" class="form-control" >
                            <option value="" >--</option>
                            <?php 
                                $estados = Sis::getState();
                                $estado_selecionado = $dados_formulario['estado'];
                                foreach($estados as $key => $estado){
                                    $selected = (trim($estado_selecionado)==trim($key))?"selected":"";
                                    echo('<option value="'.$key.'" '.$selected.' >'.$key.'</option>');
                                }
                             ?>
                        </select>
                    </div>
                </div>
	                

				<?php if ($boleto_desconto==1): ?>
					
					<?php if (isset($_SESSION['ecommerce_cupom'])): 
						//Regra de negócio, o desconto em forma de pagamento não é cumulativo quando houver o uso do CUPOM  de desconto.
					?>
						<div class="alert alert-warning" >O desconto de <strong><?php echo $boleto_desconto_percentual; ?>%</strong> no pagamento com boleto não é cumulativo quando já estiver usando um cupom de desconto em seu pedido.</div>
					<?php else: ?>
						<h5>Pagando com boleto você adquire <strong><?php echo $boleto_desconto_percentual; ?>%</strong> de desconto em seu pedido.</h5>
	        			<h3 class="text-success mb-3" >
	        				Total do pedido com o desconto: R$ <span class="boleto-desconto-pedido-total" ><?php echo number_format(($car_subtotal-$desconto_valor-(($car_subtotal-$desconto_valor)*($boleto_desconto_percentual/100))), 2, "," , "."); ?></span>
	        			</h3>
	        			<!-- <div class="boleto-desconto-pedido-aviso alert alert-warning" >Certfique-se de que selecionou sua opção de entrega para visualizar seu desconto.</div> -->
	        			
					<?php endif ?>

        		<?php endif ?>
				
				<br/>
				O seu Boleto será gerado ap&oacute;s &quot;Finalizar o Pedido&quot;.<br/><br/>
				Se voc&ecirc; n&atilde;o visualizar ou receber seu boleto por e-mail, entre em contato.

			</div>
        	<?php endif ?>


        	<?php if (false): ?>
			<div class="payment-form iugu-transferencia" style="display: none;" >
				
				<div class="form-row align-items-end">
	                <div class="form-group col-md-6">
	                    <label for="trans_provider" >Escolha o Banco</label>
	                    <select name="trans_provider" id="trans_provider" class="form-control trans_provider iugu_trans_provider" >
	                    	<option value="" ></option>
	                    	<option value="Bradesco" >Bradesco</option>
	                    	<option value="BancodoBrasil" >Banco do Brasil</option>
	                    </select>
	                </div>
	            </div>

			</div>
        	<?php endif ?>
        	
			<div class="payment-form iugu-cartao" >
	            <div class="form-row align-items-end">
	                <div class="form-group col-md-6">
	                    <label for="cf1" >Número do Cartão</label>
                    	<input id="cf1" type="text" class="form-control form--custom iugu_cc_card_num cc-num" onkeyup="javascript:objIugu.loadInstalments();" data-iugu="number" >
	                </div>
	                <div class="form-group col-md-3">
	                    <label for="cf2" >Expira em</label>
						<input type="text" class="form-control  form--custom iugu_cc_exp_date cc-exp" id="f2" data-iugu="expiration" >
	                </div>
	                <div class="form-group col-md-3">
	                    <label for="cf3" >Código CCV</label>
	                    <input type="text" class="form-control  form--custom iugu_cc_card_code cc-cvc" id="cf3" data-iugu="verification_value" >
	                </div>
	            </div>
	            <div class="form-group" >
	                <label for="cf5">Nome do Portador <span class="nome-no-cartao" >(Exatamente como escrito no cart&atilde;o)</span></label>
	                <input type="text" class="form-control  form--custom iugu_cc_holder_name" id="cf5" name="cc_holder" data-iugu="full_name" >
	            </div>
	            <div class="form-row align-items-end installments-option" >
	                <div class="form-group col-md-12" >
	                	<label for="cf6" >Número de parcelas</label><br/>
						<div class="text-gray installments-info alert alert-warning fs-16 py-2 " >
							<small>Informe o número do seu cartão para obter os valores das parcelas</small>
						</div>
						<select id="cf6" name="installments" class="form-control iugu_installments" style="display:none;" >
						</select>
	                </div>
	            </div>
	        </div>
        </div>
    </div>
</div>

