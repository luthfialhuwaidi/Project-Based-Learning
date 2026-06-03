-- ============================================================
-- Sistem MBG (Makanan Bergizi Gratis) - Database SQL Dump
-- Versi: 1.0 | Laravel 12 | PHP 8.3
-- Import ke phpMyAdmin: buat database "sistem_mbg" dahulu
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+07:00";

-- Buat dan gunakan database
CREATE DATABASE IF NOT EXISTS `sistem_mbg`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `sistem_mbg`;

-- ============================================================
-- Tabel: users
-- ============================================================
CREATE TABLE `users` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('petugas','guru','orangtua') NOT NULL DEFAULT 'orangtua',
    `phone` VARCHAR(20) NULL,
    `avatar` VARCHAR(255) NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `remember_token` VARCHAR(100) NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`),
    KEY `users_role_index` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Tabel: password_reset_tokens
-- ============================================================
CREATE TABLE `password_reset_tokens` (
    `email` VARCHAR(255) NOT NULL,
    `token` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Tabel: sessions
-- ============================================================
CREATE TABLE `sessions` (
    `id` VARCHAR(255) NOT NULL,
    `user_id` BIGINT UNSIGNED NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `payload` LONGTEXT NOT NULL,
    `last_activity` INT NOT NULL,
    PRIMARY KEY (`id`),
    KEY `sessions_user_id_index` (`user_id`),
    KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Tabel: schools
-- ============================================================
CREATE TABLE `schools` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `address` VARCHAR(255) NOT NULL,
    `kelurahan` VARCHAR(255) NULL,
    `kecamatan` VARCHAR(255) NULL,
    `latitude` DECIMAL(10,7) NULL,
    `longitude` DECIMAL(10,7) NULL,
    `principal_name` VARCHAR(255) NULL,
    `phone` VARCHAR(20) NULL,
    `teacher_id` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `schools_teacher_id_foreign` (`teacher_id`),
    CONSTRAINT `schools_teacher_id_foreign`
        FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Tabel: students
-- ============================================================
CREATE TABLE `students` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `nis` VARCHAR(255) NULL,
    `parent_id` BIGINT UNSIGNED NOT NULL,
    `school_id` BIGINT UNSIGNED NOT NULL,
    `class` VARCHAR(50) NULL,
    `gender` ENUM('L','P') NULL,
    `birth_date` DATE NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `students_nis_unique` (`nis`),
    KEY `students_parent_id_foreign` (`parent_id`),
    KEY `students_school_id_foreign` (`school_id`),
    CONSTRAINT `students_parent_id_foreign`
        FOREIGN KEY (`parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `students_school_id_foreign`
        FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Tabel: deliveries
-- ============================================================
CREATE TABLE `deliveries` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `kode_pengiriman` VARCHAR(255) NOT NULL,
    `courier_id` BIGINT UNSIGNED NOT NULL,
    `school_id` BIGINT UNSIGNED NOT NULL,
    `status` ENUM('dimasak','dikemas','dalam_perjalanan','sudah_sampai','diterima_guru','diterima_murid','selesai') NOT NULL DEFAULT 'dimasak',
    `total_portions` INT NOT NULL DEFAULT 0,
    `notes` TEXT NULL,
    `started_at` TIMESTAMP NULL DEFAULT NULL,
    `arrived_at` TIMESTAMP NULL DEFAULT NULL,
    `completed_at` TIMESTAMP NULL DEFAULT NULL,
    `delivery_date` DATE NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `deliveries_kode_pengiriman_unique` (`kode_pengiriman`),
    KEY `deliveries_courier_id_foreign` (`courier_id`),
    KEY `deliveries_school_id_foreign` (`school_id`),
    KEY `deliveries_delivery_date_index` (`delivery_date`),
    KEY `deliveries_status_index` (`status`),
    CONSTRAINT `deliveries_courier_id_foreign`
        FOREIGN KEY (`courier_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `deliveries_school_id_foreign`
        FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Tabel: delivery_tracking
-- ============================================================
CREATE TABLE `delivery_tracking` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `delivery_id` BIGINT UNSIGNED NOT NULL,
    `latitude` DECIMAL(10,7) NOT NULL,
    `longitude` DECIMAL(10,7) NOT NULL,
    `accuracy` FLOAT NULL,
    `speed` FLOAT NULL,
    `address` VARCHAR(500) NULL,
    `recorded_at` TIMESTAMP NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `delivery_tracking_delivery_id_recorded_at_index` (`delivery_id`, `recorded_at`),
    CONSTRAINT `delivery_tracking_delivery_id_foreign`
        FOREIGN KEY (`delivery_id`) REFERENCES `deliveries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Tabel: confirmations
-- ============================================================
CREATE TABLE `confirmations` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `delivery_id` BIGINT UNSIGNED NOT NULL,
    `student_id` BIGINT UNSIGNED NOT NULL,
    `teacher_confirmed` TINYINT(1) NOT NULL DEFAULT 0,
    `teacher_confirmed_at` TIMESTAMP NULL DEFAULT NULL,
    `teacher_id` BIGINT UNSIGNED NULL,
    `parent_confirmed` TINYINT(1) NOT NULL DEFAULT 0,
    `parent_confirmed_at` TIMESTAMP NULL DEFAULT NULL,
    `eaten_status` TINYINT(1) NOT NULL DEFAULT 0,
    `eaten_at` TIMESTAMP NULL DEFAULT NULL,
    `notes` TEXT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `confirmations_delivery_id_student_id_unique` (`delivery_id`, `student_id`),
    KEY `confirmations_student_id_foreign` (`student_id`),
    KEY `confirmations_teacher_id_foreign` (`teacher_id`),
    CONSTRAINT `confirmations_delivery_id_foreign`
        FOREIGN KEY (`delivery_id`) REFERENCES `deliveries` (`id`) ON DELETE CASCADE,
    CONSTRAINT `confirmations_student_id_foreign`
        FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
    CONSTRAINT `confirmations_teacher_id_foreign`
        FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Tabel: activity_logs
-- ============================================================
CREATE TABLE `activity_logs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NULL,
    `action` VARCHAR(255) NOT NULL,
    `model_type` VARCHAR(255) NULL,
    `model_id` BIGINT UNSIGNED NULL,
    `properties` JSON NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` VARCHAR(500) NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `activity_logs_user_id_index` (`user_id`),
    KEY `activity_logs_model_type_model_id_index` (`model_type`, `model_id`),
    CONSTRAINT `activity_logs_user_id_foreign`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Tabel: notifications
-- ============================================================
CREATE TABLE `notifications` (
    `id` CHAR(36) NOT NULL,
    `type` VARCHAR(255) NOT NULL,
    `notifiable_type` VARCHAR(255) NOT NULL,
    `notifiable_id` BIGINT UNSIGNED NOT NULL,
    `data` TEXT NOT NULL,
    `read_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`, `notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Tabel: cache
-- ============================================================
CREATE TABLE `cache` (
    `key` VARCHAR(255) NOT NULL,
    `value` MEDIUMTEXT NOT NULL,
    `expiration` INT NOT NULL,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache_locks` (
    `key` VARCHAR(255) NOT NULL,
    `owner` VARCHAR(255) NOT NULL,
    `expiration` INT NOT NULL,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Tabel: jobs (untuk queue)
-- ============================================================
CREATE TABLE `jobs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `queue` VARCHAR(255) NOT NULL,
    `payload` LONGTEXT NOT NULL,
    `attempts` TINYINT UNSIGNED NOT NULL,
    `reserved_at` INT UNSIGNED NULL,
    `available_at` INT UNSIGNED NOT NULL,
    `created_at` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `job_batches` (
    `id` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `total_jobs` INT NOT NULL,
    `pending_jobs` INT NOT NULL,
    `failed_jobs` INT NOT NULL,
    `failed_job_ids` LONGTEXT NOT NULL,
    `options` MEDIUMTEXT NULL,
    `cancelled_at` INT NULL,
    `created_at` INT NOT NULL,
    `finished_at` INT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `failed_jobs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `uuid` VARCHAR(255) NOT NULL,
    `connection` TEXT NOT NULL,
    `queue` TEXT NOT NULL,
    `payload` LONGTEXT NOT NULL,
    `exception` LONGTEXT NOT NULL,
    `failed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Tabel: personal_access_tokens (Sanctum)
-- ============================================================
CREATE TABLE `personal_access_tokens` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `tokenable_type` VARCHAR(255) NOT NULL,
    `tokenable_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `token` VARCHAR(64) NOT NULL,
    `abilities` TEXT NULL,
    `last_used_at` TIMESTAMP NULL DEFAULT NULL,
    `expires_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
    KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`, `tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DATA SEEDER
-- ============================================================

-- Users (password: "password" - bcrypt hash)
INSERT INTO `users` (`name`, `email`, `password`, `role`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES
('Ahmad Kurir', 'petugas@mbg.test', '$2y$12$VNbVblxIfbqiPpPKh9wLduIFjNAhgbLX2q6I9l8hAm0S5VJE6Yzey', 'petugas', '081234567890', 1, NOW(), NOW()),
('Budi Santoso', 'petugas2@mbg.test', '$2y$12$VNbVblxIfbqiPpPKh9wLduIFjNAhgbLX2q6I9l8hAm0S5VJE6Yzey', 'petugas', '082345678901', 1, NOW(), NOW()),
('Ibu Siti Rahayu', 'guru@mbg.test', '$2y$12$VNbVblxIfbqiPpPKh9wLduIFjNAhgbLX2q6I9l8hAm0S5VJE6Yzey', 'guru', '083456789012', 1, NOW(), NOW()),
('Pak Joko Widodo', 'guru2@mbg.test', '$2y$12$VNbVblxIfbqiPpPKh9wLduIFjNAhgbLX2q6I9l8hAm0S5VJE6Yzey', 'guru', '084567890123', 1, NOW(), NOW()),
('Pak Bambang', 'orangtua@mbg.test', '$2y$12$VNbVblxIfbqiPpPKh9wLduIFjNAhgbLX2q6I9l8hAm0S5VJE6Yzey', 'orangtua', '085678901234', 1, NOW(), NOW()),
('Ibu Dewi', 'orangtua2@mbg.test', '$2y$12$VNbVblxIfbqiPpPKh9wLduIFjNAhgbLX2q6I9l8hAm0S5VJE6Yzey', 'orangtua', '086789012345', 1, NOW(), NOW()),
('Pak Hendra', 'orangtua3@mbg.test', '$2y$12$VNbVblxIfbqiPpPKh9wLduIFjNAhgbLX2q6I9l8hAm0S5VJE6Yzey', 'orangtua', '087890123456', 1, NOW(), NOW());

-- Schools
INSERT INTO `schools` (`name`, `address`, `kelurahan`, `kecamatan`, `latitude`, `longitude`, `principal_name`, `phone`, `teacher_id`, `created_at`, `updated_at`) VALUES
('SD Negeri 001 Sukajaya', 'Jl. Pendidikan No. 1, Sukajaya', 'Sukajaya', 'Pekanbaru Kota', 0.5332000, 101.4474000, 'Drs. Suparman', '0761-12345', 3, NOW(), NOW()),
('SD Negeri 002 Harapan', 'Jl. Harapan Baru No. 5, Tenayan Raya', 'Sail', 'Tenayan Raya', 0.5095000, 101.4781000, 'Hj. Marwah, S.Pd', '0761-67890', 4, NOW(), NOW());

-- Students
INSERT INTO `students` (`name`, `nis`, `parent_id`, `school_id`, `class`, `gender`, `is_active`, `created_at`, `updated_at`) VALUES
('Andi Putra Bambang', '2024001', 5, 1, '3A', 'L', 1, NOW(), NOW()),
('Putri Dewi', '2024002', 6, 1, '4B', 'P', 1, NOW(), NOW()),
('Rizki Hendra', '2024003', 7, 2, '2A', 'L', 1, NOW(), NOW()),
('Sari Wulandari', '2024004', 5, 1, '5C', 'P', 1, NOW(), NOW());

-- Sample delivery (kemarin - selesai)
INSERT INTO `deliveries` (`kode_pengiriman`, `courier_id`, `school_id`, `status`, `total_portions`, `notes`, `started_at`, `arrived_at`, `completed_at`, `delivery_date`, `created_at`, `updated_at`) VALUES
('MBG20240101001', 1, 1, 'selesai', 30, 'Semua berjalan lancar', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 23 HOUR), DATE_SUB(NOW(), INTERVAL 22 HOUR), DATE(DATE_SUB(NOW(), INTERVAL 1 DAY)), DATE_SUB(NOW(), INTERVAL 1 DAY), NOW()),
('MBG20240101002', 2, 2, 'selesai', 25, 'Menu: Nasi + Ayam + Sayur', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 23 HOUR), DATE_SUB(NOW(), INTERVAL 22 HOUR), DATE(DATE_SUB(NOW(), INTERVAL 1 DAY)), DATE_SUB(NOW(), INTERVAL 1 DAY), NOW());

-- Sample tracking data (untuk delivery pertama)
INSERT INTO `delivery_tracking` (`delivery_id`, `latitude`, `longitude`, `accuracy`, `recorded_at`, `created_at`, `updated_at`) VALUES
(1, 0.5280000, 101.4390000, 5.0, DATE_SUB(NOW(), INTERVAL '23:30' HOUR_MINUTE), DATE_SUB(NOW(), INTERVAL '23:30' HOUR_MINUTE), NOW()),
(1, 0.5295000, 101.4410000, 4.5, DATE_SUB(NOW(), INTERVAL '23:25' HOUR_MINUTE), DATE_SUB(NOW(), INTERVAL '23:25' HOUR_MINUTE), NOW()),
(1, 0.5310000, 101.4435000, 4.0, DATE_SUB(NOW(), INTERVAL '23:20' HOUR_MINUTE), DATE_SUB(NOW(), INTERVAL '23:20' HOUR_MINUTE), NOW()),
(1, 0.5325000, 101.4458000, 3.5, DATE_SUB(NOW(), INTERVAL '23:10' HOUR_MINUTE), DATE_SUB(NOW(), INTERVAL '23:10' HOUR_MINUTE), NOW()),
(1, 0.5332000, 101.4474000, 3.0, DATE_SUB(NOW(), INTERVAL 23 HOUR), DATE_SUB(NOW(), INTERVAL 23 HOUR), NOW());

-- Sample confirmations
INSERT INTO `confirmations` (`delivery_id`, `student_id`, `teacher_confirmed`, `teacher_confirmed_at`, `teacher_id`, `parent_confirmed`, `parent_confirmed_at`, `eaten_status`, `eaten_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, DATE_SUB(NOW(), INTERVAL '22:30' HOUR_MINUTE), 3, 1, DATE_SUB(NOW(), INTERVAL 22 HOUR), 1, DATE_SUB(NOW(), INTERVAL 22 HOUR), DATE_SUB(NOW(), INTERVAL 1 DAY), NOW()),
(1, 2, 1, DATE_SUB(NOW(), INTERVAL '22:30' HOUR_MINUTE), 3, 1, DATE_SUB(NOW(), INTERVAL 21 HOUR), 1, DATE_SUB(NOW(), INTERVAL 21 HOUR), DATE_SUB(NOW(), INTERVAL 1 DAY), NOW()),
(1, 4, 1, DATE_SUB(NOW(), INTERVAL '22:30' HOUR_MINUTE), 3, 0, NULL, 0, NULL, DATE_SUB(NOW(), INTERVAL 1 DAY), NOW()),
(2, 3, 1, DATE_SUB(NOW(), INTERVAL '22:30' HOUR_MINUTE), 4, 1, DATE_SUB(NOW(), INTERVAL 22 HOUR), 1, DATE_SUB(NOW(), INTERVAL 22 HOUR), DATE_SUB(NOW(), INTERVAL 1 DAY), NOW());

-- ============================================================
-- CATATAN AKUN DEMO
-- ============================================================
-- Role: Petugas/Kurir  | Email: petugas@mbg.test   | Pass: password
-- Role: Petugas/Kurir  | Email: petugas2@mbg.test  | Pass: password
-- Role: Guru           | Email: guru@mbg.test       | Pass: password
-- Role: Guru           | Email: guru2@mbg.test      | Pass: password
-- Role: Orang Tua      | Email: orangtua@mbg.test   | Pass: password
-- Role: Orang Tua      | Email: orangtua2@mbg.test  | Pass: password
-- Role: Orang Tua      | Email: orangtua3@mbg.test  | Pass: password
-- ============================================================
