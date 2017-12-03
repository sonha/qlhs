<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\KinhPhiDoiTuong;
use App\Models\KinhPhiNamHoc;
use App\Models\HoSoBaoCao;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Exception;
use Excel;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
class LapHoSo2Controller extends Controller
{
	public function getPermission(){
        $json = [];
        $val = [];
    	$data = DB::select('SELECT pu.module_id,pu.permission_id FROM permission_users pu WHERE pu.role_user_id = '.Auth::user()->id.' and pu.module_id = 9');
    	foreach ($data as $key => $value) {
    		$val[]= $value->permission_id.'';
    	}
    	$json['permission'] = $val;
    	return $json;
    }
    
	public function chiphihoctap(Request $req){
		$json = [];
		$files =  $req->file('file');
		$truong = $req->input('id_truong');
		$namhoc = $req->input('nam_hoc');
		$user = Auth::user()->id;
		$now = Carbon::now('Asia/Ho_Chi_Minh');
		$report_name = $req->input('name');
		$report_user_sign = $req->input('create_sign');
		$user_name = $req->input('create_name');
		$note = $req->input('note');

		$status = $req->input('status');
		$filename_attach = "";
		if(trim($files) != ""){
			$filenames = 'File-'.$user.'-'.$files->getClientOriginalName();
			$filename_attach = $filenames;
		}

        $checkReportName = DB::table('qlhs_hosobaocao')->where('report_name', 'LIKE', '%'.$report_name.'%')->where('report', 'LIKE', '%CPHT%')->get();

        if (!is_null($checkReportName) && !empty($checkReportName) && count($checkReportName) > 0) {
            $result['error'] = "Tên báo cáo đã tồn tại, xin mời nhập tên khác!";
            return $result;
        }
		
		$data1 = DB::table('qlhs_profile')
		->leftJoin('qlhs_profile_subject','profile_id', '=' ,'profile_subject_profile_id')
		->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$namhoc.'-'.($namhoc + 1).'"'))
		->join('qlhs_kinhphidoituong as kp1','kp1.idTruong','=',DB::raw('profile_school_id AND kp1.doituong_id = 92'))
		->select('kp1.money','profile_id','profile_name','profile_year', 'level_old','level_cur','level_new',
			DB::raw('MAX(profile_subject_subject_id) as profile_subject_subject_id'),
			DB::raw('MAX(CASE when profile_subject_subject_id = 24 then 1 else 0 END) DT1'),
			DB::raw('MAX(CASE when profile_subject_subject_id = 25 then 1 else 0 END) DT2'),
			DB::raw("MAX(CASE when level_old <> '' and qlhs_profile.profile_year < '".$namhoc."-06-01' and profile_subject_subject_id in (24,25) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".$namhoc."-01-01')) then 1 else 0 END) 'HKII1'"),
			DB::raw("MAX(CASE when level_cur <> '' and qlhs_profile.profile_year < '".$namhoc."-12-31' and profile_subject_subject_id in (24,25) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".$namhoc."-05-31')) then 1 else 0 END) 'HKI2'"),
			DB::raw("MAX(CASE when level_cur <> '' and qlhs_profile.profile_year < '".($namhoc + 1)."-06-01' and profile_subject_subject_id in (24,25) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".((int)$namhoc+1)."-01-01')) then 1 else 0 END) 'HKII2'"),
			DB::raw("MAX(CASE when level_new <> '' and qlhs_profile.profile_year < '".($namhoc +1)."-12-31' and profile_subject_subject_id in (24,25) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".((int)$namhoc+1)."-05-31')) then 1 else 0 END) 'HKI3'"))
		->where('profile_year','<',$namhoc.'-06-01')
		->where('profile_school_id','=',$truong)
		->whereIn('profile_subject_subject_id',[24,25])
		->groupBy('profile_id','profile_name','profile_year','level_old','level_cur','level_new','kp1.money');

		$data11 = DB::table(DB::raw("({$data1->toSql()}) as m"))->mergeBindings( $data1 )->select('HKII1','HKI2','HKII2','HKI3','m.money','m.DT1','m.DT2','m.profile_id','m.profile_subject_subject_id','m.profile_name','m.profile_year','m.level_old','m.level_cur','m.level_new',DB::raw('CASE 
	when m.profile_subject_subject_id in (24,25) then ((m.HKII1*5*m.money)+m.HKI2*4*(m.money))
END NhuCau'),DB::raw('CASE 
	when m.profile_subject_subject_id in (24,25) then ((m.HKII2*5*m.money)+m.HKI3*4*(m.money))
END DuToan'));

		$data2 = DB::table('qlhs_profile')
		->leftJoin('qlhs_profile_subject','profile_id', '=' ,'profile_subject_profile_id')
		->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$namhoc.'-'.($namhoc + 1).'"'))
		->join('qlhs_kinhphidoituong as kp1','kp1.idTruong','=',DB::raw('profile_school_id AND kp1.doituong_id = 92'))
		->select('kp1.money','profile_id','profile_name','profile_year', 'level_old','level_cur','level_new',
			DB::raw('MAX(profile_subject_subject_id) as profile_subject_subject_id'),
			DB::raw('MAX(CASE when profile_subject_subject_id = 24 then 1 else 0 END) DT1'),
			DB::raw('MAX(CASE when profile_subject_subject_id = 25 then 1 else 0 END) DT2'),
			DB::raw("MAX(CASE when level_old <> '' and qlhs_profile.profile_year < '".$namhoc."-06-01' and profile_subject_subject_id in (24,25) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".$namhoc."-01-01')) then 1 else 0 END) 'HKII1'"),
			DB::raw("MAX(CASE when level_cur <> '' and qlhs_profile.profile_year < '".$namhoc."-12-31' and profile_subject_subject_id in (24,25) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".$namhoc."-05-31')) then 1 else 0 END) 'HKI2'"),
			DB::raw("MAX(CASE when level_cur <> '' and qlhs_profile.profile_year < '".($namhoc + 1)."-06-01' and profile_subject_subject_id in (24,25) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".((int)$namhoc+1)."-01-01')) then 1 else 0 END) 'HKII2'"),
			DB::raw("MAX(CASE when level_new <> '' and qlhs_profile.profile_year < '".($namhoc +1)."-12-31' and profile_subject_subject_id in (24,25) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".((int)$namhoc+1)."-05-31')) then 1 else 0 END) 'HKI3'"))
		->where('profile_year','>',$namhoc.'-05-31')
		->where('profile_year','<',((int)$namhoc+1).'-06-01')
		->where('profile_school_id','=',$truong)
		->whereIn('profile_subject_subject_id',[24,25])
		->groupBy('profile_id','profile_name','profile_year','level_old','level_cur','level_new','kp1.money');

		$data22 = DB::table(DB::raw("({$data2->toSql()}) as m"))->mergeBindings( $data2 )->select('HKII1','HKI2','HKII2','HKI3','m.money','m.DT1','m.DT2','m.profile_id','m.profile_subject_subject_id','m.profile_name','m.profile_year','m.level_old','m.level_cur','m.level_new',DB::raw('CASE 
	when m.profile_subject_subject_id in (24,25) then ((m.HKII1*5*m.money)+m.HKI2*4*(m.money))
END NhuCau'),DB::raw('CASE 
	when m.profile_subject_subject_id in (24,25) then ((m.HKII2*5*m.money)+m.HKI3*4*(m.money))
END DuToan'));

		$data3 = DB::table('qlhs_profile')
		->leftJoin('qlhs_profile_subject','profile_id', '=' ,'profile_subject_profile_id')
		->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.($namhoc + 1).'-'.($namhoc + 2).'"'))
		->join('qlhs_kinhphidoituong as kp1','kp1.idTruong','=',DB::raw('profile_school_id AND kp1.doituong_id = 92'))
		->select('kp1.money','profile_id','profile_name','profile_year', 'level_old','level_cur','level_new',
			DB::raw('MAX(profile_subject_subject_id) as profile_subject_subject_id'),
			DB::raw('MAX(CASE when profile_subject_subject_id = 24 then 1 else 0 END) DT1'),
			DB::raw('MAX(CASE when profile_subject_subject_id = 25 then 1 else 0 END) DT2'),
			DB::raw("MAX(CASE when level_old <> '' and qlhs_profile.profile_year < '".$namhoc."-06-01' and profile_subject_subject_id in (24,25) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".$namhoc."-01-01')) then 1 else 0 END) 'HKII1'"),
			DB::raw("MAX(CASE when level_old <> '' and qlhs_profile.profile_year < '".$namhoc."-12-31' and profile_subject_subject_id in (24,25) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".$namhoc."-05-31')) then 1 else 0 END) 'HKI2'"),
			DB::raw("MAX(CASE when level_old <> '' and qlhs_profile.profile_year < '".($namhoc + 1)."-06-01' and profile_subject_subject_id in (24,25) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".((int)$namhoc+1)."-01-01')) then 1 else 0 END) 'HKII2'"),
			DB::raw("MAX(CASE when level_cur <> '' and qlhs_profile.profile_year < '".($namhoc +1)."-12-31' and profile_subject_subject_id in (24,25) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".((int)$namhoc+1)."-05-31')) then 1 else 0 END) 'HKI3'"))
		->where('profile_year','>',((int)$namhoc+1).'-05-31')
		->where('profile_year','<',((int)$namhoc+2).'-01-01')
		->where('profile_school_id','=',$truong)
		->whereIn('profile_subject_subject_id',[24,25])
		->groupBy('profile_id','profile_name','profile_year','level_old','level_cur','level_new','kp1.money');

		$data33 = DB::table(DB::raw("({$data3->toSql()}) as m"))->mergeBindings( $data3 )->select('HKII1','HKI2','HKII2','HKI3','m.money','m.DT1','m.DT2','m.profile_id','m.profile_subject_subject_id','m.profile_name','m.profile_year','m.level_old','m.level_cur','m.level_new',DB::raw('CASE 
	when m.profile_subject_subject_id in (24,25) then ((m.HKII1*5*m.money)+m.HKI2*4*(m.money))
END NhuCau'),DB::raw('CASE 
	when m.profile_subject_subject_id in (24,25) then ((m.HKII2*5*m.money)+m.HKI3*4*(m.money))
END DuToan'));

		if($data11->count()==0 && $data22->count()==0 && $data33->count()==0){
				$json['success'] = "Danh sách trống ";
		}else{
			$import =  $this->insertReport($data11,$data22,$data33,$truong,$namhoc,$user,$now,$report_name,$report_user_sign,$user_name,$status,$filename_attach,$files, $note);			
			if($import){
				$json['success'] = "Tạo danh sách thành công";
			}else{
				$json['error'] = "Lập danh sách lỗi. Mời thử lại.";
			}
		}	
			return $json;
	}

	public function insertReport($data11,$data22,$data33,$truong,$namhoc,$user,$now,$report_name,$report_user_sign,$user_name,$status,$filename_attach,$files,$note){
		$time = time();
		$dir = storage_path().'/files/CPHT';
        if($data11->count()>0){
			foreach ($data11->get() as $key => $value) {
				$json['1'] =DB::table('qlhs_chiphihoctap')->insert([
					'cpht_profile_id' => $value->profile_id,
					'cpht_doituong1' => $value->DT1,
					'cpht_doituong2' => $value->DT2,
					'hocky2_old' => $value->HKII1,
					'hocky1_cur' => $value->HKI2,
					'hocky2_cur' => $value->HKII2,
					'hocky1_new' => $value->HKI3,
					'ho_tro' => $value->money,
					'nhu_cau' => $value->NhuCau,
					'du_toan' => $value->DuToan,
					'year_old' => (int)$namhoc,
					'year_cur' => (int)$namhoc+1,
					'type' => 1,
					'type_code' => 'CPHT-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time
					]);
			}
		}else{
			$json['1'] = 2;
		}
		if($data22->count()>0){
			foreach ($data22->get() as $key => $value) {
				$json['2'] = DB::table('qlhs_chiphihoctap')->insert([
					'cpht_profile_id' => $value->profile_id,
					'cpht_doituong1' => $value->DT1,
					'cpht_doituong2' => $value->DT2,
					'hocky2_old' => $value->HKII1,
					'hocky1_cur' => $value->HKI2,
					'hocky2_cur' => $value->HKII2,
					'hocky1_new' => $value->HKI3,
					'ho_tro' => $value->money,
					'nhu_cau' => $value->NhuCau,
					'du_toan' => $value->DuToan,
					'year_old' => (int)$namhoc,
					'year_cur' => (int)$namhoc+1,
					'type' => 2,
					'type_code' => 'CPHT-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time
					]);
			}
		}else{
			$json['2'] = 2;
		}
		if($data33->count()>0){
			foreach ($data33->get() as $key => $value) {
				$json['3'] = DB::table('qlhs_chiphihoctap')->insert([
					'cpht_profile_id' => $value->profile_id,
					'cpht_doituong1' => $value->DT1,
					'cpht_doituong2' => $value->DT2,
					'hocky2_old' => $value->HKII1,
					'hocky1_cur' => $value->HKI2,
					'hocky2_cur' => $value->HKII2,
					'hocky1_new' => $value->HKI3,
					'ho_tro' => $value->money,
					'nhu_cau' => $value->NhuCau,
					'du_toan' => $value->DuToan,
					'year_old' => (int)$namhoc,
					'year_cur' => (int)$namhoc+1,
					'type' => 3,
					'type_code' => 'CPHT-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time
					]);
			}

		}else{
			$json['3'] = 2;
		}
		
		if((int)$json['1']> 0 && (int)$json['2']>0 && (int)$json['3']>0){
			//$check = DB::table('qlhs_hosobaocao')->where('report_type','=','CPHT-'.$user.'-'.$truong.''.$namhoc.''.((int)$namhoc+1))->where('report_status','=',0)->count();
			
			if(trim($files) != ""){
				 if(file_exists($dir.'/'. $filename_attach)){
						$files->move($dir, $filename_attach.'-'.$time);	
				 		//File::delete($dir.'/'. $filename_attach);	
				 }else{
				 		$files->move($dir, $filename_attach);	
				 }
			}
			
			//
			//if($check==0){
				
				$insert_returnID = DB::table('qlhs_hosobaocao')->insertGetId([
					'report_name' => $report_name,
					'report_type' => 'CPHT-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time,
					'report_date' => $now,
					'created_at' => $now,
					'updated_at' => $now,
					'create_userid' => $user,
					'update_userid' => $user,
					'report_user' => $user_name,
					'report_user_sign' => $report_user_sign,
					'report_attach_name' => $filename_attach,
					'report_nature' => $status,
					'report_year' => $namhoc,
					'report_id_truong' => $truong,
					'report_note' => $note,
					'report' => 'CPHT'
				]);
				
				if(!is_null($insert_returnID) && $insert_returnID > 0 ){
					$this->exportforSchools($insert_returnID);
					if (file_exists(storage_path().'/exceldownload/CPHT/'.'CPHT-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time.'.xlsx')) {
						return TRUE;
					}
					else {
						$deleteHoso = DB::table('qlhs_hosobaocao')->where('report_type', 'LIKE', '%'.'CPHT-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time.'%')->delete();
						$deleteCPHT = DB::table('qlhs_chiphihoctap')->where('type_code', 'LIKE', '%'.'CPHT-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time.'%')->delete();
						return false;
					}
				}else{
					return false;
				}
			//}else{
				// DB::table('qlhs_hosobaocao')->where('report_type','=','CPHT-'.$user.'-'.$truong.''.$namhoc.''.((int)$namhoc+1))->where('report_status','=',0)->delete();
				// DB::table('qlhs_hosobaocao')->update([
				// 	'report_name' => $report_name,
				// 	'report_type' => 'CPHT-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time,
				// 	'report_date' => $now,
				// 	//'created_at' => $now,
				// 	'updated_at' => $now,
				// 	//'create_userid' => $user,
				// 	'update_userid' => $user,
				// 	'report_user' => $user_name,
				// 	'report_user_sign' => $report_user_sign,
				// 	'report_attach_name' => $filename_attach,
				// 	'report_nature' => $status,
				// 	'report' => 'CPHT'
				// ]);
				
		}else{
			return false;
		}
		//}
    }

    public function getMucTheoDoiTuong(){
        return view('admin.kinhphi.capnhatmuchotrodoituong.listing');
    }
    public function getHocPhiTheoNam(){
        return view('admin.kinhphi.capnhatmuchocphithemnam.listing');
    }

    public function loadMucTheoDoiTuong(Request $req){
    	$json = [];
    	$start = $req->input('start');
    	$limit = $req->input('limit');
    	//$loadkinhphidoituong = [];
    	$json['totalRows'] = DB::table('qlhs_kinhphidoituong')->leftJoin('qlhs_group','qlhs_kinhphidoituong.doituong_id' ,'=', 'qlhs_group.group_id')->leftJoin('users','users.id' ,'=', 'qlhs_kinhphidoituong.update_userid')->leftJoin('qlhs_schools','qlhs_kinhphidoituong.idTruong' ,'=', 'qlhs_schools.schools_id')->where('qlhs_schools.schools_active', 1)->where('qlhs_group.group_active', 1)->count();
		$datas = DB::table('qlhs_kinhphidoituong')->leftJoin('qlhs_group','qlhs_kinhphidoituong.doituong_id' ,'=', 'qlhs_group.group_id')->leftJoin('users','users.id' ,'=', 'qlhs_kinhphidoituong.update_userid')->leftJoin('qlhs_schools','qlhs_kinhphidoituong.idTruong' ,'=', 'qlhs_schools.schools_id')->where('qlhs_schools.schools_active', 1)->where('qlhs_group.group_active', 1)->select('qlhs_kinhphidoituong.idTruong','qlhs_schools.schools_name','qlhs_kinhphidoituong.id','qlhs_kinhphidoituong.start_date','qlhs_kinhphidoituong.end_date','qlhs_kinhphidoituong.code','qlhs_kinhphidoituong.doituong_id','qlhs_kinhphidoituong.money','qlhs_group.group_name','users.username','qlhs_kinhphidoituong.updated_at')->orderBy('qlhs_kinhphidoituong.updated_at', 'desc')->skip($start*$limit)->take($limit)->get();
		// foreach ($datas->$key as $data) {
		 	$json['startRecord'] = ($start);
	     	$json['numRows'] = $limit;
		// 	$json['datatable'] = $data->data;
		// }
		$json['data'] = $datas;
	    	return $json;
    }
    public function insertMucTheoDoiTuong(Request $request){
    	$json = [];
    	try{
			$user = Auth::user()->id;
	    	$code = $request->input("code");
	    	$idTruong = $request->input("idTruong");
	    	$idDoiTuong = $request->input("idDoiTuong");
	    	$sotien = $request->input("money");
	    	$startDate = $request->input("startDate");
	    	$endDate = $request->input("endDate");
	    	$now = Carbon::now('Asia/Ho_Chi_Minh');
	    	$kinhphidoituong = new KinhPhiDoiTuong();
	    	$kinhphidoituong->code=$code;
	    	$kinhphidoituong->idTruong=$idTruong;
	    	$kinhphidoituong->doituong_id=$idDoiTuong;
	    	$kinhphidoituong->money=$sotien;
	    	$kinhphidoituong->start_date=Carbon::parse($startDate);
	    	$kinhphidoituong->end_date=Carbon::parse($endDate);
	    	$kinhphidoituong->created_at=$now;
	    	$kinhphidoituong->updated_at=$now;
	    	$kinhphidoituong->create_userid=$user;
	    	$kinhphidoituong->update_userid=$user;
	    	$kinhphidoituong->save();
	    	$json['success'] = "Lưu bản ghi thành công";
	    }catch(\Exception $e){
	    	$json['error'] = "Lưu bản ghi lỗi.".$e;
	    }
	    return $json;
    }  
    public function getMucTheoDoiTuongById($id){
    	$loadkinhphidoituong = DB::table('qlhs_kinhphidoituong')->leftJoin('qlhs_group','qlhs_kinhphidoituong.doituong_id' ,'=', 'qlhs_group.group_id')->leftJoin('users','users.id' ,'=', 'qlhs_kinhphidoituong.update_userid')->leftJoin('qlhs_schools','qlhs_kinhphidoituong.idTruong' ,'=', 'qlhs_schools.schools_id')->where('qlhs_schools.schools_active', 1)->where('qlhs_group.group_active', 1)->where('qlhs_kinhphidoituong.id', $id)->select('qlhs_kinhphidoituong.idTruong','qlhs_schools.schools_name','qlhs_kinhphidoituong.id','qlhs_kinhphidoituong.start_date','qlhs_kinhphidoituong.end_date','qlhs_kinhphidoituong.code','qlhs_kinhphidoituong.doituong_id','qlhs_kinhphidoituong.money','qlhs_group.group_name','users.username','qlhs_kinhphidoituong.updated_at')->get();

	    	return $loadkinhphidoituong;
    }
    public function searchMucTheoDoiTuong($id){
    	$loadkinhphidoituong = DB::table('qlhs_kinhphidoituong')->leftJoin('qlhs_group','qlhs_kinhphidoituong.doituong_id' ,'=', 'qlhs_group.group_id')->leftJoin('users','users.id' ,'=', 'qlhs_kinhphidoituong.update_userid')->leftJoin('qlhs_schools','qlhs_kinhphidoituong.idTruong' ,'=', 'qlhs_schools.schools_id')->where('qlhs_schools.schools_active', 1)->where('qlhs_group.group_active', 1)->select('qlhs_kinhphidoituong.idTruong','qlhs_schools.schools_name','qlhs_kinhphidoituong.id','qlhs_kinhphidoituong.start_date','qlhs_kinhphidoituong.end_date','qlhs_kinhphidoituong.code','qlhs_kinhphidoituong.doituong_id','qlhs_kinhphidoituong.money','qlhs_group.group_name','users.username','qlhs_kinhphidoituong.updated_at');
		//if ($id!='') {
         
       // }
	    	 //$loadkinhphidoituong->where("schools_name","LIKE","%".$id."%")->orWhere("code","LIKE","%".$id."%")->orWhere("group_name","LIKE","%".$id."%")->get();
	    	 return $loadkinhphidoituong->where("schools_name", "LIKE","%$id%")
                    ->orWhere("code", "LIKE", "%$id%")
                    ->orWhere("group_name", "LIKE", "%$id%")->get();
    	//return $loadkinhphidoituong->where("code","LIKE","%KPDT-04%")->get();
    }
    public function updateMucTheoDoiTuong(Request $request){
    	$json = [];
    	try{
			$user = Auth::user()->id;
			$id = $request->input("id");
			$idTruong = $request->input("idTruong");
	    	$code = $request->input("code");
	    	$idDoiTuong = $request->input("idDoiTuong");
	    	$sotien = $request->input("money");
	    	$startDate = $request->input("startDate");
	    	$endDate = $request->input("endDate");
	    	$now = Carbon::now('Asia/Ho_Chi_Minh');
	    	$kinhphidoituong = KinhPhiDoiTuong::find($id);
	    	$kinhphidoituong->idTruong=$idTruong;
	    	$kinhphidoituong->code=$code;
	    	$kinhphidoituong->doituong_id=$idDoiTuong;
	    	$kinhphidoituong->money=$sotien;
	    	$kinhphidoituong->start_date=Carbon::parse($startDate);
	    	$kinhphidoituong->end_date=Carbon::parse($endDate);
	    	//$kinhphidoituong->created_at=$now;
	    	$kinhphidoituong->updated_at=$now;
	    	//$kinhphidoituong->create_userid=$user;
	    	$kinhphidoituong->update_userid=$user;
	    	$kinhphidoituong->save();
	    	$json['success'] = "Cập nhật bản ghi thành công";
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
    // cập nhật kinh phí them năm học

    public function insertMucTheoNamHoc(Request $request){
    	$json = [];
    	try{
			$user = Auth::user()->id;
	    	$code = $request->input("code");
	    	$idTruong = $request->input("idTruong");
	    	$CodeNamHoc = $request->input("CodeNamHoc");
	    	$sotien = $request->input("money");
	    	$startDate = $request->input("startDate");
	    	$endDate = $request->input("endDate");
	    	$now = Carbon::now('Asia/Ho_Chi_Minh');
	    	$kinhphinamhoc = new KinhPhiNamHoc();
	    	$kinhphinamhoc->code=$code;
	    	$kinhphinamhoc->codeYear=$CodeNamHoc;
	    	$kinhphinamhoc->money=$sotien;
	    	$kinhphinamhoc->idTruong=$idTruong;
	    	$kinhphinamhoc->start_date=Carbon::parse($startDate);
	    	$kinhphinamhoc->end_date=Carbon::parse($endDate);
	    	$kinhphinamhoc->created_at=$now;
	    	$kinhphinamhoc->updated_at=$now;
	    	$kinhphinamhoc->create_userid=$user;
	    	$kinhphinamhoc->update_userid=$user;
	    	$kinhphinamhoc->save();
	    	$json['success'] = "Lưu bản ghi thành công";
	    }catch(\Exception $e){
	    	$json['error'] = "Lưu bản ghi lỗi.".$e;
	    }
	    return $json;
    }
    public function loadMucTheoNamHoc(Request $req){
    	$json = [];
    	$start = $req->input('start');
    	$limit = $req->input('limit');
    	//$datas = DB::table('qlhs_hosobaocao')->where('report','=','CPHT');
    	//$json['totalRows'] = $datas->count();
     	$json['totalRows'] = DB::table('qlhs_kinhphinamhoc')->leftJoin('qlhs_years','qlhs_kinhphinamhoc.codeYear' ,'=', 'qlhs_years.code')->leftJoin('users','users.id' ,'=', 'qlhs_kinhphinamhoc.update_userid')->count();
		 $datas = DB::table('qlhs_kinhphinamhoc')->leftJoin('qlhs_years','qlhs_kinhphinamhoc.codeYear' ,'=', 'qlhs_years.code')->leftJoin('users','users.id' ,'=', 'qlhs_kinhphinamhoc.update_userid')->leftJoin('qlhs_schools','qlhs_kinhphinamhoc.idTruong' ,'=', 'qlhs_schools.schools_id')->where('qlhs_schools.schools_active', 1)->select('qlhs_kinhphinamhoc.idTruong','qlhs_schools.schools_name','qlhs_kinhphinamhoc.id','qlhs_kinhphinamhoc.start_date','qlhs_kinhphinamhoc.end_date','qlhs_kinhphinamhoc.code as kpcode','qlhs_years.code','qlhs_kinhphinamhoc.money','qlhs_years.name','users.username','qlhs_kinhphinamhoc.updated_at')->orderBy('qlhs_kinhphinamhoc.updated_at', 'desc')->skip($start*$limit)->take($limit)->get();
		// foreach ($datas->$key as $data) {
		 	$json['startRecord'] = ($start);
	     	$json['numRows'] = $limit;
		// 	$json['datatable'] = $data->data;
		// }
		$json['data'] = $datas->orderBy('updated_at','desc')->skip($start*$limit)->take($limit)->get();;
	    	return $json;
    }
    public function download_attach($id){
    	$data = DB::table('qlhs_hosobaocao')->where('report','=','CPHT')->where('report_id','=',$id)->select('report_attach_name')->first(); 
    	$dir = storage_path().'/files/CPHT/'.$data->report_attach_name;
    	return response()->download($dir,$data->report_attach_name);
    }
    public function delete_report($id){
    	$dir = storage_path().'/files/CPHT';
    	$json = [];
    	$data = DB::table('qlhs_hosobaocao')->where('report','=','CPHT')->where('report_id','=',$id)->select('report_type','report_attach_name')->first(); 
    	$del1 = DB::table('qlhs_chiphihoctap')->where('type_code','=',$data->report_type)->delete();
    	if($del1>0){
    		$del2 = DB::table('qlhs_hosobaocao')->where('report','=','CPHT')->where('report_id','=',$id)->delete();
    		if(file_exists($dir.'/'. $data->report_attach_name)){
					//$files->move($dir, $filename_attach.'-'.$time);	
			 		File::delete($dir.'/'. $data->report_attach_name);	
			}

			if (file_exists(storage_path().'/exceldownload/CPHT/'.$data->report_type.'.xlsx')) {
				File::delete(storage_path().'/exceldownload/CPHT/'.$data->report_type.'.xlsx');
			}

    		if($del2> 0){
    			$json['success'] = 'Xóa thành công';
    		}else{
    			$json['error'] = 'Xóa lỗi';
    		}
    	}
    	return $json;
    }
    public function loadDataAll(Request $req){
    	$json = [];
    	$start = $req->input('start');
    	$limit = $req->input('limit');
    	$type = $req->input('type');
    	$user = Auth::user()->id;

    	$getIdTruong = DB::table('users')->select('truong_id')->where('id', '=', $user)->first();
    	$datas = DB::table('qlhs_hosobaocao')->where('report','=',$type);
    	if ($getIdTruong->truong_id > 0) {
    		$datas->where('report_id_truong', '=', $getIdTruong->truong_id);
    	}
    	$json['totalRows'] = $datas->count();
     	
		// foreach ($datas->$key as $data) {
		 	$json['startRecord'] = ($start);
	     	$json['numRows'] = $limit;
		// 	$json['datatable'] = $data->data;
		// }
		$json['data'] = $datas->orderBy('updated_at','desc')->skip($start*$limit)->take($limit)->get();;
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
	    	$code = $request->input("code");
	    	$idTruong = $request->input("idTruong");
	    	$CodeNamHoc = $request->input("CodeNamHoc");
	    	$sotien = $request->input("money");
	    	$startDate = $request->input("startDate");
	    	$endDate = $request->input("endDate");
	    	$now = Carbon::now('Asia/Ho_Chi_Minh');
	    	$kinhphinamhoc = KinhPhiNamHoc::find($id);
	    	$kinhphinamhoc->idTruong=$idTruong;
	    	$kinhphinamhoc->code=$code;
	    	$kinhphinamhoc->codeYear=$CodeNamHoc;
	    	$kinhphinamhoc->money=$sotien;
	    	$kinhphinamhoc->start_date=Carbon::parse($startDate);
	    	$kinhphinamhoc->end_date=Carbon::parse($endDate);
	    	//$kinhphidoituong->created_at=$now;
	    	$kinhphinamhoc->updated_at=$now;
	    	//$kinhphidoituong->create_userid=$user;
	    	$kinhphinamhoc->update_userid=$user;
	    	$kinhphinamhoc->save();
	    	$json['success'] = "Cập nhật bản ghi thành công";
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
    public function sendCPHT($id){
    	try{
	    	$hosobaocao = HoSoBaoCao::find($id);
	    	$hosobaocao->report_status = 1;
	    	$hosobaocao->report_verify = 0;
	    	$hosobaocao->report_user_send = Auth::user()->id;
	    	$hosobaocao->save();
	    	$now = Carbon::now('Asia/Ho_Chi_Minh');
			$string = str_random(15);
	    	$CPHT = DB::table('qlhs_thamdinh')->insert([
	    			'thamdinh_name' => 'CPHT-'.$string,
					'thamdinh_type' => 'CPHT',
					'thamdinh_hoso_id' => $id,
					'thamdinh_trangthai' => 0,
					'thamdinh_ngaygui' => $now,
					'thamdinh_nguoigui' => Auth::user()->id					
					]);
	    	$json['success'] = "Gửi thành công.";
	    }catch(\Exception $e){
	    	$json['error'] = "Gửi lỗi.".$e;
	    }
	    return $json;
    }

    public function downloadfile_Export($id){
    	$data = DB::table('qlhs_hosobaocao')->where('report','=','CPHT')->where('report_id','=',$id)->select('report_type')->first(); 
    	$dir = storage_path().'/exceldownload/CPHT/'.$data->report_type.'.xlsx';
    	return response()->download($dir, $data->report_type.'.xlsx');
    }

	public function exportforSchools($id){
	//$type = true;
    	if (is_null($id) || empty($id) || $id == 0) {
    		return "Mời bấm vào tên danh sách muốn kết xuất để kêt xuất file Excel!";
    	}

    	$getSchoolName = DB::table('qlhs_hosobaocao')->join('qlhs_schools', 'qlhs_schools.schools_id', '=', 'qlhs_hosobaocao.report_id_truong')->where('qlhs_hosobaocao.report_id', '=', $id)->select('qlhs_schools.schools_name', 'qlhs_hosobaocao.report_type', 'qlhs_hosobaocao.report_year')->first();
		$data_results = [];
		$data_results['aT'] = 'Học sinh có mặt tại trường tháng 5/' . $getSchoolName->report_year;
        $data_results['bT'] = 'Học sinh dự kiến tuyển mới năm học ' . $getSchoolName->report_year . '-' . ((int)$getSchoolName->report_year + 1);
        $data_results['cT'] = 'Học sinh dự kiến tuyển mới năm học ' . ((int)$getSchoolName->report_year + 1) . '-' . ((int)$getSchoolName->report_year + 2);
    	if ($getSchoolName->report_type != null && $getSchoolName->report_type != "") {
    		$data_results['aCount'] = $this->countValue('1',$getSchoolName->report_type);
    		$data_results['bCount'] = $this->countValue('2',$getSchoolName->report_type);
    		$data_results['cCount'] = $this->countValue('3',$getSchoolName->report_type);
    		$data_results['TotalCount'] = $this->countValue(null,$getSchoolName->report_type);
    		//Get by type A
    		$namhoc = $getSchoolName->report_year;
    		$data_results['a'] = DB::table('qlhs_chiphihoctap')
    		->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_chiphihoctap.cpht_profile_id')
    		->leftJoin(DB::raw('(select decided_profile_id, max(decided_id) decided_id, max(decided_confirmation) decided_confirmation, max(decided_confirmdate) decided_confirmdate, max(decided_number) decided_number, max(decided_type) decided_type from qlhs_decided GROUP BY decided_profile_id) as qlhs_decided'), 'qlhs_decided.decided_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_decided.decided_type = "CPHT"'))
    		->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
    		->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_chiphihoctap.cpht_profile_id and qlhs_profile_history.history_year = "'.$namhoc.'-'.($namhoc + 1).'"'))
    		->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
    		->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
    		->where('qlhs_chiphihoctap.type_code', '=', $getSchoolName->report_type)
    		->where('qlhs_chiphihoctap.type', '=', 1)
    		->select('qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile_history.history_year', 'qlhs_decided.decided_id', 'qlhs_decided.decided_confirmation', 'qlhs_decided.decided_number', 'qlhs_decided.decided_confirmdate', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'qlhs_chiphihoctap.*')->DISTINCT()->get();

    		$data_results['b'] = DB::table('qlhs_chiphihoctap')
    		->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_chiphihoctap.cpht_profile_id')
    		->leftJoin(DB::raw('(select decided_profile_id, max(decided_id) decided_id, max(decided_confirmation) decided_confirmation, max(decided_confirmdate) decided_confirmdate, max(decided_number) decided_number, max(decided_type) decided_type from qlhs_decided GROUP BY decided_profile_id) as qlhs_decided'), 'qlhs_decided.decided_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_decided.decided_type = "CPHT"'))
    		->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
    		->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_chiphihoctap.cpht_profile_id and qlhs_profile_history.history_year = "'.$getSchoolName->report_year.'-'.($getSchoolName->report_year + 1).'"'))
    		->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
    		->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
    		->where('qlhs_chiphihoctap.type_code', '=', $getSchoolName->report_type)
    		->where('qlhs_chiphihoctap.type', '=', 2)
    		->select('qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile_history.history_year', 'qlhs_decided.decided_id', 'qlhs_decided.decided_confirmation', 'qlhs_decided.decided_number', 'qlhs_decided.decided_confirmdate', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'qlhs_chiphihoctap.*')->DISTINCT()->get();

    		$data_results['c'] = DB::table('qlhs_chiphihoctap')
    		->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_chiphihoctap.cpht_profile_id')
    		->leftJoin(DB::raw('(select decided_profile_id, max(decided_id) decided_id, max(decided_confirmation) decided_confirmation, max(decided_confirmdate) decided_confirmdate, max(decided_number) decided_number, max(decided_type) decided_type from qlhs_decided GROUP BY decided_profile_id) as qlhs_decided'), 'qlhs_decided.decided_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_decided.decided_type = "CPHT"'))
    		->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
    		->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_chiphihoctap.cpht_profile_id and qlhs_profile_history.history_year = "'.($getSchoolName->report_year + 1).'-'.($getSchoolName->report_year + 2).'"'))
    		->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
    		->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
    		->where('qlhs_chiphihoctap.type_code', '=', $getSchoolName->report_type)
    		->where('qlhs_chiphihoctap.type', '=', 3)
    		->select('qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile_history.history_year', 'qlhs_decided.decided_id', 'qlhs_decided.decided_confirmation', 'qlhs_decided.decided_number', 'qlhs_decided.decided_confirmdate', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'qlhs_chiphihoctap.*')->DISTINCT()->get();
    	}
        $data_results['schools_name'] = $getSchoolName->schools_name;
        $data_results['report_year'] = $getSchoolName->report_year;
        $this->addCellExcel($data_results, $getSchoolName->report_type, TRUE);
	}

	private function addCellExcel($data_results, $filename, $type = true){
		$excel = 	Excel::load(storage_path().'/exceltemplate/laphosoCPHT.xlsx', function($reader) use($data_results){
			$borderArray = array(
			  	'borders' => array(
				    'allborders' => array(
				        'style' => 'thin',
				        'color' => array('argb' => 'FF000000')
				    )
			  	)
			);
			$FontArray = array(
				'font' => array(
				    'bold' => 'bold'
			  	)
			);
			$FontArrayitalic = array(
				'font' => array(
				    'italic' => 'italic'
			  	)
			);
			$style = array(
		        'alignment' => array(
		            'horizontal' => 'center',
		        )
		    );
		    $styleLeft = array(
		        'alignment' => array(
		            'horizontal' => 'left',
		        )
		    );
		    $styleRight = array(
		        'alignment' => array(
		            'horizontal' => 'right',
		        )
		    );
			$row = 8;

			$col = 0;
			$colA = 0;

			$class_lv1 = 0;
			$class_lv2 = 0;
			$class_lv3 = 0;

			//-----------------------------------------Title------------------------------------------------------------------------------------------------
			$reader->getActiveSheet()->setCellValueByColumnAndRow(0, 2, 'NHU CẦU KINH PHÍ NĂM '.$data_results['report_year'].', DỰ TOÁN NĂM '.($data_results['report_year'] + 1).' ĐỐI VỚI CHÍNH SÁCH HỖ TRỢ CHI PHÍ HỌC TẬP THEO NGHỊ ĐỊNH SỐ 86/2015/NĐ-CP CỦA CHÍNH PHỦ')->getStyle('A2')->applyFromArray($FontArray)->applyFromArray($style);
			// $reader->getActiveSheet()->setCellValueByColumnAndRow(8, 3, '(Kèm theo Công văn số        /STC-KHNS ngày     /8/'.$data_results['report_year'].' của Sở Tài chính Yên Bái)')->getStyle('I3')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
			$reader->getActiveSheet()->setCellValueByColumnAndRow(2, 5, 'Thông tin cơ bản (tính tại thời điểm tháng 5/'.$data_results['report_year'].')')->getStyle('C5')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

			$reader->getActiveSheet()->setCellValueByColumnAndRow(2, 7, 'Học kì II năm học '.($data_results['report_year'] - 1).'-'.$data_results['report_year'])->getStyle('C7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
			$reader->getActiveSheet()->setCellValueByColumnAndRow(3, 7, 'Năm học '.$data_results['report_year'].'-'.($data_results['report_year'] + 1))->getStyle('D7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
			$reader->getActiveSheet()->setCellValueByColumnAndRow(4, 7, 'Năm học '.($data_results['report_year'] + 1).'-'.($data_results['report_year'] + 2))->getStyle('E7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

			$reader->getActiveSheet()->setCellValueByColumnAndRow(12, 7, 'Học kỳ II năm học '.($data_results['report_year'] - 1).'-'.$data_results['report_year'])->getStyle('M7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
			$reader->getActiveSheet()->setCellValueByColumnAndRow(13, 7, 'Học kỳ I năm học '.$data_results['report_year'].'-'.($data_results['report_year'] + 1))->getStyle('N7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
			$reader->getActiveSheet()->setCellValueByColumnAndRow(14, 7, 'Học kỳ II năm học '.$data_results['report_year'].'-'.($data_results['report_year'] + 1))->getStyle('O7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
			$reader->getActiveSheet()->setCellValueByColumnAndRow(15, 7, 'Học kỳ I năm học '.($data_results['report_year'] + 1).'-'.($data_results['report_year'] + 2))->getStyle('P7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

			$reader->getActiveSheet()->setCellValueByColumnAndRow(16, 5, 'Nhu cầu kinh phí năm '.$data_results['report_year'])->getStyle('Q5')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
			$reader->getActiveSheet()->setCellValueByColumnAndRow(17, 5, 'Dự toán kinh phí năm '.($data_results['report_year'] + 1))->getStyle('R5')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
			//-----------------------------------------End Title----------------------------------------------------------------------------------------------------

			//$data_results['aCount'][0]
			$reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row,$data_results['schools_name'])->getStyle('B'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($FontArrayitalic)->applyFromArray($styleLeft);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(2, $row, '')->getStyle('C'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(3, $row, '')->getStyle('D'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(4, $row, '')->getStyle('E'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(5, $row, '')->getStyle('F'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(6, $row, '')->getStyle('G'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(7, $row, '')->getStyle('H'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(8, $row, '')->getStyle('I'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(9, $row, '')->getStyle('J'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(10, $row, '')->getStyle('K'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(11, $row, '')->getStyle('L'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		  	$reader->getActiveSheet()->setCellValueByColumnAndRow(12, $row,$data_results['TotalCount']->tonghocky2_old)->getStyle('M'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
		  	$reader->getActiveSheet()->setCellValueByColumnAndRow(13, $row,$data_results['TotalCount']->tonghocky1_cur)->getStyle('N'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row,$data_results['TotalCount']->tonghocky2_cur)->getStyle('O'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row,$data_results['TotalCount']->tonghocky1_new)->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row,$data_results['TotalCount']->tong_nhucau)->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row,$data_results['TotalCount']->tong_dutoan)->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);

			$row++;
			$reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row,'a')->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

			//____________________________________________________________________________________________________________________________________________________
		 	$reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row,$data_results['aT'])->getStyle('B'.$row)->applyFromArray($FontArray)->applyFromArray($styleLeft);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(2, $row, '')->getStyle('C'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(3, $row, '')->getStyle('D'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(4, $row, '')->getStyle('E'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(5, $row, '')->getStyle('F'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(6, $row, '')->getStyle('G'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(7, $row, '')->getStyle('H'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(8, $row, '')->getStyle('I'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(9, $row, '')->getStyle('J'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(10, $row, '')->getStyle('K'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(11, $row, '')->getStyle('L'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		 	
		  	$reader->getActiveSheet()->setCellValueByColumnAndRow(12, $row,$data_results['aCount']->tonghocky2_old)->getStyle('M'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		  	$reader->getActiveSheet()->setCellValueByColumnAndRow(13, $row,$data_results['aCount']->tonghocky1_cur)->getStyle('N'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row,$data_results['aCount']->tonghocky2_cur)->getStyle('O'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row,$data_results['aCount']->tonghocky1_new)->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row,$data_results['aCount']->tong_nhucau)->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row,$data_results['aCount']->tong_dutoan)->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);

			if($data_results['a']->count()>0){
				$indexa = 0;
				foreach($data_results['a'] as $key => $value){
					$col = 0;	$row++;

					// $strYear = substr((string)$value->history_year, 0, 4);
					// if ($strYear < $data_results['report_year']) {
					// 	$class_lv1 = $value->level_next_1;
					// 	$class_lv2 = $value->level_next_2;
					// 	$class_lv3 = $value->level_next_3;
					// }

					// if ($strYear == $data_results['report_year']) {
					// 	$class_lv1 = $value->level_next;
					// 	$class_lv2 = $value->level_next_2;
					// 	$class_lv3 = $value->level_next_3;
					// }

					$decided_date = "";
					if (!is_null($value->decided_confirmdate) && !empty($value->decided_confirmdate)) {					
						$decided_date = Carbon::parse($value->decided_confirmdate)->format('d-m-Y');
					}

				    $reader->getActiveSheet()->setCellValueByColumnAndRow($col, $row,++$indexa)->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($style);

				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_name)->getStyle('B'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
				        	
					$reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->level_old)->getStyle('C'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
				        	
					$reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->level_cur)->getStyle('D'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
				        	
					$reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->level_new)->getStyle('E'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,Carbon::parse($value->profile_birthday)->format('d-m-Y'))->getStyle('F'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight); 
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_parentname)->getStyle('G'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);  
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->decided_number)->getStyle('H'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->decided_confirmation)->getStyle('I'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$decided_date)->getStyle('J'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 

				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->cpht_doituong1)->getStyle('K'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->cpht_doituong2)->getStyle('L'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_old)->getStyle('M'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_cur)->getStyle('N'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight); 

				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_cur)->getStyle('O'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_new)->getStyle('P'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->nhu_cau)->getStyle('Q'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->du_toan)->getStyle('R'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
					}
				}
			//b

			$row++;
			$reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row,'b')->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

		 	$reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row,$data_results['bT'])->getStyle('B'.$row)->applyFromArray($FontArray);
			// /$colAnext = 14;
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(2, $row, '')->getStyle('C'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(3, $row, '')->getStyle('D'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(4, $row, '')->getStyle('E'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(5, $row, '')->getStyle('F'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(6, $row, '')->getStyle('G'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(7, $row, '')->getStyle('H'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(8, $row, '')->getStyle('I'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(9, $row, '')->getStyle('J'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(10, $row, '')->getStyle('K'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(11, $row, '')->getStyle('L'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		 	$reader->getActiveSheet()->setCellValueByColumnAndRow(12, $row,$data_results['bCount']->tonghocky2_old)->getStyle('M'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		  	$reader->getActiveSheet()->setCellValueByColumnAndRow(13, $row,$data_results['bCount']->tonghocky1_cur)->getStyle('N'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row,$data_results['bCount']->tonghocky2_cur)->getStyle('O'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row,$data_results['bCount']->tonghocky1_new)->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row,$data_results['bCount']->tong_nhucau)->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row,$data_results['bCount']->tong_dutoan)->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);

			$indexb = 0;

			if($data_results['b']->count()>0){
				$indexa = 0;
				foreach($data_results['b'] as $key => $value){
					$col = 0;	$row++;

					// $strYear = substr((string)$value->history_year, 0, 4);
					// if ($strYear < $data_results['report_year']) {
					// 	$class_lv1 = $value->level_next;
					// 	$class_lv2 = $value->level_next_1;
					// 	$class_lv3 = $value->level_next_2;
					// }

					// if ($strYear == $data_results['report_year']) {
					// 	$class_lv1 = 0;
					// 	$class_lv2 = $value->level_next_1;
					// 	$class_lv3 = $value->level_next_2;
					// }

					// if ($strYear > $data_results['report_year']) {
					// 	$class_lv1 = 0;
					// 	$class_lv2 = 0;
					// 	$class_lv3 = $value->level_next_1;
					// }

					$decided_date = "";
					if (!is_null($value->decided_confirmdate) && !empty($value->decided_confirmdate)) {					
						$decided_date = Carbon::parse($value->decided_confirmdate)->format('d-m-Y');
					}

				    $reader->getActiveSheet()->setCellValueByColumnAndRow($col, $row,++$indexa)->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($style);

				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_name)->getStyle('B'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
				        	
					$reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->level_old)->getStyle('C'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
				        	
					$reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->level_cur)->getStyle('D'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
				        	
					$reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->level_new)->getStyle('E'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,Carbon::parse($value->profile_birthday)->format('d-m-Y'))->getStyle('F'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight); 
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_parentname)->getStyle('G'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);  
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->decided_number)->getStyle('H'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->decided_confirmation)->getStyle('I'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$decided_date)->getStyle('J'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 

				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->cpht_doituong1)->getStyle('K'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->cpht_doituong2)->getStyle('L'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_old)->getStyle('M'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_cur)->getStyle('N'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight); 

				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_cur)->getStyle('O'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_new)->getStyle('P'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->nhu_cau)->getStyle('Q'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->du_toan)->getStyle('R'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
					}
				}

				//c

			$row++;
			$reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row,'c')->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

		 	$reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row,$data_results['cT'])->getStyle('B'.$row)->applyFromArray($FontArray);
			// /$colAnext = 14;
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(2, $row, '')->getStyle('C'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(3, $row, '')->getStyle('D'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(4, $row, '')->getStyle('E'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(5, $row, '')->getStyle('F'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(6, $row, '')->getStyle('G'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(7, $row, '')->getStyle('H'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(8, $row, '')->getStyle('I'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(9, $row, '')->getStyle('J'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(10, $row, '')->getStyle('K'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(11, $row, '')->getStyle('L'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		 	$reader->getActiveSheet()->setCellValueByColumnAndRow(12, $row,$data_results['cCount']->tonghocky2_old)->getStyle('M'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		  	$reader->getActiveSheet()->setCellValueByColumnAndRow(13, $row,$data_results['cCount']->tonghocky1_cur)->getStyle('N'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row,$data_results['cCount']->tonghocky2_cur)->getStyle('O'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row,$data_results['cCount']->tonghocky1_new)->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row,$data_results['cCount']->tong_nhucau)->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row,$data_results['cCount']->tong_dutoan)->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);

			$indexc = 0;
			if($data_results['c']->count()>0){
				$indexa = 0;
				foreach($data_results['c'] as $key => $value){
					$col = 0;	$row++;

					// $strYear = substr((string)$value->history_year, 0, 4);
					// if ($strYear < $data_results['report_year']) {
					// 	$class_lv1 = $value->level_next;
					// 	$class_lv2 = $value->level_next_1;
					// 	$class_lv3 = $value->level_next_2;
					// }

					// if ($strYear == $data_results['report_year']) {
					// 	$class_lv1 = 0;
					// 	$class_lv2 = $value->level_next_1;
					// 	$class_lv3 = $value->level_next_2;
					// }

					// if ($strYear > $data_results['report_year']) {
					// 	$class_lv1 = 0;
					// 	$class_lv2 = 0;
					// 	$class_lv3 = $value->level_next_1;
					// }

					$decided_date = "";
					if (!is_null($value->decided_confirmdate) && !empty($value->decided_confirmdate)) {					
						$decided_date = Carbon::parse($value->decided_confirmdate)->format('d-m-Y');
					}

				    $reader->getActiveSheet()->setCellValueByColumnAndRow($col, $row,++$indexa)->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($style);

				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_name)->getStyle('B'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
				        	
					$reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,'')->getStyle('C'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
				        	
					$reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,'')->getStyle('D'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
				        	
					$reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->level_cur)->getStyle('E'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,Carbon::parse($value->profile_birthday)->format('d-m-Y'))->getStyle('F'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight); 
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_parentname)->getStyle('G'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);  
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->decided_number)->getStyle('H'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->decided_confirmation)->getStyle('I'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$decided_date)->getStyle('J'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 

				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->cpht_doituong1)->getStyle('K'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->cpht_doituong2)->getStyle('L'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_old)->getStyle('M'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_cur)->getStyle('N'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight); 

				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_cur)->getStyle('O'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_new)->getStyle('P'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->nhu_cau)->getStyle('Q'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->du_toan)->getStyle('R'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
				}
			}
		});
		if($type){
	    	return $excel->setFilename($filename)->store('xlsx', storage_path().'/exceldownload/CPHT');
	    }else{
	    	return $excel->setFilename($filename)->download('xlsx');
	    }
	}
    
    public function countValue($type = null,$code){

    	if($type!=null){
    		$count = DB::table('qlhs_chiphihoctap')->where('type_code','=',$code)->where('type','=',$type)
    		->select(
    			DB::raw('sum(hocky2_old) as tonghocky2_old'),
    			DB::raw('sum(hocky1_cur) as tonghocky1_cur'),
    			DB::raw('sum(hocky2_cur) as tonghocky2_cur'),
    			DB::raw('sum(hocky1_new) as tonghocky1_new'),
    			DB::raw('sum(nhu_cau) as tong_nhucau'),
    			DB::raw('sum(du_toan) as tong_dutoan'))->first();
    		return $count;
    	}else{
    		$count = DB::table('qlhs_chiphihoctap')->where('type_code','=',$code)
    		->select(
    			DB::raw('sum(hocky2_old) as tonghocky2_old'),
    			DB::raw('sum(hocky1_cur) as tonghocky1_cur'),
    			DB::raw('sum(hocky2_cur) as tonghocky2_cur'),
    			DB::raw('sum(hocky1_new) as tonghocky1_new'),
    			DB::raw('sum(nhu_cau) as tong_nhucau'),
    			DB::raw('sum(du_toan) as tong_dutoan'))->first();
    		return $count;
    	}
    }
    
}
