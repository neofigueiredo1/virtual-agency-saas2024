<?php
	// VERIFICANDO A PERMISSÃO
  	if (!Sis::checkPerm($directIn->MODULO_CODIGO.'-2') && !Sis::checkPerm($directIn->MODULO_CODIGO.'-4'))
  	{
  		Sis::setAlert('<i class="fa fa-warning"></i> Você não tem acesso à este recurso!', 1, '/admin/');
  	}

	if ($_GET['menu_id'] !== ""){
		$directIn->theDelete();
	}
?>