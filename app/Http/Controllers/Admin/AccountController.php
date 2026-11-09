<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\DefaultPassword;
use App\Support\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use DataTables;

class AccountController extends Controller
{
    /** Email admin yang sedang login (profil hanya boleh mengubah akun sendiri). */
    private function sessionEmail()
    {
        return (string) Session::get('Tsemail');
    }

    private function forbidden()
    {
        return response()->json(['status' => 'Failed', 'pesan' => __('common.error_occurred', ['message' => 'session'])], 403);
    }

    public function getTable()
    {
        $query = DB::connection('ifcaadm')->select("SELECT ROW_NUMBER() OVER (ORDER BY t.id) AS [row_number], t.* FROM mgr.all_login t ORDER BY t.id");
        return DataTables::of($query)->make(true);
    }
    /** Data profil admin yang sedang login (parameter URL diabaikan; tanpa kolom password). */
    public function getbyemail($email = null)
    {
        $data = DB::connection('ifcaadm')->table('mgr.all_login')
            ->where('email', $this->sessionEmail())
            ->where('tableforeign', 'administrator')
            ->get(['id', 'name', 'email', 'handphone', 'pict', 'tableforeign', 'idforeign']);
        // akun tenant yang memakai email yang sama (pilihan "ganti juga email tenant" di form)
        foreach ($data as $row) {
            $row->shared_tenants = $this->sharedTenants($this->sessionEmail());
        }
        echo json_encode($data);
    }

    /** Nama business tenant yang memakai email ini (mgr.tenant), untuk ditampilkan di form profil. */
    private function sharedTenants($email)
    {
        return DB::connection('ifcaadm')->table('mgr.tenant')
            ->where('email', $email)
            ->orderBy('name')
            ->get(['business_no', 'name', 'flag'])
            ->map(fn ($t) => trim((string) $t->name) . ' (' . trim((string) $t->business_no) . ($t->flag ? ', ' . $t->flag : '') . ')')
            ->unique()->values()->all();
    }
    /**
     * Unggah foto profil ke images/user/ (dipanggil dari modal profil setelah foto dipotong).
     * Balasan JSON: status OK|Failed, pesan, url (absolut), picname.
     */
    public function savepic(Request $request)
    {
        $file = $request->file('userfile');

        if (!$file) {
            // $_FILES kosong: tidak ada file, atau melebihi post_max_size / upload_max_filesize
            return response()->json(['status' => 'Failed', 'pesan' => __('admin/account.no_file_received')]);
        }
        if (!$file->isValid()) {
            return response()->json(['status' => 'Failed', 'pesan' => __('admin/account.upload_error_detail', ['message' => $file->getErrorMessage()])]);
        }
        if ($file->getSize() > 5000000) {
            return response()->json(['status' => 'Failed', 'pesan' => __('common.upload_max_size', ['size' => '5MB'])]);
        }

        $ext = strtolower($file->getClientOriginalExtension() ?: $file->extension());
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'], true)) {
            return response()->json(['status' => 'Failed', 'pesan' => __('common.upload_only_image')]);
        }

        // nama unik supaya tidak menimpa file lain dan tidak kena cache browser
        $base    = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $base    = preg_replace('/[^A-Za-z0-9_-]+/', '_', $base) ?: 'profile';
        $picname = $base . '_' . date('YmdHis') . '.' . $ext;
        $target  = base_path('images/user');

        try {
            if (!is_dir($target)) {
                mkdir($target, 0775, true);
            }
            $file->move($target, $picname);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['status' => 'Failed', 'pesan' => __('admin/account.upload_failed_detail', ['message' => $e->getMessage()])]);
        }

        return response()->json([
            'status'  => 'OK',
            'pesan'   => __('common.upload_done', ['name' => $picname]),
            'url'     => url('images/user/' . $picname),
            'picname' => $picname,
        ]);
    }
    public function updateprofile(Request $request)
    {
        $name       = $request->name;
        $telp       = $request->handphone;
        $images      = $request->labelimage;
        $email      = $this->sessionEmail();
        if ($email === '') {
            return $this->forbidden();
        }
        $data = array(
            'name' => $name,
            'handphone' => $telp,
        );

        // Foto: labelimage berisi nama file hasil savepic atau URL lama.
        // Kosong -> foto yang tersimpan tidak diubah (dulu tersimpan '.../images/user/' tanpa nama file).
        $images = trim((string) $images);
        $image  = null;
        if ($images !== '') {
            $image = filter_var($images, FILTER_VALIDATE_URL) ? $images : url('images/user/' . basename($images));
            $data['pict'] = $image;
        }
        // hanya akun administrator: email yang sama bisa juga dipakai akun tenant
        $criteria = array('email' => $email, 'tableforeign' => 'administrator');

        // Ganti email (opsional): dicek dulu sebelum ada yang disimpan
        $newEmail = trim((string) $request->email);
        $emailChanged = $newEmail !== '' && strcasecmp($newEmail, $email) !== 0;
        if ($emailChanged && ($error = $this->emailChangeError($email, $newEmail, (string) $request->current_password))) {
            return response()->json(['status' => 'Failed', 'pesan' => $error]);
        }

        try {
            
                DB::connection('ifcaadm')
                    ->table('mgr.all_login')
                    ->where($criteria)
                    ->update($data);

                // header memakai nilai dari session
                Session::put('Tsdisplay_name', $name);
                if ($image !== null) {
                    Session::put('Tspict', $image);
                }

                // dicentang di form: akun tenant dengan email yang sama ikut pindah ke email baru
                $withTenant = $emailChanged && $request->boolean('change_tenant_email')
                    && DB::connection('ifcaadm')->table('mgr.tenant')->where('email', $email)->exists();
                if ($emailChanged) {
                    $this->changeEmail($email, $newEmail, $withTenant);
                }

                $msg = $emailChanged ? __('admin/account.email_changed', ['email' => $newEmail]) : __('common.updated');
                if ($withTenant) {
                    $msg .= ' ' . __('admin/account.email_changed_tenant');
                }
                $st  = 'OK';
             
        } catch(\Illuminate\Database\QueryException $ex){ 
            report($ex);
            $msg = __('common.save_failed', ['message' => $ex->getMessage()]);
            $st  = 'Failed';
        }
        return response()->json([
            'status' => $st,
            'pesan' => $msg,
            'email' => ($st === 'OK' && $emailChanged) ? $newEmail : null,
        ]);
    }

    /**
     * Alasan email admin tidak bisa diganti, atau null kalau boleh: format valid, belum dipakai
     * akun lain (all_login / tenant), dan password saat ini benar.
     */
    private function emailChangeError($email, $newEmail, $password)
    {
        $db = DB::connection('ifcaadm');
        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL) || strlen($newEmail) > 100) {
            return __('admin/account.email_invalid');
        }
        $taken = $db->table('mgr.all_login')->where('email', $newEmail)->exists()
            || $db->table('mgr.tenant')->where('email', $newEmail)->exists();
        if ($taken) {
            return __('admin/account.email_taken');
        }
        $hashes = $db->table('mgr.all_login')->where('email', $email)->where('tableforeign', 'administrator')->pluck('password');
        if ($password === '' || !$hashes->contains(function ($hash) use ($password) { return Password::check($password, $hash); })) {
            return __('admin/account.current_password_wrong');
        }
        return null;
    }

    /**
     * Pindahkan akun admin ke email baru. $withTenant = false: hanya baris all_login administrator,
     * akun tenant dengan email lama tetap (email tenant diatur dari sinkron IFCA). $withTenant = true
     * (dicentang di form): akun tenant dengan email lama ikut pindah (all_login tenant, mgr.tenant,
     * responden survey). Email di IFCA perlu diubah juga, kalau tidak sinkron berikutnya
     * mengembalikannya (dan mereset password tenant).
     */
    private function changeEmail($email, $newEmail, $withTenant = false)
    {
        $db = DB::connection('ifcaadm');
        $db->transaction(function () use ($db, $email, $newEmail, $withTenant) {
            $db->table('mgr.all_login')->where('email', $email)->where('tableforeign', 'administrator')->update(['email' => $newEmail]);
            if ($withTenant) {
                $db->table('mgr.all_login')->where('email', $email)->where('tableforeign', 'tenant')->update(['email' => $newEmail]);
                $db->table('mgr.tenant')->where('email', $email)->update(['email' => $newEmail]);
                $db->table('mgr.survey_respondents')->where('email', $email)->update(['email' => $newEmail]);
            }
            // user_locale memakai email huruf kecil (App\Support\UserLocale::key). Kalau email lama
            // masih dipakai akun tenant, pilihan bahasanya disalin (bukan dipindah).
            $locale = $db->table('mgr.user_locale')->where('email', strtolower($email))->first();
            if ($locale && !$db->table('mgr.user_locale')->where('email', strtolower($newEmail))->exists()) {
                if ($db->table('mgr.all_login')->where('email', $email)->exists()) {
                    $db->table('mgr.user_locale')->insert(array_merge((array) $locale, ['email' => strtolower($newEmail)]));
                } else {
                    $db->table('mgr.user_locale')->where('email', strtolower($email))->update(['email' => strtolower($newEmail)]);
                }
            }
        });

        \Illuminate\Support\Facades\Log::info('Email admin diganti', ['dari' => $email, 'ke' => $newEmail, 'akun_tenant_ikut' => $withTenant]);

        Session::put('Tsemail', $newEmail);
        if (strcasecmp((string) Session::get('login_email'), $email) === 0) {
            Session::put('login_email', strtolower($newEmail));
        }
        // menu pindah portal (PortalLoginController::switchAdmin) memakai email ini
        $portals = Session::get('portals', array());
        if (!empty($portals['admin'])) {
            $portals['admin']['email'] = $newEmail;
            Session::put('portals', $portals);
        }
    }

    public function changepass(Request $request)
    {
        $password = Password::make($request->password);
        $data = array(
            'password' => $password
        );
        // akun sendiri (email dari sesi, bukan dari form)
        $criteria = array('email' => $this->sessionEmail());
        if ($criteria['email'] === '') {
            return $this->forbidden();
        }
        try {
            
                DB::connection('ifcaadm')
                    ->table('mgr.all_login')
                    ->where($criteria)
                    ->update($data);
                
                $msg = __('common.updated');
                $st  = 'OK';
             
        } catch(\Illuminate\Database\QueryException $ex){ 
            report($ex);
            $msg = __('common.save_failed', ['message' => $ex->getMessage()]);
            $st  = 'Failed';
        }
        return response()->json([
            'status' => $st,
            'pesan' => $msg
        ]);
    }
    public function resetpass(Request $request)
    {
        // dari tabel defaultpassword (menu System Spec -> Default Password)
        $password_default = DefaultPassword::get();
        $password = Password::make($password_default);
        
        $emailsend = $request->email;
        $subj = "Replacement login information for ". $request->name;
        $body ="";
        $body.='<h3>Dear '.$request->name.', '."</h3>";
        $body.='A request to reset the password for your account has been made at TWP.'."<br>";
        $body.='Your new password is '.$password_default.". <br><br>";
        $body.='TWP System,<br>';
        $body.='Administrator';
        try { 
            $criteria = array('email' => $request->email);
            $data = array(
                'password' => $password
            );
                DB::connection('ifcaadm')
                    ->table('mgr.all_login')
                    ->where($criteria)
                    ->update($data);
                $msg = __('common.updated');
                $st  = 'OK';
             
        } catch(\Illuminate\Database\QueryException $ex){ 
            report($ex);
            $msg = __('common.save_failed', ['message' => $ex->getMessage()]);
            $st  = 'Failed';
        }
        return response()->json([
            'status' => $st,
            'pesan' => $msg
        ]);
    }

}
