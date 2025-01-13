<?php
class banner_model extends HandleSql {

	//Constantes de nome do banco do módulo
	protected $TB_BANNER;
	protected $TB_BANNER_TIPO;
	protected $TB_CONTEUDO_PAGINA;

	function __construct(){
		parent::__construct();
		$this->TB_BANNER = self::getPrefix() . "_banner";
		$this->TB_BANNER_TIPO = self::getPrefix() . "_banner_tipo";
		$this->TB_CONTEUDO_PAGINA = self::getPrefix() . "_conteudo_pagina";
	}

	public function mListaTodosTipos(){
		$res = parent::select("SELECT * FROM " . self::TB_BANNER_TIPO . " ORDER BY nome");

		if(count($res) >= 1){
			return $res;
		}else{
			return false;
		}
	}

	public function mInserirTipo($nome, $perfil, $largura, $altura, $animacao_tempo, $animacao_velocidade, $animacao){
		$check = parent::select("SELECT * FROM ".self::TB_BANNER_TIPO." WHERE nome='".$nome."'");

		if($check == true){
			return false;
		} else {
			$res = parent::insere("INSERT INTO  ".self::TB_BANNER_TIPO."(
										tipo_idx,
										nome,
										perfil,
										largura,
										altura,
										animacao,
										animacao_tempo,
										animacao_velocidade,
										data_cadastro
									)VALUES(
										NULL,
										'". $nome . "',
										'". $perfil . "',
										" . $largura . ",
										" . $altura . ",
										'". $animacao . "',
										". $animacao_tempo . ",
										". $animacao_velocidade . ",
										NULL)");
			if($res == true){
				sis::log("TIPO_DE_BANNER - CADASTRAR");
				return true;
			} else {
				return false;
			}
		}
	}


	public function mRemoverTipo($t_id){
		$res = parent::remove("DELETE FROM " . self::TB_BANNER_TIPO . " WHERE tipo_idx='" .$t_id. "'");

		if($res == true){
			sis::log("TIPO_DE_BANNER - REMOVER");
			return true;
		} else {
			return false;
		}
	}

	public function mListaTipoSel($t_id){
		$res = parent::select("SELECT * FROM ".self::TB_BANNER_TIPO." WHERE tipo_idx=" . $t_id . " LIMIT 1");

		if(count($res) >= 1){
			return $res;
		} else {
			return false;
		}
	}

	public function mAtualizarTipo($t_id, $nome, $perfil, $largura, $altura, $animacao_tempo, $animacao_velocidade, $animacao){
		$check = parent::select("SELECT * FROM ".self::TB_BANNER_TIPO." WHERE nome='".$nome."' AND tipo_idx<>'".$t_id."'");

		if($check == true){
			return false;
		} else {
			$res = parent::atualiza("UPDATE ".self::TB_BANNER_TIPO."
										SET
											nome                = '" . $nome . "',
											perfil              = '" . $perfil . "',
											largura             = " . $largura . ",
											altura              = " . $altura . ",
											animacao_tempo      = " . $animacao_tempo . ",
											animacao_velocidade = " . $animacao_velocidade . ",
											animacao            = '" . $animacao . "'
										WHERE
											tipo_idx = ".$t_id);

			if($res == true){
				sis::log("TIPO_DE_BANNER - EDITAR");
				return true;
			} else {
				return false;
			}
		}
	}

	public function mListaTodos($t_id) {

		$res = parent::select("SELECT * FROM ".self::TB_BANNER." WHERE tipo=" . round($t_id) . " ORDER BY ranking DESC");

		if(count($res) >= 1){
			return $res;
		}else{
			return false;
		}
	}

	public function inserir_m($status, $formato, $alinhamento, $tipo, $pagina, $nome, $descricao, $url, $alvo, $arquivo, $horario, $horario_ini,$horario_fim, $indica_data, $data_publicacao, $data_expiracao, $tipo_list_banner){
		// $check = parent::select("SELECT * FROM ".self::TB_BANNER." WHERE nome='".$nome."'");

		// if($check == true){
			// return false;
		// } else {
			$res = parent::insere("INSERT INTO ".self::TB_BANNER."(
										banner_idx,
										ranking,
										status,
										tipo,
										alinhamento,
										arquivo,
										formato,
										pagina,
										nome,
										descricao,
										url,
										alvo,
										horario,
										horario_ini,
										horario_fim,
										indica_data,
										data_publicacao,
										data_expiracao,
										data_cadastro,
										tipo_list_banner
									)VALUES(
										NULL,
										0,
										 " . $status . ",
										 " . $tipo . ",
										 " . $alinhamento . ",
										'" . $arquivo . "',
										 " . $formato . ",
										'" . $pagina . "',
										'" . $nome . "',
										'" . $descricao . "',
										'" . $url . "',
										'" . $alvo . "',
										 " . $horario . ",
										 " . $horario_ini . ",
										 " . $horario_fim . ",
										 " . $indica_data . ",
										'" . $data_publicacao . "',
										'" . $data_expiracao . "',
										'" . $tipo_list_banner . "',
										NULL)");
			if($res == true){
				sis::log("BANNER - CADASTRAR");
				return true;
			} else {
				return false;
			}
		// }
	}

	public function mListaSel($b_id){
		$res = parent::select("SELECT * FROM " . self::TB_BANNER . " WHERE banner_idx=" . $b_id . " LIMIT 1");

		if(count($res) >= 1){
			return $res;
		} else {
			return false;
		}
	}

	public function atualizar_m($b_id, $status, $formato, $tipo, $alinhamento, $pagina, $nome, $descricao, $url, $alvo, $arquivo, $horario, $horario_ini, $horario_fim, $indica_data, $data_publicacao, $data_expiracao, $tipo_list_banner){
		// $check = parent::select("SELECT * FROM ".self::TB_BANNER." WHERE nome='".$nome."' AND banner_idx<>" . $b_id);


		// if($check == true){
		// 	return false;
		// } else {
			$res = parent::insere("UPDATE " . self::TB_BANNER . "
									SET
										status          =  " . $status . ",
										formato         =  " . $formato . ",
										tipo            =  " . $tipo . ",
										alinhamento     =  " . $alinhamento . ",
										pagina          = '" . $pagina . "',
										nome            = '" . $nome . "',
										descricao       = '" . $descricao . "',
										url             = '" . $url . "',
										alvo            = '" . $alvo . "',
										arquivo         = '" . $arquivo . "',
										horario         =  " . $horario . ",
										horario_ini     =  " . $horario_ini . ",
										horario_fim     =  " . $horario_fim . ",
										indica_data     =  " . $indica_data . ",
										data_publicacao = '" . $data_publicacao . "',
										data_expiracao  = '" . $data_expiracao . "'
										tipo_list_banner  = '" . $tipo_list_banner . "'
									WHERE
										banner_idx = " . $b_id);
			if($res == true){
				sis::log("BANNER - EDITAR");
				return true;
			} else {
				return false;
			}
		// }
	}

	public function remover_m($b_id){
		$res = parent::remove("DELETE FROM " . self::TB_BANNER . " WHERE banner_idx='" .$b_id. "'");

		if($res == true){
			sis::log("BANNER - REMOVER");
			return true;
		} else {
			return false;
		}
	}
}
