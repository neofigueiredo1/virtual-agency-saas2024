<?php
	$login = TRUE;

	$rpass = (isset($_GET['rpass'])) ? Text::clean($_GET['rpass']) : 0;
    $rpassArr = explode("-", base64_decode($rpass));

	if(is_array($rpassArr) && count($rpassArr) == 2){
		$rpass = $rpassArr[1];
		$dataArr = explode("_", base64_decode($rpassArr[0]));
        if(Sis::isValidMd5($rpass) && date('Ymd') == $dataArr[0] && (int)date('His')){
			$login = false;
		}
	}

	$codeConfirm = (isset($_GET['codeconfirm'])) ? Text::clean($_GET['codeconfirm']) : "";
	if($codeConfirm!=""){
    	$codeConfirm = base64_decode($codeConfirm);
        if(Sis::isValidMd5($codeConfirm)){
            self::getCadastroConfirmation($codeConfirm);
		}
	}

	$exe = (isset($_POST['exe'])) ? (int)$_POST['exe'] : 0;
	//if(!is_numeric($exe)) $exe = 0;
	switch ($exe) {
		case 1:
			self::login();
			break;
		case 2:
			self::geraCodigoNovaSenha();
			break;
		case 3:
			self::cadastroInsertInicial();
			//self::iniciaCadastroComCep();
			break;
		case 4:
			self::salvaNovaSenha($rpass);
			break;
		case 5:
			self::confirmaCadastro();
			break;
	}
	/*
	if($exe == 1) self::login();
	if($exe == 2) self::geraCodigoNovaSenha();
	if($exe == 3) self::iniciaCadastroComCep();
	if($exe == 4) self::salvaNovaSenha($rpass);
	if($exe == 5) self::confirmaCadastro();
	*/

	$_SESSION["ecommerce_url_back_cadastro"] = $_SERVER['REQUEST_URI'];
?>

<!--
<h3><?php echo ($login) ? "Login":"Recuperação de senha" ?></h3>
<p><?php echo ($login) ? "Se você já é um usuário cadastrado, entre com seus dados.":"Entre com uma nova senha e clique em salvar!" ?></p>
-->

<?php
	$emailSession = "";
	$msgSession = null;
	if(isset($_SESSION['ecommerce_login_usuario_alerts']) && is_array($_SESSION['ecommerce_login_usuario_alerts']))
   {
   	$msgSession=$_SESSION['ecommerce_login_usuario_alerts'];
   	unset($_SESSION['ecommerce_login_usuario_alerts']);
   }
   if(isset($_SESSION['ecommerce_cadastro_usuario_erros']) && is_array($_SESSION['ecommerce_cadastro_usuario_erros']))
   {
   	$msgSession=$_SESSION['ecommerce_cadastro_usuario_erros'];
   	unset($_SESSION['ecommerce_cadastro_usuario_erros']);
   }

   if ($msgSession!=null) {

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

      <div id="alert-bts" style="display: block;" class="alert alert-<?php echo $tipo; ?>">
         <button onclick="javascript: $(this).parent().slideUp('fast');" type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
         <?php echo $icone.$msgSession['mensagem']; ?>
      </div>
      <?php
      	if(array_key_exists('email', $msgSession)){
      		$emailSession = $msgSession['email'];
      	}
         //unset($msgSession);
   	// }else{
      ?>
      <?php
   }
?>

<!--<hr class='hr' >-->



<?php
/**
 * Caso NÃO tenha sido passado nenhum parâmetro com um MD5 válido para a verificação da chave (e-mail+cpf), ele exibe a tela de login.
 * Senão, ele exibe a tela de recuperação de senha.
 */
if($login) :

	header("Location:/"); exit();

?>
<div class="a-login row" >
	<div class="col-md-5 offset-md-1">


		<div class="forms_container">

			<h2>Já possui cadastro? <span class="bold">Entre na sua conta</span></h2>

			<!-- FORM - LOGIN -->
			<form action="" name="form_login" method="post" class="form_login" id="form-login">
				<input type="hidden" name="exe" value="1">
				
				<div class="alert alert-danger mb-20" id="error-box" >Preencha os dados corretamente para continuar!</div>
				
				<div class="form-group" >
					<div class="input-group input-group-lg" >
						<span class="input-group-addon  glyphicon glyphicon-envelope" id="sizing-addon1" ></span>
						<input type="email" name="email" class="form-control" placeholder="E-mail" aria-describedby="sizing-addon1" data-required="true" placeholder="Digite o seu e-mail"
						value="<?php echo (isset($emailSession))?$emailSession:""; ?>" >
					</div>
				</div>
				<div class="form-group" >
					<div class="input-group input-group-lg" >
						<span class="input-group-addon glyphicon glyphicon-asterisk" id="sizing-addon1"></span>
						<input type="password" name="senha" class="form-control" data-required="true" value="" placeholder="Senha" aria-describedby="sizing-addon1">
					</div>
				</div>
				<div class="form-check d-flex justify-content-around align-items-center" >
					
					<div class="lembrar-dados d-flex align-items-center" >
		                <input type="checkbox" class="form-check-input"  id="lembrar_dados" name="lembrar_dados" value="1" />
		                <label class="form-check-label" for="lembrar_dados">Lembrar dados</label>
		            </div>
					<a href="#" class="recuperar-senha" onclick="javascript:if(document.form_login.email.value!=''){ document.form_login.exe.value=2; document.form_login.submit(); }else{ $('#error-box').slideDown('normal',function(){ setTimeout(function(){ $('#error-box').fadeOut(); },3000) }); }" >Recuperar senha</a>

				</div>
				<div class="form-group" >
					<input type="button" name="submit-1" class="btn btn btn-login" value="Entrar" onclick="javascript:checkFormRequire(document.form_login, '#error-box');" />
				</div>

				<?php if (false): ?>
				<div class="form-group" >
					<a href="" class="btn btn-t3 btn-vasado btn-100" style="margin-top: 4px;" 
						onclick="javascript: $('.form-esqueceu').slideUp('fast', function(){ $('.form-confirmar').slideDown(); }); return false;"
					>Confirmar meu cadastro</a>
				</div>
				<?php endif ?>

			</form>
			<div class="clear"></div>
			
			<?php if (false): ?>
				<!-- FORM - ESQUECEU A SENHA -->
				<form action="" name="form_esqueci" method="post" class="form_login form-esqueceu" id="form-login">
					<input type="hidden" name="exe" value="2">
					<div class="alert alert-danger subtitulo-t5 mt-light" id="error-box-esqueci" >Por favor, preencha os dados corretamente!</div>
					<label for="email_esqueci">E-mail</label>
					<input type="email" name="email_esqueci" class="form-control transition" data-required="true" placeholder="Digite o seu e-mail" value="" />
					<label for="cpf">CPF</label>
					<input type="text" name="cpf" class="cpf form-control" data-required="true" placeholder="Digite os números do seu CPF." value="" />
					<input type="button" name="cancel" class="btn btn-red pull-right" value="Recuperar senha" onclick="javascript:checkFormRequire(document.form_esqueci, '#error-box-esqueci');" />
					<input type="button" name="submit-2" class="btn btn-default pull-right btn-cancel" value="Cancelar" onclick="javascript: $('.form-esqueceu').slideUp();" />
					<div class="clear"></div>
				</form>
				<div class="clear"></div>

				<!-- FORM - CONFIRMAÇÃO DE CADASTRO -->
				<form action="" name="form_confirmar" method="post" class="form_login form-confirmar" id="form-login">
					<input type="hidden" name="exe" value="5">
					<div class="alert alert-danger subtitulo-t5 mt-light" id="error-box-confirmar" >Por favor, preencha os dados corretamente!</div>
					<label for="email_esqueci" >E-mail</label>
					<input type="email" name="email_confirmacao" class="form-control transition" data-required="true" placeholder="Digite o seu e-mail" value="" />
					<input type="button" name="cancel" class="btn btn-red pull-right" value="Enviar" 
						onclick="javascript:checkFormRequire(document.form_confirmar, '#error-box-confirmar');" 
					/>
					&nbsp;&nbsp;&nbsp;
					<input type="button" name="submit-3" class="btn btn-default pull-right btn-cancel" value="Cancelar" onclick="javascript: $('.form-confirmar').slideUp();" />
					<div class="clear" ></div>
				</form>
			<?php endif ?>
		</div>

	</div>
	<div class="col-md-5 offset-md-1">

		<div class="forms_container">

			<h2>Não possui cadastro? <span class="bold">Crie agora uma conta</span></h2>
	    	
			<form action="" name="form_cadastro_novo" method="post" class="form_login" id="form-cadastro-novo" >
				<div class="alert alert-danger subtitulo-t5 mt-light" id="error-box-cadastro-novo" >Por favor, preencha os dados corretamente!</div>
				<div class="alert alert-danger subtitulo-t5 mt-light" id="msg-box-cadastro-novo" >Por favor, preencha os dados corretamente!</div>
				<input type="hidden" name="exe" value="3" >
				
				<div class="form-group" >
					<input type="name" class="form-control" placeholder="Nome" name="nome_completo" data-required="true"  >
					
				</div>
				<div class="form-group" >
					<input type="name" class="form-control" placeholder="E-mail" name="email" data-required="true" >
				</div>
				<div class="form-group" >
					<input type="password" class="form-control" placeholder="Senha" name="senha" data-required="true" >
					
				</div>
				<div class="form-group" >
					<input type="password" class="form-control" placeholder="Confirma senha" name="senha_conf" data-required="true" >
					
				</div>
				<div class="form-check d-flex mb-3 justify-content-between align-items-center">
					<input type="checkbox" class="form-check-input" id="receber_boletim"  name="receber_boletim" value="1" >
					<label class="form-check-label" for="receber_boletim">Desejo receber novidades do site no meu e-mail.</label>
				</div>
				<div class="form-check d-flex mb-3 justify-content-between align-items-center">
					<input type="checkbox" class="form-check-input" id="termos"  name="termos" value="1" >
					<label class="form-check-label" for="termos" >Li, compreendi, e aceito os <a href="/termos-e-condicoes-de-uso" >termos de uso</a>.</label>
				</div>
				<input type="button" name="submit-3" class="btn btn-login" value="Começar"
					onclick="javascript:checkFormRequire(document.form_cadastro_novo, '#error-box-cadastro-novo', checkCadastroInicial);" />
			</form>

		</div>


	</div>	
</div>

<?php else : ?>

	<div class="a-login row" >
		<div class="col-sm-8 cormo-medium" >

			<h2>Recuperação de senha</h2>
			<form action="" name="form_login_recover" method="post" class="form_login" id="form-login" >
				<input type="hidden" name="exe" value="4" >
				<div class="alert alert-danger subtitulo-t5 mt-light" style="display:none;" id="error-box">Preencha os dados corretamente para continuar!</div>
				<div class="alert alert-danger subtitulo-t5 mt-light" style="display:none;" id="error-box-combinacao-pass">Confirme sua nova senha corretamente!</div>
				
				<label for="senha">Senha</label>
				<input type="password" name="senha" class="form-control transition" data-required="true" placeholder="Digite uma nova senha" value="" />

				<label for="senha_confirm">Confirmação de Senha</label>
				<input type="password" name="senha_confirm" class="form-control transition" data-required="true" placeholder="Confirme a sua nova senha" value="" />

				<!-- <a href="/login" class="show-form btn green float-left"><i class="fa fa-chevron-left" ></i> Voltar para o login</a> -->

				<input type="button" name="submit-4" class="btn btn-red float-right" value="Continuar" onclick="javascript:Util.checkFormRequire(document.form_login_recover, '#error-box', checkSenhaReset);" />

				<div class="clear"></div>
			</form>

		</div>
	</div>

<?php endif; ?>

<div class="clear"></div>
<?php if (false): ?>
<script>
	/*
	$('.cpf').mask('999.999.999-99');
	$('.cep').mask('99999-999');
	*/
</script>
<?php endif ?>
