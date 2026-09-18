<?php

use App\Http\Controllers\Admin\WsbangunController;
use App\Http\Controllers\Tenant\DashController as TenantDash;
use App\Http\Controllers\Tenant\TicketController as TenantTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Gabungan routes/api.php webadmin + webtenant. Tetap di /api/... (tanpa
| prefix portal) supaya integrasi eksternal (mis. Wsbangun) tidak berubah.
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// --- dari webadmin ---
Route::post('/business/{method}/{value}', [WsbangunController::class, 'business']);
Route::post('/ticket/update/{params}', [WsbangunController::class, 'update_ticket']);

// --- dari webtenant ---
Route::post('/ticket/save', [TenantTicket::class, 'update']);
Route::post('/dash/getGraphMeterId', [TenantDash::class, 'getGraphMeterId']);