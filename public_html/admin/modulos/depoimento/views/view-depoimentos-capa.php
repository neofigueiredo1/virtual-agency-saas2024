<?php
    global $pagina;
    $depoimentos = self::listDepoimentos();
    if(is_array($depoimentos)&&count($depoimentos)>0):
?>
    <section class="s_depoimentos py-5 <?php echo ($pagina == 0) ? 's-depoimento-home' : '' ?>" >
        <div class="wrapper wrapper-1340 py-xl-5 " >

            <div class="mb-5">
                <h2 class="text-center branco" ><?php echo Sis::config("CAPA-SESSAO-DEPOIMENTOS-TITUTLO"); ?></h2>
                <p class="text-center branco" ><?php echo Sis::config("CAPA-SESSAO-DEPOIMENTOS-DESCRICAO"); ?></p>
            </div>

            <div class="depoimentos_lista depoimentos_lista_capa owl-carousel-depoimentos-capa owl-carousel owl-theme">
                
                <?php 
                foreach($depoimentos as $depoimento) :
                    $descricaoDepoimento = strip_tags($depoimento['descricao']);
                ?>
                    <div class="item" >
                        <article class="depoimento-item">
                            
                                <div class="d-thumb" style="background-image:url('/sitecontent/depoimento/depoimento/<?php echo $depoimento['imagem'];?>')" ></div>
                                <div class="d-info" >
                                    <strong class="d-block mt-3"><?php echo $depoimento['nome'];?></strong>
                                    <hr>
                                    <p class="verde" ><?php echo $descricaoDepoimento;?></p>
                                </div>
                            
                        </article>
                    </div>
                <?php endforeach;?>
                
            </div>

        </div>
    </section>

           

<?php endif;?>