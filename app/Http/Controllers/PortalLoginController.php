<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\LoginController as AdminLogin;
use App\Http\Controllers\Tenant\LoginController as TenantLogin;
use App\Support\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

/**
 * Login satu pintu untuk Admin dan Tenant (halaman "/").
 *
 * Email + password dicek ke tabel all_login:
 *  - tableforeign = 'administrator' dan password cocok  -> session admin  -> /admin/dash
 *  - tableforeign = 'tenant' (via tabel tenant) dan cocok -> session tenant -> /tenant/dash
 *
 * Semua portal yang bisa dibuka oleh password tersebut (admin dan/atau business tenant
 * mana saja) disimpan di session 'portals', dipakai oleh dropdown di halaman login
 * dan oleh menu "pindah portal" di header (switchAdmin / switchTenant).
 *
 * Pengisian session tetap dilakukan oleh LoginController masing-masing portal
 * (createSession) supaya isinya sama persis dengan login lama.
 */
class PortalLoginController extends Controller
{
    public function index()
    {
        if (Session::get('is_login')) {
            return redirect('/admin/dash');
        }
        if (Session::get('is_Tenant_logged')) {
            return redirect('/tenant/dash');
        }
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [], [
            'email' => __('shared/login.attributes.email'),
            'password' => __('shared/login.attributes.password'),
        ]);

        $email = $request->email;
        $plain = $request->password;
        $tenantLogin = app(TenantLogin::class);

        // ---------- Admin: cocok? ----------
        $admin = DB::connection('ifcaadm')
            ->table('all_login')
            ->where('email', $email)
            ->where('tableforeign', 'administrator')
            ->first();

        $adminOk = null;
        if ($admin && Password::check($plain, $admin->password)) {
            $adminOk = $admin;
            // akun lama (md5) yang berhasil login langsung dipindah ke bcrypt
            Password::upgrade(
                DB::connection('ifcaadm')->table('all_login')->where('id', $admin->id),
                $admin->password,
                $plain
            );
        }

        // ---------- Tenant: business mana saja yang cocok? ----------
        // Hash bcrypt punya salt berbeda tiap baris, jadi pencocokan tidak bisa lewat
        // where('password', ...) seperti dulu; barisnya diambil lalu dicek satu per satu.
        $tenantOptions = array();
        $tenants = $tenantLogin->activeTenants($email);
        if (count($tenants) > 0) {
            $ids = array_map(function ($t) { return $t->id; }, $tenants);
            $logins = DB::table('all_login')
                ->where('tableforeign', 'tenant')
                ->whereIn('idforeign', $ids)
                ->get();

            $matchedIds = array();
            foreach ($logins as $login) {
                if (!Password::check($plain, $login->password)) {
                    continue;
                }
                $matchedIds[] = $login->idforeign;
                Password::upgrade(
                    DB::table('all_login')->where('id', $login->id),
                    $login->password,
                    $plain
                );
            }

            foreach ($tenants as $t) {
                if (in_array($t->id, $matchedIds)) {
                    $tenantOptions[] = array('id' => $t->id, 'name' => $t->name);
                }
            }
        }

        if (!$adminOk && count($tenantOptions) === 0) {
            return redirect('/')
                ->withInput($request->only('email', 'bsn'))
                ->with('alert', __('shared/login.incorrect'));
        }

        // portal yang boleh dibuka tanpa login ulang (menu pindah portal di header)
        Session::put('portals', array(
            'admin' => $adminOk ? array('id' => $adminOk->idforeign, 'email' => $email) : null,
            'tenants' => $tenantOptions,
        ));

        // ---------- admin selalu diprioritaskan; pindah ke tenant lewat menu di header ----------
        if ($adminOk) {
            return $this->enterAdmin($adminOk->idforeign, $email);
        }

        // ---------- tenant: business yang dipilih di dropdown halaman login ----------
        $bsn = $request->bsn;
        $tenantIds = array_column($tenantOptions, 'id');
        if (!empty($bsn) && in_array($bsn, $tenantIds)) {
            return $this->enterTenant($bsn);
        }
        if (count($tenantOptions) === 1) {
            return $this->enterTenant($tenantOptions[0]['id']);
        }

        // fallback tanpa JavaScript: satu email, beberapa business -> pilih business
        // dulu lewat halaman step lama
        return view('tenant.login.step', array(
            'combo' => $tenantLogin->get_combo(null, array('email' => $email)),
            'dataimages' => array(),
        ));
    }

    /**
     * AJAX dari halaman login: portal apa saja yang dimiliki sebuah email
     * ({admin: bool, tenants: [{id, name}]}) untuk dropdown "Login sebagai".
     */
    public function businesses(Request $request)
    {
        $email = trim((string) $request->query('email'));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(array('admin' => false, 'tenants' => array()));
        }

        $isAdmin = DB::connection('ifcaadm')
            ->table('all_login')
            ->where('email', $email)
            ->where('tableforeign', 'administrator')
            ->exists();

        $list = array();
        foreach (app(TenantLogin::class)->activeTenants($email) as $t) {
            $list[] = array('id' => $t->id, 'name' => $t->name);
        }
        return response()->json(array('admin' => $isAdmin, 'tenants' => $list));
    }

    /** Menu header: pindah ke portal Admin (hanya kalau password login tadi cocok untuk admin). */
    public function switchAdmin()
    {
        $portals = Session::get('portals', array());
        if (empty($portals['admin'])) {
            abort(403, __('shared/login.no_admin_access'));
        }
        return $this->enterAdmin($portals['admin']['id'], $portals['admin']['email']);
    }

    /** Menu header: pindah ke portal Tenant untuk business tertentu. */
    public function switchTenant($id)
    {
        $portals = Session::get('portals', array());
        $ids = array_column($portals['tenants'] ?? array(), 'id');
        if (!in_array($id, $ids)) {
            abort(403, __('shared/login.no_business_access'));
        }
        return $this->enterTenant($id);
    }

    public function logout()
    {
        Session::flush();
        return redirect('/');
    }

    private function enterAdmin($adminId, $email)
    {
        if (!Session::get('is_login')) {
            app(AdminLogin::class)->createSession($adminId, $email);
        }
        return redirect('/admin/dash');
    }

    private function enterTenant($tenantId)
    {
        if (!Session::get('is_Tenant_logged') || Session::get('Tuser_id') != $tenantId) {
            app(TenantLogin::class)->createSession($tenantId);
        }
        return redirect('/tenant/dash');
    }
}
