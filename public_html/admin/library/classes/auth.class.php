<?php

	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;

	/**
	 * Classe de autenticação
	 * CLasse utlizada para autenticar, checar as permissões de um determinado usuário, e recupear senha.
	 */
	class Auth{
		/**
		 * Método que realiza o login do usuário.
		 * Cria a sessão de usuário, registra o Log e o redireciona para a inicial do sistema, isso se ele conseguir logar.
		 * Caso ele não consiga, registramos apenas o log e o redirecionamos para o Login.
		 */
		public static function logIn()
		{

			$login 		= Text::clean(addslashes(trim(strip_tags($_POST['nome']))));
			$password 	= md5(addslashes(trim(strip_tags($_POST['senha']))));

			$handleSql 	= new HandleSql();
			$content = $handleSql->select("SELECT usuario_idx, nivel, nome, email, login FROM ".$handleSql->getPrefix()."_login
						WHERE login = '" . $login . "' AND senha =  '" . $password . "' AND
						( ( status = 1 AND ( (validade > now() AND set_validade = 1) OR (set_validade <> 1) ) ) OR nivel=1 ) LIMIT 0,1");

			if(is_array($content) && count($content) > 0){
				foreach ($content as $v){
					$userPermissions 	= "";
					$userPermissionsData = $handleSql->select("Select * From ".$handleSql->getPrefix()."_login_permissao Where usuario_idx=".round($v['usuario_idx'])." ");
					if(is_array($userPermissionsData) && count($userPermissionsData)>0)
					{
						foreach($userPermissionsData as $user_permission)
						{
							$userPermissions .= "[".$user_permission['modulo_codigo']."-".$user_permission['permissao_codigo']."]";
						}
					}

					/**
					 * Gera a sessão usuário, e armazena um array com as informações do mesmo.
					 */
					$_SESSION['usuario'] = array(
	              	'id'			=> $v['usuario_idx'],
	              	'nivel'		=> $v['nivel'],
	              	'nome'		=> $v['nome'],
	              	'email'		=> $v['email'],
	              	'login'		=> $v['login'],
	              	'permissao'	=> $userPermissions
					);
					/**
					 * Inserindo o Log, informando que o login foi bem sucedido.
					 * Em seguida, verificação de permissão do filemanager com base nas permissoes do usuário no modulo de conteudo.
					 */
					Sis::insertLog(0, "Login", "efetuado", $_SESSION['usuario']['id'], $_SESSION['usuario']['nome'], "Sucesso");

					$_SESSION['ck_editor_auth'] = false;
					if(Sis::checkPerm('10001-4') || Sis::checkPerm('10001-1')){
						$_SESSION['ck_editor_auth'] = true;
					}

					/**
					 * Captura a informação do uso em disco no servidor, e armazena em sessão (sis_used_quota).
					 */
					$_SESSION['sis_used_quota'] = 0;
					try {
						$basedir 						= realpath($_SERVER["DOCUMENT_ROOT"]);//str_replace("\admin\library","",realpath(dirname(__FILE__)));
						$folder_site_size 				= Sis::folderSizeWL($basedir);
						$_SESSION['sis_used_quota'] 	= (int)$folder_site_size;
					} catch (Exception $e) {
						//Do Nothing
					}

					/**
					 * Redirecionando para o painel ou para a url que o usuário tentou acessar sem estar logado.
					 */
					if (isset($_SESSION["url_back"])) {
						die('<script>window.location = "'.$_SESSION["url_back"].'";</script>');
						unset($_SESSION["url_back"]);
					}else{
						echo '<script>window.location = "/' . PASTA_DIRECTIN . '/";</script>';
					}

				}
			}else{
				/**
				 * Caso o usuário ou senha estejam incorretos, o log é inserido, e o usuário volta para a senha de login.
				 */
				Sis::insertLog(0, "Login", "", 0, "", "Tentativa de Login por ".$login);
				// Sis::insertLog('TENTATIVA DE LOGIN', 'Tentou entrar usando o login: "'. $login .'"');
				ob_end_clean();
				die('<script>alert("Não foi possível efetuar o login!");history.back();</script>');
			}
		}


		/**
		 * Método que valida a sessão do usuário.
		 * Caso ela não tenha sido setada, ele verifica se foi passado via GET o código "rpass".
		 * Caso esse código tenha sidop passado, ele vai para a função validateCode, passando o mesmo como parâmetro
		 * Senão, ele redireciona para a página de login, deixando a sessão "url_back" setada.
		 */
		public static function validate(){
			if(!isset($_SESSION['usuario']) || empty($_SESSION['usuario'])) {
				$ckEditorAuth = isset($_GET['CKEditor']) ? true : false;
				if(!$ckEditorAuth) {
					$_SESSION["url_back"] = Sis::currPageUrl();
					if(isset($_GET['rpass'])){
						self::validateCode($_GET['rpass']);
					}else{
						$root = realpath($_SERVER["DOCUMENT_ROOT"]);
						require_once($root.'/admin/library/views/view-login.php');
					}
					die();
				}
			}
		}

		/**
		 * Método que realiza o LogOut no sistema.
		 * Destroi as sessões e redireciona o usuário para a página de login
		 */
		public static function logout(){
			session_destroy();
			die(Sis::redirect("/" . PASTA_DIRECTIN . "/",0));
		}


		/**
		 * Método que verifica a permissão do usuário.
		 */
		public static function checkPermission($perm){
			if($_SESSION['usuario']['nivel'] > $perm)
			{
				ob_end_clean();
				die(Sis::redirect("/" . PASTA_DIRECTIN . "/",0.1));
			}
		}


		/**
		 * Na recuperação de senha, o usuário retorna um código ($code) via GET, que trás o id do usuário e email juntos em um md5
		 * Essa função verifica se esse código é valido, e caso seja o usuário é direcionado para uma página de redefinição de senha. Sendo definidas as sessões com seu nome e id.
		 * Senão, ele é direcionado para a página de login.
		 * @param string $code - Código md5 com o id do usuário e seu e-mail
		 */
		public static function validateCode($code){
			$code = Text::clean(addslashes(trim(strip_tags($code))));
			$handleSql = new HandleSql();

			$sql 			= "SELECT usuario_idx, nome FROM ".$handleSql->getPrefix()."_login Where md5(CONCAT(usuario_idx,email))='".$code."' LIMIT 0,1";
			$codeDataV 	= $handleSql->select($sql);
			if(is_array($codeDataV) && count($codeDataV) > 0){
				$_SESSION['recu_password']['user_idx'] = $codeDataV[0]['usuario_idx'];
				$_SESSION['recu_password']['nome'] 		= $codeDataV[0]['nome'];
				require_once('library/recover-pass.php');
			}else{
				require_once('library/login.php');
			}
			die();
		}

		/**
		 * Método que altera a senha do usuário. Chamado no arquivo library/recover-pass.php
		 * @param int $uid - Id do usuário que terá a senha alterada. Está armazenado em sessão no momento em que o formlário é submetido.
		 * @return alert com uma mensagem de sucesso ou erro.
		 */
		public static function alterUserPass($uid){
			$senha 		= md5(addslashes(trim(strip_tags($_POST['senha']))));
			$recu_senha = md5(addslashes(trim(strip_tags($_POST['recu_senha']))));
			if($senha == $recu_senha){
				$uid 			= addslashes(trim(strip_tags($uid)));
				if($uid != 0){
					$handleSql = new HandleSql();
					$sql 	= "UPDATE ".$handleSql->getPrefix()."_login SET senha='".$senha."' Where usuario_idx=".$uid." ";
					$alterUserPass = $handleSql->update($sql);
					if(isset($alterUserPass) && $alterUserPass != FALSE){
						die("<script>alert('Senha alterada com sucesso.');location.href='/admin/'</script>");
					}else{
						die("<script>alert('Erro ao salvar os dados.');location.href='/admin/'</script>");
					}
				}
			}else{
				die("<script>alert('Erro ao salvar os dados.');location.href='/admin/'</script>");
			}
		}


		/**
		 * Método para enviar um e-mail para o usário que deseja recuperar a senha com um link da página de recuperação de senha.
		 * @param string $mail - E-mail do usuário para quem a mensagem deve ser enviada. Lembrando que este e-mail deve ser válido e deve conter na base de dados do sistema, e este usuário deve estar válido (status == 1).
		 */
		public static function sendMailUser($mail){
			$mail = Text::clean(addslashes(trim(strip_tags($mail))));
			if (!empty($mail) && Sis::isValidEmail($mail)){
				$handleSql 	= new HandleSql();
				$sql 		= "SELECT usuario_idx,email,nome,status FROM ".$handleSql->getPrefix()."_login WHERE email = '" . $mail . "' LIMIT 0,1";
				$usuarioData = $handleSql->select($sql);
				/**
				 * Caso tenha sido encontrado algum usuário com o e-mail especificado, ele formata a mensagem e envia o e-mail.
				 */
				if(is_array($usuarioData) && count($usuarioData) > 0){
					$keyToSend 			= (md5($usuarioData[0]["usuario_idx"].$usuarioData[0]["email"]));
					$mailClass 			= new PHPMailer();
					$toEmail 			= $mail;
					$HTML_mensagem 	= Sis::returnMessageBodyClient("DirectIn - Recuperação de senha.");
					$corpo_mensagem 	= "Oi, ".$usuarioData[0]['nome'].". <br /><br />
												Esqueceu sua senha? <br /><br />
												Recentemente alguém realizou o processo de recuperação de senha no administrador do site ".str_replace("http://", "", Sis::config("CLI_URL")).", utlizando seu e-mail. <br /><br />
												Se não foi você, não se preocupe! Seus dados estão seguros.<br /><br />
												Se foi você, clique <a href='".Sis::config("CLI_URL")."/admin?rpass=".$keyToSend."'>neste link</a> para recuperar sua senha.<br /><br />
												Atenciosamente, <br />
												".Sis::config("CLI_NOME");
					$HTML_mensagem 	= str_replace("[HTML_DADOS]",$corpo_mensagem,$HTML_mensagem) ;
					echo "<a href='/admin?rpass=".$keyToSend."'>neste link</a>";
					die();

					if(class_exists("PHPMailer\PHPMailer\PHPMailer")){

						$mailClass->CharSet     = "UTF-8";
						$mailClass->ContentType = "text/html";
						$mailClass->IsSMTP();
						$mailClass->Host        = Sis::config("CLI_SMTP_HOST");
						if(Sis::config("CLI_SMTP_HOST_PORTA")!="undefined"){ $mailClass->Port = Sis::config("CLI_SMTP_HOST_PORTA"); }
						if(Sis::config("CLI_SMTP_CONEXAO")){ $mailClass->SMTPSecure = Sis::config("CLI_SMTP_CONEXAO"); }

						if(Sis::config("CLI_SMTP_MAIL") != ""){
							$mailClass->SMTPAuth    = true;
							$mailClass->Username    = Sis::config("CLI_SMTP_MAIL");
							$mailClass->Password    = Sis::config("CLI_SMTP_PASS");
						}
						$CLI_MAIL_CONTATO = explode(",",trim(Sis::config("CLI_MAIL_CONTATO")));
				        $fromEmail = trim($CLI_MAIL_CONTATO[0]);
				        $mailClass->From        = $fromEmail;
				        $mailClass->FromName    = Sis::config("CLI_NOME");

						$mailClass->AddAddress(trim($toEmail), $usuarioData[0]['nome']);
						$mailClass->Subject = "DirectIn - Recuperação de senha.";
						$mailClass->Body = $HTML_mensagem;
						if($mailClass->Send()){
							return "ok";
						}else{
							return "nao";
						}
					}
					die();
				}else{
					die("<script>alert('O e-mail que você digitou não consta na base de dados. Verifique se você digitou o e-mail corretamente.');location.href='/admin/'</script>");
				}
			}else{
				die("<script>alert('O e-mail que você digitou não consta na base de dados. Verifique se você digitou o e-mail corretamente.');location.href='/admin/'</script>");
			}
		}

	}//End class Auth
?>