<?php $m_curso->getView('controller-curso-detalhes'); ?>
<?php require_once("site/direct-includes/site-header.php"); ?>
<?php require_once("site/direct-includes/site-layout-topo.php"); ?>
	
	<main>
		
		<!-- Section Conteudo -->
		<section class="s_cursos_detalhes py-5">
			<div class="wrapper wrapper-1340">
				
				<?php $m_curso->getView('view-curso-detalhes'); ?>

			</div>
		</section>
		<!-- ./Section Conteudo -->	
	</main>

<?php require_once("site/direct-includes/site-layout-rodape.php"); ?>
<?php require_once("site/direct-includes/site-footer.php"); ?>