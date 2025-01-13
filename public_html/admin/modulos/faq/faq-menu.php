
<div class="btn-group">
   <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=faq" <?php echo ($pag=="faq"&&!isset($_GET['act']))?"disabled='disabled'":""; ?> >Áreas de FAQ</a>
   <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=faq&act=add"
   	<?php echo ($pag=="faq"&&isset($_GET['act']))?		($_GET['act']=="add")?"disabled='disabled'" :""	: ""; ?>
   	<?php echo (!Sis::checkPerm($modulo['codigo'].'-2'))?"disabled='disabled'":""; ?> >Nova área de FAQ</a>
</div>
<?php if (isset($_GET['fid'])&&$pag=="faq-item"): ?>
<div class="btn-group">
   <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=faq-item&fid=<?php echo $_GET['fid']; ?>" <?php echo ($pag=="faq-item"&&!isset($_GET['act']))?"disabled='disabled'":""; ?> >Perguntas e respostas</a>
   <a class="btn btn-default" href="?mod=<?php echo $mod; ?>&pag=faq-item&act=add&fid=<?php echo $_GET['fid']; ?>"
   	<?php echo ($pag=="faq-item"&&isset($_GET['act']))? (($_GET['act']=="add")?"disabled='disabled'" : "") : ""; ?>
   	<?php echo (!Sis::checkPerm($modulo['codigo'].'-2'))?"disabled='disabled'":""; ?> >Nova pergunta e resposta</a>
</div>
<?php endif ?>