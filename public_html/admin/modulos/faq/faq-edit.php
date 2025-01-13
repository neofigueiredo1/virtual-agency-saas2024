<?php
	if(!Sis::checkPerm($modulo['codigo'].'-1') && !Sis::checkPerm($modulo['codigo'].'-2')){
		Sis::setAlert('Você não tem acesso à este recurso!', 1, '/admin/');
	}
	$fid   = isset($_GET['fid']) ? (int)$_GET['fid'] : "";
	$send = isset($_POST['exe']) ? (int)$_POST['exe'] : "";
	if ($send == "1") {
		$directIn->_update();
	}
?>

<ol class="breadcrumb">
   <li>FAQ</li>
   <li><a href="?mod=<?php echo $mod; ?>&pag=faq">&Aacute;reas de FAQ</a></li>
   <li>Editar &aacute;rea</li>
</ol>

<?php include_once("faq-menu.php"); ?>

<hr />

<div class="alert alert-danger" id="error-box" style="display:none;"><i class="fa fa-exclamation-triangle"></i>&nbsp;&nbsp;Preencha todos os campos corretamente!</div>

<?php
	if (is_numeric($fid) && $fid != 0) {
		$list = $directIn->listAll($fid);
		if(is_array($list) && count($list) > 0){
?>
<form action="<?php echo Sis::currPageUrl(); ?>" method="post" class="form_dados" name="form_dados" id="form-pagina" >
	<input type="hidden" name="exe" value="1">
	<input type="hidden" name="fid" value="<?php echo $list[0]['faq_idx']; ?>">
	<table class="table table_form">
		<tr>
			<th width="20%" class="middle bg">Situação</th>
			<td class="middle">
				<label class="radio-inline">
				  <input type="radio" id="" name="status" value="1" <?php echo ($list[0]['status'] == "1") ? "checked" : ""; ?>>Ativo
				</label>
				<label class="radio-inline">
				  <input type="radio" id="" name="status" value="0" <?php echo ($list[0]['status'] == "0") ? "checked" : ""; ?>>Inativo
				</label>
			</td>
		</tr>
		<tr>
			<th class="middle bg">Nome*</th>
			<td><input type="text" name="nome" id="nome" value="<?php echo $list[0]['nome']; ?>" class="form-control" data-required="true"></td>
		</tr>
		<tr>
			<th class="top bg">Descrição</th>
			<td>
				<textarea name="descricao" id="descricao" rows="4" class="form-control" ><?php echo $list[0]['descricao']; ?></textarea>
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
<?php }else{
	echo "
         <div class='alert alert-warning'>
	         <i class='fa fa-exclamation-triangle'></i>&nbsp;&nbsp;
	         Nenhum registro encontrado.
	      </div>";
  			Sis::redirect("?mod=" . $mod . "&pag=" . $pag, 2);
		}
	}
?>