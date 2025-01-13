<?php
require_once "../../config.php";

// VERIFICANDO A PERMISSÃO
if (!Sis::checkPerm('10006-2') && !Sis::checkPerm('10006-3'))
{
    exit();
}

$mod = 'ecommerce';
$pag = 'fabricante';

// require_once "fabricante-model.php";
// require_once "fabricante-control.php";

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

$exe = (isset($_GET['exe'])) ? $_GET['exe'] : 0;
$tbl = (isset($_GET['tbl'])) ? $_GET['tbl'] : 0;
$imagem_idx = (isset($_GET['id'])) ? $_GET['id'] : 0;


$tbPrefix = Connect::getPrefix();
$TB_FABRICANTES     = $tbPrefix."_ecommerce_fabricante";
$TB_PRODUTO     = $tbPrefix."_ecommerce_produto";
$TB_CATEGORIA     = $tbPrefix."_ecommerce_produto_categoria";
$TB_PRODUTO_IMAGEM     = $tbPrefix."_ecommerce_produto_imagem";
$TB_SUB_CATEGORIA_IMAGEM     = $tbPrefix."_ecommerce_produto_subcategoria_imagem";

$TB_COLABORADOR         = $tbPrefix."_ecommerce_colaborador";
$TB_COLABORADOR_GRUPO 	= $tbPrefix."_ecommerce_colaborador_grupo";


//ORDENAÇÃO DOS FABRICANTES ATRAVÉS DO SORTABLE!
if($exe==1)
{
    if($tbl == 4){
        $tbl = $TB_SUB_CATEGORIA_IMAGEM;
        $name_camp = "ranking";
        $whereCause = "imagem_idx";
    }else if($tbl == 5){
        $tbl = $TB_PRODUTO_IMAGEM;
        $name_camp = "ranking";
        $whereCause = "produto_imagem_idx";
    }else if($tbl == 6){
        $tbl = $TB_COLABORADOR;
        $name_camp = "ranking";
        $whereCause = "colaborador_idx";
    }else if($tbl == 7){
        $tbl = $TB_COLABORADOR_GRUPO;
        $name_camp = "ranking";
        $whereCause = "grupo_idx";
    }else{
        $tbl = $TB_FABRICANTES;
        $name_camp = "posicao";
        $whereCause = "fabricante_idx";
    }

    $sqlQuery = new HandleSql;

    $ordem = $_GET["ordem"];
    $aOrdem = explode(",",$ordem);
    $rnk = (count($aOrdem)*10)+10;

    for($i=0;$i<count($aOrdem);$i++)
    {
        $res = $sqlQuery->update("UPDATE ".$tbl." SET ".$name_camp."=".round($rnk)." WHERE ".$whereCause."=".round($aOrdem[$i]));
        $rnk -= 10;
    }
    ob_clean();
    echo "ok";
    exit();
}

//Exclui a imagem.
if($exe==2)
{
    $sqlQuery = new HandleSql;

    if($tbl == 4){
        $tbl = $TB_SUB_CATEGORIA_IMAGEM;
        $whereCause = "imagem_idx";
        $ModuloDir = "categoria";
    }else if($tbl == 5){
        $tbl = $TB_PRODUTO_IMAGEM;
        $whereCause = "produto_imagem_idx";
        $ModuloDir = "produtos";
    }

    $res = $sqlQuery->select("SELECT imagem FROM ".$tbl." WHERE ".$whereCause."=".round($imagem_idx));

    if(isset($res) && $res!=false ){

        $arquivo_midia = $res[0]['imagem'];

        //Lista as imagens enviadas
        //Verifica as pastas do sistema
        $BaseDir = realpath("..\\..\\..\\");
        $SisDir = constant("PASTA_CONTENT");
        $LocalDir = $BaseDir."\\".$SisDir."\\ecommerce";

        //Pasta do módulo
        $LocalModuloDir = $LocalDir."\\".$ModuloDir;

        //Pasta de imagens do módulo
        $ModuloImgDir = "images";
        $LocalModuloImgDir = $LocalModuloDir."\\".$ModuloImgDir;

        if(file_exists($LocalModuloImgDir."\\".$arquivo_midia)){  unlink($LocalModuloImgDir."\\".$arquivo_midia); }
        if(file_exists($LocalModuloImgDir."\\p\\".$arquivo_midia)){  unlink($LocalModuloImgDir."\\p\\".$arquivo_midia); }
        if(file_exists($LocalModuloImgDir."\\m\\".$arquivo_midia)){ unlink($LocalModuloImgDir."\\m\\".$arquivo_midia); }
        if(file_exists($LocalModuloImgDir."\\m\\gb_".$arquivo_midia)){ unlink($LocalModuloImgDir."\\m\\gb_".$arquivo_midia); }
        if(file_exists($LocalModuloImgDir."\\g\\".$arquivo_midia)){ unlink($LocalModuloImgDir."\\g\\".$arquivo_midia); }

        $res = $sqlQuery->delete("DELETE FROM ".$tbl." WHERE ".$whereCause."=".round($imagem_idx));

    }

    ob_clean();
    echo "ok";
    exit();
}

/*Retorna as informações do vídeo*/
if($exe==3)
 {
    $url = (isset($_GET['url'])) ? $_GET['url'] : "";
    if(trim($url)==""){
        ob_clean();
        echo "nodata";
        exit();
    }
    $video_source = Sis::getVideoIframe($url);
    $video_image = Sis::getVideoThumb($url);
    ob_clean();
    echo $video_image."@@".$video_source;
    exit();
 }

//Ordenação específica para os produtos em destaque no site.
if($exe==33)
{
    $sqlQuery = new HandleSql;

    $ordem = $_GET["ordem"];
    $aOrdem = explode(",",$ordem);
    $rnk = (count($aOrdem)*10)+10;

    for($i=0;$i<count($aOrdem);$i++)
    {
        $res = $sqlQuery->update("UPDATE ".$TB_PRODUTO." SET destaque=".round($rnk)." WHERE produto_idx=".round($aOrdem[$i]));
        $rnk -= 10;
    }
    ob_clean();
    echo "ok";
    exit();
}

if($exe==4)
{
    $sqlQuery = new HandleSql;
    $ordem = $_GET["ordem"];
    $aOrdem = explode(",",$ordem);
    $rnk = (count($aOrdem)*10)+10;
    for($i=0;$i<count($aOrdem);$i++)
    {
        $res = $sqlQuery->update("UPDATE ".$TB_PRODUTO." SET ranking=".round($rnk)." WHERE produto_idx=".round($aOrdem[$i]));
        $rnk -= 10;
    }
    ob_clean();
    echo "ok";
    exit();
}

if($exe==5)
{
    $sqlQuery = new HandleSql;
    $ordem = $_GET["ordem"];
    $aOrdem = explode(",",$ordem);
    $rnk = (count($aOrdem)*10)+10;
    for($i=0;$i<count($aOrdem);$i++)
    {
        $res = $sqlQuery->update("UPDATE ".$TB_CATEGORIA." SET ranking=".round($rnk)." WHERE categoria_idx=".round($aOrdem[$i]));
        $rnk -= 10;
    }
    ob_clean();
    echo "ok";
    exit();
}
 
?>
