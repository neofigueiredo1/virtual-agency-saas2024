<?php
// VERIFICANDO A PERMISSÃO
if (!Sis::checkPerm('10002-2') && !Sis::checkPerm('10002-3'))
{
	Sis::setAlert('Você não tem acesso à este recurso!', 1, '/admin/');
}

//Armazenamento do id do tipo de banner
$tid = isset($_GET['tid']) ? $_GET['tid'] : "";
$bid   = isset($_GET['bid'])?$_GET['bid']:"";
if($tid==0){ Sis::setAlert('Selecione um tipo para carregar a lista de banners!', 1,'?mod=banner&pag=banner&act=tipo-list'); }

//Envio dos dados do formulário
$exe = isset($_POST['exe']) ? $_POST['exe'] : "";
if(!is_numeric($exe)){ $exe=0; }
if($exe==1)
{
    $directIn->theUpdate();
}

function marca_check($iten,$id){
	$iten_array = explode(",",$iten);
	for($i=0;$i<count($iten_array);$i++){
		if($iten_array[$i] == "-".$id."-"){
			echo  "checked";
		}
	}
}

?>
<ol class="breadcrumb">
    <li><a href="?pag=<?php echo $pag; ?>&amp;act=tipo-list">Tipos de Banner</a></li>
    <li>
    		<a href="?pag=<?php echo $pag; ?>&amp;tid=<?php echo $tid; ?>">
			<?php
			$listaTipo = $directIn->tipoListSelected($tid);
            if(isset($listaTipo) && $listaTipo !== false)
                foreach ($listaTipo as $listaTipoArr)
                    echo $listaTipoArr['nome'];
			?>
			</a>
		</li>
		<li>Editar banner</li>
</ol>

<div class="btn-group">
    <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=tipo-list">Tipos de banner</a>
    <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=tipo-add" <?php echo (!Sis::checkPerm('10002-2') && !Sis::checkPerm('10002-4'))?"disabled='disabled'":""; ?> >Criar novo tipo</a>
    <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=list&tid=<?php echo $tid; ?>" >Lista de banners</a>
    <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=add&tid=<?php echo $tid; ?>" >Criar novo banner</a>
</div>

<hr />

<div class="alert alert-danger" id="error-box" style="display:none;"><i class="fa fa-exclamation-triangle"></i>&nbsp;&nbsp;Preencha todos os campos corretamente!</div>

<?php
	if (is_numeric($bid) && $bid!=0) {
		$lista = $directIn->listSelected($bid);
		if(isset($lista) && $lista !== false){
			foreach ($lista as $lista_arr){
				?>

<form action="<?php echo Sis::currPageUrl(); ?>" method="post" class="form_dados" name="form_dados" enctype="multipart/form-data" >

	<input type="hidden" name="exe" value="1" >
	<input type="hidden" name="tid" value="<?php echo $tid; ?>" >
	<input type="hidden" name="bid" value="<?php echo $lista_arr['banner_idx'];?>">
	<input type="hidden" name="arquivo_cad" value="<?php echo $lista_arr['arquivo'];?>">

 <table class="table table_form">
		<tr>
			<th width="20%" class="middle bg">Situação</th>
			<td width="18%">
				<label class="radio-inline"><input type="radio" name="status" value="1" id="status1" <?php if ($lista_arr['status'] == 1) { echo 'checked'; } ?> > On-line</label>
				<label class="radio-inline"><input type="radio" name="status" value="0" id="status0" <?php if ($lista_arr['status'] == 0) { echo 'checked'; } ?> > Off-line</label>
			</td>
			<th width="13%" class="middle bg">Tipo de Banner</th>
				<td width="35%" >
					<?php $listaSubTipo = $directIn->transformTipoBanner("",$tid);
					?>
					<select class="form-control" name="subtipo_banner" data-required="true">
						<option value="">-- Selecione um tipo de banner --</option>
						<?php foreach($listaSubTipo as $subtipo) {?>
							<option value="<?php echo $subtipo['subtipo_idx'];?>" <?php if ($lista_arr['subtipo_banner'] == $subtipo['subtipo_idx']) { echo 'selected'; } ?>><?php echo $subtipo['subtipo_nome'];?></option>
						<?php 
						}?>
					</select>
				</td>
		</tr>
		<tr>
			<th class="middle bg">Nome</th>
			<td colspan="3"><input type="text" class="form-control" name="nome" id="nome" data-required="true" value="<?php echo $lista_arr['nome']; ?>" ></td>
		</tr>
		<tr>
			<th class="middle bg" valign="top">Descrição</th>
			<td colspan="3"><textarea name="descricao" id="descricao" class="ckeditor" style="height:505px;"><?php echo $lista_arr['descricao']; ?></textarea></td>
		</tr>
		<tr>
			<th class="top bg">
				Arquivo<br>
			</th>
			<td colspan="3">
				<div class="fl_left">
					Dimensões de exibição:
					Largura e altura definadas na opção <strong>"Tipo de banner"</strong> entre parênteses
					<br />
					<input type="file" name="arquivo" id="arquivo" title="Trocar arquivo">
					<br />
				</div>
				<div class="clear"></div>
				<img src="<?php echo "/".PASTA_CONTENT."/".$mod."/".$lista_arr['arquivo']; ?>" alt="" style="max-width:100%" />
				<div class="clear"></div>
			</td>
		</tr>
		<tr>
			<th class="top bg" > Páginas </th>
			<td colspan="3" >
				<div style="width:100%; float:left;" >
					<div id="caixa-tipo-1" style="width:100%;" >
						Selecione as páginas onde o banner será visualizado.
						<div style="background:#eee;margin:5px 0;overflow:auto;padding:10px">
							<table class="table" style="margin:0px;" >
								<tr class="no_bg">
									<td>
										<label for="pagina-0">
											<input type="checkbox" name="pagina[]" value="0" id="pagina-0">&nbsp;<span style="font-size:14px;">Todas</span>
										</label>
									</td>
								</tr>
								<?php
								//Lista de páginas
								$lista_pg_m = $directIn->listContentPages();
								if(!empty($lista_pg_m)){
									foreach($lista_pg_m as $lista_arr_m){
										?>
										<tr class="no_bg">
											<td style="padding:0px;" >
												<div class="radio" >
													<label for="pagina-<?php echo $lista_arr_m['pagina_idx']; ?>" >
														<input type="checkbox" name="pagina[]" value="<?php echo $lista_arr_m['pagina_idx']; ?>" id="pagina-<?php echo $lista_arr_m['pagina_idx']; ?>" <?php echo Sis::checked($lista_arr['pagina'],$lista_arr_m['pagina_idx']); ?> />
														<?php echo $lista_arr_m['titulo']; ?>
													</label>
												</div>
											</td>
											<?php
											//Lista de páginas filho
											$lista_pg_f = $directIn->listContentPages($lista_arr_m['pagina_idx']);
											if(!empty($lista_pg_f)){
												foreach($lista_pg_f as $lista_arr_f){
													?>
													<tr class="no_bg">
														<td style="padding:0px;padding-left:25px;" >
															<div class="radio" >
																<label for="pagina-<?php echo $lista_arr_f['pagina_idx']; ?>">
																	<input type="checkbox" name="pagina[]" value="<?php echo $lista_arr_f['pagina_idx'];?>" id="pagina-<?php echo $lista_arr_f['pagina_idx'];?>" <?php echo Sis::checked($lista_arr['pagina'],$lista_arr_f['pagina_idx']); ?> />
																	<?php echo $lista_arr_f['titulo'];?>
																</label>
															</div>
														</td>
													</tr>
													<?php
												}
											}
											?>
										</tr>
										<?php
									}
								}
								?>
							</table>
						</div>
					</div>

				</div>
			</td>
		</tr>
		
		<tr>
			<th class="middle bg" >Link</th>
			<td><input type="text" class="form-control" name="url" id="url" placeholder="#" value="<?php echo $lista_arr['url']; ?>" ></td>
			<th class="middle bg" >Alvo do Link</th>
			<td>
				<select name="alvo" class="form-control" id="alvo">
					<option <?php if ($lista_arr['alvo'] == '_blank') { echo 'selected'; } ?> value="_blank">Nova Janela</option>
					<option <?php if ($lista_arr['alvo'] == '_parent') { echo 'selected'; } ?> value="_parent">Janela Pai</option>
					<option <?php if ($lista_arr['alvo'] == '_self') { echo 'selected'; } ?> value="_self">Mesma Janela</option>
					<option <?php if ($lista_arr['alvo'] == '_top') { echo 'selected'; } ?> value="_top">Janela Superior</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="middle bg">
				<b>Vídeo</b>
				<a class="fa fa-info-circle ctn-popover"
				data-content="<p>Estes são os canais mais populares: youtube.com, vimeo.com, dailymotion.com</p>"
				data-original-title="Informe a url do vídeo" ></a>
			</td>
			<td colspan="3" >
				<input type="text" name="video_url" id="video_url" class="form-control video_url" value="<?php echo $lista_arr['video_url']?>"/>
			</td>
		</tr>
		<tr>
			<th height="52" class="top bg">

				<a class="fa fa-exclamation-circle ctn-popover pull-right"
				style='font-size:20px;text-decoration: none;'
				data-content="Ao indicar um horário de exibição o banner somente é exibido no site no intervalo de horário especificado."
				data-original-title="Horário de exibição" ></a>

			</th>
			<td class="top"><label><input type="checkbox" <?php if ($lista_arr['horario'] == 1) { echo 'checked'; } ?> name="horario" id="horario" value="1" onclick="javascript:checkboxCmdToogle(this,$('.horario'));"> &nbsp;Indicar horário de exibição</label></td>
			<td colspan="2" style="padding:0px;" >

				<div class="horario <?php echo (round($lista_arr['horario']) == 0)?"s-hidden":""; ?>" >

					<table class="table table_form" style="margin:0px;" >
						<tr style="border:0px;" >
							<th width="50%" class="middle bg" >Horário</th>
							<td>
								<select name="horario_ini" class="form-control pull-left" style="width:45%;margin-right:15px;" >
									<option value="0" >Inicio</option>
									<?php
							         for($i=0;$i<24;$i++){
											$selected="";
											if($i==$lista_arr['horario_ini']){ $selected = "selected"; }
											echo("<option value=".$i." ".$selected." >".$i."</option>");
							          }
									?>
								</select>
								     <select name="horario_fim" class="form-control pull-left" style="width:45%;" >
									<option value="0" >Fim</option>
						         <?php
							         for($i=0;$i<24;$i++){
											$selected="";
											if($i==$lista_arr['horario_fim']){ $selected = "selected"; }
											echo("<option value=".$i." ".$selected." >".$i."</option>");
							          }
									?>

								</select>
							</td>
						</tr>
					</table>

				</div>
			</td>
		</tr>
		<tr>
			<th class="top bg">
				<a class="fa fa-exclamation-circle ctn-popover pull-right"
				style='font-size:20px;text-decoration: none;'
				data-content="Opção indicada para banner com exibição temporal, com data para iniciar a exibir e data de sua saída do site."
				data-original-title="Período de publicação" ></a>
			</th>
			<td class="top" >
					<label><input type="checkbox" <?php if ($lista_arr['indica_data'] == 1) { echo 'checked'; } ?> name="indica_data" id="indica_data" onclick="javascript:checkboxCmdToogle(this,$('#validade'));"> &nbsp;Indicar período de publicação.</label>
			</td>
			<td colspan="2" style="padding:0px;" >
				<div id="validade" class="<?php echo (round($lista_arr['indica_data']) == 0)?"s-hidden":""; ?>" >

					<table class="table table_form" style="margin:0px;" >
						<tr style="border:0px;" >
							<th width="50%" class="middle bg" >Data de publicação</th>
							<td><input type="text" class="datepicker form-control" name="data_publicacao" id="data_publicacao" value="<?php echo date('d/m/Y', strtotime(Date::fromMysql($lista_arr['data_publicacao'])));?>" /></td>
						</tr>
						<tr>
							<th width="50%" class="middle bg" >Data de expiração</th>
							<td><input type="text" class="datepicker form-control" name="data_expiracao" id="data_expiracao" value="<?php echo date('d/m/Y', strtotime(Date::fromMysql($lista_arr['data_expiracao']))); ?>" /></td>
						</tr>
					</table>

				</div>
			</td>
		</tr>
		<tr>
			<td colspan="4" class="right" >

				<input type="button" value="Cancelar" class="btn btn-default" onclick="javascript:if(confirm('Ao cancelar, todos os dados inseridos serão descartados.\nDeseja executar este ação ?')){ window.location.href='?pag=<?php echo $pag; ?>&amp;act=list&amp;tid=<?php echo $tid; ?>'}else{ return false; };">
     			<input type="button" value="Enviar" class="btn btn-primary" data-loading-text="Carregando..."  onclick="javascript:checkFormRequire(document.form_dados,'#error-box');">

			</td>
		</tr>
	</table>
</form>


<?php
			}
		}else{
			echo "
			 	<div class='alert alert-warning'>
                <i class='fa fa-exclamation-triangle'></i>&nbsp;&nbsp;
                Registro inexistente.
            </div>";
			Sis::redirect("?mod=".$mod."&pag=".$pag."&tid=".$tid,3);
		}
	} else {
		echo "
			 	<div class='alert alert-warning'>
                <i class='fa fa-exclamation-triangle'></i>&nbsp;&nbsp;
                Registro inexistente.
            </div>";
		Sis::redirect("?mod=".$mod."&pag=".$pag."&tid=".$tid,3);
	}

die();

?>








				<form action="<?php echo sis::curr_page_url(); ?>" method="post" id='form-cad-banner' enctype="multipart/form-data">



					<table>
						<tr>
							<td class="first-child">Status</td>
							<td width="40%">
								<label for="status1"><input type="radio" name="status" value="1" id="status1"  >&nbsp;On-line</label> &nbsp;&nbsp;
								<label for="status0"><input type="radio" name="status" value="0" id="status0" <?php if ($lista_arr['status'] == 0) { echo 'checked'; } ?> >&nbsp;Off-line</label> &nbsp;
							</td>
							<td class="first-child">Formato</td>
							<td >
								<label for="formato-1"><input type="radio" name="formato" value="1" id="formato-1" <?php if ($lista_arr['formato'] == 1) { echo 'checked'; } ?> >&nbsp;Imagem</label> &nbsp;&nbsp;
								<label for="formato-2"><input type="radio" name="formato" value="2" id="formato-2" <?php if ($lista_arr['formato'] == 2) { echo 'checked'; } ?> >&nbsp;Adobe Flash</label> &nbsp;
							</td>
						</tr>

                        <tr>
                            <td class="first-child">Alinhamento</td>
                            <td width="40%">
                                <label><input type="radio" name="alinhamento" value="0" id="alinhamento" <?php if ($lista_arr['alinhamento'] == 0) { echo 'checked'; } ?>>&nbsp;Esquerda</label> &nbsp;&nbsp;
                                <label><input type="radio" name="alinhamento" value="1" id="alinhamento" <?php if ($lista_arr['alinhamento'] == 1) { echo 'checked'; } ?>>&nbsp;Centralizado</label> &nbsp;
                                <label><input type="radio" name="alinhamento" value="2" id="alinhamento" <?php if ($lista_arr['alinhamento'] == 2) { echo 'checked'; } ?>>&nbsp;Direita</label> &nbsp;

                            </td>
                            <td class="first-child">&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>

						<tr>
							<td class="first-child"><label for="nome">Nome</label></td>
							<td colspan="3"><input type="text" name="nome" id="nome" value="<?php echo $lista_arr['nome']; ?>"></td>
						</tr>

						<tr>
							<td class="first-child" valign="top"><label for="descricao"><p>Descrição</p></label></td>
							<td colspan="3"><textarea name="descricao" id="descricao"><?php echo $lista_arr['descricao']; ?></textarea></td>
						</tr>

						<tr>
							<td class="first-child" valign="top">
								<label for="arquivo"><p>Arquivo</p></label>
								Largura:&nbsp;<?=$listaTipo[0]['largura']?>px <br>
								Altura:&nbsp;&nbsp;&nbsp;&nbsp;<?=$listaTipo[0]['altura']?>px
							</td>
							<td colspan="3">

							</td>
						</tr>

						<tr>
							<td colspan="4" class="first-child">

								<div style="width:100%; float:left; position:relative; height:216px;">

									<div id="caixa-tipo-1" style="width:100%; position:absolute;">
										<p>Selecione as páginas onde o banner será visualizado</p>
										<div style="height:165px;background:#ffffff;margin:5px 0;overflow:auto;padding:3px 10px">
											<table>
												<tr class="no_bg">
													<td>
														<label for="pagina-0">
															<input type="checkbox" name="pagina[]" value="0" id="pagina-0" <?php echo sis::checked($lista_arr['pagina'],0); ?> >&nbsp;<span style="font-size:14px;">Todas</span>
														</label>
													</td>
												</tr>
												<?php
												//Lista de páginas
												$lista_pg_m = $directIn->seleciona("Select titulo,pagina_idx From apl_conteudo_pagina Where status=1 And pagina_mae=0 Order By titulo");

												if(!empty($lista_pg_m)){
													foreach($lista_pg_m as $lista_arr_m){
														?>
														<tr class="no_bg">
															<td>
																<label for="pagina-<?php echo $lista_arr_m['pagina_idx']; ?>">
																	<input type="checkbox" name="pagina[]" value="<?php echo $lista_arr_m['pagina_idx']; ?>" id="pagina-<?php echo $lista_arr_m['pagina_idx']; ?>" <?php echo sis::checked($lista_arr['pagina'],$lista_arr_m['pagina_idx']); ?> >&nbsp;<span style="font-size:14px;"><?php echo $lista_arr_m['titulo']; ?></span>
																</label>
															</td>

															<?php
														//Lista de páginas filho
															$lista_pg_f = $directIn->seleciona("Select titulo,pagina_idx From apl_conteudo_pagina Where status=1 And pagina_mae=" . $lista_arr_m['pagina_idx'] . " Order By indice DESC");

															if(!empty($lista_pg_f)){
																foreach($lista_pg_f as $lista_arr_f){
																	?>
																	<tr class="no_bg">
																		<td style="padding-left:20px;">
																			<label for="pagina-<?php echo $lista_arr_f['pagina_idx']; ?>">
																				<input type="checkbox" name="pagina[]" value="<?php echo $lista_arr_f['pagina_idx']; ?>" id="pagina-<?php echo $lista_arr_f['pagina_idx']; ?>"  <?php echo sis::checked($lista_arr['pagina'],$lista_arr_f['pagina_idx']); ?> >&nbsp;<span style="font-size:14px;"><?php echo $lista_arr_f['titulo']; ?></span>
																			</label>
																		</td>
																	</tr>
																	<?php
																}
															}
															?>

														</tr>
														<?php
													}
												}
												?>
											</table>
										</div>
									</div>

								</div>
							</td>
						</tr>

						<tr>
							<td class="first-child" ><label for="url"><p>Link</p></label></td>
							<td><input type="text" name="url" id="url" value="<?php echo $lista_arr['url']; ?>"></td>
							<td class="first-child" ><label for="alvo"><p>Alvo do Link</p></label></td>
							<td>
								<select name="alvo" id="alvo">
									<option value="_blank"  <?php if ($lista_arr['alvo'] == '_blank') { echo 'selected'; } ?> >Nova Janela (_blank)</option>
									<option value="_parent" <?php if ($lista_arr['alvo'] == '_parent') { echo 'selected'; } ?> >Janela Pai (_parent)</option>
									<option value="_self"   <?php if ($lista_arr['alvo'] == '_self') { echo 'selected'; } ?> >Mesma Janela (_self)</option>
									<option value="_top"    <?php if ($lista_arr['alvo'] == '_top') { echo 'selected'; } ?> >Janela Superior (_top)</option>
								</select>
							</td>
						</tr>
						<?php if ($lista_arr['horario'] <> '0') {  ?>
                        	<style type="text/css">
								.horario{ display:block; }
							</style>
                  <?php } ?>
						<tr>
							<td class="first-child"><img src="library/img/exclamacao.png" alt="" class="fl_right" ></td>
							<td><label for="indica_horario"><input type="checkbox" name="horario" value="1" id="horario" onclick="javascript:checkboxCmdToogle(this,$('.horario'));" <?php if ($lista_arr['horario'] <> '0') { echo 'checked'; } ?> > &nbsp;Indicar horário de exibição</label></td>
							<td><div class="horario" hidden >Horário</div></td>
							<td>
                                <select name="horario_ini" hidden class="horario">
                                    <option value="0" >Inicio</option>
                                    <?php
                                    for($i=0;$i<24;$i++){
										$selected="";
										if($i==$lista_arr['horario_ini']){ $selected = "selected"; }
										echo("<option value=".$i." ".$selected." >".$i."</option>");
									}
                                    ?>
                                </select>
                                <select name="horario_fim" hidden class="horario">
                                    <option value="0" >Fim</option>
                                    <?php
                                    for($i=0;$i<24;$i++){
										$selected="";
										if($i==$lista_arr['horario_fim']){ $selected = "selected"; }
										echo("<option value=".$i." ".$selected." >".$i."</option>");
									 }
                                    ?>

                                </select>

							</td>
						</tr>

						<tr>
							<td class="first-child" valign="top"><img src="library/img/exclamacao.png" alt="" class="fl_right" style="margin-top:10px"></td>
							<td colspan="3"><label for="indica_data" style="height: 25px;display:block;margin:8px 0 0 0"><input type="checkbox" name="indica_data" id="indica_data" value="1" onclick="javascript:checkboxCmdToogle(this,$('#validade'));" <?php if ($lista_arr['indica_data'] == '1') { echo 'checked'; } ?> > &nbsp;Indicar data de publicação e expiração</label>
								<div class="clearfix"></div>
								<div id="validade" <?php if ($lista_arr['indica_data'] <> '1') { echo 'hidden'; } ?> >
									<table>
										<tr>
											<td><label for=""><p>Publicação</p></label></td>
											<td><label for="data_publicacao"><input type="text" name="data_publicacao" id="data_publicacao" value="<?php echo data::from_mysql($lista_arr['data_publicacao']);?>" class="datepicker"></label></td>
											<td>Expiração</td>
											<td><label for="data_expiracao"><input type="text" name="data_expiracao" id="data_expiracao" value="<?php echo data::from_mysql($lista_arr['data_expiracao']); ?>" class="datepicker"></label></td>
										</tr>
									</table>
								</div>
							</td>
						</tr>

						<tr>
							<td colspan="4">
								<div class="btn fl_left" style="margin-right:10px;">
									<input type="submit" value="Enviar" data-loading-text="Carregando..." />
								</div>
								<div class="btn fl_left">
									<input type="button" value="Cancelar" onclick="if(confirm('Você deseja realmente cancelar?\n Os dados não salvos serão perdidos.')){ window.location.href='?pag=<?php echo $pag; ?>&amp;act=list&amp;t_id=<?php echo $t_id; ?>'}else{ return false;}" />
								</div>
							</td>
						</tr>
					</table>
				</form>





