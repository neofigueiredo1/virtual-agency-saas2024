<?php 
    $bannersEspaco = self::getBanner(11,$pagina=0,$randomize=0,$lista=0,$limite=0,$cycle=1,$cycle_pause=0,$arrayImages=1,$prioridade=0); 
    /* 
       • Banners Galeria (778x513) (1)
    */
?>  
<?php if(is_array($bannersEspaco) &&count($bannersEspaco)>0) :?>
    <?php foreach ($bannersEspaco as $video): 
        ?>
        <?php if($video['subtipo_banner']==2):?>
            <section class="s_espaco_video">
                <div class="content_video">
                    <a href="<?php echo $video['video_url'];?>" data-toggle="lightbox">
                        <div class="box_video_espaco img-fluid" style="background-image: url('/sitecontent/banner/<?php echo $video['arquivo'];?>');"></div>
                    </a>
                </div>
            </section>
        <?php endif;?>
    <?php endforeach;?>
<?php endif;?>

