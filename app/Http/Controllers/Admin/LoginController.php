<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

/**
 * Login admin sekarang satu pintu di "/" (App\Http\Controllers\PortalLoginController).
 * Class ini tinggal menyediakan pengisian session admin, redirect /admin, dan logout.
 */
class LoginController extends Controller
{
    public function index()
    {
        if (Session::get('is_login')) {
            return redirect('/admin/dash');
        }
        return redirect('/');
    }

    /**
     * Isi session admin. Dipakai PortalLoginController (login satu pintu & pindah portal).
     */
    public function createSession($adminId, $email)
    {
        $dataAdmin = DB::connection('ifcaadm')
            ->table('administrator')
            ->where(array('id' => $adminId))
            ->get();

        Session::put('is_login', TRUE);
        Session::put('Tsuname', $dataAdmin[0]->name);
        Session::put('Tsemail', $email);
        Session::put('Tsuser_id', $dataAdmin[0]->id);
    }

    public function logout()
    {
        Session::flush();
        return redirect('/')->with('alert', 'Already logout!');
    }
}
