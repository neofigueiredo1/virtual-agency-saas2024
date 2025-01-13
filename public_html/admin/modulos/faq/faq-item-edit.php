<?php
	if(!Sis::checkPerm($modulo['codigo'].'-2')){
		Sis::setAlert('Você não tem acesso à este recurso!', 1, '/admin/');
	}
	$fid = isset($_GET['fid']) ? (int)$_GET['fid'] 	: 0;
	$iid = isset($_GET['iid']) ? (int)$_GET['iid'] 	: 0;
	$exe = isset($_POST['exe']) ? (int)$_POST['exe'] 	: 0;
	if ($exe == "1") {
		$directIn->_update($iid);
	}
	if($fid == 0){ Sis::setAlert('Selecione uma área carregar os itens!', 1,'?mod='.$mod.'&pag='.$pag.'&act=list&fid='.(int)$fid.''); }
	if($iid == 0){ Sis::setAlert('Selecione um item!', 1,'?mod='.$mod.'&pag='.$pag.'&act=list&fid='.(int)$fid.''); }

	include_once("faq-model.php");
	include_once("faq-control.php");
?>

<ol class="breadcrumb">
   <li>FAQ</li>
   <li><a href="?mod=<?php echo $mod; ?>&pag=faq">&Aacute;reas de FAQ</a></li>
   <li>
   	<?php
   		$faq = new faq();
   		$nameFaq = $faq->listAll($fid,'nome');
   		if(is_array($nameFaq) && count($nameFaq) > 0){
         	echo '<a href="?mod='.$mod.'&pag=faq-item&fid='.$fid.'&"> Categorias ('.$nameFaq[0]['nome'].')</a> ';
	      }else{
				Sis::setAlert('Selecione uma área carregar os itens!', 1,'?mod='.$mod.'&pag=faq');
	      }
   	?>
   </li>
   <li>Editar pergunta e resposta</li>
</ol>

<?php include_once("faq-menu.php"); ?>

<hr />

<div class="alert alert-danger" id="error-box" style="display:none;"><i class="fa fa-exclamation-triangle"></i>&nbsp;&nbsp;Preencha todos os campos corretamente!</div>

<?php
	if (is_numeric($iid) && $iid != 0) {
		$list = $directIn->listAll($iid);
		if(is_array($list) && count($list) > 0){
?>
<form action="<?php echo Sis::currPageUrl(); ?>" method="post" class="form_dados" name="form_dados" id="form-pagina" >
	<input type="hidden" name="exe" value="1">
	<input type="hidden" name="iid" value="<?php echo $iid; ?>">
	<input type="hidden" name="fid" value="<?php echo $fid; ?>">
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
			<th class="middle bg">Pergunta*</th>
			<td><input type="text" name="pergunta" id="pergunta" class="form-control" data-required="true" value="<?php echo $list[0]['pergunta']; ?>"></td>
		</tr>
		<tr>
			<th width="20%" class="top bg" >Resposta</th>
			<td colspan="3">
				<textarea name="resposta" rows="4" id="resposta" class="ckeditor form-control"><?php echo $list[0]['resposta']; ?></textarea>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td class="right">
				<input type="button" value="Cancelar" class="btn btn-default" onclick="JavaScript:var x = confirm('Você deseja realmente cancelar?\n Os dados não salvos serão perdidos.'); if(x){ location.href='?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=list&fid=<?php echo (int)$_GET['fid']; ?>' }">
				<input type="button" value="Enviar" class="btn btn-primary" data-loading-text="Carregando..."  onclick="JavaScript:checkFormRequire(document.form_dados,'#error-box');">
			</td>
		</tr>
	</table>
</form>
<script>tbl=4;//identifica a base para manipular as imagens</script>
<?php }else{
	echo "
         <div class='alert alert-warning'>
	         <i class='fa fa-exclamation-triangle'></i>&nbsp;&nbsp;
	         Nenhum registro encontrado.
	      </div>";
  			Sis::redirect("?mod=" . $mod . "&pag=" . $pag."&act=list&fid=".(int)$fid, 2);
		}
	}
?>