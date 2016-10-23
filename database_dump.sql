-- --------------------------------------------------------
-- Servidor:                     127.0.0.1
-- Versão do servidor:           10.1.9-MariaDB - mariadb.org binary distribution
-- OS do Servidor:               Win32
-- HeidiSQL Versão:              9.3.0.4984
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Copiando estrutura para tabela webhook.tb_destination
CREATE TABLE IF NOT EXISTS `tb_destination` (
  `id_destination` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  `dt_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_destination`),
  UNIQUE KEY `i_u_destination_url` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportação de dados foi desmarcado.


-- Copiando estrutura para tabela webhook.tb_destination_post
CREATE TABLE IF NOT EXISTS `tb_destination_post` (
  `id_destination_post` int(11) NOT NULL AUTO_INCREMENT,
  `id_destination` int(11) NOT NULL DEFAULT '0',
  `msg_body` text NOT NULL,
  `content_type` varchar(50) NOT NULL,
  `dt_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_destination_post`),
  KEY `fk_destination_post_destination` (`id_destination`),
  CONSTRAINT `fk_destination_post_destination` FOREIGN KEY (`id_destination`) REFERENCES `tb_destination` (`id_destination`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportação de dados foi desmarcado.


-- Copiando estrutura para tabela webhook.tb_destination_post_queue
CREATE TABLE IF NOT EXISTS `tb_destination_post_queue` (
  `id_destination_post_queue` int(11) NOT NULL AUTO_INCREMENT,
  `id_destination_post` int(11) NOT NULL,
  `attempts` tinyint(4) NOT NULL DEFAULT '0',
  `status` enum('W','P') NOT NULL DEFAULT 'W',
  `dt_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dt_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_destination_post_queue`),
  KEY `fk_destination_post_queue_destination_post` (`id_destination_post`),
  CONSTRAINT `fk_destination_post_queue_destination_post` FOREIGN KEY (`id_destination_post`) REFERENCES `tb_destination_post` (`id_destination_post`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportação de dados foi desmarcado.


-- Copiando estrutura para tabela webhook.tb_destination_post_queue_log
CREATE TABLE IF NOT EXISTS `tb_destination_post_queue_log` (
  `id_destination_post_queue_log` int(11) NOT NULL AUTO_INCREMENT,
  `id_destination_post` int(11) NOT NULL,
  `attempt` int(11) NOT NULL,
  `http_return` int(11) DEFAULT NULL,
  `dt_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dt_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_destination_post_queue_log`),
  UNIQUE KEY `i_u_destination_post_queue` (`id_destination_post`,`attempt`),
  CONSTRAINT `fk_destination_post_queue_log_destination_post` FOREIGN KEY (`id_destination_post`) REFERENCES `tb_destination_post` (`id_destination_post`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportação de dados foi desmarcado.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
