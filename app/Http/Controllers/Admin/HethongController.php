<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\updateRoleGroup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Exception;
class HethongController extends Controller
{
    protected function message()
    {
        $level = Auth::user()->level;
        $thamdinh = [];
        if((int)$level==1){
          $table = DB::table('qlhs_pheduyet as  td')->join('qlhs_pheduyettonghop as hs','td.pheduyet_hoso_thamdinh' ,'=', 'hs.pheduyettonghop_id')->where('pheduyet_view','=',0)->where('pheduyet_trangthai','=',0);
          $thamdinh['count'] = $table->count();
             $items = array();
             $js = [];
          foreach ($table->select('pheduyet_ngaygui','pheduyettonghop_name','type','pheduyet_id')->get() as $key => $value) {
            $item['pheduyet_ngaygui'] =  Carbon::parse($value->pheduyet_ngaygui )->diffForHumans();
            $item['pheduyettonghop_name'] = $value->pheduyettonghop_name;
            $item['type'] = $value->type;
            $item['pheduyet_id'] = $value->pheduyet_id;
            $js[] = $item;
          }
          $thamdinh['content'] = $js;
          return $thamdinh;
        }else{
          return 1;
        }
        
    }
    protected function changePasswordUser(Request $response)
    {
        // Get the currently authenticated user...
        $user = Auth::user();
           return view('layouts.change_password',['category' => $user]);
    }
    public function changeProfile(Request $request){
      $status = [];
      try{
        $user = Auth::user();
        $txtLastName = $request->input("txtLastName");
        $txtFirstName = $request->input("txtFirstName");
        $txtEmail = $request->input("txtEmail");
        $now = Carbon::now('Asia/Ho_Chi_Minh');
       // $kinhphidoituong = KinhPhiDoiTuong::find($id);
        $user->first_name=$txtFirstName;
        $user->last_name=$txtLastName;
        $user->email=$txtEmail;
        $user->updated_at=$now;
        $user->save();
        $status['success'] = "Thay đổi thành công.";
      }catch(\Exception $e){
        $status['error'] = $e;
      }
      return $status;
    }
    public function changePass(Request $request){
      $status = [];
      try{
          $txtPassOld = $request->input("txtPassOld");
          $txtPassNew = $request->input("txtPassNew");
          $txtRePassNew = $request->input("txtRePassNew");
          $now = Carbon::now('Asia/Ho_Chi_Minh');
          $user = User::find(Auth::user()->id);
          
          if(Hash::check($txtPassOld,$user->password) && $txtPassNew = $txtRePassNew){
            $user->password = bcrypt($txtRePassNew);
            $user->updated_at=$now;
            $user->save();
            $status['success'] = "Thay đổi thành công.";
          }else {
            $status['error'] = "Mật khẩu hiện tại không đúng.";
          }
        }catch(\Exception $e){
          $status['error'] = $e;
        }
        return $status;
    }
    public function getGroupList()
    {
        $list = DB::table('roles')->paginate(5);
        return view('admin.hethong.group.listRoleGroup',['list'=>$list]);
    }
    public function getUserList()
    {
        $list = DB::table('users')->paginate(5);
        return view('admin.hethong.user.listUsers',['list'=>$list]);
    }
    public function getconfigGroupRole($id)
    {
      
      $arrays = [];
        $array =  DB::table('permission_role')->where('role_id','=',$id)->get(); 
        $i = 0;
        $featureId = 0;
        foreach($array as $key => $value)
          {
            $i ++;
            $arrayget = [];
            if($value->permission_id == 1){
                $arrayget['add'] = 'x';
            }
            // else{
            //     $arrayget['add'] = 'o';
            // }
            if($value->permission_id == 2){
               $arrayget['update'] = 'x';
            }
            if($value->permission_id == 3){
               $arrayget['delete'] = 'x';
            }
            if($value->permission_id == 4){
               $arrayget['business'] = 'x';
            }
            if($value->permission_id == 5){
               $arrayget['get'] = 'x';
            }
            // else{
            //     $arrayget['get'] = 'o';
            // }
              $arrayget['featureId'] = $value->module_id ;
           
           $arrays[] = ($arrayget);
            //$arrays = array_add($array,$key,$value);
            
          } 
        return $arrays;
    }
    public function getconfigUserRole($id)
    {
      $arrays = [];
        $array =  DB::table('permission_users')->where('role_user_id','=',$id)->get();
        $i = 0;
        $featureId = 0;
        foreach($array as $key => $value)
          {
            $i ++;
            $arrayget = [];
            if($value->permission_id == 1){
                $arrayget['add'] = 'x';
            }
            // else{
            //     $arrayget['add'] = 'o';
            // }
            if($value->permission_id == 2){
               $arrayget['update'] = 'x';
            }
            if($value->permission_id == 3){
               $arrayget['delete'] = 'x';
            }
            if($value->permission_id == 4){
               $arrayget['business'] = 'x';
            }
            if($value->permission_id == 5){
               $arrayget['get'] = 'x';
            }
            // else{
            //     $arrayget['get'] = 'o';
            // }
              $arrayget['featureId'] = $value->module_id ;
 

           $arrays[] = ($arrayget);
            //$arrays = array_add($array,$key,$value);
            
          }
        return $arrays;
    }
    public function updateGroupRole(Request $request){
      //if ($request->isMethod('post')) {

            $error;
            $user = Auth::user()->id;
            $roleName = $request->input('roleName');
            $roleCode = $request->input('roleCode');
            $desciption = $request->input('desciption');
            $adRoleId = $request->input('adRoleId');
            $results = DB::update("update roles set name = '$roleCode', display_name = '$roleName', description = '$desciption',updated_at = now(),updated_user='$user' where id = '$adRoleId'");
            if ($results >= 0) {
                $error = 'Sửa thành công!';
            }
            else {
                $error = 'Lỗi!';
            }    
            return $results;
        
       
    }
    public function insertGroupRole(Request $request){
      //if ($request->isMethod('post')) 
            $json;
            $user = Auth::user()->id;
            $roleName = $request->input('roleName');
            $roleCode = $request->input('roleCode');
            $desciption = $request->input('desciption');
            $adRoleId = $request->input('adRoleId');
            $results = DB::update("insert into roles(name,display_name,description,created_at,updated_at,created_user,updated_user)  values('$roleCode','$roleName','$desciption',now(),now(),$user,$user)");
            if ($results >= 0) {
                $json['success'] = 'Thêm mới thành công!';
            }
            else {
                $json['error'] = 'Thêm mới lỗi!';
            }    
            return $json;
    }
    public function getUserInfo($id){

            $results = DB::table('users')->select('id','username','first_name','last_name','email','activated')->where('id','=',$id)->get(); 
            return $results;
        
       
    }

    public function getRoleGroup(){

            $results = DB::table('roles')->select('name','display_name','id')->where('active','=',1)->get(); 
            return $results;
        
       
    }

    public function delGroupRole($id){
      $json = [];
            $results = DB::table('roles')->where('id','=',$id)->delete();
            if($results>0){
      $json['success'] = "Xóa bản ghi thành công";
            } else{
      $json['error'] = "Xóa bản ghi lỗi";
            }
            return $json;
    }

    public function configUserRole(Request $request){
      //if ($request->isMethod('post')) {
      $arrayget = []; 
      $arrayadd = []; 
      $arrayupdate = []; 
      $arraydelete = []; 
      $arraybusiness = [];

      $get = $request->input('get');
      $add = $request->input('add');
      $update = $request->input('update');
      $delete = $request->input('delete');
      $business = $request->input('business');
     // $rules = [];
      $roleId = $request->input('UserId');
      $role = $request->input('sltRoleGroup');

      // return $roleId;
      
      if($role !=''){
        DB::table('users')->where('id','=',$roleId)->update(['nhomquyen_id' => $role]);
      }else{
        DB::table('users')->where('id','=',$roleId)->update(['nhomquyen_id' => null]);
      }
      DB::table('permission_users')->where('role_user_id','=',$roleId)->delete();    
      if(count($get) > 0 ){
        if(count($get) == 1){
              $arrayget['role_user_id'] = $roleId; 
              $arrayget['module_id'] = $get; 
              $arrayget['permission_id'] = 5; 
              DB::table('permission_users')->insert($arrayget);  
        }else{
          foreach($get as $key => $value)
          {
              $arrayget['role_user_id'] = $roleId; 
              $arrayget['module_id'] = $value; 
              $arrayget['permission_id'] = 5; 
              DB::table('permission_users')->insert($arrayget);     
          }
        }
      }
      if(count($add) > 0 ){
        if(count($add) == 1){
              $arrayget['role_user_id'] = $roleId; 
              $arrayget['module_id'] = $add; 
              $arrayget['permission_id'] = 1; 
              DB::table('permission_users')->insert($arrayget);  
        }else{
          foreach($add as $key => $value)
          {
              $arrayadd['role_user_id'] = $roleId; 
              $arrayadd['module_id'] = $value; 
              $arrayadd['permission_id'] = 1; 
              DB::table('permission_users')->insert($arrayadd);     
          }
        }
      }
      if(count($update) > 0 ){
        if(count($update) == 1 ){
              $arrayupdate['role_user_id'] = $roleId; 
              $arrayupdate['module_id'] = $update; 
              $arrayupdate['permission_id'] = 2; 
              DB::table('permission_users')->insert($arrayupdate);   
        }else{
          foreach($update as $key => $value)
          {
              $arrayupdate['role_user_id'] = $roleId; 
              $arrayupdate['module_id'] = $value; 
              $arrayupdate['permission_id'] = 2; 
              DB::table('permission_users')->insert($arrayupdate);     
          }
        }
      }
      if(count($delete) > 0 ){
        if(count($delete) == 1 ){
              $arraydelete['role_user_id'] = $roleId; 
              $arraydelete['module_id'] = $delete; 
              $arraydelete['permission_id'] = 3; 
              DB::table('permission_users')->insert($arraydelete);     
        }else{
          foreach($delete as $key => $value)
          {
              $arraydelete['role_user_id'] = $roleId; 
              $arraydelete['module_id'] = $value; 
              $arraydelete['permission_id'] = 3; 
              DB::table('permission_users')->insert($arraydelete);     
          }
        }
      }
      if(count($business) > 0 ){
        if(count($business) == 1 ){
              $arraybusiness['role_user_id'] = $roleId; 
              $arraybusiness['module_id'] = $business; 
              $arraybusiness['permission_id'] = 4; 
              DB::table('permission_users')->insert($arraybusiness);   
        }else{
          foreach($business as $key => $value)
          {
              $arraybusiness['role_user_id'] = $roleId; 
              $arraybusiness['module_id'] = $value; 
              $arraybusiness['permission_id'] = 4; 
              DB::table('permission_users')->insert($arraybusiness);     
          }
        }
      }
       
      return $arrayget;
    }

    public function configGroupRole(Request $request){
      //if ($request->isMethod('post')) {
      $arrayget = []; $arrayadd = []; $arrayupdate = []; $arraydelete = []; $arraybusiness = [];

      $get = $request->input('get');
      $add = $request->input('add');
      $update = $request->input('update');
      $delete = $request->input('delete');
      $business = $request->input('business');
     // $rules = [];
      $roleId = $request->input('roleId');
       DB::table('permission_role')->where('role_id','=',$roleId)->delete();    
      if(count($get) > 0 ){
        if(count($get) == 1){
              $arrayget['role_id'] = $roleId; 
              $arrayget['module_id'] = $get; 
              $arrayget['permission_id'] = 5; 
              DB::table('permission_role')->insert($arrayget);  
        }else{
          foreach($get as $key => $value)
          {
              $arrayget['role_id'] = $roleId; 
              $arrayget['module_id'] = $value; 
              $arrayget['permission_id'] = 5; 
              DB::table('permission_role')->insert($arrayget);     
          }
        }
      }
      if(count($add) > 0 ){
        if(count($add) == 1){
              $arrayget['role_id'] = $roleId; 
              $arrayget['module_id'] = $add; 
              $arrayget['permission_id'] = 1; 
              DB::table('permission_role')->insert($arrayget);  
        }else{
          foreach($add as $key => $value)
          {
              $arrayadd['role_id'] = $roleId; 
              $arrayadd['module_id'] = $value; 
              $arrayadd['permission_id'] = 1; 
              DB::table('permission_role')->insert($arrayadd);     
          }
        }
      }
      if(count($update) > 0 ){
        if(count($update) == 1 ){
              $arrayupdate['role_id'] = $roleId; 
              $arrayupdate['module_id'] = $update; 
              $arrayupdate['permission_id'] = 2; 
              DB::table('permission_role')->insert($arrayupdate);   
        }else{
          foreach($update as $key => $value)
          {
              $arrayupdate['role_id'] = $roleId; 
              $arrayupdate['module_id'] = $value; 
              $arrayupdate['permission_id'] = 2; 
              DB::table('permission_role')->insert($arrayupdate);     
          }
        }
      }
      if(count($delete) > 0 ){
        if(count($delete) == 1 ){
              $arraydelete['role_id'] = $roleId; 
              $arraydelete['module_id'] = $delete; 
              $arraydelete['permission_id'] = 3; 
              DB::table('permission_role')->insert($arraydelete);     
        }else{
          foreach($delete as $key => $value)
          {
              $arraydelete['role_id'] = $roleId; 
              $arraydelete['module_id'] = $value; 
              $arraydelete['permission_id'] = 3; 
              DB::table('permission_role')->insert($arraydelete);     
          }
        }
      }
      if(count($business) > 0 ){
        if(count($business) == 1 ){
              $arraybusiness['role_id'] = $roleId; 
              $arraybusiness['module_id'] = $business; 
              $arraybusiness['permission_id'] = 4; 
              DB::table('permission_role')->insert($arraybusiness);   
        }else{
          foreach($business as $key => $value)
          {
              $arraybusiness['role_id'] = $roleId; 
              $arraybusiness['module_id'] = $value; 
              $arraybusiness['permission_id'] = 4; 
              DB::table('permission_role')->insert($arraybusiness);     
          }
        }
      }
       
            return $arraybusiness;
        
       
    }
	public function addGet(){
      //  if($request->isMethod('get')){
        //    $category = DB::table('qlhs_modules')->select('module_id','module_name','module_path','module_parentid','module_icon')->get();
    //return view('admin.pages.blank');
            return view('admin.danhmuc.addCategory');
       // }
    }
	public function getBlank($category)
    {
    	return view('admin.pages.dashboard', ['category' => $category]);
        //return view('admin.pages.blank');
    }
    public function myFunction()
  	{
     return view('admin.pages.dashboard')->with('category', 'ssss');

  	}
    public function loadAllUser(Request $req){
      $json = [];
      $start = $req->input('start');
      $limit = $req->input('limit');
      $keysearch = $req->input('key');
      //$loadkinhphidoituong = [];
      $json['totalRows'] = DB::table('users as  u1')->count();
      $data = DB::table('users as  u1')->leftJoin('users as u2','u2.id' ,'=', 'u1.updated_userid')->select('u1.nhomquyen_id','u1.id','u1.username','u1.first_name','u1.last_name','u1.email','u1.activated','u1.updated_at','u1.updated_userid','u2.username as username_up');
      if($keysearch != null && $keysearch !=""){
        $json['data'] =  $data->where('u1.username','LIKE',"%".$keysearch."%")->orWhere("u1.first_name", "LIKE", "%".$keysearch."%")->orWhere("u1.last_name", "LIKE", "%".$keysearch."%")->orWhere("u1.email", "LIKE", "%".$keysearch."%")->orderBy('u1.updated_at', 'desc')->skip($start*$limit)->take($limit)->get();
      }else{
        $json['data'] =  $data->orderBy('u1.updated_at', 'desc')->skip($start*$limit)->take($limit)->get();

      }
      $json['startRecord'] = ($start);
      $json['numRows'] = $limit;
      // $datas;
        return $json;
    }
    public function loadListRole(Request $req){
      $json = [];
      $start = $req->input('start');
      $limit = $req->input('limit');
      $datas = DB::table('roles')->leftJoin('users','roles.updated_user','=','users.id')->select('roles.id','roles.name','roles.display_name','roles.description','roles.updated_at','roles.updated_user','users.username as username_up');
      $json['totalRows'] = $datas->count();
      $datas = $datas->orderBy('updated_at', 'desc')->skip($start*$limit)->take($limit)->get();
      $json['startRecord'] = ($start);
      $json['numRows'] = $limit;
      $json['data'] = $datas;
        return $json;
    }
    public function getgroupbyid($id){
      $datas = DB::table('roles')->select('roles.id','roles.name','roles.display_name','roles.description','roles.updated_at','roles.updated_user')->where('roles.id','=',$id)->get();
        return $datas;
    }
    public function getuserbyid($id){
      $datas = DB::table('users')->leftJoin('qlhs_department','department_id','=','phongban_id')->leftJoin('qlhs_schools','schools_id','=','truong_id')->select('level','department_name','schools_name','users.id','username','first_name','last_name','email','activated','phongban_id','truong_id','nhomquyen_id')->where('users.id','=',$id)->get();
        return $datas;
    }
    public function lockuser($id){
      $json = [];
      try{
        DB::table('users')->where('id','=',$id)->update(['activated' => 0]);   

        //   $now = Carbon::now('Asia/Ho_Chi_Minh');
        //   $user = User::find($id);
        //   $user->activated = 0;
        // //  $user->updated_at=$now;
        // //  $user->updated_userid = Auth::user()->id;
        //   $user->save();
          $json['success'] = "Thay đổi thành công.";
          }catch(\Exception $e){
        $json['error'] = "Lưu bản ghi lỗi.".$e;
      }
      return $json;
    }
    public function unlockuser($id){
      $json = [];
      try{
          DB::table('users')->where('id','=',$id)->update(['activated' => 1]);   
          $json['success'] = "Thay đổi thành công.";
          }catch(\Exception $e){
        $json['error'] = "Lưu bản ghi lỗi.".$e;
      }
      return $json;
    }


    public function insertUser(Request $req){
      $json = [];
      try{
        $userid = Auth::user()->id;

        $username = $req->input("username");
        $first_name = $req->input("first_name");
        $last_name = $req->input("last_name");
        $password = $req->input("password");
        $email = $req->input("email");
        $phongban_id = $req->input("phongban_id");
        $truong_id = $req->input("truong_id");
        $level = $req->input("cap_bac");
        $now = Carbon::now('Asia/Ho_Chi_Minh');
        $user = new User();
        $user->username=$username;
        $user->first_name=$first_name;
        $user->last_name=$last_name;
        $user->email=$email;
        $user->phongban_id=$phongban_id;
        $user->password=bcrypt($password);
        $user->truong_id=$truong_id;
        $user->created_at=$now;
        $user->updated_at=$now;
        $user->created_userid=$userid;
        $user->updated_userid=$userid;
        $user->level=$level;
        $user->save();
        $json['success'] = "Thêm mới thành công.";
      }catch(\Exception $e){
        $json['error'] = "Thêm mới lỗi.".$e;
      }
      return $json;
    }
    public function updateUser(Request $req){
      $json = [];
      try{
        $userid = Auth::user()->id;

        $id = $req->input("id");
        $first_name = $req->input("first_name");
        $last_name = $req->input("last_name");
        $level = $req->input("cap_bac");
        $email = $req->input("email");
        $phongban_id = $req->input("phongban_id");
        $truong_id = $req->input("truong_id");

        $now = Carbon::now('Asia/Ho_Chi_Minh');
        $user = User::find($id);
        //$user->username=$username;
        $user->first_name=$first_name;
        $user->last_name=$last_name;
        $user->email=$email;
        $user->phongban_id=$phongban_id;
        //$user->password=bcrypt($password);
        $user->truong_id=$truong_id;
        $user->level=$level;
        $user->updated_at=$now;
        //$user->created_userid=$userid;
        $user->updated_userid=$userid;
        $user->save();
        $json['success'] = "Cập nhật thành công.";
      }catch(\Exception $e){
        $json['error'] = "Cập nhật lỗi.".$e;
      }
      return $json;
    }
    public function deleteUser($id){
      $json = [];
      try{
        //$user = User::find($id);
        //$user->delete();
        $del = DB::table('permission_users')->where('role_user_id','=',$id)->delete() ;
        if($del>0){
          $user = User::find($id)->delete();
          $json['success'] = "Xóa bản ghi thành công";
        }
        
      }catch(\Exception $e){
        $json['error'] = "Xóa bản ghi lỗi.".$e;
      }
      return $json;
    }
}
