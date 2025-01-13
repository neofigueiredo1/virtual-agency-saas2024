--
-- Estrutura da tabela `[ADMPREFIX]_conteudo_menu`
--

CREATE TABLE IF NOT EXISTS `[ADMPREFIX]_conteudo_menu` (
  `menu_idx` int(10) NOT NULL auto_increment,
  `status` int(10) default NULL,
  `nome` varchar(50) default NULL,
  `descricao` longtext,
  `data_cadastro` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`menu_idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `[ADMPREFIX]_conteudo_menu_pagina`
--

CREATE TABLE IF NOT EXISTS `[ADMPREFIX]_conteudo_menu_paginas` (
  `menu_pagina_idx` int(10) unsigned NOT NULL auto_increment,
  `menu_idx` int(10) unsigned default NULL,
  `pagina_idx` int(10) unsigned default NULL,
  `nome` varchar(500) default NULL,
  `ranking` int(10) unsigned default NULL,
  PRIMARY KEY  (`menu_pagina_idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `[ADMPREFIX]_conteudo_pagina`
--

CREATE TABLE IF NOT EXISTS `[ADMPREFIX]_conteudo_pagina` (
  `pagina_idx` int(10) NOT NULL auto_increment,
  `titulo` varchar(128) default NULL,
  `titulo_seo` varchar(250) default NULL,
  `palavra_chave` longtext,
  `descricao` longtext,
  `conteudo` longtext,
  `status` tinyint(3) unsigned default NULL,
  `indice` int(10) default NULL,
  `menu` int(10) default NULL,
  `pagina_mae` int(10) default NULL,
  `link_externo` varchar(128) default NULL,
  `alvo_link` varchar(128) default NULL,
  `url_rewrite` longtext,
  `url_pagina` longtext,
  `extra` longtext,
  `data_cadastro` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`pagina_idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------