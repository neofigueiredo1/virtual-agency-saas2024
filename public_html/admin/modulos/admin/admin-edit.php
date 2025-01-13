<?php
	Auth::checkPermission("2");
	$id     = isset($_GET['id'])      ? (int)$_GET['id']      : 0 ;
	$enviar = isset($_POST['enviar']) ? (int)$_POST['enviar'] : 0 ;
	if ($enviar === 1) $directIn->_update();
?>

<ul class="breadcrumb" >
	<li><a href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>">Administradores</a></li>
	<li>Editar administrador</li>
</ul>

<div class="btn-group" >
	<a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>">Listar administradores</a>
	<a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=add">Criar novo administrador</a>
	<a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=edit-meus-dados">Editar meus dados</a>
	<a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=edit-pass" >Alterar minha senha</a>
</div>

<hr />

<div class="alert alert-danger" id="error-box" style="display:none;"><i class="fa fa-exclamation-triangle"></i>&nbsp;&nbsp;Preencha todos os campos corretamente!</div>

	<?php
	$lista = $directIn->listSelected($id);
	if(is_array($lista) && count($lista) > 0){
		foreach ($lista as $lista_arr)
		{
			$_user_permissions = "";
			$user_permissions = $directIn->Select("Select * From ".$directIn->DB_PREFIX."_login_permissao Where usuario_idx=".round($lista_arr['usuario_idx'])." ");
			if(is_array($user_permissions) && count($user_permissions)>0)
			{
				foreach($user_permissions as $user_permission)
				{
					$_user_permissions .= "!".$user_permission['modulo_codigo']."-".$user_permission['permissao_codigo']."!";
				}
			}

			?>
			<form action="<?php echo Sis::currPageUrl(); ?>" name="form_dados" id="form_dados" method="post" class="admin_form" >

				<input type="hidden" name="id" value="<?php echo $id; ?>">
				<input type="hidden" name="enviar" value="1">
				<input type="hidden" name="up" value="<?php echo $_user_permissions; ?>" />

				<table class="table table_form" border="0">
					<tr >
						<th width="25%" class="middle bg">Situação</th>
						<td width="75%" colspan="3" class="middle">
							<label class="radio-inline">
								<input type="radio" name="status" id="status1" value="1" <?php if ((int)$lista_arr['status']===1) echo "checked=checked"; ?> >Ativo
							</label>
							<label class="radio-inline">
								<input type="radio" name="status" id="status0" value="0" <?php if ((int)$lista_arr['status']===0) echo "checked=checked"; ?> >Inativo
							</label>
						</td>
					</tr>
					<tr>
						<th class="middle bg" >Nome</th>
						<td colspan="3"><input type="text" name="nome" id="nome" data-required="true" value="<?php echo $lista_arr['nome'] ?>"   class="form-control"></td>
					</tr>
					<tr>
						<th width="25%"class="middle bg" >Email</th>
						<td width="25%" ><input type="text" name="email" id="email" data-required="true" value="<?php echo $lista_arr['email'] ?>"  class="form-control"></td>
						<th width="25%" class="middle bg" nowrap >Nome para login</th>
						<td width="25%" ><input type="text" name="login" id="login" data-required="true" value="<?php echo $lista_arr['login'] ?>"  class="form-control"></td>
					</tr>
					<tr>
						<th>
							<div class="checkbox">
								<label>
									<input type="checkbox" name="set_pass" value="1" id="set_pass" onclick="$('#wrap_pass').slideToggle();" >Redefinir senha de acesso.
								</label>
							</div>
						</th>
						<td colspan="3"></td>
					</tr>
					<tr class="reset" >
						<td colspan="4" class="reset " valign="top" style='border:0px;' >
							<div id="wrap_pass" hidden>
								<table class="table table_form" border="0"  style='margin-bottom:0px;' >
									<th width="25%" class="middle bg" >Senha</th>
									<td width="25%" ><input type="password" name="senha" id="senha" class="form-control"></td>
									<th width="25%" nowrap class="middle bg" >Confirme a senha</th>
									<td width="25%" ><input type="password" name="senha_confirm" id="senha_confirm" class="form-control"></td>
								</table>
							</div>
						</td>
					</tr>
					<tr>
						<th class="top">
							<div class="checkbox">
								<label onclick="javascript: show_set_validade(this);">
									<input type="checkbox" <?php if ((int)$lista_arr['set_validade']===1) echo "checked=checked"; ?> name="set_validade" value="1" id="set_validade">Indicar data de validade
								</label>
							</div>
						</th>
						<td>
							<div id="validade-wrap" <?php if ((int)$lista_arr['set_validade'] === 0) echo "hidden"; ?> >
								<input type="text" name="validade" value="<?php echo Date::fromMysql($lista_arr['validade']); ?>" id="validade" class="datepicker form-control" >
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
								<input type="radio" value="1" <?php if ((int)$lista_arr['nivel']===1) echo "checked=checked"; ?> name="nivel" id="nivel-superadmin" onclick="javascript:$('.m_permissoes').slideUp();" >
								&nbsp;Super Administrador</label>
								<a class="fa fa-info-circle ctn-popover"
								data-content="<p>Possui controle completo sobre a aplicação e Contas de Super Administradores.</p>"
								data-original-title="Super Administrador" ></a>
							</div>
							<?php } ?>

							<div class="radio">
								<label for="nivel-admin">
								<input type="radio" value="2" <?php if ((int)$lista_arr['nivel']===2) echo "checked=checked"; ?> name="nivel" id="nivel-admin" onclick="javascript:$('.m_permissoes').slideUp();" >
								&nbsp;Administrador</label>
								<a class="fa fa-info-circle ctn-popover"
								data-content="<p>Possui controle completo sobre a aplicação.</p>
								<p>É possível criar mais de um administrador.</p>
								<p>A conta de administrador tem acesso a todas<br> as configurações da aplicação.</p>"
								data-original-title="Administrador" ></a>
							</div>

							<div class="radio">
								<label for="nivel-user">
								<input type="radio" value="3" <?php if ((int)$lista_arr['nivel']===3) echo "checked=checked"; ?> name="nivel" id="nivel-user" onclick="javascript:$('.m_permissoes').slideDown();" >
								&nbsp;Usuário</label>
								<a class="fa fa-info-circle ctn-popover"
								data-content="Pode adicionar, modificar e excluir informações,<br>
								de acordo com as permissões definidas pelo <br>administrador da aplicação."
								data-original-title="Usuário" ></a>
							</div>

						</td>
						<td colspan="3" class="user_level" >

							<div class="m_permissoes" style="display:<?php echo ((int)$lista_arr['nivel']===3)?"block":""; ?>" >
								<p class="text-primary">Marque as opções de permissão para o usuário.</p>
								<hr>
								<?
									$modulos = $directIn->Select("Select * From ".$directIn->DB_PREFIX."_modulo Order By nome ASC ");
									if(is_array($modulos) && count($modulos)>0)
									{
										echo("<ol class='modulo' >");
										foreach($modulos as $modulo)
										{
											echo("<li>".$modulo['nome']);
											$permissoes = $directIn->Select("Select * From ".$directIn->DB_PREFIX."_modulo_permissao Where modulo_codigo=".$modulo['codigo']." Order By permissao_codigo ASC ");
											if(is_array($permissoes) && count($permissoes)>0){
												echo("<ol class='permissao' >");
												foreach($permissoes as $permissao){
													$_checked = (strpos($_user_permissions, $permissao['modulo_codigo']."-".$permissao['permissao_codigo'])!==false)?"checked":"";
													echo("<li><div class='checkbox'><label><input type='checkbox' ".$_checked." value='".$permissao['modulo_codigo']."-".$permissao['permissao_codigo']."' name='m_permissao[]' > &nbsp; ".$permissao['nome']." <i>( ".$permissao['descricao']." )</i></label></div></li>");
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
						<td class="right" colspan="4" >
							<input type="button" value="Cancelar" class="btn btn-default" onclick="JavaScript:var x = confirm('Você deseja realmente cancelar?\n Os dados não salvos serão perdidos.'); if(x){ location.href='?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>' }">
			     			<input type="button" value="Enviar" class="btn btn-primary" data-loading-text="Carregando..."  onclick="JavaScript:checkFormRequire(document.form_dados,'#error-box',checkEditAdmin);">
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
