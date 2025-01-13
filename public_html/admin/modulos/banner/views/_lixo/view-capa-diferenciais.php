<?php 
    $bannersDiferenciais = self::getBanner(10,$pagina=0,$randomize=0,$lista=0,$limite=0,$cycle=1,$cycle_pause=0,$arrayImages=1,$prioridade=0); 
    /* 
       • Banner Nossos diferenciais - (90x120) = Tipo de banner (3)
    */
?> 
<?php if(is_array($bannersDiferenciais) &&count($bannersDiferenciais)>0) :?>
    <section class="s_diferenciais padding_section" >
        <div class="wrapper wrapper-1360">
            <h1 class="title_section text-center">Nossos Diferenciais</h1>
            <div class="items_diferenciais d-flex align-items-center">
                <?php foreach ($bannersDiferenciais as $diferenciais): ?>
                    <?php if($diferenciais['subtipo_banner']==3):?>
                        <div class="box_diferenciais">
                            <figure class="mb-0">
                                <img src="/sitecontent/banner/<?php echo $diferenciais['arquivo'];?>" alt="" class="responsive m-auto">
                                <figcaption>
                                    <?php echo $diferenciais['descricao'];?>
                                </figcaption>
                            </figure>
                        </div>
                    <?php endif; ?>	
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif;?>	

