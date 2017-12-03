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

class LapHoSo6Controller extends Controller
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

    public function loadDataHSKT(Request $request)
    {
    	$json = [];
    	$start = $request->input('start');
    	$limit = $request->input('limit');
    	$user = Auth::user()->id;

    	$getIdTruong = DB::table('users')->select('truong_id')->where('id', '=', $user)->first();
    	$datas = DB::table('qlhs_hosobaocao')->where('report','=','HSKT');

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

            $checkReportName = DB::table('qlhs_hosobaocao')->where('report_name', 'LIKE', '%'.$report_name.'%')->where('report', 'LIKE', '%HSKT%')->get();

            if (!is_null($checkReportName) && !empty($checkReportName) && count($checkReportName) > 0) {
	            $result['error'] = "Tên báo cáo đã tồn tại, xin mời nhập tên khác!";
	            return $result;
	        }
			// and qlhs_kinhphidoituong.start_date < '.$year.'-01-10 < qlhs_kinhphidoituong.end_date
			//and qlhs_profile.profile_year < '.$year.'-09-01
			$getDataType1 = DB::table('qlhs_profile')
			->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id in (47, 50)'))
			->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))
			->join('qlhs_kinhphidoituong as hocbong', DB::raw('hocbong.doituong_id = 95 and hocbong.idTruong'), '=', 'qlhs_profile.profile_school_id')
			->join('qlhs_kinhphidoituong as muadodunght', DB::raw('muadodunght.doituong_id = 100 and muadodunght.idTruong'), '=', 'qlhs_profile.profile_school_id')
			->join('qlhs_kinhphidoituong as hocbong_new', DB::raw('hocbong_new.doituong_id = 95 and hocbong_new.idTruong'), '=', 'qlhs_profile.profile_school_id')
			->join('qlhs_kinhphidoituong as muadodunght_new', DB::raw('muadodunght_new.doituong_id = 100 and muadodunght_new.idTruong'), '=', 'qlhs_profile.profile_school_id')
			->where('qlhs_profile.profile_school_id', '=', $school_id)
			->where('hocbong.start_date', '<', $year.'-09-10')
			->where('hocbong.end_date', '>', $year.'-09-10')
			->where('muadodunght.start_date', '<', $year.'-09-10')
			->where('muadodunght.end_date', '>', $year.'-09-10')
			->where('hocbong_new.start_date', '<', ($year + 1).'-09-10')
			->where('hocbong_new.end_date', '>', ($year + 1).'-09-10')
			->where('muadodunght_new.start_date', '<', ($year + 1).'-09-10')
			->where('muadodunght_new.end_date', '>', ($year + 1).'-09-10')
			->where('qlhs_profile.profile_year', '<', $year.'-06-01')
			->select('qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'hocbong.money as hocbonghotro', 'muadodunght.money as muadodunghotro', 'hocbong_new.money as hocbonghotro_new', 'muadodunght_new.money as muadodunghotro_new', 
				DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 47 then 1 else 0 END) as hotrohocbong'), 
				DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 50 then 1 else 0 END) as hotromuadodunght'), 
				
				DB::raw('(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hotro_hocky2_old'), 
				DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hotro_hocky1_cur'), 
				DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.($year + 1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hotro_hocky2_cur'), 
				DB::raw('(CASE when qlhs_profile_history.level_new <> "" and qlhs_profile.profile_year < "'.($year + 1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hotro_hocky1_new')
				)
			->groupBy('qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'hocbong.money', 'muadodunght.money', 'hocbong_new.money', 'muadodunght_new.money')->get();
			//and qlhs_profile.profile_year < '.$year.'-05-30 

			$getDataType2 = DB::table('qlhs_profile')
			->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id in (47, 50)'))
			->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))
			->join('qlhs_kinhphidoituong as hocbong', DB::raw('hocbong.doituong_id = 95 and hocbong.idTruong'), '=', 'qlhs_profile.profile_school_id')
			->join('qlhs_kinhphidoituong as muadodunght', DB::raw('muadodunght.doituong_id = 100 and muadodunght.idTruong'), '=', 'qlhs_profile.profile_school_id')
			->join('qlhs_kinhphidoituong as hocbong_new', DB::raw('hocbong_new.doituong_id = 95 and hocbong_new.idTruong'), '=', 'qlhs_profile.profile_school_id')
			->join('qlhs_kinhphidoituong as muadodunght_new', DB::raw('muadodunght_new.doituong_id = 100 and muadodunght_new.idTruong'), '=', 'qlhs_profile.profile_school_id')
			->where('qlhs_profile.profile_school_id', '=', $school_id)
			->where('hocbong.start_date', '<', $year.'-09-10')
			->where('hocbong.end_date', '>', $year.'-09-10')
			->where('muadodunght.start_date', '<', $year.'-09-10')
			->where('muadodunght.end_date', '>', $year.'-09-10')
			->where('hocbong_new.start_date', '<', ($year + 1).'-09-10')
			->where('hocbong_new.end_date', '>', ($year + 1).'-09-10')
			->where('muadodunght_new.start_date', '<', ($year + 1).'-09-10')
			->where('muadodunght_new.end_date', '>', ($year + 1).'-09-10')
			->where('qlhs_profile.profile_year', '>', $year.'-05-31')
            ->where('qlhs_profile.profile_year', '<', ($year + 1).'-06-01')
			->select('qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'hocbong.money as hocbonghotro', 'muadodunght.money as muadodunghotro', 'hocbong_new.money as hocbonghotro_new', 'muadodunght_new.money as muadodunghotro_new', 
				DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 47 then 1 else 0 END) as hotrohocbong'), 
				DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 50 then 1 else 0 END) as hotromuadodunght'), 
				
				DB::raw('(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hotro_hocky2_old'), 
				DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hotro_hocky1_cur'), 
				DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.($year + 1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hotro_hocky2_cur'), 
				DB::raw('(CASE when qlhs_profile_history.level_new <> "" and qlhs_profile.profile_year < "'.($year + 1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hotro_hocky1_new')
				)
			->groupBy('qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'hocbong.money', 'muadodunght.money', 'hocbong_new.money', 'muadodunght_new.money')->get();

			$getDataType3 = DB::table('qlhs_profile')
			->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id in (47, 50)'))
			->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.($year + 1).'-'.($year + 2).'"'))
			->join('qlhs_kinhphidoituong as hocbong', DB::raw('hocbong.doituong_id = 95 and hocbong.idTruong'), '=', 'qlhs_profile.profile_school_id')
			->join('qlhs_kinhphidoituong as muadodunght', DB::raw('muadodunght.doituong_id = 100 and muadodunght.idTruong'), '=', 'qlhs_profile.profile_school_id')
			->join('qlhs_kinhphidoituong as hocbong_new', DB::raw('hocbong_new.doituong_id = 95 and hocbong_new.idTruong'), '=', 'qlhs_profile.profile_school_id')
			->join('qlhs_kinhphidoituong as muadodunght_new', DB::raw('muadodunght_new.doituong_id = 100 and muadodunght_new.idTruong'), '=', 'qlhs_profile.profile_school_id')
			->where('qlhs_profile.profile_school_id', '=', $school_id)
			->where('hocbong.start_date', '<', $year.'-09-10')
			->where('hocbong.end_date', '>', $year.'-09-10')
			->where('muadodunght.start_date', '<', $year.'-09-10')
			->where('muadodunght.end_date', '>', $year.'-09-10')
			->where('hocbong_new.start_date', '<', ($year + 1).'-09-10')
			->where('hocbong_new.end_date', '>', ($year + 1).'-09-10')
			->where('muadodunght_new.start_date', '<', ($year + 1).'-09-10')
			->where('muadodunght_new.end_date', '>', ($year + 1).'-09-10')
			->where('qlhs_profile.profile_year', '>', ($year + 1).'-05-31')
			->where('qlhs_profile.profile_year', '<', ($year + 2).'-01-01')
			->select('qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'hocbong.money as hocbonghotro', 'muadodunght.money as muadodunghotro', 'hocbong_new.money as hocbonghotro_new', 'muadodunght_new.money as muadodunghotro_new', 
				DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 47 then 1 else 0 END) as hotrohocbong'), 
				DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 50 then 1 else 0 END) as hotromuadodunght'), 
				
				DB::raw('(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hotro_hocky2_old'), 
				DB::raw('(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hotro_hocky1_cur'), 
				DB::raw('(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile.profile_year < "'.($year + 1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hotro_hocky2_cur'), 
				DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.($year + 1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hotro_hocky1_new')
				)
			->groupBy('qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'hocbong.money', 'muadodunght.money', 'hocbong_new.money', 'muadodunght_new.money')->get();
//return $getDataType2;
			$time = time();

            if ((is_null($getDataType1) || empty($getDataType1) || count($getDataType1) == 0) && (is_null($getDataType2) || empty($getDataType2) || count($getDataType2) == 0) && (is_null($getDataType3) || empty($getDataType3) || count($getDataType3) == 0)) {
                $result['success'] = "Danh sách trống!";
                return $result;
            }
			
			if (!is_null($getDataType1) && !empty($getDataType1) && count($getDataType1) > 0) {
				$check = $this->insertHSKT($getDataType1, 1, $current_user_id, $school_id, $year, $time);
			}

			if (!is_null($getDataType2) && !empty($getDataType2) && count($getDataType2) > 0 && $check) {
				$check = $this->insertHSKT($getDataType2, 2, $current_user_id, $school_id, $year, $time);
			}

			if (!is_null($getDataType3) && !empty($getDataType3) && count($getDataType3) > 0 && $check) {
				$check = $this->insertHSKT($getDataType3, 3, $current_user_id, $school_id, $year, $time);
			}

			if ($check) {
				$type_code = 'HSKT-' . $current_user_id . '-' . $school_id . '-' . $year . '' . ($year + 1) . '-' . $time;

                $dir = storage_path().'/files/HSKT';
                if(trim($files) != ""){
                    if(file_exists($dir.'/'. $filename_attach)){
                        $files->move($dir, $filename_attach.'-'.$time); 
                        //File::delete($dir.'/'. $filename_attach); 
                    }else{
                        $files->move($dir, $filename_attach);   
                    }
                }

				$insert_hosobaocao_id = DB::table('qlhs_hosobaocao')->insertGetId(['report_name' => $report_name, 'report_type' => $type_code, 'report_date' => $current_date, 'created_at' => $current_date, 'updated_at' => $current_date, 'create_userid' => $current_user_id, 'update_userid' => $current_user_id, 'report_user' => $user_create, 'report_user_sign' => $user_sign, 'report_attach_name' => $filename_attach, 'report_nature' => $status, 'report_year' => $year, 'report_id_truong' => $school_id, 'report_note' => $note, 'report' => 'HSKT']);

				if (!is_null($insert_hosobaocao_id) && $insert_hosobaocao_id > 0) {
					$this->exportforSchools($insert_hosobaocao_id);

					if (file_exists(storage_path().'/exceldownload/HSKT/'.$type_code.'.xlsx')) {
						$result['success'] = "Thêm mới thành công!";
					}
					else {
						$deleteHoso = DB::table('qlhs_hosobaocao')->where('report_type', 'LIKE', '%'.$type_code.'%')->delete();
						$deleteHSKT = DB::table('qlhs_hotrohocsinhkhuyettat')->where('type_code', 'LIKE', '%'.$type_code.'%')->delete();
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

    public function insertHSKT($getDataType, $type, $current_user_id, $school_id, $year, $time){
    	try {
    		$bool = TRUE;
    		
			$type_code = 'HSKT-' . $current_user_id . '-' . $school_id . '-' . $year . '' . ($year + 1) . '-' . $time;

			foreach ($getDataType as $value) {

				$nhucau_hocbong = 0;
				$nhucau_muadodung = 0;
				$dutoan_hocbong = 0;
				$dutoan_muadodung = 0;

				if ($value->{'hotrohocbong'} == 1) {
					$nhucau_hocbong = ($value->{'hotro_hocky2_old'} * 5 * $value->{'hocbonghotro'}) + ($value->{'hotro_hocky1_cur'} * 4 * $value->{'hocbonghotro'});
					$dutoan_hocbong = ($value->{'hotro_hocky2_cur'} * 5 * $value->{'hocbonghotro_new'}) + ($value->{'hotro_hocky1_new'} * 4 * $value->{'hocbonghotro_new'});
				}
				if ($value->{'hotromuadodunght'} == 1) {					
					$nhucau_muadodung = ($value->{'hotro_hocky2_old'} * 5 * $value->{'muadodunghotro'}) + ($value->{'hotro_hocky1_cur'} * 4 * $value->{'muadodunghotro'});
					$dutoan_muadodung = ($value->{'hotro_hocky2_cur'} * 5 * $value->{'muadodunghotro_new'}) + ($value->{'hotro_hocky1_new'} * 4 * $value->{'muadodunghotro_new'});
				}
				
				$tong_nhucau = ($nhucau_hocbong + $nhucau_muadodung);
				
				$tong_dutoan = ($dutoan_hocbong + $dutoan_muadodung);

				$insert_type = DB::table('qlhs_hotrohocsinhkhuyettat')->insert([
					'profile_id' => $value->{'profile_id'}, 
					'hotrohocbong' => $value->{'hotrohocbong'}, 
					'hotromuadodung' => $value->{'hotromuadodunght'}, 
					'hocky2_old' => $value->{'hotro_hocky2_old'}, 
					'hocky1_cur' => $value->{'hotro_hocky1_cur'}, 
					'hocky2_cur' => $value->{'hotro_hocky2_cur'}, 
					'hocky1_new' => $value->{'hotro_hocky1_new'}, 
					'nhucau_hocbong' => $nhucau_hocbong, 
					'nhucau_muadodung' => $nhucau_muadodung, 
					'tong_nhucau' => $tong_nhucau, 
					'dutoan_hocbong' => $dutoan_hocbong, 
					'dutoan_muadodung' => $dutoan_muadodung, 
					'tong_dutoan' => $tong_dutoan,  
					'type_code' => $type_code, 'type' => $type
					]);

				if ($insert_type == 0) {
					$bool = FALSE;
					$deleteHSKT = DB::table('qlhs_hotrohocsinhkhuyettat')->where('type_code', 'LIKE', '%'.$type_code.'%')->where('type', '=', $type)->delete();
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
        $data = DB::table('qlhs_hosobaocao')->where('report_id','=',$id)->select('report_type','report_attach_name','report')->first(); 
        $del1 = DB::table('qlhs_hotrohocsinhkhuyettat')->where('type_code','=',$data->report_type)->delete();
        $dir = storage_path().'/files/'.$data->report;
        if($del1>0){
            $del2 = DB::table('qlhs_hosobaocao')->where('report_id','=',$id)->delete();

            if(file_exists($dir.'/'. $data->report_attach_name)){  
                File::delete($dir.'/'. $data->report_attach_name);  
            }

			if (file_exists(storage_path().'/exceldownload/HSKT/'.$data->report_type.'.xlsx')) {
				File::delete(storage_path().'/exceldownload/HSKT/'.$data->report_type.'.xlsx');
			}

            if($del2> 0){
                $json['success'] = 'Xóa thành công';
            }else{
                $json['error'] = 'Xóa lỗi';
            }
        }
        return $json;
    }


    public function download_attach($id){
    	$data = DB::table('qlhs_hosobaocao')->where('report','=','HSKT')->where('report_id','=',$id)->select('report_attach_name')->first(); 
    	$dir = storage_path().'/files/HSKT/'.$data->report_attach_name;
    	return response()->download($dir,$data->report_attach_name);
    }

    public function downloadfile_Export($id){
    	$data = DB::table('qlhs_hosobaocao')->where('report','=','HSKT')->where('report_id','=',$id)->select('report_type')->first(); 
    	$dir = storage_path().'/exceldownload/HSKT/'.$data->report_type.'.xlsx';
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
    		$data_results['a'] = DB::table('qlhs_hotrohocsinhkhuyettat')
    		->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_hotrohocsinhkhuyettat.profile_id')
    		->leftJoin(DB::raw('(select decided_profile_id, max(decided_id) decided_id, max(decided_confirmation) decided_confirmation, max(decided_confirmdate) decided_confirmdate, max(decided_number) decided_number, max(decided_type) decided_type from qlhs_decided GROUP BY decided_profile_id) as qlhs_decided'), 'qlhs_decided.decided_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_decided.decided_type = "HSKT"'))
    		->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
    		->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_hotrohocsinhkhuyettat.profile_id and qlhs_profile_history.history_year = "'.$getSchoolName->report_year.'-'.($getSchoolName->report_year + 1).'"'))
    		->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
            ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
    		->where('qlhs_hotrohocsinhkhuyettat.type_code', '=', $getSchoolName->report_type)
    		->where('qlhs_hotrohocsinhkhuyettat.type', '=', 1)
    		->select('qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile_history.history_year', 'qlhs_decided.decided_id', 'qlhs_decided.decided_confirmation', 'qlhs_decided.decided_number', 'qlhs_decided.decided_confirmdate', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'qlhs_hotrohocsinhkhuyettat.*')->get();

    		$data_results['b'] = DB::table('qlhs_hotrohocsinhkhuyettat')
    		->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_hotrohocsinhkhuyettat.profile_id')
    		->leftJoin(DB::raw('(select decided_profile_id, max(decided_id) decided_id, max(decided_confirmation) decided_confirmation, max(decided_confirmdate) decided_confirmdate, max(decided_number) decided_number, max(decided_type) decided_type from qlhs_decided GROUP BY decided_profile_id) as qlhs_decided'), 'qlhs_decided.decided_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_decided.decided_type = "HSKT"'))
    		->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
    		->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_hotrohocsinhkhuyettat.profile_id and qlhs_profile_history.history_year = "'.$getSchoolName->report_year.'-'.($getSchoolName->report_year + 1).'"'))
    		->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
            ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
    		->where('qlhs_hotrohocsinhkhuyettat.type_code', '=', $getSchoolName->report_type)
    		->where('qlhs_hotrohocsinhkhuyettat.type', '=', 2)
    		->select('qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile_history.history_year', 'qlhs_decided.decided_id', 'qlhs_decided.decided_confirmation', 'qlhs_decided.decided_number', 'qlhs_decided.decided_confirmdate', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'qlhs_hotrohocsinhkhuyettat.*')->get();

    		$data_results['c'] = DB::table('qlhs_hotrohocsinhkhuyettat')
    		->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_hotrohocsinhkhuyettat.profile_id')
    		->leftJoin(DB::raw('(select decided_profile_id, max(decided_id) decided_id, max(decided_confirmation) decided_confirmation, max(decided_confirmdate) decided_confirmdate, max(decided_number) decided_number, max(decided_type) decided_type from qlhs_decided GROUP BY decided_profile_id) as qlhs_decided'), 'qlhs_decided.decided_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_decided.decided_type = "HSKT"'))
    		->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
    		->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_hotrohocsinhkhuyettat.profile_id and qlhs_profile_history.history_year = "'.($getSchoolName->report_year + 1).'-'.($getSchoolName->report_year + 2).'"'))
    		->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
            ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
    		->where('qlhs_hotrohocsinhkhuyettat.type_code', '=', $getSchoolName->report_type)
    		->where('qlhs_hotrohocsinhkhuyettat.type', '=', 3)
    		->select('qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile_history.history_year', 'qlhs_decided.decided_id', 'qlhs_decided.decided_confirmation', 'qlhs_decided.decided_number', 'qlhs_decided.decided_confirmdate', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'qlhs_hotrohocsinhkhuyettat.*')->get();
    	}

        $data_results['schools_name'] = $getSchoolName->schools_name;
        $data_results['report_year'] = $getSchoolName->report_year;
        $this->addCellExcel($data_results, $getSchoolName->report_type, TRUE);
	}

	private function addCellExcel($data_results, $filename, $type = true){
		$excel = 	Excel::load(storage_path().'/exceltemplate/laphosoHSKT.xlsx', function($reader) use($data_results){
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
			$reader->getActiveSheet()->setCellValueByColumnAndRow(0, 2, 'NHU CẦU KINH PHÍ NĂM '.$data_results['report_year'].', DỰ TOÁN NĂM '.($data_results['report_year'] + 1).' ĐỐI VỚI CHÍNH SÁCH HỖ TRỢ HỌC SINH KHUYẾT TẬT THEO THÔNG TƯ LIÊN TỊCH SỐ 42/2013/TTLT-BGDĐT-BLĐTBXH-BTC (KHỐI MẦM NON VÀ TRUNG HỌC PHỔ THÔNG)')->getStyle('A2')->applyFromArray($FontArray)->applyFromArray($style);
			$reader->getActiveSheet()->setCellValueByColumnAndRow(2, 5, 'Thông tin cơ bản (tính tại thời điểm tháng 5/'.$data_results['report_year'].')')->getStyle('C5')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
			$reader->getActiveSheet()->setCellValueByColumnAndRow(22, 5, 'Nhu cầu kinh phí năm '.$data_results['report_year'])->getStyle('W5')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
			$reader->getActiveSheet()->setCellValueByColumnAndRow(25, 5, 'Dự toán kinh phí năm '.($data_results['report_year'] + 1))->getStyle('Z5')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

			$reader->getActiveSheet()->setCellValueByColumnAndRow(2, 7, 'Học kì II năm học '.($data_results['report_year'] - 1).'-'.$data_results['report_year'])->getStyle('C7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
			$reader->getActiveSheet()->setCellValueByColumnAndRow(3, 7, 'Năm học '.$data_results['report_year'].'-'.($data_results['report_year'] + 1))->getStyle('D7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
			$reader->getActiveSheet()->setCellValueByColumnAndRow(4, 7, 'Năm học '.($data_results['report_year'] + 1).'-'.($data_results['report_year'] + 2))->getStyle('E7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

			$reader->getActiveSheet()->setCellValueByColumnAndRow(18, 7, 'Học kỳ II năm học '.($data_results['report_year'] - 1).'-'.$data_results['report_year'])->getStyle('S7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
			$reader->getActiveSheet()->setCellValueByColumnAndRow(19, 7, 'Học kỳ I năm học '.$data_results['report_year'].'-'.($data_results['report_year'] + 1))->getStyle('T7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
			$reader->getActiveSheet()->setCellValueByColumnAndRow(20, 7, 'Học kỳ II năm học '.$data_results['report_year'].'-'.($data_results['report_year'] + 1))->getStyle('U7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
			$reader->getActiveSheet()->setCellValueByColumnAndRow(21, 7, 'Học kỳ I năm học '.($data_results['report_year'] + 1).'-'.($data_results['report_year'] + 2))->getStyle('V7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
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
            $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row, '')->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row, '')->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row, '')->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		  	$reader->getActiveSheet()->setCellValueByColumnAndRow(18, $row,$data_results['TotalCount']->tonghocky2_old)->getStyle('S'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight)->applyFromArray($FontArrayitalic);
		  	$reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row,$data_results['TotalCount']->tonghocky1_cur)->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight)->applyFromArray($FontArrayitalic);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row,$data_results['TotalCount']->tonghocky2_cur)->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight)->applyFromArray($FontArrayitalic);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row,$data_results['TotalCount']->tonghocky1_new)->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight)->applyFromArray($FontArrayitalic);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row,$data_results['TotalCount']->tong_tongnhucau)->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight)->applyFromArray($FontArrayitalic);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row,$data_results['TotalCount']->tongnhucau_hocbong)->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight)->applyFromArray($FontArrayitalic);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(24, $row,$data_results['TotalCount']->tongnhucau_muadodung)->getStyle('Y'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight)->applyFromArray($FontArrayitalic);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(25, $row,$data_results['TotalCount']->tong_tongdutoan)->getStyle('Z'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight)->applyFromArray($FontArrayitalic);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(26, $row,$data_results['TotalCount']->tongdutoan_hocbong)->getStyle('AA'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight)->applyFromArray($FontArrayitalic);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(27, $row,$data_results['TotalCount']->tongdutoan_muadodung)->getStyle('AB'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight)->applyFromArray($FontArrayitalic);


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
            $reader->getActiveSheet()->setCellValueByColumnAndRow(12, $row, '')->getStyle('M'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(13, $row, '')->getStyle('N'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row, '')->getStyle('O'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row, '')->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row, '')->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row, '')->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		  	$reader->getActiveSheet()->setCellValueByColumnAndRow(18, $row,$data_results['aCount']->tonghocky2_old)->getStyle('S'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		  	$reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row,$data_results['aCount']->tonghocky1_cur)->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row,$data_results['aCount']->tonghocky2_cur)->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row,$data_results['aCount']->tonghocky1_new)->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row,$data_results['aCount']->tong_tongnhucau)->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row,$data_results['aCount']->tongnhucau_hocbong)->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(24, $row,$data_results['aCount']->tongnhucau_muadodung)->getStyle('Y'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(25, $row,$data_results['aCount']->tong_tongdutoan)->getStyle('Z'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(26, $row,$data_results['aCount']->tongdutoan_hocbong)->getStyle('AA'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(27, $row,$data_results['aCount']->tongdutoan_muadodung)->getStyle('AB'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);

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

					// if ($strYear > $data_results['report_year']) {
					// 	$class_lv1 = 0;
					// 	$class_lv2 = 0;
					// 	$class_lv3 = $value->level_next_1;
					// }

					$decided_date = "";
					if (!is_null($value->decided_confirmdate) && !empty($value->decided_confirmdate)) {					
						$decided_date = Carbon::parse($value->decided_confirmdate)->format('d-m-Y');
					}

				    $reader->getActiveSheet()->setCellValueByColumnAndRow($col, $row, ++$indexa)->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($style);

				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->profile_name)->getStyle('B'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
				        	
					$reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->level_old)->getStyle('C'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
				        	
					$reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->level_cur)->getStyle('D'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
				        	
					$reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->level_new)->getStyle('E'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, Carbon::parse($value->profile_birthday)->format('d-m-Y'))->getStyle('F'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->profile_parentname)->getStyle('G'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);  
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->profile_household)->getStyle('H'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->tenxa)->getStyle('I'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->tenhuyen)->getStyle('J'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 

				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->decided_confirmation)->getStyle('K'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->decided_number)->getStyle('L'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $decided_date)->getStyle('M'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, '-')->getStyle('N'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);

				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, '-')->getStyle('O'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, '-')->getStyle('P'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->hotrohocbong)->getStyle('Q'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->hotromuadodung)->getStyle('R'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->hocky2_old)->getStyle('S'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->hocky1_cur)->getStyle('T'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->hocky2_cur)->getStyle('U'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->hocky1_new)->getStyle('V'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->tong_nhucau)->getStyle('W'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->nhucau_hocbong)->getStyle('X'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->nhucau_muadodung)->getStyle('Y'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->tong_dutoan)->getStyle('Z'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->dutoan_hocbong)->getStyle('AA'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->dutoan_muadodung)->getStyle('AB'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
					}
				}
			//b

			$row++;
			$reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row,'b')->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

		 	$reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row,$data_results['bT'])->getStyle('B'.$row)->applyFromArray($FontArray)->applyFromArray($styleLeft);
		 	
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
            $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row, '')->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row, '')->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row, '')->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		  	$reader->getActiveSheet()->setCellValueByColumnAndRow(18, $row,$data_results['bCount']->tonghocky2_old)->getStyle('S'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		  	$reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row,$data_results['bCount']->tonghocky1_cur)->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row,$data_results['bCount']->tonghocky2_cur)->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row,$data_results['bCount']->tonghocky1_new)->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row,$data_results['bCount']->tong_tongnhucau)->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row,$data_results['bCount']->tongnhucau_hocbong)->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(24, $row,$data_results['bCount']->tongnhucau_muadodung)->getStyle('Y'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(25, $row,$data_results['bCount']->tong_tongdutoan)->getStyle('Z'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(26, $row,$data_results['bCount']->tongdutoan_hocbong)->getStyle('AA'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(27, $row,$data_results['bCount']->tongdutoan_muadodung)->getStyle('AB'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);

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

				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->profile_name)->getStyle('B'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
				        	
					$reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->level_old)->getStyle('C'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
				        	
					$reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->level_cur)->getStyle('D'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
				        	
					$reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->level_new)->getStyle('E'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, Carbon::parse($value->profile_birthday)->format('d-m-Y'))->getStyle('F'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->profile_parentname)->getStyle('G'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);  
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->profile_household)->getStyle('H'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->tenxa)->getStyle('I'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->tenhuyen)->getStyle('J'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 

				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->decided_confirmation)->getStyle('K'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->decided_number)->getStyle('L'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $decided_date)->getStyle('M'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, '-')->getStyle('N'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);

				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, '-')->getStyle('O'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, '-')->getStyle('P'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->hotrohocbong)->getStyle('Q'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->hotromuadodung)->getStyle('R'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->hocky2_old)->getStyle('S'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->hocky1_cur)->getStyle('T'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->hocky2_cur)->getStyle('U'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->hocky1_new)->getStyle('V'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->tong_nhucau)->getStyle('W'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->nhucau_hocbong)->getStyle('X'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->nhucau_muadodung)->getStyle('Y'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->tong_dutoan)->getStyle('Z'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->dutoan_hocbong)->getStyle('AA'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->dutoan_muadodung)->getStyle('AB'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
					}
				}

				//c

			$row++;
			$reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row,'c')->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

		 	$reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row,$data_results['cT'])->getStyle('B'.$row)->applyFromArray($FontArray)->applyFromArray($styleLeft);
		 	
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
            $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row, '')->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row, '')->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row, '')->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
		  	$reader->getActiveSheet()->setCellValueByColumnAndRow(18, $row,$data_results['cCount']->tonghocky2_old)->getStyle('S'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		  	$reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row,$data_results['cCount']->tonghocky1_cur)->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row,$data_results['cCount']->tonghocky2_cur)->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row,$data_results['cCount']->tonghocky1_new)->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row,$data_results['cCount']->tong_tongnhucau)->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row,$data_results['cCount']->tongnhucau_hocbong)->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(24, $row,$data_results['cCount']->tongnhucau_muadodung)->getStyle('Y'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(25, $row,$data_results['cCount']->tong_tongdutoan)->getStyle('Z'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(26, $row,$data_results['cCount']->tongdutoan_hocbong)->getStyle('AA'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(27, $row,$data_results['cCount']->tongdutoan_muadodung)->getStyle('AB'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);

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

				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->profile_name)->getStyle('B'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
				        	
					$reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, '')->getStyle('C'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
				        	
					$reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, '')->getStyle('D'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
				        	
					$reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->level_cur)->getStyle('E'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, Carbon::parse($value->profile_birthday)->format('d-m-Y'))->getStyle('F'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->profile_parentname)->getStyle('G'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);  
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->profile_household)->getStyle('H'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->tenxa)->getStyle('I'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->tenhuyen)->getStyle('J'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 

				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->decided_confirmation)->getStyle('K'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->decided_number)->getStyle('L'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $decided_date)->getStyle('M'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, '-')->getStyle('N'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);

				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, '-')->getStyle('O'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, '-')->getStyle('P'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->hotrohocbong)->getStyle('Q'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->hotromuadodung)->getStyle('R'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->hocky2_old)->getStyle('S'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->hocky1_cur)->getStyle('T'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->hocky2_cur)->getStyle('U'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->hocky1_new)->getStyle('V'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->tong_nhucau)->getStyle('W'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->nhucau_hocbong)->getStyle('X'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->nhucau_muadodung)->getStyle('Y'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->tong_dutoan)->getStyle('Z'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->dutoan_hocbong)->getStyle('AA'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);     	
				    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->dutoan_muadodung)->getStyle('AB'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
				}
			}
		});
		if($type){
	    	return $excel->setFilename($filename)->store('xlsx', storage_path().'/exceldownload/HSKT');
	    }else{
	    	return $excel->setFilename($filename)->download('xlsx');
	    }
	}
    
    public function countValue($type = null,$code){

    	if($type!=null){
    		$count = DB::table('qlhs_hotrohocsinhkhuyettat')->where('type_code','=',$code)->where('type','=',$type)
    		->select(
    			DB::raw('sum(hocky2_old) as tonghocky2_old'),
    			DB::raw('sum(hocky1_cur) as tonghocky1_cur'),
    			DB::raw('sum(hocky2_cur) as tonghocky2_cur'),
    			DB::raw('sum(hocky1_new) as tonghocky1_new'),
    			DB::raw('sum(nhucau_hocbong) as tongnhucau_hocbong'),
    			DB::raw('sum(nhucau_muadodung) as tongnhucau_muadodung'),
    			DB::raw('sum(tong_nhucau) as tong_tongnhucau'),
    			DB::raw('sum(dutoan_hocbong) as tongdutoan_hocbong'),
    			DB::raw('sum(dutoan_muadodung) as tongdutoan_muadodung'),
    			DB::raw('sum(tong_dutoan) as tong_tongdutoan'))->first();
    		return $count;
    	}else{
    		$count = DB::table('qlhs_hotrohocsinhkhuyettat')->where('type_code','=',$code)
    		->select(
    			DB::raw('sum(hocky2_old) as tonghocky2_old'),
    			DB::raw('sum(hocky1_cur) as tonghocky1_cur'),
    			DB::raw('sum(hocky2_cur) as tonghocky2_cur'),
    			DB::raw('sum(hocky1_new) as tonghocky1_new'),
    			DB::raw('sum(nhucau_hocbong) as tongnhucau_hocbong'),
    			DB::raw('sum(nhucau_muadodung) as tongnhucau_muadodung'),
    			DB::raw('sum(tong_nhucau) as tong_tongnhucau'),
    			DB::raw('sum(dutoan_hocbong) as tongdutoan_hocbong'),
    			DB::raw('sum(dutoan_muadodung) as tongdutoan_muadodung'),
    			DB::raw('sum(tong_dutoan) as tong_tongdutoan'))->first();
    		return $count;
    	}
    }
}

?>