<?php 
    $bannersFuncionamento = self::getBanner(10,$pagina=0,$randomize=0,$lista=0,$limite=0,$cycle=1,$cycle_pause=0,$arrayImages=1,$prioridade=0); 
    /* 
       • Banner Horário de Funcionamento - (915x618) = Tipo de banner (4)
    */
?> 
<?php if(is_array($bannersFuncionamento) &&count($bannersFuncionamento)>0) :
    foreach ($bannersFuncionamento as $funcionamento):
        if($funcionamento['subtipo_banner']==4): ?>
            <section class="s_funcionamento" >
                <div class="row">
                    <div class="col-md-6 p-0 order-md-0 order-1">
                        <div class="bg_funcionamento" style="background-image: url('/sitecontent/banner/<?php echo $funcionamento['arquivo'];?>');"></div>
                    </div>
                    <div class="col-md-6 box_funcionamento order-md-1 order-0">
                        <div class="content_funcionamento">
                            <h1 class="title_section">Horário de Funcionamento</h1>
                            <p class="content_p">
                                <?php echo Sis::config("DESCRICAO-CAPA-SECAO-HORARIOS"); ?>
                            </p>
                            <div class="d-md-flex d-block bloco_funcionamento">
                                <?php echo $funcionamento['descricao'];?>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </section>
        <?php endif; ?>	
    <?php endforeach; ?>
<?php endif;?>	