<?php
   $enviar = (isset($_POST['enviar']))?(int)$_POST['enviar']:0;
   if($enviar == 1){
      self::cadastroInsert();
   }
   $time = FALSE;
   if(isset($_SESSION["ecommerce_cadastro_usuario"]) && is_array($_SESSION["ecommerce_cadastro_usuario"])){
      $time = TRUE;
      $dataArr = explode("-", $_SESSION["ecommerce_cadastro_usuario"]['time_limit']);
      if(date('Ymd') == $dataArr[0] && (int)date('His') - (int)$dataArr[1] <= 500){
         $time = FALSE;
      }
      if($time){
         unset($_SESSION["ecommerce_cadastro_usuario"]);
      }
   }else{
      unset($_SESSION["ecommerce_cadastro_usuario"]);
   }

   if(isset($_SESSION['ecommerce_cadastro_usuario_erros']) && is_array($_SESSION['ecommerce_cadastro_usuario_erros']))
   {
      $tipo="warning";
      switch($_SESSION['ecommerce_cadastro_usuario_erros']['tipo'])
      {
         case 1 : $tipo="warning";  $icone = "<i class='fa fa-exclamation-triangle'></i>&nbsp;&nbsp;";
         break;
         case 2 : $tipo="info";     $icone = "<i class='fa fa-info-circle'></i>&nbsp;&nbsp;";
         break;
         case 3 : $tipo="success";  $icone = "<i class='fa fa-check-circle-o'></i>&nbsp;&nbsp;";
         break;
         case 4 : $tipo="danger";   $icone = "<i class='fa fa-ban'></i>&nbsp;&nbsp;";
         break;
      }
   ?>

      <div id="alert-bts" style="display: block;" class="alert alert-<?php echo $tipo; ?> fade in">
         <button onclick="javascript: $(this).parent().slideUp('fast');" type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
         <?php echo $icone.$_SESSION['ecommerce_cadastro_usuario_erros']['mensagem']; ?>
      </div>

      <?php
         unset($_SESSION['ecommerce_cadastro_usuario_erros']);

         if (isset($_SESSION["ecommerce_url_back_cadastro"]) && $_SESSION["ecommerce_url_back_cadastro"] != "") {
            $sessionTemp = $_SESSION["ecommerce_url_back_cadastro"];
            unset($_SESSION["ecommerce_url_back_cadastro"]);
            echo '<script> setTimeout(function(){ window.location = "'.$sessionTemp.'"; }, 5000); </script>';
         }
      // }else{
         ?>
         <script>
            setTimeout(function(){
               $('#alert-bts').slideUp('fast');
            },5000);
         </script>
         <?php
      }

?>

<div class="form-cadastro box_form box_form_width pricipal" style="width:100%;" >
   <div id='contato_form_sucesso' class="alert alert-success" style='display:none;' >Seu cadastro foi realizado com sucesso!</div>
   <div id='contato_form_preloader' class="alert alert-loader" style='display:none;' >
      <img src="/site/images/loader.gif" align="absmiddle" /> &nbsp;&nbsp;Aguarde, realizando seu cadastro...
   </div>
   <div id="contato-help"  class="alert alert-danger" style='display:none;' >Preencha todos os campos corretamente.</div>

   <div class="clear" ></div>

   <form id="cadastro_form" name="cadastro_form" role="form" method="post">
      <input type="hidden" name="enviar" value="1">
      <div class="form-group col-sm-6">
          <label class="required" for="nome_completo">Nome completo</label>
         <input type="text" class="form-control" id="nome_completo" name="nome_completo" placeholder="Informe seu nome" data-required="true" value="<?php echo isset($_SESSION["ecommerce_cadastro_usuario"])?$_SESSION["ecommerce_cadastro_usuario"]['nome_completo']:""; ?>" />
      </div>
      <div class="form-group col-sm-6">
         <label for="nome_informal">Nome informal</label>
         <input type="text" class="form-control" id="nome_informal" name="nome_informal" placeholder="Como você gostaria de ser chamado?" value="<?php echo isset($_SESSION["ecommerce_cadastro_usuario"])?$_SESSION["ecommerce_cadastro_usuario"]['nome_informal']:""; ?>" />
      </div>
     <div class="form-group col-sm-6">
         <label class="required" for="data_nasc">Data de nascimento</label>
         <input type="text" class="form-control data" id="data_nasc" name="data_nasc" placeholder="dd/mm/aaaa" data-required="true" value="<?php echo isset($_SESSION["ecommerce_cadastro_usuario"])?$_SESSION["ecommerce_cadastro_usuario"]['data_nasc']:""; ?>"  />
      </div>
      <div class="form-group col-sm-6">
          <label class="required" for="genero" class="genero">Gênero</label>
         <div class="clear"></div>
         <label class="radio-inline">
            <input type="radio" class="" id="" name="genero" value="1"<?php echo (!isset($_SESSION["ecommerce_cadastro_usuario"]))?"":($_SESSION["ecommerce_cadastro_usuario"]['genero']=="1")?"checked":""; ?> >Masculino
         </label>
         <label class="radio-inline">
            <input type="radio" class="" id="" name="genero" value="0"<?php echo (!isset($_SESSION["ecommerce_cadastro_usuario"]))?"":($_SESSION["ecommerce_cadastro_usuario"]['genero']=="0")?"checked":""; ?> >Feminino
         </label>
     </div>
     <div class="clear"></div>
      <div class="form-group col-sm-6">
          <label class="required" for="cpf">CPF</label>
         <input type="text" class="form-control cpf" id="cpf" name="cpf" placeholder="Informe seu CPF" data-required="true" value="<?php echo isset($_SESSION["ecommerce_cadastro_usuario"])?$_SESSION["ecommerce_cadastro_usuario"]['cpf']:""; ?>"  />
      </div>
      <div class="form-group col-sm-6">
         <label for="telefone_resid">Telefone residencial</label>
         <input type="text" class="form-control telefone" id="telefone_resid" name="telefone_resid" placeholder="(88) 9999-9999" value="<?php echo isset($_SESSION["ecommerce_cadastro_usuario"])?$_SESSION["ecommerce_cadastro_usuario"]['telefone_resid']:""; ?>"  />
      </div>
      <div class="form-group col-sm-6">
         <label for="telefone_comer">Telefone comercial</label>
         <input type="text" class="form-control telefone" id="telefone_comer" name="telefone_comer" placeholder="(88) 9999-9999" value="<?php echo isset($_SESSION["ecommerce_cadastro_usuario"])?$_SESSION["ecommerce_cadastro_usuario"]['telefone_comer']:""; ?>"  />
      </div>
      <div class="form-group col-sm-6">
         <label for="celular">Telefone celular</label>
         <input type="text" class="form-control telefone" id="celular" name="celular" placeholder="(88) 9999-9999" value="<?php echo isset($_SESSION["ecommerce_cadastro_usuario"])?$_SESSION["ecommerce_cadastro_usuario"]['celular']:""; ?>"  />
      </div>
      <div class="form-group col-sm-4">
          <label class="required" for="cep">CEP</label>
         <input type="text" class="form-control cep" id="cep" name="cep" placeholder="" data-required="true" value="<?php echo (isset($_SESSION['ecommerce_usuario_cep']) && !empty($_SESSION['ecommerce_usuario_cep'])) ? $_SESSION['ecommerce_usuario_cep'] : ""; ?>" onkeyup="JavaScript:achaEndereco(this.value);" />
      </div>
      <div class="form-group col-sm-8">
          <br>Digite apenas números para CEP!<br />Não sabe o seu CEP? <a href="http://www.buscacep.correios.com.br/" class="fancybox" target="_blank">Clique aqui!</a>
      </div>
      <div class="form-group col-sm-12" >
          <label class="required" for="endereco">Endereço</label>
         <input type="text" class="form-control" id="endereco" name="endereco" value="" data-required="true" value="<?php echo isset($_SESSION["ecommerce_cadastro_usuario"])?$_SESSION["ecommerce_cadastro_usuario"]['endereco']:""; ?>"  />
      </div>
      <div class="form-group col-sm-6">
          <label class="required" for="numero">Número</label>
         <input type="text" class="form-control" id="numero" name="numero" placeholder="" data-required="true" value="<?php echo isset($_SESSION["ecommerce_cadastro_usuario"])?$_SESSION["ecommerce_cadastro_usuario"]['numero']:""; ?>"  />
      </div>
      <div class="form-group col-sm-6">
         <label for="complemento">Complemento</label>
         <input type="text" class="form-control" id="complemento" name="complemento" placeholder="" value="<?php echo isset($_SESSION["ecommerce_cadastro_usuario"])?$_SESSION["ecommerce_cadastro_usuario"]['complemento']:""; ?>"  />
      </div>
      <div class="form-group col-sm-6">
         <label for="bairro">Bairro</label>
         <input type="text" class="form-control" id="bairro" name="bairro" placeholder="" value="<?php echo isset($_SESSION["ecommerce_cadastro_usuario"])?$_SESSION["ecommerce_cadastro_usuario"]['bairro']:""; ?>"  />
      </div>
      <div class="form-group col-sm-6">
         <label for="cidade">Cidade</label>
         <input type="text" class="form-control" id="cidade" name="cidade" placeholder="" value="<?php echo isset($_SESSION["ecommerce_cadastro_usuario"])?$_SESSION["ecommerce_cadastro_usuario"]['cidade']:""; ?>"  />
      </div>
      <div class="form-group col-sm-6">
         <label for="estado">Estado</label>
         <select class="form-control"  name="estado" id="estado" >
             <?php 
                $estados = Sis::getState();
                $estado_selecionado = isset($_SESSION["ecommerce_cadastro_usuario"])?$_SESSION["ecommerce_cadastro_usuario"]['estado']:"";
                foreach($estados as $key => $estado){
                    $selected = (trim($estado_selecionado)==trim($key))?"selected":"";
                    echo('<option value="'.$key.'" '.$selected.' >'.$estado.'</option>');
                }
             ?>
         </select>
         
      </div>
      <div class="form-group col-sm-6">
         <label for="pais">País</label>
         <input type="text" class="form-control" id="pais" name="pais" placeholder="" value="<?php echo isset($_SESSION["ecommerce_cadastro_usuario"])?$_SESSION["ecommerce_cadastro_usuario"]['pais']:""; ?>"  />
      </div>
      <div class="form-group col-sm-12">
          <label class="required" for="email">E-mail</label>
         <input type="email" class="form-control" id="email" name="email" placeholder="Informe seu e-mail" data-required="true" value="<?php echo isset($_SESSION["ecommerce_cadastro_usuario"])?$_SESSION["ecommerce_cadastro_usuario"]['email']:""; ?>"  />
      </div>
      <div class="form-group col-sm-6">
          <label class="required" for="senha">Senha</label>
         <input type="password" class="form-control" id="senha" name="senha" placeholder="" data-required="true" value="" />
      </div>
      <div class="form-group col-sm-6">
          <label class="required" for="senha_conf">Confimar senha</label>
         <input type="password" class="form-control" id="senha_conf" name="senha_conf" placeholder="" data-required="true" value="" />
      </div>
      <div class="form-group col-sm-12">
      <div class="checkbox">
         <label>
            <input type="checkbox" name="receber_boletim" value="1" /> Desejo receber novidades do site.
         </label>
      </div>
      <div class="checkbox">
         <label>
            <input type="checkbox" name="termos" value="" /> Li e concordo com os <a href="<?php echo Sis::config("CLI_LINK_TERMOS_CONDICOES"); ?>" target="_blank" >termos e condições</a>!
         </label>
      </div>
      </div>
      <div class="form-group col-sm-12">
         <input type="button" class="btn btn-red" onclick="javascript:checkFormRequire(document.cadastro_form,'#contato-help', checkCadastro);" value="Enviar" />
      </div>
   </form>
   <script>
      $('.cpf').mask('999.999.999-99');
      $('.cep').mask('99999-999');
      $('.telefone').focusout(function(){
         var phone, element;
         element = $(this);
         element.unmask();
         phone = element.val().replace(/\D/g, '');
         if(phone.length > 10) {
            element.mask("(99) 99999-9999");
         } else {
            element.mask("(99) 9999-99999");
         }
      }).trigger('focusout');
      $('.data').mask('99/99/9999');
   </script>
   <div class="clear" ></div>
</div>
<?php
   unset($_SESSION['ecommerce_usuario_cep']);
?>