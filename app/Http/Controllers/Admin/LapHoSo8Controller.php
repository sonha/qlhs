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

class LapHoSo8Controller extends Controller
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

    public function loadDataNGNA(Request $request)
    {
    	$json = [];
    	$start = $request->input('start');
    	$limit = $request->input('limit');
    	$user = Auth::user()->id;

    	$getIdTruong = DB::table('users')->select('truong_id')->where('id', '=', $user)->first();
    	$datas = DB::table('qlhs_hosobaocao')->where('report','=','NGNA');

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

            $checkReportName = DB::table('qlhs_hosobaocao')->where('report_name', 'LIKE', '%'.$report_name.'%')->where('report', 'LIKE', '%NGNA%')->get();

            if (!is_null($checkReportName) && !empty($checkReportName) && count($checkReportName) > 0) {
	            $result['error'] = "Tên báo cáo đã tồn tại, xin mời nhập tên khác!";
	            return $result;
	        }
			// and qlhs_kinhphidoituong.start_date < '.$year.'-01-10 < qlhs_kinhphidoituong.end_date
			//and qlhs_profile.profile_year < '.$year.'-09-01

			// $getData = DB::table('qlhs_profile')
			// ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))
			// ->join('qlhs_kinhphidoituong as nhucau', DB::raw('nhucau.doituong_id = 102 and nhucau.idTruong'), '=', 'qlhs_profile.profile_school_id')
			// ->join('qlhs_kinhphidoituong as dutoan', DB::raw('dutoan.doituong_id = 102 and dutoan.idTruong'), '=', 'qlhs_profile.profile_school_id')
			// ->where('qlhs_profile.profile_school_id', '=', $school_id)
			// ->where('nhucau.start_date', '<', $year.'-09-10')
			// ->where('nhucau.end_date', '>', $year.'-09-10')
			// ->where('dutoan.start_date', '<', ($year + 1).'-09-10')
			// ->where('dutoan.end_date', '>', ($year + 1).'-09-10')
			// ->where('qlhs_profile.profile_bantru', '=', 1)
			// ->select('nhucau.money as nhucau1', 'dutoan.money as dutoan1', 
				
			// 	DB::raw('SUM(CASE when qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as sohocsinhhocky2_old'), 
			// 	DB::raw('SUM(CASE when qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-09-01")) then 1 else 0 END) as sohocsinhhocky1_cur'), 
			// 	DB::raw('SUM(CASE when qlhs_profile.profile_year < "'.($year + 1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as sohocsinhhocky2_cur'), 
			// 	DB::raw('SUM(CASE when qlhs_profile.profile_year < "'.($year + 1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-09-01")) then 1 else 0 END) as sohocsinhhocky1_new')
			// 	)
			// ->groupBy('nhucau.money', 'dutoan.money')->get();

	        $getNhucau = DB::table('qlhs_kinhphidoituong')->where('doituong_id', '=', 102)->where('idTruong', '=', $school_id)->where('start_date', '<', $year.'-09-01')->where('end_date', '>', $year.'-09-01')->select('money')->first();

	        $getDutoan = DB::table('qlhs_kinhphidoituong')->where('doituong_id', '=', 102)->where('idTruong', '=', $school_id)->where('start_date', '<', ($year + 1).'-09-01')->where('end_date', '>', ($year + 1).'-09-01')->select('money')->first();

			$getHS_old = DB::select("select * from qlhs_profile where profile_bantru = 1 and profile_school_id = ".$school_id." and profile_year < '".$year."-06-01' and (profile_leaveschool_date IS NULL or profile_leaveschool_date > '".$year."-01-01')", array());

			$getHS_cur1 = DB::select("select * from qlhs_profile where profile_bantru = 1 and profile_school_id = ".$school_id." and profile_year < '".$year."-12-31' and (profile_leaveschool_date IS NULL or profile_leaveschool_date > '".$year."-09-01')", array());

			$getHS_cur2 = DB::select("select * from qlhs_profile where profile_bantru = 1 and profile_school_id = ".$school_id." and profile_year < '".($year + 1)."-06-01' and (profile_leaveschool_date IS NULL or profile_leaveschool_date > '".($year + 1)."-01-01')", array());

			$getHS_new = DB::select("select * from qlhs_profile where profile_bantru = 1 and profile_school_id = ".$school_id." and profile_year < '".($year + 1)."-12-31' and (profile_leaveschool_date IS NULL or profile_leaveschool_date > '".($year + 1)."-09-01')", array());
//return count($getHS_new);
			$time = time();

            if ((is_null($getHS_old) || empty($getHS_old) || count($getHS_old) == 0)
            	&& (is_null($getHS_cur1) || empty($getHS_cur1) || count($getHS_cur1) == 0)
            	&& (is_null($getHS_cur2) || empty($getHS_cur2) || count($getHS_cur2) == 0)
            	&& (is_null($getHS_new) || empty($getHS_new) || count($getHS_new) == 0)) {
                $result['success'] = "Danh sách trống!";
                return $result;
            }
			
			// if (!is_null($getData) && !empty($getData) && count($getData) > 0) {
			$check = $this->insertNGNA($getNhucau->money, $getDutoan->money, count($getHS_old), count($getHS_cur1), count($getHS_cur2), count($getHS_new), $current_user_id, $school_id, $year, $time);
			// }

			if ($check) {
				$type_code = 'NGNA-' . $current_user_id . '-' . $school_id . '-' . $year . '' . ($year + 1) . '-' . $time;

                $dir = storage_path().'/files/NGNA';
                if(trim($files) != ""){
                    if(file_exists($dir.'/'. $filename_attach)){
                        $files->move($dir, $filename_attach.'-'.$time); 
                        //File::delete($dir.'/'. $filename_attach); 
                    }else{
                        $files->move($dir, $filename_attach);   
                    }
                }

				$insert_hosobaocao_id = DB::table('qlhs_hosobaocao')->insertGetId(['report_name' => $report_name, 'report_type' => $type_code, 'report_date' => $current_date, 'created_at' => $current_date, 'updated_at' => $current_date, 'create_userid' => $current_user_id, 'update_userid' => $current_user_id, 'report_user' => $user_create, 'report_user_sign' => $user_sign, 'report_attach_name' => $filename_attach, 'report_nature' => $status, 'report_year' => $year, 'report_id_truong' => $school_id, 'report_note' => $note, 'report' => 'NGNA']);

				if (!is_null($insert_hosobaocao_id) && $insert_hosobaocao_id > 0) {
					$this->exportforSchools($insert_hosobaocao_id);

					if (file_exists(storage_path().'/exceldownload/NGNA/'.$type_code.'.xlsx')) {
						$result['success'] = "Thêm mới thành công!";
					}
					else {
						$deleteHoso = DB::table('qlhs_hosobaocao')->where('report_type', 'LIKE', '%'.$type_code.'%')->delete();
						$deleteNGNA = DB::table('qlhs_hotronguoinauan')->where('type_code', 'LIKE', '%'.$type_code.'%')->delete();
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

    public function insertNGNA($nhucau, $dutoan, $hs_old, $hs_cur1, $hs_cur2, $hs_new, $current_user_id, $school_id, $year, $time){
    	try {
    		$bool = TRUE;
    		
			$type_code = 'NGNA-' . $current_user_id . '-' . $school_id . '-' . $year . '' . ($year + 1) . '-' . $time;

			// foreach ($getData as $value) {
				$sohocsinhhocky2_old = (int)$hs_old;//$value->{'sohocsinhhocky2_old'};
				$sohocsinhhocky1_cur = (int)$hs_cur1;//$value->{'sohocsinhhocky1_cur'};
				$sohocsinhhocky2_cur = (int)$hs_cur2;//$value->{'sohocsinhhocky2_cur'};
				$sohocsinhhocky1_new = (int)$hs_new;//$value->{'sohocsinhhocky1_new'};

				$nguoinauanhocky2_old = (int)($sohocsinhhocky2_old/50);
				$nguoinauanhocky1_cur = (int)($sohocsinhhocky1_cur/50);
				$nguoinauanhocky2_cur = (int)($sohocsinhhocky2_cur/50);
				$nguoinauanhocky1_new = (int)($sohocsinhhocky1_new/50);

				if (($sohocsinhhocky2_old % 50) > 30) {
					$nguoinauanhocky2_old++;
				}
				if (($sohocsinhhocky1_cur % 50) > 30) {
					$nguoinauanhocky1_cur++;
				}
				if (($sohocsinhhocky2_cur % 50) > 30) {
					$nguoinauanhocky2_cur++;
				}
				if (($sohocsinhhocky1_new % 50) > 30) {
					$nguoinauanhocky1_new++;
				}

				$tong_nhucau = ($nguoinauanhocky2_old * $nhucau * 5) + ($nguoinauanhocky1_cur * $nhucau * 4);
				$tong_dutoan = ($nguoinauanhocky2_cur * $dutoan * 5) + ($nguoinauanhocky1_new * $dutoan * 4);

				$insert_type = DB::table('qlhs_hotronguoinauan')->insert([
					'school_id' => $school_id, 
					'sohocsinhhocky2_old' => $sohocsinhhocky2_old, 
					'sohocsinhhocky1_cur' => $sohocsinhhocky1_cur, 
					'sohocsinhhocky2_cur' => $sohocsinhhocky2_cur, 
					'sohocsinhhocky1_new' => $sohocsinhhocky1_new, 
					'nguoinauanhocky2_old' => $nguoinauanhocky2_old, 
					'nguoinauanhocky1_cur' => $nguoinauanhocky1_cur, 
					'nguoinauanhocky2_cur' => $nguoinauanhocky2_cur, 
					'nguoinauanhocky1_new' => $nguoinauanhocky1_new, 
					'nhucau' => $tong_nhucau, 
					'dutoan' => $tong_dutoan, 
					'type_code' => $type_code
					]);

				if ($insert_type == 0) {
					$bool = FALSE;
				}
			// }

			return $bool;
    	} catch (Exception $e) {
    		return $e;
    	}
    }


    public function delete_report($id){
        
        $json = [];
        $data = DB::table('qlhs_hosobaocao')->where('report_id','=',$id)->select('report_type','report_attach_name','report')->first(); 
        $del1 = DB::table('qlhs_hotronguoinauan')->where('type_code','=',$data->report_type)->delete();
        $dir = storage_path().'/files/'.$data->report;
        if($del1>0){
            $del2 = DB::table('qlhs_hosobaocao')->where('report_id','=',$id)->delete();

            if(file_exists($dir.'/'. $data->report_attach_name)){ 
                File::delete($dir.'/'. $data->report_attach_name);  
            }

			if (file_exists(storage_path().'/exceldownload/NGNA/'.$data->report_type.'.xlsx')) {
				File::delete(storage_path().'/exceldownload/NGNA/'.$data->report_type.'.xlsx');
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
    	$data = DB::table('qlhs_hosobaocao')->where('report','=','NGNA')->where('report_id','=',$id)->select('report_attach_name')->first(); 
    	$dir = storage_path().'/files/NGNA/'.$data->report_attach_name;
    	return response()->download($dir,$data->report_attach_name);
    }

    public function downloadfile_Export($id){
    	$data = DB::table('qlhs_hosobaocao')->where('report','=','NGNA')->where('report_id','=',$id)->select('report_type')->first(); 
    	$dir = storage_path().'/exceldownload/NGNA/'.$data->report_type.'.xlsx';
    	return response()->download($dir, $data->report_type.'.xlsx');
    }

	public function exportforSchools($id){
	//$type = true;
    	if (is_null($id) || empty($id) || $id == 0) {
    		return "Mời bấm vào tên danh sách muốn kết xuất để kêt xuất file Excel!";
    	}

    	$getSchoolName = DB::table('qlhs_hosobaocao')->join('qlhs_schools', 'qlhs_schools.schools_id', '=', 'qlhs_hosobaocao.report_id_truong')->where('qlhs_hosobaocao.report_id', '=', $id)->select('qlhs_schools.schools_name', 'qlhs_hosobaocao.report_type', 'qlhs_hosobaocao.report_year')->first();
		$data_results = [];
		
    	if ($getSchoolName->report_type != null && $getSchoolName->report_type != "") {
    		//Get by type A
    		$data_results = DB::table('qlhs_hotronguoinauan')
    		->join('qlhs_schools', 'qlhs_schools.schools_id', '=', 'qlhs_hotronguoinauan.school_id')
    		->where('qlhs_hotronguoinauan.type_code', '=', $getSchoolName->report_type)
    		->select('qlhs_schools.schools_name', 'qlhs_hotronguoinauan.*')->get();
    	}
        
        $data_results['report_year'] = $getSchoolName->report_year;
        $this->addCellExcel($data_results, $getSchoolName->report_type, TRUE);
	}
	
	private function addCellExcel($data_results, $filename, $type = true){
		$excel = 	Excel::load(storage_path().'/exceltemplate/laphosoNGNA.xlsx', function($reader) use($data_results){
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

			//-----------------------------------------Title------------------------------------------------------------------------------------------------
			$reader->getActiveSheet()->setCellValueByColumnAndRow(0, 3, 'NHU CẦU KINH PHÍ NĂM '.$data_results['report_year'].', DỰ TOÁN NĂM '.($data_results['report_year'] + 1).' ĐỐI VỚI CHÍNH SÁCH HỖ TRỢ NGƯỜI NẤU ĂN THEO NGHỊ QUYẾT SỐ 23/2015/NQ-HĐND CỦA HỘI ĐỒNG NHÂN DÂN')->getStyle('A3')->applyFromArray($FontArray)->applyFromArray($style);

			$reader->getActiveSheet()->setCellValueByColumnAndRow(2, 7, 'Học kỳ II năm học '.($data_results['report_year'] - 1).'-'.$data_results['report_year'])->getStyle('C7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
			$reader->getActiveSheet()->setCellValueByColumnAndRow(3, 7, 'Học kỳ I năm học '.$data_results['report_year'].'-'.($data_results['report_year'] + 1))->getStyle('D7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
			$reader->getActiveSheet()->setCellValueByColumnAndRow(4, 7, 'Học kỳ II năm học '.$data_results['report_year'].'-'.($data_results['report_year'] + 1))->getStyle('E7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
			$reader->getActiveSheet()->setCellValueByColumnAndRow(5, 7, 'Học kỳ I năm học '.($data_results['report_year'] + 1).'-'.($data_results['report_year'] + 2))->getStyle('F7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

			$reader->getActiveSheet()->setCellValueByColumnAndRow(6, 7, 'Học kỳ II năm học '.($data_results['report_year'] - 1).'-'.$data_results['report_year'])->getStyle('G7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
			$reader->getActiveSheet()->setCellValueByColumnAndRow(7, 7, 'Học kỳ I năm học '.$data_results['report_year'].'-'.($data_results['report_year'] + 1))->getStyle('H7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
			$reader->getActiveSheet()->setCellValueByColumnAndRow(8, 7, 'Học kỳ II năm học '.$data_results['report_year'].'-'.($data_results['report_year'] + 1))->getStyle('I7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
			$reader->getActiveSheet()->setCellValueByColumnAndRow(9, 7, 'Học kỳ I năm học '.($data_results['report_year'] + 1).'-'.($data_results['report_year'] + 2))->getStyle('J7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

			$reader->getActiveSheet()->setCellValueByColumnAndRow(10, 6, 'Nhu cầu kinh phí năm '.$data_results['report_year'])->getStyle('K6')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
			$reader->getActiveSheet()->setCellValueByColumnAndRow(11, 6, 'Dự toán kinh phí năm '.($data_results['report_year'] + 1))->getStyle('L6')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
			//-----------------------------------------End Title----------------------------------------------------------------------------------------------------

			//$data_results['aCount'][0]
			$reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row, 1)->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($style);
		  	$reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data_results[0]->schools_name)->getStyle('B'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
		  	$reader->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $data_results[0]->sohocsinhhocky2_old)->getStyle('C'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $data_results[0]->sohocsinhhocky1_cur)->getStyle('D'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $data_results[0]->sohocsinhhocky2_cur)->getStyle('E'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $data_results[0]->sohocsinhhocky1_new)->getStyle('F'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $data_results[0]->nguoinauanhocky2_old)->getStyle('G'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(7, $row, $data_results[0]->nguoinauanhocky1_cur)->getStyle('H'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(8, $row, $data_results[0]->nguoinauanhocky2_cur)->getStyle('I'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(9, $row, $data_results[0]->nguoinauanhocky1_new)->getStyle('J'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(10, $row, $data_results[0]->nhucau)->getStyle('K'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(11, $row, $data_results[0]->dutoan)->getStyle('L'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(12, $row, '')->getStyle('M'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
			
		});
		if($type){
	    	return $excel->setFilename($filename)->store('xlsx', storage_path().'/exceldownload/NGNA');
	    }else{
	    	return $excel->setFilename($filename)->download('xlsx');
	    }
	}

	public function loadAllUser(){
		try {
			$getAllUser = DB::table('users')->leftJoin('qlhs_department', 'qlhs_department.department_id', '=', 'users.phongban_id')
			->where('users.level', '=', 1)
			->select('users.id', 'users.first_name', 'users.last_name', 'qlhs_department.department_name')->get();

			return $getAllUser;
		} catch (Exception $e) {
			
		}
	}

	public function loadNoteAndFile($id){
		try {
			$getHosobaocao = DB::table('qlhs_hosobaocao')->where('report_id', '=', $id)
			->select('report_id', 'report_note', 'report_file_revert')->get();

			return $getHosobaocao;
		} catch (Exception $e) {
			
		}
	}

	public function download_filerevert($id){
        $data = DB::table('qlhs_hosobaocao')->where('report_id','=',$id)->select('report_file_revert')->first(); 
        $dir = storage_path().'/files/'.$data->report_file_revert;
        return response()->download($dir, $data->report_file_revert);
    }
}

?>