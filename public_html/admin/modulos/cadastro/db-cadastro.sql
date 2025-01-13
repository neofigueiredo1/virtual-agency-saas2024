--
-- Estrutura da tabela `[ADMPREFIX]_cadastro`
--

CREATE TABLE IF NOT EXISTS `[ADMPREFIX]_cadastro` (
  `cadastro_idx` int(10) NOT NULL AUTO_INCREMENT,
  `status` int(10) DEFAULT NULL,
  `nome_completo` varchar(550) DEFAULT NULL COMMENT 'Nome Completo',
  `nome_informal` varchar(50) DEFAULT NULL COMMENT 'Nome informal',
  `genero` int(10) DEFAULT NULL COMMENT 'Gênero',
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
  `receber_boletim` int(10) DEFAULT NULL COMMENT 'Receber boletim',
  `data_cadastro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cadastro_idx`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `[ADMPREFIX]_cadastro_interesse`
--

CREATE TABLE IF NOT EXISTS `[ADMPREFIX]_cadastro_interesse` (
  `interesse_idx` int(10) NOT NULL auto_increment,
  `status` smallint(5) default NULL,
  `nome` varchar(40) default NULL,
  `descricao` longtext,
  `data_cadastro` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `ranking` int(10) NOT NULL,
  PRIMARY KEY(`interesse_idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `[ADMPREFIX]_cadastro_seleciona`
--

CREATE TABLE IF NOT EXISTS `[ADMPREFIX]_cadastro_seleciona` (
  `cadastro_interesse_idx` int(10) NOT NULL auto_increment,
  `interesse_idx` int(10) NOT NULL,
  `cadastro_idx` int(10) NOT NULL,
  PRIMARY KEY(`cadastro_interesse_idx`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
