<?php $m_ecommerce->getView('controller-valida-certificado'); ?>
<?php require_once("site/direct-includes/site-header.php"); ?>
<?php require_once("site/direct-includes/site-layout-topo.php"); ?>
	
	<main>
		
		<?php $m_banner->getView('view-interna-topo'); ?>

		<header class="bg-azul text-center py-5 branco" >
			<h1>
				<?php if (trim($pagina_data['titulo_mae']!="")): ?>
					<small><?php echo $pagina_data['titulo_mae'];?></small>
				<?php endif ?>
				<?php echo $pagina_data['titulo'];?>
	        </h1>
		</header>

		<!-- Section Conteudo -->
		<section class="s_solucoes py-5" >
			<div class="w-100 mxw-650 m-auto" >
				<?php $m_ecommerce->getView('view-valida-certificado'); ?>
			</div>
		</section>
		<!-- ./Section Conteudo -->	
	</main>

<?php require_once("site/direct-includes/site-layout-rodape.php"); ?>
<?php require_once("site/direct-includes/site-footer.php"); ?>