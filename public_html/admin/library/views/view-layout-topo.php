<!DOCTYPE html>
<head>
	<title>DirectIn - CMS</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="description" content="">

	<link rel="apple-touch-icon" sizes="144x144" href="/apple-touch-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="114x114" href="/apple-touch-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="72x72" href="/apple-touch-icon-72x72.png">
	<link rel="apple-touch-icon" href="/apple-touch-icon.png">

	<link rel="shortcut icon" href="/admin/favicon.ico" />

	<?php
		$handleSql = new HandleSql();
		$getVarSystem = $handleSql->select("SELECT valor FROM " . $handleSql->getPrefix() . "_config WHERE nome = 'SIS_COLOR' LIMIT 1 ");
		$default_color = "#34a7bd";
	?>
	<style>
		html,body{height:100%;}
		.background_color{ background: 	<?php echo $default_color; ?> !important; }
		.background_padrao_hover:hover{ background: 	<?php echo $default_color; ?> !important; }
		.background_padrao { background: 	inherit; }
		.cor_padrao_hover:hover { color: 		inherit; }
		.cor_padrao { color: 		inherit; }
		.border_padrao { border-color:<?php echo $default_color; ?> !important; }
	</style>

	<link rel="stylesheet" href="/admin/public/css/fonts/font.css">
	<link rel="stylesheet" href="/admin/public/css/base.css">
	<link rel="stylesheet" href="/admin/public/css/style.css">

	<link rel="stylesheet" href="/admin/public/vendor/bootstrap-3.0.3-dist/css/bootstrap.css">
	<link rel="stylesheet" href="/admin/public/vendor/font-awesome-4.7.0/css/font-awesome.css">

	<link rel="stylesheet" href="/admin/public/vendor/jquery-ui-1.10.4/css/smoothness/jquery-ui-1.10.4.custom.css">
	<link rel="stylesheet" href="/admin/public/vendor/jquery-ui-1.10.4/css/jquery-ui-timepicker-addon.css">
	<link rel="stylesheet" href="/admin/public/vendor/bootstrap-toggle/css/bootstrap-toggle.css">

	<script src="/admin/public/vendor/jquery-ui-1.10.4/js/jquery-1.10.2.js" ></script>
	<script src="/admin/public/vendor/jquery-ui-1.10.4/js/jquery-ui-1.10.4.custom.js" ></script>
	<script src="/admin/public/vendor/bootstrap-toggle/js/bootstrap-toggle.js" ></script>
	<script src="/admin/public/vendor/jquery-ui-1.10.4/js/jquery-ui-timepicker-addon.js"></script>
	<script src="/admin/public/vendor/jquery-ui-1.10.4/js/jquery.ui.datepicker-pt-br.js"></script>
	<script src="/admin/public/vendor/bootstrap-3.0.3-dist/js/bootstrap.min.js" ></script>

	<script src="library/editor/ckeditor/ckeditor.js"></script>
	<script src="/admin/public/js/scripts.js"></script>

	<script src="/admin/public/vendor/jscolor/jscolor.js"></script>
	<script src="/admin/public/vendor/masked-input.js"></script>

	<!--[if gte IE 9]> <style type="text/css"> body, button, input[type=button], input[type=reset], input[type=submit] { filter: none; } </style> <![endif]-->

</head>
<body>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel"></h4>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<ul id="todo">
	<li style="" class="topo-list">

		<div id="info-topo" class="wrap">
			<div id="marca-cliente" class="text-center" >
				<figure><img src="/admin/public/images/logomarca_cliente.svg" height="75" ></figure>
			</div>
			<div id="info-usuario-topo">
				<div class="saudacao-and-topo">

					<p>Olá, <span class="user-name"><?php echo $_SESSION['usuario']['nome']; ?>. </span> Bem vindo(a)!</p>

					<div class="dropdown a-box-sair">
						<a id="box-sair" id="dLabel" role="button" data-toggle="dropdown" data-target="#" href="/admin">
							<img src="/admin/public/images/seta-baixo-box-sair.png" height="5" width="7" class="seta_braixo_box_sair">
							<div id="foto-usuario">
								<img src="<?php echo Sis::getGravatar($_SESSION['usuario']['email']); ?>" height="27" width="28">
							</div>
						</a>
						<ul class="dropdown-menu dropdown-menu-sair" role="menu" aria-labelledby="dLabel">
							<div class="arrow-box"></div>
					   	<img src="<?php echo Sis::getGravatar($_SESSION['usuario']['email']); ?>" class="image-maior-user" width="77">
					   	<div class="info-user">
					   		<p><?php echo $_SESSION['usuario']['nome']; ?></p><br />
					   		<span class="mail-user"><?php echo $_SESSION['usuario']['email']; ?></span><br />
					   		<a class="btn btn-primary links_sair" href="?mod=admin&pag=admin&act=edit-meus-dados">Alterar meus dados</a>
					   	</div>
					   	<div class="clearfix"></div>
					   	<span class="image-by">Imagem por: <a href="https://pt.gravatar.com/" target="_blank">Gravatar.</a></span>
					   	<div class="clearfix"></div>
					   	<li role="presentation" class="divider"></li>
					   	<a class="altera_senha links_sair" href="?mod=admin&pag=admin&act=edit-pass">Alterar minha senha</a>
					   	<a href="?pag=logout" class="btn btn-default btn-sair">&nbsp;&nbsp;&nbsp;Sair&nbsp;&nbsp;</a>
					  	</ul>
					</div>

					<div class="dropdown a-box-sair">
						<a class="box-config" id="cLabel" role="button" data-toggle="dropdown" data-target="#" href="">
							<i class="fa fa-gears"></i>
						</a>
						<ul class="dropdown-menu dropdown-menu-config" role="menu" aria-labelledby="cLabel">
							<div class="arrow-box"></div>
							<li>
								<ul id="menu-config-topo">
									<li class="ctn-popover item-menu-topo <?php if($mod=="admin"){ echo("active"); } ?>" data-placement="left" data-content="<span style='margin:0px;padding:0px;font-size:12px;color:#777;'>Administradores do sistema</span>"  >
										<a href="?mod=admin&pag=admin" title="" >
											<i class="fa fa-user"></i>&nbsp;&nbsp;&nbsp;Administradores
										</a>
									</li>
									<li class="ctn-popover item-menu-topo <?php if($mod=="config"){ echo("active"); } ?>" data-placement="left" data-content="<span style='margin:0px;padding:0px;font-size:12px;color:#777;'>Vari&aacute;veis de ambiente e hist&oacute;rico de a&ccedil;&otilde;es</span>" >
										<a href="?mod=config&pag=config" title="" >
											<i class="fa fa-gears"></i>&nbsp;Configurações gerais
										</a>
									</li>
									<?php if($_SESSION['usuario']['nivel'] == 1){ ?>
									<li class="ctn-popover item-menu-topo <?php if($mod=="modulo"){ echo("active"); } ?>" data-placement="left" data-content="<span style='margin:0px;padding:0px;font-size:12px;color:#777;'>M&oacute;dulos de administra&ccedil;&atilde;o</span>" >
										<a href="?mod=modulo&pag=modulo" title="">
											<i class="fa fa-cubes"></i>M&oacute;dulos
										</a>
									</li>
									<?php } ?>
								</ul>
							</li>
					  	</ul>
					</div>

				</div>
			</div>
			<?php
				if(isset($_SESSION['usuario']['lastLogin']))
				{
					if((int)$_SESSION['usuario']['lastLogin']!=0)
					{
						$data = $_SESSION['usuario']['lastLogin'];
						$arrayDateHours = explode(" ", $data);
						$onlyDate 	= $arrayDateHours[0];
						$onlyHours 	= $arrayDateHours[1];
						$arrayDate 	= explode("-", $onlyDate);
						$arrayHours = explode(":", $onlyHours);
						echo '<span class="info-login">Seu último acesso foi em ' . $arrayDate[2] . ' de ' . Date::getMonth($arrayDate[1]) . ' de ' . $arrayDate[0] . ' às ' . $arrayHours[0] . ':' . $arrayHours[1] . '</span>';
					}else{
						echo '<span class="info-login">Este é o seu primeiro login!</span>';
					}
				}else{
					$getLogUser = new HandleSql();
					$tbPrefix 	= $getLogUser->getPrefix();
					$getLog 		= $getLogUser->select("SELECT data FROM ".$tbPrefix."_log Where usuario_idx = '" . $_SESSION['usuario']['id'] . "' Order By data DESC limit 2");
					if(is_array($getLog) && count($getLog) == 2){
						$_SESSION['usuario']['lastLogin'] = $getLog[0]['data'];
					}else{
						$_SESSION['usuario']['lastLogin']=0;
					}
				}
			?>
		</div>
		<div class="clearfix"></div>
		</div>
	</li>
