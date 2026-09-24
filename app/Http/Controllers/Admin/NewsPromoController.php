<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;

class NewsPromoController extends Controller
{
    public function getTable()
    {
        $query = DB::connection('ifcaadm')->select("SELECT @rownum := @rownum + 1 AS row_number, t.* FROM newsfeed t, (SELECT @rownum := 0) r");
        return DataTables::of($query)->make(true);
    }
    public function addform($type='',$id=0)
    {
        if($type == 'A')
        {
            $jdl  = __('admin/news.add_title');
            $form ='add';
        } else if($type == 'E') {
            $jdl  = __('admin/news.edit_title');
            $form ='edit';
        }

        $content = array(
            'id' => $id,
            'jdl'=> $jdl,
            'form'=>$form
        );
        return view('admin.news.add', $content);
    }
    public function savePic()
    {
        $picture = !empty($_FILES) ? $picture = $_FILES["userfile"] : '';
        if (!empty($picture["name"])) {
            $picname = str_replace(' ', '_', $picture["name"]);
            $picture = $_FILES["userfile"];
           
            $psn = '';$url='';
            $msg = '';
            $picture = array_filter($picture);

            $target_dir = "./images/newspromo/";
            
            if (!is_dir($target_dir)) {
                mkdir($target_dir);
            }
            $target_file = $target_dir . str_replace(' ', '_', basename($_FILES["userfile"]["name"]));
            $uploadOk = 1;
            $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);

            if ($_FILES["userfile"]["size"] > 5000000) {
                $msg = __('common.upload_max_size', ['size' => '5MB']);
                $uploadOk = 0;
                $psn = 'failed';
                $res = array("pesan" => $msg, "status" => $psn);

                echo json_encode($res);
                exit();
            }

            $imageFileType = strtolower($imageFileType);
            // Allow certain file formats
            if (
                $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                && $imageFileType != "gif" && $imageFileType != "JPG"
            ) {
                $msg = __('common.upload_only_image');
                $uploadOk = 0;
                $psn = 'failed';
                $res = array("pesan" => $msg, "status" => $psn);

                echo json_encode($res);
                exit();
            }
            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                $msg = __('common.upload_not_saved');
                $psn = "Failed";
                // if everything is ok, try to upload file
            } else {
                if (move_uploaded_file($_FILES["userfile"]["tmp_name"], $target_file)) {
                    $msg = __('common.upload_done', ['name' => basename($_FILES["userfile"]["name"])]);
                    $psn = "OK";
                    $descs = "/images/newspromo/" . $picname;
                    $url = url('/admin') . $descs;
                } else {
                    $msg = __('common.upload_error');
                    $psn = "Failed";
                }
            }
        } else {
            $msg = __('common.upload_error');
            $psn = "Failed";
        }

        $res = array(
            'pesan' => $msg,
            'status' => $psn,
            'url' => $url,
            'picname' => $picname,
        );
        echo json_encode($res);
    }
    public function getByID($id = '')
    {
        $where = array('id' => $id);
        $data = DB::connection('ifcaadm')
            ->table('newsfeed')
            ->where($where)
            ->get();
        echo json_encode($data);
    }
    public function save(Request $request)
    {
        $msg            = '';
        $id             = $request->id;
        $start_date = explode('/', $request->start_date);
        $start_date = $start_date[2].'-'.$start_date[1].'-'.$start_date[0].' 00:00:00';

        $end_date = explode('/', $request->end_date);
        $end_date = $end_date[2].'-'.$end_date[1].'-'.$end_date[0].' 23:59:59';
        $data = array(
            'content_type'         => $request->content_type,
            'subject'  => $request->news_title,
            'content'   => $request->news_descs,
            'status'   => 1,
            'attach_type' => $request->attach_type,
            'youtube_link' => $request->youtubelink,
            'picture'     => $request->picturepath,
            'date_created'    => date('Y-m-d H:i:s'),
            'start_date' => $start_date,
            'end_date' => $end_date,
        );

        $criteria = array('id' => $id);
        try { 
            if ($id > 0) { //update
                unset($data['date_created']);
                DB::connection('ifcaadm')
                    ->table('newsfeed')
                    ->where($criteria)
                    ->update($data);
                
                $msg = __('common.updated');
                $st  = 'OK';
                
            } else {//create
               
                DB::connection('ifcaadm')
                    ->table('newsfeed')
                    ->insert($data);
                
                $msg = __('common.saved');
                $st = 'OK';
                
                
            }
        } catch(\Illuminate\Database\QueryException $ex){ 
            $msg = __('common.save_failed', ['message' => $ex->getMessage()]);
            $st  = 'Failed';
        }
        return response()->json([
            'status' => $st,
            'pesan' => $msg
        ]);
    }
    public function delete(Request $request)
    {
       
        $criteria = array('id' => $request->id);
        try { 
            DB::connection('ifcaadm')
            ->table('newsfeed')
            ->where($criteria)
            ->delete();
            $msg = __('common.deleted');
            $st  = 'OK';
        } catch(\Illuminate\Database\QueryException $ex){ 
            $msg = __('common.delete_failed', ['message' => $ex->getMessage()]);
            $st  = 'Fail';
        }
        return response()->json([
            'status' => $st,
            'pesan' => $msg
        ]);
    }
}
