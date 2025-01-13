<?php

$root = dirname(dirname(dirname(dirname(__FILE__))));
require_once($root.DIRECTORY_SEPARATOR."admin".DIRECTORY_SEPARATOR."library".DIRECTORY_SEPARATOR."interfaces".DIRECTORY_SEPARATOR."urlr.dao.interface.php");

/*
* Classe Concreta DAO UrlRewrite
*/
class UrlRewriteDAO implements UrlRewriteDAO_interface{

		private $tbl_urlrewrite_rule;
		private $handleSql;
		function __construct(){
			$this->handleSql = new HandleSql();
			$this->tbl_urlrewrite_rule = $this->handleSql->DB_PREFIX.'_urlrewrite_rule';
		}

		/**
		* Define os registro de Rules para o módulo específico com status = 0
		* Vai auxiliar na separação das regras que continuam das que deverão ser removidas após a atualização
		*/
		public function updateRulesStatusToClean($modulo){
			$this->handleSql->update('UPDATE ' . $this->tbl_urlrewrite_rule . ' SET status=0 WHERE codigo_modulo=' . $modulo . ' AND status=1 AND perfil=1 ');
		}

		/**
		* Deleta os registros de Rules com status=0
		*/
		public function deleteRulesWithStatusOut($modulo){
			$this->handleSql->delete('DELETE FROM ' . $this->tbl_urlrewrite_rule . ' WHERE codigo_modulo=' . $modulo . ' AND status=0');
		}

		/**
		* Persiste uma determinada regra
		*/
		public function insert(UrlRewriteRule $Rule){
				$retorno = $this->handleSql->insert('INSERT INTO ' . $this->tbl_urlrewrite_rule . '
			               (
									status,
									perfil,
									nome,
									combinar,
									codigo_modulo,
									changefreq,
									priority,
									lastmod,
									acao
								) VALUES(
									'  . (int)$Rule->getStatus() . ',
									'  . (int)$Rule->getPerfil() . ',
									"' . Text::stripVar($Rule->getNome()) . '",
									"' . Text::stripVar($Rule->getCombinar()) . '",
									"' . (int)$Rule->getCodigo_modulo() . '",
									"' . Text::stripVar($Rule->getChangefreq()) . '",
									"' . Text::stripVar($Rule->getPriority()) . '",
									"' . Text::stripVar($Rule->getLastmod()) . '",
									"' . Text::stripVar($Rule->getAcao()) . '"
								)');
			return $retorno;
		}

		public function update(UrlRewriteRule $Rule){
				$retorno = $this->handleSql->update('UPDATE ' . $this->tbl_urlrewrite_rule . ' SET
	            	status='  . (int)$Rule->getStatus() . ',
						perfil='  . (int)$Rule->getPerfil() . ',
						nome="' . Text::stripVar($Rule->getNome()) . '",
						combinar="' . Text::stripVar($Rule->getCombinar()) . '",
						codigo_modulo="' . (int)$Rule->getCodigo_modulo() . '",
						changefreq="' . $Rule->getChangefreq() . '",
						priority="' . $Rule->getPriority() . '",
						lastmod="' . $Rule->getLastmod() . '",
						acao="' . Text::stripVar($Rule->getAcao()) . '
					WHERE rule_idx='.(int)$Rule->getRule_idx().'
					');
			return $retorno;
		}

		public function delete(UrlRewriteRule $Rule){
			$this->handleSql->delete('DELETE FROM ' . $this->tbl_urlrewrite_rule . ' WHERE rule_idx='.(int)$Rule->getRule_idx().' ');
		}

		public function toString(UrlRewriteRule $Rule){
			var_dump($Rule);
		}

		public function getAll(){
			try {
				$retorno = $this->handleSql->select('SELECT * FROM ' . $this->tbl_urlrewrite_rule . ' ','UrlRewriteRule');
				return $retorno;
			} catch (Exception $e) {
				throw $e;
			}
		}

		public function getAllOn(){
			try {
				$retorno = $this->handleSql->select('SELECT * FROM ' . $this->tbl_urlrewrite_rule . ' WHERE status=1','UrlRewriteRule');
				return $retorno;
			} catch (Exception $e) {
				throw $e;
			}
		}

		public function get($id){
			try {
				$retorno = $this->handleSql->select('SELECT * FROM ' . $this->tbl_urlrewrite_rule . ' WHERE rule_idx='.(int)$id.'','UrlRewriteRule');
				return $retorno;
			} catch (Exception $e) {
				throw $e;
			}
		}

		public function getByCombination($combine, $action, $moduloCodigo){
			try {
				$retorno = $this->handleSql->select('SELECT * FROM ' . $this->tbl_urlrewrite_rule . ' WHERE codigo_modulo='.(int)$moduloCodigo.' AND combinar = "' . $combine . '" AND acao = "' . $action . '" ');
				return $retorno;
			} catch (Exception $e) {
				throw $e;
			}
		}

}