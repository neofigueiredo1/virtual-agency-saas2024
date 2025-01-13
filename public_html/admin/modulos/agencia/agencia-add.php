<?php
    $enviar = isset($_POST['enviar']) ? $_POST['enviar'] : "";
    if ($enviar != "") {
    	if ($enviar == "1") {
    		$directIn->theInsert();
    	}
    }
?>

<ol class="breadcrumb">
    <li><a href="?mod=<?php echo $mod ?>&pag=cadastro">Cadastros</a></li>
    <li class="active">Adicionar cadastro</li>
</ol>

<div class="btn-group">
    <a class="btn btn-default" href="?mod=<?php echo $mod ?>&pag=cadastro">Cadastros</a>
    <a class="btn btn-default" href="?mod=<?php echo $mod ?>&pag=cadastro&act=add" disabled="disabled">Adicionar cadastro</a>
</div>
<div class="btn-group">
    <a class="btn btn-default" href="?mod=<?php echo $mod ?>&pag=area-interesse">Áreas de interesse</a>
    <a class="btn btn-default" href="?mod=<?php echo $mod ?>&pag=area-interesse&act=add" >Nova área de interesse</a>
</div>
<div class="btn-group">
    <a class="btn btn-success" href="?mod=<?php echo $mod ?>&pag=cadastro&act=exportar">Exportar cadastros</a>
</div>

<hr />

<div class="alert alert-danger" id="error-box" style="display:none;"><i class="fa fa-exclamation-triangle"></i>&nbsp;&nbsp;Preencha todos os campos corretamente!</div>

<form  action="<?php echo sis::currPageUrl(); ?>" method="post" enctype="multipart/form-data" class="form_dados" name="form_dados">
	<input type="hidden" name="enviar" value="1">
	<table class="table table_form">
    	<tr>
            <th width="20%" class="middle bg">Situação</th>
            <td width="30%" class="middle">
                <label class="radio-inline">
                  <input type="radio" id="" name="status" value="1" checked>Ativo
                </label>
                <label class="radio-inline">
                  <input type="radio" id="" name="status" value="0">Inativo
                </label>
                <label class="radio-inline">
                  <input type="radio" id="" name="status" value="4" >Newsletter
                </label>
            </td>
            <?php if (false): ?>
                <th width="20%" class="middle bg" >Perfil</th>
                <td width="30%" class="middle" >
                    <label class="radio-inline" >
                        <input type="radio" id="" name="perfil" value="0" checked
                            onclick="javascript:$('#produto_iugu_info').slideUp('fast');" 
                        > Aluno
                    </label>
                    <label class="radio-inline">
                        <input type="radio" id="" name="perfil" value="1"
                            onclick="javascript:$('#produto_iugu_info').slideDown('fast');" 
                        > Produtor
                    </label>
                    <label class="radio-inline">
                        <input type="radio" id="" name="perfil" value="2"
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
                    <input type="radio" id="" name="lp_active" value="1" checked >Ativo
                </label>
                <label class="radio-inline" >
                    <input type="radio" id="" name="lp_active" value="0" >Inativo
                </label>
            </td>
            <th width="20%" class="middle bg" >Quota de armazenamento (MB)</th>
            <td width="30%" class="middle" >
                <input type="text" class="form-control" name="lp_quota_mb" id="lp_quota_mb" />
            </td>
        </tr>
        <tr>
            <th width="20%" class="middle bg" >Nome completo</th>
            <td colspan="3">
                <input type="text" class="form-control" name="nome_informal" id="nome_informal" data-required="true" />
            </td>
        </tr>
    	<tr>
			<th width="20%" class="middle bg">
                Nome informal
                <a class="fa fa-info-circle ctn-popover" data-content="<p>Nome como prefere ser chamado.</p>" data-original-title="Nome informal" ></a>
            </th>
			<td>
                <input type="text" class="form-control" name="nome_informal" id="nome_informal">
            </td>
            <th width="20%" class="middle bg" >CPF/CNPJ (somente números)</th>
            <td>
                <input type="text" class="form-control cpf_cnpj" name="cpf_cnpj" id="cpf_cnpj">
            </td>
		</tr>
        <tr>
            <th class="middle bg">E-mail: </th>
            <td>
                <input type="email" class="form-control" name="email" id="email" data-required="true">
            </td>
            <th class="middle bg">Telefone residencial </th>
            <td>
                <input type="text" class="form-control telefone" name="telefone_resid" id="telefone_resid">
            </td>
		</tr>
        <tr>
			<th class="middle bg">Telefone comercial </th>
            <td>
                <input type="text" class="form-control telefone" name="telefone_comer" id="telefone_comer">
            </td>
            <th class="middle bg">Celular </th>
            <td>
                <input type="text" class="form-control telefone" name="celular" id="celular">
            </td>
        </tr>
        <tr>
            <th class="middle bg">Gênero </th>
            <td class="middle">
                <label class="radio-inline">
                  <input type="radio" id="" name="genero" value="1" checked>Masculino
                </label>
                <label class="radio-inline">
                  <input type="radio" id="" name="genero" value="2">Feminino
                </label>
            </td>
            <th class="middle bg">Data de nascimento </th>
            <td>
                <input type="date" class="form-control" name="data_nasc" id="data_nasc" >
            </td>
        </tr>
        <tr>
            <th class="middle bg">Endereço </th>
            <td>
                <input type="text" class="form-control" name="endereco" id="endereco">
            </td>
            <th class="middle bg">Nº </th>
            <td>
                <input type="text" class="form-control" name="numero" id="numero">
            </td>
        </tr>
        <tr>
            <th class="middle bg">Complemento </th>
            <td>
                <input type="text" class="form-control" name="complemento" id="complemento">
            </td>
            <th class="middle bg">Bairro </th>
            <td>
                <input type="text" class="form-control" name="bairro" id="bairro">
            </td>
        </tr>
        <tr>
            <th class="middle bg">Cep </th>
            <td>
                <input type="text" class="form-control" name="cep" id="cep">
            </td>
            <th class="middle bg">Cidade </th>
            <td>
                <input type="text" class="form-control" name="cidade" id="cidade">
            </td>
		</tr>
        <tr>
            <th class="middle bg">Estado </th>
            <td>
                <input type="text" class="form-control" name="estado" id="estado">
            </td>
            <th class="middle bg">País </th>
            <td>
                <input type="text" class="form-control" name="pais" id="pais">
            </td>
       </tr>
        <tr>
            <th class="middle bg">Áreas de interesse </th>
            <td colspan="3" class="middle">
                <?php
                    $lista = $directIn->listInterest();
                    if(is_array($lista)&&count($lista)>0){
                        foreach($lista as $arrayListInteresse){
                        echo "<div style='float:left; margin-right:15px; margin-bottom:0px;'>
                                <div class='checkbox'>
                                    <label><input type='checkbox' name='area_interesse[]' value='" . $arrayListInteresse['interesse_idx'] ."' /> " . $arrayListInteresse['nome'] . "</label>
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
                        <input name="definir" type="checkbox" value="" id="c_senha" onclick="javascript:if(this.checked){$('#senha').fadeIn('fast');}else{$('#senha').fadeOut('fast');};" >
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
                        <input name="receber_boletim" type="checkbox" value="1"/>
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
                    <input type="radio" id="" name="certificado_emitir" value="1" onclick="javascript:$('#certificado_imagens').show();" > Sim
                </label>
                <label class="radio-inline">
                    <input type="radio" id="" name="certificado_emitir" value="0" onclick="javascript:$('#certificado_imagens').hide();" checked > Não
                </label>
            </td>
        </tr>
        <tr>
            <td colspan="4" id="certificado_imagens" style="padding:0px;display:none;" >
                <div style="padding:15px 0px;" >
                    <b>Logomarca e Assinatura</b>
                    <br>
                    <small>
                        Caso o usu&aacute;rio seja produtor ou afiliado, a identidade do mesmo e a assinatura devem ser enviadas abaixo.
                    </small>
                </div>
                <table  class="table table_form" >
                    <tr>
                        <th width="20%" class="top bg" >Logo</th>
                        <td colspan="3" >
                            <div class="fl_left">
                                Dimensões:
                                Largura e altura: 350 x 150 pixels
                                <input type="file" name="certificado_logo" id="certificado_logo" title="Escolher arquivo" />
                            </div>
                            <div class="clear"></div>
                        </td>
                    </tr>
                    <tr>
                        <th width="20%" class="top bg" >Assinatura</th>
                        <td colspan="3" >
                            <div class="fl_left">
                                Dimensões:
                                Largura e altura: 350 x 150 pixels
                                <input type="file" name="certificado_assina" id="certificado_assina" title="Escolher arquivo" />
                            </div>
                            <div class="clear"></div>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>

        <tr>
            <td style="border:0px; padding:0px;" colspan="4" >
                
                <div id="produto_iugu_info" class="panel panel-default" style="display:none;" >
                    <div class="panel-heading" >
                        IUGU - Informações da conta
                    </div>
                    <div class="panel-body" style="padding:0px;" >
                        <table class="table table_form" style="margin-bottom:0px;" >
                            <tr>
                                <th class="middle bg" width="20%" >Nome da Conta</th>
                                <td width="30%" >
                                    <input type="text" class="form-control" name="iugu_split_subcount_name" id="iugu_split_subcount_name">
                                </td>
                                <th class="middle bg" >ID da Conta</th>
                                <td>
                                    <input type="text" class="form-control" name="iugu_split_account_id" id="iugu_split_account_id">
                                </td>
                            </tr>
                            <tr>
                                <th class="middle bg" width="20%"  >API - TOKEN (Live)</th>
                                <td colspan="3" >
                                    <input type="text" class="form-control" name="iugu_split_live_api_token" id="iugu_split_live_api_token">
                                </td>
                            </tr>
                            <tr>
                                <th class="middle bg" >API - TOKEN (Teste)</th>
                                <td colspan="3" >
                                    <input type="text" class="form-control" name="iugu_split_test_api_token" id="iugu_split_test_api_token">
                                </td>
                            </tr>
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
<script>
    //$('#cpf').mask('999.999.999-99');
</script>