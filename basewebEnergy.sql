-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- Máquina: 127.0.0.1
-- Data de Criação: 04-Nov-2013 às 00:12
-- Versão do servidor: 5.6.11
-- versão do PHP: 5.5.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de Dados: `webenergy`
--
CREATE DATABASE IF NOT EXISTS `webenergy` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `webenergy`;

-- --------------------------------------------------------

--
-- Estrutura da tabela `user`
--

CREATE TABLE `user` (
  `userId` int(11) NOT NULL AUTO_INCREMENT,
  `userName` varchar(50) COLLATE utf8_bin NOT NULL,
  `userLogin` varchar(30) COLLATE utf8_bin NOT NULL,
  `userPass` varchar(45) COLLATE utf8_bin NOT NULL,
  `userLastName` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `userMail` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `userPhoto` varchar(30) COLLATE utf8_bin DEFAULT NULL,
  `userArduinoIp` varchar(15) COLLATE utf8_bin DEFAULT NULL,
  `userArduinoPort` varchar(6) COLLATE utf8_bin DEFAULT NULL,
  `userSerialXBee` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`userId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

--
-- Extraindo dados da tabela `user`
--

INSERT INTO `user` (`userId`, `userName`, `userLogin`, `userPass`, `userLastName`, `userMail`, `userPhoto`, `userArduinoIp`, `userArduinoPort`, `userSerialXBee`) VALUES
(1, 'KESSILER', 'kessiler', 'GNZz/T8l3j3Vgg/IRPf0W/ErJSU19dC6+7zGdlkFanA=', 'teste', 'kessiler@gmail.com', '', '201.80.135.177', '8081', 'teste');

-- --------------------------------------------------------

--
-- Estrutura da tabela `userdata`
--

CREATE TABLE `userdata` (
  `userId` int(11) NOT NULL,
  `userPotencia` float NOT NULL,
  `userCorrente` float NOT NULL,
  `userDateInfo` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Estrutura da tabela `userevent`
--

-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- Máquina: 127.0.0.1
-- Data de Criação: 04-Nov-2013 às 00:39
-- Versão do servidor: 5.6.11
-- versão do PHP: 5.5.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de Dados: `webenergy`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `userevent`
--

CREATE TABLE IF NOT EXISTS `userevent` (
  `idEvent` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `dispEvent` varchar(30) CHARACTER SET latin1 NOT NULL,
  `descriptionEvent` text CHARACTER SET latin1 NOT NULL,
  `actionExecute` int(11) NOT NULL,
  `usesAverageConsumption` int(11) NOT NULL,
  `averageConsumption` float DEFAULT NULL,
  `usesCheckPresence` int(11) NOT NULL,
  `timeExecution` time NOT NULL,
  `dateCreate` datetime NOT NULL,
  PRIMARY KEY (`idEvent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
