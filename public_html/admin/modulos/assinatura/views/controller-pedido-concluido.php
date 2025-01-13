<?php

	global $paymentSuccess,$paymentMessage,$token,$pcodigo,$paymentLink,$paymentMethod,$paymentPixData;

	$paymentSuccess = true;
	$paymentMessage = "Paragmento processado com sucesso!";
	$pcodigo = Text::clean($_GET[ 'pcodigo' ]);

	$pedidoId = (int)$pcodigo;
	$ecomPagamento = new EcommercePagamento();
	$ecomPagamento->setPagamentoId(1);
	$ecomPagamento->registrarGateway('sandbox');
	$ecomPagamento->setPedidoId($pedidoId);
	$ecomPagamento->loadTransaction();

	$paymentMethod = $ecomPagamento->gateway->getPaymentMethod();
	$paymentLink = $ecomPagamento->gateway->getPaymentLink();
	$paymentPixData = $ecomPagamento->gateway->getPixData();

	$pedido_resource = (isset($_SESSION['ECOM_PEDIDO_RESOURCE'])) ? unserialize($_SESSION['ECOM_PEDIDO_RESOURCE']) : false ;
	$facebook_track = "";
	$alaytics_track = "";
	if ($pedido_resource!==false) {

		$pedido  = $pedido_resource['pedido'];
		$pedido_itens = $pedido_resource['pedido_itens'];

		$facebook_track_pixel_id = "";
		$google_tag_manager_id = "";

		$facebook_track_ids = "";
		$alaytics_track_items = "";
		foreach ($pedido_itens as $key => $produto) {

			$facebook_track_pixel_id = $produto['facebook_pixel_id'];
			$google_tag_manager_id = $produto['google_tag_manager_id'];

			$subtotal_item = $produto["item_valor"];
			
			// $facebook_track_ids .= (($key>0)?',':'') . "'".$produto['pcodigo']."'";
			$alaytics_track_items .=  (($key>0)?',':'') . "{
			      'id': '".$produto['pcodigo']."',
			      'name': '".$produto["pnome"]."',
			      'brand': 'Conexo',
			      'category': 'EAD',
			      'quantity': '1',
			      'price': '".number_format($subtotal_item,2,".","")."'
			    }";
		}

		$valor_subtotal_itens = $pedido_resource['pedido_total'];
		//Verifica o desconto no pedido.
		$desconto_valor=0;
		$boleto_desconto_valor=0;
		if ($pedido["pagamento_com_boleto"]==1 && $pedido["pagamento_com_boleto_desconto_valor"]>0) {
			$boleto_desconto_valor = $pedido["pagamento_com_boleto_desconto_valor"];
		}

		$pedido_total = (($valor_subtotal_itens-$desconto_valor))-$boleto_desconto_valor;

		//Cria os parametros para os trackers
		$facebook_track = ",{
						value: ".number_format($pedido_total,2,'.','').",
						currency: 'BRL'
					}";

		if (trim($google_tag_manager_id)!="") {
			
			$alaytics_track = "
				gtag('config','".$google_tag_manager_id."');
				gtag('event','purchase',{
				  'transaction_id': '".$pedido['pedido_idx']."',
				  'affiliation': 'Conexo',
				  'value': ".number_format($pedido_total,2,'.','').",
				  'currency': 'BRL',
				  'tax': 0.00,
				  'shipping': 0.00,
				  'items': [".$alaytics_track_items."]
				});
			";
		}

		//Limpa a sessão com os dados da compra atual
		unset($_SESSION['ECOM_PEDIDO_RESOURCE']);
	}

	if ($facebook_track!="" && $alaytics_track!=""){
		//trackers - facebook / analytics
		//gtag('event','purchase'".$alaytics_track.");
		$_SESSION["platform_event_tracker"] = "
			fbq('init', '".$facebook_track_pixel_id."');
			fbq('trackSingle','".$facebook_track_pixel_id."','Purchase'".$facebook_track.");
			".$alaytics_track."
		";
	}
	
?>