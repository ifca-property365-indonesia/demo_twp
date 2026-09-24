<?php

use App\Http\Controllers\PortalLoginController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Satu aplikasi, satu halaman login ("/") untuk Admin dan Tenant.
| Setelah login, masing-masing masuk ke portalnya:
|   /admin/...   -> routes/admin.php   (App\Http\Controllers\Admin,  views/admin)
|   /tenant/...  -> routes/tenant.php  (App\Http\Controllers\Tenant, views/tenant)
|
*/

Route::get('/', [PortalLoginController::class, 'index']);
Route::post('/login', [PortalLoginController::class, 'login']);
Route::get('/login/businesses', [PortalLoginController::class, 'businesses']);
// pindah portal dari header tanpa login ulang (lihat session 'portals')
Route::get('/switch/admin', [PortalLoginController::class, 'switchAdmin']);
Route::get('/switch/tenant/{id}', [PortalLoginController::class, 'switchTenant'])->where('id', '[0-9]+');
Route::get('/logout', [PortalLoginController::class, 'logout']);

// Ganti bahasa tampilan (menu "Language" di header). Disimpan di session dan, untuk user
// yang sedang login, di tabel user_locale supaya dipakai lagi di login berikutnya
// (App\Support\UserLocale). User yang belum pernah memilih selalu English.
// Cookie 'locale' versi lama dihapus supaya tidak terbawa ke user lain di browser yang sama.
Route::get('/language/{locale}', function (string $locale) {
    session(['locale' => $locale]);
    \App\Support\UserLocale::save($locale);

    return redirect()->back()->withoutCookie('locale');
})->whereIn('locale', array_keys(\App\Http\Middleware\SetLocale::LOCALES));

Route::prefix('admin')->group(base_path('routes/admin.php'));
Route::prefix('tenant')->group(base_path('routes/tenant.php'));

// URL project lama (/webadmin/..., /webtenant/...) -> halaman login satu pintu
Route::get('/webadmin/{any?}', function () {
    return redirect('/');
})->where('any', '.*');
Route::get('/webtenant/{any?}', function () {
    return redirect('/');
})->where('any', '.*');
