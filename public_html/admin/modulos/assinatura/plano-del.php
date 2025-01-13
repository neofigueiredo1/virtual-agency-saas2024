<?php
	if(!Sis::checkPerm($modulo['codigo'].'-2') && !Sis::checkPerm($modulo['codigo'].'-1')){
		Sis::setAlert('Você não tem acesso à este recurso!', 1, '/admin/');
	}
	$directIn->theDelete(); 
?>