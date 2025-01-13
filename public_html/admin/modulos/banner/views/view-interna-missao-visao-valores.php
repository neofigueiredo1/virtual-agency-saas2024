<?php 
$bMisVisVal = self::getBanner(24,2,$pagina=0,$randomize=0,$lista=0,$limite=0,$cycle=0,$cycle_pause=0,$arrayImages=1,$prioridade=0); 
?>  
<?php if(is_array($bMisVisVal) &&count($bMisVisVal)>0) :?>
<section class="s_box_missao bg-light py-5" >

    <div class="wrapper wrapper-1340 d-flex flex-wrap align-items-stretch justify-content-center " >

    <?php foreach ($bMisVisVal as $banner): ?>

        <article class="box_missao bg-white p-3 p-md-4  m-md-3 my-2 mx-0 shadow" >
            
            <figure class="">
                <img src="/sitecontent/banner/<?php echo $banner['arquivo'];?>" alt="">
                <figcaption class="">
                    <h3 class="azul-1 text-center" ><?php echo $banner['nome'];?></h3>
                    <p><?php echo strip_tags($banner['descricao']);?></p>
                </figcaption>
            </figure>
            
        </article>
            
        
    <?php endforeach ?>

    </div>
</section>
<?php endif;?>	
