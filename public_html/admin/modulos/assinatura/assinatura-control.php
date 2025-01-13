<?php
	
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;

	class pedido extends pedido_model{

		public $MODULO_CODIGO = "10033";
		public $MODULO_AREA = "E-commerce - Pedido";
		public $mod, $pag, $act;

		function __construct(){
			parent::__construct();
		}

		/**
		* Processa a lista completa dos registros dos fabricantes.
		*/
		public function pedidosListAll($id=0, $filtros="", $paginaAtual=0, $registroPagina=0){
			return parent::mPedidosListAll($id=0, $filtros, $paginaAtual, $registroPagina);
		}

		public function pedidoListSelected($pid)
		{

			$sql_query = "";

			/*Envia mensagem ao cliente / operador*/
			$pedido = parent::select("SELECT tbPedido.*,
			                         tbPedidoEnd.endereco,
			                         tbPedidoEnd.numero,
			                         tbPedidoEnd.bairro,
			                         tbPedidoEnd.cep,
			                         tbPedidoEnd.cidade,
			                         tbPedidoEnd.estado,
			                         tbPedidoEnd.complemento,
			                         tbFormaPg.nome as forma_pagamento_nome,  tbStatus.nome as status_nome,
			                         tbCadAfl.nome_completo as afiliado_nome,tbCadAfl.afiliado_codigo,
			                         (Select count(repasse_idx) as total FROM ". $this->TB_REPASSE." Where pedido_idx=tbPedido.pedido_idx ) as has_repasse
			                         FROM ". $this->TB_PEDIDO." as tbPedido
											INNER JOIN ". $this->TB_PEDIDO_ENDERECO." as tbPedidoEnd ON tbPedidoEnd.pedido_idx = tbPedido.pedido_idx
											LEFT JOIN  ". $this->TB_PEDIDO_STATUS." as tbStatus ON tbStatus.status_idx = tbPedido.status
											LEFT JOIN  ". $this->TB_PAGAMENTO." as tbFormaPg ON tbFormaPg.pagamento_idx = tbPedido.pagamento_idx
											LEFT JOIN  ". $this->TB_CADASTRO." as tbCadAfl ON tbCadAfl.cadastro_idx = tbPedido.afiliado_idx
											Where tbPedido.pedido_idx=".$pid."
                ");

			$pedido_itens = parent::select("SELECT DISTINCT tbPedidoItens.*,tbCur.nome as pnome,tbCur.imagem as pimage
							  FROM ". $this->TB_PEDIDO_ITENS." as tbPedidoItens
									INNER JOIN ". $this->TB_CURSO." as tbCur ON tbCur.curso_idx = tbPedidoItens.curso_idx
									Where tbPedidoItens.pedido_idx=".$pid." ");
			
			$cadastro = false;
			if(is_array($pedido)&&count($pedido)>0){
				$cadastro = parent::select("SELECT * FROM ". $this->TB_CADASTRO." Where cadastro_idx=".round($pedido[0]['cadastro_idx'])." ");
			}

			return array(
				'pedido'=>$pedido,
				'pedido_itens'=>$pedido_itens,
				'cadastro'=>$cadastro
			);

		}

		public function pedidoById($pid)
		{
			return parent::select("SELECT * FROM ". $this->TB_PEDIDO." Where pedido_idx=".$pid." ");
		}

		public function listUsersWithPedido($id=0){
			return parent::mListUsersWithPedido($id);
		}

		public function listSituacaoWithPedido($id=0){
			return parent::mListSituacaoWithPedido($id);
		}

		/**
		* Método para atualizar o status do pedido
		* @param int $id => codigo do pedido
		* @return void
		*/
		public function pedidoConfirmaPagamento($id=0,$pstatus)
		{
		
			$pedido = parent::sqlCRUD(array('pedido_idx' => $id), 'pedido_idx,cadastro_idx, pedido_chave,pagamento_idx', $this->TB_PEDIDO, '', 'S', 0, 0);
			$pedido_itens = parent::select("SELECT tbPedidoItens.*,tbCur.nome as pnome, tbCur.imagem as pimage, tbCur.disponibilidade_tempo_dias
														FROM ". $this->TB_PEDIDO_ITENS." as tbPedidoItens
														INNER JOIN ". $this->TB_CURSO." as tbCur ON tbCur.curso_idx = tbPedidoItens.curso_idx
														Where tbPedidoItens.pedido_idx=" . $id . "
													");

			$message = '';
			$pedido_detalhe = "";
			if(is_array($pedido_itens) && count($pedido_itens) > 0){
				// $valor_frete = $pedido[0]['frete_valor'];
				$pedido_detalhe =
				'
					<tr>
						<td bgcolor="#efeeee" style="padding: 10px 20px; border-radius: 3px 3px 0px 0px;">
							<b style="font-size: 17px;">Itens do seu pedido</b>
						</td>
					</tr>
					<tr>
						<td bgcolor="#ffffff" style="padding: 10px 20px; border-radius: 0px 0px 3px 3px;">
							<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="padding: 20px 10px 25px;">
								<tr>
									<td></td>
									<td><b>Item:</b></td>
									<td><b>Valor:</b></td>
								</tr>
				';

									$valor_subtotal_itens = 0;
									foreach ($pedido_itens as $key => $item) {

										$subtotal_item = $item["item_valor"];
										$valor_subtotal_itens += $subtotal_item;

										// $opcoes = self::getOpcaoDadoValorByVariacao($item['variacao_idx']);
										$opcoes_txt = "";
										// if(is_array($opcoes) && count($opcoes) > 0){
										// 	$opcoes_txt .= "<br />";
										// 	foreach ($opcoes as $key => $opcao) {
										// 		$opcoes_txt .= '<b>'.$opcao['dado_nome'].'</b>: '.$opcao['valor_nome'].'<br />';
										// 	}
										// }

										$pedido_detalhe .= '
														<tr>
															<td style="padding-bottom:20px;">'.	((!is_null($item['pimage']) && trim($item['pimage'])!="Null")?'<img width="150" src="http://'.$_SERVER["HTTP_HOST"].'/sitecontent/curso/curso/images/'.$item['pimage'].'" />':'')	 .'</td>
															<td>'.$item["pnome"].'</td>
															<td width="100" style="padding-right: 25px;" > R$ '.number_format($subtotal_item, 2, ',', '.').' </td>
														</tr>
														';

									}

				$pedido_detalhe .=
				'

							</td>
						</tr>
					</table>
					<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="padding: 20px 0px 25px;">
						<tr>
							<td bgcolor="#ededed" style="padding: 8px 20px 10px;">
								<b style="font-size: 17px;">Valores à pagar</b>
							</td>
						</tr>
						<tr>
							<td bgcolor="#ffffff" style="padding: 10px 20px 12px;">
								<div style="float: left; font-size: 15px;">Total: </div>
								<div style="float: right;">R$ ' . number_format($valor_subtotal_itens, 2, ',', '.') . '</div>
							</td>
						</tr>
						<tr>
							<td bgcolor="#ffffff" style="padding: 15px 20px 0px;">
								<div style="font-size: 15px;" >
									Esta é uma mensagem automática, não é necessário responder. Em caso de dúvidas, críticas e/ou sugestões, escreva para '.trim(Sis::config("ECOMMERCE_EMAIL_ATENDIMENTO")).'.
								</div>
							</td>
						</tr>
					</table>
				';

				reset($pedido_itens);

			}

			$desconto_valor = 0;

			$totalDaVenda = $valor_subtotal_itens;

			$sendMail = false;
			switch($pstatus){
				case 2: //Confirmar pagamento do pedido - Status no banco: 6
					
					$subject = "Seu pagamento foi confirmado.";
					$message .= "<p><strong>Temos uma boa notícia: o seu pagamento foi confirmado!</strong></p>
									<p>Para conferir as informações e acompanhar o status do seu pedido, basta logar em nosso <a href='" . Sis::config('CLI_URL') . "' target='_blank'>site</a> e acessar a seção: Minha Conta e Minhas Compras.</p>" . $pedido_detalhe;
					$sendMail = true;
					$dados = parent::sqlCRUD(array('pedido_idx' => $id, 'pagamento_status' => 1), 'cadastro_idx', $this->TB_PEDIDO, '', 'U', 0, 0);
					
					break;
				case 4: //Confirmar pedido - Status no banco: 4
					
					$subject = "Sua compra foi confirmada.";
					$message .= "<p><strong>Temos uma boa notícia: a sua compra foi confirmada!</strong></p>
									<p>Para conferir as informações e acompanhar o status da sua compra, basta logar em nosso <a href='" . Sis::config('CLI_URL') . "' target='_blank'>site</a> e acessar a seção: Minha Conta e Minhas Compras.</p>" . $pedido_detalhe;
					$sendMail = true;
					$dados = parent::sqlCRUD(array('pedido_idx' => $id, 'status' => 2), 'cadastro_idx', $this->TB_PEDIDO, '', 'U', 0, 0);

					//Processa a matricula do usuário nos cursos comprados.
					if (is_array($pedido_itens)&&count($pedido_itens)>0){
						foreach ($pedido_itens as $key => $pedidoItem) {
							if ( ! self::checkUserSubscription($pedidoItem['curso_idx'],$pedido[0]['cadastro_idx']) ){
								$itemData = array(
									'pedido_idx'=>$pedido[0]['pedido_idx'],
									'curso_idx'=>$pedidoItem['curso_idx'],
									'cadastro_idx'=>$pedido[0]['cadastro_idx'],
									'expira'=>0,
									'expira_data'=>date("Y-m-d H:i:s"),
									'data_cadastro'=>date("Y-m-d H:i:s"),
								);
								if ($pedidoItem['disponibilidade_tempo_dias']>0) {
									$itemData['expira']=1;
									$itemData['expira_data']=date("Y-m-d H:i:s",strtotime("+".(int)$pedidoItem['disponibilidade_tempo_dias']." days"));
								}

								parent::sqlCRUD($itemData,'', $this->TB_CURSO_INSCRITOS, '', 'I', 0, 0);

								try {
									self::sendEmailSubscription($pedidoItem['curso_idx'],$pedido[0]['cadastro_idx']);
								} catch (Exception $e) {
									die( $e->getMenssage() );
									exit();
								}
							}
						}
					}
					
					break;
				case 3: //Cancelar Pedido - Status no banco: 3
					
					$subject = "Sua compra foi cancelada.";
					$message .= "<p>Infelizmente, não conseguimos finalizar sua compra!</p>
									<p>Houve algum problema na hora de processá-lo. Por favor, tente novamente em nosso site ou mande uma mensagem pra gente. Vai ser um prazer te ajudar na sua compra!</p>
									<p>Para conferir as informações e acompanhar o status da sua compra, basta logar em nosso <a href='" . Sis::config('CLI_URL') . "' target='_blank'>site</a> e acessar a seção: Minha Conta e Minhas Compras.</p>
									" . $pedido_detalhe;
					$sendMail = true;
					$dados = parent::sqlCRUD(array('pedido_idx' => $id, 'status' => 3), 'cadastro_idx', $this->TB_PEDIDO, '', 'U', 0, 0);

					//Remove a inscrição do usuário no curso adquirido.
					self::userUnsubscribe($id,$pedido_itens[0]['curso_idx'],$pedido[0]['cadastro_idx']);

					//Realiza o estorno do pagamento junto ao gateway
					if ((int)$pedido[0]['pagamento_idx']==1)
					{
						$ecomPagamento = new EcommercePagamento();
						try {

							$ecomPagamento->setPagamentoId(1);
							$ecomPagamento->setPedidoId($id);
							$ecomPagamento->registrarGateway('production');
							$ecomPagamento->loadTransaction();
							$ecomPagamento->gatewaySetCredentials();

							$pagamento_status=0;
							if ($ecomPagamento->gateway->getTransactionStatusCode()=='paid') {
								//Faturas pagas são processadas com reembolso
								$retornoCancelamento = $ecomPagamento->gateway->refund($totalDaVenda);
								$pagamento_status=3;
							}else{//Faturas não pagas podem ser canceladas
								//Cancela a venda
								$retornoCancelamento = $ecomPagamento->gateway->cancel($totalDaVenda);
							}

							//Obtem os dados atualizados da transação em sua origem.
							$ecomPagamento->gateway->updateFromOrigin();
							//Atualiza o registro da transação atualizada
							$ecomPagamento->transactionUpdate();

							if (! (boolean)$retornoCancelamento ) {
								Sis::setAlert('O pedido foi cancelado no sistema, mas ocorreu um erro ao cancelar junto ao gateway de pagamento. Consulte o dashboard do gateway para mais detalhes.', 4, "?mod=" . $_GET['mod'] . "&pag=" . $_GET['pag'] . "&act=".$_GET['act']."&pid=".$_GET['pid']);
								exit();
							}

							//Atualiza o status do pagamento.
							parent::sqlCRUD(array('pedido_idx' => $id, 'pagamento_status' => $pagamento_status), 'cadastro_idx', $this->TB_PEDIDO, '', 'U', 0, 0);

						} catch (Exception $e) {
							Sis::setAlert('O pedido foi cancelado no sistema, mas ocorreu um erro ao cancelar junto ao gateway de pagamento. Detalhes: '.$e->getMessage().' ', 4, "?mod=" . $_GET['mod'] . "&pag=" . $_GET['pag'] . "&act=".$_GET['act']."&pid=".$_GET['pid']);
							exit();
						}
					}
					//Realiza o estorno do pagamento junto ao gateway

					break;
			}
			// exit();

			/**
			 * Caso precise ser enviado e-mail,
			 * Ele seleciona o nome e e-mail do usuário para enviar o e-mail
			 */

			if($sendMail)
			{
				$nome = "";
				$email = "";
				if(is_array($pedido) && count($pedido) > 0){
					$pedido = $pedido[0];
					$usuario = parent::sqlCRUD(array('cadastro_idx' => $pedido['cadastro_idx']), 'nome_completo, nome_informal, email', $this->TB_CADASTRO, '', 'S', 0, 0);
					if(is_array($usuario) && count($usuario) > 0){
						$usuario = $usuario[0];
						$nome = (trim($usuario['nome_informal']) == "") ? $usuario['nome_completo'] : $usuario['nome_informal'];
						$email = $usuario['email'];
					}
				}
				// echo '<pre>'; var_dump($pedido); echo('</pre>');

				$message =
				'
				<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="padding: 20px 0px 0px;">
					<tr>
						<td bgcolor="#ffffff" style="padding: 5px 0 8px 0;">
							<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="padding: 0 0px;">
								<tr>
									<td width="50%">
										<p style="font-size: 24px; color:#3580b5;">Nº do pedido: <span style="color:#565656; font-size: 24px;">'.$pedido['pedido_chave'].'</span></p>
									</td>
									<td width="50%" align="right" >
										<span>Em 27/11/2014 às 09:33</span>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td style="padding: 18px 30px; border-top: 1px solid #e7e7e7; border-bottom: 1px solid #e7e7e7;">
							<p><strong style="font-size:15px">Olá, ' . $nome . '!</strong></p>' . $message .'
						</td>
					</tr>
				</table>
				';

				$newMensagem = Sis::returnMessageBodyClient($subject);
				$message_body = str_replace("[HTML_DADOS]", $message, $newMensagem);


				// ob_clean();
				// echo $message_body;
				

				if(class_exists("PHPMailer\PHPMailer\PHPMailer")){

					/**/
					$mail = new PHPMailer();
					$mail->CharSet     = "UTF-8";
					$mail->ContentType = "text/html";

					$mail->IsSMTP();
					$mail->Host        = Sis::config("CLI_SMTP_HOST");
					if(Sis::config("CLI_SMTP_PORTA")!="undefined"){ $mail->Port = Sis::config("CLI_SMTP_PORTA"); }
					if(Sis::config("CLI_SMTP_CONEXAO")){ $mail->SMTPSecure = Sis::config("CLI_SMTP_CONEXAO"); }

					if(Sis::config("CLI_SMTP_MAIL")!="")
					{
						$mail->SMTPAuth    = true;
						$mail->Username    = Sis::config("CLI_SMTP_MAIL");
						$mail->Password    = Sis::config("CLI_SMTP_PASS");
					}
					$CLI_MAIL_CONTATO = explode(",",trim(Sis::config("CLI_MAIL_CONTATO")));
			        $fromEmail = trim($CLI_MAIL_CONTATO[0]);
			        $mail->From        = $fromEmail;
			        $mail->FromName    = Sis::config("CLI_NOME");

					$mail->AddAddress(trim($email), $nome);
					if(trim(Sis::config("ECOMMERCE_EMAIL_ATENDIMENTO")) != ""){
						$mail->AddBCC(trim(Sis::config("ECOMMERCE_EMAIL_ATENDIMENTO")));
					}

					$mail->AddReplyTo(Sis::config("CLI_SMTP_MAIL"), Sis::config("CLI_NOME"));
					$mail->Subject = $subject;
					$mail->Body = $message_body;
					$mail->Send();

				}

				// exit();

			}
			if(isset($dados) && $dados != FALSE){
				Sis::setAlert('Dados salvos com sucesso!', 3, "?mod=" . $_GET['mod'] . "&pag=" . $_GET['pag'] . "&act=".$_GET['act']."&pid=".$_GET['pid']);
			}else{
				Sis::setAlert('Ocorreu um erro ao salvar dados!', 4, "?mod=" . $_GET['mod'] . "&pag=" . $_GET['pag'] . "&act=".$_GET['act']."&pid=".$_GET['pid']);
			}

		}


		/**
		* Método para atualizar o status do pedido
		* @param int $id => codigo do pedido
		* @return void
		*/
		public function pedidoNotificaCancelamento($idPedido,$pedidoData=null)
		{
			
			if (is_null($pedidoData)) {
				$pedidoData = self::pedidoListSelected($idPedido);
			}

			$pedido = $pedidoData['pedido'][0];
			$pedido_itens = $pedidoData['pedido_itens'];
			$cadastro = $pedidoData['cadastro'][0];

			$message = '';
			$pedido_detalhe = "";
			if(is_array($pedido_itens) && count($pedido_itens) > 0){
				
				$pedido_detalhe =
				'
					<tr>
						<td bgcolor="#efeeee" style="padding: 10px 20px; border-radius: 3px 3px 0px 0px;">
							<b style="font-size: 17px;">Itens do seu pedido</b>
						</td>
					</tr>
					<tr>
						<td bgcolor="#ffffff" style="padding: 10px 20px; border-radius: 0px 0px 3px 3px;">
							<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="padding: 20px 10px 25px;">
								<tr>
									<td></td>
									<td><b>Item:</b></td>
									<td><b>Valor:</b></td>
								</tr>
				';

				$valor_subtotal_itens = 0;
				foreach ($pedido_itens as $key => $item) {

					$subtotal_item = $item["item_valor"];
					$valor_subtotal_itens += $subtotal_item;

					$pedido_detalhe .= '
									<tr>
										<td style="padding-bottom:20px;">'.	((!is_null($item['pimage']) && trim($item['pimage'])!="Null")?'<img width="150" src="http://'.$_SERVER["HTTP_HOST"].'/sitecontent/curso/curso/images/'.$item['pimage'].'" />':'')	 .'</td>
										<td>'.$item["pnome"].'</td>
										<td width="100" style="padding-right: 25px;" > R$ '.number_format($subtotal_item, 2, ',', '.').' </td>
									</tr>
									';

				}

				$pedido_detalhe .=
				'

							</td>
						</tr>
					</table>
					<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="padding: 20px 0px 25px;">
						<tr>
							<td bgcolor="#ededed" style="padding: 8px 20px 10px;">
								<b style="font-size: 17px;">Valores à pagar</b>
							</td>
						</tr>
						<tr>
							<td bgcolor="#ffffff" style="padding: 10px 20px 12px;">
								<div style="float: left; font-size: 15px;">Total: </div>
								<div style="float: right;">R$ ' . number_format($valor_subtotal_itens, 2, ',', '.') . '</div>
							</td>
						</tr>
						<tr>
							<td bgcolor="#ffffff" style="padding: 15px 20px 0px;">
								<div style="font-size: 15px;" >
									Esta é uma mensagem automática, não é necessário responder. Em caso de dúvidas, críticas e/ou sugestões, escreva para '.trim(Sis::config("ECOMMERCE_EMAIL_ATENDIMENTO")).'.
								</div>
							</td>
						</tr>
					</table>
				';

				reset($pedido_itens);

			}

			$desconto_valor = 0;

			$totalDaVenda = $valor_subtotal_itens;
			
			$subject = "Sua compra foi cancelada.";
			$message .= "<p>Infelizmente, não conseguimos finalizar sua compra!</p>
							<p>Houve algum problema na hora de processá-lo. Por favor, tente novamente em nosso site ou mande uma mensagem pra gente. Vai ser um prazer te ajudar na sua compra!</p>
							<p>Para conferir as informações e acompanhar o status da sua compra, basta logar em nosso <a href='" . Sis::config('CLI_URL') . "' target='_blank'>site</a> e acessar a seção: Minha Conta e Minhas Compras.</p>
							" . $pedido_detalhe;
			
			/**
			 * Caso precise ser enviado e-mail.
			 * Ele seleciona o nome e e-mail do usuário para enviar o e-mail
			 */

				$nome = (trim($cadastro['nome_informal']) == "") ? $cadastro['nome_completo'] : $cadastro['nome_informal'];
				$email = $cadastro['email'];

				$message =
				'
					<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="padding: 20px 0px 0px;">
						<tr>
							<td bgcolor="#ffffff" style="padding: 5px 0 8px 0;">
								<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="padding: 0 0px;">
									<tr>
										<td width="50%">
											<p style="font-size: 24px; color:#3580b5;">Nº do pedido: <span style="color:#565656; font-size: 24px;">'.$pedido['pedido_chave'].'</span></p>
										</td>
										<td width="50%" align="right" >
											<span>Em 27/11/2014 às 09:33</span>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td style="padding: 18px 30px; border-top: 1px solid #e7e7e7; border-bottom: 1px solid #e7e7e7;">
								<p><strong style="font-size:15px">Olá, ' . $nome . '!</strong></p>' . $message .'
							</td>
						</tr>
					</table>
				';

				$newMensagem = Sis::returnMessageBodyClient($subject);
				$message_body = str_replace("[HTML_DADOS]", $message, $newMensagem);


				if(class_exists("PHPMailer\PHPMailer\PHPMailer")){

					/**/
					$mail = new PHPMailer();
					$mail->CharSet     = "UTF-8";
					$mail->ContentType = "text/html";

					$mail->IsSMTP();
					$mail->Host        = Sis::config("CLI_SMTP_HOST");
					if(Sis::config("CLI_SMTP_PORTA")!="undefined"){ $mail->Port = Sis::config("CLI_SMTP_PORTA"); }
					if(Sis::config("CLI_SMTP_CONEXAO")){ $mail->SMTPSecure = Sis::config("CLI_SMTP_CONEXAO"); }

					if(Sis::config("CLI_SMTP_MAIL")!="")
					{
						$mail->SMTPAuth    = true;
						$mail->Username    = Sis::config("CLI_SMTP_MAIL");
						$mail->Password    = Sis::config("CLI_SMTP_PASS");
					}
					$CLI_MAIL_CONTATO = explode(",",trim(Sis::config("CLI_MAIL_CONTATO")));
			        $fromEmail = trim($CLI_MAIL_CONTATO[0]);
			        $mail->From        = $fromEmail;
			        $mail->FromName    = Sis::config("CLI_NOME");

					$mail->AddAddress(trim($email), $nome);
					if(trim(Sis::config("ECOMMERCE_EMAIL_ATENDIMENTO")) != ""){
						$mail->AddBCC(trim(Sis::config("ECOMMERCE_EMAIL_ATENDIMENTO")));
					}

					$mail->AddReplyTo(Sis::config("CLI_SMTP_MAIL"), Sis::config("CLI_NOME"));
					$mail->Subject = $subject;
					$mail->Body = $message_body;
					$mail->Send();

				}

		}


		public function pedidoCursoProcessainscritos($pedidoId)
		{
			$pedido = parent::sqlCRUD(array('pedido_idx' => $pedidoId), 'pedido_idx,cadastro_idx, pedido_chave,pagamento_idx', $this->TB_PEDIDO, '', 'S', 0, 0);
			$pedido_itens = parent::select("SELECT tbPedidoItens.*,tbCur.nome as pnome, tbCur.imagem as pimage, tbCur.disponibilidade_tempo_dias
				FROM ". $this->TB_PEDIDO_ITENS." as tbPedidoItens
				INNER JOIN ". $this->TB_CURSO." as tbCur ON tbCur.curso_idx = tbPedidoItens.curso_idx
				Where tbPedidoItens.pedido_idx=" . $pedidoId . "
			");

			//Processa a matricula do usuário nos cursos comprados.
			if (is_array($pedido_itens)&&count($pedido_itens)>0){
				foreach ($pedido_itens as $key => $pedidoItem) {

					if ( ! self::checkUserSubscription($pedidoItem['curso_idx'],$pedido[0]['cadastro_idx']) ){
						
						$itemData = array(
							'pedido_idx'=>$pedido[0]['pedido_idx'],
							'curso_idx'=>$pedidoItem['curso_idx'],
							'cadastro_idx'=>$pedido[0]['cadastro_idx'],
							'expira'=>0,
							'expira_data'=>date("Y-m-d H:i:s"),
							'data_cadastro'=>date("Y-m-d H:i:s"),
						);
						if ($pedidoItem['disponibilidade_tempo_dias']>0) {
							$itemData['expira']=1;
							$itemData['expira_data']=date("Y-m-d H:i:s",strtotime("+".(int)$pedidoItem['disponibilidade_tempo_dias']." days"));
						}
						parent::sqlCRUD($itemData,'', $this->TB_CURSO_INSCRITOS, '', 'I', 0, 0);

						try {
							self::sendEmailSubscription($pedidoItem['curso_idx'],$pedido[0]['cadastro_idx']);
						} catch (Exception $e) {
							die( $e->getMenssage() );
							exit();
						}

					}

				}
			}
		}

		public function pedidoSalvaObservacoes($pedido_idx){
			$observacoes = isset($_POST['observacoes']) ? Text::clean($_POST['observacoes']) : "";
			$dados = parent::sqlCRUD(array('pedido_idx' => $pedido_idx, 'observacoes' => $observacoes), '', $this->TB_PEDIDO, '', 'U', 0, 0);
			if(isset($dados) && $dados != FALSE){
				Sis::setAlert('Dados salvos com sucesso!', 3, "?mod=" . $_GET['mod'] . "&pag=" . $_GET['pag'] . "&act=".$_GET['act']."&pid=".$_GET['pid']);
			}else{
				Sis::setAlert('Ocorreu um erro ao salvar dados!', 4, "?mod=" . $_GET['mod'] . "&pag=" . $_GET['pag'] . "&act=".$_GET['act']."&pid=".$_GET['pid']);
			}
		}

		public function checkUserSubscription($cursoId,$cadastroId)
		{
			$isSubscribed = false;
			//Ou a matricula nao expira ou está dentro da validade se estiver marcada para expirar.
			$reg = self::select("SELECT inscricao_idx FROM ".$this->TB_CURSO_INSCRITOS." as tbInsc
				Where curso_idx=".(int)$cursoId." And cadastro_idx=".(int)$cadastroId."
				And ( expira=0 Or ( expira=1 And expira_data>=Now() ) )
			");
			if (is_array($reg)&&count($reg)>0) {
				$isSubscribed=true;
			}
			return $isSubscribed;
		}

		//Remove a inscrição do aluno em determinado curso.
		public function userUnsubscribe($pedidoId,$cursoId,$cadastroId)
		{
			self::delete("DELETE FROM ".$this->TB_CURSO_INSCRITOS." as tbInsc
				Where pedido_idx=".(int)$pedidoId." And curso_idx=".(int)$cursoId." And cadastro_idx=".(int)$cadastroId."
			");
		}

		public function sendEmailSubscription($cursoId,$cadastroId)
		{
			$cursoExists=false;
			$cadastroExists=false;

			$curso = parent::select("SELECT nome,transacional_inscricao_corpo FROM ". $this->TB_CURSO ." Where curso_idx=".$cursoId);
			if (is_array($curso)&&count($curso)>0) {
				$curso = $curso[0];
				$cursoExists=true;
			}
			$cadastro = parent::select("SELECT nome_completo as nome,email FROM ". $this->TB_CADASTRO ." Where cadastro_idx=".$cadastroId);
			if (is_array($cadastro)&&count($cadastro)>0) {
				$cadastro = $cadastro[0];
				$cadastroExists=true;
			}

			if ($cadastroExists && $cursoExists) {

				$subject = 'Inscrição no curso "'.$curso['nome'].'" realizada com sucesso.';
				$HTML_mensagem 	= Sis::returnMessageBodyClient($subject);

				$corpo_mensagem = "Ol&aacute; ".$cadastro['nome'].", seja muito bem-vindo(a) ao curso <b>".$curso['nome']."</b>.
				<br/><br/> Fique atento as datas de libera&ccedil;&atilde;o das aulas e n&atilde;o esque&ccedil;a de montar seu cronograma de estudos.
				<br/><br/> Bons estudos! <br/><br/> Atenciosamente, <br /> ".Sis::config("CLI_NOME");

				$transacional_inscricao_corpo = trim(strip_tags($curso['transacional_inscricao_corpo']));
				if($transacional_inscricao_corpo!="") {
					$corpo_mensagem = $curso['transacional_inscricao_corpo'];
					$corpo_mensagem = str_replace("{nome_usuario}",$cadastro['nome'],$corpo_mensagem);
					$corpo_mensagem = str_replace("{nome_curso}",$curso['nome'],$corpo_mensagem);
				}

				$HTML_mensagem = str_replace("[HTML_DADOS]",$corpo_mensagem,$HTML_mensagem);

				// ob_clean();
				// echo ($HTML_mensagem);
				// exit();

				if(class_exists("PHPMailer\PHPMailer\PHPMailer")){

					try {

						$mail = new PHPMailer();

						$mail->CharSet     = "UTF-8";
						$mail->ContentType = "text/html";

						$mail->IsSMTP();
						$mail->SMTPDebug = 0;

						$mail->Host = Sis::config("CLI_SMTP_HOST");
						if(Sis::config("CLI_SMTP_PORTA")!="undefined"){ $mail->Port = Sis::config("CLI_SMTP_PORTA"); }
						if(Sis::config("CLI_SMTP_CONEXAO")){ $mail->SMTPSecure = Sis::config("CLI_SMTP_CONEXAO"); }

						if(Sis::config("CLI_SMTP_MAIL")!="")
						{
							$mail->SMTPAuth    = true;
							$mail->Username    = Sis::config("CLI_SMTP_MAIL");
							$mail->Password    = Sis::config("CLI_SMTP_PASS");
						}
						$mail->From        = Sis::config("CLI_SMTP_MAIL");
			    		$mail->FromName    = Sis::config("CLI_NOME");

			    		$mail->AddAddress(trim($cadastro['email']), trim($cadastro['nome']));

						$mail->Subject = $subject;
						$mail->Body = $HTML_mensagem;
						if (!$mail->Send()) {
			                // echo 'Mailer Error: ' . $mail->ErrorInfo;
			                // exit();
			            }else{
			                // die("ok");
			            }

					} catch (Exception $e) {
						// die( $e->getMessage() );
					}
					
				}

			}

		}

		public function theDelete()
		{
			$pid = isset($_GET['pid']) ? Text::clean((int)$_GET['pid']) : 0;

			$array = array('pedido_idx' => $pid);

			//Dado complementares do produto
			$p_itens = parent::sqlCRUD($array, '', $this->TB_PEDIDO_ITENS, '', 'D', 0, 0);
            $p_endereco = parent::sqlCRUD($array, '', $this->TB_PEDIDO_ENDERECO, '', 'D', 0, 0);
            $p_pgpagseguro = parent::sqlCRUD($array, '', $this->TB_PEDIDO_PGSEGURO, '', 'D', 0, 0);
            $p_pgpagseguro = parent::sqlCRUD($array, '', $this->TB_PEDIDO_PAYPAL, '', 'D', 0, 0);
            $pedido = parent::sqlCRUD($array, '', $this->TB_PEDIDO, '', 'D', 0, 0);

            ob_end_clean();
			if(isset($pedido) && $pedido !== NULL){
				Sis::setAlert('Dados removidos com sucesso!', 3,"?mod=ecommerce&pag=pedido");
			} else {
				Sis::setAlert('Ocorreu um erro ao remover os dados!', 4);
			}
		}

		


		public function pedidoCupom($id){
        	return self::select("SELECT * FROM ". $this->TB_CUPOM ." Where cupom_idx=".$id." ");
		}


		public function autorizarSolicitacaoDeRepasse($pedido_id){

			//Recupera o valor percentual de comissão a ser repassada.
			$taxaSobreVendaPercentual = ((int)Sis::config("PLATAFORMA-COMISSAO-PERCENTUAL")>0)?(int)Sis::config("PLATAFORMA-COMISSAO-PERCENTUAL"):0;
			$taxaSobreVendaPercentual = ($taxaSobreVendaPercentual>50)?0:$taxaSobreVendaPercentual;
			if($taxaSobreVendaPercentual>0) { //processa o registro dos itens
				
				//Ajusta o valor de percentual para o formato decimal
				$taxaSobreVendaPercentual = $taxaSobreVendaPercentual/100;

				// $this->REPASSE
				// $this->REPASSE_ITENS 

				//Recupera a lista de itens para registrar a repasse.
				// tbCadastro.cadastro_idx, 
				// 
				$queryStr = "SELECT tbItens.pedido_idx, tbItens.item_valor, tbItens.item_idx, tbCurso.produtor_idx, tbCadastro.cadastro_idx
							FROM ".$this->TB_PEDIDO_ITENS." as tbItens
							INNER JOIN ".$this->TB_CURSO." as tbCurso ON tbItens.curso_idx=tbCurso.curso_idx
							INNER JOIN ".$this->TB_CADASTRO." as tbCadastro ON tbCurso.produtor_idx=tbCadastro.cadastro_idx
							WHERE tbItens.pedido_idx=".$pedido_id."
							";

				// var_dump($queryStr);
				$itens_pedido = parent::select($queryStr);
				// var_dump($itens_pedido);
				// exit();


				if (!(is_array($itens_pedido)&&count($itens_pedido)>0)) {
					Sis::setAlert("Não foram identificados itens para o pedido informado. ", 4);
				}

				//Obtem a quantidade de parcelas do pagamento.
				$ecomPagamento = new EcommercePagamento();
				$ecomPagamento->setPagamentoId(1);
				$ecomPagamento->registrarGateway('sandbox');
				$ecomPagamento->setPedidoId($pedido_id);
				$ecomPagamento->loadTransaction();
				$pedidoTransactionDueDate = $ecomPagamento->gateway->billGetDueDate();
				$pedidoTransaction = $ecomPagamento->gateway->getTransaction();
				$pedidoTransacaoId = $ecomPagamento->getTransacaoIdx();
				$parcelamento = 1;

				if ((int)$pedidoTransaction->installments>1 && $ecomPagamento->gateway->getPaymentMethod()=='credit_card') {
					$parcelamento = (int)$pedidoTransaction->installments;
				}

				for ($parcela=1; $parcela <= $parcelamento; $parcela++) {
				//Executa os registros de repasse de acordo com cada parcela de pagamento.
					
					$cadastroRepasse = array();
					foreach ($itens_pedido as $key => $item) {
						$cadastroID = $item['produtor_idx'];
						if ((int)$cadastroID==0 || (int)$pedido_id==0 ) {
							Sis::setAlert("Ocorreu um problema ao identificar o pedido e produtor, tente novamente mais tarde, se o problema persistir entre em contato com o administrador do sistema. ", 4);
							exit();
						}
						if (!array_key_exists($cadastroID, $cadastroRepasse)) {
							$repasseID = 0;
							//Verifica se o repasse do pedido para o usuário lojista existe e cria se necessário.
							$queryStrRepasse = "SELECT repasse_idx FROM ".$this->TB_REPASSE." WHERE pedido_idx=".(int)$pedido_id." And cadastro_idx=".(int)$cadastroID." And transacao_idx=".(int)$pedidoTransacaoId." And transacao_parcela=".(int)$parcela." ";
							$repasse = parent::select($queryStrRepasse);
							if (is_array($repasse)&&count($repasse)>0) {
								//Exisbe um registro de repasse.
								$repasseID = $repasse[0]['repasse_idx'];
							}else{
								//Data de pagamento da parcela
								if ($parcela>1) {
									$parcelaData = date("Y-m-d",strtotime($pedidoTransactionDueDate . "+ ".($parcela-1)." months "));
								}else{
									$parcelaData = $pedidoTransactionDueDate;
								}
								//Cria um registro de repasse.
								$array = array(
									'status'    => 0,
									'cadastro_idx' => $cadastroID,
									'pedido_idx'   => $pedido_id,
									'transacao_idx' => $pedidoTransacaoId,
									'transacao_parcela' => $parcela,
									'transacao_parcelas_total' => $parcelamento,
									'transacao_parcela_status' => 'pending',
									'transacao_parcela_data' => $parcelaData,
								);
								$messageLog = '';
								$repasseID = parent::sqlCRUD($array, '', $this->TB_REPASSE, $messageLog, 'I', 0, 0);
							}
							$cadastroRepasse[$cadastroID] = $repasseID;
						}
					}
					// var_dump($cadastroRepasse);
					// exit();

					$valor_subtotal_itens=0;
					if (is_array($itens_pedido)&&count($itens_pedido)>0) {

						//Verifica se o item já está registrado e relacionado ao repasse e cria se necessário.

						foreach ($itens_pedido as $key => $item) {
							$subtotal_item = $item["item_valor"];
							$valor_subtotal_itens += $subtotal_item;
						}

	                  	reset($itens_pedido);

	                  	//PEDIDO COM DESCONTO
	                  	//Calcula o desconto que haverá nos itens antes de definir o valor de comissão
						$pedido = self::pedidoById($pedido_id);
						$pedido = $pedido[0];
						$desconto_percent = 0;
						if ($pedido['cupom_idx']!=0){
							if ($pedido['cupom_tipo']==1) {//Já é percentual
								$desconto_percent = $pedido['cupom_valor']/100;
							}else{//Calculo reverso, define em percentual qual o desconto obtido no pedido.
								$desconto_percent = $pedido['desconto_valor'] / $valor_subtotal_itens;
							}
						}

						foreach ($itens_pedido as $key => $item) {
							
							//Verifica se o item já foi registrado anteriormente.
							$repasse_item = parent::select("SELECT repasse_item_idx FROM ".$this->TB_REPASSE_ITENS." WHERE pedido_idx=".$item['pedido_idx']." And pedido_item_idx=".$item['item_idx']." And repasse_idx=".(int)$repasseID."  ");

							if (!(is_array($repasse_item)&&count($repasse_item)>0)){
								//Registra o item de comissão
								$valor_item = $item['item_valor'];

								//TO DO - o critério de aplicação do desconto deve levar em considerácão a LOJA que participa.
								if ($desconto_percent>0) {//Calcula o valor do item com o desconto.
									$valor_item = $item['item_valor'] - ($item['item_valor']*$desconto_percent);	
								}

								$valor_repasse = $valor_item-($valor_item*$taxaSobreVendaPercentual);

								//Define o valor de repasse de acordo com o que foi, caso tenha sido, parcelado.
								$valor_repasse_parcela = $valor_repasse/$parcelamento;

								$dados_repasse = array(
									'repasse_idx'=> (int)$cadastroRepasse[$item['cadastro_idx']],
									'pedido_idx'=> $item['pedido_idx'],
									'pedido_item_idx'=> $item['item_idx'],
									'quantidade'=> 1,
									'valor_repasse'=> number_format($valor_repasse_parcela,2,".",""),
									'data_cadastro'=> date("Y-m-d H:i:s")
								);
								parent::sqlCRUD($dados_repasse, '', $this->TB_REPASSE_ITENS, '', 'I', 0, 0);

							}

						}

						reset($itens_pedido);

					}

				}

				// var_dump($itens_pedido);
				// exit();
				Sis::setAlert("Os registros de repasse para o pedido ".$pedido_id." foram criados com sucesso!", 3,"?mod=ecommerce&pag=pedido&act=view&pid=".$pedido_id);

			}else{
				Sis::setAlert("O valor de percentual é igual a 0 (zero). <br />Certifique-se de que a variável de ambiente \"PLATAFORMA-COMISSAO-PERCENTUAL\" está registrada no sistema e se ela não ultrapassa o valor de 50% para comissão ", 4);
				
			}
		}


	}
?>