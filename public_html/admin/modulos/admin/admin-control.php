<?php
	class admin extends admin_model{

		private $mod, $pag, $act;
		// public $dbPrefix    		= Connect::DB_PREFIX;
		public $MODULO_CODIGO 	= "0";
		public $MODULO_AREA 		= "Administrador";

		public function __construct() {
			parent::__construct();
			global $mod, $pag, $act;
			$this->mod = $mod;
			$this->pag = $pag;
			$this->act = $act;
		}

		public function listAll(){
			$dados = parent::listAllM();
			if(is_array($dados) && count($dados)){
				return $dados;
			}
			else{
				return false;
			}
		}

		public function listSelected($id = 0,$campo=""){
			$array = array(
			   'usuario_idx' => Text::clean($id)
			);
			$camposSelect 		= ($campo!="") ? $campo : '';

			$dados = parent::sqlCRUD($array, $camposSelect, $this->TB_ADMIN, '', 'S', 0, 0);
			if(isset($dados) && $dados !== NULL){ return $dados; }
			else{ return false; }
		}


		public function _insert() {
			$status       = isset($_POST['status'])       ? (int)$_POST['status']         : 0;
			$nivel        = isset($_POST['nivel'])        ? (int)$_POST['nivel']          : 3;
			$nome         = isset($_POST['nome'])         ? Text::clean($_POST['nome'])  : '';
			$email        = isset($_POST['email'])        ? Text::clean($_POST['email']) : '';
			$login        = isset($_POST['login'])        ? Text::clean($_POST['login']) : '';
			$senha        = isset($_POST['senha'])        ? md5($_POST['senha'])          : '';
			$set_validade = isset($_POST['set_validade']) ? (int)$_POST['set_validade']   : 0 ;
			$validade     = isset($_POST['validade'])     ? Date::toMysql(Text::clean($_POST['validade']),2) : "0000-00-00 00:00:00";

			if(!is_numeric($nivel)){
				$nivel = 3;
			}

			if($_SESSION['usuario']['nivel'] > $nivel){
				Sis::setAlert("Ocorreu um erro ao cadastrar! Verifique se os dados estão preenchidos corretamente!", 4);
			}

			$array = array(
			   'status'  		=> $status,
			   'nivel'  		=> $nivel,
			   'nome'  			=> $nome,
			   'email'  		=> $email,
			   'login'  		=> $login,
			   'senha'  		=> $senha,
			   'set_validade' => $set_validade,
			   'validade'  	=> $validade
			);

			$messageLog = array('modulo_codigo'=>$this->MODULO_CODIGO,'modulo_area'=>$this->MODULO_AREA,'reg_nome'=>$array['nome']);
			$dados 		= parent::sqlCRUD($array, '', $this->TB_ADMIN, $messageLog, 'I', 0, 0);

			/* Registra as permissoes do usuario */
			$usuario_idx = $dados;
			$m_permissoes = isset($_POST['m_permissao']) ? $_POST['m_permissao'] : '' ;
			if($m_permissoes!="" && is_array($m_permissoes))
			{
					foreach($m_permissoes as $m_permissao)
					{
						$a_permissao = explode("-",$m_permissao);
						$modulo_codigo = $a_permissao[0];
						$permissao_codigo =  $a_permissao[1];
						$dados = parent::insert("Insert Into ".$this->TB_ADMIN_PERMISSAO."(usuario_idx,modulo_codigo,permissao_codigo) values(".round($usuario_idx).",'".$modulo_codigo."','".$permissao_codigo."')");
					}
			}

			ob_end_clean();
			if(isset($dados) && $dados !== NULL){
				if ($dados == true) {
					Sis::setAlert("Dados cadastrados com sucesso!", 3, "?mod=".$this->mod."&pag=".$this->pag."");
				}else{
					Sis::setAlert("Ocorreu um erro ao cadastrar verifique se os dados estão preenchidos corretamente e se o registro já não existe no sistema!", 4);
				}
			} else {
				Sis::setAlert("Ocorreu um erro ao cadastrar dados!", 4);
			}
		}


		public function _delete()
		{
			$id = isset($_GET['id']) ? (int)$_GET['id'] : 0 ;

			$array = array('usuario_idx' => $id);
			$listSelected = self::listSelected($id, 'nome');
			if(is_array($listSelected) && count($listSelected) == 1){
				$listSelected = $listSelected[0]['nome'];
			}

			$messageLog = array('modulo_codigo'=>$this->MODULO_CODIGO,'modulo_area'=>$this->MODULO_AREA,'reg_nome'=>$listSelected);
			$dados = parent::sqlCRUD($array, '', $this->TB_ADMIN, $messageLog, 'D', 0, 0);
			$dados_permissao = parent::delete("Delete From ".$this->TB_ADMIN_PERMISSAO." Where usuario_idx=".round($id)." ");

			ob_end_clean();
			if(isset($dados) && $dados !== NULL){
				if ($dados == true) {
					Sis::setAlert("Dados removidos com sucesso!", 3, "?mod=".$this->mod."&pag=".$this->pag."");
				}else{
					Sis::setAlert("Ocorreu um erro ao cadastrar verifique se os dados estão preenchidos corretamente e se o registro já não existe no sistema!", 2);
					die("<script type='text/javascript'>history.back();</script>");
				}
			} else {
				Sis::setAlert("Ocorreu um erro ao cadastrar dados!", 1);
				die("<script type='text/javascript'>history.back();</script>");
			}

		}

		public function _update()
		{
			$id           = isset($_POST['id'])           ? (int)$_POST['id']             : 0 ;
			$status       = isset($_POST['status'])       ? (int)$_POST['status']         : 0 ;
			$nivel        = isset($_POST['nivel'])        ? (int)$_POST['nivel']          : 0 ;
			$nome         = isset($_POST['nome'])         ? Text::clean($_POST['nome'])  	: '';
			$email        = isset($_POST['email'])        ? Text::clean($_POST['email']) 	: '';
			$login        = isset($_POST['login'])        ? Text::clean($_POST['login']) 	: '';
			$senha        = isset($_POST['senha'])        ? md5($_POST['senha'])          : '';
			$set_validade = isset($_POST['set_validade']) ? (int)$_POST['set_validade']   : 0 ;
			$validade     = isset($_POST['validade'])     ? Date::toMysql(Text::clean($_POST['validade']),2) : "0000-00-00 00:00:00";
			$set_pass     = isset($_POST['set_pass'])     ? (int)$_POST['set_pass']          : 0 ;
			$senha        = isset($_POST['senha'])        ? md5($_POST['senha'])             : "";

			if($_SESSION['usuario']['nivel'] > $nivel){
				Sis::setAlert("Ocorreu um erro ao cadastrar! Verifique se os dados estão preenchidos corretamente!", 4);
			}

			$array = array(
			   'usuario_idx'  => $id,
			   'status'  		=> $status,
			   'nivel'  		=> $nivel,
			   'nome'  			=> $nome,
			   'email'  		=> $email,
			   'login'  		=> $login,
			   'set_validade' => $set_validade,
			   'validade'  	=> $validade
			);
			if(round($set_pass)==1){
				Sis::arrayPutToPosition($array, $senha, 2, 'senha');
			}

			$messageLog = array('modulo_codigo'=>$this->MODULO_CODIGO,'modulo_area'=>$this->MODULO_AREA,'reg_nome'=>$array['nome']);
			//Nesse caso, o método do Crud só é chamado se algum arquivo válido for submetido
			$dados = parent::sqlCRUD($array, '', $this->TB_ADMIN, $messageLog, 'U', 0, 0);

			/*Registra as permissoes do usuario*/
			$usuario_idx = $id;
			$m_permissoes_old = isset($_POST['up']) ? $_POST['up'] : '' ;
			$m_permissoes_old = str_replace("!!",",",$m_permissoes_old);
			$m_permissoes_old = str_replace("!","",$m_permissoes_old);

			$m_permissoes = isset($_POST['m_permissao']) ? $_POST['m_permissao'] : '' ;
			if($m_permissoes!="" && is_array($m_permissoes))
			{
					foreach($m_permissoes as $m_permissao)
					{
						if(strpos($m_permissoes_old,$m_permissao)===false)
						{
							$a_permissao = explode("-",$m_permissao);
							$modulo_codigo = $a_permissao[0];
							$permissao_codigo =  $a_permissao[1];
							parent::insert("Insert Into ".$this->TB_ADMIN_PERMISSAO."(usuario_idx,modulo_codigo,permissao_codigo) values(".round($usuario_idx).",'".$modulo_codigo."','".$permissao_codigo."')");
						}
					}
			}

			$t_m_permissoes = (is_array($m_permissoes))?implode(",",$m_permissoes):$m_permissoes;
			$a_m_permissoes_old = explode(",",$m_permissoes_old);
			if(is_array($a_m_permissoes_old) && count($a_m_permissoes_old)>0)
			{
				foreach($a_m_permissoes_old as $a_m_permicao_old)
				{
					if(trim($a_m_permicao_old)!="")
					{
						if (strpos($t_m_permissoes,$a_m_permicao_old)===false)
						{
							$a_permissao = explode("-",$a_m_permicao_old);
							$modulo_codigo = $a_permissao[0];
							$permissao_codigo =  $a_permissao[1];
							parent::insert("Delete From ".$this->TB_ADMIN_PERMISSAO." Where usuario_idx=".round($usuario_idx)." And modulo_codigo='".$modulo_codigo."' And permissao_codigo='".$permissao_codigo."' ");
						}
					}
				}
			}

			ob_end_clean();
			if(isset($dados) && $dados !== NULL){
				if ($dados == true) {
					Sis::setAlert("Dados atualizados com sucesso!", 3, "?mod=".$this->mod."&pag=".$this->pag."");
				}else{
					Sis::setAlert("Ocorreu um erro ao cadastrar verifique se os dados estão preenchidos corretamente e se o registro já não existe no sistema!", 2);
					die("<script type='text/javascript'>history.back();</script>");
				}
			} else {
				Sis::setAlert("Ocorreu um erro ao cadastrar dados!", 1);
				die("<script type='text/javascript'>history.back();</script>");
			}
		}

		public function updateMyAccount()
		{
			$id           = $_SESSION['usuario']['id'];
			$nome         = isset($_POST['nome'])         ? Text::clean($_POST['nome'])  : '';
			$email        = isset($_POST['email'])        ? Text::clean($_POST['email']) : '';
			$login        = isset($_POST['login'])        ? Text::clean($_POST['login']) : '';

			$array = array(
			   'usuario_idx'  => $id,
			   'nome'  			=> $nome,
			   'email'  		=> $email,
			   'login'  		=> $login
			);

			$messageLog = array('modulo_codigo'=>$this->MODULO_CODIGO, 'modulo_area'=>$this->MODULO_AREA, 'reg_nome'=>$array['nome'], 'descricao'=>'Editar meus dados');
			$dados = parent::sqlCRUD($array, '', $this->TB_ADMIN, $messageLog, 'U', 0, 0);

			ob_end_clean();
			if(isset($dados) && $dados !== NULL){
				if ($dados == true) {
					Sis::setAlert("Dados atualizados com sucesso!", 3, "?mod=".$this->mod."&pag=".$this->pag."");
				}else{
					Sis::setAlert("Ocorreu um erro ao cadastrar verifique se os dados estão preenchidos corretamente e se o registro já não existe no sistema!", 2);
					die("<script type='text/javascript'>history.back();</script>");
				}
			} else {
				Sis::setAlert("Ocorreu um erro ao cadastrar dados!", 1);
				die("<script type='text/javascript'>history.back();</script>");
			}
		}


		public function updatePassword()
		{
			$id     		= $_SESSION['usuario']['id'] ;
			$senha  		= isset($_POST['senha']) ? md5($_POST['senha']) : "";
			$old_senha  = isset($_POST['old_senha']) ? md5($_POST['old_senha']) : "";

			$arrayToSelect = array(
			   'usuario_idx'  => $id,
			);
			$dadosToSelect = parent::sqlCRUD($arrayToSelect, '', $this->TB_ADMIN, '', 'S', 0, 0);
			// var_dump($dadosToSelect);
			// die();
			if(is_array($dadosToSelect) && count($dadosToSelect) > 0){
				if ($dadosToSelect[0]['senha'] != $old_senha) {
					Sis::setAlert("A [ SENHA ANTERIOR ] não confere, tente novamente!", 4, "?mod=".$this->mod."&pag=".$this->pag."&act=".$this->act."");
				}else{

					$array = array(
					   'usuario_idx'  => $id,
					   'senha'  		=> $senha
					);
					$messageLog = array('modulo_codigo'=>$this->MODULO_CODIGO, 'modulo_area'=>$this->MODULO_AREA, 'reg_nome'=>$array['nome'], 'descricao'=>'Editar minha senha');
					$dados = parent::sqlCRUD($array, '', $this->TB_ADMIN, $messageLog, 'U', 0, 0);

					ob_end_clean();
					if(isset($dados) && $dados !== NULL){
						if ($dados == true) {
							Sis::setAlert("Senha atualizada com sucesso!", 3, "?mod=".$this->mod."&pag=".$this->pag."");
						}else{
							Sis::setAlert("Ocorreu um erro ao cadastrar, verifique se os dados estão preenchidos corretamente!", 2);
						}
					} else {
						Sis::setAlert("Ocorreu um erro ao cadastrar dados!", 1);
					}

				}
			}
		}
	}
?>