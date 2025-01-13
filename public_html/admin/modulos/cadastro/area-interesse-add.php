<?php
$exe = isset($_POST['exe']) ? (int)$_POST['exe'] : 0 ;
if ($exe === 1){ $directIn->theInsert(); }
?>

<ol class="breadcrumb">
	<li><a href="?mod=cadastro&pag=cadastro">Cadastros</a></li>
	<li><a href="?mod=cadastro&pag=area-interesse" >Áreas de interesse</a></li>
	<li>Nova área de interesse</li>
</ol>

<div class="btn-group">
  	<a class="btn btn-default" href="?mod=<?php echo $mod ?>&pag=cadastro" >Cadastros</a>
  	<a class="btn btn-default" href="?mod=<?php echo $mod ?>&pag=cadastro&act=add" >Adicionar cadastro</a>
</div>
<div class="btn-group">
  	<a class="btn btn-default" href="?mod=<?php echo $mod ?>&pag=area-interesse" >Áreas de interesse</a>
  	<a class="btn btn-default" href="?mod=<?php echo $mod ?>&pag=area-interesse&act=add" disabled="disabled"  >Nova área de interesse</a>
</div>
<div class="btn-group">
  	<a class="btn btn-success" href="?mod=<?php echo $mod ?>&pag=cadastro&act=exportar">Exportar cadastros</a>
</div>

<hr />

<div class="alert alert-danger" id="error-box" style="display:none;"><i class="fa fa-exclamation-triangle"></i>&nbsp;&nbsp;Preencha todos os campos corretamente!</div>

<form action="<?php echo Sis::currPageUrl(); ?>" name="form_dados" method="post" id="form_dados" >
	<input type="hidden" name="exe" value="1">
	<?php
	$ranking = 0 ;
	$last_cat = $directIn->getLastCat();
	if(isset($last_cat) && $last_cat !== false){
		foreach ($last_cat as $last_cat_arr){ $ranking = $last_cat_arr['ranking']+5; }
	}
	?>
	<input type="hidden" name="ranking" value="<?php echo $ranking; ?>">
	<table class="table table_form">
		<tr>
			<th width="25%" class="middle bg">Status</th>
			<td width="75%" class="middle" nowrap="nowrap">
				<label class="radio-inline" for="status1"><input type="radio" name="status" id="status1" value="1" checked="checked" > Ativo</label>
				<label class="radio-inline" for="status0"><input type="radio" name="status" id="status0" value="0" > Inativo</label>
			</td>
		</tr>

		<tr>
			<th class="middle bg">Nome</th>
			<td class="middle" ><input type="text" name="nome" id="nome" class="form-control" data-required="true" ></td>
		</tr>

		<tr>
			<th class="middle bg">Descrição</th>
			<td class="middle" ><textarea name="descricao" id="descricao" class="form-control" ></textarea></td>
		</tr>

		<tr>
			<td colspan="2" class="right" >

				<input type="button" value="Cancelar" class="btn btn-default" onclick="JavaScript:var x = confirm('Você deseja realmente cancelar?\n Os dados não salvos serão perdidos.'); if(x){ location.href='?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>' }">
	     		<input type="button" value="Enviar" class="btn btn-primary" data-loading-text="Carregando..."  onclick="JavaScript:checkFormRequire(document.form_dados,'#error-box');">

			</td>
		</tr>
	</table>
</form>