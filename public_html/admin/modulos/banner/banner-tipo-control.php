<?php
class banner_tipo extends banner_tipo_model{

	//Constantes de nome do banco do módulo
	public $mod, $pag, $act;

	public function __construct() {
		global $mod, $pag, $act;
		$this->mod = $mod;
		$this->pag = $pag;
		$this->act = $act;
		if(!is_dir("..".DS.PASTA_CONTENT.DS.$this->pag)){
			mkdir("..".DS.PASTA_CONTENT.DS.$this->pag);
		}
	}

	public function tipoListAll(){
		$array = array('orderby' => 'ORDER BY nome');
		$dados = parent::sqlCRUD($array, '', $this->TB_BANNER_TIPO, '', 'S', 0, 0);

		if(is_array($dados) && count($dados) > 0){
			return $dados;
		} else  {
			return false;
		}
	}

	public function tipoInsert()
	{
		$array = array(
	      'nome' 						=> isset($_POST['nome'])                 ? Text::clean($_POST['nome'])                	: "",
	      'largura' 					=> ($_POST['largura'] != "")             ? Text::clean((int)$_POST['largura'])             	: 0,
	      'altura' 					=> ($_POST['altura'] != "")              ? Text::clean((int)$_POST['altura'])              	: 0,
	      'animacao_tempo' 			=> ($_POST['animacao_tempo'] != "")      ? Text::clean((int)$_POST['animacao_tempo'])      	: 0,
	      'animacao_velocidade'	=> ($_POST['animacao_velocidade'] != "") ? Text::clean((int)$_POST['animacao_velocidade']) 	: 0,
	      'perfil' 					=> ($_POST['perfil'] != "") 				  ? Text::clean((int)$_POST['perfil']) 						: 0,
	      'animacao_array' 			=> isset($_POST['animacao'])             ? ($_POST['animacao'])            					: "",
	      'animacao' 					=> "",
		);
		$animacao = $array['animacao'];
		if(is_array($animacao))
		{
			$animacao = implode(",",$animacao);
		}
		$dados = parent::sqlCRUD($array, '', $this->TB_BANNER_TIPO, 'BANNER - TIPO - INSERIR', 'I', 0, 0);

		ob_end_clean();
		if(isset($dados) && $dados !== NULL){
			if ($dados == true){
				Sis::setAlert('Dados salvos com sucesso!', 3,"?mod=banner&pag=banner&act=tipo-list");
			}else{
				Sis::setAlert('O registro informado j&aacute; existe!', 4);
			}
		}else {
			Sis::setAlert('Ocorreu um erro ao salvar os dados!', 4);
		}
	}

	public function tipoDelete()
	{
		$tid = isset($_GET['t_id']) ? Text::clean((int)$_GET['t_id']) : "";
		$checkBanners = self::listAll($tid);
		if(is_array($checkBanners) && count($checkBanners)>0)
		{
			ob_end_clean();
			Sis::setAlert('Não foi possível remover. Existem banners relacionados a este tipo!', 1,"?mod=banner&pag=banner&act=tipo-list");
			exit();
		}
		$array = array(
	      'tipo_idx' => $tid
		);
		$dados = parent::sqlCRUD($array, '', $this->TB_BANNER_TIPO, 'BANNER - TIPO - EXCLUIR', 'D', 0, 0);
		ob_end_clean();
		if(isset($dados) && $dados !== NULL){
			Sis::setAlert('Dados removidos com sucesso!', 3,"?mod=banner&pag=banner&act=tipo-list");
		} else {
			Sis::setAlert('Ocorreu um erro ao remover os dados!', 4);
		}
	}

	public function listaTipoSel($id)
	{
		$array = array(
	      'tipo_idx' => $id
		);
		$dados = parent::sqlCRUD($array, '', $this->TB_BANNER_TIPO, '', 'S', 0, 0);
		if(isset($dados) && $dados !== NULL){
			return $dados;
		} else {
			return false;
		}
	}

	public function atualizarTipo() {
		$t_id                = isset($_POST['t_id'])                 ? Text::clean($_POST['t_id']) : 0;
		$nome                = isset($_POST['nome'])                 ? Text::clean($_POST['nome']) : "";
		$largura             = ($_POST['largura'] != "")             ? Text::clean($_POST['largura']) : 0;
		$altura              = ($_POST['altura'] != "")              ? Text::clean($_POST['altura']) : 0;
		$perfil = ($_POST['perfil'] != "") ? Text::clean($_POST['perfil']) : 0;
		$animacao_tempo      = ($_POST['animacao_tempo'] != "")      ? Text::clean($_POST['animacao_tempo']) : 0;
		$animacao_velocidade = ($_POST['animacao_velocidade'] != "") ? Text::clean($_POST['animacao_velocidade']) : 0;
		$animacao_array      = isset($_POST['animacao'])             ? ($_POST['animacao']) : "";
		$animacao            = "";

		foreach($animacao_array as $key => $value) {
			if ($key <> 0) $animacao .= ",";
			$animacao .= $value;
		}

		$dados = parent::mAtualizarTipo($t_id, $nome, $perfil, $largura, $altura, $animacao_tempo, $animacao_velocidade, $animacao);

		ob_end_clean();

		if(isset($dados) && $dados !== NULL){
			if ($dados == true) {
				$_SESSION['sis_mens'] = "Dados cadastrados com sucesso!";
				$_SESSION['sis_mens_tipo'] = 3;
				die("<script type='text/javascript'>window.location='?mod=" . $this->mod . "&pag=" . $this->pag . "&act=tipo.list';</script>");
			}else{
				$_SESSION['sis_mens'] = "O nome informado já existe!";
				$_SESSION['sis_mens_tipo'] = 2;
				die("<script type='text/javascript'>history.back();</script>");
			}
		} else {
			$_SESSION['sis_mens'] = "Ocorreu um erro ao cadastrar dados!";
			$_SESSION['sis_mens_tipo'] = 1;
			die("<script type='text/javascript'>history.back();</script>");
		}
	}

	public function listAll($tid=0){
		$tid = (int)round($tid);
		$array = array(
	      'tipo_idx' => $tid
		);
		$dados = parent::sqlCRUD($array, '', $this->TB_BANNER, '', 'S', 0, 0);
		if(isset($dados) && $dados !== NULL){
			return $dados;
		} else {
			return false;
		}
	}

	public function inserir() {

		$status                 = ($_POST['status'] != "") ? Text::clean($_POST['status']) : 0;
		$formato                = ($_POST['formato'] != "") ? Text::clean($_POST['formato']) : 0;
		$tipo                   = ($_POST['t_id'] != "") ? Text::clean($_POST['t_id']) : 0;
		$alinhamento                   = ($_POST['alinhamento'] != "") ? Text::clean($_POST['alinhamento']) : 0;
		$pagina                 = isset($_POST['pagina']) ? $_POST['pagina'] : "";
		$nome                   = isset($_POST['nome']) ? Text::clean($_POST['nome']) : "";
		$descricao              = isset($_POST['descricao']) ? Text::clean($_POST['descricao']) : "";
		$url                    = isset($_POST['url']) ? Text::clean($_POST['url']) : "";
		$alvo                   = isset($_POST['alvo']) ? Text::clean($_POST['alvo']) : "";
		$arquivo                = isset($_FILES["arquivo"]) ? $_FILES["arquivo"] : "";
		$horario                = ($_POST['horario'] != "") ? Text::clean($_POST['horario']) : 0;
		$horario_ini                = ($_POST['horario_ini'] != "") ? Text::clean($_POST['horario_ini']) : 0;
		$horario_fim               = ($_POST['horario_fim'] != "") ? Text::clean($_POST['horario_fim']) : 0;
		//$subtipo_list_banner               = ($_POST['subtipo_list_banner'] != "") ? Text::clean($_POST['subtipo_list_banner']) : 0;
		$indica_data            = isset($_POST['indica_data']) ? 1 : 0;

		$data_publicacao        = isset($_POST['data_publicacao'])     ? data::to_mysql(Text::clean($_POST['data_publicacao']),2) : "0000-00-00 00:00:00";
		$data_expiracao        = isset($_POST['data_expiracao'])     ? data::to_mysql(Text::clean($_POST['data_expiracao']),2) : "0000-00-00 00:00:00";

		//Páginas marcadas
		$pagina_opcoes = "";
		if($pagina !== ""){
			for($i=0;$i<count($pagina);$i++){
				$pagina_opcoes .= "-".$pagina[$i]."-,";
			}
		}

		if ($arquivo <> "") {
			if (!empty($arquivo["name"])) {

				// Verifica se o arquivo enviado é compativél com o formato escolhido
				if ($formato == '1') {
					if(!preg_match("/^image\/(pjpeg|jpeg|png|gif|bmp)$/", $arquivo["type"])){
						$_SESSION['sis_mens'] = "O arquivo escolhido não é uma imagem!";
						$_SESSION['sis_mens_tipo'] = 2;
						die("<script type='text/javascript'>history.back();</script>");
					}
				} elseif ($formato == '2') {
					if($arquivo["type"] != "image/swf"){
						$_SESSION['sis_mens'] = "O arquivo escolhido não é Adobe Flash!";
						$_SESSION['sis_mens_tipo'] = 2;
						die("<script type='text/javascript'>history.back();</script>");
					}
				}

				$nome_arquivo = $arquivo["name"];

				preg_match("/\.(gif|bmp|png|jpg|jpeg|swf){1}$/i", $nome_arquivo, $ext);

				$nome_arquivo = str_replace($ext[1], "", $nome_arquivo);

				$nome_arquivo = Text::clean($nome_arquivo) . "-" . rand() . "." . $ext[1];

				$local = ".." . DS . PASTA_CONTENT . DS . $this->mod . DS . $nome_arquivo;

				move_uploaded_file($arquivo["tmp_name"], $local);

				$arquivo = Text::clean($nome_arquivo);
			}
		}

		$dados = parent::inserir_m($status, $formato, $alinhamento, $tipo, $pagina_opcoes, $nome, $descricao, $url, $alvo, $arquivo, $horario,$horario_ini,$horario_fim, $indica_data, $data_publicacao, $data_expiracao, $tipo_list_banner);
		ob_end_clean();

		if(isset($dados) && $dados !== NULL){
			if ($dados == true) {
				$_SESSION['sis_mens'] = "Dados cadastrados com sucesso!";
				$_SESSION['sis_mens_tipo'] = 3;
				die("<script type='text/javascript'>window.location='?mod=" . $this->mod . "&pag=" . $this->pag . "&act=list&t_id=". $tipo ."';</script>");
			}else{
				$_SESSION['sis_mens'] = "O nome informado já existe!";
				$_SESSION['sis_mens_tipo'] = 2;
				die("<script type='text/javascript'>history.back();</script>");
			}
		} else {
			$_SESSION['sis_mens'] = "Ocorreu um erro ao cadastrar dados!";
			$_SESSION['sis_mens_tipo'] = 1;
			die("<script type='text/javascript'>history.back();</script>");
		}
	}

	public function listaSel($b_id){
		$b_id = (int)round($b_id);
		$dados = parent::mListaSel($b_id);

		if(isset($dados) && $dados !== NULL){
			return $dados;
		} else {
			return false;
		}
	}

	public function atualizar() {

		$status                 = ($_POST['status'] != "") ? Text::clean($_POST['status']) : 0;
		$formato                = ($_POST['formato'] != "") ? Text::clean($_POST['formato']) : 0;
		$tipo                   = ($_POST['t_id'] != "") ? Text::clean($_POST['t_id']) : 0;
		$alinhamento                   = ($_POST['alinhamento'] != "") ? Text::clean($_POST['alinhamento']) : 0;
		$b_id                   = ($_POST['b_id'] != "") ? Text::clean($_POST['b_id']) : 0;
		$pagina                 = isset($_POST['pagina']) ? $_POST['pagina'] : "";
		$nome                   = isset($_POST['nome']) ? Text::clean($_POST['nome']) : "";
		$descricao              = isset($_POST['descricao']) ? Text::clean($_POST['descricao']) : "";
		$url                    = isset($_POST['url']) ? Text::clean($_POST['url']) : "";
		$alvo                   = isset($_POST['alvo']) ? Text::clean($_POST['alvo']) : "";
		$arquivo                = isset($_FILES["arquivo"]) ? $_FILES["arquivo"] : "";
		$arquivo_cad            = isset($_POST["arquivo_cad"]) ? $_POST["arquivo_cad"] : "";
		$horario                = ($_POST['horario'] != "") ? Text::clean($_POST['horario']) : 0;
		$horario_ini                = ($_POST['horario_ini'] != "") ? Text::clean($_POST['horario_ini']) : 0;
		$horario_fim               = ($_POST['horario_fim'] != "") ? Text::clean($_POST['horario_fim']) : 0;
		$indica_data            = isset($_POST['indica_data']) ? 1 : 0;
		$data_publicacao        = isset($_POST['data_publicacao']) ? data::to_mysql(Text::clean($_POST['data_publicacao'])) : "";
		$data_expiracao         = isset($_POST['data_expiracao']) ? data::to_mysql(Text::clean($_POST['data_expiracao'])) : "";
		//         = isset($_POST['subtipo_list_banner']) ? Text::clean($_POST['subtipo_list_banner']) : 0;

		//Páginas marcadas
		$pagina_opcoes = "";
		if($pagina !== ""){
			for($i=0;$i<count($pagina);$i++){
				$pagina_opcoes .= "-".$pagina[$i]."-,";
			}
		}

		if ($arquivo["error"] <> 4) {
			if (!empty($arquivo["name"])) {
				// Verifica se o arquivo enviado é compativél com o formato escolhido
				if ($formato == '1') {
					if(!preg_match("/^image\/(pjpeg|jpeg|png|gif|bmp)$/", $arquivo["type"])){
						$_SESSION['sis_mens'] = "O arquivo escolhido não é uma imagem!";
						$_SESSION['sis_mens_tipo'] = 2;
						die("<script type='text/javascript'>history.back();</script>");
					}
				} elseif ($formato == '2') {
					if($arquivo["type"] != "image/swf"){
						$_SESSION['sis_mens'] = "O arquivo escolhido não é Adobe Flash!";
						$_SESSION['sis_mens_tipo'] = 2;
						die("<script type='text/javascript'>history.back();</script>");
					}
				}

				$nome_arquivo = $arquivo["name"];

				preg_match("/\.(gif|bmp|png|jpg|jpeg|swf){1}$/i", $nome_arquivo, $ext);

				$nome_arquivo = str_replace($ext[1], "", $nome_arquivo);

				$nome_arquivo = Text::clean($nome_arquivo) . "-" . rand() . "." . $ext[1];

				$local = ".." . DS . PASTA_CONTENT . DS . $this->mod . DS . $nome_arquivo;

				if($arquivo_cad <> ""){
					unlink(".." . DS . PASTA_CONTENT . DS . $this->mod . DS . $arquivo_cad);
				}

				move_uploaded_file($arquivo["tmp_name"], $local);

				$arquivo = Text::clean($nome_arquivo);

			}
		}else{
			$arquivo = $arquivo_cad;
		}

		$dados = parent::atualizar_m($b_id, $status, $formato, $tipo, $alinhamento, $pagina_opcoes, $nome, $descricao, $url, $alvo, $arquivo, $horario, $horario_ini, $horario_fim, $indica_data, $data_publicacao, $data_expiracao);
		ob_end_clean();

		if(isset($dados) && $dados !== NULL){
			if ($dados == true) {
				$_SESSION['sis_mens'] = "Dados cadastrados com sucesso!";
				$_SESSION['sis_mens_tipo'] = 3;
				die("<script type='text/javascript'>window.location='?mod=" . $this->mod . "&pag=" . $this->pag . "&act=list&t_id=". $tipo ."';</script>");
			}else{
				$_SESSION['sis_mens'] = "O nome informado já existe!";
				$_SESSION['sis_mens_tipo'] = 2;
				die("<script type='text/javascript'>history.back();</script>");
			}
		} else {
			$_SESSION['sis_mens'] = "Ocorreu um erro ao cadastrar dados!";
			$_SESSION['sis_mens_tipo'] = 1;
			die("<script type='text/javascript'>history.back();</script>");
		}
	}

	public function remover() {
		$t_id = isset($_GET['t_id']) ? Text::clean($_GET['t_id']) : "";
		$b_id = isset($_GET['b_id']) ? Text::clean($_GET['b_id']) : "";

		//Exclui a imagem do banner
		$img = parent::seleciona("Select arquivo From " . parent::TB_BANNER . " Where banner_idx=" . $b_id . "");
		if($img != false){
			$local = ".." . DS . PASTA_CONTENT . DS . $this->mod . DS . $img[0]['arquivo'];
			unlink($local);
		}

		$dados = parent::remover_m($b_id);

		ob_end_clean();

		if(isset($dados) && $dados !== NULL){
			$_SESSION['sis_mens'] = "Dados removidos com sucesso!";
			$_SESSION['sis_mens_tipo'] = 3;
			die("<script type='text/javascript'>window.location='?mod=" . $this->mod . "&pag=" . $this->pag . "&act=list&t_id=" . $t_id ."';</script>");
		} else {
			$_SESSION['sis_mens'] = "Ocorreu um erro ao remover dados!";
			$_SESSION['sis_mens_tipo'] = 1;
			die("<script type='text/javascript'>history.back();</script>");
		}
	}

}
