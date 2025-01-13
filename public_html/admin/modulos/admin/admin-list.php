<?php
	Auth::checkPermission("2");
?>

<ul class="breadcrumb" >
	<li>Administradores</li>
</ul>

<div class="btn-group" >
	<a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>" disabled="disabled">Listar administradores</a>
	<a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=add" >Criar novo administrador</a>
	<a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=edit-meus-dados" >Editar meus dados</a>
	<a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=edit-pass" >Alterar minha senha</a>
</div>

<hr />

<?php
	$lista = $directIn->listAll();
	if(is_array($lista) && count($lista) > 0){
		echo '
		<div class="panel panel-default">
		  <div class="panel-heading">Lista de contas cadastradas</div>

			<table class="table table-hover table-striped table_list">
			<thead>
				<tr>
					<th width="50%">Nome</th>
					<th width="50%">E-mail</th>
					<th width="0%" align="center">Situação</th>
					<th width="0%" align="center">Ações</th>
				</tr>
			</thead>
			<tbody class="table-list-striped" >
			';
			foreach ($lista as $listArray){
				echo "
				<tr>
					<td><a href='?pag=" . $pag . "&act=edit&id=" . $listArray['usuario_idx'] . "' >" . $listArray['nome'] . "</a></td>
					<td><a href='mailto:" . $listArray['email'] . "'>" . $listArray['email'] . "</a></td>
					<td align='center'>" . sis::getStatusFormat($listArray['status']) . "</td>
					<td nowrap>
						<a class='a_tooltip' data-placement='top' title='Editar' href='?pag=" . $pag . "&act=edit&id=" . $listArray['usuario_idx'] . "' ><i class='fa fa-pencil-square-o'></i></a>
						&nbsp;
						<a class='a_tooltip' data-placement='top' title='Excluir' href='#' onclick='javascript: if (confirm(&quot;Você deseja excluir os dados?&quot;)) { window.location=&quot;?pag=" . $pag . "&act=del&id=" . $listArray['usuario_idx'] . "&quot;; } else { return false; };' ><i class='fa fa-trash-o'></i></a>
					</td>
				</tr>
				";
			}
		echo '</tbody>
		</table>
	</div>';
	} else {
		echo "
			<div class='alert alert-warning'>
            <i class='fa fa-exclamation-triangle'></i>&nbsp;&nbsp;
            Nenhum registro encontrado.
         </div>
		";
	}
?>