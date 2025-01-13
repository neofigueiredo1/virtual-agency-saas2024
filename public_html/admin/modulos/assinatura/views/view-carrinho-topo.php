<?php
$ecommerceCart = new EcommerceCarrinho();
$cartNumeroItens = $ecommerceCart->carGetTotalItens();
?>
<a href="/seu-carrinho" class="nav-link">
    <div class="carrinho">
        <i class="fas fa-shopping-basket"></i>
        <span><?php echo str_pad($cartNumeroItens, 1, '0', STR_PAD_LEFT); ?></span>
    </div>
</a>