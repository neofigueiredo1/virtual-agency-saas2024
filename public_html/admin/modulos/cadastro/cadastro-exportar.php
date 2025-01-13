<?php
   $exe = isset($_POST['exe']) ? (int)$_POST['exe'] : 0;
   $linkToExport = "";
   if ($exe == 1) {
      if(isset($_POST['campos'])){
         $cadastrosExport = $directIn->listAll((isset($_SESSION['filtros']) ? $_SESSION['filtros'] : 0), 0, 0, $_POST['campos']);
         if(is_string($cadastrosExport)){
            $linkToExport = '<div class="alert alert-success" role="alert">Exportação concluída! <a href="/sitecontent/cadastro/'.$cadastrosExport.'"> Clique aqui </a> para fazer o Download do arquivo. </div>';
         }
      }else{
         Sis::setAlert("Selecione os campos que você deseja exportar.", 1);
      }
   }
?>


<ol class="breadcrumb">
    <li class="active">Cadastros</li>
</ol>

<div class="btn-group">
   <a class="btn btn-default" href="?mod=<?php echo $mod ?>&pag=cadastro">Cadastros</a>
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

<?php echo $linkToExport; ?>

<form action="<?php echo Sis::currPageUrl(); ?>" method="post" class="form_dados" name="form_dados">
   <input type="hidden" name="filtros" value="<?php echo isset($_SESSION['filtros']) ? $_SESSION['filtros'] : 0; ?>">
   <input type="hidden" name="exe" value="1">
   <table class="table table_form">
		<tr>
			<td colspan="2" class="middle bg">
				<div class="panel panel-default">
				   <div class="panel-heading">
				   	Dados à exportar
                  <Br />
                  <small>Os dados serão exportados em uma planilha (.xls)<br />Selecione os campos que você deseja exportar!</small>
                  <Br />
                  <?php
                     if($_SESSION['filtros_texto'] != ""){
                        echo '<hr />
                              <div>
                                 <div class="">Filtros utilizados: <br>'.$_SESSION['filtros_texto'].'</div>
                                 <div class="clear"></div>
                                 <a href="" class="">Limpar</a>
                              </div>';
                     }
                  ?>
               </div>
			   	<div class="panel-body">
                  <table class="table table_form" style="margin:0px;" >
                     <tr class="">
                        <?php
                           $fields = Sis::getColumnsFromTable('cadastro');
                           if(is_array($fields) && count($fields) > 0){
                              $index = 0;
                              $checked = "";

                              // $checkVars = $checkVars[0]['valor'];
                              // $checkVarsArr = explode(",", $checkVars);
                              foreach ($fields as $key => $value) {
                                 $checked = "";
                                 $estilo_a = 'default';
                                 $estilo_b = 'hide';
                                 $retorno = true;
                                 if($value['Comment'] !== "") :
                                    echo '<td width="30%">
                                             <label style="display:initial" >
                                             <span class="btn btn-'.($estilo_a).' transition" style="width:100%;" >
                                             <input onclick="javascript:if(this.checked){
                                                                  $(this).parent().find(\'i\').removeClass(\'hide\');
                                                                  $(this).parent().removeClass(\'btn-default\');
                                                                  $(this).parent().addClass(\'btn-success\');
                                                               }else{
                                                                  $(this).parent().find(\'i\').addClass(\'hide\');
                                                                  $(this).parent().removeClass(\'btn-success\');
                                                                  $(this).parent().addClass(\'btn-default\');
                                                               }" '.$checked.' type="checkbox" style="display:none" name="campos[]" value="'.$value['Field'].'"><i class="fa fa-check-circle '.$estilo_b.'" style="font-size:13px;" ></i> '.$value['Comment'].' </span></label></td>';
                                    $index++;
                                    if ($index == 3) { echo "</tr><tr class=''>"; $index = 0; }
                                 endif;
                              }
                           }
                        ?>
                     </tr>
                  </table>
               </div>
				</div>
			</td>
		</tr>
      <tr>
         <td colspan="4" class="right" >
            <input type="button" value="Cancelar" class="btn btn-default" onclick="JavaScript: location.href='?mod=<?php echo $mod; ?>&pag=cadastro'; ">
            <input type="button" value="Enviar" class="btn btn-primary" data-loading-text="Carregando..."  onclick="JavaScript:checkFormRequire(document.form_dados,'#error-box');">
         </td>
      </tr>
	</table>
</form>
<script>
   function testesss(){
      $('[data-loading-text]').button('reset')
   }
</script>