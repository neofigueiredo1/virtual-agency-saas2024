<?php
	/**
	 * Classe de gerenciamento de dados dos cadastros
	 *
	 * @package cadastro
	 **/
	class cadastro extends cadastro_m {

		public $MODULO_CODIGO 		= "10013";
		public $MODULO_AREA 		= "Cadastro";
		public $mod, $pag, $act, $perfis, $pasta_modulo;

		public function __construct() {
			
			parent::__construct();
			
			global $act;
			$this->mod = "cadastro";
			$this->pag = "cadastro";
			$this->act = $act;
			$this->perfis = Array('Aluno','Produtor','Co-produtor');

			$basedir = BASE_PATH.DS.PASTA_CONTENT;
			$this->pasta_modulo = $basedir.DS.$this->mod.DS;

			if(!is_dir($this->pasta_modulo)){
			   mkdir($this->pasta_modulo);
			}

		}

		public function listAll($filtros="", $atualPageDi=0, $totalPages=0, $campos="") {
			
			if($totalPages==0){
				$totalPages = 20;
			}

			if (is_array($filtros) && count($filtros) > 0) {
				$array = $filtros;
			}

			$array['orderby'] = 'ORDER BY data_cadastro DESC';

			$dados = parent::listAllM($array, $this->TB_CADASTRO, $this->TB_AREA_SELECIONA, $atualPageDi, $totalPages, $campos);

			/**
			* Se os campos vierem armazenados em um array,
			* é porque está sendo feita uma exportação dos cadastros
			*/
			if(is_array($campos) && count($campos) > 0){
		      	if(is_array($dados) && count($dados) > 0){

		      		$iCount = 0;
		      		$bCount = -1;

		      		$fileName = "exportacao-directin-".rand().".xls";
			      	$exportText = '<table style="font-family: Arial; font-size: 14px;" border="1"><tr>';

			      	foreach ($campos as $key => $campo) {
			      		$exportText .=	'<th>'.$campos[$iCount].'</th>';
		      	 		$iCount++;
			      	}

			      	$path 	= BASE_PATH.DS.PASTA_CONTENT.DS.$_GET['mod'].DS.$fileName;
	         		$handle 	= fopen($path, "wb");
		            fwrite($handle,$exportText);

		            $exportText = "";
			      	foreach ($dados as $key => $dado) {
				      	$exportText .= '</tr><tr>';
				      	foreach ($campos as $key => $campo) {
				      	 	$bCount++;
				      	 	$contentTd = "";
				      	 	/**
				      	 	 * Verifica se é masculino ou feminino
				      	 	 */
				      	 	if($campos[$bCount] !== "genero"){
				      	 		$contentTd = $dado[$campos[$bCount]];
				      	 	}else if($dado[$campos[$bCount]] == "1"){
				      	 		$contentTd = "M";
				      	 	}else{
				      	 		$contentTd = "F";
				      	 	}
				      		$exportText .=	'<td>'.$contentTd.'</td>';
				      		if($bCount == count($campos)-1){
				      		  	$exportText .= '</tr><tr>';
				      		  	$bCount = -1;
				      		  	next($campos);
				      		}
				      	}
				      }

	   				$exportText .= '</tr></table>';

				      fwrite($handle,$exportText);
				      return $fileName;
			      }
		      	return $exportText;
	      	}else{
				return $dados;
	      	}
		}


		public function listSelected($id = 0, $campos="") {
			$id = (isset($id) && is_numeric($id)) ? $id : 0;
			$array = array('cadastro_idx' => $id);
	      $dados = parent::sqlCRUD($array, $campos, $this->TB_CADASTRO, '', 'S', 0, 0);

			if(is_array($dados) && count($dados)){
				return $dados;
			} else {
				return false;
			}
		}


		public function listInterest() {
			$array = array('orderby' => '');

	      $dados = parent::sqlCRUD($array, '', $this->TB_AREA_INTERESSE, '', 'S', 0, 0);

			if(is_array($dados) && count($dados)){
				return $dados;
			} else {
				return false;
			}
		}


		public function listLastInsert() {
			$array = array('orderby' => 'ORDER by data_cadastro DESC', 'status' => 1);

	      $dados = parent::sqlCRUD($array, '', $this->TB_CADASTRO, '', 'S', 0, 0);

			if(is_array($dados) && count($dados)){
				return $dados;
			} else {
				return false;
			}
		}


		public function areaInteresseInsert($usuarioIdx, $area) {
			$usuarioIdx = (isset($usuarioIdx) 	&& is_numeric($usuarioIdx)) ? $usuarioIdx : 0;
			$area 		= (isset($area) 			&& is_numeric($area)) ? $area : 0;
			$array = array(
			         'cadastro_idx' 	=> $usuarioIdx,
			         'interesse_idx' 	=> $area
			);

			// $messageLog = array('modulo_codigo'=>$this->MODULO_CODIGO,'modulo_area'=>$this->MODULO_AREA_INTERESSE,'reg_nome'=>$array['nome']);
	      $dados = parent::sqlCRUD($array, '', $this->TB_AREA_SELECIONA, '', 'I', 0, 0);

			if(isset($dados) && $dados != FALSE){
				return true;
			} else {
				return false;
			}
		}


		public function listUserSeleciona($id = 0, $campos="") {
			$id = (isset($id) && is_numeric($id)) ? $id : 0;
			$array = array('cadastro_idx' => $id);
	      $dados = parent::sqlCRUD($array, $campos, $this->TB_AREA_SELECIONA, '', 'S', 0, 0);

			if(is_array($dados) && count($dados)){
				return $dados;
			} else {
				return false;
			}
		}


		public function theInsert() {

			$afiliado_ativo = isset($_POST['afiliado_ativo']) ? (int)$_POST['afiliado_ativo'] : 0 ;

			$codigoNotIsUniq = true;
            while($codigoNotIsUniq){
                $codigo = uniqid();
                //Valida Código.
                $resultCheckCodigo = parent::sqlCRUD(array('afiliado_codigo'=>$codigo,'orderby'=>' LIMIT 0,1'), 'afiliado_codigo', $this->TB_CADASTRO, '', 'S', 0, 0);
                $codigoNotIsUniq = (is_array($resultCheckCodigo)&&count($resultCheckCodigo)>0);
            }
            $afiliado_codigo = $codigoNotIsUniq;

			$certificado_emitir = isset($_POST['certificado_emitir']) ? (int)$_POST['certificado_emitir'] : 0 ;

			$status    		  	= isset($_POST['status'])    ? (int)$_POST['status']             				: 0 ;
			$perfil    		  	= isset($_POST['perfil'])    ? (int)$_POST['perfil']             				: 0 ;
			$nome_completo    = isset($_POST['nome_completo'])   ? Text::clean($_POST['nome_completo']) 	: '';
			$nome_informal    = isset($_POST['nome_informal']) ? Text::clean($_POST['nome_informal'])   	: '';
			$genero			  	= isset($_POST['genero'])    ? (int)$_POST['genero']             				: 0 ;
			$data_nasc		  	= isset($_POST['data_nasc']) ? Text::clean($_POST['data_nasc'])       		: '';
			$email			  	= isset($_POST['email']) ? Text::clean($_POST['email'])       					: '';
			$senha			  	= isset($_POST['senha']) ? md5(Text::clean($_POST['senha']))       			: '';
			$telefone_resid	= isset($_POST['telefone_resid']) ? Text::clean($_POST['telefone_resid']) 	: '';
			$telefone_comer   = isset($_POST['telefone_comer']) ? Text::clean($_POST['telefone_comer']) 	: '';
			$celular		  		= isset($_POST['celular']) ? Text::clean($_POST['celular'])       			: '';
			$endereco		  	= isset($_POST['endereco']) ? Text::clean($_POST['endereco'])       			: '';
			$numero			  	= isset($_POST['numero']) ? Text::clean($_POST['numero'])       				: '';
			$complemento	  	= isset($_POST['complemento']) ? Text::clean($_POST['complemento'])       	: '';
			$bairro			  	= isset($_POST['bairro']) ? Text::clean($_POST['bairro'])       				: '';
			$cep			  	= isset($_POST['cep']) ? Text::clean($_POST['cep'])       						: '';
			$cpf_cnpj			= isset($_POST['cpf_cnpj']) ? Text::clean($_POST['cpf_cnpj'])       						: '';
			$cidade			  	= isset($_POST['cidade']) ? Text::clean($_POST['cidade'])       				: '';
			$estado			  	= isset($_POST['estado']) ? Text::clean($_POST['estado'])       				: '';
			$pais			  		= isset($_POST['pais']) ? Text::clean($_POST['pais'])       					: '';
			$receber_boletim  = isset($_POST['receber_boletim'])?(int)$_POST['receber_boletim']				:0;

			$lp_active = isset($_POST['lp_active']) ? (int)$_POST['lp_active'] : 0 ;
			$lp_quota_mb = isset($_POST['lp_quota_mb']) ? (int)$_POST['lp_quota_mb'] : 0 ;

			$iugu_split_account_id = isset($_POST['iugu_split_account_id']) ? Text::clean($_POST['iugu_split_account_id']) : '';
			$iugu_split_subcount_name = isset($_POST['iugu_split_subcount_name']) ? Text::clean($_POST['iugu_split_subcount_name']) : '';
			$iugu_split_user_token = isset($_POST['iugu_split_user_token']) ? Text::clean($_POST['iugu_split_user_token']) : '';
			$iugu_split_live_api_token = isset($_POST['iugu_split_live_api_token']) ? Text::clean($_POST['iugu_split_live_api_token']) : '';
			$iugu_split_test_api_token = isset($_POST['iugu_split_test_api_token']) ? Text::clean($_POST['iugu_split_test_api_token']) : '';

			$array = array(
				'certificado_emitir' => $certificado_emitir,
				'status' 			=> $status,
				'lp_active' 		=> $lp_active,
				'lp_quota_mb' 		=> $lp_quota_mb,
				'perfil' 			=> $perfil,
				'nome_completo' 	=> $nome_completo,
				'nome_informal' 	=> $nome_informal,
				'genero' 			=> $genero,
				'data_nasc' 		=> $data_nasc,
				'email' 			=> $email,
				'senha' 			=> $senha,
				'telefone_resid' 	=> $telefone_resid,
				'telefone_comer'	=> $telefone_comer,
				'celular'			=> $celular,
				'endereco' 			=> $endereco,
				'numero' 			=> $numero,
				'complemento' 		=> $complemento,
				'bairro' 			=> $bairro,
				'cep' 				=> $cep,
				'cpf_cnpj' 			=> $cpf_cnpj,
				'cidade' 			=> $cidade,
				'estado' 			=> $estado,
				'pais' 				=> $pais,
				'iugu_split_account_id' => $iugu_split_account_id,
				'iugu_split_subcount_name' => $iugu_split_subcount_name,
				'iugu_split_user_token' => $iugu_split_user_token,
				'iugu_split_live_api_token' => $iugu_split_live_api_token,
				'iugu_split_test_api_token' => $iugu_split_test_api_token,
				'receber_boletim' => $receber_boletim,
				'afiliado_ativo' => $afiliado_ativo,
				'afiliado_codigo' => $afiliado_codigo,
				'certificado_logo'=> 'null',
				'certificado_assina'=> 'null'
			);

			//Registra as imagens enviadas
			$arquivo_certificado_logo = $_FILES['certificado_logo'];
			if(is_array($arquivo_certificado_logo)){
				if(trim($arquivo_certificado_logo['name']) != ''){
					
					$filePath = pathinfo($arquivo_certificado_logo['name']);
					$ext = $filePath['extension'];
					$fileName = $filePath['filename'];

					$extCheck = self::checkFileType($arquivo_certificado_logo['name']);
					$nome_arquivo = 'cert_logo_' . date("dmYHis_") . Text::friendlyUrl(strtolower(Text::clean($fileName))) .".".$ext;
					//Verificando se a extensão do arquivo é compatível
					if($extCheck !== false){
						//armazenando o arquivo na pasta de destino do modulo
						if(move_uploaded_file($arquivo_certificado_logo['tmp_name'], $this->pasta_modulo.$nome_arquivo)){
							$array['certificado_logo'] = $nome_arquivo;
						}
					}
				}
			}

			//Registra as imagens enviadas
			$arquivo_certificado_assina = $_FILES['certificado_assina'];
			if(is_array($arquivo_certificado_assina)){
				if(trim($arquivo_certificado_assina['name']) != ''){
					$filePath = pathinfo($arquivo_certificado_assina['name']);
					$ext = $filePath['extension'];
					$fileName = $filePath['filename'];

					$extCheck = self::checkFileType($arquivo_certificado_assina['name']);
					$nome_arquivo = 'cert_assi_' . date("dmYHis_") . Text::friendlyUrl(strtolower(Text::clean($fileName))) .".".$ext;
					//Verificando se a extensão do arquivo é compatível
					if($extCheck !== false){
						//armazenando o arquivo na pasta de destino do modulo
						if(move_uploaded_file($arquivo_certificado_assina['tmp_name'], $this->pasta_modulo.$nome_arquivo)){
							$array['certificado_assina'] = $nome_arquivo;
						}
					}
				}
			}

			$messageLog = array('modulo_codigo'=>$this->MODULO_CODIGO,'modulo_area'=>$this->MODULO_AREA,'reg_nome'=>$array['nome_completo']);
			$dados = parent::sqlCRUD($array, '', $this->TB_CADASTRO, $messageLog, 'I', 0, 0);

			//Recupera id do último registro cadastrado
			$recuperaId = self::listLastInsert();
			$usuarioidx = $recuperaId[0]['cadastro_idx'];

			//Cadastro das áreas de interesse
			$area_interesse = isset($_POST['area_interesse']) ? $_POST['area_interesse'] : 0;

			if($area_interesse != 0){
				foreach($area_interesse as $area){
					self::areaInteresseInsert($usuarioidx, $area);
				}
			}

			if (ob_get_contents()) ob_end_clean();

			if(isset($dados) && $dados !== NULL){
				Sis::setAlert('Dados cadastrados com sucesso!', 3, '?mod=cadastro&pag=cadastro');
			} else {
				Sis::setAlert('Ocorreu um erro ao salvar os dados!', 4);
			}
		}



		public function theUpdate() {


			$id = isset($_POST['id']) ? (int)$_POST['id'] : 0 ;

			$afiliado_ativo = isset($_POST['afiliado_ativo']) ? (int)$_POST['afiliado_ativo'] : 0 ;
			$afiliado_codigo = isset($_POST['afiliado_codigo']) ? Text::clean($_POST['afiliado_codigo']) : '';

			if (trim($afiliado_codigo)!='') {
				$resultCheckCodigo = parent::select("SELECT afiliado_codigo FROM ".$this->TB_CADASTRO." WHERE cadastro_idx<>".(int)$id." And afiliado_codigo Like '".trim($afiliado_codigo)."' ");
				if (is_array($resultCheckCodigo)&&count($resultCheckCodigo)>0){
					ob_end_clean();
					Sis::setAlert("O código de afiliado informado já existe no sistema!", 4);
					exit();
				}
			}

			if (trim($afiliado_codigo)=='' && $afiliado_ativo==1) {
				
				$codigoNotIsUniq = true;
	            while($codigoNotIsUniq){
	                $codigo = uniqid();
	                //Valida Código.
	                $resultCheckCodigo = parent::sqlCRUD(array('afiliado_codigo'=>$codigo,'orderby'=>' LIMIT 0,1'), 'afiliado_codigo', $this->TB_CADASTRO, '', 'S', 0, 0);
	                $codigoNotIsUniq = (is_array($resultCheckCodigo)&&count($resultCheckCodigo)>0);
	            }
	            $afiliado_codigo = $codigo;

			}

			$certificado_emitir = isset($_POST['certificado_emitir']) ? (int)$_POST['certificado_emitir'] : 0 ;

			$status    		  	= isset($_POST['status'])    ? (int)$_POST['status']             				: 0 ;
			// $perfil    		  	= isset($_POST['perfil'])    ? (int)$_POST['perfil']             				: 0 ;
			$nome_completo    	= isset($_POST['nome_completo'])   ? Text::clean($_POST['nome_completo']) 	: '';
			$nome_informal    	= isset($_POST['nome_informal']) ? Text::clean($_POST['nome_informal'])   	: '';
			$genero			  	= isset($_POST['genero'])    ? (int)$_POST['genero']             				: 0 ;
			$data_nasc		  	= isset($_POST['data_nasc']) ? Text::clean($_POST['data_nasc'])       		: '';
			$email			  	= isset($_POST['email']) ? Text::clean($_POST['email'])       					: '';
			$senha			  	= isset($_POST['senha']) ? Text::clean($_POST['senha'])       					: '';
			$senha_confirm	  	= isset($_POST['senha_confirm']) ? Text::clean($_POST['senha_confirm'])		: '';
			$telefone_resid		= isset($_POST['telefone_resid']) ? Text::clean($_POST['telefone_resid']) 	: '';
			$telefone_comer   	= isset($_POST['telefone_comer']) ? Text::clean($_POST['telefone_comer']) 	: '';
			$celular		  	= isset($_POST['celular']) ? Text::clean($_POST['celular'])       			: '';
			$endereco		  	= isset($_POST['endereco']) ? Text::clean($_POST['endereco'])       			: '';
			$numero			  	= isset($_POST['numero']) ? Text::clean($_POST['numero'])       				: '';
			$complemento	  	= isset($_POST['complemento']) ? Text::clean($_POST['complemento'])       	: '';
			$bairro			  	= isset($_POST['bairro']) ? Text::clean($_POST['bairro'])       				: '';
			$cep			  	= isset($_POST['cep']) ? Text::clean($_POST['cep'])       						: '';
			$cpf_cnpj			= isset($_POST['cpf_cnpj']) ? Text::clean($_POST['cpf_cnpj'])       			: '';
			$cidade			  	= isset($_POST['cidade']) ? Text::clean($_POST['cidade'])       				: '';
			$estado			  	= isset($_POST['estado']) ? Text::clean($_POST['estado'])       				: '';
			$pais			  	= isset($_POST['pais']) ? Text::clean($_POST['pais'])       					: '';
			$receber_boletim  = isset($_POST['receber_boletim'])?(int)$_POST['receber_boletim']				: 0;

			$lp_active = isset($_POST['lp_active']) ? (int)$_POST['lp_active'] : 0 ;
			$lp_quota_mb = isset($_POST['lp_quota_mb']) ? (int)$_POST['lp_quota_mb'] : 0 ;

			$curriculo = isset($_POST['curriculo']) ? Text::clean($_POST['curriculo']) : '';

			$iugu_split_account_id = isset($_POST['iugu_split_account_id']) ? Text::clean($_POST['iugu_split_account_id']) : '';
			$iugu_split_subcount_name = isset($_POST['iugu_split_subcount_name']) ? Text::clean($_POST['iugu_split_subcount_name']) : '';
			$iugu_split_user_token = isset($_POST['iugu_split_user_token']) ? Text::clean($_POST['iugu_split_user_token']) : '';

			$certificado_logoOld = isset($_POST['certificado_logoOld']) ? Text::clean($_POST['certificado_logoOld']) : "";
			$certificado_logoDel = isset($_POST['certificado_logoDel']) ? (int)$_POST['certificado_logoDel'] : 0;

			$certificado_assinaOld = isset($_POST['certificado_assinaOld']) ? Text::clean($_POST['certificado_assinaOld']) : "";
			$certificado_assinaDel = isset($_POST['certificado_assinaDel']) ? (int)$_POST['certificado_assinaDel'] : 0;

			
			$array = array(
				'cadastro_idx'		=> $id,
				'certificado_emitir' => $certificado_emitir,
				'status' 			=> $status,
				'lp_active' 		=> $lp_active,
				'lp_quota_mb' 		=> $lp_quota_mb,
				'perfil' 			=> $perfil,
				'nome_completo' 	=> $nome_completo,
				'nome_informal' 	=> $nome_informal,
				'genero' 			=> $genero,
				'data_nasc' 		=> $data_nasc,
				'email' 			=> $email,
				'telefone_resid' 	=> $telefone_resid,
				'telefone_comer'	=> $telefone_comer,
				'celular'			=> $celular,
				'endereco' 			=> $endereco,
				'numero' 			=> $numero,
				'complemento' 		=> $complemento,
				'bairro' 			=> $bairro,
				'cep' 				=> $cep,
				'cpf_cnpj' 			=> $cpf_cnpj,
				'cidade' 			=> $cidade,
				'estado' 			=> $estado,
				'pais' 				=> $pais,
				'curriculo' => $curriculo,
				'iugu_split_account_id' => $iugu_split_account_id,
				'iugu_split_subcount_name' => $iugu_split_subcount_name,
				'iugu_split_user_token' => $iugu_split_user_token,
				'receber_boletim' => $receber_boletim,
				'certificado_logo' => $certificado_logoOld,
				'certificado_assina' => $certificado_assinaOld,
				'afiliado_ativo' => $afiliado_ativo,
				'afiliado_codigo' => $afiliado_codigo
			);


			//Registra as imagens enviadas
			$arquivo_certificado_logo = $_FILES['certificado_logo'];
			if(is_array($arquivo_certificado_logo)){

				if(trim($arquivo_certificado_logo['name']) != ''){

					$filePath = pathinfo($arquivo_certificado_logo['name']);
					$ext = $filePath['extension'];
					$fileName = $filePath['filename'];

					$extCheck = self::checkFileType($arquivo_certificado_logo['name']);
					$nome_arquivo = 'cert_logo_' . date("dmYHis_") . Text::friendlyUrl(strtolower(Text::clean($fileName))) .".".$ext;

					//Verificando se a extensão do arquivo é compatível
					if($extCheck !== false){
						//armazenando o arquivo na pasta de destino do modulo
						if(move_uploaded_file($arquivo_certificado_logo['tmp_name'], $this->pasta_modulo.$nome_arquivo)){
							$array['certificado_logo'] = $nome_arquivo;
							//Remove o arquivo antigo
							if(file_exists($this->pasta_modulo.$certificado_logoOld)&&trim($certificado_logoOld)!=''){
								unlink($this->pasta_modulo.$certificado_logoOld);
							}
						}
					}else{
						ob_end_clean();
						Sis::setAlert("Arquivo não suportado, observe os formatos aceitos!", 4);
						exit();
					}

				}
			}
			//Remove o arquivo de midia
			if ($certificado_logoDel==1) {
				if(file_exists($this->pasta_modulo.$array['certificado_logo'])&&trim($array['certificado_logo'])!=''){
					unlink($this->pasta_modulo.$array['certificado_logo']);
				}
				$array['certificado_logo'] = 'null';
			}

			//Registra as imagens enviadas
			$arquivo_certificado_assina = $_FILES['certificado_assina'];
			if(is_array($arquivo_certificado_assina)){

				if(trim($arquivo_certificado_assina['name']) != ''){

					$filePath = pathinfo($arquivo_certificado_assina['name']);
					$ext = $filePath['extension'];
					$fileName = $filePath['filename'];
					
					$extCheck = self::checkFileType($arquivo_certificado_assina['name']);
					$nome_arquivo = 'cert_assi_' . date("dmYHis_") . Text::friendlyUrl(strtolower(Text::clean($fileName))) .".".$ext;

					//Verificando se a extensão do arquivo é compatível
					if($extCheck !== false){
						//armazenando o arquivo na pasta de destino do modulo
						if(move_uploaded_file($arquivo_certificado_assina['tmp_name'], $this->pasta_modulo.$nome_arquivo)){
							$array['certificado_assina'] = $nome_arquivo;
							//Remove o arquivo antigo
							if(file_exists($this->pasta_modulo.$certificado_assinaOld)&&trim($certificado_assinaOld)!=''){
								unlink($this->pasta_modulo.$certificado_assinaOld);
							}
						}
					}else{
						ob_end_clean();
						Sis::setAlert("Arquivo não suportado, observe os formatos aceitos!", 4);
						exit();
					}

				}
			}
			//Remove o arquivo de midia
			if ($certificado_assinaDel==1) {
				if(file_exists($this->pasta_modulo.$array['certificado_assina'])&&trim($array['certificado_assina'])!=''){
					unlink($this->pasta_modulo.$array['certificado_assina']);
				}
				$array['certificado_assina'] = 'null';
			}

			$iugu_split_api_token_update = isset($_POST['iugu_split_api_token_update']) ? (int)$_POST['iugu_split_api_token_update'] : 0;
			if ($iugu_split_api_token_update==1){
				$iugu_split_live_api_token = isset($_POST['iugu_split_live_api_token']) ? Text::clean($_POST['iugu_split_live_api_token']) : '';
				$iugu_split_test_api_token = isset($_POST['iugu_split_test_api_token']) ? Text::clean($_POST['iugu_split_test_api_token']) : '';
				if (trim($iugu_split_live_api_token)=='' || trim($iugu_split_test_api_token)=='') {
					Sis::setAlert('Informe as chaves de API para continuar!', 4);
					exit();
				}
				$array['iugu_split_live_api_token'] = $iugu_split_live_api_token;
				$array['iugu_split_test_api_token'] = $iugu_split_test_api_token;
			}


			if($senha != "" && $senha == $senha_confirm){
				$array['senha'] = md5($senha);
			}

			$messageLog = array('modulo_codigo'=>$this->MODULO_CODIGO,'modulo_area'=>$this->MODULO_AREA,'reg_nome'=>$array['nome_completo']);
			$dados = parent::sqlCRUD($array, '', $this->TB_CADASTRO, $messageLog, 'U', 0, 0);

			//Cadastro das áreas de interesse
			$areaInteresse = isset($_POST['area_interesse']) ? $_POST['area_interesse'] : 0;
			parent::excludeAreas($areaInteresse, $this->TB_AREA_SELECIONA);
			if(is_array($areaInteresse)){
				foreach($areaInteresse as $area){
					self::areaInteresseInsert($id, $area);
				}
			}

			//Atualiza as afiliadoCursos relacionadas ao modulo
				
				$afiliadoCursos_old_str = isset($_POST['afiliadoCursos_old']) ? $_POST['afiliadoCursos_old'] : '' ;
		        $afiliadoCursos_old = explode(",",str_replace("!","",str_replace("!!",",",$afiliadoCursos_old_str)));
		        $afiliadoCursos = isset($_POST['afiliadoCursos']) ? $_POST['afiliadoCursos'] : '' ;
		        $afiliadoCursos_str = is_array($afiliadoCursos) ? "!".implode("!!",$afiliadoCursos)."!" : $afiliadoCursos;
		        
		        /* Relaciona os novos */
		        foreach($afiliadoCursos as $key => $afiliadoCurso){
		        	
		        	$afiliadoComissao = isset($_POST['afiliadoComissao_'.$afiliadoCurso]) ? $_POST['afiliadoComissao_'.$afiliadoCurso] : 0 ;
		        	$afiliadoDesconto = isset($_POST['afiliadoDesconto_'.$afiliadoCurso]) ? $_POST['afiliadoDesconto_'.$afiliadoCurso] : 0 ;
		        	$afiliadoEmitirCertificado = isset($_POST['afiliadoEmitirCertificado_'.$afiliadoCurso]) ? $_POST['afiliadoEmitirCertificado_'.$afiliadoCurso] : 0 ;

		            if(strpos($afiliadoCursos_old_str,"!".$afiliadoCurso."!")===false){//nao existe cria
		                self::linkCursoAfiliado($id,$afiliadoCurso,$afiliadoComissao,$afiliadoDesconto,$afiliadoEmitirCertificado);
		            }else{//existe, atualiza
		                self::updateCursoAfiliado($id,$afiliadoCurso,$afiliadoComissao,$afiliadoDesconto,$afiliadoEmitirCertificado);
		            }
		        }
		        /* Deleta os desmarcados */
		        foreach($afiliadoCursos_old as $afiliadoCurso){
		            if(strpos($afiliadoCursos_str,"!".$afiliadoCurso."!")===false){
		                self::unlinkCursoAfiliado($id,$afiliadoCurso);
		            }
		        }

			if (ob_get_contents()) ob_end_clean();

			if(isset($dados) && $dados !== NULL){
				Sis::setAlert('Dados cadastrados com sucesso!', 3, '?mod='.$_GET["mod"].'&pag=' . $_GET["pag"]);
			} else {
				Sis::setAlert('Ocorreu um erro ao cadastrar dados!', 4);
			}
		}


		/**
	    * Retorna uma confirmação se o registro não está relacionado a algum registro de outros módulos.
	    * @return array, se a consulta for realizada com sucesso, caso contrário false
	    */
		public function hasDependecies($cadastro){
			//validação de consistencia para a relação com o módulo de ecommercex.
			//Verificação se há pedidos relacionados.
			
			// $retorno2 = parent::select("SELECT cadastro_idx FROM ".self::getPrefix()."_ecommerce_pedido WHERE cadastro_idx=".$cadastro." LIMIT 0,1 ");
			// if (is_array($retorno2)&&count($retorno2)>0) {
			// 	return true; //existem pedidos relacionados
			// }
			
			return false;
		}

		public function theDelete() {
			
			$id = (isset($_GET['id']) ? (int)$_GET['id'] : 0);
			if(self::hasDependecies($id)) {
				if (ob_get_contents()) ob_end_clean();
				Sis::setAlert('Não é possível remover os dados, existem registros de outros módulos de sistema relacionados!', 4, '?mod='.$_GET["mod"].'&pag=' . $_GET["pag"]);
				exit();
			}

			$array = array('cadastro_idx' => $id);

			$removeRelaciona = parent::sqlCRUD($array, '', $this->TB_AREA_SELECIONA, '', 'D', 0, 0);

			if (ob_get_contents()) ob_end_clean();
			if(isset($removeRelaciona) && $removeRelaciona !== NULL){
				//Sis::setAlert('Dados removidos com sucesso!', 3, '?mod='.$_GET["mod"].'pag=' . $_GET["pag"]);
				$nomeCadastro = self::listSelected($id, 'nome_completo');
				if(is_array($nomeCadastro) && count($nomeCadastro) > 0){
					$nomeCadastro = $nomeCadastro[0]['nome_completo'];
				}

				$messageLog = array('modulo_codigo'=>$this->MODULO_CODIGO,'modulo_area'=>$this->MODULO_AREA,'reg_nome'=>$nomeCadastro);
				$dados = parent::sqlCRUD($array, '', $this->TB_CADASTRO, $messageLog, 'D', 0, 0);

				if (ob_get_contents()) ob_end_clean();
				if(isset($dados) && $dados !== NULL){
					Sis::setAlert('Dados removidos com sucesso!', 3, '?mod='.$_GET["mod"].'&pag=' . $_GET["pag"]);
				} else {
					Sis::setAlert('Ocorreu um erro ao remover dados!', 4);
				}
			} else {
				Sis::setAlert('Ocorreu um erro ao remover dados! [002]', 4);
			}

		}

		//Recupera a lista de cursos com comissao para afiliados.
		public function afiliadoCursos($cadastroId)
		{
			return self::select("SELECT tbCur.curso_idx,tbCur.nome,tbCur.afiliado_comissao,tbCadAf.desconto_afiliado,
				tbCadAf.comissao,tbCadAf.certificado_emitir
				FROM ".$this->TB_CURSOS." as tbCur
				LEFT JOIN ".$this->TB_CADASTRO_CURSO_AFILIADO." as tbCadAf ON tbCur.curso_idx=tbCadAf.curso_idx And tbCadAf.cadastro_idx=".$cadastroId."
			WHERE status=1 ");
		}

		//Recupera a lista de cursos que o usuário está afiliado.
		public function cadastroAfiliadoCursos($cadastroId)
		{
			return self::select("SELECT curso_idx FROM ".$this->TB_CADASTRO_CURSO_AFILIADO." WHERE cadastro_idx=".(int)$cadastroId." ");
		}

		public function linkCursoAfiliado($cadastroId,$cursoId,$comissao=0,$desconto=0,$certificado_emitir=0){
			$strSQL = "INSERT INTO ".$this->TB_CADASTRO_CURSO_AFILIADO."(cadastro_idx,curso_idx,comissao,desconto_afiliado,certificado_emitir) values(".(int)$cadastroId.",".(int)$cursoId.",".(float)$comissao.",".(float)$desconto.",".(int)$certificado_emitir.")";
			parent::insert($strSQL);
		}

		public function updateCursoAfiliado($cadastroId,$cursoId,$comissao=0,$desconto=0,$certificado_emitir=0){
			$strSQL = "UPDATE ".$this->TB_CADASTRO_CURSO_AFILIADO." SET comissao=".(float)$comissao.",desconto_afiliado=".(float)$desconto.",certificado_emitir=".(int)$certificado_emitir." WHERE cadastro_idx=".(int)$cadastroId." And curso_idx=".(int)$cursoId." ";
			parent::update($strSQL);
		}

		public function unlinkCursoAfiliado($cadastroId,$cursoId){
			parent::delete("DELETE FROM ".$this->TB_CADASTRO_CURSO_AFILIADO." WHERE cadastro_idx=".(int)$cadastroId." And curso_idx=".(int)$cursoId." ");
		}

		/**
	    * Retorna o registro da área de interesse de acordo com o ID.
	    * @return array, se a consulta for realizada com sucesso, caso contrário false
	    */
	    public function listAreaSelected($id=0){
	        $id = (int)$id;
	        $array = array('interesse_idx' => round($id));
	        $dados = parent::sqlCRUD($array, '', $this->TB_AREA_INTERESSE, '', 'S', 0, 0);
	        if(is_array($dados) && count($dados) > 0)
	        {
	            return $dados;
	        }else{
	            return false;
	        }
	    }

	    public static function checkFileType($string)
		{
			$padrao = '/^.+(\.gif|\.webp|\.png|\.jpg|\.jpeg)$/';
			$resultado = preg_match($padrao, $string);
			if(!$resultado)
				return false;
			return true;
		}

	   /**
		 * Retorna as informações para exibir na dashboard do sistema, até 2 linhas
		 */
		public function dashInfo()
		{
			$num_total=0;
			$num_online=0;

			$array = array('orderby' => 'Order By cadastro_idx');
			$totalRegs = parent::select('SELECT count(cadastro_idx) as numReg FROM '.$this->TB_CADASTRO);
			$num_total = (is_array($totalRegs)&&count($totalRegs)>0)?$totalRegs[0]['numReg']:0;
			$array = array('status' => 1);
			$totalRegsActive = parent::select('SELECT count(cadastro_idx) as numReg FROM '.$this->TB_CADASTRO." WHERE status=1 ");
			$num_online = (is_array($totalRegsActive)&&count($totalRegsActive)>0)?$totalRegsActive[0]['numReg']:0;
			$retorno = ($num_total>0)?" ".$num_online." ususário(s) ativos(s) de<br> ".$num_total." ususário(s) cadastrado(s).":"Nenhuma ususário cadastrado!";
			return $retorno;
		}

	}
?>