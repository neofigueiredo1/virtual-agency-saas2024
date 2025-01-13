<?php 
    $bannerIstitucionalDestaques = self::getBanner(13,$pagina=0,$randomize=0,$lista=0,$limite=0,$cycle=1,$cycle_pause=0,$arrayImages=1,$prioridade=0); 
?>  
<section class="s_institucional_destaques">
    <div class="wrapper wrapper-1360">
        <div class="informacoes_section text-center">
            <?php
                $valore = Sis::config("DESCRICAO-INSTITUCIONAL-DESTAQUES");
                $valoresArray = explode(PHP_EOL,$valore);
                foreach ($valoresArray as $key => $valor):
                if($key == 0):
            ?>
                <h1><?php echo $valor;?></h1>
            <?php else:?>
                <p><?php echo $valor; ?></p>
            <?php endif; endforeach;?>
        </div>
        <?php if(is_array($bannerIstitucionalDestaques) &&count($bannerIstitucionalDestaques)>0) :?>
            <?php foreach ($bannerIstitucionalDestaques as $key => $institucionalDestaque): ?>
                <?php 
                    if($institucionalDestaque['subtipo_banner']==3):
                    if($key == 4):
                ?>
                    <div class="row item_destaque align-items-center">
                        <div class="col-md-6 order-md-0 order-1 pr-md-0">
                            <div class="informacoes_section">
                                <article>
                                    <?php echo $institucionalDestaque['descricao'];?>
                                </article>
                            </div>
                        </div>
                        <div class="col-md-6 pl-md-0 order-md-1 order-0">
                            <figure class="responsive">
                                <img src="/sitecontent/banner/<?php echo $institucionalDestaque['arquivo'];?>" alt="">
                            </figure>
                        </div>
                    </div>
                    <?php else:?>
                    <div class="row item_destaque">
                        <div class="col-md-6 pr-md-0">
                            <figure class="responsive">
                                <img class="ml-auto" src="/sitecontent/banner/<?php echo $institucionalDestaque['arquivo'];?>" alt="">
                            </figure>
                        </div>
                        <div class="col-md-6 pl-md-0">
                           <div class="informacoes_section">
                               <article>
                                    <?php echo $institucionalDestaque['descricao'];?>
                                </article>
                           </div>
                        </div>
                    </div>
                    <?php endif;endif;?>	
                <?php endforeach ?>
        <?php endif;?>
    </div>
</section>
