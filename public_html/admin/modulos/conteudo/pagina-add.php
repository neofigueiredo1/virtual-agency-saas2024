<?php
	$send = isset($_POST['enviar']) && is_numeric($_POST['enviar']) ? (int)$_POST['enviar'] : 0 ;
	if ($send === 1) {
		$directIn->paginaInsert();
	}

  	// VERIFICANDO A PERMISSÃO
  	if (!Sis::checkPerm($directIn->MODULO_CODIGO.'-2') && !Sis::checkPerm($directIn->MODULO_CODIGO.'-3'))
  	{
  		Sis::setAlert('<i class="fa fa-warning"></i> Você não tem acesso à este recurso!', 1, '/admin/');
  	}
?>

<ul class="breadcrumb">
	<li><a href="?mod=<?php echo $mod; ?>&pag=pagina">Páginas</a></li>
	<li class="active">Criar nova página</li>
</ul>


<div class="btn-group">
   <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=pagina">Lista de páginas</a>
   <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=pagina&act=add" disabled="disabled">Criar nova página</a>
   <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=menu">Menus</a>
   <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=menu&act=add">Criar novo menu</a>
</div>

<hr>

<div class="alert alert-danger" id="error-box" style="display:none;">
	<i class="fa fa-exclamation-triangle"></i>&nbsp;&nbsp;Preencha todos os campos corretamente!
</div>

<form action="<?php echo Sis::currPageUrl(); ?>" method="post" class="form_dados" name="form_dados" id="form-pagina">
	<input type="hidden" name="enviar" value="1">
	<table class="table table_form">
		<tr>
			<th class="middle bg">Situação</th>
			<td width="30%" class="middle">
				<label class="radio-inline">
				  <input type="radio" id="" name="status" value="1" checked>On-line
				</label>
				<label class="radio-inline">
				  <input type="radio" id="" name="status" value="0">Off-line
				</label>
			</td>
			<th class="middle bg">Índice</th>
			<td width="30%">
				<?php
					$indiceSoma = 0 ;
					$lastPage = $directIn->getLastPage();
					if(is_array($lastPage) && count($lastPage) > 0){
						$indiceSoma = $lastPage[0]['indice'];
					}
				?>
				<input type="text" name="indice" id="indice" value="<?php echo $indiceSoma+10; ?>" class="form-control" />
			</td>
		</tr>


		<tr>
			<td class="top bg">
				<a class="btn btn-default btn-larg-todo" href="javascript:void(0);" onclick="$('.caixa_itens_avancados').stop().slideToggle('slow');">Itens Avançados</a>
			</td>
			<td colspan="3" style="" class="">
				<span class="caixa_itens_avancados middle desc_itens_avanc">Configuração de página mãe, link externo, Url amigável etc.</span>
            <div class="clearfix"></div>
				<div class="caixa_itens_avancados" style="display: none;">
					<table class="table table_form itens_avancados" border="0">
						<tr>
							<td width="50%">
								Página mãe
								<select name="pagina_mae" id="pagina_mae" class="form-control">
									<option value="0">Não possui página mãe</option>
									<?php
										$list = $directIn->paginaList();
										if(isset($list) && $list !== false){
											foreach ($list as $arrayList){
												echo '<option value="' . $arrayList["pagina_idx"] . '">' . $arrayList["titulo"] . '</option>';
											}
										}
									?>
								</select>
							</td>
							<td width="50%">
								URL amigável
								<input type="text" name="url_pagina" id="url_pagina" class="form-control" onkeyup="testExistsUrlRewrite()">
								<div class="clearfix"></div>
								<div class="alert alert-danger" id="erro-url" style="display:none; margin-top: 5px; margin-bottom: 0px; padding: 7px;">Esta url já existe</div>
							</td>
						</tr>
						<tr>
							<td>
								Link externo
								<input type="text" name="link_externo" id="link_externo" class="form-control" />
							</td>
							<td>
								Alvo
								<select name="alvo_link" id="alvo_link" class="form-control">
									<option value="0">Configuração padrão</option>
									<option value="_self">Mesma janela</option>
									<option value="_blank">Nova janela</option>
									<option value="_parent">Janela pai</option>
									<option value="_top">Janela superior</option>
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="3">
								Códigos extras
								<textarea name="extra" id="extra" rows="5" class="form-control" ></textarea>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>

		<tr>
			<td class="top bg">
				<a class="btn btn-default btn-larg-todo"  href="javascript:void(0);" onclick="$('.caixa_itens_avancados2').stop().slideToggle('slow');">Cofigurações de SEO</a>
			</td>
			<td colspan="3" style="" class="">
				<span class="caixa_itens_avancados2 middle desc_itens_avanc">Configuração do título da página, descrição e palavras-chave.</span>
            <div class="clearfix"></div>
				<div class="caixa_itens_avancados2" style="display:none;">
					<table class="table table_form itens_avancados" border="0">
						<tr>
	                  <td>
	                  	Título da página
	                  	<input type="text" class="form-control" name="titulo_seo" id="titulo_seo" />
	                  </td>
	              	</tr>
	              	<tr>
	                  <td>
	                  	Palavras-chave
	                  	<textarea name="palavra_chave" class="form-control" rows="3" id="palavra_chave"></textarea>
	                  </td>
	              	</tr>
	              	<tr>
	                  <td colspan="3">
	                  	Descrição
	                  	<textarea name="descricao" class="form-control" rows="4" id="descricao"></textarea>
	                  </td>
	              	</tr>
					</table>
				</div>
			</td>
		</tr>

		<tr>
			<th width="20%" class="middle bg">Título*</th>
			<td colspan="3">
				<input type="text" name="titulo" id="titulo" class="form-control" data-required="true" onkeyup="testExistsTitleRewrite()"  autocomplete="off">
			</td>
		</tr>

		<tr>
			<th class="middle bg top">Conteúdo</th>
			<td colspan="3"><textarea class="ckeditor" name="conteudo" id="conteudo" style="height:505px;"></textarea></td>
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
<div class="clearfix"></div>