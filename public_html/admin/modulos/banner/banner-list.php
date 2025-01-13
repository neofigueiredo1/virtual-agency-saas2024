<?php
// VERIFICANDO A PERMISSÃO
if (!Sis::checkPerm('10002-2') && !Sis::checkPerm('10002-1'))
{
	Sis::setAlert('Você não tem acesso à este recurso!', 1, '/admin/');
}

//Armazenamento do id do tipo de banner
$tid = isset($_GET['tid']) ? $_GET['tid'] : "";
if($tid==0){ Sis::setAlert('Selecione um tipo para carregar a lista de banners!', 1,'?mod=banner&pag=banner&act=tipo-list'); }

$listaTipo = $directIn->tipoListSelected($tid);
if(!(is_array($listaTipo) && count($listaTipo)>0)){
	Sis::setAlert('Selecione um tipo para carregar a lista de banners!', 1,'?mod=banner&pag=banner&act=tipo-list');
}

$subtipos = explode(PHP_EOL,$listaTipo[0]['subtipo_list_banner']);



?>

<ol class="breadcrumb">
    <li><a href="?pag=<?php echo $pag; ?>&amp;act=tipo-list">Tipos de Banner</a></li>
    <li><?php echo $listaTipo[0]['nome']; ?></li>
	<li>Lista de banners</li>
</ol>

<div class="btn-group">
    <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=tipo-list">Tipos de banner</a>
    <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=tipo-add" <?php echo (!Sis::checkPerm('10002-2') && !Sis::checkPerm('10002-4'))?"disabled='disabled'":""; ?> >Criar novo tipo</a>
    <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=list&tid=<?php echo $tid; ?>" disabled='disabled' >Lista de banners</a>
    <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=add&tid=<?php echo $tid; ?>" <?php echo (!Sis::checkPerm('10002-2') && !Sis::checkPerm('10002-3'))?"disabled='disabled'":""; ?> >Criar novo banner</a>
</div>

<hr />

<?php
if (is_numeric($tid)) {
	$lista = $directIn->listAll($tid);
	if(is_array($lista) && count($lista)>0){
	?>
	<div class="panel panel-default">
		<div class="panel-heading">Lista de tipos de banner</div>

	<?php
			echo
			'
			<table class="table table-hover table-striped table_list">
				<thead>
					<tr>
						<th ><div style="width:225px;">Imagem</div></th>
						<th  width="70%" ><strong>Nome</strong></th>
						<th width="30%" style="min-width:100px; text-align:center;" ><strong>Tipo de Banner</strong></th>
						<th width="0%" style="min-width:100px; text-align:center;" ><strong>Situação</strong></th>
						<th width="0%" style="min-width:100px; text-align:center;" ><strong>Ações</strong></th>
					</tr>
				</thead>
			</table>

			<ul id="caixa-pagina" class="list-striped cursor-move" >';
				foreach ($lista as $i => $lista_arr){

					$bSubtipo = isset($subtipos[$lista_arr['subtipo_banner']-1])?$subtipos[$lista_arr['subtipo_banner']-1]:'';

					$count = $i + 1;
					echo "
					<li id='" . $lista_arr['banner_idx'] . "' >
						<table class='list table table-hover table-striped table_list' style='margin-bottom:0px' >
							<tbody>
								<tr>
									<td><div style='padding:5px;width:225px;background-color:".$default_color."' ><img src='/".PASTA_CONTENT."/".$pag."/".$lista_arr['arquivo']."' alt='' style='width:auto;max-width:200px; max-height:200px;' /></td>
									<td width='70%' ><a href='?pag=" . $pag . "&amp;act=edit&amp;bid=" . $lista_arr['banner_idx'] . "&tid=" . $tid . "' >".$lista_arr['nome']."</a></td>
									<td width='30%' class='middle' style='min-width:100px; text-align:center;' >".$bSubtipo."</td>
									<td width='0%' class='middle' style='min-width:100px; text-align:center;' >".sis::getStatusFormat($lista_arr['status'])."</td>
									<td width='0%' class='middle' style='min-width:100px; text-align:center;' >";

									if (Sis::checkPerm('10002-2') || Sis::checkPerm('10002-5'))
									{
										echo ($lista_arr['monitor_impressao']==1 || $lista_arr['monitor_clique']==1) ? "<a class='a_tooltip' data-placement='top' title='Estatísticas do banner' href='?pag=" . $pag . "&amp;act=stats&amp;bid=" . $lista_arr['banner_idx'] . "&tid=" . $tid . "'><i class='fa fa-bar-chart-o' ></i></a> &nbsp; " : "";
									}

									if (Sis::checkPerm('10002-2') || Sis::checkPerm('10002-3'))
									{
										echo "<a class='a_tooltip' data-placement='top' title='Editar' href='?pag=" . $pag . "&amp;act=edit&amp;bid=" . $lista_arr['banner_idx'] . "&tid=" . $tid . "' ><i class='fa fa-pencil-square-o'></i></a>&nbsp;
												<a class='a_tooltip' data-placement='top' title='Excluir' href='#' onclick='javascript: if (confirm(&quot;Você deseja excluir os dados?&quot;)) { window.location=&quot;?mod=" . $mod . "&pag=" . $pag . "&amp;act=del&tid=".$lista_arr['tipo_idx']."&bid=".$lista_arr['banner_idx']."&quot;; } else { return false; };' ><i class='fa fa-trash-o'></i></a>
												";
									}
					echo "		</td>
								</tr>
							</tbody>
						</table>
					</li>";
				}
			echo '</ul>';
	} else {
			echo "
            <div class='alert alert-warning'>
                <i class='fa fa-exclamation-triangle'></i>&nbsp;&nbsp;
                Nenhum registro para a lista.
            </div>
        ";
	}

	echo("</div></div>");

}else{
	Sis::redirect("?pag=" . $pag . "&amp;act=tipo.list", 0.01);
}
?>


<script type="text/javascript" >
	function salva_ordem()
	{
		var ordem = $("#caixa-pagina").sortable("toArray");
		var url = "modulos/banner/banner-exe.php?exe=1&ordem="+ordem;
		var obj_ajax = http_request();
		obj_ajax.open("GET",url,true);
		obj_ajax.onreadystatechange = function(){
			if(obj_ajax.readyState == 4){
				if(obj_ajax.status == 200){
					var resposta = obj_ajax.responseText;
					if(resposta!="ok"){ alert(resposta);}
				}
			}
		}
		obj_ajax.send(null);
	}
	$("#caixa-pagina").sortable({ axis: "y",stop:salva_ordem });
</script>