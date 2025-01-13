<?php
	class faq_item extends faq_item_model{

		public $mod, $pag;

		public function __construct(){
			parent::__construct();

			global $act;
			$this->mod = "faq";
			$this->pag = "faq";
			$this->act = $act;
		}

		/**
		* Processa a lista completa dos registros de faqs.
		* @return array, se a consulta for realizada com sucesso, caso contrário false
		*/
		public function listAll($id=0,$fid=0,$campos=''){
			$array = array('orderby' => 'ORDER BY ranking DESC');
			if($fid != 0){
				$array['faq_idx'] = (int)$fid;
			}
			if($id != 0){
				$array['item_idx'] = (int)$id;
			}
			return parent::sqlCRUD($array, $campos, $this->TB_FAQ_ITEM, '', 'S', 0, 0);
		}

		/**
		* Método para Inserir.
		* @return bool
		*/
		public function _insert(){
			$fid = isset($_POST['fid']) ? Text::clean($_POST['fid']) : 0;
			$array = array(
	         'faq_idx' => $fid,
	         'status' => isset($_POST['status']) ? Text::clean($_POST['status']) : 0,
				'pergunta' => isset($_POST['pergunta']) ? Text::clean($_POST['pergunta']) : "",
				'resposta' => isset($_POST['resposta']) ? Text::clean($_POST['resposta']) : ""
	      );
      	$messageLog = array('FAQ - ITEM - INSERIR',$array['pergunta']);
     		$dados = parent::sqlCRUD($array, '', $this->TB_FAQ_ITEM, $messageLog, 'I', 0, 0);
			ob_end_clean();
			if(isset($dados) && $dados !== FALSE){
				Sis::setAlert("Dados salvos com sucesso!", 3, "?mod=" . $_GET['mod'] . "&pag=faq-item&fid=".$fid);
			} else {
				Sis::setAlert('Ocorreu um erro ao salvar os dados!', 4);
			}
		}

		/**
		* Atualiza o registro das categorias.
		* @return array, se a consulta for realizada com sucesso, caso contrário false
		*/
		public function _update(){
			$iid = (isset($_POST['iid']) && is_numeric($_POST['iid'])) ? (int)$_POST['iid'] : 0;
			$fid = isset($_POST['fid']) ? Text::clean($_POST['fid']) : 0;
			$array = array(
		      'item_idx' 	=> $iid,
		      'status'		=> isset($_POST['status']) ? (int)$_POST['status'] : 0,
		      'pergunta'	=> isset($_POST['pergunta']) ? Text::clean($_POST['pergunta']) : "",
		      'resposta'	=> isset($_POST['resposta']) ? Text::clean($_POST['resposta']) : ""
			);
			$messageLog = array('FAQ - ITEM - EDITAR', $array['item_idx'].' - '.$array['pergunta']);
			$dados = parent::sqlCRUD($array, '', $this->TB_FAQ_ITEM, $messageLog, 'U', 0, 0);
			if(isset($dados) && $dados != FALSE){
				Sis::setAlert("Dados salvos com sucesso!", 3, "?mod=" . $_GET['mod'] . "&pag=faq-item&fid=".$fid);
			}else{
				Sis::setAlert('Ocorreu um erro ao salvar os dados!', 4);
			}
		}

		/**
		* Exclui o registro das categorias.
		* @return bool
		*/
		public function _delete(){
			$iid = isset($_GET['iid']) ? (int)Text::clean($_GET['iid']) : "";
			$fid = isset($_GET['fid']) ? Text::clean($_GET['fid']) : 0;
			$reg  = self::listAll($iid,'pergunta');
			$regNome = (is_array($reg)&&count($reg)>0)?$reg[0]['pergunta']:"";
			$messageLog = array('FAQ - ITEM - EXCLUIR', $array['item_idx'].' - '.$regNome);
			$array = array('item_idx' => $iid);
			$dados = parent::sqlCRUD($array, '', $this->TB_FAQ_ITEM, $messageLog, 'D', 0, 0);
			ob_end_clean();
			if(isset($dados) && $dados !== FALSE){
				Sis::setAlert('Dados removidos com sucesso!', 3,"?mod=faq&pag=faq-item&fid=".$fid);
			} else {
				Sis::setAlert('Ocorreu um erro ao remover os dados!', 4);
			}
		}

	}
?>