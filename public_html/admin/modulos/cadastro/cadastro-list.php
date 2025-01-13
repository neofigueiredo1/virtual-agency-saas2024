<?php
	$filtrosTxt = "";
	$_SESSION['filtros'] = (isset($_SESSION['filtros'])) ? $_SESSION['filtros'] : [];
	$_SESSION['filtros_texto'] = (isset($_SESSION['filtros_texto'])) ? $_SESSION['filtros_texto'] : "";

	$exe = isset($_POST['exe']) ? (int)$_POST['exe'] : 0;
	if($exe == "1"){
		$_SESSION['filtros']="";
		$_SESSION['filtros_texto']="";
	}

	if($exe == "2"){
		$_SESSION['filtros'] = [];
		$_SESSION['filtros_texto'] = "";

		$status 				= isset($_POST['status']) ? (int)$_POST['status'] : 0;
		$perfil 				= isset($_POST['perfil']) ? (int)$_POST['perfil'] : 0;
		$genero 				= isset($_POST['genero']) ? (int)$_POST['genero'] : 0;
		$palavra_chave 	= (isset($_POST['palavra_chave']) && $_POST['palavra_chave'] != "") ? Text::clean($_POST['palavra_chave']) : "";
		$receber_boletim 	= isset($_POST['receber_boletim']) ? (int)$_POST['receber_boletim'] : 0;
		$pesquisa_periodo	= isset($_POST['pesquisa_periodo']) ? (int)$_POST['pesquisa_periodo'] : 0;
		$data_de 			= (isset($_POST['data_de']) && $_POST['data_de'] != "") ? Text::clean($_POST['data_de']) : "";
		$data_ate 			= (isset($_POST['data_ate']) && $_POST['data_ate'] != "") ? Text::clean($_POST['data_ate']) : "";
		$area_interesse	= isset($_POST['area_interesse']) ? $_POST['area_interesse'] : 0;

		$resultadoPesquisa = "";
		if($status != 0){
			$_SESSION['filtros']['status'] = ($status==2) ? 0 : 1;
			$statusText = ($_SESSION['filtros']['status'] == 1) ? "Ativo" : "Inativo";
			$_SESSION['filtros_texto'] .= "<b>Situação:</b> ".$statusText;
		}
		if($perfil != 3){
			$_SESSION['filtros']['perfil'] = (int)$perfil;
			$_SESSION['filtros_texto'] .= "<b>Perfil:</b> ".$directIn->perfis[(int)$_SESSION['filtros']['perfil']];
		}
		if($genero != 0){
			$_SESSION['filtros']['genero'] = ($genero==2) ? 0 : 1;
			$generoText = ($_SESSION['filtros']['genero'] == 1) ? "Masculino" : "Feminimo";
			$resultadoPesquisa .= ($resultadoPesquisa != "") ? "&nbsp;&nbsp;-&nbsp;&nbsp;" : "";
			$_SESSION['filtros_texto'] .= "<b>Gênero:</b> ".$generoText;
		}
		if($palavra_chave != ""){
			$_SESSION['filtros']['palavra_chave'] = $palavra_chave;
			$_SESSION['filtros_texto'] .= ($_SESSION['filtros_texto'] != "") ? "&nbsp;&nbsp;-&nbsp;&nbsp;" : "";
			$_SESSION['filtros_texto'] .= '<b>Palavra Chave:</b> "'.$_SESSION['filtros']['palavra_chave'].'"';
		}
		if($receber_boletim != 0){
			$_SESSION['filtros']['receber_boletim'] = ($receber_boletim==2) ? 0 : 1;
			$receber_boletimText = ($_SESSION['filtros']['receber_boletim'] == 1) ? "Sim" : "Não";
			$_SESSION['filtros_texto'] .= ($_SESSION['filtros_texto'] != "") ? "&nbsp;&nbsp;-&nbsp;&nbsp;" : "";
			$_SESSION['filtros_texto'] .= "<b>Receber boletim:</b> ".$receber_boletimText;
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
		if($area_interesse != 0){
			$_SESSION['filtros']['area_interesse'] = $area_interesse;
			$area = $directIn->listAreaSelected($_SESSION['filtros']['area_interesse']);
			$_SESSION['filtros_texto'] .= ($_SESSION['filtros_texto'] != "") ? "&nbsp;&nbsp;-&nbsp;&nbsp;" : "";
			$_SESSION['filtros_texto'] .= "<b>Área de interesse:</b> ".$area[0]['nome'];
		}

	}

	if($_SESSION['filtros_texto'] != ""){
		$filtrosTxt = '<div class="list-group-item" >Resultado Pesquisa: <br>'.$_SESSION['filtros_texto'].'</div>';
	}
?>

<ol class="breadcrumb">
    <li class="active">Cadastros</li>
</ol>

<div class="btn-group">
  	<a class="btn btn-default" href="?mod=<?php echo $mod ?>&pag=cadastro" disabled="disabled">Cadastros</a>
  	<a class="btn btn-default" href="?mod=<?php echo $mod ?>&pag=cadastro&act=add" >Adicionar cadastro</a>
</div>
<div class="btn-group">
  	<a class="btn btn-default" href="?mod=<?php echo $mod ?>&pag=area-interesse">Áreas de interesse</a>
  	<a class="btn btn-default" href="?mod=<?php echo $mod ?>&pag=area-interesse&act=add" >Nova área de interesse</a>
</div>
<div class="btn-group">
  	<a class="btn btn-success" href="?mod=<?php echo $mod ?>&pag=cadastro&act=exportar">Exportar cadastros</a>
</div>

<hr />

<form  action="<?php echo sis::currPageUrl(); ?>" method="post" enctype="multipart/form-data" class="form_limpa" name="form_limpa">
	<input type="hidden" name="exe" value="1">
</form>

<div class="panel panel-default">
	<div class="panel-heading">
		<a href="javascript:;" class="btn btn-default btn-sm" onclick="$('.search-form').slideToggle('fast');" >Filtros de pesquisa</a>
		<input type="submit" <?php echo (!is_array($_SESSION['filtros'])) ? 'style="display: none;"' : ""; ?> class="btn btn-default btn-sm" onclick="$('.form_limpa').submit();" value="Limpar filtros de pesquisa" >
	</div>
	<div class="s-hidden list-group search-form" >
		<div class="s-hidden list-group-item " >

			<form  action="<?php echo sis::currPageUrl(); ?>" method="post" enctype="multipart/form-data" class="form_dados" name="form_dados">
				<input type="hidden" name="exe" value="2">
				<table class="table table_filters" width="300">
					<tr>
			         <td width="25%" class="middle bg_white">
			         	<select class="form-control" name="status">
			         		<option value="0">Situação</option>
			         		<option value="1">Ativo</option>
			         		<option value="2">Inativo</option>
			         		<option value="4">Newsletter</option>
			         	</select>
			         </td>
			         <td width="25%" class="middle bg_white">
			         	<select class="form-control" name="genero">
			         		<option value="0">Gênero</option>
			         		<option value="1">Masculino</option>
			         		<option value="2">Feminino</option>
			         	</select>
			         </td>
			         <td width="25%" class="middle bg_white">
			         	<select class="form-control" name="receber_boletim">
			         		<option value="0">Receber boletim?</option>
			         		<option value="1">Sim</option>
			         		<option value="2">Não</option>
			         	</select>
			         </td>
			         <td width="25%">
							<select class="form-control" name="area_interesse">
			         		<option value="0">Área de interesse</option>
								<?php
			                  $lista = $directIn->listInterest();
			                  if(is_array($lista)&&count($lista)>0){
				                  foreach($lista as $arrayListInteresse){
			                        echo "<option value='" . $arrayListInteresse['interesse_idx'] ."'> " . $arrayListInteresse['nome'] . "</option>";
				                  }
			                  }
			               ?>
			            </select>
						</td>
					</tr>
					<tr>
			         <td class="middle bg_white">
			         	<input type="search" class="form-control" name="palavra_chave" placeholder="Palavra-chave" />
			         </td>
			         <td width="25%" class="middle bg_white">
			         	<select class="form-control" name="perfil">
			         		<option value="3" >Perfil</option>
			         		<option value="0" >Aluno</option>
			         		<option value="1"> Produtor</option>
			         		<option value="2" >Co-Produtor</option>
			         	</select>
			         </td>
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
					</tr>

					<tr>
						<td colspan="3"></td>
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

		</div>
	</div>
	<div class="<?php echo (!isset($filtrosTxt)) ? 's-hidden' : ""; ?> list-group search-result" >
		<?php echo ($filtrosTxt!="") ? $filtrosTxt : ""; ?>
	</div>
</div>

<!-- <hr /> -->
<?php
	$atualPageDi = (isset($_GET['pg']))?(int)$_GET['pg']:1;
	$atualPageDi = (isset($_POST['pg']))?(int)$_POST['pg']:$atualPageDi;

	$list = $directIn->listAll($_SESSION['filtros'], $atualPageDi);

	//global $totalPages;

	// if (isset($_POST['pg']) && (int)$_POST['pg'] > $totalPages) {
	// 	die("
	// 		<div class='alert alert-warning'>
 //            <i class='fa fa-exclamation-triangle'></i>&nbsp;&nbsp;
 //            Nenhum registro encontrado.
 //         </div>");
	// }

	if(is_object($list)){
		if(is_array($list->{'resultado'}) && count($list->{'resultado'}) > 0){
			$totalPages = $list->{'totalPaginas'};
			$iCount=0;
			echo '
			<div class="panel panel-default">
			   <div class="panel-heading">Lista de cadastros do site</div>
				<table class="table table-hover table-bordered table-striped table_list">
					<thead>
						<tr>
							<th width="0" >ID</th>
							<th width="50%">Nome</th>
							<th>E-mail</th>
							<th width="50%" >Perfil</th>
							<th class="center">Status</th>
							<th class="center" nowrap>Data de Cadastro</th>
							<th class="center" nowrap>Ações</th>
						</tr>
					</thead>
					<tbody>
					';
					foreach ($list->{'resultado'} as $arrayList){
						$iCount++;
						$statusStr = "";
						switch ($arrayList['status']) {
							case 0:
								$statusStr = '<span style="color:red;" >Inatvo</span>';
								break;
							case 1:
								$statusStr = '<span style="color:green;" >Ativo</span>';
								break;
							case 4:
								$statusStr = '<span style="color:#006699;" >Newsletter</span>';
								break;
							default:
								$statusStr = '<span style="color:red;" >Inatvo</span>';
								break;
						}


						$perfilStr = ($arrayList['perfil']!=null)?$directIn->perfis[$arrayList['perfil']]:'';
						if ($arrayList['perfil']==1) {//Produtor
							// $perfilStr = "Produtor";
							if (
								trim($arrayList['iugu_split_subcount_name']) == '' ||
								trim($arrayList['iugu_split_account_id']) == '' ||
								trim($arrayList['iugu_split_live_api_token']) == '' ||
								trim($arrayList['iugu_split_test_api_token']) == ''
							){
								$perfilStr .= '<br/><span class="label label-warning" >IMPORTANTE! Dados de conta Iugu imcompletos.</span>';
							}else{
								$perfilStr .= '<br/><span class="label label-success" >Dados de conta Iugu incluídos</span>';
							}
						}
						if ($arrayList['perfil']==2) {//Co-Produtor
							
							if (
								trim($arrayList['iugu_split_subcount_name']) == '' ||
								trim($arrayList['iugu_split_account_id']) == ''
							){
								$perfilStr .= '<br/><span class="label label-warning" >IMPORTANTE! Dados de conta Iugu imcompletos.</span>';
							}else{
								$perfilStr .= '<br/><span class="label label-success" >Dados de conta Iugu incluídos</span>';
							}
						}

						echo "
						<tr>
							<td width='25%'>#".$arrayList['cadastro_idx']."</td>
							<td width='20%' nowrap>
								<a href='?mod=".$mod."&pag=" . $pag . "&act=edit&id=" . $arrayList['cadastro_idx'] . "' >" . $arrayList['nome_completo'] . "</a>
							</td>
							<td width='25%'>".$arrayList['email']."</td>
							<td width='50%'>".$perfilStr."</td>
							<td class='center'>" . $statusStr . "</td>
							<td class='center' nowrap>" . Date::fromMysql($arrayList['data_cadastro']) . "</td>
							<td nowrap class='center'>
								<a class='a_tooltip' data-placement='top' title='Editar' href='?mod=".$mod."&pag=" . $pag . "&act=edit&id=" . $arrayList['cadastro_idx'] . "' >
									<i class='fa fa-pencil-square-o'></i>
								</a>
								&nbsp;
								<a class='a_tooltip' data-placement='top' title='Excluir' href='#' onclick='javascript: if (confirm(&quot;Você deseja excluir os dados?&quot;)) { window.location=&quot;?mod=".$mod."&pag=" . $pag . "&act=del&id=" . $arrayList['cadastro_idx'] . "&quot;; } else { return false; };' >
									<i class='fa fa-trash-o'></i>
								</a>
							</td>
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
			      				<a href="'.$_SERVER['PHP_SELF'].'?'.'mod='.$mod.'&pag='.$pag.'&pg='.$prev.'"><i class="fa fa-chevron-left"></i></a>
			      			</li></ul>';
			      }else{
			      	echo'<ul class="nav navbar-nav"><li><a style="filter:alpha(opacity:30);opacity:0.3;-moz-opacity:0.3;cursor:default;" href="#"><i class="fa fa-chevron-left"></i></a></li></ul>';
			      }

			      echo '<p class="navbar-text">Página '.$atualPage.' / '.$totalPages.'</p>';

			      if($atualPage < $totalPages){
			      	$next = $atualPage + 1;
			   		echo '<ul class="nav navbar-nav"><li>
			   					<a href="'.$_SERVER['PHP_SELF'].'?'.'mod='.$mod.'&pag='.$pag.'&pg='.$next.'"><i class="fa fa-chevron-right"></i></a>
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
				      <p class="navbar-text">'.$list->{'totalRegistros'}.' registros retornados.</p>
				</nav>
			';

		} else {
			echo "
					<div class='alert alert-warning'>
	               <i class='fa fa-exclamation-triangle'></i>&nbsp;&nbsp;
	               Nenhum registro encontrado.
	            </div>";
		}
	}else {
			echo "
					<div class='alert alert-warning'>
	               <i class='fa fa-exclamation-triangle'></i>&nbsp;&nbsp;
	               Nenhum registro encontrado.
	            </div>";
	}

	?>
