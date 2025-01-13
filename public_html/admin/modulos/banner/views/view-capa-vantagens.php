<?php 
$homeTreina = self::getBanner(1,2,$pagina=0,$randomize=0,$lista=0,$limite=0,$cycle=1,$cycle_pause=0,$arrayImages=1,$prioridade=0); 
?>  

<?php if(is_array($homeTreina) &&count($homeTreina)>0) :?>
<section class="s_vantagens">
    <div class="wrapper wrapper-1340">
        <div class="">
            <h2 class="text-center azul"><?php echo Sis::config('CAPA-SESSAO-VANTAGENS-TITULO'); ?></h2>
        </div>
        <div class="d-flex flex-wrap justify-content-center" >
            
            <?php foreach ($homeTreina as $tbanner): ?>
            <article class="vantagens_itens">
                
                <div class="icone_vantagem"
                    style="background-image:url('/sitecontent/banner/<?php echo $tbanner['arquivo']; ?>');
                    mask: url('/sitecontent/banner/<?php echo $tbanner['arquivo']; ?>') no-repeat center / contain;
                    -webkit-mask: url('/sitecontent/banner/<?php echo $tbanner['arquivo']; ?>') no-repeat center / contain;
                    "
                ></div>
                <div class="vantagens_textos">
                    <h3 class="cinza fs-24 text-center"><?php echo strip_tags($tbanner['descricao']); ?></h3>
                </div>
                <div class="d-flex justify-content-center" >
                    <a href="<?php echo $tbanner['url'] ?>" target="<?php echo $tbanner['alvo'] ?>" class="btn mxw-300 btn-azul-1 d-flex align-items-center justify-content-center" >Saiba mais</a>
                </div>
            </article>
            <?php endforeach; ?>
            
        </div>
    </div>
</section>
<?php endif;?>
