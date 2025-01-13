<?php 
$bannersMiddle = self::getBanner(10,$pagina=0,$randomize=0,$lista=0,$limite=0,$cycle=1,$cycle_pause=0,$arrayImages=1,$prioridade=0); 
if (is_array($bannersMiddle)&&count($bannersMiddle)>0):?>
<section class="s_middle">
    <?php foreach ($bannersMiddle as $key => $middle):?>
        <?php if($middle['subtipo_banner']==4):?>
        <div class="bg_middle" style="background-image: url('/sitecontent/banner/<?php echo $middle['arquivo'];?>');">
            <div class="wrapper wrapper-1360">
                <div class="informacoes_section">
                    <h1><?php echo $middle['nome'];?></h1>
                    <?php if($middle['url']!=""):?>
                        <div>
                            <a href="<?php echo $middle['url'];?>" class="btn btn-primary btn-230 mt-xl-4 mt-2 pt-xl-1" target="<?php echo $middle['alvo'];?>"> Saiba mais </a>
                        </div>
                    <?php endif;?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    <?php endforeach; ?>
</section>
<?php endif; ?>