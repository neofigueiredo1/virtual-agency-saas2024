<?php 
global $pagina_data;
$bannersGaleria = self::getBanner(16,$pagina=0,$randomize=0,$lista=0,$limite=0,$cycle=1,$cycle_pause=0,$arrayImages=1,$prioridade=0); 
if (is_array($bannersGaleria)&&count($bannersGaleria)>0):?>
<section class="s_galeria">
    <div class="container_galeria">
        <div class="owl-carousel owl-theme owl-galeria-g">
            <?php foreach ($bannersGaleria as $key => $galeria):?>
                <?php if($galeria['subtipo_banner']==2):?>
                    <div class="item">
                        <div class="bg_galeria" style="background-image: url('/sitecontent/banner/<?php echo $galeria['arquivo']; ?>');">
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <div class="owl-carousel owl-theme owl-galeria-p">
            <?php foreach ($bannersGaleria as $key => $galeria):?>
                <?php if($galeria['subtipo_banner']==2):?>
                    <div class="item">
                        <a href="#" data-index="<?php echo $count;?>" >
                            <div class="item_p_galeria" style="background-image: url('/sitecontent/banner/<?php echo $galeria['arquivo']; ?>');">
                            </div>
                        </a>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>