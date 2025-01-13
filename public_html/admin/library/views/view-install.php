<?php
	require_once($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR."admin".DIRECTORY_SEPARATOR."library".DIRECTORY_SEPARATOR."database".DIRECTORY_SEPARATOR."connect.class.php");
	if(Connect::checkConstants()){ header("Location: /admin"); }
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

	<meta name="description" content="" >

	<link rel="stylesheet" href="/admin/public/css/base.css">
	<link rel="stylesheet" href="/admin/public/css/fonts/font.css">
	<link rel="stylesheet" href="/admin/public/css/style.css">
	<link rel="stylesheet" href="/admin/public/vendor/bootstrap-3.0.3-dist/css/bootstrap.css">
	<link rel="stylesheet" href="/admin/public/vendor/font-awesome-4.0.3/css/font-awesome.css">

	<!--[if gte IE 9]> <style type="text/css"> body, button, input[type=button], input[type=reset], input[type=submit] { filter: none; } </style> <![endif]-->

	<script type="text/javascript" src="/admin/public/vendor/jquery-ui-1.10.4/js/jquery-1.10.2.js" ></script>
	<script type="text/javascript" src="/admin/public/js/scripts.js" ></script>

	<script type="text/javascript" >

		function validaConexao()
		{
			var data_db_host = document.frm_dados.data_db_host.value;
			var data_db_base = document.frm_dados.data_db_base.value;
			var data_db_login = document.frm_dados.data_db_login.value;
			var data_db_senha = document.frm_dados.data_db_senha.value;

			setTimeout(function(){

				$.ajax({
					url: "/admin/library/ajax-directin-install.php",
					type: "POST",
					data : {act:'1', dbhost:data_db_host, dbbase:data_db_base, dblogin:data_db_login, dbsenha:data_db_senha}
				}).done(function(retorno){

					switch(retorno)
					{
						case '1' :
						//conexao
						processaFileConn();
						break;
						case '2' :
						default :
						//conexão sucesso
						$("#form-retorno").html("Falha ao conectar");
						$("#form-retorno").slideDown();
						break;
					}

				});

			},1000);
		}

		function processaFileConn()
		{
			var data_db_host = document.frm_dados.data_db_host.value;
			var data_db_base = document.frm_dados.data_db_base.value;
			var data_db_login = document.frm_dados.data_db_login.value;
			var data_db_senha = document.frm_dados.data_db_senha.value;
			var data_db_prefix = document.frm_dados.data_db_prefix.value;

			$("#frm_dados").slideUp('fast');
			$("#action-list").slideDown('fast');

			$("#action-list #item_1").html('<img src="/admin/public/images/ajax-spinner-1.gif" alt="" align="absmiddle" /> Criando arquivo de configuração.');

			setTimeout(function(){

				$.ajax({
					url: "/admin/library/ajax-directin-install.php",
					type: "POST",
					data : {act:'2', dbprefix:data_db_prefix, dbhost:data_db_host, dbbase:data_db_base, dblogin:data_db_login, dbsenha:data_db_senha}
				}).done(function(retorno){

					switch(Math.round(retorno))
					{
						case 1 :
						//sucesso
							$("#action-list #item_1").html('<span class="fa fa-check-circle-o" style="font-size:22px;color:#00CC00" ></span>  Criando arquivo de configuração.');
							setTimeout(function(){ processaDumpSQL(); },2000);
						break;
						default :
							//erro
							$("#form-retorno").html("Falha ao executar a operação:<br>"+retorno);
							$("#form-retorno").slideDown();
						break;
					}

				});

			},1000);
		}

		function processaDumpSQL()
		{
			$("#action-list #item_2").html('<img src="/admin/public/images/ajax-spinner-1.gif" alt="" align="absmiddle" /> Preparando base de dados.');
			setTimeout(function(){
				$.ajax({
					url: "/admin/library/ajax-directin-install.php",
					type: "POST",
					data : {act:'3'}
				}).done(function(retorno){

					switch(Math.round(retorno))
					{
						case 1 :
						//sucesso
							$("#action-list #item_2").html('<span class="fa fa-check-circle-o" style="font-size:22px;color:#00CC00" ></span>  Preparando base de dados.');
							processaAdmin();
						break;
						default :
							//erro
							$("#form-retorno").html("Falha ao executar a operação:<br>"+retorno);
							$("#form-retorno").slideDown();
						break;
					}

				});
			},1000);
		}

		function processaAdmin()
		{
			$("#action-list #item_3").html('<img src="/admin/public/images/ajax-spinner-1.gif" alt="" align="absmiddle" /> Criando conta administrativa.');

			var adm_login = document.frm_dados.login.value;
			var adm_senha = document.frm_dados.login_senha.value;
			var adm_email = document.frm_dados.login_email.value;

			setTimeout(function(){
				$.ajax({
					url: "/admin/library/ajax-directin-install.php",
					type: "POST",
					data : {act:'4',login:adm_login,senha:adm_senha,email:adm_email}
				}).done(function(retorno){

					switch(Math.round(retorno))
					{
						case 1 :
							//sucesso
							$("#action-list #item_3").html('<span class="fa fa-check-circle-o" style="font-size:22px;color:#00CC00" ></span>  Criando conta administrativa.');
							processaConfig();
						break;
						default :
							//erro
							$("#form-retorno").html("Falha ao executar a operação:<br>"+retorno);
							$("#form-retorno").slideDown();
						break;
					}

				});
			},1000);
		}

		function processaConfig()
		{
			$("#action-list #item_4").html('<img src="/admin/public/images/ajax-spinner-1.gif" alt="" align="absmiddle" /> Configurando variáveis de ambiente.');
			setTimeout(function(){
				$.ajax({
					url: "/admin/library/ajax-directin-install.php",
					type: "POST",
					data : {act:'5'}
				}).done(function(retorno){

					switch(Math.round(retorno))
					{
						case 1 :
						//sucesso
							$("#action-list #item_4").html('<span class="fa fa-check-circle-o" style="font-size:22px;color:#00CC00" ></span>  Configurando variáveis de ambiente.');
							processaCheck();
						break;
						default :
							//erro
							$("#form-retorno").html("Falha ao executar a operação:<br>"+retorno);
							$("#form-retorno").slideDown();
						break;
					}

				});
			},1000);
		}

		function processaCheck()
		{
			$("#action-list #item_5").html('<img src="/admin/public/images/ajax-spinner-1.gif" alt="" align="absmiddle" /> Concluindo instalação.');
			setTimeout(function(){
				$.ajax({
					url: "/admin/library/ajax-directin-install.php",
					type: "POST",
					data : {act:'6'}
				}).done(function(retorno){

					switch(Math.round(retorno))
					{
						case 1 :
						//sucesso
							$("#action-list #item_5").html('<span class="fa fa-check-circle-o" style="font-size:22px;color:#00CC00" ></span>  Concluindo instalação.');
							setTimeout(function(){
								$("#action-list").slideUp('fast');
								$(".sucesso").slideDown('fast',function(){
									setTimeout(function(){
										window.location.href='/admin';
									},2000);
								});
							},1000);
						break;
						default :
							//erro
							$("#form-retorno").html("Falha ao executar a operação:<br>"+retorno);
							$("#form-retorno").slideDown();
						break;
					}

				});
			},1000);
		}

	</script>
	<style>

		html,body{ font-family:"roboto_condensed"; font-weight: 300; }
		#action-list{
			list-style: none;
			line-height: 200%;
			font-size: 22px;
			font-family: "roboto_condensed";
			font-weight: 300;
			display: none;
		}
		h2{ font-weight: bold; }
		#frm_dados{ display:block; }
		.wrapper{ width: 500px; }
		.sucesso{ display: none; font-size:42px; color:#00CC00; }

	</style>

</head>
<body>

	<ul class="ul_table">
		<li>
			<div class="wrapper">

				<div id="form-retorno" class="alert alert-danger" style='display:none;' >Preencha os dados corretamente.</div>

				<h2><img src="/admin/public/images/logo-directin.jpg" width="150" align="absmiddle" /> &nbsp; <small>Instalação do sistema</small></h2>

				<hr class='hr' />

				<div class="sucesso" ><span class="fa fa-check-circle-o"  ></span> Concluído com sucesso!</div>

				<ul id="action-list" >
					<li id="item_1" ><span style="color:#ccc" >Criando arquivo de configuração.</span></li>
					<li id="item_2" ><span style="color:#ccc" >Preparando base de dados.</span></li>
					<li id="item_3" ><span style="color:#ccc" >Criando conta administrativa.</span></li>
					<li id="item_4" ><span style="color:#ccc" >Configurando variáveis de ambiente.</span></li>
					<li id="item_5" ><span style="color:#ccc" >Concluindo instalação.</span></li>
				</ul>

				<form name="frm_dados" id="frm_dados" >

					<h3>Informe os dados de conexão com a base de dados:</h3>
					<table class="table" >
						<tr>
							<td>Host:</td>
							<td><input class="form-control" type="text" name="data_db_host" data-required="true" value="" /></td>
						</tr>
						<tr>
							<td>Nome do banco:</td>
							<td><input class="form-control" type="text" name="data_db_base" data-required="true" value="" /></td>
						</tr>
						<tr>
							<td>Login:</td>
							<td><input class="form-control" type="text" name="data_db_login" data-required="true" value="" /></td>
						</tr>
						<tr>
							<td>Senha:</td>
							<td><input class="form-control" type="text" name="data_db_senha" data-required="true" value="" /></td>
						</tr>
						<tr>
							<td>Prefixo das tabelas:<br>Padrão: <b>directin</b></td>
							<td><input class="form-control" type="text" name="data_db_prefix" data-required="true" value="" /><br /></td>
						</tr>
					</table>

					<h3>Login do administrador <br> <small>Informe os dados para a conta administrativa.</small></h3>
					<table class='table' >
						<tr>
							<td>Login:</td>
							<td><input class="form-control" type="text" name="login" data-required="true" value="" /></td>
						</tr>
						<tr>
							<td>E-mail:</td>
							<td><input class="form-control" type="email" name="login_email" data-required="true" value="" /></td>
						</tr>
						<tr>
							<td>Senha:</td>
							<td><input class="form-control" type="password" name="login_senha" data-required="true" value="" /></td>
						</tr>
						<tr>
							<td>Confirme a senha:</td>
							<td><input class="form-control" type="password" name="login_senha_c" data-required="true" value="" /></td>
						</tr>
					</table>

					<br />

					<button type="button" class="btn btn-default" onclick="javascript:checkFormRequire(document.frm_dados,'#form-retorno',validaConexao);" >Instalar</button>

					<br />
				</form>

				</div>
		</li>
	</ul>

</body>
</html>