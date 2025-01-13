
<?php $m_cadastro->getView("view-cadastro-newsletter"); ?>

<footer class="footer py-5" >

    <div class="wrapper wrapper-1340 d-xl-flex justify-content-between align-items-start" >


        <figure class="footer-item footer-logo mb-5">
            <a href="/">
                <img src="/assets/images/instituto-conexo-logo.svg" alt="" width="200" />
            </a>
        </figure>


        <div class="d-md-flex justify-content-between flex-fill footer-item-center mx-0 mx-lg-5" >

            <nav class="footer-item footer-nav mb-5 mb-lg-0">
                <?php
                    /* Menu Rodapé 1 */
                    $menu=2;
                    $lista=true;
                    $separador=false;
                    $submenu=false;
                    $inicio=false;
                    $inicio_side='';
                    $inicio_txt="";
                    echo $m_conteudo->get_menu($menu,$lista,$separador,$submenu,$inicio,$inicio_side,$inicio_txt);
                ?>
            </nav>

            <nav class="footer-item footer-nav mb-5 mb-lg-0">
                <?php
                    /* Menu Rodapé 1 */
                    $menu=3;
                    $lista=true;
                    $separador=false;
                    $submenu=false;
                    $inicio=false;
                    $inicio_side='';
                    $inicio_txt="";
                    echo $m_conteudo->get_menu($menu,$lista,$separador,$submenu,$inicio,$inicio_side,$inicio_txt);
                ?>
            </nav>

            <div class="footer-item footer-endereco mb-5 mb-lg-0">
       
                <div class="d-flex justify-content-flex-start mb-3">
                    <i class="fas fa-envelope m-1 mr-3"></i>
                    <div class="m-0" >
                       <?php echo Sis::config("RODAPE-CONTATO-EMAIL");?>
                    </div>
                </div>
                <div class="d-flex justify-content-flex-start mb-3">
                    <i class="fas fa-map-marker-alt m-1 mr-3" ></i>
                    <address class="m-0" >
                        <?php
                            $valoreEndereco = Sis::config("RODAPE-ENDERECO");
                            $valoreEndereco = str_replace(PHP_EOL,"<br/>",$valoreEndereco);
                            echo $valoreEndereco;
                        ?>
                    </address>
                </div>
                <div class="d-flex justify-content-flex-start mb-3">
                    <i class="fas fa-phone m-1 mr-3"></i>
                    <div class="m-0" >
                        <?php echo Sis::config("RODAPE-CONTATO-TELEFONE");?><br/>
                    </div>
                </div>
                 
            </div>

        </div>

        
        <div class="footer-item footer-social mb-5">
            <div class="redes-sociais">
                <strong class="preto" >Redes sociais</strong><br/>
                <div class="d-flex justify-content-between" >
                    <a href="<?php echo Sis::config('SOCIAL-LINK-INSTAGRAM'); ?>" target="_blank" class="d-inline-block m-2" > 
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="<?php echo Sis::config('SOCIAL-LINK-FACEBOOK'); ?>" target="_blank" class="d-inline-block m-2" > 
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="<?php echo Sis::config('SOCIAL-LINK-TWITTER'); ?>" target="_blank" class="d-inline-block m-2" > 
                        <i class="fab fa-twitter"></i>
                    </a>
                </div>
            </div>
        </div>
        
    </div>
    <div class="footer-assina wrapper wrapper-1340 text-center pt-4 fs-14" >
        &copy;<?php echo date('Y'); ?> &nbsp; Instituto Conexo
    </div>

</footer>

<div class="whats-flutuante" >
    <a href="http://api.whatsapp.com/send?1=pt_BR&phone=55<?php echo Sis::config('BOTAO-FIXO-WHATSAPP')?>" target="_blank">
        <img src="/assets/images/icon-whats-flutuante.svg" alt="" class="svg">
    </a>
</div>
