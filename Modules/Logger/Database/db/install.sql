SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
-- -------------------------------------------
SET AUTOCOMMIT=0;
START TRANSACTION;
SET SQL_QUOTE_SHOW_CREATE = 1;
-- -------------------------------------------

-- -------------------------------------------

-- START BACKUP

-- -------------------------------------------

-- -------------------------------------------

-- TABLE `logs`

-- -------------------------------------------
DROP TABLE IF EXISTS `logs`;
CREATE TABLE IF NOT EXISTS `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message` longtext,
  `context` longtext,
  `level` varchar(255),
  `level_name` varchar(255),
  `channel` varchar(255),
  `record_datetime` varchar(255),
  `extra` longtext,
  `formatted` longtext,
  `remote_addr` varchar(255) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `state_id` int(11) NOT NULL DEFAULT 1,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci  NULL,
  `type_id` int(11)  NULL,
  `referer_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_ip` varchar(255) COLLATE utf8mb4_unicode_ci  NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


COMMIT;
-- -------------------------------------------
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
 -- -------AutobackUpStart------ -- -------------------------------------------

-- -------------------------------------------

-- END BACKUP

-- -------------------------------------------
