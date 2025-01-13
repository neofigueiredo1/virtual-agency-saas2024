<?php
	$send = isset($_POST['exe']) ? (int)$_POST['exe'] : 0;
	$uid = isset($_SESSION['recu_password']['user_idx']) ? (int)$_SESSION['recu_password']['user_idx'] : 0;
	$nome = isset($_SESSION['recu_password']['nome']) ? Text::clean($_SESSION['recu_password']['nome']) : 0;
	if($send === 1) Auth::alterUserPass($uid);
?>

<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js no-ie"> <!--<![endif]-->
<head>
	<title>login</title>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<meta name="description" content="">

	<link rel="stylesheet" href="library/css/base.css">
	<link rel="stylesheet" href="library/css/login.css">
	<!--[if gte IE 9]> <style type="text/css"> body, button, input[type=button], input[type=reset], input[type=submit] { filter: none; } </style> <![endif]-->
</head>
<body>
	<ul class="login_body ul_table">
		<li>
			<div class="login_wrapper">


				<form action="" method="post" class="form-new-pass form_login" id="form-logins">
					<img src="library/images/logo-directin.jpg" width="300">
					<br><br>

					<input type="hidden" name="exe" value="1">
					<span style='font-size: 18px;'>Olá, <?php echo $nome; ?>.</span><br>
					<span style='font-size: 14px;'>Digite sua nova senha e a confirme!</span><br><br>
					<div id="senha-wrap">
						<ul class="ul_table">
							<li class="al_left"> <label for="senha">Nova senha:</label>&nbsp;&nbsp;<div class="help" hidden></div> </li>
						</ul>
						<input type="password" name="senha" id="senha" class="senha" >
					</div>
					<div id="recu-senha-wrap" >
						<ul class="ul_table">
							<li class="al_left"> <label for="recu_senha">Repetir senha:</label><div class="help" hidden></div> </li>
						</ul>
						<input type="password" name="recu_senha" id="recu_senha" class="senha"  />
					</div>

					<div class="clearfix"><!--  --></div>
					<a href="/admin" class="fl_left lost_voltar" title="Voltar" tabindex="-1">← Voltar para o login</a><br/>
					<div class="btn fl_left"><input type="submit" value="Salvar" ></div>
					<br>
				</form>

			</div>
		</li>
	</ul>


	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
	<script>window.jQuery || document.write('<script src="library/js/vendor/jquery-1.8.2.min.js"><\/script>')</script>
	<script src="library/js/login.js"></script>
	<script>
		window.onload = onLoadWn;
		window.onresize = onResizeWn;
	</script>
</body>
</html>
