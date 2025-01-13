<?php include_once("relatorio-control.php"); ?>
<?php

$relatorioClass = new relatorio();

$receitas = $relatorioClass->getAllPurchaseByCurso();

$receitaTotal = 0;
foreach($receitas as $receita){
    $receitaTotal += $receita['receita'];
}
?>

<ol class="breadcrumb">
   <li><a href="?mod=<?php echo $mod; ?>&pag=pedido">Curso</a></li>
   <li>Relatorios</li>
</ol>

<div class="wrapper wrapper-1260">
    <div class="container-relatorios">
    
        <div class="relatorios-wrap">
            <article id="relatorio-vendas">
                <strong style="font-size: 18px;">Relat&oacute;rios de vendas</strong>
        
                <div style="display:flex; align-items:center; gap: 24px; margin-top: 20px;">
                    <div style="margin: 0px;">
                        <i class="fa fa-shopping-cart" style="font-size: 70px; color:#f5bb0e;" aria-hidden="true"></i>
                    </div>
                    <div style="display:flex; flex-direction: column; margin: 0px;">
                        Receita total de todos os cursos:
                        <span style="color: #59b5ea; margin: 0px; font-size: 30px">R$ <?php echo number_format($receitaTotal,2,",","."); ?></span>
                    </div>
                </div>
        
                <div class="relatorios">
                    <hr>
                    <a href="?mod=ecommerce&pag=relatorio&act=vendas-por-dia" style="gap: 16px">
                        <i class="fa fa-file-o" style="color: #59b5ea; margin-right: 16px;"></i>
                        Vendas por dia
                    </a>
                    <hr>
                    <a href="?mod=ecommerce&pag=relatorio&act=vendas-por-curso" style="gap: 16px">
                        <i class="fa fa-file-o" style="color: #59b5ea; margin-right: 16px;"></i>
                        Vendas por curso
                    </a>
                    <hr>
                    <a href="?mod=ecommerce&pag=relatorio&act=vendas-por-produtor" style="gap: 16px">
                        <i class="fa fa-file-o" style="color: #59b5ea; margin-right: 16px;"></i>
                        Vendas por produtor
                    </a>
                </div>
        
            </article>
        </div>

    </div>
</div>


