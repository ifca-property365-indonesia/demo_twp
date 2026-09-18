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
resources/views/login.blade.php                   halaman login
resources/views/partials/portal_switch.blade.php  menu "Pindah ke Admin/Tenant" di header
resources/views/admin/**, resources/views/tenant/**
routes/web.php  ->  routes/admin.php (prefix /admin), routes/tenant.php (prefix /tenant), routes/api.php (/api)
assets/admin/, assets/tenant/                     aset yang berbeda per portal
images/, img/, public/{image,lainnya,AssetsLogin} aset bersama
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

- form Ticket: dropdown tenancy berisi **seluruh tenancy aktif** (`pm_tenancy.status = 'A'`), lalu unit
  milik tenancy yang dipilih;
- Dashboard & Billing Outstanding: combo unit/meter berisi unit semua tenant (label `tenant_no - unit`);
- Invoice, Proforma, History (ticket/overtime/billing/invoice), survey: data semua tenant.

Tenant baru otomatis ikut. Tenant biasa tetap hanya melihat data miliknya sendiri.

## Password default (`app/Support/DefaultPassword.php`)

Menu admin **Password -> Default Password** (`/admin/systemspec/defaultpass`) menyimpan nilai ke tabel
`defaultpassword` (plain text). Nilai itu dipakai oleh:

- **Password -> Password Reset** (`Admin\AccountController::resetpass`) -> `all_login.password = md5(nilai)`;
- pembuatan akun tenant baru lewat API sinkronisasi (`Admin\WsbangunController::business`, 2 tempat).

Kalau tabel kosong, fallback `cartenz123` (nilai hardcode lama).

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
  (`.../demo_twp/webadmin/...`, `.../capital/webadmin/...`) -> foto profil/slide login 404 sampai datanya
  diperbarui (lewat menu profil / System Spec).
- Pengiriman email memakai SP SQL Server `mgr.x_send_mail_twp`; ketiga pemanggilannya sengaja masih
  di-comment (`Admin/AccountController::resetpass`, `Tenant/TicketController::save`; yang ketiga ada di
  `Tenant/OvertimeController` yang sudah dihapus karena fiturnya mati).

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
