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

class LapHoSo7Controller extends Controller
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

    public function loadDataTongHop(Request $request)
    {
    	$json = [];
    	$start = $request->input('start');
    	$limit = $request->input('limit');
    	$user = Auth::user()->id;

    	$getIdTruong = DB::table('users')->select('truong_id')->where('id', '=', $user)->first();
    	$datas = DB::table('qlhs_hosobaocao')->where('report','=','TONGHOP');

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

            $checkReportName = DB::table('qlhs_hosobaocao')->where('report_name', 'LIKE', '%'.$report_name.'%')->where('report', 'LIKE', '%TONGHOP%')->get();

            if (!is_null($checkReportName) && !empty($checkReportName) && count($checkReportName) > 0) {
	            $result['error'] = "Tên báo cáo đã tồn tại, xin mời nhập tên khác!";
	            return $result;
	        }
			// and qlhs_kinhphidoituong.start_date < '.$year.'-01-10 < qlhs_kinhphidoituong.end_date
			//and qlhs_profile.profile_year < '.$year.'-09-01
			$getDataMGHP = DB::select("select SUM(qlhs_miengiamhocphi.nhu_cau) as nhucau_MGHP, SUM(qlhs_miengiamhocphi.du_toan) as dutoan_MGHP from qlhs_profile INNER JOIN qlhs_miengiamhocphi on qlhs_miengiamhocphi.id_profile = qlhs_profile.profile_id where qlhs_profile.profile_school_id = ".$school_id." and '".$year."-01-01' < qlhs_profile.profile_year < '".$year."-12-31'", array());

			$getDataCPHT = DB::select("select SUM(qlhs_chiphihoctap.nhu_cau) as nhucau_CPHT, SUM(qlhs_chiphihoctap.du_toan) as dutoan_CPHT from qlhs_profile INNER JOIN qlhs_chiphihoctap on qlhs_chiphihoctap.cpht_profile_id = qlhs_profile.profile_id where qlhs_profile.profile_school_id = ".$school_id." and '".$year."-01-01' < qlhs_profile.profile_year < '".$year."-12-31'", array());

			$getDataHTTA = DB::select("select SUM(qlhs_hotrotienan.nhu_cau) as nhucau_HTTA, SUM(qlhs_hotrotienan.du_toan) as dutoan_HTTA from qlhs_profile INNER JOIN qlhs_hotrotienan on qlhs_hotrotienan.htta_profile_id = qlhs_profile.profile_id where qlhs_profile.profile_school_id = ".$school_id." and '".$year."-01-01' < qlhs_profile.profile_year < '".$year."-12-31'", array());

			$getDataHTHSBT = DB::select("select SUM(qlhs_hotrohocsinhbantru.nhucau_hotrotienan) as nhucau_HTHSBT_tienan, SUM(qlhs_hotrohocsinhbantru.nhucau_hotrotieno) as nhucau_HTHSBT_tieno, SUM(qlhs_hotrohocsinhbantru.dutoan_hotrotienan) as dutoan_HTHSBT_tienan, SUM(qlhs_hotrohocsinhbantru.dutoan_hotrotieno) as dutoan_HTHSBT_tieno from qlhs_profile INNER JOIN qlhs_hotrohocsinhbantru on qlhs_hotrohocsinhbantru.profile_id = qlhs_profile.profile_id where qlhs_profile.profile_school_id = ".$school_id." and '".$year."-01-01' < qlhs_profile.profile_year < '".$year."-12-31'", array());

			$getDataHTHSKT = DB::select("select SUM(qlhs_hotrohocsinhkhuyettat.nhucau_hocbong) as nhucau_HTHSKT_hocbong, SUM(qlhs_hotrohocsinhkhuyettat.nhucau_muadodung) as nhucau_HTHSKT_muadodung, SUM(qlhs_hotrohocsinhkhuyettat.dutoan_hocbong) as dutoan_HTHSKT_hocbong, SUM(qlhs_hotrohocsinhkhuyettat.dutoan_muadodung) as dutoan_HTHSKT_muadodung from qlhs_profile INNER JOIN qlhs_hotrohocsinhkhuyettat on qlhs_hotrohocsinhkhuyettat.profile_id = qlhs_profile.profile_id where qlhs_profile.profile_school_id = ".$school_id." and '".$year."-01-01' < qlhs_profile.profile_year < '".$year."-12-31'", array());

			$getDataHTHSDTTS = DB::select("select SUM(qlhs_hotrohocsinhdantocthieuso.nhucau) as nhucau_HTHSDTTS, SUM(qlhs_hotrohocsinhdantocthieuso.dutoan) as dutoan_HTHSDTTS from qlhs_profile INNER JOIN qlhs_hotrohocsinhdantocthieuso on qlhs_hotrohocsinhdantocthieuso.profile_id = qlhs_profile.profile_id where qlhs_profile.profile_school_id = ".$school_id." and '".$year."-01-01' < qlhs_profile.profile_year < '".$year."-12-31'", array());

			//$getDataHTNGNA = DB::select("select qlhs_hotronguoinauan.nhucau as nhucau_HTNGNA, qlhs_hotronguoinauan.dutoan as dutoan_HTNGNA from qlhs_hotronguoinauan INNER JOIN qlhs_profile on qlhs_profile.profile_school_id = qlhs_hotronguoinauan.school_id where qlhs_profile.profile_school_id = ".$school_id." and '".$year."-01-01' < qlhs_profile.profile_year < '".$year."-12-31' GROUP BY qlhs_hotronguoinauan.nhucau, qlhs_hotronguoinauan.dutoan", array());

			$getDataHTNGNA = DB::table('qlhs_hotronguoinauan')->join('qlhs_profile', 'qlhs_profile.profile_school_id', '=', 'qlhs_hotronguoinauan.school_id')->where('qlhs_profile.profile_school_id', '=', $school_id)->where('qlhs_profile.profile_year', '>', $year.'-01-01')->where('qlhs_profile.profile_year', '<', $year.'-12-31')->select('qlhs_hotronguoinauan.nhucau as nhucau_HTNGNA', 'qlhs_hotronguoinauan.dutoan as dutoan_HTNGNA')->groupBy('qlhs_hotronguoinauan.nhucau', 'qlhs_hotronguoinauan.dutoan')->get();

			$time = time();
			
			$check = $this->insertTongHop($getDataMGHP, $getDataCPHT, $getDataHTTA, $getDataHTHSBT, $getDataHTHSKT, $getDataHTHSDTTS, $getDataHTNGNA, $current_user_id, $school_id, $year, $time);
			
			if ($check) {
				$type_code = 'TONGHOP-' . $current_user_id . '-' . $school_id . '-' . $year . '' . ($year + 1) . '-' . $time;

                $dir = storage_path().'/files/TONGHOP';
                if(trim($files) != ""){
                    if(file_exists($dir.'/'. $filename_attach)){
                        $files->move($dir, $filename_attach.'-'.$time); 
                        //File::delete($dir.'/'. $filename_attach); 
                    }else{
                        $files->move($dir, $filename_attach);   
                    }
                }

				$insert_hosobaocao_id = DB::table('qlhs_hosobaocao')->insertGetId(['report_name' => $report_name, 'report_type' => $type_code, 'report_date' => $current_date, 'created_at' => $current_date, 'updated_at' => $current_date, 'create_userid' => $current_user_id, 'update_userid' => $current_user_id, 'report_user' => $user_create, 'report_user_sign' => $user_sign, 'report_attach_name' => $filename_attach, 'report_nature' => $status, 'report_year' => $year, 'report_id_truong' => $school_id, 'report_note' => $note, 'report' => 'TONGHOP']);

				if (!is_null($insert_hosobaocao_id) && $insert_hosobaocao_id > 0) {
					$this->exportforSchools($insert_hosobaocao_id);

					if (file_exists(storage_path().'/exceldownload/TONGHOP/'.$type_code.'.xlsx')) {
						$result['success'] = "Thêm mới thành công!";
					}
					else {
						$deleteHoso = DB::table('qlhs_hosobaocao')->where('report_type', 'LIKE', '%'.$type_code.'%')->delete();
						$deleteTONGHOP = DB::table('qlhs_tonghopbaocao')->where('type_code', 'LIKE', '%'.$type_code.'%')->delete();
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

    public function insertTongHop($getDataMGHP = [], $getDataCPHT = [], $getDataHTTA = [], $getDataHTHSBT = [], $getDataHTHSKT = [], $getDataHTHSDTTS = [], $getDataHTNGNA = [], $current_user_id, $school_id, $year, $time){
    	try {
    		$bool = TRUE;
    		
			$type_code = 'TONGHOP-' . $current_user_id . '-' . $school_id . '-' . $year . '' . ($year + 1) . '-' . $time;
			$nhucau_MGHP = 0;
			$dutoan_MGHP = 0;
			$nhucau_CPHT = 0;
			$dutoan_CPHT = 0;
			$nhucau_HTTA = 0;
			$dutoan_HTTA = 0;
			$nhucau_HTHSBT_tienan = 0;
			$dutoan_HTHSBT_tienan = 0;
			$nhucau_HTHSBT_tieno = 0;
			$dutoan_HTHSBT_tieno = 0;
			$nhucau_HTHSKT_hocbong = 0;
			$dutoan_HTHSKT_hocbong = 0;
			$nhucau_HTHSKT_muadodung = 0;
			$dutoan_HTHSKT_muadodung = 0;
			$nhucau_HTHSDTTS = 0;
			$dutoan_HTHSDTTS = 0;
			$nhucau_HTNGNA = 0;
			$dutoan_HTNGNA = 0;

			if (!is_null($getDataMGHP) && !empty($getDataMGHP) && count($getDataMGHP) > 0) {				
				$nhucau_MGHP = $getDataMGHP[0]->{'nhucau_MGHP'} ? $getDataMGHP[0]->{'nhucau_MGHP'} : 0;
				$dutoan_MGHP = $getDataMGHP[0]->{'dutoan_MGHP'} ? $getDataMGHP[0]->{'dutoan_MGHP'} : 0;
			}
			if (!is_null($getDataCPHT) && !empty($getDataCPHT) && count($getDataCPHT) > 0) {
				$nhucau_CPHT = $getDataCPHT[0]->{'nhucau_CPHT'} ? $getDataCPHT[0]->{'nhucau_CPHT'} : 0;
				$dutoan_CPHT = $getDataCPHT[0]->{'dutoan_CPHT'} ? $getDataCPHT[0]->{'dutoan_CPHT'} : 0;
			}
			if (!is_null($getDataHTTA) && !empty($getDataHTTA) && count($getDataHTTA) > 0) {
				$nhucau_HTTA = $getDataHTTA[0]->{'nhucau_HTTA'} ? $getDataHTTA[0]->{'nhucau_HTTA'} : 0;
				$dutoan_HTTA = $getDataHTTA[0]->{'dutoan_HTTA'} ? $getDataHTTA[0]->{'dutoan_HTTA'} : 0;
			}
			if (!is_null($getDataHTHSBT) && !empty($getDataHTHSBT) && count($getDataHTHSBT) > 0) {
				$nhucau_HTHSBT_tienan = $getDataHTHSBT[0]->{'nhucau_HTHSBT_tienan'} ? $getDataHTHSBT[0]->{'nhucau_HTHSBT_tienan'} : 0;
				$dutoan_HTHSBT_tienan = $getDataHTHSBT[0]->{'dutoan_HTHSBT_tienan'} ? $getDataHTHSBT[0]->{'dutoan_HTHSBT_tienan'} : 0;
				$nhucau_HTHSBT_tieno = $getDataHTHSBT[0]->{'nhucau_HTHSBT_tieno'} ? $getDataHTHSBT[0]->{'nhucau_HTHSBT_tieno'} : 0;
				$dutoan_HTHSBT_tieno = $getDataHTHSBT[0]->{'dutoan_HTHSBT_tieno'} ? $getDataHTHSBT[0]->{'dutoan_HTHSBT_tieno'} : 0;
			}
			if (!is_null($getDataHTHSKT) && !empty($getDataHTHSKT) && count($getDataHTHSKT) > 0) {
				$nhucau_HTHSKT_hocbong = $getDataHTHSKT[0]->{'nhucau_HTHSKT_hocbong'} ? $getDataHTHSKT[0]->{'nhucau_HTHSKT_hocbong'} : 0;
				$dutoan_HTHSKT_hocbong = $getDataHTHSKT[0]->{'dutoan_HTHSKT_hocbong'} ? $getDataHTHSKT[0]->{'dutoan_HTHSKT_hocbong'} : 0;
				$nhucau_HTHSKT_muadodung = $getDataHTHSKT[0]->{'nhucau_HTHSKT_muadodung'} ? $getDataHTHSKT[0]->{'nhucau_HTHSKT_muadodung'} : 0;
				$dutoan_HTHSKT_muadodung = $getDataHTHSKT[0]->{'dutoan_HTHSKT_muadodung'} ? $getDataHTHSKT[0]->{'dutoan_HTHSKT_muadodung'} : 0;
			}
			if (!is_null($getDataHTHSDTTS) && !empty($getDataHTHSDTTS) && count($getDataHTHSDTTS) > 0) {
				$nhucau_HTHSDTTS = $getDataHTHSDTTS[0]->{'nhucau_HTHSDTTS'} ? $getDataHTHSDTTS[0]->{'nhucau_HTHSDTTS'} : 0;
				$dutoan_HTHSDTTS = $getDataHTHSDTTS[0]->{'dutoan_HTHSDTTS'} ? $getDataHTHSDTTS[0]->{'dutoan_HTHSDTTS'} : 0;
			}
			if (!is_null($getDataHTNGNA) && !empty($getDataHTNGNA) && count($getDataHTNGNA) > 0) {
				$nhucau_HTNGNA = $getDataHTNGNA[0]->{'nhucau_HTNGNA'} ? $getDataHTNGNA[0]->{'nhucau_HTNGNA'} : 0;
				$dutoan_HTNGNA = $getDataHTNGNA[0]->{'dutoan_HTNGNA'} ? $getDataHTNGNA[0]->{'dutoan_HTNGNA'} : 0;
			}

			$nhucau_tong_hotrobantru = $nhucau_HTHSBT_tienan + $nhucau_HTHSBT_tieno;
			$nhucau_tong_hotroHSKT = $nhucau_HTHSKT_hocbong + $nhucau_HTHSKT_muadodung;

			$dutoan_tong_hotrobantru = $dutoan_HTHSBT_tienan + $dutoan_HTHSBT_tieno;
			$dutoan_tong_hotroHSKT = $dutoan_HTHSKT_hocbong + $dutoan_HTHSKT_muadodung;

			$tong_nhucaukinhphi = $nhucau_MGHP + $nhucau_CPHT + $nhucau_HTTA + $nhucau_tong_hotrobantru + $nhucau_tong_hotroHSKT + $nhucau_HTHSDTTS + $nhucau_HTNGNA;

			$tong_dutoankinhphi = $dutoan_MGHP + $dutoan_CPHT + $dutoan_HTTA + $dutoan_tong_hotrobantru + $dutoan_tong_hotroHSKT + $dutoan_HTHSDTTS + $dutoan_HTNGNA;
			
			$insert_type = DB::table('qlhs_tonghopbaocao')->insert([
				'school_id' => $school_id, 
				'type_code' => $type_code, 
				'nhucau_capbuhocphi' => $nhucau_MGHP, 
				'nhucau_hotrochiphihoctap' => $nhucau_CPHT, 
				'nhucau_hotroantrua' => $nhucau_HTTA,  
				'nhucau_hotrotienan' => $nhucau_HTHSBT_tienan,  
				'nhucau_hotrotieno' => $nhucau_HTHSBT_tieno,  
				'nhucau_VHTT_tuthuoc' => 0, 
				'nhucau_tong_hotrobantru' => $nhucau_tong_hotrobantru, 
				'nhucau_hocbong' => $nhucau_HTHSKT_hocbong,  
				'nhucau_muadodung' => $nhucau_HTHSKT_muadodung,  
				'nhucau_tong_hotroHSKT' => $nhucau_tong_hotroHSKT, 
				'nhucau_hocbonghsdantocnoitru' => 0, 
				'nhucau_hotroHSDTTS' => $nhucau_HTHSDTTS,  
				'nhucau_hotroCPHTSVDTTS' => 0, 
				'nhucau_hotroNGNA' => $nhucau_HTNGNA,  
				'tong_nhucaukinhphi' => $tong_nhucaukinhphi
				, 
				'dutoan_capbuhocphi' => $dutoan_MGHP, 
				'dutoan_hotrochiphihoctap' => $dutoan_CPHT,  
				'dutoan_hotroantrua' => $dutoan_HTTA,  
				'dutoan_hotrotienan' => $dutoan_HTHSBT_tienan, 
				'dutoan_hotrotieno' => $dutoan_HTHSBT_tieno, 
				'dutoan_VHTT_tuthuoc' => 0, 
				'dutoan_tong_hotrobantru' => $dutoan_tong_hotrobantru, 
				'dutoan_hocbong' => $dutoan_HTHSKT_hocbong, 
				'dutoan_muadodung' => $dutoan_HTHSKT_muadodung, 
				'dutoan_tong_hotroHSKT' => $dutoan_tong_hotroHSKT, 
				'dutoan_hocbonghsdantocnoitru' => 0, 
				'dutoan_hotroHSDTTS' => $dutoan_HTHSDTTS, 
				'dutoan_hotroCPHTSVDTTS' => 0, 
				'dutoan_hotroNGNA' => $dutoan_HTNGNA, 
				'tong_dutoankinhphi' => $tong_dutoankinhphi
			]);

			if ($insert_type == 0) {
				$bool = FALSE;
			}

			return $bool;
    	} catch (Exception $e) {
    		return $e;
    	}
    }

    public function delete_report($id){
        
        $json = [];
        $data = DB::table('qlhs_hosobaocao')->where('report_id','=',$id)->select('report_type','report_attach_name','report')->first(); 
        $del1 = DB::table('qlhs_tonghopbaocao')->where('type_code','=',$data->report_type)->delete();
        $dir = storage_path().'/files/'.$data->report;
        if($del1>0){
            $del2 = DB::table('qlhs_hosobaocao')->where('report_id','=',$id)->delete();
            
            if(file_exists($dir.'/'. $data->report_attach_name)){  
                File::delete($dir.'/'. $data->report_attach_name);  
            }

			if (file_exists(storage_path().'/exceldownload/TONGHOP/'.$data->report_type.'.xlsx')) {
				File::delete(storage_path().'/exceldownload/TONGHOP/'.$data->report_type.'.xlsx');
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
    	$data = DB::table('qlhs_hosobaocao')->where('report','=','TONGHOP')->where('report_id','=',$id)->select('report_attach_name')->first(); 
    	$dir = storage_path().'/files/TONGHOP/'.$data->report_attach_name;
    	return response()->download($dir,$data->report_attach_name);
    }

    public function downloadfile_Export($id){
    	$data = DB::table('qlhs_hosobaocao')->where('report','=','TONGHOP')->where('report_id','=',$id)->select('report_type')->first(); 
    	$dir = storage_path().'/exceldownload/TONGHOP/'.$data->report_type.'.xlsx';
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
    		$data_results = DB::table('qlhs_tonghopbaocao')
    		->join('qlhs_schools', 'qlhs_schools.schools_id', '=', 'qlhs_tonghopbaocao.school_id')
    		->where('qlhs_tonghopbaocao.type_code', '=', $getSchoolName->report_type)
    		->select('qlhs_schools.schools_name', 'qlhs_tonghopbaocao.*')->get();
    	}
        
        $this->addCellExcel($data_results, $getSchoolName->report_type, TRUE);
	}
	
	private function addCellExcel($data_results, $filename, $type = true){
		$excel = 	Excel::load(storage_path().'/exceltemplate/laphosoTONGHOP.xlsx', function($reader) use($data_results){
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
            $reader->getActiveSheet()->setCellValueByColumnAndRow(0, 2, 'NHU CẦU KINH PHÍ NĂM '.$data_results['report_year'].', DỰ TOÁN NĂM '.($data_results['report_year'] + 1).' ĐỐI VỚI CÁC CHẾ ĐỘ, CHÍNH SÁCH ƯU ĐÃI CHO TRẺ EM MẪU GIÁO, HỌC SINH, SINH VIÊN')->getStyle('A2')->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(2, 5, 'NHU CẦU KINH PHÍ NĂM '.$data_results['report_year'])->getStyle('C5')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(16, 6, 'Tổng nhu cầu kinh phí năm '.$data_results['report_year'])->getStyle('Q6')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

            $reader->getActiveSheet()->setCellValueByColumnAndRow(17, 5, 'NHU CẦU KINH PHÍ NĂM '.$data_results['report_year'])->getStyle('R5')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(31, 6, 'Tổng nhu cầu kinh phí năm '.$data_results['report_year'])->getStyle('AF6')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

            $reader->getActiveSheet()->setCellValueByColumnAndRow(32, 5, 'DỰ TOÁN KINH PHÍ NĂM '.($data_results['report_year'] + 1))->getStyle('AG5')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(46, 6, 'Tổng dự toán kinh phí năm '.($data_results['report_year'] + 1))->getStyle('AU6')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

            $reader->getActiveSheet()->setCellValueByColumnAndRow(47, 5, 'DỰ TOÁN KINH PHÍ NĂM '.($data_results['report_year'] + 1))->getStyle('AV5')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(61, 6, 'Tổng dự toán kinh phí năm '.($data_results['report_year'] + 1))->getStyle('BJ6')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

			//Nhu cầu
			$reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row, 1)->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($style);
		  	$reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data_results[0]->schools_name)->getStyle('B'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
		  	$reader->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $data_results[0]->nhucau_capbuhocphi)->getStyle('C'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $data_results[0]->nhucau_hotrochiphihoctap)->getStyle('D'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $data_results[0]->nhucau_hotroantrua)->getStyle('E'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $data_results[0]->nhucau_tong_hotrobantru)->getStyle('F'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $data_results[0]->nhucau_hotrotienan)->getStyle('G'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(7, $row, $data_results[0]->nhucau_hotrotieno)->getStyle('H'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(8, $row, $data_results[0]->nhucau_VHTT_tuthuoc)->getStyle('I'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(9, $row, $data_results[0]->nhucau_tong_hotroHSKT)->getStyle('J'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(10, $row, $data_results[0]->nhucau_hocbong)->getStyle('K'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(11, $row, $data_results[0]->nhucau_muadodung)->getStyle('L'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(12, $row, $data_results[0]->nhucau_hocbonghsdantocnoitru)->getStyle('M'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(13, $row, $data_results[0]->nhucau_hotroHSDTTS)->getStyle('N'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row, $data_results[0]->nhucau_hotroCPHTSVDTTS)->getStyle('O'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row, $data_results[0]->nhucau_hotroNGNA)->getStyle('P'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row, $data_results[0]->tong_nhucaukinhphi)->getStyle('Q'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);

		    //Dự toán
		  	$reader->getActiveSheet()->setCellValueByColumnAndRow(32, $row, $data_results[0]->dutoan_capbuhocphi)->getStyle('AG'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(33, $row, $data_results[0]->dutoan_hotrochiphihoctap)->getStyle('AH'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(34, $row, $data_results[0]->dutoan_hotroantrua)->getStyle('AI'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(35, $row, $data_results[0]->dutoan_tong_hotrobantru)->getStyle('AJ'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(36, $row, $data_results[0]->dutoan_hotrotienan)->getStyle('AK'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(37, $row, $data_results[0]->dutoan_hotrotieno)->getStyle('AL'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(38, $row, $data_results[0]->dutoan_VHTT_tuthuoc)->getStyle('AM'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(39, $row, $data_results[0]->dutoan_tong_hotroHSKT)->getStyle('AN'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(40, $row, $data_results[0]->dutoan_hocbong)->getStyle('AO'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(41, $row, $data_results[0]->dutoan_muadodung)->getStyle('AP'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(42, $row, $data_results[0]->dutoan_hocbonghsdantocnoitru)->getStyle('AQ'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(43, $row, $data_results[0]->dutoan_hotroHSDTTS)->getStyle('AR'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(44, $row, $data_results[0]->dutoan_hotroCPHTSVDTTS)->getStyle('AS'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(45, $row, $data_results[0]->dutoan_hotroNGNA)->getStyle('AT'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
		    $reader->getActiveSheet()->setCellValueByColumnAndRow(46, $row, $data_results[0]->tong_dutoankinhphi)->getStyle('AU'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
			
		});
		if($type){
	    	return $excel->setFilename($filename)->store('xlsx', storage_path().'/exceldownload/TONGHOP');
	    }else{
	    	return $excel->setFilename($filename)->download('xlsx');
	    }
	}
}

?>