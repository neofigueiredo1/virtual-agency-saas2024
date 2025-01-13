<?php
	$enviar = isset($_POST['enviar']) && (is_numeric($_POST['enviar']) && $_POST['enviar'] != "") ? $_POST['enviar'] : 0;
	if ($enviar != "" && $enviar == 1) {
		$directIn->theUpdate();
	}

	// VERIFICANDO A PERMISSÃO
  	if (!Sis::checkPerm($directIn->MODULO_CODIGO.'-2') && !Sis::checkPerm($directIn->MODULO_CODIGO.'-4'))
  	{
  		Sis::setAlert('<i class="fa fa-warning"></i> Você não tem acesso à este recurso!', 1, '/admin/');
  	}
?>

<ul class="breadcrumb" >
	<li><a href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>">Menus</a></li>
	<li class="active" >Editar menu</li>
</ul>

<div class="btn-group">
    <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=pagina">Lista de páginas</a>
    <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=pagina&act=add">Criar nova página</a>
    <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=menu">Menus</a>
    <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=menu&act=add">Criar novo menu</a>
</div>

<hr />

<div class="alert alert-danger" id="error-box" style="display:none;"><i class="fa fa-exclamation-triangle"></i>&nbsp;&nbsp;Preencha todos os campos corretamente!</div>

<?php
	$mn_id = is_numeric($_GET['mn_id']) && $_GET['mn_id'] != "" ? $_GET['mn_id'] : 0;
	$lista = $directIn->listSelected($mn_id);
	if(is_array($lista) && count($lista) > 0){
		foreach ($lista as $arrayList){

			$lista_pgs = $directIn->getPagesOnMenuM($mn_id);
			$paginas = "";
			if(isset($lista_pgs) && $lista_pgs !== false){
				foreach ($lista_pgs as $lista_pgs_arr){
					$paginas.="!".$lista_pgs_arr['pagina_idx']."!";
				}
			}
			?>
			<form action="<?php echo Sis::currPageUrl(); ?>" method="post" class="form_dados" name="form_dados" id="form-pagina">
				<input type="hidden" name="enviar" value="1">
				<input type="hidden" name="mn_id" value="<?php echo $mn_id; ?>">
				<input type="hidden" name="paginas" value="<?=str_replace("!","",str_replace("!!",",",$paginas))?>" id="paginas" >
            	<input type="hidden" name="paginas_old" value="<?=$paginas?>" id="paginas_old" >
            	
				<table class="table table_form">
					<tr>
						<th width="20%" class="middle bg">Situação</th>
						<td class="middle">
							<label class="radio-inline">
							  <input type="radio" id="" name="status" value="1" <?php if ($arrayList['status']==1) {echo "checked=checked";} ?>>Ativo
							</label>
							<label class="radio-inline">
							  <input type="radio" id="" name="status" value="0" <?php if ($arrayList['status']==0) {echo "checked=checked";} ?>>Inativo
							</label>
						</td>
					</tr>
					<tr>
						<th class="middle bg">Nome*</th>
						<td>
							<input type="text" name="nome" id="nome" class="form-control" data-required="true" value="<?php echo $arrayList['nome'] ?>">
						</td>
					</tr>
					<tr>
						<th class="top bg">Descrição</th>
						<td>
							<textarea name="descricao" id="descricao" class="form-control" rows="3"><?php echo $arrayList['descricao'] ?></textarea>
						</td>
					</tr>
					<tr>
						<th class="top bg">Páginas</th>
						<td>
							<div class="paginas_list">

								<table width="100%">
									<tr style="border: 0px !important;">
										<td width="50%" class="top">
											<span class="paginas_head">Páginas disponíveis</span>
											<div class="clearfix"></div>
											<ul id="paginas-out">
												<?php
												$pageListOut = $directIn->getPagesOutMenu($mn_id);

												if(isset($pageListOut) && $pageListOut !== false){
													foreach ($pageListOut as $lista_pgs_arr){
														//echo '<li pagina-id="' . $lista_pgs_arr['pagina_idx'] . '">' . $lista_pgs_arr['titulo'] . '</li>';

														echo '<li pagina-id="' . $lista_pgs_arr['pagina_idx'] . '" style="height:45px;" >

															<input type="hidden" name="titulo_original" class="titulo_original titulo_original_' . $lista_pgs_arr['pagina_idx'] . '" value="'.$lista_pgs_arr['titulo'].'" />
															<div class="titulo" style="width:auto;display:block" >
																<a style="float:right" class="a_tooltip" data-placement="top" title="Editar" href="javascript:;" onclick="javascript:m_t_edit(this);" >
																	<i class="fa fa-pencil-square-o"></i>
																</a>
																<span>' . $lista_pgs_arr['titulo'] . '</span>
															</div>
															<div class="titulo_edit" style="width:auto;display:none" >
																<a style="float:right" class="a_tooltip" data-placement="top" title="Salvar" href="javascript:;" onclick="javascript:m_t_save(this);" >
																	<i class="fa fa-check-circle-o"></i>
																</a>
																<input type="text" class="form-control" placeholder="'.$lista_pgs_arr['titulo'].'" name="titulo_' . $lista_pgs_arr['pagina_idx'] . '" >
															</div>
															<div class="clear" ></div>

														</li>';

													}
												}
												?>
											</ul>
										</td>
										<td width="50%" class="top">
											<div class="paginas_head">Páginas presentes no menu</div>
											<div class="clearfix"></div>
											<ul id="paginas-in">
												<?php
												$lista_pgs = $directIn->getPagesOnMenu($mn_id);
												if(isset($lista_pgs) && $lista_pgs !== false){

													foreach ($lista_pgs as $lista_pgs_arr){
														//echo '<li pagina-id="' . $lista_pgs_arr['pagina_idx'] . '">' . $lista_pgs_arr['titulo'] . '</li>';


														$titulo = $lista_pgs_arr['AltTitle'];
														$titulo_campo = "";
														if(is_null($titulo) || trim($titulo)==""){
															$titulo = trim($lista_pgs_arr['titulo']);
														}else{
															$titulo = trim($lista_pgs_arr['AltTitle']);
															$titulo_campo = $titulo;
														}

														echo '<li pagina-id="' . $lista_pgs_arr['pagina_idx'] . '" style="height:45px;" >

															<input type="hidden" name="titulo_original" class="titulo_original titulo_original_' . $lista_pgs_arr['pagina_idx'] . '" value="'.$lista_pgs_arr['titulo'].'" />
															<div class="titulo" style="width:auto;display:block" >
																<a style="float:right" class="a_tooltip" data-placement="top" title="Editar" href="javascript:;" onclick="javascript:m_t_edit(this);" >
																	<i class="fa fa-pencil-square-o"></i>
																</a>
																<span>' . $titulo . '</span><br>
																<small style="margin-top:-3px;display: block;color:#777;" >Página: ' . $lista_pgs_arr['titulo'] . '</small>
															</div>
															<div class="titulo_edit" style="width:auto;display:none" >
																<a style="float:right" class="a_tooltip" data-placement="top" title="Salvar" href="javascript:;" onclick="javascript:m_t_save(this);" >
																	<i class="fa fa-check-circle-o"></i>
																</a>
																<input type="text" class="form-control" placeholder="'.$lista_pgs_arr['titulo'].'" name="titulo_' . $lista_pgs_arr['pagina_idx'] . '" value="'.$titulo_campo.'" >
															</div>
															<div class="clear" ></div>

														</li>';
													}
												}
												?>
											</ul>
										</td>
									</tr>
								</table>

							</div>
						</td>
					</tr>
					<tr>
			     		<td>&nbsp;</td>
				     	<td colspan="3" class="right">
				     		<input type="button" value="Cancelar" class="btn btn-default" onclick="JavaScript:var x = confirm('Você deseja realmente cancelar?\n Os dados não salvos serão perdidos.'); if(x){ location.href='?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>' }">
				     		<input type="button" value="Enviar" class="btn btn-primary" data-loading-text="Carregando..."  onclick="JavaScript:checkFormRequire(document.form_dados,'#error-box');">
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
             	Nenhum registro encontrado.
             </div>";
		Sis::redirect("?mod=" . $mod . "&pag=" . $pag, 2);
	}
?>