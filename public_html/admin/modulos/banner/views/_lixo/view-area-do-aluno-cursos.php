<?php 
global $pagina_data;
$bannersCursos = self::getBanner(21,$pagina=0,$randomize=0,$lista=0,$limite=0,$cycle=1,$cycle_pause=0,$arrayImages=1,$prioridade=0); ?>
<section class="informacoes_section d-block d-md-flex flex-wrap justify-content-center" >
    <?php
    if (is_array($bannersCursos)&&count($bannersCursos)>0):?>
        <?php foreach ($bannersCursos as $key => $curso):?>
            <?php if($curso['subtipo_banner']==1):?>
            <article class="card p-3" style="margin:5px;max-width:350px;font-size:14px;" >
                <figure class="responsive" ><img src="/sitecontent/banner/<?php echo $curso['arquivo']; ?>" ></figure>
                <div>
                    <h4><?php echo $curso['nome'] ?></h4>
                    <div class="content">
                        <?php echo $curso['descricao']; ?>
                    </div>
                </div>
            </article>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</section>