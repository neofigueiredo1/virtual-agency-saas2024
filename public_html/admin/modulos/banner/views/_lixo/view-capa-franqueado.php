<?php 
$bannersFraqueado = self::getBanner(10,$pagina=0,$randomize=0,$lista=0,$limite=0,$cycle=1,$cycle_pause=0,$arrayImages=1,$prioridade=0); 
/* 
   • Banner Seja Franqueado - (665x478) = Tipo de banner (7)
*/
?>
<?php if(is_array($bannersFraqueado) &&count($bannersFraqueado)>0) :?>
    <?php foreach ($bannersFraqueado as $franqueado): ?>
        <?php if($franqueado['subtipo_banner']==7):?>
            <section class="s_franqueado padding_section bg-gray-dark" >
                <div class="wrapper wrapper-1360">
                    <div class="row d-flex align-items-center">
                        <div class="col-lg-6 order-lg-0 order-1 text-lg-left text-center conteudo_fraqueado">
                            <?php echo $franqueado['descricao'];?>
                            <?php if($franqueado['url']!=""):?>
                                <div class="mt-5">
                                    <a href="<?php echo $franqueado['url'];?>" class="btn btn-216 btn-secondary">Tenho interesse</a>
                                </div>
                            <?php endif;?>
                        </div>
                        <div class="col-lg-6 order-lg-1 order-0">
                            <figure >
                                <img src="/sitecontent/banner/<?php echo $franqueado['arquivo'];?>" alt="" class="responsive m-auto">
                            </figure>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>	
    <?php endforeach; ?>
<?php endif;?>	

