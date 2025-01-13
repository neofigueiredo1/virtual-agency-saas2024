<?php
// VERIFICANDO A PERMISSÃO
if (!Sis::checkPerm('10002-2') && !Sis::checkPerm('10002-4'))
{
	Sis::setAlert('Você não tem acesso à este recurso!', 1, '/admin/');
}
//Exclusão de um tipo de banner
if(isset($_GET['tid'])){ $exclui = $directIn->tipoDelete(); }
