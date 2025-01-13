<?php
	$mysqlSys 	= new HandleSql();
	$tbPrefix 	= $mysqlSys->getPrefix();
	$m_modulos 	= $mysqlSys->select("SELECT * FROM ".$tbPrefix."_modulo Order By ranking DESC, nome ASC");
	$colorList 	= Sis::getColorMatrix();
?>
<!-- <div class="hide-menu" onclick="javascript: showHideMenu(); ">
	<i class="fa fa-caret-left"></i>
</div> -->
<ul class="main_nav">
	<li class="first <?php if($mod==""){ echo "ativo"; } ?>" >
		<a class="" href="/<?php echo PASTA_DIRECTIN; ?>/">
			<div class="icon-dashboard icon-modulo"><i class="fa fa-tachometer"></i></div>
			<span>Dashboard</span>
		</a>
	</li>
	<?php
		$menuLink = "mod=".$mod."&pag=".$pag;
		if($act != ""){ $menuLink .= "&act=".$act; }
		$iCount = 0;
		foreach($m_modulos as $modulo)
		{
			$m_data = json_decode(($modulo['dados']));
			$arrowLatDir = "";
			if(!is_array($m_data->{'menu'}))
			{
				$urlPrincipal = "href='".$m_data->{'menu'}."'";
			}else{
				$urlPrincipal = "href='javascript:;' onclick=\"javascript:menuTransition('".$modulo['codigo']."'); $('#m_filho_".$modulo['codigo']."').slideToggle();\"";
				$arrowLatDir = '<i class="fa fa-caret-right seta-to-submenu seta-right'.$modulo['codigo'].'"></i>
								<i class="fa fa-caret-down seta-to-submenu seta-to-submenu'.$modulo['codigo'].'" style="display: none;"></i>';
			}

			$ativo_class = "";
			if($mod==$modulo['pasta']){ $ativo_class = "ativo"; }

			if(Sis::checkPerm($modulo['codigo']))
			{
				$iconeMod = "";
				if(property_exists($m_data, 'icone')){
					$iconeMod = $m_data->{'icone'};
				}
				echo '<li class="'.$ativo_class.' list-menu" style="color: #'.$colorList[$iCount]->{'cor'}.';">
							<a '.$urlPrincipal.' title="" class="ctn-popover" data-content="<span style=\'margin:0px;padding:0px;font-size:12px;color:#777;\' >'.($modulo['descricao']).'</span>" >
								<div style="color: #'.$colorList[$iCount]->{'cor'}.'; border-color: #'.$colorList[$iCount]->{'cor'}.';" class="icon-modulo icon-modulo-'.$modulo['codigo'].'"><i class="fa '.$iconeMod.'"></i></div>
								<span class="mod-name">'.($modulo['nome']).'</span>
								'.$arrowLatDir.'
							</a>
						';
				if(is_array($m_data->{'menu'}))
				{
					$s_display='none';
					if($mod==$modulo['pasta']){ $s_display='block'; }
					echo "<ul id='m_filho_".$modulo['codigo']."' class='sub_nav' style='display:".$s_display."' >";
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
								if($meulink[1]==$menuLink){ $ativo_class = "sub_nav_ativo" ; }
							}
							echo ('<li><a class="'.$ativo_class.'" href="'.$s_menu->{'url'}.'" title="" >'.$s_menu->{'link'}.'</a></li>');
						}
					}
					echo "</ul>";
				}
				echo "</li>";
			}
			$iCount++;
		}
	?>
</ul>