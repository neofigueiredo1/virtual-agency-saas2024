<?php
	Auth::checkPermission("2");

	$enviar = isset($_POST['enviar']) ? (int)$_POST['enviar'] : 0;
	if ($enviar === 1){
		$directIn->_insert();
	}
?>

<ul class="breadcrumb" >
	<li><a href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>">Administradores</a></li>
	<li>Criar novo administrador</li>
</ul>

<div class="btn-group" >
	<a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>">Listar administradores</a>
	<a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=add" disabled="disabled">Criar novo administrador</a>
	<a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=edit-meus-dados">Editar meus dados</a>
	<a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=edit-pass" >Alterar minha senha</a>
</div>

<hr />

<div class="alert alert-danger" id="error-box" style="display:none;"><i class="fa fa-exclamation-triangle"></i>&nbsp;&nbsp;Preencha todos os campos corretamente!</div>

	<form action="<?php echo Sis::currPageUrl(); ?>" name="form_dados" id="form_dados" method="post" class="admin_form" >
		<input type="hidden" name="enviar" value="1">
		<table class="table table_form" border="0">
			<tr >
				<th class="middle bg">Situação</th>
				<td class="middle" colspan="3">
					<label class="radio-inline">
					  <input type="radio" id="" name="status" value="1" checked>Ativo
					</label>
					<label class="radio-inline">
					  <input type="radio" id="" name="status" value="0">Inativo
					</label>
				</td>
			</tr>
			<tr>
				<th class="middle bg" >Nome</th>
				<td colspan="3">
					<input type="text" name="nome" id="nome" data-required="true" class="form-control">
				</td>
			</tr>
			<tr>
				<th class="middle bg">Email</th>
				<td width="35%" >
					<input type="text" name="email" id="email" data-required="true"  class="form-control">
				</td>
				<th class="middle bg">Nome para login</th>
				<td width="35%" >
					<input type="text" name="login" id="login" data-required="true"  class="form-control">
				</td>
			</tr>
			<tr>
				<th class="middle bg" >Senha</th>
				<td><input type="password" name="senha" id="senha"  data-required="true"  class="form-control"></td>
				<th nowrap class="middle bg" >Confirme a senha</th>
				<td><input type="password" name="senha_confirm" id="senha_confirm" data-required="true"  class="form-control"></td>
			</tr>
			<tr height="52">
				<th nowrap>
					<div class="checkbox">
						<label onclick="javascript: show_set_validade(this);">
							<input type="checkbox" name="set_validade" value="1" id="set_validade">&nbsp;&nbsp;Indicar data de validade
						</label>
					</div>
				</th>
				<td>
					<div id="validade-wrap" hidden >
						<input type="text" name="validade" value="<?php echo date('d/m/Y', strtotime(date('Y-m-d', strtotime(date('Y-m-d'))) . '+1 month')); ?>" id="validade" class="datepicker form-control" >
					</div>
				</td>
				<td colspan="2" ></td>
			</tr>
			<tr>
				<th class="middle bg" colspan="4" >Nível de permissão da conta de usuário</th>
			</tr>

			<tr class="no_bg" >
				<td class="user_level" nowrap >

					<?php
						//SÓ ADICIONA UM SUPER ADIMINISTRADOR, QUEM É SUPER ADMINISTRADOR
						if ($_SESSION['usuario']['nivel'] == 1) {
					?>
					<div class="radio">
						<label for="nivel-superadmin">
									<input type="radio" value="1" name="nivel" id="nivel-superadmin" onclick="javascript:$('.m_permissoes').slideUp();" >
									&nbsp;Super Administrador
						</label>
							<a class="fa fa-info-circle ctn-popover"
							data-content="<p>Possui controle completo sobre a aplicação e Contas de Super Administradores.</p>"
							data-original-title="Super Administrador" ></a>
					</div>
					<?php } ?>

					<div class="radio">
						<label for="nivel-admin">
							<input type="radio" value="2" name="nivel" id="nivel-admin" onclick="javascript:$('.m_permissoes').slideUp();" >
							Administrador
						</label>
							<a class="fa fa-info-circle ctn-popover"
							data-content="<p>Possui controle completo sobre a aplicação.
							<br />A conta de administrador tem acesso a todas<br> as configurações da aplicação.</p>"
							data-original-title="Administrador" ></a>
					</div>

					<div class="radio">
						<label for="nivel-user">
							<input type="radio" value="3" name="nivel" id="nivel-user" onclick="javascript:$('.m_permissoes').slideDown();" >
							&nbsp;Usuário
						</label>
							<a class="fa fa-info-circle ctn-popover"
							data-content="Pode adicionar, modificar e excluir informações,<br />
							de acordo com as permissões definidas pelo <br>administrador da aplicação."
							data-original-title="Usuário" ></a>
					</div>

				</td>
				<td colspan="3" class="user_level" >

					<div class="m_permissoes" >
						<p class="text-primary">Marque as opções de permissão para o usuário.</p>
						<hr>
						<?php
							$modulos = $directIn->select("Select * From ".$directIn->DB_PREFIX."_modulo Order By nome ASC");
							if(is_array($modulos) && count($modulos)>0)
							{
								echo("<ol class='modulo' >");
								foreach($modulos as $modulo)
								{
									echo("<li>".$modulo['nome']);
									$permissoes = $directIn->Select("Select * From ".$directIn->DB_PREFIX."_modulo_permissao Where modulo_codigo=".$modulo['codigo']." Order By nome ASC ");
									if(is_array($permissoes) && count($permissoes)>0){
										echo("<ol class='permissao' >");
										foreach($permissoes as $permissao){
											echo("<li><div class='checkbox'><label><input type='checkbox' value='".$permissao['modulo_codigo']."-".$permissao['permissao_codigo']."' name='m_permissao[]' > &nbsp; ".$permissao['nome']."<i>( ".$permissao['descricao']." )</i></label></div></li>");
										}
										echo("</ol>");
									}
									echo("</li>");
								}
								echo("</ol>");
							}
						?>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="4" class="right" >
					<input type="button" value="Cancelar" class="btn btn-default" onclick="JavaScript:var x = confirm('Você deseja realmente cancelar?\n Os dados não salvos serão perdidos.'); if(x){ location.href='?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>' }">
	     			<input type="button" value="Enviar" class="btn btn-primary" data-loading-text="Carregando..."  onclick="JavaScript:checkFormRequire(document.form_dados,'#error-box',checkCadAdmin);">
				</td>
			</tr>
		</table>
	</form>