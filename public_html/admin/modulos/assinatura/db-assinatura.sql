
--
-- Estrutura da tabela `[ADMPREFIX]_assinatura_pagamento`
--
CREATE TABLE IF NOT EXISTS `[ADMPREFIX]_assinatura_pagamento` (
  `pagamento_idx` int(10) NOT NULL AUTO_INCREMENT,
  `nome` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`pagamento_idx`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Estrutura da tabela `[ADMPREFIX]_assinatura_pedido_pagamento_transacao`
--
CREATE TABLE IF NOT EXISTS `[ADMPREFIX]_assinatura_pagamento_transacao` (
  `transacao_idx` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pedido_idx` int(10) unsigned DEFAULT NULL,
  `pagamento_idx` int(10) unsigned DEFAULT NULL,
  `transacao_codigo` varchar(500) DEFAULT NULL,
  `transacao_source` longtext,
  `transacao_status` int(10) DEFAULT NULL,
  `transacao_boleto_numero` int(11) DEFAULT '0',
  `data_processo` datetime DEFAULT NULL,
  PRIMARY KEY (`transacao_idx`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

--
-- Estrutura da tabela `[ADMPREFIX]_assinatura_pedido`
--
CREATE TABLE IF NOT EXISTS `[ADMPREFIX]_assinatura` (
  `pedido_idx` int(10) NOT NULL AUTO_INCREMENT,
  `pedido_chave` longtext,
  `cadastro_idx` int(10) DEFAULT NULL,
  `observacoes` longtext,
  `status` int(10) DEFAULT NULL,
  `pagamento_status` int(10) DEFAULT NULL,
  `pagamento_idx` int(10) DEFAULT NULL,
  `valor` decimal(15,2) DEFAULT NULL,
  `desconto_valor` decimal(15,2) DEFAULT NULL,
  `pagamento_com_boleto` int(11) NOT NULL DEFAULT '0',
  `pagamento_com_boleto_desconto_valor` decimal(15,2) NOT NULL DEFAULT '0.00',
  `data_cadastro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pedido_idx`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Estrutura da tabela `[ADMPREFIX]_assinatura_status`
--
CREATE TABLE IF NOT EXISTS `[ADMPREFIX]_assinatura_pedido_status` (
  `status_idx` int(10) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`status_idx`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



--
-- Estrutura da tabela `[ADMPREFIX]_ecommerce_produto`
--
CREATE TABLE IF NOT EXISTS `[ADMPREFIX]_assinatura_plano` (
  `plano_idx` int(10) NOT NULL AUTO_INCREMENT,
  `status` int(10) DEFAULT NULL,
  `quantidade` int(10) DEFAULT NULL,
  `em_oferta` int(10) DEFAULT NULL,
  `em_oferta_valor` numeric(15,2) DEFAULT NULL,
  `nome` varchar(100) DEFAULT NULL,
  `valor` numeric(15,2) DEFAULT NULL,
  `codigo` varchar(255) DEFAULT NULL,
  `descricao_curta` varchar(250) DEFAULT NULL,
  `descricao_longa` longtext,
  `data_cadastro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `url_video` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`plano_idx`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
