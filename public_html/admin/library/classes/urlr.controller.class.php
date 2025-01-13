<?php

	require("urlr.class.php");
	require("urlr.dao.class.php");
	class UrlRewriteController{

		private $modulo;
		private $DAO;
		function __construct($modulo=0)
		{
			$this->modulo = $modulo;
			$this->DAO = new UrlRewriteDAO();
			$this->DAO->updateRulesStatusToClean($this->modulo);
		}

		function __destruct(){
			$this->DAO->deleteRulesWithStatusOut($this->modulo);
		}

		/**
		 * Método para atualização das URLs do site.
		 * @param Array $arrayUrls => Lista de Urls do site
		 * 	String "url"
		 * 	String "nome"
		 * 	String "acao"
		 * @param Array $informacoes => Lista de informações utilizadas no sitemap.xml
		 *		String "changefreq"
		 *		String "priority"
		 *		String "lastmod"
		 *
		 * @return VOID
		 */
		public function setAllUrlRules($arrayUrls, $informacoes){
			if(is_array($arrayUrls) && count($arrayUrls) > 0){
				foreach ($arrayUrls as $key => $url) {

					$status   	= 1;
					$perfil  	= 1;
					$name     	= $url['nome'];
					$combine	= $url['url'];
					$action 	= $url['acao'];
					$changefreq = $informacoes['changefreq'];
					$priority 	= $informacoes['priority'];
					$lastmod 	= $informacoes['lastmod'];
					
					$Rule = $this->DAO->getByCombination($combine,$action,$this->modulo);
					
					if(is_object($Rule)){
						$Rule->setStatus(1);
						$Rule->setChangefreq($changefreq);
						$Rule->setPriority($priority);
						$Rule->setLastmod($lastmod);
						$this->DAO->update($Rule);
					}else{
						$Rule = new UrlRewriteRule;
						$Rule->setStatus($status);
						$Rule->setPerfil($perfil);
						$Rule->setNome($name);
						$Rule->setCombinar($combine);
						$Rule->setAcao($action);
						$Rule->setCodigo_modulo($this->modulo);
						$Rule->setChangefreq($changefreq);
						$Rule->setPriority($priority);
						$Rule->setLastmod($lastmod);
						$this->DAO->insert($Rule);
					}
				}
			}
			try {
				self::generateSitemap();
			} catch (Exception $e) {
				throw $e;
			}
		}

		public function generateSitemap()
		{
			$urlsR = $this->DAO->getAllOn();
			if(is_array($urlsR) && count($urlsR) > 0){
				try {
					$newsXML = new SimpleXMLElement("<urlset></urlset>");
					$newsXML->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
					foreach ($urlsR as $Rule) {
						$url = $newsXML->addChild('url');
						$url->addChild('loc', "http://" . $_SERVER['HTTP_HOST'] . "/" . $Rule->getCombinar());
						$url->addChild('lastmod', $Rule->getLastmod());
						$url->addChild('changefreq', $Rule->getChangefreq());
						$url->addChild('priority', $Rule->getPriority());
					}
				} catch (Exception $e) {
					throw $e;
				}
				try {
					$fp = fopen($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "sitemap.xml", "w");
					fwrite($fp, $newsXML->asXML());
					fclose($fp);
				} catch (Exception $e) {
					throw $e;
				}
			}
		}

	}

