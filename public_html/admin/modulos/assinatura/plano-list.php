<?php
	if(!Sis::checkPerm($modulo['codigo'].'-2') && !Sis::checkPerm($modulo['codigo'].'-1')){
		Sis::setAlert('Você não tem acesso à este recurso!', 1, '/admin/');
	}

	/**
	 * Filtros de pesquisa
	 */
	$_SESSION['filtros_ecommerce_produtos'] = (isset($_SESSION['filtros_ecommerce_produtos'])) ? $_SESSION['filtros_ecommerce_produtos'] : "";
	$_SESSION['filtros_ecommerce_produtos_texto'] = (isset($_SESSION['filtros_ecommerce_produtos_texto'])) ? $_SESSION['filtros_ecommerce_produtos_texto'] : "";

	$exe = isset($_POST['exe']) ? (int)$_POST['exe'] : 0;
	$recycle = isset($_GET['recycle']) ? (int)$_GET['recycle'] : 0;
	if ($recycle == "1") {
		$pid = isset($_GET['pid']) ? (int)$_GET['pid'] : 0;
		if ($pid!=0) {
			$directIn->produtoMockupRecycle($pid);
		}
	}
	/**
	 * Ação que limpa os filtros
	 */
	if($exe == "1"){
		$_SESSION['filtros_ecommerce_produtos'] = "";
		$_SESSION['filtros_ecommerce_produtos_texto'] = "";
	}


	if($exe == "2"){
		$_SESSION['filtros_ecommerce_produtos'] = [];
		$_SESSION['filtros_ecommerce_produtos_texto'] = "";

		$valor 		= isset($_POST['valor']) ? (int)$_POST['valor'] : 0;
		$estoque 		= isset($_POST['estoque']) ? (int)$_POST['estoque'] : 0;
		$imagem 		= isset($_POST['imagem']) ? (int)$_POST['imagem'] : 0;

		$status 		= isset($_POST['status']) ? (int)$_POST['status'] : 0;
		$oferta 		= isset($_POST['oferta']) ? (int)$_POST['oferta'] : 0;
		$categoria 		= isset($_POST['categoria']) ? (int)$_POST['categoria'] : 0;
		$fabricante 	= isset($_POST['fabricante']) ? (int)$_POST['fabricante'] : 0;
		$avaliados 		= isset($_POST['avaliados']) ? (int)$_POST['avaliados'] : 0;
		$destaque 	= isset($_POST['destaque']) ? (int)$_POST['destaque'] : 0;
		$palavra_chave  = (isset($_POST['palavra_chave']) && $_POST['palavra_chave'] != "") ? Text::clean($_POST['palavra_chave']) : "";


		$resultadoPesquisa = "";
		if($valor != 0){
			$_SESSION['filtros_ecommerce_produtos']['valor'] = ($valor==2) ? 0 : 1;
			$valorText = ($_SESSION['filtros_ecommerce_produtos']['valor'] == 1) ? "Com o pre&ccedil;o" : "Sem o pre&ccedil;o";
			$resultadoPesquisa .= ($resultadoPesquisa != "") ? "&nbsp;&nbsp;-&nbsp;&nbsp;" : "";
			$resultadoPesquisa .= "<b>Pre&ccedil;o:</b> ".$valorText;
		}

		if($estoque != 0){
			//$_SESSION['filtros_ecommerce_produtos']['estoque'] = ($estoque==2) ? 0 : 1;
			$_SESSION['filtros_ecommerce_produtos']['estoque'] = $estoque;
			//$estoqueText = ($_SESSION['filtros_ecommerce_produtos']['estoque'] == 1) ? "Com estoque" : "Sem estoque";
			$estoqueText = $directIn->textEstoque($_SESSION['filtros_ecommerce_produtos']['estoque']);
			
			$resultadoPesquisa .= ($resultadoPesquisa != "") ? "&nbsp;&nbsp;-&nbsp;&nbsp;" : "";
			$resultadoPesquisa .= "<b>Estoque:</b> ".$estoqueText;
		}
		
		if($imagem != 0){
			$_SESSION['filtros_ecommerce_produtos']['imagem'] = ($imagem==2) ? 0 : 1;
			$imagemText = ($_SESSION['filtros_ecommerce_produtos']['imagem'] == 1) ? "Com imagem" : "Sem imagem";
			$resultadoPesquisa .= ($resultadoPesquisa != "") ? "&nbsp;&nbsp;-&nbsp;&nbsp;" : "";
			$resultadoPesquisa .= "<b>Imagem:</b> ".$imagemText;
		}

		if($status != 0){
			$_SESSION['filtros_ecommerce_produtos']['status'] = ($status==2) ? 0 : 1;
			$statusText = ($_SESSION['filtros_ecommerce_produtos']['status'] == 1) ? "Ativo" : "Inativo";
			$resultadoPesquisa .= ($resultadoPesquisa != "") ? "&nbsp;&nbsp;-&nbsp;&nbsp;" : "";
			$resultadoPesquisa .= "<b>Situacao:</b> ".$statusText;
		}

		if($oferta != 0){
			$_SESSION['filtros_ecommerce_produtos']['oferta'] = ($oferta==2) ? 0 : 1;
			$ofertaText = ($_SESSION['filtros_ecommerce_produtos']['oferta'] == 1) ? "Sim" : "Não";
			$resultadoPesquisa .= ($resultadoPesquisa != "") ? "&nbsp;&nbsp;-&nbsp;&nbsp;" : "";
			$resultadoPesquisa .= "<b>Em oferta:</b> ".$ofertaText;
		}
		
		if($categoria != 0){
			$_SESSION['filtros_ecommerce_produtos']['categoria'] = $categoria;
			$categoria = $m_categoria->categoriasListSelected($_SESSION['filtros_ecommerce_produtos']['categoria']);
			$resultadoPesquisa .= ($resultadoPesquisa != "") ? "&nbsp;&nbsp;-&nbsp;&nbsp;" : "";
			$resultadoPesquisa .= "<b>Categoria:</b> ".$categoria[0]['nome'];
		}
		if($fabricante != 0){
			$_SESSION['filtros_ecommerce_produtos']['fabricante'] = $fabricante;
			$fabricante = $m_fabricante->fabricantesListSelected($_SESSION['filtros_ecommerce_produtos']['fabricante']);
			$resultadoPesquisa .= ($resultadoPesquisa != "") ? "&nbsp;&nbsp;-&nbsp;&nbsp;" : "";
			$resultadoPesquisa .= "<b>Fabricante:</b> ".$fabricante[0]['nome'];
		}
		if($avaliados != 0){
			$_SESSION['filtros_ecommerce_produtos']['avaliados'] = $avaliados;
			if($_SESSION['filtros_ecommerce_produtos']['avaliados'] == 1){
				$avaliadosText = "Com avaliação";
			}else if($_SESSION['filtros_ecommerce_produtos']['avaliados'] == 2){
				$avaliadosText = "Sem avaliação";
			}else if($_SESSION['filtros_ecommerce_produtos']['avaliados'] == 3){
				$avaliadosText = "Aguardando aprovação";
			}
			$resultadoPesquisa .= ($resultadoPesquisa != "") ? "&nbsp;&nbsp;-&nbsp;&nbsp;" : "";
			$resultadoPesquisa .= "<b>Avaliados:</b> ".$avaliadosText;
		}
		if($destaque != 0){
			$_SESSION['filtros_ecommerce_produtos']['destaque'] = ($destaque==2) ? 0 : 1;
			$destaqueText = ($_SESSION['filtros_ecommerce_produtos']['destaque'] == 1) ? "Sim" : "Não";
			$resultadoPesquisa .= ($resultadoPesquisa != "") ? "&nbsp;&nbsp;-&nbsp;&nbsp;" : "";
			$resultadoPesquisa .= "<b>Destaque:</b> ".$destaqueText;
		}
		if($palavra_chave != ""){
			$_SESSION['filtros_ecommerce_produtos']['palavra_chave'] = $palavra_chave;
			$resultadoPesquisa .= ($resultadoPesquisa != "") ? "&nbsp;&nbsp;-&nbsp;&nbsp;" : "";
			$resultadoPesquisa .= '<b>Palavra Chave:</b> "'.$_SESSION['filtros_ecommerce_produtos']['palavra_chave'].'"';
		}

		if($resultadoPesquisa != ""){
			$_SESSION['filtros_ecommerce_produtos_texto'] = '<div class="list-group-item" >Resultado Pesquisa: <br>'.$resultadoPesquisa.'</div>';
		}
		header("Location: /admin/index.php?mod=ecommercex&pag=produto");
		exit();

	}
?>

<style>.row{margin-top:10px;margin-bottom:10px;}</style>
<ol class="breadcrumb">
   <li><a href="?mod=<?php echo $mod; ?>&pag=pedido">E-commerce</a></li>
   <li>Produtos</li>
</ol>

<?php include_once("produto-menu.php"); ?>

<hr />

<form  action="<?php echo Sis::currPageUrl(); ?>" method="post" enctype="multipart/form-data" class="form_limpa" name="form_limpa">
	<input type="hidden" name="exe" value="1">
</form>


<div class="panel panel-default">
	<div class="panel-heading">
		<a href="javascript:;" class="btn btn-default btn-sm" onclick="$('.search-form').slideToggle('fast');" >Filtros de pesquisa</a>
		<input type="submit" <?php echo (!is_array($_SESSION['filtros_ecommerce_produtos'])) ? 'style="display: none;"' : ""; ?> class="btn btn-success btn-sm" onclick="$('.form_limpa').submit();" value="Limpar filtros de pesquisa">
	</div>
	<div class="s-hidden list-group search-form" >
		<div class="s-hidden list-group-item " >

			<form  action="<?php echo Sis::currPageUrl(); ?>" method="post" enctype="multipart/form-data" class="form_dados" name="form_dados">
				<input type="hidden" name="exe" value="2">

				<div class="row">
					<div class="col-lg-6">
						
						<div class="row">
							<div class="col-sm-4">
								<select class="form-control" name="status">
					         		<option value="0">Situação</option>
					         		<option value="1">Ativo</option>
					         		<option value="2">Inativo</option>
					         	</select>
							</div>
							<div class="col-sm-4">
								<select class="form-control" name="categoria">
					         		<option value="0">Categoria</option>
					         		<?php
					                  $listaCat = $m_categoria->categoriasListAll();
					                  if(is_array($listaCat)&&count($listaCat)>0){
						                  foreach($listaCat as $arrayListInteresse){
					                        echo "<option value='" . $arrayListInteresse['categoria_idx'] ."'> " . $arrayListInteresse['nome'] . "</option>";
						                  }
					                  }
					               ?>
					         	</select>
							</div>
							<div class="col-sm-4">
								<select class="form-control" name="destaque">
					         		<option value="0">Destaque</option>
					         		<option value="1">Sim</option>
					         		<option value="2">Não</option>
					            </select>
							</div>
						</div>

					</div>
					<div class="col-lg-6">
						
						<div class="row">
							<div class="col-sm-4">
								<select class="form-control" name="valor">
									<option value="0">Preço</option>
									<option value="1">Com preço</option>
									<option value="2">Sem preço</option>
								</select>
							</div>
							<div class="col-sm-4">
								<select class="form-control" name="estoque">
									<option value="0" >Estoque</option>
									<option value="1" >Com estoque</option>
									<option value="3" >Estoque baixo</option>
									<option value="2" >Sem estoque</option>
								</select>	
							</div>
							<div class="col-sm-4">
								<select class="form-control" name="imagem">
									<option value="0">Imagem</option>
									<option value="1">Com imagem</option>
									<option value="2">Sem imagem</option>
								</select>
							</div>
						</div>


					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<input type="search" class="form-control" name="palavra_chave" placeholder="Palavra-chave" />
					</div>
					<div class="col-md-6">
						<select class="form-control" name="oferta">
			         		<option value="0">Em oferta</option>
			         		<option value="1">Sim</option>
			         		<option value="2">Não</option>
			         	</select>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12 text-right">
		               <input type="button" value="Cancelar" class="btn btn-default" onclick="$('.search-form').slideToggle('fast');" >
		               <input type="button" value="Pesquisar" class="btn btn-primary" data-loading-text="Carregando..."  onclick="JavaScript:checkFormRequire(document.form_dados,'#error-box');">
					</div>
				</div>

				
			</form>
			<!-- <div class="clearfix"></div> -->

		</div>
	</div>
	<div class="<?php echo (!isset($_SESSION['filtros_ecommerce_produtos_texto'])) ? 's-hidden' : ""; ?> list-group search-result" >
		<?php echo (isset($_SESSION['filtros_ecommerce_produtos_texto']) && $_SESSION['filtros_ecommerce_produtos_texto']!="") ? $_SESSION['filtros_ecommerce_produtos_texto'] : ""; ?>
	</div>
</div>

<?php
	if((isset($_POST['pg']))){
		$atualPageDi = (int)$_POST['pg'];
	}elseif(isset($_GET['pg'])){
		$atualPageDi = (int)$_GET['pg'];
	}else{
		$atualPageDi = 1;
	}
	if(!is_numeric($atualPageDi)){ $atualPageDi=1; }
	if($atualPageDi<1){ $atualPageDi=1; }
	$list = $directIn->listAll($_SESSION['filtros_ecommerce_produtos'], $atualPageDi);
	global $totalPages;

	if(is_array($list->{'resultado'}) && count($list->{'resultado'}) > 0){
		$totalPages = $list->{'totalPaginas'};
		$iCount=0;
		echo
		'
		<div class="panel panel-default">
			<div class="panel-heading">Lista de produtos</div>

				<table class="table table-hover table-striped table_list">
					<thead>
						<tr>
							<th><div style="width:150px;">Imagem</div></th>
							<th width="20%">Código PDV</th>
							<th width="60%">Nome / Preço / Estoque</th>
							<th width="0%" class="center" >Destaque</th>
							<th width="0%" class="center" >Situação</th>
							<th width="0%" class="center" style="min-width: 80px;">Ações</th>
						</tr>
					</thead>
				</table>
				<ul id="caixa-fabricante" class="list-striped" >';
					foreach ($list->{'resultado'} as $i => $arrayList){
						$count = $i + 1;

						$session = isset($_SESSION["filtros_ecommerce_produtos"]["palavra_chave"]) ? $_SESSION["filtros_ecommerce_produtos"]["palavra_chave"] : "";
						if($session != ""){
							$arrayList['nome'] = str_replace($session, "<b>". $session . "</b>", $arrayList['nome']);
						}

						if($session != ""){
							$arrayList['descricao_curta'] = str_replace($session, "<b>". $session . "</b>", $arrayList['descricao_curta']);
						}

						// $txtIntegrado = '<span class="label label-danger" >NÃO</span>';
						// if ((int)$arrayList['pdv_id']!=0) {
						// 	$txtIntegrado = '<span class="label label-success" >SIM</span>';
						// }


						$infoValor = "Preço: ".number_format($arrayList['valor'],2,",",".")."<br/>";
						if ((int)$arrayList['em_oferta']==1) {

							$infoValor = '<span class="label label-warning" >EM OFERTA</span> <br/> 
							Preço normal: R$ '.number_format($arrayList['valor'],2,",",".").'<br/>
							Preço em oferta: <b>R$ '.number_format($arrayList['em_oferta_valor'],2,",",".").'</b><br/>';

							if ((int)$arrayList['em_oferta_expira']==1) {
								$infoValor .= '<span class="label label-danger" >OFERTA EXPIRA EM: '.date("d/m/Y H:i:s",strtotime($arrayList['em_oferta_expira_data'])).'</span><br/> 
								 </br>
								';
							}
							
						}

						//".Sis::getStatusFormat($arrayList['status'])."

						echo "
						<li id='" . $arrayList['produto_idx'] . "' >
							<table class='table table-hover table-striped table_list' style='margin-bottom: 0px !important;'>
								<tr>
									<td>
										<div style='padding:5px;width:150px;height: 120px; text-align: center; background-color:".$default_color."' >";
											$prodImagem = $directIn->listAllProdImages($arrayList['produto_idx']);
											if(is_array($prodImagem) && count($prodImagem) > 0){
												if(file_exists("../".PASTA_CONTENT."/".$mod."/".$pag."/images/m/".$prodImagem[0]['imagem'])){
													echo "<img src='../".PASTA_CONTENT."/".$mod."/".$pag."/images/m/".$prodImagem[0]['imagem']."' style='width:auto; max-width:140px; height: auto; max-height:110px;' />";
												}
											}
											else{
												echo "<img src='/assets/images/no-image.png' style='width:auto; max-width:140px; height: auto; max-height:140px;' />";
											}

										echo "</div>
									</td>
									<td width='20%' >".$arrayList['pdv_id']."</td>
									<td width='60%' >
										<a href='?mod=" . $mod . "&pag=" . $pag . "&act=edit&pid=" . $arrayList['produto_idx'] . "' >".$arrayList['nome']."</a><br />
										".$infoValor."
										Estoque: ".(int)$arrayList['quantidade']."<br/>
										<small>".$arrayList['descricao_curta']."</small>
									</td>
									<td width='0%' class='middle center' style='min-width: 55px;' >
										<input type='checkbox' data-produto='".$arrayList['produto_idx']."' name='destaque_".$arrayList['produto_idx']."' value='1' class='checkbox-switch' ". ( ((int)$arrayList['destaque']==1)?'checked':'' ) ." />
									</td>
									<td width='0%' class='middle center' style='min-width: 55px;' >
										<input type='checkbox' data-produto='".$arrayList['produto_idx']."' name='status_".$arrayList['produto_idx']."' value='1' class='checkbox-switch' ". ( ((int)$arrayList['status']==1)?'checked':'' ) ." />
									</td>
									<td width='0%' class='middle center' style='min-width: 80px;' >";

										if (Sis::checkPerm($modulo['codigo'].'-2') || Sis::checkPerm($modulo['codigo'].'-3'))
										{
											// $checkRatings = $directIn->checkRatings($arrayList['produto_idx'], 'comentario_idx, status');
											// if(is_array($checkRatings) && count($checkRatings) > 0){
											// 	$color = "black";
											// 	$text = count($checkRatings)." Avaliações";
											// 	foreach ($checkRatings as $key => $status) {
											// 		if($status['status'] == 0){
											// 			$text = "Avaliações pendentes";
											// 			$color = "#BD4C4C";
											// 			break;
											// 		}
											// 	}
											// 	echo "<a class='a_tooltip' data-placement='top' title='".$text."' href='?mod=" .$mod. "&pag=" . $pag . "&act=avaliacao-list&pid=" . $arrayList['produto_idx'] . "' ><i style='color: ".$color."' class='fa fa-check-square-o'></i></a>&nbsp;";
											// }else{
											// 	echo "<a class='a_tooltip' data-placement='top' title='Nenhuma Avaliação'><i style='color: #ccc;' class='fa fa-check-square-o'></i></a>&nbsp;";
											// }
											echo "
											<a class='a_tooltip' data-placement='top' title='Editar' href='?mod=" .$mod. "&pag=" . $pag . "&act=edit&pid=" . $arrayList['produto_idx'] . "' ><i class='fa fa-pencil-square-o'></i></a>&nbsp;
											<a class='a_tooltip' data-placement='top' title='Excluir' href='#' onclick='javascript: if (confirm(\"Você deseja excluir os dados?\")) { window.location=\"?mod=" . $mod . "&pag=" . $pag . "&act=del&pid=".$arrayList['produto_idx']."\"; } else { return false; }' ><i class='fa fa-trash-o'></i></a>
													";
										}
						echo "
									</td>
								</tr>
							</table>
						</li>";

					}
				echo "</ul>
					</div>";

				if((isset($_POST['pg']))){
					$atualPage = (int)$_POST['pg'];
				}elseif(isset($_GET['pg'])){
					$atualPage = (int)$_GET['pg'];
				}else{
					$atualPage = 1;
				}
				if($atualPage < 1){ $atualPage = 1; }
				if($atualPage > $totalPages){ $atualPage = $totalPages; }

				echo'
					<nav class="navbar navbar-default" role="navigation">';
						if($atualPage > 1){
							$prev = $atualPage-1;
				      	echo '<ul class="nav navbar-nav" title="Anterior"><li>
				      				<a href="'.$_SERVER['PHP_SELF'].'?'.'mod='.$mod.'&pag='.$pag.'&pg='.$prev.'"><i class="fa fa-chevron-left"></i></a>
				      			</li></ul>';
				      }else{
				      	echo'<ul class="nav navbar-nav"><li><a style="filter:alpha(opacity:30);opacity:0.3;-moz-opacity:0.3;cursor:default;" href="javascript:;"><i class="fa fa-chevron-left"></i></a></li></ul>';
				      }

				      echo '<p class="navbar-text">Página '.$atualPage.' / '.$totalPages.'</p>';

				      if($atualPage < $totalPages){
				      	$next = $atualPage + 1;
				   		echo '<ul class="nav navbar-nav" ><li>
				   					<a href="'.$_SERVER['PHP_SELF'].'?'.'mod='.$mod.'&pag='.$pag.'&pg='.$next.'"><i class="fa fa-chevron-right"></i></a>
				   				</li></ul>';
				   	}else{
				   		echo '<ul class="nav navbar-nav"><li><a style="filter:alpha(opacity:30);opacity:0.3;-moz-opacity:0.3;cursor:default;" href="javascript:;"><i class="fa fa-chevron-right"></i></a></li></ul>';
				   	}
				   	;

				      echo '
				      <p class="navbar-text">Ir para a página: </p>
				      <form class="navbar-form navbar-left" method="post" name="form_search" class="form_search">
				      	<div class="form-group">
				         	<input type="text" class="form-control" name="pg" style="width:40px;height:32px;" >
				      	</div>
				      	<button type="submit" onclick="$(".form_search").submit();" class="btn btn-default">Ok</button>
				      </form>
				      <span style="margin: 15px 20px 0 0;" class="pull-right">Total: '.$list->{'totalRegistros'}.' produtos.</span>
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
?>

<script type="text/javascript" >
	
	// $('.checkbox-switch').each(function(){
	// 	var elem = $(this);//document.querySelector('.checkbox-switch');
		
	// 	$(this).on('change',function() {
	// 	  console.log($(this).find('input').val());
	// 	});

	// 	
	// 	var init = new Switchery(this,options);
	// });

	let options = { size: 'small' };
	var elems = document.querySelectorAll('.checkbox-switch');

	for (var i = 0; i < elems.length; i++){
		var switchery = new Switchery(elems[i],options);
			elems[i].onchange = function() {
				console.log(this.checked);
				if (($(this).attr('name')).indexOf('status_') != -1) {
					defineStatus( $(this).attr('data-produto'), ((this.checked)?1:0) );
				}else if (($(this).attr('name')).indexOf('destaque_') != -1) {
					defineDestaque( $(this).attr('data-produto'), ((this.checked)?1:0) );
				}
			};
	}

	function defineStatus(produto,status){
		 $.ajax({
            type:"GET",
            url:"/admin/modulos/ecommercex/ecommercex-exe.php",
            data:{pid:produto,exe:195,status:status}
        }).done(function(data){
           console.log(data);
        });
	}
	function defineDestaque(regId,destaque){
		$.ajax({
			type:"GET",
			url:"/admin/modulos/ecommercex/ecommercex-exe.php",
			data:{pid:regId,exe:195,destaque:destaque}
		}).done(function(data){
			console.log(data);
		});
	}


</script>