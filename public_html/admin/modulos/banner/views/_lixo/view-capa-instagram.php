<?php global $pagina_data;
$bannersInsta = self::getBanner(10,$pagina=0,$randomize=0,$lista=0,$limite=0,$cycle=1,$cycle_pause=0,$arrayImages=1,$prioridade=0); 
/* 
   • Banner Instagram - (680x454) = Tipo de banner (6)
*/
?>
<?php if(is_array($bannersInsta) &&count($bannersInsta)>0) :?>
    <section class="s_instagram padding_section <?php echo ($pagina_data['codigo']=="4")? '':'bg-gray';?>" >
        <div class="wrapper wrapper-1360">
            <div class="box_instagram">
                <div class="items_instagram">
                    <h1 class="title_section"> <i class="fab fa-instagram pr-3"></i>  A @YesLaser está no Instagram!</h1>
                    <div class="text-lg-right text-left">
                        <a href="<?php echo Sis::config("SOCIAL-LINK-INSTAGRAM"); ?>" class="btn btn-216 btn-outline-dark ml-lg-0 ml-md-3 ml-0 mt-md-0 mt-3">Seguir</a>
                    </div>
                </div>
                <div class="row px-3">
                    <?php foreach ($bannersInsta as $insta): ?>
                        <?php if($insta['subtipo_banner']==6):?>
                            <div class="col-md-6 px-0">
                                <figure>
                                    <img src="/sitecontent/banner/<?php echo $insta['arquivo'];?>" alt="" class="responsive m-auto">
                                </figure>
                            </div>
                        <?php endif; ?>	
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
<?php endif;?>	
