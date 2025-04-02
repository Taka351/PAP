-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 12-Fev-2025 às 21:05
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `memomed`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `alerta`
--

CREATE TABLE `alerta` (
  `idalerta` int(11) NOT NULL,
  `datahora` time NOT NULL,
  `refidcompartimento` int(11) NOT NULL,
  `repeticao` enum('diario','semanal','mensal','nenhum') DEFAULT 'nenhum'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `alerta`
--

INSERT INTO `alerta` (`idalerta`, `datahora`, `refidcompartimento`, `repeticao`) VALUES
(3, '08:00:00', 23, 'diario'),
(16, '13:35:00', 25, 'diario'),
(17, '12:51:00', 26, 'semanal'),
(18, '10:27:00', 25, 'semanal'),
(19, '10:52:00', 27, 'nenhum'),
(20, '14:51:00', 27, 'semanal'),
(47, '19:45:00', 28, 'semanal'),
(48, '00:50:00', 28, 'diario'),
(49, '22:06:00', 28, 'semanal');

-- --------------------------------------------------------

--
-- Estrutura da tabela `caixa`
--

CREATE TABLE `caixa` (
  `idcaixa` int(11) NOT NULL,
  `ref_user` int(11) NOT NULL,
  `num_compartimentos` int(11) NOT NULL,
  `modelo` varchar(255) DEFAULT NULL,
  `descricao` text DEFAULT NULL
) ;

--
-- Extraindo dados da tabela `caixa`
--

INSERT INTO `caixa` (`idcaixa`, `ref_user`, `num_compartimentos`, `modelo`, `descricao`) VALUES
(1, 1, 4, NULL, NULL),
(6, 4, 4, 'QR_002', NULL),
(7, 7, 6, 'QR_006', NULL),
(8, 10, 4, 'QR_002', NULL),
(9, 11, 6, 'QR_009', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `compartimento`
--

CREATE TABLE `compartimento` (
  `idcompartimento` int(11) NOT NULL,
  `descricao_compartimento` varchar(255) NOT NULL,
  `comprimidos` varchar(255) DEFAULT NULL,
  `ref_idcaixa` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `compartimento`
--

INSERT INTO `compartimento` (`idcompartimento`, `descricao_compartimento`, `comprimidos`, `ref_idcaixa`) VALUES
(23, 'Noite', 'Ibuprofeno', 1),
(24, 'Extra', 'Antibiótico', 1),
(25, 'Compartimento 3', NULL, 9),
(26, 'Compartimento 6', NULL, 9),
(27, 'Compartimento 5', NULL, 9),
(28, 'Compartimento 1', 'ASCASCA', 6),
(29, 'Compartimento 3', NULL, 6),
(30, 'Compartimento 2', NULL, 6),
(31, 'Compartimento <br />\r\n<b>Warning</b>:  Undefined variable $compartimento_id in <b>C:\\xampp\\htdocs\\memomed\\caixa.php</b> on line <b>540</b><br />\r\n', 'aknas', 6),
(32, 'Compartimento <br />\r\n<b>Warning</b>:  Undefined variable $compartimento_id in <b>C:\\xampp\\htdocs\\memomed\\caixa.php</b> on line <b>541</b><br />\r\n', 'aqks', 6);

-- --------------------------------------------------------

--
-- Estrutura da tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `telefone` varchar(15) DEFAULT NULL,
  `morada` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `created_at`, `updated_at`, `telefone`, `morada`) VALUES
(1, 'JoÃ£o Silva', 'joao.silva@example.com', 'hashed_password1', '2025-01-07 18:01:40', '2025-01-07 18:01:40', '0', ''),
(2, 'Maria Oliveira', 'maria.oliveira@example.com', 'hashed_password2', '2025-01-07 18:01:40', '2025-01-07 18:01:40', '0', ''),
(3, 'santiago', 'santiago@gmail.com', '123', '2025-01-07 18:19:13', '2025-01-07 18:19:13', '0', ''),
(4, 'Santiago Souza', 'santiagosantossouza8@gmail.com', '$2y$10$rWY7fr5REJMu/VvyhoI8Hu3lFNb8j9uStFgYhcezoSwkxSxlbC5HO', '2025-01-07 18:39:32', '2025-01-15 15:18:27', '912121212', 'Rua Marta Aberta Da SIlva Tiago'),
(5, 'Brawlhalla ', '233@gmail.com', '$2y$10$.CIeCh89xsjIkexKMnasSehQ7R2ztbFf3aTAlzq2QxWv4KdOusfM6', '2025-01-07 18:45:37', '2025-01-07 18:45:37', '0', ''),
(6, 'Elaine', 'elaine@gmail.com', '$2y$10$CpK0jLHXewWaMeyCZuYjbeBovPt6CP4eQTH2JOHl.NaUm1HRMIT3S', '2025-01-10 17:34:13', '2025-01-10 17:35:08', '91295678', 'Rua Antonio Silva'),
(7, 'Tiago', 'tiago@gmail.com', '$2y$10$Dr0H/bzRc8PbnokjRtWABe3xWz1P5OAW6f8zMplcihVE.qBrU0pty', '2025-01-13 11:28:34', '2025-01-13 11:29:32', '91899', 'fsfsfs'),
(9, 'MemoMed', 'zemanualdobrawlstrars@gmail.com', '$2y$10$751r08JD1VY7jihWiqvIHOBzb5EUIF1Ld/HnQP7kVdkLCrXY1i55u', '2025-01-15 21:43:09', '2025-01-15 21:43:09', NULL, ''),
(10, 'Tiago89', 'zemanualdobrawlstrar@gmail.coom', '$2y$10$i6AiGTbM8oOwWPr9/CNVfOcEmg5i4eAStxO7kcJO6NAF0G5hoxrNu', '2025-01-15 21:43:39', '2025-01-15 21:43:39', NULL, ''),
(11, 'MemoMed', 'santiagosantossouza88@gmail.com', '$2y$10$6xYl3pEoGm2pGvQi3sA5euJadY0i2NlGODkhDz53ucEJZYZ7XsoZ2', '2025-01-15 22:36:16', '2025-01-16 09:45:49', '912956783', 'Rua Marta Aberta Da SIlva Tiago'),
(12, 'remot600', 'remot@gmail.com', '$2y$10$8gwmRpxmQxh79N..8kKFP.xfn/Uy4MTKiYOPo5JniYnSUsfVPrLn2', '2025-02-10 12:20:51', '2025-02-10 12:20:51', NULL, '');

--
-- Acionadores `users`
--
DELIMITER $$
CREATE TRIGGER `verificar_telefone` BEFORE INSERT ON `users` FOR EACH ROW BEGIN
    IF LENGTH(NEW.telefone) != 8 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'O telefone deve ter exatamente 8 dígitos.';
    END IF;
END
$$
DELIMITER ;

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `alerta`
--
ALTER TABLE `alerta`
  ADD PRIMARY KEY (`idalerta`),
  ADD KEY `alerta_ibfk_1` (`refidcompartimento`);

--
-- Índices para tabela `caixa`
--
ALTER TABLE `caixa`
  ADD PRIMARY KEY (`idcaixa`),
  ADD KEY `ref_user` (`ref_user`);

--
-- Índices para tabela `compartimento`
--
ALTER TABLE `compartimento`
  ADD PRIMARY KEY (`idcompartimento`),
  ADD KEY `ref_idcaixa` (`ref_idcaixa`);

--
-- Índices para tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `alerta`
--
ALTER TABLE `alerta`
  MODIFY `idalerta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT de tabela `caixa`
--
ALTER TABLE `caixa`
  MODIFY `idcaixa` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `compartimento`
--
ALTER TABLE `compartimento`
  MODIFY `idcompartimento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `alerta`
--
ALTER TABLE `alerta`
  ADD CONSTRAINT `alerta_ibfk_1` FOREIGN KEY (`refidcompartimento`) REFERENCES `compartimento` (`idcompartimento`);

--
-- Limitadores para a tabela `caixa`
--
ALTER TABLE `caixa`
  ADD CONSTRAINT `caixa_ibfk_1` FOREIGN KEY (`ref_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `compartimento`
--
ALTER TABLE `compartimento`
  ADD CONSTRAINT `compartimento_ibfk_1` FOREIGN KEY (`ref_idcaixa`) REFERENCES `caixa` (`idcaixa`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
