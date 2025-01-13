<?php
	class banner_tipo_model extends HandleSql {

		public $TB_BANNER      	= "_banner";
		public $TB_BANNER_TIPO 	= "_banner_tipo";
		
		public function __construct() {
			parent::__construct();
			$this->TB_BANNER = self::getPrefix() . "_banner";
			$this->TB_BANNER_TIPO = self::getPrefix() . "_banner_tipo";
		}

	}
?>