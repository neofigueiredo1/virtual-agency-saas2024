<?php 
global $pagina_data;
$homeIntro = self::getBanner(23,1,$pagina_data['codigo'],$randomize=0,$lista=0,$limite=0,$cycle=0,$cycle_pause=0,$arrayImages=1,$prioridade=0); 
?>  
<?php if(is_array($homeIntro) &&count($homeIntro)>0) :?>
<section class="s_banner_interna_topo owl-carousel owl-theme" >
    <?php foreach ($homeIntro as $intro): ?>
        <?php if($intro['subtipo_banner']==1):

            $aLink_abre = "";
            $aLink_fecha = "";
            if (trim($intro['url'])!=""&&trim($intro['url'])!="#") {
                $aLink_abre = '<a href="'.$intro['url'].'" target="'.$intro['alvo'].'">';
                $aLink_fecha = "</a>";
            }

        ?>
            <div class="banner" style="background-image: url('/sitecontent/banner/<?php echo $intro['arquivo'];?>');" ></div>
            
        <?php endif;?>	
    <?php endforeach ?>
</section>
<?php endif;?>	
