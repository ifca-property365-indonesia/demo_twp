<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\DefaultPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WsbangunController extends Controller
{
    public function update_ticket(Request $request){
        $par = $request->params;
        $par = explode(':',$par);
        $entity = $par[0]; 
        $project = $par[1]; 
        $debtor = $par[2]; 
        $complain_no = $par[3]; 
        $status = $par[4]; 
        $data = array(
            'status'=>$status
        );
        $crit1 = array(
            'entity_cd'=>$entity,
            'project_no'=>$project,
            'tenant_no'=>$debtor,
            'complain_no'=>$complain_no
        );
        try{
            DB::connection('ifcaadm')
                ->table('sv_entry_multi')
                ->where($crit1)
                ->update($data);
            $feedback = "Data has been updated successfully";
        } catch(\Illuminate\Database\QueryException $ex){ 
         
            $feedback = "Insert failed: " . $ex->getMessage();
        }

        echo $feedback;
        
    }

    public function business($method='', $value='')
    {
        if(!empty($method)&&!empty($value))
        {
            $method = strtolower($method);
            $list_value = explode(":",$value);
            switch ($method) {
                case 'insert':
                    $content = array(
                        'entity_cd'=>$list_value[0],
                        'project_no'=>$list_value[1],
                        'tenant_no'=>$list_value[2]
                    );
                    $this->new_business($content);
                    break;
                case 'delete':
                    $content = array(
                        'entity_cd'=>$list_value[0],
                        'project_no'=>$list_value[1],
                        'tenant_no'=>$list_value[2]
                    );
                    $this->del_business($content);
                    break;
                case 'edit':
                    $content = array(
                        'entity_cd'=>$list_value[0],
                        'project_no'=>$list_value[1],
                        'business_id'=>$list_value[2]
                    );
                    $this->edit_business($content);
                    break;
            }
        }
    }
    public function new_business($value = "")
    {
        $entity = $value["entity_cd"];
        $project = $value["project_no"];
        $tenant_no = $value["tenant_no"];

        try {
            $result = DB::connection('ifcapb')->select("SELECT * FROM mgr.v_tenant_login WHERE entity_cd='$entity' AND RTRIM(project_no)='$project' AND debtor_acct='$tenant_no'");

            if (!empty($result)) {

                $id_business = $result[0]->business_id;
                $crit1 = array('business_no' => $id_business);

                $check_tenant = DB::connection('ifcaadm')
                    ->table('tenant')
                    ->where($crit1)
                    ->get();

                if (empty($check_tenant) || count($check_tenant) == 0) {

                    $data1 = array(
                        'name'            => $result[0]->name,
                        'phone'           => $result[0]->tel_no,
                        'contact_name'    => $result[0]->contact_person,
                        'contact_mobile'  => $result[0]->hand_phone,
                        'address'         => $result[0]->address1 . ' ' . $result[0]->address2 . ' ' . $result[0]->address3 . ' ' . $result[0]->post_cd,
                        'email'           => trim($result[0]->email_addr),
                        'status'          => 1,
                        'business_no'     => $result[0]->business_id,
                        'tenant_no_df'    => $tenant_no,
                        'flag'            => 'O'
                    );

                    DB::connection('ifcaadm')
                        ->table('tenant')
                        ->insert($data1);

                    if (
                        isset($result[0]) &&
                        property_exists($result[0], 'email_addr_fin') &&
                        !is_null($result[0]->email_addr_fin) &&
                        trim($result[0]->email_addr_fin) !== ''
                    ) {
                        $dataFinance = $data1;
                        $dataFinance['email'] = trim($result[0]->email_addr_fin);
                        $dataFinance['flag'] = 'F';

                        DB::connection('ifcaadm')
                            ->table('tenant')
                            ->insert($dataFinance);
                    }

                    // Ambil seluruh tenant (Owner & Finance)
                    $data_tenant = DB::connection('ifcaadm')
                        ->table('tenant')
                        ->where($crit1)
                        ->orderBy('id')
                        ->get();

                    // ID Owner untuk pm_tenancy
                    $id_tenant = $data_tenant[0]->id;

                    // password awal akun tenant baru: tabel defaultpassword (menu Default Password)
                    $password = DefaultPassword::hash();

                    foreach ($data_tenant as $tenant) {

                        $exist = DB::connection('ifcaadm')
                            ->table('all_login')
                            ->where('tableforeign', 'tenant')
                            ->where('idforeign', $tenant->id)
                            ->exists();

                        if (!$exist) {
                            DB::connection('ifcaadm')
                                ->table('all_login')
                                ->insert([
                                    'name'         => $tenant->name,
                                    'email'        => $tenant->email,
                                    'password'     => $password,
                                    'tableforeign' => 'tenant',
                                    'idforeign'    => $tenant->id
                                ]);
                        }
                    }

                    $crit2 = array('tenant_no' => $tenant_no);

                    $cnt_tenancy = DB::connection('ifcaadm')
                        ->table('pm_tenancy')
                        ->where($crit2)
                        ->get();

                    if (count($cnt_tenancy) < 1) {

                        if (
                            is_null($result[0]->contract_date) ||
                            is_null($result[0]->commence_date) ||
                            is_null($result[0]->expiry_date)
                        ) {
                            $contract_date = $result[0]->contract_date;
                            $commence_date = $result[0]->commence_date;
                            $expiry_date = $result[0]->expiry_date;
                        } else {
                            $contract_date = date('Y-m-d', strtotime($result[0]->contract_date));
                            $commence_date = date('Y-m-d', strtotime($result[0]->commence_date));
                            $expiry_date = date('Y-m-d', strtotime($result[0]->expiry_date));
                        }

                        $entity_desc = $result[0]->entity_desc;
                        $project_desc = $result[0]->project_desc;

                        $data3 = array(
                            'id'             => $id_tenant,
                            'business_no'    => $id_business,
                            'tenant_no'      => $tenant_no,
                            'entity_cd'      => $entity,
                            'project_no'     => $project,
                            'contract_date'  => $contract_date,
                            'commence_date'  => $commence_date,
                            'expiry_date'    => $expiry_date,
                            'entity_desc'    => $entity_desc,
                            'project_desc'   => $project_desc,
                            'status'         => 'A'
                        );

                        DB::connection('ifcaadm')
                            ->table('pm_tenancy')
                            ->insert($data3);
                    }
                }

                $feedback = json_encode($result);

            } else {
                $feedback = 'Bad request';
            }

        } catch (\Illuminate\Database\QueryException $ex) {

            $feedback = "Insert failed: " . $ex->getMessage();
        }

        echo $feedback;
    }
    public function edit_business($value="")
    {
        $entity = $value["entity_cd"];
        $project = $value["project_no"];
        $business_no = $value["business_id"];
        try{
            $result = DB::connection('ifcapb')->select("SELECT * FROM mgr.tenant_login WHERE business_id='$business_no'");
            if(!empty($result)||count($result)>0) {
                $tenant_no = $result[0]->debtor_acct;
                $crit1 = array('business_no'=>$business_no);
                $check_tenant = DB::connection('ifcaadm')->table('tenant')->where($crit1)->get();
                if(!empty($check_tenant)) {
                    $data = array(
                        'name'=>$result[0]->name,
                        'email'=>$result[0]->email_addr,
                        'phone'=>$result[0]->tel_no,
                        'contact_name'=>$result[0]->contact_person,
                        'contact_mobile'=>$result[0]->hand_phone
                    );
                    $id_tenant = $check_tenant[0]->id;
                    DB::connection('ifcaadm')
                    ->table('tenant')
                    ->where($crit1)
                    ->update($data);

                    $crit2 = array(
                        'idforeign'=>$id_tenant,
                        'tableforeign'=>'tenant'
                    );
                    $cnt_login = DB::connection('ifcaadm')->table('all_login')->where($crit2)->get();
                    if(count($cnt_login)<1) {
                        $data2 = array(
                            'name'=>$result[0]->name,
                            'email'=>$result[0]->email_addr,
                            'password'=>DefaultPassword::hash(),
                            'tableforeign'=>'tenant',
                            'idforeign'=>$id_tenant
                        );
                        DB::connection('ifcaadm')
                        ->table('all_login')
                        ->insert($data2);
                    } else {
                        $data2 = array('email'=>$result[0]->email_addr);
                        DB::connection('ifcaadm')
                            ->table('all_login')
                            ->where($crit2)
                            ->update($data2);
                    }
                    $feedback = 'Tenant id: '. $id_tenant.' data updated!';
                }
            } else {
                $feedback = 'Bad request';
            }
        } catch(\Illuminate\Database\QueryException $ex){ 
            $feedback = "Update failed: " . $ex->getMessage();
        }
        echo $feedback;
        
    }
    public function del_business($value="")
    {
        try{
            $check_tenancy = DB::connection('ifcaadm')->table('pm_tenancy')->where($value)->get();
            if(!empty($check_tenancy)) {
                $business_no = $check_tenancy[0]->business_no;
                $tenant_no = $check_tenancy[0]->tenant_no;
                $crit1 = array('business_no'=>$business_no);
                $cnt_tenancy = DB::connection('ifcaadm')->table('pm_tenancy')->where($crit1)->get();
                if(count($cnt_tenancy)==1) {
                    $data = array('status'=>0);
                    $data_tenant = DB::connection('ifcaadm')->table('tenant')->where($crit1)->get();
                    $id_tenant = $data_tenant[0]->id;
                    $crit2 = array(
                        'idforeign'=>$id_tenant,
                        'tableforeign'=>'tenant'
                    );
                    DB::connection('ifcaadm')
                        ->table('tenant')
                        ->where($crit1)
                        ->delete();
                    DB::connection('ifcaadm')
                        ->table('all_login')
                        ->where($crit2)
                        ->delete();
                }
                DB::connection('ifcaadm')
                        ->table('pm_tenancy')
                        ->where($value)
                        ->delete();
                $feedback='Tenant no : '. $tenant_no . ' deleted!';
            }
        } catch(\Illuminate\Database\QueryException $ex){ 
            $feedback = "Delete failed: " . $ex->getMessage();
        }
        echo $feedback;
    }
}
