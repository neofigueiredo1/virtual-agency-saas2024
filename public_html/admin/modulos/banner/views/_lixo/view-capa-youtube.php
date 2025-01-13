<?php 
$bannersYoutube = self::getBanner(10,$pagina=0,$randomize=0,$lista=0,$limite=0,$cycle=1,$cycle_pause=0,$arrayImages=1,$prioridade=0); 
if (is_array($bannersYoutube)&&count($bannersYoutube)>0):?>
<section class="s_youtube text-center">
    <div class="informacoes_section mb-md-5">
        <h1>Confira nosso canal no Youtube</h1>
    </div>
    <div class="wrapper wrapper-1360">
        <div class="row justify-content-center">
            <?php foreach ($bannersYoutube as $key => $youtube):?>
                <?php if($key > 3):?>
                    <?php if($youtube['subtipo_banner']==6):?>
                        <div class="col-md-4 mb-5 pb-md-4">
                            <a href="<?php echo $youtube['video_url'];?>" data-fancybox>
                                <div class="bg-youtube" style="background-image: url('/sitecontent/banner/<?php echo $youtube['arquivo'];?>');">
                                    <figure class="mb-0">
                                        <img src="/assets/images/play.png" alt="">
                                    </figure>
                                </div>
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <a href="<?php echo Sis::config('SOCIAL-LINK-YOUTUBE'); ?>" class="btn btn-primary btn-230" target="_blank">
            conheça o canal
        </a>
    </div>
</section>
<?php endif; ?>