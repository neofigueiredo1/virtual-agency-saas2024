<?php
	$send = isset($_POST['enviar']) && is_numeric($_POST['enviar']) ? (int)$_POST['enviar'] : 0 ;

	$listVarSis = $directIn->sisSelect();
	
	if ($send === 1) {
		if (is_array($listVarSis) && count($listVarSis) == 15) {
			$directIn->sisConfigUpdate();
		}else{
			$directIn->sisConfigInsert();
		}
	}
?>

<ol class="breadcrumb">
	<li><a href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>">Configurações</a></li>
	<li>Configurações do sistema</li>
</ol>

<div class="btn-group">
   <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>">Lista de variáveis</a>
   <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=add">Criar nova variável</a>
   <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=hist-list">Histórico</a>
   <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=sis-list" disabled="disabled">Configurações do sistema</a>
</div>

<hr>

<div class="alert alert-danger" id="error-box" style="display:none;"><i class="fa fa-exclamation-triangle"></i>&nbsp;&nbsp;Preencha todos os campos corretamente!</div>

<form action="<?php echo Sis::currPageUrl(); ?>" method="post" class="form_dados" name="form_dados" id="form-pagina" enctype="multipart/form-data">
	<input type="hidden" name="enviar" value="1">

	<ol class="breadcrumb table_form">
  		<li><b>Sobre o cliente</b></li>
  	</ol>

	<table class="table table_form">
		<tr>
			<th width="30%" class="middle">Nome do cliente</th>
			<td colspan="3">
				<input type="hidden" name="dadonome[]" value="CLI_NOME">
				<input type="text" name="dado[]" class="form-control" value="<?php echo $directIn->retVarValue("CLI_NOME"); ?>">
			</td>
		</tr>

		<tr>
			<th class="middle">
				Título do site&nbsp;
				<a class="fa fa-info-circle ctn-popover" style="cursor: help;" data-original-title="Titulo do site" data-content="Informação exibida na tag do título do site/barra da título do navegador"></a>
			</th>
			<td colspan="3">
				<input type="hidden" name="dadonome[]" value="CLI_TITULO">
				<input type="text" name="dado[]" class="form-control" value="<?php echo $directIn->retVarValue("CLI_TITULO"); ?>">
			</td>
		</tr>

		<tr>
			<th class="middle">
				Url do site&nbsp;
			</th>
			<td colspan="3">
				<input type="hidden" name="dadonome[]" value="CLI_URL">
				<input type="text" name="dado[]" class="form-control" placeholder="www.seusite.com.br" value="<?php echo $directIn->retVarValue("CLI_URL"); ?>">
			</td>
		</tr>

		<tr>
			<th class="middle">
				E-mail de contato&nbsp;
			</th>
			<td colspan="">
				<input type="hidden" name="dadonome[]" value="CLI_MAIL_CONTATO">
				<input type="text" name="dado[]" class="form-control" placeholder="contato@meusite.com.br" value="<?php echo $directIn->retVarValue("CLI_MAIL_CONTATO"); ?>">
			</td>
		</tr>

		<tr>
			<th class="top">
				Descrição do site&nbsp;
			</th>
			<td colspan="">
				<input type="hidden" name="dadonome[]" value="CLI_DESCRICAO">
				<textarea rows="3" name="dado[]" class="form-control"><?php echo $directIn->retVarValue("CLI_DESCRICAO"); ?></textarea>
			</td>
		</tr>

		<tr>
			<th class="top">
				Palavras-chave do site&nbsp;
			</th>
			<td colspan="">
				<input type="hidden" name="dadonome[]" value="CLI_KEYWORDS">
				<textarea rows="3" name="dado[]" class="form-control"><?php echo $directIn->retVarValue("CLI_KEYWORDS"); ?></textarea>
			</td>
		</tr>

	</table>

	<ol class="breadcrumb table_form">
  		<li><b>Para o administrador</b></li>
  	</ol>

	<table class="table table_form">

		<tr>
			<th width="30%" class="top">
				Logomarca do cliente&nbsp;
				<a class="fa fa-info-circle ctn-popover" style="cursor: help;" data-original-title="Logomarca do cliente nas dimensões: 247px X 86px (largura X altura)" data-content="Arquivo com a marca"></a>
			</th>
			<td colspan="">
				<input type="hidden" name="dadonome[]" value="CLI_LOGO">
				<input type="hidden" name="dado[]" value="">
				<input type="hidden" name="logo_old" value="<?php echo $directIn->retVarValue("CLI_LOGO"); ?>">
				<?php
					$imagem = $directIn->retVarValue("CLI_LOGO");
					if ($imagem != "" && trim(strtolower($imagem))!="null" ) {
					echo '<span style="float: left">Sua imagem atual é:</span>
							<div class="clearfix"></div>
							<img src="/admin/public/images/' . $imagem . '" style="float: left;" width="200">
							<div class="clearfix"></div>
							<br />
							<span style="margin-bottom: 5px; display: block;">Enviar uma nova:</span>
					';
					}
				?>
				<input type="file" name="arquivo_logo" />
			</td>
		</tr>

	</table>

	<ol class="breadcrumb table_form">
  		<li><b>Configurações de sistema</b></li>
  	</ol>

	<table class="table table_form">

		<tr>
			<th width="30%" class="middle">
				Cota do servidor&nbsp;
				<a class="fa fa-info-circle ctn-popover" style="cursor: help;" data-original-title="Cota do servidor" data-content="Define a quantidade em MB de espaço no servidor."></a>
			</th>
			<td colspan="3">
				<input type="hidden" name="dadonome[]" value="CLI_COTA">
				<input type="text" name="dado[]" id="CLI_COTA" class="form-control" placeholder="1024 MB" value="<?php echo $directIn->retVarValue("CLI_COTA"); ?>">
			</td>
		</tr>

		<tr>
			<th width="30%" class="middle">
				Nome do sistema&nbsp;
			</th>
			<td colspan="3">
				<?php echo $directIn->retVarValue("SIS_NOME"); ?>
				<!--input type="hidden" name="dadonome[]" value="SIS_NOME">
				<input type="text" name="dado[]" id="SIS_NOME" class="form-control" disabled-->
			</td>
		</tr>

		<tr>
			<th width="30%" class="middle">
				Versão do sistema&nbsp;
			</th>
			<td colspan="3">
				<?php echo $directIn->retVarValue("SIS_VERSAO"); ?>
				<!--input type="hidden" name="dadonome[]" value="SIS_VERSAO">
				<input type="text" name="dado[]" id="SIS_VERSAO" class="form-control" disabled-->
			</td>
		</tr>

		<tr>
			<th width="30%" class="middle">
				SMTP - Host&nbsp;
			</th>
			<td colspan="3">
				<input type="hidden" name="dadonome[]" value="CLI_SMTP_HOST">
				<input type="text" name="dado[]" id="CLI_SMTP_HOST" class="form-control" value="<?php echo $directIn->retVarValue("CLI_SMTP_HOST"); ?>">
			</td>
		</tr>

		<tr>
			<th width="30%" class="middle">
				SMTP - Porta&nbsp;
			</th>
			<td colspan="3">
				<input type="hidden" name="dadonome[]" value="CLI_SMTP_PORTA">
				<input type="text" name="dado[]" id="CLI_SMTP_PORTA" class="form-control" placeholder="Ex.: 587" value="<?php echo $directIn->retVarValue("CLI_SMTP_PORTA"); ?>">
			</td>
		</tr>

		<tr>
			<th width="30%" class="middle">
				SMTP - Tipo de conexão&nbsp;
			</th>
			<td colspan="3">
				<input type="hidden" name="dadonome[]" value="CLI_SMTP_CONEXAO">
				<input type="text" name="dado[]" class="form-control" placeholder="Os tipos de conexão mais utilizados são TLS e SSL" value="<?php echo $directIn->retVarValue("CLI_SMTP_CONEXAO"); ?>">
			</td>
		</tr>

		<tr>
			<th width="30%" class="middle">
				SMTP - Host E-mail&nbsp;
			</th>
			<td colspan="3">
				<input type="hidden" name="dadonome[]" value="CLI_SMTP_MAIL">
				<input type="text" name="dado[]" class="form-control" placeholder="meusite@meusite.com.br" value="<?php echo $directIn->retVarValue("CLI_SMTP_MAIL"); ?>">
			</td>
		</tr>

		<tr>
			<th width="30%" class="middle">
				SMTP - Host Senha&nbsp;
			</th>
			<td colspan="3">
				<input type="hidden" name="dadonome[]" value="CLI_SMTP_PASS">
				<input type="text" name="dado[]" class="form-control" value="<?php echo $directIn->retVarValue("CLI_SMTP_PASS"); ?>">
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
<div class="clearfix"></div>