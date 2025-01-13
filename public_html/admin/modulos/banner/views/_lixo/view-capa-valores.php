<?php 
$bannersValores = self::getBanner(10,$pagina=0,$randomize=0,$lista=0,$limite=0,$cycle=1,$cycle_pause=0,$arrayImages=1,$prioridade=0); 
if (is_array($bannersValores)&&count($bannersValores)>0):?>
<section class="s_valores">
    <div class="wrapper wrapper-1360">
        <div class="row">
            <?php foreach ($bannersValores as $key => $valores):?>
                <?php if($valores['subtipo_banner']==5):?>
                    <div class="col-md-4 item-valor mb-4">
                        <figure class="responsive">
                            <img src="/sitecontent/banner/<?php echo $valores['arquivo'];?>" alt="">
                            <figcaption>
                                <img src="/assets/images/<?php echo Text::friendlyUrl($valores['nome']);?>.svg" alt="" class="svg">
                                <h2><?php echo $valores['nome'];?></h2>
                            </figcaption>
                        </figure>
                        <div class="content-valor">
                            <?php echo $valores['descricao']?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>