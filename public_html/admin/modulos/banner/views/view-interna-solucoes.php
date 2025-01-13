<?php 
$b_solucoes = self::getBanner(25,1,$pagina=0,$randomize=0,$lista=0,$limite=0,$cycle=1,$cycle_pause=0,$arrayImages=1,$prioridade=0); 
?>  
<?php if(is_array($b_solucoes) &&count($b_solucoes)>0) :?>    
    <?php foreach ($b_solucoes as $key => $b_solucao):
        $class_lv_1 = "";
        $class_lv_2 = "";
        $class_lv_3 = "";
        if ($key % 2){
            $class_lv_1 = " order-md-2 order-1 ";
            $class_lv_2 = " order-md-1 order-2 ";
            $class_lv_3 = " text-right ";
        }
    ?>
        <div class="row justify-content-center solucoes bg-light mx-0 my-3 my-md-5" >
            <div class="col-md-6 p-0 m-0 <?php echo $class_lv_1; ?> thumb_img " style="background-image:url('/sitecontent/banner/<?php echo $b_solucao['arquivo']; ?>');" ></div>

            <div class="col-md-6 p-0 m-0 <?php echo $class_lv_2; ?> ">
                <article class="d-flex align-items-center p-4 p-lg-5">
                    <div class="<?php echo $class_lv_3; ?>" >
                        <h2><?php echo strip_tags($b_solucao['nome']); ?></h2>
                        <?php echo strip_tags($b_solucao['descricao']); ?>
                    </div>
                </article>
            </div>
        </div>
    <?php endforeach; ?>            
<?php endif;?>