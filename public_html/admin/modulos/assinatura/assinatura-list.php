<?php
	
	if(!Sis::checkPerm($modulo['codigo'].'-2')){
		Sis::setAlert('Você não tem acesso à este recurso!', 1, '/admin/');
	}

	$_SESSION['filtros_ecommerce_pedido'] = (isset($_SESSION['filtros_ecommerce_pedido'])) ? $_SESSION['filtros_ecommerce_pedido'] : [];
	$_SESSION['filtros_ecommerce_pedido_texto'] = (isset($_SESSION['filtros_ecommerce_pedido_texto'])) ? $_SESSION['filtros_ecommerce_pedido_texto'] : "";

	$exe = isset($_POST['exe']) ? (int)$_POST['exe'] : 0;
	if($exe == "1"){
		$_SESSION['filtros_ecommerce_pedido']="";
		$_SESSION['filtros_ecommerce_pedido_texto']="";
	}

	if($exe == "2"){
		$_SESSION['filtros_ecommerce_pedido'] = array();
		$_SESSION['filtros_ecommerce_pedido_texto'] = "";

		$situacao 				= isset($_POST['situacao']) ? (int)$_POST['situacao'] : 0;
		$cliente 			= isset($_POST['cliente']) ? (int)$_POST['cliente'] : 0;
		$palavra_chave 	= (isset($_POST['palavra_chave']) && $_POST['palavra_chave'] != "") ? Text::clean($_POST['palavra_chave']) : "";
		$pesquisa_periodo	= isset($_POST['pesquisa_periodo']) ? (int)$_POST['pesquisa_periodo'] : 0;
		$data_de 			= (isset($_POST['data_de']) && $_POST['data_de'] != "") ? Text::clean($_POST['data_de']) : "";
		$data_ate 			= (isset($_POST['data_ate']) && $_POST['data_ate'] != "") ? Text::clean($_POST['data_ate']) : "";

		if($situacao != 0){
			$_SESSION['filtros_ecommerce_pedido']['situacao'] = $situacao;
			$_SESSION['filtros_ecommerce_pedido_texto'] .= ($_SESSION['filtros_ecommerce_pedido_texto'] != "") ? "&nbsp;&nbsp;-&nbsp;&nbsp;" : "";
			$statusInfo = $directIn->listSituacaoWithPedido($_SESSION['filtros_ecommerce_pedido']['situacao']);
			if(is_array($statusInfo) && count($statusInfo)){
				$_SESSION['filtros_ecommerce_pedido_texto'] .= '<b>Situação:</b> "'.$statusInfo[0]['nome'].'"';
			}
		}
		if($cliente != 0){
			$_SESSION['filtros_ecommerce_pedido']['cliente'] = $cliente;
			$_SESSION['filtros_ecommerce_pedido_texto'] .= ($_SESSION['filtros_ecommerce_pedido_texto'] != "") ? "&nbsp;&nbsp;-&nbsp;&nbsp;" : "";
			$clienteInfo = $directIn->listUsersWithPedido($_SESSION['filtros_ecommerce_pedido']['cliente']);
			if($clienteInfo){
				$_SESSION['filtros_ecommerce_pedido_texto'] .= '<b>Cliente:</b> "'.$clienteInfo[0]['nome_completo'].'"';
			}
		}
		if($palavra_chave != ""){
			$_SESSION['filtros_ecommerce_pedido']['palavra_chave'] = $palavra_chave;
			$_SESSION['filtros_ecommerce_pedido_texto'] .= ($_SESSION['filtros_ecommerce_pedido_texto'] != "") ? "&nbsp;&nbsp;-&nbsp;&nbsp;" : "";
			$_SESSION['filtros_ecommerce_pedido_texto'] .= '<b>Palavra Chave:</b> "'.$_SESSION['filtros_ecommerce_pedido']['palavra_chave'].'"';
		}
		if($pesquisa_periodo != 0){
			if ($data_de != "") {
				if ($data_ate == "") {
					$_SESSION['filtros_ecommerce_pedido']['data_de'] = "";
				}else{
					$_SESSION['filtros_ecommerce_pedido']['data_de'] = Date::toMysql($data_de);
					$_SESSION['filtros_ecommerce_pedido_texto'] .= ($_SESSION['filtros_ecommerce_pedido_texto'] != "") ? "&nbsp;&nbsp;-&nbsp;&nbsp;" : "";
					$_SESSION['filtros_ecommerce_pedido_texto'] .= "<b>Cadastros de:</b> ".Date::fromMysql($_SESSION['filtros_ecommerce_pedido']['data_de']);
				}
			}if ($data_ate != "") {
				if ($data_de == "") {
					$_SESSION['filtros_ecommerce_pedido']['data_ate'] = "";
				}
				$_SESSION['filtros_ecommerce_pedido']['data_ate'] = Date::toMysql($data_ate);
				$_SESSION['filtros_ecommerce_pedido_texto'] .= ($_SESSION['filtros_ecommerce_pedido_texto'] != "") ? "&nbsp;&nbsp;-&nbsp;&nbsp;" : "";
				if($data_de == ""){
					$_SESSION['filtros_ecommerce_pedido_texto'] .= "<b>Cadastros até:</b> ".Date::fromMysql($_SESSION['filtros_ecommerce_pedido']['data_ate']);
				}else{
					$_SESSION['filtros_ecommerce_pedido_texto'] .= "<b>Até:</b> ".Date::fromMysql($_SESSION['filtros_ecommerce_pedido']['data_ate']);
				}
			}
		}

		if($_SESSION['filtros_ecommerce_pedido_texto'] != ""){
			$_SESSION['filtros_ecommerce_pedido_texto'] = '<div class="list-group-item" >Resultado Pesquisa: <br>'.$_SESSION['filtros_ecommerce_pedido_texto'].'</div>';
		}

	}
	
?>
<ol class="breadcrumb">
   <li>E-commerce</li>
   <li>Pedidos</li>
</ol>

<?php require_once("ecommerce-menu.php"); ?>

<hr />

<form  action="<?php echo sis::currPageUrl(); ?>" method="post" enctype="multipart/form-data" class="form_limpa" name="form_limpa">
	<input type="hidden" name="exe" value="1">
</form>

<div class="panel panel-default">
	<div class="panel-heading">
		<a href="javascript:;" class="btn btn-default btn-sm" onclick="$('.search-form').slideToggle('fast');" >Filtros de pesquisa</a>
		<input type="submit" <?php echo (!is_array($_SESSION['filtros_ecommerce_pedido'])) ? 'style="display: none;"' : ""; ?> class="btn btn-success btn-sm" onclick="$('.form_limpa').submit();" value="Limpar filtros de pesquisa">
	</div>
	<div class="s-hidden list-group search-form" >
		<div class="s-hidden list-group-item " >

			<form  action="<?php echo sis::currPageUrl(); ?>" method="post" enctype="multipart/form-data" class="form_dados" name="form_dados">
				<input type="hidden" name="exe" value="2">
				<table class="table table_filters" width="300">
					<tr>
			         <td width="33%" class="middle bg_white">
			         	<select class="form-control" name="situacao">
			         		<option value="0">Situação</option>
			         		<?php
			                  $listaSituacao = $directIn->listSituacaoWithPedido();
			                  if(is_array($listaSituacao) && count($listaSituacao)>0){
				                  foreach($listaSituacao as $situacao){
			                        echo "<option value='" . $situacao['status_idx'] ."'> " . $situacao['nome'] . "</option>";
				                  }
			                  }
			               ?>
			         	</select>
			         </td>
			         <td width="33%" class="middle bg_white">
			         	<select class="form-control" name="cliente">
			         		<option value="0">Cliente</option>
			         		<?php
			                  $listaUsers = $directIn->listUsersWithPedido();
			                  if(is_array($listaUsers) && count($listaUsers)>0){
				                  foreach($listaUsers as $cadastro){
			                        echo "<option value='" . $cadastro['cadastro_idx'] ."'> " . $cadastro['nome_completo'] . "</option>";
				                  }
			                  }
			               ?>
			         	</select>
			         </td>
			         <td colspan="2" class="middle bg_white">
			         	<input type="search" class="form-control" name="palavra_chave" placeholder="Palavra-chave" />
			         </td>
					</tr>
					<tr>
			         <td>
							<div class='checkbox'>
                        <label>
                        	<input type='checkbox' name='pesquisa_periodo' value='1' onclick="javascript: checkboxCmdToogle(this, $('.periodo'));" />
                        	Pesquisar por período?
                        </label>
                    	</div>
						</td>
						<td class="periodo" style="display: none;">
			         	<input type="text" class="datepicker form-control"  id="data_de" name="data_de" placeholder="Data de:">
			         </td>
			         <td class="periodo" style="display: none;">
			         	<input type="text" class="datepicker form-control"  id="data_ate" name="data_ate" placeholder="Data até:">
			         </td>
					</tr>

					<tr>
		            <td colspan="4" class="right" >
		               <input type="button" value="Cancelar" class="btn btn-default" onclick="$('.search-form').slideToggle('fast');" >
		               <input type="button" value="Pesquisar" class="btn btn-primary" data-loading-text="Carregando..."  onclick="JavaScript:checkFormRequire(document.form_dados,'#error-box');">
		            </td>
		        </tr>

				</table>
			</form>
			<!-- <div class="clearfix"></div> -->

		</div>
	</div>
	<div class="<?php echo (!isset($_SESSION['filtros_ecommerce_pedido_texto'])) ? 's-hidden' : ""; ?> list-group search-result" >
		<?php echo (isset($_SESSION['filtros_ecommerce_pedido_texto']) && $_SESSION['filtros_ecommerce_pedido_texto']!="") ? $_SESSION['filtros_ecommerce_pedido_texto'] : ""; ?>
	</div>
</div>

<?php
	$pg = (isset($_POST['pg']))?(int)$_POST['pg']:0;
	$pg = ($pg==0) ? (isset($_GET['pg']) ? (int)$_GET['pg'] : 1) :$pg;
	$registrosPorPagina = 30;
	$list = $directIn->pedidosListAll(0, $_SESSION['filtros_ecommerce_pedido'], $pg, $registrosPorPagina);
	global $totalPages;
	if(is_object($list)){
		if(is_array($list->{'resultado'}) && count($list->{'resultado'})>0){
			$totalPages = $list->{'totalPaginas'};
			$totalRegistros = $list->{'totalRegistros'};
			if ((int)$pg > $totalPages) {
				die(Sis::setAlert("Nenhum registro encontrado na página ".(int)$pg.".", 1));
			}
		echo
		'
		<form action="" class="form_download_zip_pack" method="post" name="form_download_zip_pack" >
		<input type="hidden" name="actDownZip" value="1" />
		<div class="panel panel-default">
			<div class="panel-heading">
				Lista de pedidos
				<small class="pull-right">Exibindo '.$registrosPorPagina.' registros por página</small>
			</div>

				<table class="table table-hover table-striped table_list">
					<thead>
						<tr>
							<th class="" width="0%" ><input id="main_check" type="checkbox" onclick="javascript:$(\'.pedidos_codigos\').prop(\'checked\', this.checked);" title="Selecionar todos" /></th>
							<th class="" width="8%">Chave</th>
							<th width="82%">Cliente</th>
							<th width="0%" class="center">Situação</th>
							<th width="0%" nowrap>Data do pedido</th>
							<th width="0%" class="center">Ações</th>
						</tr>
					</thead>
					<tbody>';
					foreach ($list->{'resultado'} as $i => $arrayList){
						$count = $i + 1;
						$statusClass = "";
						if($arrayList['status']==1){
							$statusClass = "aguardando";
						}else if($arrayList['status']==4){
							$statusClass = "entregue";
						}
						echo "
							<tr class='".$statusClass."'>
								<td width='0%' class='middle'>
									<input type=\"checkbox\" class=\"pedidos_codigos\" name=\"pedidos_codigos[]\" value=\"".$arrayList['pedido_idx']."\" onclick=\"javascript:$('#main_check').prop('checked', false);\" />
								</td>
								<td width='8%' class='middle'>
									".$arrayList['pedido_chave']."
								</td>
								<td width='82%' >
									<a href='?mod=" . $mod . "&pag=" . $pag . "&act=view&pid=" . $arrayList['pedido_idx'] . "' >".$arrayList['nome_completo']."</a><br />
									<small>< ".$arrayList['email']." ></small>
								</td>
								<td width='13%' class='middle center' style='min-width: 55px;' >".$arrayList['status_nome']."</td>
								<td width='13%' class='middle center' nowrap>".Date::fromMysql($arrayList['data_cadastro'], 2)."</td>
								<td width='13%' class='middle center' style='min-width: 55px;' >";

									if (Sis::checkPerm('10002-2') || Sis::checkPerm('10002-5'))
									{
										echo "<a class='a_tooltip' data-placement='top' title='Detalhes' href='?mod=" .$mod. "&pag=" . $pag . "&act=view&pid=" . $arrayList['pedido_idx'] . "' ><i class='fa fa-pencil-square-o'></i></a>&nbsp;
												<a class='a_tooltip' data-placement='top' title='Excluir' href='#' onclick='javascript: if (confirm(&quot;Você deseja excluir os dados?&quot;)) { window.location=&quot;?mod=" . $mod . "&pag=" . $pag . "&amp;act=del&pid=".$arrayList['pedido_idx']."&quot;; } else { return false; };' ><i class='fa fa-trash-o'></i></a>
												";
									}
					echo "
								</td>
							</tr>";
					}
					echo "
					</tbody>
				</table>
				</div></form>";

			echo'
				
			      	
				<div class="clear-fix" ></div><br />
			';

			echo'
				<nav class="navbar navbar-default" role="navigation">';
					if($pg > 1){
						$prev = $pg-1;
			      	echo '<ul class="nav navbar-nav" title="Anterior"><li>
			      				<a href="/admin/?'.'mod='.$mod.'&pag='.$pag.'&act='.$act.'&pg='.$prev.'"><i class="fa fa-chevron-left"></i></a>
			      			</li></ul>';
			      }else{
			      	echo'<ul class="nav navbar-nav"><li><a style="filter:alpha(opacity:30);opacity:0.3;-moz-opacity:0.3;cursor:default;" href="#"><i class="fa fa-chevron-left"></i></a></li></ul>';
			      }

			      echo '<p class="navbar-text">Página '.$pg.' / '.$totalPages.'</p>';

			      if($pg < $totalPages){
			      	$next = $pg + 1;
			   		echo '<ul class="nav navbar-nav"><li>
			   					<a href="/admin/?'.'mod='.$mod.'&pag='.$pag.'&act='.$act.'&pg='.$next.'"><i class="fa fa-chevron-right"></i></a>
			   				</li></ul>';
			   	}else{
			   		echo '<ul class="nav navbar-nav"><li><a style="filter:alpha(opacity:30);opacity:0.3;-moz-opacity:0.3;cursor:default;" href="#"><i class="fa fa-chevron-right"></i></a></li></ul>';
			   	}

			      echo '
			      <p class="navbar-text">Ir para a página</p>
			      <form class="navbar-form navbar-left" method="post" name="form_search" class="form_search">
			      	<div class="form-group">
			         	<input type="text" class="form-control" name="pg" style="width:40px;height:32px;" >
			      	</div>
			      	<button type="submit" onclick="$(".form_search").submit();" class="btn btn-default">Ok</button>
			      </form>
			      <p style="margin: 15px 10px 10px 10px;" class="pull-right">'.$totalRegistros.' registros no total.</p>
				</nav>
			';

		}else {
				echo "
	            <div class='alert alert-warning'>
	                <i class='fa fa-exclamation-triangle'></i>&nbsp;&nbsp;
	                Nenhum registro encontrado.
	            </div>
	        ";
		}
	}else {
			echo "
            <div class='alert alert-warning'>
                <i class='fa fa-exclamation-triangle'></i>&nbsp;&nbsp;
                Nenhum registro encontrado. *
            </div>
        ";
	}
?>

<script type="text/javascript" >
	function salva_ordem()
	{
		var ordem = $("#caixa-fabricante").sortable("toArray");
		var url = "modulos/ecommerce/ecommerce-exe.php?exe=1&ordem="+ordem;
		$.ajax({
		  url: url
		}).done(function(data) {
		  	if(data != "ok"){
		  		console.log(data);
		  	}
		});
	}
	$("#caixa-fabricante").sortable({ axis: "y", stop:salva_ordem });
</script>