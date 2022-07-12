-- phpMyAdmin SQL Dump
-- version 4.3.7
-- http://www.phpmyadmin.net
--
-- Host: mysql04-farm59.uni5.net
-- Tempo de geraÃ§Ã£o: 07/09/2017 Ã s 14:37
-- VersÃ£o do servidor: 5.5.40-log
-- VersÃ£o do PHP: 5.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Estrutura para tabela `arq_classificacao`
--

CREATE TABLE IF NOT EXISTS `arq_classificacao` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

--
-- Fazendo dump de dados para tabela `arq_classificacao`
--

INSERT INTO `arq_classificacao` (`id`, `nome`) VALUES
(1, 'Dep. ContÃ¡bil'),
(2, 'Dep. Fiscal'),
(3, 'Dep. FiscalizaÃ§Ã£o - AlvarÃ¡'),
(4, 'Dep. FiscalizaÃ§Ã£o - Documentos'),
(5, 'Dep. FiscalizaÃ§Ã£o - Registros'),
(6, 'Dep. Pessoal'),
(7, 'Dep. Pessoal - AdmissÃ£o'),
(8, 'Dep. Pessoal - Diversos'),
(9, 'Dep. Pessoal - FÃ©rias'),
(10, 'Dep. Pessoal - RecisÃ£o'),
(11, 'Livro Caixa'),
(12, 'Outro Documento'),
(13, 'Contabilidade');

-- --------------------------------------------------------

--
-- Estrutura para tabela `arq_tp_documento`
--

CREATE TABLE IF NOT EXISTS `arq_tp_documento` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Fazendo dump de dados para tabela `arq_tp_documento`
--

INSERT INTO `arq_tp_documento` (`id`, `nome`) VALUES
(1, 'Conta Ã  pagar'),
(2, 'Outros documentos'),
(3, 'RecÃ¡lculo');

-- --------------------------------------------------------

--
-- Estrutura para tabela `bancos`
--

CREATE TABLE IF NOT EXISTS `bancos` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) CHARACTER SET utf8 NOT NULL,
  `codigo` varchar(3) CHARACTER SET utf8 NOT NULL,
  `logo` varchar(20) CHARACTER SET utf8 NOT NULL,
  `logo_boleto` varchar(20) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=168 DEFAULT CHARSET=utf8;

--
-- Fazendo dump de dados para tabela `bancos`
--

INSERT INTO `bancos` (`id`, `nome`, `codigo`, `logo`, `logo_boleto`) VALUES
(1, 'BANCO DO BRASIL S.A.', '001', 'bb.png', 'logobb.jpg'),
(2, 'BANCO DA AMAZONIA S.A.', '003', '', ''),
(3, 'BANCO DO NORDESTE DO BRASIL S.A.', '004', '', ''),
(4, 'CC CREDICOAMO CREDITO RURAL COOPERATIVA', '010', '', ''),
(5, 'CREDIT SUISSE HEDGING-GRIFFO CORRETORA DE VALORES', '011', '', ''),
(6, 'SENSO CORRETORA DE CAMBIO E VALORES MOBILIARIOS SA', '013', '', ''),
(7, 'NATIXIS BRASIL S.A. - BANCO MÃºLTIPLO', '014', '', ''),
(8, 'SC UBS BRASIL', '015', '', ''),
(9, 'COOPERATIVA DE CRÃ©DITO MÃºTUO DOS DESPACHANTES DE', '016', '', ''),
(10, 'BNY MELLON S.A.', '017', '', ''),
(11, 'BM TRICURY S.A.', '018', '', ''),
(12, 'BANCO AZTECA DO BRASIL S.A.', '019', '', ''),
(13, 'BANESTES S.A BANCO DO ESTADO DO ESPIRITO SANTO', '021', 'banestes.png', 'banestes.jpg'),
(14, 'BANCO ALFA S/A', '025', '', ''),
(15, 'BANCO SANTANDER (BRASIL) S.A.', '033', 'santander.png', 'santander.jpg'),
(16, 'BANCO BRADESCO BBI S.A', '036', 'bradesco.png', ''),
(17, 'BANCO DO ESTADO DO PARA S.A.', '037', '', ''),
(18, 'BANCO CARGILL S.A', '040', '', ''),
(19, 'BANCO DO ESTADO DO RIO GRANDE DO SUL S.A.', '041', '', ''),
(20, 'BANCO DO ESTADO DE SERGIPE S.A.', '047', '', ''),
(21, 'CONFIDENCE CORRETORA DE CAMBIO S.A.', '060', '', ''),
(22, 'HIPERCARD BANCO MULTIPLO S.A', '062', '', ''),
(23, 'BANCO BRADESCARD S.A.', '063', '', ''),
(24, 'GOLDMAN SACHS DO BRASIL-BANCO MULTIPLO S.A', '064', '', ''),
(25, 'BANCO BRACCE S.A.', '065', '', ''),
(26, 'BANCO MORGAN STANLEY DEAN WITTER S.A', '066', '', ''),
(27, 'BPN BRASIL BANCO MULTIPLO S.A', '069', '', ''),
(28, 'BRB - BANCO DE BRASILIA S.A.', '070', '', ''),
(29, 'BANCO J. SAFRA S.A.', '074', '', ''),
(30, 'BANCO ABN AMRO S.A.', '075', '', ''),
(31, 'BANCO KDB DO BRASIL S.A', '076', '', ''),
(32, 'BANCO INTERMEDIUM S.A.', '077', '', ''),
(33, 'BES INVESTIMENTO DO BRASIL SA - BANCO DE INVESTIM.', '078', '', ''),
(34, 'BANCO ORIGINAL DO AGRONEGOCIO S.A.', '079', '', ''),
(35, 'B&amp;T ASSOCIADOS CORRETORA DE CAMBIO LTDA.', '080', '', ''),
(36, 'BBN BANCO BRASILEIRO DE NEGOCIOS S.A', '081', '', ''),
(37, 'BANCO TOPAZIO S.A.', '082', '', ''),
(38, 'BANCO DA CHINA BRASIL S.A.', '083', '', ''),
(39, 'CC UNIPRIME NORTE DO PARANA', '084', '', ''),
(40, 'COOPERATIVA CENTRAL DE CREDITO URBANO - CECRED', '085', '', ''),
(41, 'UNICRED CENTRAL SANTA CATARINA', '087', '', ''),
(42, 'BANCO RANDON S.A.', '088', '', ''),
(43, 'COOPERATIVA DE CREDITO RURAL DA REGIAO DA MOGIANA', '089', '', ''),
(44, 'COOPERATIVA CENTRAL DE CREDITO DO ESTADO DE SP', '090', '', ''),
(45, 'UNICRED CENTRAL RS - CENTRAL DE COOP ECON CRED MUT', '091', '', ''),
(46, 'BRICKELL S A CREDITO, FINANCIAMENTO E INVESTIMENTO', '092', '', ''),
(47, 'POLOCRED SOC CRED MICROEMP LTDA', '093', '', ''),
(48, 'BANCO PETRA S.A.', '094', '', ''),
(49, 'BANCO CONFIDENCE DE CAMBIO S.A.', '095', '', ''),
(50, 'BANCO BMF BOVESPA', '096', '', ''),
(51, 'COOPERATIVA CENTRAL DE CREDITO NOROESTE BRASILEIRO', '097', '', ''),
(52, 'CREDIALIANCA COOPERATIVA DE CREDITO RURAL', '098', '', ''),
(53, 'UNIPRIME CENTRAL - CENTRAL INT DE COOP DE CRED LTD', '099', '', ''),
(54, 'PLANNER CORRETORA DE VALORES S A', '100', '', ''),
(55, 'RENASCENCA DISTR TIT E VALORES MOBILIARIOS LTDA', '101', '', ''),
(56, 'XP INVEST CORRETORA DE CAMBIO TIT E VALORES MOB SA', '102', '', ''),
(57, 'CAIXA ECONOMICA FEDERAL', '104', 'caixa.png', 'logocaixa.jpg'),
(58, 'LECCA CREDITO FINANCIAMENTO E INVESTIMENTO S.A.', '105', '', ''),
(59, 'BANCO BBM S.A', '107', '', ''),
(60, 'PORTOCRED S A CREDITO FINANCIAMENTO E INVESTIMENTO', '108', '', ''),
(61, 'OLIVEIRA TRUST DISTRIBUIDORA TITULOS VALORES MOBIL', '111', '', ''),
(62, 'CC UNICRED BRASIL CENTRAL', '112', '', ''),
(63, 'MAGLIANO S.A CORRETORA CAMBIO E VALORES MOBILIARIO', '113', '', ''),
(64, 'CECOOPES-CENTRAL DAS COOP DE ECON E CRED MUTUO DO', '114', '', ''),
(65, 'ADVANCED CORRETORA DE CAMBIO LTDA', '117', '', ''),
(66, 'STANDARD CHARTERED BANK BRASIL S.A. - BANCO DE INV', '118', '', ''),
(67, 'BANCO WESTERN UNION DO BRASIL S.A.', '119', '', ''),
(68, 'BANCO RODOBENS S.A', '120', '', ''),
(69, 'BANCO GERADOR S.A.', '121', '', ''),
(70, 'BRADESCO BERJ', '122', 'bradesco.png', ''),
(71, 'AGIPLAN FINANCEIRA S.A. CRÃ©DITO, FINANCIAMENTO E ', '123', '', ''),
(72, 'BANCO WOORI BANK DO BRASIL S.A', '124', '', ''),
(73, 'BRASIL PLURAL S.A. BANCO MULTIPLO', '125', '', ''),
(74, 'BR PARTNERS BANCO DE INVESTIMENTO S.A.', '126', '', ''),
(75, 'CODEPE - CORRETORA DE VALORES S.A.', '127', '', ''),
(76, 'CARUANA S.A. SOCIEDADE DE CRÃ©DITO FINANCIAMENTO E', '130', '', ''),
(77, 'SC TULLETT PREBON', '131', '', ''),
(78, 'ICBC DO BRASIL BANCO MÃºLTIPLO S.A.', '132', '', ''),
(79, 'BANCO ITAU BBA S.A', '184', '', ''),
(80, 'BANCO BRADESCO CARTOES S.A.', '204', 'bradesco.png', ''),
(81, 'BANCO BTG PACTUAL S.A.', '208', '', ''),
(82, 'BANCO ORIGINAL S.A.', '212', '', ''),
(83, 'BANCO ARBI S.A.', '213', '', ''),
(84, 'BANCO DIBENS S.A.', '214', '', ''),
(85, 'BANCO JOHN DEERE S.A.', '217', '', ''),
(86, 'BANCO BONSUCESSO S.A.', '218', '', ''),
(87, 'BANCO CREDIT AGRICOLE BRASIL S.A.', '222', '', ''),
(88, 'BANCO FIBRA S.A.', '224', '', ''),
(89, 'UNICARD BANCO MULTIPLO S.A', '230', '', ''),
(90, 'BANCO CIFRA S.A.', '233', '', ''),
(91, 'BANCO BRADESCO S.A.', '237', 'bradesco.png', 'bradesco.jpg'),
(92, 'BANCO CLASSICO S.A.', '241', '', ''),
(93, 'BANCO MAXIMA S.A.', '243', '', ''),
(94, 'BANCO ABC BRASIL S.A.', '246', '', ''),
(95, 'BANCO BOA VISTA INTERATLANTICO S.A', '248', '', ''),
(96, 'BANCO INVESTCRED UNIBANCO S.A', '249', '', ''),
(97, 'BCV - BANCO DE CREDITO E VAREJO S.A', '250', '', ''),
(98, 'PARANA BANCO S.A.', '254', '', ''),
(99, 'BANCO CACIQUE S.A.', '263', '', ''),
(100, 'BANCO FATOR S.A.', '265', '', ''),
(101, 'BANCO CEDULA S.A.', '266', '', ''),
(102, 'BANCO DE LA NACION ARGENTINA', '300', '', ''),
(103, 'BANCO BMG S.A.', '318', '', ''),
(104, 'BANCO INDUSTRIAL E COMERCIAL S.A.', '320', '', ''),
(105, 'ITAU UNIBANCO S.A.', '341', 'itau-unibanco.png', ''),
(106, 'BANCO ABN AMRO REAL S.A.', '356', '', ''),
(107, 'BANCO SOCIETE GENERALE BRASIL S.A', '366', '', ''),
(108, 'BANCO MIZUHO DO BRASIL S.A.', '370', '', ''),
(109, 'BANCO J.P. MORGAN S.A.', '376', '', ''),
(110, 'BANCO MERCANTIL DO BRASIL S.A.', '389', '', ''),
(111, 'BANCO BRADESCO FINANCIAMENTOS S.A.', '394', 'bradesco.png', ''),
(112, 'BANCO CAPITAL S.A.', '412', '', ''),
(113, 'BANCO SAFRA S.A.', '422', '', ''),
(114, 'BANCO DE TOKYO MITSUBISHI UFJ BRASIL S.A', '456', '', ''),
(115, 'BANCO SUMITOMO MITSUI BRASILEIRO S.A.', '464', '', ''),
(116, 'BANCO CAIXA GERAL - BRASIL S.A.', '473', '', ''),
(117, 'CITIBANK N.A.', '477', '', ''),
(118, 'DEUTSCHE BANK S. A. - BANCO ALEMAO', '487', '', ''),
(119, 'JPMORGAN CHASE BANK', '488', '', ''),
(120, 'ING BANK N.V.', '492', '', ''),
(121, 'BANCO DE LA REPUBLICA ORIENTAL DEL URUGUAY', '494', '', ''),
(122, 'BANCO DE LA PROVINCIA DE BUENOS AIRES', '495', '', ''),
(123, 'BANCO CREDIT SUISSE (BRASIL) S.A.', '505', '', ''),
(124, 'BANCO LUSO BRASILEIRO S.A.', '600', '', ''),
(125, 'BANCO INDUSTRIAL DO BRASIL S. A.', '604', '', ''),
(126, 'BANCO VR S.A.', '610', '', ''),
(127, 'BANCO PAULISTA S.A.', '611', '', ''),
(128, 'BANCO GUANABARA S.A.', '612', '', ''),
(129, 'BANCO PECUNIA S.A.', '613', '', ''),
(130, 'BANCO PANAMERICANO S.A.', '623', '', ''),
(131, 'BANCO FICSA S.A.', '626', '', ''),
(132, 'BANCO INTERCAP S.A.', '630', '', ''),
(133, 'BANCO RENDIMENTO S.A.', '633', '', ''),
(134, 'BANCO TRIANGULO S.A.', '634', '', ''),
(135, 'BANCO SOFISA S.A.', '637', '', ''),
(136, 'BANCO PINE S.A.', '643', '', ''),
(137, 'BANCO ITAU HOLDING FINANCEIRA S.A', '652', 'itau-unibanco.png', ''),
(138, 'BANCO INDUSVAL S.A.', '653', '', ''),
(139, 'BANCO A.J. RENNER S.A.', '654', '', ''),
(140, 'BANCO VOTORANTIM S.A.', '655', '', ''),
(141, 'BANCO DAYCOVAL S.A.', '707', '', ''),
(142, 'BM OURINVEST', '712', '', ''),
(143, 'BANIF-BANCO INTERNACIONAL DO FUNCHAL (BRASIL) S.A', '719', '', ''),
(144, 'BANCO POTTENCIAL S.A.', '735', '', ''),
(145, 'BANCO BGN S.A.', '739', '', ''),
(146, 'BANCO BARCLAYS S.A.', '740', '', ''),
(147, 'BANCO RIBEIRAO PRETO S.A.', '741', '', ''),
(148, 'BANCO SEMEAR S.A.', '743', '', ''),
(149, 'BANCO CITIBANK S.A.', '745', '', ''),
(150, 'BANCO MODAL S.A.', '746', '', ''),
(151, 'BANCO RABOBANK INTERNATIONAL BRASIL S.A.', '747', '', ''),
(152, 'BANCO COOPERATIVO SICREDI S.A.', '748', '', ''),
(153, 'SCOTIABANK BRASIL S.A BANCO MULTIPLO', '751', '', ''),
(154, 'BANCO BNP PARIBAS BRASIL S.A', '752', '', ''),
(155, 'NOVO BANCO CONTINENTAL', '753', '', ''),
(156, ' BANK OF AMERICA MERRILL LYNCH BANCO MULTIPLO S.A.', '755', '', ''),
(157, 'BANCO COOPERATIVO DO BRASIL S.A.', '756', 'sicoob.png', 'sicoob.jpg'),
(158, 'BANCO KEB DO BRASIL S.A.', '757', '', ''),
(159, 'CORRETORA SOUZA BARROS CAMBIO E TITULOS S.A.', '901', '', ''),
(160, 'BBBIRO', '991', '', ''),
(161, 'ABBI', '992', '', ''),
(162, 'BUREAU BCN', '993', '', ''),
(163, 'BUREAU NACIONAL', '994', '', ''),
(164, 'BUREAU ABBC', '995', '', ''),
(165, 'BUREAU FEBRABAN', '996', '', ''),
(166, 'ASSOCIACAO DE BANCOS ESTADUAIS', '997', '', ''),
(167, 'HSBC BANK BRASIL S.A. - BANCO MULTIPLO', '399', 'hsbc.png', '');

-- --------------------------------------------------------

--
-- Estrutura para tabela `boletos`
--

CREATE TABLE IF NOT EXISTS `boletos` (
  `id` int(11) NOT NULL,
  `sequencial` int(11) NOT NULL,
  `lancamento_id` int(11) NOT NULL,
  `visualizado` tinyint(1) NOT NULL,
  `dt_visualizado` datetime NOT NULL,
  `nosso_numero` varchar(25) NOT NULL,
  `remessa_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `boletos_remessa`
--

CREATE TABLE IF NOT EXISTS `boletos_remessa` (
  `id` int(11) NOT NULL,
  `dt_cadastro` date DEFAULT NULL,
  `nome` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `conta_id` int(11) NOT NULL,
  `banco_id` int(11) DEFAULT NULL,
  `numero_remessa` int(11) DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `qtd_boletos` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `centro_resp`
--

CREATE TABLE IF NOT EXISTS `centro_resp` (
  `id` int(11) NOT NULL,
  `cod_centro` varchar(255) CHARACTER SET utf8 NOT NULL,
  `cod_ref` varchar(10) CHARACTER SET utf8 NOT NULL,
  `nome` varchar(50) CHARACTER SET utf8 NOT NULL,
  `hierarquia` text CHARACTER SET utf8 NOT NULL,
  `centro_pai_id` int(11) NOT NULL,
  `tp_centro` int(11) NOT NULL,
  `descricao` text CHARACTER SET utf8 NOT NULL,
  `nivel` int(11) NOT NULL,
  `posicao` int(11) NOT NULL,
  `situacao` int(11) NOT NULL,
  `dt_cadastro` date NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

--
-- Fazendo dump de dados para tabela `centro_resp`
--

INSERT INTO `centro_resp` (`id`, `cod_centro`, `cod_ref`, `nome`, `hierarquia`, `centro_pai_id`, `tp_centro`, `descricao`, `nivel`, `posicao`, `situacao`, `dt_cadastro`) VALUES
(0, '0', '', 'NÃ£o alocado', '0', 0, 1, '', 1, 0, 0, '0000-00-00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `conexao`
--

CREATE TABLE IF NOT EXISTS `conexao` (
  `id` int(11) NOT NULL,
  `dt_convite` datetime NOT NULL,
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `contador_id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `dt_inicio` datetime NOT NULL,
  `dt_final` datetime NOT NULL,
  `conectado` int(11) NOT NULL,
  `remetente` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `contas`
--

CREATE TABLE IF NOT EXISTS `contas` (
  `id` int(11) NOT NULL,
  `banco_id` int(11) NOT NULL,
  `descricao` varchar(50) NOT NULL,
  `agencia` varchar(20) NOT NULL,
  `agencia_dv` varchar(2) NOT NULL,
  `numero` varchar(20) NOT NULL,
  `numero_dv` varchar(2) NOT NULL,
  `limite_credito` decimal(10,2) NOT NULL,
  `vl_credito` decimal(20,2) NOT NULL,
  `vl_saldo` decimal(10,2) NOT NULL,
  `vl_saldo_inicial` decimal(10,2) NOT NULL,
  `contato` varchar(50) NOT NULL,
  `contato_tel` varchar(20) NOT NULL,
  `contato_email` varchar(50) NOT NULL,
  `observacao` text NOT NULL,
  `vl_saldo_banco` decimal(10,2) NOT NULL,
  `dt_saldo_banco` date NOT NULL,
  `carteira` varchar(10) NOT NULL,
  `convenio` varchar(15) NOT NULL,
  `variacao` varchar(5) NOT NULL,
  `nomeTitular` varchar(50) NOT NULL,
  `inscricao` varchar(4) NOT NULL,
  `cpf_cnpj` varchar(30) NOT NULL,
  `cod_cliente` tinyint(6) NOT NULL,
  `modalidade` char(2) NOT NULL,
  `categoria` char(1) NOT NULL,
  `cedente_cod_barras` tinyint(6) NOT NULL,
  `ceb` tinyint(4) NOT NULL,
  `boleto_ano` char(2) NOT NULL,
  `sequencial` int(11) NOT NULL,
  `custo_emissao` decimal(4,2) NOT NULL,
  `juros` decimal(4,2) NOT NULL,
  `custo_compensacao` decimal(4,2) NOT NULL,
  `msg1` varchar(100) NOT NULL,
  `msg2` varchar(100) NOT NULL,
  `msg3` varchar(100) NOT NULL,
  `inst1` varchar(100) NOT NULL,
  `inst2` varchar(100) NOT NULL,
  `inst3` varchar(100) NOT NULL,
  `multa` decimal(4,2) NOT NULL,
  `carne_leao` tinyint(1) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Fazendo dump de dados para tabela `contas`
--

INSERT INTO `contas` (`id`, `banco_id`, `descricao`, `agencia`, `agencia_dv`, `numero`, `numero_dv`, `limite_credito`, `vl_credito`, `vl_saldo`, `vl_saldo_inicial`, `contato`, `contato_tel`, `contato_email`, `observacao`, `vl_saldo_banco`, `dt_saldo_banco`, `carteira`, `convenio`, `variacao`, `nomeTitular`, `inscricao`, `cpf_cnpj`, `cod_cliente`, `modalidade`, `categoria`, `cedente_cod_barras`, `ceb`, `boleto_ano`, `sequencial`, `custo_emissao`, `juros`, `custo_compensacao`, `msg1`, `msg2`, `msg3`, `inst1`, `inst2`, `inst3`, `multa`, `carne_leao`) VALUES
(1, 0, 'Livro de Caixa', '', '', '', '', '0.00', '0.00', '0.00', '0.00', '', '', '', '', '0.00', '0000-00-00', '', '', '', '', '', '', 0, '', '', 0, 0, '', 0, '0.00', '0.00', '0.00', '', '', '', '', '', '', '0.00', 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `ctr_plc_lancamentos`
--

CREATE TABLE IF NOT EXISTS `ctr_plc_lancamentos` (
  `id` int(11) NOT NULL,
  `lancamento_id` int(11) NOT NULL,
  `centro_resp_id` int(11) NOT NULL,
  `plano_contas_id` int(11) NOT NULL,
  `tp_lancamento` varchar(1) CHARACTER SET utf8 NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `porcentagem` decimal(21,20) NOT NULL,
  `situacao` int(1) NOT NULL,
  `dt_cadastro` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `ctr_plc_lancamentos_plnj`
--

CREATE TABLE IF NOT EXISTS `ctr_plc_lancamentos_plnj` (
  `id` int(11) NOT NULL,
  `lancamento_plnj_id` int(11) NOT NULL,
  `centro_resp_id` int(11) NOT NULL,
  `plano_contas_id` int(11) NOT NULL,
  `tp_lancamento` char(1) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `porcentagem` decimal(21,20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `ctr_plc_lancamentos_rcr`
--

CREATE TABLE IF NOT EXISTS `ctr_plc_lancamentos_rcr` (
  `id` int(11) NOT NULL,
  `lancamento_rcr_id` int(11) NOT NULL,
  `centro_resp_id` int(11) NOT NULL,
  `plano_contas_id` int(11) NOT NULL,
  `tp_lancamento` char(1) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `porcentagem` decimal(21,20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `documentos`
--

CREATE TABLE IF NOT EXISTS `documentos` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

--
-- Fazendo dump de dados para tabela `documentos`
--

INSERT INTO `documentos` (`id`, `nome`) VALUES
(3, 'CarnÃª'),
(4, 'DARF'),
(5, 'Fatura'),
(6, 'Duplicata'),
(7, 'Boleto'),
(8, 'DepÃ³sito em Conta Corrente'),
(9, 'Cheque'),
(10, 'Dinheiro'),
(11, 'CartÃ£o de DÃ©bito'),
(12, 'CartÃ£o CrÃ©dito'),
(13, 'DÃ©bito em Conta'),
(14, 'Nota PromissÃ³ria'),
(15, 'DOC'),
(16, 'TED'),
(17, 'TransferÃªncia entre Contas Correntes'),
(18, 'TransferÃªncia entre PoupanÃ§as'),
(19, 'TransferÃªncia entre CC para PoupanÃ§a'),
(20, 'TransferÃªncia entre PoupanÃ§a para CC'),
(21, 'Nota Fiscal'),
(22, 'Recibo');

-- --------------------------------------------------------

--
-- Estrutura para tabela `favorecidos`
--

CREATE TABLE IF NOT EXISTS `favorecidos` (
  `id` int(11) NOT NULL,
  `inscricao` varchar(4) CHARACTER SET utf8 NOT NULL,
  `cpf_cnpj` varchar(30) CHARACTER SET utf8 NOT NULL,
  `tp_favorecido` int(1) NOT NULL,
  `nome` varchar(50) CHARACTER SET utf8 NOT NULL,
  `email` varchar(50) CHARACTER SET utf8 NOT NULL,
  `logradouro` varchar(100) CHARACTER SET utf8 NOT NULL,
  `numero` varchar(10) CHARACTER SET utf8 NOT NULL,
  `complemento` varchar(100) CHARACTER SET utf8 NOT NULL,
  `bairro` varchar(50) CHARACTER SET utf8 NOT NULL,
  `cidade` varchar(50) CHARACTER SET utf8 NOT NULL,
  `uf` varchar(2) CHARACTER SET utf8 NOT NULL,
  `cep` varchar(20) CHARACTER SET utf8 NOT NULL,
  `telefone` varchar(20) CHARACTER SET utf8 NOT NULL,
  `celular` varchar(20) CHARACTER SET utf8 NOT NULL,
  `observacao` text CHARACTER SET utf8 NOT NULL,
  `tp_conta` varchar(2) CHARACTER SET utf8 NOT NULL,
  `banco_id` int(5) NOT NULL,
  `agencia` varchar(10) CHARACTER SET utf8 NOT NULL,
  `conta` varchar(15) CHARACTER SET utf8 NOT NULL,
  `cliente_ctr_id` smallint(6) NOT NULL,
  `cliente_plc_id` smallint(6) NOT NULL,
  `fornecedor_ctr_id` smallint(6) NOT NULL,
  `fornecedor_plc_id` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `favorecidos_tipo`
--

CREATE TABLE IF NOT EXISTS `favorecidos_tipo` (
  `id` int(11) NOT NULL,
  `tipo` varchar(50) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Fazendo dump de dados para tabela `favorecidos_tipo`
--

INSERT INTO `favorecidos_tipo` (`id`, `tipo`) VALUES
(1, 'Cliente'),
(2, 'Fornecedor'),
(3, 'Cliente / Fornecedor');

-- --------------------------------------------------------

--
-- Estrutura para tabela `forma_pagamento`
--

CREATE TABLE IF NOT EXISTS `forma_pagamento` (
  `id` int(11) NOT NULL,
  `forma` varchar(50) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

--
-- Fazendo dump de dados para tabela `forma_pagamento`
--

INSERT INTO `forma_pagamento` (`id`, `forma`) VALUES
(1, 'Dinheiro'),
(2, 'Cheque'),
(3, 'Boleto'),
(4, 'DOC'),
(5, 'TED'),
(6, 'DÃ©bito em Conta'),
(7, 'CartÃ£o de CrÃ©dito');

-- --------------------------------------------------------

--
-- Estrutura para tabela `funcionarios`
--

CREATE TABLE IF NOT EXISTS `funcionarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `nome_pai` varchar(50) NOT NULL,
  `nome_mae` varchar(50) NOT NULL,
  `dt_nasc` date NOT NULL,
  `cidade_nasc` varchar(50) NOT NULL,
  `uf_nasc` char(2) NOT NULL,
  `sexo` char(1) NOT NULL,
  `raca` tinyint(1) NOT NULL,
  `deficiente` tinyint(1) NOT NULL,
  `estado_civil` tinyint(1) NOT NULL,
  `instrucao` tinyint(2) NOT NULL,
  `rg` varchar(10) NOT NULL,
  `rg_emissor` varchar(15) NOT NULL,
  `rg_dt_emissao` date NOT NULL,
  `cpf` varchar(20) NOT NULL,
  `pis` varchar(20) NOT NULL,
  `pis_dt_inscricao` date NOT NULL,
  `carteira` varchar(15) NOT NULL,
  `carteira_dt_emissao` date NOT NULL,
  `funcao_id` int(11) NOT NULL,
  `logradouro` varchar(50) NOT NULL,
  `numero` varchar(10) NOT NULL,
  `bairro` varchar(30) NOT NULL,
  `cidade` varchar(30) NOT NULL,
  `uf` char(2) NOT NULL,
  `cep` varchar(15) NOT NULL,
  `complemento` varchar(50) NOT NULL,
  `referencia` varchar(50) NOT NULL,
  `tel01` varchar(20) NOT NULL,
  `tel02` varchar(20) NOT NULL,
  `email01` varchar(30) NOT NULL,
  `email02` varchar(30) NOT NULL,
  `dt_exame_admissional` date NOT NULL,
  `dt_admissao` date NOT NULL,
  `dt_demissao` date NOT NULL,
  `salario` decimal(15,2) NOT NULL,
  `tp_salario` tinyint(1) NOT NULL,
  `desconto_transporte` tinyint(1) NOT NULL,
  `primeiro_emprego_ano` tinyint(1) NOT NULL,
  `adicional_noturno` tinyint(1) NOT NULL,
  `sindicalizado` tinyint(1) NOT NULL,
  `sindicato` varchar(50) NOT NULL,
  `insalubridade` tinyint(1) NOT NULL,
  `optante_fgts` tinyint(1) NOT NULL,
  `cod_banco_fgts` varchar(5) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `observacao` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `funcionarios_faltas`
--

CREATE TABLE IF NOT EXISTS `funcionarios_faltas` (
  `id` int(11) NOT NULL,
  `funcionario_id` int(11) NOT NULL,
  `justificado` tinyint(1) NOT NULL,
  `dt_falta` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `func_afastamentos`
--

CREATE TABLE IF NOT EXISTS `func_afastamentos` (
  `id` int(11) NOT NULL,
  `funcionario_id` int(11) NOT NULL,
  `motivo` varchar(100) NOT NULL,
  `dt_ocorrencia` date NOT NULL,
  `dt_alta` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `func_alt_funcoes`
--

CREATE TABLE IF NOT EXISTS `func_alt_funcoes` (
  `id` int(11) NOT NULL,
  `funcionario_id` int(11) NOT NULL,
  `funcao_id` int(11) NOT NULL,
  `dt_alteracao` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `func_dependentes`
--

CREATE TABLE IF NOT EXISTS `func_dependentes` (
  `id` int(11) NOT NULL,
  `funcionario_id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `dt_nascimento` date NOT NULL,
  `dt_registro` date NOT NULL,
  `cartorio` varchar(100) NOT NULL,
  `sexo` char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `func_ferias`
--

CREATE TABLE IF NOT EXISTS `func_ferias` (
  `id` int(11) NOT NULL,
  `funcionario_id` int(11) NOT NULL,
  `dt_periodo_ini` date NOT NULL,
  `dt_periodo_fim` date NOT NULL,
  `dt_ferias_ini` date NOT NULL,
  `dt_ferias_fim` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `func_funcoes`
--

CREATE TABLE IF NOT EXISTS `func_funcoes` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `func_horas_extras`
--

CREATE TABLE IF NOT EXISTS `func_horas_extras` (
  `id` int(11) NOT NULL,
  `funcionario_id` int(11) NOT NULL,
  `dt_hora_extra` date NOT NULL,
  `qtd_hora_extra` int(11) NOT NULL,
  `percentual` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `func_salarios`
--

CREATE TABLE IF NOT EXISTS `func_salarios` (
  `id` int(11) NOT NULL,
  `funcionario_id` int(11) NOT NULL,
  `valor` decimal(10,0) NOT NULL,
  `dt_alteracao` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `func_sindicatos`
--

CREATE TABLE IF NOT EXISTS `func_sindicatos` (
  `id` int(11) NOT NULL,
  `funcionario_id` int(11) NOT NULL,
  `guia` varchar(50) NOT NULL,
  `dt_contribuicao` date NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `sindicato` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `honorarios`
--

CREATE TABLE IF NOT EXISTS `honorarios` (
  `id` int(11) NOT NULL,
  `contador_id` int(11) NOT NULL,
  `lancamento_id` int(11) NOT NULL,
  `nome_contabilidade` varchar(255) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `compensado` tinyint(1) NOT NULL,
  `dt_vencimento` date NOT NULL,
  `visualizado` tinyint(1) NOT NULL,
  `link` varchar(255) NOT NULL,
  `dt_cadastro` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `lancamentos`
--

CREATE TABLE IF NOT EXISTS `lancamentos` (
  `id` int(10) unsigned NOT NULL,
  `tipo` varchar(1) CHARACTER SET utf8 DEFAULT NULL,
  `descricao` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `lancamento_pai_id` int(10) unsigned DEFAULT NULL,
  `lancamento_recorrente_id` int(10) unsigned DEFAULT NULL,
  `parcela_numero` int(10) unsigned DEFAULT NULL,
  `qtd_parcelas` int(10) unsigned DEFAULT NULL,
  `favorecido_id` int(10) unsigned DEFAULT NULL,
  `forma_pgto_id` int(2) unsigned DEFAULT NULL,
  `conta_id` int(10) unsigned DEFAULT NULL,
  `conta_id_origem` int(10) unsigned DEFAULT NULL,
  `conta_id_destino` int(10) unsigned DEFAULT NULL,
  `documento_id` int(2) unsigned DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `valor_pago` decimal(10,2) DEFAULT NULL,
  `valor_multa` decimal(10,2) DEFAULT NULL,
  `valor_juros` decimal(10,2) DEFAULT NULL,
  `frequencia` int(3) unsigned DEFAULT NULL,
  `auto_lancamento` varchar(1) CHARACTER SET utf8 DEFAULT NULL,
  `observacao` text CHARACTER SET utf8,
  `dt_emissao` date DEFAULT NULL,
  `dt_vencimento` date DEFAULT NULL,
  `sab_dom` int(1) NOT NULL,
  `dt_venc_ref` date DEFAULT NULL,
  `dt_compensacao` date DEFAULT NULL,
  `compensado` int(1) unsigned DEFAULT NULL,
  `dt_competencia` date NOT NULL,
  `mei_outros` smallint(1) NOT NULL,
  `fit_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `lancamentos_cnlc`
--

CREATE TABLE IF NOT EXISTS `lancamentos_cnlc` (
  `id` int(11) NOT NULL,
  `conta_id` int(2) NOT NULL,
  `descricao` varchar(150) NOT NULL,
  `valor` decimal(20,2) NOT NULL,
  `data` date NOT NULL,
  `vencimento` date DEFAULT NULL,
  `pagador` varchar(40) DEFAULT NULL,
  `is_boleto` tinyint(1) DEFAULT '0',
  `fit_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `lancamentos_historico`
--

CREATE TABLE IF NOT EXISTS `lancamentos_historico` (
  `id` int(11) NOT NULL,
  `tipo` char(1) NOT NULL,
  `lancamento_id` int(11) NOT NULL,
  `favorecido_id` int(11) NOT NULL,
  `conta_financeira_id` int(11) NOT NULL,
  `conta_financeira_id_origem` int(11) NOT NULL,
  `conta_financeira_id_destino` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `descricao` varchar(200) NOT NULL,
  `favorecido` varchar(200) NOT NULL,
  `conta_financeira` varchar(200) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `dt_vencimento` date NOT NULL,
  `dt_compensacao` date NOT NULL,
  `compensado` tinyint(1) NOT NULL,
  `excluido` tinyint(1) NOT NULL DEFAULT '0',
  `dt_alteracao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `lancamentos_import`
--

CREATE TABLE IF NOT EXISTS `lancamentos_import` (
  `id` int(11) NOT NULL,
  `descricao` varchar(150) NOT NULL,
  `valor` decimal(20,2) NOT NULL,
  `competencia` date NOT NULL,
  `vencimento` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `lancamentos_plnj`
--

CREATE TABLE IF NOT EXISTS `lancamentos_plnj` (
  `id` int(11) NOT NULL,
  `tipo` char(1) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `dt_vencimento` date NOT NULL,
  `observacao` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `lancamentos_recorrentes`
--

CREATE TABLE IF NOT EXISTS `lancamentos_recorrentes` (
  `id` int(11) NOT NULL,
  `tipo` varchar(1) CHARACTER SET utf8 NOT NULL,
  `favorecido_id` int(11) NOT NULL,
  `forma_pgto_id` int(11) NOT NULL,
  `conta_id` int(11) NOT NULL,
  `documento_id` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `frequencia` int(11) NOT NULL,
  `auto_lancamento` varchar(1) CHARACTER SET utf8 NOT NULL,
  `observacao` text CHARACTER SET utf8 NOT NULL,
  `qtd_dias` smallint(6) NOT NULL,
  `dia_mes` int(1) NOT NULL DEFAULT '0',
  `dia_semana` int(1) NOT NULL DEFAULT '0',
  `dt_inicio` date NOT NULL,
  `dt_vencimento` date NOT NULL,
  `dt_competencia` date NOT NULL,
  `dt_comp_mes_dif` int(4) NOT NULL,
  `dt_emissao` date NOT NULL,
  `sab_dom` int(1) NOT NULL,
  `dt_prox_venc` date NOT NULL,
  `descricao` varchar(255) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `lnct_anexos`
--

CREATE TABLE IF NOT EXISTS `lnct_anexos` (
  `id` int(11) NOT NULL,
  `lancamento_id` int(11) NOT NULL,
  `nome_arquivo` varchar(100) NOT NULL,
  `nome_arquivo_org` varchar(100) NOT NULL,
  `tp_documento_id` tinyint(2) NOT NULL,
  `classificacao_id` tinyint(2) NOT NULL,
  `dt_cadastro` date NOT NULL,
  `dt_visualizacao` date DEFAULT NULL,
  `dt_competencia` date DEFAULT NULL,
  `visualizado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `orcamentos_plnj`
--

CREATE TABLE IF NOT EXISTS `orcamentos_plnj` (
  `id` int(11) NOT NULL,
  `descricao` char(50) NOT NULL,
  `dt_cadastro` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `orcamentos_plnj_vl`
--

CREATE TABLE IF NOT EXISTS `orcamentos_plnj_vl` (
  `id` int(11) NOT NULL,
  `orcamento_id` int(11) NOT NULL,
  `plano_contas_id` int(11) NOT NULL,
  `vl_unico` tinyint(1) NOT NULL,
  `jan` decimal(10,2) NOT NULL,
  `fev` decimal(10,2) NOT NULL,
  `mar` decimal(10,2) NOT NULL,
  `abr` decimal(10,2) NOT NULL,
  `mai` decimal(10,2) NOT NULL,
  `jun` decimal(10,2) NOT NULL,
  `jul` decimal(10,2) NOT NULL,
  `ago` decimal(10,2) NOT NULL,
  `sete` decimal(10,2) NOT NULL,
  `outu` decimal(10,2) NOT NULL,
  `nov` decimal(10,2) NOT NULL,
  `dez` decimal(10,2) NOT NULL,
  `ano` smallint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `plano_contas`
--

CREATE TABLE IF NOT EXISTS `plano_contas` (
  `id` int(11) NOT NULL,
  `cod_conta` varchar(255) CHARACTER SET utf8 NOT NULL,
  `cod_ref` varchar(10) CHARACTER SET utf8 NOT NULL,
  `nome` varchar(100) CHARACTER SET utf8 NOT NULL,
  `hierarquia` text CHARACTER SET utf8 NOT NULL,
  `conta_pai_id` int(11) NOT NULL,
  `tp_conta` int(11) NOT NULL,
  `descricao` text CHARACTER SET utf8 NOT NULL,
  `nivel` int(11) NOT NULL,
  `posicao` int(11) NOT NULL,
  `situacao` int(11) NOT NULL,
  `clfc_fc` tinyint(2) NOT NULL,
  `clfc_dre` tinyint(2) NOT NULL,
  `dt_cadastro` date NOT NULL,
  `dedutivel` tinyint(1) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

INSERT INTO `plano_contas` (`id`, `cod_conta`, `cod_ref`, `nome`, `hierarquia`, `conta_pai_id`, `tp_conta`, `descricao`, `nivel`, `posicao`, `situacao`, `dt_cadastro`, `dedutivel`) VALUES
(0, '0', '', 'NÃ£o alocado', '0', 0, 1, '', 1, 0, 0, '0000-00-00', 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `provisao`
--

CREATE TABLE IF NOT EXISTS `provisao` (
  `id` int(11) NOT NULL,
  `tipo` tinyint(2) NOT NULL,
  `vl_unico` tinyint(1) NOT NULL,
  `jan` decimal(10,2) NOT NULL,
  `fev` decimal(10,2) NOT NULL,
  `mar` decimal(10,2) NOT NULL,
  `abr` decimal(10,2) NOT NULL,
  `mai` decimal(10,2) NOT NULL,
  `jun` decimal(10,2) NOT NULL,
  `jul` decimal(10,2) NOT NULL,
  `ago` decimal(10,2) NOT NULL,
  `sete` decimal(10,2) NOT NULL,
  `outu` decimal(10,2) NOT NULL,
  `nov` decimal(10,2) NOT NULL,
  `dez` decimal(10,2) NOT NULL,
  `ano` smallint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `recibos`
--

CREATE TABLE IF NOT EXISTS `recibos` (
  `id` int(11) NOT NULL,
  `tp` varchar(1) NOT NULL,
  `id_favorecido` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `inscricao` varchar(4) NOT NULL,
  `cpf_cnpj` varchar(18) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `descricao` varchar(50) NOT NULL,
  `dt_recibo` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `remessa_contabil`
--

CREATE TABLE IF NOT EXISTS `remessa_contabil` (
  `id` int(11) NOT NULL,
  `conta_id` int(11) NOT NULL,
  `vl_rcbt` decimal(10,2) NOT NULL,
  `qtd_rcbt` int(11) NOT NULL,
  `vl_pgto` decimal(10,2) NOT NULL,
  `qtd_pgto` int(11) NOT NULL,
  `saldo` decimal(10,2) NOT NULL,
  `operacao` tinyint(1) NOT NULL,
  `mes` tinyint(1) NOT NULL,
  `ano` smallint(4) NOT NULL,
  `dt_cadastro` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura para tabela `uf`
--

CREATE TABLE IF NOT EXISTS `uf` (
  `id` int(11) NOT NULL,
  `uf` varchar(2) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;

--
-- Fazendo dump de dados para tabela `uf`
--

INSERT INTO `uf` (`id`, `uf`) VALUES
(1, 'AC'),
(2, 'AL'),
(3, 'AM'),
(4, 'AP'),
(5, 'BA'),
(6, 'CE'),
(7, 'DF'),
(8, 'ES'),
(9, 'GO'),
(10, 'MA'),
(11, 'MG'),
(12, 'MS'),
(13, 'MT'),
(14, 'PA'),
(15, 'PB'),
(16, 'PE'),
(17, 'PI'),
(18, 'PR'),
(19, 'RJ'),
(20, 'RN'),
(21, 'RO'),
(22, 'RR'),
(23, 'RS'),
(24, 'SC'),
(25, 'SE'),
(26, 'SP'),
(27, 'TO');

--
-- Índices de tabelas apagadas
--

--
-- Índices de tabela `arq_classificacao`
--
ALTER TABLE `arq_classificacao`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `arq_tp_documento`
--
ALTER TABLE `arq_tp_documento`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `bancos`
--
ALTER TABLE `bancos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `boletos`
--
ALTER TABLE `boletos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `centro_resp`
--
ALTER TABLE `centro_resp`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `conexao`
--
ALTER TABLE `conexao`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `contas`
--
ALTER TABLE `contas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `ctr_plc_lancamentos`
--
ALTER TABLE `ctr_plc_lancamentos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `ctr_plc_lancamentos_plnj`
--
ALTER TABLE `ctr_plc_lancamentos_plnj`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `ctr_plc_lancamentos_rcr`
--
ALTER TABLE `ctr_plc_lancamentos_rcr`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `documentos`
--
ALTER TABLE `documentos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `favorecidos`
--
ALTER TABLE `favorecidos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `favorecidos_tipo`
--
ALTER TABLE `favorecidos_tipo`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `forma_pagamento`
--
ALTER TABLE `forma_pagamento`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `funcionarios`
--
ALTER TABLE `funcionarios`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `funcionarios_faltas`
--
ALTER TABLE `funcionarios_faltas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `func_afastamentos`
--
ALTER TABLE `func_afastamentos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `func_alt_funcoes`
--
ALTER TABLE `func_alt_funcoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `func_dependentes`
--
ALTER TABLE `func_dependentes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `func_ferias`
--
ALTER TABLE `func_ferias`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `func_funcoes`
--
ALTER TABLE `func_funcoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `func_horas_extras`
--
ALTER TABLE `func_horas_extras`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `func_salarios`
--
ALTER TABLE `func_salarios`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `func_sindicatos`
--
ALTER TABLE `func_sindicatos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `lancamentos`
--
ALTER TABLE `lancamentos`
  ADD PRIMARY KEY (`id`), ADD KEY `idx_1` (`compensado`,`conta_id`,`dt_compensacao`);

--
-- Índices de tabela `lancamentos_cnlc`
--
ALTER TABLE `lancamentos_cnlc`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `lancamentos_import`
--
ALTER TABLE `lancamentos_import`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `lancamentos_plnj`
--
ALTER TABLE `lancamentos_plnj`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `lancamentos_recorrentes`
--
ALTER TABLE `lancamentos_recorrentes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `lnct_anexos`
--
ALTER TABLE `lnct_anexos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `orcamentos_plnj`
--
ALTER TABLE `orcamentos_plnj`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `orcamentos_plnj_vl`
--
ALTER TABLE `orcamentos_plnj_vl`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `plano_contas`
--
ALTER TABLE `plano_contas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `provisao`
--
ALTER TABLE `provisao`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `recibos`
--
ALTER TABLE `recibos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `uf`
--
ALTER TABLE `uf`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `boletos_remessa`
--
ALTER TABLE `boletos_remessa`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `honorarios`
--
ALTER TABLE `honorarios`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `lancamentos_historico`
--
ALTER TABLE `lancamentos_historico`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `remessa_contabil`
--
ALTER TABLE `remessa_contabil`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de tabelas apagadas
--

--
-- AUTO_INCREMENT de tabela `arq_classificacao`
--
ALTER TABLE `arq_classificacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT de tabela `arq_tp_documento`
--
ALTER TABLE `arq_tp_documento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de tabela `bancos`
--
ALTER TABLE `bancos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=168;
--
-- AUTO_INCREMENT de tabela `boletos`
--
ALTER TABLE `boletos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `centro_resp`
--
ALTER TABLE `centro_resp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT de tabela `conexao`
--
ALTER TABLE `conexao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `contas`
--
ALTER TABLE `contas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de tabela `ctr_plc_lancamentos`
--
ALTER TABLE `ctr_plc_lancamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `ctr_plc_lancamentos_plnj`
--
ALTER TABLE `ctr_plc_lancamentos_plnj`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `ctr_plc_lancamentos_rcr`
--
ALTER TABLE `ctr_plc_lancamentos_rcr`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `documentos`
--
ALTER TABLE `documentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT de tabela `favorecidos`
--
ALTER TABLE `favorecidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `favorecidos_tipo`
--
ALTER TABLE `favorecidos_tipo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de tabela `forma_pagamento`
--
ALTER TABLE `forma_pagamento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT de tabela `funcionarios`
--
ALTER TABLE `funcionarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `funcionarios_faltas`
--
ALTER TABLE `funcionarios_faltas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `func_afastamentos`
--
ALTER TABLE `func_afastamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `func_alt_funcoes`
--
ALTER TABLE `func_alt_funcoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `func_dependentes`
--
ALTER TABLE `func_dependentes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `func_ferias`
--
ALTER TABLE `func_ferias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `func_funcoes`
--
ALTER TABLE `func_funcoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `func_horas_extras`
--
ALTER TABLE `func_horas_extras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `func_salarios`
--
ALTER TABLE `func_salarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `func_sindicatos`
--
ALTER TABLE `func_sindicatos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `lancamentos`
--
ALTER TABLE `lancamentos`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `lancamentos_cnlc`
--
ALTER TABLE `lancamentos_cnlc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `lancamentos_import`
--
ALTER TABLE `lancamentos_import`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `lancamentos_plnj`
--
ALTER TABLE `lancamentos_plnj`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `lancamentos_recorrentes`
--
ALTER TABLE `lancamentos_recorrentes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `lnct_anexos`
--
ALTER TABLE `lnct_anexos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `orcamentos_plnj`
--
ALTER TABLE `orcamentos_plnj`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `orcamentos_plnj_vl`
--
ALTER TABLE `orcamentos_plnj_vl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `plano_contas`
--
ALTER TABLE `plano_contas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT de tabela `provisao`
--
ALTER TABLE `provisao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `recibos`
--
ALTER TABLE `recibos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de tabela `uf`
--
ALTER TABLE `uf`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `boletos_remessa`
--
ALTER TABLE `boletos_remessa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `honorarios`
--
ALTER TABLE `honorarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `lancamentos_historico`
--
ALTER TABLE `lancamentos_historico`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `remessa_contabil`
--
ALTER TABLE `remessa_contabil`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
