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
use App\Models\HoSoBaoCao;

class TongHopHoSoController extends Controller
{
    public function viewList(){
       return view('admin.hoso.lapdanhsach.tonghopdanhsach');
    }

	public function loadData(Request $request)
    {
    	$json = [];
    	$start = $request->input('start');
    	$limit = $request->input('limit');

    	$schools_id = $request->input('SCHOOLID');
    	$year = $request->input('YEAR');
        $keySearch = $request->input('KEY');

    	$arrYear = [];
		$arrYear = explode("-", $year);
    	// $schoolId = 37;//$request->input('SCHOOLID');
     //    $year = 2016;//$request->input('YEAR');
     //    $profileId = 834;

        // $user = Auth::user()->id;

        $datas = null;

        $result = [];


		$getData = DB::table('qlhs_hosobaocao')
		    ->leftJoin('qlhs_hosobaocao as MGHP', 'MGHP.report_name', '=', DB::raw('qlhs_hosobaocao.report_name AND MGHP.report_id_truong = '.$schools_id.' AND MGHP.report_year = '.$arrYear[0].' AND MGHP.report = "MGHP"'))
		    ->leftJoin('qlhs_hosobaocao as CPHT', 'CPHT.report_name', '=', DB::raw('qlhs_hosobaocao.report_name AND CPHT.report_id_truong = '.$schools_id.' AND CPHT.report_year = '.$arrYear[0].' AND CPHT.report = "CPHT"'))
		    ->leftJoin('qlhs_hosobaocao as HTAT', 'HTAT.report_name', '=', DB::raw('qlhs_hosobaocao.report_name AND HTAT.report_id_truong = '.$schools_id.' AND HTAT.report_year = '.$arrYear[0].' AND HTAT.report = "HTAT"'))
		    ->leftJoin('qlhs_hosobaocao as HTBT', 'HTBT.report_name', '=', DB::raw('qlhs_hosobaocao.report_name AND HTBT.report_id_truong = '.$schools_id.' AND HTBT.report_year = '.$arrYear[0].' AND HTBT.report = "HTBT"'))
		    ->leftJoin('qlhs_hosobaocao as HSKT', 'HSKT.report_name', '=', DB::raw('qlhs_hosobaocao.report_name AND HSKT.report_id_truong = '.$schools_id.' AND HSKT.report_year = '.$arrYear[0].' AND HSKT.report = "HSKT"'))
		    ->leftJoin('qlhs_hosobaocao as HSDTTS', 'HSDTTS.report_name', '=', DB::raw('qlhs_hosobaocao.report_name AND HSDTTS.report_id_truong = '.$schools_id.' AND HSDTTS.report_year = '.$arrYear[0].' AND HSDTTS.report = "HSDTTS"'))
		    ->leftJoin('qlhs_hosobaocao as HTATHS', 'HTATHS.report_name', '=', DB::raw('qlhs_hosobaocao.report_name AND HTATHS.report_id_truong = '.$schools_id.' AND HTATHS.report_year = '.$arrYear[0].' AND HTATHS.report = "HTATHS"'))
		    ->leftJoin('qlhs_hosobaocao as HBHSDTNT', 'HBHSDTNT.report_name', '=', DB::raw('qlhs_hosobaocao.report_name AND HBHSDTNT.report_id_truong = '.$schools_id.' AND HBHSDTNT.report_year = '.$arrYear[0].' AND HBHSDTNT.report = "HBHSDTNT"'))
		    ->leftJoin('qlhs_hosobaocao as NGNA', 'NGNA.report_name', '=', DB::raw('qlhs_hosobaocao.report_name AND NGNA.report_id_truong = '.$schools_id.' AND NGNA.report_year = '.$arrYear[0].' AND NGNA.report = "NGNA"'))
		    ->where('qlhs_hosobaocao.report_id_truong', '=', $schools_id)
		    ->where('qlhs_hosobaocao.report_year', '=', $arrYear[0])
		    ->select(
		    	'MGHP.report_id as MGHP_id', 'MGHP.report_name as MGHP_name', 'MGHP.report_status as MGHP_status', 'MGHP.report_type as MGHP_type', 'MGHP.report as MGHP_report', 'MGHP.report_id_truong as MGHP_truong', 'MGHP.report_year as MGHP_year', 

		    	'CPHT.report_id as CPHT_id', 'CPHT.report_name as CPHT_name', 'CPHT.report_status as CPHT_status', 'CPHT.report_type as CPHT_type', 'CPHT.report as CPHT_report', 'CPHT.report_id_truong as CPHT_truong', 'CPHT.report_year as CPHT_year', 

		    	'HTAT.report_id as HTAT_id', 'HTAT.report_name as HTAT_name', 'HTAT.report_status as HTAT_status', 'HTAT.report_type as HTAT_type', 'HTAT.report as HTAT_report', 'HTAT.report_id_truong as HTAT_truong', 'HTAT.report_year as HTAT_year', 

		    	'HTBT.report_id as HTBT_id', 'HTBT.report_name as HTBT_name', 'HTBT.report_status as HTBT_status', 'HTBT.report_type as HTBT_type', 'HTBT.report as HTBT_report', 'HTBT.report_id_truong as HTBT_truong', 'HTBT.report_year as HTBT_year', 

		    	'HSKT.report_id as HSKT_id', 'HSKT.report_name as HSKT_name', 'HSKT.report_status as HSKT_status', 'HSKT.report_type as HSKT_type', 'HSKT.report as HSKT_report', 'HSKT.report_id_truong as HSKT_truong', 'HSKT.report_year as HSKT_year', 

		    	'HSDTTS.report_id as HSDTTS_id', 'HSDTTS.report_name as HSDTTS_name', 'HSDTTS.report_status as HSDTTS_status', 'HSDTTS.report_type as HSDTTS_type', 'HSDTTS.report as HSDTTS_report', 'HSDTTS.report_id_truong as HSDTTS_truong', 'HSDTTS.report_year as HSDTTS_year', 

		    	'HTATHS.report_id as HTATHS_id', 'HTATHS.report_name as HTATHS_name', 'HTATHS.report_status as HTATHS_status', 'HTATHS.report_type as HTATHS_type', 'HTATHS.report as HTATHS_report', 'HTATHS.report_id_truong as HTATHS_truong', 'HTATHS.report_year as HTATHS_year', 

		    	'HBHSDTNT.report_id as HBHSDTNT_id', 'HBHSDTNT.report_name as HBHSDTNT_name', 'HBHSDTNT.report_status as HBHSDTNT_status', 'HBHSDTNT.report_type as HBHSDTNT_type', 'HBHSDTNT.report as HBHSDTNT_report', 'HBHSDTNT.report_id_truong as HBHSDTNT_truong', 'HBHSDTNT.report_year as HBHSDTNT_year', 

		    	'NGNA.report_id as NGNA_id', 'NGNA.report_name as NGNA_name', 'NGNA.report_status as NGNA_status', 'NGNA.report_type as NGNA_type', 'NGNA.report as NGNA_report', 'NGNA.report_id_truong as NGNA_truong', 'NGNA.report_year as NGNA_year')
		    ->groupBy(
		    	'MGHP.report_id', 'MGHP.report_name', 'MGHP.report_status', 'MGHP.report_type', 'MGHP.report', 'MGHP.report_id_truong', 'MGHP.report_year', 

		    	'CPHT.report_id', 'CPHT.report_name', 'CPHT.report_status', 'CPHT.report_type', 'CPHT.report', 'CPHT.report_id_truong', 'CPHT.report_year', 

		    	'HTAT.report_id', 'HTAT.report_name', 'HTAT.report_status', 'HTAT.report_type', 'HTAT.report', 'HTAT.report_id_truong', 'HTAT.report_year', 

		    	'HTBT.report_id', 'HTBT.report_name', 'HTBT.report_status', 'HTBT.report_type', 'HTBT.report', 'HTBT.report_id_truong', 'HTBT.report_year', 

		    	'HSKT.report_id', 'HSKT.report_name', 'HSKT.report_status', 'HSKT.report_type', 'HSKT.report', 'HSKT.report_id_truong', 'HSKT.report_year', 

		    	'HSDTTS.report_id', 'HSDTTS.report_name', 'HSDTTS.report_status', 'HSDTTS.report_type', 'HSDTTS.report', 'HSDTTS.report_id_truong', 'HSDTTS.report_year', 

		    	'HTATHS.report_id', 'HTATHS.report_name', 'HTATHS.report_status', 'HTATHS.report_type', 'HTATHS.report', 'HTATHS.report_id_truong', 'HTATHS.report_year', 

		    	'HBHSDTNT.report_id', 'HBHSDTNT.report_name', 'HBHSDTNT.report_status', 'HBHSDTNT.report_type', 'HBHSDTNT.report', 'HBHSDTNT.report_id_truong', 'HBHSDTNT.report_year', 

		    	'NGNA.report_id', 'NGNA.report_name', 'NGNA.report_status', 'NGNA.report_type', 'NGNA.report', 'NGNA.report_id_truong', 'NGNA.report_year');

        $json['totalRows'] = $getData->get()->count();
     	
		$json['startRecord'] = ($start);
		$json['numRows'] = $limit;
			
        $json['data'] = $getData->skip($start*$limit)->take($limit)->get();
    	
	    
	    return $json;
    }

    public function sendDSTonghop(Request $req){
    	try{
	    	$id = $req->input('id');

	    	$arrData = [];

	    	$arrData = explode("-", $id);

	    	$list_id_nguoinhan = $req->input('list_id_nguoinhan');
	    	$list_id_cc = $req->input('list_id_cc');
    		
    		$time = time();
	    	$data = [];

	    	$now = Carbon::now('Asia/Ho_Chi_Minh');
			$update = 0;

			foreach ($arrData as $value) {
				$data = explode(',', $value);

				if ($data[0] > 0) {
					$hosobaocao = HoSoBaoCao::find($data[0]);
			    	$hosobaocao->report_status = 1;
			    	$hosobaocao->report_verify = 0;
			    	$hosobaocao->report_user_send = Auth::user()->id;
			    	$hosobaocao->save();

			    	$update = DB::table('qlhs_thamdinh')->insert([
		    			'thamdinh_name' => $data[1].'-'.$time,
						'thamdinh_type' => $data[1],
						'thamdinh_hoso_id' => $data[0],
						'thamdinh_trangthai' => 0,
						'thamdinh_content' => $hosobaocao->report_note,
						'thamdinh_ngaygui' => $now,
						'thamdinh_file_dinhkem' => $hosobaocao->report_type.'.xlsx',
						'thamdinh_file_dikem' => $hosobaocao->report_attach_name, 
						'thamdinh_nguoigui' => Auth::user()->id,
						'thamdinh_nguoi_nhan' => $list_id_nguoinhan,
						'thamdinh_nguoi_cc' => $list_id_cc
					]);
				}
			}
	    	// return $update;
	    	if ($update > 0) {
	    		$json['success'] = "Gửi danh sách thành công.";
	    	}
	    	else {
	    		$json['error'] = "Gửi danh sách bị lỗi.";
	    	}
	    }catch(\Exception $e){
	    	$json['error'] = "Gửi lỗi.".$e;
	    }
	    return $json;
    }

    public function deleteDSTonghop(Request $req){
    	try{
	    	$id = $req->input('id');

	    	$arrData = [];

	    	$arrData = explode("-", $id);

	    	$delete = 0;

			foreach ($arrData as $value) {
				if ($value > 0) {
					$delete = DB::table('qlhs_hosobaocao')->where('report_id', '=', $value)->delete();
				}
			}
	    	// return $update;
	    	if ($delete > 0) {
	    		$json['success'] = "Xóa danh sách thành công.";
	    	}
	    	else {
	    		$json['error'] = "Xóa danh sách bị lỗi.";
	    	}
	    }catch(\Exception $e){
	    	$json['error'] = "Xóa lỗi.".$e;
	    }
	    return $json;
    }
}
?>