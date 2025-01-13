<ol class="breadcrumb">
	<li><a href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>">Configurações</a></li>
	<li>Lista de variáveis</li>
</ol>

<div class="btn-group">
   <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>" disabled="disabled">Lista de variáveis</a>
   <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=add">Criar nova variável</a>
   <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=hist-list">Histórico</a>
   <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=sis-list">Configurações do sistema</a>
</div>

<hr />

<?php
	$list = $directIn->listAll();
	if(is_array($list) && count($list) > 0){
?>
<div class="panel panel-default">
   <div class="panel-heading">Lista de variáveis de ambiente</div>

<?php
	echo '
	<table class="table table-hover table-striped table_list">
		<thead>
				<tr>
					<th width="50%">Nome</th>
					<th width="50%">Valor</th>
					<th width="0%">Situação</th>
					<th class="center" width="0%">Ações</th>
				</tr>
		</thead>
		<tbody>
		';
		foreach ($list as $listArray){
			echo "
			<tr>
				<td>
					<a href='?mod=" . $mod . "&pag=" . $pag . "&act=edit&v_id=" . $listArray['config_idx'] . "' style='color:#000;' >" . $listArray['nome'] . "</a>
					<br><span style='color:#999;font-size:14px;'>" . $listArray['descricao'] . "</span>
				</td>
				<td class='middle'><a href='?mod=" . $mod . "&pag=" . $pag . "&act=edit&v_id=" . $listArray['config_idx'] . "' >".substr($listArray['valor'],0,65) . "</a></td>
				<td class='middle'><a href='?mod=" . $mod . "&pag=" . $pag . "&act=edit&v_id=" . $listArray['config_idx'] . "' >".Sis::getStatusFormat($listArray['status']) . "</a></td>
				<td nowrap class='middle'>
					<a class='a_tooltip' data-placement='top' title='Editar' href='?mod=" . $mod . "&pag=" . $pag . "&act=edit&v_id=" . $listArray['config_idx'] . "' >
						<i class='fa fa-pencil-square-o'></i>
					</a>
					&nbsp;
					<a class='a_tooltip' data-placement='top' title='Excluir' href='#' onclick='javascript: if (confirm(&quot;Você deseja excluir os dados?&quot;)) { window.location=&quot;?mod=config&pag=config&act=delete&v_id=" . $listArray['config_idx'] . "&nome=" . $listArray['nome'] . "&quot;; } else { return false; };' >
						<i class='fa fa-trash-o'></i>
					</a>
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
               <i class='fa fa-exclamation-triangle'></i>&nbsp;&nbsp;Nenhum registro encontrado.
            </div>
            ";
	}
?>