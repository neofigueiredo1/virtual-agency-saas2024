<?php

	class config_model extends HandleSql {

		protected $TB_CONFIG;
		protected $TB_LOG;
		protected $TB_USERS;
		protected $TB_MODULO;

		function __construct(){
			parent::__construct();
			$this->TB_CONFIG = self::getPrefix() . "_config";
			$this->TB_LOG = self::getPrefix() . "_log";
			$this->TB_USERS = self::getPrefix() . "_login";
			$this->TB_MODULO = self::getPrefix() . "_modulo";
			
		}

		public function sisConfigInsertM($dadosNome, $dados){
			$countData = count($dadosNome);
			for ($i=0; $i < $countData; $i++) {
					if(is_string($dadosNome[$i]) && is_string(Text::clean($dados[$i])))
					{
						$res = parent::insert("INSERT INTO ". $this->dbPrefix . $this->TB_CONFIG ." (nome, valor, status, nivel)
												SELECT * FROM (SELECT '" . htmlspecialchars(Text::clean($dadosNome[$i])). "', '" . htmlspecialchars(Text::clean($dados[$i])) . "', 1, 0) AS tmp
												WHERE NOT EXISTS (
												    SELECT nome FROM " .  $this->dbPrefix . $this->TB_CONFIG . " WHERE nome = '" . htmlspecialchars(Text::clean($dadosNome[$i])). "'
												) LIMIT 1;");
					}
			}
			if($res == true){
				Sis::insertLog(0, $this->MODULO_AREA. " do sistema", 'INSERT', 0, "", "");
				return true;
			} else {
				return false;
			}

		}

		public function sisConfigUpdateM($dadosNome, $dados){
			
			$countData = count($dadosNome);
			for ($i=0; $i < $countData; $i++) {
				$res = parent::update("UPDATE " .  $this->dbPrefix . $this->TB_CONFIG . " SET valor = '" . htmlspecialchars($dados[$i]) . "' WHERE nome = '" . htmlspecialchars($dadosNome[$i]) . "'");
			}


			if($res == true){
				Sis::insertLog(0, $this->MODULO_AREA. " do sistema", 'UPDATE', 0, "", "");
				return true;
			} else {
				return false;
			}
		}

		public function listAllLogM($matrix, $table, $page=0, $pageNumRows){

	      $sqlOrderBy = "";
	      $sqlWhere 	= "";
	      $and 			= "";

	      if(is_array($matrix) && count($matrix) > 1){
	         $sqlWhere = (count($matrix) <= 1) ? "" : " Where cdt.log_idx <> 0 ";
	         if(array_key_exists("orderby", $matrix)){
	         	$sqlOrderBy = $matrix['orderby'];
	         }
	         if(array_key_exists("acao", $matrix)){
	         	if(round($matrix['acao']) == 1){
	         		$matrix['acao'] = "INSERT";
	         	}elseif(round($matrix['acao']) == 2){
	         		$matrix['acao'] = "UPDATE";
	         	}else{
	         		$matrix['acao'] = "DELETE";
	         	}
	         	$sqlWhere .= "AND cdt.acao Like '".$matrix['acao']."'";
	         }

	         if(array_key_exists("modulo", $matrix)){
	         	$sqlWhere .= " AND cdt.modulo_codigo=".round($matrix['modulo']);
	         }

	         if(array_key_exists("usuario", $matrix)){
	         	$sqlWhere .= " AND cdt.usuario_idx=".round($matrix['usuario']);
	         }

	         if(array_key_exists("palavra_chave", $matrix)){
	         	$sqlWhere .= " AND ( 	cdt.modulo_area Like '%".$matrix['palavra_chave']."%'
	         	                     	OR cdt.registro_nome Like '%".$matrix['palavra_chave']."%'
	         	                     	OR cdt.descricao Like '%".$matrix['palavra_chave']."%'
	         	                     	OR cdt.ip_usuario Like '%".$matrix['palavra_chave']."%'
	         	                    	)";
	         }

	         if(array_key_exists("data_ate", $matrix)){
	         	$sqlWhere .= " AND cdt.data <= '".$matrix['data_ate']. " 00:00:00 '";
	         }
	         if(array_key_exists("data_de", $matrix)){
	         	$sqlWhere .= " AND cdt.data >= '".$matrix['data_de']. " 00:00:00 '";
	         }
	         $innerJoin = " Left Join " . parent::getPrefix() . "_login as Usr On Usr.usuario_idx = cdt.usuario_idx
	         					Left Join " . parent::getPrefix() . "_modulo as Mdl On Mdl.codigo = cdt.modulo_codigo";

	      }
	      $sqlQuery = "SELECT  cdt.*,
	      							Usr.nome as usr_nome,
	      							Usr.usuario_idx,
	      							Mdl.nome as mdl_nome
	      							FROM " . $table ." as cdt " . $innerJoin . $sqlWhere . " " . $sqlOrderBy;
	      // die($sqlQuery);

	      if($page==0)
	      {
	         return self::select($sqlQuery);
	      }else{
	         return self::selectPage($sqlQuery,$pageNumRows,$page);
	      }
		}

	} // End class
?>