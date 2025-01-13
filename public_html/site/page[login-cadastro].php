<?php require_once("site/direct-includes/site-header.php"); ?>
<?php require_once("site/direct-includes/site-layout-topo.php"); ?>

	<?php if(isset($_SESSION['plataforma_usuario'])):
		header("location: /minha-conta/cadastro-de-produtos");
		exit();
	endif; ?>
	
	<main>
		<?php $m_cadastro->getView("view-cadastro-login"); ?>
	</main>

<?php require_once("site/direct-includes/site-layout-rodape.php"); ?>
<?php require_once("site/direct-includes/site-footer.php"); ?>