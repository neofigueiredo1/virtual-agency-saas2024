<?php
	class faq_item_model extends HandleSql{
		protected $TB_FAQ_ITEM;

		public function __construct(){
			parent::__construct();
			$this->TB_FAQ_ITEM = self::getPrefix() . "_faq_item";
		}
	}
?>