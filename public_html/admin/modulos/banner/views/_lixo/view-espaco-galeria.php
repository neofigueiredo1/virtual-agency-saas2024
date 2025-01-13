<?php 
    $bannersEspaco = self::getBanner(11,$pagina=0,$randomize=0,$lista=0,$limite=0,$cycle=1,$cycle_pause=0,$arrayImages=1,$prioridade=0); 
    /* 
       • Banners Galeria (778x513) (1)
    */
?>  
<section class="box_galeria_espaco">
    <div class="owl-carousel owl-theme owl-galeria">
        <?php if(is_array($bannersEspaco) &&count($bannersEspaco)>0) :?>
                <?php foreach ($bannersEspaco as $galeria): ?>
                    <?php if($galeria['subtipo_banner']==1):?>
                        <div class="item">
                            <a href="/sitecontent/banner/<?php echo $galeria['arquivo'];?>" data-toggle="lightbox" data-gallery="espaco-gallery">
                                <!-- <img src="https://unsplash.it/600.jpg?image=251"> -->
                                <div class="bg_espaco" style="background-image: url('/sitecontent/banner/<?php echo $galeria['arquivo'];?>');">
                                </div>
                            </a>
                        </div>
                    <?php endif;?>	
                <?php endforeach ?>
        <?php endif;?>	
    </div>
</section>

