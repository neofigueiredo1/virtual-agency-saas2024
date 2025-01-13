<ol class="breadcrumb">
    <li class="active">Menus</li>
</ol>

<?php	
  	// CRIANDO A NAVEGAÇÃO DE ACORDO COM A PERMISSÃO	
  	if (!Sis::checkPerm($modulo['codigo'].'-2') && !Sis::checkPerm($modulo['codigo'].'-4')){
      Sis::setAlert('<i class="fa fa-warning"></i> Você não tem acesso à este módulo!', 1, '/admin/');
  	}else{
      if (Sis::checkPerm($modulo['codigo'].'-1') || Sis::checkPerm($modulo['codigo'].'-2'))
      {
         ?>
         <div class="btn-group">
           	<a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=pagina">Lista de páginas</a>
           	<a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=pagina&act=add">Criar nova página</a>
           	<a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=menu" disabled="disabled">Menus</a>
           	<a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=menu&act=add">Criar novo menu</a>
         </div>
         <?php
      }else{
         if (Sis::checkPerm($modulo['codigo'].'-4')) {
            ?>
            <div class="btn-group">
               <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=menu" disabled="disabled">Menus</a>
           	<a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=menu&act=add">Criar novo menu</a>
            </div>
            <?php
         }
         if (Sis::checkPerm($modulo['codigo'].'-3')) {
            Sis::redirect('?mod=conteudo&pag=pagina');
         }
      }
  	}
	?>

<hr />

<?php
	$list = $directIn->listAll();
	if(is_array($list) && count($list) > 0){
		?>
		<div class="panel panel-default">
		   <div class="panel-heading">Lista de menus</div>

		<?php
			$iCount=0;
			echo '
			<table class="table table-hover table-striped table_list">
				<thead>
					<tr>
						<th width="0%">#</th>
						<th width="20%">Nome</th>
						<th width="65%" class="left">Descrição</th>
						<th width="0%" class="center">Situação</th>
						<th width="0%" class="center">Ações</th>
					</tr>
				</thead>
				<tbody class="table-list-striped">
				';
			foreach ($list as $arrayList){
				$iCount++;
				echo "

					<tr>
						<td width='0%' >" . $iCount . "</td>
						<td>
							<a href='?mod=" . $mod . "&pag=" . $pag . "&act=edit&mn_id=" . $arrayList['menu_idx'] . "' >" . $arrayList['nome'] . "</a>
						</td>
						<td align='left'>
							<a href='?mod=" . $mod . "&pag=" . $pag . "&act=edit&mn_id=" . $arrayList['menu_idx'] . "' >" . $arrayList['descricao'] . "</a>
						</td>
						<td align='center'>" . Sis::getStatusFormat($arrayList['status']) . "</td>
						<td nowrap style='text-align:center'>
							<a class='a_tooltip' data-placement='top' title='Editar' href='?mod=" . $mod . "&pag=" . $pag . "&act=edit&mn_id=" . $arrayList['menu_idx'] . "' >
								<i class='fa fa-pencil-square-o'></i>
							</a>&nbsp;
							<a class='a_tooltip' data-placement='top' title='Excluir' href='javascript:;' onclick='javascript: if (confirm(&quot;Você deseja excluir os dados?&quot;)) { window.location=&quot;?mod=" . $mod . "&pag=" . $pag . "&act=del&menu_id=" . $arrayList['menu_idx'] . "&quot;; } else { return false; };' >
								<i class='fa fa-trash-o'></i>
							</a>
						</td>
					</tr>

				";
			}
			echo '
				</tbody>
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