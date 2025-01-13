<?php
	/**
	 * @author Being Serviços Ltda.
	 * @copyright 2014 Being Todos os direitos reservados.
	 * @package DirectIn Cms
	*/

	/**
	 * Inicia a sessão e mostra todos os erros
	 * Em seguida, define a timezone e o Charset do sistema
	 * Após isso, define a opção de configuração include_path
	 */
	session_start();
	ob_start();
	error_reporting(E_ALL | E_STRICT );
	ini_set('error_reporting', E_ALL | E_STRICT );
	setlocale(LC_NUMERIC, 'en_US');

	date_default_timezone_set('America/Fortaleza');
	header('Content-Type: text/html; charset=utf-8');
	set_include_path( get_include_path() . DIRECTORY_SEPARATOR . $_SERVER['DOCUMENT_ROOT'] );

	/**
	 * Constantes definindo o padrão de pastas do sistema.
	 */
	define("BASE_PATH",dirname(dirname(__FILE__)));
	define("PASTA_CONTENT","sitecontent");
	define("PASTA_USUARIO","user-files");
	define("PASTA_DIRECTIN","admin");
	define('DS', DIRECTORY_SEPARATOR);

	// Inclui o com as principais classes do core de sistema
	require_once "library/bootstrap.php";
	
	//Carrega as variáveis de ambiente do sistema.
	$env = new EnvLoad();
	$env->loadVars();

	/**
	 * Verifica se o arquivo de conexão existe, e copia o conteudo do arquivo base [ connect.class.php-production ] para o arquivo de conexão.
	 */
	// if(!file_exists(dirname(__FILE__).DS."library".DS."database".DS."connect.class.php")){
	// 	if(!file_exists(dirname(__FILE__).DS."library".DS."database".DS."connect.class.php-production")){
	// 		die("Arquivo de conexão base não encontrado [ \admin\library\database\connect.class.php-production ] ");
	// 	}
	// 	copy(dirname(__FILE__).DS."library".DS."database".DS."connect.class.php-production", dirname(__FILE__).DS."library".DS."database".DS."connect.class.php");
	// }

	/**
	 * Inclui os arquivos de conexão e manipulação de banco de dados
	 * Em seguinda, Valida as informacoes do arquivo de conexão
	 */
	require_once "library/database/connect.class.php";
	Connect::loadVarsFromEnv();

	if(!Connect::checkConstants()){
		//header("Location: /admin/install.php");
		// require_once('library/views/view-install.php');
		die('Nenhuma fonte de dados identificada para o sistema.');
		exit();
	}
	require_once "library/database/handle-sql.class.php";

	/**
	 * Testa a conexão com o banco de dados,
	 * Inclui os arquivos de funções, classe de e-mail, reescrita de Url, módulos e autenticação
	 */
	try {
	    $conn = Connect::getInstance();
	}catch (Exception $e){
	    die("<h1>" . $e->getMessage() . "</h1>");
	}

	require_once "modulos/modulo/views/modulos.php";
	require_once "library/classes/auth.class.php";

	/**
	 * Verifica se o usuário está autenticado.
	 */
	$checkLogin = Auth::validate();

	/**
	 * Verifica os parametros de sistema que foram passados via GET, e armazenam-os em variáveis, que serão utilizadas nos módulos
	 */
	$mod = isset($_GET['mod']) ? $_GET['mod'] : "";
	$pag = isset($_GET['pag']) ? $_GET['pag'] : "";
	$act = isset($_GET['act']) ? $_GET['act'] : "";
	$mod = ($mod!="") ? $mod : $pag;

	/**
	 * Variável que recebe o nome do arquivo controlador que será chamado
	 * E após isso terá seu nome ajustado da seguinte forma:
	 * "nomeDoModulo.NomeDaPagina" => "nomeDoModulo_NomeDaPagina"
	 */
	$control = ($pag!="" && $pag!=$mod ) ? $pag : $mod;
	$control = str_replace(".","_",$control);
	$control = str_replace("-","_",$control);

	/**
	 * Caso tenha sido passado o parâmtro "logout" via get, é chamada a função de logout do sistema.
	 */
	if ($pag === "logout") Auth::logout();
?>