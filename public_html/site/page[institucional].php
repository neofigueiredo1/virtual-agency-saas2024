<?php require_once("site/direct-includes/site-header.php"); ?>
<?php require_once("site/direct-includes/site-layout-topo.php"); ?>
	
	<main>
		
		<?php $m_banner->getView('view-interna-topo'); ?>

		<!-- Section Conteudo -->
		<section class="s_conteudo_padrao" >
			<div class="wrapper wrapper-1340 py-5 my-lg-5 my-md-4">

				<div class="row">
					<div class="col-md-6">
						<?php echo Sis::desvar($pagina_data['conteudo']); ?>
					</div>
					<div class="col-md-6">
						
						<?php
						$insttucionalB = $m_banner->getBanner(24,1,$pagina=0,$randomize=0,$lista=0,$limite=0,$cycle=0,$cycle_pause=0,$arrayImages=1,$prioridade=0); 
						if(is_array($insttucionalB) &&count($insttucionalB)>0) :
							$insttucionalBanner = $insttucionalB[0];
						?>
							<figure>
								<img class="img-fluid" src="/sitecontent/banner/<?php echo $insttucionalBanner['arquivo'];?>" alt="" />
							</figure>
						<?php endif; ?>
						
					</div>
				</div>

			</div>
		</section>
		<!-- ./Section Conteudo -->	

		<?php $m_banner->getView('view-interna-missao-visao-valores'); ?>

		<?php require_once("site/direct-includes/site-contato-form-hospede.php"); ?>

		<?php
		$paginaPoliticaPrivacidade = $m_conteudo->get_page(25);
		if ($paginaPoliticaPrivacidade['codigo']!='404'):
		?>
		<!-- Section Conteudo -->
		<section class="s_conteudo_padrao" >
			<div class="wrapper wrapper-1340 py-5 my-lg-5 my-md-4">
				<h2 class="azul-1 text-center mb-3" ><?php echo $paginaPoliticaPrivacidade['titulo'] ?></h2>
				<div>
					<?php echo Sis::desvar($paginaPoliticaPrivacidade['conteudo']); ?>
				</div>
			</div>
		</section>
		<!-- ./Section Conteudo -->
		<?php endif ?>

		<?php
		$paginaFaleConosco = $m_conteudo->get_page(8);
		if ($paginaFaleConosco['codigo']!='404'):
		?>
		<!-- Sessao - Fale Conosco -->
		<section class="p-md-4 p-3 mb-lg-5 mb-3" >
			<div class="wrapper wrapper-1340 shadow rounded-40 p-md-5 py-5 " >
				<div class="mb-md-5 mb-3 mxw-500" >
					<h2 class="azul-1 mb-4" ><?php echo $paginaFaleConosco['titulo'] ?></h2>
					<?php echo Sis::desvar($paginaFaleConosco['conteudo']); ?>
				</div>
				<?php require_once("site/direct-includes/site-contato-form.php"); ?>
			</div>
		</section>
		<?php endif ?>


	</main>

<?php require_once("site/direct-includes/site-layout-rodape.php"); ?>
<?php require_once("site/direct-includes/site-footer.php"); ?>

