--
-- Estrutura da tabela `[ADMPREFIX]_banner`
--

CREATE TABLE IF NOT EXISTS `[ADMPREFIX]_banner` (
  `banner_idx` int(10) NOT NULL auto_increment,
  `ranking` int(10) default NULL,
  `status` int(10) default NULL,
  `tipo_idx` int(10) default NULL,
  `monitor_impressao` int(10) default NULL,
  `monitor_clique` int(10) default NULL,
  `alinhamento` int(11) default NULL,
  `arquivo` longtext,
  `formato` int(10) default NULL,
  `pagina` longtext,
  `lugar` longtext,
  `nome` longtext,
  `descricao` longtext,
  `url` longtext,
  `alvo` longtext,
  `horario` int(10) default NULL,
  `horario_ini` int(10) default NULL,
  `horario_fim` int(10) default NULL,
  `indica_data` int(10) default NULL,
  `data_publicacao` datetime default NULL,
  `data_expiracao` datetime default NULL,
  `video_url` longtext default NULL,
  `subtipo_banner` int(10) default NULL,
  `data_cadastro` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`banner_idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `[ADMPREFIX]_banner_data`
--

CREATE TABLE IF NOT EXISTS `[ADMPREFIX]_banner_data` (
  `data_idx` int(10) NOT NULL auto_increment,
  `banner_idx` int(10) default NULL,
  `tipo` int(10) default NULL,
  `http_referer` longtext default NULL,
  `remote_addr` longtext default NULL,
  `data_cadastro` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`data_idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `[ADMPREFIX]_banner_tipo`
--

CREATE TABLE IF NOT EXISTS `[ADMPREFIX]_banner_tipo` (
  `tipo_idx` int(10) NOT NULL auto_increment,
  `nome` longtext default NULL,
  `largura` int(10) default NULL,
  `altura` int(10) default NULL,
  `animacao` longtext default NULL,
  `animacao_tempo` int(10) default NULL,
  `animacao_velocidade` int(10) default NULL,
  `perfil` int(11) default NULL,
  `subtipo_list_banner` longtext default NULL,
  `descricao_secao` varchar(255) default NULL,
  `data_cadastro` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`tipo_idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
