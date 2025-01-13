<?php 
    $bannerIstitucionalProfessores = self::getBanner(13,$pagina=0,$randomize=0,$lista=0,$limite=0,$cycle=1,$cycle_pause=0,$arrayImages=1,$prioridade=0); 
?>  
<section class="s_institucional_professores">
    <div class="wrapper wrapper-1360">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-lg-0 mb-4">
                <div class="informacoes_section">
                    <?php
                        $valore = Sis::config("DESCRICAO-INSTITUCIONAL-PROFESSORES");
                        $valoresArray = explode(PHP_EOL,$valore);
                        foreach ($valoresArray as $key => $valor):
                        if($key == 0):
                    ?>
                        <h1><?php echo $valor;?></h1>
                    <?php else:?>
                        <p><?php echo $valor; ?></p>
                    <?php endif; endforeach;?>
                </div>
            </div>
            <div class="col-lg-6 d-lg-flex justify-content-between text-center">
                <?php if(is_array($bannerIstitucionalProfessores) &&count($bannerIstitucionalProfessores)>0) :?>
                    <?php foreach ($bannerIstitucionalProfessores as $institucionalProfessor): ?>
                        <?php if($institucionalProfessor['subtipo_banner']==2):?>
                            <figure class="item_professor responsive">
                                <img src="/sitecontent/banner/<?php echo $institucionalProfessor['arquivo'];?>" alt="">
                                <figcaption>
                                    <article>
                                        <h2><?php echo $institucionalProfessor['nome'];?></h2>
                                        <?php echo $institucionalProfessor['descricao'];?>
                                    </article>
                                </figcaption>
                            </figure>
                        <?php endif;?>	
                    <?php endforeach ?>
            <?php endif;?>
            </div>
        </div>
    </div>
</section>
