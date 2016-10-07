--
-- Banco de Dados: `gerenciador_tarefas`
--

CREATE DATABASE IF NOT EXISTS gerenciador_tarefas;
 
USE gerenciador_tarefas;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tarefa`
--

CREATE TABLE IF NOT EXISTS `tarefa` (
  `tarefa_id` int(11) NOT NULL AUTO_INCREMENT,
  `tarefa_titulo` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `tarefa_status` int(1) NOT NULL,
  `tarefa_descricao` text COLLATE utf8_unicode_ci,
  `tarefa_data_criacao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
  `tarefa_data_edicao` TIMESTAMP,
  `tarefa_data_remocao` TIMESTAMP,
  `tarefa_data_conclusao` TIMESTAMP,
  `tarefa_removida` int(1) DEFAULT 0 NOT NULL,
  PRIMARY KEY (`tarefa_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Extraindo dados da tabela `tarefa`
--

INSERT INTO `tarefa` (`tarefa_id`, `tarefa_titulo`, `tarefa_status`, `tarefa_descricao`)
   VALUES (1, 'Tarefa 01', '0', 'Primeira tarefa de teste'),
          (2, 'Tarefa 02', '0', 'Segunda tarefa de teste'),
          (3, 'Tarefa 03', '0', 'Terceira tarefa de teste'),
          (4, 'Tarefa 04', '0', 'Quarta tarefa de teste');