<?php require_once("site/direct-includes/site-header.php"); ?>
<?php require_once("site/direct-includes/site-layout-topo.php"); ?>
    
    <main>
        
        <?php $m_banner->getView('view-interna-topo'); ?>

        <?php $m_banner->getView('view-capa-area-produtor'); ?>

        <?php if(false): ?>
            <!-- Section Conteudo -->
            <section class="s_conteudo_padrao" >
                <div class="wrapper wrapper-1340 py-5 my-lg-5 my-md-4">
                    
                    <div class="row justify-content-between">
                        <div class="col-md-6 mb-md-0 mb-4" >
                            <h1 class="fs-32 azul" >
                                <?php echo $pagina_data['titulo_seo'];?>
                            </h1>
                            <div class="informacoes_section mt-4">
                                <?php echo Sis::desvar($pagina_data['conteudo']); ?>
                            </div>
                        </div>
                        <div class="col-md-6">

                            <?php
                            $lateralB = $m_banner->getBanner(23,2,$pagina_data['codigo'],$randomize=0,$lista=0,$limite=0,$cycle=0,$cycle_pause=0,$arrayImages=1,$prioridade=1); 
                            if(is_array($lateralB) &&count($lateralB)>0) :
                                $lateralr = $lateralB[0];
                            ?>
                                <figure class="d-flex justify-content-center" >
                                    <img class="img-fluid" src="/sitecontent/banner/<?php echo $lateralr['arquivo'];?>" alt="<?php echo $lateralr['nome'];?>" />
                                </figure>
                            <?php endif; ?>

                        </div>
                    </div>

                </div>
            </section>
            <!-- ./Section Conteudo -->
        <?php endif; ?>


        <section class="d-flex justify-content-center bg-light py-5">
            <div class="wrapper wrapper-1340">
                <article class="mxw-550 m-auto">
                    
                    <h2 class="azul text-center mb-5" >Faça parte do Instituto Conexo.</h2>

                    <a href="/login-cadastro?account=produtor" class="btn btn-lg btn-azul-1 d-flex justify-content-center align-items-center m-auto">Cadastre seu produto agora!</a>

                    <?php if (false): ?>
                        <form id="entramosEmContato_frm" class="contato_frm" name="entramosEmContato_frm" role="form" action="" >
                            <input type="hidden" name="postact" value="sendContato" />
                            <input type="hidden" name="origem" value="entraremosEmContato" />
                            <div id='contato_form_sucesso' class="contato_form_sucesso alert alert-success fs-18 cormo-medium text-center" style='display:none;' >
                                Seus dados foram enviados com sucesso! <br/>
                                Em breve, entraremos em contato.
                            </div>
                            <div id='contato_form_preloader' class="contato_form_preloader alert text-center fs-18 cormo-medium" style='display:none;' >
                                <img src="/assets/images/preloader.gif" align="absmiddle" style="display:inline-block;margin:0px;" width="150" /><br/>
                                <small><i>Aguarde, enviando sua mensagem...</i></small>
                            </div>
                            <div id="contato-help" class="contato-help alert alert-danger fs-18 cormo-medium" style='display:none;' >Preencha os campos abaixo corretamente.</div>
                            <div class="clear clear_gray"></div>
                            <div class="contato_form_body">
                                <div class="inputBox form-group form-control p-0 rounded-100">
                                    <input id="ec_nome" name="nome" type="text" class="inputUser form-control" required data-required="true" />
                                    <label for="ec_nome" class="labelInput mb-0 cinza-2">Nome</label>
                                </div>
                                <div class="inputBox form-group form-control p-0 rounded-100">
                                    <input id="ec_telefone" name="telefone" type="tel" class="inputUser form-control mask_spcelphones" required data-required="true" />
                                    <label for="ec_telefone" class="labelInput mb-0 cinza-2">Telefone</label>
                                </div>
                                <div class="inputBox form-group form-control p-0 rounded-100">
                                    <input id="ec_email" name="email" type="email" class="inputUser form-control" required data-required="true" />
                                    <label for="ec_email" class="labelInput mb-0 cinza-2">E-mail</label>
                                </div>
                                <div class="d-flex justify-content-center mt-4">
                                    <input type="button" class="btn btn-lg btn-azul-1 d-flex justify-content-center align-items-center m-auto"
                                    onclick="javascript:Util.checkFormRequire(document.entramosEmContato_frm,'#contato-help',sendContato);" value="Enviar" />
                                </div>
                            </div>
                         
                        </form>
                    <?php endif ?>
                    


                </article>
            </div>
        </section>

        <section class="s_passos">
            <div class="wrapper wrapper-1340">
                <h2 class="text-center azul mb-4 mt-4" >
                    <!-- Seja agora um Produtor do Instituto Conexo -->
                    <?php echo Sis::config("SEJA-PRODUTOR-PASSOS-TITULO"); ?>
                </h2>
                <div class="row align-items-center text-center align-self-stretch" >
                    
                    <?php
                    $topicosLista = $m_banner->getBanner(23,3,$pagina_data['codigo'],$randomize=0,$lista=0,$limite=0,$cycle=0,$cycle_pause=0,$arrayImages=1,$prioridade=1); 
                    if(is_array($topicosLista) &&count($topicosLista)>0) :
                    ?>
                        <?php foreach ($topicosLista as $key => $topicosListaItem): ?>
                            <div class="col-lg-4 col-md-6 margin">
                                <article class="bg-azul-1 px-3 py-5 rounded-40">
                                    <h2 class="azul"><?php echo $topicosListaItem['nome']; ?></h2>
                                    <p class="branco"><?php echo strip_tags($topicosListaItem['descricao']); ?></p>
                                </article>
                            </div>
                        <?php endforeach ?>
                    <?php endif; ?>

                    <!-- <div class="col-lg-4 col-md-6 margin">
                        <article class="bg-azul-1 px-3 py-5 rounded-40">
                            <h2 class="azul">Passo 1:</h2>
                            <p class="branco">Cadastro 100% online - Realizando o seu cadastro via formulário nessa landing page.</p>
                        </article>
                    </div>
                    <div class="col-lg-4 col-md-6 margin">
                        <article class="bg-azul-1 px-3 py-5 rounded-40">
                            <h2 class="azul">Passo 2:</h2>
                            <p class="branco">Rapidez e agilidade - Ao se cadastrar, você já está computado como um dos nossos Produtores. Nossa equipe entrará em contato para dar mais detalhes.</p>
                        </article>
                    </div>
                    <div class="col-lg-4 col-md-6 margin">
                        <article class="bg-azul-1 px-3 py-5 rounded-40">
                            <h2 class="azul">Passo 3:</h2>
                            <p class="branco">Pronto - Agora é só desfrutar dos nossos tratamentos e começar os trabalhos</p>
                        </article>
                    </div> -->
                </div>
            </div>
        </section>




    </main>

<?php require_once("site/direct-includes/site-layout-rodape.php"); ?>
<?php require_once("site/direct-includes/site-footer.php"); ?>