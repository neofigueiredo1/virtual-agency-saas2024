<?php
/**
 * Classe de gerenciamento de dados de áreas de interesse dos cadastros
 *
 * @package area_interesse
 **/
class area_interesse extends area_interesse_m {

    public $MODULO_CODIGO = "10013";
    public $MODULO_AREA   = "Área de interesse";

    public function __construct() {
        parent::__construct();
    }

    /**
    * Processa a lista completa dos registros de Áreas de interesse.
    * @return array, se a consulta for realizada com sucesso, caso contrário false
    */
    public function listAll(){
        $array = array('orderby' => 'ORDER BY nome ASC');
        $dados = parent::sqlCRUD($array, '', $this->TB_CADASTRO_AREA, '', 'S', 0, 0);
        if(is_array($dados) && count($dados) > 0){
            return $dados;
        }else{
            return false;
        }
    }

    /**
    * Retorna o registro da área de interesse de acordo com o ID.
    * @return array, se a consulta for realizada com sucesso, caso contrário false
    */
    public function listSelected($id=0, $campos=""){
        $id = (int)$id;
        $array = array('interesse_idx' => round($id));
        $dados = parent::sqlCRUD($array, $campos, $this->TB_CADASTRO_AREA, '', 'S', 0, 0);
        if(is_array($dados) && count($dados) > 0)
        {
            return $dados;
        }else{
            return false;
        }
    }

    /**
    * Processa a inclusão da área de interesse.
    */
    public function theInsert(){

        $array = array(
            'status' => isset($_POST['status']) ? Text::clean($_POST['status']) : 0,
            'ranking' => isset($_POST['ranking']) ? Text::clean((int)$_POST['ranking']) : 0,
            'nome' => isset($_POST['nome']) ? Text::clean($_POST['nome']) : "",
            'descricao' => isset($_POST['descricao']) ? Text::clean($_POST['descricao']) : ""
        );

        $messageLog = array('modulo_codigo'=>$this->MODULO_CODIGO,'modulo_area'=>$this->MODULO_AREA,'reg_nome'=>$array['nome']);
        $dados = parent::sqlCRUD($array, '', $this->TB_CADASTRO_AREA, $messageLog, 'I', 0, 0);

        ob_end_clean();
        if(isset($dados) && $dados !== NULL){
            if ($dados == true){
                Sis::setAlert('Dados salvos com sucesso!', 3,"?mod=cadastro&pag=area-interesse");
            }else{
                Sis::setAlert('O registro informado j&aacute; existe!', 4);
            }
        }else {
            Sis::setAlert('Ocorreu um erro ao salvar os dados!', 4);
        }
    }

    /**
    * Processa a atualização da área de interesse.
    */
    public function theUpdate() {

        $array = array(
            'interesse_idx' => isset($_POST['id']) ? Text::clean((int)$_POST['id']): 0,
            'status' => isset($_POST['status']) ? Text::clean($_POST['status']) : 0,
            'ranking' => isset($_POST['ranking']) ? Text::clean((int)$_POST['ranking']) : 0,
            'nome' => isset($_POST['nome']) ? Text::clean($_POST['nome']) : "",
            'descricao' => isset($_POST['descricao']) ? Text::clean($_POST['descricao']) : ""
        );

        $messageLog = array('modulo_codigo'=>$this->MODULO_CODIGO,'modulo_area'=>$this->MODULO_AREA,'reg_nome'=>$array['nome']);
        $dados = parent::sqlCRUD($array, '', $this->TB_CADASTRO_AREA, $messageLog, 'U', 0, 0);

        ob_end_clean();
        if(isset($dados) && $dados !== NULL){
            if ($dados == true){
                Sis::setAlert('Dados salvos com sucesso!', 3,"?mod=cadastro&pag=area-interesse");
            }else{
                Sis::setAlert('O registro informado j&aacute; existe!', 4);
            }
        }else {
            Sis::setAlert('Ocorreu um erro ao salvar os dados!', 4);
        }
    }

    /**
    * Processa a exclusão da área de interesse.
    */
    public function theDelete(){
        
        $id = isset($_GET['id']) ? Text::clean((int)$_GET['id']) : "";
        $array = array(
          'interesse_idx' => $id
        );

        $nomeArea = "";
        $nomeAreaM = self::listSelected($id, 'nome');
        if(is_array($nomeAreaM) && count($nomeAreaM) > 0){
            $nomeArea = $nomeAreaM[0]['nome'];
        }

        $messageLog = array('modulo_codigo'=>$this->MODULO_CODIGO,'modulo_area'=>$this->MODULO_AREA,'reg_nome'=>$nomeArea);
        $dados = parent::sqlCRUD($array, '', $this->TB_CADASTRO_AREA, $messageLog, 'D', 0, 0);
        ob_end_clean();
        if(isset($dados) && $dados !== NULL){
            Sis::setAlert('Dados removidos com sucesso!', 3,"?mod=cadastro&pag=area-interesse");
        } else {
            Sis::setAlert('Ocorreu um erro ao remover os dados!', 4);
        }

    }

    /**
    * Retorna o ultimo registro inserido na base de área de interesse.
    * @return array, se a consulta for realizada com sucesso, caso contrário false
    */
    public function getLastCat(){
        $array = array('orderby' => 'ORDER BY interesse_idx DESC LIMIT 0,1');
        $dados = parent::sqlCRUD($array, '', $this->TB_CADASTRO_AREA, '', 'S', 0, 0);
        if(is_array($dados) && count($dados) > 0){
            return $dados;
        }else{
            return false;
        }
    }

}