<?php
$exe = isset($_POST['exe']) ? (int)$_POST['exe'] : 0 ;
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0 ;
if ($exe === 1){ $directIn->theUpdate(); }
?>

<ol class="breadcrumb">
	<li><a href="?mod=cadastro&pag=cadastro">Cadastros</a></li>
	<li><a href="?mod=cadastro&pag=area-interesse" >Áreas de interesse</a></li>
	<li>Editar área de interesse</li>
</ol>

<div class="btn-group">
  	<a class="btn btn-default" href="?mod=<?php echo $mod ?>&pag=cadastro" >Cadastros</a>
  	<a class="btn btn-default" href="?mod=<?php echo $mod ?>&pag=cadastro&act=add" >Adicionar cadastro</a>
</div>
<div class="btn-group">
  	<a class="btn btn-default" href="?mod=<?php echo $mod ?>&pag=area-interesse" >Áreas de interesse</a>
  	<a class="btn btn-default" href="?mod=<?php echo $mod ?>&pag=area-interesse&act=add" >Nova área de interesse</a>
</div>
<div class="btn-group">
  	<a class="btn btn-success" href="?mod=<?php echo $mod ?>&pag=cadastro&act=exportar">Exportar cadastros</a>
</div>

<hr />

<div class="alert alert-danger" id="error-box" style="display:none;"><i class="fa fa-exclamation-triangle"></i>&nbsp;&nbsp;Preencha todos os campos corretamente!</div>

<?php
	$lista = $directIn->listSelected($id);
	if(isset($lista) && $lista !== false){
		foreach ($lista as $lista_arr){
			?>
			<form action="<?php echo Sis::currPageUrl(); ?>" name="form_dados" method="post" id="form_dados" >
				<input type="hidden" name="exe" value="1" />
				<input type="hidden" name="id" value="<?php echo $id ?>" >
				<table class="table table_form">
					<tr>
						<th width="25%" class="middle bg">Status</th>
						<td width="75%" nowrap="nowrap">
							<label class="radio-inline" for="status1"><input type="radio" name="status" id="status1" value="1" <?php if ((int)$lista_arr['status']===1) echo "checked=checked"; ?> >Ativo</label>
							<label class="radio-inline" for="status0"><input type="radio" name="status" id="status0" value="0" <?php if ((int)$lista_arr['status']===0) echo "checked=checked"; ?> >Inativo</label>
						</td>
					</tr>
					<tr>
						<th class="middle bg">Nome</th>

						<td><input type="text" name="nome" id="nome" value="<?php echo $lista_arr['nome'] ?>" class="form-control" data-required="true" ></td>
					</tr>
					<tr>
						<th class="middle bg">Descrição</th>
						<td><textarea name="descricao" id="descricao" class="form-control" ><?php echo $lista_arr['descricao'] ?></textarea></td>
					</tr>
					<tr>
						<td colspan="2" class="right" >

							<input type="button" value="Cancelar" class="btn btn-default" onclick="JavaScript:var x = confirm('Você deseja realmente cancelar?\n Os dados não salvos serão perdidos.'); if(x){ location.href='?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>' }">
				     		<input type="button" value="Enviar" class="btn btn-primary" data-loading-text="Carregando..."  onclick="JavaScript:checkFormRequire(document.form_dados,'#error-box');">

						</td>
					</tr>
				</table>
			</form>
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