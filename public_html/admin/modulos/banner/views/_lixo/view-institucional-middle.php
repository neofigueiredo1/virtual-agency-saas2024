<?php 
    $bannerIstitucional = self::getBanner(13,$pagina=0,$randomize=0,$lista=0,$limite=0,$cycle=1,$cycle_pause=0,$arrayImages=1,$prioridade=0); 
?>  
<section class="s_middle_institucional">
    <div class="wrapper wrapper-1360">
        <?php if(is_array($bannerIstitucional) &&count($bannerIstitucional)>0) :?>
                <?php foreach ($bannerIstitucional as $institucional): ?>
                    <?php if($institucional['subtipo_banner']==1):?>
                            <div class="bg_institucional" style="background-image: url('/sitecontent/banner/<?php echo $institucional['arquivo'];?>');">
                                <div class="row justify-content-end align-items-center">
                                    <div class="col-md-6">
                                        <div class="informacoes_section">
                                            <h1><?php echo $institucional['nome']?></h1>
                                            <?php echo $institucional['descricao'];?>
                                            <?php if($institucional['url']!=""):?>
                                                <div>
                                                    <a href="<?php echo $institucional['url'];?>" class="btn btn-primary btn-230 mt-xl-5 mt-4" target="<?php echo $institucional['alvo'];?>"> Saiba mais </a>
                                                </div>
                                            <?php endif;?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif;?>	
                <?php endforeach ?>
        <?php endif;?>	
    </div>
</section>
