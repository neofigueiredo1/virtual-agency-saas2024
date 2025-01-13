<?php $m_curso->getView('controller-curso-lista'); ?>

<?php require_once("site/direct-includes/site-header.php"); ?>
<?php require_once("site/direct-includes/site-layout-topo.php"); ?>
	
	<main>
		
		<?php $m_banner->getView('view-interna-topo'); ?>
		<?php global $categoriaNome; ?>
		<header class="bg-azul text-center py-5 branco" >
			<h1>
				<?php if (trim($categoriaNome)!=''): ?>

					<small><?php echo $pagina_data['titulo'];?></small><br/>
					<?php echo $categoriaNome;?>

				<?php else: ?>
					
					<?php if (trim($pagina_data['titulo_mae']!="")): ?>
						<small><?php echo $pagina_data['titulo_mae'];?></small>
					<?php endif ?>
					<?php echo $pagina_data['titulo'];?>

				<?php endif ?>
	        </h1>
		</header>

		<!-- Section Conteudo -->
		<section class="s_cursos pb-5 pt-2" >
			<div class="wrapper wrapper-1340">
				
				<?php $m_curso->getView('view-curso-lista'); ?>

			</div>
		</section>
		<!-- ./Section Conteudo -->	
	</main>

<?php require_once("site/direct-includes/site-layout-rodape.php"); ?>
<?php require_once("site/direct-includes/site-footer.php"); ?>