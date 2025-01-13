<?php
	Auth::checkPermission("1");

	$exe = isset($_POST['exe']) ? $_POST['exe'] : 0;
	$exe = isset($_GET['exe']) ? $_GET['exe'] : 0;
	if(!is_numeric($exe)){ $exe=0; }

	//Instala o módulo
	if ($exe==1) { $directIn->inserir(); }
	//Remove o módulo
	if ($exe==2) { $directIn->remover(); }
	//Remove o arquivo de backup
	if ($exe==3) { $directIn->deleteBackup(); }
	//Restaura o arquivo de backup
	if ($exe==4) { $directIn->restoreBackup(); }
?>

<ul class="breadcrumb" >
	<li>M&oacute;dulos</li>
</ul>

<div class="panel panel-default">
   <div class="panel-heading">Lista de módulos</div>

	<?php
		$lista = $directIn->listaTodos();
		if(isset($lista) && $lista !== false){
			echo '
			<table class="table table-hover table-striped table_list">
					<thead>
						<tr>
							<th width="0%" >&nbsp;</th>
							<th width="100%" >Módulo</th>
							<th width="10%" style="text-align:center;">Versão</th>
							<th width="10%" style="text-align:center;">Ações</th>
						</tr>
					</thead>
					<tbody>
					';
			$i=0;
			foreach ($lista as $lista_arr)
			{
				$i++;
				$lista_json = json_decode($lista_arr);
				$versao="<span style='color:#ff0000;'>n/f</span>";
				$descricao="";
				$codigo=0;
				$dados_modulo=0;
				$aviso=false;
				$cor="333";
				$mData="";
				$ckModulo=NULL;
				if(is_object($lista_json))
				{
					if($lista_json->{'modulo'}!="admin" && $lista_json->{'modulo'}!="modulo" && $lista_json->{'modulo'}!="config")
					{
							$icone = "<i class='fa fa-check-circle-o' title='M&oacute;dulo n&atilde;o instalado ainda!' style=' font-size:32px; text-align:center;margin-top:5px;' ></i>";

							if(count($lista_json->{'data'})>=1)
							{
									$codigo = $lista_json->{'data'}[0]->{'codigo'};
									$versao = $lista_json->{'data'}[0]->{'versao'};
									$descricao = $lista_json->{'data'}[0]->{'descricao'};

									$mData = json_encode($lista_json->{'data'}[0]);

							}else{ $aviso=true; $cor="ff0000"; }

							if($aviso)
							{
								$icone = "<i class='fa fa-exclamation-circle' title='Arquivo de informa&ccedil;&atilde;o n&atilde;o encontrado!' style='font-size:32px;margin-top:5px;width:35px;text-align:center;' ></i>";
							}else{
								$ckModulo = $directIn->checkInstall($codigo);
								if($ckModulo){
									$icone = "<i class='fa fa-check-circle' title='M&oacute;dulo instalado!'style='font-size:32px;width:35px;text-align:center;margin-top:5px;color:#22b91b' ></i>";
								}
							}

							$backupData = "";
							if(!$aviso)
							{
							if($ckModulo)
							{
								$listaBackups = $directIn->listaModuleBackups($lista_json->{'data'}[0]->{'pasta'});
								if(isset($listaBackups) && $listaBackups !== false){
									$backupData.= '
											<fieldset class="module_backup" >
											<legend class="a_legenda">Backups</legend>
											<div class="list-backups" style="display:none;" ><table class="table" style="margin:0px;font-size:12px;" >
											<thead>
												<tr>
													<th width="100%" >Nome</th>
													<th width="0%" nowrap >Tamanho (bytes)</th>
													<th width="0%" >Data</th>
													<th width="0%" >Ação</th>
												</tr>
											</thead>
											<tbody class="table-list-striped" >
											';
									foreach (array_reverse($listaBackups) as $b_item)
									{
										$b_item_json = json_decode($b_item);
											$backupData.= '
												<tr>
													<td>'.$b_item_json->file.'</td>
													<td>'.$b_item_json->size.'</td>
													<td nowrap >'.$b_item_json->date.'</td>
													<td width="0%" nowrap >
														<a href="#" onclick="javascript:confirmAction(\'Esta ação não poderá ser desfeita, deseja continuar?\',\'/admin/?mod=modulo&pag=modulo&exe=4&mp='.$lista_json->{'data'}[0]->{'pasta'}.'&mcd='.$lista_json->{'data'}[0]->{'codigo'}.'&fname='.$b_item_json->file.'\');" title="Restaurar este backup." class="a_tooltip" ><i class="fa fa-hdd-o" title ></i></a>
														&nbsp;
														<a href="#" onclick="javascript:confirmAction(\'Esta ação não poderá ser desfeita, deseja continuar?\',\'/admin/?mod=modulo&pag=modulo&exe=3&mp='.$lista_json->{'data'}[0]->{'pasta'}.'&mcd='.$lista_json->{'data'}[0]->{'codigo'}.'&fname='.$b_item_json->file.'\');" title="Excluir este backup." class="a_tooltip" ><i class="fa fa-times-circle"></i></a>
													</td>
												</tr>
											';
									}
									$backupData.= '</tbody></table></div></fieldset>';
								}
						}}

						echo "
							<tr>
									<td style='color:#".$cor."; text-transform:capitalize; width:auto;' >".$icone."</td>
									<td style='width:100%;color:#".$cor."; text-transform:capitalize;padding:10px; ' >".((count($lista_json->{'data'})>0)?$lista_json->{'data'}[0]->{'nome'}:$lista_json->{'modulo'})."<br><span style='color:#777;font-size:12px;text-transform:none;'>".$descricao."</span>".$backupData."</td>
									<td nowrap style='text-align:center'>".$versao."</td>
									<td nowrap style='text-align:center'>
									";
							if(!$aviso)
							{
								if($ckModulo)
								{
										echo("<a class='a_tooltip' data-placement='top' title='Remover m&oacute;dulo' href='?mod=modulo&pag=modulo&exe=2&mid=".$codigo."' ><i class='fa fa-times-circle' style='width:35px;text-align:center;color:#ff0000;'></i></a>");
								}else{
										echo("<a class='a_tooltip' data-placement='top' title='Instalar m&oacute;dulo' href='#' onclick='javascript:document.modulo_".$i.".submit();' ><i class='fa fa-download' style='width:35px;text-align:center;color:#000;' ></i></a>");
								}
							}
							echo "<form name='modulo_".$i."' action='?mod=modulo&pag=modulo&exe=1' method='post' ><input type='hidden' name='m_data' value='".text::ReformatCSVString($mData)."' /></form></td>
							</tr>
							";
					}
				}
			}
			echo '</tbody>
			</table>';
		} else {
			echo "Nenhum registro encontrado.";
		}
	?>
</div>