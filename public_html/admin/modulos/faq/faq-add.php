<?php
	if(!Sis::checkPerm($modulo['codigo'].'-2')){
		Sis::setAlert('Você não tem acesso à este recurso!', 1, '/admin/');
	}
	$send = isset($_POST['exe']) ? (int)$_POST['exe'] : "";
	if ($send == "1") {
		$directIn->_insert();
	}
?>

<ol class="breadcrumb">
   <li>FAQ</li>
   <li><a href="?mod=<?php echo $mod; ?>&pag=faq">&Aacute;reas de FAQ</a></li>
   <li>Nova &aacute;rea</li>
</ol>

<?php include_once("faq-menu.php"); ?>

<hr />

<div class="alert alert-danger" id="error-box" style="display:none;"><i class="fa fa-exclamation-triangle"></i>&nbsp;&nbsp;Preencha todos os campos corretamente!</div>

<form action="<?php echo Sis::currPageUrl(); ?>" method="post" class="form_dados" name="form_dados" id="form-pagina" enctype="multipart/form-data" >
	<input type="hidden" name="exe" value="1">
	<table class="table table_form">
		<tr>
			<th width="20%" class="middle bg">Situação</th>
			<td class="middle">
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
			<td><input type="text" name="nome" id="nome" class="form-control" data-required="true"></td>
		</tr>
		<tr>
			<th class="top bg" >Descrição</th>
			<td>
				<textarea name="descricao" id="descricao" rows="4" class="form-control" ></textarea>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td class="right">
				<input type="button" value="Cancelar" class="btn btn-default" onclick="JavaScript:var x = confirm('Você deseja realmente cancelar?\n Os dados não salvos serão perdidos.'); if(x){ location.href='?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>' }">
				<input type="button" value="Enviar" class="btn btn-primary" data-loading-text="Carregando..."  onclick="JavaScript:checkFormRequire(document.form_dados,'#error-box');">
			</td>
		</tr>
	</table>
</form>