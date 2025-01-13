<?php
	//ARQUIVO DE CONFIGURAÇÃO DO SITE
	// Inicia a sessão
    if (session_status() == PHP_SESSION_NONE)
        session_start();
    
	ob_start();

	//Exibe todos erros
	error_reporting(E_ALL | E_STRICT);
	ini_set('error_reporting', E_ALL | E_STRICT);

	// Define a timezone
	date_default_timezone_set('America/Fortaleza');

	// Define o charset do sistema
	header('Content-Type: text/html; charset=utf-8');
	header('Access-Control-Allow-Origin: *');
	setlocale(LC_ALL, 'pt_BR.UTF8');
	setlocale(LC_NUMERIC, 'en_US');
	
	set_include_path( get_include_path() . PATH_SEPARATOR . $_SERVER['DOCUMENT_ROOT'] );

	// Padrão de pasta
	define("PASTA_CONTENT","sitecontent");
	define("PASTA_USUARIO","user-files");
	define("PASTA_DIRECTIN","admin");
	define('DS', DIRECTORY_SEPARATOR);
	define('SITE_BASE', str_replace("site".DS."direct-includes","",dirname(__FILE__)) );

	// Inclui o com as principais classes do core de sistema
	require_once "admin/library/bootstrap.php";
	
	//Carrega as variáveis de ambiente do sistema.
	$env = new EnvLoad();
	$env->loadVars();

	/*Verifica o bloqueio no site*/
	$allowedHosts = array('localhost','localhost:8080');
	if (!in_array($_SERVER['HTTP_HOST'], $allowedHosts)) {
		require_once "site-blocked.php";
	}

	// Inclui arquivos de conexão e manipulação de banco de dados
	require_once "admin/library/database/connect.class.php";
	Connect::loadVarsFromEnv();

	require_once "admin/library/database/handle-sql.class.php";

	/*Valida as informacoes do arquivo de conexão*/
	print_r(Connect::checkConstants());
	if(!Connect::checkConstants()){
		ob_clean();
		//die('Nenhuma fonte de dados identificada para o sistema.');
		require_once("admin/library/views/view-install.php");
		exit();
	}

	// Testar conexão com o banco de dados
	try{
		$conexao = Connect::getInstance();
	}catch (Exception $e){
	    ob_end_clean();
	    die("<h1>" . $e->getMessage() . "</h1>");
	}

	
	require_once PASTA_DIRECTIN . "/modulos/modulo/modulo-model.php";
	require_once PASTA_DIRECTIN . "/modulos/modulo/modulo-control.php";

	$pagina         = isset($_GET['pagina'])   ? (int) $_GET['pagina']    : "";
	$q              = isset($_GET['q'])        ? Text::clean($_GET['q']) : "";
	$language       = isset($_GET['language']) ? $_GET['language']        : "";
	$language_field = isset($_GET['language']) ? '_' . $_GET['language']  : "";
	$language_inc   = isset($_GET['language']) ? '-' . $_GET['language']  : "";

	if (preg_match('/\.(?:png|php|jpg|jpeg|gif)$/', $_SERVER["REQUEST_URI"])) {
	    //return false;    // serve the requested resource as-is.
	} else { 
	    $q = $_SERVER["REQUEST_URI"];
	    $q = substr($q, 1, strlen($q));
	}

	$pg = 0; //Usado especifico para paginacao

	if($q!=""){

		//var_dump($q);

		$pagina_url_arrr = explode("?",$q);
		$pagina_url_arr = explode("/",$pagina_url_arrr[0]);
		$pagina_url_fim = end($pagina_url_arr);
		if(is_numeric($pagina_url_fim)){
			$pg = (int)$pagina_url_fim;
			array_pop($pagina_url_arr);
		}else if(trim($pagina_url_fim)==""){
			array_pop($pagina_url_arr);
		}
		$q = implode("/",$pagina_url_arr);
		//$q = str_replace("/","",$pagina_url_arr[0]);
		// var_dump($q);
		// exit();

		if ($q!="") {
			// ob_clean();
			//var_dump($q);
			$mysql = new HandleSql();
			$selPage = $mysql->select("SELECT acao FROM ".$mysql->getPrefix()."_urlrewrite_rule WHERE combinar='" . trim($q) . "'");
			//var_dump($selPage);
			if(count($selPage) > 0){
				$parts = parse_url($selPage[0]['acao']);
				parse_str($parts['query'],$strVars);
				foreach ($strVars as $key => $varData)
					$$key = $varData;
			}else{
				$pagina="404";
			}
		}
	}

	//Pega o URI da página
	$pagina_uri = $_SERVER['REQUEST_URI'];
	$pagina_uri = (explode("?",$pagina_uri))[0];
	$pagina_uri_arr = explode("/",$pagina_uri);
	$pagina_uri_fim = end($pagina_uri_arr);
	if(is_numeric($pagina_uri_fim)){
		$pg = (int)$pagina_uri_fim;
		array_pop($pagina_uri_arr);
	}
	$pagina_uri = implode("/",$pagina_uri_arr);

	// Instanciar modulos
	$modulos = new modulo();
	$lista   = $modulos->listaTodos();
	$modulos_styles  = "";
	$modulos_scripts = "";
	if(isset($lista) && $lista !== false){
		foreach ($lista as $lista_arr) {
			$lista_json = json_decode($lista_arr);
			$codigo     = 0;
			$ckModulo   = NULL;
			if(is_object($lista_json)){
				if($lista_json->{'modulo'}!="admin" && $lista_json->{'modulo'}!="modulo" && $lista_json->{'modulo'}!="config" ) {
					if(count($lista_json->{'data'})>=1) {
						$codigo = $lista_json->{'data'}[0]->{'codigo'};
						$ckModulo = $modulos->checkInstall($codigo);
						if($ckModulo) {
							$moduloNome = $lista_json->{'data'}[0]->{'pasta'};
							require_once "admin/modulos/" . $moduloNome . "/views/" . $moduloNome . "-views.php";
							$classViews = str_replace("-","_",$moduloNome) . "_views";
							$objNome  = "m_" . str_replace("-","_",$moduloNome);
							$$objNome = new $classViews();
							if(file_exists('admin/modulos/' . $moduloNome . '/views/style.css')){
								$modulos_styles .= '<link rel="stylesheet" href="/admin/modulos/' . $moduloNome . '/views/style.css">';
							}
							if(file_exists('admin/modulos/' . $moduloNome . '/views/scripts.js')){
								$modulos_scripts .= '<script type="text/javascript" src="/admin/modulos/' . $moduloNome . '/views/scripts.js"></script>';
							}
						}
					}
				}
			}
		}
	}
	if (!isset($m_conteudo)) {
		die('Certifique-se de que o módulo base de conteúdo está habilitado no CMS');
	}

	//Trata o logout do usuario no sistema
	if (isset($_GET['logout'])){
		$m_cadastro->logout();
	}

	require_once("site/direct-includes/short-functions.php"); 	/*Carrega as classes de views dos módulos*/
	$pagina_data = $m_conteudo->get_page($pagina, $language);
	
	preg_match_all('/\[(.*?)\]/', $pagina_data['conteudo'], $matches);
	if(isset($matches[1][0])) {
		if ( function_exists($matches[1][0]) ) {
			$conteudo = str_replace('[' .$matches[1][0]. ']', $matches[1][0](), $pagina_data['conteudo']);
		}
	}else{
	  $conteudo = $pagina_data['conteudo'];
	}

	for($i = 1; $i < count($matches[1]); $i++) {
	    if(function_exists($matches[1][$i])) {
	        $conteudo= str_replace('[' .$matches[1][$i]. ']', $matches[1][$i](), $conteudo);
	    }
	}



	/*Verifica o bloqueio no site*/
	if (in_array($_SERVER['HTTP_HOST'], array('localhost:8088','produtor.institutoconexo.com.br','hprodutor.institutoconexo.com.br') ) ){
		$m_ecommerce->getView('view-produtor-lp');
	}


	$csrfToken = NoCSRF::generate('csrf_token');

	if($pagina_data['codigo']=="404"){
		$pagina="404";
	}
	require_once "site/direct-includes/seo.php";

?>
