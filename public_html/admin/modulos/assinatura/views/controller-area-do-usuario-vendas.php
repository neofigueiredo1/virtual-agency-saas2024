<?php
if (!isset($_SESSION['plataforma_usuario'])) {
    ob_clean();
    header("Location: /login-cadastro");
    exit();
}

// if ((int)$_SESSION['plataforma_usuario']['perfil']!=1) {
//     ob_clean();
//     header("Location: /minha-conta");
//     exit();
// }


global $vendasLista, $pg, $ecomVendas;

$pg = isset($pg) ? ( $pg>0?$pg:1 ) : 1 ;

$ecomVendas = new EcommerceVendas();
$vendasLista = $ecomVendas->getPedidos($_SESSION['filtros_ecommerce_vendas_s'],0,0);