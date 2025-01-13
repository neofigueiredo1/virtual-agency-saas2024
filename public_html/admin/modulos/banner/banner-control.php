<?php
	class banner extends banner_model{

		public $mod, $pag, $act;

		public $MODULO_CODIGO 		= "10002";
		public $MODULO_AREA 			= "Banner";
		public $MODULO_AREA_TIPO 	= "Tipo de Banner";

		public function __construct() {
			parent::__construct();

			global $mod, $pag, $act;
			$this->mod = $mod;
			$this->pag = $pag;
			$this->act = $act;
			if(!is_dir("..".DS.PASTA_CONTENT.DS.$this->pag)){
				mkdir("..".DS.PASTA_CONTENT.DS.$this->pag);
			}
		}

		/**
		 * Retorna as informações para exibir na dashboard do sistema, até 2 linhas
		 */
		public function dashInfo()
		{
			$num_total=0;
			$num_online=0;
			$bannersAll = self::listAll(0,'banner_idx');
			$num_total = (is_array($bannersAll)&&count($bannersAll)>0)?count($bannersAll):0;
			$array = array('status' => 1);
			$bannersOnLine = parent::sqlCRUD($array, '', $this->TB_BANNER, '', 'S', 0, 0);
			$num_online = (is_array($bannersOnLine)&&count($bannersOnLine)>0)?count($bannersOnLine):0;
			$retorno = ($num_total>0)?" ".$num_online." banner(s) online de<br> ".$num_total." banners cadastrados":"Nenhum banner cadastrado!";
			return $retorno;
		}


		/**
		* Processa a lista completa dos registros de Tipo de Banner.
		* @return array, se a consulta for realizada com sucesso, caso contrário false
		*/
		public function tipoListAll(){
			$array = array('orderby' => 'ORDER BY nome');
			$dados = parent::sqlCRUD($array, '', $this->TB_BANNER_TIPO, '', 'S', 0, 0);

			if(is_array($dados) && count($dados) > 0){
				return $dados;
			} else  {
				return false;
			}
		}

		/**
		* Processa a inserção do registro de Tipo de Banner.
		*/
		public function tipoInsert(){
			$array = array(
		      	'nome' 						=> isset($_POST['nome'])                ? Text::clean($_POST['nome'])                	: "",
		      	'largura' 					=> isset($_POST['largura'])             ? Text::clean((int)$_POST['largura'])             	: 0,
		      	'altura' 					=> isset($_POST['altura'])              ? Text::clean((int)$_POST['altura'])              	: 0,
		      	'animacao_tempo' 			=> isset($_POST['animacao_tempo'])      ? Text::clean((int)$_POST['animacao_tempo'])      	: 0,
		      	'animacao_velocidade'	=> isset($_POST['animacao_velocidade']) ? Text::clean((int)$_POST['animacao_velocidade']) 	: 0,
		      	'perfil' 					=> isset($_POST['perfil']) 				 ? Text::clean((int)$_POST['perfil']) 						: 0,
				'animacao' 					=> "",
				'subtipo_list_banner' 	=> isset($_POST['subtipo_list_banner'])      ? Text::clean($_POST['subtipo_list_banner']): "",
				'descricao_secao' 	=> isset($_POST['descricao_secao'])                 ? Text::clean($_POST['descricao_secao']): ""
			);

			$animacao = (isset($_POST['animacao'])) ? $_POST['animacao'] : "";
			$array['animacao'] = (is_array($animacao))?implode(",",$animacao):Text::clean($animacao);

			$messageLog = array('modulo_codigo'=>$this->MODULO_CODIGO,'modulo_area'=>$this->MODULO_AREA_TIPO,'reg_nome'=>$array['nome']);
			$dados = parent::sqlCRUD($array, '', $this->TB_BANNER_TIPO, $messageLog, 'I', 0, 0);

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

		/**
		* Processa a exclusão do registro de Tipo de Banner.
		*/
		public function tipoDelete(){
			$tid = isset($_GET['tid']) ? Text::clean((int)$_GET['tid']) : "";
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

			$nomeTipo = self::tipoListSelected($array['tipo_idx'], 'nome');
			if(is_array($nomeTipo) && count($nomeTipo) > 0){
				$nomeTipo = $nomeTipo[0]['nome'];
			}

			$messageLog = array('modulo_codigo'=>$this->MODULO_CODIGO,'modulo_area'=>$this->MODULO_AREA_TIPO,'reg_nome'=>$nomeTipo);
			$dados = parent::sqlCRUD($array, '', $this->TB_BANNER_TIPO, $messageLog, 'D', 0, 0);
			ob_end_clean();
			if(isset($dados) && $dados !== NULL){
				Sis::setAlert('Dados removidos com sucesso!', 3,"?mod=banner&pag=banner&act=tipo-list");
			} else {
				Sis::setAlert('Ocorreu um erro ao remover os dados!', 4);
			}
		}

		/**
		* Seleciona o registro de um tipo de banner específico.
		* @return array, se a consulta for realizada com sucesso, caso contrário false
		*/
		public function tipoListSelected($id, $campos=""){
			$array = array(
		      'tipo_idx' => $id
			);
			$dados = parent::sqlCRUD($array, $campos, $this->TB_BANNER_TIPO, '', 'S', 0, 0);
			if(isset($dados) && $dados !== NULL){
				return $dados;
			} else {
				return false;
			}
		}

		/**
		* Processa a atualização do registro de Tipo de Banner.
		*/
		public function tipoUpdate(){
			$array = array(
		      'tipo_idx'					=> isset($_POST['tid'])                 ? Text::clean((int)$_POST['tid'])                	: 0,
		      'nome' 						=> isset($_POST['nome'])                 ? Text::clean($_POST['nome'])                			: "",
		      'largura' 					=> isset($_POST['largura'])              ? Text::clean((int)$_POST['largura'])             	: 0,
		      'altura' 					=> isset($_POST['altura'])               ? Text::clean((int)$_POST['altura'])              	: 0,
		      'animacao_tempo' 			=> isset($_POST['animacao_tempo'])       ? Text::clean((int)$_POST['animacao_tempo'])      	: 0,
		      'animacao_velocidade'	=> isset($_POST['animacao_velocidade'])  ? Text::clean((int)$_POST['animacao_velocidade']) 	: 0,
		      'perfil' 					=> isset($_POST['perfil']) 				  ? Text::clean((int)$_POST['perfil']) 					: 0,
		      'animacao' 					=> "",
			  'subtipo_list_banner' 	=> isset($_POST['subtipo_list_banner'])                 ? Text::clean($_POST['subtipo_list_banner']): "",
			  'descricao_secao' 	=> isset($_POST['descricao_secao'])                 ? Text::clean($_POST['descricao_secao']): ""
			);
			$animacao = (isset($_POST['animacao'])) ? $_POST['animacao'] : "";
			$array['animacao'] = (is_array($animacao))?implode(",",$animacao):Text::clean($animacao);

			$messageLog = array('modulo_codigo'=>$this->MODULO_CODIGO,'modulo_area'=>$this->MODULO_AREA_TIPO,'reg_nome'=>$array['nome']);
			$dados = parent::sqlCRUD($array, '', $this->TB_BANNER_TIPO, $messageLog, 'U', 0, 0);

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

		/**
		* Lista as páginas do módulo de contéudo
		* @return array, se a consulta for realizada com sucesso, caso contrário false
		*/
		public function listContentPages($mae_id=0)
		{
			$array = array(
		      'orderby' => ' Order By indice ASC',
		      'status' => 1,
		      'pagina_mae' => $mae_id
			);
			$dados = parent::sqlCRUD($array, 'titulo, pagina_idx', $this->TB_CONTEUDO_PAGINA, '', 'S', 0, 0);
			if(isset($dados) && $dados !== NULL){
				return $dados;
			} else {
				return false;
			}
		}

		/**
		* Processa a lista completa dos registros de Banners.
		* @return array, se a consulta for realizada com sucesso, caso contrário false
		*/
		public function listAll($tid=0,$campos='')
		{
			$tid = (int)round($tid);
			$array = array(
		      'orderby' => " Order By ranking DESC"
			);
			if($tid != 0){
				$array['tipo_idx'] = $tid;
			}
			$dados = parent::sqlCRUD($array, $campos, $this->TB_BANNER, '', 'S', 0, 0);
			if(isset($dados) && $dados !== NULL){
				return $dados;
			} else {
				return false;
			}
		}

		/**
		* Processa a inserção do registro de Banner.
		*/
		public function theInsert()
		{
			$tid = isset($_POST['tid'])?(int)($_POST['tid']) : 0;
			$pagina = isset($_POST['pagina']) ? $_POST['pagina'] : "";
			$formato = isset($_POST['formato']) ? (int)($_POST['formato']) : 0;

			$array = array(
			   'status'          => isset($_POST['status']) ? (int)($_POST['status']) : 0,
			   'monitor_impressao' => isset($_POST['monitor_impressao']) ? (int)($_POST['monitor_impressao']) : 0,
			   'monitor_clique'  => isset($_POST['monitor_clique']) ? (int)($_POST['monitor_clique']) : 0,
				'ranking'         => 0,
				'formato'         => $formato,
				'tipo_idx'        => $tid,
				'alinhamento'     => isset($_POST['alinhamento']) ? Text::clean($_POST['alinhamento']) : 0,
				'pagina'          => "",
				'nome'            => isset($_POST['nome']) ? Text::clean($_POST['nome']) : "",
				'descricao'       => isset($_POST['descricao']) ? Text::clean($_POST['descricao']) : "",
				'url'             => isset($_POST['url']) ? Text::clean($_POST['url']) : "",
				'alvo'            => isset($_POST['alvo']) ? Text::clean($_POST['alvo']) : "",
				'arquivo'         => "Null",
				'horario'         => isset($_POST['horario']) ? Text::clean($_POST['horario']) : 0,
				'horario_ini'     => isset($_POST['horario_ini']) ? Text::clean($_POST['horario_ini']) : 0,
				'horario_fim'     => isset($_POST['horario_fim']) ? Text::clean($_POST['horario_fim']) : 0,
				'indica_data'     => isset($_POST['indica_data']) ? 1 : 0,
				'data_publicacao' => isset($_POST['data_publicacao']) ? Date::toMysql(Text::clean($_POST['data_publicacao']),2) : "0000-00-00 00:00:00",
				'data_expiracao'  => isset($_POST['data_expiracao']) ? Date::toMysql(Text::clean($_POST['data_expiracao']),2) : "0000-00-00 00:00:00",
				'video_url'       => isset($_POST['video_url']) ? Text::clean($_POST['video_url']) : "",
				'subtipo_banner'       => isset($_POST['subtipo_banner']) ? Text::clean($_POST['subtipo_banner']) : 0
			);

			//Páginas marcadas
			$pagina_opcoes = "";
			if($pagina !== ""){
				for($i=0;$i<count($pagina);$i++){
					$pagina_opcoes .= "-".$pagina[$i]."-,";
				}
			}
			$array['pagina'] = $pagina_opcoes;

			$arquivo = isset($_FILES["arquivo"]) ? $_FILES["arquivo"] : "";
			if ($arquivo <> ""){
				if (!empty($arquivo["name"])) {
					// Verifica se o arquivo enviado é compativél com o formato escolhido
					if ($formato == '1') {
						if(!preg_match("/^image\/(pjpeg|jpeg|png|gif|svg|bmp)$/", $arquivo["type"])){
							Sis::setAlert('O arquivo escolhido não é uma imagem!', 2);
						}
					} elseif ($formato == '2') {
						if($arquivo["type"] != "image/swf"){
							Sis::setAlert('O arquivo escolhido não é Adobe Flash!', 2);
						}
					}
					$nome_arquivo = $arquivo["name"];
					preg_match("/\.(gif|bmp|png|jpg|jpeg|svg|swf){1}$/i", $nome_arquivo, $ext);
					$nome_arquivo = str_replace(".".$ext[1], "", $nome_arquivo);
					$nome_arquivo = Text::clean($nome_arquivo) . "-" . rand() . "." . $ext[1];
					$local = "..".DS.PASTA_CONTENT.DS.$this->pag.DS.$nome_arquivo;
					move_uploaded_file($arquivo["tmp_name"], $local);
					$arquivo = $nome_arquivo;
				}
			}
			$array['arquivo'] = $arquivo;

			$messageLog = array('modulo_codigo'=>$this->MODULO_CODIGO,'modulo_area'=>$this->MODULO_AREA,'reg_nome'=>$array['nome']);
			$dados = parent::sqlCRUD($array, '', $this->TB_BANNER, $messageLog, 'I', 0, 0);

			ob_end_clean();
			if(isset($dados) && $dados !== NULL){
				if ($dados == true){
					Sis::setAlert('Dados salvos com sucesso!', 3,"?mod=banner&pag=banner&tid=".$tid);
				}else{
					Sis::setAlert('O registro informado j&aacute; existe!', 4);
				}
			}else {
				Sis::setAlert('Ocorreu um erro ao salvar os dados!', 4);
			}
		}

		public function listSelected($id, $campos="")
		{
			$array = array(
		      'banner_idx' => $id
			);
			$dados = parent::sqlCRUD($array, '', $this->TB_BANNER, '', 'S', 0, 0);
			if(isset($dados) && $dados !== NULL){
				return $dados;
			} else {
				return false;
			}
		}

		public function theUpdate()
		{
			$bid = ($_POST['bid'] != "") ? Text::clean($_POST['bid']) : 0;
			$tid = isset($_POST['tid'])?(int)($_POST['tid']) : 0;
			$pagina = isset($_POST['pagina']) ? $_POST['pagina'] : "";
			$formato = isset($_POST['formato']) ? (int)($_POST['formato']) : 0;
			$arquivo_cad = isset($_POST["arquivo_cad"]) ? Text::clean($_POST["arquivo_cad"]) : "";

			$array = array(
			   'banner_idx'      => round($bid),
			   'status'          => isset($_POST['status']) ? (int)($_POST['status']) : 0,
			   'monitor_impressao' => isset($_POST['monitor_impressao']) ? (int)($_POST['monitor_impressao']) : 0,
			   'monitor_clique'  => isset($_POST['monitor_clique']) ? (int)($_POST['monitor_clique']) : 0,
				'formato'         => $formato,
				'tipo_idx'        => $tid,
				'alinhamento'     => isset($_POST['alinhamento']) ? Text::clean($_POST['alinhamento']) : 0,
				'pagina'          => "",
				'nome'            => isset($_POST['nome']) ? Text::clean($_POST['nome']) : "",
				'descricao'       => isset($_POST['descricao']) ? Text::clean($_POST['descricao']) : "",
				'url'             => isset($_POST['url']) ? Text::clean($_POST['url']) : "",
				'alvo'            => isset($_POST['alvo']) ? Text::clean($_POST['alvo']) : "",
				'arquivo'         => $arquivo_cad,
				'horario'         => isset($_POST['horario']) ? Text::clean($_POST['horario']) : 0,
				'horario_ini'     => isset($_POST['horario_ini']) ? Text::clean($_POST['horario_ini']) : 0,
				'horario_fim'     => isset($_POST['horario_fim']) ? Text::clean($_POST['horario_fim']) : 0,
				'indica_data'     => isset($_POST['indica_data']) ? 1 : 0,
				'data_publicacao' => isset($_POST['data_publicacao']) ? Date::toMysql(Text::clean($_POST['data_publicacao']),2) : "0000-00-00 00:00:00",
				'data_expiracao'  => isset($_POST['data_expiracao']) ? Date::toMysql(Text::clean($_POST['data_expiracao']),2) : "0000-00-00 00:00:00",
				'video_url'       => isset($_POST['video_url']) ? Text::clean($_POST['video_url']) : "",
				'subtipo_banner' => isset($_POST['subtipo_banner']) ? Text::clean($_POST['subtipo_banner']) : 0
			);
			//Páginas marcadas
			$pagina_opcoes = "";
			if($pagina !== ""){
				for($i=0;$i<count($pagina);$i++){
					$pagina_opcoes .= "-".$pagina[$i]."-,";
				}
			}
			$array['pagina'] = $pagina_opcoes;

			$arquivo = isset($_FILES["arquivo"]) ? $_FILES["arquivo"] : "";
			if ($arquivo["error"] <> 4) {
				if (!empty($arquivo["name"])) {
					// Verifica se o arquivo enviado é compativél com o formato escolhido
					if ($formato == '1') {
						if(!preg_match("/^image\/(pjpeg|jpeg|png|gif|svg|bmp)$/", $arquivo["type"])){
							Sis::setAlert('O arquivo escolhido não é uma imagem!', 2);
						}
					} elseif ($formato == '2') {
						if($arquivo["type"] != "image/swf"){
							Sis::setAlert('O arquivo escolhido não é Adobe Flash!', 2);
						}
					}
					$nome_arquivo = $arquivo["name"];
					preg_match("/\.(gif|bmp|png|jpg|jpeg|svg|swf){1}$/i", $nome_arquivo, $ext);
					$nome_arquivo = str_replace(".".$ext[1], "", $nome_arquivo);
					$nome_arquivo = Text::clean($nome_arquivo) . "-" . rand() . "." . $ext[1];
					$local = "..".DS.PASTA_CONTENT.DS.$this->pag.DS.$nome_arquivo;
					if($arquivo_cad <> ""){
						if (file_exists(".." . DS . PASTA_CONTENT . DS . $this->mod . DS . $arquivo_cad))
						{
							unlink(".." . DS . PASTA_CONTENT . DS . $this->mod . DS . $arquivo_cad);
						}
					}
					move_uploaded_file($arquivo["tmp_name"], $local);
					$arquivo = $nome_arquivo;

					$array['arquivo'] = $arquivo;

				}
			}

			$messageLog = array('modulo_codigo'=>$this->MODULO_CODIGO,'modulo_area'=>$this->MODULO_AREA,'reg_nome'=>$array['nome']);
			$dados = parent::sqlCRUD($array, '', $this->TB_BANNER, $messageLog, 'U', 0, 0);

			ob_end_clean();
			if(isset($dados) && $dados !== NULL){
				if ($dados == true){
					Sis::setAlert('Dados salvos com sucesso!', 3,"?mod=banner&pag=banner&tid=".$tid);
				}else{
					Sis::setAlert('O registro informado j&aacute; existe!', 4);
				}
			}else {
				Sis::setAlert('Ocorreu um erro ao salvar os dados!', 4);
			}
		}

		public function theDelete()
		{
			$tid = isset($_GET['tid']) ? Text::clean((int)$_GET['tid']) : "";
			$bid = isset($_GET['bid']) ? Text::clean($_GET['bid']) : "";

			$bImg = self::listSelected($bid);
			$nome = "";
			if(is_array($bImg) && count($bImg)>0)
			{
				$local = ".." . DS . PASTA_CONTENT . DS . $this->mod . DS . $bImg[0]['arquivo'];
				if(file_exists($local)){ unlink($local); }
				$nome = $bImg[0]['nome'];
			}
			$array = array(
		      'banner_idx' => $bid
			);
			$messageLog = array('modulo_codigo'=>$this->MODULO_CODIGO,'modulo_area'=>$this->MODULO_AREA,'reg_nome'=>$nome);
			$dados = parent::sqlCRUD($array, '', $this->TB_BANNER, $messageLog, 'D', 0, 0);
			ob_end_clean();
			if(isset($dados) && $dados !== NULL){
				Sis::setAlert('Dados removidos com sucesso!', 3,"?mod=banner&pag=banner&tid=".$tid);
			} else {
				Sis::setAlert('Ocorreu um erro ao remover os dados!', 4);
			}
		}

		public function transformTipoBanner($strTipoBanner="", $tipoBanner=0) {
			$str = explode("!", $strTipoBanner);
			
			if($tipoBanner!=0) {
				$listaSubtipo = parent::select("SELECT subtipo_list_banner FROM ".$this->TB_BANNER_TIPO." WHERE tipo_idx=".$tipoBanner);
				$str = explode("!", $listaSubtipo[0]["subtipo_list_banner"]);
			}
			
			$listaSubtipo = array();
			foreach($str as $key => $tipo) {
				if($tipo!="") {
					$listaSubtipo[$key+1] = array(
						'tipo_idx'=>$tipoBanner,
						'subtipo_idx'=>$key+1,
						'subtipo_nome'=>trim($tipo)
					);
				}
			}
			return $listaSubtipo;
		}

	} //End Class
?>