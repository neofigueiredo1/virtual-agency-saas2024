<ol class="breadcrumb">
	<li><a href="?mod=cadastro&pag=cadastro">Cadastros</a></li>
	<li>Áreas de interesse</li>
</ol>

<div class="btn-group">
  	<a class="btn btn-default" href="?mod=<?php echo $mod ?>&pag=cadastro" >Cadastros</a>
  	<a class="btn btn-default" href="?mod=<?php echo $mod ?>&pag=cadastro&act=add" >Adicionar cadastro</a>
</div>
<div class="btn-group">
  	<a class="btn btn-default" href="?mod=<?php echo $mod ?>&pag=area-interesse" disabled="disabled" >Áreas de interesse</a>
  	<a class="btn btn-default" href="?mod=<?php echo $mod ?>&pag=area-interesse&act=add" >Nova área de interesse</a>
</div>
<div class="btn-group">
  	<a class="btn btn-success" href="?mod=<?php echo $mod ?>&pag=cadastro&act=exportar">Exportar cadastros</a>
</div>

<hr />

	<?php
	$lista = $directIn->listAll();
	if(isset($lista) && $lista !== false){
		echo '
		<div class="panel panel-default">
		   <div class="panel-heading">Lista de áreas de interesse</div>
			<table class="table table-hover table-striped table_list">
				<thead>
					<tr>
						<th width="80%">Nome</th>
						<th width="0%" >Status</th>
						<th width="0%" nowrap >Data de Cadastro</th>
						<th width="0%" nowrap >Ações</th>
					</tr>
				</thead>
				<tbody class="table-list-striped">
				';
				foreach ($lista as $lista_arr){
					echo "
					<tr>
						<td nowrap ><a href='?mod=" . $mod . "&pag=" . $pag . "&act=edit&id=" . $lista_arr['interesse_idx'] . "' >" . $lista_arr['nome'] . "</a></td>
						<td>" . Sis::getStatusFormat($lista_arr['status']) . "</td>
						<td nowrap >" . Date::fromMysql($lista_arr['data_cadastro'],1) . "</td>
						<td nowrap >
							<a class='a_tooltip' data-placement='top' title='Editar' href='?mod=" . $mod . "&pag=" . $pag . "&act=edit&id=" . $lista_arr['interesse_idx'] . "' ><i class='fa fa-pencil-square-o'></i></a>
							&nbsp;
							<a class='a_tooltip' data-placement='top' title='Excluir' href='#' onclick='javascript: if (confirm(&quot;Você deseja excluir os dados?&quot;)) { window.location=&quot;?mod=" . $mod . "&pag=" . $pag . "&act=del&id=" . $lista_arr['interesse_idx'] . "&quot;; } else { return false; };' ><i class='fa fa-trash-o'></i></a>
						</td>
					</tr>
					";
				}
			echo '</tbody>
			</table>
		</div>';
	} else {
		echo "<div class='alert alert-warning'>
	                <i class='fa fa-exclamation-triangle'></i>&nbsp;&nbsp;
	                Nenhum registro para a lista.
	            </div>";
	}
	?>