<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Support\Password;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AccountController extends Controller
{
    public function getbyemail($email)
    {
        $data = DB::select("SELECT * from all_login where email='$email'");
        echo json_encode($data);
    }
    /**
     * Unggah foto profil ke img/user/ (dipanggil dari modal profil setelah foto dipotong).
     * Balasan JSON: status OK|Failed, pesan, url (absolut), picname.
     */
    public function savepic(Request $request)
    {
        $file = $request->file('userfile');

        if (!$file) {
            // $_FILES kosong: tidak ada file, atau melebihi post_max_size / upload_max_filesize
            return response()->json(['status' => 'Failed', 'pesan' => 'No file received (check upload size limit).']);
        }
        if (!$file->isValid()) {
            return response()->json(['status' => 'Failed', 'pesan' => 'Upload error: ' . $file->getErrorMessage()]);
        }
        if ($file->getSize() > 5000000) {
            return response()->json(['status' => 'Failed', 'pesan' => 'Maximum file size is 5MB']);
        }

        $ext = strtolower($file->getClientOriginalExtension() ?: $file->extension());
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'], true)) {
            return response()->json(['status' => 'Failed', 'pesan' => 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.']);
        }

        // nama unik supaya tidak menimpa file lain dan tidak kena cache browser
        $base    = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $base    = preg_replace('/[^A-Za-z0-9_-]+/', '_', $base) ?: 'profile';
        $picname = $base . '_' . date('YmdHis') . '.' . $ext;
        $target  = base_path('img/user');

        try {
            if (!is_dir($target)) {
                mkdir($target, 0775, true);
            }
            $file->move($target, $picname);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'Failed', 'pesan' => 'Sorry, there was an error uploading your file: ' . $e->getMessage()]);
        }

        return response()->json([
            'status'  => 'OK',
            'pesan'   => 'The file ' . $picname . ' has been uploaded.',
            'url'     => url('img/user/' . $picname),
            'picname' => $picname,
        ]);
    }
    public function updateprofile(Request $request)
    {
        $name       = $request->name;
        $telp       = $request->handphone;
        $images      = $request->labelimage;
        $email      = $request->email;
        
        $data = array(
            'name' => $name,
            'handphone' => $telp,
        );

        // Foto: labelimage berisi nama file hasil savepic atau URL lama.
        // Kosong -> foto yang tersimpan tidak diubah (dulu tersimpan '.../img/user/' tanpa nama file).
        $images = trim((string) $images);
        $image  = null;
        if ($images !== '') {
            $image = filter_var($images, FILTER_VALIDATE_URL) ? $images : url('img/user/' . basename($images));
            $data['pict'] = $image;
        }
        $criteria = array('email' => $email);

        
        try { 
            
                DB::table('all_login')
                    ->where($criteria)
                    ->update($data);

                // header memakai nilai dari session
                if ($email === Session::get('Tenemail')) {
                    Session::put('Tdisplay_name', $name);
                    if ($image !== null) {
                        Session::put('Tpict', $image);
                    }
                }
                
                $msg = "Data has been updated successfully";
                $st  = 'OK';
             
        } catch(\Illuminate\Database\QueryException $ex){ 
            $msg = "Save failed: " . $ex->getMessage();
            $st  = 'Failed';
        }
        return response()->json([
            'status' => $st,
            'pesan' => $msg
        ]);
    }
    public function changepass(Request $request)
    {
        $password = Password::make($request->password);
        $data = array(
            'password' => $password
        );
        $criteria = array('email' => $request->email);

        try { 
            
                DB::table('all_login')
                    ->where($criteria)
                    ->update($data);
                
                $msg = "Data has been updated successfully";
                $st  = 'OK';
             
        } catch(\Illuminate\Database\QueryException $ex){ 
            $msg = "Save failed: " . $ex->getMessage();
            $st  = 'Failed';
        }
        return response()->json([
            'status' => $st,
            'pesan' => $msg
        ]);
    }
}
