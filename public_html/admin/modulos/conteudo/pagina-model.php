<?php
/**
 * Classe de gerenciamento de dados da página de páginas no módulo de conteudo
 *
 * @package default
 * @author
 **/
class conteudo_pagina_model extends HandleSql {

    /**
     * Nome da tabela de páginas
     *
     * @var constant string
     **/

    public $TB_PAGINA;
    public $TB_IMAGEM;

    function __construct(){
        parent::__construct();
        $this->TB_PAGINA = self::getPrefix() . "_conteudo_pagina";
        $this->TB_IMAGEM = self::getPrefix() . "_conteudo_imagem";
    }

    /**
     *
     *
     * @return bool
     * @author
     **/
    public function listPageSameNameM($titulo="", $url="", $idPage=0){
        $SqlWhere = "";
        if($idPage!=0){
            $SqlWhere = " And pagina_idx<>".$idPage;
        }
        $res = parent::select("SELECT * FROM ".$this->TB_PAGINA." WHERE (titulo Like '". trim($titulo) ."' OR url_rewrite Like '" . trim($url) . "') ".$SqlWhere." ");
        if(count($res) >= 1){
            return $res;
        } else {
            return false;
        }
    }

    /**
     * Função para listar a ultima página inserida
     *
     * @return bool
     * @author
     **/
    public function listLastM($titulo){
        $res = parent::select("SELECT * FROM ".$this->TB_PAGINA." WHERE titulo Like '".trim($titulo)."' Order By pagina_idx DESC ");
        if(count($res) >= 1){
            return $res;
        } else {
            return false;
        }
    }

    /**
     * Seleciona as páginas filhas
     *
     * @return void
     * @author
     **/
    function getDaughtersM($mother){
        $res = parent::select("SELECT pagina_idx FROM " . $this->TB_PAGINA . " WHERE pagina_mae=" . $mother . "");

        if(count($res) >= 1){
            return $res;
        }else{
            return false;
        }
    }


    /**
     * Lista as imagens da página passada por parametro
     *
     * @return void
     * @author
     **/
    function getImagesFromPageM($pagId){
        $res = parent::select("SELECT * FROM " . $this->TB_IMAGEM . " WHERE pagina_idx=" . $pagId . "");

        if(count($res) >= 1){
            return $res;
        }else{
            return false;
        }
    }


    /**
     * Lista página mãe
     *
     * @return void
     * @author
     **/
    function getMotherM($pagIdx){
        $res = parent::select("Select titulo From " . $this->TB_IMAGEM . " Where pagina_idx=" . $pagIdx . "");
        if(count($res) >= 1){
            return $res;
        }else{
            return false;
        }
    }
}
