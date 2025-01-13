<?php
class EcommerceCadastro extends HandleSql
{

    private $TB_CADASTRO;
    private $TB_CURSO;
    private $TB_CADASTRO_CURSO_AFILIADO;

	function __construct(){
		parent::__construct();
        $this->TB_CADASTRO = self::getPrefix() . "_cadastro";
        $this->TB_CURSO = self::getPrefix() . "_curso";
        $this->TB_CADASTRO_CURSO_AFILIADO = self::getPrefix() . "_cadastro_afiliado_curso";
	}

	public function getProdutorByCursoID($cursoId)
	{
		return self::select("SELECT tbCad.* FROM ".$this->TB_CADASTRO." as tbCad
			INNER JOIN ".$this->TB_CURSO." as tbCur ON tbCad.cadastro_idx=tbCur.produtor_idx WHERE tbCur.curso_idx=" . $cursoId);
	}


	//Afiliados
	public function afiliadoLoad($codigo,$cursoId,$silence=true)
	{
		$sqlQuery = "SELECT tbCur.afiliado_comissao as curso_afiliado_comissao,tbAflCur.desconto_afiliado,tbAflCur.comissao as afiliado_comissao,
			tbCad.cadastro_idx,tbCad.nome_completo,tbCad.afiliado_codigo,tbCad.iugu_split_account_id
			FROM ".$this->TB_CURSO." as tbCur
			INNER JOIN ".$this->TB_CADASTRO_CURSO_AFILIADO." as tbAflCur ON tbAflCur.curso_idx=tbCur.curso_idx
			INNER JOIN ".$this->TB_CADASTRO." as tbCad ON tbAflCur.cadastro_idx=tbCad.cadastro_idx
			WHERE tbCad.afiliado_codigo like '".Text::clean($codigo)."' And tbCur.curso_idx=".$cursoId."
		";
		$afiliadoData = self::select($sqlQuery);
		if (is_array($afiliadoData)&&count($afiliadoData)>0) {
			if ((float)$afiliadoData[0]['desconto_afiliado']>0) {
				//Desativa o desconto do cupom.
				unset($_SESSION['ecommerce_cupom']);
			}
			$_SESSION['platform_afiliado'] = $afiliadoData[0];
		}else{
			if (!$silence) {
				throw new Exception("Não existe afiliado com o código informado para o curso.", 1);
			}	
		}
	}

	public function afiliadoUnload($value='')
	{
		$_SESSION['platform_afiliado']=false;
		unset($_SESSION['platform_afiliado']);
	}

	public function getProdutorByLPUrl($lpURI)
	{
		
		if (trim($lpURI)!='') {
			return self::select("SELECT tbCad.* FROM ".$this->TB_CADASTRO." as tbCad WHERE tbCad.lp_url like '".trim($lpURI)."' ");
		}
		return false;

	}

	

}