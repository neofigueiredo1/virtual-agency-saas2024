<?php
global $pagina_data, $pagina_uri, $m_cadastro, $usuarioConta;
$usuarioConta = $m_cadastro->getMyDataUserInfo();
?>


<nav id="navbar-area-aluno" class="navbar navbar-expand-lg navbar-light p-0 pr-md-3" style="min-width:270px; @media screen and (max-width:991px){min-width:initial;}" >

    


    <header class="wrapper wrapper-1340 py-3 bg-light d-block d-lg-none" style="min-height:90px;" >
        <button class="navbar-toggler collapsed float-right" type="button" data-toggle="collapse" data-target="#navbarAreaAluno" aria-controls="navbarAreaAluno" aria-expanded="false" aria-label="Toggle navigation" ><span class="navbar-toggler-icon"></span></button>

        <a class="navbar-brand" href="#" >
            <h1 class="cinza-1 fs-28 m-0" style="line-height:20px;" >
                
                <?php if (trim($pagina_data['titulo_mae']!="")): ?>
                    <small class="fs-16" >
                        Minha Conta
                    </small><br/>
                    <?php echo $pagina_data['titulo'];?>
                <?php else: ?>
                    Minha Conta
                <?php endif ?>
            
            </h1>
        </a>
    </header>

    <div class="collapse navbar-collapse" id="navbarAreaAluno" >
        
       <div>
           
            <div class="card mb-3" >
                <div class="card-header bg-azul text-white">
                    Sua conta
                </div>
                <div class="card-body px-0" >
                    
                    <ul class="navbar-nav" >
                        <li class="nav-item <?php echo ($pagina_uri=='/minha-conta' || $pagina_uri=='/minha-conta/meus-cursos')?'active':''; ?>" >
                            <a class="nav-link" href="/minha-conta/meus-cursos" >Meus cursos</a>
                        </li>
                        
                        <li class="nav-item <?php echo ($pagina_uri=='/minha-conta/minhas-compras')?'active':''; ?>" >
                            <a class="nav-link" href="/minha-conta/minhas-compras" >Minhas compras</a>
                        </li>
                        <li class="nav-item <?php echo ($pagina_uri=='/minha-conta/cronograma-de-estudo' || $pagina_uri=='/minha-conta/criar-meu-cronograma-de-estudo')?'active':''; ?>" >
                            <a class="nav-link" href="/minha-conta/cronograma-de-estudo" >Cronograma de estudo</a>
                        </li>
                        <li class="nav-item <?php echo ($pagina_uri=='/minha-conta/meus-dados')?'active':''; ?>" >
                            <a class="nav-link" href="/minha-conta/meus-dados" >Editar meus dados</a>
                        </li>
                    </ul>

                </div>
            </div>

            <div class="card mb-3" >
                <div class="card-header bg-azul text-white">
                    Suas vendas
                </div>
                <div class="card-body px-0" >
                    
                    <ul class="navbar-nav" >
                
                        <li class="nav-item <?php echo ($pagina_uri=='/minha-conta/cadastro-de-produtos')?'active':''; ?>" >
                            <a class="nav-link" href="/minha-conta/cadastro-de-produtos" >Cadastro de produtos</a>
                        </li>
                        
                        <li class="nav-item <?php echo ($pagina_uri=='/minha-conta/minhas-vendas')?'active':''; ?>" >
                            <a class="nav-link" href="/minha-conta/minhas-vendas" >Minhas vendas</a>
                        </li>
                        
                        <li class="nav-item <?php echo ($pagina_uri=='/minha-conta/meus-cursos-a-venda')?'active':''; ?>" >
                            <a class="nav-link" href="/minha-conta/meus-cursos-a-venda" >Meus cursos &agrave; venda</a>
                        </li>
                        
                        <li class="nav-item <?php echo ($pagina_uri=='/minha-conta/mensagens-de-alunos')?'active':''; ?>" >
                            <a class="nav-link" href="/minha-conta/mensagens-de-alunos" >Mensagens de Alunos
                                <?php if ((int)$_SESSION['plataforma_usuario']['messages_unread']>0): ?>
                                    <span class="badge badge-warning" ><?php echo (int)$_SESSION['plataforma_usuario']['messages_unread']; ?></span>
                                <?php endif ?>
                            </a>
                        </li>
                        <?php if ($usuarioConta[0]['lp_active']): ?>
                        <li class="nav-item <?php echo ($pagina_uri=='/minha-conta/mensagens-de-alunos')?'active':''; ?>" >
                            <a class="nav-link" href="/minha-conta/minha-pagina-de-vendas" >Minha Página de Vendas</a>
                            <!-- <a class="nav-link" href="/assets/vendor/grapesjs" target="_blank" >Minha Página de Vendas</a> -->
                        </li>
                        <?php endif ?>

                    </ul>

                </div>
            </div>

       </div>
        
    </div>

</nav>