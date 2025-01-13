<?php
	// VERIFICANDO A PERMISSÃO
	if (!Sis::checkPerm('10010-2') && !Sis::checkPerm('10010-1') && !Sis::checkPerm('10010-3'))
	{
		Sis::setAlert('Você não tem acesso à este recurso!', 1, '/admin/');
	}
?>

<ul class="breadcrumb">
    <li>Depoimentos</li>
</ul>

<div class="btn-group">
    <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=depoimento" disabled="disabled" >Depoimentos</a>
    <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=depoimento&act=add">Criar novo depoimento</a>
</div>

<hr />

<?php

	$arrayList = $directIn->listAll();
	if(is_array($arrayList) && count($arrayList) > 0){
		?>

			<div class="panel panel-default" >
		   	<div class="panel-heading">Lista de Depoimentos</div>

		   	<table class="table table-hover table-striped table_list">
					<thead>
						<tr>
							<th width="90%">Nome</th>
							<th width="0%" class="center" nowrap>Ações</th>
						</tr>
					</thead>
				</table>
				<ul id="caixa-cliente" class="list-striped cursor-move" >
					<?php
						foreach ($arrayList as $key => $list) {
							echo "<li id='" . $list['depoimento_idx'] . "' >
										<table class='list table table-hover table-striped table_list' style='margin-bottom:0px' >
											<tbody>";
												echo '
														<tr>
															<td width="90%" class="left"> '.$list['nome'].'</td>
															<td width="0%" nowrap class="center">
										               ';
											if (Sis::checkPerm('10010-2') || Sis::checkPerm('10010-3')){
									               		echo   '<a class="a_tooltip" data-placement="top" title="Editar" href="?mod=' . $mod . '&pag=' . $pag . '&act=edit&gid=' . $list['depoimento_idx'] . '" > <i class="fa fa-pencil-square-o"></i></a>&nbsp;';
										               	echo   '&nbsp; <a class="a_tooltip" data-placement="top" title="Excluir" href="#"" onclick="javascript: if (confirm(&quot;Você deseja excluir os dados?&quot;)) { window.location=&quot;?mod=' . $mod . '&pag=' . $pag . '&act=del&gid='.$list['depoimento_idx'].'&quot;; } else { return false; };" ><i class="fa fa-trash-o" ></i></a>';
															}
												echo	'	</td>
													</tr>';
							echo "		</tbody>
										</table>
									</li>";
						}
					?>
				</ul>
		   </div>

		<?php
	}else {
		echo "
            <div class='alert alert-warning'>
                <i class='fa fa-exclamation-triangle'></i>&nbsp;&nbsp;
                Nenhum registro para a lista.
            </div>
        ";
	}

?>

<script type="text/javascript" >
	function salva_ordem_galeria()
	{
		var ordem = $("#caixa-cliente").sortable("toArray");
		var url = "modulos/depoimento/depoimento-exe.php?exe=3&ordem="+ordem;
		var obj_ajax = http_request();
		obj_ajax.open("GET",url,true);
		obj_ajax.onreadystatechange = function(){
			if(obj_ajax.readyState == 4){
				if(obj_ajax.status == 200){
					var resposta = obj_ajax.responseText;
					if(resposta!="ok"){ console.log(resposta); }
				}
			}
		}
		obj_ajax.send(null);
	}
	$("#caixa-cliente").sortable({ axis: "y",stop:salva_ordem_galeria });
</script>