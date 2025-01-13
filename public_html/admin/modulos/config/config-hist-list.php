<?php

	$_SESSION['filtros'] = (isset($_SESSION['filtros'])) ? $_SESSION['filtros'] : "";
	$_SESSION['filtros_texto'] = (isset($_SESSION['filtros_texto'])) ? $_SESSION['filtros_texto'] : "";

	$exe = isset($_POST['exe']) ? (int)$_POST['exe'] : 0;
	if($exe == "1"){
		$_SESSION['filtros']="";
		$_SESSION['filtros_texto']="";
	}

	if($exe == "2"){
		$_SESSION['filtros'] = "";
		$_SESSION['filtros_texto'] = "";

		$acao 				= isset($_POST['acao']) ? (int)$_POST['acao'] : 0;
		$modulo 				= isset($_POST['modulo']) ? (int)$_POST['modulo'] : 0;
		$palavra_chave 	= (isset($_POST['palavra_chave']) && $_POST['palavra_chave'] != "") ? Text::clean($_POST['palavra_chave']) : "";
		$usuario 			= isset($_POST['usuario']) ? (int)$_POST['usuario'] : 0;
		$pesquisa_periodo	= isset($_POST['pesquisa_periodo']) ? (int)$_POST['pesquisa_periodo'] : 0;
		$data_de 			= (isset($_POST['data_de']) && $_POST['data_de'] != "") ? Text::clean($_POST['data_de']) : "";
		$data_ate 			= (isset($_POST['data_ate']) && $_POST['data_ate'] != "") ? Text::clean($_POST['data_ate']) : "";

		if($acao != 0){
			$_SESSION['filtros']['acao'] = $acao;
			$_SESSION['filtros_texto'] .= ($_SESSION['filtros_texto'] != "") ? "&nbsp;&nbsp;-&nbsp;&nbsp;" : "";
			if($_SESSION['filtros']['acao'] == 1){
				$_SESSION['filtros_texto'] .= '<b>Acao:</b> Inserir';
			}elseif($_SESSION['filtros']['acao'] == 2){
				$_SESSION['filtros_texto'] .= '<b>Acao:</b> Alterar';
			}else{
				$_SESSION['filtros_texto'] .= '<b>Acao:</b> Excluir';
			}
		}
		if($modulo != 0){
			$_SESSION['filtros']['modulo'] = $modulo;
			$_SESSION['filtros_texto'] .= ($_SESSION['filtros_texto'] != "") ? "&nbsp;&nbsp;-&nbsp;&nbsp;" : "";
			$moduloInfo = $directIn->getAllModules($_SESSION['filtros']['modulo']);
			if($moduloInfo){
				$_SESSION['filtros_texto'] .= '<b>Módulo:</b> "'.$moduloInfo[0]['nome'].'"';
			}
		}
		if($usuario != 0){
			$_SESSION['filtros']['usuario'] = $usuario;
			$_SESSION['filtros_texto'] .= ($_SESSION['filtros_texto'] != "") ? "&nbsp;&nbsp;-&nbsp;&nbsp;" : "";
			$usuarioInfo = $directIn->getAllUsers($_SESSION['filtros']['usuario']);
			if($usuarioInfo){
				$_SESSION['filtros_texto'] .= '<b>Usuário:</b> "'.$usuarioInfo[0]['nome'].'"';
			}
		}
		if($palavra_chave != ""){
			$_SESSION['filtros']['palavra_chave'] = $palavra_chave;
			$_SESSION['filtros_texto'] .= ($_SESSION['filtros_texto'] != "") ? "&nbsp;&nbsp;-&nbsp;&nbsp;" : "";
			$_SESSION['filtros_texto'] .= '<b>Palavra Chave:</b> "'.$_SESSION['filtros']['palavra_chave'].'"';
		}
		if($pesquisa_periodo != 0){
			if ($data_de != "") {
				if ($data_ate == "") {
					$_SESSION['filtros']['data_de'] = "";
				}else{
					$_SESSION['filtros']['data_de'] = Date::toMysql($data_de);
					$_SESSION['filtros_texto'] .= ($_SESSION['filtros_texto'] != "") ? "&nbsp;&nbsp;-&nbsp;&nbsp;" : "";
					$_SESSION['filtros_texto'] .= "<b>Contatos de:</b> ".Date::fromMysql($_SESSION['filtros']['data_de']);
				}
			}if ($data_ate != "") {
				if ($data_de == "") {
					$_SESSION['filtros']['data_ate'] = "";
				}
				$_SESSION['filtros']['data_ate'] = Date::toMysql($data_ate);
				$_SESSION['filtros_texto'] .= ($_SESSION['filtros_texto'] != "") ? "&nbsp;&nbsp;-&nbsp;&nbsp;" : "";
				if($data_de == ""){
					$_SESSION['filtros_texto'] .= "<b>Contatos até:</b> ".Date::fromMysql($_SESSION['filtros']['data_ate']);
				}else{
					$_SESSION['filtros_texto'] .= "<b>Até:</b> ".Date::fromMysql($_SESSION['filtros']['data_ate']);
				}
			}
		}

		if($_SESSION['filtros_texto'] != ""){
			$_SESSION['filtros_texto'] = '<div class="list-group-item" >Resultado Pesquisa: <br>'.$_SESSION['filtros_texto'].'</div>';
		}

	}
?>

<ul class="breadcrumb">
	<li><a href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>">Configurações</a></li>
	<li>Histórico</li>
</ul>

<div class="btn-group">
   <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>">Lista de variáveis</a>
   <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=add">Criar nova variável</a>
   <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=hist-list" disabled="disabled">Histórico</a>
   <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=<?php echo $pag; ?>&act=sis-list">Configurações do sistema</a>
</div>

<hr />

<form  action="<?php echo sis::currPageUrl(); ?>" method="post" enctype="multipart/form-data" class="form_limpa" name="form_limpa">
	<input type="hidden" name="exe" value="1">
</form>

<div class="panel panel-default">
	<div class="panel-heading">
		<a href="javascript:;" class="btn btn-default btn-sm" onclick="$('.search-form').slideToggle('fast');" >Filtros de pesquisa</a>
		<input type="submit" <?php echo (!is_array($_SESSION['filtros'])) ? 'style="display: none;"' : ""; ?> class="btn btn-default btn-sm" onclick="$('.form_limpa').submit();" value="Limpar filtros de pesquisa">
	</div>
	<div class="s-hidden list-group search-form" >
		<div class="s-hidden list-group-item " >

			<form  action="<?php echo sis::currPageUrl(); ?>" method="post" enctype="multipart/form-data" class="form_dados" name="form_dados">
				<input type="hidden" name="exe" value="2">
				<table class="table table_filters" width="300">
					<tr>
			         <td width="25%" class="middle bg_white">
			         	<select class="form-control" name="acao">
			         		<option value="0">Açao</option>
			         		<option value="1">Inserir</option>
			         		<option value="2">Alterar</option>
			         		<option value="3">Excluir</option>
			         	</select>
			         </td>
			         <td width="25%" class="middle bg_white">
			         	<select class="form-control" name="modulo">
			         		<option value="0">Módulo</option>
								<?php
									$modulosList = $directIn->getAllModules();
									if($modulosList){
										foreach ($modulosList as $key => $modulo) {
											echo '<option value="'.$modulo['codigo'].'">'.$modulo['nome'].'</option>';
										}
									}
								?>
			         	</select>
			         </td>
			         <td width="25%" class="middle bg_white">
			         	<select class="form-control" name="usuario">
			         		<option value="0">Usuário</option>
			         		<?php
									$userList = $directIn->getAllUsers();
									if($userList){
										foreach ($userList as $key => $usurario) {
											echo '<option value="'.$usurario['usuario_idx'].'">'.$usurario['nome'].'</option>';
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
						<td colspan="3"></td>

					</tr>

					<tr>
		            <td colspan="4" class="right" >
		               <input type="button" value="Cancelar" class="btn btn-default" onclick="$('.search-form').slideToggle('fast');" >
		               <input type="submit" value="Pesquisar" class="btn btn-primary" data-loading-text="Carregando...">
		            </td>
		        </tr>

				</table>
			</form>
			<div class="clearfix"></div>

		</div>
	</div>
	<div class="<?php echo (!isset($_SESSION['filtros_texto'])) ? 's-hidden' : ""; ?> list-group search-result" >
		<?php echo ($_SESSION['filtros_texto']!="") ? $_SESSION['filtros_texto'] : ""; ?>
	</div>
</div>

<?php
// var_dump($_POST);
				// die();
	$atualPageDi = (isset($_POST['pg'])) ? (int)$_POST['pg'] : 1;
	$atualPageDi = (!is_numeric($atualPageDi) || $atualPageDi == 0) ? 1 : $atualPageDi;

	$list = $directIn->listAllLog($_SESSION['filtros'], $atualPageDi);

	global $totalPages;

	if(is_object($list)){
		if(is_array($list->{'resultado'}) && count($list->{'resultado'}) > 0){
			$totalPages = $list->{'totalPaginas'};
			$iCount=0;
?>

			<div class="panel panel-default">
		   	<div class="panel-heading">Histórico de ações do sistema</div>
				<?php
				if (isset($_POST['pg']) && (int)$_POST['pg'] > $totalPages) {
					die(Sis::setAlert("Nenhum registro encontrado na página ".(int)$_POST['pg'].".", 1));
				}
					echo '
				<table class="table table-hover table-striped table_list">
					<thead>
						<tr>
							<th width="0%">#</th>
							<th width="20%">Ação / Usuário</th>
							<th width="0%" nowrap>IP Usuário</th>
							<th width="80%">Descrição</th>
							<th width="0%" >Horário</th>
						</tr>
					</thead>
					<tbody>
					';
					foreach ($list->{'resultado'} as $arrayList){
						$iCount++;
						$detalhesLog 	= "";

						switch ($arrayList['acao']) {
							case "INSERT":
								$acao = "inserido(a)";
								break;
							case "UPDATE":
								$acao = "alterado(a)";
								break;
							case "DELETE":
								$acao = "excluído(a)";
								break;
							default:
								$acao = $arrayList['acao'];
								break;
						}
						if($arrayList['modulo_area'] != ""){
							$detalhesLog = $arrayList['modulo_area']." ".$acao. (($arrayList['usr_nome'] == "") ? "" : " por " . $arrayList['usr_nome']);
						}
						echo "
							<tr>
								<td>".$iCount."</td>
								<td>" . $detalhesLog. " </td>
								<td>" . $arrayList['ip_usuario'] . "</td>
								<td>".$arrayList['descricao']."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
								<td nowrap>".Date::fromMysql($arrayList['data'],2)."</td>
							</tr>
						";
					}
				echo '</tbody>
				</table>
			</div>';

			$atualPage = (isset($_POST['pg']))?(int)$_POST['pg']:1;
			if($atualPage < 1){
				$atualPage = 1;
			}

			if(isset($_POST['pg'])){
				$_SESSION['paginacao'] = (int)$_POST['pg'];
			}elseif(isset($_GET['pg'])){
				$_SESSION['paginacao'] = (int)$_GET['pg'];
			}
			if(!isset($_SESSION['paginacao']) || $_SESSION['paginacao'] == ""){
				$_SESSION['paginacao'] = $atualPage;
			}elseif(isset($_SESSION['paginacao']) && $_SESSION['paginacao'] != ""){
				$atualPage = $_SESSION['paginacao'];
			}

			echo'
				<nav class="navbar navbar-default" role="navigation">';
					if($atualPage > 1){
						$prev = $atualPage-1;
			      	echo '<ul class="nav navbar-nav" title="Anterior"><li>
			      				<a href="'.$_SERVER['PHP_SELF'].'?'.'mod='.$mod.'&pag='.$pag.'&act='.$act.'&pg='.$prev.'"><i class="fa fa-chevron-left"></i></a>
			      			</li></ul>';
			      }else{
			      	echo'<ul class="nav navbar-nav"><li><a style="filter:alpha(opacity:30);opacity:0.3;-moz-opacity:0.3;cursor:default;" href="#"><i class="fa fa-chevron-left"></i></a></li></ul>';
			      }

			      echo '<p class="navbar-text">Página '.$atualPage.' / '.$totalPages.'</p>';

			      if($atualPage < $totalPages){
			      	$next = $atualPage + 1;
			   		echo '<ul class="nav navbar-nav"><li>
			   					<a href="'.$_SERVER['PHP_SELF'].'?'.'mod='.$mod.'&pag='.$pag.'&act='.$act.'&pg='.$next.'"><i class="fa fa-chevron-right"></i></a>
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
				</nav>
			';

		} else {
			echo "
	               <div class='alert alert-warning'>
	                  <i class='fa fa-exclamation-triangle'></i>&nbsp;&nbsp;Nenhum registro encontrado.
	               </div>
	            ";
		}
	} else {
		echo "
               <div class='alert alert-warning'>
                  <i class='fa fa-exclamation-triangle'></i>&nbsp;&nbsp;Nenhum registro encontrado.
               </div>
            ";
	}
?>