<?php

	if(!Sis::checkPerm($modulo['codigo'].'-2') && !Sis::checkPerm($modulo['codigo'].'-3')){
		Sis::setAlert('Você não tem acesso à este recurso!', 1, '/admin/');
	}

	$pidx = isset($_GET['pid']) ? (int)$_GET['pid'] : 0;
	$send = isset($_POST['exe']) ? (int)$_POST['exe'] : "";
	if ($send == "1") {
		$directIn->theUpdate();
	}

	if($pidx == 0){ Sis::setAlert('Selecione um produto para editar!', 1,'?mod='.$mod.'&pag='.$pag); }

	$fabricantes 	= $m_fabricante->fabricantesListAll();
	$categorias 	= $m_categoria->categoriasListAll();
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


	$variacoes = $directIn->listAllVariacao($pidx);

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
   <li>Editar produto</li>
</ol>

<?php include_once("produto-menu.php"); ?>

<hr />

<div class="alert alert-danger" id="error-box" style="display:none;"><i class="fa fa-exclamation-triangle"></i>&nbsp;&nbsp;Preencha todos os campos corretamente!</div>

<?php
	$list = $directIn->listSelected($pidx);
	if(is_array($list) && count($list) > 0){
		foreach ($list as $arrayList) {

			$txtIntegrado = '<span class="label label-danger" >NÃO</span>';
			if ((int)$arrayList['pdv_id']!=0) {
				$txtIntegrado = '<span class="label label-success" >SIM</span>';
			}

?>
<form action="<?php echo Sis::currPageUrl(); ?>" method="post" class="form_dados" name="form_dados" id="form-pagina" enctype="multipart/form-data" >
	<input type="hidden" name="exe" value="1" >
	<input type="hidden" name="pid" value="<?php echo $pidx; ?>" >
	<input type="hidden" name="nomeOld" value="<?php echo $arrayList['nome']; ?>" >
	<input type="hidden" name="pdv_idOld" value="<?php echo $arrayList['pdv_id']; ?>" >
	<table class="table table_form" >
		
		<tr>
			<th width="20%" class="middle bg">Situação</th>
			<td class="middle">
				<label class="radio-inline">
				  <input type="radio" id="" name="status" value="1" <?php echo ($arrayList['status'] == "1") ? "checked" : ""; ?>>Ativo
				</label>
				<label class="radio-inline">
				  <input type="radio" id="" name="status" value="0" <?php echo ($arrayList['status'] == "0") ? "checked" : ""; ?>>Inativo
				</label>
			</td>
			<th width="20%" class="middle bg" >Código*</th>
			<td class="middle">
				<input type="text" name="pdv_id" id="pdv_id" class="form-control" data-required="true" value="<?php echo $arrayList['pdv_id']; ?>">
			</td>
		</tr>
		
			
		<tr>
			<th width="20%" class="middle bg">Departamento*</th>
			<td class="middle" colspan="3" >
				<select name="departamento_idx" id="departamento_idx" class="form-control" data-required="true" onchange="JavaScript:fillFromArray('departamento_idx','categoria_idx');fillFromArray('categoria_idx','subcategoria_idx');">
					<option value="" ></option>
					<?php
					if (is_array($departamentos) && count($departamentos)>0){
						foreach ($departamentos as $key => $departamento)
						{
							$selected = ($departamento['departamento_idx'] == $arrayList['departamento_idx']) ? "selected" : "";
							echo "<option value='".$departamento['departamento_idx']."' ".$selected." >".$departamento['nome']."</option>";
						}
					}
					?>
	          	</select>
			</td>
		</tr>
		<tr>
			<th width="20%" class="middle bg">Categoria*</th>
			<td class="middle">
				<select name="categoria_idx" id="categoria_idx" class="form-control" data-required="true" onchange="JavaScript:fillFromArray('categoria_idx','subcategoria_idx');">
              	<option value="" >Selecione a categoria</option>
              	<?php
              		$depCats = $depCats[$arrayList['departamento_idx']];
	              	if (is_array($depCats) && count($depCats)>0){
	                  foreach ($depCats as $key => $categoria)
	                  {
	                  	$selected = ($categoria['categoria_idx'] == $arrayList['categoria_idx']) ? "selected" : "";
	                    echo "<option value='".$categoria['categoria_idx']."' ".$selected.">".$categoria['nome']."</option>";
	                  }
	               }
               ?>
          	</select>
			</td>
			<th width="20%" class="middle bg">Subcategoria
					<a class="fa fa-info-circle ctn-popover"
						data-content="Escolha uma Categoria para que as Subcategorias referentes sejam listadas."
						data-original-title="Subcategoria" ></a>
				</th>
				<td class="middle">
					<select name="subcategoria_idx" id="subcategoria_idx" class="form-control">
	              	<option value="0" >Selecione a subcategoria</option>
	              	<?php
	              		$subCategorias = $m_categoria->subCategoriasCheck($arrayList['categoria_idx']);
		              	if (is_array($subCategorias) && count($subCategorias)>0){
		              		$selected = "";
		                  foreach ($subCategorias as $key => $subCategoria)
		                  {
		                  	$selected = ($subCategoria['subcategoria_idx'] == $arrayList['subcategoria_idx']) ? "selected" : "";
		                     echo "<option value='".$subCategoria['subcategoria_idx']."' ".$selected.">".$subCategoria['nome']."</option>";
		                  }
		               }
	               ?>
	          	</select>
			</td>
		</tr>
		
		
			
	
		<tr>
			<th width="20%" class="middle bg">Fabricante*</th>
			<td class="middle">
				<select name="fabricante_idx" id="fabricante_idx" class="form-control" data-required="true"   >
				<option value="" ></option>
				<?php
					if (is_array($fabricantes) && count($fabricantes)>0){
						$selected = "";
						foreach ($fabricantes as $key => $fabricante)
						{
						$selected = ($fabricante['fabricante_idx'] == $arrayList['fabricante_idx']) ? "selected" : "";
							echo "<option value='".$fabricante['fabricante_idx']."' ".$selected.">".$fabricante['nome']."</option>";
						}
					}
				?>
			</select>
			</td>
			<th width="20%" class="middle bg">É novidade?</th>
			<td  width="30%" class="middle">
				<label class="radio-inline">
					<input type="radio" id="" name="lancamento" value="1" <?php echo ($arrayList['lancamento'] == "1") ? "checked" : ""; ?>>Sim
				</label>
				<label class="radio-inline">
					<input type="radio" id="" name="lancamento" value="0" <?php echo ($arrayList['lancamento'] == "0") ? "checked" : ""; ?>>Não
				</label>
			</td>
			
		</tr>

		<tr>
			<th width="20%" class="middle bg">É destaque?</th>
			<td width="30%" class="middle">
				<label class="radio-inline">
				  <input type="radio" id="" name="destaque" value="1" <?php echo ($arrayList['destaque'] == "1") ? "checked" : ""; ?>>Sim
				</label>
				<label class="radio-inline">
				  <input type="radio" id="" name="destaque" value="0" <?php echo ($arrayList['destaque'] == "0") ? "checked" : ""; ?>>Não
				</label>
			</td>
			<th width="20%" class="middle bg" >
				Mais vendido
				<a class="fa fa-info-circle ctn-popover"
					data-content="Se este campo for preenchido, este produto aparecerá na lista de mais vendidos.<br>A ordenação é feita do maior número para o menor."
					data-original-title="Mais vendido" ></a>
			</th>
			<td class="middle">
				<input type="text" name="mais_vendido" id="mais_vendido" class="form-control" onkeypress="return onlyNumber(event);" value="<?php echo $arrayList['mais_vendido']; ?>">
			</td>
		
		</tr>
		<tr>
			<th width="20%" class="middle bg">Quantidade*</th>
			<td class="middle">
				<input type="text" name="quantidade" id="quantidade" class="form-control" data-required="true" onkeypress="return onlyNumber(event);" value="<?php echo $arrayList['quantidade']; ?>">
			</td>
			<th width="20%" class="middle bg">
				Peso
				<a class="fa fa-info-circle ctn-popover"
					data-content="O peso deve ser preenchido em Gramas"
					data-original-title="Peso"></a>
			</th>
			<td width="30%" class="middle">
				<input type="text" class="form-control" name="peso" id="peso" value="<?php echo $arrayList['peso']; ?>">
			</td>
		</tr>

		<tr>
			<th width="20%" class="middle bg">Preço*</th>
			<td width="30%" class="middle">
				<input type="text" class="moneymask form-control" name="valor" id="valor" data-required="true" value="<?php echo number_format($arrayList['valor'],2,",","."); ?>">
			</td>
			<th width="20%" class="middle bg"></th>
			<td width="30%" class="middle"></td>
		</tr>

		<tr>
			<th width="20%" class="middle" >É oferta?</th>
			<td class="middle">
				
				<label class="radio-inline">
					<input type="radio" id="" name="em_oferta" value="1" onclick="$('.oferta_por').show();" <?php echo ($arrayList['em_oferta'] == "1") ? "checked" : ""; ?>>Sim
				</label>
				<label class="radio-inline">
			  		<input type="radio" id="" name="em_oferta" value="0" onclick="$('.oferta_por').hide();" <?php echo ($arrayList['em_oferta'] == "0") ? "checked" : ""; ?>>Não
				</label>
			</td>
			<td colspan="2" style="padding:0px;height:52px;">

				<div id="oferta_por" class="oferta_por <?php echo ($arrayList['em_oferta'] == "0") ? "s-hidden" : ""; ?>">
					<table class="table table_form" style="margin:0px;" >
						<tr style="border:0px;" >
							<th width="40%" class="middle bg" >Valor em oferta</th>
							<td><input type="text" class="moneymask form-control" name="em_oferta_valor" id="em_oferta_valor" value="<?php echo number_format($arrayList['em_oferta_valor'],2,",","."); ?>"></td>
						</tr>
					</table>
				</div>

			</td>
		</tr>

		<tr>
			<td colspan="4" style="padding:0px;" >
				<div class="oferta_por <?php echo ($arrayList['em_oferta'] == "0") ? "s-hidden" : ""; ?>" >
					<table class="table table_form" style="margin:0px;">
						<tr>
							<th width="20%" class="middle" >Oferta expira?</th>
							<td width="30%" class="middle">
								<label class="radio-inline">
									<input type="radio" id="" name="em_oferta_expira" value="1" onclick="$('#em_oferta_expira').show();" <?php echo ((int)$arrayList['em_oferta_expira'] == 1) ? "checked" : ""; ?> > Sim
								</label>
								<label class="radio-inline">
									<input type="radio" id="" name="em_oferta_expira" value="0" onclick="$('#em_oferta_expira').hide();" <?php echo ((int)$arrayList['em_oferta_expira'] == 0) ? "checked" : ""; ?> > Não
								</label>
							</td>
							<td style="padding:0px;height:52px;" >
								<div id="em_oferta_expira" class=" <?php echo ((int)$arrayList['em_oferta_expira'] == 0) ? "s-hidden" : ""; ?>">
									<table class="table table_form" style="margin:0px;" >
										<tr style="border:0px;" >
											<th width="40%" class="middle bg" >Data/Hora de expiração</th>
											<td>
												<input type="text" class="datepicker form-control" autocomplete="off" name="em_oferta_expira_data" id="em_oferta_expira_data" value="<?php echo (!is_null($arrayList['em_oferta_expira_data'])&&$arrayList['em_oferta_expira_data']!=0)?date("d/m/Y", strtotime($arrayList['em_oferta_expira_data'])):''; ?>" />
											</td>
											<td>
												<input type="time" class="form-control" name="em_oferta_expira_data_hora" id="em_oferta_expira_data_hora"
													value="<?php echo (!is_null($arrayList['em_oferta_expira_data'])&&$arrayList['em_oferta_expira_data']!=0)?date("H:i", strtotime($arrayList['em_oferta_expira_data'])):''; ?>" 
												/>
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
				<input type="text" name="largura" id="largura" class="form-control" data-required="true"  value="<?php echo $arrayList['largura']; ?>" >
			</td>
			<th class="middle bg">Altura (cm)</th>
			<td >
				<input type="text" name="altura" id="altura" class="form-control" data-required="true" value="<?php echo $arrayList['altura']; ?>" >
			</td>
		</tr>
		<tr>
			<th class="middle bg">Comprimento (cm)</th>
			<td >
				<input type="text" name="comprimento" id="comprimento" class="form-control" data-required="true" value="<?php echo $arrayList['comprimento']; ?>" >
			</td>
			<th>&nbsp;</th>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<th class="middle bg">Nome*</th>
			<td colspan="3">
				<input type="text" name="nome" id="nome" class="form-control" data-required="true" value="<?php echo $arrayList['nome']; ?>">
			</td>
		</tr>
		<tr>
			<th width="20%" class="top bg">Breve descrição</th>
			<td colspan="3">
				<textarea name="descricao_curta" rows="4" maxlength="250" id="descricao_curta" class="form-control"><?php echo $arrayList['descricao_curta']; ?></textarea>
			</td>
		</tr>
		<tr>
			<th width="20%" class="top bg">Informações gerais</th>
			<td colspan="3">
				<textarea name="descricao_longa" rows="4" id="descricao_longa" class="ckeditor form-control"><?php echo $arrayList['descricao_longa']; ?></textarea>
			</td>
		</tr>
		<tr>
			<th width="20%" class="top bg" >Tags <br><small>Palavras chave que podem ser usadas para classificar produtos nas vitirnes da loja.</small></th>
			<td colspan="3">
				<input type="text" id="tagsinputField" name="tags" class="form-control" value="<?php echo $arrayList['tags']; ?>" />
			</td>
		</tr>
		

		<!-- Dados complementares -->
		<?php if(is_array($p_dadosc)&&count($p_dadosc)>0): ?>
			<?php
			foreach ($p_dadosc as $key => $p_dadoc):
				$p_dado_valor = $directIn->listProdutoVarDadosC($arrayList['produto_idx'],$p_dadoc['dado_idx']);
				$pdadoc_valor=(is_array($p_dado_valor)&&count($p_dado_valor))?$p_dado_valor[0]['valor_idx']:0;
				$valores = $directIn->listProdutoDadosCValor($p_dadoc['dado_idx']);
			?>
				<tr>
					<th width="20%" class="middle bg"><?php echo $p_dadoc['nome']; ?></th>
					<td class="middle" colspan="3" >
						<?php if(is_array($valores)&&count($valores)>0): ?>

							<?php if($p_dadoc['selecao']==0): //Selecao simples ?>
								<select name="p_dadoc_<?php echo Text::toAscii($p_dadoc['nome']); ?>" id="" class="form-control" >
									<option value="<?php echo $p_dadoc['dado_idx']."-0"; ?>" ></option>
									<?php foreach ($valores as $key => $valor):
										$selected = ($pdadoc_valor==$valor['valor_idx'])?"selected":"";
									?>
										<option value="<?php echo $p_dadoc['dado_idx']."-".$valor['valor_idx']; ?>" <?php echo $selected; ?> ><?php echo $valor['nome']; ?></option>
									<?php endforeach ?>
								</select>
							<?php else:  //Selecao multiplo
							$p_dado_valor_str = "";
							if(is_array($p_dado_valor)&&count($p_dado_valor)>0) {
								foreach ($p_dado_valor as $key => $p_dado_valor_value) {
									$p_dado_valor_str .= "!".$p_dado_valor_value['dado_idx']."-".$p_dado_valor_value['valor_idx']."!";
								}
							}
							?>
								<div class="dadoc_valores">
									<input type="hidden" name="p_dadoc_old_<?php echo Text::toAscii($p_dadoc['nome']); ?>" value="<?php echo "!".$p_dado_valor_str."!"; ?>" >
									<?php foreach ($valores as $key => $valor): ?>
										<div class="dadoc_valor checkbox" >
											<label>
												<input type="checkbox"
													name="p_dadoc_<?php echo Text::toAscii($p_dadoc['nome']); ?>[]"
													value="<?php echo $p_dadoc['dado_idx']."-".$valor['valor_idx']; ?>"
													<?php echo (strpos($p_dado_valor_str,$p_dadoc['dado_idx']."-".$valor['valor_idx'])!==false) ? "checked" : ""; ?>
												/>
												<?php echo $valor['nome']; ?>
											</label>
										</div>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>

						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>

		<tr>
	        <td class="middle bg">
	            <b>Vídeo</b>
	            <a class="fa fa-info-circle ctn-popover"
	            data-content="<p>Estes são os canais mais populares: youtube.com, vimeo.com, dailymotion.com</p>"
	            data-original-title="Informe a url do vídeo" ></a>
	        </td>
	        <td colspan="3" >
	            <input type="text" name="url_video" id="url" class="form-control video_url"  value="<?php echo $arrayList['url_video']; ?>"/>
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
            <th>Imagens enviadas</th>
            <td colspan="3" >

                <div id="img_lista_acoes" >
                	<div class="checkbox" style="float:left; margin:0px;" >
							<label>
                    		<input type="checkbox" id="check_all" onclick="if(this.checked){ $('.r_imagem').prop('checked',true); }else{ $('.r_imagem').prop('checked',false); }" /> Selecionar todas
                    	</label>
                  </div>
                    &nbsp; &nbsp;  | &nbsp; &nbsp; <a href="javascript:;" onclick="javascript:remove_selecionado();" >Excluir selecionadas</a>
                </div>

                <div id="img_lista_reposta" style="display:none" class="alert alert-success" ></div>
                <div id="img_lista" >
                <?php
                $imagesList = $directIn->listAllProdImages($pidx);
                $LocalModuloImgDir = "../sitecontent/ecommercex/produto/images/";

                if(isset($imagesList) && $imagesList !== false){
                    echo('<ul id="imagens-lista" >');
                    $n_count = 0;
                    foreach($imagesList as $imagem)
                    {
                        $n_count++;
                        echo('<li id="'.$imagem['produto_imagem_idx'].'" class="imagem_item" >
                        <table class="table" style="margin:0px;">
                            <tr style="border:0px;" >
                                <td align="center" style="text-align:center;" >[ '.$n_count.' ]<br><br><input type="checkbox" name="r_imagem" class="r_imagem" value="'.$imagem['produto_imagem_idx'].'" onclick="javascript:$(\'#check_all\').attr(\'checked\',false);" /></td>
                                <td width=100 ><div class="imagem" ><a href="'.$LocalModuloImgDir.'g/'.$imagem['imagem'].'" target="_blank" ><img src="'.$LocalModuloImgDir.'m/'.$imagem['imagem'].'" border=0 /></a></div></td>
                                <td>
                                    <input type="hidden" class="form-control" name="img_id[]" id="img_id_'.$imagem['produto_imagem_idx'].'" value="'.$imagem['produto_imagem_idx'].'" />
                                    Descri&ccedil;&atilde;o:<br>
                                    <input type="text" class="form-control" name="img_descricao[]" id="img_descricao_'.$imagem['produto_imagem_idx'].'" value="'.$imagem['nome'].'" />
                                </td>
                                	<td width="50" class="="center middle">
	                                	<a href="javascript:;" title="Excluir imagem" class="a_tooltip" onclick="javascript:remove_imagem('.$imagem['produto_imagem_idx'].');" >
	                                		<i style="margin-top: 28px;" class="fa fa-trash-o"></i>
	                                	</a>
	                              </td>
                            </tr>
                        </table>
                        </li>');
                    }
                    echo('</ul>');
                }
                ?>
                </div>

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
						<?php
							if(is_array($variacoes)&&count($variacoes)>0){
								foreach($variacoes as $key => $variacao){

									/*Traz os dados da variação*/
									$varid = $variacao['variacao_idx'];
									$status = $variacao['status'];
									$quantidade = $variacao['quantidade'];
									$codigo = $variacao['codigo'];
									$nome = $variacao['nome'];
									//$valor_pvar = $variacao['valor'];
									$imagem = $variacao['imagem'];

									sleep(1);
									/*identificar unico para os campos*/
									$codigo_gera = date("YmdHis").substr((string)microtime(), 2, 8);

									$dados =  $directIn->listProdutoDadosC(1);
								?>
								<li>

									<div class="produto_variacao panel panel-default show">
										<div class="panel-heading" >
											Variação do produto
											<a href="javascript:;" onclick="javascript:prod_var_remove(this,<?php echo $pidx; ?>,<?php echo $varid; ?>);" class="a_tooltip pull-right" title="Remover esta variação" ><i class="fa fa-trash-o"></i></a>
										</div>
										<div class="panel-body">

											<input type="hidden" name="pvar_cd[]" value=" <?php echo $codigo_gera; ?> " />
											<input type="hidden" name="pvar_id_<?php echo $codigo_gera; ?>" class="pvar_id" value="<?php echo $varid; ?>" />
											<input type="hidden" name="pvar_imagem_old_<?php echo $codigo_gera; ?>" value="<?php echo $imagem; ?>"  />
											<table class="table table_form" >
													<tr>
														<th width="20%" class="middle bg">Situação</th>
														<td width="30%" class="middle">
															<label class="radio-inline"> <input type="radio" class="status" name="pvar_status_<?php echo $codigo_gera; ?>" value="1" <?php echo ($status==1)?"checked":""; ?> >Ativo </label>
															<label class="radio-inline"> <input type="radio" class="status" name="pvar_status_<?php echo $codigo_gera; ?>" value="0" <?php echo ($status==0)?"checked":""; ?>>Inativo </label>
														</td>
														<th width="20%"></th>
														<td class="middle"></td>
													</tr>
													<tr>
														<th width="20%" class="middle bg">Quantidade*</th>
														<td class="middle">
															<input type="text" name="pvar_quantidade_<?php echo $codigo_gera; ?>" class="quantidade form-control" data-required="true" value="<?php echo $quantidade; ?>" >
														</td>
														<th width="20%" class="middle bg">Código*</th>
														<td class="middle">
															<input type="text" name="pvar_codigo_<?php echo $codigo_gera; ?>" class="codigo form-control" data-required="true" value="<?php echo $codigo; ?>" />
														</td>
													</tr>
													<tr>
														<th width="20%" class="middle bg">Nome*</th>
														<td class="middle" colspan="3" >
															<input type="text" name="pvar_nome_<?php echo $codigo_gera; ?>" class="nome form-control" data-required="true" value="<?php echo $nome; ?>" >
														</td>
													</tr>
													<!-- Dados complementares -->
													<?php if(is_array($dados)&&count($dados)>0): ?>
														<?php foreach($dados as $key => $dado):
															$p_variacao_dado_valor = $directIn->listProdutoVarDadosC($variacao['variacao_idx'],$dado['dado_idx'],0,1);
															$p_var_dado_valor=(is_array($p_variacao_dado_valor)&&count($p_variacao_dado_valor))?$p_variacao_dado_valor[0]['valor_idx']:0;
															$valores =  $directIn->listProdutoDadosCValor($dado['dado_idx']);
														?>
															<tr>
																<th width="20%" class="middle bg"><?php echo $dado['nome']; ?></th>
																<td class="middle" colspan="3" >
																	<?php if(is_array($valores)&&count($valores)>0): ?>
																	<select name="pvar_<?php echo Text::toAscii($dado['nome']); ?>_<?php echo $codigo_gera; ?>" id="" class="form-control" >
																		<option value="<?php echo $dado['dado_idx']."-0"; ?>" ></option>
																		<?php foreach($valores as $key => $valor): ?>
																			<option value="<?php echo $dado['dado_idx']."-".$valor['valor_idx']; ?>" <?php echo ($p_var_dado_valor==$valor['valor_idx'])?"selected":""; ?> ><?php echo $valor['nome']; ?></option>
																		<?php endforeach; ?>
																	</select>
																	<?php endif; ?>
																</td>
															</tr>
														<?php endforeach; ?>
													<?php endif; ?>
													<!-- <tr>
														<th width="20%" class="middle bg" >Valor</th>
														<td class="middle">
															<input type="text" class="form-control moneymask" name="pvar_valor_<?php //echo $codigo_gera; ?>" id="pvar_valor_<?php //echo $codigo_gera; ?>" value="<?php //echo number_format($valor_pvar,2,",","."); ?>" >
														</td>
														<th width="20%">&nbsp;</th>
														<td >&nbsp;</td>
													</tr> -->
													<tr>
														<th width="20%" class="middle bg">Imagem*<br><small>Área de exibição: 950 x 950 pixels</small></th>
														<td class="middle" colspan="3" >
															<?php if ($imagem!="Null"): ?>
																<img src='/sitecontent/ecommercex/produto/images/<?php echo $imagem; ?>' width='75' />
																<br><label><input type="checkbox" name="pvar_imagem_del_<?php echo $codigo_gera; ?>" value="1" /> &nbsp; Remover a imagem atual.</label><br>
																<br>Para mudar a imagem selecione uma outra no campo abaixo:<br>
															<?php endif ?>
															<input type="file" name="pvar_imagem_<?php echo $codigo_gera; ?>" class="imagem" class="form-control" >
														</td>
													</tr>
											</table>

										</div>
									</div>

								</li>
								<?php
								}
							}
						?>
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
<script>tbl=5;//identifica a base para manipular as imagens</script>
<script type="text/javascript" >
	$('#tagsinputField').tagsinput();
</script>
<style type="text/css">
	.bootstrap-tagsinput{ width:100%; }
	.bootstrap-tagsinput .tag{
		font-size: 13px;
		font-weight: normal;
	}
</style>
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