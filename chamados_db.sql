-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 24/09/2026 às 02:51
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
-- Banco de dados: `chamados_db`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `chamados`
--

CREATE TABLE `chamados` (
  `id` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `descricao` text NOT NULL,
  `urgencia` enum('baixa','media','alta','critica') NOT NULL DEFAULT 'media',
  `status` enum('aberto','em_andamento','fechado') NOT NULL DEFAULT 'aberto',
  `usuario_id` int(11) NOT NULL,
  `atendente_id` int(11) DEFAULT NULL,
  `data_abertura` datetime NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `data_fechamento` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `chamados`
--

INSERT INTO `chamados` (`id`, `titulo`, `descricao`, `urgencia`, `status`, `usuario_id`, `atendente_id`, `data_abertura`, `data_atualizacao`, `data_fechamento`) VALUES
(1, 'Meu email foi bloqueado', 'Quando acessei pela manhã apareceu como bloqueado', 'media', 'aberto', 1, NULL, '2026-09-23 20:30:41', '2026-09-23 20:30:41', NULL),
(2, 'PC lento', 'Mesmo com a limpeza meu PC não esta rodando direito e não abre algumas janelas', 'baixa', 'aberto', 1, NULL, '2026-09-23 20:40:23', '2026-09-23 20:40:23', NULL),
(3, 'PC desligado', 'O meu computador não liga e meus projetos estão dele', 'alta', 'fechado', 1, 3, '2026-09-23 20:57:44', '2026-09-23 20:58:44', '2026-09-23 20:58:44');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo` enum('admin','atendente','cliente') NOT NULL DEFAULT 'cliente',
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `tipo`, `criado_em`) VALUES
(1, 'Claudia Vieira Gomes', 'cvieira@gmail.com', '$2y$10$iALSMfnsKnbSarB19hZLX.OukNs/5Yv6vEbmdehe/iihGNqMdipzq', 'admin', '2026-09-23 20:29:07'),
(2, 'Fabio Soares Silva', 'Fabiosoa@gmail.com', '$2y$10$aEJf6A9zfbHPhuf6R1JULuiD8jyRtMqtqvnE453Bs9342N/.VX.Xy', 'cliente', '2026-09-23 20:46:37'),
(3, 'Ana Beatriz', 'Beatriz44@gmail.com', '$2y$10$K7XrepA/53BxYoiVhPwbQegDUyTijXYqG4N3RvoDeDcsLwSjwmmee', 'atendente', '2026-09-23 20:50:23'),
(4, 'Wilson Silva', 'wssilva@gmail.com', '$2y$10$Mi6RkdF8Scm9LlLl3Da3IuYzBi6nswDJiRtFDNW8C1Gs3Ud6ZRU86', 'cliente', '2026-09-23 20:59:33');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `chamados`
--
ALTER TABLE `chamados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `atendente_id` (`atendente_id`),
  ADD KEY `idx_chamados_status` (`status`),
  ADD KEY `idx_chamados_urgencia` (`urgencia`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `chamados`
--
ALTER TABLE `chamados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `chamados`
--
ALTER TABLE `chamados`
  ADD CONSTRAINT `chamados_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `chamados_ibfk_2` FOREIGN KEY (`atendente_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
