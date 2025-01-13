<?php 
global $paymentSuccess,$paymentMessage,$token,$pcodigo,$pagina_data;

$conteudo = $pagina_data['conteudo'];
$conteudo = str_replace("{pedido_codigo}",$pcodigo,$conteudo);

?>

<?php if (!$paymentSuccess):
if (strpos($paymentMessage,"Warning")!==false) {
	$titulo_retorno = "Aviso!";
	$titulo_retorno_class = "warning";
}else{
	$titulo_retorno = "A transação Falhou!";
	$titulo_retorno_class = "danger";
}
?>
<div class="text-center">
	<h3 class="text-<?php echo $titulo_retorno_class; ?>" ><?php echo $titulo_retorno; ?></h3>
	<small><?php echo $paymentMessage; ?></small>
</div>
<br /><br />
<?php endif ?>

<div class="text-center">
	<?php echo $conteudo; ?>
</div>