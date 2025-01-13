<?php 
$bannersCursos = self::getBanner(10,$pagina=0,$randomize=0,$lista=0,$limite=0,$cycle=1,$cycle_pause=0,$arrayImages=1,$prioridade=0); 
if (is_array($bannersCursos)&&count($bannersCursos)>0):?>
<section class="s_cursos">
    <div class="wrapper-fluid px-0">
        <div class="informacoes_section text-center">
            <?php
                $valoreCursosCapa = Sis::config("DESCRICAO-CAPA-CURSOS");
                $valoresArrayCursosCapa = explode(PHP_EOL,$valoreCursosCapa);
                foreach ($valoresArrayCursosCapa as $key => $textCursoCapa):
                if($key == 0):
            ?>
                <h1><?php echo $textCursoCapa;?></h1>
            <?php else:?>
                <p><?php echo $textCursoCapa; ?></p>
            <?php endif; endforeach;?>
        </div>
        <div class="owl-carousel owl-cursos-home owl-theme">
            <?php foreach ($bannersCursos as $key => $curso):?>
                <?php if($curso['subtipo_banner']==3):?>
                    <div class="item">
                        <a href="<?php echo $curso['url'];?>" target="<?php echo $curso['alvo'];?>">
                            <figure class="item_curso_home">
                                <img class="responsive" src="/sitecontent/banner/<?php echo $curso['arquivo']; ?>" alt="">
                                <figcaption>
                                    <div class="bar-azul"></div>
                                    <article>
                                        <h2><?php echo $curso['nome'];?></h2>
                                        <?php echo $curso['descricao'];?>
                                    </article>
                                </figcaption>
                            </figure>
                        </a>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php foreach ($bannersCursos as $key => $curso):?>
            <?php if($curso['subtipo_banner']==7):?>
                <a href="<?php echo $curso['url'];?>" target="<?php echo $curso['alvo'];?>">
                    <div class="bg-educacao-continuada" style="background-image:url('/sitecontent/banner/<?php echo $curso['arquivo']; ?>');">
                        <div class="informacoes_section py-0">
                            <?php echo $curso['descricao'];?>
                        </div>
                    </div>
                </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <div class="cta_cursos_capa">
            <a href="/cursos" class="btn btn-outline-primary btn-296">Conheça todos os cursos</a>
        </div>
    </div>
</section>
<?php endif; ?>