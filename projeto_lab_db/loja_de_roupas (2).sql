-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 27/05/2025 às 17:48
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `loja_de_roupas`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `administrador`
--

CREATE TABLE `administrador` (
  `id` int(11) NOT NULL,
  `usuario` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `administrador`
--

INSERT INTO `administrador` (`id`, `usuario`, `senha`) VALUES
(4, 'matheus', '$2y$10$bFnV3xf7nwQXHpInPnvPSeHLkLsk0lqF0towfdkamk89FuJ5M3wvS'),
(5, 'carlao', '$2y$10$G0dl49FF/s798yCfAIXFkOR5qtc3TylasrYu4MjKC8BVNieW3ovxK'),
(6, 'luan', '$2y$10$AnCmVvN1zsgPGRKwcq6TKOHarikifHt0aPjbcG7ciFgMUILElef9e');

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nome` varchar(200) NOT NULL,
  `cpf` char(11) NOT NULL,
  `email` varchar(200) NOT NULL,
  `telefone` varchar(15) DEFAULT NULL,
  `logradouro` varchar(200) DEFAULT NULL,
  `numero` varchar(10) DEFAULT NULL,
  `bairro` varchar(100) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` char(2) DEFAULT NULL,
  `sexo` char(1) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modificado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `clientes`
--

INSERT INTO `clientes` (`id`, `nome`, `cpf`, `email`, `telefone`, `logradouro`, `numero`, `bairro`, `cidade`, `estado`, `sexo`, `data_criacao`, `data_atualizacao`, `modificado_por`) VALUES
(4, 'Matheus Vitor Siqueira G', '22222222222', 'siqueiramatheusvitor@gmail.com', '15997562793', 'rua jose paulo colaco', '212', 'vila la brunetti', 'Itapetininga', 'sp', 'M', '2025-05-10 15:24:41', '2025-05-27 15:11:33', NULL),
(11, 'fabio', '12344556676', 'fabio@gmail.com', '15997562793', 'rua jose paulo colaco', '212', 'vila la brunetti', 'Itapetininga', 'sp', 'M', '2025-05-27 15:36:36', '2025-05-27 15:36:36', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `estoque`
--

CREATE TABLE `estoque` (
  `tamanho` varchar(10) NOT NULL,
  `fk_produto_id` int(11) DEFAULT NULL,
  `quantidade` varchar(255) DEFAULT NULL,
  `data_de_modificacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `data_de_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_de_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `estoque`
--

INSERT INTO `estoque` (`tamanho`, `fk_produto_id`, `quantidade`, `data_de_modificacao`, `data_de_criacao`, `data_de_atualizacao`) VALUES
('32', 1, '20', '2025-05-10 18:40:05', '2025-05-10 18:40:05', '2025-05-10 18:40:05');

-- --------------------------------------------------------

--
-- Estrutura para tabela `forma_pagto`
--

CREATE TABLE `forma_pagto` (
  `id` int(11) NOT NULL,
  `descricao` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `forma_pagto`
--

INSERT INTO `forma_pagto` (`id`, `descricao`) VALUES
(1, 'debito'),
(7, 'credito'),
(8, 'pix');

-- --------------------------------------------------------

--
-- Estrutura para tabela `item_venda`
--

CREATE TABLE `item_venda` (
  `id` int(11) NOT NULL,
  `fk_venda_id` int(11) DEFAULT NULL,
  `fk_produto_id` int(11) DEFAULT NULL,
  `qtd_vendida` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `item_venda`
--

INSERT INTO `item_venda` (`id`, `fk_venda_id`, `fk_produto_id`, `qtd_vendida`) VALUES
(2, 2, 1, 60);

-- --------------------------------------------------------

--
-- Estrutura para tabela `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `titulo` varchar(200) DEFAULT NULL,
  `descricao` varchar(200) DEFAULT NULL,
  `endereco` varchar(150) DEFAULT NULL,
  `link` varchar(200) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `meta_vendas`
--

CREATE TABLE `meta_vendas` (
  `id` int(11) NOT NULL,
  `fk_vendedor_id` int(11) DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_validade` date DEFAULT NULL,
  `modificado_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `meta_vendas`
--

INSERT INTO `meta_vendas` (`id`, `fk_vendedor_id`, `valor`, `status`, `data_criacao`, `data_validade`, `modificado_por`) VALUES
(1, 2, 5000.00, 1, '2025-05-10 14:47:25', '2025-05-15', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `nome` varchar(200) NOT NULL,
  `valor_unidade` decimal(10,2) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 0,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modificado_por` int(11) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `valor_unidade`, `quantidade`, `data_criacao`, `data_atualizacao`, `modificado_por`, `foto`) VALUES
(1, 'tenis', 1.00, 0, '2025-05-06 20:35:38', '2025-05-10 14:02:01', 0, '681f5c59eec72_Captura de tela 2025-05-10 110127.png'),
(4, 'sapato', 10.00, 0, '2025-05-09 20:29:35', '2025-05-10 14:02:13', 0, '681f5c6570c52_Captura de tela 2025-05-10 110145.png');

-- --------------------------------------------------------

--
-- Estrutura para tabela `vendas`
--

CREATE TABLE `vendas` (
  `id` int(11) NOT NULL,
  `fk_cliente_id` int(11) DEFAULT NULL,
  `fk_vendedor_id` int(11) DEFAULT NULL,
  `fk_forma_pagto_id` int(11) DEFAULT NULL,
  `valor` decimal(10,0) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_venda` date DEFAULT curdate(),
  `valor_total` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `vendas`
--

INSERT INTO `vendas` (`id`, `fk_cliente_id`, `fk_vendedor_id`, `fk_forma_pagto_id`, `valor`, `data_criacao`, `data_venda`, `valor_total`) VALUES
(2, 4, 2, 1, 5000, '2025-05-10 15:59:07', '2025-05-10', 0.00);

--
-- Acionadores `vendas`
--
DELIMITER $$
CREATE TRIGGER `verifica_meta_vendas` AFTER INSERT ON `vendas` FOR EACH ROW BEGIN
    DECLARE total_vendas DECIMAL(10,2);
    DECLARE meta DECIMAL(10,2);

    SELECT SUM(valor) INTO total_vendas
    FROM vendas
    WHERE fk_vendedor_id = NEW.fk_vendedor_id 
    AND MONTH(data_venda) = MONTH(CURRENT_DATE())
    AND YEAR(data_venda) = YEAR(CURRENT_DATE());

 
    SELECT valor INTO meta
    FROM meta_vendas
    WHERE fk_vendedor_id = NEW.fk_vendedor_id
    AND data_validade >= CURRENT_DATE() 
    ORDER BY data_validade DESC
    LIMIT 1;

    IF total_vendas >= meta THEN
        UPDATE meta_vendas
        SET status = 1 
        WHERE fk_vendedor_id = NEW.fk_vendedor_id
        AND data_validade >= CURRENT_DATE()
        LIMIT 1;
    ELSE
        UPDATE meta_vendas
        SET status = 0 
        WHERE fk_vendedor_id = NEW.fk_vendedor_id
        AND data_validade >= CURRENT_DATE()
        LIMIT 1;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `vendedores`
--

CREATE TABLE `vendedores` (
  `id` int(11) NOT NULL,
  `nome` varchar(200) NOT NULL,
  `cpf` char(11) NOT NULL,
  `email` varchar(200) NOT NULL,
  `telefone` varchar(15) DEFAULT NULL,
  `logradouro` varchar(255) DEFAULT NULL,
  `numero` varchar(10) DEFAULT NULL,
  `bairro` varchar(100) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(2) DEFAULT NULL,
  `sexo` char(1) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modificado_por` int(11) DEFAULT NULL,
  `senha` varchar(200) DEFAULT NULL,
  `tipo` enum('admin','vendedor') NOT NULL DEFAULT 'vendedor'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `vendedores`
--

INSERT INTO `vendedores` (`id`, `nome`, `cpf`, `email`, `telefone`, `logradouro`, `numero`, `bairro`, `cidade`, `estado`, `sexo`, `data_criacao`, `data_atualizacao`, `modificado_por`, `senha`, `tipo`) VALUES
(2, 'Matheus Vitor Siqueira Gusmão', '537.724.548', 'siqueiramatheusvitor@gmail.com', '15997562793', 'rua jose paulo colaco', '212', 'vila la brunetti', 'Itapetininga', 'sp', 'M', '2025-05-10 14:43:08', '2025-05-10 14:43:08', 1, '$2y$10$jZeBwiVTCzR6kCZnfKsKhOugHoVTwL/FMqdq0BAauFTz.IQ9T9XQO', 'vendedor'),
(3, 'luan', '12345678791', 'luan@gmail.com', '15997562793', 'rua jose paulo colaco', '212', 'vila la brunetti', 'Itapetininga', 'sp', 'M', '2025-05-27 15:30:27', '2025-05-27 15:30:27', 1, '$2y$10$E7I/mzfI3rLtKYO4fx5CfOcY9dTFyW4QamaFpdXMNa/lTv9dtZgWW', 'vendedor');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `administrador`
--
ALTER TABLE `administrador`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cpf` (`cpf`);

--
-- Índices de tabela `estoque`
--
ALTER TABLE `estoque`
  ADD PRIMARY KEY (`tamanho`),
  ADD KEY `fk_produto_id` (`fk_produto_id`);

--
-- Índices de tabela `forma_pagto`
--
ALTER TABLE `forma_pagto`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `item_venda`
--
ALTER TABLE `item_venda`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_venda_id` (`fk_venda_id`),
  ADD KEY `fk_produto_id` (`fk_produto_id`);

--
-- Índices de tabela `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `meta_vendas`
--
ALTER TABLE `meta_vendas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_vendedor_id` (`fk_vendedor_id`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `vendas`
--
ALTER TABLE `vendas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cliente_id` (`fk_cliente_id`),
  ADD KEY `fk_vendedor_id` (`fk_vendedor_id`),
  ADD KEY `fk_forma_pagto_id` (`fk_forma_pagto_id`);

--
-- Índices de tabela `vendedores`
--
ALTER TABLE `vendedores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cpf` (`cpf`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `administrador`
--
ALTER TABLE `administrador`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `forma_pagto`
--
ALTER TABLE `forma_pagto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `item_venda`
--
ALTER TABLE `item_venda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `meta_vendas`
--
ALTER TABLE `meta_vendas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `vendas`
--
ALTER TABLE `vendas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `vendedores`
--
ALTER TABLE `vendedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `estoque`
--
ALTER TABLE `estoque`
  ADD CONSTRAINT `estoque_ibfk_1` FOREIGN KEY (`fk_produto_id`) REFERENCES `produtos` (`id`);

--
-- Restrições para tabelas `item_venda`
--
ALTER TABLE `item_venda`
  ADD CONSTRAINT `item_venda_ibfk_1` FOREIGN KEY (`fk_venda_id`) REFERENCES `vendas` (`id`),
  ADD CONSTRAINT `item_venda_ibfk_2` FOREIGN KEY (`fk_produto_id`) REFERENCES `produtos` (`id`);

--
-- Restrições para tabelas `meta_vendas`
--
ALTER TABLE `meta_vendas`
  ADD CONSTRAINT `meta_vendas_ibfk_1` FOREIGN KEY (`fk_vendedor_id`) REFERENCES `vendedores` (`id`);

--
-- Restrições para tabelas `vendas`
--
ALTER TABLE `vendas`
  ADD CONSTRAINT `vendas_ibfk_1` FOREIGN KEY (`fk_cliente_id`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `vendas_ibfk_2` FOREIGN KEY (`fk_vendedor_id`) REFERENCES `vendedores` (`id`),
  ADD CONSTRAINT `vendas_ibfk_3` FOREIGN KEY (`fk_forma_pagto_id`) REFERENCES `forma_pagto` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
