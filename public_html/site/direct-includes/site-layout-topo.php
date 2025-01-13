<?php
$myAccountInfo = $m_cadastro->getMyDataUserInfo();
?>

<!-- HEADER TOPO 1 -->
<header>

    <div class="sup-header bg-azul-1" >
        <div class="wrapper wrapper-1340" >
            
            <div class="sup-social" >
                <a href="<?php echo Sis::config('SOCIAL-LINK-INSTAGRAM'); ?>" target="_blank" title="Siga-nos no Instagram" ><i class="fab fa-instagram"></i></a>
                <a href="<?php echo Sis::config('SOCIAL-LINK-TWITTER'); ?>" target="_blank" title="Siga-nos no Twitter" ><i class="fab fa-twitter"></i></a>
                <a href="<?php echo Sis::config('SOCIAL-LINK-FACEBOOK'); ?>" target="_blank" title="Siga-nos no Facebook" ><i class="fab fa-facebook-f"></i></a>
            </div>

            <h2 class="branco" ><?php echo Sis::config("TOPO-CABECALHO-FRASE-TARJA"); ?></h2>

        </div>
    </div>

    <div class="header wrapper wrapper-1340" >

        <nav id="navbar-principal" class="navbar navbar-expand-lg p-0  w-100" >
          
          <a class="navbar-brand" href="/" >
            <figure>
                <img src="/assets/images/instituto-conexo-logo.svg" alt="Instituto Conexo" class="img-fluid" />
            </figure>
          </a>

          <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>

            <div class="collapse navbar-collapse justify-content-between" id="navbarSupportedContent" >

                <?php if ($myAccountInfo):
                    $myAccountInfo = $myAccountInfo[0];
                    $firstName = strtok($myAccountInfo['nome_completo'], " ");
                ?>
                <div class="header_auth_container" >
                    <div class="header_auth" >

                        <div class="user-info" style="position:relative;" >
                            <figure class="responsive d-flex align-items-center m-0" >
                                <img src="/assets/images/ico-user.svg" alt="" class="mr-2" > 
                                <figcaption class="fs-14 text-nowrap" >
                                    <strong>Ol&aacute; <?php echo $firstName ?></strong>
                                    <br/>
                                    <a href="/minha-conta" class="fs-14 cinza p-0" >
                                    Minha conta
                                    </a>
                                    <small class="fs-14" >&nbsp;|&nbsp;</small>
                                    <a href="/?logout=1" class="fs-14 cinza p-0" >Sair</a>
                                </figcaption>
                            </figure>
                        </div>

                    </div>
                </div>
                <?php endif ?>

                <?php
                    /* Menu */
                    $menu=1;
                    $lista=true;
                    $separador=false;
                    $submenu=true;
                    $inicio=false;
                    $inicio_side='';
                    $inicio_txt="";
                    echo $m_conteudo->get_menu($menu,$lista,$separador,$submenu,$inicio,$inicio_side,$inicio_txt);
                ?>
                

                <?php if (!$myAccountInfo): ?>
                <div class="header_auth_container" >
                    <div class="header_auth" >
                        <a href="/login-cadastro" class="btn btn-azul d-flex align-items-center justify-content-center" type="submit" >Login</a>
                    </div>
                </div>
                <?php endif ?>
                

            </div>
        </nav>



    </div>


</header>




