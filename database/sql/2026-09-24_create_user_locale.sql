-- 2026-09-24  Pilihan bahasa tampilan per user (MySQL demo_twp).
--
-- Satu baris per email login (all_login.email): satu orang yang punya akun admin dan
-- tenant memakai pilihan yang sama. User yang belum punya baris = English (default),
-- sampai dia mengganti bahasa sendiri lewat menu Language di header.
-- Dipakai oleh App\Support\UserLocale.

CREATE TABLE IF NOT EXISTS `user_locale` (
  `email`      varchar(100) NOT NULL,
  `locale`     varchar(5)   NOT NULL DEFAULT 'en' COMMENT 'en | id',
  `updated_at` datetime     DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
