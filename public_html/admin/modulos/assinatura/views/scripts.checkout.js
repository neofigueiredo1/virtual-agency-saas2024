
var data_checkout;
var checkout_method;

function setMethod(method){
	checkout_method=method;
}

function gatewayCheckoutData(theMessageArea){
	switch(parseInt(checkout_method)){
		case 1: /*Iugu*/
			return objIugu.checkoutData(theMessageArea);
		break;
		default:
			return true;
		break;
	}	
}

function gatewayCheckIsReady(){
	switch(parseInt(checkout_method)){
		case 1: /*Iugu*/
			return objIugu.isReady();
		break;
		default:
			return true;
		break;
	}	
}

function gatewayReloadInstallments(){
	console.log(checkout_method);
	switch(parseInt(checkout_method)){
		case 1: /*Iugu*/
			objIugu.loadInstalments();
		break;
		default:
			return true;
		break;
	}	
}

function ckValidaDados(form,theMessageArea){
	
	var nome_completo = form.nome_completo.value;
	// var nome_informal = form.nome_informal.value;
	// var data_nascimento = form.data_nascimento.value;
	// var sexo = form.sexo.value;
	var cpf_cnpj = form.cpf_cnpj.value;
	var telefone = form.telefone.value;
	
	var cep = form.cep.value;
	var estado = form.estado.value;
	var cidade = form.cidade.value;
	var endereco = form.endereco.value;
	var numero = form.numero.value;
	var complemento = form.complemento.value;
	var bairro = form.bairro.value;
	// var receber_boletim = (form.receber_boletim!=null) ? ( (form.receber_boletim.checked)?1:0 ) : 0;
	var receber_boletim = 1;
	var termos = (form.termos!=null) ? ( (form.termos.checked)?true:false ) : false;

	var hasPass = (form.senha!=null);

	var daddosValidos = true;
	var mensagemErro = "";

	var payment_option = $('.payment_option:checked').val();

	var cpfCnpjLimpo = cpf_cnpj.replace(/\D/g, '');
	console.log(cpfCnpjLimpo);
    //console.log(cpfCnpjLimpo.length);
    if (cpfCnpjLimpo.trim()=='') {
    
    	daddosValidos = false;
		mensagemErro += "- O CPF/CNPJ é requerido.<br/>";
		$(form.cpf).addClass("error");
    
    }else{

		if (cpfCnpjLimpo.length === 11)
	    {//Valida como CPF
	        if(verificaCpf(cpfCnpjLimpo) == false){
	            daddosValidos = false;
				mensagemErro += "- O CPF informado é inválido.<br/>";
				$(form.cpf_cnpj).addClass("error");
	            
	        }
	    }else {//Valida como CNPJ
	        if(verificaCNPJ(cpfCnpjLimpo) == false){
	            daddosValidos = false;
				mensagemErro += "- O CNPJ informado é inválido.<br/>";
				$(form.cpf_cnpj).addClass("error");
	        }
	    }

    }

	// if (data_nascimento!=""){
	// 	if (!Util.isDate(data_nascimento)){
	// 		daddosValidos = false;
	// 		mensagemErro += "- A Data de Nascimento informada é inválida.<br />";
	// 		$(form.data_nascimento).addClass("error");
	// 	}
	// }

	if (!termos && hasPass) {
		daddosValidos = false;
		mensagemErro += "- Certifique-se de que leu e aceita nossos termos de uso.<br />";
	}

	var email = form.email.value;
	var senha = null;

	if(!Util.isEmail(email)){
        daddosValidos = false;
		mensagemErro += "- Informe um e-mail válido.<br />";
		$(form.email).addClass("error");
    }

	// if (hasPass){
		// 	email = form.email.value;
		// 	senha = form.senha.value;
		// 	var senha_c = form.senha_c.value;
		// 	if (senha.trim()==""){
		// 		daddosValidos = false;
		// 		mensagemErro += "- Informe uma senha.<br />";
		// 		$(form.cpf).addClass("error");
		// 	}else if (senha != senha_c)
	    //     {
	    //         daddosValidos = false;
	    //         mensagemErro += "- A confirmação de senha deve ser igual a senha.<br />";
	    //         $(form.senha_c).addClass("error");
	    //     }else if(senha.length < 6){
	    //     	daddosValidos = false;
	    //         mensagemErro += "- A senha deve ter no mínimo 6 caracteres.<br />";
	    //         $(form.senha).addClass("error");
	    //     }
		// }


	if (payment_option=='boleto'){
		if (cep.trim()=='') {
	    	daddosValidos = false;
			mensagemErro += "- O CEP é requerido.<br/>";
			$(form.cep).addClass("error");
	    }
	    if (endereco.trim()=='') {
	    	daddosValidos = false;
			mensagemErro += "- O Endereço é requerido.<br/>";
			$(form.endereco).addClass("error");
	    }
	    if (numero.trim()=='') {
	    	daddosValidos = false;
			mensagemErro += "- O Número é requerido.<br/>";
			$(form.numero).addClass("error");
	    }
	    if (cidade.trim()=='') {
	    	daddosValidos = false;
			mensagemErro += "- O Cidade é requerido.<br/>";
			$(form.cidade).addClass("error");
	    }
	    if (estado.trim()=='') {
	    	daddosValidos = false;
			mensagemErro += "- O Estado é requerido.<br/>";
			$(form.estado).addClass("error");
	    }
	}


	if (daddosValidos){
		data_checkout = {
			ac:'ecomCheckout',
			processa:1,
			nome_completo:nome_completo, 
			// nome_informal:nome_informal, 
			// data_nascimento:data_nascimento, 
			// sexo:sexo,
			cpf_cnpj:cpf_cnpj, 
			telefone:telefone,
			cep:cep,
			estado:estado, 
			cidade:cidade, 
			endereco:endereco, 
			numero:numero, 
			complemento:complemento, 
			bairro:bairro,
			forma_pagamento:checkout_method,
			receber_boletim:receber_boletim,
			email:email
			// senha:null
		};

		// if (hasPass){
		// 	data_checkout.email = email;
		// 	data_checkout.senha = senha;
		// }

		return true;

	}else{
		$('html, body').animate({ scrollTop: ($(form).offset().top-150)}, 'slow');
		$(theMessageArea).html(mensagemErro);
		$(theMessageArea).stop().slideDown('fast',function(){
			$(theMessageArea).css('height','auto');
			setTimeout(function(){
				$(theMessageArea).slideUp('fast');
			},35000);
		});
		return false;
	}

}


var cepInfoIsLoading=false;
var cepInfoTryLoad=0;
function ckCompletaEnderecoPorCEP(form){
	var cep = form.cep.value;
	console.log(cep.length);
	if(cep.length == 9){
		if (!cepInfoIsLoading) {
			cepInfoIsLoading=true;
	        $.ajax({
	            type:'POST',
	            url:'/site/direct-includes/modulo-cadastro-ajax-controller.php',
	            data:{ ac:'addressByCEP', cep:cep } //csrf_token:form.csrf_token.value
	        }).done(function(data){
	        	data = data.trim();
	        	cepInfoIsLoading=false;
	            console.log(data);
                let dataArr = data.split("&");
	            if(data.trim() !== 'erro' && dataArr.length>1){
	                form.endereco.value=dataArr[0];
	                form.bairro.value=dataArr[1];
	                form.cidade.value=dataArr[2];
	                form.estado.value=dataArr[3];
	            }
	            if (freteInfoTryLoad==1) {
	            	freteInfoTryLoad=0;
	            	ckCompletaEnderecoPorCEP(form);
	            }
	            // else{
	            // 	if(data.trim() == 'erro'){
	            // 		alert('Não foi possível obter o seu endereço com base no CEP informado, certifique-se de que o CEP está correto.');
	            // 	}
	            // }
	            // Util.reloadNoCSRF(document.form_checkout.csrf_token);
	        }).always(function(){
	        	cepInfoIsLoading=false;
	        });
		}
    }
}

function ckRegistraPedido(form,theMessageArea){
	
	//Captura os dados de pagamento
	console.log(data_checkout);

	//Trackers - Facebook/Analytics Tracking
	if (CursoFacebookPixelID!=null){
		if ( CursoFacebookPixelID.trim() != ''){
			fbq('init',CursoFacebookPixelID);
			fbq('trackSingle',CursoFacebookPixelID,'InitiateCheckout');
		}
	}
	//Trackers - Facebook/Analytics Tracking
	if (CursoGTMID!=null){
		if ( CursoGTMID.trim() != ''){
			gtag('config',CursoGTMID);
			gtag('event','begin_checkout');
		}
	}


	$(form).slideUp("fast");
	$(".carrinho-checkout").slideUp("fast");
	$('#action_preloader').slideDown("fast", function(event){
		$.ajax({
			url : "/site/direct-includes/modulo-ecommerce-ajax-controller.php",
			type: "POST",
			data : data_checkout,
			success: function(data, textStatus, jqXHR)
			{	
				//console.log(data);
				var retorno;
				try{
					retorno = JSON.parse(data);
					//console.log(retorno);
				}catch(exception){
					retorno=false;
				}
				if (retorno===false){
					
					$(".carrinho-checkout").slideDown("fast");
					$(form).slideDown("fast");
					$('#action_preloader').slideUp("fast");
					$(theMessageArea).html(data);
					$(theMessageArea).slideDown("fast");

				}else{
					if(retorno.error==0){

						$('#action_preloader').slideUp("fast");
						$('#action_sucesso').html(retorno.message);
						$('#action_sucesso').slideDown("fast", function(event){
							//ckIniciaTransacao(retorno.url_pagamento);
							console.log(checkout_method);
							console.log(data_checkout);
							switch(parseInt(checkout_method)){
								case 1: //Iugu flow

									console.log(data_checkout.paymentMethod);
									console.log(retorno);
									if ( (retorno.pagamento.code).trim() == 'paid' || (retorno.pagamento.code).trim() == 'pending' ) {

										switch(data_checkout.paymentMethod){
											case "creditCard":
												window.location.href='/pedido-concluido/?pcodigo='+retorno.pedido_codigo;
												break;

											case "debitCard":
												if (retorno.pagamento.code==2) {
													window.location.href='/pedido-concluido/?pcodigo='+retorno.pedido_codigo;
												}else{
													if (retorno.url_pagamento!=null) {
														window.open(retorno.url_pagamento,"winPayment","width=700,height=600");
													}
													$('#action_sucesso').append('<br /><br /><small>Caso a janela de pagamento não seja carregada, <a href="#" onclick="javascript:abreJanelaRedireciona(\''+retorno.url_pagamento+'\',\'/pedido-concluido/?pcodigo='+retorno.pedido_codigo+'\');" >clique aqui para iniciar!</a></small>');
												}
												break;

											case "pix":
											case "boleto":
												window.location.href='/pedido-concluido/?pcodigo='+retorno.pedido_codigo;
												// if (retorno.url_pagamento!=null) {
												// 	window.open(retorno.url_pagamento,"winPayment","width=700,height=600");
												// }
												// $('#action_sucesso').append('<br /><br /><small>Caso a janela de pagamento não seja carregada, <a href="#" onclick="javascript:abreJanelaRedireciona(\''+retorno.url_pagamento+'\',\'/pedido-concluido/?pcodigo='+retorno.pedido_codigo+'\');" >clique aqui para visualizar o seu boleto!</a></small>');
											
												break;

											case "eft":
												if (retorno.pagamento.code==2) {
													window.location.href='/pedido-concluido/?pcodigo='+retorno.pedido_codigo;
												}else{
													if (retorno.url_pagamento!=null) {
														window.open(retorno.url_pagamento,"winPayment","width=700,height=600");
													}
													$('#action_sucesso').append('<br /><br /><small>Caso a janela de pagamento não seja carregada, <a href="#" onclick="javascript:abreJanelaRedireciona(\''+retorno.url_pagamento+'\',\'/pedido-concluido/?pcodigo='+retorno.pedido_codigo+'\');" >clique aqui para iniciar!</a></small>');
												}
												break;
										}

									}else{
										alert('Pagamento não realizado.\n\nRetorno da financeira: ' + retorno.pagamento.message );
										window.location.href='/pagamento-cancelado/?pcodigo='+retorno.pedido_codigo;
									}

								break;
								case 2: //Boleto Físico
								default://Sem meio de pagamento.
									console.log('default');
									console.log(retorno.url_pagamento);
									window.location.href='/pedido-concluido/?pcodigo='+retorno.pedido_codigo;
								break;
							}

						});
					}else{

						$(".carrinho-checkout").slideDown("fast");
						$(form).slideDown("fast");
						$('#action_preloader').slideUp("fast");
						$(theMessageArea).html(retorno.message);
						$(theMessageArea).slideDown("fast",function(){
							$('html, body').animate({scrollTop: $(theMessageArea).offset().top - 50}, 1000);
						});
					}
				}
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				if (checkout_method==1){ //paypal flow
					//paypal.checkout.closeFlow();
				}
				if (checkout_method==2){ //pagseguro flow
					//
				}
				
				$(form).slideDown("fast");
				$(".carrinho-checkout").slideDown("fast");
				$('#action_preloader').slideUp("fast");
				$(theMessageArea).html("Nâo foi possível completar a solicitação, tente novamente em instantes, se o problema persistir entre em contato.");
				$(theMessageArea).slideDown("fast");
				console.log("003 - Falha!"+textStatus);
			}
		}); //end ajax
	});
}

var goCheckoutData,gatewayData;

function goAndPay(){
	var paymentIsReady = gatewayCheckIsReady();
	console.log(paymentIsReady);
	if (paymentIsReady) {
		//trackers - facebook / analytics
		// fbq('track','AddPaymentInfo');
		// gtag('event','add_payment_info');

		if (goCheckoutData && gatewayData){
			ckRegistraPedido(document.form_checkout,$('#form_checkout .error-message'));
		}
	}else{
		setTimeout(function(){
			goAndPay();
		},1000);
	}
}

$(document).ready(function(){

	// setMethod($("#pagamento_id").val());
	setMethod(1);
	
	$('#confirmBtnWithGateWay').click(function(e){
		e.preventDefault();
		setMethod($("#pagamento_id").val());
		goCheckoutData = Util.checkFormRequire(document.form_checkout,$('#form_checkout .error-message'),ckValidaDados);
		if (goCheckoutData) {
			gatewayData = gatewayCheckoutData($('#form_checkout .error-message'));
			goAndPay(goCheckoutData,gatewayData);
		}
	});

	// $('#confirmBtnNoGateWay').click(function(e){
	// 	e.preventDefault();
	// 	setMethod(1);
	// 	var goCheckoutData = Util.checkFormRequire(document.form_checkout,$('#form_checkout .error-message'),ckValidaDados);
	// 	if (goCheckoutData){
	// 		ckRegistraPedido(document.form_checkout,$('#form_checkout .error-message'));
	// 	}
	// });

	// Não se aplica ao projeto atual
	// if ($(document.form_checkout).length>0) {
	// 	getAndSetFreteInformation(document.form_checkout);
	// }

	//Incluir recurso de cartão - front-end
	// $('input.cc-num').payment('formatCardNumber');
	// $('input.cc-exp').payment('formatCardExpiry');
	// $('input.cc-cvc').payment('formatCardCVC');

	// $('.cpf').mask('999.999.999-99');
	// $('.cep').mask('99999-999');
	// $('.telefone').focusout(function(){
	// 	var phone, element;
	// 	element = $(this);
	// 	element.unmask();
	// 	phone = element.val().replace(/\D/g, '');
	// 	if(phone.length > 10) {
	// 	element.mask("(99) 99999-9999");
	// 	} else {
	// 	element.mask("(99) 9999-99999");
	// }
	// }).trigger('focusout');
	// $('.data').mask('99/99/9999');

});

function abreJanelaRedireciona(urlJanela,urlRedirect){
	window.open(urlJanela,"winPayment","width=500,height=500");
	setTimeout(function(){
		window.location.href=urlRedirect;
	},3000);
}


//Define a opcao de frete
function setFreteCheckout(freteCodigo) {
	$.ajax({
		type:'POST',
		url:'/site/direct-includes/modulo-ecommerce-ajax-controller.php',
		data:{ frete_selecionado: freteCodigo, ac:'pSetSelectedFrete' }
	}).done(function(data){
		console.log(data);
		// gatewayReloadInstallments();
	});
	//Após selecionar o frete os valores são recalculados.
	calcCheckoutOptions();
}


function calcCheckoutOptions(){
	
	var subTotal = pedido_subtotal, //O valor de "pedido_subtotal" é definido na construção do carrinho.
		freteValor = -1,
		pedidoValorTotal = 0;

	//Verifica as opções de frete e verifica a que está selecionada
	var opcao_frete = $('.opcao_frete:checked');
	if (opcao_frete.length>0) {
		freteValor = $('.opcao_frete:checked').attr('data-valor');
	}

	// console.log(freteValor);

	if (freteValor>=0){
		pedidoValorTotal = parseFloat(subTotal) + parseFloat(freteValor);
		$('.total-valor').html("R$ " + Util.formatMoney(pedidoValorTotal,2,",",".") );
		$('.card-pedido-total-valor').html(" R$ " + Util.formatMoney(pedidoValorTotal,2,",",".") );
		if(freteValor==0) {
			$('.frete-valor').html("Grátis");
			$('.card-frete-valor').html("Grátis" );
		}else{
			$('.frete-valor').html("R$ " + Util.formatMoney(freteValor,2,",","."));
			$('.card-frete-valor').html("R$ " + Util.formatMoney(freteValor,2,",",".") );
		}
		if($('.boleto-desconto-pedido-total').length>0&&DesFP==1){
			var boletoPedidoValorTotalDesconto = pedidoValorTotal - parseFloat(DesFPVal*(pedidoValorTotal/100));
			$('.boleto-desconto-pedido-total').html( Util.formatMoney(boletoPedidoValorTotalDesconto,2,",",".") );
			$('.boleto-desconto-pedido-aviso').slideUp('fast');
		}

	}else{
		if($('.boleto-desconto-pedido-aviso').length>0) {
			$('.boleto-desconto-pedido-aviso').slideDown('fast');
		}
		$('.boleto-desconto-pedido-total').html(" -- ");
		$('.frete-valor').html(" -- ");
		$('.total-valor').html(" -- ");
		$('.card-pedido-total-valor').html(" -- ");
		$('.card-frete-valor').html(" -- ");
	}
}

