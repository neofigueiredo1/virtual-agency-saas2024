<?php 
$homeIntro = self::getBanner(1,1,$pagina=0,$randomize=1,$lista=0,$limite=0,$cycle=1,$cycle_pause=0,$arrayImages=1,$prioridade=0); 
?>  
<?php if(is_array($homeIntro) &&count($homeIntro)>0) :?>
<section class="s_banner_capa_topo owl-carousel owl-theme" >
    <?php foreach ($homeIntro as $intro): ?>
        <?php if($intro['subtipo_banner']==1):

            $aLink_abre = "";
            $aLink_fecha = "";
            if (trim($intro['url'])!=""&&trim($intro['url'])!="#") {
                $aLink_abre = '<a href="'.$intro['url'].'" target="'.$intro['alvo'].'">';
                $aLink_fecha = "</a>";
            }

        ?>
            <div class="banner" style="background-image: url('/sitecontent/banner/<?php echo $intro['arquivo'];?>');" >
            <?php echo $aLink_abre; ?>

            <?php if (false): ?>
                <article class="text-white text-center" >
                    <?php
                    $b_descricao = trim(html_entity_decode(strip_tags($intro['descricao'])));
                    if ($b_descricao!=""): ?>
                    <h1 class="fs-48" >
                        <?php echo $intro['nome'];?>
                        <small><?php echo $intro['descricao'];?></small>
                    </h1>
                    <?php endif ?>
                </article>
            <?php endif ?>

            <?php echo $aLink_fecha; ?>
            </div>
            
        <?php endif;?>	
    <?php endforeach ?>
</section>
<?php endif;?>	
