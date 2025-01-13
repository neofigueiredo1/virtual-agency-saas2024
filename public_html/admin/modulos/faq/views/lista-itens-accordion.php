<?php $fGrupos = self::getFaqGrupos(); ?>
<?php if (is_array($fGrupos)&&count($fGrupos)>0): ?>

<div id="faq-accordion" >
  <?php foreach ($fGrupos as $key => $fGrupo):
    $fItens = self::getFaqItens($fGrupo['faq_idx']);
  ?>
  <div class="item-h subtitulo-t5 mt-semibold text-uppercase azul" ><p><?php echo $fGrupo['nome']; ?></p></div>
  <div class="itens" >
    <?php if (is_array($fItens)&&count($fItens)>0): ?>
      <?php foreach ($fItens as $key => $fItem): ?>
      <div class="item">
        <div class="item-p mt-bold" ><?php echo $fItem['pergunta'] ?></div>
        <div class="item-r montserrat-medium" ><?php echo $fItem['resposta'] ?></div>
      </div>
      <?php endforeach ?>
    <?php endif ?>
  </div>
  <?php endforeach ?>
</div>

<?php endif ?>