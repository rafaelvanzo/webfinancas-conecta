-- phpMyAdmin SQL Dump
-- version 4.3.7
-- http://www.phpmyadmin.net
--
-- Host: mysql02-farm61.uni5.net
-- Tempo de geração: 04/06/2019 às 15:23
-- Versão do servidor: 5.5.46-log
-- Versão do PHP: 5.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Banco de dados: `web2business21`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `agenda`
--

CREATE TABLE IF NOT EXISTS `agenda` (
  `Id` int(11) NOT NULL,
  `Situacao` int(11) NOT NULL,
  `IdConsulta` int(11) NOT NULL,
  `IdFavorecido` int(11) NOT NULL,
  `IdResponsavel` int(11) NOT NULL,
  `IdDoutor` int(11) NOT NULL,
  `DataInicial` datetime NOT NULL,
  `DataFinal` datetime NOT NULL,
  `TipoPlano` int(11) NOT NULL,
  `Observacao` text NOT NULL,
  `IdLancamento` int(11) NOT NULL,
  `DtCadastro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Excluido` int(11) NOT NULL,
  `DtExclusao` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `agendaReagendar`
--

CREATE TABLE IF NOT EXISTS `agendaReagendar` (
  `Id` int(11) NOT NULL,
  `Situacao` int(11) NOT NULL,
  `IdConsulta` int(11) NOT NULL,
  `IdFavorecido` int(11) NOT NULL,
  `IdResponsavel` int(11) NOT NULL,
  `IdDoutor` int(11) NOT NULL,
  `DataInicial` datetime NOT NULL,
  `DataFinal` datetime NOT NULL,
  `TipoPlano` int(11) NOT NULL,
  `Observacao` text NOT NULL,
  `IdConsultaReagendada` int(11) NOT NULL,
  `DtCadastro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Excluido` int(11) NOT NULL,
  `DtExclusao` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `configConsultaProc`
--

CREATE TABLE IF NOT EXISTS `configConsultaProc` (
  `Id` int(11) NOT NULL,
  `Descricao` varchar(300) NOT NULL,
  `Tipo` int(11) NOT NULL,
  `Tempo` int(2) NOT NULL,
  `Valor` decimal(10,2) NOT NULL,
  `DtCadastro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Excluido` int(11) NOT NULL,
  `DtExclusao` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `logerro`
--

CREATE TABLE IF NOT EXISTS `logerro` (
  `id` int(11) NOT NULL,
  `DtCadastro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Usuario` varchar(50) NOT NULL,
  `MsgErro` text NOT NULL,
  `Controller` varchar(30) NOT NULL,
  `Action` varchar(50) NOT NULL,
  `Params` varchar(50) NOT NULL,
  `Ip` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------


--
-- Índices de tabelas apagadas
--

--
-- Índices de tabela `agenda`
--
ALTER TABLE `agenda`
  ADD PRIMARY KEY (`Id`);

--
-- Índices de tabela `agendaReagendar`
--
ALTER TABLE `agendaReagendar`
  ADD PRIMARY KEY (`Id`);

--
-- Índices de tabela `configConsultaProc`
--
ALTER TABLE `configConsultaProc`
  ADD PRIMARY KEY (`Id`);

--
-- Índices de tabela `logerro`
--
ALTER TABLE `logerro`
  ADD PRIMARY KEY (`id`);



--
-- AUTO_INCREMENT de tabelas apagadas
--

--
-- AUTO_INCREMENT de tabela `agenda`
--
ALTER TABLE `agenda`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `agendaReagendar`
--
ALTER TABLE `agendaReagendar`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `configConsultaProc`
--
ALTER TABLE `configConsultaProc`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `logerro`
--
ALTER TABLE `logerro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
