<?php
	if(!Sis::checkPerm($modulo['codigo'].'-2')){
		Sis::setAlert('Você não tem acesso à este recurso!', 1, '/admin/');
	}
	$fid = isset($_GET['fid']) ? (int)$_GET['fid'] : 0;
	if($fid==0){ Sis::setAlert('Selecione uma área carregar os itens!', 1,'?mod='.$mod.'&pag=faq'); }

	include_once("faq-model.php");
	include_once("faq-control.php");
?>

<ol class="breadcrumb">
   <li>FAQ</li>
   <li><a href="?mod=<?php echo $mod; ?>&pag=faq">&Aacute;reas de FAQ</a></li>
   <li>
   	<?php
   		$faq = new faq();
   		$nameFaq = $faq->listAll($fid,'nome');
   		if(is_array($nameFaq) && count($nameFaq) > 0){
         	echo " Categorias (".$nameFaq[0]['nome'].") ";
	      }else{
				Sis::setAlert('Selecione uma área carregar os itens!', 1,'?mod='.$mod.'&pag=faq');
	      }
   	?>
   </li>
</ol>

<?php include_once("faq-menu.php"); ?>

<hr />

<?php
	$list = $directIn->listAll(0,$fid);
	if(is_array($list) && count($list)>0){
		echo
		'
		<div class="panel panel-default">
			<div class="panel-heading">Lista de categorias</div>

				<table class="table table-hover table-striped table_list">
					<thead>
						<tr>
							<th width="100%">Nome</th>
							<th width="0%" nowrap class="center">Situação</th>
							<th width="0%" nowrap class="center">Ações</th>
						</tr>
				</thead>
			</table>

			<ul id="caixa-pagina" class="list-striped cursor-move" >';
					foreach ($list as $i => $arrayList){
						$count = $i + 1;
						echo "
					<li id='" . $arrayList['item_idx'] . "' >
						<table class='list table table-hover table-striped table_list' style='margin-bottom:0px' >
							<tbody>
							<tr>
								<td width='100%' >
									<a href='?mod=" . $mod . "&pag=" . $pag . "&act=edit&fid=" . $arrayList['faq_idx'] . "&iid=". $arrayList['item_idx'] ."' >".$arrayList['pergunta']."</a><br />
									<small>".$arrayList['resposta']."</small>
								</td>
								<td width='0%' class='middle' style='min-width:100px; text-align:center;' >".sis::getStatusFormat($arrayList['status'])."</td>
								<td width='0%' class='middle' style='min-width:100px; text-align:center;' >";

									if (Sis::checkPerm($modulo['codigo'].'-2'))
									{
										echo "<a class='a_tooltip' data-placement='top' title='Editar' href='?mod=" . $mod . "&pag=" . $pag . "&act=edit&fid=" . $arrayList['faq_idx'] . "&iid=". $arrayList['item_idx'] ."' ><i class='fa fa-pencil-square-o'></i></a>&nbsp;
												<a class='a_tooltip' data-placement='top' title='Excluir' href='#' onclick='javascript: if (confirm(&quot;Você deseja excluir os dados?&quot;)) { window.location=&quot;?mod=" . $mod . "&pag=" . $pag . "&amp;act=del&fid=".$arrayList['faq_idx']."&iid=".$arrayList['item_idx']."&quot;; } else { return false; };' ><i class='fa fa-trash-o'></i></a>
												";
									}
						echo "</td></tr>
						</tbody>
						</table>
					</li>
					";
					}
				echo "</ul>";
		}else {
				echo "
	            <div class='alert alert-warning'>
	                <i class='fa fa-exclamation-triangle'></i>&nbsp;&nbsp;
	                Nenhum registro encontrado.
	            </div>
	        ";
		}
	echo "</div>";
?>

<script type="text/javascript" >
	function salva_ordem()
	{
		var ordem = $("#caixa-pagina").sortable("toArray");
		var url = "modulos/faq/faq-exe.php?exe=1&tbl=2&ordem="+ordem;
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