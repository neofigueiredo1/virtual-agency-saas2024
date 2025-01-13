<?php
    /**
     * Classe de controle de fluxo da página de pastas no módulo de conteudo
     *
     * @package Conteudo
     **/
    class pagina extends conteudo_pagina_model{

        public $MODULO_CODIGO   = "10001";
        public $MODULO_AREA     = "Página";

        public $pasta_modulo            = "";
        public $pasta_modulo_images     = "";
        public $pasta_modulo_images_p   = "";
        public $pasta_modulo_images_m   = "";
        public $pasta_modulo_images_g   = "";

        function __construct(){
            parent::__construct();
        }

        /**
         * Funcao para retornar a lista de paginas e subpaginas.
         * @return String
         **/
        public function getPageList($idMae,$pLeft,$urlMae)
        {
            global $mod,$pag,$pt_id;

            $rsPagina;
            $returnPages    = "";
            $myPLeft        = $pLeft;
            $SetaIs         = "";

            $rsPagina = parent::select("SELECT * FROM " . $this->TB_PAGINA . " WHERE pagina_mae=" . round($idMae) . " ORDER BY indice ASC ");

            if(isset($rsPagina) && $rsPagina !== false)
            {
                $_ncount = 0;
                foreach ($rsPagina as $_pagina)
                {

                    $sqlCheckFilho  = parent::select("SELECT titulo FROM " . $this->TB_PAGINA . " WHERE pagina_mae=" . round($_pagina["pagina_idx"]) . "  ORDER BY indice ASC ");

                    $IconSon        = "";
                    $status        = "offline";
                    if($_pagina["status"]==1){
                        $status = "online";
                    }

                    $returnPages .= "<li id='list_" . $_pagina["pagina_idx"] . "' class='list_item' > <div class='list_item_div ".$status."' >

                                            <div class='acoes' >
                                                <div class='acao' >
                                                    <a class='a_tooltip' data-placement='top' title='Editar' href='?mod=" . $mod . "&pag=" . $pag . "&act=edit&pg_id=" . $_pagina['pagina_idx'] . "' >
                                                        <i class='fa fa-pencil-square-o'></i>
                                                    </a>
                                                    &nbsp;
                                                    <a class='a_tooltip' data-placement='top' title='Excluir' href='#' onclick='javascript: if (confirm(&quot;Você deseja excluir os dados?&quot;)) { window.location=&quot;?mod=" . $mod . "&pag=" . $pag . "&act=del&pg_id=".$_pagina['pagina_idx']."&quot; } else { return false; };' >
                                                        <i class='fa fa-trash-o'></i>
                                                    </a>
                                                </div>
                                                <div class='acao' >" . Sis::getStatusFormat($_pagina['status']) . "</div>
                                            </div>
                                            <div class='info' ><span class='disclose' ><span></span></span><a href='?mod=" . $mod . "&pag=" . $pag . "&act=edit&pg_id=" . $_pagina["pagina_idx"] . "' >".$_pagina['titulo']."</a></div>

                                        </div>
                                    ";

                    if(count($sqlCheckFilho)>0)
                    {
                        $returnPages .= "<ol>";
                        $returnPages .= self::getPageList($_pagina["pagina_idx"],($myPLeft+36),$urlMae.$_pagina["titulo"]);
                        $returnPages .= "</ol>";
                    }else{
                        $returnPages .= self::getPageList($_pagina["pagina_idx"],($myPLeft+36),$urlMae.$_pagina["titulo"]);
                    }

                    $returnPages .= "</li>";
                    $_ncount++;
                }
            }

            return $returnPages;
        }


        /**
         * Função para inserir uma página. Utiliza o método sqlCRUD para inserir
         *
         * @return
         * @author
         **/
        public function paginaInsert() {
            set_time_limit(999);

            $status        = isset($_POST['status']) && is_numeric($_POST['status']) ? (int)$_POST['status'] : 0 ;
            $indice        = isset($_POST['indice']) && is_numeric($_POST['indice']) ? (int)$_POST['indice'] : 0 ;
            $titulo        = isset($_POST['titulo']) ? Text::clean($_POST['titulo']) : "";
            $titulo_seo    = isset($_POST['titulo_seo']) ? Text::clean($_POST['titulo_seo']) : "";
            $palavra_chave = isset($_POST['palavra_chave']) ? Text::clean($_POST['palavra_chave']) : "";
            $descricao     = isset($_POST['descricao']) ? Text::clean($_POST['descricao']) : "";
            $conteudo      = isset($_POST['conteudo']) ? Text::clean($_POST['conteudo']) : "";

            //Itens avançados
            $pagina_mae    = isset($_POST['pagina_mae']) && is_numeric($_POST['pagina_mae']) ? (int)$_POST['pagina_mae'] : 0 ;
            $link_externo  = isset($_POST['link_externo']) ? Text::clean($_POST['link_externo']) : "";
            $pagina_mae    = isset($_POST['pagina_mae']) && is_numeric($_POST['pagina_mae']) ? (int)$_POST['pagina_mae'] : 0 ;
            $alvo_link     = isset($_POST['alvo_link']) ? Text::clean($_POST['alvo_link']) : "";
            $extra         = isset($_POST['extra']) ? Text::clean($_POST['extra']) : "";
            $url_pagina    = isset($_POST['url_pagina']) ? Text::clean($_POST['url_pagina']) : "";
            $url_pagina    = str_replace("/", "", $url_pagina);

            $pageWithSameName = parent::listPageSameNameM("", $url_pagina);
            if ((isset($pageWithSameName) && $pageWithSameName !== FALSE && $pagina_mae == $pageWithSameName[0]['pagina_mae']) || ($url_pagina == $pageWithSameName[0]['url_rewrite'])) {
                Sis::setAlert('O registro informado j&aacute; existe!', 4);
            }

            $array = array(
                    'status'        => $status,
                    'indice'        => $indice,
                    'titulo'        => $titulo,
                    'titulo_seo'    => $titulo_seo,
                    'palavra_chave' => $palavra_chave,
                    'descricao'     => $descricao,
                    'conteudo'      => $conteudo,
                    'pagina_mae'    => $pagina_mae,
                    'link_externo'  => $link_externo,
                    'pagina_mae'    => $pagina_mae,
                    'alvo_link'     => $alvo_link,
                    'extra'         => $extra,
                    'url_rewrite'   => $url_pagina,
                    'url_pagina'    => 0
            );
            $fildsToSelect      = '';
            $messageLog         = array('modulo_codigo'=>$this->MODULO_CODIGO,'modulo_area'=>$this->MODULO_AREA,'reg_nome'=>$array['titulo']);
            $actionSql          = 'I';
            $page               = 0;
            $recordPage         = 0;

            $dados = parent::sqlCRUD($array, $fildsToSelect, $this->TB_PAGINA, $messageLog, $actionSql, $page, $recordPage);

             if (ob_get_contents()) ob_end_clean();
            if(isset($dados) && $dados !== NULL){
                if ($dados === FALSE){
                    Sis::setAlert('O registro informado j&aacute; existe!', 4);
                }
            } else {
                Sis::setAlert('Ocorreu um erro ao salvar os dados!', 4);
            }

            //Recupera o ultimo registro salvo.
            //$pageIdx = $dados;
            /*$lastPage = parent::listLastM($titulo);
            if(isset($lastPage) && $lastPage !== NULL)
            {
                $pageIdx = $lastPage[0]['pagina_idx'];
            }else{
                Sis::setAlert('Ocorreu um erro ao salvar os dados!', 4);
            }*/

             if (ob_get_contents()) ob_end_clean();
            self::urlRewriteUpdate();
            Sis::setAlert('Dados salvos com sucesso!', 3,"?mod=conteudo&pag=pagina");
        }


        /**
         * Lista todas as páginas
         *
         * @author
         **/
        function paginaList($paginaMae=0, $campos=""){
            $array = array(
                'pagina_mae'    => (int)$paginaMae,
                'orderby'       => 'ORDER BY titulo ASC'
            );
            $fildsToSelect  = $campos;
            $messageLog     = '';
            $actionSql      = 'S';
            $page           = 0;
            $recordPage     = 0;

            $dados = parent::sqlCRUD($array, $fildsToSelect, $this->TB_PAGINA, $messageLog, $actionSql, $page, $recordPage);
            if(isset($dados) && $dados !== NULL){
                return $dados;
            } else  {
                return false;
            }
        }


        /**
         * Pega a ultima página inserida
         *
         * @return void
         * @author
         **/
        function getLastPage(){
            $array = array(
                    'orderby'       => 'ORDER BY indice DESC'
            );
            $fildsToSelect  = 'pagina_idx, indice';
            $messageLog     = '';
            $actionSql      = 'S';
            $page           = 0;
            $recordPage     = 0;

            $dados = parent::sqlCRUD($array, $fildsToSelect, $this->TB_PAGINA, $messageLog, $actionSql, $page, $recordPage);

            if(isset($dados) && $dados !== NULL){
                return $dados;
            } else  {
                return false;
            }
        }

        /**
         * Retorna a página mãe
         * @return String
         **/
        public function getMother($pageId = 0){
            $pageId = isset($pageId) && is_numeric($pageId) ? (int)$pageId : 0;

            $dados = parent::getMotherM($pageId);
            if(isset($dados) && $dados !== NULL && $dados !== false){
                return $dados[0]['pagina_idx'];
            } else  {
                return "Não possui";
            }
        }


        /**
         * Função para deletar as páginas e suas respectivas imagens
         *
         * @return void
         * @author
         **/
        public function paginaDelete(){

            $pageId = isset($_GET['pg_id']) && is_numeric($_GET['pg_id']) ? $_GET['pg_id'] : 0;

            $selImage = parent::getDaughtersM($pageId);
            if(isset($selImage) && $selImage !== false){
                Sis::setAlert('Esta página contém outros registros relacionados à ela.\nPor favor, exclua-os primeiro!', 1);
                die();
            }

            $array = array(
               'pagina_idx' => $pageId
            );
            $dados_s = parent::sqlCRUD($array, 'titulo', $this->TB_PAGINA, '', 'S', 0, 0);
            $nome_pagina = (is_array($dados_s)&&count($dados_s)>0)?$dados_s[0]['titulo']:"";
            $messageLog = array('modulo_codigo'=>$this->MODULO_CODIGO,'modulo_area'=>$this->MODULO_AREA,'reg_codigo'=>$pageId,'reg_nome'=>$nome_pagina);

            $dados = parent::sqlCRUD($array, '', $this->TB_PAGINA, $messageLog, 'D', 0, 0);

             if (ob_get_contents()) ob_end_clean();
            if(isset($dados) && $dados !== NULL){
                Sis::setAlert("Dados removidos com sucesso!", 3, "?mod=" . $_GET['mod'] . "&pag=" . $_GET['pag']);
            } else {
                Sis::setAlert("Ocorreu um erro ao remover dados!", 4);
            }
        }


        /**
         * Lista a página selecionada com o @param pageId
         *
         * @return void
         * @author
         **/
        function paginaListSelected($pageId = 0){
            $pageId = isset($pageId) && is_numeric($pageId) ? (int)$pageId : 0 ;
            $array = array(
               'pagina_idx' => $pageId
            );

            $dados = parent::sqlCRUD($array, '', $this->TB_PAGINA, '', 'S', 0, 0);

            if(isset($dados) && $dados !== NULL){
                return $dados;
            } else  {
                return false;
            }
        }


        /**
         * Função para atualizar a página
         *
         * @return void
         * @author
         **/
        public function paginaUpdate()
    	{
    		set_time_limit(999);

    		$pageId        = isset($_POST['pg_id']) && is_numeric($_POST['pg_id']) ? (int)$_POST['pg_id'] : 0;
            $status        = isset($_POST['status']) && is_numeric($_POST['status']) ? (int)$_POST['status'] : 0 ;
            $indice        = isset($_POST['indice']) && is_numeric($_POST['indice']) ? (int)$_POST['indice'] : 0 ;

            $titulo        = isset($_POST['titulo']) ? Text::clean($_POST['titulo']) : "";
    		$titulo_seo    = isset($_POST['titulo_seo']) ? Text::clean($_POST['titulo_seo']) : "";
            $palavra_chave = isset($_POST['palavra_chave']) ? Text::clean($_POST['palavra_chave']) : "";
            $descricao     = isset($_POST['descricao']) ? Text::clean($_POST['descricao']) : "";
            $conteudo      = isset($_POST['conteudo']) ? Text::clean($_POST['conteudo']) : "";

            //Itens avançados
            $pagina_mae    = isset($_POST['pagina_mae']) && is_numeric($_POST['pagina_mae']) ? (int)$_POST['pagina_mae'] : 0 ;
            $link_externo  = isset($_POST['link_externo']) ? Text::clean($_POST['link_externo']) : "";
            $alvo_link     = isset($_POST['alvo_link']) ? Text::clean($_POST['alvo_link']) : "";
            $extra         = isset($_POST['extra']) ? Text::clean($_POST['extra']) : "";

            $titulo_old     = isset($_POST['titulo_old']) ? Text::clean($_POST['titulo_old']) : "";
            $url_pagina     = isset($_POST['url_pagina']) ? Text::clean($_POST['url_pagina']) : "";
            $url_pagina_old = isset($_POST['url_pagina_old']) ? Text::clean($_POST['url_pagina_old']) : "";
            $status_old     = isset($_POST['status_old']) ? Text::clean($_POST['status_old']) : 0;
            $mae_old     = isset($_POST['mae_old']) ? Text::clean($_POST['mae_old']) : 0;

            $array = array(
                    'pagina_idx'      => $pageId,
                    'status'        =>  $status,
                    'indice'        =>  $indice,
                    'titulo'        =>  $titulo,
                    'titulo_seo'    =>  $titulo_seo,
                    'palavra_chave' =>  $palavra_chave,
                    'descricao'     =>  $descricao,
                    'conteudo'      =>  $conteudo,
                    'pagina_mae'    =>  $pagina_mae,
                    'link_externo'  =>  $link_externo,
                    'alvo_link'     =>  $alvo_link,
                    'extra'         =>  $extra,
                    'url_rewrite'   =>  $url_pagina,
                    'url_pagina'    =>  ''
            );
            $fildsToSelect  = '';
            $messageLog = array('modulo_codigo'=>$this->MODULO_CODIGO,'modulo_area'=>$this->MODULO_AREA,'reg_codigo'=>$pageId,'reg_nome'=>$array['titulo']);
            $actionSql      = 'U';
            $page           = 0;
            $recordPage     = 0;

            $pageWithSameName = parent::listPageSameNameM($titulo, $url_pagina, $pageId);

            if (is_array($pageWithSameName) && count($pageWithSameName)>0){
                Sis::setAlert('A URL amigável já existe, favor indicar informações diferentes!', 1);
            }

            $dados = parent::sqlCRUD($array, $fildsToSelect, $this->TB_PAGINA, $messageLog, $actionSql, $page, $recordPage);

             if (ob_get_contents()) ob_end_clean();
            if(isset($dados) && $dados !== NULL){
                if (!$dados == true){
                    Sis::setAlert('O registro informado j&aacute; existe!', 4);
    			}
    		} else {
                Sis::setAlert('Ocorreu um erro ao salvar os dados!', 4);
    		}

    		//Recupera o ultimo registro salvo.
    		$pageIdx = 0;
    		$lastPage = parent::listLastM($titulo);
    		if(isset($lastPage) && $lastPage !== NULL)
    		{
    			$pageIdx = $lastPage[0]['pagina_idx'];
    		}else{
    			Sis::setAlert('Ocorreu um erro ao salvar os dados!', 4);
    		}

            $imageExists = (isset($_POST['image_exists_idx'])) ? $_POST['image_exists_idx'] : 0;
            //No caso de atualização das imagens existentes
            if ($imageExists != 0) {
                for($x = 0; $x < count($imageExists); $x++)
                {
                    //Insere a imagem na base de dados
                    $descricao = isset($_POST['img_descricao_'.$imageExists[$x]]) ? Text::clean($_POST['img_descricao_'.$imageExists[$x]]) : "";;

                    $array = array(
                            'imagem_idx'    => $imageExists[$x],
                            'descricao'     => $descricao,
                    );

                    $fildsToSelect  = '';
                    $messageLog     = 'CONTEUDO - IMAGENS - EDITAR';
                    $actionSql      = 'U';
                    $page           = 0;
                    $recordPage     = 0;

                    $dados = parent::sqlCRUD($array, $fildsToSelect, $this->TB_IMAGEM, $messageLog, $actionSql, $page, $recordPage);
                }
            }

             if (ob_get_contents()) ob_end_clean();
            if (($titulo != $titulo_old) || ($url_pagina != $url_pagina_old) || ($status != $status_old) || ($pagina_mae != $mae_old)) {
                self::urlRewriteUpdate();
            }
            Sis::setAlert("Dados salvos com sucesso!", 3, "?mod=" . $_GET['mod'] . "&pag=" . $_GET['pag']);
        }

        /**
         * Funcao para atualizar as regras de URL amigável
        **/
        public function urlRewriteUpdate()
        {

            $paginas = array();
            $paginasList = self::paginaList(0, "pagina_idx, titulo, pagina_mae");

            if(is_array($paginasList) && count($paginasList) > 0){
                foreach ($paginasList as $key => $pagina){

                    $paginaFilho = self::paginaList($pagina['pagina_idx'], "pagina_idx, titulo, pagina_mae");

                    if(is_array($paginaFilho) && count($paginaFilho) > 0){

                        foreach ($paginaFilho as $key => $paginaF){
                            $arrayPaginasF = array(
                                "url" => Text::friendlyUrl($pagina['titulo']) . "/" . Text::friendlyUrl($paginaF['titulo']),
                                "nome" => $paginaF['titulo'],
                                "acao" => "?pagina=" . $paginaF['pagina_idx']
                            );
                            array_push($paginas, $arrayPaginasF);
                        }
                    }

                    $arrayPaginas = array(
                        "url" => Text::friendlyUrl($pagina['titulo']),
                        "nome" => $pagina['titulo'],
                        "acao" => "?pagina=" . $pagina['pagina_idx']
                    );
                    array_push($paginas, $arrayPaginas);
                }
            }

            $informacoes = array(
                    "changefreq" => "monthly", "priority" => "1.0", "lastmod" => date("Y-m-d")
            );

            $urlr = new UrlRewriteController($this->MODULO_CODIGO);
            $urlr->setAllUrlRules($paginas, $informacoes);
        }

    }
?>