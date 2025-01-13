-- MySQL dump 10.13  Distrib 8.0.18, for macos10.14 (x86_64)
--
-- Host: localhost    Database: virtual_agency_pl2025
-- ------------------------------------------------------
-- Server version	8.0.28

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `vapl_banner`
--

DROP TABLE IF EXISTS `vapl_banner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vapl_banner` (
  `banner_idx` int NOT NULL AUTO_INCREMENT,
  `ranking` int DEFAULT NULL,
  `status` int DEFAULT NULL,
  `tipo_idx` int DEFAULT NULL,
  `monitor_impressao` int DEFAULT NULL,
  `monitor_clique` int DEFAULT NULL,
  `alinhamento` int DEFAULT NULL,
  `arquivo` longtext,
  `formato` int DEFAULT NULL,
  `pagina` longtext,
  `lugar` longtext,
  `nome` longtext,
  `descricao` longtext,
  `url` longtext,
  `alvo` longtext,
  `horario` int DEFAULT NULL,
  `horario_ini` int DEFAULT NULL,
  `horario_fim` int DEFAULT NULL,
  `indica_data` int DEFAULT NULL,
  `data_publicacao` datetime DEFAULT NULL,
  `data_expiracao` datetime DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL,
  `video_url` longtext,
  `subtipo_banner` int DEFAULT NULL,
  PRIMARY KEY (`banner_idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vapl_banner`
--

LOCK TABLES `vapl_banner` WRITE;
/*!40000 ALTER TABLE `vapl_banner` DISABLE KEYS */;
/*!40000 ALTER TABLE `vapl_banner` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vapl_banner_data`
--

DROP TABLE IF EXISTS `vapl_banner_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vapl_banner_data` (
  `data_idx` int NOT NULL AUTO_INCREMENT,
  `banner_idx` int DEFAULT NULL,
  `tipo` int DEFAULT NULL,
  `http_referer` longtext,
  `remote_addr` longtext,
  `data_cadastro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`data_idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vapl_banner_data`
--

LOCK TABLES `vapl_banner_data` WRITE;
/*!40000 ALTER TABLE `vapl_banner_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `vapl_banner_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vapl_banner_tipo`
--

DROP TABLE IF EXISTS `vapl_banner_tipo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vapl_banner_tipo` (
  `tipo_idx` int NOT NULL AUTO_INCREMENT,
  `nome` longtext,
  `largura` int DEFAULT NULL,
  `altura` int DEFAULT NULL,
  `animacao` longtext,
  `animacao_tempo` int DEFAULT NULL,
  `animacao_velocidade` int DEFAULT NULL,
  `perfil` int DEFAULT NULL,
  `subtipo_list_banner` longtext,
  `descricao_secao` varchar(255) DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tipo_idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vapl_banner_tipo`
--

LOCK TABLES `vapl_banner_tipo` WRITE;
/*!40000 ALTER TABLE `vapl_banner_tipo` DISABLE KEYS */;
/*!40000 ALTER TABLE `vapl_banner_tipo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vapl_cadastro`
--

DROP TABLE IF EXISTS `vapl_cadastro`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vapl_cadastro` (
  `cadastro_idx` int NOT NULL AUTO_INCREMENT,
  `status` int DEFAULT NULL,
  `nome_completo` varchar(550) DEFAULT NULL COMMENT 'Nome Completo',
  `nome_informal` varchar(50) DEFAULT NULL COMMENT 'Nome informal',
  `genero` int DEFAULT NULL COMMENT 'Gênero',
  `data_nasc` varchar(20) DEFAULT NULL COMMENT 'Data de nascimento',
  `email` varchar(550) DEFAULT NULL COMMENT 'E-mail',
  `senha` varchar(550) DEFAULT NULL,
  `telefone_resid` varchar(50) DEFAULT NULL COMMENT 'Telefone residencial',
  `telefone_comer` varchar(50) DEFAULT NULL COMMENT 'Telefone comercial',
  `celular` varchar(50) DEFAULT NULL COMMENT 'Celular',
  `endereco` varchar(50) DEFAULT NULL COMMENT 'Endereço',
  `numero` varchar(50) DEFAULT NULL COMMENT 'Número',
  `complemento` varchar(50) DEFAULT NULL COMMENT 'Complemento',
  `bairro` varchar(50) DEFAULT NULL COMMENT 'Bairro',
  `cep` varchar(50) DEFAULT NULL COMMENT 'CEP',
  `cidade` varchar(50) DEFAULT NULL COMMENT 'Cidade',
  `estado` varchar(50) DEFAULT NULL COMMENT 'Estado',
  `pais` varchar(50) DEFAULT NULL COMMENT 'País',
  `imagem` longtext,
  `cpf` varchar(50) DEFAULT NULL COMMENT 'CPF',
  `receber_boletim` int DEFAULT NULL COMMENT 'Receber boletim',
  `data_cadastro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cadastro_idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vapl_cadastro`
--

LOCK TABLES `vapl_cadastro` WRITE;
/*!40000 ALTER TABLE `vapl_cadastro` DISABLE KEYS */;
/*!40000 ALTER TABLE `vapl_cadastro` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vapl_cadastro_interesse`
--

DROP TABLE IF EXISTS `vapl_cadastro_interesse`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vapl_cadastro_interesse` (
  `interesse_idx` int NOT NULL AUTO_INCREMENT,
  `status` smallint DEFAULT NULL,
  `nome` varchar(40) DEFAULT NULL,
  `descricao` longtext,
  `data_cadastro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ranking` int NOT NULL,
  PRIMARY KEY (`interesse_idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vapl_cadastro_interesse`
--

LOCK TABLES `vapl_cadastro_interesse` WRITE;
/*!40000 ALTER TABLE `vapl_cadastro_interesse` DISABLE KEYS */;
/*!40000 ALTER TABLE `vapl_cadastro_interesse` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vapl_cadastro_seleciona`
--

DROP TABLE IF EXISTS `vapl_cadastro_seleciona`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vapl_cadastro_seleciona` (
  `cadastro_interesse_idx` int NOT NULL AUTO_INCREMENT,
  `interesse_idx` int NOT NULL,
  `cadastro_idx` int NOT NULL,
  PRIMARY KEY (`cadastro_interesse_idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vapl_cadastro_seleciona`
--

LOCK TABLES `vapl_cadastro_seleciona` WRITE;
/*!40000 ALTER TABLE `vapl_cadastro_seleciona` DISABLE KEYS */;
/*!40000 ALTER TABLE `vapl_cadastro_seleciona` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vapl_config`
--

DROP TABLE IF EXISTS `vapl_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vapl_config` (
  `config_idx` int NOT NULL AUTO_INCREMENT,
  `status` int DEFAULT NULL,
  `nome` longtext,
  `valor` longtext,
  `descricao` longtext,
  `nivel` int DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`config_idx`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vapl_config`
--

LOCK TABLES `vapl_config` WRITE;
/*!40000 ALTER TABLE `vapl_config` DISABLE KEYS */;
INSERT INTO `vapl_config` VALUES (1,1,'SIS_NOME','DirectIn',NULL,0,'2014-02-17 20:05:33'),(2,1,'SIS_VERSAO','1.0',NULL,0,'2014-02-17 20:05:33'),(3,1,'SIS_COLOR','#5c87b2','Cor tema do sistema.',0,'2014-02-17 20:06:19'),(4,1,'CLI_COTA','1024','Cota de armazenamento do cliente no servidor de hospedagem.',0,'2014-02-17 20:05:34'),(5,1,'SIS_NOME','DirectIn','Nome do Sistema',0,'2025-01-13 19:43:45'),(6,1,'SIS_VERSAO','1.0','Versão do sistema',0,'2025-01-13 19:43:45');
/*!40000 ALTER TABLE `vapl_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vapl_conteudo_menu`
--

DROP TABLE IF EXISTS `vapl_conteudo_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vapl_conteudo_menu` (
  `menu_idx` int NOT NULL AUTO_INCREMENT,
  `status` int DEFAULT NULL,
  `nome` varchar(50) DEFAULT NULL,
  `descricao` longtext,
  `data_cadastro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`menu_idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vapl_conteudo_menu`
--

LOCK TABLES `vapl_conteudo_menu` WRITE;
/*!40000 ALTER TABLE `vapl_conteudo_menu` DISABLE KEYS */;
/*!40000 ALTER TABLE `vapl_conteudo_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vapl_conteudo_menu_paginas`
--

DROP TABLE IF EXISTS `vapl_conteudo_menu_paginas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vapl_conteudo_menu_paginas` (
  `menu_pagina_idx` int unsigned NOT NULL AUTO_INCREMENT,
  `menu_idx` int unsigned DEFAULT NULL,
  `pagina_idx` int unsigned DEFAULT NULL,
  `nome` varchar(500) DEFAULT NULL,
  `ranking` int unsigned DEFAULT NULL,
  PRIMARY KEY (`menu_pagina_idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vapl_conteudo_menu_paginas`
--

LOCK TABLES `vapl_conteudo_menu_paginas` WRITE;
/*!40000 ALTER TABLE `vapl_conteudo_menu_paginas` DISABLE KEYS */;
/*!40000 ALTER TABLE `vapl_conteudo_menu_paginas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vapl_conteudo_pagina`
--

DROP TABLE IF EXISTS `vapl_conteudo_pagina`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vapl_conteudo_pagina` (
  `pagina_idx` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(128) DEFAULT NULL,
  `titulo_seo` varchar(250) DEFAULT NULL,
  `palavra_chave` longtext,
  `descricao` longtext,
  `conteudo` longtext,
  `status` tinyint unsigned DEFAULT NULL,
  `indice` int DEFAULT NULL,
  `menu` int DEFAULT NULL,
  `pagina_mae` int DEFAULT NULL,
  `link_externo` varchar(128) DEFAULT NULL,
  `alvo_link` varchar(128) DEFAULT NULL,
  `url_rewrite` longtext,
  `url_pagina` longtext,
  `extra` longtext,
  `data_cadastro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pagina_idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vapl_conteudo_pagina`
--

LOCK TABLES `vapl_conteudo_pagina` WRITE;
/*!40000 ALTER TABLE `vapl_conteudo_pagina` DISABLE KEYS */;
/*!40000 ALTER TABLE `vapl_conteudo_pagina` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vapl_log`
--

DROP TABLE IF EXISTS `vapl_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vapl_log` (
  `log_idx` int NOT NULL AUTO_INCREMENT,
  `usuario_idx` int DEFAULT NULL,
  `modulo_codigo` int DEFAULT NULL,
  `modulo_area` varchar(255) DEFAULT NULL,
  `registro_codigo` int DEFAULT NULL,
  `registro_nome` varchar(255) DEFAULT NULL,
  `acao` varchar(255) DEFAULT NULL,
  `descricao` longtext,
  `ip_usuario` varchar(100) DEFAULT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_idx`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vapl_log`
--

LOCK TABLES `vapl_log` WRITE;
/*!40000 ALTER TABLE `vapl_log` DISABLE KEYS */;
INSERT INTO `vapl_log` VALUES (23,2,0,'Login',2,'Admin','efetuado','Sucesso','::1','2025-01-13 19:45:04'),(24,2,0,'Módulo',0,'Conteúdo','INSERT','','::1','2025-01-13 19:55:40'),(25,2,0,'Módulo',0,'Cadastro','INSERT','','::1','2025-01-13 19:55:43'),(26,2,0,'Módulo',0,'Banner','INSERT','','::1','2025-01-13 20:34:22');
/*!40000 ALTER TABLE `vapl_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vapl_login`
--

DROP TABLE IF EXISTS `vapl_login`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vapl_login` (
  `usuario_idx` int NOT NULL AUTO_INCREMENT,
  `status` int DEFAULT NULL,
  `nivel` int DEFAULT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `login` varchar(255) DEFAULT NULL,
  `senha` varchar(500) DEFAULT NULL,
  `set_validade` int DEFAULT NULL,
  `validade` datetime DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`usuario_idx`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vapl_login`
--

LOCK TABLES `vapl_login` WRITE;
/*!40000 ALTER TABLE `vapl_login` DISABLE KEYS */;
INSERT INTO `vapl_login` VALUES (2,1,1,'Admin','dev-team@div.tec.br','divstack','88ec364bb524d9c4c78eba81ff98bf63',0,'2025-01-13 16:44:34','2025-01-13 19:43:44');
/*!40000 ALTER TABLE `vapl_login` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vapl_login_permissao`
--

DROP TABLE IF EXISTS `vapl_login_permissao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vapl_login_permissao` (
  `login_permissao_idx` int unsigned NOT NULL AUTO_INCREMENT,
  `usuario_idx` int unsigned DEFAULT NULL,
  `modulo_codigo` int unsigned DEFAULT NULL,
  `permissao_codigo` int unsigned DEFAULT NULL,
  PRIMARY KEY (`login_permissao_idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vapl_login_permissao`
--

LOCK TABLES `vapl_login_permissao` WRITE;
/*!40000 ALTER TABLE `vapl_login_permissao` DISABLE KEYS */;
/*!40000 ALTER TABLE `vapl_login_permissao` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vapl_modulo`
--

DROP TABLE IF EXISTS `vapl_modulo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vapl_modulo` (
  `modulo_idx` int unsigned NOT NULL AUTO_INCREMENT,
  `codigo` int unsigned DEFAULT NULL,
  `ranking` int unsigned DEFAULT NULL,
  `nome` varchar(455) DEFAULT NULL,
  `versao` varchar(45) DEFAULT NULL,
  `descricao` varchar(455) DEFAULT NULL,
  `dados` longtext,
  `pasta` varchar(455) DEFAULT NULL,
  PRIMARY KEY (`modulo_idx`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vapl_modulo`
--

LOCK TABLES `vapl_modulo` WRITE;
/*!40000 ALTER TABLE `vapl_modulo` DISABLE KEYS */;
INSERT INTO `vapl_modulo` VALUES (1,10001,NULL,'Conteúdo','1.0','Faz o gerenciamento de todas as páginas de conteúdo do site.<br> Opçoes para criação de menus personalizados e páginas filho.','{\"codigo\":\"10001\",\"nome\":\"Conteúdo\",\"versao\":\"1.0\",\"pasta\":\"conteudo\",\"icone\":\"fa-file-o\",\"descricao\":\"Faz o gerenciamento de todas as páginas de conteúdo do site.<br> Opçoes para criação de menus personalizados e páginas filho.\",\"menu\":[{\"link\":\"Páginas\",\"url\":\"/admin/?mod=conteudo&pag=pagina\",\"permissao\":\"\"},{\"link\":\"Menus\",\"url\":\"/admin/?mod=conteudo&pag=menu\",\"permissao\":\"4\"}],\"permissao\":[{\"codigo\":\"1\",\"descricao\":\"Acessa o módulo\",\"nome\":\"Acesso\"},{\"codigo\":\"2\",\"descricao\":\"Controle total do módulo\",\"nome\":\"Geral\"},{\"codigo\":\"3\",\"descricao\":\"Insere, Edita e Exclui as páginas do site\",\"nome\":\"Páginas de contéudo\"},{\"codigo\":\"4\",\"descricao\":\"Insere, Edita e Exclui os menus do site\",\"nome\":\"Menus\"},{\"codigo\":\"5\",\"descricao\":\"Gerenciar o filemanager do Editor\",\"nome\":\"CKEditor\"}]}','conteudo'),(2,10003,NULL,'Cadastro','1.0','Faz o cadastro dos usuários do site','{\"codigo\":\"10003\",\"nome\":\"Cadastro\",\"versao\":\"1.0\",\"pasta\":\"cadastro\",\"icone\":\"fa-users\",\"descricao\":\"Faz o cadastro dos usuários do site\",\"menu\":[{\"link\":\"Cadastros\",\"url\":\"/admin/?mod=cadastro&pag=cadastro\",\"permissao\":\"\"},{\"link\":\"Novo Cadastro\",\"url\":\"/admin/?mod=cadastro&pag=cadastro&act=add\",\"permissao\":\"\"},{\"link\":\"Áreas de interesse\",\"url\":\"/admin/?mod=cadastro&pag=area-interesse&act=list\",\"permissao\":\"\"}],\"permissao\":[{\"codigo\":\"1\",\"nome\":\"Acesso ao módulo\",\"descricao\":\"Dá acesso simples de consulta ao dados do módulo\"}]}','cadastro'),(3,10002,90,'Banner','1.0','Faz o gerenciamento de todos os banners do site.','{\"codigo\":\"10002\",\"nome\":\"Banner\",\"versao\":\"1.0\",\"pasta\":\"banner\",\"icone\":\"fa-image\",\"descricao\":\"Faz o gerenciamento de todos os banners do site.\",\"menu\":\"/admin/?mod=banner&pag=banner&act=tipo-list\",\"permissao\":[{\"codigo\":\"1\",\"descricao\":\"Acessa o módulo\",\"nome\":\"Acesso\"},{\"codigo\":\"2\",\"descricao\":\"Controle total do módulo\",\"nome\":\"Geral\"},{\"codigo\":\"3\",\"descricao\":\"Gerenciar banners\",\"nome\":\"Banners\"},{\"codigo\":\"4\",\"descricao\":\"Gerenciar tipos do banner\",\"nome\":\"Tipos de banner\"},{\"codigo\":\"5\",\"descricao\":\"Consulta às estatísticas do banner\",\"nome\":\"Estatísticas\"}]}','banner');
/*!40000 ALTER TABLE `vapl_modulo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vapl_modulo_permissao`
--

DROP TABLE IF EXISTS `vapl_modulo_permissao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vapl_modulo_permissao` (
  `modulo_permissao_idx` int unsigned NOT NULL AUTO_INCREMENT,
  `modulo_codigo` int unsigned DEFAULT NULL,
  `permissao_codigo` int unsigned DEFAULT NULL,
  `nome` varchar(450) DEFAULT NULL,
  `descricao` longtext,
  PRIMARY KEY (`modulo_permissao_idx`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vapl_modulo_permissao`
--

LOCK TABLES `vapl_modulo_permissao` WRITE;
/*!40000 ALTER TABLE `vapl_modulo_permissao` DISABLE KEYS */;
INSERT INTO `vapl_modulo_permissao` VALUES (1,10001,1,'Acesso','Acessa o módulo'),(2,10001,2,'Geral','Controle total do módulo'),(3,10001,3,'Páginas de contéudo','Insere, Edita e Exclui as páginas do site'),(4,10001,4,'Menus','Insere, Edita e Exclui os menus do site'),(5,10001,5,'CKEditor','Gerenciar o filemanager do Editor'),(6,10003,1,'Acesso ao módulo','Dá acesso simples de consulta ao dados do módulo'),(7,10002,1,'Acesso','Acessa o módulo'),(8,10002,2,'Geral','Controle total do módulo'),(9,10002,3,'Banners','Gerenciar banners'),(10,10002,4,'Tipos de banner','Gerenciar tipos do banner'),(11,10002,5,'Estatísticas','Consulta às estatísticas do banner');
/*!40000 ALTER TABLE `vapl_modulo_permissao` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vapl_urlrewrite_rule`
--

DROP TABLE IF EXISTS `vapl_urlrewrite_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vapl_urlrewrite_rule` (
  `rule_idx` int NOT NULL AUTO_INCREMENT,
  `status` int DEFAULT NULL,
  `perfil` int DEFAULT NULL COMMENT 'definir se a regra foi criada automaticamente ou através do sistema',
  `codigo_modulo` int DEFAULT NULL,
  `nome` longtext,
  `changefreq` varchar(50) DEFAULT NULL,
  `priority` varchar(20) DEFAULT NULL,
  `lastmod` varchar(20) DEFAULT NULL,
  `combinar` longtext,
  `acao` longtext,
  `descricao` longtext,
  PRIMARY KEY (`rule_idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vapl_urlrewrite_rule`
--

LOCK TABLES `vapl_urlrewrite_rule` WRITE;
/*!40000 ALTER TABLE `vapl_urlrewrite_rule` DISABLE KEYS */;
/*!40000 ALTER TABLE `vapl_urlrewrite_rule` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-01-13 18:17:51
