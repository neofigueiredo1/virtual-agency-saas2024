<?php 
    $tipo			      = 15;
    $pagina			      = 10;
    $randomize		      = 1;
    $limite			      = 0;
    $bannerCapa           = self::getBannerData($tipo,$pagina,$randomize,$limite);
?>

<?php 
    $tipo			      = 13;
    $pagina			      = 10;
    $randomize		      = 0;
    $limite			      = 0;
    $bannerCapaMobile     = self::getBannerData($tipo,$pagina,$randomize,$limite);
?>
<section class="s_banner_intro">
    <?php if(is_array($bannerCapa)&&count($bannerCapa)>0) : ?>
        <div class="d-lg-block d-none">
                <div class="owl-carousel owl-banner-topo owl-theme">
                    <?php foreach ($bannerCapa as $banner) : ?>
                        <div class="item">
                            <a href="<?php echo $banner['url'] ?>" target="<?php echo $banner['alvo'] ?>">
                                <div class="item_banner_cap_big" style="background-image: url('/sitecontent/banner/<?php echo $banner['arquivo'];?>');">
                                </div>
                            </a>
                        </div>
                    <?php endforeach ?>            
                </div>
        </div>
    <?php endif;?>

    <?php if(is_array($bannerCapaMobile)&&count($bannerCapaMobile)>0) : ?>
        <div class="d-lg-none d-block">
                <div class="owl-carousel owl-banner-topo owl-theme">
                    <?php foreach ($bannerCapaMobile as $bannermobile) : ?>
                        <div class="item">
                            <a href="<?php echo $bannermobile['url'] ?>" target="<?php echo $bannermobile['alvo'] ?>">
                                <div class="item_banner_cap_big" style="background-image: url('/sitecontent/banner/<?php echo $bannermobile['arquivo'];?>');">
                                </div>
                            </a>
                        </div>
                    <?php endforeach ?>            
                </div>
        </div>
    <?php endif; ?>
</section>