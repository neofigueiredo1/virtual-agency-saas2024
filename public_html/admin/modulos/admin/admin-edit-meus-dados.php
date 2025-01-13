<?php
	$send = isset($_POST['enviar']) ? (int)$_POST['enviar'] : 0 ;
	if ($send === 1) $directIn->updateMyAccount();
?>

<ul class="breadcrumb" >
	<?php if($_SESSION['usuario']['nivel'] < 3){ ?>
		<li><a href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>">Administradores</a></li>
	<?php }else{ ?>
		<li>Administradores</li>
	<?php } ?>
	<li>Editar meus dados</li>
</ul>

<div class="btn-group" >
	<?php
		if($_SESSION['usuario']['nivel'] < 3){
	?>
	<a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>">Listar administradores</a>
	<a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=add">Criar novo administrador</a>
	<?php
		}
	?>
	<a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=edit-meus-dados" disabled="disabled">Editar meus dados</a>
	<a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=edit-pass" >Alterar minha senha</a>
</div>

<hr />

<div class="alert alert-danger" id="error-box" style="display:none;"><i class="fa fa-exclamation-triangle"></i>&nbsp;&nbsp;Preencha todos os campos corretamente!</div>

<?php
	$list = $directIn->listSelected($_SESSION['usuario']['id']);
	if(is_array($list) && count($list) > 0){
		foreach ($list as $arrayList){
			?>
			<form action="<?php echo sis::currPageUrl(); ?>" method="post" class="form_dados" name="form_dados">
				<input type="hidden" name="id" value="<?php echo $_SESSION['usuario']['id']; ?>">
				<input type="hidden" name="nivel" id="nivel-admin" value="<?php echo $arrayList['nivel']?>">
				<input type="hidden" name="enviar" value="1">
				<table class="table table_form" border="0">
					<tr>
						<th width="25%" class="middle bg">Nome*</th>
						<td colspan="3">
                     <input type="text" name="nome" id="nome" class="form-control" data-required="true" value="<?php echo $arrayList['nome'] ?>">
                  </td>
					</tr>

					<tr>
						<th width="25%" class="middle bg">E-mail*</th>
						<td width="30%">
							<input type="text" name="email" id="email" class="form-control" data-required="true" value="<?php echo $arrayList['email'] ?>">
						</td>
						<th width="25%" class="middle bg">Nome de usuário*</th>
						<td width="30%">
							<input type="text" name="login" id="login" class="form-control" data-required="true" value="<?php echo $arrayList['login'] ?>">
						</td>
					</tr>

					<tr>
			     		<td>&nbsp;</td>
				     	<td colspan="3" class="right">
				     		<input type="button" value="Cancelar" class="btn btn-default" onclick="JavaScript:var x = confirm('Você deseja realmente cancelar?\n Os dados não salvos serão perdidos.'); if(x){ location.href='?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>' }">
				     		<input type="button" value="Enviar" class="btn btn-primary" data-loading-text="Carregando..."  onclick="JavaScript:checkFormRequire(document.form_dados,'#error-box', checkEditMeusDados);">
				     	</td>
			    	</tr>
				</table>
			</form>
			<?php
		}
	} else {
		echo "
            <div class='alert alert-warning'>
               <i class='fa fa-exclamation-triangle'></i>&nbsp;&nbsp;Nenhum registro encontrado.
            </div>";
        Sis::redirect("?mod=" . $mod . "&pag=" . $pag, 2);
	}
?>