<?php
	// VERIFICANDO A PERMISSÃO ANTES DE EXCLUIR
  	if (!Sis::checkPerm($directIn->MODULO_CODIGO.'-2') && !Sis::checkPerm($directIn->MODULO_CODIGO.'-3'))
  	{
  		Sis::setAlert('<i class="fa fa-warning"></i> Você não tem acesso à este recurso!', 1, '/admin/');
  	}
	$directIn->paginaDelete();
?>