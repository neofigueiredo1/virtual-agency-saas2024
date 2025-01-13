<?php

require_once("ecommerce.pedido.class.php");
require_once("ecommerce.carrinho.class.php");

class EcommerceCupom extends HandleSql{

	private $TB_CUPOM;
	private $TB_CUPOM_CADASTRO;
	private $TB_CUPOM_UTILIZACAO;
	private $TB_PEDIDO;

	function __construct(){
		parent::__construct();
		$this->TB_CUPOM = self::getPrefix() . "_ecommerce_cupom";
		$this->TB_CUPOM_CADASTRO = self::getPrefix() . "_ecommerce_cupom_cadastro";
		$this->TB_CUPOM_UTILIZACAO = self::getPrefix() . "_ecommerce_cupom_utilizacao";
		$this->TB_PEDIDO = self::getPrefix() . "_ecommerce_pedido";
	}

	public function getCupom($codigo,$cadastro_idx,$curso_idx=0){

		$dataHoje = date('Y-m-d H:i:s');
		//Verifica se o código existe e é válido.
		$cupom = self::select("SELECT * FROM ".$this->TB_CUPOM." Where status=1 And curso_idx=".(int)$curso_idx." And codigo='".$codigo."' And ( expiracao=0 Or ( expiracao=1 And expiracao_data_ini <= '".$dataHoje."' AND expiracao_data_fim >= '".$dataHoje."') )  ");
		if (is_array($cupom)&&count($cupom)>0) {
			$cupom = $cupom[0];
			//Verificar a restrição de utilização por usuários específicos
			if ($cupom['usuarios_marcados']==1) {
				//Veririca se o usuário consta na lista de utilizadores do cupom.
				$getRelacaoCadastro = self::select("SELECT cupom_idx FROM ".$this->TB_CUPOM_CADASTRO." Where cupom_idx=".$cupom['cupom_idx']." And cadastro_idx=".$cadastro_idx." ");
				// var_dump($cadastro_idx);
				// var_dump($getRelacaoCadastro);
				if (!(is_array($getRelacaoCadastro)&&count($getRelacaoCadastro)>0)) {
					throw new Exception("Você não pode usar um códido que está vinculado a outros clientes, certifique-se de que seu usuário está relacionado a este código promocional.");
				}
				//exit();
			}
			//Verificar a restrição de utilização por usuário
			if ($cupom['limite_utilizacao_usuario']==1) {
				//Veririca se o usuário atual já fez uso do cupom.
				$getUtilizacaoCadastro = self::select("SELECT cupom_idx FROM ".$this->TB_CUPOM_UTILIZACAO." Where cupom_idx=".$cupom['cupom_idx']." And cadastro_idx=".$cadastro_idx." ");
				if (is_array($getUtilizacaoCadastro)&&count($getUtilizacaoCadastro)>0) {
					throw new Exception("O Código informado já foi usado.");
				}
			}
			if ($cupom['limite_utilizacao_geral']>0) {
				//Veririca o numero de utilizações do cupom
				$getUtilizacaoTotal = self::select("SELECT count(cupom_idx) as totalUtilizado FROM ".$this->TB_CUPOM_UTILIZACAO." Where cupom_idx=".$cupom['cupom_idx']." ");
				$cupomUtilizacaoTotal = $getUtilizacaoTotal[0]['totalUtilizado'];
				if ((int)$cupomUtilizacaoTotal>=(int)$cupom['limite_utilizacao_geral']) {
					throw new Exception("O Código informado já excedeu seu limite de uso.");
				}
			}
			//Verificar a restrição de utilização para o primeiro pedido
			if ($cupom['somente_primeiro_pedido']==1) {
				//Veririca se o usuário atual já fez uso do cupom.
				$getPedidosCadastro = self::select("SELECT pedido_idx FROM ".$this->TB_PEDIDO." Where cadastro_idx=".$cadastro_idx."  limit 0,1 ");
				if (is_array($getPedidosCadastro)&&count($getPedidosCadastro)>0) {
					throw new Exception("O Código informado é apenas para o primeiro pedido no site.");
				}
			}

			//Verificar a restrição de utilização para valor mínimo. / Aplicado no carrinho

			return $cupom;

		}else{
			throw new Exception("O Código informado não existe ou não é válido, certifique-se do período de cada campanha.");
		}
	}

	public function getCupomById($id){
		return self::select("SELECT * FROM ".$this->TB_CUPOM." Where cupom_idx=".$id." ");
	}

	//Novo!
	public function getCupomByCodigo($codigo){
		return self::select("SELECT * FROM ".$this->TB_CUPOM." Where codigo='".$codigo."' ");
	}

	public function registraUso($cupom,$cadastro_idx,$pedido_idx,$curso_idx=0){
		//Verifica se o código existe.
		try {

			// $cupom = self::getCupom($codigo,$cadastro_idx);

			$query = "INSERT INTO ".$this->TB_CUPOM_UTILIZACAO."(pedido_idx,cupom_idx,cadastro_idx,tipo,valor,data_cadastro) values(".$pedido_idx.",".$cupom['cupom_idx'].",".$cadastro_idx.",".$cupom['tipo_desconto'].",".number_format($cupom['valor_desconto'],2).",'".date('Y-m-d h:i:s')."') ";
			self::insert($query);

		} catch (Exception $e) {
			throw $e;
		}
	}

	public function cancelaUso($pedido_idx){
		if ((int)$pedido_idx!=0) {
			$query = "DELETE FROM ".$this->TB_CUPOM_UTILIZACAO." WHERE pedido_idx=".(int)$pedido_idx." ";
			self::delete($query);
		}
	}

	//Métodos mais novos

	//O método verifica se o usuário já está relacionado ao cupom ou cupons determinados para uso.
	public function verificaSeUsuarioSeRelacionaACupons($codigos,$cadastro_idx,$curso_idx=0){
		//Verifica se o código existe.
		try {

			$codigos_lista = explode(",",$codigos);
			$whereCodigos = "";
			foreach ($codigos_lista as $key => $codigo) {
				$whereCodigos .= ( ($key>0)?" Or ":"") . " tbCupom.codigo='".$codigo."' ";
			}

			$querySQL = "SELECT CupomCad.cupom_idx,tbCupom.codigo,tbCupom.valor_desconto
						FROM ".$this->TB_CUPOM_CADASTRO."  as CupomCad
						INNER JOIN " .$this->TB_CUPOM. " as tbCupom ON tbCupom.cupom_idx=CupomCad.cupom_idx
						Where CupomCad.cadastro_idx=".$cadastro_idx." And (".$whereCodigos.") ";
			
			//verifica se a relação já existe.
			return self::select($querySQL);

		} catch (Exception $e) {
			throw $e;
		}
	}

	public function relacionaUsuarioAoCupom($codigo,$cadastro_idx,$curso_idx=0){
		//Verifica se o código existe.
		try {
			$cupom = self::getCupomByCodigo($codigo);
			if (is_array($cupom)&&count($cupom)>0) {
				$cupom = $cupom[0];
				$getRelacaoCadastro = self::verificaSeUsuarioSeRelacionaACupons($codigo,$cadastro_idx);
				if (!(is_array($getRelacaoCadastro)&&count($getRelacaoCadastro)>0)) {
					$query = "INSERT INTO ".$this->TB_CUPOM_CADASTRO."(cupom_idx,cadastro_idx) values(".$cupom['cupom_idx'].",".$cadastro_idx.") ";
					self::insert($query);
				}
			}else{
				throw new Exception("O Código informado não existe.");
			}
		} catch (Exception $e) {
			throw $e;
		}
	}

	public function retornaCupomRelacaoQuantidade($codigos,$periodo=array(),$curso_idx=0){
		//retorna a quantidade de usuarios relacionados a um copom para um período determinado.
		try {
			$WherePeriodo = "";
			if (is_array($periodo)&&count($periodo)==2) {
				//Datas nos formatos 2000-01-01 00:00:00
				$data_inicio = $periodo[0];
				$data_fim = $periodo[1];
				$WherePeriodo = " And CupomCad.data BETWEEN '".$data_inicio."' And '".$data_fim."' ";
			}
			$codigos_lista = explode(",",$codigos);
			$whereCodigos = "";
			foreach ($codigos_lista as $key => $codigo) {
				$whereCodigos .= ( ($key>0)?" Or ":"") . " tbCupom.codigo='".$codigo."' ";
			}

			$querySQL = "SELECT tbCupom.cupom_idx,tbCupom.codigo,
						( SELECT count(CupomCad.cadastro_idx) as totalRegistro FROM ".$this->TB_CUPOM_CADASTRO." as CupomCad Where CupomCad.cupom_idx=tbCupom.cupom_idx ".$WherePeriodo." ) as totalRegistro
						FROM " .$this->TB_CUPOM. " as tbCupom
						Where ".$whereCodigos;

			return self::select($querySQL);

		} catch (Exception $e) {
			throw $e;
		}
	}

}