<?php
	class admin_model extends HandleSql {

		public $TB_ADMIN;
		public $TB_ADMIN_PERMISSAO;

		function __construct(){
			parent::__construct();
			$this->TB_ADMIN = self::getPrefix() . "_login";
			$this->TB_ADMIN_PERMISSAO = self::getPrefix() . "_login_permissao";
		}

		public function listAllM()
		{
			$res = parent::select("SELECT * FROM " . $this->TB_ADMIN . " WHERE usuario_idx!=" . $_SESSION['usuario']['id'] . " AND nivel >= " . $_SESSION['usuario']['nivel'] . " ORDER BY nome");

			if(is_array($res) && count($res) > 0){
				return $res;
			}else{
				return false;
			}
		}
	}
?>