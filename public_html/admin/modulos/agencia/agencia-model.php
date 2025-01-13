<?php
/**
 * Classe de gerenciamento de dados dos cadastros
 *
 * @package Cadastro
 **/
class cadastro_m extends HandleSql
{

   public $TB_CADASTRO;
   public $TB_AREA_SELECIONA;
   public $TB_AREA_INTERESSE;

   public $TB_CURSOS;
   public $TB_CADASTRO_CURSO_AFILIADO;

   public function __construct() {
      parent::__construct();
      $this->TB_CADASTRO = self::getPrefix() . "_cadastro";
      $this->TB_AREA_SELECIONA = self::getPrefix() . "_cadastro_seleciona";
      $this->TB_AREA_INTERESSE = self::getPrefix() . "_cadastro_interesse";

      $this->TB_CURSOS = self::getPrefix() . "_curso";
      $this->TB_CADASTRO_CURSO_AFILIADO = self::getPrefix() . "_cadastro_afiliado_curso";

   }

	public function listAllM($matrix, $table, $tableInner, $page=0, $pageNumRows=0, $campos=''){
      
      $sqlOrderBy = " Order By data_cadastro DESC ";
      $sqlWhere 	= "";
      $and 			= "";
      $innerJoin 	= "";
      if(is_array($matrix) && count($matrix) > 0){
         $sqlWhere = (count($matrix) <= 1) ? "" : " Where cdt.cadastro_idx <> 0 ";
         if(array_key_exists("status", $matrix)){
         	$sqlWhere .= " AND cdt.status=".(int)$matrix['status'];
         }
         if(array_key_exists("perfil", $matrix)){
            $sqlWhere .= " AND cdt.perfil=".(int)$matrix['perfil'];
         }
         if(array_key_exists("perfis", $matrix)){
            $sqlWhere .= " AND cdt.perfil IN (".(int)$matrix['perfis'].") ";
         }
         if(array_key_exists("genero", $matrix)){
         	$sqlWhere .= " AND cdt.genero=".(int)$matrix['genero'];
         }
         if(array_key_exists("palavra_chave", $matrix)){
         	$sqlWhere .= " AND ( 	cdt.nome_completo Like '%".$matrix['palavra_chave']."%'
         	                     	OR cdt.nome_informal Like '%".$matrix['palavra_chave']."%'
                                    OR cdt.email Like '%".$matrix['palavra_chave']."%'
         	                     	OR cdt.cpf_cnpj Like '%".$matrix['palavra_chave']."%'
         	                     	OR cdt.telefone_resid Like '%".$matrix['palavra_chave']."%'
         	                     	OR cdt.telefone_comer Like '%".$matrix['palavra_chave']."%'
         	                     	OR cdt.celular Like '%".$matrix['palavra_chave']."%'
         	                     	OR cdt.endereco Like '%".$matrix['palavra_chave']."%'
         	                     	OR cdt.bairro Like '%".$matrix['palavra_chave']."%'
         	                     	OR cdt.cidade Like '%".$matrix['palavra_chave']."%'
         	                     	OR cdt.estado Like '%".$matrix['palavra_chave']."%'
         	                     	OR cdt.pais Like '%".$matrix['palavra_chave']."%'
         	                    	)";
         }
         if(array_key_exists("receber_boletim", $matrix)){
         	$sqlWhere .= " AND cdt.receber_boletim=".(int)$matrix['receber_boletim'];
         }
         if(array_key_exists("data_ate", $matrix)){
         	$sqlWhere .= " AND cdt.data_cadastro <= '".$matrix['data_ate']. " 00:00:00 '";
         }
         if(array_key_exists("data_de", $matrix)){
         	$sqlWhere .= " AND cdt.data_cadastro >= '".$matrix['data_de']. " 00:00:00 '";
         }
         if(array_key_exists("area_interesse", $matrix)){
        		$innerJoin = "Inner join " . $tableInner . " as slo on slo.cadastro_idx=cdt.cadastro_idx ";
        		$sqlWhere .= " AND slo.interesse_idx = ".$matrix['area_interesse']. " ";
         }
         if(array_key_exists("orderby", $matrix)){
            $sqlOrderBy = $matrix['orderby'];
         }
      }

      $sqlQuery = "SELECT ".(is_array($campos) ? implode(",", $campos) : "*")." FROM " . $table ." as cdt " . $innerJoin . $sqlWhere . " " . $sqlOrderBy;

      if($page==0)
      {
         return self::select($sqlQuery);
      }else{
         return self::selectPage($sqlQuery, $pageNumRows, $page);
      }
      
	}

	public function excludeAreas($areaInteresse, $table){
		if (isset($areaInteresse)) {
			for ($i=0; $i < count($areaInteresse); $i++) {
				$res = parent::delete("DELETE FROM " . $table . " where cadastro_idx = " . round($_GET['id']) . " ");
			}
			if(isset($res) && $res != FALSE){
				return true;
			}
			return false;
		}
		die("nao");
		return true;
	}

}
?>
