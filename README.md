# TWP Demo (webadmin + webtenant digabung)

Satu aplikasi Laravel 12 dengan **satu halaman login** untuk Admin dan Tenant; setelah login
masing-masing masuk ke portalnya sendiri. Hasil penggabungan `demo_twp/webadmin` dan
`demo_twp/webtenant` (folder lama di `D:\WORK_IFCA\twp\demo_twp` tidak diubah dan menjadi arsip).
Folder kerja project ini: `D:\xampp\htdocs\twp_demo` (URL lokal `https://localhost/twp_demo/`).

| Portal | URL setelah login | Identitas | Controller | Views | Routes |
|---|---|---|---|---|---|
| Admin  | `/twp_demo/admin/...`  | `all_login` (tableforeign = administrator), session `is_login` | `app/Http/Controllers/Admin/` | `resources/views/admin/` | `routes/admin.php` |
| Tenant | `/twp_demo/tenant/...` | `tenant` + `all_login` (tableforeign = tenant), session `is_Tenant_logged` | `app/Http/Controllers/Tenant/` | `resources/views/tenant/` | `routes/tenant.php` |

## Struktur

```
app/Http/Controllers/PortalLoginController.php   login satu pintu, pindah portal
app/Http/Controllers/Admin/*                      controller portal admin (namespace App\Http\Controllers\Admin)
app/Http/Controllers/Tenant/*                     controller portal tenant (namespace App\Http\Controllers\Tenant)
app/Http/Middleware/AdminAuth, TenantAuth, RevalidateBackHistory
app/Support/TenantScope.php                       cakupan data tenant (mode semua tenant untuk admin)
app/Support/DefaultPassword.php                   password default dari tabel defaultpassword
resources/views/layouts/app.blade.php             kerangka CoreUI bersama (sidebar/header diisi tiap portal)
resources/views/layouts/auth.blade.php            kerangka halaman login
resources/views/login.blade.php                   halaman login
resources/views/partials/portal_switch.blade.php  menu "Pindah ke Admin/Tenant" di header
resources/views/admin/**, resources/views/tenant/**
routes/web.php  ->  routes/admin.php (prefix /admin), routes/tenant.php (prefix /tenant), routes/api.php (/api)
assets/coreui/                                    CoreUI 5.9 + CoreUI Icons 3.1 (dist)
assets/vendor/                                    jQuery, DataTables 2 (bs5), Select2, SweetAlert2, bootstrap-datepicker, Chart.js 4, Highcharts, CKEditor 5, pdfmake
assets/app/css/app.css, assets/app/js/app.js      style & script bersama di atas CoreUI
assets/pdf/                                       CSS lama khusus template PDF (dompdf)
images/, img/, public/{image,lainnya/img}         aset bersama
database/sql/*.sql                                skrip perubahan data (lihat bawah)
```

## Login satu pintu (`/twp_demo/`)

`resources/views/login.blade.php` + `PortalLoginController`:

1. Setelah email diketik, halaman memanggil `GET /login/businesses?email=...` (AJAX). Field password
   terkunci sampai pengecekan selesai (placeholder "Memeriksa email...").
2. Email **administrator** -> dropdown tidak ditampilkan; login **selalu** masuk portal admin
   (`/admin/dash`). Pindah ke tenant lewat menu di header.
3. Email **tenant** -> dropdown **Business Name** muncul: terisi otomatis jika satu business, wajib dipilih
   jika lebih dari satu. Dikirim sebagai `bsn` saat `POST /login`. Tanpa JavaScript, tenant dengan lebih dari
   satu business jatuh ke halaman pilih business lama (`tenant.login.step` -> `POST /tenant/login`).
4. Password dicek ke `all_login` (md5). Gagal -> kembali ke `/` dengan pesan.
5. `/admin` dan `/tenant` (halaman login lama) diarahkan ke `/`; semua logout kembali ke `/`.
6. URL project lama `/twp_demo/webadmin/...` dan `/twp_demo/webtenant/...` -> redirect ke `/twp_demo/`
   (route catch-all di `routes/web.php`).

Pengisian session dilakukan oleh `createSession()` di `Admin\LoginController` / `Tenant\LoginController`
(isinya sama persis dengan login lama).

## Pindah portal tanpa login ulang

Saat login, semua portal yang password-nya cocok (admin dan/atau business tenant) disimpan di session
`portals`. Dropdown user di header (`partials/portal_switch.blade.php`) menampilkan
"Pindah ke Admin" / "Pindah ke Tenant: <business>" -> `GET /switch/admin`, `GET /switch/tenant/{id}`
(hanya ke portal yang ada di `portals`, selain itu 403). Session portal sebelumnya tidak dihapus,
jadi bolak-balik cepat; Sign out (`Session::flush()`) menghapus semuanya.

## Mode semua tenant (`app/Support/TenantScope.php`)

Akun yang juga administrator (mis. `admin@ifca.co.id`, yang tenant_no `IFCA`-nya tidak punya data di
SQL Server) masuk portal tenant dengan session `Tall_tenants = true`. Semua query portal tenant memakai
`TenantScope::tenantNos()` / `tenantIds()` / `businessNos()` (`IN (...)`) sehingga untuk akun ini:

- form Ticket & Permit: dropdown tenancy berisi **seluruh tenancy aktif** (`pm_tenancy.status = 'A'`), lalu unit
  milik tenancy yang dipilih;
- Dashboard & Billing Outstanding: combo unit/meter berisi unit semua tenant (label `tenant_no - unit`);
- Invoice, Proforma, History (ticket/overtime/billing/invoice), survey: data semua tenant.

Selain `debtor_acct`, batasan **entity_cd / project_no** juga ikut melebar lewat
`TenantScope::entityCds()` / `projectNos()` / `sqlEntity()` (dipakai Dashboard, Invoice, Proforma,
History Invoice, grafik & combo meter). Tanpa itu, akun admin tetap tersaring ke entity/project
tenancy-nya sendiri sehingga tenant di project lain tidak muncul.

Tenant baru otomatis ikut. Tenant biasa tetap hanya melihat data miliknya sendiri.

## Password default (`app/Support/DefaultPassword.php`)

Menu admin **Password -> Default Password** (`/admin/systemspec/defaultpass`) menyimpan nilai ke tabel
`defaultpassword` (plain text). Nilai itu dipakai oleh:

- **Password -> Password Reset** (`Admin\AccountController::resetpass`) -> `all_login.password = md5(nilai)`;
- pembuatan akun tenant baru lewat API sinkronisasi (`Admin\WsbangunController::business`, 2 tempat).

Kalau tabel kosong, fallback `cartenz123` (nilai hardcode lama).

## Letter Permit (portal tenant & admin)

Menu **Letter Permit** di kedua portal: `/tenant/permit/history` & `/admin/permit/history`
(form: `.../permit/add`). Logika bersama di `App\Http\Controllers\BasePermitController`,
turunannya `Tenant\PermitController` dan `Admin\PermitController` hanya menentukan cakupan data,
layout dan identitas pemohon. View bersama `resources/views/permit/{form,history,print}.blade.php`,
CSS `assets/app/css/permit.css`.

- Tiga jenis permit: **Work Permit** (`W`), **Entry Permit of Goods** (`I`), **Exit Permit of Goods** (`O`).
  Satu form: bagian Location / Schedule / Note sama untuk semua jenis; bagian detail dan daftar
  (pekerja / barang) berganti mengikuti jenis. Field di bagian yang tersembunyi di-*disable*
  sehingga tidak ikut terkirim.
- Simpan lewat satu endpoint `POST /tenant/permit/save` (JSON). Validasi server mengembalikan
  `errors` per field yang ditampilkan inline di form.
- Semua data ke SQL Server (`dblive`): `mgr.sv_entry_letter` (header, `complain_type` = W/I/O,
  `complain_no` = nomor permit), `mgr.permit_letter_hd/dtl` (Work Permit + pekerja),
  `mgr.permit_letter_tools` (kegiatan + peralatan / APD Work Permit),
  `mgr.permit_goods_hd/dtl` (Permit of Goods + barang), `mgr.sv_entry_letter_log`.
  Script tabel: `database/sql/2026-09-23_permit_letter_work_tools.sql` dan
  `database/sql/2026-09-23_permit_goods_letter.sql`.
- Nomor permit dari `mgr.sv_spec.letter_no` per entity/project (`LP100001` -> `LP100002`). Diambil di
  dalam transaksi dengan `lockForUpdate()` (updlock/holdlock) lalu dinaikkan, jadi dua request bersamaan
  tidak pernah mendapat nomor yang sama. Nomor di form hanya pratinjau (`GET /tenant/permit/letterNo/{id}`).
- Tanggal ke kolom `datetime` selalu `yyyymmdd`, audit_date `yyyy-mm-ddThh:mm:ss` (koneksi ODBC memakai
  `DATEFORMAT dmy`, format lain salah baca).
- History: DataTables server side (`GET /tenant/permit/historyTable`), filter nomor / jenis / status /
  tanggal mulai; tombol Print -> `GET /tenant/permit/print/{doc_no}` (dompdf), hanya permit milik tenant
  yang sedang login (`TenantScope`).
- **Beda portal**: tenant hanya melihat/membuat permit miliknya (`TenantScope`); admin melihat
  **semua tenant**, bisa membuat permit untuk tenant mana pun (pilihan unit tetap mengikuti tenant
  yang dipilih), dan punya filter Tenant + kolom Tenant di History.
- **Ubah permit**: tombol pensil di History -> `GET <portal>/permit/edit/{doc_no}` (form yang sama,
  mode edit) dan `POST <portal>/permit/update`. Bisa diubah selama status `R` (Open) atau `M`
  (Modify) — lihat `BasePermitController::EDITABLE_STATUSES`. Bagian 1-2 (jenis permit, nomor,
  tenant, unit, lantai) selalu hanya tampilan. **Tenant** boleh mengubah bagian 3-5; **admin** hanya
  Work Tools (Work Permit) + bagian 4-5 — field terkunci dirender tanpa atribut `name` dan nilainya
  diambil dari data tersimpan di server (`lockedFields()`), jadi tidak bisa diakali lewat POST.
- **Status & log**: tiap perubahan menambah baris baru di `mgr.sv_entry_letter_log` (tidak pernah
  di-update), sehingga jejak create/modify/approve/cancel tetap lengkap:

  | Aksi | status `sv_entry_letter` | remarks log |
  |---|---|---|
  | Buat permit | `R` | `Request created by tenant` / `... by admin` |
  | Tenant / admin ubah isi | `M` | `Modified by tenant` / `Modified by admin` |
  | Admin pilih **Approve** saat menyimpan | `Y` | `Approved by admin` |
  | Admin pilih **Cancel** saat menyimpan | `X` | `Cancelled by admin` |
  | Tenant tekan tombol Cancel di History | `X` | `Cancelled by tenant` |

  Setelah `Y` atau `X`, permit tidak bisa diubah atau dibatalkan lagi oleh siapa pun
  (`POST <portal>/permit/cancel` dan `update` menolak dengan 422).
- `database/sql/2026-09-21_create_tenant_permit_tables.sql` (tabel MySQL) **tidak dipakai**; ditinggalkan
  sebagai catatan rancangan awal.

## Konfigurasi

- **Database** (`config/database.php`): kedua app memakai 2 database yang sama, hanya nama koneksinya berbeda;
  semua nama tetap tersedia supaya kode lama tidak perlu diubah:
  - MySQL `demo_twp` (`DB_*`): `mysql` (default, kode tenant) dan `ifcaadm` (kode admin)
  - SQL Server `jbc_live` (`DB_*2`): `TWP` (kode tenant) dan `ifcapb` (kode admin). Keduanya memakai
    `trust_server_certificate = true` (env `DB_TRUST_SERVER_CERTIFICATE2`) karena ODBC Driver 18 menolak
    sertifikat self-signed `sql.ifca.co.id`.
- **Session**: satu cookie session untuk seluruh aplikasi.
- **Middleware**: `admin-auth` (`AdminAuth`), `tenant-auth` (`TenantAuth`), `revalidate`.
- `composer.json` memakai versi webtenant (termasuk `barryvdh/laravel-dompdf` untuk export PDF).
  Timezone `Asia/Jakarta`.
- Deploy: folder project langsung di bawah document root (`.../twp_demo/`); `.htaccess` di root
  memaksa HTTPS dan mengarahkan request non-file ke `index.php` -> `public/index.php`.

## Setup

```bash
composer install
```

Sesuaikan `.env` (`APP_URL` menunjuk ke `/twp_demo/`), pastikan `storage/` dan `bootstrap/cache/` writable.

## Perubahan data (MySQL `demo_twp`) — skrip di `database/sql/`

| File | Isi |
|---|---|
| `2026-09-18_all_login_admin_as_tenant.sql` | Memberi `admin@ifca.co.id` baris `all_login` tenant (id tenant PT. IFCA) dengan password sama seperti akun admin-nya, supaya bisa pindah ke portal tenant |
| `2026-09-18_all_login_renumber.sql` | Menomori ulang `all_login.id` (baris tenant id = tenant.id) |
| `2026-09-18_fix_management_and_log_login.sql` | `management@ifca.co.id` -> `idforeign = 2` (Admin Management); `log_login` di-remap ke id tenant baru, baris uji dihapus |

Skrip-skrip ini spesifik untuk kondisi data lokal saat itu; jangan dijalankan di server lain tanpa dicek.
Keadaan akhir lokal: `tenant` id 1 (IFCA), 2 (00050-A Aditya), 3 (00050-A Ilham); `all_login` id 1..5.

## Catatan data yang perlu diperhatikan

- `all_login.pict` admin dan `image_login.image_url` menyimpan URL absolut project lama
  (`.../demo_twp/webadmin/...`, `.../Carstensz/webadmin/...`) -> foto profil/slide login 404 sampai datanya
  diperbarui (lewat menu profil / System Spec).

## Changelog 2026-09-18

1. **Penggabungan** webadmin + webtenant menjadi satu app: prefix URL `/admin` & `/tenant`, controller
   di-namespace, view diberi prefix folder (`admin.*`, `tenant.*`, termasuk `PDF::loadView`), aset `assets/`
   dipisah per portal (sisanya identik -> bersama), 4 nama koneksi DB, `trust_server_certificate`.
2. **Login satu pintu** di `/` dengan deteksi email (AJAX), dropdown Business Name untuk tenant, password
   terkunci sampai email dicek, admin selalu diprioritaskan.
3. **Pindah portal** dari header (`portals` di session, `/switch/admin`, `/switch/tenant/{id}`).
4. **Mode semua tenant** untuk akun admin di portal tenant (`TenantScope`).
5. **Password default** dari tabel `defaultpassword` (`DefaultPassword`), bukan hardcode `cartenz123`.
6. **Sidebar admin**: menu **Password** dengan submenu Password Reset & Default Password; kondisi aktif
   submenu (`request()->is('admin/...')`) dibetulkan agar submenu terbuka otomatis.
7. **Redirect URL lama** `/webadmin/*`, `/webtenant/*` -> `/`.
8. **Pembersihan kode mati**: file backup (`*_backup.php`, `*_12082026.php`, `* - Copy`, dll.),
   controller tanpa route (Group, Menu, Projects, Overtime admin & tenant, Test), view yang tidak pernah
   dirender, middleware yang tidak terdaftar, route yang menunjuk method/view yang tidak ada, method yang
   tidak pernah dipanggil, komentar berisi kode lama / `var_dump` / `dd()`, `use` yang tidak terpakai.
   Cabang tenant khusus `BM` di `Tenant/TicketController` dihapus (tidak ada di data), query unit disatukan
   ke `lotsOfTenancy()`. Dua debug aktif dihapus: `dd()` di `HistoryController::billingTable` (history
   billing tenant hidup lagi) dan `var_dump("ISI")` di API `WsbangunController::business`.
9. **Keamanan**: route publik `GET /admin/account/data` (membocorkan `all_login` + hash) dan
   `GET /admin/account/forgot_password` dihapus; yang dilindungi middleware tetap.
10. **Data**: `all_login` untuk admin@ifca sebagai tenant, penomoran ulang id, perbaikan `idforeign`
    management, remap `log_login` (lihat `database/sql/`).

## Changelog 2026-09-22

1. **Letter Permit** dirapikan: satu endpoint `POST /tenant/permit/save` (menggantikan `workpermit` &
   `permitofgoods`), nomor permit diambil di dalam transaksi dengan lock baris `sv_spec` (bebas nomor dobel),
   nama field form disatukan, nilai panjang dipotong sesuai lebar kolom SQL Server, pesan error SQL hanya
   tampil saat `APP_DEBUG`. Endpoint mati `getTicketNew` di PermitController dihapus.
2. **Tampilan permit** dibangun ulang (form bertahap 1-5, validasi inline, daftar pekerja/barang dengan
   Enter untuk tambah baris, konfirmasi sebelum submit, filter + badge status di History) dengan CSS
   terpisah `assets/app/css/permit.css`; PDF permit: label, checkbox jenis, status.
3. **Template tenant**: jQuery tidak lagi dimuat dua kali (CDN + bundle), modal dipindah ke dalam `<body>`,
   `@stack('styles')`/`@stack('scripts')` dan `@section('title')` tersedia untuk halaman;
   header memakai query builder (bukan SQL string dengan email dari session) dan tidak error saat baris
   `all_login` tidak ada; sidebar menandai menu aktif.
4. **Bug**: form ticket mengirim `tenant_no` (bukan `id_tenancy`) ke `getLotNo` sehingga dropdown unit
   kadang kosong; `getLotNo` sekarang memeriksa cakupan tenant dan tidak error untuk id tidak valid;
   header `Cache-Control: nocache` -> `no-cache` di middleware `revalidate`.

## Template CoreUI (migrasi 2026-09-22)

Seluruh tampilan (login, portal admin, portal tenant) memakai **CoreUI 5.9** (Bootstrap 5.3) dan
**CoreUI Icons Free**; DashLite 2.2 (Bootstrap 4) dan AdminLTE halaman login dihapus (`assets/admin`,
`assets/tenant`, `public/AssetsLogin`, `public/lainnya/{bootstrap,dist,plugins}`).

- Semua aset lokal (tanpa build step) di `assets/coreui`, `assets/vendor`, `assets/app`. Versi: jQuery 3.7.1,
  jQuery Validation 1.21, DataTables 2.3.4 + Buttons 3.2.5 (integrasi bs5), Select2 4.0.13 + tema bootstrap-5,
  SweetAlert2 11, bootstrap-datepicker 1.10, moment 2.30, Chart.js 4.5, Highcharts 12.4, CKEditor 5 41 (classic),
  pdfmake 0.2 + JSZip.
- Layout: `layouts/app.blade.php` (kerangka: head + script, `wrapper`, footer, modal bersama
  `#modal/#modalsm/#modallg/#modalxl`, `#overlaySpinner`). `tenant/template/base` & `admin/template/layout2/base`
  hanya mengisi section `sidebar` dan `header`. Halaman: `@section('title')`, `@push('styles')`,
  `@push('head-scripts')`, `@push('scripts')`.
- Script dimuat di `<head>` (seperti sebelumnya) karena banyak halaman memakai jQuery langsung di dalam
  `@section('content')`. CoreUI mendaftarkan plugin jQuery, jadi `$('#modal').modal('show')` tetap jalan;
  event modal memakai `.coreui.modal`, atribut `data-coreui-toggle/dismiss/target`.
- `assets/app/js/app.js`: header CSRF AJAX, default Select2 (tema bs5, lebar 100%, `dropdownParent` otomatis di
  dalam modal), inisialisasi `.date-picker` (juga untuk isi modal yang dimuat AJAX), helper global lama
  `block()`, `FormatDateNew()`, `FormatDateTimeNew()`.
- Kelas pengganti DashLite (didefinisikan di `app.css`): `page-head / page-head-row / page-head-content /
  page-title / page-desc / page-block`, `card-title-group`, `form-control-wrap + form-icon`, `badge-soft-*`,
  `table-dark` untuk thead gelap, `.toolbar` untuk tombol DataTables lama (`dom: '<"toolbar group">frtip'`).
- Ikon: `ni ni-*` -> `cil-*` (peta di scratch conversion; mis. calendar, cloud-download, search, plus, trash, x,
  lock-locked, task, history, pencil, warning, user, swap-horizontal, account-logout, reload, newspaper, menu,
  clipboard, speedometer, wallet, people, tags, send, print, info, filter, description, building, bar-chart).
- Dashboard: Chart.js 1.x (`new Chart(ctx).Bar`) di dashboard admin ditulis ulang ke Chart.js 4; grafik
  tenant (4 salinan kode) disatukan ke `renderCharts()`. Halaman tenant `/tenant/oustanding` dan admin
  `/admin/history/overtime` sudah error SQL sebelum migrasi (kolom `mcurr_cd` / view `mgr.v_overtime_history`
  tidak ada) dan tidak ada di menu; view-nya tetap dikonversi.
- Perbaikan yang ikut: form ticket tenant tidak lagi menimpa dropdown unit (parameter `id_tenancy`), tombol
  Generate PDF di History admin dipulihkan (kirim `debtor_acct`), survey tenant tidak lagi bergantung
  jQuery Validate (`checkValidity()`), field password profil bertipe `password`.
