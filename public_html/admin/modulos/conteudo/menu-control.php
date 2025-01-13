<?php
/**
 * Classe de controle da página de menus no módulo de Conteudo
 *
 * @package Conteudo
 **/

class menu extends conteudo_menu_model{

	/**
	 * Nome das tabelas de menus
	 *
	 **/
	public $MODULO_CODIGO = "10001";
	public $MODULO_AREA  = "Menu";

	function __construct(){
		parent::__construct();
	}

   /**
	 * Função para listar todos os menus através do CRUD
	 *
	 * @return array $dados ou bool
	 **/
	function listAll(){
		$array = array('orderby' => 'ORDER BY nome ASC');
      	$dados = parent::sqlCRUD($array, '', $this->TB_MENU, '', 'S', 0, 0);

		if(isset($dados) && $dados !== NULL){
			return $dados;
		} else  {
			return false;
		}
	}


	/**
	 * Função para listar os menus que tiverem o IDX = @param $menu_id através do CRUD
	 *
	 * @return bool
	 **/
	public function listSelected($menu_id){
		$array = array(
		            'menu_idx' => (is_numeric($menu_id) && (int)$menu_id != 0) ? $menu_id : 0
		);
      $dados = parent::sqlCRUD($array, '', $this->TB_MENU, '', 'S', 0, 0);

		if(isset($dados) && $dados !== NULL){
			return $dados;
		} else {
			return false;
		}
	}


	/**
	 * Função para inserir os menus e seus relacionamentos através do CRUD
	 *
	 * @return bool
	 **/
	public function theInsert(){

		/*$array = array('nome' => isset($_POST['nome']) ? Text::clean($_POST['nome']) : "");
		$data = parent::sqlCRUD($array, '', $this->TB_MENU, '', 'S', 0, 0);*/

		$array = array(
         'status' 		=> is_numeric($_POST['status']) && $_POST['status'] != "" ? $_POST['status'] : 0,
         'nome' 		=> isset($_POST['nome']) ? Text::clean($_POST['nome']) : "",
         'descricao' 	=> isset($_POST['descricao']) ? Text::clean($_POST['descricao']) : "",
		);
		$messageLog = array('modulo_codigo'=>$this->MODULO_CODIGO,'modulo_area'=>$this->MODULO_AREA,'reg_nome'=>$array['nome']);
		$dataInsert	= parent::sqlCRUD($array, '', $this->TB_MENU, $messageLog, 'I', 0, 0);

		if (isset($dataInsert) && $dataInsert != FALSE){
			$menuIdx = $dataInsert;
		}else{
			ob_end_clean();
			Sis::setAlert('Ocorreu um erro ao salvar os dados!', 4);
		}

		/*$array_u = array(
         'nome' => trim($array['nome']),
         'orderby' => 'Order by menu_idx DESC'
		);
		$lastMenu = parent::sqlCRUD($array_u, '', $this->TB_MENU, '', 'S', 0, 0);
		if(is_array($lastMenu) && count($lastMenu) > 0)
		{
			$menuIdx = $lastMenu[0]['menu_idx'];
		}else{
			Sis::setAlert('Ocorreu um erro ao salvar os dados!', 4);
		}*/

		$pages = isset($_POST['paginas']) ? Text::clean($_POST['paginas']) : "";
		if($pages != ""){
			$pageArrayList = explode(",",$pages);

			for($i=0; $i < count($pageArrayList); $i++){
				/*$array_p = array(
			               'menu_idx' 		=> round($menuIdx),
			               'pagina_idx' 	=> round($pageArrayList[$i])
				);
				$pageMenuRelationship = parent::sqlCRUD($array_p, '', $this->TB_MENU_PAGINAS, '', 'S', 0, 0);
				if(is_array($pageMenuRelationship) && count($pageMenuRelationship) > 0)
				{
					Sis::setAlert('Ocorreu um erro ao salvar os dados!', 4);
				}else{*/
					$array = array(
			         'menu_idx' 		=> round($menuIdx),
				      'pagina_idx' 	=> round($pageArrayList[$i]),
				      'nome' 	=> (isset($_POST['titulo_'.$pageArrayList[$i]]))?Text::clean($_POST['titulo_'.$pageArrayList[$i]]):""
					);
					$data = parent::sqlCRUD($array, '', $this->TB_MENU_PAGINAS, "", 'I', 0, 0);
					if ($data===FALSE){
						ob_end_clean();
						Sis::setAlert('Ocorreu um erro ao salvar os dados!', 4);
					}

				/*}*/
			}
			//Sis::setAlert('Dados salvos com sucesso!', 3, '?mod=conteudo&pag=menu');
		}
		Sis::setAlert('Dados salvos com sucesso!', 3, '?mod=conteudo&pag=menu');
	}

	/**
	 * Função para atualizar os menus e seus relacionamentos através do CRUD
	 *
	 * @return bool
	 **/
	public function theUpdate(){
		$mn_id     = is_numeric($_POST['mn_id']) && $_POST['mn_id'] != "" ? $_POST['mn_id'] : 0;
		$status    = is_numeric($_POST['status']) && $_POST['status'] != "" ? $_POST['status'] : 0;
		$nome      = isset($_POST['nome']) ? Text::clean($_POST['nome']) : "";
		$descricao = isset($_POST['descricao']) ? Text::clean($_POST['descricao']) : "";

		$array = array(
            'menu_idx' 	=> $mn_id,
            'status' 	=> $status,
	         'nome' 		=> $nome,
	         'descricao' => $descricao,
		);
		$messageLog = array('modulo_codigo'=>$this->MODULO_CODIGO,'modulo_area'=>$this->MODULO_AREA,'reg_codigo'=>$mn_id,'reg_nome'=>$array['nome']);
		$dados = parent::sqlCRUD($array, '', $this->TB_MENU, $messageLog, 'U', 0, 0);

		$menuIdx = $mn_id;

		//Registra os dados relacionados do lugar
		$this->paginas = isset($_POST['paginas']) ? Text::clean($_POST['paginas']) : "";
		$this->oldPage = isset($_POST['paginas_old']) ? Text::clean($_POST['paginas_old']) : "";

		$this->paginas = "!".str_replace(",","!!",$this->paginas)."!";

		$oldPage = $this->oldPage;
		$oldPage = str_replace("!!",",",$oldPage);
		$oldPage = str_replace("!","",$oldPage);
		$oldPageArray = explode(",",$oldPage);

		$paginas = $this->paginas;
		$paginas = str_replace("!!",",",$paginas);
		$paginas = str_replace("!","",$paginas);
		$paginas_arr = explode(",",$paginas);

		for($i=0;$i<count($oldPageArray);$i++){
			//Remove as paginas do menu
			$pos = strrpos($this->paginas,"!".$oldPageArray[$i]."!");
			if($pos===false){ $dados_tipo = parent::delete("DELETE FROM " . $this->TB_MENU_PAGINAS ." WHERE menu_idx=".$menuIdx." And pagina_idx=".round($oldPageArray[$i])." "); }
		}
		for($i=0;$i<count($paginas_arr);$i++){
			//Registra as paginas do menu
			$pos = strrpos($this->oldPage,"!".$paginas_arr[$i]."!");
			$menuTitle = (isset($_POST['titulo_'.$paginas_arr[$i]]))?Text::clean($_POST['titulo_'.$paginas_arr[$i]]):"";
			if($pos===false){
				$array = array(
				   'menu_idx' 	=> $menuIdx,
				   'pagina_idx'=> $paginas_arr[$i],
				   'ranking' 	=> ((count($paginas_arr)*10)-($i*10)),
				   'nome' 	=> $menuTitle
				);
				$dados_tipo = parent::sqlCRUD($array, '', $this->TB_MENU_PAGINAS, '', 'I', 0, 0);
			}else{
				$ranking = ((count($paginas_arr)*10)-($i*10));
				$dados_tipo = parent::update("UPDATE " . $this->TB_MENU_PAGINAS . " Set ranking=". $ranking .", nome='".$menuTitle."' Where menu_idx=".round($menuIdx)." And pagina_idx=".round($paginas_arr[$i])." " );
			}
		}

		ob_end_clean();
		if(isset($dados) && $dados !== NULL){
			if ($dados == true) {
				Sis::setAlert('Dados salvos com sucesso!', 3, '?mod=conteudo&pag=menu');
			}else{
				Sis::setAlert('O nome informado já existe!', 4);
			}
		} else {
			Sis::setAlert('Ocorreu um erro ao cadastrar dados!', 4);
		}
	}

	/**
	 * Função para deletar os menus e seus relacionamentos através do CRUD
	 *
	 * @return bool
	 **/
	public function theDelete(){
		$menuIdx = is_numeric($_GET['menu_id']) && $_GET['menu_id'] != "" ? $_GET['menu_id'] : 0;

		$array = array('menu_idx' => $menuIdx);
		$dados_select = parent::sqlCRUD($array, 'nome', $this->TB_MENU, '', 'S', 0, 0);
		$nome_menu = (is_array($dados_select)&&count($dados_select)>0)?$dados_select[0]['nome']:"";
		$messageLog = array('modulo_codigo'=>$this->MODULO_CODIGO,'modulo_area'=>$this->MODULO_AREA,'reg_codigo'=>$menuIdx,'reg_nome'=>$nome_menu);

      $dados = parent::sqlCRUD($array, '', $this->TB_MENU, $messageLog, 'D', 0, 0);
      $dados = parent::sqlCRUD($array, '', $this->TB_MENU_PAGINAS, '', 'D', 0, 0);

      ob_end_clean();
		if(isset($dados) && $dados !== NULL){
			Sis::setAlert('Dados removidos com sucesso!', 3, '?mod=' . $_GET["mod"] . '&pag=' . $_GET["pag"] . '');
		} else {
			Sis::setAlert('Ocorreu um erro ao remover dados!', 4);
		}
	}

	/**
	 * Função para listar todas as páginas através do CRUD
	 *
	 * @return bool
	 **/
	function pageList(){
		$array = array(
		         'status' => '1',
		         'orderby' => 'ORDER BY indice ASC'
		               );
      $dados = parent::sqlCRUD($array, '', $this->TB_PAGINA, '', 'S', 0, 0);

		if(isset($dados) && $dados !== NULL){
			return $dados;
		} else  {
			return false;
		}
	}

	/**
	 * Função para selecionar as que estão no menu através do MODEL
	 *
	 * @return bool
	 **/
	function getPagesOnMenu($mn_id){
		$mn_id = is_numeric($mn_id) && $mn_id != "" ? $mn_id : 0;
		$dados = parent::getPagesOnMenuM($mn_id);

		if(isset($dados) && $dados !== NULL){
			return $dados;
		} else  {
			return false;
		}
	}


	/**
	 * Função para selecionar as que NÃO estão no menu através do MODEL
	 *
	 * @return bool
	 **/
	function getPagesOutMenu($menuIdx){
		$menuIdx = is_numeric($menuIdx) && $menuIdx != "" ? $menuIdx : 0;
		$dados = parent::getPagesOutMenuM($menuIdx);

		if(isset($dados) && $dados !== NULL){
			return $dados;
		} else  {
			return false;
		}
	}

}