<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Exception;
use Excel;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class LapHoSo4Controller extends Controller
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

    public function loadDataBanTru(Request $request)
    {
    	$json = [];
    	$start = $request->input('start');
    	$limit = $request->input('limit');
    	$user = Auth::user()->id;

    	$getIdTruong = DB::table('users')->select('truong_id')->where('id', '=', $user)->first();
    	$datas = DB::table('qlhs_hosobaocao')->where('report','=','HTBT');

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

    public function getData(Request $request){
    	$result = [];
    	try {
    		$files =  $request->file('FILE');
			$school_id = $request->input('SCHOOLID');
			$year = $request->input('YEAR');
			$current_user_id = Auth::user()->id;
			$current_date = Carbon::now('Asia/Ho_Chi_Minh');
			$report_name = $request->input('REPORTNAME');
			$user_sign = $request->input('CREATESIGN');
			$user_create = $request->input('CREATENAME');
			$note = $request->input('NOTE');

			$status = $request->input('STATUS');
			$filename_attach = "";
			if(trim($files) != ""){
				$filenames = 'File-'.$current_user_id.'-'.$files->getClientOriginalName();
				$filename_attach = $filenames;
			}

			$check = TRUE;

            $checkReportName = DB::table('qlhs_hosobaocao')->where('report_name', 'LIKE', '%'.$report_name.'%')->where('report', 'LIKE', '%HTBT%')->get();

            if (!is_null($checkReportName) && !empty($checkReportName) && count($checkReportName) > 0) {
	            $result['error'] = "Tên báo cáo đã tồn tại, xin mời nhập tên khác!";
	            return $result;
	        }
			// and qlhs_kinhphidoituong.start_date < '.$year.'-01-10 < qlhs_kinhphidoituong.end_date
			//and qlhs_profile.profile_year < '.$year.'-09-01
			$getDataType1 = DB::table('qlhs_profile')
			->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id in (46, 48, 66)'))
			->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))
			->join('qlhs_kinhphidoituong as tienan', DB::raw('tienan.doituong_id = 94 and tienan.idTruong'), '=', 'qlhs_profile.profile_school_id')
			->join('qlhs_kinhphidoituong as tieno', DB::raw('tieno.doituong_id = 98 and tieno.idTruong'), '=', 'qlhs_profile.profile_school_id')
			->join('qlhs_kinhphidoituong as tuthuoc', DB::raw('tuthuoc.doituong_id = 115 and tuthuoc.idTruong'), '=', 'qlhs_profile.profile_school_id')
			->join('qlhs_kinhphidoituong as tienan_new', DB::raw('tienan_new.doituong_id = 94 and tienan_new.idTruong'), '=', 'qlhs_profile.profile_school_id')
			->join('qlhs_kinhphidoituong as tieno_new', DB::raw('tieno_new.doituong_id = 98 and tieno_new.idTruong'), '=', 'qlhs_profile.profile_school_id')
			->join('qlhs_kinhphidoituong as tuthuoc_new', DB::raw('tuthuoc_new.doituong_id = 115 and tuthuoc_new.idTruong'), '=', 'qlhs_profile.profile_school_id')
			->where('qlhs_profile.profile_school_id', '=', $school_id)
			->where('qlhs_profile.profile_bantru', '=', 1)
			->where('tienan.start_date', '<', $year.'-09-10')
			->where('tienan.end_date', '>', $year.'-09-10')
			->where('tieno.start_date', '<', $year.'-09-10')
			->where('tieno.end_date', '>', $year.'-09-10')
			->where('tuthuoc.start_date', '<', $year.'-09-10')
			->where('tuthuoc.end_date', '>', $year.'-09-10')
			->where('tienan_new.start_date', '<', ($year + 1).'-09-10')
			->where('tienan_new.end_date', '>', ($year + 1).'-09-10')
			->where('tieno_new.start_date', '<', ($year + 1).'-09-10')
			->where('tieno_new.end_date', '>', ($year + 1).'-09-10')
			->where('tuthuoc_new.start_date', '<', ($year + 1).'-09-10')
			->where('tuthuoc_new.end_date', '>', ($year + 1).'-09-10')
			->where('qlhs_profile.profile_year', '<', $year.'-06-01')
			->select('qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'tienan.money as tienanhotro', 'tieno.money as tienohotro', 'tuthuoc.money as tuthuochotro', 'tienan_new.money as tienanhotro_new', 'tieno_new.money as tienohotro_new', 'tuthuoc_new.money as tuthuochotro_new', 
				DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 46 then 1 else 0 END) as hotrotienan'), 
				DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 48 then 1 else 0 END) as hotrotieno'), 
				DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 66 then 1 else 0 END) as hotroVHTT'), 

				DB::raw('MAX(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile_subject.profile_subject_subject_id = 46 and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hotrotienan_hocky2_old'), 
				DB::raw('MAX(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile_subject.profile_subject_subject_id = 48 and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hotrotieno_hocky2_old'), 
				DB::raw('MAX(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hotroVHTT_hocky2_old'), 

				DB::raw('MAX(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile_subject.profile_subject_subject_id = 46 and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hotrotienan_hocky1_cur'), 
				DB::raw('MAX(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile_subject.profile_subject_subject_id = 48 and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hotrotieno_hocky1_cur'), 
				DB::raw('MAX(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hotroVHTT_hocky1_cur'), 

				DB::raw('MAX(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile_subject.profile_subject_subject_id = 46 and qlhs_profile.profile_year < "'.($year +1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hotrotienan_hocky2_cur'), 
				DB::raw('MAX(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile_subject.profile_subject_subject_id = 48 and qlhs_profile.profile_year < "'.($year +1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hotrotieno_hocky2_cur'), 
				DB::raw('MAX(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.($year +1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hotroVHTT_hocky2_cur'), 

				DB::raw('MAX(CASE when qlhs_profile_history.level_new <> "" and qlhs_profile_subject.profile_subject_subject_id = 46 and qlhs_profile.profile_year < "'.($year +1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hotrotienan_hocky1_new'), 
				DB::raw('MAX(CASE when qlhs_profile_history.level_new <> "" and qlhs_profile_subject.profile_subject_subject_id = 48 and qlhs_profile.profile_year < "'.($year +1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hotrotieno_hocky1_new'), 
				DB::raw('MAX(CASE when qlhs_profile_history.level_new <> "" and qlhs_profile.profile_year < "'.($year +1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hotroVHTT_hocky1_new')
				)
			->groupBy('qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'tienan.money', 'tieno.money', 'tuthuoc.money', 'tienan_new.money', 'tieno_new.money', 'tuthuoc_new.money')
			->get();

			$getDataType2 = DB::table('qlhs_profile')
			->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id in (46, 48, 66)'))
			->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))
			->join('qlhs_kinhphidoituong as tienan', DB::raw('tienan.doituong_id = 94 and tienan.idTruong'), '=', 'qlhs_profile.profile_school_id')
			->join('qlhs_kinhphidoituong as tieno', DB::raw('tieno.doituong_id = 98 and tieno.idTruong'), '=', 'qlhs_profile.profile_school_id')
			->join('qlhs_kinhphidoituong as tuthuoc', DB::raw('tuthuoc.doituong_id = 115 and tuthuoc.idTruong'), '=', 'qlhs_profile.profile_school_id')
			->join('qlhs_kinhphidoituong as tienan_new', DB::raw('tienan_new.doituong_id = 94 and tienan_new.idTruong'), '=', 'qlhs_profile.profile_school_id')
			->join('qlhs_kinhphidoituong as tieno_new', DB::raw('tieno_new.doituong_id = 98 and tieno_new.idTruong'), '=', 'qlhs_profile.profile_school_id')
			->join('qlhs_kinhphidoituong as tuthuoc_new', DB::raw('tuthuoc_new.doituong_id = 115 and tuthuoc_new.idTruong'), '=', 'qlhs_profile.profile_school_id')
			->where('qlhs_profile.profile_school_id', '=', $school_id)
			->where('qlhs_profile.profile_bantru', '=', 1)
			->where('tienan.start_date', '<', $year.'-09-10')
			->where('tienan.end_date', '>', $year.'-09-10')
			->where('tieno.start_date', '<', $year.'-09-10')
			->where('tieno.end_date', '>', $year.'-09-10')
			->where('tuthuoc.start_date', '<', $year.'-09-10')
			->where('tuthuoc.end_date', '>', $year.'-09-10')
			->where('tienan_new.start_date', '<', ($year + 1).'-09-10')
			->where('tienan_new.end_date', '>', ($year + 1).'-09-10')
			->where('tieno_new.start_date', '<', ($year + 1).'-09-10')
			->where('tieno_new.end_date', '>', ($year + 1).'-09-10')
			->where('tuthuoc_new.start_date', '<', ($year + 1).'-09-10')
			->where('tuthuoc_new.end_date', '>', ($year + 1).'-09-10')
			->where('qlhs_profile.profile_year', '>', $year.'-05-31')
			->where('qlhs_profile.profile_year', '<', ($year + 1).'-06-01')
			->select('qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'tienan.money as tienanhotro', 'tieno.money as tienohotro', 'tuthuoc.money as tuthuochotro', 'tienan_new.money as tienanhotro_new', 'tieno_new.money as tienohotro_new', 'tuthuoc_new.money as tuthuochotro_new', 
				DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 46 then 1 else 0 END) as hotrotienan'), 
				DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 48 then 1 else 0 END) as hotrotieno'), 
				DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 66 then 1 else 0 END) as hotroVHTT'), 

				DB::raw('MAX(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile_subject.profile_subject_subject_id = 46 and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hotrotienan_hocky2_old'), 
				DB::raw('MAX(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile_subject.profile_subject_subject_id = 48 and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hotrotieno_hocky2_old'), 
				DB::raw('MAX(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hotroVHTT_hocky2_old'), 

				DB::raw('MAX(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile_subject.profile_subject_subject_id = 46 and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hotrotienan_hocky1_cur'), 
				DB::raw('MAX(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile_subject.profile_subject_subject_id = 48 and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hotrotieno_hocky1_cur'), 
				DB::raw('MAX(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hotroVHTT_hocky1_cur'), 

				DB::raw('MAX(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile_subject.profile_subject_subject_id = 46 and qlhs_profile.profile_year < "'.($year +1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hotrotienan_hocky2_cur'), 
				DB::raw('MAX(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile_subject.profile_subject_subject_id = 48 and qlhs_profile.profile_year < "'.($year +1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hotrotieno_hocky2_cur'), 
				DB::raw('MAX(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.($year +1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hotroVHTT_hocky2_cur'), 

				DB::raw('MAX(CASE when qlhs_profile_history.level_new <> "" and qlhs_profile_subject.profile_subject_subject_id = 46 and qlhs_profile.profile_year < "'.($year +1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hotrotienan_hocky1_new'), 
				DB::raw('MAX(CASE when qlhs_profile_history.level_new <> "" and qlhs_profile_subject.profile_subject_subject_id = 48 and qlhs_profile.profile_year < "'.($year +1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hotrotieno_hocky1_new'), 
				DB::raw('MAX(CASE when qlhs_profile_history.level_new <> "" and qlhs_profile.profile_year < "'.($year +1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hotroVHTT_hocky1_new')
				)
			->groupBy('qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'tienan.money', 'tieno.money', 'tuthuoc.money', 'tienan_new.money', 'tieno_new.money', 'tuthuoc_new.money')
			->get();

			$getDataType3 = DB::table('qlhs_profile')
			->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id in (46, 48, 66)'))
			->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.($year + 1).'-'.($year + 2).'"'))
			->join('qlhs_kinhphidoituong as tienan', DB::raw('tienan.doituong_id = 94 and tienan.idTruong'), '=', 'qlhs_profile.profile_school_id')
			->join('qlhs_kinhphidoituong as tieno', DB::raw('tieno.doituong_id = 98 and tieno.idTruong'), '=', 'qlhs_profile.profile_school_id')
			->join('qlhs_kinhphidoituong as tuthuoc', DB::raw('tuthuoc.doituong_id = 115 and tuthuoc.idTruong'), '=', 'qlhs_profile.profile_school_id')
			->join('qlhs_kinhphidoituong as tienan_new', DB::raw('tienan_new.doituong_id = 94 and tienan_new.idTruong'), '=', 'qlhs_profile.profile_school_id')
			->join('qlhs_kinhphidoituong as tieno_new', DB::raw('tieno_new.doituong_id = 98 and tieno_new.idTruong'), '=', 'qlhs_profile.profile_school_id')
			->join('qlhs_kinhphidoituong as tuthuoc_new', DB::raw('tuthuoc_new.doituong_id = 115 and tuthuoc_new.idTruong'), '=', 'qlhs_profile.profile_school_id')
			->where('qlhs_profile.profile_school_id', '=', $school_id)
			->where('qlhs_profile.profile_bantru', '=', 1)
			->where('tienan.start_date', '<', $year.'-09-10')
			->where('tienan.end_date', '>', $year.'-09-10')
			->where('tieno.start_date', '<', $year.'-09-10')
			->where('tieno.end_date', '>', $year.'-09-10')
			->where('tuthuoc.start_date', '<', $year.'-09-10')
			->where('tuthuoc.end_date', '>', $year.'-09-10')
			->where('tienan_new.start_date', '<', ($year + 1).'-09-10')
			->where('tienan_new.end_date', '>', ($year + 1).'-09-10')
			->where('tieno_new.start_date', '<', ($year + 1).'-09-10')
			->where('tieno_new.end_date', '>', ($year + 1).'-09-10')
			->where('tuthuoc_new.start_date', '<', ($year + 1).'-09-10')
			->where('tuthuoc_new.end_date', '>', ($year + 1).'-09-10')
			->where('qlhs_profile.profile_year', '>', ($year + 1).'-05-31')
			->where('qlhs_profile.profile_year', '<', ($year + 2).'-01-01')
			->select('qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'tienan.money as tienanhotro', 'tieno.money as tienohotro', 'tuthuoc.money as tuthuochotro', 'tienan_new.money as tienanhotro_new', 'tieno_new.money as tienohotro_new', 'tuthuoc_new.money as tuthuochotro_new', 
				DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 46 then 1 else 0 END) as hotrotienan'), 
				DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 48 then 1 else 0 END) as hotrotieno'), 
				DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 66 then 1 else 0 END) as hotroVHTT'), 

				DB::raw('MAX(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile_subject.profile_subject_subject_id = 46 and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hotrotienan_hocky2_old'), 
				DB::raw('MAX(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile_subject.profile_subject_subject_id = 48 and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hotrotieno_hocky2_old'), 
				DB::raw('MAX(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hotroVHTT_hocky2_old'),

				DB::raw('MAX(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile_subject.profile_subject_subject_id = 46 and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hotrotienan_hocky1_cur'), 
				DB::raw('MAX(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile_subject.profile_subject_subject_id = 48 and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hotrotieno_hocky1_cur'), 
				DB::raw('MAX(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hotroVHTT_hocky1_cur'), 

				DB::raw('MAX(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile_subject.profile_subject_subject_id = 46 and qlhs_profile.profile_year < "'.($year +1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hotrotienan_hocky2_cur'), 
				DB::raw('MAX(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile_subject.profile_subject_subject_id = 48 and qlhs_profile.profile_year < "'.($year +1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hotrotieno_hocky2_cur'), 
				DB::raw('MAX(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile.profile_year < "'.($year +1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hotroVHTT_hocky2_cur'), 

				DB::raw('MAX(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile_subject.profile_subject_subject_id = 46 and qlhs_profile.profile_year < "'.($year +1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hotrotienan_hocky1_new'), 
				DB::raw('MAX(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile_subject.profile_subject_subject_id = 48 and qlhs_profile.profile_year < "'.($year +1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hotrotieno_hocky1_new'), 
				DB::raw('MAX(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.($year +1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hotroVHTT_hocky1_new')
				)
			->groupBy('qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'tienan.money', 'tieno.money', 'tuthuoc.money', 'tienan_new.money', 'tieno_new.money', 'tuthuoc_new.money')
			->get();
//return $getDataType3;
    		$time = time();

			if ((is_null($getDataType1) || empty($getDataType1) || count($getDataType1) == 0) && (is_null($getDataType2) || empty($getDataType2) || count($getDataType2) == 0) && (is_null($getDataType3) || empty($getDataType3) || count($getDataType3) == 0)) {
				$result['success'] = "Danh sách trống!";
				return $result;
			}

			if (!is_null($getDataType1) && !empty($getDataType1) && count($getDataType1) > 0) {
				$check = $this->insertHTBT($getDataType1, 1, $current_user_id, $school_id, $year, $time);
			}

			if (!is_null($getDataType2) && !empty($getDataType2) && count($getDataType2) > 0 && $check) {
				$check = $this->insertHTBT($getDataType2, 2, $current_user_id, $school_id, $year, $time);
			}

			if (!is_null($getDataType3) && !empty($getDataType3) && count($getDataType3) > 0 && $check) {
				$check = $this->insertHTBT($getDataType3, 3, $current_user_id, $school_id, $year, $time);
			}

			if ($check) {
				$type_code = 'HTBT-' . $current_user_id . '-' . $school_id . '-' . $year . '' . ($year + 1) . '-' . $time;

				$dir = storage_path().'/files/HTBT';
				if(trim($files) != ""){
					if(file_exists($dir.'/'. $filename_attach)){
						$files->move($dir, $filename_attach.'-'.$time);	
					 	//File::delete($dir.'/'. $filename_attach);	
					}else{
					 	$files->move($dir, $filename_attach);	
					}
				}

				$insert_hosobaocao_id = DB::table('qlhs_hosobaocao')->insertGetId(['report_name' => $report_name, 'report_type' => $type_code, 'report_date' => $current_date, 'created_at' => $current_date, 'updated_at' => $current_date, 'create_userid' => $current_user_id, 'update_userid' => $current_user_id, 'report_user' => $user_create, 'report_user_sign' => $user_sign, 'report_attach_name' => $filename_attach, 'report_nature' => $status, 'report_year' => $year, 'report_id_truong' => $school_id, 'report_note' => $note, 'report' => 'HTBT']);


				if (!is_null($insert_hosobaocao_id) && $insert_hosobaocao_id > 0) {
					$this->exportforSchools($insert_hosobaocao_id);

					if (file_exists(storage_path().'/exceldownload/HTBT/'.$type_code.'.xlsx')) {
						$result['success'] = "Thêm mới thành công!";
					}
					else {
						$deleteHoso = DB::table('qlhs_hosobaocao')->where('report_type', 'LIKE', '%'.$type_code.'%')->delete();
						$deleteHTBT = DB::table('qlhs_hotrohocsinhbantru')->where('type_code', 'LIKE', '%'.$type_code.'%')->delete();
						$result['error'] = "Thêm mới thất bại!";
					}
				}
				else {$result['error'] = "Thêm mới thất bại!";}
			}
			else {$result['error'] = "Thêm mới thất bại!";}

			return $result;
    	} catch (Exception $e) {
    		return $e;
    	}		
    }

    public function insertHTBT($getDataType, $type, $current_user_id, $school_id, $year, $time){
    	try {
    		$bool = TRUE;
    		
			$type_code = 'HTBT-' . $current_user_id . '-' . $school_id . '-' . $year . '' . ($year + 1) . '-' . $time;

			foreach ($getDataType as $value) {

				$nhucautienan = 0;
				$nhucautieno = 0;
				$nhucauVHTT = 0;
				$dutoantienan = 0;
				$dutoantieno = 0;
				$dutoanVHTT = 0;

				if ($value->{'hotroVHTT'} == 1) {
					$nhucauVHTT = ($value->{'hotroVHTT_hocky2_old'} * 5 * $value->{'tuthuochotro'}) + ($value->{'hotroVHTT_hocky1_cur'} * 4 * $value->{'tuthuochotro'});
					$dutoanVHTT = ($value->{'hotroVHTT_hocky2_cur'} * 5 * $value->{'tuthuochotro_new'}) + ($value->{'hotroVHTT_hocky1_new'} * 4 * $value->{'tuthuochotro_new'});
				}

				if ($value->{'hotrotienan'} == 1) {
					$nhucautienan = ($value->{'hotrotienan_hocky2_old'} * 5 * $value->{'tienanhotro'}) + ($value->{'hotrotienan_hocky1_cur'} * 4 * $value->{'tienanhotro'});
					$dutoantienan = ($value->{'hotrotienan_hocky2_cur'} * 5 * $value->{'tienanhotro_new'}) + ($value->{'hotrotienan_hocky1_new'} * 4 * $value->{'tienanhotro_new'});
				}
				if ($value->{'hotrotieno'} == 1) {
					$nhucautieno = ($value->{'hotrotieno_hocky2_old'} * 5 * $value->{'tienohotro'}) + ($value->{'hotrotieno_hocky1_cur'} * 4 * $value->{'tienohotro'});
					$dutoantieno = ($value->{'hotrotieno_hocky2_cur'} * 5 * $value->{'tienohotro_new'}) + ($value->{'hotrotieno_hocky1_new'} * 4 * $value->{'tienohotro_new'});
				}				
				
				$tongnhucau = ($nhucautienan + $nhucautieno + $nhucauVHTT);
				
				$tongdutoan = ($dutoantienan + $dutoantieno + $dutoanVHTT);

				$insert_type = DB::table('qlhs_hotrohocsinhbantru')
				->insert([
					'profile_id' => $value->{'profile_id'}, 
					'hotrotienan' => $value->{'hotrotienan'}, 
					'hotrotieno' => $value->{'hotrotieno'}, 
					'hotrotienan_hocky2_old' => $value->{'hotrotienan_hocky2_old'}, 
					'hotrotieno_hocky2_old' => $value->{'hotrotieno_hocky2_old'}, 
					'hotrotienan_hocky1_cur' => $value->{'hotrotienan_hocky1_cur'}, 
					'hotrotieno_hocky1_cur' => $value->{'hotrotieno_hocky1_cur'}, 
					'hotrotienan_hocky2_cur' => $value->{'hotrotienan_hocky2_cur'}, 
					'hotrotieno_hocky2_cur' => $value->{'hotrotieno_hocky2_cur'}, 
					'hotrotienan_hocky1_new' => $value->{'hotrotienan_hocky1_new'}, 
					'hotrotieno_hocky1_new' => $value->{'hotrotieno_hocky1_new'}, 
					'nhucau_hotrotienan' => $nhucautienan, 
					'nhucau_hotrotieno' => $nhucautieno, 
					'nhucau_VHTT' => $nhucauVHTT, 
					'tong_nhucau' => $tongnhucau, 
					'dutoan_hotrotienan' => $dutoantienan, 
					'dutoan_hotrotieno' => $dutoantieno, 
					'dutoan_VHTT' => $dutoanVHTT, 
					'tong_dutoan' => $tongdutoan, 
					'type_code' => $type_code, 
					'type' => $type
					]);

				if ($insert_type == 0) {
					$bool = FALSE;
					$deleteHTBT = DB::table('qlhs_hotrohocsinhbantru')->where('type_code', 'LIKE', '%'.$type_code.'%')->where('type', '=', $type)->delete();
					break;
				}
			}

			return $bool;
    	} catch (Exception $e) {
    		return $e;
    	}
    }

    public function delete_report($id){
    	
    	$json = [];
    	$data = DB::table('qlhs_hosobaocao')->where('report_id', '=', $id)->select('report_type','report_attach_name','report')->first(); 
    	$del1 = DB::table('qlhs_hotrohocsinhbantru')->where('type_code', '=', $data->report_type)->delete();
    	$dir = storage_path().'/files/'.$data->report;
    	if($del1 > 0){
    		$del2 = DB::table('qlhs_hosobaocao')->where('report_id','=',$id)->delete();

    		if(file_exists($dir.'/'. $data->report_attach_name)){
				//$files->move($dir, $filename_attach.'-'.$time);	
			 	File::delete($dir.'/'. $data->report_attach_name);
			}

			if (file_exists(storage_path().'/exceldownload/HTBT/'.$data->report_type.'.xlsx')) {
				File::delete(storage_path().'/exceldownload/HTBT/'.$data->report_type.'.xlsx');
			}

    		if($del2 > 0){
    			$json['success'] = 'Xóa thành công';
    		}else{
    			$json['error'] = 'Xóa lỗi';
    		}
    	}
    	return $json;
    }

    public function download_attach($id){
    	$data = DB::table('qlhs_hosobaocao')->where('report','=','HTBT')->where('report_id','=',$id)->select('report_attach_name')->first(); 
    	$dir = storage_path().'/files/HTBT/'.$data->report_attach_name;
    	return response()->download($dir,$data->report_attach_name);
    }

    public function downloadfile_Export($id){
    	$data = DB::table('qlhs_hosobaocao')->where('report','=','HTBT')->where('report_id','=',$id)->select('report_type')->first(); 
    	$dir = storage_path().'/exceldownload/HTBT/'.$data->report_type.'.xlsx';
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
    		$data_results['a'] = DB::table('qlhs_hotrohocsinhbantru')
    		->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_hotrohocsinhbantru.profile_id')
    		->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
    		->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_hotrohocsinhbantru.profile_id and qlhs_profile_history.history_year = "'.$getSchoolName->report_year.'-'.($getSchoolName->report_year + 1).'"'))
    		->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
    		->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
    		->where('qlhs_hotrohocsinhbantru.type_code', '=', $getSchoolName->report_type)
    		->where('qlhs_hotrohocsinhbantru.type', '=', 1)
    		->select('qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile_history.history_year', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_km', 'qlhs_profile.profile_giaothong', 'qlhs_profile.profile_year', 'qlhs_hotrohocsinhbantru.*')->DISTINCT()->get();

    		$data_results['b'] = DB::table('qlhs_hotrohocsinhbantru')
    		->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_hotrohocsinhbantru.profile_id')
    		->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
    		->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_hotrohocsinhbantru.profile_id and qlhs_profile_history.history_year = "'.$getSchoolName->report_year.'-'.($getSchoolName->report_year + 1).'"'))
    		->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
    		->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
    		->where('qlhs_hotrohocsinhbantru.type_code', '=', $getSchoolName->report_type)
    		->where('qlhs_hotrohocsinhbantru.type', '=', 2)
    		->select('qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile_history.history_year', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_km', 'qlhs_profile.profile_giaothong', 'qlhs_profile.profile_year', 'qlhs_hotrohocsinhbantru.*')->DISTINCT()->get();

    		$data_results['c'] = DB::table('qlhs_hotrohocsinhbantru')
    		->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_hotrohocsinhbantru.profile_id')
    		->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
    		->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_hotrohocsinhbantru.profile_id and qlhs_profile_history.history_year = "'.($getSchoolName->report_year + 1).'-'.($getSchoolName->report_year + 2).'"'))
    		->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
    		->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
    		->where('qlhs_hotrohocsinhbantru.type_code', '=', $getSchoolName->report_type)
    		->where('qlhs_hotrohocsinhbantru.type', '=', 3)
    		->select('qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile_history.history_year', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_km', 'qlhs_profile.profile_giaothong', 'qlhs_profile.profile_year', 'qlhs_hotrohocsinhbantru.*')->DISTINCT()->get();
    	}
        $data_results['schools_name'] = $getSchoolName->schools_name;
        $data_results['report_year'] = $getSchoolName->report_year;
        $this->addCellExcel($data_results, $getSchoolName->report_type, TRUE);
	}

	private function addCellExcel($data_results, $filename, $type = true){
		$excel = 	Excel::load(storage_path().'/exceltemplate/laphosoHTBT.xlsx', function($reader) use($data_results){
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
			$reader->getActiveSheet()->setCellValueByColumnAndRow(0, 2, 'NHU CẦU KINH PHÍ NĂM '.$data_results['report_year'].', DỰ TOÁN NĂM '.($data_results['report_year'] + 1).' ĐỐI VỚI CHÍNH SÁCH HỖ TRỢ ĂN TRƯA CHO TRẺ EM MẪU GIÁO THEO QUYẾT ĐỊNH SỐ 60/QĐ-TTG CỦA THỦ TƯỚNG CHÍNH PHỦ')->getStyle('A2')->applyFromArray($FontArray)->applyFromArray($style);
			$reader->getActiveSheet()->setCellValueByColumnAndRow(2, 4, 'Thông tin cơ bản (tính tại thời điểm tháng 5/'.$data_results['report_year'].')')->getStyle('C4')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
			$reader->getActiveSheet()->setCellValueByColumnAndRow(23, 4, 'Nhu cầu kinh phí năm '.$data_results['report_year'])->getStyle('X4')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
			$reader->getActiveSheet()->setCellValueByColumnAndRow(27, 4, 'Dự toán kinh phí năm '.($data_results['report_year'] + 1))->getStyle('AB4')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

			$reader->getActiveSheet()->setCellValueByColumnAndRow(2, 7, 'Học kì II năm học '.($data_results['report_year'] - 1).'-'.$data_results['report_year'])->getStyle('C7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
			$reader->getActiveSheet()->setCellValueByColumnAndRow(3, 7, 'Năm học '.$data_results['report_year'].'-'.($data_results['report_year'] + 1))->getStyle('D7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
			$reader->getActiveSheet()->setCellValueByColumnAndRow(4, 7, 'Năm học '.($data_results['report_year'] + 1).'-'.($data_results['report_year'] + 2))->getStyle('E7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

			$reader->getActiveSheet()->setCellValueByColumnAndRow(15, 6, 'Học kỳ II năm học '.($data_results['report_year'] - 1).'-'.$data_results['report_year'])->getStyle('P6')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
			$reader->getActiveSheet()->setCellValueByColumnAndRow(17, 6, 'Học kỳ I năm học '.$data_results['report_year'].'-'.($data_results['report_year'] + 1))->getStyle('R6')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
			$reader->getActiveSheet()->setCellValueByColumnAndRow(19, 6, 'Học kỳ II năm học '.$data_results['report_year'].'-'.($data_results['report_year'] + 1))->getStyle('T6')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
			$reader->getActiveSheet()->setCellValueByColumnAndRow(21, 6, 'Học kỳ I năm học '.($data_results['report_year'] + 1).'-'.($data_results['report_year'] + 2))->getStyle('V6')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
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
            $reader->getActiveSheet()->setCellValueByColumnAndRow(12, $row, '')->getStyle('M'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(13, $row, '')->getStyle('N'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row, '')->getStyle('O'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		  	$reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row,$data_results['TotalCount']->tongtienan_hocky2_old)->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
		  	$reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row,$data_results['TotalCount']->tongtieno_hocky2_old)->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row,$data_results['TotalCount']->tongtienan_hocky1_cur)->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(18, $row,$data_results['TotalCount']->tongtieno_hocky1_cur)->getStyle('S'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row,$data_results['TotalCount']->tongtienan_hocky2_cur)->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row,$data_results['TotalCount']->tongtieno_hocky2_cur)->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row,$data_results['TotalCount']->tongtienan_hocky1_new)->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row,$data_results['TotalCount']->tongtieno_hocky1_new)->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row,$data_results['TotalCount']->tong_tongnhucau)->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(24, $row,$data_results['TotalCount']->tongnhucau_hotrotienan)->getStyle('Y'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(25, $row,$data_results['TotalCount']->tongnhucau_hotrotieno)->getStyle('Z'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(26, $row,$data_results['TotalCount']->tongnhucau_hotroVHTT)->getStyle('AA'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(27, $row,$data_results['TotalCount']->tong_tongdutoan)->getStyle('AB'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(28, $row,$data_results['TotalCount']->tongdutoan_hotrotienan)->getStyle('AC'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(29, $row,$data_results['TotalCount']->tongdutoan_hotrotieno)->getStyle('AD'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(30, $row,$data_results['TotalCount']->tongdutoan_hotroVHTT)->getStyle('AE'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);

			$row++;
			$reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row,'a')->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

			//____________________________________________________________________________________________________________________________________________________
		 	$reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row,$data_results['aT'])->getStyle('B'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($styleLeft);
		 	
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
            $reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row, '')->getStyle('O'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		  	$reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row,$data_results['aCount']->tongtienan_hocky2_old)->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		  	$reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row,$data_results['aCount']->tongtieno_hocky2_old)->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row,$data_results['aCount']->tongtienan_hocky1_cur)->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(18, $row,$data_results['aCount']->tongtieno_hocky1_cur)->getStyle('S'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row,$data_results['aCount']->tongtienan_hocky2_cur)->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row,$data_results['aCount']->tongtieno_hocky2_cur)->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row,$data_results['aCount']->tongtienan_hocky1_new)->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row,$data_results['aCount']->tongtieno_hocky1_new)->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row,$data_results['aCount']->tong_tongnhucau)->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(24, $row,$data_results['aCount']->tongnhucau_hotrotienan)->getStyle('Y'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(25, $row,$data_results['aCount']->tongnhucau_hotrotieno)->getStyle('Z'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(26, $row,$data_results['aCount']->tongnhucau_hotroVHTT)->getStyle('AA'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(27, $row,$data_results['aCount']->tong_tongdutoan)->getStyle('AB'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(28, $row,$data_results['aCount']->tongdutoan_hotrotienan)->getStyle('AC'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(29, $row,$data_results['aCount']->tongdutoan_hotrotieno)->getStyle('AD'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(30, $row,$data_results['aCount']->tongdutoan_hotroVHTT)->getStyle('AE'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);


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
					// 	$class_lv2 = $value->level_next_1;
					// 	$class_lv3 = $value->level_next_2;
					// }

				    $reader->getActiveSheet()->setCellValueByColumnAndRow($col, $row,++$indexa)->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($style);

				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_name)->getStyle('B'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
				        	
					$reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->level_old)->getStyle('C'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
				        	
					$reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->level_cur)->getStyle('D'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
				        	
					$reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->level_new)->getStyle('E'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,Carbon::parse($value->profile_birthday)->format('d-m-Y'))->getStyle('F'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight); 
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->nationals_name)->getStyle('G'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);  
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_parentname)->getStyle('H'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_household)->getStyle('I'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->tenxa)->getStyle('J'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 

				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->tenhuyen)->getStyle('K'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_km)->getStyle('L'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_giaothong)->getStyle('M'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hotrotienan)->getStyle('N'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight); 

				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hotrotieno)->getStyle('O'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hotrotienan_hocky2_old)->getStyle('P'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hotrotieno_hocky2_old)->getStyle('Q'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hotrotienan_hocky1_cur)->getStyle('R'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hotrotieno_hocky1_cur)->getStyle('S'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hotrotienan_hocky2_cur)->getStyle('T'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hotrotieno_hocky2_cur)->getStyle('U'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hotrotienan_hocky1_new)->getStyle('V'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hotrotieno_hocky1_new)->getStyle('W'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->tong_nhucau)->getStyle('X'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->nhucau_hotrotienan)->getStyle('Y'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->nhucau_hotrotieno)->getStyle('Z'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->nhucau_VHTT)->getStyle('AA'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->tong_dutoan)->getStyle('AB'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->dutoan_hotrotienan)->getStyle('AC'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->dutoan_hotrotieno)->getStyle('AD'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);       	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->dutoan_VHTT)->getStyle('AE'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
					}
				}
			//b

			$row++;
			$reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row,'b')->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

		 	$reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row,$data_results['bT'])->getStyle('B'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray);
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
            $reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row, '')->getStyle('O'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		 	$reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row,$data_results['bCount']->tongtienan_hocky2_old)->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		  	$reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row,$data_results['bCount']->tongtieno_hocky2_old)->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row,$data_results['bCount']->tongtienan_hocky1_cur)->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(18, $row,$data_results['bCount']->tongtieno_hocky1_cur)->getStyle('S'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row,$data_results['bCount']->tongtienan_hocky2_cur)->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row,$data_results['bCount']->tongtieno_hocky2_cur)->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row,$data_results['bCount']->tongtienan_hocky1_new)->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row,$data_results['bCount']->tongtieno_hocky1_new)->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row,$data_results['bCount']->tong_tongnhucau)->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(24, $row,$data_results['bCount']->tongnhucau_hotrotienan)->getStyle('Y'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(25, $row,$data_results['bCount']->tongnhucau_hotrotieno)->getStyle('Z'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(26, $row,$data_results['bCount']->tongnhucau_hotroVHTT)->getStyle('AA'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(27, $row,$data_results['bCount']->tong_tongdutoan)->getStyle('AB'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(28, $row,$data_results['bCount']->tongdutoan_hotrotienan)->getStyle('AC'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(29, $row,$data_results['bCount']->tongdutoan_hotrotieno)->getStyle('AD'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(30, $row,$data_results['bCount']->tongdutoan_hotroVHTT)->getStyle('AE'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
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

				    $reader->getActiveSheet()->setCellValueByColumnAndRow($col, $row,++$indexa)->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($style);

				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_name)->getStyle('B'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
				        	
					$reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->level_old)->getStyle('C'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
				        	
					$reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->level_cur)->getStyle('D'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
				        	
					$reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->level_new)->getStyle('E'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,Carbon::parse($value->profile_birthday)->format('d-m-Y'))->getStyle('F'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight); 
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->nationals_name)->getStyle('G'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);  
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_parentname)->getStyle('H'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_household)->getStyle('I'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->tenxa)->getStyle('J'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 

				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->tenhuyen)->getStyle('K'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_km)->getStyle('L'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_giaothong)->getStyle('M'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hotrotienan)->getStyle('N'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight); 

				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hotrotieno)->getStyle('O'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hotrotienan_hocky2_old)->getStyle('P'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hotrotieno_hocky2_old)->getStyle('Q'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hotrotienan_hocky1_cur)->getStyle('R'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hotrotieno_hocky1_cur)->getStyle('S'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hotrotienan_hocky2_cur)->getStyle('T'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hotrotieno_hocky2_cur)->getStyle('U'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hotrotienan_hocky1_new)->getStyle('V'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hotrotieno_hocky1_new)->getStyle('W'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->tong_nhucau)->getStyle('X'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->nhucau_hotrotienan)->getStyle('Y'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->nhucau_hotrotieno)->getStyle('Z'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->nhucau_VHTT)->getStyle('AA'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->tong_dutoan)->getStyle('AB'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->dutoan_hotrotienan)->getStyle('AC'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->dutoan_hotrotieno)->getStyle('AD'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);       	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->dutoan_VHTT)->getStyle('AE'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
					}
				}

				//c

			$row++;
			$reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row,'c')->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

		 	$reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row,$data_results['cT'])->getStyle('B'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray);
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
            $reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row, '')->getStyle('O'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		 	$reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row,$data_results['cCount']->tongtienan_hocky2_old)->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		  	$reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row,$data_results['cCount']->tongtieno_hocky2_old)->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row,$data_results['cCount']->tongtienan_hocky1_cur)->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(18, $row,$data_results['cCount']->tongtieno_hocky1_cur)->getStyle('S'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row,$data_results['cCount']->tongtienan_hocky2_cur)->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row,$data_results['cCount']->tongtieno_hocky2_cur)->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row,$data_results['cCount']->tongtienan_hocky1_new)->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row,$data_results['cCount']->tongtieno_hocky1_new)->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row,$data_results['cCount']->tong_tongnhucau)->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(24, $row,$data_results['cCount']->tongnhucau_hotrotienan)->getStyle('Y'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(25, $row,$data_results['cCount']->tongnhucau_hotrotieno)->getStyle('Z'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(26, $row,$data_results['cCount']->tongnhucau_hotroVHTT)->getStyle('AA'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(27, $row,$data_results['cCount']->tong_tongdutoan)->getStyle('AB'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(28, $row,$data_results['cCount']->tongdutoan_hotrotienan)->getStyle('AC'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(29, $row,$data_results['cCount']->tongdutoan_hotrotieno)->getStyle('AD'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(30, $row,$data_results['cCount']->tongdutoan_hotroVHTT)->getStyle('AE'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
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

				    $reader->getActiveSheet()->setCellValueByColumnAndRow($col, $row,++$indexa)->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($style);

				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_name)->getStyle('B'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
				        	
					$reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,'')->getStyle('C'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
				        	
					$reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,'')->getStyle('D'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
				        	
					$reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->level_cur)->getStyle('E'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,Carbon::parse($value->profile_birthday)->format('d-m-Y'))->getStyle('F'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight); 
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->nationals_name)->getStyle('G'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);  
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_parentname)->getStyle('H'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_household)->getStyle('I'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->tenxa)->getStyle('J'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 

				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->tenhuyen)->getStyle('K'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_km)->getStyle('L'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_giaothong)->getStyle('M'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hotrotienan)->getStyle('N'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight); 

				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hotrotieno)->getStyle('O'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hotrotienan_hocky2_old)->getStyle('P'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hotrotieno_hocky2_old)->getStyle('Q'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hotrotienan_hocky1_cur)->getStyle('R'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hotrotieno_hocky1_cur)->getStyle('S'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hotrotienan_hocky2_cur)->getStyle('T'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hotrotieno_hocky2_cur)->getStyle('U'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hotrotienan_hocky1_new)->getStyle('V'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hotrotieno_hocky1_new)->getStyle('W'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->tong_nhucau)->getStyle('X'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->nhucau_hotrotienan)->getStyle('Y'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->nhucau_hotrotieno)->getStyle('Z'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->nhucau_VHTT)->getStyle('AA'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->tong_dutoan)->getStyle('AB'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->dutoan_hotrotienan)->getStyle('AC'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->dutoan_hotrotieno)->getStyle('AD'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);       	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->dutoan_VHTT)->getStyle('AE'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
				}
			}
		});
		if($type){
	    	return $excel->setFilename($filename)->store('xlsx', storage_path().'/exceldownload/HTBT');
	    }else{
	    	return $excel->setFilename($filename)->download('xlsx');
	    }
	}
    
    public function countValue($type = null,$code){

    	if($type!=null){
    		$count = DB::table('qlhs_hotrohocsinhbantru')->where('type_code','=',$code)->where('type','=',$type)
    		->select(
    			DB::raw('sum(hotrotienan) as tonghotrotienan'),
    			DB::raw('sum(hotrotieno) as tonghotrotieno'),
    			DB::raw('sum(hotrotienan_hocky2_old) as tongtienan_hocky2_old'),
    			DB::raw('sum(hotrotieno_hocky2_old) as tongtieno_hocky2_old'),
    			DB::raw('sum(hotrotienan_hocky1_cur) as tongtienan_hocky1_cur'),
    			DB::raw('sum(hotrotieno_hocky1_cur) as tongtieno_hocky1_cur'),
    			DB::raw('sum(hotrotienan_hocky2_cur) as tongtienan_hocky2_cur'),
    			DB::raw('sum(hotrotieno_hocky2_cur) as tongtieno_hocky2_cur'),
    			DB::raw('sum(hotrotienan_hocky1_new) as tongtienan_hocky1_new'),
    			DB::raw('sum(hotrotieno_hocky1_new) as tongtieno_hocky1_new'),
    			DB::raw('sum(nhucau_hotrotienan) as tongnhucau_hotrotienan'),
    			DB::raw('sum(nhucau_hotrotieno) as tongnhucau_hotrotieno'),
    			DB::raw('sum(nhucau_VHTT) as tongnhucau_hotroVHTT'),
    			DB::raw('sum(tong_nhucau) as tong_tongnhucau'),
    			DB::raw('sum(dutoan_hotrotienan) as tongdutoan_hotrotienan'),
    			DB::raw('sum(dutoan_hotrotieno) as tongdutoan_hotrotieno'),
    			DB::raw('sum(dutoan_VHTT) as tongdutoan_hotroVHTT'),
    			DB::raw('sum(tong_dutoan) as tong_tongdutoan'))->first();
    		return $count;
    	}else{
    		$count = DB::table('qlhs_hotrohocsinhbantru')->where('type_code','=',$code)
    		->select(
    			DB::raw('sum(hotrotienan) as tonghotrotienan'),
    			DB::raw('sum(hotrotieno) as tonghotrotieno'),
    			DB::raw('sum(hotrotienan_hocky2_old) as tongtienan_hocky2_old'),
    			DB::raw('sum(hotrotieno_hocky2_old) as tongtieno_hocky2_old'),
    			DB::raw('sum(hotrotienan_hocky1_cur) as tongtienan_hocky1_cur'),
    			DB::raw('sum(hotrotieno_hocky1_cur) as tongtieno_hocky1_cur'),
    			DB::raw('sum(hotrotienan_hocky2_cur) as tongtienan_hocky2_cur'),
    			DB::raw('sum(hotrotieno_hocky2_cur) as tongtieno_hocky2_cur'),
    			DB::raw('sum(hotrotienan_hocky1_new) as tongtienan_hocky1_new'),
    			DB::raw('sum(hotrotieno_hocky1_new) as tongtieno_hocky1_new'),
    			DB::raw('sum(nhucau_hotrotienan) as tongnhucau_hotrotienan'),
    			DB::raw('sum(nhucau_hotrotieno) as tongnhucau_hotrotieno'),
    			DB::raw('sum(nhucau_VHTT) as tongnhucau_hotroVHTT'),
    			DB::raw('sum(tong_nhucau) as tong_tongnhucau'),
    			DB::raw('sum(dutoan_hotrotienan) as tongdutoan_hotrotienan'),
    			DB::raw('sum(dutoan_hotrotieno) as tongdutoan_hotrotieno'),
    			DB::raw('sum(dutoan_VHTT) as tongdutoan_hotroVHTT'),
    			DB::raw('sum(tong_dutoan) as tong_tongdutoan'))->first();
    		return $count;
    	}
    }
}

?>