<?php
	$enviar = isset($_POST['enviar']) ? (int)$_POST['enviar'] : 0 ;
	$id     = isset($_GET['id'])      ? (int)$_GET['id']      : 0 ;
	if ($enviar ===	 1) $directIn->theUpdate();


	$cadastroAfiliadoCursos = $directIn->cadastroAfiliadoCursos($id);
	$cadAfCursos = [];
	if (is_array($cadastroAfiliadoCursos)) {
		for($i=0; $i<count($cadastroAfiliadoCursos); ++$i){
			$cadAfCursos[] = $cadastroAfiliadoCursos[$i]['curso_idx'];
		}
	}


?>

<ol class="breadcrumb">
    <li><a href="?mod=<?php echo $mod ?>&pag=cadastro" >Cadastros</a></li>
    <li class="active" >Editar cadastro</li>
</ol>

<div class="btn-group">
    <a class="btn btn-default" href="?mod=<?php echo $mod ?>&pag=cadastro">Cadastros</a>
    <a class="btn btn-default" href="?mod=<?php echo $mod ?>&pag=cadastro&act=add">Adicionar cadastro</a>
</div>
<div class="btn-group">
    <a class="btn btn-default" href="?mod=<?php echo $mod ?>&pag=area-interesse">Áreas de interesse</a>
    <a class="btn btn-default" href="?mod=<?php echo $mod ?>&pag=area-interesse&act=add" >Nova área de interesse</a>
</div>
<div class="btn-group">
  	<a class="btn btn-success" href="?mod=<?php echo $mod ?>&pag=cadastro&act=exportar">Exportar cadastros</a>
</div>

<hr />

<section class="content">
	<?php
	$list = $directIn->listSelected($id);
	if(is_array($list) && count($list) > 0){
		foreach ($list as $arrayList){

			$perfil = (!is_null($arrayList['perfil'])) ? (int)$arrayList['perfil']:0;

			?>
			<form action="<?php echo sis::currPageUrl(); ?>" method="post" id="form_dados" class="form_dados" name="form_dados" enctype="multipart/form-data">
				<input type="hidden" name="enviar" value="1">
				<input type="hidden" name="id" value="<?php echo $id ?>">
				<input type="hidden" name="certificado_logoOld" value="<?php echo $arrayList['certificado_logo']; ?>">
				<input type="hidden" name="certificado_assinaOld" value="<?php echo $arrayList['certificado_assina']; ?>">
				<input type="hidden" name="afiliadoCursos_old" value="!<?php echo implode("!!",$cadAfCursos); ?>!">
				<table class="table table_form">
			    	<tr>
			            <th width="20%" class="middle bg" >Situação</th>
			            <td width="30%" class="middle">
							<label class="radio-inline">
								<input type="radio" id="" name="status" value="1" <?php if ($arrayList['status']=="1"){ echo "checked='checked'"; } ?> >Ativo
							</label>
							<label class="radio-inline">
								<input type="radio" id="" name="status" value="0" <?php if ($arrayList['status']=="0"){ echo "checked='checked'"; } ?> >Inativo
							</label>
							<label class="radio-inline">
								<input type="radio" id="" name="status" value="4" <?php if ($arrayList['status']=="4"){ echo "checked='checked'"; } ?> >Newsletter
							</label>
			            </td>
			            <?php if (false): ?>
			            	<th width="20%" class="middle bg">Perfil</th>
				            <td width="30%" class="middle">
				               <label class="radio-inline">
									  <input type="radio" id="" name="perfil" value="0" <?php echo ((int)$perfil==0) ? 'checked="checked"' :''; ?>
									  	onclick="javascript:$('#produto_iugu_info').slideUp('fast');" 
									  > Aluno
									</label>
									<label class="radio-inline">
									  <input type="radio" id="" name="perfil" value="1" <?php echo ((int)$perfil==1) ? 'checked="checked"' :''; ?>
									  	onclick="javascript:$('#produto_iugu_info').slideDown('fast');" 
									  > Produtor
									</label>
									<label class="radio-inline">
									  <input type="radio" id="" name="perfil" value="2" <?php echo ((int)$perfil==2) ? 'checked="checked"' :''; ?>
									  	onclick="javascript:$('#produto_iugu_info').slideDown('fast');" 
									  > Co-Produtor
									</label>
				            </td>
			            <?php endif ?>
			        </tr>
			        <tr>
			            <th width="20%" class="middle bg" >P&aacute;gina de venda</th>
			            <td width="30%" class="middle">
			                <label class="radio-inline" >
			                    <input type="radio" id="" name="lp_active" value="1"  <?php if ((int)$arrayList['lp_active']==1){ echo "checked='checked'"; } ?>  >Ativo
			                </label>
			                <label class="radio-inline" >
			                    <input type="radio" id="" name="lp_active" value="0"  <?php if ((int)$arrayList['lp_active']==0){ echo "checked='checked'"; } ?> >Inativo
			                </label>
			            </td>
			            <th width="20%" class="middle bg" >Quota de armazenamento (MB)</th>
			            <td width="30%" class="middle" >
			                <input type="text" class="form-control" name="lp_quota_mb" id="lp_quota_mb" value="<?php echo $arrayList['lp_quota_mb']; ?>" />
			            </td>
			        </tr>
			        <tr>
			            <th width="20%" class="middle bg">Nome completo </th>
			            <td colspan="3">
			                <input type="text" class="form-control" name="nome_completo" id="nome_completo" data-required="true" value="<?php echo $arrayList['nome_completo']; ?>">
			            </td>
			        </tr>
			    	<tr>
						<th width="20%" class="middle bg">
			                Nome informal
			                <a class="fa fa-info-circle ctn-popover" data-content="<p>Nome como prefere ser chamado.</p>" data-original-title="Nome informal" ></a>
			            </th>
						<td>
			                <input type="text" class="form-control" name="nome_informal" id="nome_informal" value="<?php echo $arrayList['nome_informal']; ?>">
			            </td>
			            <th width="20%" class="middle bg">CPF/CNPJ (somente números) </th>
			            <td>
			                <input type="text" class="form-control" name="cpf_cnpj" id="cpf_cnpj" value="<?php echo $arrayList['cpf_cnpj']; ?>">
			            </td>
					</tr>
			        <tr>
			            <th class="middle bg">E-mail: </th>
			            <td>
			                <input type="email" class="form-control" name="email" id="email" data-required="true" value="<?php echo $arrayList['email']; ?>">
			            </td>
			            <th class="middle bg">Telefone residencial </th>
			            <td>
			                <input type="text" class="form-control telefone_edit" name="telefone_resid" id="telefone_resid" value="<?php echo $arrayList['telefone_resid']; ?>">
			            </td>
					</tr>
			        <tr>
						<th class="middle bg">Telefone comercial </th>
			            <td>
			                <input type="text" class="form-control telefone_edit" name="telefone_comer" id="telefone_comer" value="<?php echo $arrayList['telefone_comer']; ?>">
			            </td>
			            <th class="middle bg">Celular </th>
			            <td>
			                <input type="text" class="form-control telefone_edit" name="celular" id="celular" value="<?php echo $arrayList['celular']; ?>">
			            </td>
			        </tr>
			        <tr>
			            <th class="middle bg">Gênero </th>
			            <td class="middle">
			                <label class="radio-inline">
			                  <input type="radio" id="" name="genero" value="1" <?php if ($arrayList['genero']=="1"){ echo "checked='checked'"; } ?>>Masculino
			                </label>
			                <label class="radio-inline">
			                  <input type="radio" id="" name="genero" value="0" <?php if ($arrayList['genero']=="2"){ echo "checked='checked'"; } ?>>Feminino
			                </label>
			            </td>
			            <th class="middle bg">Data de nascimento </th>
			            <td>
			                <input type="text" class="form-control" name="data_nasc" id="data_nasc" value="<?php echo date("m/d/Y",strtotime($arrayList['data_nasc'])); ?>">
			            </td>
			        </tr>
			        <tr>
			            <th class="middle bg">Endereço </th>
			            <td>
			                <input type="text" class="form-control" name="endereco" id="endereco" value="<?php echo $arrayList['endereco']; ?>">
			            </td>
			            <th class="middle bg">Nº </th>
			            <td>
			                <input type="text" class="form-control" name="numero" id="numero" value="<?php echo $arrayList['numero']; ?>">
			            </td>
			        </tr>
			        <tr>
			            <th class="middle bg">Complemento </th>
			            <td>
			                <input type="text" class="form-control" name="complemento" id="complemento" value="<?php echo $arrayList['complemento']; ?>">
			            </td>
			            <th class="middle bg">Bairro </th>
			            <td>
			                <input type="text" class="form-control" name="bairro" id="bairro" value="<?php echo $arrayList['bairro']; ?>">
			            </td>
			        </tr>
			        <tr>
			            <th class="middle bg">Cep </th>
			            <td>
			                <input type="text" class="form-control" name="cep" id="cep" value="<?php echo $arrayList['cep']; ?>">
			            </td>
			            <th class="middle bg">Cidade </th>
			            <td>
			                <input type="text" class="form-control" name="cidade" id="cidade" value="<?php echo $arrayList['cidade']; ?>">
			            </td>
					</tr>
			        <tr>
			            <th class="middle bg">Estado </th>
			            <td>
			                <input type="text" class="form-control" name="estado" id="estado" value="<?php echo $arrayList['estado']; ?>">
			            </td>
			            <th class="middle bg">País </th>
			            <td>
			                <input type="text" class="form-control" name="pais" id="pais" value="<?php echo $arrayList['pais']; ?>">
			            </td>
			       	</tr>
			       	<tr>
			            <th class="top bg">Sobre (Mini currículo) </th>
			            <td colspan="3" class="middle" >
			            	<textarea name="curriculo" id="curriculo" class="form-control" rows="5" ><?php echo trim($arrayList['curriculo']); ?></textarea>
			            </td>
			        </tr>
			        <tr>
			            <th class="middle bg">Áreas de interesse </th>
			            <td colspan="3" class="middle">
			            	<?php
				            	$listInterest = $directIn->listInterest();
				            	if(is_array($listInterest)&&count($listInterest)>0)
				            	{
				                  $listUserSeleciona = $directIn->listUserSeleciona($arrayList['cadastro_idx']);

				                  $interesse_idx = [];
				                  if (is_array($listUserSeleciona)) {
				                  	for($i=0; $i<count($listUserSeleciona); ++$i){
				                    	$interesse_idx[] = $listUserSeleciona[$i]['interesse_idx'];
				                    }
				                  }

				                    foreach($listInterest as $arrayList_inte){
				                        $checked = "";
				                        if(array_search($arrayList_inte['interesse_idx'],$interesse_idx) !== false){ $checked = "checked='checked'";}
				                        echo "
				                        	<div style='float:left; margin-right:15px; margin-bottom:0px;'>
				                        		<div class='checkbox'>
				                        			<label><input type='checkbox' name='area_interesse[]' value='" . $arrayList_inte['interesse_idx'] ."' " . $checked . " /> " . $arrayList_inte['nome'] . "</label>
				                        		</div>
				                        	</div>";
				                    }
				               }
		                    ?>
			            </td>
			        </tr>
			       <tr>
			            <th>
			                <div class='checkbox'>
			                    <label>
			                        <input name="definir" type="checkbox" value="" id="c_senha" onclick="javascript:$('#senha').toggle('slow');" >
			                        <b>Definir senha de acesso:</b>
			                    </label>
			                </div>
			            </th>
			           	<td></td>
			           	<td></td>
			           	<td></td>
			       </tr>
			       <tr style="display:none;" id="senha">
			         <th class="middle bg">Senha </th>
						<td>
		               <input class="form-control" type="password" name="senha" id="senha">
		            </td>
		            <th class="middle bg">Confirme a senha </th>
		            <td>
		                <input class="form-control" type="password" name="senha_confirm" id="senha_confirm">
		            </td>
				   </tr>
			       <tr>
			            <th>
			                <div class='checkbox'>
			                    <label>
			                        <input name="receber_boletim" type="checkbox" value="1" <?php if($arrayList['receber_boletim']){ echo "checked='checked'"; } ?>>
			                        <b>Receber boletim? </b>
			                    </label>
			                </div>
			            </th>
			            <td></td>
			            <td></td>
			            <td></td>
					</tr>
					

					<tr>
						<td colspan="4" ><h3> Certificado do curso </h3></td>
					</tr>
					<tr>
						<th width="20%" class="middle bg" > Emitir certificado </th>
						<td class="middle" colspan="3" >
							<label class="radio-inline">
								<input type="radio" id="" name="certificado_emitir" value="1" onclick="javascript:$('#certificado_imagens').show();" <?php echo ((int)$arrayList['certificado_emitir'] == 1) ? "checked" : ""; ?> > Sim
							</label>
							<label class="radio-inline">
								<input type="radio" id="" name="certificado_emitir" value="0" onclick="javascript:$('#certificado_imagens').hide();" <?php echo ((int)$arrayList['certificado_emitir'] == 0) ? "checked" : ""; ?> > Não
							</label>
						</td>
					</tr>
					<tr>
						<td colspan="4" id="certificado_imagens" style="padding:0px;display:<?php echo ((int)$arrayList['certificado_emitir'] == 1) ? "table-cell;" : "none"; ?>;" >

							<div style="padding:15px 0px;">
								<b>Logomarca e Assinatura</b>
			                    <br>
			                    <small>
			                        Caso o usu&aacute;rio seja produtor ou afiliado, a identidade do mesmo e a assinatura devem ser enviadas abaixo.
			                    </small>
							</div>
							<table  class="table table_form" >
								<tr>
									<th width="20%" class="top bg" >
										Produtor Logo<br/>
										<small>Dimensões de exibição: 350 x 150 pixels</small>
									</th>
									<td colspan="3" >
									<?php if (trim(strtolower($arrayList['certificado_logo']))!=''): ?>
											
										<div class="row" >
											<div class="col-md-6" >
												Atual: <br/>
												<img src="<?php echo "/".PASTA_CONTENT."/".$mod."/".$arrayList['certificado_logo']; ?>" style="width:100%;height:auto;max-width:200px;" alt=""/>
												<div class="certificado_logo_file" style="display:none;" >
													<hr />
													<strong>Enviar nova logo:</strong><br/>
													<input type="file" name="certificado_logo" id="certificado_logo" title="Escolher arquivo" />
												</div>
											</div>
											<div class="col-md-6" >
												<div class="checkbox">
													<label>
														<input name="certificado_logoUpdate" type="checkbox" value="1" onclick="javascript:if(this.checked){$('.certificado_logo_file').slideDown('fast');}else{$('.certificado_logo_file').slideUp('fast');};" /> Trocar a logo atual
													</label>
												</div>
												<div class="checkbox">
													<label>
														<input name="certificado_logoDel" type="checkbox" value="1" /> Excluir a logo atual
													</label>
												</div>
											</div>
										</div>

									<?php else: ?>

										<input type="file" name="certificado_logo" id="certificado_logo" title="Escolher arquivo" />

									<?php endif ?>
									</td>
								</tr>

								<tr>
									<th width="20%" class="top bg" >
										Produtor Assinatura<br/>
										<small>Dimensões de exibição: 350 x 150 pixels</small>
									</th>
									<td colspan="3" >
									<?php if (trim(strtolower($arrayList['certificado_assina']))!=''): ?>
											
										<div class="row" >
											<div class="col-md-6" >
												Assinatura atual: <br/>
												<img src="<?php echo "/".PASTA_CONTENT."/".$mod."/".$arrayList['certificado_assina']; ?>" style="width:100%;height:auto;max-width:200px;" alt=""/>
												<div class="certificado_assina_file" style="display:none;" >
													<hr />
													<strong>Enviar nova assinatura:</strong><br/>
													<input type="file" name="certificado_assina" id="certificado_assina" title="Escolher arquivo" />
												</div>
											</div>
											<div class="col-md-6" >
												<div class="checkbox">
													<label>
														<input name="certificado_assinaUpdate" type="checkbox" value="1" onclick="javascript:if(this.checked){$('.certificado_assina_file').slideDown('fast');}else{$('.certificado_assina_file').slideUp('fast');};" /> Trocar a assinatura atual
													</label>
												</div>
												<div class="checkbox">
													<label>
														<input name="certificado_assinaDel" type="checkbox" value="1" /> Excluir a assinatura atual
													</label>
												</div>
											</div>
										</div>

									<?php else: ?>

										<input type="file" name="certificado_assina" id="certificado_assina" title="Escolher arquivo" />

									<?php endif ?>
									</td>
								</tr>
							</table>
				
						</td>
					</tr>

					<tr>
						<td colspan="4" ><h3> IUGU - Informações da conta </h3></td>
					</tr>
					<tr>
			            <td style="border:0px; padding:0px;" colspan="4" >
			                
			                <div id="produto_iugu_info" class="panel panel-default" >
			                    <div class="panel-body" style="padding:0px;" >
			                        <table class="table table_form" style="margin-bottom:0px;" >
			                            <tr>
			                                <th class="middle bg" width="20%" >Nome da Conta</th>
			                                <td width="30%" >
			                                    <input type="text" class="form-control" name="iugu_split_subcount_name" id="iugu_split_subcount_name" value="<?php echo $arrayList['iugu_split_subcount_name']; ?>">
			                                </td>
			                                <th class="middle bg" >ID da Conta</th>
			                                <td>
			                                    <input type="text" class="form-control" name="iugu_split_account_id" id="iugu_split_account_id" value="<?php echo $arrayList['iugu_split_account_id']; ?>" >
			                                </td>
			                            </tr>
			                            <tr>
			                                <td colspan="4" >
			                                	<label>
			                                		<input type="checkbox" id="iugu_split_api_token_update" name="iugu_split_api_token_update" value="1"
			                                			onclick="javascript:if(this.checked){$('.iugu_api_keys').show();}else{$('.iugu_api_keys').hide();};" 
			                                		> &nbsp; Atualizar tokens da API
			                                	</label>
			                                </td>
			                            </tr>
			                            <tr>
			                                <th class="middle bg" width="20%" >API - TOKEN (Live)</th>
			                                <td colspan="3" >
			                                	Chave atual: <code><?php echo str_pad(substr($arrayList['iugu_split_live_api_token'],0,6),64,"*",STR_PAD_RIGHT); ?></code><br/>
			                                    <input type="text" class="form-control iugu_api_keys" name="iugu_split_live_api_token" id="iugu_split_live_api_token" style="display:none;" placeholder="API - TOKEN (Live)" />
			                                </td>
			                            </tr>
			                            <tr>
			                                <th class="middle bg" >API - TOKEN (Teste)</th>
			                                <td colspan="3" >
			                                	Chave atual: <code><?php echo str_pad(substr($arrayList['iugu_split_test_api_token'],0,6),64,"*",STR_PAD_RIGHT); ?></code><br/>
			                                    <input type="text" class="form-control iugu_api_keys" name="iugu_split_test_api_token" id="iugu_split_test_api_token" style="display:none;" placeholder="API - TOKEN (Teste)" />
			                                </td>
			                            </tr>
			                        </table>
			                    </div>
			                </div>

			            </td>
			        </tr>
			        <tr>
			            <th width="20%" class="middle bg" >Afiliado</th>
			            <td width="30%" class="middle" >
							<label class="radio-inline">
								<input type="radio" id="" name="afiliado_ativo" value="1" <?php if ((int)$arrayList['afiliado_ativo']==1){ echo "checked='checked'"; } ?>
									onclick="javascript:$('#afiliado_cursos_info').slideDown('fast');"
								> Ativo
							</label>
							<label class="radio-inline">
								<input type="radio" id="" name="afiliado_ativo" value="0" <?php if ((int)$arrayList['afiliado_ativo']==0){ echo "checked='checked'"; } ?>
									onclick="javascript:$('#afiliado_cursos_info').slideUp('fast');"
								> Inativo
							</label>
			            </td>
						<?php if ((int)$arrayList['afiliado_ativo']==1): ?>
			            <th width="20%" class="middle bg" >
								C&oacute;digo de afiliado:
			            </th>
			            <td width="30%" class="middle" >
				            <?php echo $arrayList['afiliado_codigo'] ?> - <a href="javascript:;" class="btn btn-sm btn-info" style="padding:0px 10px;" onclick="javascript:$('#afiliado_codigo_edit').slideToggle('fast');"
								> Mudar c&oacute;digo </a>
							<div id="afiliado_codigo_edit" style="display:none;margin-top:5px;" >
								<input type="text" class="form-control" name="afiliado_codigo" id="afiliado_codigo" value="<?php echo $arrayList['afiliado_codigo']; ?>" >
							</div>	
				        </td>
						<?php endif ?>
			        </tr>
			        <tr>
			            <td style="border:0px; padding:0px;" colspan="4" >
			                
			                <div id="afiliado_cursos_info" class="panel panel-default" style="display:<?php echo ((int)$arrayList['afiliado_ativo']==1)?'block':'none'; ?>;" >
			                    <div class="panel-heading" >
			                        Cursos afiliados
			                    </div>
			                    <div class="panel-body" style="padding:0px;" >
			                        <table class="table table_form" style="margin-bottom:0px;" >
			                            <tr>
			                                <th>#</th>
			                                <th>Curso</th>
			                                <th nowrap="" >
			                                	Comissão (%)<br/>
			                                	<small>Definida no curso</small>
			                                </th>
			                                <th nowrap="" >
												Comissão (%)<br/>
												<small>Definida para o afiliado</small>
			                                </th>
			                                <th nowrap="" >
												Desconto (%)
												<a class="fa fa-info-circle ctn-popover" data-content="<p>O desconto na compra permite que o usuário ao realizar sua compra usando o código desse filiado, ele tenha um desconto. Esse desconto não é cumulativo em relação ao cupom de desconto e outras promoções ativas.</p>" data-original-title="Desconto na compra" ></a>
			                                </th>
			                                <th>
												Emitir <br/>
												Certificado
			                                </th>
			                            </tr>
			                            <?php
							            	$afiliadosCursos = $directIn->afiliadoCursos($id);
							            	if(is_array($afiliadosCursos)&&count($afiliadosCursos)>0)
							            	{
												
												foreach($afiliadosCursos as $afiliadosCurso){
							                        $checked = (array_search($afiliadosCurso['curso_idx'],$cadAfCursos) !== false)?"checked='checked'":"";
							                        $checkedEmitirCertificado = ((int)$afiliadosCurso['certificado_emitir'] == 1)?"checked='checked'":"";
							                        echo '
							                        	<tr>
							                                <td><input name="afiliadoCursos[]" type="checkbox" value="'.$afiliadosCurso['curso_idx'].'" '.$checked.' ></td>
							                                <td>
								                                '.$afiliadosCurso['nome'].'
								                                Link de compra do afiliado:
								                                <code><small>https://'.$_SERVER['HTTP_HOST'].'/seu-carrinho?caction=1&cursoId='.$afiliadosCurso['curso_idx'].'&afiliado='.$arrayList['afiliado_codigo'].'</small></code>
							                                </td>
							                                <td>'.number_format($afiliadosCurso['afiliado_comissao'],2,".","").'</td>
							                                
							                                <td width="100" ><input type="text" class="form-control" name="afiliadoComissao_'.$afiliadosCurso['curso_idx'].'" value="'.number_format($afiliadosCurso['comissao'],2,".","").'" ></td>

							                                <td width="100" ><input type="text" class="form-control" name="afiliadoDesconto_'.$afiliadosCurso['curso_idx'].'" value="'.number_format($afiliadosCurso['desconto_afiliado'],2,".","").'" ></td>

							                                <td align="center" ><input name="afiliadoEmitirCertificado_'.$afiliadosCurso['curso_idx'].'" type="checkbox" value="1" '.$checkedEmitirCertificado.' ></td>

							                            </tr>
							                        ';
							                    }

											}
					                    ?>
			                            
			                        </table>
			                    </div>
			                </div>

			            </td>
			        </tr>




			        <tr>
			            <td colspan="4" class="right" >
			                <input type="button" value="Cancelar" class="btn btn-default" onclick="JavaScript:var x = confirm('Você deseja realmente cancelar?\n Os dados não salvos serão perdidos.'); if(x){ location.href='?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>' }">
			                <input type="button" value="Enviar" class="btn btn-primary" data-loading-text="Carregando..."  onclick="JavaScript:checkFormRequire(document.form_dados,'#error-box', checkCadastro);">
			            </td>
			        </tr>
				</table>

			</form>
			<?php
		}
	}else{
		echo "<div class='alert alert-warning'>
               <i class='fa fa-exclamation-triangle'></i>&nbsp;&nbsp;
               Nenhum registro encontrado.
            </div>";
		sis::redirect("?mod=".$mod."&pag=" . $pag , 2);
	}
	?>
</section>
<script>
    // $('#cpf').mask('999.999.999-99');
    jQuery(document).ready(function($) {
		$('.telefone_edit').focusout(function(){
	      var phone, element;
	      element = $(this);
	      element.unmask();
	      phone = element.val().replace(/\D/g, '');
	      element.val(phone);
	      if(phone.length > 10) {
	         element.mask("(99) 99999-9999");
	      } else {
	         element.mask("(99) 9999-99999");
	      }
	   }).trigger('focusout');
		// $("#c_senha").click(function() {
		// 	$("#senha").toggle("slow");
		// });
	});
</script>