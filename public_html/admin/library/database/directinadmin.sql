-- phpMyAdmin SQL Dump
-- version 4.0.9
-- http://www.phpmyadmin.net
--
-- Máquina: localhost
-- Data de Criação: 06-Jan-2014 às 10:13
-- Versão do servidor: 5.6.14-log
-- versão do PHP: 5.3.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de Dados: `[DATABASE_NAME]`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `[ADMPREFIX]_config`
--

CREATE TABLE IF NOT EXISTS `[ADMPREFIX]_config` (
  `config_idx` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) DEFAULT NULL,
  `nome` longtext,
  `valor` longtext,
  `descricao` longtext,
  `nivel` int(11) DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`config_idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Incluindo os dados da tabela `[ADMPREFIX]_config`
--

INSERT INTO `[ADMPREFIX]_config` (`config_idx`, `status`, `nome`, `valor`, `descricao`, `nivel`, `data_cadastro`) VALUES
(1, 1, 'SIS_NOME', 'DirectIn', NULL, 0, '2014-02-17 20:05:33'),
(2, 1, 'SIS_VERSAO', '1.0', NULL, 0, '2014-02-17 20:05:33'),
(3, 1, 'SIS_COLOR', '#5c87b2', 'Cor tema do sistema.', 0, '2014-02-17 20:06:19'),
(4, 1, 'CLI_COTA', '1024', 'Cota de armazenamento do cliente no servidor de hospedagem.', 0, '2014-02-17 20:05:34');

-- --------------------------------------------------------

--
-- Estrutura da tabela `[ADMPREFIX]_log`
--

CREATE TABLE IF NOT EXISTS `[ADMPREFIX]_log` (
  `log_idx` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_idx` int(11) DEFAULT NULL,
  `modulo_codigo` int(11) DEFAULT NULL,
  `modulo_area` varchar(255) DEFAULT NULL,
  `registro_codigo` int(11) DEFAULT NULL,
  `registro_nome` varchar(255) DEFAULT NULL,
  `acao` varchar(255) DEFAULT NULL,
  `descricao` longtext,
  `ip_usuario` varchar(100) DEFAULT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_idx`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `[ADMPREFIX]_login`
--

CREATE TABLE IF NOT EXISTS `[ADMPREFIX]_login` (
  `usuario_idx` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) DEFAULT NULL,
  `nivel` int(11) DEFAULT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `login` varchar(255) DEFAULT NULL,
  `senha` varchar(500) DEFAULT NULL,
  `set_validade` int(11) DEFAULT NULL,
  `validade` datetime DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`usuario_idx`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Extraindo dados da tabela `[ADMPREFIX]_login`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `[ADMPREFIX]_login_permissao`
--

CREATE TABLE IF NOT EXISTS `[ADMPREFIX]_login_permissao` (
  `login_permissao_idx` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_idx` int(10) unsigned DEFAULT NULL,
  `modulo_codigo` int(10) unsigned DEFAULT NULL,
  `permissao_codigo` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`login_permissao_idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `[ADMPREFIX]_modulo`
--

CREATE TABLE IF NOT EXISTS `[ADMPREFIX]_modulo` (
  `modulo_idx` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `codigo` int(10) unsigned DEFAULT NULL,
  `ranking` int(10) unsigned DEFAULT NULL,
  `nome` varchar(455) DEFAULT NULL,
  `versao` varchar(45) DEFAULT NULL,
  `descricao` varchar(455) DEFAULT NULL,
  `dados` longtext,
  `pasta` varchar(455) DEFAULT NULL,
  PRIMARY KEY (`modulo_idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `[ADMPREFIX]_modulo_permissao`
--

CREATE TABLE IF NOT EXISTS `[ADMPREFIX]_modulo_permissao` (
  `modulo_permissao_idx` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `modulo_codigo` int(10) unsigned DEFAULT NULL,
  `permissao_codigo` int(10) unsigned DEFAULT NULL,
  `nome` varchar(450) DEFAULT NULL,
  `descricao` longtext,
  PRIMARY KEY (`modulo_permissao_idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `[ADMPREFIX]_urlrewrite_rule`
--

CREATE TABLE IF NOT EXISTS `[ADMPREFIX]_urlrewrite_rule` (
  `rule_idx` int(10) NOT NULL AUTO_INCREMENT,
  `status` int(10) DEFAULT NULL,
  `perfil` int(10) DEFAULT NULL COMMENT 'definir se a regra foi criada automaticamente ou através do sistema',
  `codigo_modulo` int(10) DEFAULT NULL,
  `nome` longtext,
  `changefreq` varchar(50) DEFAULT NULL,
  `priority` varchar(20) DEFAULT NULL,
  `lastmod` varchar(20) DEFAULT NULL,
  `combinar` longtext,
  `acao` longtext,
  `descricao` longtext,
  PRIMARY KEY (`rule_idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
