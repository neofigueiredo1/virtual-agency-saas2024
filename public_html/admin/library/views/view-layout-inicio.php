<?php
	include_once('modulos/config/config-model.php');
	include_once('modulos/config/config-control.php');
	$m_config = new config();
?>

<?php $cMatrix = Sis::getColorMatrix(); ?>

<h1 class='titulo_dash' ><i class="fa fa-tachometer" ></i> &nbsp;Dashboard</h1>

<?php
	$mysqlSys = new HandleSql;
	$tbPrefix = $mysqlSys->getPrefix();
	$m_modulos = $mysqlSys->select("SELECT * FROM ".$tbPrefix."_modulo Order By ranking DESC,nome ASC");
?>

<ul class='table dashboard' >
	<li class='td center' >

	<h3 class='titulo_dash_down' ><i class="fa fa-flash" ></i> &nbsp;Ações rápidas</h3>

	<?php
		$zIndex = 800;
		$menuLink = "mod=".$mod."&pag=".$pag;
		if($act != ""){ $menuLink .= "&act=".$act; }

		echo "<ul id='gradesort' class='gradesort' >";

		$mcount = 0;
		foreach($m_modulos as $modulo)
		{
			$m_data = json_decode(($modulo['dados']));

			if(!is_array($m_data->{'menu'}))
			{
				$urlPrincipal = "href='".$m_data->{'menu'}."'";
			}else{
				$urlPrincipal = "href='javascript:;' onclick=\"javascript:$('#s_filho_".$modulo['codigo']."').slideToggle();\"";
			}
			$moduloNome = $m_data->{'pasta'};

			if(Sis::checkPerm($modulo['codigo']))
			{
				$txtDashInfo="";
				if(file_exists('modulos/' . $moduloNome . '/' . $moduloNome . '-model.php')){
					require_once "modulos/" . $moduloNome . "/" . $moduloNome . "-model.php";
				}
				if(file_exists('modulos/' . $moduloNome . '/' . $moduloNome . '-control.php')){
					require_once "modulos/" . $moduloNome . "/" . $moduloNome . "-control.php";
					$classMod = str_replace("-","_",$moduloNome);
					$objNome  = "m_" . str_replace("-","_",$moduloNome);
					$$objNome = new $classMod();
					if(method_exists($$objNome,'dashInfo')){
						$txtDashInfo = $$objNome->dashInfo();
					}
				}

				$m_icone = (property_exists($m_data, 'icone'))?$m_icone = $m_data->{'icone'}:"fa-cubes";

				echo '<li id="'.$modulo['codigo'].'" class="sortitem" style="z-index:'.$zIndex.'" >
						<div class="dash_item" >
						<a '.$urlPrincipal.' title="" class="background_padrao_hover ctn-popover" data-placement="right" data-content="<span style=\'margin:0px;padding:0px;font-size:12px;color:#777;\' >'.($modulo['descricao']).'</span>" >
							<ul class="dash-mod table" >
								<li class="mod-icone td" style="background-color:#'.$cMatrix[$mcount]->{'cor'}.';" ><i class="fa '.$m_icone.'" ></i></li>
								<li class="mod-dados td">
									<span class="titulo" style="color:#'.$cMatrix[$mcount]->{'corb'}.';" >'.($modulo['nome']).'</span><br>
									<span class="descricao" style="color:#'.$cMatrix[$mcount]->{'cor'}.';"  >
										'.$txtDashInfo.'
									</span>
								</li>
							</ul>
						</a>';


				if(is_array($m_data->{'menu'}))
				{
					$s_display='none';
					echo "<ul id='s_filho_".$modulo['codigo']."' class='sub_nav' style='border-color:#".$cMatrix[$mcount]->{'cor'}.";' >";
					foreach($m_data->{'menu'} as $s_menu)
					{
						$_exibe_link=true;
						if(property_exists($s_menu, 'permissao'))
						{
							if(trim($s_menu->{'permissao'})!=""){
								$_exibe_link = Sis::checkPerm($modulo['codigo'].'-2'); //Padrão para controle geral do módulo
								if(!$_exibe_link){
									$_exibe_link = Sis::checkPerm($modulo['codigo'].'-'.$s_menu->{'permissao'});
								}
							}
						}
						if($_exibe_link)
						{
							$ativo_class = "";
							$meulink = explode("?",$s_menu->{'url'});
							if(count($meulink)>1){
								if($meulink[1]==$menuLink){ $ativo_class = "sub_nav_ativo background_color" ; }
							}
							echo ('<li><a href="'.$s_menu->{'url'}.'" title="" >'.$s_menu->{'link'}.'</a></li>');
						}
					}
					echo "</ul>";
				}
				echo "</div></li>";
			}
			$mcount++;
			$zIndex--;
		}

		echo "</ul>";
	?>

	</li>
	<li class='td sidebar' >

		<h3 class='titulo_dash_down' ><i class="fa fa-cloud" ></i> &nbsp;Cota de armazenamento</h3>
		<?php
		$clicota = (is_numeric(Sis::config('CLI_COTA')))?	(round(Sis::config('CLI_COTA'))>0)?Sis::config('CLI_COTA'):0 :0;
		$percentual_cota = round((100/(($clicota*1024)*1024))*$_SESSION['sis_used_quota']);
		$cor_progress_bar = "progress-bar-success";
		if($percentual_cota>75){ $cor_progress_bar = "progress-bar-warning"; }
		if($percentual_cota>90){ $cor_progress_bar = "progress-bar-danger"; }
		?>
		<div class="panel">
			<div class="panel-body panel_user_quota" >
				<span class="info" ><?php echo Sis::formatBytes($_SESSION['sis_used_quota']); ?> de <?php echo Sis::formatBytes(($clicota*1024)*1024); ?></span>
				<div class="progress">
					<div class="progress-bar <?php echo $cor_progress_bar; ?>" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width:0%;">
						0%
					</div>
				</div>
			</div>
		</div>
		<script>
		$(document).ready(function(){
			$(".progress .progress-bar").animate({width:'<?php echo $percentual_cota; ?>%'},{
					step: function( now, fx ) {
						$(".progress .progress-bar").html(Math.round(now)+"%");
					},
					duration: 2000,
					specialEasing: {
						width: "easeOutBounce"
					}
				});
		});
		</script>

		<h3 class='titulo_dash_down' ><i class="fa fa-flag"></i> &nbsp;Ultimas Atividades</h3>

		<div class="panel">
			<div class="panel-body">
			<?php
				$ultimas_atividades = $m_config->listLogLasts(5);
				if (is_array($ultimas_atividades)&&count($ultimas_atividades)>0){
					foreach ($ultimas_atividades as $key => $atividade){

						$txtAcao = "";
						$icoAcao = "fa-bolt";
						switch($atividade['acao']){
							case "INSERT" :
								$txtAcao = "inserido(a)";
								$icoAcao = "fa-file-o";
							break;
							case "UPDATE" :
								$txtAcao = "editado(a)";
								$icoAcao = "fa-pencil";
							break;
							case "DELETE" :
								$txtAcao = "excluído(a)";
								$icoAcao = "fa-trash-o";
							break;
						}
						$data_atv = "";
						if(date("dmY", strtotime($atividade['data'])) == date("dmY"))
						{
							$data_atv = "Hoje às ".date("H:i:s",strtotime($atividade['data']));
						}elseif( date("dmY",strtotime($atividade['data'])) == date(strtotime(date("dmY").' -1 day')) ){
							$data_atv = "Ontem às ".date("H:i:s",strtotime($atividade['data']));
						}elseif( date("dmY",strtotime($atividade['data'])) == date(strtotime(date("dmY").' -2 day')) ){
							$data_atv = "Anteontem às ".date("H:i:s",strtotime($atividade['data']));
						}else{
							$data_atv = "Em: ".date("d/m/Y \à\s H:i:s",strtotime($atividade['data']));
						}

						echo '
							<div class="atividade" >
								<div class="icone" ><i class="fa '.$icoAcao.'"></i></div>
								<div class="dados" >
									'.$atividade['modulo_area'].' <span class="destaca" >'.$atividade['registro_nome'].'</span>  '.$txtAcao.'<br>
									por <span class="destaca" >'.$atividade['nome'].'</span><br>
									<small>'.$data_atv.'</small>
								</div>
								<div class="clear-fix" ></div>
							</div>
							<div class="clear-fix" ></div>
						';
					}
				}
			?>
			</div>
		</div>

		<a href="/admin/?mod=config&pag=config&act=hist-list" class="pull-right" style='display:block;font-size: 13px; color:#888888;'>ver todas as atividades</a>

	</li>
</ul>


<script>
  $(function() {
    $("#gradesort").sortable({
    	stop: function( event, ui ){
    		var zIndex = 1000;
    		$("#gradesort .sortitem").each(function( index ) {
			  $(this).css("z-index",zIndex.toString());
			  zIndex--;
			});
			modulo_salva_ordem();
    	}
    });
    $("#gradesort").disableSelection();
  });
  </script>
