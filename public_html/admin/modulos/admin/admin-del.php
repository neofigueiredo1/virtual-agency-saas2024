<?php
	Auth::checkPermission("2");

	$id = isset($_GET['id']) ? (int)$_GET['id'] : 0 ;
	if ($id !== 0){
		$directIn->_delete();
	}else{
		echo "
	         <div class='alert alert-warning'>
	             <i class='fa fa-exclamation-triangle'></i>&nbsp;&nbsp;
	             Nenhum registro encontrado.
	         </div>
          	";
		sis::redirect("?mod=".$mod."&pag=" . $pag, 2);
	}
?>