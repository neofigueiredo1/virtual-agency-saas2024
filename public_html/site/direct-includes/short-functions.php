<?php

	function galeria1() {
		$m_curriculo = new curriculo_views;
		$m_curriculo->getView('curriculo-form');

		//return $m_curriculo->teste();

		//return curriculo_views::teste();
		//return '<img src="/site/public/images/encontre-lojas.png">';

	}

	function galeria2() {
		return 'Função galeria 2';
	}
	
?>