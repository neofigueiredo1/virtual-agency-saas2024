<?php require_once "config.php"; ?>

<?php require_once "library/views/view-layout-topo.php" ?>

<li style="height: 100%;">
	<div id="content-geral" class="wrap" style="background: <?php echo (count($_GET) > 1) ? "#ffffff" : "#f7f7f7"; ?>;">
		<ul class='table content_geral_table' >
			<li class='td side_menu' >
				<?php require_once "library/views/view-layout-menu.php" ?>
			</li>
			<li class='td side_body' >
				<div id="content-real">
					<br />
					<?php
		        		/**
		        		 * Trata as mensagens das acoes executadas, e as escreve com seu ícone correspondente
		        		 * Após o erro ser exibido por 3 segundos, ele é ocultado, e as sessões de mensagem são destruidas.
						 * ---------------------------------------
						 * Classes utlizadas = block, info, success, error
		        		 */
						if(isset($_SESSION['sis_mens']) && $_SESSION['sis_mens']!="")
						{
							$tipo="warning";
							switch($_SESSION['sis_mens_tipo'])
							{
								case 1 : $tipo="warning"; 	$icone = "<i class='fa fa-exclamation-triangle'></i>&nbsp;&nbsp;";
								break;
								case 2 : $tipo="info"; 		$icone = "<i class='fa fa-info-circle'></i>&nbsp;&nbsp;";
								break;
								case 3 : $tipo="success"; 	$icone = "<i class='fa fa-check-circle-o'></i>&nbsp;&nbsp;";
								break;
								case 4 : $tipo="danger"; 	$icone = "<i class='fa fa-ban'></i>&nbsp;&nbsp;";
								break;
							}
						?>

							<div id="alert-bts" class="alert alert-<?php echo $tipo; ?> fade in">
					        	<?php echo $icone; ?>
					        	<?php echo $_SESSION['sis_mens']; ?>
							</div>
							<script type="text/javascript">
				            setTimeout(function(){ $('#alert-bts').slideUp('slow'); },3000);
				       	</script>

				      <?php
							unset($_SESSION['sis_mens']);
							unset($_SESSION['sis_mens_tipo']);
						}

						/**
						 * verifica se algum módulo foi passado.
						 */
						if($mod!=''){


							/**
							 * Verifica se o controlador do módulo existe
							 */
							if(file_exists('modulos/'.$mod.'/'.$pag.'-control.php')){

								/**
								 * Verifica se o model do módulo existe e inclui o mesmo
								 */
								//var_dump(file_exists('modulos/'.$mod.'/'.$pag.'-model.php'));
								if(file_exists('modulos'.DS.$mod.DS.$pag.'-model.php')){
									require_once('modulos'.DS.$mod.DS.$pag.'-model.php');
								}

								/**
								 * Após isso, inclui o  arquivo controlador
								 */
								if(file_exists('modulos'.DS.$mod.DS.$pag.'-control.php')){
									require_once('modulos'.DS.$mod.DS.$pag.'-control.php');
								}
								/**
								 * Instancia a classe controladora
								 */

								try {
									$directIn = new $control();
								} catch (Exception $e) {
									die($e->getMessage());
								}
								
								/**
								* Verifica se os arquivos de estilo e scripts do módulo existem, e os incluem
								*/
								if(file_exists('modulos/'.$mod.'/'.$mod.'-style.css')){
									echo '<link rel="stylesheet" href="modulos/'.$mod.'/'.$mod.'-style.css" >';
								}
								if(file_exists('modulos/'.$mod.'/'.$mod.'-scripts.js')){
									echo '<script type="text/javascript" src="modulos/'.$mod.'/'.$mod.'-scripts.js" ></script>';
								}
								
								/**
								 * Caso alguma ação tenha sido passada...
								 */
								if ($act != "") {
									/**
									 * Inclui o arquivo de ação (se ele existir).
									 * Senão, ele exibe uma mensagem informado, e redireciona o usuário
									 */
									if(file_exists('modulos/'.$mod.'/'.$pag.'-'.$act.'.php')){
										require_once('modulos/'.$mod.'/'.$pag.'-'.$act.'.php');
									}else{
										echo "<div class='alert alert-warning' ><i class='fa fa-warning'></i> Arquivo de ação não encontrado.</div>";
										Sis::redirect("?mod=".$mod."&pag=".$pag, 2);
									}
								}else{
									/**
									 * Se nenhumaa ação foi passada, incli o arquivos de listagem da página (modulo/pagina-list.php).
									 * Senão, ele exibe uma mensagem informado, e redireciona o usuário
									 */

									if(file_exists('modulos/'.$mod.'/'.$pag.'-list.php')){
										require_once('modulos/'.$mod.'/'.$pag.'-list.php');
									}else{
										echo "<div class='alert alert-warning' ><i class='fa fa-warning'></i> Arquivo de listagem não encontrado.</div>";
										Sis::redirect("/" . PASTA_DIRECTIN . "/",2);
									}
								}
								
							}else{
								/**
								 * Caso o arquivo Controlador não exista, mostra a mensagem e redireciona o usuário para a Dashboard
								 */
								echo "<div class='alert alert-warning' ><i class='fa fa-warning'></i> O recurso que voc&ecirc; procura n&atilde;o foi encontrado.</div>";
								Sis::redirect("/" . PASTA_DIRECTIN . "/",2);
							}
						}else{
							/**
							 * Se não foi passado nenhum módulo via GET ($mod), ele chama a Dashboard.
							 */
							require_once('library/views/view-layout-inicio.php');
						}
					?>

				</div>
			</li>
		</ul>

	</div>
</li>
<div class="clear"></div>
<?php require_once "library/views/view-layout-rodape.php" ?>