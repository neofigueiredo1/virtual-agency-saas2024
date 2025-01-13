
<div class="btn-group">
   <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=produto" <?php echo ($pag=="produto"&&!isset($_GET['act']))?"disabled='disabled'":""; ?> >Produtos</a>
   <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=produto&act=add"
   	<?php echo ($pag=="produto"&&isset($_GET['act']))?		($_GET['act']=="add")?"disabled='disabled'" :""	: ""; ?>
   	<?php echo (!Sis::checkPerm($modulo['codigo'].'-2') && !Sis::checkPerm($modulo['codigo'].'-3'))?"disabled='disabled'":""; ?> >Novo produto</a>
</div>
<div class="btn-group">

   <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=departamento"
		<?php echo ($pag=="departamento"&&!isset($_GET['act']))?"disabled='disabled'":""; ?>
		<?php echo (!Sis::checkPerm($modulo['codigo'].'-2') && !Sis::checkPerm($modulo['codigo'].'-4'))?"disabled='disabled'":""; ?>
	>Departamentos</a>

	<a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=departamento&act=add"
		<?php echo ($pag=="departamento"&&isset($_GET['act']))?		($_GET['act']=="add")?"disabled='disabled'" :""	: ""; ?>
		<?php echo (!Sis::checkPerm($modulo['codigo'].'-2') && !Sis::checkPerm($modulo['codigo'].'-4'))?"disabled='disabled'":""; ?>
	>Novo departamento</a>


   <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=categoria"
   	<?php echo ($pag=="categoria"&&!isset($_GET['act']))?"disabled='disabled'":""; ?>
   	<?php echo (!Sis::checkPerm($modulo['codigo'].'-2') && !Sis::checkPerm($modulo['codigo'].'-4'))?"disabled='disabled'":""; ?>
   >Categorias</a>
   <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=categoria&act=add"
		<?php echo ($pag=="categoria"&&isset($_GET['act']))?		($_GET['act']=="add")?"disabled='disabled'" :""	: ""; ?>
   	<?php echo (!Sis::checkPerm($modulo['codigo'].'-2') && !Sis::checkPerm($modulo['codigo'].'-4'))?"disabled='disabled'":""; ?>>Nova categoria</a>
   <?php if (isset($cpid) && isset($_GET['act']) ): ?>
	   <?php if ($cpid!=0 && strpos($_GET['act'],"subcategoria")!==false): ?>
			<a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=categoria&act=subcategoria-list&cpid=<?php echo $cpid; ?>" <?php echo ($pag=="categoria"&&$_GET['act']=="subcategoria-list")?"disabled='disabled'":""; ?> >Subcategorias</a>
			<a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=categoria&act=subcategoria-add&cpid=<?php echo $cpid; ?>" <?php echo ($pag=="categoria"&&$_GET['act']=="subcategoria-add")?"disabled='disabled'":""; ?> >Nova subcateogira</a>
	   <?php endif ?>
   <?php endif ?>

</div>


<div class="btn-group">
	<a class="btn btn-default a_tooltip" href="?mod=<?php echo $mod; ?>&pag=pvdc" title='Usados nos produtos e nas variações de produtos' <?php echo ($pag=="pvdc"&&!isset($_GET['act']))?"disabled='disabled'":""; ?> >Dados complementares</a>
	<a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=pvdc&act=add"
		<?php echo ($pag=="pvdc"&&isset($_GET['act']))?		($_GET['act']=="add")?"disabled='disabled'" :""	: ""; ?>
   	<?php echo (!Sis::checkPerm($modulo['codigo'].'-2') && !Sis::checkPerm($modulo['codigo'].'-3'))?"disabled='disabled'":""; ?>
	>Novo dado complementar</a>
	<?php if (isset($did) && isset($_GET['act']) ): ?>
	   <?php if ($did!=0 && strpos($_GET['act'],"valor")!==false): ?>
			<a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=pvdc&act=valor-list&did=<?php echo $did; ?>" <?php echo ($pag=="pvdc"&&$_GET['act']=="valor-list")?"disabled='disabled'":""; ?> >Valores</a>
			<a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=pvdc&act=valor-add&did=<?php echo $did; ?>" <?php echo ($pag=="pvdc"&&$_GET['act']=="valor-add")?"disabled='disabled'":""; ?> >Novo valor</a>
	   <?php endif ?>
   <?php endif ?>
</div>

<div class="btn-group">
	<a class="btn btn-success" href="?mod=<?php echo $mod; ?>&pag=produto&act=urlRewrite" title='Atualizar os urls amigáveis dos produtos'  >Atualizar os urls amigáveis dos produtos</a>
</div>
