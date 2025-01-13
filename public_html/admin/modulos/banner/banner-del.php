<?php
// VERIFICANDO A PERMISSÃO
if (!Sis::checkPerm('10002-2') && !Sis::checkPerm('10002-3'))
{
	Sis::setAlert('Você não tem acesso à este recurso!', 1, '/admin/');
}
//Exclusão de um banner
if (isset($_GET['bid'])) $exclui = $directIn->theDelete();
