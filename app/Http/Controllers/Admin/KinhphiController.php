<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\KinhPhiDoiTuong;
use App\Models\KinhPhiNamHoc;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Exception;
class KinhphiController extends Controller
{
    public function getMucTheoDoiTuong(){
        return view('admin.kinhphi.capnhatmuchotrodoituong.listing');
    }
    public function danhsachhotro(){
        return view('admin.kinhphi.danhsachhotrodoituong.listing');
    }
    public function getHocPhiTheoNam(){
        return view('admin.kinhphi.capnhatmuchocphithemnam.listing');
    }
	public function getPermission(){
        $json = [];
        $val = [];
    	$data = DB::select('SELECT pu.module_id,pu.permission_id FROM permission_users pu WHERE pu.role_user_id = '.Auth::user()->id.' and pu.module_id = 12');
    	foreach ($data as $key => $value) {
    		$val[]= $value->permission_id.'';
    	}
    	$json['permission'] = $val;
    	return $json;
    }
    public function loadMucTheoDoiTuong(Request $req){
    	$json = [];
    	$start = $req->input('start');
    	$limit = $req->input('limit');
    	$key = str_replace('+',' ',$req->input('key'));
		$user = Auth::user()->id;

	    $getIdTruong = DB::table('users')->select('truong_id')->where('id', '=', $user)->first();

    	if($key!=''){
			$loadkinhphidoituong = DB::table('qlhs_kinhphidoituong')
			->leftJoin('qlhs_group','qlhs_kinhphidoituong.doituong_id' ,'=', 'qlhs_group.group_id')
			->leftJoin('users','users.id' ,'=', 'qlhs_kinhphidoituong.update_userid')
			->leftJoin('qlhs_schools','qlhs_kinhphidoituong.idTruong' ,'=', 'qlhs_schools.schools_id')
			->where('qlhs_schools.schools_active', 1)
			->where('qlhs_group.group_active', 1)
			->select('qlhs_kinhphidoituong.idTruong','qlhs_schools.schools_name','qlhs_kinhphidoituong.id','qlhs_kinhphidoituong.start_date','qlhs_kinhphidoituong.end_date','qlhs_kinhphidoituong.code','qlhs_kinhphidoituong.doituong_id','qlhs_kinhphidoituong.money','qlhs_group.group_name','users.username','qlhs_kinhphidoituong.updated_at');

			$loadkinhphidoituong->where(function($query) use($key){
				$query->where("schools_name", "LIKE","%".$key."%")
				->orWhere("code", "LIKE", "%".$key."%")
				->orWhere("group_name", "LIKE", "%".$key."%")
				->orWhere("username", "LIKE", "%".$key."%")
				->orWhere("money", "=", $key);
			});			

	    	if ($getIdTruong->truong_id > 0) {
	    		$loadkinhphidoituong->where('qlhs_kinhphidoituong.idTruong', '=', $getIdTruong->truong_id);
	    	}

			$json['totalRows'] = $loadkinhphidoituong->count();
			$json['data'] = $loadkinhphidoituong->skip($start*$limit)->take($limit)->get();

    	}else{
	    	//$loadkinhphidoituong = [];
	    	$dataCount = DB::table('qlhs_kinhphidoituong')
	    	->leftJoin('qlhs_group','qlhs_kinhphidoituong.doituong_id' ,'=', 'qlhs_group.group_id')
	    	->leftJoin('users','users.id' ,'=', 'qlhs_kinhphidoituong.update_userid')
	    	->leftJoin('qlhs_schools','qlhs_kinhphidoituong.idTruong' ,'=', 'qlhs_schools.schools_id')
	    	->where('qlhs_schools.schools_active', 1)->where('qlhs_group.group_active', 1)->count();

			$datas = DB::table('qlhs_kinhphidoituong')
			->leftJoin('qlhs_group','qlhs_kinhphidoituong.doituong_id' ,'=', 'qlhs_group.group_id')
			->leftJoin('users','users.id' ,'=', 'qlhs_kinhphidoituong.update_userid')
			->leftJoin('qlhs_schools','qlhs_kinhphidoituong.idTruong' ,'=', 'qlhs_schools.schools_id')
			->where('qlhs_schools.schools_active', 1)->where('qlhs_group.group_active', 1)
			->select('qlhs_kinhphidoituong.idTruong','qlhs_schools.schools_name','qlhs_kinhphidoituong.id','qlhs_kinhphidoituong.start_date','qlhs_kinhphidoituong.end_date','qlhs_kinhphidoituong.code','qlhs_kinhphidoituong.doituong_id','qlhs_kinhphidoituong.money','qlhs_group.group_name','users.username','qlhs_kinhphidoituong.updated_at');

			if ($getIdTruong->truong_id > 0) {
	    		$datas->where('qlhs_kinhphidoituong.idTruong', '=', $getIdTruong->truong_id);
	    	}
			// foreach ($datas->$key as $data) {
			 	
			// 	$json['datatable'] = $data->data;
			// }
			$json['totalRows'] = $datas->count();
			$json['data'] = $datas->orderBy('qlhs_kinhphidoituong.updated_at', 'desc')->skip($start*$limit)->take($limit)->get();
		}
		$json['startRecord'] = ($start);
		$json['numRows'] = $limit;
	    	return $json;
    }

    public function insertMucTheoDoiTuong(Request $request){
    	$json = [];
    	try{
			$user = Auth::user()->id;
	    	//$code = $request->input("code");
	    	$idTruong = $request->input("idTruong");
	    	$idDoiTuong = $request->input("idDoiTuong");
	    	$sotien = $request->input("money");
	    	$startDate = $request->input("startDate");
	    	// $endDate = $request->input("endDate");
	    	$time = time();
	    	$code = "KPDT-".$idTruong."-".$idDoiTuong.'-'.$time;
	    	$now = Carbon::now('Asia/Ho_Chi_Minh');
	    	
	    	$startDateformat = Carbon::parse($startDate);

	    	$year = substr($startDate, 6);
	    	$month = substr($startDate, 3, 2);
	    	$day = substr($startDate, 0, 2);

	    	if ($month == 1 && $year > 0) {
	    		$month = 12;
	    		$year = $year - 1;
	    	}
	    	else {
	    		$month = $month - 1;
	    		$year = $year;
	    	}

	    	$endDateFormat = Carbon::parse($day.'-'.$month.'-'.$year);


	    	$getLastId = DB::table('qlhs_kinhphidoituong')->where('doituong_id', '=', $idDoiTuong)->where('idTruong', '=', $idTruong)
	    		->select(DB::raw('MAX(id) as id'))->get();

	    	$id = null;
	    	foreach ($getLastId as $value) {
	    		$id = $value->{'id'};
	    	}
	    	// return $id;

	    	$table = DB::table('qlhs_kinhphidoituong')->where('doituong_id','=',$idDoiTuong)->where('idTruong','=',$idTruong)->where('start_date','>=', $startDateformat)->count();

	    	if($table == 0){

	    		if (!is_null($id) && !empty($id) && $id > 0) {
	    			$kinhphidoituongold = KinhPhiDoiTuong::find($id);
			    	$kinhphidoituongold->end_date = $endDateFormat;
			    	$kinhphidoituongold->save();
	    		}	    		

		    	$kinhphidoituong = new KinhPhiDoiTuong();
		    	$kinhphidoituong->code = $code;
		    	$kinhphidoituong->idTruong = $idTruong;
		    	$kinhphidoituong->doituong_id = $idDoiTuong;
		    	$kinhphidoituong->money = $sotien;
		    	$kinhphidoituong->start_date = Carbon::parse($startDate);
		    	$kinhphidoituong->end_date = null;
		    	$kinhphidoituong->created_at = $now;
		    	$kinhphidoituong->updated_at = $now;
		    	$kinhphidoituong->create_userid = $user;
		    	$kinhphidoituong->update_userid = $user;
		    	$kinhphidoituong->status = 1;
		    	$kinhphidoituong->save();
		    	$json['success'] = "Lưu bản ghi thành công";


	    	//---------------------------------------------------Execute Store-----------------------------------------------------
	            
	            $end_date_exe = $now->addYears(10);

	            DB::statement("call month_year_table('".$end_date_exe."')");

		    }else{
		    	$json['error'] = "Đối tượng đã được cấp kinh phí";
		    }
	    }catch(\Exception $e){
	    	$json['error'] = "Lưu bản ghi lỗi.".$e;
	    }
	    return $json;
    }

    public function getMucTheoDoiTuongById($id){
    	$loadkinhphidoituong = DB::table('qlhs_kinhphidoituong')->leftJoin('qlhs_group','qlhs_kinhphidoituong.doituong_id' ,'=', 'qlhs_group.group_id')->leftJoin('users','users.id' ,'=', 'qlhs_kinhphidoituong.update_userid')->leftJoin('qlhs_schools','qlhs_kinhphidoituong.idTruong' ,'=', 'qlhs_schools.schools_id')->where('qlhs_schools.schools_active', 1)->where('qlhs_group.group_active', 1)->where('qlhs_kinhphidoituong.id', $id)->select('qlhs_kinhphidoituong.idTruong','qlhs_schools.schools_name','qlhs_kinhphidoituong.id','qlhs_kinhphidoituong.start_date','qlhs_kinhphidoituong.end_date','qlhs_kinhphidoituong.code','qlhs_kinhphidoituong.doituong_id','qlhs_kinhphidoituong.money','qlhs_group.group_name','users.username','qlhs_kinhphidoituong.updated_at')->get();

	    	return $loadkinhphidoituong;
    }

    public function searchMucTheoDoiTuong(Request $req){
    	$json = [];
    	$key = $req->input('key');
    	$start = $req->input('start');
    	$limit = $req->input('limit');
    	
    	$json['startRecord'] = ($start);
	    $json['numRows'] = $limit;
    	$loadkinhphidoituong = DB::table('qlhs_kinhphidoituong')->leftJoin('qlhs_group','qlhs_kinhphidoituong.doituong_id' ,'=', 'qlhs_group.group_id')->leftJoin('users','users.id' ,'=', 'qlhs_kinhphidoituong.update_userid')->leftJoin('qlhs_schools','qlhs_kinhphidoituong.idTruong' ,'=', 'qlhs_schools.schools_id')->where('qlhs_schools.schools_active', 1)->where('qlhs_group.group_active', 1)->select('qlhs_kinhphidoituong.idTruong','qlhs_schools.schools_name','qlhs_kinhphidoituong.id','qlhs_kinhphidoituong.start_date','qlhs_kinhphidoituong.end_date','qlhs_kinhphidoituong.code','qlhs_kinhphidoituong.doituong_id','qlhs_kinhphidoituong.money','qlhs_group.group_name','users.username','qlhs_kinhphidoituong.updated_at')->where("schools_name", "LIKE","%$id%")->orWhere("code", "LIKE", "%".$key."%")->orWhere("group_name", "LIKE", "%".$key."%");

    	$json['totalRows'] = $loadkinhphidoituong->count();
    	$json['data'] = $loadkinhphidoituong->skip($start*$limit)->take($limit)->get();
	    	 return $json;
    	//return $loadkinhphidoituong->where("code","LIKE","%KPDT-04%")->get();
    }

    public function updateMucTheoDoiTuong(Request $request){
    	$json = [];
    	try{
			$user = Auth::user()->id;
			$id = $request->input("id");
			$idTruong = $request->input("idTruong");
	    	//$code = $request->input("code");
	    	$idDoiTuong = $request->input("idDoiTuong");
	    	$sotien = $request->input("money");
	    	$startDate = $request->input("startDate");
	    	$endDate = $request->input("endDate");
	    	$time = time();
	    	$code = "KPDT-".$idTruong."-".$idDoiTuong."-".$time;
	    	//if()
	    	$now = Carbon::now('Asia/Ho_Chi_Minh');
	    	$kinhphidoituong = KinhPhiDoiTuong::find($id);
	    	if(Hash::check($code,$kinhphidoituong->code)){
	    		 $json['error'] = "Đối tượng đã nhập kinh phí.";
	           
	        }else {
	           $kinhphidoituong->idTruong = $idTruong;
		    	$kinhphidoituong->code = $code;
		    	$kinhphidoituong->doituong_id = $idDoiTuong;
		    	$kinhphidoituong->money = $sotien;
		    	$kinhphidoituong->start_date = Carbon::parse($startDate);
		    	// $kinhphidoituong->end_date = $endDate!="" ? Carbon::parse($endDate) : null;
		    	//$kinhphidoituong->created_at = $now;
		    	$kinhphidoituong->updated_at = $now;
		    	//$kinhphidoituong->create_userid = $user;
		    	$kinhphidoituong->update_userid = $user;
		    	$kinhphidoituong->status = 1;
		    	$kinhphidoituong->save();
		    	$json['success'] = "Cập nhật bản ghi thành công";

	    	//---------------------------------------------------Execute Store-----------------------------------------------------
	            
	            $end_date_exe = $now->addYears(10);

	            DB::statement("call month_year_table('".$end_date_exe."')");
	        }
	    	
	    }catch(\Exception $e){
	    	$json['error'] = "Cập nhật bản ghi lỗi.".$e;
	    }
	    return $json;
    }

    public function delMucTheoDoiTuongById($id){
    	$json = [];
    	try{
			//$user = Auth::user()->id;
			
	    	$kinhphidoituong = KinhPhiDoiTuong::find($id);
	    	$kinhphidoituong->delete();
	    	$json['success'] = "Xóa bản ghi thành công";
	    }catch(\Exception $e){
	    	$json['error'] = "Xóa bản ghi lỗi.".$e;
	    }
	    return $json;
    }

    public function listNhomDoituong(){
      	$qlhs_group = DB::table('qlhs_group')->whereNotIn('group_id', [89, 90, 91])->where('group_active','=',1)->select('group_id','group_name')->get();
      	return $qlhs_group;
    }
    // cập nhật kinh phí them năm học

    public function insertMucTheoNamHoc(Request $request){
    	$json = [];
    	try{
			$user = Auth::user()->id;
	    	
	    	$idTruong = $request->input("idTruong");
	    	$CodeNamHoc = $request->input("CodeNamHoc");
	    	$sotien = $request->input("money");
	    	// $startDate = $request->input("startDate");
	    	// $endDate = $request->input("endDate");
	    	$time = time();
	    	$code = "KPNH-".$idTruong."-".$CodeNamHoc."-".$time;
	    	$now = Carbon::now('Asia/Ho_Chi_Minh');

	    	$getKinhphiNam = DB::table('qlhs_kinhphinamhoc')->where('codeYear', '=', $CodeNamHoc)->where('idTruong', '=', $idTruong)->get();

	    	if (!is_null($getKinhphiNam) && count($getKinhphiNam) > 0) {
	    		$json['success'] = "Trường đã có kinh phí cho năm học " . $CodeNamHoc . '-' . ($CodeNamHoc + 1);
	    		return $json;
	    	}

	    	$table = DB::table('qlhs_kinhphinamhoc')->where('code','=',$code)->select('code');
	    	 if($table->count()==0){
				$kinhphinamhoc = new KinhPhiNamHoc();
		    	$kinhphinamhoc->code = $code;
		    	$kinhphinamhoc->codeYear = $CodeNamHoc;
		    	$kinhphinamhoc->money = $sotien;
		    	$kinhphinamhoc->idTruong = $idTruong;
		    	$kinhphinamhoc->start_date = null;//Carbon::parse($startDate);
		    	$kinhphinamhoc->end_date = null;//$endDate!="" ? Carbon::parse($endDate) : null;
		    	$kinhphinamhoc->created_at = $now;
		    	$kinhphinamhoc->updated_at = $now;
		    	$kinhphinamhoc->create_userid = $user;
		    	$kinhphinamhoc->update_userid = $user;
		    	$kinhphinamhoc->save();
		    	$json['success'] = "Lưu bản ghi thành công";
	    	 }else{
				$json['error'] = "Trường này đã được lập kinh phí.";
	    	 }
	    	
	    }catch(\Exception $e){
	    	$json['error'] = "Lưu bản ghi lỗi.".$e;
	    }
	    return $json;
    }
    public function loadMucTheoNamHoc(Request $req){
    	$json = [];
    	$start = $req->input('start');
    	$limit = $req->input('limit');
    	$key = str_replace('+',' ',$req->input('keysearch'));

    	$user = Auth::user()->id;

	    $getIdTruong = DB::table('users')->select('truong_id')->where('id', '=', $user)->first();

    	//$json['totalRows'] = DB::table('qlhs_kinhphinamhoc')->leftJoin('qlhs_years','qlhs_kinhphinamhoc.codeYear' ,'=', 'qlhs_years.code')->leftJoin('users','users.id' ,'=', 'qlhs_kinhphinamhoc.update_userid')->count();
		$datas = DB::table('qlhs_kinhphinamhoc')->leftJoin('qlhs_years','qlhs_kinhphinamhoc.codeYear' ,'=', 'qlhs_years.code')->leftJoin('users','users.id' ,'=', 'qlhs_kinhphinamhoc.update_userid')->leftJoin('qlhs_schools','qlhs_kinhphinamhoc.idTruong' ,'=', 'qlhs_schools.schools_id')->where('qlhs_schools.schools_active', 1);

		if ($getIdTruong->truong_id > 0) {
	    	$datas->where('qlhs_kinhphinamhoc.idTruong', '=', $getIdTruong->truong_id);
	    }

		if (!is_null($key) && !empty($key)) {
			//$datas->where('qlhs_kinhphinamhoc.code', 'LIKE', '%'.$key.'%')->orWhere('qlhs_schools.schools_name', 'LIKE', '%'.$key.'%');

			$datas->where(function($query) use($key){
				$query->where("qlhs_schools.schools_name", "LIKE","%".$key."%")
				->orWhere("qlhs_kinhphinamhoc.code", "LIKE", "%".$key."%")
				->orWhere("qlhs_years.name", "LIKE", "%".$key."%")
				->orWhere("users.username", "LIKE", "%".$key."%")
				->orWhere("qlhs_kinhphinamhoc.money", "=", $key);
			});	
		}
		// foreach ($datas->$key as $data) {
		$json['startRecord'] = ($start);
	    $json['numRows'] = $limit;
		$json['totalRows'] = $datas->count();
		$json['data'] = $datas->select('qlhs_kinhphinamhoc.idTruong','qlhs_schools.schools_name','qlhs_kinhphinamhoc.id','qlhs_kinhphinamhoc.start_date','qlhs_kinhphinamhoc.end_date','qlhs_kinhphinamhoc.code as kpcode','qlhs_years.code','qlhs_kinhphinamhoc.money','qlhs_years.name','users.username','qlhs_kinhphinamhoc.updated_at')->orderBy('qlhs_kinhphinamhoc.updated_at', 'desc')->skip($start*$limit)->take($limit)->get();
	    	return $json;
    }
    public function getMucTheoNamHocById($id){
    	$loadkinhphinamhoc = DB::table('qlhs_kinhphinamhoc')->leftJoin('qlhs_years','qlhs_kinhphinamhoc.codeYear' ,'=', 'qlhs_years.code')->leftJoin('users','users.id' ,'=', 'qlhs_kinhphinamhoc.update_userid')->leftJoin('qlhs_schools','qlhs_kinhphinamhoc.idTruong' ,'=', 'qlhs_schools.schools_id')->where('qlhs_schools.schools_active', 1)->where('qlhs_kinhphinamhoc.id', $id)->select('qlhs_kinhphinamhoc.idTruong','qlhs_schools.schools_name','qlhs_kinhphinamhoc.id','qlhs_kinhphinamhoc.start_date','qlhs_kinhphinamhoc.end_date','qlhs_kinhphinamhoc.code as kpcode','qlhs_years.code','qlhs_kinhphinamhoc.money','qlhs_years.name','users.username','qlhs_kinhphinamhoc.updated_at')->get();
	    	return $loadkinhphinamhoc;
    }
    public function updateMucTheoNamHoc(Request $request){
    	$json =[];
    	try{
			$user = Auth::user()->id;
			$id = $request->input("id");
	    	// $code = $request->input("code");
	    	$idTruong = $request->input("idTruong");
	    	$CodeNamHoc = $request->input("CodeNamHoc");
	    	$sotien = $request->input("money");
	    	// $startDate = $request->input("startDate");
	    	// $endDate = $request->input("endDate");
	    	$time = time();
	    	$code = "KPNH-".$idTruong."-".$CodeNamHoc."-".$time;
	    	$now = Carbon::now('Asia/Ho_Chi_Minh');
	    	$kinhphinamhoc = KinhPhiNamHoc::find($id);

	    	if(Hash::check($code,$kinhphinamhoc->code)){
	    		 $json['error'] = "Trường đã nhập kinh phí.";
	        }
	        else {
		    	$kinhphinamhoc->idTruong = $idTruong;
		    	$kinhphinamhoc->code = $code;
		    	$kinhphinamhoc->codeYear = $CodeNamHoc;
		    	$kinhphinamhoc->money = $sotien;
		    	$kinhphinamhoc->start_date = null;//Carbon::parse($startDate);
		    	$kinhphinamhoc->end_date = null;//$endDate!="" ? Carbon::parse($endDate) : null;
		    	//=Carbon::parse($endDate);
		    	//$kinhphidoituong->created_at=$now;
		    	$kinhphinamhoc->updated_at=$now;
		    	//$kinhphidoituong->create_userid=$user;
		    	$kinhphinamhoc->update_userid=$user;
		    	$kinhphinamhoc->save();
		    	$json['success'] = "Cập nhật bản ghi thành công";
		    }
	    }catch(\Exception $e){
	    	$json['error'] = "Cập nhật bản ghi lỗi.".$e;
	    }
	    return $json;
    }
    public function delMucTheoNamHocById($id){
    	try{
	    	$kinhphinamhoc = KinhPhiNamHoc::find($id);
	    	$kinhphinamhoc->delete();
	    	$json['success'] = "Xóa bản ghi thành công";
	    }catch(\Exception $e){
	    	$json['error'] = "Xóa bản ghi lỗi.".$e;
	    }
	    return $json;
    }

//-----------------------------------------------------Load danh sách hỗ trợ đối tượng---------------------------------------------------------------
    public function loadDataDSHTDT(Request $request){
    	try {

    		$json = [];
	    	$start = $request->input('start');
	    	$limit = $request->input('limit');
	    	$keySearch = $request->input('key');

    		$getDanhsach = DB::table('qlhs_pheduyet')
    		->leftJoin('users as nguoiduyet', 'nguoiduyet.id', '=', 'qlhs_pheduyet.pheduyet_nguoiduyet')
    		->leftJoin('users as nguoigui', 'nguoigui.id', '=', 'qlhs_pheduyet.pheduyet_nguoigui')
    		->leftJoin('users as nguoithamdinh', 'nguoithamdinh.id', '=', 'qlhs_pheduyet.pheduyet_nguoithamdinh')
    		->where('qlhs_pheduyet.pheduyet_trangthai' , '=', 1);

    		if (!is_null($keySearch) && !empty($keySearch)) {
				$getDanhsach->where('qlhs_pheduyet.pheduyet_name', 'LIKE', '%'.$keySearch.'%')
				->orWhere('nguoiduyet.first_name', 'LIKE', '%'.$keySearch.'%')
				->orWhere('nguoiduyet.last_name', 'LIKE', '%'.$keySearch.'%')
				->orWhere('nguoigui.first_name', 'LIKE', '%'.$keySearch.'%')
				->orWhere('nguoigui.last_name', 'LIKE', '%'.$keySearch.'%')
				->orWhere('nguoithamdinh.first_name', 'LIKE', '%'.$keySearch.'%')
				->orWhere('nguoithamdinh.last_name', 'LIKE', '%'.$keySearch.'%');
			}

			$json['startRecord'] = ($start);
		    $json['numRows'] = $limit;
			$json['totalRows'] = $getDanhsach->count();

    		$json['data'] = $getDanhsach->select(
    			'qlhs_pheduyet.pheduyet_id', 
    			'qlhs_pheduyet.pheduyet_name', 
    			'qlhs_pheduyet.pheduyet_ngayduyet', 
    			'qlhs_pheduyet.pheduyet_ngaygui', 
    			'qlhs_pheduyet.pheduyet_file_dinhkem', 
    			'qlhs_pheduyet.pheduyet_file_dikem', 
    			'qlhs_pheduyet.pheduyet_trangthai', 
    			'nguoiduyet.first_name as nguoiduyet_first_name', 
    			'nguoiduyet.last_name as nguoiduyet_last_name', 
    			'nguoigui.first_name as nguoigui_first_name', 
    			'nguoigui.last_name as nguoigui_last_name', 
    			'nguoithamdinh.first_name as nguoithamdinh_first_name', 
    			'nguoithamdinh.last_name as nguoithamdinh_last_name')
    		->skip($start*$limit)->take($limit)->get();

    		return $json;
    	} catch (Exception $e) {
    		return $e;
    	}
    }

    public function downloadDSHTDT_fileDinhkem($id){
    	$getPheduyet = DB::table('qlhs_pheduyet')->where('pheduyet_id', '=', $id)->select('pheduyet_hoso_thamdinh', 'pheduyet_file_dinhkem')->first();
        $dir = storage_path().'/exceldownload/THAMDINH/'.$getPheduyet->pheduyet_file_dinhkem;
        return response()->download($dir, $getPheduyet->pheduyet_file_dinhkem);
    }

    public function downloadDSHTDT_fileDikem($id){
    	$getPheduyet = DB::table('qlhs_pheduyet')->where('pheduyet_id', '=', $id)->select('pheduyet_hoso_thamdinh', 'pheduyet_file_dikem')->first();
        $dir = storage_path().'/files/'.$getPheduyet->pheduyet_file_dikem;
        return response()->download($dir, $getPheduyet->pheduyet_file_dikem);
    }
}
