<?php
	$enviar = isset($_POST['enviar']) ? (int)$_POST['enviar'] : 0 ;
	$id     = isset($_GET['id'])      ? (int)$_GET['id']      : 0 ;
	if ($enviar ===	 1) $directIn->theUpdate();
?>

<ol class="breadcrumb">
    <li><a href="?mod=<?php echo $mod ?>&pag=cadastro">Cadastros</a></li>
    <li class="active">Editar cadastro</li>
</ol>

<div class="btn-group">
    <a class="btn btn-default" href="?mod=<?php echo $mod ?>&pag=cadastro">Cadastros</a>
    <a class="btn btn-default" href="?mod=<?php echo $mod ?>&pag=cadastro&act=add">Adicionar cadastro</a>
</div>
<div class="btn-group">
    <a class="btn btn-default" href="?mod=<?php echo $mod ?>&pag=area-interesse">Áreas de interesse</a>
    <a class="btn btn-default" href="?mod=<?php echo $mod ?>&pag=area-interesse&act=add" >Nova área de interesse</a>
</div>
<div class="btn-group">
  	<a class="btn btn-success" href="?mod=<?php echo $mod ?>&pag=cadastro&act=exportar">Exportar cadastros</a>
</div>

<hr />

<section class="content">
	<?php
	$list = $directIn->listSelected($id);
	if(is_array($list) && count($list) > 0){
		foreach ($list as $arrayList){
			?>
			<form action="<?php echo sis::currPageUrl(); ?>" method="post" id="form_dados" class="form_dados" name="form_dados" enctype="multipart/form-data">
				<input type="hidden" name="enviar" value="1">
				<input type="hidden" name="id" value="<?php echo $id ?>">
				<table class="table table_form">
			    	<tr>
		            <th width="20%" class="middle bg">Situação</th>
		            <td class="middle">
						  	<?php if ($arrayList['status']=="1"){ echo "Ativo"; } else if ($arrayList['status']=="0"){ echo "Inativo"; } else if ($arrayList['status']=="4"){ echo "Newsletter"; } ?>
		            </td>
		            <td></td>
		            <td></td>
		        	</tr>
			      <tr>
		            <th width="20%" class="middle bg">Nome completo </th>
		            <td colspan="3">
		               <?php echo $arrayList['nome_completo']; ?>
		            </td>
		        	</tr>
			    	<tr>
						<th width="20%" class="middle bg">
		               Nome informal
			         </th>
						<td>
							<?php echo $arrayList['nome_informal']; ?>
			         </td>
		            <th width="20%" class="middle bg">CPF/CNPJ </th>
		            <td>
		               <?php echo $arrayList['cpf_cnpj']; ?>
		            </td>
					</tr>
			      <tr>
		            <th class="middle bg">E-mail: </th>
		            <td>
		                <?php echo $arrayList['email']; ?>
		            </td>
		            <th class="middle bg">Telefone residencial </th>
		            <td>
		                <?php echo $arrayList['telefone_resid']; ?>
		            </td>
					</tr>
			        <tr>
						<th class="middle bg">Telefone comercial </th>
			            <td>
			                <?php echo $arrayList['telefone_comer']; ?>
			            </td>
			            <th class="middle bg">Celular </th>
			            <td>
			                <?php echo $arrayList['celular']; ?>
			            </td>
			        </tr>
			        <tr>
			            <th class="middle bg">Gênero </th>
			            <td class="middle">
	                  	<?php if ($arrayList['status']=="1"){ echo "Masculino"; } else if ($arrayList['status']=="0"){ echo "Feminino"; } ?>
			            </td>
			            <th class="middle bg">Data de nascimento </th>
			            <td>
			               <?php echo $arrayList['data_nasc']; ?>
			            </td>
			        </tr>
			        <tr>
			            <th class="middle bg">Endereço </th>
			            <td>
			               <?php echo $arrayList['endereco']; ?>
			            </td>
			            <th class="middle bg">Nº </th>
			            <td>
			               <?php echo $arrayList['numero']; ?>
			            </td>
			        </tr>
			        <tr>
			            <th class="middle bg">Complemento </th>
			            <td>
			               <?php echo $arrayList['complemento']; ?>
			            </td>
			            <th class="middle bg">Bairro </th>
			            <td>
			               <?php echo $arrayList['bairro']; ?>
			            </td>
			        </tr>
			        <tr>
			            <th class="middle bg">Cep </th>
			            <td>
			               <?php echo $arrayList['cep']; ?>
			            </td>
			            <th class="middle bg">Cidade </th>
			            <td>
			               <?php echo $arrayList['cidade']; ?>
			            </td>
					</tr>
			        <tr>
			            <th class="middle bg">Estado </th>
			            <td>
			               <?php echo $arrayList['estado']; ?>
			            </td>
			            <th class="middle bg">País </th>
			            <td>
			               <?php echo $arrayList['pais']; ?>
			            </td>
			       	</tr>
			        <tr>
			            <th class="middle bg">Áreas de interesse </th>
			            <td colspan="3" class="middle">
			            	<?php
				            	$listInterest = $directIn->listInterest();
				            	if(is_array($listInterest)&&count($listInterest)>0)
				            	{
				                  $listUserSeleciona = $directIn->listUserSeleciona($arrayList['cadastro_idx']);

				                    for($i=0; $i<count($listUserSeleciona); ++$i){
				                    	$interesse_idx[] = $listUserSeleciona[$i]['interesse_idx'];
				                    }

				                    foreach($listInterest as $arrayList_inte){
				                        $checked = "";
				                        if(array_search($arrayList_inte['interesse_idx'],$interesse_idx) !== false){ $checked = "checked='checked'";}
				                        echo "
				                        	<div style='float:left; margin-right:15px; margin-bottom:0px;'>
			                        			" . $arrayList_inte['nome'] . "
				                        	</div>";
				                    }
				               }
		                    ?>
			            </td>
			        </tr>
			       <tr>
			            <th>
                        <b>Receber boletim? </b>
			            </th>
			            <td>
			            	<?php if($arrayList['receber_boletim']){ echo "Sim"; } else{ echo "Não"; } ?>
			            </td>
			            <td></td>
			            <td></td>
					</tr>
			        	<tr>
			            <td colspan="4" class="right" >
			            </td>
			        	</tr>
				</table>

			</form>
			<?php
		}
	}else{
		echo "<div class='alert alert-warning'>
               <i class='fa fa-exclamation-triangle'></i>&nbsp;&nbsp;
               Nenhum registro encontrado.
            </div>";
		sis::redirect("?mod=".$mod."&pag=" . $pag , 2);
	}
	?>
</section>
<script>
    // $('#cpf').mask('999.999.999-99');
</script>