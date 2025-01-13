<?php

	/**
	* Classe Controladora dos depoimentos.
	*/
	class depoimento extends depoimento_model
	{

		/**
		* Variáveis que armazenam os nomes das tabelas relacionadas a esse módulo.
		*/

		public $mod, $pag, $act;

		public $MODULO_CODIGO   = "10011";
		public $MODULO_AREA     = "Depoimento";

		/**
		* Médoto construtor, que verifica se as pastas relacionadas aos depoimentos já foram criadas. Caso não seja, ele cria para que as imagens sejam armazandas nelas.
		*/
		function __construct()
		{
			parent::__construct();
			global $mod, $pag, $act;
			$this->mod = $mod;
			$this->pag = $pag;
			$this->act = $act;

			$this->pasta_modulo = "..".DS.PASTA_CONTENT.DS.$this->mod;
			$this->pasta_modulo_pag = $this->pasta_modulo.DS.$this->pag.DS;
			
			if(!is_dir($this->pasta_modulo)){
				mkdir($this->pasta_modulo);
			}
			if(!is_dir($this->pasta_modulo_pag)){
				mkdir($this->pasta_modulo_pag);
			}
		}

		/**
		* Método para listar todas os depoimentos.
		*/
		public function listAll(){
			$array = array('orderby' => 'ORDER BY ranking DESC');
			$dados = parent::sqlCRUD($array, '', $this->TB_DEPOIMENTO, '', 'S', 0, 0);
			if(is_array($dados) && count($dados) > 0){
				return $dados;
			} else  {
				return false;
			}
		}


		/**
		* Insere o depoimento com suas devidas imagens
		* @return bollean
		*/
		public function theInsert()
		{
			$array = array(
		      'nome' 				=> isset($_POST['nome']) 				? Text::clean($_POST['nome']) : "",
		      'status' 			=> ($_POST['status'] != "") 			? Text::clean((int)$_POST['status']) : 0,
		      'ranking' 			=> 0,
			  'descricao' 		=> ($_POST['descricao'] != "") 		? Text::clean($_POST['descricao']) 	: "",
			  'imagem' => 'null'
			);

			$arquivo = isset($_FILES["imagem"]) ? $_FILES["imagem"] : "";
			if ($arquivo <> ""){
				if (!empty($arquivo["name"])) {
					// Verifica se o arquivo enviado é compativél com o formato escolhido
					
					if(!preg_match("/^image\/(pjpeg|jpeg|png|gif|bmp)$/", $arquivo["type"])){
						Sis::setAlert('O arquivo escolhido não é uma imagem!', 2);
					}
					
					$nome_arquivo = $arquivo["name"];
					preg_match("/\.(gif|bmp|png|jpg|jpeg|swf){1}$/i", $nome_arquivo, $ext);
					$nome_arquivo = str_replace($ext[1], "", $nome_arquivo);
					$nome_arquivo = Text::clean($nome_arquivo) . "-" . rand() . "." . $ext[1];
					$local = $this->pasta_modulo_pag.$nome_arquivo;
					move_uploaded_file($arquivo["tmp_name"], $local);
					$array['imagem'] = $nome_arquivo;
				}
			}

			$dados = parent::sqlCRUD($array, '', $this->TB_DEPOIMENTO, 'DEPOIMENTO - INSERIR', 'I', 0, 0);
			$depoimento_idx = $dados;

			ob_end_clean();
			if(isset($dados) && $dados !== NULL){
				if ($dados == true){
					Sis::setAlert('Dados salvos com sucesso!', 3,"?mod=" . $_GET['mod'] . "&pag=" . $_GET['pag'] . "");
				}else{
					Sis::setAlert('O registro informado j&aacute; existe!', 4);
				}
			}else {
				Sis::setAlert('Ocorreu um erro ao salvar os dados!', 4);
			}
		}


		/**
		* Lista as imagens de um determinado depoimento
		* @return bollean
		* @param int $id
		*/
		// public function listImages($id)
		// {
		// 	$array = array(
		//       'depoimento_idx' => $id,
		//       'orderby' => " Order By ranking DESC"
		// 	);
		// 	$dados = parent::sqlCRUD($array, '', $this->TB_DEPOIMENTO_IMAGE, '', 'S', 0, 0);
		// 	if(is_array($dados) && count($dados) > 0){
		// 		return $dados;
		// 	} else {
		// 		return false;
		// 	}
		// }


		/**
		* Exclui um depoimento passada por parâmetro e suas respectivas imagens
		* @return bollean
		*/
		public function theDelete()
		{
			$gid = isset($_GET['gid']) ? Text::clean((int)$_GET['gid']) : 0;
			
			$array = array(
		      'depoimento_idx' => $gid
			);
			$dados = parent::sqlCRUD($array, '', $this->TB_DEPOIMENTO, 'DEPOIMENTO - EXCLUIR', 'D', 0, 0);


			ob_end_clean();
			if(isset($dados) && $dados !== FALSE){
				Sis::setAlert('Dados removidos com sucesso!', 3,"?mod=".$_GET['mod']."&pag=".$_GET['pag']."");
			} else {
				Sis::setAlert('Ocorreu um erro ao remover os dados!', 4);
			}
		}


		/**
		* Lista a categoria selecionada
		* @return bollean
		* @param int $id
		*/
		public function listSelected($id)
		{
			$array = array(
		      'depoimento_idx' => $id
			);
			$dados = parent::sqlCRUD($array, '', $this->TB_DEPOIMENTO, '', 'S', 0, 0);
			if(is_array($dados) && count($dados)){
				return $dados;
			} else {
				return false;
			}
		}


		/**
		* Função para alterar um depoimento e os dados que estão associados a ela. Por exemplo, suas imagens
		* @return bollean
		* @param int $id
		*/
		public function theUpdate(){
			$gid 		= isset($_POST['gid']) ? Text::clean($_POST['gid']) : 0;
			$nome_old = isset($_POST["nome_old"]) ? Text::clean($_POST["nome_old"]) : "";
			$arquivo_old = isset($_POST["arquivo_old"]) ? Text::clean($_POST["arquivo_old"]) : "";

			$array_g = array(
		      'depoimento_idx' 		=> $gid,
		      'nome' 				=> isset($_POST['nome']) ? Text::clean($_POST['nome']) : "",
		      'status' 			=> ($_POST['status'] != "") ? Text::clean((int)$_POST['status']) : 0,
		      'descricao' 		=> ($_POST['descricao'] != "") ? Text::clean($_POST['descricao']) : "",
		      'imagem' 			=> $arquivo_old
			);

			$arquivo = isset($_FILES["imagem"]) ? $_FILES["imagem"] : "";
			
			
			if ($arquivo["error"] <> 4) {
				if (!empty($arquivo["name"])) {
					// Verifica se o arquivo enviado é compativél com o formato escolhido
					
					if(!preg_match("/^image\/(pjpeg|jpeg|png|gif|bmp)$/", $arquivo["type"])){
						Sis::setAlert('O arquivo escolhido não é uma imagem!', 2);
					}
					
					$nome_arquivo = $arquivo["name"];
					preg_match("/\.(gif|bmp|png|jpg|jpeg|swf){1}$/i", $nome_arquivo, $ext);
					$nome_arquivo = str_replace($ext[1], "", $nome_arquivo);
					$nome_arquivo = Text::clean($nome_arquivo) . "-" . rand() . "." . $ext[1];
					$local = $this->pasta_modulo_pag.$nome_arquivo;
					if($arquivo_old <> ""){
						if (file_exists(".." . DS . PASTA_CONTENT . DS . $this->mod . DS . $this->pag . $arquivo_old))
						{
							unlink(".." . DS . PASTA_CONTENT . DS . $this->mod . DS . $this->pag . $arquivo_old);
						}
					}
					move_uploaded_file($arquivo["tmp_name"], $local);
					$arquivo = $nome_arquivo;

					$array_g['imagem'] = $arquivo;
				}
			}
			$dados = parent::sqlCRUD($array_g, '', $this->TB_DEPOIMENTO, 'DEPOIMENTO - UPDATE', 'U', 0, 0);

			if (trim($nome_old)!=trim($array_g['nome'])) {

			}

			ob_end_clean();
			if(isset($dados) && $dados !== NULL){
				if ($dados == true){
					Sis::setAlert('Dados atualizados com sucesso!', 3,"?mod=".$_GET['mod']."&pag=".$_GET['pag']."");
				}else{
					Sis::setAlert('O registro informado j&aacute; existe!', 4);
				}
			}else {
				Sis::setAlert('Ocorreu um erro ao salvar os dados!', 4);
			}
		}

	} //End class
?>