<?php
	class config extends config_model {

		public $MODULO_CODIGO 	= "0";
		public $MODULO_AREA 		= "Configuração";

		function __construct(){
			parent::__construct();
		}

		public function listAll()
		{
			$array = array(
	         'nivel' 		=> 1,
	         'orderby' 	=> 'Order by nome Asc'
	      );

	     	$dados = parent::sqlCRUD($array, '', $this->TB_CONFIG, '', 'S', 0, 0);

			if(is_array($dados) && count($dados) > 0){
				return $dados;
			} else  {
				return false;
			}
		}

		public function theInsert()
		{
			$array = array(
	         'status'    => isset($_POST['status']) ? Text::clean($_POST['status']) : 0,
				'nome'      => isset($_POST['nome']) ? Text::clean($_POST['nome']) : "",
				'valor'     => isset($_POST['valor']) ? Text::clean($_POST['valor']) : "",
				'descricao' => isset($_POST['descricao']) ? Text::clean($_POST['descricao']) : "",
				'nivel'     => 1
	      );

	     	$messageLog = array('modulo_codigo'=>$this->MODULO_CODIGO,'modulo_area'=>$this->MODULO_AREA,'reg_nome'=>$array['nome']);
	     	$dados = parent::sqlCRUD($array, '', $this->TB_CONFIG, $messageLog, 'I', 0, 0);

			ob_end_clean();
			if(isset($dados) && $dados !== NULL){
				if ($dados == true) {
					Sis::setAlert("Dados salvos com sucesso!", 3, "?mod=" . $_GET['mod'] . "&pag=" . $_GET['pag']);
				}else{
					Sis::setAlert('O registro informado j&aacute; existe!', 4);
				}
			} else {
				Sis::setAlert('Ocorreu um erro ao salvar os dados!', 4);
			}
		}

		public function listSelected($varId, $campo="")
		{
			$array = array(
	               'config_idx'    => ($varId != 0 && is_numeric($varId)) ? Text::clean($varId) : 0
	      );
	      $camposSelect 		= ($campo!="") ? $campo : '';

	     	$dados = parent::sqlCRUD($array, $camposSelect, $this->TB_CONFIG, '', 'S', 0, 0);

			if(is_array($dados) && count($dados)){
				return $dados;
			} else {
				return false;
			}
		}

		public function theUpdate()
		{
			$array = array(
            'config_idx'	=> isset($_POST['v_id']) ? Text::clean($_POST['v_id']) : 0,
            'status'    	=> isset($_POST['status']) ? Text::clean($_POST['status']) : 0,
				'nome'      	=> isset($_POST['nome']) ? Text::clean($_POST['nome']) : "",
				'valor'     	=> isset($_POST['valor']) ? Text::clean($_POST['valor']) : "",
				'descricao' 	=> isset($_POST['descricao']) ? Text::clean($_POST['descricao']) : ""
	      	);

	     	$messageLog = array('modulo_codigo'=>$this->MODULO_CODIGO,'modulo_area'=>$this->MODULO_AREA,'reg_nome'=>$array['nome']);
	     	$dados = parent::sqlCRUD($array, '', $this->TB_CONFIG, $messageLog, 'U', 0, 0);

			ob_end_clean();
			if(isset($dados) && $dados !== NULL){
				if ($dados == true) {
					Sis::setAlert("Dados salvos com sucesso!", 3, "?mod=" . $_GET['mod'] . "&pag=" . $_GET['pag']);
				}else{
					Sis::setAlert('O registro informado j&aacute; existe!', 4);
				}
			} else {
				Sis::setAlert('Ocorreu um erro ao salvar os dados!', 4);
			}
		}

		public function theDelete()
		{
			$nome = (isset($_GET['nome']) && $_GET['nome'] != "") ? Text::clean($_GET['nome']) : "";
			$array = array(
	        'config_idx' => (isset($_GET['v_id']) && is_numeric($_GET['v_id']) && $_GET['v_id'] != 0) ? Text::clean($_GET['v_id']) : ""
	     	);
	     	$fildsToSelect  = '';

	     	$messageLog = array('modulo_codigo'=>$this->MODULO_CODIGO,'modulo_area'=>$this->MODULO_AREA,'reg_nome'=>$nome);
			$dados = parent::sqlCRUD($array, $fildsToSelect, $this->TB_CONFIG, $messageLog, 'D', 0, 0);

	      ob_end_clean();

	     	if(isset($dados) && $dados !== NULL){
	     		Sis::setAlert("Dados removidos com sucesso!", 3, "?mod=" . $_GET['mod'] . "&pag=" . $_GET['pag']);
	     	} else {
	         Sis::setAlert("Ocorreu um erro ao remover dados!", 4);
	     	}
		}

		public function listAllLog($filtros="", $atualPageDi)
		{
	      if (is_array($filtros) && count($filtros) > 0) {
				$array = $filtros;
			}
			$array['nivel'] = 1;
			$array['orderby'] = 'Order by cdt.data DESC';
	     	$dados = parent::listAllLogM($array, $this->TB_LOG, $atualPageDi, 40);

			if(is_object($dados)){
				return $dados;
			} else  {
				return false;
			}
		}

		public function listLogLasts($limit=0)
		{
			$sql_query = 'Select tbLog.acao,tbLog.modulo_area,tbLog.registro_nome,tbLog.data,tbUserAdm.nome,tbUserAdm.email
							From '. $this->TB_LOG.' as tbLog
			           INNER JOIN '. $this->TB_USERS.' as tbUserAdm ON tbUserAdm.usuario_idx=tbLog.usuario_idx
			           Order by tbLog.data DESC LIMIT 0,5';
	     	$dados = parent::select($sql_query);
			if(is_array($dados) && count($dados)){
				return $dados;
			} else {
				return false;
			}
		}

		public function getUserName($uId=0)
		{
			$array = array(
	               'usuario_idx' => (isset($uId) && is_numeric($uId) && $uId != 0) ? $uId : 0
	      );
	     	$dados = parent::sqlCRUD($array, '', $this->TB_USERS, '', 'S', 0, 0);
	     	if(is_array($dados) && count($dados) > 0){
				return $dados[0]['nome'];
			} else {
				return false;
			}
		}

		public function getAllModules($mId=0)
		{
			$array = array('orderby' => 'Order by nome Asc');
			if($mId !== 0){
				$array['codigo'] = $mId;
			}
	     	$dados = parent::sqlCRUD($array, 'codigo, nome',  $this->TB_MODULO, '', 'S', 0, 0);
	     	if(is_array($dados) && count($dados) > 0){
	     		return $dados;
	     	}else{
	     		return FALSE;
	     	}
		}

		public function getAllUsers($uId=0)
		{
			$array = array('orderby' => 'Order by nome Asc');
			if($uId !== 0){
				$array['usuario_idx'] = $uId;
			}
	     	$dados = parent::sqlCRUD($array, 'usuario_idx, nome', $this->TB_USERS, '', 'S', 0, 0);
	     	if(is_array($dados) && count($dados) > 0){
	     		return $dados;
	     	}else{
	     		return FALSE;
	     	}
		}



		public function sisConfigInsert()
		{

			$dadonomes = $_POST['dadonome'];
			$dados = $_POST['dado'];

			/**
			 * Fazendo o upload da marca do cliente
			 */
			$arquivo = isset($_FILES["arquivo_logo"]) ? $_FILES["arquivo_logo"] : "";
			$nome_arquivo = "Null";
			if ($arquivo != ""){
				if (!empty($arquivo["name"])) {
					if(!preg_match("/^image\/(pjpeg|jpeg|png|gif|bmp|svg\+xml)$/", $arquivo["type"])){
						Sis::setAlert('O arquivo escolhido não é uma imagem!', 2);
					}
					$nome_arquivo = $arquivo["name"];
					preg_match("/\.(gif|bmp|png|jpg|jpeg|swf|svg){1}$/i", $nome_arquivo, $ext);
					$nome_arquivo = 'logomarca_cliente'.$ext[0];
					$local = ".." . DS . PASTA_DIRECTIN . DS . 'public' . DS . 'images' . DS . $nome_arquivo;
					if (file_exists($local)) {
						unlink($local);
					}
					move_uploaded_file($arquivo["tmp_name"], $local);
					$nome_arquivo = Text::clean($nome_arquivo);
				}
			}

			foreach($dadonomes as $dadonome){
				if($dadonome=="CLI_LOGO")
				{
					$chave = key($dadonomes);
					$dados[$chave] = $nome_arquivo;
				}
				next($dadonomes);
			}

			ob_clean();
			$dados = parent::sisConfigInsertM($dadonomes, $dados);

			ob_end_clean();
			if(isset($dados) && $dados !== NULL){
				Sis::setAlert("Dados salvos com sucesso!", 3, "?mod=" . $_GET['mod'] . "&pag=" . $_GET['pag'] . "&act=sis-list");
			} else {
				Sis::setAlert('Ocorreu um erro ao salvar os dados!', 4);
			}
		}

		public function sisConfigUpdate()
		{
			$dadonomes = $_POST['dadonome'];
			$dados = $_POST['dado'];

			$arquivo_ok = isset($_POST["logo_old"]) ? $_POST["logo_old"] : "";

			$arquivo = isset($_FILES["arquivo_logo"]) ? $_FILES["arquivo_logo"] : "";
			
			if ($arquivo != ""){
				if (!empty($arquivo["name"])) {
					if(!preg_match("/^image\/(pjpeg|jpeg|png|gif|bmp|svg\+xml)$/", $arquivo["type"])){
						Sis::setAlert('O arquivo escolhido não é uma imagem!', 2);
					}

					$nome_arquivo = $arquivo["name"];
					preg_match("/\.(gif|bmp|png|jpg|jpeg|swf|svg){1}$/i", $nome_arquivo, $ext);
					$nome_arquivo = 'logomarca_cliente'.$ext[0];
					$local = ".." . DS . PASTA_DIRECTIN . DS . 'public' . DS . 'images' . DS . $nome_arquivo;
					if (file_exists($local)) {
						unlink($local);
					}
					move_uploaded_file($arquivo["tmp_name"], $local);
					$arquivo_ok = Text::clean($nome_arquivo);
				}

				foreach($dadonomes as $dadonome){
					if($dadonome=="CLI_LOGO")
					{
						$chave = key($dadonomes);
						$dados[$chave] = $arquivo_ok;
					}
					next($dadonomes);
				}

			}

			ob_clean();
			$dados = parent::sisConfigUpdateM($dadonomes, $dados);

			ob_end_clean();
			if(isset($dados) && $dados !== NULL){
				Sis::setAlert("Dados salvos com sucesso!", 3, "?mod=" . $_GET['mod'] . "&pag=" . $_GET['pag'] . "&act=sis-list");
			} else  {
				Sis::setAlert('Ocorreu um erro ao salvar os dados!', 4);
			}
		}

		public function sisSelect()
		{
			$array = array(
	               'nivel' => 0
	      );
	     	$dados = parent::sqlCRUD($array, '', $this->TB_CONFIG, '', 'S', 0, 0);

			if(isset($dados) && $dados !== NULL){
				return $dados;
			} else  {
				return false;
			}
		}

		function recursiveArraySearch($needle,$haystack) {
	    foreach($haystack as $key=>$value) {
	        $current_key=$key;
	        if($needle===$value OR (is_array($value) && self::recursiveArraySearch($needle,$value))) {
	            return $current_key;
	        }
	    }
	    return false;
		}

		function retVarValue($var,$default=""){
			global $listVarSis;
			return (self::recursiveArraySearch($var, $listVarSis)!==false)? $listVarSis[self::recursiveArraySearch($var, $listVarSis)]['valor'] : $default ;
		}
	} //End class
?>