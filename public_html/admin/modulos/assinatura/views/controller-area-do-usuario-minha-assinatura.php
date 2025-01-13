<?php
if (!isset($_SESSION['plataforma_usuario'])) {
    ob_clean();
    header("Location: /login-cadastro");
    exit();
}

global $pedidos,$pg,$pagina,$pagina_uri,$ecomPedido;

$registrosPorPagina = 20;
$pg_atual = ($pg>0)?$pg:1;

$ecomPedido = new EcommercePedido();
$pedidos = $ecomPedido->getPedidos($registrosPorPagina,$pg_atual);

