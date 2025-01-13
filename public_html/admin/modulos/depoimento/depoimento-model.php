<?php
	/**
	*
	*/
	class depoimento_model extends HandleSql
	{
		protected $TB_DEPOIMENTO;

		function __construct()
		{
			parent::__construct();
			$this->TB_DEPOIMENTO = self::getPrefix() . "_depoimento";
		}
	}
?>