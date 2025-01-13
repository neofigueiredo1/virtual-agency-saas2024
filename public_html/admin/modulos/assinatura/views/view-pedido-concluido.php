<?php 
global $paymentSuccess,$paymentMessage,$token,$pcodigo,$pagina_data,$paymentLink,$paymentMethod,$paymentPixData;

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

<div class="text-center" >
	<?php echo $conteudo; ?>
	<?php if (trim($paymentMethod)=='bank_slip'): ?>
		<script type="text/javascript">window.open('<?php echo $paymentLink; ?>');</script>
		<div class="fs-20 mt-5 azul" >
			Caso a janela da fatura <b>não abra</b> automáticamente, <br/>
			<a href="#" class=" azul" onclick="javascript:window.open('<?php echo $paymentLink; ?>');" ><b>clique aqui para visualizar sua fatura com o boleto!</b></a>
		</div>
	<?php endif ?>
	<?php if (trim($paymentMethod)=='pix'):?>
		<input type="hidden" id="pix_qr_code" name="pix_qr_code" value="<?php echo $paymentPixData->qrcode_text; ?>" />
		<div class="card container p-0 mt-5">
			<div class="card-body p-3 m-0 d-flex  justify-content-center text-left align-items-center fs-20 azul" >
				<div class="pixImage mr-3 mxw-200">
					<img src="<?php echo $paymentPixData->qrcode; ?>" class="m-0 img-fluid" />
				</div>
				<div class="pixCopiaCola mxw-500">
					<h3>PIX Copia e Cola</h3>
					Seu paramento via PIX já está disponível<br/>
					<a href="javascript:;" class="azul"
						onclick="javascript:navigator.clipboard.writeText($('#pix_qr_code').val());$('#pixCopiaOk').stop().slideDown('fast',function(){ setTimeout(function(){ $('#pixCopiaOk').stop().slideUp('fast'); },5000); })"

					><b>Clique aqui para copiar o c&oacute;digo de pagamento.</b></a>
					<div id="pixCopiaOk" style="display:none;" class="alert alert-success w-100 mxw-500 fs-16 mt-2 mx-auto" >C&oacute;digo copiado com sucesso!</div>
				</div>
			</div>
		</div>
	<?php endif ?>
</div>