<?php
    $enviar = (isset($_POST['enviar'])) ? (int)$_POST['enviar'] : 0;
    if($enviar != 0){
        self::changeMyProfile();
    }
	$myAccountInfo = self::getMyDataUserInfo();
	if(is_array($myAccountInfo) && count($myAccountInfo) > 0){
?>
<div class="form-cadastro box_form box_form_width pricipal" style="width:100%;" >
   <?php
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
         <script>
            $(document).ready(function(){
               $('html, body').animate({ scrollTop: $(document.body).offset().top}, 1000);
               setTimeout(function(){
                  $('#alert-bts').slideUp('fast', function(){
                     <?php echo (isset($_SESSION['ecommerce_url_back_cadastro'])) ? "location.href='".$_SESSION["ecommerce_url_back_cadastro"]."'" : ""; ?>
                  });
               }, 5000);
            });
         </script>
         <?php
         if(isset($_SESSION["ecommerce_url_back_cadastro"]) && $_SESSION["ecommerce_url_back_cadastro"] == "/login"){
            unset($_SESSION["ecommerce_usuario"]);
         }
         unset($_SESSION["ecommerce_url_back_cadastro"]);
         unset($_SESSION['ecommerce_cadastro_usuario_erros']);
      }
   ?>
   <div id='contato_form_sucesso' class="alert alert-success" style='display:none;' >Seu cadastro foi realizado com sucesso!</div>
   <div id='contato_form_preloader' class="alert alert-loader" style='display:none;' >
      <img src="/site/images/loader.gif" align="absmiddle" width="25" /> &nbsp;&nbsp;Aguarde, suas alterações serão concluídas em breve...
   </div>
   <div id="contato-help"  class="alert alert-danger" style='display:none;' >Preencha todos os campos corretamente.</div>

   <form id="meu_cadastro_form" name="meu_cadastro_form" role="form" method="post">
      <input type="hidden" name="enviar" value="1">
      <div class="form-group col-sm-6">
         <label for="nome_completo">Nome completo*</label>
         <input type="text" class="form-control" id="nome_completo" name="nome_completo" placeholder="Informe seu nome" data-required="true" value="<?php echo $myAccountInfo[0]['nome_completo']; ?>" />
      </div>
      <div class="form-group col-sm-6">
         <label for="nome_informal">Nome informal</label>
         <input type="text" class="form-control" id="nome_informal" name="nome_informal" placeholder="Como você gostaria de ser chamado?" value="<?php echo $myAccountInfo[0]['nome_informal']; ?>" />
      </div>
     <div class="form-group col-sm-6">
         <label for="data_nasc">Data de nascimento*</label>
         <input type="text" class="form-control data" id="data_nasc" name="data_nasc" placeholder="" data-required="true" value="<?php echo $myAccountInfo[0]['data_nasc']; ?>"  />
      </div>
      <div class="form-group col-sm-6">
         <label for="genero">Sexo*</label>
         <div class="clear"></div>
         <label class="radio-inline">
            <input type="radio" class="" id="" name="genero" value="1" <?php echo ($myAccountInfo[0]['genero']==1)?"checked":""; ?>>Masculino
         </label>
         <label class="radio-inline">
            <input type="radio" class="" id="" name="genero" value="0" <?php echo ($myAccountInfo[0]['genero']==0)?"checked":""; ?> >Feminino
         </label>
     </div>
     <div class="clear"></div>
      <div class="form-group col-sm-6">
         <label for="cpf_cnpj">CPF / CNPJ*</label>
         <input type="text" disabled class="form-control mask_cpfcnpj" id="cpf_cnpj" name="cpf_cnpj" placeholder="Informe seu CPF/CNPJ" data-required="true" value="<?php echo $myAccountInfo[0]['cpf_cnpj']; ?>"  />
      </div>
      <div class="form-group col-sm-6">
         <label for="telefone_resid">Telefone residencial</label>
         <input type="text" class="form-control telefone" id="telefone_resid" name="telefone_resid" placeholder="(88) 9999-9999" value="<?php echo $myAccountInfo[0]['telefone_resid']; ?>"  />
      </div>
      <div class="form-group col-sm-6">
         <label for="telefone_comer">Telefone comercial</label>
         <input type="text" class="form-control telefone" id="telefone_comer" name="telefone_comer" placeholder="(88) 9999-9999" value="<?php echo $myAccountInfo[0]['telefone_comer']; ?>"  />
      </div>
      <div class="form-group col-sm-6">
         <label for="celular">Telefone celular</label>
         <input type="text" class="form-control telefone" id="celular" name="celular" placeholder="(88) 9999-9999" value="<?php echo $myAccountInfo[0]['celular']; ?>"  />
      </div>
      <div class="form-group col-sm-4">
         <label for="cep">CEP*</label>
         <input type="text" class="form-control cep" id="cep" name="cep" placeholder="" data-required="true" value="<?php echo $myAccountInfo[0]['cep']; ?>" onkeyup="JavaScript:achaEndereco(this.value);" />
      </div>
      <div class="form-group col-sm-8">
          <br>Digite apenas números para CEP!<br />Não sabe o seu CEP? <a href="http://www.buscacep.correios.com.br/" class="fancybox" target="_blank">Clique aqui!</a>
      </div>
      <div class="form-group col-sm-12" >
         <label for="endereco">Endereço*</label>
         <input type="text" class="form-control" id="endereco" name="endereco" data-required="true" value="<?php echo $myAccountInfo[0]['endereco']; ?>"  />
      </div>
      <div class="form-group col-sm-6">
         <label for="numero">Número*</label>
         <input type="text" class="form-control" id="numero" name="numero" placeholder="" data-required="true" value="<?php echo $myAccountInfo[0]['numero']; ?>"  />
      </div>
      <div class="form-group col-sm-6">
         <label for="complemento">Complemento</label>
         <input type="text" class="form-control" id="complemento" name="complemento" placeholder="" value="<?php echo $myAccountInfo[0]['complemento']; ?>"  />
      </div>
      <div class="form-group col-sm-6">
         <label for="bairro">Bairro</label>
         <input type="text" class="form-control" id="bairro" name="bairro" placeholder="" value="<?php echo $myAccountInfo[0]['bairro']; ?>"  />
      </div>
      <div class="form-group col-sm-6">
         <label for="cidade">Cidade</label>
         <input type="text" class="form-control" id="cidade" name="cidade" placeholder="" value="<?php echo $myAccountInfo[0]['cidade']; ?>"  />
      </div>
      <div class="form-group col-sm-6">
         <label for="estado">Estado</label>
         <input type="text" class="form-control" id="estado" name="estado" placeholder="" value="<?php echo $myAccountInfo[0]['estado']; ?>"  />
      </div>
      <div class="form-group col-sm-6">
         <label for="pais">País</label>
         <input type="text" class="form-control" id="pais" name="pais" placeholder="" value="<?php echo $myAccountInfo[0]['pais']; ?>"  />
      </div>
      <div class="form-group col-sm-12">
         <label for="email">E-mail* <span class="email_info"></span></label>
         <input type="email" class="form-control" id="email" name="email" onkeyup="verificaEmailAtual()" placeholder="Informe seu e-mail" data-required="true" value="<?php echo $myAccountInfo[0]['email']; ?>"  />
         <input type="hidden" value="<?php echo $myAccountInfo[0]['email']; ?>" id="email_atual">
      </div>
      <div class="form-group col-sm-6" style="">
         <label for="senha_antiga">Sua Senha*</label>
         <input type="password" class="form-control" id="senha_antiga" name="senha_antiga" placeholder="" data-required="true" value="" />
      </div>

      <div class="form-group col-sm-12">
         <div class="checkbox">
            <label>
               <input type="checkbox" id="alterar_senha" name="alterar_senha" onclick="javascript:$('.senha_alt').slideToggle('fast');" value="" /> Alterar Senha
            </label>
         </div>
      </div>
      <div class="form-group col-sm-6 senha_alt" style="display: none;">
         <label for="senha">Senha*</label>
         <input type="password" class="form-control" id="senha" name="senha" placeholder="" value="" />
      </div>
      <div class="form-group col-sm-6 senha_alt" style="display: none;">
         <label for="senha_conf">Confimar senha*</label>
         <input type="password" class="form-control" id="senha_conf" name="senha_conf" placeholder="" value="" />
      </div>

      <div class="form-group col-sm-12">
         <input type="button" class="btn btn-default" onclick="javascript:checkFormRequire(document.meu_cadastro_form,'#contato-help', checkCadastro);" value="Enviar" />
      </div>
      <div class="clear"></div>
   </form>
   <script>
      $('.cpf').mask('999.999.999-99');
      $('.cep').mask('99999-999');
      // $('.telefone').mask('(99) 999999999');
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
      document.getElementById("alterar_senha").checked = false;
   </script>
   <div class="clear" ></div>
</div>
<?php
	}else{
		header("Location: /login");
	}
?>