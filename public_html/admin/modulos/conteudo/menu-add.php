<?php
	$enviar = isset($_POST['enviar']) ? $_POST['enviar'] : "";
	if ($enviar != "" && $enviar == "1") {
		$directIn->theInsert();
	}


	// VERIFICANDO A PERMISSÃO
  	if (!Sis::checkPerm($directIn->MODULO_CODIGO.'-2') && !Sis::checkPerm($directIn->MODULO_CODIGO.'-4'))
  	{
  		Sis::setAlert('<i class="fa fa-warning"></i> Você não tem acesso à este recurso!', 1, '/admin/');
  	}
?>

<ul class="breadcrumb">
	<li><a href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>">Menus</a></li>
	<li class="active">Criar novo menu</li>
</ul>

<div class="btn-group">
    <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=pagina">Lista de páginas</a>
    <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=pagina&act=add">Criar nova página</a>
    <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=menu">Menus</a>
    <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=menu&act=add" disabled="disabled">Criar novo menu</a>
</div>

<hr />

<div class="alert alert-danger" id="error-box" style="display:none;"><i class="fa fa-exclamation-triangle"></i>&nbsp;&nbsp;Preencha todos os campos corretamente!</div>

<form action="<?php echo Sis::currPageUrl(); ?>" method="post" class="form_dados" name="form_dados">
	<input type="hidden" name="enviar" value="1">
	<input type="hidden" name="paginas" value="" id="paginas">
	<table class="table table_form">
		<tr>
			<th width="25%" class="middle bg">Situação</th>
			<td width="75%" class="middle">
				<label class="radio-inline">
				  <input type="radio" id="" name="status" value="1" checked>Ativo
				</label>
				<label class="radio-inline">
				  <input type="radio" id="" name="status" value="0">Inativo
				</label>
			</td>
		</tr>
		<tr>
			<th class="middle bg">Nome*</th>
			<td>
				<input type="text" name="nome" id="nome" class="form-control" data-required="true">
			</td>
		</tr>
		<tr>
			<th class="top bg">Descrição</th>
			<td>
				<textarea name="descricao" id="descricao" rows="3" class="form-control" ></textarea>
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
									$lista = $directIn->pageList();
									if(isset($lista) && $lista !== false){
										foreach ($lista as $lista_arr){
											echo '<li pagina-id="' . $lista_arr['pagina_idx'] . '" style="height:45px;" >

														<input type="hidden" name="titulo_original" class="titulo_original titulo_original_' . $lista_arr['pagina_idx'] . '" value="'.$lista_arr['titulo'].'" />
														<div class="titulo" style="width:auto;display:block" >
															<a style="float:right" class="a_tooltip" data-placement="top" title="Editar" href="javascript:;" onclick="javascript:m_t_edit(this);" >
																<i class="fa fa-pencil-square-o"></i>
															</a>
															<span>' . $lista_arr['titulo'] . '</span><br>
																<small style="margin-top:-3px;display: block;color:#777;" >Página: ' . $lista_arr['titulo'] . '</small>
														</div>
														<div class="titulo_edit" style="width:auto;display:none" >
															<a style="float:right" class="a_tooltip" data-placement="top" title="Salvar" href="javascript:;" onclick="javascript:m_t_save(this);" >
																<i class="fa fa-check-circle-o"></i>
															</a>
															<input type="text" class="form-control" placeholder="'.$lista_arr['titulo'].'" name="titulo_' . $lista_arr['pagina_idx'] . '" >
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