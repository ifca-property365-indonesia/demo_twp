-- 2026-09-23  Work Permit (W) disesuaikan dengan form "SURAT IZIN KERJA / WORKING PERMIT".
--
--   1. mgr.permit_letter_hd   + pic_hp        (Telp. Kantor / HP penanggung jawab)
--                             work_tools 50 -> 255 (ringkasan daftar peralatan, dipakai laporan lama)
--   2. mgr.permit_letter_tools  TABEL BARU: rincian Jenis Pekerjaan / Kegiatan,
--                             Peralatan / Alat Pelindung Diri, Keterangan (banyak baris per permit).
--
-- Daftar pekerja tetap di mgr.permit_letter_dtl (tidak berubah); PDF hanya menampilkan jumlahnya.
-- Jam Kerja (10.00-22.00 / 22.00-10.00 / lain-lain) diturunkan dari start_time/end_time, tanpa kolom baru.
-- sv_entry_letter, sv_entry_letter_dt, sv_entry_letter_log: tidak berubah.

USE [jbc_live]
GO

SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

-- ---------------------------------------------------------------------------
-- 1. mgr.permit_letter_hd (create ulang)
--    Kalau tabel sudah berisi data dan tidak mau di-drop, pakai ALTER di bagian bawah file.
-- ---------------------------------------------------------------------------
-- DROP TABLE [mgr].[permit_letter_hd]
-- GO

CREATE TABLE [mgr].[permit_letter_hd](
	[rowID] [numeric](12, 0) IDENTITY(1,1) NOT NULL,
	[entity_cd] [varchar](4) NOT NULL,
	[project_no] [varchar](20) NOT NULL,
	[doc_no] [varchar](10) NOT NULL,
	[member_email] [varchar](60) NULL,
	[member_name] [varchar](50) NOT NULL,
	[member_hp] [varchar](20) NULL,
	[debtor_acct] [varchar](20) NULL,
	[pic_name] [varchar](50) NOT NULL,
	[pic_hp] [varchar](20) NULL,
	[kontraktor_name] [varchar](50) NOT NULL,
	[tower] [varchar](20) NULL,
	[floor] [varchar](5) NOT NULL,
	[unit] [varchar](8) NOT NULL,
	[work_type] [varchar](50) NULL,
	[work_tools] [varchar](255) NULL,
	[start_day] [varchar](15) NULL,
	[end_day] [varchar](15) NULL,
	[start_date] [datetime] NULL,
	[end_date] [datetime] NULL,
	[start_time] [varchar](5) NULL,
	[end_time] [varchar](5) NULL,
	[note] [varchar](1000) NULL,
	[audit_user] [varchar](10) NOT NULL,
	[audit_date] [datetime] NOT NULL,
 CONSTRAINT [PK_permit_letter_hd] PRIMARY KEY CLUSTERED
(
	[rowID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO

-- ---------------------------------------------------------------------------
-- 2. mgr.permit_letter_tools (baru)
--    Satu baris = satu baris tabel di form: kegiatan + peralatan/APD + keterangan.
--    Urutan tampil mengikuti rowID (sama seperti permit_letter_dtl).
-- ---------------------------------------------------------------------------
CREATE TABLE [mgr].[permit_letter_tools](
	[rowID] [numeric](12, 0) IDENTITY(1,1) NOT NULL,
	[entity_cd] [varchar](4) NOT NULL,
	[project_no] [varchar](20) NOT NULL,
	[doc_no] [varchar](10) NOT NULL,
	[debtor_acct] [varchar](20) NULL,
	[lot_no] [varchar](8) NULL,
	[activity] [varchar](100) NOT NULL,
	[tool_name] [varchar](100) NOT NULL,
	[remarks] [varchar](255) NULL,
	[audit_user] [varchar](10) NOT NULL,
	[audit_date] [datetime] NOT NULL,
 CONSTRAINT [PK_permit_letter_tools] PRIMARY KEY CLUSTERED
(
	[rowID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO

CREATE NONCLUSTERED INDEX [IX_permit_letter_tools_doc] ON [mgr].[permit_letter_tools]
(
	[entity_cd] ASC,
	[project_no] ASC,
	[doc_no] ASC
) ON [PRIMARY]
GO

-- ---------------------------------------------------------------------------
-- Alternatif untuk no. 1 tanpa drop (data lama tetap ada):
-- ---------------------------------------------------------------------------
-- ALTER TABLE [mgr].[permit_letter_hd] ADD [pic_hp] [varchar](20) NULL
-- GO
-- ALTER TABLE [mgr].[permit_letter_hd] ALTER COLUMN [work_tools] [varchar](255) NULL
-- GO
