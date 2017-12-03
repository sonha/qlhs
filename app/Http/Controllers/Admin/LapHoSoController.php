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
class LapHoSoController extends Controller
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
	public function miengiamhocphi(Request $req){
		$json = [];
		$files =  $req->file('file');
		$truong = $req->input('id_truong');
		$namhoc = $req->input('nam_hoc');
		$note = $req->input('note');
		$user = Auth::user()->id;
		$now = Carbon::now('Asia/Ho_Chi_Minh');
		$report_name = $req->input('name');
		$report_user_sign = $req->input('create_sign');
		$user_name = $req->input('create_name');

		$status = $req->input('status');
		$filename_attach = "";
		if(trim($files) != ""){
			$filenames = 'File-'.$user.'-'.$files->getClientOriginalName();
			$filename_attach = $filenames;
		}

        $checkReportName = DB::table('qlhs_hosobaocao')->where('report_name', 'LIKE', '%'.$report_name.'%')->where('report', 'LIKE', '%MGHP%')->get();

        if (!is_null($checkReportName) && !empty($checkReportName) && count($checkReportName) > 0) {
            $result['error'] = "Tên báo cáo đã tồn tại, xin mời nhập tên khác!";
            return $result;
        }

        // ->where(function($query) use ($namhoc)
	 //    {
	 //        $query->where(function($qry) use ($namhoc){
		// 		$qry->where('qlhs_profile_history.history_year', '=', ($namhoc - 1).'-'.$namhoc)->where(function($que) use ($namhoc){
		// 			$que->where('qlhs_level.level_next_2', '=', 0);
		// 		})->orWhere('qlhs_profile.profile_leaveschool_date', '>', $namhoc.'-01-01');
	 //        })->orWhere('qlhs_profile_history.history_year', '=', $namhoc.'-'.($namhoc + 1));
	 //    })
		
		$data1 = DB::table('qlhs_profile')
		->leftJoin('qlhs_profile_subject','profile_id', '=' ,'profile_subject_profile_id')
		->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$namhoc.'-'.($namhoc + 1).'"'))
		->join('qlhs_kinhphinamhoc as kp1','kp1.idTruong','=',DB::raw('profile_school_id and kp1.codeYear = '.($namhoc - 1).''))
		->join('qlhs_kinhphinamhoc as kp2','kp2.idTruong','=',DB::raw('profile_school_id and kp2.codeYear = '.$namhoc.''))
		->join('qlhs_kinhphinamhoc as kp3','kp3.idTruong','=',DB::raw('profile_school_id and kp3.codeYear = '.($namhoc + 1).''))
		->select('kp1.money as money1','kp2.money as money2','kp3.money as money3','profile_id','profile_name','profile_year', 'level_old', 'level_cur', 'level_new', 
			DB::raw('MAX(qlhs_profile_subject.profile_subject_subject_id) as profile_subject_subject_id'), 
			DB::raw('MAX(CASE when profile_subject_subject_id = 35 then 1 else 0 END) Mien1'),
			DB::raw('MAX(CASE when profile_subject_subject_id = 36 then 1 else 0 END) Mien2'),
			DB::raw('MAX(CASE when profile_subject_subject_id = 38 then 1 else 0 END) Mien3'),
			DB::raw('MAX(CASE when profile_subject_subject_id = 19 then 1 else 0 END) Mien4'),
			DB::raw('MAX(CASE when profile_subject_subject_id = 39 then 1 else 0 END) Mien5'),
			DB::raw('MAX(CASE when profile_subject_subject_id = 34 then 1 else 0 END) Giam70'),
			DB::raw('MAX(CASE when profile_subject_subject_id = 40 then 1 else 0 END) Giam501'),
			DB::raw('MAX(CASE when profile_subject_subject_id = 41 then 1 else 0 END) Giam502'),
			DB::raw("MAX(CASE when level_old <> '' and qlhs_profile.profile_year < '".$namhoc."-06-01' and profile_subject_subject_id in (35,36,19,38,39,34,40,41) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".$namhoc."-01-01')) then 1 else 0 END) 'HKII1'"),
			DB::raw("MAX(CASE when level_cur <> '' and qlhs_profile.profile_year < '".$namhoc."-12-31' and profile_subject_subject_id in (35,36,19,38,39,34,40,41) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".$namhoc."-05-31')) then 1 else 0 END) 'HKI2'"),
			DB::raw("MAX(CASE when level_cur <> '' and qlhs_profile.profile_year < '".($namhoc + 1)."-06-01' and profile_subject_subject_id in (35,36,19,38,39,34,40,41) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".((int)$namhoc+1)."-01-01')) then 1 else 0 END) 'HKII2'"),
			DB::raw("MAX(CASE when level_new <> '' and qlhs_profile.profile_year < '".($namhoc +1)."-12-31' and profile_subject_subject_id in (35,36,19,38,39,34,40,41) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".((int)$namhoc+1)."-05-31')) then 1 else 0 END) 'HKI3'"))		
		->where('profile_year','<',$namhoc.'-06-01')
		->where('profile_school_id','=',$truong)
		->whereIn('profile_subject_subject_id',[35,36,19,38,39,34,40,41])		
		->groupBy('profile_id','profile_name','profile_year','level_old','level_cur','level_new','kp1.money','kp2.money','kp3.money');
		$data11 = DB::table(DB::raw("({$data1->toSql()}) as m"))->mergeBindings( $data1 )
		->select('HKII1','HKI2','HKII2','HKI3','m.money2','m.money3','m.Mien1','m.Mien2','m.Mien3','m.Mien4','m.Mien5','m.Giam70','m.Giam501','m.Giam502','m.profile_id','m.profile_subject_subject_id','m.profile_name','m.profile_year','m.level_old','m.level_cur','m.level_new',
			DB::raw('CASE 
					when m.profile_subject_subject_id in (35,36,19,38,39) then ((m.HKII1*5*m.money2) + m.HKI2*4*(m.money2))
					when m.profile_subject_subject_id = 34 then (((m.HKII1*5*m.money2) + m.HKI2*4*(m.money2))*7)/10 
					when m.profile_subject_subject_id in (40,41) then (((m.HKII1*5*m.money2) + m.HKI2*4*(m.money2))*5)/10 
				END NhuCau'),
			DB::raw('CASE 
					when m.profile_subject_subject_id = 34 then (((m.HKII2*5*m.money3)+m.HKI3*4*(m.money3))*7)/10 
					when m.profile_subject_subject_id in (40,41) then (((m.HKII2*5*m.money3)+m.HKI3*4*(m.money3))*5)/10 
					when m.profile_subject_subject_id in (35,36,19,38,39) then ((m.HKII2*5*m.money3)+m.HKI3*4*(m.money3))
				END DuToan'));

		$data2 = DB::table('qlhs_profile')
		->leftJoin('qlhs_profile_subject','profile_id', '=' ,'profile_subject_profile_id')
		->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$namhoc.'-'.($namhoc + 1).'"'))
		->join('qlhs_kinhphinamhoc as kp1','kp1.idTruong','=',DB::raw('profile_school_id and kp1.codeYear = '.($namhoc - 1).''))
		->join('qlhs_kinhphinamhoc as kp2','kp2.idTruong','=',DB::raw('profile_school_id and kp2.codeYear = '.$namhoc.''))
		->join('qlhs_kinhphinamhoc as kp3','kp3.idTruong','=',DB::raw('profile_school_id and kp3.codeYear = '.($namhoc + 1).''))
		->select('kp1.money as money1','kp2.money as money2','kp3.money as money3','profile_id', 'profile_name','profile_year', 'level_old', 'level_cur', 'level_new', 
				DB::raw('MAX(qlhs_profile_subject.profile_subject_subject_id) as profile_subject_subject_id'), 
				DB::raw('MAX(CASE when profile_subject_subject_id = 35 then 1 else 0 END) Mien1'),
				DB::raw('MAX(CASE when profile_subject_subject_id = 36 then 1 else 0 END) Mien2'),
				DB::raw('MAX(CASE when profile_subject_subject_id = 38 then 1 else 0 END) Mien3'),
				DB::raw('MAX(CASE when profile_subject_subject_id = 19 then 1 else 0 END) Mien4'),
				DB::raw('MAX(CASE when profile_subject_subject_id = 39 then 1 else 0 END) Mien5'),
				DB::raw('MAX(CASE when profile_subject_subject_id = 34 then 1 else 0 END) Giam70'),
				DB::raw('MAX(CASE when profile_subject_subject_id = 40 then 1 else 0 END) Giam501'),
				DB::raw('MAX(CASE when profile_subject_subject_id = 41 then 1 else 0 END) Giam502'),
				DB::raw("MAX(CASE when level_old <> '' and qlhs_profile.profile_year < '".$namhoc."-06-01' and profile_subject_subject_id in (35,36,19,38,39,34,40,41) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".($namhoc)."-01-01')) then 1 else 0 END) 'HKII1'"),
				DB::raw("MAX(CASE when level_cur <> '' and qlhs_profile.profile_year < '".$namhoc."-12-31' and profile_subject_subject_id in (35,36,19,38,39,34,40,41) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".($namhoc)."-05-31')) then 1 else 0 END) 'HKI2'"),
				DB::raw("MAX(CASE when level_cur <> '' and qlhs_profile.profile_year < '".($namhoc + 1)."-06-01' and profile_subject_subject_id in (35,36,19,38,39,34,40,41) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".((int)$namhoc+1)."-01-01')) then 1 else 0 END) 'HKII2'"),
				DB::raw("MAX(CASE when level_new <> '' and qlhs_profile.profile_year < '".($namhoc +1)."-12-31' and profile_subject_subject_id in (35,36,19,38,39,34,40,41) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".((int)$namhoc+1)."-05-31')) then 1 else 0 END) 'HKI3'"))
			->where('profile_year','>',$namhoc.'-05-31')
			->where('profile_year','<',((int)$namhoc+1).'-06-01')
			->where('profile_school_id','=',$truong)
			->whereIn('profile_subject_subject_id',[35,36,19,38,39,34,40,41])
			->groupBy('profile_id','profile_name','profile_year','level_old','level_cur','level_new','kp1.money','kp2.money','kp3.money' );
		$data22 = DB::table(DB::raw("({$data2->toSql()}) as m"))->mergeBindings( $data2 )
		->select('HKII1','HKI2','HKII2','HKI3','m.money2','m.money3','m.Mien1','m.Mien2','m.Mien3','m.Mien4','m.Mien5','m.Giam70','m.Giam501','m.Giam502','m.profile_id','m.profile_subject_subject_id','m.profile_name','m.profile_year','m.level_old','m.level_cur','m.level_new',
			DB::raw('CASE 
					when m.profile_subject_subject_id in (35,36,19,38,39) then ((m.HKII1*5*m.money2)+m.HKI2*4*(m.money2))
					when m.profile_subject_subject_id = 34 then (((m.HKII1*5*m.money2)+m.HKI2*4*(m.money2))*7)/10 
					when m.profile_subject_subject_id in (40,41) then (((m.HKII1*5*m.money2)+m.HKI2*4*(m.money2))*5)/10 
				END NhuCau'),
			DB::raw('CASE 
					when m.profile_subject_subject_id = 34 then (((m.HKII2*5*m.money3)+m.HKI3*4*(m.money3))*7)/10 
					when m.profile_subject_subject_id in (40,41) then (((m.HKII2*5*m.money3)+m.HKI3*4*(m.money3))*5)/10 
					when m.profile_subject_subject_id in (35,36,19,38,39) then ((m.HKII2*5*m.money3)+m.HKI3*4*(m.money3))
				END DuToan'));

		$data3 = DB::table('qlhs_profile')
		->leftJoin('qlhs_profile_subject','profile_id', '=' ,'profile_subject_profile_id')
		->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.($namhoc + 1).'-'.($namhoc + 2).'"'))
		->join('qlhs_kinhphinamhoc as kp1','kp1.idTruong','=',DB::raw('profile_school_id and kp1.codeYear = '.($namhoc - 1).''))
		->join('qlhs_kinhphinamhoc as kp2','kp2.idTruong','=',DB::raw('profile_school_id and kp2.codeYear = '.$namhoc.''))
		->join('qlhs_kinhphinamhoc as kp3','kp3.idTruong','=',DB::raw('profile_school_id and kp3.codeYear = '.($namhoc + 1).''))
		->select('kp1.money as money1','kp2.money as money2','kp3.money as money3','profile_id', 'profile_name','profile_year', 'level_old', 'level_cur', 'level_new', 
				DB::raw('MAX(qlhs_profile_subject.profile_subject_subject_id) as profile_subject_subject_id'), 
				DB::raw('MAX(CASE when profile_subject_subject_id = 35 then 1 else 0 END) Mien1'),
				DB::raw('MAX(CASE when profile_subject_subject_id = 36 then 1 else 0 END) Mien2'),
				DB::raw('MAX(CASE when profile_subject_subject_id = 38 then 1 else 0 END) Mien3'),
				DB::raw('MAX(CASE when profile_subject_subject_id = 19 then 1 else 0 END) Mien4'),
				DB::raw('MAX(CASE when profile_subject_subject_id = 39 then 1 else 0 END) Mien5'),
				DB::raw('MAX(CASE when profile_subject_subject_id = 34 then 1 else 0 END) Giam70'),
				DB::raw('MAX(CASE when profile_subject_subject_id = 40 then 1 else 0 END) Giam501'),
				DB::raw('MAX(CASE when profile_subject_subject_id = 41 then 1 else 0 END) Giam502'),
				DB::raw("MAX(CASE when level_old <> '' and qlhs_profile.profile_year < '".$namhoc."-06-01' and profile_subject_subject_id in (35,36,19,38,39,34,40,41) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".($namhoc)."-01-01')) then 1 else 0 END) 'HKII1'"),
				DB::raw("MAX(CASE when level_old <> '' and qlhs_profile.profile_year < '".$namhoc."-12-31' and profile_subject_subject_id in (35,36,19,38,39,34,40,41) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".($namhoc)."-05-31')) then 1 else 0 END) 'HKI2'"),
				DB::raw("MAX(CASE when level_old <> '' and qlhs_profile.profile_year < '".($namhoc + 1)."-06-01' and profile_subject_subject_id in (35,36,19,38,39,34,40,41) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".((int)$namhoc+1)."-01-01')) then 1 else 0 END) 'HKII2'"),
				DB::raw("MAX(CASE when level_cur <> '' and qlhs_profile.profile_year < '".($namhoc +1)."-12-31' and profile_subject_subject_id in (35,36,19,38,39,34,40,41) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".((int)$namhoc+1)."-05-31')) then 1 else 0 END) 'HKI3'"))
			->where('profile_year','>',((int)$namhoc+1).'-05-31')
			->where('profile_year','<',((int)$namhoc+2).'-01-01')
			->where('profile_school_id','=',$truong)
			->whereIn('profile_subject_subject_id',[35,36,19,38,39,34,40,41])
			->groupBy('profile_id','profile_name','profile_year','level_old','level_cur','level_new','kp1.money','kp2.money','kp3.money' );
		$data33 = DB::table(DB::raw("({$data3->toSql()}) as m"))->mergeBindings( $data3 )
		->select('HKII1','HKI2','HKII2','HKI3','m.money2','m.money3','m.Mien1','m.Mien2','m.Mien3','m.Mien4','m.Mien5','m.Giam70','m.Giam501','m.Giam502','m.profile_id','m.profile_subject_subject_id','m.profile_name','m.profile_year','m.level_old','m.level_cur','m.level_new',
			DB::raw('CASE 
					when m.profile_subject_subject_id in (35,36,19,38,39) then ((m.HKII1*5*m.money2)+m.HKI2*4*(m.money2))
					when m.profile_subject_subject_id = 34 then (((m.HKII1*5*m.money2)+m.HKI2*4*(m.money2))*7)/10 
					when m.profile_subject_subject_id in (40,41) then (((m.HKII1*5*m.money2)+m.HKI2*4*(m.money2))*5)/10 
				END NhuCau'),
			DB::raw('CASE 
					when m.profile_subject_subject_id = 34 then (((m.HKII2*5*m.money3)+m.HKI3*4*(m.money3))*7)/10 
					when m.profile_subject_subject_id in (40,41) then (((m.HKII2*5*m.money3)+m.HKI3*4*(m.money3))*5)/10 
					when m.profile_subject_subject_id in (35,36,19,38,39) then ((m.HKII2*5*m.money3)+m.HKI3*4*(m.money3))
				END DuToan'));

		if($data11->count()==0 && $data22->count()==0 && $data33->count()==0){
				$json['success'] = "Danh sách trống ";
		}else{
			$import =  $this->insertReport($data1,$data2,$data3,$truong,$namhoc,$user,$now,$report_name,$report_user_sign,$user_name,$status,$filename_attach,$files,$note);
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
		$dir = storage_path().'/files/MGHP';
        if($data11->count()>0){
			foreach ($data11->get() as $key => $value) {
				$nhucau = 0;
				$dutoan = 0;

				if ($value->Mien1 > 0 || $value->Mien2 > 0 || $value->Mien3 > 0 || $value->Mien4 > 0 || $value->Mien5 > 0) {
					$nhucau = ($value->HKII1 * 5 * $value->money1) + ($value->HKI2 * 4 * $value->money2);
					$dutoan = ($value->HKII2 * 5 * $value->money2) + ($value->HKI3 * 4 * $value->money3);
				}
				else {
					if ($value->Giam70 > 0) {
						$nhucau = ((($value->HKII1 * 5 * $value->money1) + ($value->HKI2 * 4 * $value->money2)) * 7) / 10;
						$dutoan = ((($value->HKII2 * 5 * $value->money2) + ($value->HKI3 * 4 * $value->money3)) * 7) / 10;
					}
					else {
						$nhucau = ((($value->HKII1 * 5 * $value->money1) + ($value->HKI2 * 4 * $value->money2)) * 5) / 10;
						$dutoan = ((($value->HKII2 * 5 * $value->money2) + ($value->HKI3 * 4 * $value->money3)) * 5) / 10;
					}
				}

				$json['1'] =DB::table('qlhs_miengiamhocphi')->insert([
					'id_profile' => $value->profile_id,
					'mienphi_1' => $value->Mien1,
					'mienphi_2' => $value->Mien2,
					'mienphi_3' => $value->Mien3,
					'mienphi_4' => $value->Mien4,
					'mienphi_5' => $value->Mien5,
					'giam_70' => $value->Giam70,
					'giam_50_1' => $value->Giam501,
					'giam_50_2' => $value->Giam502,
					'hocky2_old' => $value->HKII1,
					'hocky1_cur' => $value->HKI2,
					'hocky2_cur' => $value->HKII2,
					'hocky1_new' => $value->HKI3,
					'hocphi_old' => $value->money1,
					'hocphi_new' => $value->money2,
					'nhu_cau' => $nhucau,
					'du_toan' => $dutoan,
					'year_old' => (int)$namhoc,
					'year_cur' => (int)$namhoc+1,
					'type' => 1,
					'type_code' => 'MGHP-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time
					]);
			}
		}else{
			$json['1'] = 1;
		}
		if($data22->count()>0){
			foreach ($data22->get() as $key => $value) {
				$nhucau = 0;
				$dutoan = 0;

				if ($value->Mien1 > 0 || $value->Mien2 > 0 || $value->Mien3 > 0 || $value->Mien4 > 0 || $value->Mien5 > 0) {
					$nhucau = ($value->HKII1 * 5 * $value->money1) + ($value->HKI2 * 4 * $value->money2);
					$dutoan = ($value->HKII2 * 5 * $value->money2) + ($value->HKI3 * 4 * $value->money3);
				}
				else {
					if ($value->Giam70 > 0) {
						$nhucau = ((($value->HKII1 * 5 * $value->money1) + ($value->HKI2 * 4 * $value->money2)) * 7) / 10;
						$dutoan = ((($value->HKII2 * 5 * $value->money2) + ($value->HKI3 * 4 * $value->money3)) * 7) / 10;
					}
					else {
						$nhucau = ((($value->HKII1 * 5 * $value->money1) + ($value->HKI2 * 4 * $value->money2)) * 5) / 10;
						$dutoan = ((($value->HKII2 * 5 * $value->money2) + ($value->HKI3 * 4 * $value->money3)) * 5) / 10;
					}
				}

				$json['2'] = DB::table('qlhs_miengiamhocphi')->insert([
					'id_profile' => $value->profile_id,
					'mienphi_1' => $value->Mien1,
					'mienphi_2' => $value->Mien2,
					'mienphi_3' => $value->Mien3,
					'mienphi_4' => $value->Mien4,
					'mienphi_5' => $value->Mien5,
					'giam_70' => $value->Giam70,
					'giam_50_1' => $value->Giam501,
					'giam_50_2' => $value->Giam502,
					'hocky2_old' => $value->HKII1,
					'hocky1_cur' => $value->HKI2,
					'hocky2_cur' => $value->HKII2,
					'hocky1_new' => $value->HKI3,
					'hocphi_old' => $value->money1,
					'hocphi_new' => $value->money2,
					'nhu_cau' => $nhucau,
					'du_toan' => $dutoan,
					'year_old' => (int)$namhoc,
					'year_cur' => (int)$namhoc+1,
					'type' => 2,
					'type_code' => 'MGHP-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time
					]);
			}
		}else{
			$json['2'] = 1;
		}
		if($data33->count()>0){
			foreach ($data33->get() as $key => $value) {
				$nhucau = 0;
				$dutoan = 0;

				if ($value->Mien1 > 0 || $value->Mien2 > 0 || $value->Mien3 > 0 || $value->Mien4 > 0 || $value->Mien5 > 0) {
					$nhucau = ($value->HKII1 * 5 * $value->money1) + ($value->HKI2 * 4 * $value->money2);
					$dutoan = ($value->HKII2 * 5 * $value->money2) + ($value->HKI3 * 4 * $value->money3);
				}
				else {
					if ($value->Giam70 > 0) {
						$nhucau = ((($value->HKII1 * 5 * $value->money1) + ($value->HKI2 * 4 * $value->money2)) * 7) / 10;
						$dutoan = ((($value->HKII2 * 5 * $value->money2) + ($value->HKI3 * 4 * $value->money3)) * 7) / 10;
					}
					else {
						$nhucau = ((($value->HKII1 * 5 * $value->money1) + ($value->HKI2 * 4 * $value->money2)) * 5) / 10;
						$dutoan = ((($value->HKII2 * 5 * $value->money2) + ($value->HKI3 * 4 * $value->money3)) * 5) / 10;
					}
				}

				$json['3'] = DB::table('qlhs_miengiamhocphi')->insert([
					'id_profile' => $value->profile_id,
					'mienphi_1' => $value->Mien1,
					'mienphi_2' => $value->Mien2,
					'mienphi_3' => $value->Mien3,
					'mienphi_4' => $value->Mien4,
					'mienphi_5' => $value->Mien5,
					'giam_70' => $value->Giam70,
					'giam_50_1' => $value->Giam501,
					'giam_50_2' => $value->Giam502,
					'hocky2_old' => $value->HKII1,
					'hocky1_cur' => $value->HKI2,
					'hocky2_cur' => $value->HKII2,
					'hocky1_new' => $value->HKI3,
					'hocphi_old' => $value->money1,
					'hocphi_new' => $value->money2,
					'nhu_cau' => $nhucau,
					'du_toan' => $dutoan,
					'year_old' => (int)$namhoc,
					'year_cur' => (int)$namhoc+1,
					'type' => 3,
					'type_code' => 'MGHP-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time
					]);
			}

		}else{
			$json['3'] = 1;
		}
		
		if((int)$json['1']> 0 && (int)$json['2']>0 && (int)$json['3']>0){
			//$check = DB::table('qlhs_hosobaocao')->where('report_type','=','MGHP-'.$user.'-'.$truong.''.$namhoc.''.((int)$namhoc+1))->where('report_status','=',0)->count();
			if(trim($files) != ""){
				 if(file_exists($dir.'/'. $filename_attach)){
						$files->move($dir, $filename_attach.'-'.$time);	
				 		//File::delete($dir.'/'. $filename_attach);	
				 }else{
				 		$files->move($dir, $filename_attach);	
				 }
			}
			$insert_returnID = DB::table('qlhs_hosobaocao')->insertGetId([
					'report_name' => $report_name,
					'report_type' => 'MGHP-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time,
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
					'report' => 'MGHP'
				]);

				if(!is_null($insert_returnID) && $insert_returnID > 0 ){
					$this->exportforSchools($insert_returnID);
					if (file_exists(storage_path().'/exceldownload/MGHP/'.'MGHP-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time.'.xlsx')) {
						return TRUE;
					}
					else {
						$deleteHoso = DB::table('qlhs_hosobaocao')->where('report_type', 'LIKE', '%'.'MGHP-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time.'%')->delete();
						$deleteMGHP = DB::table('qlhs_miengiamhocphi')->where('type_code', 'LIKE', '%'.'MGHP-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time.'%')->delete();
						return false;
					}
				}else{
					return false;
				}
			//}else{
				// DB::table('qlhs_hosobaocao')->where('report_type','=','MGHP-'.$user.'-'.$truong.''.$namhoc.''.((int)$namhoc+1))->where('report_status','=',0)->delete();
				// DB::table('qlhs_hosobaocao')->update([
				// 	'report_name' => $report_name,
				// 	'report_type' => 'MGHP-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time,
				// 	'report_date' => $now,
				// 	//'created_at' => $now,
				// 	'updated_at' => $now,
				// 	//'create_userid' => $user,
				// 	'update_userid' => $user,
				// 	'report_user' => $user_name,
				// 	'report_user_sign' => $report_user_sign,
				// 	'report_attach_name' => $filename_attach,
				// 	'report_nature' => $status,
				// 	'report' => 'MGHP'
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
    	//$datas = DB::table('qlhs_hosobaocao')->where('report','=','MGHP');
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
    	$data = DB::table('qlhs_hosobaocao')->where('report','=','MGHP')->where('report_id','=',$id)->select('report_attach_name')->first(); 
    	$dir = storage_path().'/files/MGHP/'.$data->report_attach_name;
    	return response()->download($dir,$data->report_attach_name);
    }
    public function delete_report($id){
    	
    	$json = [];
    	$data = DB::table('qlhs_hosobaocao')->where('report_id','=',$id)->select('report_type','report_attach_name','report')->first(); 
    	$del1 = DB::table('qlhs_miengiamhocphi')->where('type_code','=',$data->report_type)->delete();
    	$dir = storage_path().'/files/'.$data->report;
    	if($del1>0){
    		$del2 = DB::table('qlhs_hosobaocao')->where('report_id','=',$id)->delete();

    		if(file_exists($dir.'/'. $data->report_attach_name)){
					//$files->move($dir, $filename_attach.'-'.$time);	
			 		File::delete($dir.'/'. $data->report_attach_name);	
			}

			if (file_exists(storage_path().'/exceldownload/MGHP/'.$data->report_type.'.xlsx')) {
				File::delete(storage_path().'/exceldownload/MGHP/'.$data->report_type.'.xlsx');
			}

    		if($del2> 0){
    			$json['success'] = 'Xóa thành công';
    		}else{
    			$json['error'] = 'Xóa lỗi';
    		}
    	}
    	return $json;
    }
    public function loadData(Request $req){
    	$json = [];
    	$start = $req->input('start');
    	$limit = $req->input('limit');
    	$user = Auth::user()->id;

    	$getIdTruong = DB::table('users')->select('truong_id')->where('id', '=', $user)->first();

    	$datas = DB::table('qlhs_hosobaocao')->where('report', '=', 'MGHP');

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
    public function sendMGHP(Request $req){
    	try{
	    	$id = $req->input('id');
	    	$type = $req->input('type');

	    	$list_id_nguoinhan = $req->input('list_id_nguoinhan');
	    	$list_id_cc = $req->input('list_id_cc');
    		
    		$time = time();
	    	$hosobaocao = HoSoBaoCao::find($id);
	    	$hosobaocao->report_status = 1;
	    	$hosobaocao->report_verify = 0;
	    	//$hosobaocao->report_type = 0;
	    	$hosobaocao->report_user_send = Auth::user()->id;
	    	$hosobaocao->save();
	    	//$dir = storage_path().'/excel/MGHP';
	  //   	if(file_exists($dir.'/'. $hosobaocao->report_type.'_'.Auth::user()->username)){
			// 			//$files->move($dir, $hosobaocao->report_type.'_'.Auth::user()->username.'_'.$time);	
			// 	 		$filename = $hosobaocao->report_type.'_'.Auth::user()->username.'_'.$time;
			// 	 }else{
			// 	 		//$files->move($dir, $hosobaocao->report_type.'_'.Auth::user()->username);	
			// 	 		
			// }
	    	// $this->exportforSchool($id,true);
	    	$now = Carbon::now('Asia/Ho_Chi_Minh');
			//$string = str_random(5);
	    	$mghp = DB::table('qlhs_thamdinh')->insert([
	    			'thamdinh_name' => $type.'-'.$time,
					'thamdinh_type' => $type,
					'thamdinh_hoso_id' => $id,
					'thamdinh_trangthai' => 0,
					'thamdinh_content' => $hosobaocao->report_note,
					'thamdinh_ngaygui' => $now,
					'thamdinh_file_dinhkem' => $hosobaocao->report_type.'.xlsx',
					'thamdinh_file_dikem' => $hosobaocao->report_attach_name, 
					'thamdinh_nguoigui' => Auth::user()->id,
					'thamdinh_nguoi_nhan' => $list_id_nguoinhan,
					'thamdinh_nguoi_cc' => $list_id_cc
					]);
	    	$json['success'] = "Gửi thành công.";
	    }catch(\Exception $e){
	    	$json['error'] = "Gửi lỗi.".$e;
	    }
	    return $json;
    }

    public function downloadfile_Export($id){
    	$data = DB::table('qlhs_hosobaocao')->where('report','=','MGHP')->where('report_id','=',$id)->select('report_type')->first(); 
    	$dir = storage_path().'/exceldownload/MGHP/'.$data->report_type.'.xlsx';
    	return response()->download($dir, $data->report_type.'.xlsx');
    }

 	public function exportforSchools($id){
 		$this->exportforSchool($id, TRUE);
 	}

	public function exportforSchool($id,$type = true){
	//$type = true;
    	if (is_null($id) || empty($id) || $id == 0) {
    		return "Mời bấm vào tên danh sách muốn kết xuất để kêt xuất file Excel!";
    	}

    	$getSchoolName = DB::table('qlhs_hosobaocao')->join('qlhs_schools', 'qlhs_schools.schools_id', '=', 'qlhs_hosobaocao.report_id_truong')->where('qlhs_hosobaocao.report_id', '=', $id)->select('qlhs_schools.schools_name', 'qlhs_hosobaocao.report_type', 'qlhs_hosobaocao.report_year')->first();
		$data_nghilc = [];
		$data_nghilc['aT'] = 'Học sinh có mặt tại trường tháng 5/' . $getSchoolName->report_year;
        $data_nghilc['bT'] = 'Học sinh dự kiến tuyển mới năm học ' . $getSchoolName->report_year . '-' . ((int)$getSchoolName->report_year + 1);
        $data_nghilc['cT'] = 'Học sinh dự kiến tuyển mới năm học ' . ((int)$getSchoolName->report_year + 1) . '-' . ((int)$getSchoolName->report_year + 2);
    	if ($getSchoolName->report_type != null && $getSchoolName->report_type != "") {
    		$data_nghilc['aCount'] = $this->countValue('1',$getSchoolName->report_type);
    		$data_nghilc['bCount'] = $this->countValue('2',$getSchoolName->report_type);
    		$data_nghilc['cCount'] = $this->countValue('3',$getSchoolName->report_type);
    		$data_nghilc['TotalCount'] = $this->countValue(null, $getSchoolName->report_type);
    		//Get by type A    		
    		$data_nghilc['a'] = DB::table('qlhs_miengiamhocphi')
    		->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_miengiamhocphi.id_profile')
    		->leftJoin(DB::raw('(select decided_profile_id, max(decided_id) decided_id, max(decided_confirmation) decided_confirmation, max(decided_confirmdate) decided_confirmdate, max(decided_number) decided_number, max(decided_type) decided_type from qlhs_decided GROUP BY decided_profile_id) as qlhs_decided'), 'qlhs_decided.decided_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_decided.decided_type = "MGHP"'))
    		->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
    		->leftJoin('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_miengiamhocphi.id_profile and qlhs_profile_history.history_year = "'.$getSchoolName->report_year.'-'.($getSchoolName->report_year + 1).'"'))
    		->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
    		->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
    		->where('qlhs_miengiamhocphi.type_code', '=', $getSchoolName->report_type) 
    		->where('qlhs_miengiamhocphi.type', '=', 1)
    		->select('qlhs_profile_history.history_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_decided.decided_id', 'qlhs_decided.decided_confirmation', 'qlhs_decided.decided_number', 'qlhs_decided.decided_confirmdate', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'qlhs_miengiamhocphi.*')->DISTINCT()->get();

    		$data_nghilc['b'] = DB::table('qlhs_miengiamhocphi')
    		->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_miengiamhocphi.id_profile')
    		->leftJoin(DB::raw('(select decided_profile_id, max(decided_id) decided_id, max(decided_confirmation) decided_confirmation, max(decided_confirmdate) decided_confirmdate, max(decided_number) decided_number, max(decided_type) decided_type from qlhs_decided GROUP BY decided_profile_id) as qlhs_decided'), 'qlhs_decided.decided_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_decided.decided_type = "MGHP"'))
    		->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
    		->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_miengiamhocphi.id_profile and qlhs_profile_history.history_year = "'.$getSchoolName->report_year.'-'.($getSchoolName->report_year + 1).'"'))
    		->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
    		->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
    		->where('qlhs_miengiamhocphi.type_code', '=', $getSchoolName->report_type)
    		->where('qlhs_miengiamhocphi.type', '=', 2)
    		->select('qlhs_profile_history.history_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_decided.decided_id', 'qlhs_decided.decided_confirmation', 'qlhs_decided.decided_number', 'qlhs_decided.decided_confirmdate', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'qlhs_miengiamhocphi.*')->DISTINCT()->get();

    		$data_nghilc['c'] = DB::table('qlhs_miengiamhocphi')
    		->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_miengiamhocphi.id_profile')
    		->leftJoin(DB::raw('(select decided_profile_id, max(decided_id) decided_id, max(decided_confirmation) decided_confirmation, max(decided_confirmdate) decided_confirmdate, max(decided_number) decided_number, max(decided_type) decided_type from qlhs_decided GROUP BY decided_profile_id) as qlhs_decided'), 'qlhs_decided.decided_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_decided.decided_type = "MGHP"'))
    		->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
    		->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_miengiamhocphi.id_profile and qlhs_profile_history.history_year = "'.($getSchoolName->report_year + 1).'-'.($getSchoolName->report_year + 2).'"'))
    		->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
    		->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
    		->where('qlhs_miengiamhocphi.type_code', '=', $getSchoolName->report_type)
    		->where('qlhs_miengiamhocphi.type', '=', 3)
    		->select('qlhs_profile_history.history_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_decided.decided_id', 'qlhs_decided.decided_confirmation', 'qlhs_decided.decided_number', 'qlhs_decided.decided_confirmdate', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'qlhs_miengiamhocphi.*')->DISTINCT()->get();
    	}
        $data_nghilc['schools_name'] = $getSchoolName->schools_name;
        $data_nghilc['report_year'] = $getSchoolName->report_year;
        $this->addCellExcel($data_nghilc, $getSchoolName->report_type, $type);
	}

private function addCellExcel($data_nghilc,$filename,$type=true){
	$excel = 	Excel::load(storage_path().'/exceltemplate/laphosoMGHP.xlsx', function($reader) use($data_nghilc){
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
	    $FormatCurrency = array(
	    	'VND' => '#,###'
			//'type'  => PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_VND
	    );
		$row = 5;

		$col = 0;
		$colA = 0;

		$class_lv1 = 0;
		$class_lv2 = 0;
		$class_lv3 = 0;

		//-----------------------------------------Title------------------------------------------------------------------------------------------------
		$reader->getActiveSheet()->setCellValueByColumnAndRow(2, 2, 'Thông tin cơ bản (tính tại thời điểm tháng 5/'.$data_nghilc['report_year'].')')->getStyle('C2')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

		$reader->getActiveSheet()->setCellValueByColumnAndRow(2, 4, 'Học kì II năm học '.($data_nghilc['report_year'] - 1).'-'.$data_nghilc['report_year'])->getStyle('C4')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
		$reader->getActiveSheet()->setCellValueByColumnAndRow(3, 4, 'Năm học '.$data_nghilc['report_year'].'-'.($data_nghilc['report_year'] + 1))->getStyle('D4')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
		$reader->getActiveSheet()->setCellValueByColumnAndRow(4, 4, 'Năm học '.($data_nghilc['report_year'] + 1).'-'.($data_nghilc['report_year'] + 2))->getStyle('E4')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

		$reader->getActiveSheet()->setCellValueByColumnAndRow(22, 4, 'Học kỳ II năm học '.($data_nghilc['report_year'] - 1).'-'.$data_nghilc['report_year'])->getStyle('W4')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
		$reader->getActiveSheet()->setCellValueByColumnAndRow(23, 4, 'Học kỳ I năm học '.$data_nghilc['report_year'].'-'.($data_nghilc['report_year'] + 1))->getStyle('X4')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
		$reader->getActiveSheet()->setCellValueByColumnAndRow(24, 4, 'Học kỳ II năm học '.$data_nghilc['report_year'].'-'.($data_nghilc['report_year'] + 1))->getStyle('Y4')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
		$reader->getActiveSheet()->setCellValueByColumnAndRow(25, 4, 'Học kỳ I năm học '.($data_nghilc['report_year'] + 1).'-'.($data_nghilc['report_year'] + 2))->getStyle('Z4')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

		$reader->getActiveSheet()->setCellValueByColumnAndRow(26, 3, 'Năm học '.($data_nghilc['report_year'] - 1).'-'.$data_nghilc['report_year'])->getStyle('AA3')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
		$reader->getActiveSheet()->setCellValueByColumnAndRow(27, 3, 'Năm học '.$data_nghilc['report_year'].'-'.($data_nghilc['report_year'] + 1).', Năm học '.($data_nghilc['report_year'] + 1).'-'.($data_nghilc['report_year'] + 2))->getStyle('AB3')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

		$reader->getActiveSheet()->setCellValueByColumnAndRow(28, 2, 'Nhu cầu kinh phí năm '.$data_nghilc['report_year'])->getStyle('AC2')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
		$reader->getActiveSheet()->setCellValueByColumnAndRow(29, 2, 'Dự toán kinh phí năm '.($data_nghilc['report_year'] + 1))->getStyle('AD2')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
		//-----------------------------------------End Title----------------------------------------------------------------------------------------------------

		//$data_nghilc['aCount'][0]
		$reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row,$data_nghilc['schools_name'])->getStyle('B'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($FontArrayitalic);
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
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(12, $row, '')->getStyle('M'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(13, $row, '')->getStyle('N'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		$reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row,$data_nghilc['TotalCount']->tongmien1)->getStyle('O'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
	  	$reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row,$data_nghilc['TotalCount']->tongmien2)->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
	  	$reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row,$data_nghilc['TotalCount']->tongmien3)->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row,$data_nghilc['TotalCount']->tongmien4)->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(18, $row,$data_nghilc['TotalCount']->tongmien5)->getStyle('S'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row,$data_nghilc['TotalCount']->tonggiam70)->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row,$data_nghilc['TotalCount']->tonggiam501)->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row,$data_nghilc['TotalCount']->tonggiam502)->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row,$data_nghilc['TotalCount']->tonghk12)->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row,$data_nghilc['TotalCount']->tonghk21)->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(24, $row,$data_nghilc['TotalCount']->tonghk22)->getStyle('Y'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(25, $row,$data_nghilc['TotalCount']->tonghk31)->getStyle('Z'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(26, $row, '')->getStyle('AA'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(27, $row, '')->getStyle('AB'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(28, $row,$data_nghilc['TotalCount']->tongnc)->getStyle('AC'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight)->applyFromArray($FontArrayitalic)->applyFromArray($FormatCurrency);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(29, $row,$data_nghilc['TotalCount']->tongdt)->getStyle('AD'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight)->applyFromArray($FontArrayitalic);

		$row++;
		$reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row,'a')->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

	 	$reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row,$data_nghilc['aT'])->getStyle('B'.$row)->applyFromArray($FontArray);
		$colAnext = 14;
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
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(12, $row, '')->getStyle('M'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(13, $row, '')->getStyle('N'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	 	$reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row,$data_nghilc['aCount']->tongmien1)->getStyle('O'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	  	$reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row,$data_nghilc['aCount']->tongmien2)->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	  	$reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row,$data_nghilc['aCount']->tongmien3)->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row,$data_nghilc['aCount']->tongmien4)->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(18, $row,$data_nghilc['aCount']->tongmien5)->getStyle('S'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row,$data_nghilc['aCount']->tonggiam70)->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row,$data_nghilc['aCount']->tonggiam501)->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row,$data_nghilc['aCount']->tonggiam502)->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row,$data_nghilc['aCount']->tonghk12)->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row,$data_nghilc['aCount']->tonghk21)->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(24, $row,$data_nghilc['aCount']->tonghk22)->getStyle('Y'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(25, $row,$data_nghilc['aCount']->tonghk31)->getStyle('Z'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(26, $row, '')->getStyle('AA'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(27, $row, '')->getStyle('AB'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(28, $row,$data_nghilc['aCount']->tongnc)->getStyle('AC'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(29, $row,$data_nghilc['aCount']->tongdt)->getStyle('AD'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		$indexa = 0;
		if($data_nghilc['a']->count()>0){
			foreach($data_nghilc['a'] as $key => $value){
				$col = 0;	$row++;

				// $strYear = substr((string)$value->history_year, 0, 4);
				// if ($strYear < $data_nghilc['report_year']) {
				// 	$class_lv1 = $value->level_next_1;
				// 	$class_lv2 = $value->level_next_2;
				// 	$class_lv3 = $value->level_next_3;
				// }

				// if ($strYear == $data_nghilc['report_year']) {
				// 	$class_lv1 = $value->level_next_1;
				// 	$class_lv2 = $value->level_next_2;
				// 	$class_lv3 = $value->level_next_3;
				// }

				// if ($strYear > $data_nghilc['report_year']) {
				// 	$class_lv1 = 0;
				// 	$class_lv2 = 0;
				// 	$class_lv3 = $value->level_next_1;
				// }

				$decided_date = "";
				if (!is_null($value->decided_confirmdate) && !empty($value->decided_confirmdate)) {					
					$decided_date = Carbon::parse($value->decided_confirmdate)->format('d-m-Y');
				}

			    $reader->getActiveSheet()->setCellValueByColumnAndRow($col, $row,++$indexa)->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($style);

			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_name)->getStyle('B'.$row)->applyFromArray($borderArray);
			        	
				$reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->level_old)->getStyle('C'.$row)->applyFromArray($borderArray);
			        	
				$reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->level_cur)->getStyle('D'.$row)->applyFromArray($borderArray);
			        	
				$reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->level_new)->getStyle('E'.$row)->applyFromArray($borderArray);
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,Carbon::parse($value->profile_birthday)->format('d-m-Y'))->getStyle('F'.$row)->applyFromArray($borderArray); 
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->nationals_name)->getStyle('G'.$row)->applyFromArray($borderArray);  
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_household)->getStyle('H'.$row)->applyFromArray($borderArray); 
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->tenxa)->getStyle('I'.$row)->applyFromArray($borderArray); 
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->tenhuyen)->getStyle('J'.$row)->applyFromArray($borderArray); 

			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_parentname)->getStyle('K'.$row)->applyFromArray($borderArray);

			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->decided_confirmation)->getStyle('L'.$row)->applyFromArray($borderArray);   	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->decided_number)->getStyle('M'.$row)->applyFromArray($borderArray);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$decided_date)->getStyle('N'.$row)->applyFromArray($borderArray);

			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->mienphi_1)->getStyle('O'.$row)->applyFromArray($borderArray)->applyFromArray($style);
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->mienphi_2)->getStyle('P'.$row)->applyFromArray($borderArray)->applyFromArray($style);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->mienphi_3)->getStyle('Q'.$row)->applyFromArray($borderArray)->applyFromArray($style);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->mienphi_4)->getStyle('R'.$row)->applyFromArray($borderArray)->applyFromArray($style);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->mienphi_5)->getStyle('S'.$row)->applyFromArray($borderArray)->applyFromArray($style);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->giam_70)->getStyle('T'.$row)->applyFromArray($borderArray)->applyFromArray($style);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->giam_50_1)->getStyle('U'.$row)->applyFromArray($borderArray)->applyFromArray($style);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->giam_50_2)->getStyle('V'.$row)->applyFromArray($borderArray)->applyFromArray($style);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_old)->getStyle('W'.$row)->applyFromArray($borderArray)->applyFromArray($style);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_cur)->getStyle('X'.$row)->applyFromArray($borderArray)->applyFromArray($style);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_cur)->getStyle('Y'.$row)->applyFromArray($borderArray)->applyFromArray($style);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_new)->getStyle('Z'.$row)->applyFromArray($borderArray)->applyFromArray($style);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocphi_old)->getStyle('AA'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocphi_new)->getStyle('AB'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->nhu_cau)->getStyle('AC'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->du_toan)->getStyle('AD'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				}
			}
		//b

		$row++;
			$reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row,'b')->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

	 	$reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row,$data_nghilc['bT'])->getStyle('B'.$row)->applyFromArray($FontArray);
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
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(12, $row, '')->getStyle('M'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(13, $row, '')->getStyle('N'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	 	$reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row,$data_nghilc['bCount']->tongmien1)->getStyle('O'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	  	$reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row,$data_nghilc['bCount']->tongmien2)->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	  	$reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row,$data_nghilc['bCount']->tongmien3)->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row,$data_nghilc['bCount']->tongmien4)->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(18, $row,$data_nghilc['bCount']->tongmien5)->getStyle('S'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row,$data_nghilc['bCount']->tonggiam70)->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row,$data_nghilc['bCount']->tonggiam501)->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row,$data_nghilc['bCount']->tonggiam502)->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row,$data_nghilc['bCount']->tonghk12)->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row,$data_nghilc['bCount']->tonghk21)->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(24, $row,$data_nghilc['bCount']->tonghk22)->getStyle('Y'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(25, $row,$data_nghilc['bCount']->tonghk31)->getStyle('Z'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(26, $row, '')->getStyle('AA'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(27, $row, '')->getStyle('AB'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(28, $row,$data_nghilc['bCount']->tongnc)->getStyle('AC'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(29, $row,$data_nghilc['bCount']->tongdt)->getStyle('AD'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		$indexb = 0;

		if($data_nghilc['b']->count()>0){
			foreach($data_nghilc['b'] as $key => $value){
				$col = 0;	$row++;

				// $strYear = substr((string)$value->history_year, 0, 4);
				// if ($strYear < $data_nghilc['report_year']) {
				// 	$class_lv1 = $value->level_next;
				// 	$class_lv2 = $value->level_next_1;
				// 	$class_lv3 = $value->level_next_2;
				// }

				// if ($strYear == $data_nghilc['report_year']) {
				// 	$class_lv1 = 0;
				// 	$class_lv2 = $value->level_next_1;
				// 	$class_lv3 = $value->level_next_2;
				// }

				// if ($strYear > $data_nghilc['report_year']) {
				// 	$class_lv1 = 0;
				// 	$class_lv2 = 0;
				// 	$class_lv3 = $value->level_next_1;
				// }

				$decided_date = "";
				if (!is_null($value->decided_confirmdate) && !empty($value->decided_confirmdate)) {					
					$decided_date = Carbon::parse($value->decided_confirmdate)->format('d-m-Y');
				}

			    $reader->getActiveSheet()->setCellValueByColumnAndRow($col, $row,++$indexb)->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($style);

			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_name)->getStyle('B'.$row)->applyFromArray($borderArray);
			        	
				$reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->level_old)->getStyle('C'.$row)->applyFromArray($borderArray);
			        	
				$reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->level_cur)->getStyle('D'.$row)->applyFromArray($borderArray);
			        	
				$reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->level_new)->getStyle('E'.$row)->applyFromArray($borderArray);
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,Carbon::parse($value->profile_birthday)->format('d-m-Y'))->getStyle('F'.$row)->applyFromArray($borderArray); 
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->nationals_name)->getStyle('G'.$row)->applyFromArray($borderArray);  
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_household)->getStyle('H'.$row)->applyFromArray($borderArray); 
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->tenxa)->getStyle('I'.$row)->applyFromArray($borderArray); 
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->tenhuyen)->getStyle('J'.$row)->applyFromArray($borderArray); 

			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_parentname)->getStyle('K'.$row)->applyFromArray($borderArray);     	
			    
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->decided_confirmation)->getStyle('L'.$row)->applyFromArray($borderArray);   	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->decided_number)->getStyle('M'.$row)->applyFromArray($borderArray);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$decided_date)->getStyle('N'.$row)->applyFromArray($borderArray);

			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->mienphi_1)->getStyle('O'.$row)->applyFromArray($borderArray)->applyFromArray($style);
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->mienphi_2)->getStyle('P'.$row)->applyFromArray($borderArray)->applyFromArray($style);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->mienphi_3)->getStyle('Q'.$row)->applyFromArray($borderArray)->applyFromArray($style);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->mienphi_4)->getStyle('R'.$row)->applyFromArray($borderArray)->applyFromArray($style);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->mienphi_5)->getStyle('S'.$row)->applyFromArray($borderArray)->applyFromArray($style);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->giam_70)->getStyle('T'.$row)->applyFromArray($borderArray)->applyFromArray($style);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->giam_50_1)->getStyle('U'.$row)->applyFromArray($borderArray)->applyFromArray($style);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->giam_50_2)->getStyle('V'.$row)->applyFromArray($borderArray)->applyFromArray($style);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_old)->getStyle('W'.$row)->applyFromArray($borderArray)->applyFromArray($style);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_cur)->getStyle('X'.$row)->applyFromArray($borderArray)->applyFromArray($style);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_cur)->getStyle('Y'.$row)->applyFromArray($borderArray)->applyFromArray($style);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_new)->getStyle('Z'.$row)->applyFromArray($borderArray)->applyFromArray($style);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocphi_old)->getStyle('AA'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocphi_new)->getStyle('AB'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->nhu_cau)->getStyle('AC'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->du_toan)->getStyle('AD'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				}
			}

			//c

		$row++;
		$reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row,'c')->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

	 	$reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row,$data_nghilc['cT'])->getStyle('B'.$row)->applyFromArray($FontArray);
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
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(12, $row, '')->getStyle('M'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(13, $row, '')->getStyle('N'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	 	$reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row,$data_nghilc['cCount']->tongmien1)->getStyle('O'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	  	$reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row,$data_nghilc['cCount']->tongmien2)->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	  	$reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row,$data_nghilc['cCount']->tongmien3)->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row,$data_nghilc['cCount']->tongmien4)->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(18, $row,$data_nghilc['cCount']->tongmien5)->getStyle('S'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row,$data_nghilc['cCount']->tonggiam70)->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row,$data_nghilc['cCount']->tonggiam501)->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row,$data_nghilc['cCount']->tonggiam502)->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row,$data_nghilc['cCount']->tonghk12)->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row,$data_nghilc['cCount']->tonghk21)->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(24, $row,$data_nghilc['cCount']->tonghk22)->getStyle('Y'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(25, $row,$data_nghilc['cCount']->tonghk31)->getStyle('Z'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(26, $row, '')->getStyle('AA'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(27, $row, '')->getStyle('AB'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(28, $row,$data_nghilc['cCount']->tongnc)->getStyle('AC'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
	    $reader->getActiveSheet()->setCellValueByColumnAndRow(29, $row,$data_nghilc['cCount']->tongdt)->getStyle('AD'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		$indexc = 0;
		if($data_nghilc['c']->count()>0){
			foreach($data_nghilc['c'] as $key => $value){
				$col = 0;	$row++;

				// $strYear = substr((string)$value->history_year, 0, 4);
				// if ($strYear < $data_nghilc['report_year']) {
				// 	$class_lv1 = $value->level_next;
				// 	$class_lv2 = $value->level_next_1;
				// 	$class_lv3 = $value->level_next_2;
				// }

				// if ($strYear == $data_nghilc['report_year']) {
				// 	$class_lv1 = 0;
				// 	$class_lv2 = $value->level_next;
				// 	$class_lv3 = $value->level_next_1;
				// }

				// if ($strYear > $data_nghilc['report_year']) {
				// 	$class_lv1 = 0;
				// 	$class_lv2 = 0;
				// 	$class_lv3 = $value->level_next_1;
				// }

				$decided_date = "";
				if (!is_null($value->decided_confirmdate) && !empty($value->decided_confirmdate)) {					
					$decided_date = Carbon::parse($value->decided_confirmdate)->format('d-m-Y');
				}

			    $reader->getActiveSheet()->setCellValueByColumnAndRow($col, $row,++$indexc)->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($style);

			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_name)->getStyle('B'.$row)->applyFromArray($borderArray);
			        	
				$reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,'')->getStyle('C'.$row)->applyFromArray($borderArray);
			        	
				$reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,'')->getStyle('D'.$row)->applyFromArray($borderArray);
			        	
				$reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->level_cur)->getStyle('E'.$row)->applyFromArray($borderArray);
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,Carbon::parse($value->profile_birthday)->format('d-m-Y'))->getStyle('F'.$row)->applyFromArray($borderArray); 
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->nationals_name)->getStyle('G'.$row)->applyFromArray($borderArray);  
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_household)->getStyle('H'.$row)->applyFromArray($borderArray); 
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->tenxa)->getStyle('I'.$row)->applyFromArray($borderArray); 
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->tenhuyen)->getStyle('J'.$row)->applyFromArray($borderArray); 

			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_parentname)->getStyle('K'.$row)->applyFromArray($borderArray);     	
			    
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->decided_confirmation)->getStyle('L'.$row)->applyFromArray($borderArray);   	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->decided_number)->getStyle('M'.$row)->applyFromArray($borderArray);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$decided_date)->getStyle('N'.$row)->applyFromArray($borderArray);

			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->mienphi_1)->getStyle('O'.$row)->applyFromArray($borderArray)->applyFromArray($style);
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->mienphi_2)->getStyle('P'.$row)->applyFromArray($borderArray)->applyFromArray($style);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->mienphi_3)->getStyle('Q'.$row)->applyFromArray($borderArray)->applyFromArray($style);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->mienphi_4)->getStyle('R'.$row)->applyFromArray($borderArray)->applyFromArray($style);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->mienphi_5)->getStyle('S'.$row)->applyFromArray($borderArray)->applyFromArray($style);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->giam_70)->getStyle('T'.$row)->applyFromArray($borderArray)->applyFromArray($style);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->giam_50_1)->getStyle('U'.$row)->applyFromArray($borderArray)->applyFromArray($style);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->giam_50_2)->getStyle('V'.$row)->applyFromArray($borderArray)->applyFromArray($style);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_old)->getStyle('W'.$row)->applyFromArray($borderArray)->applyFromArray($style);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_cur)->getStyle('X'.$row)->applyFromArray($borderArray)->applyFromArray($style);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_cur)->getStyle('Y'.$row)->applyFromArray($borderArray)->applyFromArray($style);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_new)->getStyle('Z'.$row)->applyFromArray($borderArray)->applyFromArray($style);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocphi_old)->getStyle('AA'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocphi_new)->getStyle('AB'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->nhu_cau)->getStyle('AC'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
			    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->du_toan)->getStyle('AD'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				}
			}
	    });
		if($type){
    		return $excel->setFilename($filename)->store('xlsx', storage_path().'/exceldownload/MGHP');
    	}else{
    		return $excel->setFilename($filename)->download('xlsx');
    	}
	}
    
    public function countValue($type = null,$code){

    	if($type!=null){
    		$count = DB::table('qlhs_miengiamhocphi')->where('type_code','=',$code)->where('type','=',$type)->select(DB::raw('sum(mienphi_1) as tongmien1'),DB::raw('sum(mienphi_2) as tongmien2'),DB::raw('sum(mienphi_3) as tongmien3'),DB::raw('sum(mienphi_4) as tongmien4'),DB::raw('sum(mienphi_5) as tongmien5'),DB::raw('sum(giam_70) as tonggiam70'),DB::raw('sum(giam_50_1) as tonggiam501'),DB::raw('sum(giam_50_2) as tonggiam502'),DB::raw('sum(hocky2_old) as tonghk12'),DB::raw('sum(hocky1_cur) as tonghk21'),DB::raw('sum(hocky2_cur) as tonghk22'),DB::raw('sum(hocky1_new) as tonghk31'),DB::raw('sum(nhu_cau) as tongnc'),DB::raw('sum(du_toan) as tongdt'))->first();
    		return $count;
    	}else{
    		$count = DB::table('qlhs_miengiamhocphi')->where('type_code','=',$code)->select(DB::raw('sum(mienphi_1) as tongmien1'),DB::raw('sum(mienphi_2) as tongmien2'),DB::raw('sum(mienphi_3) as tongmien3'),DB::raw('sum(mienphi_4) as tongmien4'),DB::raw('sum(mienphi_5) as tongmien5'),DB::raw('sum(giam_70) as tonggiam70'),DB::raw('sum(giam_50_1) as tonggiam501'),DB::raw('sum(giam_50_2) as tonggiam502'),DB::raw('sum(hocky2_old) as tonghk12'),DB::raw('sum(hocky1_cur) as tonghk21'),DB::raw('sum(hocky2_cur) as tonghk22'),DB::raw('sum(hocky1_new) as tonghk31'),DB::raw('sum(nhu_cau) as tongnc'),DB::raw('sum(du_toan) as tongdt'))->first();
    		return $count;
    	}
    }
    
}
