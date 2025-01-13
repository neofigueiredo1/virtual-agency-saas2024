<?php
	$enviar = isset($_POST['enviar']) ? $_POST['enviar'] : "";
	if ($enviar != "") {
		if ($enviar == "1") {
			$directIn->theUpdate();
		}
	}
?>

<ol class="breadcrumb">
   <li><a href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>">Configurações</a></li>
   <li>Editar variável</li>
</ol>

<div class="btn-group">
   <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>">Lista de variáveis</a>
   <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=add">Criar nova variável</a>
   <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=hist-list">Histórico</a>
   <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=sis-list">Configurações do sistema</a>
</div>
<hr />

<div class="alert alert-danger" id="error-box" style="display:none;"><i class="fa fa-exclamation-triangle"></i>&nbsp;&nbsp;Preencha todos os campos corretamente!</div>

<?php
	if (isset($_GET['v_id']) && is_numeric($_GET['v_id']) && $_GET['v_id']!=0) {
		$lista = $directIn->listSelected($_GET['v_id']);
		if(isset($lista) && $lista !== false){
			foreach ($lista as $lista_arr){
				?>
				<form action="<?php echo sis::currPageUrl(); ?>" method="post" class="form_dados" name="form_dados">
					<input type="hidden" name="v_id" value="<?php echo $_GET['v_id']; ?>">
					<input type="hidden" name="enviar" value="1">
					<table class="table table_form">
						<tr>
							<th width="20%" class="middle bg">Situação</th>
							<td class="middle">
								<label class="radio-inline">
								  <input type="radio" id="" name="status" value="1" <?php if ($lista_arr['status']==1) {echo "checked=checked";} ?>>Ativo
								</label>
								<label class="radio-inline">
								  <input type="radio" id="" name="status" value="0" <?php if ($lista_arr['status']==0) {echo "checked=checked";} ?>>Inativo
								</label>
							</td>
						</tr>

						<tr>
							<th class="middle bg">Nome*</th>
							<td><input type="text" name="nome" id="nome" class="form-control" data-required="true" value="<?php echo $lista_arr['nome'] ?>"></td>
						</tr>

						<tr>
							<th class="top bg">Valor</th>
							<td>
								<textarea name="valor" id="valor" rows="4" class="form-control"><?php echo $lista_arr['valor'] ?></textarea>
							</td>
						</tr>

						<tr>
							<th class="top bg">Descrição</th>
							<td>
								<textarea name="descricao" id="descricao" rows="4" class="form-control"><?php echo $lista_arr['descricao'] ?></textarea>
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
				<?php
			}
		}else{
			echo "<div class='alert alert-warning' ><i class='fa fa-warning'></i> Variável não encontrada.</div>";
			Sis::redirect("?mod=".$_GET['mod']."&pag=".$_GET['pag'],2);
		}
	} else {
		echo "<div class='alert alert-warning' ><i class='fa fa-warning'></i> Variável não encontrada.</div>";
		Sis::redirect("?mod=".$_GET['mod']."&pag=".$_GET['pag'],2);
	}
?>