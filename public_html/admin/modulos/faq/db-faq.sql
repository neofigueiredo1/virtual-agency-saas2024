--
-- Estrutura da tabela `[ADMPREFIX]_faq`
--
CREATE TABLE IF NOT EXISTS `[ADMPREFIX]_faq` (
  `faq_idx` int(10) NOT NULL AUTO_INCREMENT,
  `status` int(10) DEFAULT NULL,
  `ranking` int(10) DEFAULT NULL,
  `nome` varchar(200) DEFAULT NULL,
  `descricao` varchar(500) DEFAULT NULL,
  `data_cadastro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`faq_idx`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Estrutura da tabela `[ADMPREFIX]_faq_item`
--
CREATE TABLE IF NOT EXISTS `[ADMPREFIX]_faq_item` (
  `item_idx` int(10) NOT NULL AUTO_INCREMENT,
  `faq_idx` int(10) DEFAULT NULL,
  `ranking` int(10) DEFAULT NULL,
  `status` int(10) DEFAULT NULL,
  `pergunta` varchar(500) DEFAULT NULL,
  `resposta` varchar(500) DEFAULT NULL,
  `data_cadastro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`item_idx`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;