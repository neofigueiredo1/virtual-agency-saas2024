
if (typeof Array.prototype.reIndexOf === 'undefined') {
    Array.prototype.reIndexOf = function (rx) {
        for (var i in this) {
            if (this[i].toString().match(rx)) {
                return i;
            }
        }
        return -1;
    };
}


function ocultaMeuPedido(pedido_idx){
	$('#box-pedido-detalhe-'+pedido_idx).addClass('box-pedido-detalhe');

	$('.pedidoDetalheHide'+pedido_idx).fadeOut(0, function(){
		$('.pedidoDetalheShow'+pedido_idx).fadeIn(0);
	});

	$('#box-pedido-detalhe-'+pedido_idx).animate({"height" : 100}, 100, function(){
		$('#box-pedido-detalhe-'+pedido_idx+' .conteudo').html('');
	});
	$('#box-pedido-detalhe-'+pedido_idx).slideUp(20);

}

function exibeMeuPedido(pedido_idx){

	$('#box-pedido-detalhe-'+pedido_idx+' .conteudo').css("display","none");

	$.ajax({
	  	type: "POST",
	  	url: "/site/direct-includes/modulo-ecommerce-ajax-controller.php",
	  	data:{ exe:10, ac:'pGetPedidoDetalhes', pid:pedido_idx }
	}).done(function( data ){
		if(data!="erro"){
			
			$('#box-pedido-detalhe-'+pedido_idx).removeClass('box-pedido-detalhe');

			if($('.box-pedido-detalhe').length > 0){
				$('.box-pedido-detalhe').slideUp('fast');
			}

			$('.box-pedido-detalhe .conteudo').html("");

			$('.table-list-pedidos .fa-times').fadeOut(0, function(){
				$('.table-list-pedidos .fa-search').fadeIn(0);
			});

			$('#box-pedido-detalhe-'+pedido_idx).slideDown("fast", function(){

				$('.pedidoDetalheShow'+pedido_idx).fadeOut(0, function(){
					$('.pedidoDetalheHide'+pedido_idx).fadeIn(0);
				});


				$('#loader-'+pedido_idx).fadeIn('fast', function(){
					$('#box-pedido-detalhe-'+pedido_idx+' .conteudo').html(data).promise().done(function(){
						setTimeout(function(){
							$('#loader-'+pedido_idx).fadeOut('fast', function(){
								//var newheight = ($('#box-pedido-detalhe-'+pedido_idx+' .conteudo .email').height() + 50) + "px";

								/*$('#box-pedido-detalhe-'+pedido_idx+'').animate({
									'height': newheight
								}, 200);*/
                                $('#box-pedido-detalhe-'+pedido_idx+'').css("height","auto");
                                $('#box-pedido-detalhe-'+pedido_idx+' .conteudo').slideDown();
								$('#box-pedido-detalhe-'+pedido_idx).addClass('box-pedido-detalhe');
							});
						}, 1000);
					});
				});

			});

		}else{

			alert("Não possível ober os dados do pedido, tente novamente em instantes.");
            console.log(data);

		}
	});
}


/*detalhes das vendas*/
function ocultaVenda(pedido_idx){
	$('#box-venda-detalhe-'+pedido_idx).addClass('box-venda-detalhe');

	$('.vendaDetalheHide'+pedido_idx).fadeOut(0, function(){
		$('.vendaDetalheShow'+pedido_idx).fadeIn(0);
	});

	$('#box-venda-detalhe-'+pedido_idx).animate({"height" : 100}, 100, function(){
		$('#box-venda-detalhe-'+pedido_idx+' .conteudo').html('');
	});
	$('#box-venda-detalhe-'+pedido_idx).slideUp(20);

}

function exibeVenda(pedido_idx){

	$('#box-venda-detalhe-'+pedido_idx+' .conteudo').css("display","none");

	$.ajax({
	  	type: "POST",
	  	url: "/site/direct-includes/modulo-ecommerce-ajax-controller.php",
	  	data:{ exe:10, ac:'pGetVendaDetalhes', pid:pedido_idx }
	}).done(function( data ) {
		if(data!="erro"){
			$('#box-venda-detalhe-'+pedido_idx).removeClass('box-venda-detalhe');

			if($('.box-venda-detalhe').length > 0){
				$('.box-venda-detalhe').slideUp('fast');
			}

			$('.box-venda-detalhe .conteudo').html("");

			// $('.table-list-pedidos .fa-times').fadeOut(0, function(){
			// 	$('.table-list-pedidos .fa-search').fadeIn(0);
			// });

			$('#box-venda-detalhe-'+pedido_idx).slideDown("fast", function(){

				$('.vendaDetalheShow'+pedido_idx).fadeOut(0, function(){
					$('.vendaDetalheHide'+pedido_idx).fadeIn(0);
				});

				$('#loader-'+pedido_idx).fadeIn('fast', function(){
					$('#box-venda-detalhe-'+pedido_idx+' .conteudo').html(data).promise().done(function(){
						setTimeout(function(){
							$('#loader-'+pedido_idx).fadeOut('fast', function(){
								$('#box-venda-detalhe-'+pedido_idx+'').css("height","auto");
                                $('#box-venda-detalhe-'+pedido_idx+' .conteudo').slideDown();
								$('#box-venda-detalhe-'+pedido_idx).addClass('box-venda-detalhe');
							});
						}, 1000);
					});
				});

			});

		}else{
			alert("Não possível ober os dados da venda, tente novamente em instantes.");
            console.log(data);
		}
	});
}

function validaUser(callback){
	let cData = { exe: 3 };
	$.ajax({
		url : "/site/direct-includes/modulo-ecommerce-ajax-controller.php",
		type: "POST",
		data : cData,
		success: function(data, textStatus, jqXHR)
		{
			if(typeof callback != "undefined"){
				if(data == "ok"){
					callback(true);
				}else{
					callback(false);
				}
			}else{
				return false;
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			console.log("002 - Falha! "+textStatus);
		}
	});
}


function showBoxAvaliar(session_user){
	if(session_user == false){
		location.href = "/login";
	}else{
		$('.avaliacao-form').slideDown("fast");
	}
}

function updateMyFavorites(produto_ids){
	$.ajax({
	  	url: '/site/direct-includes/modulo-ecommerce-ajax-controller.php',
	  	type: 'POST',
	  	data: { produtos: produto_ids, ac:'uFavUpdate', exe: 4 },
	  	success: function(data, textStatus, xhr) {
	  		if(data=="login_error"){
	  			location.href="/cadastro";
	  		}else{
	  			if(data=="ok"){

	  				//Trackers - Facebook/Analytics Tracking
	  				var gtag_args = (typeof gtag_track_args !== 'undefined' && gtag_track_args !== null) ? gtag_track_args : {} ;
					// gtag('event','add_to_wishlist',gtag_args);
					var facebook_args = (typeof facebook_track_args !== 'undefined' && facebook_track_args !== null) ? facebook_track_args : {} ;
					// fbq('track', 'AddToWishlist', facebook_args);

	  				$(".alert-favoritos").html("Curso adicionado a sua lista de desejos!");
	  				$(".alert-favoritos").addClass("alert-success");
	  				
	  				$(".lista_desejos_off").css("display","none");
	  				$(".lista_desejos_on").css("display","block");

	  				$(".alert-favoritos").slideDown("fast", function(){
	  					setTimeout(function(){
	  						$(".alert-favoritos").slideUp("fast");
	  					}, 6000);
	  				});
	  			}else{
	  				$(".alert-favoritos").html("Erro ao inserir curso na sua lista de desejos! Tente mais tarde!");
	  				$(".alert-favoritos").addClass("alert-danger");
	  				$(".alert-favoritos").slideDown("fast", function(){
	  					setTimeout(function(){
	  						$(".alert-favoritos").slideUp("fast");
	  					}, 6000);
	  				});
	  			}
	  		}
	  	},
	  	error: function(xhr, textStatus, errorThrown) {
	  		$(".alert-favoritos").html("Erro ao inserir curso na sua lista de desejos! Tente mais tarde!");
  				$(".alert-favoritos").addClass("alert-danger");
  				$(".alert-favoritos").slideDown("fast", function(){
  					setTimeout(function(){
  						$(".alert-favoritos").slideUp("fast");
  					}, 6000);
  				});
	  	}
	});
}


function removeFavorites(produto){
	if(confirm("Você realmente quer remover este item?")){
		$.ajax({
		  	url: '/site/direct-includes/modulo-ecommerce-ajax-controller.php',
		  	type: 'POST',
		  	data: { produtos: produto, ac:'uFavRemove', exe: 5 },
		  	success: function(data, textStatus, xhr) {
	  			if(data=="ok"){
	  				$(".alert-favoritos").html("Curso removido com sucesso!");
	  				$(".alert-favoritos").addClass("alert-success");
	  				$(".alert-favoritos").slideDown("fast", function(){
	  					setTimeout(function(){
			  				$(".alert-favoritos").slideUp("fast", function(){});
	  					}, 6000);
	  				});
	  				$("#produto-"+produto).fadeOut('normal',function(){
	  					$("#produto-"+produto).remove();
	  				});
	  			}else{
	  				$(".alert-favoritos").html("Erro ao excluir curso na sua lista de desejos! Tente mais tarde!");
	  				$(".alert-favoritos").addClass("alert-danger");
	  				$(".alert-favoritos").slideDown("fast", function(){
	  					setTimeout(function(){
	  						$(".alert-favoritos").slideUp("fast");
	  					}, 6000);
	  				});
	  			}
		  	},
		  	error: function(xhr, textStatus, errorThrown) {
		  		$(".alert-favoritos").html("Erro ao excluir curso na sua lista de desejos! Tente mais tarde!");
	  				$(".alert-favoritos").addClass("alert-danger");
	  				$(".alert-favoritos").slideDown("fast", function(){
	  					setTimeout(function(){
	  						$(".alert-favoritos").slideUp("fast");
	  					}, 6000);
	  				});
		  	}
		});
	}else{
		return false;
	}
}


$(document).ready(function(){
	$('input.cc-num').payment('formatCardNumber');
	// $('input.cc-exp').payment('formatCardExpiry');
	$('input.cc-exp').mask('99/99');
	$('input.cc-cvc').payment('formatCardCVC');
});

function sendRating(){

	var produto_idx = document.frmProduto.id_produto_avaliacao.value;
	var voto = document.frmProduto.voto_avaliacao.value;
	var titulo_avaliacao = document.frmProduto.titulo_avaliacao.value;
	var conteudo_avaliacao = document.frmProduto.conteudo_avaliacao.value;

	if(produto_idx == "" || voto == "" || titulo_avaliacao == "" || conteudo_avaliacao == ""){
		$('.alert-avaliacao').html("Preeencha todos os campos e faça a sua avaliação!");
		$('.alert-avaliacao').removeClass("alert-success");
		$('.alert-avaliacao').addClass("alert-danger");
		$('.alert-avaliacao').slideDown("fast", function(){
			setTimeout(function(){
				$('.alert-avaliacao').slideUp("fast", function(){
					$('.alert-avaliacao').html("");
				});
			}, 4000);
		});
	}

	$.ajax({
	  	url: '/site/direct-includes/modulo-ecommerce-ajax-controller.php',
	  	type: 'POST',
	  	data: { produto_idx: produto_idx, voto:voto, titulo_avaliacao:titulo_avaliacao, conteudo_avaliacao:conteudo_avaliacao, exe: 7 },
		success: function(data, textStatus, xhr) {
			if(data == "ok"){
				var ol = $('.stars li').parent('ol'); if( $('.stars li').hasClass('active') && !$('.stars li').next('li').hasClass('active') ){ $( ol ).find('li').removeClass('active'); rating = 0; } else{ $( ol ).find('li').removeClass('active'); for( var i=0; i<$('.stars li').index()+1; i++ ){ $( ol ).find('li').eq( i ).removeClass('active'); }; }
				document.frmProduto.voto_avaliacao.value = "";
				document.frmProduto.titulo_avaliacao.value = "";
				document.frmProduto.conteudo_avaliacao.value = "";
				$('.alert-avaliacao').html("Sua avaliação está sendo moderada e você receberá um e-mail de confirmação quando ela for aceita!");
				$('.alert-avaliacao').removeClass("alert-danger");
				$('.alert-avaliacao').addClass("alert-success");
				$('.alert-avaliacao').slideDown("fast", function(){
					setTimeout(function(){
						$('.alert-avaliacao').slideUp("fast", function(){
							$('.avaliacao-form').slideUp("fast");
							$('.alert-avaliacao').html("");
						});
					}, 6000);
				});
			}else{
				$('.alert-avaliacao').html("Não foi possível fazer a avaliação. Verifique o preenchimento dos campos!");
				$('.alert-avaliacao').removeClass("alert-success");
				$('.alert-avaliacao').addClass("alert-danger");
				$('.alert-avaliacao').slideDown("fast", function(){
					setTimeout(function(){
						$('.alert-avaliacao').slideUp("fast", function(){
							$('.alert-avaliacao').html("");
						});
					}, 4000);
				});
			}
	  	},
	  	error: function(xhr, textStatus, errorThrown) {
	  		$('.alert-avaliacao').html("Não foi possível fazer a avaliação. Tente mais tarde!");
			$('.alert-avaliacao').removeClass("alert-success");
			$('.alert-avaliacao').addClass("alert-danger");
			$('.alert-avaliacao').slideDown("fast", function(){
				setTimeout(function(){
					$('.alert-avaliacao').slideUp("fast", function(){
						$('.alert-avaliacao').html("");
					});
				}, 4000);
			});
	  	}
	});

}