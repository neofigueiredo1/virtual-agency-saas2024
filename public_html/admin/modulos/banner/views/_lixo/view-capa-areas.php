<?php
    $BannersAreas = self::getBanner(10,$pagina=0,$randomize=0,$lista=0,$limite=0,$cycle=1,$cycle_pause=0,$arrayImages=1,$prioridade=0); 
    /* 
    • Banner Áreas - (620x720) = Tipo de banner (5)
    */
    $varAreasCorporaisF = Sis::config("AREAS-CORPORAIS-FEMININAS");
    $varAreasCorporaisM = Sis::config("AREAS-CORPORAIS-MASCULINAS");
    $areasCorporaisF = explode(PHP_EOL,$varAreasCorporaisF);
    $areasCorporaisM = explode(PHP_EOL,$varAreasCorporaisM);
?>

<section class="s_areas padding_section" >
	<div class="wrapper wrapper-1160">
		<h1 class="title_section text-center">Conheça as Áreas</h1>
		<article class="desc_section">
            <?php echo Sis::config("DESCRICAO-CAPA-SECAO-CONHECA-AREAS"); ?>
		</article>
		<div class="row box_areas">
            <div class="col-md-12 text-center">
                <ul class="cta_areas first-item-active">
                    <li class="item_nav_tecnica_day" data-target="item-nav-tecnica-0">Feminino</li>
                    <li class="item_nav_tecnica_day" data-target="item-nav-tecnica-1">Masculino</li>
                </ul>
            </div>
            <?php if(is_array($BannersAreas)&&count($BannersAreas)>0):
                $count = 0;
                ?>
                <?php foreach($BannersAreas as $key => $areas):
                $parte = 'front';
                $genero = ($count > 1)? 'man':'woman';
                    ?>
                    <?php if($areas['subtipo_banner']==5):
                        $parte = (($count % 2)==0)? 'front':'back';
                        if($genero=='woman'&&$parte=='front') {$qtdDots = 26; }
                        elseif($genero=='man'&&$parte=='front') {$qtdDots = 20; }
                        else {$qtdDots = 8;}
                        ?>
                        <div class="content_img_bodies <?php echo $genero;?> item-nav-tecnica-<?php echo $count;?>">
                            <div class="info_tecnica_day" style="background-image: url('/sitecontent/banner/<?php echo $areas['arquivo'];?>');">
                                <?php for ($i=0; $i < $qtdDots ; $i++) :?>
                                    <span class="dots <?php echo $genero;?> <?php echo $parte;?> <?php echo "areas".$key;?>"></span>
                                <?php endfor;?>
                            </div>
                        </div>
                        <?php 
                        $count++;
                        endif;
                        ?>
                <?php endforeach;?>
                <div class="col-md-5 ">
                    <div class="box_select_area">
                        <h3>Selecione a área</h3>
                        <div class="item_areas item-nav-tecnica-0">
                            <?php foreach($areasCorporaisF as $key => $area): ?>
                                <div class="form-check" id="areas-fm">
                                    <input class="form-check-input areas" type="radio" name="areas" id="<?php echo Text::friendlyUrl($area)."".$key;?>" value="area-f-<?php echo $key;?>">
                                    <label class="form-check-label" for="<?php echo Text::friendlyUrl($area)."".$key;?>">
                                        <?php echo $area;?>
                                    </label>
                                </div>
                            <?php endforeach;?>
                        </div>
                        <div class="item_areas item-nav-tecnica-1">
                            <?php foreach($areasCorporaisM as $key => $area): ?>
                                <div class="form-check" id="areas-fm">
                                    <input class="form-check-input" type="radio" name="areas" id="<?php echo Text::friendlyUrl($area)."".$key;?>" value="area-m-<?php echo $key;?>">
                                    <label class="form-check-label" for="<?php echo Text::friendlyUrl($area)."".$key;?>">
                                        <?php echo $area;?>
                                    </label>
                                </div>
                            <?php endforeach;?>
                        </div>
                        <div class="text-center mt-md-4 mt-2">
                            <a href="/nossos-servicos/depilacao-a-laser" class="btn btn-256 btn-primary mt-3">Ver todas as áreas <img class="ml-2 svg" src="/assets/images/arrow-right.svg" alt="seta"></a>
                        </div>
                    </div>
                </div>
            <?php endif;?>
		</div>
	</div>
</section>



