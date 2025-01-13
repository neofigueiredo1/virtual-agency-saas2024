<?php 
global $pagina_data, $catId, $idCurso;

$ecomProdCategoria = new EcommerceProdutoCategoria();
$categoria = $ecomProdCategoria->categoriasListAll($catId);
$bannersInterna = self::getBanner(19,$pagina=$pagina_data['codigo'],$randomize=0,$lista=0,$limite=1,$cycle=1,$cycle_pause=0,$arrayImages=1,$prioridade=0); ?>
<section class="s_intro_interna_pages">
    <?php
    if (is_array($bannersInterna)&&count($bannersInterna)>0):?>
        <?php foreach ($bannersInterna as $key => $interna):?>
            <?php if($interna['subtipo_banner']==1):?>
                <div class="bg_intro_page" style="background-image: url('/sitecontent/banner/<?php echo $interna['arquivo']; ?>');">

                    <div class="informacoes_section text-center" >
                        <?php if($catId!=""):?>
                            <h1 class="text-center">
                                <?php echo $categoria[0]['nome']; ?><br>
                                <small><?php echo $pagina_data['titulo'] ?></small>
                            </h1>
                        <?php else:?>
                            <h1>
                                <?php echo $pagina_data['titulo'] ?>
                                <?php if (trim($pagina_data['titulo_mae'])!=""): ?>
                                    <small><?php echo $pagina_data['titulo_mae']; ?></small>
                                <?php endif ?>
                            </h1>
                        <?php endif;?>

                    <?php if (false): ?>
                        <div class="informacoes_section text-center">
                            <h1>
                                <?php echo $pagina_data['titulo'] ?>
                                <?php if (trim($pagina_data['titulo_mae'])!=""): ?>
                                    <small><?php echo $pagina_data['titulo_mae']; ?></small>
                                <?php endif ?>
                            </h1>
                            <?php echo $interna['descricao'];?>
                    <?php endif ?>

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