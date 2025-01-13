<?php
class connException extends Exception { }
class Connect{

	// Conexão
	private static $DB_HOST;
	private static $DB_NOME;
	private static $DB_USER;
	private static $DB_PASS;
	private static $DB_PREFIX;

	private static $instance = NULL;

	function __construct(){
	}

	public static function loadVarsFromEnv()
	{
		global $env;
		self::$DB_HOST = $env->get('DB_HOST');
		self::$DB_NOME = $env->get('DB_NOME');
		self::$DB_USER = $env->get('DB_USER');
		self::$DB_PASS = $env->get('DB_PASS');
		self::$DB_PREFIX = $env->get('DB_PREFIX');
	}

	public static function getHost()
	{
		return self::$DB_HOST;
	}
	public static function getNome()
	{
		return self::$DB_NOME;
	}
	public static function getUser()
	{
		return self::$DB_USER;
	}
	public static function getPass()
	{
		return self::$DB_PASS;
	}

	/**
	 * Método que retorna o prefixo das tabelas.
	 */
	public static function getPrefix(){
		return self::$DB_PREFIX;
	}

	/**
	 * Método que verifica se as constantes foram geradas ou não.
	 * @return Boolean - Caso as constantes tenham sido geradas na instalação, retorna TRUE;
	 */
	public static function checkConstants(){
		return (is_null(self::$DB_HOST) || is_null(self::$DB_NOME) || is_null(self::$DB_USER) || is_null(self::$DB_PASS) || is_null(self::$DB_PREFIX) ) ? false : true;
	}

	/**
	 * Método de conexão ao banco.
	 * Utiliza a extensão PDO, que serve para utilizar vários modelos de banco de dados.
	 */
	public static function getInstance()
	{
		try {
			if(self::checkConstants()){
				if (self::$instance===NULL){
					try{
					   self::$instance = @new PDO("mysql:host=".self::$DB_HOST.";dbname=".self::$DB_NOME.";charset=utf8", self::$DB_USER, self::$DB_PASS);
						return self::$instance;
					} catch(PDOException $e) {
					   die("N&atilde;o foi poss&iacute;vel conectar &agrave; fonte de dados. <br/> Detalhes: ". $e->getMessage());
					}
				}
				return self::$instance;
			}else{
				throw new connException('Contantes de acesso ao banco de dados não definidas!');
			};
		}catch (Exception $e) {
			throw $e;
		}
	}

	public static function validadeConnection($DB_HOST,$DB_NOME,$DB_USER,$DB_PASS){
		try{
		   self::$instance = @new PDO("mysql:host=".$DB_HOST.";dbname=".$DB_NOME, $DB_USER, $DB_PASS);
			return self::$instance;
		} catch(PDOException $e) {
			throw $e->getMessage();
		}
	}

} // End class Connect
