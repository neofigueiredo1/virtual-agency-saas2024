<?php

	include("admin/modulos/cadastro/cadastro-model.php");

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;

	class cadastro_views extends HandleSql{

		public $TB_CADASTRO;
		public $TB_CADASTRO_INTERESSE;
		public $TB_CADASTRO_INTERESSE_SELECIONA;

		public function __construct()
		{
			parent::__construct();

			$this->mod = "cadastro";
			$this->pag = "cadastro";

			$this->TB_CADASTRO = self::getPrefix() . "_cadastro";
			$this->TB_CADASTRO_INTERESSE = self::getPrefix() . "_cadastro_interesse";
			$this->TB_CADASTRO_INTERESSE_SELECIONA = self::getPrefix() . "_cadastro_seleciona";

			$basedir = $_SERVER['DOCUMENT_ROOT'];
			if(!is_dir($basedir.DS.PASTA_CONTENT.DS.$this->pag)){
				mkdir($basedir.DS.PASTA_CONTENT.DS.$this->pag);
			}
			if(!is_dir($basedir.DS.PASTA_CONTENT.DS.$this->pag.DS."profile")){
				mkdir($basedir.DS.PASTA_CONTENT.DS.$this->pag.DS."profile");
			}

			//Usado para validar a mensagem de Obrigado no cadastro.
			// $userDados = array('nome_completo'=>'Neo Figueiredo','email'=>'neo@being.com.br');
			// self::sendMailObrigado($userDados);
		}

		public function updateUrlUserAll(){

			$cadastros = parent::sqlCRUD(array(), '', $this->TB_CADASTRO, '', 'S', 0, 0);
			foreach ($cadastros as $key => $cadastro) {
				if (trim($cadastro['url_user'])!="") {
					self::defineUrlRewrite($cadastro);
				}
			}

		}

		public function getView($nome="")
		{
			if(file_exists('admin/modulos/cadastro/views/' . $nome . '.php')){
				require( $nome . '.php');
			}else{
				echo 'View não encontrada';
			}
		}

		public function getCadastro($uid=0, $dados="")
		{
			$array = array('cadastro_idx' => $uid);
        	return parent::sqlCRUD($array, $dados, $this->TB_CADASTRO, '', 'S', 0, 0);
		}

		public function getAreasInteresse()
		{
			$array = array('status'=>1);
			return parent::sqlCRUD($array, '', $this->TB_CADASTRO_INTERESSE, '', 'S', 0, 0);
		}

		public function isLogged(){
			
			//verifica se existe um cookie armazenado
			if (isset($_COOKIE["plataforma_usuario"]) && !isset($_SESSION['plataforma_usuario'])) {
				$usuario_dados = unserialize($_COOKIE['plataforma_usuario']);
				if (is_array($usuario_dados)){
					if (array_key_exists("id",$usuario_dados)) {
						$_SESSION['plataforma_usuario'] = $usuario_dados;
					}
				}
			}
			if(isset($_SESSION['plataforma_usuario'])){
				if(is_array($_SESSION['plataforma_usuario'])){
					return true;
				}
			}
			return false;
		}

		public function setIuguID($cadastro_idx,$iugu_id){
			try {
				parent::update("UPDATE ".$this->TB_CADASTRO." SET iugu_id='".$iugu_id."' Where cadastro_idx=".$cadastro_idx." ");
			} catch (Exception $e) {
				throw $e;
			}
		}

		public function updateFromCheckout($request){

			//$nome_completo,$nome_informal,$data_nascimento,$sexo,$cpf,$telefone,$cep,$estado,$cidade,$endereco,$numero,$complemento,$bairro
			// unset($_SESSION['plataforma_usuario']);
			// exit();

			$rCadastro=false;
			
			if (isset($_SESSION['plataforma_usuario'])) { //Já cadastrado

				$userCPF_CNPJ = isset($request['cpf_cnpj']) ? Text::getOnlyNumber($request['cpf_cnpj']) : '';

				$array = array(
					'cadastro_idx' => $_SESSION['plataforma_usuario']['id'],
					'nome_completo' => $request['nome_completo'],
					// 'nome_informal' => $request['nome_informal'],
					'cpf_cnpj' => $userCPF_CNPJ,
					// 'data_nasc' => Date::toMysql($request['data_nascimento']),
					// 'genero' => $request['sexo'],
					'telefone_resid' => $request['telefone'],
	        	);
	        	if (isset($request['cep'])) {
	        		if (trim($request['cep'])!='') {
	        			$array['cep'] = $request['cep'];
						$array['estado'] = $request['estado'];
						$array['cidade'] = $request['cidade'];
						$array['endereco'] = $request['endereco'];
						$array['numero'] = $request['numero'];
						$array['complemento'] = $request['complemento'];
						$array['bairro'] = $request['bairro'];
	        		}
	        	}
				
				$meusDadosAtuais = self::getMyDataUserInfo();
				if(is_array($meusDadosAtuais) && count($meusDadosAtuais) > 0)
					$rCadastro = $meusDadosAtuais[0];

				
				if($rCadastro['cpf_cnpj'] != $request['cpf_cnpj']){
					$validaCPFCNPJ =  (strlen($request['cpf_cnpj'])>11) ? Sis::validaCNPJ($request['cpf_cnpj']) : Sis::validaCPF($request['cpf_cnpj']) ;
					if ($validaCPFCNPJ) {
						$checkCadastroCpf = parent::select("SELECT cadastro_idx FROM ". $this->TB_CADASTRO." WHERE cpf_cnpj='".trim($request['cpf_cnpj'])."' And cadastro_idx<>".$_SESSION['plataforma_usuario']['id']." ");
						if(is_array($checkCadastroCpf) && count($checkCadastroCpf) > 0){
							throw new Exception("Já temos um cadastro utilizando este CPF/CNPJ. Para mais informações, entre em contato.", 1);
						}
					}else{
						throw new Exception("O CPF/CNPJ informado é inválido.", 1);
					}
				}
				try {
					parent::sqlCRUD($array, '', $this->TB_CADASTRO, '', 'U', 0, 0);
				} catch (Exception $e) {
					throw $e;
				}

				$_SESSION['plataforma_usuario_buyer'] = $_SESSION['plataforma_usuario'];

			}else{ //Novos cadastrados

				$checkCadastroEmail = parent::sqlCRUD(array('email' => $request['email']), 'cadastro_idx,status', $this->TB_CADASTRO, '', 'S', 0, 0);
				if((is_array($checkCadastroEmail) && count($checkCadastroEmail) > 0)){
					$rCadastro = $checkCadastroEmail[0];
					// if ((int)$rCadastro['status']!=4) { //4 == E-mail inicialmente cadastrado na newsletter, o cadastro vai ser atualizado.
					// 	$rCadastro==false;
					// 	throw new Exception("Já temos um cadastro utilizando este e-mail. Para recuperar sua senha, acesse a opção de Recuperar senha na área de login.", 1);
					// }
				}

				$checkCadastroCpf=null;
				//Veririca se o cpf informado é válido ou existente.
				$validaCPFCNPJ =  (strlen($request['cpf_cnpj'])>11) ? Sis::validaCNPJ($request['cpf_cnpj']) : Sis::validaCPF($request['cpf_cnpj']) ;
				if ($validaCPFCNPJ) {
					$checkCadastroCpf = parent::select("SELECT cadastro_idx,cpf_cnpj,email FROM ". $this->TB_CADASTRO." WHERE cpf_cnpj='".trim($request['cpf_cnpj'])."' ");
					//if(is_array($checkCadastroCpf) && count($checkCadastroCpf) > 0){
					//	throw new Exception("Já temos um cadastro utilizando este CPF. Para mais informações, entre em contato.", 1);
					//}
				}else{
					throw new Exception("O CPF informado é inválido.", 1);
				}

				if (is_array($checkCadastroCpf) && count($checkCadastroCpf)>0){//Existe uma conta com o CPF informado.
					//Verificando email em relação ao CPF
					if (($rCadastro['email'] != $checkCadastroCpf['email']) || ($rCadastro['cpf_cnpj'] != $checkCadastroCpf['cpf_cnpj'])) { //email e cpf não batem.
						throw new Exception("O CPF informado está vinculado a um cadastro existente.", 1);
					}
				}

				if($rCadastro!==false){

					$array = array(
						'cadastro_idx' => $rCadastro['cadastro_idx'],
						// 'status' => 1,
						'nome_completo' => $request['nome_completo'],
						// 'nome_informal' => $request['nome_informal'],
						// 'cpf_cnpj' => $request['cpf_cnpj'],
						// 'data_nasc' => Date::toMysql($request['data_nascimento']),
						// 'genero' => $request['sexo'],
						'telefone_resid' => $request['telefone'],
						// 'email' => $request['email'],
						// 'senha' => md5(Text::clean(strip_tags($request['senha']))),
						// 'senha' => md5(date('dmYHis')),
						// 'receber_boletim' => (int)$request['receber_boletim']
		        	);

					if (isset($request['cep'])) {
		        		if (trim($request['cep'])!='') {
		        			$array['cep'] = $request['cep'];
							$array['estado'] = $request['estado'];
							$array['cidade'] = $request['cidade'];
							$array['endereco'] = $request['endereco'];
							$array['numero'] = $request['numero'];
							$array['complemento'] = $request['complemento'];
							$array['bairro'] = $request['bairro'];
		        		}
		        	}

		        	$acaoRegistro = 'U'; //Atualização a partir da newsletter

				}else{

					$array = array(
						'status' => 1,
						'nome_completo' => $request['nome_completo'],
						// 'nome_informal' => $request['nome_informal'],
						'cpf_cnpj' => $request['cpf_cnpj'],
						// 'data_nasc' => Date::toMysql($request['data_nascimento']),
						// 'genero' => $request['sexo'],
						'telefone_resid' => $request['telefone'],
						'email' => $request['email'],
						'senha' => md5(date('dmYHis')),
						'receber_boletim' => (int)$request['receber_boletim']
		        	);
		        	if (isset($request['cep'])) {
		        		if (trim($request['cep'])!='') {
		        			$array['cep'] = $request['cep'];
							$array['estado'] = $request['estado'];
							$array['cidade'] = $request['cidade'];
							$array['endereco'] = $request['endereco'];
							$array['numero'] = $request['numero'];
							$array['complemento'] = $request['complemento'];
							$array['bairro'] = $request['bairro'];
		        		}
		        	}
		        	$acaoRegistro = 'I'; //Novo Cadastro

				}

				try {
					
					$resultado = parent::sqlCRUD($array, '', $this->TB_CADASTRO, '', $acaoRegistro, 0, 0);
					
					if(!is_null($resultado)){
						//seta as informações do cadastro na sessão.
						$usuario_dados = array(
											'id' => ($rCadastro!==false) ? $rCadastro['cadastro_idx'] : $resultado,
											'nome'	=> $request['nome_completo'],
											'perfil'	=> 0,
											'imagem_perfil' => 'null',
											'email' => $request['email']
										);

										// 'login' => $request['nome_informal'],
										// 'meus_cursos' =>  ($rCadastro!==false) ? $rCadastro['meus_cursos'] : '',

						$_SESSION['plataforma_usuario_buyer'] = $usuario_dados;
					}

				} catch (Exception $e) {
					throw $e;
				}

			}
		}

		/**
		 * Login no e-commerce
		 */
		public function login()
		{
			$email 		= Text::clean(addslashes(trim(strip_tags($_POST['email']))));
			$password 	= md5(addslashes(trim(strip_tags($_POST['senha']))));

			$usuarioArr = parent::select("SELECT cadastro_idx, nome_completo, imagem_perfil, nome_informal, perfil, email FROM ".$this->TB_CADASTRO." WHERE email = '" . $email . "' AND senha =  '" . $password . "' AND status = 1 LIMIT 0,1");

			if(is_array($usuarioArr) && count($usuarioArr) > 0){
				//foreach ($usuarioArr as $usuario){
				$usuario = $usuarioArr[0];
					
					/**
					 * Gera a sessão usuário, e armazena um array com as informações do mesmo.
					 */
					$usuario_dados = array('id' => $usuario['cadastro_idx'],
											'nome'	=> $usuario['nome_completo'],
											'perfil'	=> $usuario['perfil'],
											'imagem_perfil' => $usuario['imagem_perfil'],
											'email' => $usuario['email']
											);

					$_SESSION['plataforma_usuario'] = $usuario_dados;

					$lembrar_dados = (isset($_POST['lembrar_dados']))?(int)$_POST['lembrar_dados']:0;

					if ($lembrar_dados==1) {
						setcookie("plataforma_usuario", serialize($usuario_dados), time() + (86400 * 5)); // 86400 = 1 day
					}

					//Define o token da sessão atual
					self::setAccessToken($usuario_dados);

					//Verifica e armazena as mensagens não lidas
					$cursoMens = new CursoMensagens();
					$cursoMens->setUnreadTotal();

					/**
					 * Inserindo o Log, informando que o login foi bem sucedido.
					 */
					//Sis::insertLog(0, "Ecommerce - Login", "efetuado", $_SESSION['plataforma_usuario']['id'], $_SESSION['plataforma_usuario']['nome'], "Sucesso");

					//trackers - facebook / analytics
					// $_SESSION["platform_event_tracker"] = "gtag('event','login',{method:'Site'});";

					/**
					 * Redirecionando para onde ele estava ou para a inicial do site.
					 */
					if (isset($_SESSION["plataforma_url_back_login"])) {
						$sessionTemp = $_SESSION["plataforma_url_back_login"];
						unset($_SESSION["plataforma_url_back_login"]);
						die('<script>window.location = "'.$sessionTemp.'";</script>');
					}else{
						echo '<script>window.location = "/minha-conta/meus-cursos";</script>';
					}

				//}
			}else{
				/**
				 * Caso o usuário ou senha estejam incorretos, o log é inserido, e o usuário volta para a senha e login.
				 */
				Sis::insertLog(0, "Ecommerce - Login", "", 0, "", "Tentativa de Login utilizando o e-mail  ".$email);
				ob_end_clean();
				die('<script>alert("Não foi possível efetuar o login!");history.back();</script>');
			}
		}

		/**
		 * Método que altera um código e o envia para o e-mail do usuário.
		 */
		public function geraCodigoNovaSenha()
		{
			$email = addslashes(trim(strip_tags($_POST['email'])));
			/*
			$cpf 	= addslashes(trim(strip_tags($_POST['cpf_cnpj'])));
			$cpf 	= "";
			*/

			$usuarioArr = parent::select("SELECT cadastro_idx, nome_completo, nome_informal, email, cpf_cnpj FROM ".$this->TB_CADASTRO." WHERE email = '" . $email . "' AND status=1 LIMIT 0,1");
			if(is_array($usuarioArr) && count($usuarioArr) == 1){

	            $keyToSend = md5($usuarioArr[0]['email'].$usuarioArr[0]['cpf_cnpj']);

	            // var_dump( base64_encode( base64_encode(date('Ymd_His'))."-".$keyToSend ) );
	            // exit();

				$HTML_mensagem 	= Sis::returnMessageBodyClient(" Recuperação de senha.");
				$corpo_mensagem 	= "<strong style='font-size:15px;'>Olá, ".$usuarioArr[0]['nome_completo']."! </strong><br /><br />
											Esqueceu sua senha? <br /><br />
											Recentemente alguém realizou o processo de recuperação de senha no  site ".$_SERVER['HTTP_HOST'].", utlizando seu e-mail. <br />
											Se não foi você, não se preocupe! Seus dados estão seguros.<br /><br />
											Se foi você, clique <a style='color: blue;' target='_blank' href='https://".$_SERVER['HTTP_HOST']."/login-cadastro/?rpass=".base64_encode( base64_encode(date('Ymd_His'))."-".$keyToSend )."' >neste link</a> para recuperar sua senha.<br /><br />
											Atenciosamente, <br /> ".Sis::config("CLI_NOME");

				$HTML_mensagem 	= str_replace("[HTML_DADOS]",$corpo_mensagem,$HTML_mensagem);


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

                		$mail->AddAddress(trim($usuarioArr[0]['email']), trim($usuarioArr[0]['nome_completo']));
						
						$mail->AddReplyTo(Sis::config("CLI_SMTP_MAIL"), Sis::config("CLI_NOME"));

						$mail->Subject = "Recuperação de senha";
						$mail->Body = $HTML_mensagem;
						if (!$mail->Send()) {
		                    // echo 'Mailer Error: ' . $mail->ErrorInfo;
		                    // exit();
		                }else{
		                    //die("ok");
		                }


					} catch (Exception $e) {
						//die( $e->getMessage() );
					}
					
				}
				ob_clean();
                // $_SESSION['plataforma_login_usuario_alerts'] = array('tipo' => 2, 'mensagem' => 'Uma mensagem foi enviada para o seu e-mail de cadastro, com as instruções de recuperação da sua senha.', 'email' => $email);
                // header("Location: /");
                die("<script>alert('Uma mensagem foi enviada para o seu e-mail de cadastro, com as instruções de recuperação da sua senha.');window.location.href='/';</script>");
                exit();

			}else{
				die("<script>alert('Não conseguimos localizar sua conta. Verifique se o e-mail está correto.');history.back();</script>");
			}
		}

		/**
		 * Método que altera um código e o envia para o e-mail do usuário.
		 */
		public function salvaNovaSenha($code)
		{
			$senha = md5(addslashes(trim(strip_tags($_POST['senha']))));
			$senha_confirm = md5(addslashes(trim(strip_tags($_POST['senha_confirm']))));
			if($senha == $senha_confirm)
			{
				$codeDataV = parent::select("SELECT cadastro_idx, nome_completo FROM ".$this->TB_CADASTRO." WHERE md5(CONCAT(email,cpf_cnpj))='".$code."' LIMIT 0,1");
				if(is_array($codeDataV) && count($codeDataV) == 1){
					try {
						parent::update("UPDATE ".$this->TB_CADASTRO." SET senha='".$senha."' Where cadastro_idx=".$codeDataV[0]['cadastro_idx']." ");
						ob_clean();
						echo "<script>alert('Senha alterada com sucesso.');location.href='/login-cadastro';</script>";
						exit();
					} catch (Exception $e) {
						echo "<script>alert('Erro ao salvar os dados. \n\n".$e->getMessage()."');</script>" ;
					}
				}else{
					echo "<script>alert('Código de autorização não identificado, certifique-se de que acessou a recuperação de senha através do e-mail que lhe foi enviado.');</script>";
				}
			}else{
				echo "<script>alert('Confirme sua nova senha corretamente!');'</script>" ;
			}
		}

		public function logout()
		{
			if (isset($_SERVER['HTTP_COOKIE'])) {
			   setcookie('plataforma_usuario', '', time()-1000);
			}
			unset($_SESSION['plataforma_usuario']);
			Sis::redirect('/');
		}

		public function iniciaCadastroComCep()
		{
			$cep = (isset($_POST['cep'])) ? Text::clean($_POST['cep']) : "";
			$_SESSION['plataforma_usuario_cep'] = "";
			if($cep!=""){
				$_SESSION['plataforma_usuario_cep'] = $cep;
			}
			Sis::redirect('/cadastre-se');
		}


		public function cadastroInsertInicial()
		{

			$_SESSION["plataforma_cadastro_usuario"] = "";
			$_SESSION["plataforma_cadastro_usuario_erros"] = "";

			$termos = isset($_POST['termos']) ? (int)$_POST['termos'] : 0 ;
			if ((int)$termos==0) {
				$_SESSION["plataforma_cadastro_usuario_erros"] = array('tipo' => 4, 'mensagem' => 'Você precisa aceitar os termos e condições e nossa política de privacidade!');
				die('<script>history.back();</script>');
			}

			$array = array(
			   'status' => 1,//Usuário já fica ativo no sistema
		       'perfil' => 0,
		       'nome_completo' => isset($_POST['nome_completo']) ? Text::clean(strip_tags($_POST['nome_completo'])) : '',
		       'curriculo' => isset($_POST['breve_curriculo']) ? Text::clean(strip_tags($_POST['breve_curriculo'])) : '',
				'celular' => isset($_POST['telefone']) ? Text::clean(strip_tags($_POST['telefone'])) : '',
				// 'data_nasc' => isset($_POST['data_nascimento']) ? Date::toMysql(Text::clean(strip_tags($_POST['data_nascimento']))) : '',
				// 'genero' => isset($_POST['genero']) ? Text::clean(strip_tags($_POST['genero'])) : '',
		       'email' => isset($_POST['email']) ? Text::clean(strip_tags($_POST['email'])) : '',
		       'senha' => isset($_POST['senha']) ? md5(Text::clean(strip_tags($_POST['senha']))) : '',
		       'receber_boletim' => isset($_POST['receber_boletim'])?(int)$_POST['receber_boletim'] :0,
		       'aceite_politica_privacidade' => isset($_POST['termos'])?(int)$_POST['termos'] :0
			);
			
			$_SESSION["plataforma_cadastro_usuario"] = $array;
			$_SESSION["plataforma_cadastro_usuario"]['time_limit'] = date('Ymd-His');

			if($array['nome_completo']=="" || $array['email']=="" || $array['senha']==""){
				$_SESSION["plataforma_cadastro_usuario_erros"] = array('tipo' => 4, 'mensagem' => 'Preencha os campos corretamente e verifique se todos os campos obrigatórios estão preenchidos!');
				die('<script>history.back();</script>');
			}else{
				$checkCadastroEmail 	= parent::sqlCRUD(array('email' => $array['email']), 'cadastro_idx,status', $this->TB_CADASTRO, '', 'S', 0, 0);
				//$checkCadastroCpf 	= parent::sqlCRUD(array('cpf_cnpj' => $array['cpf_cnpj']), 'cadastro_idx', $this->TB_CADASTRO, '', 'S', 0, 0);
				if((is_array($checkCadastroEmail) && count($checkCadastroEmail) > 0)){
					$theCadastro = $checkCadastroEmail[0];
					if ((int)$theCadastro['status']==4) { //E-mail inicialmente cadastrado na newsletter, o cadastro vai ser atualizado.
						$arrayUpdate = array(
						   'cadastro_idx'		=> $theCadastro['cadastro_idx'],
					       'status'            => 1,//Usuário já fica ativo no sistema
					       'nome_completo'     => $array['nome_completo'],
					       'email'             => $array['email'],
					       'senha'             => $array['senha'],
					       'receber_boletim'   => $array['receber_boletim'],
			        	);
						$dados = parent::sqlCRUD($arrayUpdate, '', $this->TB_CADASTRO, '', 'U', 0, 0);
						
						if(isset($dados) && $dados !== FALSE){
							unset($_SESSION['plataforma_cadastro_usuario']);
							
							$mensagemObrigado = "SUCESSO";
							try {
								$sendMail = self::sendMailObrigado($array);
								if (!$sendMail) {
									$mensagemObrigado = "FALHA";
								}
							} catch (Exception $e) {
								$mensagemObrigado = "FALHA: ".$e->getMessage();
							}

							$mensagemNotifica = "SUCESSO";
							try {
								$sendMailNotifica = self::sendMailNotifica($array);
								if (!$sendMailNotifica) {
									$mensagemNotifica = "FALHA";
								}
							} catch (Exception $e) {
								$mensagemNotifica = "FALHA: ".$e->getMessage();
							}
						  
						  //trackers - facebook / analytics
						  $_SESSION["platform_event_tracker"] = "fbq('track', 'CompleteRegistration');"; //gtag('event','sign_up',{method:'Site'});

		                  $_SESSION["plataforma_cadastro_usuario_erros"] = array('tipo' => 3, 'mensagem' => 'Seu cadastro foi realizado com sucesso! <!-- '.$mensagemObrigado.' [NOTIFICA: '.$mensagemNotifica.'] -->');

		                  die("<script>window.location.href='/';</script>");
		                  exit();

						}
					}
					$_SESSION["plataforma_cadastro_usuario_erros"] = array('tipo' => 4, 'mensagem' => 'Já temos um cadastro utilizando este e-mail. <br /> Para recuperar sua senha, acesse a opção de Recuperar senha.');
					die("<script>history.back();</script>");
				}else{
					
					$cadastroId = parent::sqlCRUD($array, '', $this->TB_CADASTRO, '', 'I', 0, 0);
					
					//Relaciona a área de interesse.
					$interesseId = isset($_POST['tema_interesse']) ? (int)$_POST['tema_interesse'] : 0 ;
					if ((int)$interesseId>0) {
						$arrayInteresse = array(
							'cadastro_idx'=>$cadastroId,
							'interesse_idx'=>$interesseId
						);
						$cadastroId = parent::sqlCRUD($arrayInteresse, '', $this->TB_CADASTRO_INTERESSE_SELECIONA, '', 'I', 0, 0);
					}

					// if(isset($dados) && $dados !== FALSE){
						
						unset($_SESSION['plataforma_cadastro_usuario']);

						$mensagemObrigado = "SUCESSO";
						try {
							// $sendMail = self::sendMailObrigado($array);
							if (!$sendMail) {
								$mensagemObrigado = "FALHA";
							}
						} catch (Exception $e) {
							$mensagemObrigado = "FALHA: ".$e->getMessage();
						}
						$mensagemNotifica = "SUCESSO";
						try {
							$sendMailNotifica = self::sendMailNotifica($array);
							if (!$sendMailNotifica) {
								$mensagemNotifica = "FALHA";
							}
						} catch (Exception $e) {
							$mensagemNotifica = "FALHA: ".$e->getMessage();
						}
						
						//trackers - facebook / analytics
						// $_SESSION["platform_event_tracker"] = "fbq('track', 'CompleteRegistration');"; //gtag('event','sign_up',{method:'Site'});


						$_SESSION["plataforma_cadastro_usuario_erros"] = array('tipo' => 3, 'mensagem' => 'Seu cadastro foi realizado com sucesso! <!-- '.$mensagemObrigado.' [NOTIFICA: '.$mensagemNotifica.'] -->');
						ob_clean();
						die("<script>window.location.href='/login-cadastro';</script>");
						exit();

              		// }

				}
			}
			/*
			$_SESSION["plataforma_cadastro_usuario_erros"] = array('tipo' => 3, 'mensagem' => 'Seu cadastro foi realizado com sucesso!'); // Você receberá um e-mail para confirmar seus dados.
			die("<script>history.back();</script>");
			*/
		}

		public function cadastroInsert()
		{

			$_SESSION["plataforma_cadastro_usuario"] = "";
			$_SESSION["plataforma_cadastro_usuario_erros"] = "";

			$array = array(
                'status'            => 1,//Usuário já fica ativo no sistema
                'nome_completo'     => isset($_POST['nome_completo']) ? Text::clean($_POST['nome_completo'])     	: '',
                'nome_informal'     => isset($_POST['nome_informal']) ? Text::clean($_POST['nome_informal'])       : '',
                'genero'            => isset($_POST['genero']) ? (int)$_POST['genero']                          	: 0 ,
                'data_nasc'         => isset($_POST['data_nasc']) ? Text::clean($_POST['data_nasc'])               : '',
                'email'             => isset($_POST['email']) ? Text::clean($_POST['email'])                       : '',
                'senha'             => isset($_POST['senha']) ? md5(Text::clean($_POST['senha']))                  : '',
                'telefone_resid'    => isset($_POST['telefone_resid']) ? Text::clean($_POST['telefone_resid'])     : '',
                'telefone_comer'    => isset($_POST['telefone_comer']) ? Text::clean($_POST['telefone_comer'])     : '',
                'celular'           => isset($_POST['celular']) ? Text::clean($_POST['celular'])                   : '',
                'endereco'          => isset($_POST['endereco']) ? Text::clean($_POST['endereco'])                 : '',
                'numero'            => isset($_POST['numero']) ? Text::clean($_POST['numero'])                     : '',
                'complemento'       => isset($_POST['complemento']) ? Text::clean($_POST['complemento'])           : '',
                'bairro'            => isset($_POST['bairro']) ? Text::clean($_POST['bairro'])                     : '',
                'cep'               => isset($_POST['cep']) ? Text::clean($_POST['cep'])                           : '',
                'cpf_cnpj'               => isset($_POST['cpf_cnpj']) ? str_replace(".", "", str_replace("-", "", Text::clean($_POST['cpf_cnpj']))) : '',
                'cidade'            => isset($_POST['cidade']) ? Text::clean($_POST['cidade'])                     : '',
                'estado'            => isset($_POST['estado']) ? Text::clean($_POST['estado'])                     : '',
                'pais'              => isset($_POST['pais']) ? Text::clean($_POST['pais'])                         : '',
                'receber_boletim'   => isset($_POST['receber_boletim'])?(int)$_POST['receber_boletim']             :0
        	);

			$_SESSION["plataforma_cadastro_usuario"] = $array;
			$_SESSION["plataforma_cadastro_usuario"]['time_limit'] = date('Ymd-His');

			if($array['nome_completo']=="" || $array['data_nasc']=="" || $array['cpf_cnpj']=="" || $array['cep']=="" || $array['endereco']=="" || $array['numero']=="" || $array['email']=="" || $array['senha']==""){
				$_SESSION["plataforma_cadastro_usuario_erros"] = array('tipo' => 4, 'mensagem' => 'Preencha os campos corretamente e verifique se todos os campos obrigatórios estão preenchidos!');
				die('<script>history.back();</script>');
			}else{
				$checkCadastroEmail 	= parent::sqlCRUD(array('email' => $array['email']), 'cadastro_idx', $this->TB_CADASTRO, '', 'S', 0, 0);
				$checkCadastroCpf 	= parent::sqlCRUD(array('cpf_cnpj' => $array['cpf_cnpj']), 'cadastro_idx', $this->TB_CADASTRO, '', 'S', 0, 0);

				if((is_array($checkCadastroEmail) && count($checkCadastroEmail) > 0)){
					$_SESSION["plataforma_cadastro_usuario_erros"] = array('tipo' => 4, 'mensagem' => 'Já temos um cadastro utilizando este e-mail. <br />Para recuperar sua senha, por favor <a href="/login-cadastro"><b>clique aqui</b></a> e vá em Recuperar senha.');
					die("<script>history.back();</script>");
				}else if(is_array($checkCadastroCpf) && count($checkCadastroCpf) > 0){
					$_SESSION["plataforma_cadastro_usuario_erros"] = array('tipo' => 4, 'mensagem' => 'Já temos um cadastro utilizando este CPF. <br />Para recuperar sua senha, por favor <a href="/login-cadastro"><b>clique aqui</b></a> e vá em Recuperar senha.');
					die("<script>history.back();</script>");
				}else{

					$dados = parent::sqlCRUD($array, '', $this->TB_CADASTRO, '', 'I', 0, 0);
					if(isset($dados) && $dados !== FALSE){

						unset($_SESSION['plataforma_cadastro_usuario']);

						/*$sendMail = self::sendMailConfirmation($dados);
						if($sendMail){
							$_SESSION["plataforma_cadastro_usuario_erros"] = array('tipo' => 3, 'mensagem' => 'Seu cadastro foi realizado com sucesso! Você receberá um e-mail para confirmar seus dados.');
							die("<script>history.back();</script>");
						}*/
                        $_SESSION["plataforma_cadastro_usuario_erros"] = array('tipo' => 3, 'mensagem' => 'Seu cadastro foi realizado com sucesso!');
                        die("<script>history.back();</script>");
					}
				}
			}
			$_SESSION["plataforma_cadastro_usuario_erros"] = array('tipo' => 3, 'mensagem' => 'Seu cadastro foi realizado com sucesso!'); // Você receberá um e-mail para confirmar seus dados.
			die("<script>history.back();</script>");
		}

		public function newsletterSign($dados=null)
		{
			if (!is_array($dados)&&count($dados)<3) {
				return false;
			}

			$array = array(
                'status'            => $dados['status'],
                'nome_completo'     => $dados['nome'],
                'email'             => $dados['email'],
                'receber_boletim'   => 1
        	);
			$checkCadastroEmail = parent::sqlCRUD(array('email' => trim($array['email'])), 'cadastro_idx', $this->TB_CADASTRO, '', 'S', 0, 0);

			if((is_array($checkCadastroEmail) && count($checkCadastroEmail) > 0)){
				return true;
			}else{
				try{
					$cadastroId = parent::sqlCRUD($array, '', $this->TB_CADASTRO, '', 'I', 0, 0);
					//Registra a área de interesse
					if ((int)$dados['area_interesse']>0) {
						$dataAreaInteresse = array(
							'cadastro_idx'=>$cadastroId,
							'interesse_idx'=>(int)$dados['area_interesse']
						);
						$cadastroInteresseId = parent::sqlCRUD($dataAreaInteresse, '', $this->TB_CADASTRO_INTERESSE_SELECIONA, '', 'I', 0, 0);
					}
					if(isset($cadastroId) && $cadastroId !== FALSE){
						return true;
					}
				}catch (Exception $e) {
					return $e->getMessage();
				}
			}
		}

		public function getCadastroConfirmation($code){
			$_SESSION['plataforma_login_usuario_erros'] = "";
			$codeDataV 	= parent::select("SELECT cadastro_idx, email FROM ".$this->TB_CADASTRO." WHERE md5(CONCAT(cadastro_idx,email))='".$code."' LIMIT 0,1");
			if(is_array($codeDataV) && count($codeDataV) == 1){
				$updateStatus = parent::sqlCRUD(array('cadastro_idx' => $codeDataV[0]['cadastro_idx'], 'status' => 1), '', $this->TB_CADASTRO, '', 'U', 0, 0);
				if(isset($updateStatus) && $updateStatus !== FALSE){
					$_SESSION['plataforma_login_usuario_alerts'] = array('tipo' => 3, 'mensagem' => 'Cadastro confirmado com sucesso. Faça login para continuar!', 'email' => $codeDataV[0]['email']);
				}
			}
		}

		/**
		 * Seleciona os dados do usuário em sessão
		 * @param String $dados => Dados que deseja selecionar. Caso seja vazio, ele seleciona todos os dados.
		 */
		public function getMyDataUserInfo($dados=""){
			if(isset($_SESSION['plataforma_usuario']['id'])){
				return parent::sqlCRUD(array('cadastro_idx' => $_SESSION['plataforma_usuario']['id']), $dados, $this->TB_CADASTRO, '', 'S', 0, 0);
			}
			return false;
		}


		public function updateLPData($request)
		{

			$lpData = isset($request['dataPage']) ? addslashes($request['dataPage']) : false ;
			if ($lpData!==false) {
				
				$lpData = str_replace("<body>","",$lpData);
				$lpData = str_replace("</body>","",$lpData);

				$arrayData = Array(
					'cadastro_idx' => $_SESSION['plataforma_usuario']['id'],
					'lp_html_css' => $lpData
				);

				parent::sqlCRUD($arrayData, '', $this->TB_CADASTRO, '', 'U', 0, 0);
				
			}

		}
		public function updateLPURL($request)
		{
			$lpURL = isset($request['url'])? addslashes(Text::friendlyUrl($request['url'])) :false;
			if ($lpURL!==false) {
				$arrayData = Array(
					'cadastro_idx' => $_SESSION['plataforma_usuario']['id'],
					'lp_url' => $lpURL
				);
				parent::sqlCRUD($arrayData, '', $this->TB_CADASTRO, '', 'U', 0, 0);
			}
		}


		/**
		 * Atualiza os registros do usuário.
		 * Sessão /minha-conta/meus-dados/meus-dados
		 */
		public function changeMyProfile()
		{
			$_SESSION["plataforma_cadastro_usuario"] = "";
			$_SESSION["plataforma_cadastro_usuario_erros"] = "";

			$celular = isset($_POST['celular']) ? Text::clean($_POST['celular']) : '';
			$celular_formatado = $celular;

			$telefone = isset($_POST['telefone_resid']) ? Text::clean($_POST['telefone_resid']) : '';
			$telefone_formatado = $telefone;

			$remove_imagem_perfil = isset($_POST['remove_imagem_perfil']) ? Text::clean($_POST['remove_imagem_perfil']) : 0;

			$userCPF_CNPJ = isset($_POST['cpf_cnpj']) ? Text::getOnlyNumber($_POST['cpf_cnpj']) : '';

			$array = array(
				'cadastro_idx' 	    => $_SESSION['plataforma_usuario']['id'],
				'nome_completo'     => isset($_POST['nome_completo']) ? Text::clean($_POST['nome_completo']) : '',
				'nome_informal'     => isset($_POST['nome_informal']) ? Text::clean($_POST['nome_informal']) : '',
				'email'             => isset($_POST['email']) ? Text::clean($_POST['email']) : '',
				'cpf_cnpj'               => $userCPF_CNPJ,
				'data_nasc'         => isset($_POST['data_nasc']) ? Date::toMysql($_POST['data_nasc']) : '',
				'genero'         	=> isset($_POST['genero']) ? (int)$_POST['genero']: 0,
				'telefone_resid'    => $telefone_formatado,
				'celular'           => $celular_formatado,
				'endereco'          => isset($_POST['endereco']) ? Text::clean($_POST['endereco']) : '',
				'numero'            => isset($_POST['numero']) ? Text::clean($_POST['numero']) : '',
				'complemento'       => isset($_POST['complemento']) ? Text::clean($_POST['complemento']) : '',
				'cep'           	=> isset($_POST['cep']) ? Text::clean($_POST['cep']) : '',
				'bairro'           => isset($_POST['bairro']) ? Text::clean($_POST['bairro']) : '',
				'cidade'           => isset($_POST['cidade']) ? Text::clean($_POST['cidade']) : '',
				'estado'           => isset($_POST['estado']) ? Text::clean($_POST['estado']) : ''
        	);

			// ob_clean();
			// var_dump($_SESSION['plataforma_usuario']);
			// exit();

			$meusDadosAtuais = self::getMyDataUserInfo();
			if(is_array($meusDadosAtuais) && count($meusDadosAtuais) > 0)
			{
				$meusDadosAtuais = $meusDadosAtuais[0];
	        	$erroSenha = false;
				if(!isset($_POST['senha_antiga']) || (isset($_POST['senha_antiga']) && Text::clean($_POST['senha_antiga']) == "")){
					$erroSenha = true;
				}else if(md5(Text::clean($_POST['senha_antiga'])) !== $meusDadosAtuais['senha']){
					$erroSenha = true;
				}
				unset($_SESSION["plataforma_url_back_cadastro"]);

				/**
				 * Caso a senha não tenha seja válida, ele retorna o erro.
				 */
				if($erroSenha){
					$_SESSION["plataforma_cadastro_usuario_erros"] = array('tipo' => 4, 'mensagem' => 'Senha atual incorreta.');
					die("<script>history.back();</script>");
				}

				//$array['status'] = $meusDadosAtuais['status'];
				if($meusDadosAtuais['email'] != $array['email']){
					unset($_SESSION["plataforma_url_back_cadastro"]);
					$checkCadastroEmail 	= parent::sqlCRUD(array('email' => $array['email']), 'cadastro_idx', $this->TB_CADASTRO, '', 'S', 0, 0);
					if(is_array($checkCadastroEmail) && count($checkCadastroEmail) > 0){
						$_SESSION["plataforma_cadastro_usuario_erros"] = array('tipo' => 4, 'mensagem' => 'Já temos um cadastro utilizando este e-mail. Por favor, tente utilizar outro e-mail.');
						die("<script>history.back();</script>");
					}
				}
				if($meusDadosAtuais['cpf_cnpj'] != $array['cpf_cnpj']){

					$validaCPFCNPJ =  (strlen($array['cpf_cnpj'])>11) ? Sis::validaCNPJ($array['cpf_cnpj']) : Sis::validaCPF($array['cpf_cnpj']) ;
					if ($validaCPFCNPJ) {
						unset($_SESSION["plataforma_url_back_cadastro"]);
						$checkCadastroCpfCnpj 	= parent::sqlCRUD(array('cpf_cnpj' => $array['cpf_cnpj']), 'cadastro_idx', $this->TB_CADASTRO, '', 'S', 0, 0);
						if(is_array($checkCadastroCpfCnpj) && count($checkCadastroCpfCnpj) > 0){
							$_SESSION["plataforma_cadastro_usuario_erros"] = array('tipo' => 4, 'mensagem' => 'Já temos um cadastro utilizando este CPF/CNPJ. Para mais informações, entre em contato.');
							die("<script>history.back();</script>");
						}
					}else{
						$_SESSION["plataforma_cadastro_usuario_erros"] = array('tipo' => 4, 'mensagem' => 'CPF/CNPJ inválido!');
						die("<script>history.back();</script>");
					}


				}

				if(Text::clean($_POST['senha']) != ""){
					if(Text::clean($_POST['senha']) == Text::clean($_POST['senha_conf'])){
						$array['senha'] = md5(Text::clean($_POST['senha']));
						// $_SESSION["plataforma_url_back_cadastro"] = "/login";
					}else{
						$_SESSION["plataforma_cadastro_usuario_erros"] = array('tipo' => 4, 'mensagem' => 'A confirmação de senha deve ser igual a senha.');
						die("<script>history.back();</script>");
					}
				}

				$arquivo = isset($_FILES["imagem_perfil"]) ? $_FILES["imagem_perfil"] : "";
				if ($arquivo <> ""){
					if (!empty($arquivo["name"])) {
						// Verifica se o arquivo enviado é compativél com o formato escolhido
						if(!preg_match("/^image\/(pjpeg|jpeg|png|gif|bmp)$/", $arquivo["type"])){
							$_SESSION["plataforma_cadastro_usuario_erros"] = array('tipo' => 4, 'mensagem' => 'O arquivo escolhido não é uma imagem!');
							die("<script>history.back();</script>");
						}
						$nome_arquivo = $arquivo["name"];
						preg_match("/\.(gif|bmp|png|jpg|jpeg|swf){1}$/i", $nome_arquivo, $ext);
						$nome_arquivo = "profile-".$meusDadosAtuais['cadastro_idx'];
						$nome_arquivo = Text::clean($nome_arquivo) . "-" . rand() . "." . strtolower($ext[1]);
						$local = PASTA_CONTENT.DS.$this->pag.DS."profile".DS.$nome_arquivo;
						try {
							move_uploaded_file($arquivo["tmp_name"], $local);
						} catch (Exception $e) {
							//echo $e->getMessage();
							die("<script>history.back();</script>");
						}
						
						//trata o tamanho da imagem
						$img = new SimpleImage();
						$img->load($local)->adaptive_resize(320,320)->save($local);
						$array['imagem_perfil'] = $nome_arquivo;
						$_SESSION['plataforma_usuario']['imagem_perfil'] = $nome_arquivo;
						//exclui a imagem atual
						if(file_exists(PASTA_CONTENT.DS.$this->pag.DS."profile".DS.$meusDadosAtuais['imagem_perfil'])) {
							unlink(PASTA_CONTENT.DS.$this->pag.DS."profile".DS.$meusDadosAtuais['imagem_perfil']);
						}
					}
				}
				if ($remove_imagem_perfil==1) {
					$fileImageProfile = PASTA_CONTENT.DS.$this->pag.DS."profile".DS.$array['imagem_perfil'];
					if (file_exists($fileImageProfile))
						unlink($fileImageProfile);
					$array['imagem_perfil']="Null";
					$_SESSION['plataforma_usuario']['imagem_perfil'] = $array['imagem_perfil'];
				}

				$dados = parent::sqlCRUD($array, '', $this->TB_CADASTRO, '', 'U', 0, 0);
				if(isset($dados) && $dados != false){
					$_SESSION["plataforma_cadastro_usuario_erros"] = array('tipo' => 3, 'mensagem' => 'Dados salvos com sucesso!');
					header("Location: /minha-conta/meus-dados");
					exit();
					//die('<script>window.location = "/perfil";</script>');
					//echo "<script>reload();</script>";
				}else{
					$_SESSION["plataforma_cadastro_usuario_erros"] = array('tipo' => 4, 'mensagem' => 'Ocorreu um erro ao processar os dados. Por favor, tente mais tarde.');
					die("<script>history.back();</script>");
				}

			}else{
				$_SESSION["plataforma_cadastro_usuario_erros"] = array('tipo' => 4, 'mensagem' => 'Ocorreu um erro ao processar os dados. Por favor, tente mais tarde.');
				die("<script>history.back();</script>");
			}
		}

		public function sendMailConfirmation($cadastro_idx){
			$checkCadastroToConfirm = parent::sqlCRUD(array('cadastro_idx' => $cadastro_idx), '', $this->TB_CADASTRO, '', 'S', 0, 0);
			if(is_array($checkCadastroToConfirm) && count($checkCadastroToConfirm) > 0){

				$keyToSend = base64_encode(md5($checkCadastroToConfirm[0]['cadastro_idx'].$checkCadastroToConfirm[0]['email']));

				$HTML_mensagem 	= Sis::returnMessageBodyClient(Sis::config("CLI_NOME")." - Confirmação de cadastro.");
				$corpo_mensagem 	= "Olá, ".$checkCadastroToConfirm[0]['nome_completo'].". <br /><br />
											Recentemente, você se cadastrou no nosso site utlizando seu e-mail (<b>".$checkCadastroToConfirm[0]['email']."</b>). <br />
											<a href='https://".str_replace("http://", "", Sis::config("CLI_URL"))."/login-cadastro?codeconfirm=".$keyToSend."'>Confirmar agora seu cadastro</a>, e boas compras!<br /><br />
											<b>Por que você recebeu este e-mail?</b><br />
											Para sua segurança, a ".Sis::config("CLI_NOME")." solicita esta confirmação de identidade, sempre que um novo endereço de e-mail é cadastrado em nosso site. <br />Você não poderá realizar nenhuma compra até confirmar seu endereço de e-mail.<br /><br />
											Atenciosamente, <br /> ".Sis::config("CLI_NOME");
				$HTML_mensagem 	= str_replace("[HTML_DADOS]",$corpo_mensagem,$HTML_mensagem);

				//  var_dump($HTML_mensagem);
				// //  die();
				//exit();

				if(class_exists("PHPMailer\PHPMailer\PHPMailer")){


				    $mail = new PHPMailer(true);
                    try {
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

                         $mail->AddAddress(trim($checkCadastroToConfirm[0]['email']), $checkCadastroToConfirm[0]['nome_completo']);
                         $mail->AddBCC(trim(Sis::config("plataforma_EMAIL_ATENDIMENTO")));

                         $mail->AddReplyTo(Sis::config("CLI_SMTP_MAIL"), Sis::config("CLI_NOME"));
                         $mail->Subject = "Confirmação de cadastro";
                         $mail->Body = $HTML_mensagem;
                         $mail->Send();

                    } catch (phpmailerException $e) {
                      echo $e->errorMessage();
                    } catch (Exception $e) {
                      echo $e->getMessage();
                    }

                    //var_dump($checkCadastroToConfirm[0]['email']);
                    //var_dump($mail);
                    //exit();


					return true;
				}
			}
			return false;
		}

		public function sendMailObrigado($userDados){

			$HTML_mensagem = '
				<html>
					<head>
						<style>
						@import url(\'https://fonts.googleapis.com/css?family=Montserrat|Open+Sans:300,400\');
						</style>
					</head>
					<body>
						<table width="800" cellpadding="0" cellspacing="0" border="0" align="center" >

							<tr>
								<td style="padding-left: 120px;padding-bottom:50px;" >
									<img src="http://'.$_SERVER['HTTP_HOST'].'/assets/images/instituto-conexo-logo.svg" alt="" style="margin:10px 0px;" width="100" >
									<hr style="height:1px;border:0px;border-bottom:1px solid #eee;background:transparent;" />
								</td>
							</tr>
							
							<tr>
								<td style="font-size:28px;font-family: \'Montserrat\', sans-serif;padding-left: 120px;padding-bottom:50px;" >
									<p style="font-size:28px;font-family: \'Montserrat\', sans-serif;" >
										<strong>Olá '.$userDados['nome_completo'].', obrigado!</strong><br /><br />
										O seu cadastro<br />
										foi efetuado<br />
										com sucesso.
									</p>
									<p style="font-size:18px;font-family: \'Montserrat\', sans-serif;" >
										Acesse a nossa <a href="http://'.$_SERVER['HTTP_HOST'].'">plataforma online</a> e bons estudos.
									</p>
								</td>
							</tr>
							<tr>
								<td style="padding-left:120px; padding-top:20px;border-top:1px solid #eee; " valign="middle" >
									<a style="font-size: 15px; font-family: \'Open Sans\', sans-serif; font-weight: 400; font-style: normal;color:#000; text-decoration: none;" href="http://'.$_SERVER['HTTP_HOST'].'"><strong>'.$_SERVER['HTTP_HOST'].'</strong></a>
								</td>
							</tr>
						</table>
					</body>
				</html>
			';

			// ob_clean();
			// echo($HTML_mensagem);
			// exit();

			if(class_exists("PHPMailer\PHPMailer\PHPMailer")){

				$mail = new PHPMailer(true);
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

				$mail->AddAddress(trim($userDados['email']));

				$mail->AddReplyTo($fromEmail, Sis::config("CLI_NOME"));
				$mail->Subject = "Obrigado por se cadastrar!";
				$mail->Body = $HTML_mensagem;
				$mail->Send();
			}
               
		}

		public function sendMailNotifica($userDados){

			$HTML_mensagem = '
				<html>
					<head>
						<style>
						@import url(\'https://fonts.googleapis.com/css?family=Montserrat|Open+Sans:300,400\');
						</style>
					</head>
					<body>
						<table width="800" cellpadding="0" cellspacing="0" border="0" align="center" >
							<tr>
								<td style="padding-left: 120px;padding-bottom:50px;" >
									<img src="http://'.$_SERVER['HTTP_HOST'].'/assets/images/instituto-conexo-logo.svg" alt="" style="margin:10px 0px;" >
									<hr style="height:1px;border:0px;border-bottom:1px solid #eee;background:transparent;" />
								</td>
							</tr>
							<tr>
								<td style="font-family: \'Montserrat\', sans-serif;padding-left: 120px;padding-bottom:50px;" >
									<p style="font-family: \'Montserrat\', sans-serif;" >
										<h3 style="font-size:28px;" >Novo cadastro pelo site!</h3>
										<strong>Nome completo:</strong> &nbsp; '.$userDados['nome_completo'].'<br />
										<strong>E-mail:</strong> &nbsp; '.$userDados['email'].'<br />
									</p>
									<p style="font-size:14px;font-family: \'Montserrat\', sans-serif;" >
										Acesse o cadastro completo pelo gerenciador, em <a href="http://'.$_SERVER['HTTP_HOST'].'/admin" >http://'.$_SERVER['HTTP_HOST'].'/admin</a>.
									</p>
								</td>
							</tr>
							<tr>
								<td style="padding-left:120px; padding-top:20px;border-top:1px solid #eee; " valign="middle" >
									<a style="font-size: 15px; font-family: \'Open Sans\', sans-serif; font-weight: 400; font-style: normal;color:#000; text-decoration: none;" href="http://'.$_SERVER['HTTP_HOST'].'">'.$_SERVER['HTTP_HOST'].'</a>
								</td>
							</tr>
						</table>
					</body>
				</html>
			';

			// ob_clean();
			// echo($HTML_mensagem);
			// exit();

			if(class_exists("PHPMailer\PHPMailer\PHPMailer")){

				$mail = new PHPMailer(true);
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

				$mail->AddAddress(trim(Sis::config('plataforma_EMAIL_ATENDIMENTO')));
				$mail->Subject = "Novo cadastro pelo site!";
				$mail->Body = $HTML_mensagem;
				$mail->Send();
			}
               
		}

		public function confirmaCadastro()
		{
			$email = isset($_POST['email_confirmacao']) ? Text::clean($_POST['email_confirmacao']) : "";
			if(trim($email) == ""){
				$_SESSION["plataforma_login_usuario_alerts"] = array('tipo' => 4, 'mensagem' => 'Erro ao localizar seus dados. Verifique se o seu e-mail está correto e tente novamente.');
				die("<script>history.back();</script>");
			}
			$array = array('email' => $email, 'status' => 0);
        	$dados = parent::sqlCRUD($array, "cadastro_idx", $this->TB_CADASTRO, '', 'S', 0, 0);
        	if(is_array($dados) && count($dados) > 0){
	     		$sendmail = self::sendMailConfirmation($dados[0]['cadastro_idx']);
	     		if($sendmail){
	     			$_SESSION["plataforma_login_usuario_alerts"] = array('tipo' => 3, 'mensagem' => 'Um e-mail será enviado para a confirmação dos seus dados.');
					die("<script>history.back();</script>");
	     		}else{
	     			$_SESSION["plataforma_login_usuario_alerts"] = array('tipo' => 4, 'mensagem' => 'Erro ao localizar seus dados. Verifique se o seu e-mail está correto e tente novamente.');
					die("<script>history.back();</script>");
	     		}
        	}
     		$_SESSION["plataforma_login_usuario_alerts"] = array('tipo' => 4, 'mensagem' => 'Erro ao localizar seus dados. Verifique se o seu e-mail está correto e tente novamente.');
			die("<script>history.back();</script>");
		}



	//Metodos para informacoes de LP do produtor
		public function LP_urlSlugExists($produtorUrlSlug)
		{
			try {
				
				$urlCheck = parent::select("SELECT lp_url FROM ". $this->TB_CADASTRO ." WHERE cadastro_idx<>".(int)$_SESSION['plataforma_usuario']['id']." And lp_url like '".$produtorUrlSlug."' LIMIT 0,1 ");
				
				if (is_array($urlCheck)&&count($urlCheck)>0)
					return true;

			} catch (Exception $e){
				throw $e;
			}
			
			return false;
		}

		public function LP_infoSave($request)
		{
			$dataSave = Array(
				'cadastro_idx' => (int)$_SESSION['plataforma_usuario']['id'],
				'lp_url' => $request['lp_url'], 
				'lp_title' => $request['lp_title'],
				'lp_descricao' => $request['lp_descricao']
			);
			try {
				parent::sqlCRUD($dataSave, '', $this->TB_CADASTRO, '', 'U', 0, 0);
			} catch (Exception $e) {
				throw $e;
			}
		}

		public function LP_infoHeaderSave($request)
		{
			$dataSave = Array(
				'cadastro_idx' => (int)$_SESSION['plataforma_usuario']['id'],
				'lp_header' => $request['lp_header']
			);
			try {
				parent::sqlCRUD($dataSave, '', $this->TB_CADASTRO, '', 'U', 0, 0);
			} catch (Exception $e) {
				throw $e;
			}
		}

		public function LP_infoFooterSave($request)
		{
			$dataSave = Array(
				'cadastro_idx' => (int)$_SESSION['plataforma_usuario']['id'],
				'lp_footer' => $request['lp_footer']
			);
			try {
				parent::sqlCRUD($dataSave, '', $this->TB_CADASTRO, '', 'U', 0, 0);
			} catch (Exception $e) {
				throw $e;
			}
		}

		
	//Métodos de controle e bloqueio de acessos simultaneos usando access token
		//Verifica se o token atual é válido para acesso.
		public function checkAccessToken()
		{

			if (!isset($_SESSION['plataforma_usuario'])) {
				exit();
			}
			
			$activeAccessToken = md5($_SESSION['plataforma_usuario']['email'].session_id());

			$filename = "user_token_".$_SESSION['plataforma_usuario']['id'];
			$fileToken = dirname($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'plataforma_data'.DIRECTORY_SEPARATOR.$filename;

			$tokenTxtFile = fopen($fileToken, "r");
			$tokenTxt = fread($tokenTxtFile,filesize($fileToken));
			fclose($tokenTxtFile);

			if (trim($activeAccessToken)!=$tokenTxt) {
				
				//Desloga o usuario e encaminha ele para a capa do site.
				if (isset($_SERVER['HTTP_COOKIE'])) {
				   setcookie('plataforma_usuario', '', time()-1000);
				}
				unset($_SESSION['plataforma_usuario']);
				
				return false;
			}
			return true;

		}

		//Define o token para o login mais recente.
		public function setAccessToken($user)
		{
			$accessToken = md5($user['email'].session_id());
			$filename = "user_token_".$user['id'];
			$fileToken = dirname($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'plataforma_data'.DIRECTORY_SEPARATOR.$filename;
			$tokenTxtFile = fopen($fileToken, "w");
			fwrite($tokenTxtFile,$accessToken);
			fclose($tokenTxtFile);
		}
       


	} //End class
?>

