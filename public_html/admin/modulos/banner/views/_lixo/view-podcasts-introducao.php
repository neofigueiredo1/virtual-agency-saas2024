<?php 
global $pagina_data;
$bannersPodcasts = self::getBanner(18,$pagina=0,$randomize=0,$lista=0,$limite=0,$cycle=1,$cycle_pause=0,$arrayImages=1,$prioridade=0); ?>
<section class="s_intro_interna_pages">
    <?php
    if (is_array($bannersPodcasts)&&count($bannersPodcasts)>0):?>
        <?php foreach ($bannersPodcasts as $key => $podcasts):?>
            <?php if($podcasts['subtipo_banner']==1):?>
            <div class="bg_intro_page" style="background-image: url('/sitecontent/banner/<?php echo $podcasts['arquivo']; ?>');">
                <div class="informacoes_section">
                    <h1>
                        <?php echo $pagina_data['titulo'] ?>
                        <?php if (trim($pagina_data['titulo_mae'])!=""): ?>
                            <small><?php echo $pagina_data['titulo_mae']; ?></small>
                        <?php endif ?>
                    </h1>
                </div>
            </div>
                <?php if($pagina_data['conteudo'] != ""):?>
                    <div class="wrapper wrapper-1360 my-5 py-md-5 ">
                        <div class="row justify-content-center">
                            <div class="col-lg-8">
                                <div class="informacoes_section">
                                    <?php echo $pagina_data['conteudo'] ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
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