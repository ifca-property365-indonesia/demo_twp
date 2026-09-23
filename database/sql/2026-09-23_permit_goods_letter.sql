-- 2026-09-23  Entry / Exit Permit of Goods (I / O) disesuaikan dengan form
--             "SURAT IZIN KELUAR / MASUK BARANG".
--
--   1. mgr.permit_goods_hd   + end_date, start_time, end_time   (Hari / Tanggal, Jam ... s.d. ...)
--                            + sender_name, sender_id_no, sender_address, sender_hp
--                              (Nama Pengirim / Pengambil, No. KTP / SIM, Alamat, No. Telepon)
--                            + vehicle_type                     (Jenis Kendaraan)
--                            company_name jadi NULL (tidak ada di form, tetap disimpan untuk data lama)
--   2. mgr.permit_goods_dtl  + item_qty                         (JUMLAH, teks bebas mis. "2 box")
--                            item_name 50 -> 100, item_descs 50 -> 255 NULL (KETERANGAN)
--
-- work_type (Jenis Pekerjaan) dan owner_name (Nama Pemilik / Penyewa) sudah ada.
-- sv_entry_letter dan sv_entry_letter_log: tidak berubah.

USE [jbc_live]
GO

SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

-- ---------------------------------------------------------------------------
-- 1. mgr.permit_goods_hd (create ulang)
--    Kalau tabel sudah berisi data dan tidak mau di-drop, pakai ALTER di bagian bawah file.
-- ---------------------------------------------------------------------------
-- DROP TABLE [mgr].[permit_goods_hd]
-- GO

CREATE TABLE [mgr].[permit_goods_hd](
	[rowID] [numeric](12, 0) IDENTITY(1,1) NOT NULL,
	[entity_cd] [varchar](4) NOT NULL,
	[project_no] [varchar](20) NOT NULL,
	[doc_no] [varchar](10) NOT NULL,
	[member_email] [varchar](60) NULL,
	[member_name] [varchar](50) NOT NULL,
	[member_hp] [varchar](20) NULL,
	[debtor_acct] [varchar](20) NULL,
	[company_name] [varchar](50) NULL,
	[owner_name] [varchar](50) NOT NULL,
	[tower] [varchar](20) NULL,
	[floor] [varchar](5) NOT NULL,
	[unit] [varchar](10) NOT NULL,
	[start_date] [datetime] NULL,
	[end_date] [datetime] NULL,
	[start_time] [varchar](5) NULL,
	[end_time] [varchar](5) NULL,
	[sender_name] [varchar](50) NULL,
	[sender_id_no] [varchar](30) NULL,
	[sender_address] [varchar](255) NULL,
	[sender_hp] [varchar](20) NULL,
	[vehicle_type] [varchar](30) NULL,
	[vehicle_no] [varchar](50) NULL,
	[work_type] [varchar](50) NULL,
	[note] [varchar](1000) NULL,
	[audit_user] [varchar](10) NOT NULL,
	[audit_date] [datetime] NOT NULL,
 CONSTRAINT [PK_permit_goods_hd] PRIMARY KEY CLUSTERED
(
	[rowID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO

-- ---------------------------------------------------------------------------
-- 2. mgr.permit_goods_dtl (create ulang)
-- ---------------------------------------------------------------------------
-- DROP TABLE [mgr].[permit_goods_dtl]
-- GO

CREATE TABLE [mgr].[permit_goods_dtl](
	[rowID] [numeric](12, 0) IDENTITY(1,1) NOT NULL,
	[entity_cd] [varchar](4) NOT NULL,
	[project_no] [varchar](20) NOT NULL,
	[doc_no] [varchar](10) NOT NULL,
	[debtor_acct] [varchar](20) NULL,
	[lot_no] [varchar](8) NULL,
	[item_name] [varchar](100) NOT NULL,
	[item_qty] [varchar](20) NULL,
	[item_descs] [varchar](255) NULL,
	[audit_user] [varchar](10) NOT NULL,
	[audit_date] [datetime] NOT NULL,
 CONSTRAINT [PK_permit_goods_dtl] PRIMARY KEY CLUSTERED
(
	[rowID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO

-- ---------------------------------------------------------------------------
-- Alternatif tanpa drop (data lama tetap ada):
-- ---------------------------------------------------------------------------
-- ALTER TABLE [mgr].[permit_goods_hd] ALTER COLUMN [company_name] [varchar](50) NULL
-- GO
-- ALTER TABLE [mgr].[permit_goods_hd] ADD
-- 	[end_date] [datetime] NULL,
-- 	[start_time] [varchar](5) NULL,
-- 	[end_time] [varchar](5) NULL,
-- 	[sender_name] [varchar](50) NULL,
-- 	[sender_id_no] [varchar](30) NULL,
-- 	[sender_address] [varchar](255) NULL,
-- 	[sender_hp] [varchar](20) NULL,
-- 	[vehicle_type] [varchar](30) NULL
-- GO
-- ALTER TABLE [mgr].[permit_goods_dtl] ALTER COLUMN [item_name] [varchar](100) NOT NULL
-- GO
-- ALTER TABLE [mgr].[permit_goods_dtl] ALTER COLUMN [item_descs] [varchar](255) NULL
-- GO
-- ALTER TABLE [mgr].[permit_goods_dtl] ADD [item_qty] [varchar](20) NULL
-- GO
