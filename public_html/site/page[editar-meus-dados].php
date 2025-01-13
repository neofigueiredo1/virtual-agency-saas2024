<?php require_once("site/direct-includes/site-header.php"); ?>
<?php require_once("site/direct-includes/site-layout-topo.php"); ?>
    
    <main>
        
        <?php $m_banner->getView('view-interna-topo'); ?>

        <header class="bg-azul text-center py-5 branco" >
            <h1>
                <?php if (trim($pagina_data['titulo_mae']!="")): ?>
                    <small><?php echo $pagina_data['titulo_mae'];?></small>
                <?php endif ?>
                <?php echo $pagina_data['titulo'];?>
            </h1>
        </header>

        <!-- Section Conteudo -->
        <section class="s_conteudo_padrao" >
            <div class="wrapper wrapper-1340 py-5 my-lg-5 my-md-4">
                <?php $m_cadastro->getView("view-editar-meu-perfil"); ?>
            </div>
        </section>
        <!-- ./Section Conteudo --> 
    </main>

<?php require_once("site/direct-includes/site-layout-rodape.php"); ?>
<?php require_once("site/direct-includes/site-footer.php"); ?>