<?php
require_once "../../config.php";

// VERIFICANDO A PERMISSÃO
if (!Sis::checkPerm('10034-2'))
{
    exit();
}

$mod = 'faq';
$pag = 'faq';

require_once "faq-model.php";
require_once "faq-control.php";

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

$exe = (isset($_GET['exe'])) ? $_GET['exe'] : 0;
$tbl = (isset($_GET['tbl'])) ? $_GET['tbl'] : 0;

$tbPrefix   = Connect::DB_PREFIX;
switch ($tbl) {
    case 1: //faq
        $TB_NOME  = $tbPrefix."_faq";
        $TB_CAMPO  = "faq_idx";
        break;
    case 2: //faq-item
        $TB_NOME  = $tbPrefix."_faq_item";
        $TB_CAMPO  = "item_idx";
        break;
}

if($exe==1)
{
    $sqlQuery = new HandleSql;
    $ordem = $_GET["ordem"];
    $aOrdem = explode(",",$ordem);
    $rnk = (count($aOrdem)*10)+10;
    for($i=0;$i<count($aOrdem);$i++)
    {
        $res = $sqlQuery->update("UPDATE ".$TB_NOME." SET ranking=".round($rnk)." WHERE ".$TB_CAMPO."=".round($aOrdem[$i]));
        $rnk -= 10;
    }
    ob_clean();
    echo "ok";
    exit();
}
?>
