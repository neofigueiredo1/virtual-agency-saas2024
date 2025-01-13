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
	if(!is_numeric($exe)) $exe = 0;
	if($exe == 1) self::login();
	if($exe == 2) self::geraCodigoNovaSenha();
	if($exe == 3) self::iniciaCadastroComCep();
	if($exe == 4) self::salvaNovaSenha($rpass);
	if($exe == 5) self::confirmaCadastro();

	$_SESSION["ecommerce_url_back_cadastro"] = $_SERVER['REQUEST_URI'];
?>

<!--
<h3><?php echo ($login) ? "Login":"Recuperação de senha" ?></h3>
<p><?php echo ($login) ? "Se você já é um usuário cadastrado, entre com seus dados.":"Entre com uma nova senha e clique em salvar!" ?></p>
-->

<?php
	$emailSession = "";
	if(isset($_SESSION['ecommerce_login_usuario_alerts']) && is_array($_SESSION['ecommerce_login_usuario_alerts']))
   {
      $tipo="warning";
      switch($_SESSION['ecommerce_login_usuario_alerts']['tipo'])
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

      <div id="alert-bts" style="display: block;" class="alert alert-<?php echo $tipo; ?> fade in">
         <button onclick="javascript: $(this).parent().slideUp('fast');" type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
         <?php echo $icone.$_SESSION['ecommerce_login_usuario_alerts']['mensagem']; ?>
      </div>

      <?php
      	if(array_key_exists('email', $_SESSION['ecommerce_login_usuario_alerts'])){
      		$emailSession = $_SESSION['ecommerce_login_usuario_alerts']['email'];
      	}
         unset($_SESSION['ecommerce_login_usuario_alerts']);
   // }else{
      ?>
      <script>
         $(document).ready(function(){
            setTimeout(function(){
               $('#alert-bts').slideUp('fast');
            }, 5000);
         });
      </script>
      <?php
   }
?>

<!--<hr class='hr' >-->

<div class="panel">
	<div class="panel-body box_form">

		<?php
			/**
			 * Caso NÃO tenha sido passado nenhum parâmetro com um MD5 válido para a verificação da chave (e-mail+cpf), ele exibe a tela de login.
			 * Senão, ele exibe a tela de recuperação de senha.
			 */
			if($login) :
		?>

		<ul class="table-login" >
			<li class="side">
				<h2>Já sou cliente</h2>
				<!-- FORM - LOGIN -->
				<form action="" name="form_login" method="post" class="form_login" id="form-login">
					<input type="hidden" name="exe" value="1">
					<div class="alert alert-danger" id="error-box">Preencha os dados corretamente para continuar!</div>
					
                    <div class="form-group col-sm-12" >
                        <label for="email">E-mail</label>
                        <input type="email" name="email" class="form-control transition" data-required="true" placeholder="Digite o seu e-mail" value="<?php echo (isset($emailSession))?$emailSession:""; ?>" />
                    </div>
                    <div class="form-group col-sm-12" >
                        <label for="senha">Senha</label>
                        <input type="password" name="senha" class="form-control transition" data-required="true" placeholder="Digite a sua senha" value="" />
                    </div>
                    <div class="form-group col-sm-12" >
					    <input type="button" name="submit-1" class="btn btn-red pull-right" value="Continuar" onclick="javascript:checkFormRequire(document.form_login, '#error-box');" />
                    
                        <div class="pull-left">
                            <a href="" class="text-primary" onclick="javascript: $('.form-confirmar').slideUp('fast', function(){ $('.form-esqueceu').slideDown(); }); return false;">Esqueci minha senha</a><div class="clear"></div>
                            <a href="" class="text-primary" style="margin-top: 4px;" onclick="javascript: $('.form-esqueceu').slideUp('fast', function(){ $('.form-confirmar').slideDown(); }); return false;">Confirmar meu cadastro</a>
                        </div>
                    </div>

				</form>
				<div class="clear"></div>
				<!-- FORM - ESQUECEU A SENHA -->
				<form action="" name="form_esqueci" method="post" class="form_login form-esqueceu" id="form-login">
					<input type="hidden" name="exe" value="2">
					<div class="alert alert-danger" id="error-box-esqueci">Por favor, preencha os dados corretamente!</div>
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
					<div class="alert alert-danger" id="error-box-confirmar">Por favor, preencha os dados corretamente!</div>
					<label for="email_esqueci">E-mail</label>
					<input type="email" name="email_confirmacao" class="form-control transition" data-required="true" placeholder="Digite o seu e-mail" value="" />
					<input type="button" name="cancel" class="btn btn-red pull-right" value="Enviar" onclick="javascript:checkFormRequire(document.form_confirmar, '#error-box-confirmar');" />&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="button" name="submit-3" class="btn btn-default pull-right btn-cancel" value="Cancelar" onclick="javascript: $('.form-confirmar').slideUp();" />
					<div class="clear"></div>
				</form>
				<br />
				<p><i class="fa fa-lock"></i>&nbsp; Nós trabalhamos para que as compras efetuadas em nosso site sejam 100% seguras.</p>
			</li>
			<li class="side">
				<h2>Quero ser cliente</h2>
				<form action="" name="form_cadastro_cep" method="post" class="form_login form-cadastro-cep" id="form-login">
					<input type="hidden" name="exe" value="3">
					<div class="alert alert-danger" id="error-box-cadastro-cep">Por favor, preencha os dados corretamente!</div>
					<label for="cep">CEP</label>
					<input type="text" name="cep" class="cep form-control" data-required="true" placeholder="Digite os números do seu CEP." value="" />
					<input type="button" name="submit-3" class="btn btn-red pull-right" value="Continuar" onclick="javascript:checkFormRequire(document.form_cadastro_cep, '#error-box-cadastro-cep');" />
					<div class="clear"></div>
				</form>
			</li>
		</ul>

		<?php else : ?>

		<ul class="table-login">
			<li class="side">
				<h2>Recuperação de senha</h2>
				<form action="" name="form_login" method="post" class="form_login" id="form-login" >
					<input type="hidden" name="exe" value="4" >
					<div class="alert alert-danger" id="error-box">Preencha os dados corretamente para continuar!</div>
					<div class="alert alert-danger" id="error-box-combinacao-pass">Confirme sua nova senha corretamente!</div>
					<label for="senha">Senha</label>
					<input type="password" name="senha" class="form-control transition" data-required="true" placeholder="Digite uma nova senha" value="" />
					<label for="senha_confirm">Confirmação de Senha</label>
					<input type="password" name="senha_confirm" class="form-control transition" data-required="true" placeholder="Confirme a sua nova senha" value="" />
					
					<input type="button" name="submit-4" class="btn btn-red pull-right" value="Continuar" onclick="javascript:checkFormRequire(document.form_login, '#error-box', checkSenhaReset);" />
					
					<a href="/login" class="show-form"><i class="fa fa-chevron-left" ></i> Voltar para o login</a>
					<div class="clear"></div>
				</form>
			</li>
		</ul>

		<?php endif; ?>
		<div class="clear"></div>

		<script>
			$('.cpf').mask('999.999.999-99');
			$('.cep').mask('99999-999');
		</script>
	</div>
</div>