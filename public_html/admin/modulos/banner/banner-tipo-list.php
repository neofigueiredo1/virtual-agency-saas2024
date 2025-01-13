<?php
// VERIFICANDO A PERMISSÃO
if(!Sis::checkPerm('10002-2') && !Sis::checkPerm('10002-1'))
{
	Sis::setAlert('Você não tem acesso à este recurso!', 1, '/admin/');
}
?>
<ol class="breadcrumb">
    <li>Tipos de banner</li>
</ol>

<div class="btn-group">
    <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=tipo-list" disabled="disabled" >Tipos de banner</a>
    <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=tipo-add" <?php echo (!Sis::checkPerm('10002-2') && !Sis::checkPerm('10002-4'))?"disabled='disabled'":""; ?> >Criar novo tipo</a>
</div>

<hr />

<?php
	$list = $directIn->tipoListAll();

	if(is_array($list) && count($list) > 0){
		?>
		<div class="panel panel-default">
		   <div class="panel-heading">Lista de tipos de banner</div>

		<?php
		   $iCount=0;
			echo '
				<table class="table table-hover table-striped table_list">
					<thead>
						<tr>
							<th width="0%">&nbsp;&nbsp;#&nbsp;</th>
							<th width="70%">Nome</th>
							<th width="20%" >Tipos de banners disponíveis</th>
							<th width="0%" class="center" nowrap>Ações</th>
						</tr>
					</thead>
					<tbody>
					';
				foreach ($list as $arrayList){
					$iCount++;
		         //Listagem dos efeitos de transição do banner
	            $efx = "";
	            foreach (Sis::jqueryEfx() as $key => $value) {
	                if (stristr($arrayList['animacao'], $value)) {
	                    $efx .= $key."<br>";
	                }
	            }
				
				echo '
						<tr>
							<td>
								<a href="?mod=' . $mod . '&pag=' . $pag . '&act=list&tid=' . $arrayList['tipo_idx'] . '" >' . $iCount . '</a>
							</td>
							<td>
								<a href="?mod=' . $mod . '&pag=' . $pag . '&act=list&tid=' . $arrayList['tipo_idx'] . '" >'.$arrayList['nome'].'</a>
							</td>
							<td>
								<span>
								';
								$lista = $directIn->transformTipoBanner($arrayList['subtipo_list_banner'], $arrayList['tipo_idx']);
								foreach($lista as $key => $tipo) {
									echo "- " .$tipo['subtipo_nome'] ."<br>";
								}
								
								echo '</span>
							</td>
							
							<td nowrap>
		               
		                 <a class="a_tooltip" data-placement="top" title="Lista de banners" href="?mod=' . $mod . '&pag=' . $pag . '&act=list&tid=' . $arrayList['tipo_idx'] . '" > <i class="fa fa-list-ul"></i></a>&nbsp;';
							if (Sis::checkPerm('10002-2') || Sis::checkPerm('10002-4'))
							{
	               		echo   '<a class="a_tooltip" data-placement="top" title="Editar" href="?mod=' . $mod . '&pag=' . $pag . '&act=tipo-edit&tid=' . $arrayList['tipo_idx'] . '" > <i class="fa fa-pencil-square-o"></i></a>&nbsp;';
		               	echo   '<a class="a_tooltip" data-placement="top" title="Excluir" href="#"" onclick="javascript: if (confirm(&quot;Você deseja excluir os dados?&quot;)) { window.location=&quot;?mod=' . $mod . '&pag=' . $pag . '&act=tipo-del&tid='.$arrayList['tipo_idx'].'&quot;; } else { return false; };" ><i class="fa fa-trash-o" ></i></a>';
							}
				echo	'	</td>
						</tr>
					</tbody>
				';
		}

		echo("</table></div>");

	} else {
		echo "
            <div class='alert alert-warning'>
                <i class='fa fa-exclamation-triangle'></i>&nbsp;&nbsp;
                Nenhum registro para a lista.
            </div>
        ";
	}
	?>