<?php

	include("categoria-model.php");
	include("categoria-control.php");

	include("fabricante-model.php");
	include("fabricante-control.php");

	$m_categoria = new categoria();
	$m_fabricante = new fabricante();

	class produto extends produto_model{

		public $MODULO_CODIGO = "10033";
		public $MODULO_AREA = "E-commerce - Produto";

		public $mod, $pag, $act;
		public $pasta_modulo;
		public $pasta_modulo_pag;
		public $pasta_modulo_images;
		public $pasta_modulo_images_p;
		public $pasta_modulo_images_m;
		public $pasta_modulo_images_g;

		public function __construct(){

			parent::__construct();

			global $act;
			$this->mod = "ecommercex";
			$this->pag = "produto";
			$this->act = $act;

			$basedir = BASE_PATH.DS.PASTA_CONTENT;

			$this->pasta_modulo = $basedir.DS.$this->mod.DS;
			$this->pasta_modulo_pag = $this->pasta_modulo.$this->pag.DS;
			$this->pasta_modulo_images =   $this->pasta_modulo_pag."images".DS;
			$this->pasta_modulo_images_p = $this->pasta_modulo_images."p".DS;
			$this->pasta_modulo_images_m = $this->pasta_modulo_images."m".DS;
			$this->pasta_modulo_images_g = $this->pasta_modulo_images."g".DS;

			if(!is_dir($this->pasta_modulo)){
			   mkdir($this->pasta_modulo);
			}
			if(!is_dir($this->pasta_modulo_pag)){
			   mkdir($this->pasta_modulo_pag);
			}
			if(!is_dir($this->pasta_modulo_images)){
			   mkdir($this->pasta_modulo_images);
			}
			if(!is_dir($this->pasta_modulo_images_p)){
			   mkdir($this->pasta_modulo_images_p);
			}
			if(!is_dir($this->pasta_modulo_images_m)){
			   mkdir($this->pasta_modulo_images_m);
			}
			if(!is_dir($this->pasta_modulo_images_g)){
			   mkdir($this->pasta_modulo_images_g);
			}

		}

		public function listAll($array="", $atualPageDi=0) {
			$array = (is_array($array))? $array : [];
			$array['orderby'] = 'ORDER BY nome ASC';
			return parent::listAllM($array, $atualPageDi, 50);
		}

		public function listAllVariacao($id){
			$array = array('produto_idx' => $id,'orderby' => 'ORDER BY ranking ASC');
			$dados = parent::sqlCRUD($array, '', $this->TB_PRODUTO_VARIACAO, '', 'S', 0, 0);
			if(is_array($dados) && count($dados) > 0){
				return $dados;
			} else  {
				return false;
			}
		}

		public function listProdutoVarDadosC($id,$dado_id=0,$valor_id=0,$tipo=0)
		{
			$array = array(((round($tipo)==0)?'produto_idx':'variacao_idx')=>$id);
			if($dado_id!=0){
				$array['dado_idx']=$dado_id;
			}
			if($valor_id!=0){
				$array['valor_idx']=$valor_id;
			}
			$dados = parent::sqlCRUD($array, '', (((round($tipo)==0)?$this->TB_PRODUTO_DADO:$this->TB_PRODUTO_VAR_DADO)), '', 'S', 0, 0);
			if(is_array($dados) && count($dados) > 0){
				return $dados;
			} else  {
				return false;
			}
		}

		public function listProdutoDadosC($tipo=0){
			$array = array('tipo' => $tipo,'orderby' => 'ORDER BY nome ASC');
			$dados = parent::sqlCRUD($array, '', $this->TB_VAR_DADO, '', 'S', 0, 0);
			if(is_array($dados) && count($dados) > 0){
				return $dados;
			} else  {
				return false;
			}
		}

		public function listProdutoDadosCValor($dado){
			$array = array('dado_idx'=>$dado,'orderby' => 'ORDER BY nome ASC');
			$dados = parent::sqlCRUD($array, '', $this->TB_VAR_VALOR, '', 'S', 0, 0);
			if(is_array($dados) && count($dados) > 0){
				return $dados;
			} else  {
				return false;
			}
		}


		public function theInsert()
		{

			$em_oferta_expira_data = (isset($_POST['em_oferta_expira_data']) && Date::isDate($_POST['em_oferta_expira_data'])) ? Date::toMysql($_POST['em_oferta_expira_data'],2) : date("Y-m-d H:i:s");
			
			$array = array(
				'departamento_idx'=> isset($_POST['departamento_idx']) ? (int)($_POST['departamento_idx']) : 0,
				'categoria_idx'=> isset($_POST['categoria_idx']) ? (int)($_POST['categoria_idx']) : 0,
				'subcategoria_idx'=> isset($_POST['subcategoria_idx']) ? (int)($_POST['subcategoria_idx']) : 0,
				'status'=> isset($_POST['status']) ? (int)($_POST['status']) : 0,
				'quantidade_no_sync'=> isset($_POST['quantidade_no_sync']) ? (int)($_POST['quantidade_no_sync']) : 0,
				'quantidade'=> isset($_POST['quantidade']) ? (int)($_POST['quantidade']) : 0,
				'mais_vendido'=> isset($_POST['mais_vendido']) ? (int)($_POST['mais_vendido']) : 0,
				'lancamento'=> isset($_POST['lancamento']) ? (int)($_POST['lancamento']) : 0,
				'destaque'=> isset($_POST['destaque']) ? (int)($_POST['destaque']) : 0,
				'em_oferta_expira'=> isset($_POST['em_oferta_expira']) ? (int)($_POST['em_oferta_expira']) : 0,
				'em_oferta'=> isset($_POST['em_oferta']) ? (int)($_POST['em_oferta']) : 0,
				'em_oferta_valor'=> (isset($_POST['em_oferta_valor']) && $_POST['em_oferta_valor'] != "") ? Text::clean($_POST['em_oferta_valor']) : 0,
				'fabricante_idx'=> isset($_POST['fabricante_idx']) ? (int)($_POST['fabricante_idx']) : 0,
				'nacionalidade'=> isset($_POST['nacionalidade']) ? Text::clean($_POST['nacionalidade']) : "",
				'nome'=> isset($_POST['nome']) ? Text::clean($_POST['nome']) : "",
				'nome_noaccent'=> isset($_POST['nome']) ? Text::clean($_POST['nome']) : "",
				'peso'=> isset($_POST['peso']) ? (int)($_POST['peso']) : 0,
				'largura'=> isset($_POST['largura']) ? (int)($_POST['largura']) : 0,
				'altura'=> isset($_POST['altura']) ? (int)($_POST['altura']) : 0,
				'comprimento'=> isset($_POST['comprimento']) ? (int)($_POST['comprimento']) : 0,
				'valor'=> isset($_POST['valor']) ? Text::clean($_POST['valor']) : 0,
				'pdv_id'=> isset($_POST['pdv_id']) ? Text::clean($_POST['pdv_id']) : "",
				'descricao_curta'=> isset($_POST['descricao_curta']) ? Text::clean($_POST['descricao_curta']) : "",
				'descricao_longa'=> isset($_POST['descricao_longa']) ? Text::clean($_POST['descricao_longa']) : "",
				'tags'=> isset($_POST['tags']) ? Text::clean($_POST['tags']) : "",
				'url_video'=> isset($_POST['url_video']) ? Text::clean($_POST['url_video']) : ""
			);

			if ((int)$array['em_oferta_expira']==1){
				$array['em_oferta_expira_data'] = $em_oferta_expira_data;
			}

			if($array['nome_noaccent']!="") {
				$nome_aux = str_replace("-", " ",Text::friendlyUrl(Text::clean($_POST['nome'])));
				$array['nome_noaccent'] = strtoupper($nome_aux);
			}

			$array['valor'] = str_replace(".","",$array['valor']);
			$array['valor'] = str_replace(",",".",$array['valor']);

			$array['em_oferta_valor'] = str_replace(".","",$array['em_oferta_valor']);
			$array['em_oferta_valor'] = str_replace(",",".",$array['em_oferta_valor']);

			if(!is_numeric($array['em_oferta_valor'])){ $array['em_oferta_valor']=0; }
			if(!is_numeric($array['valor'])){ $array['valor']=0; }

			$codigoExists = parent::sqlCRUD( array('pdv_id' => $array['pdv_id']), 'pdv_id', $this->TB_PRODUTO, '', 'S', 0, 0);

			if(is_array($codigoExists) && count($codigoExists)>0) {
				Sis::setAlert('Esse código já existe, confira as informações!', 4);
			}

			$messageLog = array('modulo_codigo'=>$this->MODULO_CODIGO,'modulo_area'=>$this->MODULO_AREA,'reg_nome'=>$array['nome']);
			$dados = parent::sqlCRUD($array, '', $this->TB_PRODUTO, $messageLog, 'I', 0, 0);
			$produto_idx = $dados;

			

			/*Inclui os dados complementares do produto*/
			$produto_dadosc = self::listProdutoDadosC(0);
			if (is_array($produto_dadosc)&&count($produto_dadosc)>0){
				foreach ($produto_dadosc as $key => $prod_dado) {
					$p_dadoc = isset($_POST['p_dadoc_'.Text::toAscii($prod_dado['nome'])]) ? ($_POST['p_dadoc_'.Text::toAscii($prod_dado['nome'])]) : 0;
					if (is_array($p_dadoc)){ //Selecao Multipla
						foreach ($p_dadoc as $key => $p_dadoc_value) {
							$arr_dado = explode("-",$p_dadoc_value);
							if(count($arr_dado)==2){
								$var_dado = $arr_dado[0];
								$var_valor = $arr_dado[1];
								$pvar_dadosc_array = array(
									'produto_idx' => $produto_idx,
									'dado_idx' => (int)$var_dado,
									'valor_idx' => (int)$var_valor
								);
								$varDadosC = parent::sqlCRUD($pvar_dadosc_array, '', $this->TB_PRODUTO_DADO, '', 'I', 0, 0);
							}
						}
					}else{ //Selecao Simples
						$arr_dado = explode("-",$p_dadoc);
						if(count($arr_dado)==2){
							$var_dado = $arr_dado[0];
							$var_valor = $arr_dado[1];
							$pvar_dadosc_array = array(
								'produto_idx' => $produto_idx,
								'dado_idx' => (int)$var_dado,
								'valor_idx' => (int)$var_valor
							);
							$varDadosC = parent::sqlCRUD($pvar_dadosc_array, '', $this->TB_PRODUTO_DADO, '', 'I', 0, 0);
						}
					}
				}
			}

			//Registra as imagens enviadas
			$imagens = $_FILES['imagens'];
			$total_imagens = count($imagens['name']);

			for($x = 0; $x < $total_imagens; $x++)
			{
				if ($imagens['name'][$x] != '')
				{

					$path_parts = pathinfo($imagens['name'][$x]);
					$nome_imagem = Text::normalize($path_parts['filename']);
					$ext = $path_parts['extension'];
					$imagem = date("dmYHis_") . $x . $nome_imagem .".". $ext;

					//move a imagem para a pasta de imagens
					if(move_uploaded_file($imagens['tmp_name'][$x], $this->pasta_modulo_images.$imagem)){
	                 //trata o tamanho da imagem
	                 $img = new SimpleImage();

	                 try {
		                 $img->load($this->pasta_modulo_images . $imagem)->best_fit(100,100)->save($this->pasta_modulo_images_p . $imagem);
		                 $img->load($this->pasta_modulo_images . $imagem)->best_fit(420,420)->save($this->pasta_modulo_images_m . $imagem);
		                 $img->load($this->pasta_modulo_images . $imagem)->best_fit(1098,1098)->save($this->pasta_modulo_images_g . $imagem);
	                 } catch (Exception $e) {
	                 	ob_end_clean();
	                 	Sis::setAlert('Ocorreu um erro ao salvar dados! [Imagens Upload - '.$e->getMessage().']', 4);
	                 }


	                 //Elimina a original enviada
	                 unlink($this->pasta_modulo_images.$imagem);

	                 //Insere a imagem na base de dados
	               	$array = array(
						      'produto_idx' 	=> $produto_idx,
						      'status' 	=> 1,
						      'ranking' 	=> 0,
						      'nome' 		=> $nome_imagem,
						      'imagem' 	=> $imagem
							);
							$dados = parent::sqlCRUD($array, '', $this->TB_PRODUTO_IMAGEM, '', 'I', 0, 0);

	             }else{
		              ob_end_clean();
	                 Sis::setAlert('Imagens - Ocorreu um erro ao salvar dados!', 4,"?mod=" . $_GET['mod'] . "&pag=" . $_GET['pag'] . "");
	             }

				}
			}

			//Registra as variacoes do produto
			$img = new SimpleImage();
			$pvar_cd = isset($_POST['pvar_cd']) ? $_POST['pvar_cd'] : "";
			if(is_array($pvar_cd))
			{
				for($i=0;$i<count($pvar_cd);$i++)
				{
					$_pvar_cd = trim($pvar_cd[$i]);
					$pvar_status = isset($_POST['pvar_status_'.$_pvar_cd]) ? Text::clean($_POST['pvar_status_'.$_pvar_cd]) : "";
					$pvar_quantidade = isset($_POST['pvar_quantidade_'.$_pvar_cd]) ? Text::clean($_POST['pvar_quantidade_'.$_pvar_cd]) : "";
					$pvar_codigo = isset($_POST['pvar_codigo_'.$_pvar_cd]) ? Text::clean($_POST['pvar_codigo_'.$_pvar_cd]) : "";
					$pvar_nome = isset($_POST['pvar_nome_'.$_pvar_cd]) ? Text::clean($_POST['pvar_nome_'.$_pvar_cd]) : "";
					$pvar_valor = isset($_POST['pvar_valor_'.$_pvar_cd]) ? Text::clean($_POST['pvar_valor_'.$_pvar_cd]) : 0;
					$pvar_imagem = isset($_FILES['pvar_imagem_'.$_pvar_cd]) ? $_FILES['pvar_imagem_'.$_pvar_cd] : "";

					$pvar_valor = str_replace(".","",$pvar_valor);
					$pvar_valor = str_replace(",",".",$pvar_valor);

					$pvar_array = array(
						'produto_idx' => $produto_idx,
						'status' => (int)$pvar_status,
						'ranking' => $i,
						'quantidade' => (int)$pvar_quantidade,
						'codigo' => Text::clean($pvar_codigo),
						'nome' => Text::clean($pvar_nome),
						//'valor' => $pvar_valor,
						'imagem' => 'Null'
					);
					$pvar_imagem_nome = "Null";
					/*Trata a imagem da variacao*/
					if ($pvar_imagem['name'] != '')
					{
						$path_parts = pathinfo($pvar_imagem['name']);
						$nome_imagem = Text::normalize($path_parts['filename']);
						$ext = $path_parts['extension'];
						$pvar_imagem_nome = "prod_var_".date("dmYHis_") . $x . $nome_imagem .".". $ext;

						// $pvar_imagem_nome = //Text::normalize(substr($pvar_imagem['name'],0,(strlen($pvar_imagem['name'])-4)));
						// $ext = substr($pvar_imagem['name'], -4);
						// $pvar_imagem_nome = "prod_var_".date("dmYHis_") . $x . $pvar_imagem_nome . $ext;

						//move a imagem para a pasta de imagens
						if(move_uploaded_file($pvar_imagem['tmp_name'], $this->pasta_modulo_images.$pvar_imagem_nome)){
		                 //trata o tamanho da imagem
		                 $img->load($this->pasta_modulo_images . $pvar_imagem_nome)->best_fit(980,980)->save($this->pasta_modulo_images . $pvar_imagem_nome);
						}
					}
					$pvar_array['imagem'] = $pvar_imagem_nome;
					$dados = parent::sqlCRUD($pvar_array, '', $this->TB_PRODUTO_VARIACAO, '', 'I', 0, 0);
					$variacao_idx = $dados;

					/*Inclui os dados relacionados da variação*/
					$dadosc = self::listProdutoDadosC(1);
					if (is_array($dadosc)&&count($dadosc)>0){
						foreach ($dadosc as $key => $dado) {
							$pvar_cdado = isset($_POST['pvar_'.Text::toAscii($dado['nome']).'_'.$_pvar_cd]) ? Text::clean($_POST['pvar_'.Text::toAscii($dado['nome']).'_'.$_pvar_cd]) : 0;
							$arr_dado = explode("-",$pvar_cdado);
							if(count($arr_dado)==2){
								$var_dado = $arr_dado[0];
								$var_valor = $arr_dado[1];
								$pvar_dadosc_array = array(
									'variacao_idx' => $variacao_idx,
									'dado_idx' => (int)$var_dado,
									'valor_idx' => (int)$var_valor
								);
								$varDadosC = parent::sqlCRUD($pvar_dadosc_array, '', $this->TB_PRODUTO_VAR_DADO, '', 'I', 0, 0);
							}
						}
					}

				}
			}

			self::urlRewriteUpdate(); //Gera os urls amigaveis dos produtos

			ob_end_clean();
			if(isset($dados) && $dados !== NULL){
				if ($dados == true){
					Sis::setAlert('Produto - Dados salvos com sucesso!', 3,"?mod=".$this->mod."&pag=".$this->pag."");
				}else{
					Sis::setAlert('Produto - O registro informado j&aacute; existe!', 4);
				}
			}else {
				Sis::setAlert('Produto - Ocorreu um erro ao salvar os dados!', 4);
			}
		}

		public function theUpdate(){

			$logError = "";
			$atualizacaoQuantidade = 0;

			$pid = isset($_POST['pid']) ? Text::clean($_POST['pid']) : 0;
			$nomeOld = isset($_POST['nomeOld']) ? Text::clean($_POST['nomeOld']) : "";
			$pdv_idOld = isset($_POST['pdv_idOld']) ? Text::clean($_POST['pdv_idOld']) : "";

			$em_oferta_expira_data = (isset($_POST['em_oferta_expira_data']) && Date::isDate($_POST['em_oferta_expira_data'])) ? Date::toMysql($_POST['em_oferta_expira_data'].' '.$_POST['em_oferta_expira_data_hora'].':00',2) : date("Y-m-d H:i:s");
			
			$array = array(
                'produto_idx' => $pid,
                'departamento_idx'=> isset($_POST['departamento_idx']) ? (int)($_POST['departamento_idx']) : 0,
                'categoria_idx'=> isset($_POST['categoria_idx']) ? (int)($_POST['categoria_idx']) : 0,
                'subcategoria_idx'=> isset($_POST['subcategoria_idx']) ? (int)($_POST['subcategoria_idx']) : 0,
                'status'=> isset($_POST['status']) ? (int)($_POST['status']) : 0,
                'lancamento'=> isset($_POST['lancamento']) ? (int)($_POST['lancamento']) : 0,
				'fabricante_idx'=> isset($_POST['fabricante_idx']) ? (int)($_POST['fabricante_idx']) : 0,
                'quantidade'=> isset($_POST['quantidade']) ? (int)($_POST['quantidade']) : 0,
                'quantidade_no_sync'=> isset($_POST['quantidade_no_sync']) ? (int)($_POST['quantidade_no_sync']) : 0,
                'mais_vendido'=> isset($_POST['mais_vendido']) ? (int)($_POST['mais_vendido']) : 0,
                'destaque'=> isset($_POST['destaque']) ? (int)($_POST['destaque']) : 0,
                'em_oferta'=> isset($_POST['em_oferta']) ? (int)($_POST['em_oferta']) : 0,
                'em_oferta_valor'=> (isset($_POST['em_oferta_valor']) && $_POST['em_oferta_valor'] != "") ? Text::clean($_POST['em_oferta_valor']) : 0,
                'em_oferta_expira'=> isset($_POST['em_oferta_expira']) ? (int)($_POST['em_oferta_expira']) : 0,
				'nome'=> isset($_POST['nome']) ? Text::clean($_POST['nome']) : "",
				'nome_noaccent'=> isset($_POST['nome']) ? str_replace("-", " ",Text::toAscii(Text::clean($_POST['nome']))) : "",
                'peso'=> isset($_POST['peso']) ? (int)($_POST['peso']) : 0,
                'largura'=> isset($_POST['largura']) ? (int)($_POST['largura']) : 0,
				'altura'=> isset($_POST['altura']) ? (int)($_POST['altura']) : 0,
				'comprimento'=> isset($_POST['comprimento']) ? (int)($_POST['comprimento']) : 0,
                'valor'=> isset($_POST['valor']) ? Text::clean($_POST['valor']) : 0,
                'pdv_id'=> isset($_POST['pdv_id']) ? Text::clean($_POST['pdv_id']) : "",
                'descricao_curta'=> isset($_POST['descricao_curta']) ? Text::clean($_POST['descricao_curta']) : "",
                'descricao_longa'=> isset($_POST['descricao_longa']) ? Text::clean($_POST['descricao_longa']) : "",
                'tags'=> isset($_POST['tags']) ? Text::clean($_POST['tags']) : "",
                'url_video'=> isset($_POST['url_video']) ? Text::clean($_POST['url_video']) : ""
			);
                // 'lancamento'=> isset($_POST['lancamento']) ? (int)($_POST['lancamento']) : 0,
                // 'nacionalidade'=> isset($_POST['nacionalidade']) ? Text::clean($_POST['nacionalidade']) : "",

			if($array['nome_noaccent']!="") {
				$nome_aux = str_replace("-", " ",Text::friendlyUrl(Text::clean($_POST['nome'])));
				$array['nome_noaccent'] = strtoupper($nome_aux);
			}

			if($array['quantidade'] != 0){
				$atualizacaoQuantidade = $array['quantidade'];
			}

			if ((int)$array['em_oferta_expira']==1){
				$array['em_oferta_expira_data'] = $em_oferta_expira_data;
			}

			$array['valor'] = str_replace(".","",$array['valor']);
			$array['valor'] = str_replace(",",".",$array['valor']);

			$array['em_oferta_valor'] = str_replace(".","",$array['em_oferta_valor']);
			$array['em_oferta_valor'] = str_replace(",",".",$array['em_oferta_valor']);

			if(!is_numeric($array['em_oferta_valor'])){ $array['em_oferta_valor']=0; }
			if(!is_numeric($array['valor'])){ $array['valor']=0; }

			if(trim($pdv_idOld)!=trim($array['pdv_id'])) {
				$pdv_idExists = parent::sqlCRUD(array('pdv_id' => $array['pdv_id']), 'pdv_id', $this->TB_PRODUTO, '', 'S', 0, 0);

				if(is_array($pdv_idExists) && count($pdv_idExists)>0) {
					Sis::setAlert('Esse código já existe, confira as informações!', 4);
				}
			}
			
			try {
				$dados = parent::sqlCRUD($array, '', $this->TB_PRODUTO, 'ECOMMERCE - PRODUTOS - UPDATE', 'U', 0, 0);
			} catch (Exception $e) {
				$logError .= " [".$e->getMessage()."] ";
			}

			/*Atualiza ou insere os dados complementares do produto*/
			$produto_dadosc = self::listProdutoDadosC(0);
			if (is_array($produto_dadosc)&&count($produto_dadosc)>0){
				foreach ($produto_dadosc as $key => $prod_dado) {
					$p_dadoc = isset($_POST['p_dadoc_'.Text::toAscii($prod_dado['nome'])]) ? $_POST['p_dadoc_'.Text::toAscii($prod_dado['nome'])] : 0;

					if (is_array($p_dadoc)){ //Selecao Multipla

						/*Registra as permissoes do usuario*/
						$p_dadoc_old = isset($_POST['p_dadoc_old_'.Text::toAscii($prod_dado['nome'])]) ? Text::clean($_POST['p_dadoc_old_'.Text::toAscii($prod_dado['nome'])]) : '';
						$p_dadoc_old = str_replace("!!",",",$p_dadoc_old);
						$p_dadoc_old = str_replace("!","",$p_dadoc_old);
						foreach($p_dadoc as $p_dadoc_valor)
						{
							if(strpos($p_dadoc_old,$p_dadoc_valor)===false)
							{
								$arr_dado = explode("-",$p_dadoc_valor);
								if(count($arr_dado)==2){
									$var_dado = $arr_dado[0];
									$var_valor = $arr_dado[1];
									$pvar_dadosc_array = array(
										'produto_idx' => $pid,
										'dado_idx' => $var_dado,
										'valor_idx' => $var_valor
									);
									$ProdDadosC = parent::sqlCRUD($pvar_dadosc_array, '', $this->TB_PRODUTO_DADO, '', 'I', 0, 0);
								}
							}
						}

						$t_p_dadoc = (is_array($p_dadoc))?implode(",",$p_dadoc):$p_dadoc;
						$a_p_dadoc_old = explode(",",$p_dadoc_old);
						if(is_array($a_p_dadoc_old) && count($a_p_dadoc_old)>0)
						{
							foreach($a_p_dadoc_old as $a_m_p_dadoc_old)
							{
								if(trim($a_m_p_dadoc_old)!="")
								{
									if (strpos($t_p_dadoc,$a_m_p_dadoc_old)===false)
									{
										$arr_dado = explode("-",$a_m_p_dadoc_old);
										if(count($arr_dado)==2){
											$var_dado = $arr_dado[0];
											$var_valor = $arr_dado[1];
											$pvar_dadosc_array = array(
												'produto_idx' => $pid,
												'dado_idx' => $var_dado,
												'valor_idx' => $var_valor
											);
											$ProdDadosC = parent::sqlCRUD($pvar_dadosc_array, '', $this->TB_PRODUTO_DADO, '', 'D', 0, 0);
										}
									}
								}
							}
						}

					}else{ //Selecao Simples
						$arr_dado = explode("-",$p_dadoc);
						if(count($arr_dado)==2){
							$var_dado = $arr_dado[0];
							$var_valor = $arr_dado[1];
							$pvar_dadosc_array = array(
								'produto_idx' => $pid,
								'dado_idx' => $var_dado,
								'valor_idx' => $var_valor
							);
							$p_dadoc_ck = self::listProdutoVarDadosC($pid,$var_dado);
							if(is_array($p_dadoc_ck) && count($p_dadoc_ck)>0){ //Atualiza
								$ProdDadosC = parent::update("UPDATE ".$this->TB_PRODUTO_DADO." Set valor_idx=".round($var_valor)." Where produto_idx=".round($pid)." And dado_idx=".round($var_dado)." ");
							}else{ //Insere
								$ProdDadosC = parent::sqlCRUD($pvar_dadosc_array, '', $this->TB_PRODUTO_DADO, '', 'I', 0, 0);
							}
						}
					}


				}
			}

			//Atualiza os registros de imagens existentes
			$reg_images = isset($_POST['img_id']) ? $_POST['img_id'] : "";
			$reg_images_desc = isset($_POST['img_descricao']) ? $_POST['img_descricao'] : "";
			if(is_array($reg_images)){
				for($i=0;$i<count($reg_images);$i++){
					$array_img = array(
				      'produto_imagem_idx' => $reg_images[$i],
				      'nome' => $reg_images_desc[$i]
					);
					$dados = parent::sqlCRUD($array_img, '', $this->TB_PRODUTO_IMAGEM, 'ECOMMERCE - PRODUTOS - IMAGEM - UPDATE', 'U', 0, 0);
				}
			}

			//Registra as imagens enviadas
			$imagens = $_FILES['imagens'];
			$total_imagens = count($imagens['name']);
			try {
				for($x = 0; $x < $total_imagens; $x++)
				{
					if ($imagens['name'][$x] != '')
					{
						$path_parts = pathinfo($imagens['name'][$x]);
						$nome_imagem = Text::normalize($path_parts['filename']);
						$ext = $path_parts['extension'];
						$imagem = date("dmYHis_") . $x . $nome_imagem . "." . $ext;
						// var_dump($this->pasta_modulo_images.$imagem);
						// exit();
						//move a imagem para a pasta de imagens
						if(move_uploaded_file($imagens['tmp_name'][$x], $this->pasta_modulo_images.$imagem)){
							
							//trata o tamanho da imagem
							$img = new SimpleImage();

							try {
								$img->load($this->pasta_modulo_images . $imagem)->best_fit(100,100)->save($this->pasta_modulo_images_p . $imagem);
								$img->load($this->pasta_modulo_images . $imagem)->best_fit(420,420)->save($this->pasta_modulo_images_m . $imagem);
								$img->load($this->pasta_modulo_images . $imagem)->best_fit(1098,1098)->save($this->pasta_modulo_images_g . $imagem);
							} catch (Exception $e) {
								ob_end_clean();
			                 	Sis::setAlert('Ocorreu um erro ao salvar dados! [Imagens Upload - '.$e->getMessage().']', 4);
							}
							
							//Elimina a original enviada
							unlink($this->pasta_modulo_images.$imagem);

							//Insere a imagem na base de dados
							$array_img = array(
							   'produto_idx' 	=> $pid,
							   'status' 	=> 1,
							   'ranking' 	=> 0,
							   'nome' 		=> $nome_imagem,
							   'imagem' 	=> $imagem
							);
							$dados = parent::sqlCRUD($array_img, '', $this->TB_PRODUTO_IMAGEM, 'ECOMMERCE - PRODUTOS - IMAGEM - INSERIR', 'I', 0, 0);

			             }else{
			                 ob_end_clean();
			                 Sis::setAlert('Ocorreu um erro ao salvar dados! [Imagens Upload]', 4);
			             }

					}
				}
			} catch (Exception $e) {
				$logError .= " [".$e->getMessage()."] ";
			}
			//Registra as variacoes do produto
			$img = new SimpleImage();
			$pvar_cd = isset($_POST['pvar_cd']) ? $_POST['pvar_cd'] : "";
			if(is_array($pvar_cd)){
				for($i=0;$i<count($pvar_cd);$i++){

					$_pvar_cd = trim($pvar_cd[$i]);
					$pvar_id = isset($_POST['pvar_id_'.$_pvar_cd]) ? Text::clean($_POST['pvar_id_'.$_pvar_cd]) : "";
					$pvar_status = isset($_POST['pvar_status_'.$_pvar_cd]) ? Text::clean($_POST['pvar_status_'.$_pvar_cd]) : "";
					$pvar_quantidade = isset($_POST['pvar_quantidade_'.$_pvar_cd]) ? Text::clean($_POST['pvar_quantidade_'.$_pvar_cd]) : "";
					$pvar_codigo = isset($_POST['pvar_codigo_'.$_pvar_cd]) ? Text::clean($_POST['pvar_codigo_'.$_pvar_cd]) : "";
					$pvar_nome = isset($_POST['pvar_nome_'.$_pvar_cd]) ? Text::clean($_POST['pvar_nome_'.$_pvar_cd]) : "";
					//$pvar_valor = isset($_POST['pvar_valor_'.$_pvar_cd]) ? Text::clean($_POST['pvar_valor_'.$_pvar_cd]) : 0;
					$pvar_imagem = isset($_FILES['pvar_imagem_'.$_pvar_cd]) ? $_FILES['pvar_imagem_'.$_pvar_cd] : "";
					$pvar_imagem_old = isset($_POST['pvar_imagem_old_'.$_pvar_cd]) ? Text::clean($_POST['pvar_imagem_old_'.$_pvar_cd]) : "";
					$pvar_imagem_del = isset($_POST['pvar_imagem_del_'.$_pvar_cd]) ? (int)$_POST['pvar_imagem_del_'.$_pvar_cd] : 0;

					$pvar_valor = str_replace(".","",$pvar_valor);
					$pvar_valor = str_replace(",",".",$pvar_valor);

					if($pvar_id!=0){
						$pvar_array = array(
							'variacao_idx' => $pvar_id,
							'status' => $pvar_status,
							'ranking' => $i,
							'quantidade' => $pvar_quantidade,
							'codigo' => $pvar_codigo,
							'nome' => $pvar_nome,
							//'valor' => (float)$pvar_valor,
							'imagem' => $pvar_imagem_old
						);

						if($pvar_array['quantidade'] != 0){
							$atualizacaoQuantidade = $pvar_array['quantidade'];
						}
						$pvar_array_acao = "U"; //Update
						$pvar_array_acao_txt = "ECOMMERCE - PRODUTOS - VARIACÃO - EDITAR";
					}else{
						$pvar_array = array(
							'produto_idx' => $pid,
							'status' => $pvar_status,
							'ranking' => $i,
							'quantidade' => $pvar_quantidade,
							'codigo' => $pvar_codigo,
							'nome' => $pvar_nome,
							//'valor' => (float)$pvar_valor,
							'imagem' => $pvar_imagem_old
						);
						if($pvar_array['quantidade'] != 0){
							$atualizacaoQuantidade = $pvar_array['quantidade'];
						}
						$pvar_array_acao = "I"; //Insert
						$pvar_array_acao_txt = "ECOMMERCE - PRODUTOS - VARIACÃO - INSERIR";
					}

					$pvar_imagem_nome = $pvar_imagem_old;

					/*Trata a imgem da variacao*/
					if ($pvar_imagem['name'] != '')
					{
						// $pvar_imagem_nome = Text::normalize(substr($pvar_imagem['name'],0,(strlen($pvar_imagem['name'])-4)));
						// $ext = substr($pvar_imagem['name'], -4);
						// $pvar_imagem_nome = "prod_var_".date("dmYHis_") . $x . $pvar_imagem_nome . $ext;

						$path_parts = pathinfo($pvar_imagem['name']);
						$nome_imagem = Text::normalize($path_parts['filename']);
						$ext = $path_parts['extension'];
						$pvar_imagem_nome = "prod_var_".date("dmYHis_") . $x . $nome_imagem .".". $ext;

						//move a imagem para a pasta de imagens
						if(move_uploaded_file($pvar_imagem['tmp_name'], $this->pasta_modulo_images.$pvar_imagem_nome)){
		                 //trata o tamanho da imagem
		                 $img->load($this->pasta_modulo_images . $pvar_imagem_nome)->best_fit(980,980)->save($this->pasta_modulo_images . $pvar_imagem_nome);
		                 //Remove a imagem anterior
		                 if(file_exists($this->pasta_modulo_images.$pvar_imagem_old)){
		                 		unlink($this->pasta_modulo_images.$pvar_imagem_old);
		                 }
						}
					}
					if ($pvar_imagem_del==1) {//Remove a imagem atual.
						if(file_exists($this->pasta_modulo_images.$pvar_imagem_nome)){
		                 	unlink($this->pasta_modulo_images.$pvar_imagem_nome);
		                }
		                $pvar_imagem_nome='Null';
					}
					$pvar_array['imagem'] = $pvar_imagem_nome;
					$dados = parent::sqlCRUD($pvar_array, '', $this->TB_PRODUTO_VARIACAO, $pvar_array_acao_txt, $pvar_array_acao, 0, 0);

					$variacao_idx = ($pvar_id!=0)?$pvar_id:$dados;
					/*Inclui os dados relacionados da variação*/
					$dadosc = self::listProdutoDadosC(1);
					if (is_array($dadosc)&&count($dadosc)>0){
						foreach ($dadosc as $key => $dado) {
							$pvar_cdado = isset($_POST['pvar_'.Text::toAscii($dado['nome']).'_'.$_pvar_cd]) ? Text::clean($_POST['pvar_'.Text::toAscii($dado['nome']).'_'.$_pvar_cd]) : 0;
							$arr_dado = explode("-",$pvar_cdado);
							if(count($arr_dado)==2){
								$var_dado = $arr_dado[0];
								$var_valor = $arr_dado[1];
								$pvar_dadosc_array = array(
									'variacao_idx' => $variacao_idx,
									'dado_idx' => $var_dado,
									'valor_idx' => $var_valor
								);
								$p_var_dado = self::listProdutoVarDadosC($variacao_idx,$var_dado,0,1);

								if(is_array($p_var_dado)&&count($p_var_dado)>0){ //Atualiza
									if (count($p_var_dado)>1) {
										parent::delete("DELETE FROM ".$this->TB_PRODUTO_VAR_DADO." Where variacao_idx=".round($variacao_idx)." And dado_idx=".round($var_dado)." ");
										$varDadosC = parent::sqlCRUD($pvar_dadosc_array, '', $this->TB_PRODUTO_VAR_DADO, '', 'I', 0, 0);
									}else{
										$varDadosC = parent::update("UPDATE ".$this->TB_PRODUTO_VAR_DADO." Set valor_idx=".round($var_valor)." Where variacao_idx=".round($variacao_idx)." And dado_idx=".round($var_dado)." ");
									}
								}else{ //Insere
									$varDadosC = parent::sqlCRUD($pvar_dadosc_array, '', $this->TB_PRODUTO_VAR_DADO, '', 'I', 0, 0);
								}
							}
						}
					}

				}
			}

			/**
			 * Caso alguma variação - ou o produto em si - tenha quantidade diferente de 0 (zero),
			 * Chama-se um método que verifica se tem alguém aguardando a chegada daquele produto.
			 */
			if($atualizacaoQuantidade !== 0){
				self::sendMailToAviseme($pid);
			}

			/**
			 * Atualiza as urls amigáveis caso o nome seja atualizado
			 */
			if(trim($nomeOld)!=trim($array['nome'])){
				self::urlRewriteUpdate();
			}

			ob_end_clean();
			if(isset($dados) && $dados !== FALSE){
				if ($dados == true){
					Sis::setAlert('Dados atualizados com sucesso!', 3,"?mod=".$this->mod."&pag=".$this->pag."&act=edit&pid=".$pid);
				}else{
					Sis::setAlert('O registro informado j&aacute; existe!', 4);
				}
			}else {
				Sis::setAlert('Ocorreu um erro ao salvar os dados!<br/>'.$logError, 4);
			}
		}

		/*
		* Identifica e redefine os produtos em promoção com data de expiração.
		*/
		public function ofertaExpiresReset()
		{
			$produtos_oferta = self::select("SELECT count(produto_idx) as total FROM ".$this->TB_PRODUTO." WHERE em_oferta_expira=1 And em_oferta_expira_data<'".date("Y-m-d H:i:s")."' ");

			if (is_array($produtos_oferta)&&count($produtos_oferta)>0) {
				if ((int)$produtos_oferta[0]['total']>0) {
					//Redefine os vencidos
					self::update("UPDATE ".$this->TB_PRODUTO." SET em_oferta=0, em_oferta_valor=0, em_oferta_expira=0, em_oferta_expira_data=NULL WHERE em_oferta_expira=1 And em_oferta_expira_data < '".date("Y-m-d H:i:s")."' ");
				}
			}
		}

		public function theDelete()
		{
			$pid = isset($_GET['pid']) ? Text::clean((int)$_GET['pid']) : "";
			$checkImagens = self::listAllProdImages($pid);
			if(is_array($checkImagens) && count($checkImagens)>0)
			{
				foreach($checkImagens as $image){
					if(file_exists($this->pasta_modulo_images.$image['imagem'])){
						unlink($this->pasta_modulo_images.$image['imagem']);
					}
					if(file_exists($this->pasta_modulo_images_p.$image['imagem'])){
						unlink($this->pasta_modulo_images_p.$image['imagem']);
					}
					if(file_exists($this->pasta_modulo_images_m.$image['imagem'])){
						unlink($this->pasta_modulo_images_m.$image['imagem']);
					}
					if(file_exists($this->pasta_modulo_images_g.$image['imagem'])){
						unlink($this->pasta_modulo_images_g.$image['imagem']);
					}
				}
			}
			$checkVariacao = self::listAllProdVariacao($pid);
			if(is_array($checkVariacao) && count($checkVariacao)>0)
			{
				foreach($checkVariacao as $variacao)
				{
					if(file_exists($this->pasta_modulo_images.$variacao['imagem'])){
						unlink($this->pasta_modulo_images.$variacao['imagem']);
					}
				}
			}
			$array = array(
		   	'produto_idx' => $pid
			);
			//Dado complementares do produto
			$dados_vdadosc = parent::sqlCRUD($array, '', $this->TB_PRODUTO_VAR_DADO, '', 'D', 0, 0);

			$dadosa = parent::sqlCRUD($array, '', $this->TB_PRODUTO_IMAGEM, 'ECOMMERCE - PRODUTOS - IMAGEM - EXCLUIR', 'D', 0, 0);
			$dadosb = parent::sqlCRUD($array, '', $this->TB_PRODUTO_VARIACAO, 'ECOMMERCE - PRODUTOS - VARIAÇÃO - EXCLUIR', 'D', 0, 0);
			$dados = parent::sqlCRUD($array, '', $this->TB_PRODUTO, 'ECOMMERCE - PRODUTOS - EXCLUIR', 'D', 0, 0);
			
			self::urlRewriteUpdate();

			ob_end_clean();
			if(isset($dados) && $dados !== NULL){
				Sis::setAlert('Dados removidos com sucesso!', 3,"?mod=".$this->mod."&pag=".$this->pag."");
			} else {
				Sis::setAlert('Ocorreu um erro ao remover os dados!', 4);
			}
		}

		public function deleteVariacao($pid=0,$var=0)
		{
			$pid=(int)$pid;
			$var=(int)$var;

			$checkVariacao = self::listSelectedProdVar($pid,$var);
			if(is_array($checkVariacao) && count($checkVariacao)>0)
			{
				foreach($checkVariacao as $variacao)
				{
					if(file_exists($this->pasta_modulo_images.$variacao['imagem'])){
						unlink($this->pasta_modulo_images.$variacao['imagem']);
					}
				}
			}
			$array = array(
		   	'produto_idx' => $pid,
		   	'variacao_idx' => $var
			);
			//Dado complementares da variacao
			$array_vdadosc = array(
		   	'variacao_idx' => $var
			);
			$dados_vdadosc = parent::sqlCRUD($array_vdadosc, '', $this->TB_PRODUTO_VAR_DADO, '', 'D', 0, 0);

			$dados = parent::sqlCRUD($array, '', $this->TB_PRODUTO_VARIACAO, 'ECOMMERCE - PRODUTOS - VARIAÇÃO - EXCLUIR', 'D', 0, 0);
			return $dados;
		}

		public function listSelected($id)
		{
			$array = array(
		      'produto_idx' => $id
			);
			$dados = parent::sqlCRUD($array, '', $this->TB_PRODUTO, '', 'S', 0, 0);
			if(isset($dados) && $dados !== NULL){
				return $dados;
			} else {
				return false;
			}
		}

		public function listSelectedProdVar($pid=0,$id=0)
		{
			$array = array(
		      'produto_idx' => $pid,
		      'variacao_idx' => $id
			);
			$dados = parent::sqlCRUD($array, '', $this->TB_PRODUTO_VARIACAO, '', 'S', 0, 0);
			if(isset($dados) && $dados !== NULL){
				return $dados;
			} else {
				return false;
			}
		}

		public function listAllProdImages($id)
		{
			$array = array(
		      'produto_idx' => $id,
		      'orderby' => " Order By ranking DESC"
			);
			$dados = parent::sqlCRUD($array, '', $this->TB_PRODUTO_IMAGEM, '', 'S', 0, 0);
			if(isset($dados) && $dados !== NULL){
				return $dados;
			} else {
				return false;
			}
		}

		public function listAllProdVariacao($id)
		{
			$array = array(
		      'produto_idx' => $id,
		      'orderby' => " Order By ranking DESC"
			);
			$dados = parent::sqlCRUD($array, '', $this->TB_PRODUTO_VARIACAO, '', 'S', 0, 0);
			if(isset($dados) && $dados !== NULL){
				return $dados;
			} else {
				return false;
			}
		}

		/**
		 * Método que verifica se tem alguém aguardando a chegada daquele produto.
		 * @param Int $produto_idx => Id do produto
		 * @return void
		 */
		private function sendMailToAviseme($produto_idx)
		{
			$array = array('produto_idx' => (int)$produto_idx, 'status' => 0);
			$checkUsersAviseme = parent::sqlCRUD($array, '', $this->TB_AVISEME, '', 'S', 0, 0);
			if(is_array($checkUsersAviseme) && count($checkUsersAviseme) > 0){
				$nome = "";
				$email = "";
				$infoUser = "";
				foreach ($checkUsersAviseme as $key => $value) {
					parent::sqlCRUD(array('aviseme_idx' => $value['aviseme_idx'], 'status' => 1), '', $this->TB_AVISEME, '', 'U', 0, 0);

					if(!is_null($value['cadastro_idx']) && $value['cadastro_idx'] != 0){
						$infoUser = parent::sqlCRUD(array('cadastro_idx' => (int)$value['cadastro_idx']), '', $this->TB_CADASTRO, '', 'S', 0, 0);
					}

					if(!is_null($value['email']) && $value['email'] != ""){
						$nome = $value['nome'];
						$email = $value['email'];
					}else if(is_array($infoUser) && count($infoUser) > 0){
						$infoUser = $infoUser[0];
						$email = $infoUser['email'];
						$nome = $infoUser['nome_completo'];
					}

					$productInfo = parent::select(" SELECT tbProd.*,
					                              (SELECT imagem FROM ".$this->TB_PRODUTO_IMAGEM." Where produto_idx=tbProd.produto_idx Limit 0,1 ) as pimage
															FROM ".$this->TB_PRODUTO." as tbProd");

					if(is_array($productInfo) && count($productInfo) > 0){

						$productInfo = $productInfo[0];

						$emailBody = " <style type='text/css'>
											@import url(http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700);
											.email{ background-color: #f9f9f9; padding: 20px 50px; max-width:760px; font-size: 15px; } .email *{font-family: 'Open Sans Condensed', sans-serif; color:#777777; } .email small{ font-size: 80%; } .panel{ padding: 10px 20px 15px 20px; border:1px solid #f5f5f5; background-color:#fff; border-radius:3px; margin-bottom:10px;  } .panel span{ color: #000; } .panel h2{ font-size:18px; margin:0px; padding: 0px; } .panel h1{ font-size:26px; margin:0px; padding: 0px; } .panel .w50{ width: 50%; } .panel .w100{ width: 100%; } .pull-left{ float:left; } .pull-right{ float:right; } .clear{ clear:both; } .table td, .table th{ padding: 5px; text-align: left } .email hr{ border:0px; background-color: transparent; border-bottom:1px solid #eee; }
											.btn{ background: #3f90c9; padding: 7px 11px; color: #fff; border-radius: 4px; }
											</style><div class='email'>";
						$emailBody .= 	"<div class='panel'><h1>Produto em estoque<br /><br /></h1><p>Olá".(($nome != "") ? ", ".$nome."!" : "")."</p>";
						$emailBody .= 	"<p>Temos uma boa notícia para você! O produto que você procurou no <b>".Sis::config("CLI_NOME")."</b> já está disponível em nosso estoque.</p>";
						$emailBody .= 	"<p>Você pediu e por isso estamos avisando em primeira mão sobre essa disponibilidade.</p>";
						$emailBody .= 	"<p>Aproveite e faça já o seu pedido, pois a quantidade desse item é limitada!</p>";
						$emailBody .= 	"<small>*Essa mensagem é apenas um aviso e não garante a reserva do produto.</small></div>";

						$emailBody .= "<hr />";
						$emailBody .= "<div class='produto'>";
						$emailBody .= "<table class='table w100' >
												<tr>
													<td>
														<a href='http://".$_SERVER["HTTP_HOST"]."/produtos-detalhes/".$productInfo['produto_idx']."/".Text::friendlyUrl($productInfo['nome'])."/".base64_encode("avise_me-".$value['aviseme_idx'])."' target='_blank'>
															<img src='http://".$_SERVER["HTTP_HOST"]."/sitecontent/ecommerce/produto/images/m/".$productInfo['pimage']."' width='200' />
														</a>
													</td>
													<td>
														<a href='http://".$_SERVER["HTTP_HOST"]."/produtos-detalhes/".$productInfo['produto_idx']."/".Text::friendlyUrl($productInfo['nome'])."/".base64_encode("avise_me-".$value['aviseme_idx'])."' target='_blank'>
															<h3>".$productInfo['nome']."</h3>
															<div class='clear'></div>
															<p>".$productInfo['descricao_curta']."</p>
														</a>
														<div class='clear'></div>
														<a href='http://".$_SERVER["HTTP_HOST"]."/produtos-detalhes/".$productInfo['produto_idx']."/".Text::friendlyUrl($productInfo['nome'])."/".base64_encode("avise_me-".$value['aviseme_idx'])."' target='_blank' class='btn'>Comprar</a>
													</td>
												</tr>
											</table>";
						$emailBody .= "</div>";

						$emailBody .= "</div>";

						if(class_exists("PHPMailer")){
							/**
							 * ================================
							 * 			DESCOMENTAR
							 * ================================
							 */

									// $mail = new PHPMailer();
									// $mail->CharSet     = "UTF-8";
									// $mail->ContentType = "text/html";

									// $mail->IsSMTP();
									// $mail->Host        = Sis::config("CLI_SMTP_HOST");
									// if(Sis::config("CLI_SMTP_HOST_PORTA")!="undefined"){ $mail->Port = Sis::config("CLI_SMTP_HOST_PORTA"); }
									// if(Sis::config("CLI_SMTP_CONEXAO")){ $mail->SMTPSecure = Sis::config("CLI_SMTP_CONEXAO"); }

									// if(Sis::config("CLI_SMTP_MAIL")!="")
									// {
									// 	$mail->SMTPAuth    = true;
									// 	$mail->Username    = Sis::config("CLI_SMTP_MAIL");
									// 	$mail->Password    = Sis::config("CLI_SMTP_PASS");
									// }
									// $CLI_MAIL_CONTATO = explode(",",trim(Sis::config("CLI_MAIL_CONTATO")));
									// $fromEmail = trim($CLI_MAIL_CONTATO[0]);
									// $mail->From        = $fromEmail;
									// $mail->FromName    = Sis::config("CLI_NOME");

									// $mail->AddAddress(trim($email), $nome);
									// $mail->AddBCC(trim(Sis::config("ECOMMERCE_EMAIL_ATENDIMENTO")));

									// $mail->AddReplyTo(Sis::config("CLI_EMAIL"), Sis::config("CLI_NOME"));
									// $mail->Subject = "O produto que você deseja já está disponível!";
									// $mail->Body = $emailBody;
									// $mail->Send();

							/**
							 * ================================
							 * 			/DESCOMENTAR
							 * ================================
							 */
						}

					}
				}
			}
		}

		public function checkRatings($produto_idx, $campos="")
		{
			return parent::sqlCRUD(array('produto_idx' => $produto_idx), $campos, $this->TB_PRODUTO_COMENTARIO, '', 'S', 0, 0);
			return false;
		}

		public function listAllRatings($produto_idx, $campos="")
		{
			return parent::select("SELECT DISTINCT Comm.*, Usr.nome_completo, Usr.nome_informal FROM " . $this->TB_PRODUTO_COMENTARIO . " as Comm
				                        INNER JOIN " . $this->TB_CADASTRO . " as Usr On Usr.cadastro_idx=Comm.cadastro_idx
												WHERE produto_idx=".$produto_idx."");
		}

		public function aproveRating($produto_idx, $avaliacao_idx){
			if(is_numeric($avaliacao_idx) && $avaliacao_idx != 0){

				$dados = parent::sqlCRUD(array('comentario_idx' => $avaliacao_idx, 'status' => 1), '', $this->TB_PRODUTO_COMENTARIO, '', 'U', 0, 0);
				$produto = self::listSelected($produto_idx);
				$comentario = parent::sqlCRUD(array('comentario_idx' => $avaliacao_idx), '', $this->TB_PRODUTO_COMENTARIO, '', 'S', 0, 0);
				$emailTo = "";
				$nameTo = "";
				if (isset($dados) && $dados != FALSE && is_array($produto) && count($produto)>0 && is_array($comentario) && count($comentario) > 0){
					$comentario = $comentario[0];
					$produto 	= $produto[0];
					$imagens = self::listAllProdImages($produto['produto_idx']);
					$emailBody = " <style type='text/css'>
										@import url(http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700);
										.email{ background-color: #f9f9f9; padding: 20px 50px; max-width:760px; font-size: 15px; } .email *{font-family: 'Open Sans Condensed', sans-serif; color:#777777; } .email small{ font-size: 80%; } .panel{ padding: 10px 20px 15px 20px; border:1px solid #f5f5f5; background-color:#fff; border-radius:3px; margin-bottom:10px;  } .panel span{ color: #000; } .panel h2{ font-size:18px; margin:0px; padding: 0px; } .panel h1{ font-size:26px; margin:0px; padding: 0px; } .panel .w50{ width: 50%; } .panel .w100{ width: 100%; } .pull-left{ float:left; } .pull-right{ float:right; } .clear{ clear:both; } .table td, .table th{ padding: 5px; text-align: left } .email hr{ border:0px; background-color: transparent; border-bottom:1px solid #eee; }
										.btn{ background: #3f90c9; padding: 7px 11px; color: #fff; border-radius: 4px; }
										</style><div class='email'>";
					$emailBody .= 	"<div class='panel'><h1>Sua avaliação foi publicada<br /><br /></h1><p>Olá!</p>";
					$emailBody .= 	"<p>Sua opnião é muito importante para nós e para os clientes ".Sis::config("CLI_NOME").". Obrigado por compartilhá-la conosco. ";
					$emailBody .= 	"Obrigado por contribuir escrevendo sobre o produto ".$produto['nome'].".</p>";
					$emailBody .= 	"<hr />";
					$emailBody .= 	"<h3>".$comentario['titulo']."</h3>";
					$emailBody .= 	"<p>".$comentario['comentario']."</p>";
					$emailBody .= 	"<hr />";
					$emailBody .= 	"<p>Volte sempre, estamos ansiosos para ouvir mais!</p>";
					$emailBody .= 	"</div>";


					$emailBody .= "</div>";

					// var_dump($emailBody);
					// die();

					if(class_exists("PHPMailer")){
						$usuario = parent::sqlCRUD(array('cadastro_idx' => $comentario['cadastro_idx']), '', $this->TB_CADASTRO, '', 'S', 0, 0);
						if(is_array($usuario) && count($usuario) > 0){
							$usuario = $usuario[0];
							$emailTo = $usuario['email'];
							$nameTo = $usuario['nome_completo'];;
						}


						/**
						 * ================================
						 * 			DESCOMENTAR
						 * ================================
						 */

								// $mail = new PHPMailer();
								// $mail->CharSet     = "UTF-8";
								// $mail->ContentType = "text/html";

								// $mail->IsSMTP();
								// $mail->Host        = Sis::config("CLI_SMTP_HOST");
								// if(Sis::config("CLI_SMTP_HOST_PORTA")!="undefined"){ $mail->Port = Sis::config("CLI_SMTP_HOST_PORTA"); }
								// if(Sis::config("CLI_SMTP_CONEXAO")){ $mail->SMTPSecure = Sis::config("CLI_SMTP_CONEXAO"); }

								// if(Sis::config("CLI_SMTP_MAIL")!="")
								// {
								// 	$mail->SMTPAuth    = true;
								// 	$mail->Username    = Sis::config("CLI_SMTP_MAIL");
								// 	$mail->Password    = Sis::config("CLI_SMTP_PASS");
								// }
								// $mail->From        = Sis::config("CLI_EMAIL");
								// $mail->FromName    = Sis::config("CLI_NOME");

								// $mail->AddAddress(trim($emailTo), $nameTo);
								// $mail->AddBCC(trim(Sis::config("ECOMMERCE_EMAIL_ATENDIMENTO")));

								// $mail->AddReplyTo(Sis::config("CLI_EMAIL"), Sis::config("CLI_NOME"));
								// $mail->Subject = "Sua avaliação foi publicada.";
								// $mail->Body = $emailBody;
								// $mail->Send();

						/**
						 * ================================
						 * 			/DESCOMENTAR
						 * ================================
						 */
					}

					Sis::setAlert('Dados atualizados com sucesso!', 3,"?mod=".$this->mod."&pag=".$this->pag."&act=avaliacao-list&pid=".$produto_idx);
				}else{
					Sis::setAlert('Ocorreu um erro ao salvar os dados!', 4);
				}
			}else{
				Sis::setAlert('Ocorreu um erro ao salvar os dados!', 4);
			}
		}

		public function deleteRating()
		{
			$pid = isset($_GET['pid']) ? (int)Text::clean($_GET['pid']) : 0;
			$aid = isset($_GET['aid']) ? (int)Text::clean($_GET['aid']) : 0;
			if($pid != 0){
				$dados = parent::sqlCRUD(array('comentario_idx' => $aid), '', $this->TB_PRODUTO_COMENTARIO, '', 'D', 0, 0);

				ob_end_clean();
				if(isset($dados) && $dados !== NULL){
					Sis::setAlert('Dados removidos com sucesso!', 3,"?mod=".$this->mod."&pag=".$this->pag."&act=avaliacao-list&pid=".$pid);
				} else {
					Sis::setAlert('Ocorreu um erro ao remover os dados!', 4);
				}
			}else{
				Sis::setAlert('Ocorreu um erro ao remover os dados!', 4);
			}
		}

		/**
         * Funcao para atualizar as regras de URL amigável
        **/
        public function urlRewriteUpdate()
        {

            $uriList = array();
            $regList = self::listAll(array('status'=>1));
            $paginaCodigo = 34; //Código da página no módulo de conteúdo.
            $paginaNome = "produto"; //Código da página no módulo de conteúdo.

            if(is_array($regList) && count($regList) > 0){
                foreach ($regList as $key => $reg){
                		$arrayPaginas = array(
                        "url" => $paginaNome."/".Text::friendlyUrl($reg['nome']),
                        "nome" => $reg['nome'],
                        "acao" => "?pagina=" . $paginaCodigo . "&prodId=" . $reg['produto_idx']
                    );
                    array_push($uriList, $arrayPaginas);
                }
            }

            $informacoes = array(
                    "changefreq" => "monthly", "priority" => "1.0", "lastmod" => date("Y-m-d")
            );
            $urlr = new UrlRewriteController($this->MODULO_CODIGO.'2'); //1 indica urls gerados de produtos
            $urlr->setAllUrlRules($uriList, $informacoes);
        }

        public function textEstoque($idEstoque){
        	switch ($idEstoque) {
        		case 1:
        			return "Com Estoque";
        			break;
        		case 2:
        			return "Sem Estoque";
        			break;
        		case 3:
        			return "Baixo Estoque";
        			break;
        		
        		default:
        			# code...
        			break;
        	}
        }


	} //End class
?>