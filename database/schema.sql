-- =========================================================
-- Crumb & Cream — Database Schema
-- Import this in phpMyAdmin or HeidiSQL (both included with Laragon)
-- =========================================================

CREATE DATABASE IF NOT EXISTS `crumb_and_cream`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `crumb_and_cream`;

CREATE TABLE IF NOT EXISTS `orders` (
  `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `customer_name`  VARCHAR(120)      NOT NULL,
  `contact_info`   VARCHAR(150)      NOT NULL COMMENT 'Phone number or email',
  `size`           VARCHAR(50)       NOT NULL,
  `quantity`       SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  `message`        TEXT              NULL,
  `status`         ENUM('new', 'contacted', 'completed', 'cancelled') NOT NULL DEFAULT 'new',
  `created_at`     TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
