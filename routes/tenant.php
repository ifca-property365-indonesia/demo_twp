<?php

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Dimuat oleh routes/web.php di dalam Route::prefix('tenant').
| Semua path di sini otomatis berada di bawah /tenant (contoh: '/dash' => /tenant/dash).
| Controller: App\Http\Controllers\Tenant, view: resources/views/tenant.
| Login dilakukan satu pintu di "/" (PortalLoginController); POST /tenant/login hanya
| dipakai halaman pilih business (tenant.login.step) untuk tenant dengan >1 business.
|
*/

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tenant\AccountController as Account;
use App\Http\Controllers\Tenant\LoginController as Login;
use App\Http\Controllers\Tenant\DashController as Dash;
use App\Http\Controllers\Tenant\TicketController as Ticket;
use App\Http\Controllers\Tenant\HistoryController as History;
use App\Http\Controllers\Tenant\NewHistoryController as NewHistory;
use App\Http\Controllers\Tenant\NewsController as News;
use App\Http\Controllers\Tenant\OnlineSurveyController as OnlineSurvey;
use App\Http\Controllers\Tenant\BillingOutstandingController as BillingOutstanding;
use App\Http\Controllers\Tenant\InvoiceController as Invoice;
use App\Http\Controllers\Tenant\UserSurveyController as UserSurvey;
use App\Http\Controllers\Tenant\PermitController as Permit;

// /tenant -> dashboard kalau sudah login, kalau belum ke halaman login "/"
Route::get('/', [Login::class, 'index']);
Route::post('/login', [Login::class, 'login']);
Route::get('/logout', [Login::class, 'logout']);

Route::group(['middleware' => ['tenant-auth', 'revalidate']], function () {
	// DashController
	Route::get('/dash', [Dash::class, 'index']);
	Route::post('/dash/getGraph', [Dash::class, 'getGraph']);
	Route::post('/dash/getGraphMeterId', [Dash::class, 'getGraphMeterId']);
	Route::post('/dash/gen', [Dash::class, 'gen']);
	Route::get('/dash/export/{nm}/{lot_no}', [Dash::class, 'export']);
	Route::post('/dash/cancelOT', [Dash::class, 'cancelOT']);
	Route::post('/dash/getMeterIdByUtility', [Dash::class, 'getMeterIdByUtility']);

	// AccountController
	Route::view('/account/profile', 'tenant.account.profile');
	Route::get('/account/getbyemail/{email}', [Account::class, 'getbyemail']);
	Route::post('/account/updateprofile', [Account::class, 'updateprofile']);
	Route::post('/account/savepic', [Account::class, 'savepic']);
	Route::post('/account/changepass', [Account::class, 'changepass']);

	// TicketController
	Route::get('/ticket', [Ticket::class, 'index']);
	Route::get('/ticket/{id}/{form}', [Ticket::class, 'index']);
	Route::get('/ticket/{id}', [Ticket::class, 'getByID']);
	Route::get('/ticket/getTicket/{ent}/{prj}', [Ticket::class, 'getTicket']);
	Route::get('/ticket/getTicketNew/{ent}/{prj}', [Ticket::class, 'getTicketNew']);
	Route::get('/ticket/getTicketPrefix/{ent}/{prefix}', [Ticket::class, 'getTicketPrefix']);
	Route::post('/ticket/getCat', [Ticket::class, 'getCat']);
	Route::get('/ticket/getCatEdit/{complain_type}/{category_cd}', [Ticket::class, 'getCatEdit']);
	Route::post('/ticket/getLotNo', [Ticket::class, 'getLotNo']);
	Route::get('/ticket/getLotNoEdit/{tenant_no}/{lot_no}', [Ticket::class, 'getLotnoEdit']);
	Route::post('/ticket/savepic', [Ticket::class, 'savepic']);
	Route::get('/ticketharga/getHargaItem', [Ticket::class, 'getHargaItem']);
	Route::get('/ticketharga/getHargaJasa', [Ticket::class, 'getHargaJasa']);

	// BillingOutstandingController / InvoiceController
	Route::get('/oustanding', [BillingOutstanding::class, 'index']);
	Route::get('/invoice', [Invoice::class, 'index']);
	Route::get('/proforma', [Invoice::class, 'proforma']);
	Route::get('/invoice/proforma/pdf/{file}', [Invoice::class, 'proformaPdf'])
		->where('file', '.*')
		->name('invoice.proforma.pdf');

	// HistoryController
	Route::view('/history/ticket', 'tenant.history.ticket_history');
	Route::view('/history/overtime', 'tenant.history.overtime_history');
	Route::view('/history/billing', 'tenant.history.billing_history');
	Route::view('/history/invoice', 'tenant.history.invoice_history');
	Route::get('/hticketTable', [History::class, 'ticketTable']);
	Route::get('/hovertimeTable', [History::class, 'overtimeTable']);
	Route::get('/hbillingTable', [History::class, 'billingTable']);
	Route::get('/hinvoiceTable', [History::class, 'invoiceTable']);
	Route::post('/hticketSearch', [History::class, 'ticketSearch']);
	Route::post('/hovertimeSearch', [History::class, 'overtimeSearch']);
	Route::post('/hbillingSearch', [History::class, 'billingSearch']);
	Route::post('/gethistorybillingtable', [NewHistory::class, 'getbillingtable']);

	// NewsController
	Route::get('/news', [News::class, 'index']);

	// OnlineSurveyController
	Route::get('/online_survey', [OnlineSurvey::class, 'index']);
	Route::post('/online_survey/save', [OnlineSurvey::class, 'save']);

	// UserSurveyController
	Route::get('/usersurvey/index', [UserSurvey::class, 'index']);
	Route::post('/usersurvey/submit', [UserSurvey::class, 'submit']);

	// PermitController (Letter Permit)
	Route::get('/permit/add', [Permit::class, 'index']);
	Route::get('/permit/letterNo/{id_tenancy}', [Permit::class, 'letterNo'])->whereNumber('id_tenancy');
	Route::post('/permit/save', [Permit::class, 'save']);
	Route::get('/permit/history', [Permit::class, 'history']);
	Route::get('/permit/historyTable', [Permit::class, 'table']);
	Route::get('/permit/print/{doc_no}', [Permit::class, 'printPdf'])->where('doc_no', '[A-Za-z0-9\-]+');
});
