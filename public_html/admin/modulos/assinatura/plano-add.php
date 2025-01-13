<?php
	if(!Sis::checkPerm($modulo['codigo'].'-2') && !Sis::checkPerm($modulo['codigo'].'-3')){
		Sis::setAlert('Você não tem acesso à este recurso!', 1, '/admin/');
	}

	$send = isset($_POST['exe']) ? (int)$_POST['exe'] : "";
	if ($send == "1") {
		$directIn->theInsert();
	}
	$fabricantes 	= $m_fabricante->fabricantesListAll();
	$categorias = $m_categoria->categoriasListAll();

	$departamentos = $m_departamento->all();

	$depCats = Array(Array());
	foreach ($departamentos as $key => $departamento) {
		$depCats[$departamento['departamento_idx']] = Array();
	}
	reset($departamentos);
	foreach ($categorias as $key => $categoria) {
		$departamento_idx = ( !is_null($categoria['departamento_nome']) ) ? (int)$categoria['departamento_idx'] : 0 ;
		if (!isset($depCats[$departamento_idx])) {
			$depCats[$departamento_idx] = Array();
		}
		array_push($depCats[$departamento_idx], $categoria);
	}
	reset($categorias);

	/*Dados complementares*/
	$p_dadosc =  $directIn->listProdutoDadosC(0);

?>

<script type="text/javascript" >

	//Sem valor selecionado
	var departamento_idx_nm_ = new Array('Selecione um departamento antes.');
	var departamento_idx_id_ = new Array('');

	<?php
		foreach ($departamentos as $key => $departamento) {

			$depId = $departamento['departamento_idx'];
			$nomes = Array("'Selecione a categoria'");
			$ids = Array("''");
			$depCat = $depCats[$depId];
			foreach ($depCat as $key2 => $depCat_item) {
				array_push($nomes, "'".$depCat_item['nome']."'");
				array_push($ids, "'".$depCat_item['categoria_idx']."'");
			}
			echo "	var departamento_idx_nm_".$depId." = new Array(".implode(",",$nomes).");" . PHP_EOL;
			echo "	var departamento_idx_id_".$depId." = new Array(".implode(",",$ids).");" . PHP_EOL;
			echo PHP_EOL;
		}
	?>

	//Sem valor selecionado
	var categoria_idx_nm_ = new Array('Selecione uma categoria antes.');
	var categoria_idx_id_ = new Array('');

	<?php
		foreach ($categorias as $key => $categoria) {
			$catId = $categoria['categoria_idx'];
			$nomes = Array("'Selecione a subcategoria'");
			$ids = Array("''");
			$subCategorias = $m_categoria->subCategoriasCheck($categoria['categoria_idx']);
			if ($subCategorias!==false){
				foreach ($subCategorias as $key2 => $subCategoria) {
					array_push($nomes, "'".$subCategoria['nome']."'");
					array_push($ids, "'".$subCategoria['subcategoria_idx']."'");
				}
			}
			echo "	var categoria_idx_nm_".$catId." = new Array(".implode(",",$nomes).");" . PHP_EOL;
			echo "	var categoria_idx_id_".$catId." = new Array(".implode(",",$ids).");" . PHP_EOL;
			echo PHP_EOL;
		}
	?>

	function fillFromArray(fillFrom,fillFor){
		let optNms = eval(fillFrom+'_nm_'+ $('#'+fillFrom).val());
		let optIds = eval(fillFrom+'_id_'+ $('#'+fillFrom).val());
		let optionsItems = "";
		for (i=0; i < optNms.length; i++)
	 		optionsItems += '<option value="'+optIds[i]+'" >'+optNms[i]+'</option>';
	 	console.log(optionsItems);
	  	$('#'+fillFor).html(optionsItems);
	}
	
</script>


<ol class="breadcrumb">
   <li><a href="?mod=<?php echo $mod; ?>&pag=pedido">E-commerce</a></li>
   <li><a href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>">Produtos</a></li>
   <li>Novo produto</li>
</ol>

<?php include_once("produto-menu.php"); ?>

<hr />

<div class="alert alert-danger" id="error-box" style="display:none;"><i class="fa fa-exclamation-triangle"></i>&nbsp;&nbsp;Preencha todos os campos corretamente!</div>

<form action="<?php echo Sis::currPageUrl(); ?>" method="post" class="form_dados" name="form_dados" id="form-pagina" enctype="multipart/form-data" >
	<input type="hidden" name="exe" value="1">
	<table class="table table_form">
		<tr>
			<th width="20%" class="middle bg" >Situação</th>
			<td class="middle" >
				<label class="radio-inline">
				  <input type="radio" id="" name="status" value="1" checked> Ativo
				</label>
				<label class="radio-inline">
				  <input type="radio" id="" name="status" value="0" >Inativo
				</label>
			</td>
			<th width="20%" class="middle bg">Código*</th>
			<td class="middle">
				<input type="text" name="pdv_id" id="pdv_id" class="form-control" data-required="true">
			</td>
		</tr>
		
			

		<tr>
			<th width="20%" class="middle bg">Departamento*</th>
			<td class="middle" colspan="3" >
				<select name="departamento_idx" id="departamento_idx" class="form-control" data-required="true" onchange="JavaScript:fillFromArray('departamento_idx','categoria_idx');">
					<option value="0"></option>
					<?php
					if (is_array($departamentos) && count($departamentos)>0){
						foreach ($departamentos as $key => $departamento)
						{
							echo "<option value='".$departamento['departamento_idx']."' >".$departamento['nome']."</option>";
						}
					}
					?>
	          	</select>
			</td>
		</tr>
		<tr>
			<th width="20%" class="middle bg" >Categoria*</th>
			<td class="middle" >
				<select name="categoria_idx" id="categoria_idx" class="form-control" data-required="true" onchange="JavaScript:fillFromArray('categoria_idx','subcategoria_idx');" ></select>
			</td>
			<th width="20%" class="middle bg">
				Subcategoria
				<a class="fa fa-info-circle ctn-popover"
					data-content="Escolha uma Categoria para que as Subcategorias referentes sejam listadas."
					data-original-title="Subcategoria" ></a>
			</th>
			<td class="middle">
				<select name="subcategoria_idx" id="subcategoria_idx" class="form-control" ></select>
			</td>
		</tr>

		<tr>
			<th width="20%" class="middle bg">Fabricante*</th>
			<td class="middle" >
				<select name="fabricante_idx" id="fabricante_idx" class="form-control" data-required="true"   >
              	<option value=""></option>
              	<?php
	              	if (is_array($fabricantes) && count($fabricantes)>0){
	                  foreach ($fabricantes as $key => $fabricante)
	                  {
	                      echo "<option value='".$fabricante['fabricante_idx']."' >".$fabricante['nome']."</option>";
	                  }
	               }
               ?>
          	</select>
			</td>
			<th width="20%" class="middle bg">É novidade?</th>
			<td  width="30%" class="middle">
				<label class="radio-inline">
					<input type="radio" id="" name="lancamento" value="1">Sim
				</label>
				<label class="radio-inline">
					<input type="radio" id="" name="lancamento" value="0" checked>Não
				</label>
			</td>
		</tr>

		<tr>
			<th width="20%" class="middle bg">É destaque?</th>
			<td width="30%" class="middle">
				<label class="radio-inline">
				  <input type="radio" id="" name="destaque" value="1">Sim
				</label>
				<label class="radio-inline">
				  <input type="radio" id="" name="destaque" value="0" checked>Não
				</label>
			</td>
			<th width="20%" class="middle bg">
				Mais vendido
				<a class="fa fa-info-circle ctn-popover"
					data-content="Se este campo for preenchido, este produto aparecerá na lista de mais vendidos.<br>A ordenação é feita do maior número para o menor."
					data-original-title="Mais vendido" ></a>
			</th>
			<td class="middle">
				<input type="text" name="mais_vendido" id="mais_vendido" class="form-control" onkeypress="return onlyNumber(event);" value="0">
			</td>
		</tr>
		<tr>
			<th width="20%" class="middle bg">Quantidade*</th>
			<td class="middle">
				<input type="text" name="quantidade" id="quantidade" class="form-control" data-required="true" onkeypress="return onlyNumber(event);">
			</td>
			<th width="20%" class="middle bg">
				Peso
				<a class="fa fa-info-circle ctn-popover"
					data-content="O peso deve ser preenchido em Gramas"
					data-original-title="Peso"></a>
			</th>
			<td width="30%" class="middle">
				<input type="text" class="form-control" name="peso" id="peso" value="">
			</td>
		</tr>

		<tr>
			<th width="20%" class="middle bg">Preço*</th>
			<td width="30%" class="middle">
				<input type="text" class="form-control moneymask" name="valor" id="valor" value="" data-required="true" >
			</td>
			<th width="20%" class="middle bg"></th>
			<td width="30%" class="middle"></td>
		</tr>

		<tr>
			<th width="20%" class="middle" >É oferta?</th>
			<td class="middle">
				<label class="radio-inline">
				  <input type="radio" id="" name="em_oferta" value="1" onclick="$('.oferta_por').show();">Sim
				</label>
				<label class="radio-inline">
				  <input type="radio" id="" name="em_oferta" value="0" onclick="$('.oferta_por').hide();" checked>Não
				</label>
			</td>
			<td colspan="2" style="padding:0px;height:52px;" >
				<div id="oferta_por" class="oferta_por s-hidden">
					<table class="table table_form" style="margin:0px;" >
						<tr style="border:0px;" >
							<th width="40%" class="middle bg" >Valor em oferta</th>
							<td><input type="text" class="form-control moneymask" name="em_oferta_valor" id="em_oferta_valor"></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>

		<tr>
			<td colspan="4" style="padding:0px;" >
				<div class="oferta_por s-hidden" >
					<table class="table table_form" style="margin:0px;">
						<tr>
							<th width="20%" class="middle" >Oferta expira?</th>
							<td width="30%" class="middle">
								<label class="radio-inline">
								  <input type="radio" id="" name="em_oferta_expira" value="1" onclick="$('#em_oferta_expira').show();" > Sim
								</label>
								<label class="radio-inline">
								  <input type="radio" id="" name="em_oferta_expira" value="0" onclick="$('#em_oferta_expira').hide();" checked > Não
								</label>
							</td>
							<td colspan="2" style="padding:0px;height:52px;" >
								<div id="em_oferta_expira" class="s-hidden">
									<table class="table table_form" style="margin:0px;" >
										<tr style="border:0px;" >
											<th width="40%" class="middle bg" >Data/Hora de expiração</th>
											<td>
												<input type="text" class="datepicker form-control" autocomplete="off" name="em_oferta_expira_data" id="em_oferta_expira_data" />
											</td>
											<td>
												<input type="time" class="form-control" name="em_oferta_expira_data_hora" id="em_oferta_expira_data_hora" >
											</td>
										</tr>
									</table>
								</div>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		
		<tr>
			<th class="middle bg">Largura (cm)</th>
			<td >
				<input type="text" name="largura" id="largura" class="form-control" data-required="true">
			</td>
			<th class="middle bg">Altura (cm)</th>
			<td >
				<input type="text" name="altura" id="altura" class="form-control" data-required="true">
			</td>
		</tr>
		<tr>
			<th class="middle bg">Comprimento (cm)</th>
			<td >
				<input type="text" name="comprimento" id="comprimento" class="form-control" data-required="true">
			</td>
			<th>&nbsp;</th>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<th class="middle bg">Nome*</th>
			<td colspan="3">
				<input type="text" name="nome" id="nome" class="form-control" data-required="true">
			</td>
		</tr>
		<tr>
			<th width="20%" class="top bg">Breve descrição</th>
			<td colspan="3">
				<textarea name="descricao_curta" rows="4" id="descricao_curta" class="ckeditor form-control"></textarea>
			</td>
		</tr>
		<tr>
			<th width="20%" class="top bg" >Informações gerais</th>
			<td colspan="3">
				<textarea name="descricao_longa" rows="4" id="descricao_longa" class="ckeditor form-control"></textarea>
			</td>
		</tr>
		<tr>
			<th width="20%" class="top bg" >Tags <br><small>Palavras chave que podem ser usadas para classificar produtos nas vitirnes da loja.</small></th>
			<td colspan="3">
				<input type="text" id="tagsinputField" name="tags" class="form-control" value="" />
			</td>
		</tr>

		<!-- Dados complementares do produto -->
		<?php if (is_array($p_dadosc)&&count($p_dadosc)>0): ?>
			<?php
			foreach ($p_dadosc as $key => $p_dadoc):
				$valores = $directIn->listProdutoDadosCValor($p_dadoc['dado_idx']);
			?>
				<tr>
					<th width="20%" class="top bg" ><?php echo $p_dadoc['nome']; ?></th>
					<td class="top" colspan="3" >
						<?php if (is_array($valores)&&count($valores)>0): ?>
							<?php if ($p_dadoc['selecao']==0): //Selecao simples ?>
								<select name="p_dadoc_<?php echo Text::toAscii($p_dadoc['nome']); ?>" id="" class="form-control" >
									<option value="<?php echo $p_dadoc['dado_idx']."-0"; ?>" ></option>
									<?php foreach ($valores as $key => $valor): ?>
										<option value="<?php echo $p_dadoc['dado_idx']."-".$valor['valor_idx']; ?>" ><?php echo $valor['nome']; ?></option>
									<?php endforeach ?>
								</select>
							<?php else:  //Selecao multiplo ?>
								<div class="dadoc_valores">
									<?php foreach ($valores as $key => $valor): ?>
										<div class="dadoc_valor checkbox" ><label><input type="checkbox" name="p_dadoc_<?php echo Text::toAscii($p_dadoc['nome']); ?>[]" value="<?php echo $p_dadoc['dado_idx']."-".$valor['valor_idx']; ?>" /><?php echo $valor['nome']; ?></label></div>
									<?php endforeach ?>
								</div>
							<?php endif ?>
						<?php endif ?>
					</td>
				</tr>
			<?php endforeach ?>
		<?php endif ?>

		<tr>
            <td class="middle bg">
                <b>Vídeo</b>
                <a class="fa fa-info-circle ctn-popover"
                data-content="<p>Estes são os canais mais populares: youtube.com, vimeo.com, dailymotion.com</p>"
                data-original-title="Informe a url do vídeo" ></a>
            </td>
            <td colspan="3" >
                <input type="text" name="url_video" id="video" class="form-control video_url" />
                <div class='video_info video_image'></div>
                <div class='video_info video_source'></div>
                <div class="clear"></div>
            </td>
        </tr>
		<tr>
			<th width="20%" class="top bg">Imagens</th>
			<td colspan="3">
				Dimensões:<br />
				900px X 900px (largura x altura)<br /><br />
				<input type="file" name="imagens[]" id="imagens" multiple="multiple">
			</td>
		</tr>
		
		<tr>
			<th width="20%" class="top bg">
				Variações do produto<br /><br />
				<button type="button" class="btn btn-default" onclick="javascript:prod_var_add();" >Incluir nova variação</button>
			</th>
			<td colspan="3">
				<div id="variacao_area" >
					<ul id="variacao_lista" >

					</ul>
				</div>
			</td>
		</tr>
		
		<tr>
			<td colspan="3">&nbsp;</td>
			<td class="right">
				<input type="button" value="Cancelar" class="btn btn-default" onclick="JavaScript:var x = confirm('Você deseja realmente cancelar?\n Os dados não salvos serão perdidos.'); if(x){ location.href='?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>' }">
				<input type="button" value="Enviar" class="btn btn-primary" data-loading-text="Carregando..."  onclick="JavaScript:checkFormRequire(document.form_dados,'#error-box');">
			</td>
		</tr>
	</table>
</form>

<script type="text/javascript" >
	$('#tagsinputField').tagsinput();
</script>
<style type="text/css" >
	.bootstrap-tagsinput{ width:100%; }
	.bootstrap-tagsinput .tag{
		font-size: 13px;
		font-weight: normal;
	}
</style>
