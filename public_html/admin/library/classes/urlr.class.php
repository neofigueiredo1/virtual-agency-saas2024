<?php

	//require("urlr.DAO.class.php");
	class UrlRewriteRule{

		private $rule_idx;
		private $status;
		private $perfil;
		private $codigo_modulo;
		private $nome;
		private $changefreq;
		private $priority;
		private $lastmod;
		private $combinar;
		private $acao;
		private $descricao;

		function __construct()
		{
			//
			// $this->rule_idx = ($rule_idx===null) ? 0 : $rule_idx ;
			// $this->status = ($status===null) ? 0 : $status ;
			// $this->perfil = ($perfil===null) ? 0 : $perfil ;
			// $this->codigo_modulo = ($codigo_modulo===null) ? 0 : $codigo_modulo ;
			// $this->nome = ($nome===null) ? "" : $nome ;
			// $this->changefreq = ($changefreq===null) ? "" : $changefreq ;
			// $this->priority = ($priority===null) ? "" : $priority ;
			// $this->lastmod = ($lastmod===null) ? "" : $lastmod ;
			// $this->combinar = ($combinar===null) ? "" : $combinar ;
			// $this->acao = ($acao===null) ? "" : $acao ;
			// $this->descricao = ($descricao===null) ? "" : $descricao ;
			//$rule_idx=null,$status=null,$perfil=null,$codigo_modulo=null,$nome=null,$changefreq=null,$priority=null,$lastmod=null,$combinar=null,$acao=null,$descricao=null
		}
		public function __set($name, $value) {}

		public function getRule_idx() {
		    return $this->rule_idx;
		}
		public function setRule_idx($rule_idx) {
		    $this->rule_idx = $rule_idx;
			return $this;
		}

		public function getStatus() {
		    return $this->status;
		}
		public function setStatus($status) {
		    $this->status = $status;
			return $this;
		}

		public function getPerfil() {
		    return $this->perfil;
		}
		public function setPerfil($perfil) {
		    $this->perfil = $perfil;
			return $this;
		}

		public function getCodigo_modulo() {
		    return $this->codigo_modulo;
		}
		public function setCodigo_modulo($codigo_modulo) {
		    $this->codigo_modulo = $codigo_modulo;
			return $this;
		}

		public function getNome() {
		    return $this->nome;
		}
		public function setNome($nome) {
		    $this->nome = $nome;
			return $this;
		}

		public function getChangefreq() {
		    return $this->changefreq;
		}
		public function setChangefreq($changefreq) {
		    $this->changefreq = $changefreq;
			return $this;
		}

		public function getPriority() {
		    return $this->priority;
		}
		public function setPriority($priority) {
		    $this->priority = $priority;
			return $this;
		}

		public function getLastmod() {
		    return $this->lastmod;
		}
		public function setLastmod($lastmod) {
		    $this->lastmod = $lastmod;
			return $this;
		}

		public function getCombinar() {
		    return $this->combinar;
		}
		public function setCombinar($combinar) {
		    $this->combinar = $combinar;
			return $this;
		}

		public function getAcao() {
		    return $this->acao;
		}
		public function setAcao($acao) {
		    $this->acao = $acao;
			return $this;
		}

		public function getDescricao() {
		    return $this->descricao;
		}
		public function setDescricao($descricao) {
		    $this->descricao = $descricao;
			return $this;
		}

	}
?>
