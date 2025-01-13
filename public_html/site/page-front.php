<?php require_once("site/direct-includes/site-header.php"); ?>
<?php require_once("site/direct-includes/site-layout-topo.php"); ?>

<main>

	<?php $m_banner->getView('view-capa-intro'); ?>

	<?php $m_banner->getView('view-capa-vantagens'); ?>

	<?php //$m_depoimento->getView("view-depoimentos-capa"); ?>

	<?php $m_noticia->getView("view-ultimas-capa"); ?>


</main>

<?php require_once("site/direct-includes/site-layout-rodape.php"); ?>
<?php require_once("site/direct-includes/site-footer.php"); ?>