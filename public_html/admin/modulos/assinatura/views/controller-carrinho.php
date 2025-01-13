<?php

	$_SESSION["ecommerce_url_back_login"] = "/seu-carrinho";

	global $ecomCarrinho,$car_session,$car_itens,$cursoId;
	global $boleto_desconto, $boleto_desconto_percentual;
	global $facebookAction;

	$cursoId = (isset($_POST['cursoId'])) ? (int)Text::clean($_POST['cursoId']) : ((isset($_GET['cursoId'])) ? (int)Text::clean($_GET['cursoId']) : 0) ;
	
	$ecomCarrinho = new EcommerceCarrinho();

	//Inicia o carrinho
	$ecomCarrinho->carInicializa();

	$caction = (isset($_POST['caction']))?$_POST['caction']: ((isset($_GET['caction']))?$_GET['caction']:0) ;
	$caction = (is_numeric($caction))?$caction:0;
	
	if ($caction!=0) {
		switch($caction){
			case 1 : //Add
				unset($_SESSION['ecommerce_cupom']);
				$curso = $ecomCarrinho->carItemInsert($cursoId);

				if (!isset($_SESSION['platform_event_tracker'])) {
				    $_SESSION['platform_event_tracker']='';
				}

				$valor = (round($curso['em_oferta'])==1) ? $curso['em_oferta_valor'] : $curso['valor'];

				//Define um evento para pixel do facebook caso esteja definido no curso
				if (trim((string)$curso['facebook_pixel_id'])) {
				
				    $_SESSION['platform_cart_tracker_facebook_pixel_id'] = $curso['facebook_pixel_id'];

				    $_SESSION['platform_event_tracker_facebook_pixel_id'] = $curso['facebook_pixel_id'];
				    $_SESSION['platform_event_tracker'] .= "fbq('init', '".$curso['facebook_pixel_id']."');
				    fbq('trackSingle','".$curso['facebook_pixel_id']."','AddToCart',{ 
						value: '".number_format($valor,2,".","")."',
						currency: 'BRL',
						content_name: '".$curso['nome']."',
						content_type: 'product', 
						content_ids: '".$curso['codigo']."'
			    	});fbq('trackSingle','".$curso['facebook_pixel_id']."','PageView');";
				}

				//Define um evento para o GTM caso esteja definido no curso
				if (trim((string)$curso['google_tag_manager_id'])) {
					
					$_SESSION['platform_cart_tracker_google_tag_manager_id'] = $curso['google_tag_manager_id'];

				    $_SESSION['platform_event_tracker_google_tag_manager_id'] = $curso['google_tag_manager_id'];
				    $_SESSION['platform_event_tracker'] .= "gtag('config','".$curso['google_tag_manager_id']."');
					    gtag('event','add_to_cart',{
						  currency: 'BRL',
						  items: [{
						    id: '".$curso['codigo']."',
						    name: '".$curso['nome']."',
						    item_id: '".$curso['codigo']."',
						    item_name: '".$curso['nome']."',
						    price: ".number_format($valor,2,".","").",
						    currency: 'BRL',
						    quantity: 1
						  }],
						  value: ".number_format($valor,2,".","")."
						});
					";
				}
				
				//trackers - facebook / analytics
				// $_SESSION["platform_event_tracker"] = "
				// 	fbq('track','AddToCart',{ 
				// 		value: '".number_format($valor,2,".","")."',
				// 		currency: 'BRL',
				// 		content_name: '".$curso['nome']."',
				// 		content_type: 'product', 
				// 		content_ids: '".$curso['codigo']."'
			 	//    	});
				// 	gtag('event','add_to_cart', {
				// 	  'items': [
				// 	    {
				// 	      'id': '".$curso['codigo']."',
				// 	      'name': '".$curso['nome']."',
				// 	      'brand': 'Manix',
				// 	      'category': 'Apparel&Accessories/Jewelry',
				// 	      'quantity': ".(int)$qtd.",
				// 	      'price': '".number_format($valor,2,".","")."'
				// 	    }
				// 	  ]
				// 	});
				// ";

				//Veririca o envio de código de afiliado para ativar
				$afiliadoCode = (isset($_GET['afiliado']))?trim($_GET['afiliado']):'';
				if ($afiliadoCode!=''){
					$ecomCadastro = new EcommerceCadastro();
					try {
						$ecomCadastro->afiliadoLoad($afiliadoCode,$cursoId);
					} catch (Exception $e) {
						// die($e->getMessage());
						//Do Nothing
					}
				}
			
				break;
			case 2 : //Edit
				//Não se aplica
				break;
			case 3 : //Del
                $curso = $ecomCarrinho->carItemDelete($cursoId);
                $valor = (round($curso['em_oferta'])==1) ? $curso['em_oferta_valor'] : $curso['valor'];
      					//$_SESSION["platform_event_tracker"] = "gtag('event','remove_from_cart', {
						//   'items': [
						//     {
						//       'id': '".$curso['codigo']."',
						//       'name': '".$curso['nome']."',
						//       'brand': 'Manix',
						//       'category': 'Apparel&Accessories/Jewelry',
						//       'price': '".number_format($valor,2,".","")."'
						//     }
						//   ]
						// });";
				break;

			
		}
		header("Location: /seu-carrinho");
		exit();
	}

	$car_itens = $ecomCarrinho->carListItens();

	// //Valida a lista de cursos já selecionados
	// try {
	// 	$ecomCarrinho->carCheckList($car_itens);
	// } catch (Exception $e) {
	// 	echo "<!-- ERROR INTEGRACAO [".$e->getMessage()."] -->";
	// }
	
	try {
		$car_session = $ecomCarrinho->carGetSession();
	} catch (Exception $e) {
		die($e->getMessage());
	}
	
	//Verificação para o desconto em BOLETO
    $boleto_desconto = (int)Sis::config("ECOMMERCE-BOLETO-DESCONTO-ATIVO");
    $boleto_desconto_percentual = (int)Sis::config("ECOMMERCE-BOLETO-DESCONTO-PERCENTUAL");
    if ($boleto_desconto_percentual>100)
    	$boleto_desconto_percentual=100;


?>