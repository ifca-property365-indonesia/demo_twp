-- Tabel permit portal tenant (MySQL demo_twp).
-- Work Permit  (permit_type W)   : tenant_work_permit  + tenant_work_permit_dtl (daftar pekerja)
-- Permit of Goods (I = Entry, O = Exit) : tenant_permit_goods + tenant_permit_goods_dtl (daftar barang)
-- Konvensi kolom mengikuti sv_entry_multi (id_tenant, tenant_no, entity_cd, project_no, status).

CREATE TABLE IF NOT EXISTS `tenant_work_permit` (
  `id`            int(11)      NOT NULL AUTO_INCREMENT,
  `permit_no`     varchar(20)  NOT NULL,
  `id_tenant`     int(11)      NOT NULL,
  `tenant_no`     varchar(20)  DEFAULT NULL,
  `business_no`   varchar(20)  DEFAULT NULL,
  `entity_cd`     varchar(4)   DEFAULT NULL,
  `project_no`    varchar(20)  DEFAULT NULL,
  `applicant`     varchar(60)  DEFAULT NULL,
  `contact_no`    varchar(20)  DEFAULT NULL,
  `incharge`      varchar(60)  DEFAULT NULL,
  `contractor`    varchar(100) DEFAULT NULL,
  `floor`         varchar(20)  DEFAULT NULL,
  `job_type`      varchar(100) DEFAULT NULL,
  `work_tool`     varchar(255) DEFAULT NULL,
  `note`          varchar(255) DEFAULT NULL,
  `start_date`    date         DEFAULT NULL,
  `end_date`      date         DEFAULT NULL,
  `start_time`    time         DEFAULT NULL,
  `end_time`      time         DEFAULT NULL,
  `status`        char(1)      DEFAULT 'R',
  `source`        varchar(20)  DEFAULT 'TWP',
  `created_by`    varchar(60)  DEFAULT NULL,
  `created_at`    datetime     DEFAULT NULL,
  `updated_at`    datetime     DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_work_permit_no` (`permit_no`),
  KEY `idx_work_permit_tenant` (`id_tenant`, `tenant_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `tenant_work_permit_dtl` (
  `id`            int(11)      NOT NULL AUTO_INCREMENT,
  `id_permit`     int(11)      NOT NULL,
  `permit_no`     varchar(20)  NOT NULL,
  `line_no`       int(5)       NOT NULL,
  `worker_name`   varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_work_permit_dtl` (`id_permit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `tenant_permit_goods` (
  `id`            int(11)      NOT NULL AUTO_INCREMENT,
  `permit_no`     varchar(20)  NOT NULL,
  `permit_type`   char(1)      NOT NULL COMMENT 'I = Entry, O = Exit',
  `id_tenant`     int(11)      NOT NULL,
  `tenant_no`     varchar(20)  DEFAULT NULL,
  `business_no`   varchar(20)  DEFAULT NULL,
  `entity_cd`     varchar(4)   DEFAULT NULL,
  `project_no`    varchar(20)  DEFAULT NULL,
  `applicant`     varchar(60)  DEFAULT NULL,
  `contact_no`    varchar(20)  DEFAULT NULL,
  `company`       varchar(100) DEFAULT NULL,
  `owner`         varchar(100) DEFAULT NULL,
  `floor`         varchar(20)  DEFAULT NULL,
  `vehicle_no`    varchar(20)  DEFAULT NULL,
  `note`          varchar(255) DEFAULT NULL,
  `start_date`    date         DEFAULT NULL,
  `end_date`      date         DEFAULT NULL,
  `status`        char(1)      DEFAULT 'R',
  `source`        varchar(20)  DEFAULT 'TWP',
  `created_by`    varchar(60)  DEFAULT NULL,
  `created_at`    datetime     DEFAULT NULL,
  `updated_at`    datetime     DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_permit_goods_no` (`permit_no`),
  KEY `idx_permit_goods_tenant` (`id_tenant`, `tenant_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `tenant_permit_goods_dtl` (
  `id`            int(11)      NOT NULL AUTO_INCREMENT,
  `id_permit`     int(11)      NOT NULL,
  `permit_no`     varchar(20)  NOT NULL,
  `line_no`       int(5)       NOT NULL,
  `item_name`     varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_permit_goods_dtl` (`id_permit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2026-09-21 (rev): form permit sekarang memilih Tenant + Unit; simpan lot_no di header.
ALTER TABLE `tenant_work_permit`  ADD COLUMN `lot_no` varchar(8) DEFAULT NULL AFTER `contractor`;
ALTER TABLE `tenant_permit_goods` ADD COLUMN `lot_no` varchar(8) DEFAULT NULL AFTER `owner`;
