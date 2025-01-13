<?php
	$send = isset($_POST['exe']) ? (int)$_POST['exe'] : 0 ;
	if($send === 1) Auth::logIn();
	if($send === 3) Auth::sendMailUser($_POST['email']);
?>

<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js no-ie h-100" > <!--<![endif]-->
<head>
	<title>login</title>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<meta name="description" content="">

	<!-- <link rel="stylesheet" href="/admin/public/css/base.css"> -->
	<!-- <link rel="stylesheet" href="/admin/public/css/login.css"> -->

	<!-- Google Font: Source Sans Pro -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
	<!-- Font Awesome Icons -->
	<link rel="stylesheet" href="/admin/public/vendor/admin-lte/plugins/fontawesome-free/css/all.min.css">
	<!-- Theme style -->
	<link rel="stylesheet" href="/admin/public/vendor/admin-lte/dist/css/adminlte.min.css">

	<!-- Admin Custom style -->
	<link rel="stylesheet" href="/admin/public/vendor/css/adminlte.theme.css" >

	<!--[if gte IE 9]> <style type="text/css"> body, button, input[type=button], input[type=reset], input[type=submit] { filter: none; } </style> <![endif]-->
</head>
<body class="dark-mode h-100" >


<section class="container h-100 d-flex align-items-center justify-content-center" >
	<div class="login-box" >
		<div class="login-logo">
		<figure><img src="/admin/public/images/logo.svg" ></figure>
	</div>
	<!-- /.login-logo -->
	<div class="s_form_login card " >
		<div class="card-body login-card-body">
			<p class="login-box-msg">Login</p>
			<form id="form-login" action="" method="post">
				<input type="hidden" name="exe" value="1">
				<div class="input-group mb-3">
					<input type="text" class="form-control" name="nome" id="nome"  placeholder="Login" required >
					<div class="input-group-append">
						<div class="input-group-text">
							<span class="fas fa-user"></span>
						</div>
					</div>
				</div>
				<div class="input-group mb-3">
					<input type="password" name="senha" id="senha" class="form-control" placeholder="Senha" required >
					<div class="input-group-append">
						<div class="input-group-text">
							<span class="fas fa-lock"></span>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-8">
						<div class="icheck-primary" >
							<input type="checkbox" id="remember">
							<label for="remember">
								Manter conectado
							</label>
						</div>
					</div>
					<!-- /.col -->
					<div class="col-4">
						<button type="submit" class="btn btn-primary btn-block">Entrar</button>
					</div>
					<!-- /.col -->
				</div>
			</form>
			<p class="mb-1">
				<a href="javascript:;" title="Esqueceu a senha?" tabindex="-1"
					onclick="javascript:$('.s_form_login').slideUp('fast');$('.s_form_login_recover').slideDown('fast');"
				>Esqueceu a senha?</a>
			</p>
		</div>
		<!-- /.login-card-body -->
	</div>

	<!-- /.login-logo -->
	<div class="s_form_login_recover card " style="display:none;" >
		<div class="card-body login-card-body">
			<p class="login-box-msg" >
				Esqueceu a sua senha?
				<br/><small>
					Digite o seu e-mail abaixo, que nós explicaremos <br />como recuperar sua senha!
				</small>
			</p>
			<form id="form-login" action="" method="post">
				<input type="hidden" name="exe" value="3">
				<div class="input-group mb-3">
					<input type="email" class="form-control" name="email" id="email"  placeholder="E-mail" required >
					<div class="input-group-append">
						<div class="input-group-text">
							<span class="fas fa-envelope"></span>
						</div>
					</div>
				</div>
				<div class="row" >
					<!-- /.col -->
					<div class="col-4 offset-8" >
						<button type="submit" class="btn btn-primary btn-block" >Enviar</button>
					</div>
					<!-- /.col -->
				</div>
			</form>
			<p class="mb-1">
				<a href="javascript:;" title="Voltar para o login" tabindex="-1"
					onclick="javascript:$('.s_form_login_recover').slideUp('fast');$('.s_form_login').slideDown('fast');"
				>Voltar para o login</a>
			</p>
		</div>
		<!-- /.login-card-body -->
	</div>

</div>
<!-- /.login-box -->
</section>








	<ul class="login_body ul_table d-none">
		<li>
			<div class="login_wrapper">


				<form action="" method="post" class="form_login " id="form-login">
					<img src="/admin/public/images/logo-directin.jpg" width="300">
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


				<form action="" method="post" id="form-pass-lost" class="form_login form_get_lost_pass" hidden >
					<img src="/admin/public/images/logo-directin.jpg" width="250">
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


	<!-- <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script> -->
	<script>//window.jQuery || document.write('<script src="/admin/public/js/vendor/jquery-1.8.2.min.js"><\/script>')</script>
	
	<script>
	// window.onload = onLoadWn;
	// window.onresize = onResizeWn;
	</script>





		<!-- jQuery -->
		<script src="/admin/public/vendor/admin-lte/plugins/jquery/jquery.min.js"></script>
		<!-- Bootstrap 4 -->
		<script src="/admin/public/vendor/admin-lte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

		<script src="/admin/public/js/login.js"></script>
		<!-- AdminLTE App -->
		<script src="/admin/public/vendor/admin-lte/dist/js/adminlte.min.js"></script>
		<!-- AdminLTE for demo purposes -->
		<!-- <script src="/admin/public/vendor/admin-lte/dist/js/demo.js"></script> -->



</body>
</html>
