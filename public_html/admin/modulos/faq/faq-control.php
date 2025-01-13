<?php
	include_once("faq-item-model.php");
	include_once("faq-item-control.php");

	class faq extends faq_model{

		public $dbPrefix;
		public $TB_FAQ;
		public $mod, $pag;

		public function __construct(){
			
			parent::__construct();

			global $act;
			$this->mod = "faq";
			$this->pag = "faq";
			$this->act = $act;

			$this->TB_FAQ = self::getPrefix() . "_faq";
		}

		/**
		* Processa a lista completa dos registros de faqs.
		* @return array, se a consulta for realizada com sucesso, caso contrário false
		*/
		public function listAll($id=0,$campos=''){
			$array = array('orderby' => 'ORDER BY ranking DESC');
			if($id != 0){
				$array['faq_idx'] = (int)$id;
			}
			return parent::sqlCRUD($array, $campos, $this->dbPrefix . $this->TB_FAQ, '', 'S', 0, 0);
		}


		/**
		* Método para Inserir.
		* @return bool
		*/
		public function _insert(){
			$array = array(
	         'status'    => isset($_POST['status']) ? Text::clean($_POST['status']) : 0,
				'nome'      => isset($_POST['nome']) ? Text::clean($_POST['nome']) : "",
				'descricao'      => isset($_POST['descricao']) ? Text::clean($_POST['descricao']) : ""
	      );
      	$messageLog = array('FAQ - INSERIR ÁREA',''.$array['nome']);
     		$dados = parent::sqlCRUD($array, '', $this->dbPrefix . $this->TB_FAQ, $messageLog, 'I', 0, 0);

			ob_end_clean();
			if(isset($dados) && $dados !== FALSE){
				Sis::setAlert("Dados salvos com sucesso!", 3, "?mod=" . $_GET['mod'] . "&pag=faq");
			} else {
				Sis::setAlert('Ocorreu um erro ao salvar os dados!', 4);
			}
		}

		/**
		* Atualiza o registro das categorias.
		* @return array, se a consulta for realizada com sucesso, caso contrário false
		*/
		public function _update(){
			$fid = (isset($_POST['fid']) && is_numeric($_POST['fid'])) ? (int)$_POST['fid'] : 0;
			$array = array(
		      'faq_idx' 	=> $fid,
		      'status'		=> isset($_POST['status']) ? (int)$_POST['status'] : 0,
		      'nome'      => isset($_POST['nome']) ? Text::clean($_POST['nome']) : "",
		      'descricao' => isset($_POST['descricao']) ? Text::clean($_POST['descricao']) : ""
			);
			$messageLog = array('FAQ - EDITAR ÁREA', $array['faq_idx'].' - '.$array['nome']);
			$dados = parent::sqlCRUD($array, '', $this->dbPrefix . $this->TB_FAQ, $messageLog, 'U', 0, 0);
			if(isset($dados) && $dados != FALSE){
				Sis::setAlert("Dados salvos com sucesso!", 3, "?mod=" . $_GET['mod'] . "&pag=faq");
			} else {
				Sis::setAlert('Ocorreu um erro ao salvar os dados!', 4);
			}
		}

		/**
		* Exclui o registro das categorias.
		* @return bool
		*/
		public function _delete(){
			$fid = isset($_GET['fid']) ? (int)Text::clean($_GET['fid']) : "";
			$faqItem = new faq_item();
       	$checkItens = $faqItem->listAll(0,$fid,$campos=' count(*) as nReg ');
			if(is_array($checkItens) && count($checkItens) > 0)
			{
				if ((int)$checkItens[0]['nReg']>0) {
					ob_end_clean();
					Sis::setAlert('Não foi possível remover. Existem itens relacionados a este grupo!', 1,"?mod=faq&pag=faq");
					exit();
				}
			}
			$reg  = self::listAll($fid,'nome');
			$regNome = (is_array($reg)&&count($reg)>0)?$reg[0]['nome']:"";
			$messageLog = array('FAQ - EXCLUIR ÁREA', $array['faq_idx'].' - '.$regNome);
			$array = array('faq_idx' => $fid);
			$dados = parent::sqlCRUD($array, '', $this->dbPrefix . $this->TB_FAQ, $messageLog, 'D', 0, 0);
			ob_end_clean();
			if(isset($dados) && $dados !== FALSE){
				Sis::setAlert('Dados removidos com sucesso!', 3,"?mod=faq&pag=faq");
			} else {
				Sis::setAlert('Ocorreu um erro ao remover os dados!', 4);
			}
		}


	}
?>