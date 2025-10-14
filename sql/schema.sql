-- AnomFIN Call-Survey ULTRALIGHT schema (utf8mb4)
CREATE DATABASE IF NOT EXISTS `anomfin_call_survey`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `anomfin_call_survey`;

CREATE TABLE IF NOT EXISTS `call_sessions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `call_sid` VARCHAR(64) NOT NULL,
  `contact_number` VARCHAR(32) NOT NULL,
  `agent_pstn` VARCHAR(32) DEFAULT NULL,
  `dtmf_selection` CHAR(1) DEFAULT NULL,
  `nps_score` TINYINT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_call_sid` (`call_sid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `message_events` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `message_sid` VARCHAR(64) NOT NULL,
  `channel` ENUM('sms','whatsapp') NOT NULL,
  `status` VARCHAR(32) NOT NULL,
  `payload` JSON DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_message_sid` (`message_sid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
