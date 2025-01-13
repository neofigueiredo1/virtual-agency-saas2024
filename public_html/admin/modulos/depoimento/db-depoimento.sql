-- --------------------------------------------------------

--
-- Estrutura da tabela `[ADMPREFIX]_depoimento_image`
--
CREATE TABLE IF NOT EXISTS `[ADMPREFIX]_depoimento_image` (
  `image_idx` int(10) NOT NULL auto_increment,
  `depoimento_idx` int(10) default NULL,
  `status` int(10) default NULL,
  `ranking` int(10) default NULL,
  `destaque` int(10) default NULL,
  `imagem` varchar(500) default NULL,
  `nome` varchar(500),
  `descricao` longtext,
  `data_cadastro` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`image_idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `[ADMPREFIX]_depoimento`
--
CREATE TABLE IF NOT EXISTS `[ADMPREFIX]_depoimento` (
  `depoimento_idx` int(10) NOT NULL auto_increment,
  `status` int(10) NOT NULL,
  `ranking` int(10) NOT NULL,
  `nome`  varchar(500) default NULL,
  `titulo_pessoa`  varchar(50) default NULL,
  `descricao` longtext,
  `data_cadastro` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`depoimento_idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;