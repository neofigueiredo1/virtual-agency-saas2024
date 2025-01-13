<?php 
    $ondeEstamosIntro = self::getBanner(9,$pagina=0,$randomize=0,$lista=0,$limite=0,$cycle=1,$cycle_pause=0,$arrayImages=1,$prioridade=0); 
    /* 
       • Banner Onde Estamos Desktop - (1920x600) = Tipo de banner (1)
       • Banner Onde Estamos Mobile  - (780x450) = Tipo de banner (2)
    */
?>  
<section class="s_bg_intro desktop">
    <?php if(is_array($ondeEstamosIntro) &&count($ondeEstamosIntro)>0) :?>
            <?php foreach ($ondeEstamosIntro as $ondeEstamosDesk): ?>
                <?php if($ondeEstamosDesk['subtipo_banner']==1):?>
                    <div class="item">
                        <div class="bg_intro" style="background-image: url('/sitecontent/banner/<?php echo $ondeEstamosDesk['arquivo'];?>');">
                            <div class="infos_intro">
                                <?php echo $ondeEstamosDesk['descricao'];?>
                                <?php if($ondeEstamosDesk['url']!=""):?>
                                    <div>
                                        <a href="<?php echo $ondeEstamosDesk['url'];?>" class="btn btn-secondary btn-216 mt-xl-5 mt-4" target="<?php echo $ondeEstamosDesk['alvo'];?>"> Saiba mais </a>
                                    </div>
                                <?php endif;?>
                            </div>
                        </div>
                    </div>
                <?php endif;?>	
            <?php endforeach ?>
    <?php endif;?>	
</section>

<section class="s_bg_intro mobile">
    <?php if(is_array($ondeEstamosIntro) &&count($ondeEstamosIntro)>0) :?>
        <?php foreach ($ondeEstamosIntro as $ondeEstamosMob): ?>
            <?php if($ondeEstamosMob['subtipo_banner']==2):?>
                <div class="item">
                    <a href="<?php echo ($ondeEstamosMob['url']!="") ? $ondeEstamosMob['url']: ''; ?>">
                        <div class="bg_intro" style="background-image: url('/sitecontent/banner/<?php echo $ondeEstamosMob['arquivo'];?>">
                        </div>
                    </a>
                </div>
            <?php endif;?>	
        <?php endforeach ?>
    <?php endif;?>	
</section>