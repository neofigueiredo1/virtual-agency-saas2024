<?php
require_once "../../config.php";

// VERIFICANDO A PERMISSÃO
if (!Sis::checkPerm('10002-2') && !Sis::checkPerm('10002-3'))
{
    exit();
}

$mod = 'banner';
$pag = 'banner';

require_once "banner-model.php";
require_once "banner-control.php";

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

$exe = (isset($_GET['exe'])) ? $_GET['exe'] : 0;
$tbl = (isset($_GET['tbl'])) ? $_GET['tbl'] : 0;

$sqlQuery = new HandleSql;

$tbPrefix   = $sqlQuery->getPrefix();
$TB_BANNER  = $tbPrefix."_banner";

if($exe==1)
{
    $ordem = $_GET["ordem"];
    $aOrdem = explode(",",$ordem);
    $rnk = (count($aOrdem)*10)+10;

    for($i=0;$i<count($aOrdem);$i++)
    {
        $res = $sqlQuery->update("UPDATE ".$TB_BANNER." SET ranking=".round($rnk)." WHERE banner_idx=".round($aOrdem[$i]));
        $rnk -= 10;
    }
    ob_clean();
    echo "ok";
    exit();
}
?>
