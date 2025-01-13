<?php 

    global $car_session,$csrfToken;
    global $car_subtotal, $desconto_valor, $boleto_desconto, $boleto_desconto_percentual,$dados_formulario;

    $facebook_pixel_id = isset($_SESSION['platform_cart_tracker_facebook_pixel_id'])?$_SESSION['platform_cart_tracker_facebook_pixel_id']:'';
    $google_tag_manager_id = isset($_SESSION['platform_cart_tracker_google_tag_manager_id'])?$_SESSION['platform_cart_tracker_google_tag_manager_id']:'';

    $dados_formulario = array(
        'nome_completo' => '',
        'nome_informal' => '',
        'data_nascimento' => '',
        'sexo' => 0,
        'email' => '',
        'cpf_cnpj' => '',
        'telefone' => '',
        'cep' => '',
        'estado' => '',
        'cidade' => '',
        'endereco' => '',
        'numero' => '',
        'complemento' => '',
        'bairro' => ''
    );

    $m_cadastro = new cadastro_views();

    if($m_cadastro->isLogged()){

        $cadastro = $m_cadastro->getCadastro($_SESSION['plataforma_usuario']['id']);

        if (is_array($cadastro)&&count($cadastro)>0) {
            $cadastro = $cadastro[0];
            $dados_formulario['nome_completo'] = $cadastro['nome_completo'];
            $dados_formulario['nome_informal'] = $cadastro['nome_informal'];
            $dados_formulario['data_nascimento'] = $cadastro['data_nasc'];
            $dados_formulario['sexo'] = $cadastro['genero'];
            $dados_formulario['cpf_cnpj'] = $cadastro['cpf_cnpj'];
            $dados_formulario['email'] = $cadastro['email'];
            $dados_formulario['telefone'] = $cadastro['telefone_resid'];
            $dados_formulario['cep'] = $cadastro['cep'];
            $dados_formulario['estado'] = $cadastro['estado'];
            $dados_formulario['cidade'] = $cadastro['cidade'];
            $dados_formulario['endereco'] = $cadastro['endereco'];
            $dados_formulario['numero'] = $cadastro['numero'];
            $dados_formulario['complemento'] = $cadastro['complemento'];
            $dados_formulario['bairro'] = $cadastro['bairro'];
        }
    }

?>
<?php if(is_array($car_session->{'cursos'})&&count($car_session->{'cursos'})>0): ?>

<script>
    var checkoutCartTotal = <?php echo number_format(($car_subtotal-$desconto_valor),2,".",""); ?>;
    var DesFP = <?php echo $boleto_desconto; ?>;
    var DesFPVal = <?php echo $boleto_desconto_percentual; ?>;
    var CursoFacebookPixelID = '<?php echo $facebook_pixel_id; ?>';
    var CursoGTMID = '<?php echo $google_tag_manager_id; ?>';
</script>

<section class="checkout-cadastro p-md-0 m-md-0" >

    <div class="clear" ></div>

    <div id="action_preloader" class="display-none text-center fs-22 pt-5 pb-5" >
        <img src="/assets/images/preloader.gif" class="d-inline-block" width="100" /> <br/>
        Registrando seu pedido...
    </div>
    <div id="action_sucesso" class="display-none text-success text-center fs-28 pt-5 pb-5" ></div>

    <form name="form_checkout" id="form_checkout" action="" method="post" >
        <input type="hidden" name="opcao_pagamento" value='1' />
        <input type="hidden" name="csrf_token" value='<?php echo($csrfToken); ?>' />

        <div class="container">
            
            <div class="alert alert-danger error-message display-none mb-5" role="alert" ></div>

            <div class="row">

                <div class="col-md-4 mb-5">
                    <h3 class="checkout-cadastro__title"><span class="number-form">1</span>Seu dados</h3>
                    <div class="cadastro login">

                        <?php if (false):
                            //!$m_cadastro->isLogged()
                        ?>
                            <a href="/login-cadastro" class="btn btn-azul fs-20 mb-4" style="width:100%;" >Já sou cadastrado</a>
                        <?php endif; ?>

                        <div class="form-group">
                            <label class="checkout-cadastro__label" for="nome_completo">Nome completo</label>
                            <input type="text" name="nome_completo" id="nome_completo" data-required="true" value="<?php echo $dados_formulario['nome_completo']; ?>" class="form-control form--custom ">
                        </div>
                        <div class="form-group">
                            <label class="checkout-cadastro__label" >CPF/CNPJ</label>
                            <input type="text" name="cpf_cnpj" id="cpf_cnpj" class="form-control form--custom mask_cpfcnpj cpf_cnpj" value="<?php echo $dados_formulario['cpf_cnpj']; ?>" data-required="true" >
                            
                        </div>
                        <div class="form-group">
                            <label class="checkout-cadastro__label" >E-mail</label>
                            <input type="text" name="email" data-required="true" class="form-control form--custom " value="<?php echo $dados_formulario['email']; ?>" />
                        </div>

                        <?php if (false): ?>
                            <div class="form-group">
                                 <label class="checkout-cadastro__label">Como deseja ser chamado</label>
                                <input type="text" name="nome_informal" value="<?php echo $dados_formulario['nome_informal']; ?>" class="form-control form--custom ">
                               
                            </div>
                            <div class="form-row align-items-end">
                                <div class="form-group col-md">
                                    <label class="checkout-cadastro__label">Data de Nascimento</label>
                                    <input type="text" name="data_nascimento" id="data_nascimento" value="<?php echo ($dados_formulario['data_nascimento']!=0) ? date("d/m/Y",strtotime($dados_formulario['data_nascimento'])) : ''; ?>" class="form-control form--custom data mask_date">
                                </div>
                                <div class="form-group col-md">
                                    <label class="checkout-cadastro__label">Sexo</label>
                                    <select name="sexo" class="sexo form-control" data-required="true" >
                                        <option value="0" >--</option>
                                        <option value="1" <?php echo ($dados_formulario['sexo']==1)?'selected':''; ?> >Masculino</option>
                                        <option value="2" <?php echo ($dados_formulario['sexo']==2)?'selected':''; ?> >Feminino</option>
                                    </select>
                                </div>
                            </div>
                        <?php endif ?>

                        <div class="form-group">
                            <label class="checkout-cadastro__label" >Telefone</label>
                            <input type="text" name="telefone" id="telefone" class="form-control form--custom telefone mask_spcelphones" value="<?php echo $dados_formulario['telefone']; ?>" data-required="true" >
                        </div>

                        <?php if (false):
                            //!$m_cadastro->isLogged()
                        ?>
                            <div class="clear"><br /></div>
                            <div class="form-group">
                                <h5 class="checkout__subtitle" >Dados para login</h5>
                                <hr class="titulo-separa" style="border-bottom-color: #eee;" >
                            </div>
                            <div class="form-group">
                                <label class="checkout-cadastro__label" >E-mail</label>
                                <input type="text" name="email" data-required="true" value="" class="form-control form--custom " />
                            </div>
                            <div class="form-group">
                                <label class="checkout-cadastro__label" >Senha</label>
                                <input type="password" name="senha" value="" class="form-control form--custom " data-required="true" />
                            </div>
                            <div class="form-group">
                                <label class="checkout-cadastro__label" >Confirme a senha</label>
                                <input type="password" name="senha_c" value="" class="form-control form--custom " data-required="true" >
                            </div>

                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="receber_boletim" name="receber_boletim" value="1">
                                <label class="form-check-label fs-12 montserrat_regular rosa_97" for="receber_boletim" >
                                    Desejo receber novidades do site no meu e-mail.
                                </label>
                            </div>

                        <?php endif ?>

                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="termos" name="termos" value="1">
                            <label class="form-check-label fs-14 montserrat_regular rosa_97" for="termos" >
                                Li, compreendi, e aceito os <a href="/politica-de-privacidade" target="_blank" >termos de uso</a>.
                            </label>
                        </div>
                        
                    </div>
                </div>
                <div class="col-md-8" >
                    <h3 class="checkout-cadastro__title" ><span class="number-form" >2</span>Dados de pagamento</h3>
                    <?php self::getView('view-carrinho-checkout-gateway-form-iugu'); ?>
                </div>
                <?php if (false): ?>
                <div class="col-md-6">
                    <h3 class="checkout-cadastro__title" ><span class="number-form">2</span>Endereço</h3>
                    <div class="endereco login">
                        <div class="form-group">
                            <label for="cep" class="checkout-cadastro__label">CEP <span class="just-numbers fs-12 cinza-claro font-italic">(Somente números)</span></label>
                            <input type="text" class="form-control form--custom mask_cep cep" id="cep" value="<?php echo $dados_formulario['cep']; ?>" data-required="true"
                                onkeyup="javascript:if(this.value.length==9){ckCompletaEnderecoPorCEP(this.form);};getAndSetFreteInformation(this.form);"
                                onblur="javascript:if(this.value.length==9){ckCompletaEnderecoPorCEP(this.form);};getAndSetFreteInformation(this.form);"
                            >
                        </div>
                        <div class="form-group">
                            <label class="checkout-cadastro__label">Endereço</label>
                            <input type="text" name="endereco" class="form-control form--custom " value="<?php echo $dados_formulario['endereco']; ?>" data-required="true">
                        </div>
                        <div class="form-group">
                            <label class="checkout-cadastro__label">Número</label>
                            <input type="text" name="numero" id="numero" class="form-control form--custom " value="<?php echo $dados_formulario['numero']; ?>" data-required="true">
                        </div>
                        <div class="form-group">
                            <label class="checkout-cadastro__label">Complemento</label>
                            <input type="text" name="complemento" class="form-control form--custom " value="<?php echo $dados_formulario['complemento']; ?>" >
                        </div>
                        <div class="form-group">
                            <label class="checkout-cadastro__label">Bairro</label>
                            <input type="text" name="bairro" class="form-control form--custom " value="<?php echo $dados_formulario['bairro']; ?>" data-required="true">
                        </div>
                        <div class="form-row align-items-end">
                            <div class="form-group col-md-7">
                                <label class="checkout-cadastro__label">Cidade</label>
                                <input type="text" name="cidade" class="form-control form--custom " value="<?php echo $dados_formulario['cidade']; ?>"data-required="true">
                            </div>
                            <div class="form-group col-md-5">
                                <label class="checkout-cadastro__label">Estado</label>
                                <select id="estado" name="estado" class="form-control" data-required="true" >
                                    <option value="" >--</option>
                                    <?php 
                                        $estados = Sis::getState();
                                        $estado_selecionado = $dados_formulario['estado'];
                                        foreach($estados as $key => $estado){
                                            $selected = (trim($estado_selecionado)==trim($key))?"selected":"";
                                            echo('<option value="'.$key.'" '.$selected.' >'.$key.'</option>');
                                        }
                                     ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif ?>
            </div>
        </div>

        <section class="mt-3 mb-3">
            <div class="container" >
                <div class="text-center">
                    <button id="confirmBtnWithGateWay" class="btn btn-azul btn-296" > FINALIZAR O PEDIDO </button>
                    <!-- confirmBtnNoGateWay -->
                </div>
                <br/><br/>

            </div>
        </section>
        
        <div class="clear"></div>

    </form>

</section>

<style>
    .display-none{ display: none; }
    .dados-cadastro .cadastro, 
    .dados-cadastro .endereco, 
    .dados-cadastro .entrega{ height:auto !important; }
    .entrega .observacoes{ margin-top:20px; }
</style>

<?php endif; ?>