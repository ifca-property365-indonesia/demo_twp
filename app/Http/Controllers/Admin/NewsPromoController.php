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
            $jdl  = 'Add News and Promo';
            $form ='add';
        } else if($type == 'E') {
            $jdl  = 'Edit News and Promo';
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
                $msg = "Maximum file size is 5MB";
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
                $msg = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = 0;
                $psn = 'failed';
                $res = array("pesan" => $msg, "status" => $psn);

                echo json_encode($res);
                exit();
            }
            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                $msg = "Sorry, your file was not uploaded.";
                $psn = "Failed";
                // if everything is ok, try to upload file
            } else {
                if (move_uploaded_file($_FILES["userfile"]["tmp_name"], $target_file)) {
                    $msg = "The file " . basename($_FILES["userfile"]["name"]) . " has been uploaded.";
                    $psn = "OK";
                    $descs = "/images/newspromo/" . $picname;
                    $url = url('/admin') . $descs;
                } else {
                    $msg = "Sorry, there was an error uploading your file.";
                    $psn = "Failed";
                }
            }
        } else {
            $msg = "Sorry, there was an error uploading your file.";
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
                
                $msg = "Data has been updated successfully";
                $st  = 'OK';
                
            } else {//create
               
                DB::connection('ifcaadm')
                    ->table('newsfeed')
                    ->insert($data);
                
                $msg = "Data has been saved successfully";
                $st = 'OK';
                
                
            }
        } catch(\Illuminate\Database\QueryException $ex){ 
            $msg = "Save failed: " . $ex->getMessage();
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
            $msg = "Data has been deleted successfully";
            $st  = 'OK';
        } catch(\Illuminate\Database\QueryException $ex){ 
            $msg = "Delete failed: " . $ex->getMessage();
            $st  = 'Fail';
        }
        return response()->json([
            'status' => $st,
            'pesan' => $msg
        ]);
    }
}
