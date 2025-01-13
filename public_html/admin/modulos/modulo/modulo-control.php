<?php
	class modulo extends ModuloModel
	{

		public $MODULO_CODIGO 	= "0";
		public $MODULO_AREA 		= "Módulo";

		function __construct(){
			parent::__construct();
		}

		public function listaTodos()
		{
			$dados = parent::mListaTodos();

			if(is_array($dados) && count($dados)){
				return $dados;
			} else  {
				return false;
			}
		}

		public function listaModuleBackups($module)
		{
			$dados = parent::mListaModuleBackups($module);
			if(is_array($dados) && count($dados)){
				return $dados;
			} else  {
				return false;
			}
		}

		public function checkInstall($id)
		{
			$dados = parent::mCheckInstall($id);
			if(is_array($dados) && count($dados)){
				return $dados;
			} else  {
				return false;
			}
		}

		public function selectById($id){
			$dados = parent::mSelectById($id);
			if(is_array($dados) && count($dados)){
				return $dados;
			} else {
				return false;
			}
		}

		public function remover() {
			$mid = isset($_GET['mid']) ? (int)$_GET['mid'] : 0;
			if(!is_numeric($mid) || $mid == 0){
				Sis::setAlert("Ocorreu um erro ao remover o módulo!", 4, "?mod=".$this->mod."&pag=".$this->pag."");
			}
			$dados = parent::mSelectById($mid);
			if(is_array($dados) && count($dados)>0)
			{
				/* Faz o backup do sql do módulo */
				parent::mModuleBackup($dados[0]['pasta']);

				$sqlFile = 'modulos'. DS . $dados[0]['pasta'] . DS . 'db-'. $dados[0]['pasta'] .'-out.sql';
				if (file_exists($sqlFile)) {
					Sis::execQueryByFile($sqlFile);
				}
			}

			$dadosR = parent::mRemover($mid);

			ob_end_clean();
			if(isset($dadosR) && $dadosR !== NULL){
				$_SESSION['sis_mens'] = "Dados removidos com sucesso!";
				$_SESSION['sis_mens_tipo'] = 3;
				die("<script type='text/javascript'>window.location='?mod=" . $_GET['mod'] . "&pag=" . $_GET['pag'] . "';</script>");
			} else {
				$_SESSION['sis_mens'] = "Ocorreu um erro ao remover dados!";
				$_SESSION['sis_mens_tipo'] = 4;
				die("<script type='text/javascript'>history.back();</script>");
			}
		}


		public function restoreBackup()
		{
			$mp = isset($_GET['mp']) ? $_GET['mp'] : "";
			$mcd = isset($_GET['mcd']) ? $_GET['mcd'] : "";
			$fname = isset($_GET['fname']) ? $_GET['fname'] : "";

			$mid = parent::mSelectById($mcd, 'nome');
			if(is_array($mid) && count($mid) == 1){
				$nome = $mid[0]['nome'];
			}

			$sqlFile       = 'modulos' . DS . $mp . DS . 'sql-backup'. DS .$fname;
			if(file_exists($sqlFile)) {
				Sis::execQueryByFile($sqlFile);
				Sis::insertLog(0, $this->MODULO_AREA, 'UPDATE', 0, $nome, "Restauração de backup");
				$_SESSION['sis_mens'] = "Arquivo de backup processado com sucesso!";
				$_SESSION['sis_mens_tipo'] = 3;
			}else{
				ob_end_clean();
				$_SESSION['sis_mens'] = "Arquivo de backup inexistente!";
				$_SESSION['sis_mens_tipo'] = 1;
				die("<script type='text/javascript'>window.location='?mod=" . $_GET['mod'] . "&pag=" . $_GET['pag'] . "';</script>");
			}
			ob_end_clean();
			die("<script type='text/javascript'>window.location='?mod=" . $_GET['mod'] . "&pag=" . $_GET['pag'] . "';</script>");

		}


		public function deleteBackup()
		{
			$mp = isset($_GET['mp']) ? $_GET['mp']:"";
			$fname = isset($_GET['fname']) ? $_GET['fname']:"";
			$mcd = isset($_GET['mcd']) ? $_GET['mcd'] : "";

			$mid = parent::mSelectById($mcd, 'nome');
			if(is_array($mid) && count($mid) == 1){
				$nome = $mid[0]['nome'];
			}

			$sqlFile       = 'modulos' . DS . $mp . DS. 'sql-backup'.DS.$fname;
			if(file_exists($sqlFile)) {
				unlink($sqlFile);
			}else{
				ob_end_clean();
				$_SESSION['sis_mens'] = "Arquivo de backup inexistente!";
				$_SESSION['sis_mens_tipo'] = 1;
				die("<script type='text/javascript'>window.location='?mod=" . $_GET['mod'] . "&pag=" . $_GET['pag'] . "';</script>");
				exit();
			}
			ob_end_clean();
			Sis::insertLog(0, $this->MODULO_AREA, 'UPDATE', 0, $nome, "Exclusão de backup");
			$_SESSION['sis_mens'] = "Arquivo de backup excluído com sucesso!";
			$_SESSION['sis_mens_tipo'] = 3;
			die("<script type='text/javascript'>window.location='?mod=" . $_GET['mod'] . "&pag=" . $_GET['pag'] . "';</script>");

		}


		public function inserir()
		{
			$m_data = isset($_POST['m_data']) ? $_POST['m_data'] : "";

			if($m_data==""){
				$_SESSION['sis_mens'] = "Sem dados para registrar o módulo!";
				$_SESSION['sis_mens_tipo'] = 1;
				die("<script type='text/javascript'>window.location='?mod=" . $_GET['mod'] . "&pag=" . $_GET['pag'] . "';</script>");
			}

			$m_data        = str_replace("\\", '', $m_data);

			$elemento_data = json_decode($m_data);

			$codigo        = $elemento_data->{'codigo'};
			$nome          = $elemento_data->{'nome'};
			$versao        = $elemento_data->{'versao'};
			$descricao     = $elemento_data->{'descricao'};
			$pasta         = $elemento_data->{'pasta'};
			$sqlFile       = 'modulos' . DS . $pasta . DS . 'db-'.$pasta.'.sql';

			if (file_exists($sqlFile)) {
				Sis::execQueryByFile($sqlFile);
			}

			$permissao = $elemento_data->{'permissao'};

			//Registra o módulo
			$dados = parent::mInserir($codigo,$nome,$versao,$descricao,$pasta,$m_data);

			//Registra suas permissões
			if(is_array($permissao)){
				foreach($permissao as $dPermissao)
				{
					$dados_perm = parent::mInserirPermissao($codigo,$dPermissao->codigo,$dPermissao->nome,$dPermissao->descricao);
				}
			}

			// ob_end_clean();

			// if(isset($dados) && $dados !== NULL)
			// {
			// 	if ($dados == true)
			// 	{
			// 		$_SESSION['sis_mens'] = "Dados cadastrados com sucesso!";
			// 		$_SESSION['sis_mens_tipo'] = 3;
			// 		die("<script type='text/javascript'>window.location='?mod=" . $_GET['mod'] . "&pag=" . $_GET['pag'] . "';</script>");
			// 	}else{
			// 		$_SESSION['sis_mens'] = "M&oacute;dulo j&aacute; registrado no sistema!";
			// 		$_SESSION['sis_mens_tipo'] = 1;
			// 		die("<script type='text/javascript'>window.location='?mod=" . $_GET['mod'] . "&pag=" . $_GET['pag'] . "';</script>");
			// 	}
			// } else {
			// 	$_SESSION['sis_mens'] = "Ocorreu um erro ao cadastrar dados!";
			// 	$_SESSION['sis_mens_tipo'] = 4;
			// 	die("<script type='text/javascript'>history.back();</script>");
			// }
			Sis::redirect('?mod='.$_GET["mod"].'&pag='.$_GET['mod']);
		}

	}
?>