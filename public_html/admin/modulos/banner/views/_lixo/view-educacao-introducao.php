<?php 
global $pagina_data;
$bannersdEducacao = self::getBanner(16,$pagina=0,$randomize=0,$lista=0,$limite=0,$cycle=1,$cycle_pause=0,$arrayImages=1,$prioridade=0); ?>
<section class="s_intro_interna_pages">
    <?php
    if (is_array($bannersdEducacao)&&count($bannersdEducacao)>0):?>
        <?php foreach ($bannersdEducacao as $key => $dEducacao):?>
            <?php if($dEducacao['subtipo_banner']==1):?>
            <div class="bg_intro_page" style="background-image: url('/sitecontent/banner/<?php echo $dEducacao['arquivo']; ?>');">
                <div class="informacoes_section">
                    <h1>
                        <?php echo $pagina_data['titulo'] ?>
                        <?php if (trim($pagina_data['titulo_mae'])!=""): ?>
                            <small><?php echo $pagina_data['titulo_mae']; ?></small>
                        <?php endif ?>
                    </h1>
                </div>
            </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php else:?>
        <div class="title-interna mb-4">
            <h1>
                <?php echo $pagina_data['titulo'] ?>
                <?php if (trim($pagina_data['titulo_mae'])!=""): ?>
                    <small><?php echo $pagina_data['titulo_mae']; ?></small>
                <?php endif ?>
            </h1>
        </div>
    <?php endif; ?>
</section>