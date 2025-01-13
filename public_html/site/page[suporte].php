<?php require_once("site/direct-includes/site-header.php"); ?>
<?php require_once("site/direct-includes/site-layout-topo.php"); ?>
    
    <main>
        
        <?php $m_banner->getView('view-interna-topo'); ?>

        <!-- Section Conteudo -->
        <section class="s_conteudo_padrao" >
            <div class="wrapper wrapper-1340 py-5 my-lg-5 my-md-4">
                
                <div class="row justify-content-between">
                    <div class="col-md-5 mb-md-0 mb-4" >
                        <h1 class="fs-32 azul" >
                            <?php if (trim($pagina_data['titulo_mae']!="")): ?>
                                <small><?php echo $pagina_data['titulo_mae'];?></small>
                            <?php endif ?>
                            <?php echo $pagina_data['titulo'];?>
                        </h1>
                        <div class="informacoes_section">
                            <?php echo Sis::desvar($pagina_data['conteudo']); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <?php require_once("site/direct-includes/site-contato-form.php"); ?>
                    </div>
                </div>

            </div>
        </section>
        <!-- ./Section Conteudo --> 
    </main>

<?php require_once("site/direct-includes/site-layout-rodape.php"); ?>
<?php require_once("site/direct-includes/site-footer.php"); ?>