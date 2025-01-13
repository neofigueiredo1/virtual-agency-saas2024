<?php

global $paymentSuccess,$paymentMessage,$token,$pcodigo;

$paymentSuccess = false;
$paymentMessage = "";

$pcodigo = isset( $_GET[ 'pcodigo' ] ) ? (int)Text::clean($_GET[ 'pcodigo' ]) : "";

$pcodigo = str_pad($pcodigo, 9, "0", STR_PAD_LEFT); //codigo 99 => codigo_pedido 000000099