<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\qlhs_message;
class PagesController extends Controller
{
    public function loadListMessage($limit){
        $result = [];
        $mess = qlhs_message::leftJoin('qlhs_schools','schools_id','school_id')->where('status',0);
        $result['data'] = $mess->select('type','message_text','schools_name','qlhs_message.updated_at')->orderBy('qlhs_message.updated_at','desc')->take($limit)->get();
        $result['total'] = $mess->count();
        return $result;
        
    }
    public function getPage($url)
    {
      $module = DB::select('select qlhs_modules.* from qlhs_modules LEFT JOIN (SELECT module_id,role_user_id from permission_users GROUP BY module_id,role_user_id ) as permission_user
 on qlhs_modules.module_id = permission_user.module_id and role_user_id = :id where module_view = :view and module_path = :path order by module_order,module_id', ['id' => Auth::user()->id,'view' => 1,'path' => $url]);
      if($url=='welcome' || $url==''){
            return view('welcome',['category' => 'Phần mềm quản lý hồ sơ']);
      }
      if(count($module)>0 ){
         if($url=='bao-cao'){
            return view('admin.baocao.listing',['category' => 'Hệ thống']);
        }else if($url=='ho-so-chinh-sach'){
            return view('admin.hoso.listing',['category' => 'Hồ sơ chinh sách']);
        }else if($url=='kinh-phi-ho-tro'){
            return view('admin.kinhphi.listing',['category' => 'Quản lý kinh phí hỗ trợ']);
        }else if($url=='danh-muc'){
            return view('admin.danhmuc.listing',['category' => 'Quản lý danh mục']);
        }else if($url=='he-thong'){
            return view('admin.hethong.listing',['category' => 'Hệ thống']);
        }
      }else{
          return view('errors.permission');
      }

    }

    public function getDashboard()
    {
       return view('welcome',['category' => 'Phần mềm quản lý hồ sơ']);
		 	// return view('admin.pages.dashboard', ['category' => 'welcome']);
		
        //return view('admin.pages.dashboard');
    }
	public function addGet(){
      //  if($request->isMethod('get')){
        //    $category = DB::table('qlhs_modules')->select('module_id','module_name','module_path','module_parentid','module_icon')->get();
    //return view('admin.pages.blank');
            return view('admin.danhmuc.addCategory');
       // }
    }
    public function phanquyennguoidung(){
           return view('layouts.intro');
    }
    public function listGet(){
       $results = DB::table('qlhs_department')->get();

    //return response()->json($results);
    //die (json_encode($results));
    return view('admin.danhmuc.listCategory')->with('departments', $results);
    }
       
    public function listGets(){
       $list = DB::table('roles')->paginate(5);
        return view('admin.danhmuc.listCategory',['list'=>$list]);
      //      return view('admin.danhmuc.listCategory');
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
}
