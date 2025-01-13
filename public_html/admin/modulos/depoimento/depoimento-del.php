<?php
// VERIFICANDO A PERMISSÃO
if (!Sis::checkPerm('10010-2') && !Sis::checkPerm('10010-3'))
{
	Sis::setAlert('Você não tem acesso à este recurso!', 1, '/admin/');
}
//Exclusão de um tipo de banner
if(isset($_GET['gid'])){ $exclui = $directIn->theDelete(); }
