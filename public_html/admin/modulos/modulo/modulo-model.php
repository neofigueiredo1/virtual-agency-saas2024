<?php
	/**
	 * Classe de gerenciamento de dados dos módulos
	 *
	 * @package default
	 * @author
	 **/

	class ModuloModel extends HandleSql {

		public $TB_MODULO;
		public $TB_MODULO_PERMISSAO;

		public $MODULO_CODIGO 	= "0";
		public $MODULO_AREA 		= "Módulo";

		function __construct(){
			parent::__construct();
			$this->TB_MODULO = self::getPrefix() . "_modulo";
			$this->TB_MODULO_PERMISSAO 	= self::getPrefix() . "_modulo_permissao";
		}

		public function mListaTodos()
		{
         $dados_export = array();
         if ($handle = opendir($_SERVER['DOCUMENT_ROOT'] . DS . PASTA_DIRECTIN . '/modulos'))
         {
            /* Essa � a forma correta de rodar o loop sobre a pasta. */
            while (false !== ($entry = readdir($handle)))
            {
               if($entry!="." && $entry!="..")
              	{
                  //Faz a leitura do arquivo de configuracao.
                  $fileName = "modulo.info";
                  $fileLocal = realpath($_SERVER['DOCUMENT_ROOT'] . DS . PASTA_DIRECTIN . '/modulos/'.$entry);
                  $data_modulo = "";
                  if(file_exists($fileLocal.DS.$fileName))
                  {
                     $file = file_get_contents($fileLocal.DS.$fileName, FILE_USE_INCLUDE_PATH);
                     $data_modulo = $file;
                  }
                  array_push($dados_export,'{"modulo":"'.$entry.'","data":['.$data_modulo.']}');
               }
            }
            closedir($handle);
			}

			if(count($dados_export) > 0){
				return $dados_export;
			}else{
				return false;
			}
		}


		public function mListaModuleBackups($module)
		{
	      $dados_export = array();
	      if (file_exists($_SERVER['DOCUMENT_ROOT'] . DS . PASTA_DIRECTIN . DS . 'modulos' .DS.$module.DS."sql-backup"))
	      {
		      if ($handle = opendir($_SERVER['DOCUMENT_ROOT'] . DS . PASTA_DIRECTIN . DS . 'modulos' .DS.$module.DS."sql-backup"))
		      {
					/* Essa é a forma correta de rodar o loop sobre a pasta. */
					while (false !== ($entry = readdir($handle)))
					{
						if($entry!="." && $entry!="..")
						{
			            //Faz a leitura do arquivo de configuracao.
			            $fileLocal = realpath($_SERVER['DOCUMENT_ROOT'] . DS . PASTA_DIRECTIN . DS . 'modulos' .DS.$module.DS.'sql-backup'.DS.$entry);
			            $fdate = "";
			            $fsize = 0;
			            if (file_exists($fileLocal)) {
							    $fdate = date ("d/m/Y H:i:s.", filemtime($fileLocal));
							    $fsize = filesize($fileLocal);
							}
			            array_push($dados_export,'{"file":"'.$entry.'","date":"'.$fdate.'","size":"'.$fsize.'"}');
		        		}
					}
					closedir($handle);
				}
			}
			if(count($dados_export) >= 1){
				return $dados_export;
			}else{
				return false;
			}
		}

		public function mCheckInstall($_id)
		{
			$res = parent::select("SELECT codigo FROM ".$this->TB_MODULO." WHERE codigo=".round($_id));
			if(count($res) >= 1)
			{
				return $res;
			} else {
				return false;
			}
		}

		public function mSelectById($id, $campos=""){
			$res = parent::select("SELECT ".(($campos !== "") ? $campos : "*")." FROM " .$this->TB_MODULO. " WHERE codigo=".round($id)." ");
			return $res;
		}

		public function mModuleBackup($module)
		{
			parent::MySqlDump($module);
		}

		public function mRemover($m_id)
		{
			$moduloName = self::mSelectById($m_id, 'nome');

			if(is_array($moduloName) && count($moduloName) == 1){
				$moduloName = $moduloName[0]['nome'];
			}else{
				$moduloName = "";
			}

			$res = parent::delete("DELETE FROM " .$this->TB_MODULO. " WHERE codigo=".round($m_id)." ");
			$res_perm = parent::delete("DELETE FROM " .$this->TB_MODULO_PERMISSAO. " WHERE modulo_codigo=".round($m_id)." ");

			ob_end_clean();
			if($res == true){
				Sis::insertLog(0, $this->MODULO_AREA, 'DELETE', 0, $moduloName, "");
				return true;
			} else {
				return false;
			}
		}

		public function mInserir($codigo,$nome,$versao,$descricao,$pasta,$dados)
		{
			$check = parent::select("SELECT * FROM ".$this->TB_MODULO." WHERE nome='".$nome."'");

			if($check == true){
				return true;
			}else{
				$res = parent::insert("INSERT INTO  ".$this->TB_MODULO."(
											 codigo,
											 nome,
											 versao,
											 descricao,
											 pasta,
											 dados
										 )VALUES(
											 '".$codigo."',
											 '".$nome."',
											 '".$versao."',
											 '".$descricao."',
											 '".$pasta."',
											 '".$dados."')");
				if($res == true){
					Sis::insertLog(0, $this->MODULO_AREA, 'INSERT', 0, $nome, "");
					return true;
				} else {
					return false;
				}
			}
		}

		public function mInserirPermissao($m_codigo,$p_codigo,$nome,$descricao)
		{
			$check_perm = parent::select("SELECT * FROM ".$this->TB_MODULO_PERMISSAO." WHERE modulo_codigo=".round($m_codigo)." And permissao_codigo=".round($p_codigo)." ");

			if($check_perm == true){
				return true;
			}else{
				$res_perm = parent::insert("INSERT INTO ".$this->TB_MODULO_PERMISSAO."(
											 modulo_codigo,
											 permissao_codigo,
											 nome,
											 descricao
										 )VALUES(
											 '".$m_codigo."',
											 '".$p_codigo."',
											 '".$nome."',
											 '".$descricao."')");
				if($res_perm == true){
					return true;
				} else {
					return false;
				}
			}
		}

	}
?>