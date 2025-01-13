<?php
  $goUpdate = (isset($_POST['goUpdate'])) ? (int)$_POST['goUpdate'] : 0;
  if($goUpdate != 0){
      self::changeMyProfile();
  }

$myAccountInfo = self::getMyDataUserInfo();
if(is_array($myAccountInfo) && count($myAccountInfo) > 0){

  // $arr = explode('-',$myAccountInfo[0]['telefone_resid']);
  // $ddd_telefone = (isset($arr[0])? $arr[0] : '');
  // $telefone = (isset($arr[1])? $arr[1] : '');

  // $arrCell = explode('-', $myAccountInfo[0]['celular']);
  // $ddd_celular =  (isset($arrCell[0])? $arrCell[0] : '');
  // $celular = (isset($arrCell[1])? $arrCell[1] : '');

?>

<?php
$emailSession = "";
$msgSession = array();

if(isset($_SESSION['plataforma_cadastro_usuario_erros']))
{
   $msgSession=$_SESSION['plataforma_cadastro_usuario_erros'];
   unset($_SESSION['plataforma_cadastro_usuario_erros']);
}

if($msgSession!=null)
{
   $tipo="warning";
   switch($msgSession['tipo'])
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
   <div id="alert-bts" style="display:block;" class="alert alert-<?php echo $tipo; ?>">
      <button onclick="javascript: $(this).parent().slideUp('fast');" type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
      <?php echo $icone.$msgSession['mensagem']; ?>
      <script type="text/javascript">setTimeout(function(){$('#alert-bts').slideUp();},5000);</script>
   </div>
<?php
   if(isset($_SESSION["ecommerce_url_back_cadastro"]) && $_SESSION["ecommerce_url_back_cadastro"] == "/login"){
      unset($_SESSION["ecommerce_usuario"]);
   }
   unset($_SESSION["ecommerce_url_back_cadastro"]);  
}
?>


<div class="contaner bg-white rounded-20 px-3 p-md-5 py-5 form-cadastro" >

  <div id='contato_form_sucesso' class="alert alert-bts alert-success" style='display:none;' >Seu cadastro foi realizado com sucesso!</div>
  <div id='contato_form_preloader' class="alert alert-bts text-center alert-loader" style='display:none;' >
   <img src="/assets/images/preloader.gif" align="absmiddle" class="d-inline load-img"  /> <br /> <span class="fs-13 lexendpeta_regular lt-spc-n2 rosa_10">Aguarde, atualizando seus dados...</span>
  </div>
  <div id="alerts-help"  class="alert alert-bts alert-danger" style='display:none;' >Preencha todos os campos corretamente.</div>

  <div class="clearfix"></div>

<form id="meu_cadastro_form" name="meu_cadastro_form" role="form" method="post" enctype="multipart/form-data" >
    <input type="hidden" name="goUpdate" value="1" >
    <div class="dados-pessoais" >
        <h4 class="azul" >Dados Pessoais</h4>
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="form-group col-lg-3 col-md-12 text-lg-left text-md-center">
                        <?php
                            $imagemPerfil = "/assets/images/profile-temp.png";
                            if (trim($myAccountInfo[0]['imagem_perfil'])!="" && trim($myAccountInfo[0]['imagem_perfil'])!='null'){
                                $imagemPerfil = "/sitecontent/cadastro/profile/".$myAccountInfo[0]['imagem_perfil'];
                            }
                        ?>
                        <div id="image-upload" class="m-auto" style="background-image:url('<?php echo $imagemPerfil; ?>');" ></div>
                        <div class="form-group text-center">
                            <label for="imagem_perfil" class="text-imagem">
                                <p class="">Imagem de perfil</p>
                                <small><i>Selecione uma imagem para atualizar</i></small>
                            </label>
                            <input style="overflow:hidden;" type="file" class="form-control border-0" id="imagem_perfil" name="imagem_perfil"  />
                        <?php if (trim($myAccountInfo[0]['imagem_perfil'])!="" && trim($myAccountInfo[0]['imagem_perfil'])!='null'): ?>
                            <hr>
                            <input type="checkbox" class="form-check-input" id="remove_imagem_perfil" name="remove_imagem_perfil" value="1" />
                            <label for="remove_imagem_perfil" class="fs-14">
                                Excluir imagem atual do perfil.
                            </label>
                        <?php endif ?>
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-12 mt-lg-0 mt-4">
                        <div class="row align-items-end justify-content-between">
                            <div class="form-group col-md-6  mb-lg-3 mb-md-1">
                                <label class="minha-conta__label" for="nome_completo">Nome Completo</label>
                                <input type="text" class="form-control form--custom fs-16" id="nome_completo" name="nome_completo" value="<?php echo $myAccountInfo[0]['nome_completo']; ?>" >
                            </div>
                            <div class="form-group col-md-6 mb-lg-3 mb-md-1">
                                <label class="minha-conta__label" for="nome_informal">Como deseja ser chamado</label>
                                <input type="text" class="form-control form--custom fs-16" id="nome_informal" name="nome_informal" value="<?php echo $myAccountInfo[0]['nome_informal']; ?>" >
                            </div>
                        </div>
                        <div class="form-group mb-lg-3 mb-md-1">
                            <label class="minha-conta__label" for="cpf" >CPF/CNPJ</label>
                            <input type="text" placeholder="Digite seu CPF ou CNPJ" class="form-control form--custom fs-16 mask_cpfcnpj" id="cpf_cnpj" name="cpf_cnpj" value="<?php echo $myAccountInfo[0]['cpf_cnpj']; ?>" />
                        </div>
                        
                        <div class="row align-items-end justify-content-between">
                            <div class="form-group mb-lg-3 mb-md-1 col-md-6">
                                <label class="minha-conta__label" for="data_nasc">Data de Nascimento</label>
                                <input type="text" placeholder="Digite sua data de nascimento" class="form-control form--custom fs-16 mask_date"  id="data_nasc" name="data_nasc"
                                value="<?php echo date("d/m/Y",strtotime($myAccountInfo[0]['data_nasc'])); ?>"  autocomplete="off" />
                            </div>
                            <div class="form-group mb-lg-3 mb-md-1 col-md-6">
                                <label class="minha-conta__label" for="genero">Gênero <i><small>(opcional)</small></i></label>
                                <select class="form-control" id="genero" name="genero" >
                                    <option value="0" ></option>
                                    <option value="2" <?php echo ((int)$myAccountInfo[0]['genero']==2) ? 'selected': '' ; ?> >Feminino</option>
                                    <option value="1" <?php echo ((int)$myAccountInfo[0]['genero']==1) ? 'selected': '' ; ?> >Masculino</option>
                                    <option value="0" >Prefiro n&atilde;o informar</option>
                                </select>
                            </div>
                        </div>
                        <div class="row align-items-end justify-content-between">
                            <div class="col-md-6">
                                <div class="form-group mb-lg-3 mb-md-1">
                                    <label class="minha-conta__label" for="telefone_resid">Telefone Fixo</label>
                                    <input type="text" class="form-control form--custom fs-16 mask_phone_with_ddd" id="telefone_resid" name="telefone_resid" value="<?php echo $myAccountInfo[0]['telefone_resid']; ?>" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-lg-3 mb-md-1">
                                    <label class="minha-conta__label" for="celular">Telefone Celular</label>
                                    <input type="text" class="form-control form--custom fs-16 mask_spcelphones" id="celular" name="celular" value="<?php echo $myAccountInfo[0]['celular']; ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12"><hr class="divisor"></div>
            <div class="col-md-12 dados-endereco pt-2" >
                
                <h4 class="azul">Dados de Endereço</h4>

                <div class="row mb-3">
                    <div class="col-md-6" >
                        <label class="minha-conta__label" for="endereco">Endereço</label>
                        <input type="text" placeholder="Digite seu endereço" class="form-control " id="endereco" name="endereco" value="<?php echo $myAccountInfo[0]['endereco']; ?>" >
                    </div>
                    <div class="col-md-2" >
                        <label class="minha-conta__label" for="numero">Número</label>
                        <input type="text" placeholder="Número" class="form-control" id="numero" name="numero" value="<?php echo $myAccountInfo[0]['numero']; ?>" />
                    </div>
                    <div class="col-md-4">
                        <label class="minha-conta__label" for="cep">CEP</label>
                        <input type="text" placeholder="CEP" class="form-control" id="cep" name="cep" value="<?php echo $myAccountInfo[0]['cep']; ?>" />
                    </div>
                </div>

                <div class="row" >
                    <div class="col-md-3" >
                        <label class="minha-conta__label" for="complemento"> Complemento</label>
                        <input type="text" placeholder="Digite o complemento" class="form-control " id="complemento" name="complemento" value="<?php echo $myAccountInfo[0]['complemento']; ?>" >
                    </div>
                    <div class="col-md-3" >
                        <label class="minha-conta__label" for="bairro">Bairro</label>
                        <input type="text" placeholder="Digite seu bairro" class="form-control " id="bairro" name="bairro" value="<?php echo $myAccountInfo[0]['bairro']; ?>" >
                    </div>
                    <div class="col-md-3" >
                        <label class="minha-conta__label" for="cidade">Cidade</label>
                        <input type="text" placeholder="Digite sua cidade" class="form-control " id="cidade" name="cidade" value="<?php echo $myAccountInfo[0]['cidade']; ?>" >
                    </div>
                    <div class="col-md-3">
                        <label class="minha-conta__label" for="estado">Estado</label>
                        <select id="estado" name="estado" class="form-control " >
                            <option value="" >Estado</option>
                            <?php
                            $estados = Sis::getState();
                            $estado_selecionado = $myAccountInfo[0]['estado'];
                            foreach($estados as $key => $estado){
                            $selected = (trim($estado_selecionado)==trim($key))?"selected":"";
                            echo('<option value="'.$key.'" '.$selected.' >'.$key.'</option>');
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-12" ><hr class="divisor"></div>
            <div class="col-md-12 pt-2" >
                <div class="dados-acesso" >
                    <h4 class="azul" >Dados de Acesso</h4>
                    <div class="row align-items-end justify-content-between">
                        <div class="form-group col-md-12">
                            <label class="minha-conta__label" for="email">E-mail</label>
                            <input  type="email" name="email" id="email" class="form-control form--custom fs-16" data-required="true" value="<?php echo $myAccountInfo[0]['email']; ?>" />
                            <input type="hidden" value="<?php echo $myAccountInfo[0]['email']; ?>" id="email_atual" >
                        </div>
                        <div class="form-group col-md-12">
                            <div class="form-check mt-2">
                                <input type="checkbox" class="form-check-input" id="receber_boletim"  name="receber_boletim" value="1" <?php echo ((int)$myAccountInfo[0]['receber_boletim']==1) ? 'checked': '' ; ?>  >
                                <label class="form-check-label receber_boletim" for="receber_boletim">Desejo receber novidades do site no meu e-mail.</label>
                            </div>
                        </div>
                        <div class="form-group col-md-12" >
                            <div class="checkbox" >
                                <label class="checkDadosPessoais" >
                                    <input type="checkbox" id="alterar_senha" name="alterar_senha" onclick="javascriot:if(this.checked){$('.nova-senha').slideDown();}else{$('.nova-senha').slideUp();};" value="" />
                                    <a class="editar-senha" >Mudar a senha da conta</a>
                                </label>
                            </div>
                            <div class="nova-senha px-3 pb-3 row align-items-end justify-content-between" style="display:none;" >
                                <div class="form-group col-md-6">
                                    <label class="minha-conta__label" for="senha">Nova senha</label>
                                    <input type="password" placeholder="Nova senha" class="form-control form--custom fs-16" id="senha" name="senha" data-required="false" value="" />
                                </div>
                                <div class="col-md-6">
                                    <label class="minha-conta__label" for="senha_conf">Confirmar nova senha</label>
                                    <input type="password" class="form-control form--custom fs-16" id="senha_conf" name="senha_conf" data-required="false" placeholder="Confirme a sua senha" value="" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="dados-acesso-salvar px-3">
                <div class="row" >
                    <div class="col-md-12" >
                        <div class="row align-items-center">
                            <div class="col-sm-6">
                                <label class="minha-conta__label" for="senha_antiga">Senha atual</label>
                                <input type="password" placeholder="Digite sua senha atual" class="form-control form--custom fs-16" id="senha_antiga" name="senha_antiga" placeholder="" data-required="true" value="" />
                            </div>
                            <div class="col-md-6 pt-4" >
                                <p class="text-senha mb-0 p-md-0 p-3" >
                                    Digite a senha atual para realizar as altera&ccedil;&otilde;es.
                                    Para atualiza&ccedil;&atilde;o da senha, esta deve ter no m&iacute;nimo 6 caracteres.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-5 text-right">
                        <input type="button" class="btn btn-180 btn-azul" onclick="javascript:Util.checkFormRequire(document.meu_cadastro_form,'#alerts-help', checkCadastroUpdate);" value="Salvar" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</form>


<?php
}else{
  header("Location: /cadastro");
}
?>

