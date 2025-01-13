<?php
	$send = isset($_POST['exe']) ? (int)$_POST['exe'] : 0 ;
	if($send === 1) Auth::logIn();
	if($send === 3) Auth::sendMailUser($_POST['email']);
?>

<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js no-ie"> <!--<![endif]-->
<head>
	<title>Login</title>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<meta name="description" content="">

	<link rel="stylesheet" href="/admin/public/css/base.css">
	<link rel="stylesheet" href="/admin/public/css/login.css">
	<!--[if gte IE 9]> <style type="text/css"> body, button, input[type=button], input[type=reset], input[type=submit] { filter: none; } </style> <![endif]-->
</head>
<body>
	<ul class="login_body ul_table">
		<li>
			<div class="login_wrapper">


				<form action="" method="post" class="form_login " id="form-login">
					<!-- <figure><img src="/admin/public/images/logomarca_cliente.svg" ></figure> -->
					<br><br>

					<input type="hidden" name="exe" value="1">

					<div id="nome-wrap" >
						<ul class="ul_table">
							<li class="al_left"> <label for="nome">Nome:</label><div class="help" hidden></div> </li>
						</ul>
						<input type="text" name="nome" id="nome" >
					</div>
					<div id="senha-wrap">
						<ul class="ul_table">
							<li class="al_left"> <label for="senha">Senha:</label>&nbsp;&nbsp;<div class="help" hidden></div> </li>
						</ul>
						<input type="password" name="senha" id="senha" class="v_key_write_pass" >
						<input type="text" name="senha_t" id="senha_t" style="display:none" class="v_key_write_pass" >
					</div>

					<div class="clearfix"><!--  --></div>
					<div class="btn fl_left"><input type="submit" value="Entrar" ></div>
					<br>
					<div class="al_right"> <a href="javascript:;" id="pass-lost" title="Esqueceu a senha?" tabindex="-1">Esqueceu sua senha?</a> </div>

				</form>


				<form action="" method="post" id="form-pass-lost" class="form_login form_get_lost_pass" hidden>
					<!-- <figure><img src="/admin/public/images/logomarca_cliente.svg" ></figure> -->
					<br><br>
					<input type="hidden" name="exe" value="3">
					<span style='font-size: 18px;'>Esqueceu a sua senha?</span><br>
					<span style='font-size: 12px;'>Digite o seu e-mail abaixo, que nós explicaremos <br />como recuperar sua senha!</span><br><br>
					<div id="email-wrap">
						<ul class="ul_table">
							<li class="al_left"> <label for="email">Email:</label>&nbsp;&nbsp;<div class="help" hidden></div></li>
							<li class="al_right"> <a href="javascript:;" class="lost_voltar" title="Voltar" tabindex="-1">← Voltar</a> </li>
						</ul>
						<input type="text" name="email" id="email">
					</div>
					<div class="btn fl_left"><input type="submit" value="Enviar" ></div>
				</form>


			</div>
		</li>
	</ul>


	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
	<script>window.jQuery || document.write('<script src="/admin/public/js/vendor/jquery-1.8.2.min.js"><\/script>')</script>
	<script src="/admin/public/js/login.js"></script>
	<script>
	window.onload = onLoadWn;
	window.onresize = onResizeWn;
	</script>
</body>
</html>
