<?php require_once("site/direct-includes/site-header.php"); ?>
<?php require_once("site/direct-includes/site-layout-topo.php"); ?>
	
	<main>
		
		<?php if (isset($noticiaId)): ?>
			<?php $m_noticia->getView("view-noticia-detalhes"); ?>
		<?php else: ?>
			<?php
				$categoria_nome='';
				if (isset($cat)) {
					$categoria = $m_noticia->getCat((int)$cat);
					$categoria_nome = (is_array($categoria) && count($categoria)>0) ? $categoria[0]['nome'] : '';
				}
			 ?>
			<header class="bg-azul text-center py-5 branco" >
				<h1>
					<?php if (trim($categoria_nome)!=''): ?>
						<?php if (trim($pagina_data['titulo']!="")): ?>
							<small><?php echo $pagina_data['titulo'];?></small><br/>
						<?php endif ?>
						<?php echo $categoria_nome;?>
					<?php else: ?>
						<?php echo $pagina_data['titulo'];?>
					<?php endif ?>
		        </h1>
			</header>
			<?php $m_noticia->getView("view-noticias-lista"); ?>
		<?php endif ?>

	</main>

<?php require_once("site/direct-includes/site-layout-rodape.php"); ?>
<?php require_once("site/direct-includes/site-footer.php"); ?>