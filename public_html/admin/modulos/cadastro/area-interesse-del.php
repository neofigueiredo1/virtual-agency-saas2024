<?php
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0 ;

if ($id !== 0){
    $directIn->theDelete();
}else{
    echo "Nenhum registro encontrado";
    sis::redirecionar("?mod=" . $mod . "&pag=" . $pag,2);
}
?>