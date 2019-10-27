SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `ip_bans` (
  `ip` varchar(42) NOT NULL COMMENT 'IP',
  `err_count` int(11) DEFAULT '0' COMMENT 'Количество ошибок (неудачных попыток входа, например)',
  `ban_time` int(11) DEFAULT NULL COMMENT 'Время, когда произошла блокировка'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Список забаненных IP адресов';

CREATE TABLE `users` (
  `id` int(11) NOT NULL COMMENT 'Идентификатор пользоателя',
  `name` varchar(40) DEFAULT NULL COMMENT 'Имя',
  `surname` varchar(40) DEFAULT NULL COMMENT 'Фамилия',
  `email` varchar(100) NOT NULL COMMENT 'Email',
  `password` varchar(255) NOT NULL COMMENT 'Хеш пароля',
  `avatar` text COMMENT 'URL аватарочки',
  `reg_time` int(11) DEFAULT NULL COMMENT 'Время регистрации',
  `reg_ip` varchar(42) DEFAULT NULL COMMENT 'IP регистрации',
  `last_time` int(11) DEFAULT NULL COMMENT 'Время последнего входа',
  `last_ip` varchar(42) DEFAULT NULL COMMENT 'IP последнего входа',
  `info` text CHARACTER SET utf8mb4 COMMENT 'Информация о пользователе'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Пользователи';


ALTER TABLE `ip_bans`
  ADD UNIQUE KEY `ip` (`ip`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `email_2` (`email`);


ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор пользоателя';
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
