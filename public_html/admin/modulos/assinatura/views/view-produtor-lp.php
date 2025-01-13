<?php
ob_clean();

global $pagina_uri,$q;

$ecomCadastro = new EcommerceCadastro();

$produtor = $ecomCadastro->getProdutorByLPUrl($q);

$pLP404 = true;
if (is_array($produtor)&&count($produtor)>0) {
	$produtor = $produtor[0];
	if ((int)$produtor['lp_active']==1) {
		$pLP404=false;
	}
}
if (!$pLP404):
?>
	<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="descricao" content="<?php echo $produtor['lp_descricao'] ?>" />
		<title><?php echo $produtor['lp_title'] ?></title>
		<?php echo $produtor['lp_header'] ?>
	</head>
	<body>
		<?php echo $produtor['lp_html_css'] ?>
		<?php echo $produtor['lp_footer'] ?>
	</body>
	</html>
<?php else: ?>
	<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>404</title>
	</head>
	<body>
		Página não encontrada!
	</body>
	</html>
<?php endif ?>
<?php exit(); ?>