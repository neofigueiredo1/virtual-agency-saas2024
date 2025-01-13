<?php
class EcommerceCarrinho extends HandleSql
{

	private $dbPrefix;
	private $TB_CURSO;
	private $TB_CADASTRO;
	private $car;
	
	/**
   	* Estrutura dos dados do carrinho em Json
	*	"{
	*		"cursos":[
	*			{
	*				"curso_idx":"0",
	*				"valor":"0",
	*				"quantidade":"0"
	*			}
	*		]
	*	}
 	*/
 	function __construct(){
 		parent::__construct();
 		$this->TB_CURSO = $this->DB_PREFIX . "_curso";
 		$this->TB_CADASTRO = $this->DB_PREFIX . "_cadastro";
 		self::carInicializa();
 		$this->car = self::carGetSession();
 	}

	/**
	* Inicia a sessão do carrinho
	* @return void
	*/
	public function carInicializa(){
		if(!isset($_SESSION['ecommerce_cart'])){
			$_SESSION['ecommerce_cart'] = '{ "cursos":[] }';
		}
	}
	public function carReset(){
		$_SESSION['ecommerce_cart'] = '{ "cursos":[] }';
		return $_SESSION['ecommerce_cart'];
	}

	public function carFinaliza(){
		unset($_SESSION['ecommerce_cart']);
	}

	/**
	* Retorna a sessão do carrinho
	* @return void
	*/
	public function carGetSession(){
		//self::carInicializa();
		$strCart = $_SESSION['ecommerce_cart'];
		try {
			$jsonCart = json_decode($strCart);
			if(!$jsonCart){
				$strCart = self::carReset();
				$jsonCart = json_decode($strCart);
			}
		} catch (Exception $e) {
			$strCart = self::carReset();
			$jsonCart = json_decode($strCart);
		}
		return $jsonCart;
	}

	/**
	 * Retorna a lista de cursos do carrinho
	 * @return mixed boolean(false) | array - Retorna a lista de registros ou Falso para nenhum item.
	 */
	public function carListItens()
	{
		//$car = json_decode(self::carGetSession());
		$WhereCurso = "";
		if (is_array($this->car->{'cursos'})&&count($this->car->{'cursos'})>0)
		{
			$i = 0;
			foreach ($this->car->{'cursos'} as $key => $curso) {
				$Or = ($i>0) ? " Or " : "" ;
				$WhereCurso .= $Or." (tbCur.curso_idx=".round($curso->{'curso_idx'})." ) " ;
				$i++;
			}
			$WhereCurso = (trim($WhereCurso)!="") ? " And ( ".$WhereCurso." ) " : $WhereCurso ;

			return self::select("SELECT tbCur.curso_idx,tbCur.valor,tbCur.em_oferta,tbCur.em_oferta_valor,tbCur.nome,tbCur.codigo,tbCur.parcelamento_max,tbCur.imagem,tbCur.produtor_idx,tbCur.plataforma_comissao
								FROM ".$this->TB_CURSO." as tbCur
								Where tbCur.status=1 ".$WhereCurso);
		}
		return false;
	}

	public function carGetCurso()
	{
		if (is_array($this->car->{'cursos'})&&count($this->car->{'cursos'})>0)
		{
	        return $this->car->cursos[0]->curso_idx;
		}
		return 0;
	}


	public function carGetTotal()
	{
		//$car = json_decode(self::carGetSession());
		$totalCar=0;
		if (is_array($this->car->{'cursos'})&&count($this->car->{'cursos'})>0)
		{
			$i = 0;
			foreach ($this->car->{'cursos'} as $key => $curso) {
                $totalCar += $curso->{'valor'};
			}

		}
		return $totalCar;
	}

	public function carGetTotalItens()
	{
		$totalInCart=0;
		if(is_array($this->car->{'cursos'})){
	        $totalInCart = count($this->car->{'cursos'});
		}
		return $totalInCart;
	}

	/**
	 * Passa a lista de itens do carrinho para ver se o mesmo ainda está disponível para compra.
	 * @return Void.
	 */
	function carCheckList($carrinho_itens=NULL)
	{
		//$item_out=false;
		//Não implementa
	}

	/**
	 * Insere o curso no carrinho, valida a existencia do curso solicitado e quantidade disponível
	 * @param int $codigo_curso - Código do curso
	 * @param $dados_complementares -> dados complementares para variação do curso, string de ids separados por virgula
	 * @param int $quantidade
	 * @return boolean(true|false) - Indica de foi inserido com sucesso na lista.
	 */
	function carItemInsert($codigo_curso)
	{

		//Sempre que Inclui um curso diferente o carrinho é zerado.
		self::carReset();
 		$this->car = self::carGetSession();

		$valor=0;
		
		//Captura o valor do curso
		$c_cursos = new cCursos();
		$curso = $c_cursos->all(array('curso'=>$codigo_curso));
		if (is_array($curso)&&count($curso)>0) {

			if ((int)$curso[0]['disponivel_venda']==0 || (int)$curso[0]['inscricao_gratis']==1) {
				//Não entra no checkout
				return false;
			}

			$valor = (round($curso[0]['em_oferta'])==1) ? $curso[0]['em_oferta_valor'] : $curso[0]['valor'];

             /*Carrega o desconto geral do sistema*/
             $desconto_ativo = (int)Sis::Config("ECOMMERCE-DESCONTO-ATIVO");
             // /* Usado para fazer os testes online - usuario Neo*/
             // if(isset($_SESSION['ecommerce_usuario'])){
             //     if($_SESSION['ecommerce_usuario']['id']==17){
             //         $desconto_ativo=1;
             //     }
             // }

             $desconto_valor = (float)Sis::Config("ECOMMERCE-DESCONTO-VALOR");
             if( $desconto_ativo==1 && $desconto_valor>0 ){
                 $valor = (float)$curso[0]['valor']-$desconto_valor;
                 $valor = number_format($valor,2,".",",");
             }
		}
		//Incluir o curso na sessão do carrinho
		if ($valor>0) {
			self::carItemAddSession($codigo_curso,$valor);
			return $curso[0];
		}
		return false;
	}

	/**
	 * Deleta o curso da sessão do carrinho
	 * @param int $codigo_curso - Código do curso
	 * @return void
	 */
	public function carItemUpdate($codigo_curso)
	{
		//Não implementa
	}

	/**
	 * Deleta o curso da sessão do carrinho
	 * @param int $codigo_c - Código do curso
	 * @return void
	 */
	public function carItemDelete($codigo_c)
	{
        self::carItemDelSession($codigo_c);
        $c_cursos = new cCursos();
		$curso = $c_cursos->all(array('curso'=>$codigo_curso));
		if (is_array($curso)&&count($curso)>0) {
         	return $curso[0];
		}
		return false;
	}


	/**
	 * Pesquisa o curso no carrinho
	 * @param int $codigo_c - Código do curso
	 * @return int - Posição do item na lista, caso nao exista retorna -1.
	 */
	public function carItemExisteSession($codigo_c)
	{

		foreach($this->car->{'cursos'} as $key => $valor)
			if(round($valor->{'curso_idx'}) == round($codigo_c))
				return $valor;
		return -1;
	}

	/**
	 * Deleta o curso da sessão do carrinho
	 * @param int $codigo_c - Código do curso
	 * @return void
	 */
	private function carItemDelSession($codigo_c)
	{
		$cursos = $this->car->{'cursos'};
		$tmpArray = array();
		foreach($cursos as $key => $cursoItem)
		{
		  if(!( round($cursoItem->{'curso_idx'}) == round($codigo_c)) )
		  {
		      $tmpArray[] = $cursoItem;
		  }
		}
      	$this->car->{'cursos'} = $tmpArray;
      	self::save();
	}

	/**
	 * Edita o curso na sessão do carrinho
	 * @param int $codigo_c - Código do curso
	 * @return void
	 */
	private function carItemEditSession($codigo_curso)
	{
		//Não implementa
	}

	/**
	 * Inclui o curso da sessão do carrinho
	 * @param int $codigo_c - Código do curso
	 * @param int $codigo_v - Código da variação do curso
	 * @return void
	 */
	private function carItemAddSession($codigo_c,$valor)
	{
		if(!is_object(self::carItemExisteSession($codigo_c))){
			$curso = json_decode('{
					"curso_idx":"'.$codigo_c.'",
					"valor":"'.$valor.'"
				}');
			array_push( $this->car->{'cursos'}, $curso );
		}
		self::save();
	}

	private function save(){
		$_SESSION['ecommerce_cart'] = json_encode($this->car);
	}
}