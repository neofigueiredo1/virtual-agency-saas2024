<?php global $pagina, $pagina_data, $m_conteudo;?>
<section class="s_breadcrumb">
    <div class="wrapper wrapper-1360 d-flex justify-content-between align-items-center">
        <?php if($pagina == '404') :?>
            <h1><?php echo '404' ?></h1>
        <?php else: ?>
            <h1><?php echo $pagina_data["titulo"]; ?></h1>
        <?php endif;?>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/" title='Ir para o início do site' class="link_inicio" >Home</a></li>
                <?php if($pagina == '404') :?>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo '404' ?></li>
                <?php else: ?>
                    <?php if($pagina_data['titulo_mae']!=""){ echo("<li class='breadcrumb-item'> <a href='/" . Text::toAscii($pagina_data['titulo_mae']) . "' class='titulo_mae' >".$pagina_data['titulo_mae']."</a> &nbsp; <i class='fas fa-chevron-right fs-14'></i> </li>"); } ?>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo $pagina_data["titulo"]; ?></li>
                <?php endif;?>
            </ol>
        </nav>
    </div>
</section>