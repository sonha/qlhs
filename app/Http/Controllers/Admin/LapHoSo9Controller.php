<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Exception;
use Excel,datetime;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Models\qlhs_message;

class LapHoSo9Controller extends Controller
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

//---------------------------------------------------------Chế độ học sinh-------------------------------------------------------------
    public function loadData(Request $request)
    {
        $json = [];
        $start = $request->input('start');
        $limit = $request->input('limit');

        $schools_id = $request->input('SCHOOLID');
        $year = $request->input('YEAR');
        $keySearch = $request->input('KEY');
        $status = $request->input('STATUS');

        $arrYear = [];
        $arrYear = explode("-", $year);
        // $schoolId = 37;//$request->input('SCHOOLID');
     //    $year = 2016;//$request->input('YEAR');
     //    $profileId = 834;

        // $user = Auth::user()->id;

        $datas = null;

        if ($arrYear[0] == "HK1") {
            $datas = DB::table('qlhs_tonghopchedo')
            ->join('qlhs_profile', 'profile_id', '=', DB::raw('qlhs_thcd_profile_id AND qlhs_thcd_school_id = '.$schools_id.' AND qlhs_thcd_nam = '.$arrYear[1].''))

            ->leftJoin('qlhs_schools', 'schools_id', '=', 'profile_school_id')
            ->leftJoin('qlhs_class', 'class_id', '=', 'profile_class_id')
            ->select('qlhs_thcd_id', 
                'qlhs_thcd_tien_nhucau_MGHP as MGHP', 
                'qlhs_thcd_tien_nhucau_CPHT as CPHT', 
                'qlhs_thcd_tien_nhucau_HTAT as HTAT', 
                'qlhs_thcd_tien_nhucau_HTBT_TA as HTBT_TA', 
                'qlhs_thcd_tien_nhucau_HTBT_TO as HTBT_TO', 
                'qlhs_thcd_tien_nhucau_HTBT_VHTT as HTBT_VHTT', 
                'qlhs_thcd_tien_nhucau_HSDTTS as HSDTTS', 
                'qlhs_thcd_tien_nhucau_HSKT_HB as HSKT_HB', 
                'qlhs_thcd_tien_nhucau_HSKT_DDHT as HSKT_DDHT', 
                'qlhs_thcd_tien_nhucau_HTATHS as HTATHS', 
                'qlhs_thcd_tien_nhucau_HBHSDTNT as HBHSDTNT', 
                'qlhs_thcd_tongtien_nhucau as TONGTIEN', 
                'qlhs_thcd_trangthai as TRANGTHAI', 
                'qlhs_thcd_trangthai_PD as TRANGTHAIPHEDUYET', 
                'profile_id', 
                'profile_name', 
                'profile_birthday', 
                'schools_name', 
                'class_name', 
                'qlhs_thcd_ghichu as GHICHU');
            // ->where('qlhs_thcd_school_id', '=', $schools_id)->where('qlhs_thcd_nam', '=', $arrYear[1]);

            if (!is_null($status) && !empty($status)) {
                if ($status == "CHO") {
                    $datas->where('qlhs_thcd_trangthai', '=', 0);
                }
                if ($status == "DA") {
                    $datas->where('qlhs_thcd_trangthai', '=', 1);
                }
            }
        }

        if ($arrYear[0] == "HK2") {
            $datas = DB::table('qlhs_tonghopchedo')
            ->join('qlhs_profile', 'profile_id', '=', DB::raw('qlhs_thcd_profile_id AND qlhs_thcd_school_id = '.$schools_id.' AND qlhs_thcd_nam = '.$arrYear[1].''))

            ->leftJoin('qlhs_schools', 'schools_id', '=', 'profile_school_id')
            ->leftJoin('qlhs_class', 'class_id', '=', 'profile_class_id')
            ->select('qlhs_thcd_id', 
                'qlhs_thcd_tien_nhucau_MGHP_HK2 as MGHP', 
                'qlhs_thcd_tien_nhucau_CPHT_HK2 as CPHT', 
                'qlhs_thcd_tien_nhucau_HTAT_HK2 as HTAT', 
                'qlhs_thcd_tien_nhucau_HTBT_TA_HK2 as HTBT_TA', 
                'qlhs_thcd_tien_nhucau_HTBT_TO_HK2 as HTBT_TO', 
                'qlhs_thcd_tien_nhucau_HTBT_VHTT_HK2 as HTBT_VHTT', 
                'qlhs_thcd_tien_nhucau_HSDTTS_HK2 as HSDTTS', 
                'qlhs_thcd_tien_nhucau_HSKT_HB_HK2 as HSKT_HB', 
                'qlhs_thcd_tien_nhucau_HSKT_DDHT_HK2 as HSKT_DDHT', 
                'qlhs_thcd_tien_nhucau_HTATHS_HK2 as HTATHS', 
                'qlhs_thcd_tien_nhucau_HBHSDTNT_HK2 as HBHSDTNT', 
                'qlhs_thcd_tongtien_nhucau_HK2 as TONGTIEN', 
                'qlhs_thcd_trangthai_HK2 as TRANGTHAI', 
                'qlhs_thcd_trangthai_PD_HK2 as TRANGTHAIPHEDUYET', 
                'profile_id', 
                'profile_name', 
                'profile_birthday', 
                'schools_name', 
                'class_name', 
                'qlhs_thcd_ghichu_HK2 as GHICHU');
            // ->where('qlhs_thcd_school_id', '=', $schools_id)->where('qlhs_thcd_nam', '=', $arrYear[1]);

            if (!is_null($status) && !empty($status)) {
                if ($status == "CHO") {
                    $datas->where('qlhs_thcd_trangthai_HK2', '=', 0);
                }
                if ($status == "DA") {
                    $datas->where('qlhs_thcd_trangthai_HK2', '=', 1);
                }
            }
        }

        if ($arrYear[0] == "CA") {
            $datas = DB::table('qlhs_tonghopchedo')
            ->join('qlhs_profile', 'profile_id', '=', DB::raw('qlhs_thcd_profile_id AND qlhs_thcd_school_id = '.$schools_id.' AND qlhs_thcd_nam = '.$arrYear[1].''))

            ->leftJoin('qlhs_schools', 'schools_id', '=', 'profile_school_id')
            ->leftJoin('qlhs_class', 'class_id', '=', 'profile_class_id')
            ->select('qlhs_thcd_id', 
                DB::raw('CASE 
                    when qlhs_thcd_tien_nhucau_MGHP is not null and qlhs_thcd_tien_nhucau_MGHP_HK2 is not null 
                    then (qlhs_thcd_tien_nhucau_MGHP + qlhs_thcd_tien_nhucau_MGHP_HK2) else 0 end MGHP'),
                DB::raw('CASE 
                    when qlhs_thcd_tien_nhucau_CPHT is not null and qlhs_thcd_tien_nhucau_CPHT_HK2 is not null 
                    then (qlhs_thcd_tien_nhucau_CPHT + qlhs_thcd_tien_nhucau_CPHT_HK2) else 0 end CPHT'),
                DB::raw('CASE 
                    when qlhs_thcd_tien_nhucau_HTAT is not null and qlhs_thcd_tien_nhucau_HTAT_HK2 is not null 
                    then (qlhs_thcd_tien_nhucau_HTAT + qlhs_thcd_tien_nhucau_HTAT_HK2) else 0 end HTAT'),
                DB::raw('CASE 
                    when qlhs_thcd_tien_nhucau_HTBT_TA is not null and qlhs_thcd_tien_nhucau_HTBT_TA_HK2 is not null 
                    then (qlhs_thcd_tien_nhucau_HTBT_TA + qlhs_thcd_tien_nhucau_HTBT_TA_HK2) else 0 end HTBT_TA'),
                DB::raw('CASE 
                    when qlhs_thcd_tien_nhucau_HTBT_TO is not null and qlhs_thcd_tien_nhucau_HTBT_TO_HK2 is not null 
                    then (qlhs_thcd_tien_nhucau_HTBT_TO + qlhs_thcd_tien_nhucau_HTBT_TO_HK2) else 0 end HTBT_TO'),
                DB::raw('CASE 
                    when qlhs_thcd_tien_nhucau_HTBT_VHTT is not null and qlhs_thcd_tien_nhucau_HTBT_VHTT_HK2 is not null 
                    then (qlhs_thcd_tien_nhucau_HTBT_VHTT + qlhs_thcd_tien_nhucau_HTBT_VHTT_HK2) else 0 end HTBT_VHTT'),
                DB::raw('CASE 
                    when qlhs_thcd_tien_nhucau_HSDTTS is not null and qlhs_thcd_tien_nhucau_HSDTTS_HK2 is not null 
                    then (qlhs_thcd_tien_nhucau_HSDTTS + qlhs_thcd_tien_nhucau_HSDTTS_HK2) else 0 end HSDTTS'),
                DB::raw('CASE 
                    when qlhs_thcd_tien_nhucau_HSKT_HB is not null and qlhs_thcd_tien_nhucau_HSKT_HB_HK2 is not null 
                    then (qlhs_thcd_tien_nhucau_HSKT_HB + qlhs_thcd_tien_nhucau_HSKT_HB_HK2) else 0 end HSKT_HB'),
                DB::raw('CASE 
                    when qlhs_thcd_tien_nhucau_HSKT_DDHT is not null and qlhs_thcd_tien_nhucau_HSKT_DDHT_HK2 is not null 
                    then (qlhs_thcd_tien_nhucau_HSKT_DDHT + qlhs_thcd_tien_nhucau_HSKT_DDHT_HK2) else 0 end HSKT_DDHT'),
                DB::raw('CASE 
                    when qlhs_thcd_tien_nhucau_HTATHS is not null and qlhs_thcd_tien_nhucau_HTATHS_HK2 is not null 
                    then (qlhs_thcd_tien_nhucau_HTATHS + qlhs_thcd_tien_nhucau_HTATHS_HK2) else 0 end HTATHS'),
                DB::raw('CASE 
                    when qlhs_thcd_tien_nhucau_HBHSDTNT is not null and qlhs_thcd_tien_nhucau_HBHSDTNT_HK2 is not null 
                    then (qlhs_thcd_tien_nhucau_HBHSDTNT + qlhs_thcd_tien_nhucau_HBHSDTNT_HK2) else 0 end HBHSDTNT'),
                DB::raw('CASE 
                    when qlhs_thcd_tongtien_nhucau is not null and qlhs_thcd_tongtien_nhucau_HK2 is not null 
                    then (qlhs_thcd_tongtien_nhucau + qlhs_thcd_tongtien_nhucau_HK2) else 0 end TONGTIEN'),
                DB::raw('CASE 
                    when (qlhs_thcd_trangthai is not null and qlhs_thcd_trangthai = 1) and (qlhs_thcd_trangthai_HK2 is not null and qlhs_thcd_trangthai_HK2 = 1)
                    then 1 else 0 end TRANGTHAI'),
                DB::raw('CASE 
                    when (qlhs_thcd_trangthai_PD is not null and qlhs_thcd_trangthai_PD = 1) and (qlhs_thcd_trangthai_PD_HK2 is not null and qlhs_thcd_trangthai_PD_HK2 = 1)
                    then 1 else 0 end TRANGTHAIPHEDUYET'),
                // '(qlhs_thcd_tien_nhucau_MGHP + qlhs_thcd_tien_nhucau_MGHP_HK2) as MGHP', 
                // '(qlhs_thcd_tien_nhucau_CPHT + qlhs_thcd_tien_nhucau_CPHT_HK2) as CPHT', 
                // '(qlhs_thcd_tien_nhucau_HTAT + qlhs_thcd_tien_nhucau_HTAT_HK2) as HTAT', 
                // '(qlhs_thcd_tien_nhucau_HTBT_TA + qlhs_thcd_tien_nhucau_HTBT_TA_HK2) as HTBT_TA', 
                // '(qlhs_thcd_tien_nhucau_HTBT_TO + qlhs_thcd_tien_nhucau_HTBT_TO_HK2) as HTBT_TO', 
                // '(qlhs_thcd_tien_nhucau_HTBT_VHTT + qlhs_thcd_tien_nhucau_HTBT_VHTT_HK2) as HTBT_VHTT', 
                // '(qlhs_thcd_tien_nhucau_HSDTTS + qlhs_thcd_tien_nhucau_HSDTTS_HK2) as HSDTTS', 
                // '(qlhs_thcd_tien_nhucau_HSKT_HB + qlhs_thcd_tien_nhucau_HSKT_HB_HK2) as HSKT_HB', 
                // '(qlhs_thcd_tien_nhucau_HSKT_DDHT + qlhs_thcd_tien_nhucau_HSKT_DDHT_HK2) as HSKT_DDHT', 
                // '(qlhs_thcd_tongtien_nhucau + qlhs_thcd_tongtien_nhucau_HK2) as TONGTIEN', 
                // 'qlhs_thcd_trangthai_HK2 as TRANGTHAI', 
                'profile_id', 
                'profile_name', 
                'profile_birthday', 
                'schools_name', 
                'class_name', 
                'qlhs_thcd_ghichu_CANAM as GHICHU');
            // ->where('qlhs_thcd_school_id', '=', $schools_id)->where('qlhs_thcd_nam', '=', $arrYear[1]);

            if (!is_null($status) && !empty($status)) {
                if ($status == "CHO") {
                    $datas->where('qlhs_thcd_trangthai', '=', 0)->where('qlhs_thcd_trangthai_HK2', '=', 0);
                }
                if ($status == "DA") {
                    $datas->where('qlhs_thcd_trangthai', '=', 1)->where('qlhs_thcd_trangthai_HK2', '=', 1);
                }
            }
        }

        $json['totalRows'] = 0;

        if (!is_null($datas)) {
            if (!is_null($keySearch) && !empty($keySearch)) {
                $datas->where('profile_name', 'LIKE', '%'.$keySearch.'%')
                    ->orWhere('class_name', 'LIKE', '%'.$keySearch.'%');
            }

            $json['totalRows'] = $datas->count();
        
            $json['startRecord'] = ($start);
            $json['numRows'] = $limit;
            
            $json['data'] = $datas->orderBy('qlhs_profile.updated_at','desc')->orderBy('qlhs_profile.profile_name','desc')->skip($start*$limit)->take($limit)->get();
        }
        
        
        return $json;
    }

    public function loadHocky(){
        $result = [];
        try {
            $result['NAMHOC'] = DB::table('qlhs_years')->orderBy('code','asc')->get();
            $result['HOCKY'] = DB::table('qlhs_hocky')->orderBy('qlhs_hocky_order','asc')->get();

            return $result;
        } catch (Exception $e) {
            
        }
    }

    public function approvedAll(Request $request){
        try {
            $result = [];

            $level = $request->input('LEVEL');
            $schools_id = $request->input('SCHOOLID');
            $year = $request->input('YEAR');

            $status = 0;

            if ($level == 1) {
                $status = DB::table('qlhs_tonghopchedo')
                    ->where('qlhs_thcd_school_id', $schools_id)->where('qlhs_thcd_nam', $year)
                    ->update([
                        'qlhs_thcd_trangthai' => 1,
                        'qlhs_thcd_trangthai_HK2' => 1,

                        'qlhs_thcd_trangthai_MGHP' => 1,
                        'qlhs_thcd_trangthai_CPHT' => 1,
                        'qlhs_thcd_trangthai_HTAT' => 1,
                        'qlhs_thcd_trangthai_HTBT_TA' => 1,
                        'qlhs_thcd_trangthai_HTBT_TO' => 1,
                        'qlhs_thcd_trangthai_HTBT_VHTT' => 1,
                        'qlhs_thcd_trangthai_HSKT_HB' => 1,
                        'qlhs_thcd_trangthai_HSKT_DDHT' => 1,
                        'qlhs_thcd_trangthai_HSDTTS' => 1,
                        'qlhs_thcd_trangthai_HTATHS' => 1,
                        'qlhs_thcd_trangthai_HBHSDTNT' => 1,

                        'qlhs_thcd_trangthai_MGHP_HK2' => 1,
                        'qlhs_thcd_trangthai_CPHT_HK2' => 1,
                        'qlhs_thcd_trangthai_HTAT_HK2' => 1,
                        'qlhs_thcd_trangthai_HTBT_TA_HK2' => 1,
                        'qlhs_thcd_trangthai_HTBT_TO_HK2' => 1,
                        'qlhs_thcd_trangthai_HTBT_VHTT_HK2' => 1,
                        'qlhs_thcd_trangthai_HSKT_HB_HK2' => 1,
                        'qlhs_thcd_trangthai_HSKT_DDHT_HK2' => 1,
                        'qlhs_thcd_trangthai_HSDTTS_HK2' => 1,
                        'qlhs_thcd_trangthai_HTATHS_HK2' => 1,
                        'qlhs_thcd_trangthai_HBHSDTNT_HK2' => 1]);

                if ($status == 0) {
                    $result['error'] = 'Toàn bộ học sinh đã được chọn';
                }
                else {
                    $result['success'] = 'Chọn toàn bộ học sinh thành công';
                }
            }
            if ($level == 2) {
                $status = DB::table('qlhs_tonghopchedo')
                    ->where('qlhs_thcd_school_id', $schools_id)->where('qlhs_thcd_nam', $year)
                    ->update([
                        'qlhs_thcd_trangthai_PD' => 1,
                        'qlhs_thcd_trangthai_PD_HK2' => 1,

                        'PheDuyet_trangthai_MGHP' => 1,
                        'PheDuyet_trangthai_CPHT' => 1,
                        'PheDuyet_trangthai_HTAT' => 1,
                        'PheDuyet_trangthai_HTBT_TA' => 1,
                        'PheDuyet_trangthai_HTBT_TO' => 1,
                        'PheDuyet_trangthai_HTBT_VHTT' => 1,
                        'PheDuyet_trangthai_HSKT_HB' => 1,
                        'PheDuyet_trangthai_HSKT_DDHT' => 1,
                        'PheDuyet_trangthai_HSDTTS' => 1,
                        'PheDuyet_trangthai_HTATHS' => 1,
                        'PheDuyet_trangthai_HBHSDTNT' => 1,

                        'PheDuyet_trangthai_MGHP_HK2' => 1,
                        'PheDuyet_trangthai_CPHT_HK2' => 1,
                        'PheDuyet_trangthai_HTAT_HK2' => 1,
                        'PheDuyet_trangthai_HTBT_TA_HK2' => 1,
                        'PheDuyet_trangthai_HTBT_TO_HK2' => 1,
                        'PheDuyet_trangthai_HTBT_VHTT_HK2' => 1,
                        'PheDuyet_trangthai_HSKT_HB_HK2' => 1,
                        'PheDuyet_trangthai_HSKT_DDHT_HK2' => 1,
                        'PheDuyet_trangthai_HSDTTS_HK2' => 1,
                        'PheDuyet_trangthai_HTATHS_HK2' => 1,
                        'PheDuyet_trangthai_HBHSDTNT_HK2' => 1]);

                if ($status == 0) {
                    $result['error'] = 'Toàn bộ học sinh đã được phê duyệt';
                }
                else {
                    $result['success'] = 'Phê duyệt toàn bộ học sinh thành công';
                }
            }
            if ($level == 3) {
                $status = DB::table('qlhs_tonghopchedo')
                    ->where('qlhs_thcd_school_id', $schools_id)->where('qlhs_thcd_nam', $year)
                    ->update([
                        'qlhs_thcd_trangthai_TD' => 1,
                        'qlhs_thcd_trangthai_TD_HK2' => 1,

                        'ThamDinh_trangthai_MGHP' => 1,
                        'ThamDinh_trangthai_CPHT' => 1,
                        'ThamDinh_trangthai_HTAT' => 1,
                        'ThamDinh_trangthai_HTBT_TA' => 1,
                        'ThamDinh_trangthai_HTBT_TO' => 1,
                        'ThamDinh_trangthai_HTBT_VHTT' => 1,
                        'ThamDinh_trangthai_HSKT_HB' => 1,
                        'ThamDinh_trangthai_HSKT_DDHT' => 1,
                        'ThamDinh_trangthai_HSDTTS' => 1,
                        'ThamDinh_trangthai_HTATHS' => 1,
                        'ThamDinh_trangthai_HBHSDTNT' => 1,

                        'ThamDinh_trangthai_MGHP_HK2' => 1,
                        'ThamDinh_trangthai_CPHT_HK2' => 1,
                        'ThamDinh_trangthai_HTAT_HK2' => 1,
                        'ThamDinh_trangthai_HTBT_TA_HK2' => 1,
                        'ThamDinh_trangthai_HTBT_TO_HK2' => 1,
                        'ThamDinh_trangthai_HTBT_VHTT_HK2' => 1,
                        'ThamDinh_trangthai_HSKT_HB_HK2' => 1,
                        'ThamDinh_trangthai_HSKT_DDHT_HK2' => 1,
                        'ThamDinh_trangthai_HSDTTS_HK2' => 1,
                        'ThamDinh_trangthai_HTATHS_HK2' => 1,
                        'ThamDinh_trangthai_HBHSDTNT_HK2' => 1]);

                if ($status == 0) {
                    $result['error'] = 'Toàn bộ học sinh đã được thẩm định';
                }
                else {
                    $result['success'] = 'Thẩm định toàn bộ học sinh thành công';
                }
            }

            return $result;
        } catch (Exception $e) {
            return $result['error'] = $e;
        }
    }

    public function unApprovedAll(Request $request){
        try {
            $result = [];

            $level = $request->input('LEVEL');
            $schools_id = $request->input('SCHOOLID');
            $year = $request->input('YEAR');

            $status = 0;

            if ($level == 1) {
                $status = DB::table('qlhs_tonghopchedo')
                    ->where('qlhs_thcd_school_id', $schools_id)->where('qlhs_thcd_nam', $year)->where('qlhs_thcd_trangthai_PD', 0)->where('qlhs_thcd_trangthai_PD_HK2', 0)
                    ->update([
                        'qlhs_thcd_trangthai' => 0,
                        'qlhs_thcd_trangthai_HK2' => 0,

                        'qlhs_thcd_trangthai_MGHP' => 0,
                        'qlhs_thcd_trangthai_CPHT' => 0,
                        'qlhs_thcd_trangthai_HTAT' => 0,
                        'qlhs_thcd_trangthai_HTBT_TA' => 0,
                        'qlhs_thcd_trangthai_HTBT_TO' => 0,
                        'qlhs_thcd_trangthai_HTBT_VHTT' => 0,
                        'qlhs_thcd_trangthai_HSKT_HB' => 0,
                        'qlhs_thcd_trangthai_HSKT_DDHT' => 0,
                        'qlhs_thcd_trangthai_HSDTTS' => 0,
                        'qlhs_thcd_trangthai_HTATHS' => 0,
                        'qlhs_thcd_trangthai_HBHSDTNT' => 0,

                        'qlhs_thcd_trangthai_MGHP_HK2' => 0,
                        'qlhs_thcd_trangthai_CPHT_HK2' => 0,
                        'qlhs_thcd_trangthai_HTAT_HK2' => 0,
                        'qlhs_thcd_trangthai_HTBT_TA_HK2' => 0,
                        'qlhs_thcd_trangthai_HTBT_TO_HK2' => 0,
                        'qlhs_thcd_trangthai_HTBT_VHTT_HK2' => 0,
                        'qlhs_thcd_trangthai_HSKT_HB_HK2' => 0,
                        'qlhs_thcd_trangthai_HSKT_DDHT_HK2' => 0,
                        'qlhs_thcd_trangthai_HSDTTS_HK2' => 0,
                        'qlhs_thcd_trangthai_HTATHS_HK2' => 0,
                        'qlhs_thcd_trangthai_HBHSDTNT_HK2' => 0]);

                if ($status == 0) {
                    $result['error'] = 'Toàn bộ học sinh đã được hủy chọn';
                }
                else {
                    $result['success'] = 'Hủy chọn toàn bộ học sinh thành công';
                }
            }
            if ($level == 2) {
                $status = DB::table('qlhs_tonghopchedo')
                    ->where('qlhs_thcd_school_id', $schools_id)->where('qlhs_thcd_nam', $year)->where('qlhs_thcd_trangthai_TD', 0)->where('qlhs_thcd_trangthai_TD_HK2', 0)
                    ->update([
                        'qlhs_thcd_trangthai_PD' => 0,
                        'qlhs_thcd_trangthai_PD_HK2' => 0,

                        'PheDuyet_trangthai_MGHP' => 0,
                        'PheDuyet_trangthai_CPHT' => 0,
                        'PheDuyet_trangthai_HTAT' => 0,
                        'PheDuyet_trangthai_HTBT_TA' => 0,
                        'PheDuyet_trangthai_HTBT_TO' => 0,
                        'PheDuyet_trangthai_HTBT_VHTT' => 0,
                        'PheDuyet_trangthai_HSKT_HB' => 0,
                        'PheDuyet_trangthai_HSKT_DDHT' => 0,
                        'PheDuyet_trangthai_HSDTTS' => 0,
                        'PheDuyet_trangthai_HTATHS' => 0,
                        'PheDuyet_trangthai_HBHSDTNT' => 0,

                        'PheDuyet_trangthai_MGHP_HK2' => 0,
                        'PheDuyet_trangthai_CPHT_HK2' => 0,
                        'PheDuyet_trangthai_HTAT_HK2' => 0,
                        'PheDuyet_trangthai_HTBT_TA_HK2' => 0,
                        'PheDuyet_trangthai_HTBT_TO_HK2' => 0,
                        'PheDuyet_trangthai_HTBT_VHTT_HK2' => 0,
                        'PheDuyet_trangthai_HSKT_HB_HK2' => 0,
                        'PheDuyet_trangthai_HSKT_DDHT_HK2' => 0,
                        'PheDuyet_trangthai_HSDTTS_HK2' => 0,
                        'PheDuyet_trangthai_HTATHS_HK2' => 0,
                        'PheDuyet_trangthai_HBHSDTNT_HK2' => 0]);

                if ($status == 0) {
                    $result['error'] = 'Toàn bộ học sinh đã được hủy phê duyệt';
                }
                else {
                    $result['success'] = 'Hủy phê duyệt toàn bộ học sinh thành công';
                }
            }
            if ($level == 3) {
                $status = DB::table('qlhs_tonghopchedo')
                    ->where('qlhs_thcd_school_id', $schools_id)->where('qlhs_thcd_nam', $year)
                    ->update([
                        'qlhs_thcd_trangthai_TD' => 0,
                        'qlhs_thcd_trangthai_TD_HK2' => 0,

                        'ThamDinh_trangthai_MGHP' => 0,
                        'ThamDinh_trangthai_CPHT' => 0,
                        'ThamDinh_trangthai_HTAT' => 0,
                        'ThamDinh_trangthai_HTBT_TA' => 0,
                        'ThamDinh_trangthai_HTBT_TO' => 0,
                        'ThamDinh_trangthai_HTBT_VHTT' => 0,
                        'ThamDinh_trangthai_HSKT_HB' => 0,
                        'ThamDinh_trangthai_HSKT_DDHT' => 0,
                        'ThamDinh_trangthai_HSDTTS' => 0,
                        'ThamDinh_trangthai_HTATHS' => 0,
                        'ThamDinh_trangthai_HBHSDTNT' => 0,

                        'ThamDinh_trangthai_MGHP_HK2' => 0,
                        'ThamDinh_trangthai_CPHT_HK2' => 0,
                        'ThamDinh_trangthai_HTAT_HK2' => 0,
                        'ThamDinh_trangthai_HTBT_TA_HK2' => 0,
                        'ThamDinh_trangthai_HTBT_TO_HK2' => 0,
                        'ThamDinh_trangthai_HTBT_VHTT_HK2' => 0,
                        'ThamDinh_trangthai_HSKT_HB_HK2' => 0,
                        'ThamDinh_trangthai_HSKT_DDHT_HK2' => 0,
                        'ThamDinh_trangthai_HSDTTS_HK2' => 0,
                        'ThamDinh_trangthai_HTATHS_HK2' => 0,
                        'ThamDinh_trangthai_HBHSDTNT_HK2' => 0]);

                if ($status == 0) {
                    $result['error'] = 'Toàn bộ học sinh đã được hủy thẩm định';
                }
                else {
                    $result['success'] = 'Hủy thẩm định toàn bộ học sinh thành công';
                }
            }

            return $result;
        } catch (Exception $e) {
            return $result['error'] = $e;
        }
    }

    public function approvedTHCD($strData){
        $result = [];
        try {

            $arrData = [];
            $update = 0;

            $statusMGHP = 0;
            $statusCPHT = 0;
            $statusHTAT = 0;
            $statusHTBT_TA = 0;
            $statusHTBT_TO = 0;
            $statusHTBT_VHTT = 0;
            $statusHSKT_HB = 0;
            $statusHSKT_DDHT = 0;
            $statusHSDTTS = 0;
            $statusHTATHS = 0;
            $statusHBHSDTNT = 0;

            $trangthai_1 = 0;
            $trangthai_2 = 0;

            $arrData = explode("-", $strData);


            $id = substr($arrData[0], 2);

            $note = $arrData[4];

            // return $arrData;

            if ($arrData[1] == "HK1") {
                
                foreach ($arrData as $value) {
                    // return $value;
                    //MGHP
                    if ($value == 89 || $value == 90 || $value == 91) {

                        $statusMGHP = 1;
                    }

                    //CPHT
                    if ($value == 92) {

                        $statusCPHT = 1;
                    }

                    //HTAT
                    if ($value == 93) {

                        $statusHTAT = 1;
                    }

                    //HTBT
                    if ($value == 94) {

                        $statusHTBT_TA = 1;
                    }

                    if ($value == 98) {

                        $statusHTBT_TO = 1;
                    }

                    if ($value == 115) {

                        $statusHTBT_VHTT = 1;
                    }

                    //HSKT
                    if ($value == 95) {

                        $statusHSKT_HB = 1;
                    }

                    if ($value == 100) {

                        $statusHSKT_DDHT = 1;
                    }

                    //HSDTTS
                    if ($value == 99) {

                        $statusHSDTTS = 1;
                    }

                    //HTATHS
                    if ($value == 118) {

                        $statusHTATHS = 1;
                    }

                    //HBHSDTNT
                    if ($value == 119) {

                        $statusHBHSDTNT = 1;
                    }
                }

                if ($statusMGHP == 0 && $statusCPHT == 0 && $statusHTAT == 0 && $statusHTBT_TA == 0 &&  $statusHTBT_TO == 0 && $statusHTBT_VHTT == 0 && $statusHSKT_HB == 0 && $statusHSKT_DDHT == 0 && $statusHSDTTS == 0 && $statusHTATHS == 0 && $statusHBHSDTNT == 0) {
                    
                    $result['success'] = 'Hủy chọn học kỳ 1 thành công';
                }
                else {
                    $trangthai_1 = 1;
                    $result['success'] = 'Chọn học kỳ 1 thành công';
                }

                $update = DB::update("update qlhs_tonghopchedo set
                    qlhs_thcd_trangthai = $trangthai_1, 
                    qlhs_thcd_trangthai_MGHP = $statusMGHP, 
                    qlhs_thcd_trangthai_CPHT = $statusCPHT,
                    qlhs_thcd_trangthai_HTAT = $statusHTAT,
                    qlhs_thcd_trangthai_HTBT_TA = $statusHTBT_TA,
                    qlhs_thcd_trangthai_HTBT_TO = $statusHTBT_TO,
                    qlhs_thcd_trangthai_HTBT_VHTT = $statusHTBT_VHTT,
                    qlhs_thcd_trangthai_HSKT_HB = $statusHSKT_HB,
                    qlhs_thcd_trangthai_HSKT_DDHT = $statusHSKT_DDHT,
                    qlhs_thcd_trangthai_HSDTTS = $statusHSDTTS,
                    qlhs_thcd_trangthai_HTATHS = $statusHTATHS,
                    qlhs_thcd_trangthai_HBHSDTNT = $statusHBHSDTNT,
                    qlhs_thcd_ghichu = '$note'
                    where qlhs_thcd_id = $id");
            }

            if ($arrData[1] == "HK2") {
                foreach ($arrData as $value) {
                    //MGHP
                    if ($value == 89 || $value == 90 || $value == 91) {

                        $statusMGHP = 1;
                    }

                    //CPHT
                    if ($value == 92) {

                        $statusCPHT = 1;
                    }

                    //HTAT
                    if ($value == 93) {

                        $statusHTAT = 1;
                    }

                    //HTBT
                    if ($value == 94) {

                        $statusHTBT_TA = 1;
                    }

                    if ($value == 98) {

                        $statusHTBT_TO = 1;
                    }

                    if ($value == 115) {

                        $statusHTBT_VHTT = 1;
                    }

                    //HSKT
                    if ($value == 95) {

                        $statusHSKT_HB = 1;
                    }

                    if ($value == 100) {

                        $statusHSKT_DDHT = 1;
                    }

                    //HSDTTS
                    if ($value == 99) {

                        $statusHSDTTS = 1;
                    }

                    //HTATHS
                    if ($value == 118) {

                        $statusHTATHS = 1;
                    }

                    //HBHSDTNT
                    if ($value == 119) {

                        $statusHBHSDTNT = 1;
                    }
                } 

                if ($statusMGHP == 0 && $statusCPHT == 0 && $statusHTAT == 0 && $statusHTBT_TA == 0 &&  $statusHTBT_TO == 0 &&  $statusHTBT_VHTT == 0 && $statusHSKT_HB == 0 && $statusHSKT_DDHT == 0 && $statusHSDTTS == 0 && $statusHTATHS == 0 && $statusHBHSDTNT == 0) {
                    
                    $result['success'] = 'Hủy chọn học kỳ 2 thành công';
                }
                else {
                    $trangthai_2 = 1;
                    $result['success'] = 'Chọn học kỳ 2 thành công';
                }

                // return $statusHTATHS;

                $update = DB::update("update qlhs_tonghopchedo set
                    qlhs_thcd_trangthai_HK2 = $trangthai_2, 
                    qlhs_thcd_trangthai_MGHP_HK2 = $statusMGHP, 
                    qlhs_thcd_trangthai_CPHT_HK2 = $statusCPHT,
                    qlhs_thcd_trangthai_HTAT_HK2 = $statusHTAT,
                    qlhs_thcd_trangthai_HTBT_TA_HK2 = $statusHTBT_TA,
                    qlhs_thcd_trangthai_HTBT_TO_HK2 = $statusHTBT_TO,
                    qlhs_thcd_trangthai_HTBT_VHTT_HK2 = $statusHTBT_VHTT,
                    qlhs_thcd_trangthai_HSKT_HB_HK2 = $statusHSKT_HB,
                    qlhs_thcd_trangthai_HSKT_DDHT_HK2 = $statusHSKT_DDHT,
                    qlhs_thcd_trangthai_HSDTTS_HK2 = $statusHSDTTS,
                    qlhs_thcd_trangthai_HTATHS_HK2 = $statusHTATHS,
                    qlhs_thcd_trangthai_HBHSDTNT_HK2 = $statusHBHSDTNT,
                    qlhs_thcd_ghichu_HK2 = '$note'
                    where qlhs_thcd_id = $id");
            }

            if ($arrData[1] == "CA") {
                foreach ($arrData as $value) {
                    //MGHP
                    if ($value == 89 || $value == 90 || $value == 91) {

                        $statusMGHP = 1;
                    }

                    //CPHT
                    if ($value == 92) {

                        $statusCPHT = 1;
                    }

                    //HTAT
                    if ($value == 93) {

                        $statusHTAT = 1;
                    }

                    //HTBT
                    if ($value == 94) {

                        $statusHTBT_TA = 1;
                    }

                    if ($value == 98) {

                        $statusHTBT_TO = 1;
                    }

                    if ($value == 115) {

                        $statusHTBT_VHTT = 1;
                    }

                    //HSKT
                    if ($value == 95) {

                        $statusHSKT_HB = 1;
                    }

                    if ($value == 100) {

                        $statusHSKT_DDHT = 1;
                    }

                    //HSDTTS
                    if ($value == 99) {

                        $statusHSDTTS = 1;
                    }

                    //HTATHS
                    if ($value == 118) {

                        $statusHTATHS = 1;
                    }

                    //HBHSDTNT
                    if ($value == 119) {

                        $statusHBHSDTNT = 1;
                    }
                } 

                if ($statusMGHP == 0 && $statusCPHT == 0 && $statusHTAT == 0 && $statusHTBT_TA == 0 &&  $statusHTBT_TO == 0 &&  $statusHTBT_VHTT == 0 && $statusHSKT_HB == 0 && $statusHSKT_DDHT == 0 && $statusHSDTTS == 0 && $statusHTATHS == 0 && $statusHBHSDTNT == 0) {
                    
                    $result['success'] = 'Hủy chọn cả năm thành công';
                }
                else {
                    $trangthai_1 = 1;
                    $trangthai_2 = 1;
                    $result['success'] = 'Chọn cả năm thành công';
                }

                $update = DB::update("update qlhs_tonghopchedo set
                    qlhs_thcd_trangthai = $trangthai_1, 
                    qlhs_thcd_trangthai_HK2 = $trangthai_2, 
                    qlhs_thcd_trangthai_MGHP = $statusMGHP, 
                    qlhs_thcd_trangthai_CPHT = $statusCPHT,
                    qlhs_thcd_trangthai_HTAT = $statusHTAT,
                    qlhs_thcd_trangthai_HTBT_TA = $statusHTBT_TA,
                    qlhs_thcd_trangthai_HTBT_TO = $statusHTBT_TO,
                    qlhs_thcd_trangthai_HTBT_VHTT = $statusHTBT_VHTT,
                    qlhs_thcd_trangthai_HSKT_HB = $statusHSKT_HB,
                    qlhs_thcd_trangthai_HSKT_DDHT = $statusHSKT_DDHT,
                    qlhs_thcd_trangthai_HSDTTS = $statusHSDTTS, 
                    qlhs_thcd_trangthai_HTATHS = $statusHTATHS,
                    qlhs_thcd_trangthai_HBHSDTNT = $statusHBHSDTNT,
                    
                    qlhs_thcd_trangthai_MGHP_HK2 = $statusMGHP, 
                    qlhs_thcd_trangthai_CPHT_HK2 = $statusCPHT,
                    qlhs_thcd_trangthai_HTAT_HK2 = $statusHTAT,
                    qlhs_thcd_trangthai_HTBT_TA_HK2 = $statusHTBT_TA,
                    qlhs_thcd_trangthai_HTBT_TO_HK2 = $statusHTBT_TO,
                    qlhs_thcd_trangthai_HTBT_VHTT_HK2 = $statusHTBT_VHTT,
                    qlhs_thcd_trangthai_HSKT_HB_HK2 = $statusHSKT_HB,
                    qlhs_thcd_trangthai_HSKT_DDHT_HK2 = $statusHSKT_DDHT,
                    qlhs_thcd_trangthai_HSDTTS_HK2 = $statusHSDTTS,
                    qlhs_thcd_trangthai_HTATHS_HK2 = $statusHTATHS,
                    qlhs_thcd_trangthai_HBHSDTNT_HK2 = $statusHBHSDTNT,
                    qlhs_thcd_ghichu_CANAM = '$note'
                    where qlhs_thcd_id = $id");
            }

            return $result;
        } catch (Exception $e) {
            return $result['error'] = $e;
        }
    }

    public function revertApprovedTHCD($strData){
        $result = [];
        try {

            $arrData = [];
            $update = 0;

            $arrData = explode("-", $strData);

            $id = $arrData[0];

            if ($arrData[1] == "HK1") {
                $update = DB::update("update qlhs_tonghopchedo set
                    qlhs_thcd_trangthai = 0 
                    where qlhs_thcd_id = $id");
            }

            if ($arrData[1] == "HK2") {
                $update = DB::update("update qlhs_tonghopchedo set
                    qlhs_thcd_trangthai_HK2 = 0 
                    where qlhs_thcd_id = $id");
            }

            if ($arrData[1] == "CA") {
                $update = DB::update("update qlhs_tonghopchedo set
                    qlhs_thcd_trangthai = 0, qlhs_thcd_trangthai_HK2 = 0 
                    where qlhs_thcd_id = $id");
            }

            if ($update > 0) {
                $result['success'] = 'Hủy chọn thành công';
            }
            else {
                $result['error'] = 'Hủy chọn thất bại';
            }

            return $result;
        } catch (Exception $e) {
            
        }
    }

    public function getProfileSubjectById($id){
        try {
            $dataResult = [];

            $listData = [];

            $lstData = [];

            $result = [];

            // return $id;
            $arrData = [];

            $arrData = explode("-", $id);

            // return count($arrData);

            if ($arrData[3] > 0) {
                $dataResult['GROUP'] = DB::table('qlhs_group')
                    ->leftJoin('qlhs_subject_history', 'subject_history_group_id', '=', 'group_id')
                    ->leftJoin('qlhs_profile_subject', 'profile_subject_subject_id', '=', 'subject_history_subject_id')
                    ->where('profile_subject_profile_id', '=', $arrData[0])
                    ->where('qlhs_profile_subject.start_year', '<=', DB::raw($arrData[3].' AND qlhs_profile_subject.active = 1 AND (qlhs_profile_subject.end_year > '.$arrData[3].' OR qlhs_profile_subject.end_year is null)'))
                    ->select('group_id', 'group_name')->get();

                $listData = DB::table('qlhs_subject')
                    ->leftJoin('qlhs_profile_subject', 'profile_subject_subject_id', '=', 'subject_id')
                    ->leftJoin('qlhs_subject_history', 'subject_history_subject_id', '=', 'subject_id')
                    ->leftJoin('qlhs_group', 'group_id', '=', 'subject_history_group_id')
                    ->where('profile_subject_profile_id', '=', $arrData[0])
                    ->where('qlhs_profile_subject.start_year', '<=', DB::raw($arrData[3].' AND qlhs_profile_subject.active = 1 AND (qlhs_profile_subject.end_year > '.$arrData[3].' OR qlhs_profile_subject.end_year is null)'))
                    ->select('subject_history_group_id', 
                        'group_name', 
                        'subject_id', 
                        'subject_name')
                    ->get();
            }
            else {
                $dataResult['GROUP'] = DB::table('qlhs_group')
                    ->leftJoin('qlhs_subject_history', 'subject_history_group_id', '=', 'group_id')
                    ->leftJoin('qlhs_profile_subject', 'profile_subject_subject_id', '=', 'subject_history_subject_id')
                    ->where('profile_subject_profile_id', '=', $arrData[0])
                    ->select('group_id', 'group_name')->get();

                $listData = DB::table('qlhs_subject')
                    ->leftJoin('qlhs_profile_subject', 'profile_subject_subject_id', '=', 'subject_id')
                    ->leftJoin('qlhs_subject_history', 'subject_history_subject_id', '=', 'subject_id')
                    ->leftJoin('qlhs_group', 'group_id', '=', 'subject_history_group_id')
                    ->where('profile_subject_profile_id', '=', $arrData[0])
                    ->select('subject_history_group_id', 
                        'group_name', 
                        'subject_id', 
                        'subject_name')
                    ->get();
            }
            
                // ->select('subject_history_group_id', 
                //     DB::raw('GROUP_CONCAT(group_name) as group_name'), 
                //     'subject_id', 
                //     'subject_name')
                // ->groupBy('subject_history_group_id', 'subject_id', 'subject_name')
                // ->get();

            //Miễn 100%
            $tantat = 0;
            $hocanngheo = 0;
            //Giảm 70%
            $vungkhokhan = 0;
            $dantocthieuso = 0;
            //HTBT
            $hsbantru = 0;
            $ongoaitruong = 0;
            // $dataResult['SUBJECT']

            // return $listData[0];
            foreach ($listData as $value) {
                if ($value->subject_id == 74) {
                    $tantat = 1;
                }
                if ($value->subject_id == 41) {
                    $hocanngheo = 1;
                }
                if ($value->subject_id == 34) {
                    $vungkhokhan = 1;
                }
                if ($value->subject_id == 49) {
                    $dantocthieuso = 1;
                }
                if ($value->subject_id == 46) {
                    $hsbantru = 1;
                }
                if ($value->subject_id == 72) {
                    $ongoaitruong = 1;
                }
                array_push($lstData, $value);
            }
            
            if ($tantat > 0 && $hocanngheo > 0) {
                $getSubMien100 = DB::table('qlhs_subject')
                    ->whereIn('subject_id', [41, 74])
                    ->select(
                        'subject_name')
                    ->get();

                $getGroupMien100 = DB::table('qlhs_group')
                    ->where('group_id', '=', 89)
                    ->select(
                        'group_id', 
                        'group_name')
                    ->get();

                $getGroupHTATMG = DB::table('qlhs_group')
                    ->where('group_id', '=', 93)
                    ->select(
                        'group_id', 
                        'group_name')
                    ->get();
                

                $data = [];
                $dataHTATMG = [];
                $subName = '';
                $subNameHTATMG = '';

                foreach ($lstData as $value) {
                    if ($value->subject_history_group_id == 89 || $value->subject_history_group_id == 90 || $value->subject_history_group_id == 91) {
                        $data['subject_history_group_id'] = $value->subject_history_group_id;
                        $data['group_name'] = "Cấp bù học phí";
                        $data['subject_name'] = $getSubMien100[0]->{'subject_name'} . ' + ' . $getSubMien100[1]->{'subject_name'} . '. ';
                        if ($value->subject_name != $subName) {
                            $data['subject_name'] .= $value->subject_name . '. ';
                        }
                        $subName = $value->subject_name;
                    }
                    else if ($value->subject_history_group_id != 93) {
                        $dt['subject_history_group_id'] = $value->subject_history_group_id;
                        $dt['group_name'] = $value->group_name;
                        $dt['subject_name'] = $value->subject_name;

                        array_push($result, $dt);
                    }
                    else if ($value->subject_history_group_id == 93) {
                        $dataHTATMG['subject_history_group_id'] = $value->subject_history_group_id;
                        $dataHTATMG['group_name'] = $value->group_name;
                        $dataHTATMG['subject_name'] = $getSubMien100[0]->{'subject_name'} . ' + ' . $getSubMien100[1]->{'subject_name'} . '. ';

                        if ($value->subject_name != $subNameHTATMG) {
                            $dataHTATMG['subject_name'] .= $value->subject_name . '. ';
                        }
                        $subNameHTATMG = $value->subject_name;
                    }
                }

                if (!is_null($data) && !empty($data) && count($data) > 0) {
                    
                    array_push($result, $data);
                }
                else {
                    $data['subject_history_group_id'] = 89;
                    $data['group_name'] = "Cấp bù học phí";
                    $data['subject_name'] = $getSubMien100[0]->{'subject_name'} . ' + ' . $getSubMien100[1]->{'subject_name'} . '. ';
                    array_push($result, $data);
                }

                if (!is_null($dataHTATMG) && !empty($dataHTATMG) && count($dataHTATMG) > 0) {
                    
                    array_push($result, $dataHTATMG);
                }
                else {
                    //Ăn trưa mẫu giáo
                    $dt['subject_history_group_id'] = $getGroupHTATMG[0]->{'group_id'};
                    $dt['group_name'] = $getGroupHTATMG[0]->{'group_name'};
                    $dt['subject_name'] = $getSubMien100[0]->{'subject_name'} . ' + ' . $getSubMien100[1]->{'subject_name'} . '. ';

                    array_push($result, $dt);
                }
            }

            if ($vungkhokhan > 0 && $dantocthieuso > 0) {
                $getSubGiam70 = DB::table('qlhs_subject')
                    ->whereIn('subject_id', [34, 49])
                    ->select(
                        'subject_name')
                    ->get();

                $getGroupGiam70 = DB::table('qlhs_group')
                    ->where('group_id', '=', 90)
                    ->select(
                        'group_id', 
                        'group_name')
                    ->get();

                $data = [];

                foreach ($lstData as $value) {
                    if ($value->subject_history_group_id == 89 || $value->subject_history_group_id == 90 || $value->subject_history_group_id == 91) {
                        $data['subject_history_group_id'] = $value->subject_history_group_id;
                        $data['group_name'] = "Cấp bù học phí";
                        $data['subject_name'] = $value->subject_name . '. ' . $getSubGiam70[0]->{'subject_name'} . ' + ' . $getSubGiam70[1]->{'subject_name'} . '. ';
                    }
                    else {
                        $dt['subject_history_group_id'] = $value->subject_history_group_id;
                        $dt['group_name'] = $value->group_name;
                        $dt['subject_name'] = $value->subject_name;

                        array_push($result, $dt);
                    }
                }
                
                if (!is_null($data) && !empty($data) && count($data) > 0) {
                    array_push($result, $data);
                }
                else {
                    $data['subject_history_group_id'] = 90;
                    $data['group_name'] = "Cấp bù học phí";
                    $data['subject_name'] = $getSubGiam70[0]->{'subject_name'} . ' + ' . $getSubGiam70[1]->{'subject_name'} . '. ';
                    array_push($result, $data);
                }
            }

            if ($vungkhokhan > 0) {
                $getSubBanTru = DB::table('qlhs_subject')
                    ->where('subject_id', '=', 34)
                    ->select(
                        'subject_name')
                    ->get();

                $getGroupBanTru = DB::table('qlhs_group')
                    ->whereIn('group_id', [94, 98])
                    ->select(
                        'group_id', 
                        'group_name')
                    ->get();

                foreach ($getGroupBanTru as $value) {
                    
                    $data['subject_history_group_id'] = $value->group_id;
                    $data['group_name'] = $value->group_name;
                    $data['subject_name'] = $getSubBanTru[0]->{'subject_name'};

                    array_push($lstData, $data);
                }

                // $dataResult['SUBJECT'] = $lstData;
            }

            // if ($hsbantru > 0 && $ongoaitruong > 0) {
            //     $getSubBanTru = DB::table('qlhs_subject')
            //         ->whereIn('subject_id', [46, 72])
            //         ->select(
            //             'subject_name')
            //         ->get();

            //     $getGroupBanTru = DB::table('qlhs_group')
            //         ->whereIn('group_id', [94, 98, 115])
            //         ->select(
            //             'group_id', 
            //             'group_name')
            //         ->get();

            //     foreach ($getGroupBanTru as $value) {
                    
            //         $data['subject_history_group_id'] = $value->group_id;
            //         $data['group_name'] = $value->group_name;
            //         $data['subject_name'] = $getSubBanTru[0]->{'subject_name'} . ', ' . $getSubBanTru[1]->{'subject_name'};

            //         array_push($lstData, $data);
            //     }

            //     // $dataResult['SUBJECT'] = $lstData;
            // }

            // if ($hsbantru > 0 && $ongoaitruong == 0) {
            //     $getSubBanTru = DB::table('qlhs_subject')
            //         ->where('subject_id', '=', 46)
            //         ->select(
            //             'subject_name')
            //         ->get();

            //     $getGroupBanTru = DB::table('qlhs_group')
            //         ->whereIn('group_id', [98, 115])
            //         ->select(
            //             'group_id', 
            //             'group_name')
            //         ->get();

            //     foreach ($getGroupBanTru as $value) {
                    
            //         $data['subject_history_group_id'] = $value->group_id;
            //         $data['group_name'] = $value->group_name;
            //         $data['subject_name'] = $getSubBanTru[0]->{'subject_name'} . ', ở trong trường.';

            //         array_push($lstData, $data);
            //     }

            //     // $dataResult['SUBJECT'] = $lstData;
            // }

            // foreach ($lstData as $key => $valuesss) {

            //     // if ($value->subject_history_group_id == 89) {
            //     //     // $data['subject_history_group_id'] = $value->subject_history_group_id;
            //     //     // $data['group_name'] = "Cấp bù học phí";
            //     //     // $data['subject_name'] = $value->subject_name;
            //     // }
            //     // else {
            //     //     // $data['subject_history_group_id'] = $value->subject_history_group_id;
            //     //     // $data['group_name'] = $value->group_name;
            //     //     // $data['subject_name'] = $value->subject_name;

            //         // array_push($result, $value);
            //     // }
            // }

            // if (!is_null($data) && !empty($data)) {
            //     array_push($result, $data);
            // }

            if (!is_null($result) && !empty($result) && count($result) > 0) {
                $dataResult['SUBJECT'] = $result;
            }
            else {

                $data = [];

                foreach ($lstData as $value) {
                    if ($value->subject_history_group_id == 89 || $value->subject_history_group_id == 90 || $value->subject_history_group_id == 91) {
                        $data['subject_history_group_id'] = $value->subject_history_group_id;
                        $data['group_name'] = "Cấp bù học phí";
                        $data['subject_name'] = $value->subject_name . '. ';
                    }
                    else {
                        $dt['subject_history_group_id'] = $value->subject_history_group_id;
                        $dt['group_name'] = $value->group_name;
                        $dt['subject_name'] = $value->subject_name;

                        array_push($result, $dt);
                    }
                }
                
                if (!is_null($data) && !empty($data) && count($data) > 0) {
                    array_push($result, $data);
                }

                $dataResult['SUBJECT'] = $result;
            }

            if ($arrData[1] != 0 && $arrData[1] == 1 && count($arrData) > 3) {
                $dataResult['CHEDO'] = DB::table('qlhs_tonghopchedo')
                ->where('qlhs_thcd_profile_id', '=', $arrData[0])
                ->where('qlhs_thcd_nam', '=', $arrData[3])
                ->select('qlhs_thcd_trangthai_MGHP as MGHP', 
                    'qlhs_thcd_trangthai_CPHT as CPHT', 
                    'qlhs_thcd_trangthai_HTAT as HTAT',
                    'qlhs_thcd_trangthai_HTBT_TA as HTBT_TA',
                    'qlhs_thcd_trangthai_HTBT_TO as HTBT_TO',
                    'qlhs_thcd_trangthai_HTBT_VHTT as HTBT_VHTT',
                    'qlhs_thcd_trangthai_HSKT_HB as HSKT_HB',
                    'qlhs_thcd_trangthai_HSKT_DDHT as HSKT_DDHT',
                    'qlhs_thcd_trangthai_HSDTTS as HSDTTS',
                    'qlhs_thcd_trangthai_HTATHS as HTATHS',
                    'qlhs_thcd_trangthai_HBHSDTNT as HBHSDTNT',
                    'qlhs_thcd_trangthai as TRANGTHAI',
                    'qlhs_thcd_ghichu as GHICHU',

                    'PheDuyet_trangthai_MGHP as MGHP_PHEDUYET',
                    'PheDuyet_trangthai_CPHT as CPHT_PHEDUYET',
                    'PheDuyet_trangthai_HTAT as HTAT_PHEDUYET',
                    'PheDuyet_trangthai_HTBT_TA as HTBT_TA_PHEDUYET',
                    'PheDuyet_trangthai_HTBT_TO as HTBT_TO_PHEDUYET',
                    'PheDuyet_trangthai_HTBT_VHTT as HTBT_VHTT_PHEDUYET',
                    'PheDuyet_trangthai_HSKT_HB as HSKT_HB_PHEDUYET',
                    'PheDuyet_trangthai_HSKT_DDHT as HSKT_DDHT_PHEDUYET',
                    'PheDuyet_trangthai_HSDTTS as HSDTTS_PHEDUYET',
                    'PheDuyet_trangthai_HTATHS as HTATHS_PHEDUYET',
                    'PheDuyet_trangthai_HBHSDTNT as HBHSDTNT_PHEDUYET',
                    'qlhs_thcd_trangthai_PD as TRANGTHAI_PHEDUYET',
                    'PheDuyet_ghichu as GHICHU_PHEDUYET',

                    'ThamDinh_trangthai_MGHP as MGHP_THAMDINH',
                    'ThamDinh_trangthai_CPHT as CPHT_THAMDINH',
                    'ThamDinh_trangthai_HTAT as HTAT_THAMDINH',
                    'ThamDinh_trangthai_HTBT_TA as HTBT_TA_THAMDINH',
                    'ThamDinh_trangthai_HTBT_TO as HTBT_TO_THAMDINH',
                    'ThamDinh_trangthai_HTBT_VHTT as HTBT_VHTT_THAMDINH',
                    'ThamDinh_trangthai_HSKT_HB as HSKT_HB_THAMDINH',
                    'ThamDinh_trangthai_HSKT_DDHT as HSKT_DDHT_THAMDINH',
                    'ThamDinh_trangthai_HSDTTS as HSDTTS_THAMDINH',
                    'ThamDinh_trangthai_HTATHS as HTATHS_THAMDINH',
                    'ThamDinh_trangthai_HBHSDTNT as HBHSDTNT_THAMDINH',
                    'qlhs_thcd_trangthai_TD as TRANGTHAI_THAMDINH',
                    'ThamDinh_ghichu as GHICHU_THAMDINH')
                ->get();
            }
            else if ($arrData[1] != 0 && $arrData[1] == 2 && count($arrData) > 3) {
                $dataResult['CHEDO'] = DB::table('qlhs_tonghopchedo')
                ->where('qlhs_thcd_profile_id', '=', $arrData[0])
                ->where('qlhs_thcd_nam', '=', $arrData[3])
                ->select('qlhs_thcd_trangthai_MGHP_HK2 as MGHP', 
                    'qlhs_thcd_trangthai_CPHT_HK2 as CPHT', 
                    'qlhs_thcd_trangthai_HTAT_HK2 as HTAT',
                    'qlhs_thcd_trangthai_HTBT_TA_HK2 as HTBT_TA',
                    'qlhs_thcd_trangthai_HTBT_TO_HK2 as HTBT_TO',
                    'qlhs_thcd_trangthai_HTBT_VHTT_HK2 as HTBT_VHTT',
                    'qlhs_thcd_trangthai_HSKT_HB_HK2 as HSKT_HB',
                    'qlhs_thcd_trangthai_HSKT_DDHT_HK2 as HSKT_DDHT',
                    'qlhs_thcd_trangthai_HSDTTS_HK2 as HSDTTS',
                    'qlhs_thcd_trangthai_HTATHS_HK2 as HTATHS',
                    'qlhs_thcd_trangthai_HBHSDTNT_HK2 as HBHSDTNT',
                    'qlhs_thcd_trangthai_HK2 as TRANGTHAI',
                    'qlhs_thcd_ghichu_HK2 as GHICHU',

                    'PheDuyet_trangthai_MGHP_HK2 as MGHP_PHEDUYET',
                    'PheDuyet_trangthai_CPHT_HK2 as CPHT_PHEDUYET',
                    'PheDuyet_trangthai_HTAT_HK2 as HTAT_PHEDUYET',
                    'PheDuyet_trangthai_HTBT_TA_HK2 as HTBT_TA_PHEDUYET',
                    'PheDuyet_trangthai_HTBT_TO_HK2 as HTBT_TO_PHEDUYET',
                    'PheDuyet_trangthai_HTBT_VHTT_HK2 as HTBT_VHTT_PHEDUYET',
                    'PheDuyet_trangthai_HSKT_HB_HK2 as HSKT_HB_PHEDUYET',
                    'PheDuyet_trangthai_HSKT_DDHT_HK2 as HSKT_DDHT_PHEDUYET',
                    'PheDuyet_trangthai_HSDTTS_HK2 as HSDTTS_PHEDUYET',
                    'PheDuyet_trangthai_HTATHS_HK2 as HTATHS_PHEDUYET',
                    'PheDuyet_trangthai_HBHSDTNT_HK2 as HBHSDTNT_PHEDUYET',
                    'qlhs_thcd_trangthai_PD_HK2 as TRANGTHAI_PHEDUYET',
                    'PheDuyet_ghichu_HK2 as GHICHU_PHEDUYET',

                    'ThamDinh_trangthai_MGHP_HK2 as MGHP_THAMDINH',
                    'ThamDinh_trangthai_CPHT_HK2 as CPHT_THAMDINH',
                    'ThamDinh_trangthai_HTAT_HK2 as HTAT_THAMDINH',
                    'ThamDinh_trangthai_HTBT_TA_HK2 as HTBT_TA_THAMDINH',
                    'ThamDinh_trangthai_HTBT_TO_HK2 as HTBT_TO_THAMDINH',
                    'ThamDinh_trangthai_HTBT_VHTT_HK2 as HTBT_VHTT_THAMDINH',
                    'ThamDinh_trangthai_HSKT_HB_HK2 as HSKT_HB_THAMDINH',
                    'ThamDinh_trangthai_HSKT_DDHT_HK2 as HSKT_DDHT_THAMDINH',
                    'ThamDinh_trangthai_HSDTTS_HK2 as HSDTTS_THAMDINH',
                    'ThamDinh_trangthai_HTATHS_HK2 as HTATHS_THAMDINH',
                    'ThamDinh_trangthai_HBHSDTNT_HK2 as HBHSDTNT_THAMDINH',
                    'qlhs_thcd_trangthai_TD_HK2 as TRANGTHAI_THAMDINH',
                    'ThamDinh_ghichu_HK2 as GHICHU_THAMDINH')
                ->get();
            }
            else if ($arrData[1] != 0 && $arrData[1] == 3 && count($arrData) > 3) {
                $dataResult['CHEDO'] = DB::table('qlhs_tonghopchedo')
                ->where('qlhs_thcd_profile_id', '=', $arrData[0])
                ->where('qlhs_thcd_nam', '=', $arrData[3])
                ->select(
                    DB::raw('CASE 
                        when (qlhs_thcd_trangthai_MGHP is not null and qlhs_thcd_trangthai_MGHP = 1) and (qlhs_thcd_trangthai_MGHP_HK2 is not null and qlhs_thcd_trangthai_MGHP_HK2 = 1)
                        then 1 else 0 end MGHP'), 
                    DB::raw('CASE 
                        when (qlhs_thcd_trangthai_CPHT is not null and qlhs_thcd_trangthai_CPHT = 1) and (qlhs_thcd_trangthai_CPHT_HK2 is not null and qlhs_thcd_trangthai_CPHT_HK2 = 1)
                        then 1 else 0 end CPHT'), 
                    DB::raw('CASE 
                        when (qlhs_thcd_trangthai_HTAT is not null and qlhs_thcd_trangthai_HTAT = 1) and (qlhs_thcd_trangthai_HTAT_HK2 is not null and qlhs_thcd_trangthai_HTAT_HK2 = 1)
                        then 1 else 0 end HTAT'),
                    DB::raw('CASE 
                        when (qlhs_thcd_trangthai_HTBT_TA is not null and qlhs_thcd_trangthai_HTBT_TA = 1) and (qlhs_thcd_trangthai_HTBT_TA_HK2 is not null and qlhs_thcd_trangthai_HTBT_TA_HK2 = 1)
                        then 1 else 0 end HTBT_TA'),
                    DB::raw('CASE 
                        when (qlhs_thcd_trangthai_HTBT_TO is not null and qlhs_thcd_trangthai_HTBT_TO = 1) and (qlhs_thcd_trangthai_HTBT_TO_HK2 is not null and qlhs_thcd_trangthai_HTBT_TO_HK2 = 1)
                        then 1 else 0 end HTBT_TO'),
                    DB::raw('CASE 
                        when (qlhs_thcd_trangthai_HTBT_VHTT is not null and qlhs_thcd_trangthai_HTBT_VHTT = 1) and (qlhs_thcd_trangthai_HTBT_VHTT_HK2 is not null and qlhs_thcd_trangthai_HTBT_VHTT_HK2 = 1)
                        then 1 else 0 end HTBT_VHTT'),
                    DB::raw('CASE 
                        when (qlhs_thcd_trangthai_HSKT_HB is not null and qlhs_thcd_trangthai_HSKT_HB = 1) and (qlhs_thcd_trangthai_HSKT_HB_HK2 is not null and qlhs_thcd_trangthai_HSKT_HB_HK2 = 1)
                        then 1 else 0 end HSKT_HB'),
                    DB::raw('CASE 
                        when (qlhs_thcd_trangthai_HSKT_DDHT is not null and qlhs_thcd_trangthai_HSKT_DDHT = 1) and (qlhs_thcd_trangthai_HSKT_DDHT_HK2 is not null and qlhs_thcd_trangthai_HSKT_DDHT_HK2 = 1)
                        then 1 else 0 end HSKT_DDHT'),
                    DB::raw('CASE 
                        when (qlhs_thcd_trangthai_HSDTTS is not null and qlhs_thcd_trangthai_HSDTTS = 1) and (qlhs_thcd_trangthai_HSDTTS_HK2 is not null and qlhs_thcd_trangthai_HSDTTS_HK2 = 1)
                        then 1 else 0 end HSDTTS'),
                    DB::raw('CASE 
                        when (qlhs_thcd_trangthai_HTATHS is not null and qlhs_thcd_trangthai_HTATHS = 1) and (qlhs_thcd_trangthai_HTATHS_HK2 is not null and qlhs_thcd_trangthai_HTATHS_HK2 = 1)
                        then 1 else 0 end HTATHS'),
                    DB::raw('CASE 
                        when (qlhs_thcd_trangthai_HBHSDTNT is not null and qlhs_thcd_trangthai_HBHSDTNT = 1) and (qlhs_thcd_trangthai_HBHSDTNT_HK2 is not null and qlhs_thcd_trangthai_HBHSDTNT_HK2 = 1)
                        then 1 else 0 end HBHSDTNT'),
                    DB::raw('CASE 
                        when (qlhs_thcd_trangthai is not null and qlhs_thcd_trangthai = 1) and (qlhs_thcd_trangthai_HK2 is not null and qlhs_thcd_trangthai_HK2 = 1)
                        then 1 else 0 end TRANGTHAI'),
                    'qlhs_thcd_ghichu_CANAM as GHICHU',

                    DB::raw('CASE 
                        when (PheDuyet_trangthai_MGHP is not null and PheDuyet_trangthai_MGHP = 1) and (PheDuyet_trangthai_MGHP_HK2 is not null and PheDuyet_trangthai_MGHP_HK2 = 1)
                        then 1 else 0 end MGHP_PHEDUYET'), 
                    DB::raw('CASE 
                        when (PheDuyet_trangthai_CPHT is not null and PheDuyet_trangthai_CPHT = 1) and (PheDuyet_trangthai_CPHT_HK2 is not null and PheDuyet_trangthai_CPHT_HK2 = 1)
                        then 1 else 0 end CPHT_PHEDUYET'), 
                    DB::raw('CASE 
                        when (PheDuyet_trangthai_HTAT is not null and PheDuyet_trangthai_HTAT = 1) and (PheDuyet_trangthai_HTAT_HK2 is not null and PheDuyet_trangthai_HTAT_HK2 = 1)
                        then 1 else 0 end HTAT_PHEDUYET'),
                    DB::raw('CASE 
                        when (PheDuyet_trangthai_HTBT_TA is not null and PheDuyet_trangthai_HTBT_TA = 1) and (PheDuyet_trangthai_HTBT_TA_HK2 is not null and PheDuyet_trangthai_HTBT_TA_HK2 = 1)
                        then 1 else 0 end HTBT_TA_PHEDUYET'),
                    DB::raw('CASE 
                        when (PheDuyet_trangthai_HTBT_TO is not null and PheDuyet_trangthai_HTBT_TO = 1) and (PheDuyet_trangthai_HTBT_TO_HK2 is not null and PheDuyet_trangthai_HTBT_TO_HK2 = 1)
                        then 1 else 0 end HTBT_TO_PHEDUYET'),
                    DB::raw('CASE 
                        when (PheDuyet_trangthai_HTBT_VHTT is not null and PheDuyet_trangthai_HTBT_VHTT = 1) and (PheDuyet_trangthai_HTBT_VHTT_HK2 is not null and PheDuyet_trangthai_HTBT_VHTT_HK2 = 1)
                        then 1 else 0 end HTBT_VHTT_PHEDUYET'),
                    DB::raw('CASE 
                        when (PheDuyet_trangthai_HSKT_HB is not null and PheDuyet_trangthai_HSKT_HB = 1) and (PheDuyet_trangthai_HSKT_HB_HK2 is not null and PheDuyet_trangthai_HSKT_HB_HK2 = 1)
                        then 1 else 0 end HSKT_HB_PHEDUYET'),
                    DB::raw('CASE 
                        when (PheDuyet_trangthai_HSKT_DDHT is not null and PheDuyet_trangthai_HSKT_DDHT = 1) and (PheDuyet_trangthai_HSKT_DDHT_HK2 is not null and PheDuyet_trangthai_HSKT_DDHT_HK2 = 1)
                        then 1 else 0 end HSKT_DDHT_PHEDUYET'),
                    DB::raw('CASE 
                        when (PheDuyet_trangthai_HSDTTS is not null and PheDuyet_trangthai_HSDTTS = 1) and (PheDuyet_trangthai_HSDTTS_HK2 is not null and PheDuyet_trangthai_HSDTTS_HK2 = 1)
                        then 1 else 0 end HSDTTS_PHEDUYET'),
                    DB::raw('CASE 
                        when (PheDuyet_trangthai_HTATHS is not null and PheDuyet_trangthai_HTATHS = 1) and (PheDuyet_trangthai_HTATHS_HK2 is not null and PheDuyet_trangthai_HTATHS_HK2 = 1)
                        then 1 else 0 end HTATHS_PHEDUYET'),
                    DB::raw('CASE 
                        when (PheDuyet_trangthai_HBHSDTNT is not null and PheDuyet_trangthai_HBHSDTNT = 1) and (PheDuyet_trangthai_HBHSDTNT_HK2 is not null and PheDuyet_trangthai_HBHSDTNT_HK2 = 1)
                        then 1 else 0 end HBHSDTNT_PHEDUYET'),
                    DB::raw('CASE 
                        when (qlhs_thcd_trangthai_PD is not null and qlhs_thcd_trangthai_PD = 1) and (qlhs_thcd_trangthai_PD_HK2 is not null and qlhs_thcd_trangthai_PD_HK2 = 1)
                        then 1 else 0 end TRANGTHAI_PHEDUYET'),
                    'PheDuyet_ghichu_CANAM as GHICHU_PHEDUYET',

                    DB::raw('CASE 
                        when (ThamDinh_trangthai_MGHP is not null and ThamDinh_trangthai_MGHP = 1) and (ThamDinh_trangthai_MGHP_HK2 is not null and ThamDinh_trangthai_MGHP_HK2 = 1)
                        then 1 else 0 end MGHP_THAMDINH'), 
                    DB::raw('CASE 
                        when (ThamDinh_trangthai_CPHT is not null and ThamDinh_trangthai_CPHT = 1) and (ThamDinh_trangthai_CPHT_HK2 is not null and ThamDinh_trangthai_CPHT_HK2 = 1)
                        then 1 else 0 end CPHT_THAMDINH'), 
                    DB::raw('CASE 
                        when (ThamDinh_trangthai_HTAT is not null and ThamDinh_trangthai_HTAT = 1) and (ThamDinh_trangthai_HTAT_HK2 is not null and ThamDinh_trangthai_HTAT_HK2 = 1)
                        then 1 else 0 end HTAT_THAMDINH'),
                    DB::raw('CASE 
                        when (ThamDinh_trangthai_HTBT_TA is not null and ThamDinh_trangthai_HTBT_TA = 1) and (ThamDinh_trangthai_HTBT_TA_HK2 is not null and ThamDinh_trangthai_HTBT_TA_HK2 = 1)
                        then 1 else 0 end HTBT_TA_THAMDINH'),
                    DB::raw('CASE 
                        when (ThamDinh_trangthai_HTBT_TO is not null and ThamDinh_trangthai_HTBT_TO = 1) and (ThamDinh_trangthai_HTBT_TO_HK2 is not null and ThamDinh_trangthai_HTBT_TO_HK2 = 1)
                        then 1 else 0 end HTBT_TO_THAMDINH'),
                    DB::raw('CASE 
                        when (ThamDinh_trangthai_HTBT_VHTT is not null and ThamDinh_trangthai_HTBT_VHTT = 1) and (ThamDinh_trangthai_HTBT_VHTT_HK2 is not null and ThamDinh_trangthai_HTBT_VHTT_HK2 = 1)
                        then 1 else 0 end HTBT_VHTT_THAMDINH'),
                    DB::raw('CASE 
                        when (ThamDinh_trangthai_HSKT_HB is not null and ThamDinh_trangthai_HSKT_HB = 1) and (ThamDinh_trangthai_HSKT_HB_HK2 is not null and ThamDinh_trangthai_HSKT_HB_HK2 = 1)
                        then 1 else 0 end HSKT_HB_THAMDINH'),
                    DB::raw('CASE 
                        when (ThamDinh_trangthai_HSKT_DDHT is not null and ThamDinh_trangthai_HSKT_DDHT = 1) and (ThamDinh_trangthai_HSKT_DDHT_HK2 is not null and ThamDinh_trangthai_HSKT_DDHT_HK2 = 1)
                        then 1 else 0 end HSKT_DDHT_THAMDINH'),
                    DB::raw('CASE 
                        when (ThamDinh_trangthai_HSDTTS is not null and ThamDinh_trangthai_HSDTTS = 1) and (ThamDinh_trangthai_HSDTTS_HK2 is not null and ThamDinh_trangthai_HSDTTS_HK2 = 1)
                        then 1 else 0 end HSDTTS_THAMDINH'),
                    DB::raw('CASE 
                        when (ThamDinh_trangthai_HTATHS is not null and ThamDinh_trangthai_HTATHS = 1) and (ThamDinh_trangthai_HTATHS_HK2 is not null and ThamDinh_trangthai_HTATHS_HK2 = 1)
                        then 1 else 0 end HTATHS_THAMDINH'),
                    DB::raw('CASE 
                        when (ThamDinh_trangthai_HBHSDTNT is not null and ThamDinh_trangthai_HBHSDTNT = 1) and (ThamDinh_trangthai_HBHSDTNT_HK2 is not null and ThamDinh_trangthai_HBHSDTNT_HK2 = 1)
                        then 1 else 0 end HBHSDTNT_THAMDINH'),
                    DB::raw('CASE 
                        when (qlhs_thcd_trangthai_TD is not null and qlhs_thcd_trangthai_TD = 1) and (qlhs_thcd_trangthai_TD_HK2 is not null and qlhs_thcd_trangthai_TD_HK2 = 1)
                        then 1 else 0 end TRANGTHAI_THAMDINH'),
                    'ThamDinh_ghichu_CANAM as GHICHU_THAMDINH')
                ->get();
            }

            // $dataResult['CHEDO'] = DB::table('qlhs_tonghopchedo')
            //     ->where('qlhs_thcd_profile_id', '=', $arrData[0])
            //     ->select('qlhs_thcd_trangthai_MGHP as MGHP', 
            //      'qlhs_thcd_trangthai_CPHT as CPHT', 
            //      'qlhs_thcd_trangthai_HTAT as HTAT',
            //      'qlhs_thcd_trangthai_HTBT_TA as HTBT_TA',
            //      'qlhs_thcd_trangthai_HTBT_TO as HTBT_TO',
            //      'qlhs_thcd_trangthai_HTBT_VHTT as HTBT_VHTT',
            //      'qlhs_thcd_trangthai_HSKT_HB as HSKT_HB',
            //      'qlhs_thcd_trangthai_HSKT_DDHT as HSKT_DDHT',
            //      'qlhs_thcd_trangthai_HSDTTS as HSDTTS',
            //      'qlhs_thcd_trangthai as TRANGTHAIHK1',
            //      'qlhs_thcd_trangthai_HK2 as TRANGTHAIHK2')
            //     ->get();

            return $dataResult;
        } catch (Exception $e) {
            
        }       
    }

    public function getProfileSubjectByIdPhongSo($objJson){
        try {
            $dataResult = [];

            $listData = [];

            $lstData = [];

            $result = [];

            $obj = json_decode($objJson);
            $profile_id = $obj->{'PROFILEID'};
            $socongvan = $obj->{'SOCONGVAN'};


            $dataResult['GROUP'] = DB::table('qlhs_profile')
            ->leftJoin('qlhs_miengiamhocphi', 'id_profile', '=', DB::raw('qlhs_profile.profile_id AND qlhs_miengiamhocphi.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "MGHP" and report_name = "'.$socongvan.'")'))
            ->leftJoin('qlhs_chiphihoctap', 'cpht_profile_id', '=', DB::raw('qlhs_profile.profile_id AND qlhs_chiphihoctap.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "CPHT" and report_name = "'.$socongvan.'")'))
            ->leftJoin('qlhs_hotrotienan', 'htta_profile_id', '=', DB::raw('qlhs_profile.profile_id AND qlhs_hotrotienan.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "HTAT" and report_name = "'.$socongvan.'")'))
            ->leftJoin('qlhs_hotrohocsinhbantru', 'qlhs_hotrohocsinhbantru.profile_id', '=', DB::raw('qlhs_profile.profile_id AND qlhs_hotrohocsinhbantru.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "HTBT" and report_name = "'.$socongvan.'")'))
            ->leftJoin('qlhs_hotrohocsinhkhuyettat', 'qlhs_hotrohocsinhkhuyettat.profile_id', '=', DB::raw('qlhs_profile.profile_id AND qlhs_hotrohocsinhkhuyettat.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "HSKT" and report_name = "'.$socongvan.'")'))
            ->leftJoin('qlhs_hotrohocsinhdantocthieuso AS hsdtts', 'hsdtts.profile_id', '=', DB::raw('qlhs_profile.profile_id AND hsdtts.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "HSDTTS" and report_name = "'.$socongvan.'")'))
            ->leftJoin('qlhs_hotrohocsinhdantocthieuso AS htaths', 'htaths.profile_id', '=', DB::raw('qlhs_profile.profile_id AND htaths.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "HTATHS" and report_name = "'.$socongvan.'")'))
            ->leftJoin('qlhs_hotrohocsinhdantocthieuso AS hbhsdtnt', 'hbhsdtnt.profile_id', '=', DB::raw('qlhs_profile.profile_id AND hbhsdtnt.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "HBHSDTNT" and report_name = "'.$socongvan.'")'))
            ->leftJoin('qlhs_schools', 'schools_id', '=', 'profile_school_id')
            ->leftJoin('qlhs_class', 'class_id', '=', 'profile_class_id')
            ->where('qlhs_profile.profile_id', '=', $profile_id)
            ->select('trangthai_pheduyet_MGHP', 'trangthai_pheduyet_CPHT', 'trangthai_pheduyet_HTAT', 'trangthai_pheduyet_HTBT', 'trangthai_pheduyet_HSKT', 'hsdtts.trangthai_pheduyet_HSDTTS as trangthai_pheduyet_HSDTTS', 'htaths.trangthai_pheduyet_HSDTTS as trangthai_pheduyet_HTATHS', 'hbhsdtnt.trangthai_pheduyet_HSDTTS as trangthai_pheduyet_HBHSDTNT', 'trangthai_thamdinh_MGHP', 'trangthai_thamdinh_CPHT', 'trangthai_thamdinh_HTAT', 'trangthai_thamdinh_HTBT', 'trangthai_thamdinh_HSKT', 'hsdtts.trangthai_thamdinh_HSDTTS as trangthai_thamdinh_HSDTTS', 'htaths.trangthai_thamdinh_HSDTTS as trangthai_thamdinh_HTATHS', 'hbhsdtnt.trangthai_thamdinh_HSDTTS as trangthai_thamdinh_HBHSDTNT')->get();

            $listData = DB::table('qlhs_subject')
                ->leftJoin('qlhs_profile_subject', 'profile_subject_subject_id', '=', 'subject_id')
                ->leftJoin('qlhs_subject_history', 'subject_history_subject_id', '=', 'subject_id')
                ->leftJoin('qlhs_group', 'group_id', '=', 'subject_history_group_id')
                ->where('profile_subject_profile_id', '=', $profile_id)
                ->select('subject_history_group_id', 
                    'group_name', 
                    'subject_id', 
                    'subject_name')
                ->get();
                // ->select('subject_history_group_id', 
                //     DB::raw('GROUP_CONCAT(group_name) as group_name'), 
                //     'subject_id', 
                //     'subject_name')
                // ->groupBy('subject_history_group_id', 'subject_id', 'subject_name')
                // ->get();

            //Miễn 100%
            $tantat = 0;
            $hocanngheo = 0;
            //Giảm 70%
            $vungkhokhan = 0;
            $dantocthieuso = 0;
            //HTBT
            $hsbantru = 0;
            $ongoaitruong = 0;
            // $dataResult['SUBJECT']

            // return $listData[0];
            foreach ($listData as $value) {
                if ($value->subject_id == 74) {
                    $tantat = 1;
                }
                if ($value->subject_id == 41) {
                    $hocanngheo = 1;
                }
                if ($value->subject_id == 34) {
                    $vungkhokhan = 1;
                }
                if ($value->subject_id == 49) {
                    $dantocthieuso = 1;
                }
                if ($value->subject_id == 46) {
                    $hsbantru = 1;
                }
                if ($value->subject_id == 72) {
                    $ongoaitruong = 1;
                }
                array_push($lstData, $value);
            }
            
            if ($tantat > 0 && $hocanngheo > 0) {
                $getSubMien100 = DB::table('qlhs_subject')
                    ->whereIn('subject_id', [41, 74])
                    ->select(
                        'subject_name')
                    ->get();

                $getGroupMien100 = DB::table('qlhs_group')
                    ->where('group_id', '=', 89)
                    ->select(
                        'group_id', 
                        'group_name')
                    ->get();

                $getGroupHTATMG = DB::table('qlhs_group')
                    ->where('group_id', '=', 93)
                    ->select(
                        'group_id', 
                        'group_name')
                    ->get();
                

                $data = [];
                $dataHTATMG = [];
                $subName = '';
                $subNameHTATMG = '';

                foreach ($lstData as $value) {
                    if ($value->subject_history_group_id == 89 || $value->subject_history_group_id == 90 || $value->subject_history_group_id == 91) {
                        $data['subject_history_group_id'] = $value->subject_history_group_id;
                        $data['group_name'] = "Cấp bù học phí";
                        $data['subject_name'] = $getSubMien100[0]->{'subject_name'} . ' + ' . $getSubMien100[1]->{'subject_name'} . '. ';
                        if ($value->subject_name != $subName) {
                            $data['subject_name'] .= $value->subject_name . '. ';
                        }
                        $subName = $value->subject_name;
                    }
                    else if ($value->subject_history_group_id != 93) {
                        $dt['subject_history_group_id'] = $value->subject_history_group_id;
                        $dt['group_name'] = $value->group_name;
                        $dt['subject_name'] = $value->subject_name;

                        array_push($result, $dt);
                    }
                    else if ($value->subject_history_group_id == 93) {
                        $dataHTATMG['subject_history_group_id'] = $value->subject_history_group_id;
                        $dataHTATMG['group_name'] = $value->group_name;
                        $dataHTATMG['subject_name'] = $getSubMien100[0]->{'subject_name'} . ' + ' . $getSubMien100[1]->{'subject_name'} . '. ';

                        if ($value->subject_name != $subNameHTATMG) {
                            $dataHTATMG['subject_name'] .= $value->subject_name . '. ';
                        }
                        $subNameHTATMG = $value->subject_name;
                    }
                }

                if (!is_null($data) && !empty($data) && count($data) > 0) {
                    
                    array_push($result, $data);
                }
                else {
                    $data['subject_history_group_id'] = 89;
                    $data['group_name'] = "Cấp bù học phí";
                    $data['subject_name'] = $getSubMien100[0]->{'subject_name'} . ' + ' . $getSubMien100[1]->{'subject_name'} . '. ';
                    array_push($result, $data);
                }

                if (!is_null($dataHTATMG) && !empty($dataHTATMG) && count($dataHTATMG) > 0) {
                    
                    array_push($result, $dataHTATMG);
                }
                else {
                    //Ăn trưa mẫu giáo
                    $dt['subject_history_group_id'] = $getGroupHTATMG[0]->{'group_id'};
                    $dt['group_name'] = $getGroupHTATMG[0]->{'group_name'};
                    $dt['subject_name'] = $getSubMien100[0]->{'subject_name'} . ' + ' . $getSubMien100[1]->{'subject_name'} . '. ';

                    array_push($result, $dt);
                }
            }

            if ($vungkhokhan > 0 && $dantocthieuso > 0) {
                $getSubGiam70 = DB::table('qlhs_subject')
                    ->whereIn('subject_id', [34, 49])
                    ->select(
                        'subject_name')
                    ->get();

                $getGroupGiam70 = DB::table('qlhs_group')
                    ->where('group_id', '=', 90)
                    ->select(
                        'group_id', 
                        'group_name')
                    ->get();

                $data = [];

                foreach ($lstData as $value) {
                    if ($value->subject_history_group_id == 89 || $value->subject_history_group_id == 90 || $value->subject_history_group_id == 91) {
                        $data['subject_history_group_id'] = $value->subject_history_group_id;
                        $data['group_name'] = "Cấp bù học phí";
                        $data['subject_name'] = $value->subject_name . '. ' . $getSubGiam70[0]->{'subject_name'} . ' + ' . $getSubGiam70[1]->{'subject_name'} . '. ';
                    }
                    else {
                        $dt['subject_history_group_id'] = $value->subject_history_group_id;
                        $dt['group_name'] = $value->group_name;
                        $dt['subject_name'] = $value->subject_name;

                        array_push($result, $dt);
                    }
                }
                
                if (!is_null($data) && !empty($data) && count($data) > 0) {
                    array_push($result, $data);
                }
                else {
                    $data['subject_history_group_id'] = 90;
                    $data['group_name'] = "Cấp bù học phí";
                    $data['subject_name'] = $getSubGiam70[0]->{'subject_name'} . ' + ' . $getSubGiam70[1]->{'subject_name'} . '. ';
                    array_push($result, $data);
                }
            }

            if ($vungkhokhan > 0) {
                $getSubBanTru = DB::table('qlhs_subject')
                    ->where('subject_id', '=', 34)
                    ->select(
                        'subject_name')
                    ->get();

                $getGroupBanTru = DB::table('qlhs_group')
                    ->whereIn('group_id', [94, 98])
                    ->select(
                        'group_id', 
                        'group_name')
                    ->get();

                foreach ($getGroupBanTru as $value) {
                    
                    $data['subject_history_group_id'] = $value->group_id;
                    $data['group_name'] = $value->group_name;
                    $data['subject_name'] = $getSubBanTru[0]->{'subject_name'};

                    array_push($lstData, $data);
                }

                // $dataResult['SUBJECT'] = $lstData;
            }


            if (!is_null($result) && !empty($result) && count($result) > 0) {
                $dataResult['SUBJECT'] = $result;
            }
            else {

                $data = [];

                foreach ($lstData as $value) {
                    if ($value->subject_history_group_id == 89 || $value->subject_history_group_id == 90 || $value->subject_history_group_id == 91) {
                        $data['subject_history_group_id'] = $value->subject_history_group_id;
                        $data['group_name'] = "Cấp bù học phí";
                        $data['subject_name'] = $value->subject_name . '. ';
                    }
                    else {
                        $dt['subject_history_group_id'] = $value->subject_history_group_id;
                        $dt['group_name'] = $value->group_name;
                        $dt['subject_name'] = $value->subject_name;

                        array_push($result, $dt);
                    }
                }
                
                if (!is_null($data) && !empty($data) && count($data) > 0) {
                    array_push($result, $data);
                }

                $dataResult['SUBJECT'] = $result;
            }

            return $dataResult;
        } catch (Exception $e) {
            // $mess = new qlhs_message();    
            //         $mess->type = 0;
            //         $mess->message_text = "Công văn mới số : ".$report_name;
            //         $mess->school_id = $school_id;
            //         $mess->created_user = Auth::user()->id;
            //         $mess->report_name = $report_name;
            //         $mess->save();
        }       
    }

//------------------------------------------------------Lập danh sách--------------------------------------------------------------------
    public function lapdanhsachTHCD(Request $request){
        try {
            // $files =  $request->file('FILE');
            $school_id = $request->input('SCHOOLID');
            $year = $request->input('YEAR');
            $report_name = $request->input('REPORTNAME');
            $user_sign = '';//$request->input('SIGNNAME');
            $user_create = '';//$request->input('CREATENAME');
            $note = $request->input('NOTE');
            $status = 0;//$request->input('STATUS');
            $chedo = $request->input('ARRCHEDO');

            $current_user_id = Auth::user()->id;
            $current_date = Carbon::now('Asia/Ho_Chi_Minh');

            $arrYear = [];
            $arrYear = explode("-", $year);

            $strYear = $arrYear[1];

            $result = [];

            $arrChedo = explode(",", $chedo);
            // return $arrChedo;

            $checkReportName = DB::table('qlhs_hosobaocao')
            ->where('report_name', 'LIKE', DB::raw("'%".$report_name."%' and (report LIKE '%MGHP%' or report LIKE '%CPHT%' or report LIKE '%HTAT%' or report LIKE '%HTBT%' or report LIKE '%HSKT%' or report LIKE '%HSDTTS%' or report LIKE '%HTATHS%' or report LIKE '%HBHSDTNT%' or report LIKE '%NGNA%')"))
            ->get();


            if (!is_null($checkReportName) && !empty($checkReportName) && count($checkReportName) > 0) {
                $result['error'] = "Tên báo cáo đã tồn tại, xin mời nhập tên khác!";
                return $result;
            }

            $getUnit = DB::table('qlhs_schools')->where('schools_id', '=', $school_id)->select('schools_unit_id')->first();

            // return $getUnit->schools_unit_id;
            $number = 0;
            

            // // Export Data
            foreach ($arrChedo as $value) {
                if ($value == 1) {
                    $number = 1;
                    $this->miengiamhocphi($school_id, $strYear, $note, $report_name, $user_sign, $user_create, $status);
                }
                if ($value == 2) {
                    $number = 1;
                    $this->chiphihoctap($school_id, $strYear, $note, $report_name, $user_sign, $user_create, $status);
                }
                if ($value == 3 && $getUnit->schools_unit_id == 1) {
                    $number = 1;
                    $this->hoTroAnTruaTreEm($school_id, $strYear, $note, $report_name, $user_sign, $user_create, $status);
                }
                if ($value == 4 && $getUnit->schools_unit_id != 1) {
                    $number = 1;
                    $this->getDataHTBT($school_id, $strYear, $report_name, $user_sign, $user_create, $note, $status);
                }
                if ($value == 5) {
                    $number = 1;
                    $this->getDataHSKT($school_id, $strYear, $report_name, $user_sign, $user_create, $note, $status);
                }
                if ($value == 6 && $getUnit->schools_unit_id != 1) {
                    $number = 1;
                    $this->getDataHTATHS($school_id, $strYear, $report_name, $user_sign, $user_create, $note, $status);
                }
                if ($value == 7) {
                    $number = 1;
                    $this->getDataHSDTTS($school_id, $strYear, $report_name, $user_sign, $user_create, $note, $status);
                }
                if ($value == 8 && $getUnit->schools_unit_id != 1) {
                    $number = 1;
                    $this->getDataHBHSDTNT($school_id, $strYear, $report_name, $user_sign, $user_create, $note, $status);
                }
                if ($value == 9) {
                    $number = 1;
                    $this->getDataNGNA($school_id, $strYear, $report_name, $user_sign, $user_create, $note, $status);
                }
            }
            
            

            if ($number == 1) {
                try{
                    $mess = new qlhs_message();    
                    $mess->type = 0;
                    $mess->message_text = "Công văn mới số : ".$report_name;
                    $mess->school_id = $school_id;
                    $mess->created_user = Auth::user()->id;
                    $mess->report_name = $report_name;
                    $mess->save();
                    $result['success'] = "Lập danh sách thành công";
                }catch(Exception $e){
                    $mess = new qlhs_message();    
                    $mess->type = 0;
                    $mess->message_text = "Công văn mới số : ".$report_name;
                    $mess->school_id = $school_id;
                    $mess->created_user = Auth::user()->id;
                    $mess->report_name = $report_name;
                    $mess->save();
                    $result['success'] = "Lập danh sách thành công";
                }
            }
            else {
                $result['error'] = "Lập danh sách thất bại";
            }          
            

            // if ($getUnit->schools_unit_id != 1) {
                
                
                
            //     // $this->getDataNGNA($files, $school_id, $strYear, $report_name, $user_sign, $user_create, $note, $status);
            // }

            return $result;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function exportExcelTruongDeNghi($jsonData){
        try {
            $obj = json_decode($jsonData);
            $reportName = $obj->{'REPORTNAME'};
            $reportType = $obj->{'REPORTTYPE'};
            
            $getReport = DB::table('qlhs_hosobaocao')
                ->where('report_name', '=', $reportName)
                ->where('report', '=', $reportType)
                ->select('report_id', 'report_type', 'report_year')->first();
              
            if ($reportType == "MGHP") {
                $this->exportforSchoolsMGHP($getReport->report_id, false);
            }
            if ($reportType == "CPHT") {
                $this->exportforSchoolsCPHT($getReport->report_id, false);
            }
            if ($reportType == "HTAT") {
                $this->exportforSchoolsHTAT($getReport->report_id, false);
            }
            if ($reportType == "HTBT") {
                $this->exportforSchoolsHTBT($getReport->report_id, false);
            }
            if ($reportType == "HSKT") {
                $this->exportforSchoolsHSKT($getReport->report_id, false);
            }
            if ($reportType == "HSDTTS") {
                $this->exportforSchoolsHSDTTS($getReport->report_id, false);
            }
            if ($reportType == "HTATHS") {
                $this->exportforSchoolsHTATHS($getReport->report_id, false);
            }
            if ($reportType == "HBHSDTNT") {
                $this->exportforSchoolsHBHSDTNT($getReport->report_id, false);
            }
            if ($reportType == "NGNA") {
                $this->exportforSchoolsNGNA($getReport->report_id, false);
            }
        } catch (Exception $e) {
            return $e;
        }
    }

//---------------------------------------------------------------------MGHP------------------------------------------------------------------------
    public function miengiamhocphi($truong, $namhoc, $note, $report_name, $report_user_sign, $user_name, $status){
        $json = [];
        
        $user = Auth::user()->id;
        $now = Carbon::now('Asia/Ho_Chi_Minh');
        
        
        $data1 = DB::table('qlhs_profile')
            ->join('qlhs_profile_subject', 'profile_id', '=' , DB::raw('profile_subject_profile_id AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.$namhoc.' AND (qlhs_profile_subject.end_year is null OR qlhs_profile_subject.end_year > '.$namhoc.')'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$namhoc.'-'.($namhoc + 1).'"'))
            ->leftJoin('qlhs_kinhphinamhoc as kp1','kp1.idTruong','=',DB::raw('profile_school_id and kp1.codeYear = '.($namhoc - 1).''))
            ->leftJoin('qlhs_kinhphinamhoc as kp2','kp2.idTruong','=',DB::raw('profile_school_id and kp2.codeYear = '.$namhoc.''))
            ->leftJoin('qlhs_tonghopchedo as yearold', 'yearold.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearold.qlhs_thcd_nam = '.($namhoc - 1)))
            ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearcur.qlhs_thcd_nam = '.$namhoc))
            ->leftJoin('qlhs_tonghopchedo as yearnew', 'yearnew.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearnew.qlhs_thcd_nam = '.($namhoc + 1)))
            ->select('kp1.money as money1','kp2.money as money2','profile_id','profile_name','profile_year', 'level_old', 'level_cur', 'level_new', 'yearold.qlhs_thcd_trangthai_MGHP as old_MGHP', 'yearold.qlhs_thcd_trangthai_MGHP_HK2 as old_MGHP_HK2', 'yearcur.qlhs_thcd_trangthai_MGHP as cur_MGHP', 'yearcur.qlhs_thcd_trangthai_MGHP_HK2 as cur_MGHP_HK2',
                DB::raw('MAX(qlhs_profile_subject.profile_subject_subject_id) as profile_subject_subject_id'), 
                DB::raw('MAX(CASE when profile_subject_subject_id = 74 then 1 else 0 END) Mien'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 35 then 1 else 0 END) Mien1'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 36 then 1 else 0 END) Mien2'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 38 then 1 else 0 END) Mien3'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 73 then 1 else 0 END) Mien4'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 39 then 1 else 0 END) Mien5'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 28 then 1 else 0 END) Mien6'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 34 then 1 else 0 END) Giam70'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 49 then 1 else 0 END) Giam70_1'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 40 then 1 else 0 END) Giam501'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 41 then 1 else 0 END) Giam502'),

                DB::raw("MAX(CASE when level_old <> '' and qlhs_profile.profile_year < '".$namhoc."-06-01' and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".$namhoc."-01-01')) then 1 else 0 END) 'HKII1'"),
                DB::raw("MAX(CASE when level_cur <> '' and qlhs_profile.profile_year < '".$namhoc."-12-31' and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".$namhoc."-05-31')) then 1 else 0 END) 'HKI2'"),
                DB::raw("MAX(CASE when level_cur <> '' and qlhs_profile.profile_year < '".($namhoc + 1)."-06-01' and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".((int)$namhoc+1)."-01-01')) then 1 else 0 END) 'HKII2'"),
                DB::raw("MAX(CASE when level_new <> '' and qlhs_profile.profile_year < '".($namhoc +1)."-12-31' and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".((int)$namhoc+1)."-05-31')) then 1 else 0 END) 'HKI3'"))

            ->where('profile_year','<',$namhoc.'-06-01')
            ->where('profile_school_id','=',$truong)
            ->whereIn('profile_subject_subject_id', [28,35,36,73,38,39,34,40,41,74,49])
            // ->where('qlhs_thcd_nam', '=', DB::raw(($namhoc - 1).' and (qlhs_thcd_trangthai_MGHP = 1 or qlhs_thcd_trangthai_MGHP_HK2 = 1)'))
            
            ->groupBy('profile_id','profile_name','profile_year','level_old','level_cur','level_new','kp1.money','kp2.money', 'yearold.qlhs_thcd_trangthai_MGHP', 'yearold.qlhs_thcd_trangthai_MGHP_HK2', 'yearcur.qlhs_thcd_trangthai_MGHP', 'yearcur.qlhs_thcd_trangthai_MGHP_HK2');
        $data11 = DB::table(DB::raw("({$data1->toSql()}) as m"))->mergeBindings( $data1 )
            ->select('HKII1','HKI2','HKII2','HKI3','m.money1','m.money2','m.Mien','m.Mien1','m.Mien2','m.Mien3','m.Mien4','m.Mien5','m.Mien6','m.Giam70','m.Giam70_1','m.Giam501','m.Giam502','m.profile_id','m.profile_subject_subject_id','m.profile_name','m.profile_year','m.level_old','m.level_cur','m.level_new', 'old_MGHP', 'old_MGHP_HK2', 'cur_MGHP',  'cur_MGHP_HK2', 
                DB::raw('CASE 
                        when m.profile_subject_subject_id in (28,35,36,73,38,39) then ((m.HKII1*5*m.money1) + m.HKI2*4*(m.money2))
                        when m.profile_subject_subject_id = 34 then (((m.HKII1*5*m.money1) + m.HKI2*4*(m.money2))*7)/10 
                        when m.profile_subject_subject_id in (40,41) then (((m.HKII1*5*m.money1) + m.HKI2*4*(m.money2))*5)/10 
                    END NhuCau'),
                DB::raw('CASE 
                        when m.profile_subject_subject_id = 34 then (((m.HKII2*5*m.money2)+m.HKI3*4*(m.money2))*7)/10 
                        when m.profile_subject_subject_id in (40,41) then (((m.HKII2*5*m.money2)+m.HKI3*4*(m.money2))*5)/10 
                        when m.profile_subject_subject_id in (28,35,36,73,38,39) then ((m.HKII2*5*m.money2)+m.HKI3*4*(m.money2))
                    END DuToan'));

        $data2 = DB::table('qlhs_profile')
            ->join('qlhs_profile_subject', 'profile_id', '=' , DB::raw('profile_subject_profile_id AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.$namhoc.' AND (qlhs_profile_subject.end_year is null OR qlhs_profile_subject.end_year > '.$namhoc.')'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$namhoc.'-'.($namhoc + 1).'"'))
            ->leftJoin('qlhs_kinhphinamhoc as kp1','kp1.idTruong','=',DB::raw('profile_school_id and kp1.codeYear = '.($namhoc - 1).''))
            ->leftJoin('qlhs_kinhphinamhoc as kp2','kp2.idTruong','=',DB::raw('profile_school_id and kp2.codeYear = '.$namhoc.''))
            ->leftJoin('qlhs_tonghopchedo as yearold', 'yearold.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearold.qlhs_thcd_nam = '.($namhoc - 1)))
            ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearcur.qlhs_thcd_nam = '.$namhoc))
            ->leftJoin('qlhs_tonghopchedo as yearnew', 'yearnew.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearnew.qlhs_thcd_nam = '.($namhoc + 1)))
            ->select('kp1.money as money1','kp2.money as money2','profile_id','profile_name','profile_year', 'level_old', 'level_cur', 'level_new', 'yearold.qlhs_thcd_trangthai_MGHP as old_MGHP', 'yearold.qlhs_thcd_trangthai_MGHP_HK2 as old_MGHP_HK2', 'yearcur.qlhs_thcd_trangthai_MGHP as cur_MGHP', 'yearcur.qlhs_thcd_trangthai_MGHP_HK2 as cur_MGHP_HK2',
                DB::raw('MAX(qlhs_profile_subject.profile_subject_subject_id) as profile_subject_subject_id'), 
                DB::raw('MAX(CASE when profile_subject_subject_id = 74 then 1 else 0 END) Mien'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 35 then 1 else 0 END) Mien1'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 36 then 1 else 0 END) Mien2'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 38 then 1 else 0 END) Mien3'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 73 then 1 else 0 END) Mien4'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 39 then 1 else 0 END) Mien5'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 28 then 1 else 0 END) Mien6'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 34 then 1 else 0 END) Giam70'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 49 then 1 else 0 END) Giam70_1'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 40 then 1 else 0 END) Giam501'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 41 then 1 else 0 END) Giam502'),

                DB::raw("MAX(CASE when level_old <> '' and qlhs_profile.profile_year < '".$namhoc."-06-01' and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".($namhoc)."-01-01')) then 1 else 0 END) 'HKII1'"),
                DB::raw("MAX(CASE when level_cur <> '' and qlhs_profile.profile_year < '".$namhoc."-12-31' and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".($namhoc)."-05-31')) then 1 else 0 END) 'HKI2'"),
                DB::raw("MAX(CASE when level_cur <> '' and qlhs_profile.profile_year < '".($namhoc + 1)."-06-01' and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".((int)$namhoc+1)."-01-01')) then 1 else 0 END) 'HKII2'"),
                DB::raw("MAX(CASE when level_new <> '' and qlhs_profile.profile_year < '".($namhoc +1)."-12-31' and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".((int)$namhoc+1)."-05-31')) then 1 else 0 END) 'HKI3'"))

            ->where('profile_year','>',$namhoc.'-05-31')
            ->where('profile_year','<',((int)$namhoc+1).'-06-01')
            ->where('profile_school_id','=',$truong)
            ->whereIn('profile_subject_subject_id',[28,35,36,73,38,39,34,40,41,74,49])
            // ->where('yearcur.qlhs_thcd_nam', '=', DB::raw($namhoc.' and (yearcur.qlhs_thcd_trangthai_MGHP = 1 or yearcur.qlhs_thcd_trangthai_MGHP_HK2 = 1)'))
            ->groupBy('profile_id','profile_name','profile_year','level_old','level_cur','level_new','kp1.money','kp2.money', 'yearold.qlhs_thcd_trangthai_MGHP', 'yearold.qlhs_thcd_trangthai_MGHP_HK2', 'yearcur.qlhs_thcd_trangthai_MGHP', 'yearcur.qlhs_thcd_trangthai_MGHP_HK2');
        $data22 = DB::table(DB::raw("({$data2->toSql()}) as m"))->mergeBindings( $data2 )
            ->select('HKII1','HKI2','HKII2','HKI3','m.money1','m.money2','m.Mien','m.Mien1','m.Mien2','m.Mien3','m.Mien4','m.Mien5','m.Mien6','m.Giam70','m.Giam70_1','m.Giam501','m.Giam502','m.profile_id','m.profile_subject_subject_id','m.profile_name','m.profile_year','m.level_old','m.level_cur','m.level_new', 'old_MGHP', 'old_MGHP_HK2', 'cur_MGHP',  'cur_MGHP_HK2', 
                DB::raw('CASE 
                        when m.profile_subject_subject_id in (28,35,36,73,38,39) then ((m.HKII1*5*m.money1)+m.HKI2*4*(m.money2))
                        when m.profile_subject_subject_id = 34 then (((m.HKII1*5*m.money1)+m.HKI2*4*(m.money2))*7)/10 
                        when m.profile_subject_subject_id in (40,41) then (((m.HKII1*5*m.money1)+m.HKI2*4*(m.money2))*5)/10 
                    END NhuCau'),
                DB::raw('CASE 
                        when m.profile_subject_subject_id = 34 then (((m.HKII2*5*m.money2)+m.HKI3*4*(m.money2))*7)/10 
                        when m.profile_subject_subject_id in (40,41) then (((m.HKII2*5*m.money2)+m.HKI3*4*(m.money2))*5)/10 
                        when m.profile_subject_subject_id in (28,35,36,73,38,39) then ((m.HKII2*5*m.money2)+m.HKI3*4*(m.money2))
                    END DuToan'));

        $data3 = DB::table('qlhs_profile')
            ->join('qlhs_profile_subject', 'profile_id', '=' , DB::raw('profile_subject_profile_id AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.($namhoc + 1).' AND (qlhs_profile_subject.end_year is null OR qlhs_profile_subject.end_year > '.($namhoc + 1).')'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.($namhoc + 1).'-'.($namhoc + 2).'"'))
            ->leftJoin('qlhs_kinhphinamhoc as kp1','kp1.idTruong','=',DB::raw('profile_school_id and kp1.codeYear = '.($namhoc - 1).''))
            ->leftJoin('qlhs_kinhphinamhoc as kp2','kp2.idTruong','=',DB::raw('profile_school_id and kp2.codeYear = '.$namhoc.''))

            ->leftJoin('qlhs_tonghopchedo', 'qlhs_thcd_profile_id', '=', 'profile_id')

            ->select('kp1.money as money1','kp2.money as money2','profile_id','profile_name','profile_year', 'level_old', 'level_cur', 'level_new', 
                DB::raw('MAX(qlhs_profile_subject.profile_subject_subject_id) as profile_subject_subject_id'), 
                DB::raw('MAX(CASE when profile_subject_subject_id = 74 then 1 else 0 END) Mien'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 35 then 1 else 0 END) Mien1'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 36 then 1 else 0 END) Mien2'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 38 then 1 else 0 END) Mien3'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 73 then 1 else 0 END) Mien4'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 39 then 1 else 0 END) Mien5'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 28 then 1 else 0 END) Mien6'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 34 then 1 else 0 END) Giam70'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 49 then 1 else 0 END) Giam70_1'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 40 then 1 else 0 END) Giam501'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 41 then 1 else 0 END) Giam502'),

                DB::raw("MAX(CASE when level_old <> '' and qlhs_profile.profile_year < '".$namhoc."-06-01' and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".($namhoc)."-01-01')) then 1 else 0 END) 'HKII1'"),
                DB::raw("MAX(CASE when level_old <> '' and qlhs_profile.profile_year < '".$namhoc."-12-31' and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".($namhoc)."-05-31')) then 1 else 0 END) 'HKI2'"),
                DB::raw("MAX(CASE when level_old <> '' and qlhs_profile.profile_year < '".($namhoc + 1)."-06-01' and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".((int)$namhoc+1)."-01-01')) then 1 else 0 END) 'HKII2'"),
                DB::raw("MAX(CASE when level_cur <> '' and qlhs_profile.profile_year < '".($namhoc +1)."-12-31' and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".((int)$namhoc+1)."-05-31')) then 1 else 0 END) 'HKI3'"))

            ->where('profile_year','>',((int)$namhoc+1).'-05-31')
            ->where('profile_year','<',((int)$namhoc+2).'-01-01')
            ->where('profile_school_id','=',$truong)
            ->whereIn('profile_subject_subject_id',[28,35,36,73,38,39,34,40,41,74,49])
            // ->where('qlhs_thcd_nam', '=', DB::raw(($namhoc + 1).' and (qlhs_thcd_trangthai_MGHP = 1 or qlhs_thcd_trangthai_MGHP_HK2 = 1)'))
            ->where('qlhs_thcd_nam', '=', ($namhoc + 1))
            ->groupBy('profile_id','profile_name','profile_year','level_old','level_cur','level_new','kp1.money','kp2.money');
        $data33 = DB::table(DB::raw("({$data3->toSql()}) as m"))->mergeBindings( $data3 )
            ->select('HKII1','HKI2','HKII2','HKI3','m.money1','m.money2','m.Mien','m.Mien1','m.Mien2','m.Mien3','m.Mien4','m.Mien5','m.Mien6','m.Giam70','m.Giam70_1','m.Giam501','m.Giam502','m.profile_id','m.profile_subject_subject_id','m.profile_name','m.profile_year','m.level_old','m.level_cur','m.level_new', 
                DB::raw('CASE 
                        when m.profile_subject_subject_id in (28,35,36,73,38,39) then ((m.HKII1*5*m.money1)+m.HKI2*4*(m.money2))
                        when m.profile_subject_subject_id = 34 then (((m.HKII1*5*m.money1)+m.HKI2*4*(m.money2))*7)/10 
                        when m.profile_subject_subject_id in (40,41) then (((m.HKII1*5*m.money1)+m.HKI2*4*(m.money2))*5)/10 
                    END NhuCau'),
                DB::raw('CASE 
                        when m.profile_subject_subject_id = 34 then (((m.HKII2*5*m.money2)+m.HKI3*4*(m.money2))*7)/10 
                        when m.profile_subject_subject_id in (40,41) then (((m.HKII2*5*m.money2)+m.HKI3*4*(m.money2))*5)/10 
                        when m.profile_subject_subject_id in (28,35,36,73,38,39) then ((m.HKII2*5*m.money2)+m.HKI3*4*(m.money2))
                    END DuToan'));

        if($data11->count()==0 && $data22->count()==0 && $data33->count()==0){
                
        }else{
            $import =  $this->insertReportMGHP($data1,$data2,$data3,$truong,$namhoc,$user,$now,$report_name,$report_user_sign,$user_name,$status,$note);

            // return $import;
        }   
        // return $json;
    }

    public function insertReportMGHP($data11,$data22,$data33,$truong,$namhoc,$user,$now,$report_name,$report_user_sign,$user_name,$status,$note){
        $json = [];
        $time = time();
        $dir = storage_path().'/files/MGHP';
        if($data11->count() > 0){
            foreach ($data11->get() as $value) {

                $giam70hp = 0;
                $nhucau = 0;
                $dutoan = 0;

                if ($value->Giam70 > 0 && $value->Giam70_1 > 0) {
                    $giam70hp = 1;
                }

                if ($value->Mien > 0 && $value->Giam502 > 0) {

                        $nhucau = ($value->HKII1 * 5 * $value->money1 * $value->old_MGHP_HK2) + ($value->HKI2 * 4 * $value->money2 * $value->cur_MGHP);
                        $dutoan = ($value->HKII2 * 5 * $value->money2 * $value->cur_MGHP_HK2) + ($value->HKI3 * 4 * $value->money2);

                        $json['1'] = DB::table('qlhs_miengiamhocphi')->insert([
                            'id_profile' => $value->profile_id,
                            'mienphi_1' => $value->Mien1,
                            'mienphi_2' => 1,//$value->Mien2,
                            'mienphi_3' => $value->Mien3,
                            'mienphi_4' => $value->Mien4,
                            'mienphi_5' => $value->Mien5,
                            'mienphi_6' => $value->Mien6, 
                            'giam_70' => $giam70hp,//$value->Giam70,
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
                            'type_code' => 'MGHP-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time,
                            'trangthai_pheduyet_MGHP' => 1,
                            'trangthai_thamdinh_MGHP' => 1
                            ]);

                        $json['1'] = 1;
                }
                else {
                    if ($value->Mien1 > 0 || $value->Mien2 > 0 || $value->Mien3 > 0 || $value->Mien4 > 0 || $value->Mien5 > 0 || $value->Mien6 > 0) {
                        $nhucau = ($value->HKII1 * 5 * $value->money1 * $value->old_MGHP_HK2) + ($value->HKI2 * 4 * $value->money2 * $value->cur_MGHP);
                        $dutoan = ($value->HKII2 * 5 * $value->money2 * $value->cur_MGHP_HK2) + ($value->HKI3 * 4 * $value->money2);
                    }
                    else {
                        if ($value->Giam70 > 0 && $value->Giam70_1 > 0) {
                            $nhucau = ((($value->HKII1 * 5 * $value->money1 * $value->old_MGHP_HK2) + ($value->HKI2 * 4 * $value->money2 * $value->cur_MGHP)) * 7) / 10;
                            $dutoan = ((($value->HKII2 * 5 * $value->money2 * $value->cur_MGHP_HK2) + ($value->HKI3 * 4 * $value->money2)) * 7) / 10;
                        }
                        else if ($value->Giam501 > 0 || $value->Giam502 > 0) {
                            $nhucau = ((($value->HKII1 * 5 * $value->money1 * $value->old_MGHP_HK2) + ($value->HKI2 * 4 * $value->money2 * $value->cur_MGHP)) * 5) / 10;
                            $dutoan = ((($value->HKII2 * 5 * $value->money2 * $value->cur_MGHP_HK2) + ($value->HKI3 * 4 * $value->money2)) * 5) / 10;
                        }
                    }

                    if ($nhucau > 0 || $dutoan > 0) {

                        $json['1'] = DB::table('qlhs_miengiamhocphi')->insert([
                            'id_profile' => $value->profile_id,
                            'mienphi_1' => $value->Mien1,
                            'mienphi_2' => 0,//$value->Mien2,
                            'mienphi_3' => $value->Mien3,
                            'mienphi_4' => $value->Mien4,
                            'mienphi_5' => $value->Mien5,
                            'mienphi_6' => $value->Mien6, 
                            'giam_70' => $giam70hp,//$value->Giam70,
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
                            'type_code' => 'MGHP-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time,
                            'trangthai_pheduyet_MGHP' => 1,
                            'trangthai_thamdinh_MGHP' => 1
                            ]);
                    }
                    else {
                        $json['1'] = 1;
                    }
                }
            }
        }else{
            $json['1'] = 1;
        }

        if($data22->count() > 0){
            foreach ($data22->get() as $value) {

                $giam70hp = 0;
                $nhucau = 0;
                $dutoan = 0;

                if ($value->Giam70 > 0 && $value->Giam70_1 > 0) {
                    $giam70hp = 1;
                }

                if ($value->Mien > 0 && $value->Giam502 > 0) {

                        $nhucau = ($value->HKII1 * 5 * $value->money1 * $value->old_MGHP_HK2) + ($value->HKI2 * 4 * $value->money2 * $value->cur_MGHP);
                        $dutoan = ($value->HKII2 * 5 * $value->money2 * $value->cur_MGHP_HK2) + ($value->HKI3 * 4 * $value->money2);

                        $json['2'] = DB::table('qlhs_miengiamhocphi')->insert([
                            'id_profile' => $value->profile_id,
                            'mienphi_1' => $value->Mien1,
                            'mienphi_2' => 1,//$value->Mien2,
                            'mienphi_3' => $value->Mien3,
                            'mienphi_4' => $value->Mien4,
                            'mienphi_5' => $value->Mien5,
                            'mienphi_6' => $value->Mien6, 
                            'giam_70' => $giam70hp,//$value->Giam70,
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
                            'type_code' => 'MGHP-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time,
                            'trangthai_pheduyet_MGHP' => 1,
                            'trangthai_thamdinh_MGHP' => 1
                            ]);

                        $json['2'] = 1;
                }
                else {
                    if ($value->Mien1 > 0 || $value->Mien2 > 0 || $value->Mien3 > 0 || $value->Mien4 > 0 || $value->Mien5 > 0 || $value->Mien6 > 0) {
                        $nhucau = ($value->HKII1 * 5 * $value->money1 * $value->old_MGHP_HK2) + ($value->HKI2 * 4 * $value->money2 * $value->cur_MGHP);
                        $dutoan = ($value->HKII2 * 5 * $value->money2 * $value->cur_MGHP_HK2) + ($value->HKI3 * 4 * $value->money2);
                    }
                    else {
                        if ($value->Giam70 > 0 && $value->Giam70_1 > 0) {
                            $nhucau = ((($value->HKII1 * 5 * $value->money1 * $value->old_MGHP_HK2) + ($value->HKI2 * 4 * $value->money2 * $value->cur_MGHP)) * 7) / 10;
                            $dutoan = ((($value->HKII2 * 5 * $value->money2 * $value->cur_MGHP_HK2) + ($value->HKI3 * 4 * $value->money2)) * 7) / 10;
                        }
                        else if ($value->Giam501 > 0 || $value->Giam502 > 0) {
                            $nhucau = ((($value->HKII1 * 5 * $value->money1 * $value->old_MGHP_HK2) + ($value->HKI2 * 4 * $value->money2 * $value->cur_MGHP)) * 5) / 10;
                            $dutoan = ((($value->HKII2 * 5 * $value->money2 * $value->cur_MGHP_HK2) + ($value->HKI3 * 4 * $value->money2)) * 5) / 10;
                        }
                    }

                    if ($nhucau > 0 || $dutoan > 0) {

                        $json['2'] = DB::table('qlhs_miengiamhocphi')->insert([
                            'id_profile' => $value->profile_id,
                            'mienphi_1' => $value->Mien1,
                            'mienphi_2' => 0,//$value->Mien2,
                            'mienphi_3' => $value->Mien3,
                            'mienphi_4' => $value->Mien4,
                            'mienphi_5' => $value->Mien5,
                            'mienphi_6' => $value->Mien6, 
                            'giam_70' => $giam70hp,//$value->Giam70,
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
                            'type_code' => 'MGHP-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time,
                            'trangthai_pheduyet_MGHP' => 1,
                            'trangthai_thamdinh_MGHP' => 1
                            ]);
                    }
                    else {
                        $json['2'] = 1;
                    }
                }
            }
        }else{
            $json['2'] = 1;
        }
        if($data33->count() > 0){
            foreach ($data33->get() as $value) {

                $giam70hp = 0;
                $nhucau = 0;
                $dutoan = 0;

                if ($value->Giam70 > 0 && $value->Giam70_1 > 0) {
                    $giam70hp = 1;
                }

                if ($value->Mien > 0 && $value->Giam502 > 0) {

                        $nhucau = ($value->HKII1 * 5 * $value->money1) + ($value->HKI2 * 4 * $value->money2);
                        $dutoan = ($value->HKII2 * 5 * $value->money2) + ($value->HKI3 * 4 * $value->money2);

                        $json['3'] = DB::table('qlhs_miengiamhocphi')->insert([
                            'id_profile' => $value->profile_id,
                            'mienphi_1' => $value->Mien1,
                            'mienphi_2' => 1,//$value->Mien2,
                            'mienphi_3' => $value->Mien3,
                            'mienphi_4' => $value->Mien4,
                            'mienphi_5' => $value->Mien5,
                            'mienphi_6' => $value->Mien6, 
                            'giam_70' => $giam70hp,//$value->Giam70,
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
                            'type_code' => 'MGHP-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time,
                            'trangthai_pheduyet_MGHP' => 1,
                            'trangthai_thamdinh_MGHP' => 1
                            ]);

                        $json['3'] = 1;
                }
                else {
                    if ($value->Mien1 > 0 || $value->Mien2 > 0 || $value->Mien3 > 0 || $value->Mien4 > 0 || $value->Mien5 > 0 || $value->Mien6 > 0) {
                        $nhucau = ($value->HKII1 * 5 * $value->money1) + ($value->HKI2 * 4 * $value->money2);
                        $dutoan = ($value->HKII2 * 5 * $value->money2) + ($value->HKI3 * 4 * $value->money2);
                    }
                    else {
                        if ($value->Giam70 > 0 && $value->Giam70_1 > 0) {
                            $nhucau = ((($value->HKII1 * 5 * $value->money1) + ($value->HKI2 * 4 * $value->money2)) * 7) / 10;
                            $dutoan = ((($value->HKII2 * 5 * $value->money2) + ($value->HKI3 * 4 * $value->money2)) * 7) / 10;
                        }
                        else if ($value->Giam501 > 0 || $value->Giam502 > 0) {
                            $nhucau = ((($value->HKII1 * 5 * $value->money1) + ($value->HKI2 * 4 * $value->money2)) * 5) / 10;
                            $dutoan = ((($value->HKII2 * 5 * $value->money2) + ($value->HKI3 * 4 * $value->money2)) * 5) / 10;
                        }
                    }

                    if ($nhucau > 0 || $dutoan > 0) {

                        $json['3'] = DB::table('qlhs_miengiamhocphi')->insert([
                            'id_profile' => $value->profile_id,
                            'mienphi_1' => $value->Mien1,
                            'mienphi_2' => 0,//$value->Mien2,
                            'mienphi_3' => $value->Mien3,
                            'mienphi_4' => $value->Mien4,
                            'mienphi_5' => $value->Mien5,
                            'mienphi_6' => $value->Mien6, 
                            'giam_70' => $giam70hp,//$value->Giam70,
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
                            'type_code' => 'MGHP-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time,
                            'trangthai_pheduyet_MGHP' => 1,
                            'trangthai_thamdinh_MGHP' => 1
                            ]);
                    }
                    else {
                        $json['3'] = 1;
                    }
                }
            }

        }else{
            $json['3'] = 1;
        }
        // return $json;
        if((int)$json['1'] > 0 && (int)$json['2'] > 0 && (int)$json['3'] > 0){
            
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
                    'report_attach_name' => '',
                    'report_nature' => $status,
                    'report_year' => $namhoc,
                    'report_id_truong' => $truong,
                    'report_note' => $note,
                    'report' => 'MGHP'
                ]);

            if(!is_null($insert_returnID) && $insert_returnID > 0 ){
                $this->exportforSchoolsMGHP($insert_returnID);
                if (file_exists(storage_path().'/exceldownload/MGHP/'.'MGHP-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time.'.xlsx')) {
                        
                }
                else {
                    $deleteHoso = DB::table('qlhs_hosobaocao')->where('report_type', 'LIKE', '%'.'MGHP-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time.'%')->delete();
                    $deleteMGHP = DB::table('qlhs_miengiamhocphi')->where('type_code', 'LIKE', '%'.'MGHP-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time.'%')->delete();
                }
            }else{
                    
            }

            return $insert_returnID;
        }
        else {
            return 0;
        }
    }

    public function exportforSchoolsMGHP($id, $type = true){
        $this->exportforSchool($id, $type);
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
            $data_nghilc['aCount'] = $this->countValueMGHP('1',$getSchoolName->report_type);
            $data_nghilc['bCount'] = $this->countValueMGHP('2',$getSchoolName->report_type);
            $data_nghilc['cCount'] = $this->countValueMGHP('3',$getSchoolName->report_type);
            $data_nghilc['TotalCount'] = $this->countValueMGHP(null, $getSchoolName->report_type);
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
        $this->addCellExcelMGHP($data_nghilc, $getSchoolName->report_type, $type);
    }

    private function addCellExcelMGHP($data_nghilc,$filename,$type=true){
        $excel =    Excel::load(storage_path().'/exceltemplate/laphosoMGHP.xlsx', function($reader) use($data_nghilc){
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

            $reader->getActiveSheet()->setCellValueByColumnAndRow(23, 4, 'Học kỳ II năm học '.($data_nghilc['report_year'] - 1).'-'.$data_nghilc['report_year'])->getStyle('X4')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(24, 4, 'Học kỳ I năm học '.$data_nghilc['report_year'].'-'.($data_nghilc['report_year'] + 1))->getStyle('Y4')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(25, 4, 'Học kỳ II năm học '.$data_nghilc['report_year'].'-'.($data_nghilc['report_year'] + 1))->getStyle('Z4')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(26, 4, 'Học kỳ I năm học '.($data_nghilc['report_year'] + 1).'-'.($data_nghilc['report_year'] + 2))->getStyle('AA4')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

            $reader->getActiveSheet()->setCellValueByColumnAndRow(27, 3, 'Năm học '.($data_nghilc['report_year'] - 1).'-'.$data_nghilc['report_year'])->getStyle('AB3')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(28, 3, 'Năm học '.$data_nghilc['report_year'].'-'.($data_nghilc['report_year'] + 1).', Năm học '.($data_nghilc['report_year'] + 1).'-'.($data_nghilc['report_year'] + 2))->getStyle('AC3')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

            $reader->getActiveSheet()->setCellValueByColumnAndRow(29, 2, 'Nhu cầu kinh phí năm '.$data_nghilc['report_year'])->getStyle('AD2')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(30, 2, 'Dự toán kinh phí năm '.($data_nghilc['report_year'] + 1))->getStyle('AE2')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
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
            $reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row,$data_nghilc['TotalCount']->tongmien6)->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row,$data_nghilc['TotalCount']->tonggiam70)->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row,$data_nghilc['TotalCount']->tonggiam501)->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row,$data_nghilc['TotalCount']->tonggiam502)->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row,$data_nghilc['TotalCount']->tonghk12)->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(24, $row,$data_nghilc['TotalCount']->tonghk21)->getStyle('Y'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(25, $row,$data_nghilc['TotalCount']->tonghk22)->getStyle('Z'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(26, $row,$data_nghilc['TotalCount']->tonghk31)->getStyle('AA'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(27, $row, '')->getStyle('AB'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(28, $row, '')->getStyle('AC'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(29, $row,$data_nghilc['TotalCount']->tongnc)->getStyle('AD'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight)->applyFromArray($FontArrayitalic)->applyFromArray($FormatCurrency);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(30, $row,$data_nghilc['TotalCount']->tongdt)->getStyle('AE'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight)->applyFromArray($FontArrayitalic);

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
            $reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row,$data_nghilc['aCount']->tongmien6)->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row,$data_nghilc['aCount']->tonggiam70)->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row,$data_nghilc['aCount']->tonggiam501)->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row,$data_nghilc['aCount']->tonggiam502)->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row,$data_nghilc['aCount']->tonghk12)->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(24, $row,$data_nghilc['aCount']->tonghk21)->getStyle('Y'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(25, $row,$data_nghilc['aCount']->tonghk22)->getStyle('Z'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(26, $row,$data_nghilc['aCount']->tonghk31)->getStyle('AA'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(27, $row, '')->getStyle('AB'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(28, $row, '')->getStyle('AC'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(29, $row,$data_nghilc['aCount']->tongnc)->getStyle('AD'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(30, $row,$data_nghilc['aCount']->tongdt)->getStyle('AE'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
            $indexa = 0;
            if($data_nghilc['a']->count()>0){
                foreach($data_nghilc['a'] as $key => $value){
                    $col = 0;   $row++;

                    // $strYear = substr((string)$value->history_year, 0, 4);
                    // if ($strYear < $data_nghilc['report_year']) {
                    //  $class_lv1 = $value->level_next_1;
                    //  $class_lv2 = $value->level_next_2;
                    //  $class_lv3 = $value->level_next_3;
                    // }

                    // if ($strYear == $data_nghilc['report_year']) {
                    //  $class_lv1 = $value->level_next_1;
                    //  $class_lv2 = $value->level_next_2;
                    //  $class_lv3 = $value->level_next_3;
                    // }

                    // if ($strYear > $data_nghilc['report_year']) {
                    //  $class_lv1 = 0;
                    //  $class_lv2 = 0;
                    //  $class_lv3 = $value->level_next_1;
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
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->mienphi_6)->getStyle('T'.$row)->applyFromArray($borderArray)->applyFromArray($style);        
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->giam_70)->getStyle('U'.$row)->applyFromArray($borderArray)->applyFromArray($style);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->giam_50_1)->getStyle('V'.$row)->applyFromArray($borderArray)->applyFromArray($style);        
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->giam_50_2)->getStyle('W'.$row)->applyFromArray($borderArray)->applyFromArray($style);        
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_old)->getStyle('X'.$row)->applyFromArray($borderArray)->applyFromArray($style);       
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_cur)->getStyle('Y'.$row)->applyFromArray($borderArray)->applyFromArray($style);       
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_cur)->getStyle('Z'.$row)->applyFromArray($borderArray)->applyFromArray($style);       
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_new)->getStyle('AA'.$row)->applyFromArray($borderArray)->applyFromArray($style);       
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocphi_old)->getStyle('AB'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);         
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocphi_new)->getStyle('AC'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);         
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->nhu_cau)->getStyle('AD'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);        
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->du_toan)->getStyle('AE'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);        
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
            $reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row,$data_nghilc['bCount']->tongmien6)->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row,$data_nghilc['bCount']->tonggiam70)->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row,$data_nghilc['bCount']->tonggiam501)->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row,$data_nghilc['bCount']->tonggiam502)->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row,$data_nghilc['bCount']->tonghk12)->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(24, $row,$data_nghilc['bCount']->tonghk21)->getStyle('Y'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(25, $row,$data_nghilc['bCount']->tonghk22)->getStyle('Z'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(26, $row,$data_nghilc['bCount']->tonghk31)->getStyle('AA'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(27, $row, '')->getStyle('AB'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(28, $row, '')->getStyle('AC'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(29, $row,$data_nghilc['bCount']->tongnc)->getStyle('AD'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(30, $row,$data_nghilc['bCount']->tongdt)->getStyle('AE'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
            $indexb = 0;

            if($data_nghilc['b']->count()>0){
                foreach($data_nghilc['b'] as $key => $value){
                    $col = 0;   $row++;

                    // $strYear = substr((string)$value->history_year, 0, 4);
                    // if ($strYear < $data_nghilc['report_year']) {
                    //  $class_lv1 = $value->level_next;
                    //  $class_lv2 = $value->level_next_1;
                    //  $class_lv3 = $value->level_next_2;
                    // }

                    // if ($strYear == $data_nghilc['report_year']) {
                    //  $class_lv1 = 0;
                    //  $class_lv2 = $value->level_next_1;
                    //  $class_lv3 = $value->level_next_2;
                    // }

                    // if ($strYear > $data_nghilc['report_year']) {
                    //  $class_lv1 = 0;
                    //  $class_lv2 = 0;
                    //  $class_lv3 = $value->level_next_1;
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
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->mienphi_6)->getStyle('T'.$row)->applyFromArray($borderArray)->applyFromArray($style);     
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->giam_70)->getStyle('U'.$row)->applyFromArray($borderArray)->applyFromArray($style);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->giam_50_1)->getStyle('V'.$row)->applyFromArray($borderArray)->applyFromArray($style);        
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->giam_50_2)->getStyle('W'.$row)->applyFromArray($borderArray)->applyFromArray($style);        
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_old)->getStyle('X'.$row)->applyFromArray($borderArray)->applyFromArray($style);       
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_cur)->getStyle('Y'.$row)->applyFromArray($borderArray)->applyFromArray($style);       
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_cur)->getStyle('Z'.$row)->applyFromArray($borderArray)->applyFromArray($style);       
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_new)->getStyle('AA'.$row)->applyFromArray($borderArray)->applyFromArray($style);       
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocphi_old)->getStyle('AB'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);         
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocphi_new)->getStyle('AC'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);         
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->nhu_cau)->getStyle('AD'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);        
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->du_toan)->getStyle('AE'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);        
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
            $reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row,$data_nghilc['cCount']->tongmien6)->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row,$data_nghilc['cCount']->tonggiam70)->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row,$data_nghilc['cCount']->tonggiam501)->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row,$data_nghilc['cCount']->tonggiam502)->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row,$data_nghilc['cCount']->tonghk12)->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(24, $row,$data_nghilc['cCount']->tonghk21)->getStyle('Y'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(25, $row,$data_nghilc['cCount']->tonghk22)->getStyle('Z'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(26, $row,$data_nghilc['cCount']->tonghk31)->getStyle('AA'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(27, $row, '')->getStyle('AB'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(28, $row, '')->getStyle('AC'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(29, $row,$data_nghilc['cCount']->tongnc)->getStyle('AD'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(30, $row,$data_nghilc['cCount']->tongdt)->getStyle('AE'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
            $indexc = 0;
            if($data_nghilc['c']->count()>0){
                foreach($data_nghilc['c'] as $key => $value){
                    $col = 0;   $row++;

                    // $strYear = substr((string)$value->history_year, 0, 4);
                    // if ($strYear < $data_nghilc['report_year']) {
                    //  $class_lv1 = $value->level_next;
                    //  $class_lv2 = $value->level_next_1;
                    //  $class_lv3 = $value->level_next_2;
                    // }

                    // if ($strYear == $data_nghilc['report_year']) {
                    //  $class_lv1 = 0;
                    //  $class_lv2 = $value->level_next;
                    //  $class_lv3 = $value->level_next_1;
                    // }

                    // if ($strYear > $data_nghilc['report_year']) {
                    //  $class_lv1 = 0;
                    //  $class_lv2 = 0;
                    //  $class_lv3 = $value->level_next_1;
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
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->mienphi_6)->getStyle('T'.$row)->applyFromArray($borderArray)->applyFromArray($style);       
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->giam_70)->getStyle('U'.$row)->applyFromArray($borderArray)->applyFromArray($style);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->giam_50_1)->getStyle('V'.$row)->applyFromArray($borderArray)->applyFromArray($style);        
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->giam_50_2)->getStyle('W'.$row)->applyFromArray($borderArray)->applyFromArray($style);        
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_old)->getStyle('X'.$row)->applyFromArray($borderArray)->applyFromArray($style);       
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_cur)->getStyle('Y'.$row)->applyFromArray($borderArray)->applyFromArray($style);       
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_cur)->getStyle('Z'.$row)->applyFromArray($borderArray)->applyFromArray($style);       
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_new)->getStyle('AA'.$row)->applyFromArray($borderArray)->applyFromArray($style);       
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocphi_old)->getStyle('AB'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);         
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocphi_new)->getStyle('AC'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);         
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->nhu_cau)->getStyle('AD'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);        
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->du_toan)->getStyle('AE'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);        
                    }
                }
            });
        if($type){
            $excel->setFilename($filename)->store('xlsx', storage_path().'/exceldownload/MGHP');
        }else{
            $excel->setFilename($filename)->download('xlsx');
        }
    }
    
    public function countValueMGHP($type = null,$code){

        if($type!=null){
            $count = DB::table('qlhs_miengiamhocphi')->where('type_code','=',$code)->where('type','=',$type)->select(DB::raw('sum(mienphi_1) as tongmien1'),DB::raw('sum(mienphi_2) as tongmien2'),DB::raw('sum(mienphi_3) as tongmien3'),DB::raw('sum(mienphi_4) as tongmien4'),DB::raw('sum(mienphi_5) as tongmien5'),DB::raw('sum(mienphi_6) as tongmien6'),DB::raw('sum(giam_70) as tonggiam70'),DB::raw('sum(giam_50_1) as tonggiam501'),DB::raw('sum(giam_50_2) as tonggiam502'),DB::raw('sum(hocky2_old) as tonghk12'),DB::raw('sum(hocky1_cur) as tonghk21'),DB::raw('sum(hocky2_cur) as tonghk22'),DB::raw('sum(hocky1_new) as tonghk31'),DB::raw('sum(nhu_cau) as tongnc'),DB::raw('sum(du_toan) as tongdt'))->first();
            return $count;
        }else{
            $count = DB::table('qlhs_miengiamhocphi')->where('type_code','=',$code)->select(DB::raw('sum(mienphi_1) as tongmien1'),DB::raw('sum(mienphi_2) as tongmien2'),DB::raw('sum(mienphi_3) as tongmien3'),DB::raw('sum(mienphi_4) as tongmien4'),DB::raw('sum(mienphi_5) as tongmien5'),DB::raw('sum(mienphi_6) as tongmien6'),DB::raw('sum(giam_70) as tonggiam70'),DB::raw('sum(giam_50_1) as tonggiam501'),DB::raw('sum(giam_50_2) as tonggiam502'),DB::raw('sum(hocky2_old) as tonghk12'),DB::raw('sum(hocky1_cur) as tonghk21'),DB::raw('sum(hocky2_cur) as tonghk22'),DB::raw('sum(hocky1_new) as tonghk31'),DB::raw('sum(nhu_cau) as tongnc'),DB::raw('sum(du_toan) as tongdt'))->first();
            return $count;
        }
    }

//---------------------------------------------------------------------CPHT------------------------------------------------------------------------
    public function chiphihoctap($truong, $namhoc, $note, $report_name, $report_user_sign, $user_name, $status){
        try {
            $json = [];
            
            $user = Auth::user()->id;
            $now = Carbon::now('Asia/Ho_Chi_Minh');
           

            $data1 = DB::table('qlhs_profile')
            ->join('qlhs_profile_subject', 'profile_id', '=' , DB::raw('profile_subject_profile_id AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.$namhoc.' AND (qlhs_profile_subject.end_year is null OR qlhs_profile_subject.end_year > '.$namhoc.')'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$namhoc.'-'.($namhoc + 1).'"'))

            ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$truong.' AND kp.id_doituong = 92 AND '.($namhoc - 1).' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($namhoc + 1).''))
            ->leftJoin('qlhs_tonghopchedo as yearold', 'yearold.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearold.qlhs_thcd_nam = '.($namhoc - 1).' and (yearold.qlhs_thcd_trangthai_CPHT = 1 or yearold.qlhs_thcd_trangthai_CPHT_HK2 = 1)'))
            ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearcur.qlhs_thcd_nam = '.$namhoc.' and (yearcur.qlhs_thcd_trangthai_CPHT = 1 or yearcur.qlhs_thcd_trangthai_CPHT_HK2 = 1)'))
            ->leftJoin('qlhs_tonghopchedo as yearnew', 'yearnew.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearnew.qlhs_thcd_nam = '.($namhoc + 1)))
            
            ->select('profile_id','profile_name','profile_year', 'level_old','level_cur','level_new', 
                DB::raw('MAX(profile_subject_subject_id) as profile_subject_subject_id'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 28 then 1 else 0 END) DT1'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 73 then 1 else 0 END) DT2'),

                DB::raw("MAX(CASE when level_old <> '' and qlhs_profile.profile_year < '".$namhoc."-06-01' and profile_subject_subject_id in (28,73) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".$namhoc."-01-01')) then 1 else 0 END) 'HKII1'"),
                DB::raw("MAX(CASE when level_cur <> '' and qlhs_profile.profile_year < '".$namhoc."-12-31' and profile_subject_subject_id in (28,73) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".$namhoc."-05-31')) then 1 else 0 END) 'HKI2'"),
                DB::raw("MAX(CASE when level_cur <> '' and qlhs_profile.profile_year < '".($namhoc + 1)."-06-01' and profile_subject_subject_id in (28,73) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".((int)$namhoc+1)."-01-01')) then 1 else 0 END) 'HKII2'"),
                DB::raw("MAX(CASE when level_new <> '' and qlhs_profile.profile_year < '".($namhoc +1)."-12-31' and profile_subject_subject_id in (28,73) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".((int)$namhoc+1)."-05-31')) then 1 else 0 END) 'HKI3'"),
                DB::raw("MAX(
                    CASE
                        WHEN (level_old <> ''
                        AND qlhs_profile.profile_year < '".$namhoc."-06-01'
                        AND profile_subject_subject_id IN (28, 73)
                        AND (
                            profile_leaveschool_date IS NULL
                            OR (
                                profile_leaveschool_date > '".$namhoc."-01-01'
                            )
                        ) AND kp.months in (1,2,3,4,5) AND kp.years = ".$namhoc." AND yearold.qlhs_thcd_trangthai_HK2 = 1 AND yearold.qlhs_thcd_trangthai_CPHT_HK2 = 1) 
                        OR (level_cur <> ''
                        AND qlhs_profile.profile_year < '".$namhoc."-12-31'
                        AND profile_subject_subject_id IN (28, 73)
                        AND (
                            profile_leaveschool_date IS NULL
                            OR (
                                profile_leaveschool_date > '".$namhoc."-05-31'
                            )
                        ) AND kp.months in (9,10,11,12) AND kp.years = ".$namhoc." AND yearcur.qlhs_thcd_trangthai = 1 AND yearcur.qlhs_thcd_trangthai_CPHT = 1)  THEN
                        kp.value_m
                        ELSE
                            0
                        END
                    ) 'NhuCau2'"),
                DB::raw("MAX(
                    CASE
                        WHEN (level_cur <> ''
                        AND qlhs_profile.profile_year < '".($namhoc + 1)."-06-01'
                        AND profile_subject_subject_id IN (28, 73)
                        AND (
                            profile_leaveschool_date IS NULL
                            OR (
                                profile_leaveschool_date > '".($namhoc + 1)."-01-01'
                            )
                        ) AND kp.months in (1,2,3,4,5) AND kp.years = ".($namhoc + 1)." AND yearcur.qlhs_thcd_trangthai_HK2 = 1 AND yearcur.qlhs_thcd_trangthai_CPHT_HK2 = 1)  
                        OR (level_new <> ''
                        AND qlhs_profile.profile_year < '".($namhoc + 1)."-12-31'
                        AND profile_subject_subject_id IN (28, 73)
                        AND (
                            profile_leaveschool_date IS NULL
                            OR (
                                profile_leaveschool_date > '".($namhoc + 1)."-05-31'
                            )
                        ) AND kp.months in (9,10,11,12) AND kp.years = ".($namhoc + 1).")  THEN
                        kp.value_m
                        ELSE
                            0
                        END
                    ) 'DuToan2'"))
            
            ->where('profile_year','<',$namhoc.'-06-01')
            ->where('profile_school_id','=',$truong)
            ->whereIn('profile_subject_subject_id',[28,73])
            
            // ->where('yearcur.qlhs_thcd_nam', '=', DB::raw($namhoc.' and (yearcur.qlhs_thcd_trangthai_CPHT = 1 or yearcur.qlhs_thcd_trangthai_CPHT_HK2 = 1 or yearold.qlhs_thcd_trangthai_CPHT = 1 or yearold.qlhs_thcd_trangthai_CPHT_HK2 = 1)'))
            
            ->groupBy('profile_id','profile_name','profile_year','level_old','level_cur','level_new','kp.value_m','kp.months','kp.years');

            $data11 = DB::table(DB::raw("({$data1->toSql()}) as m"))->mergeBindings( $data1 )
            ->select('HKII1','HKI2','HKII2','HKI3','m.DT1','m.DT2','m.profile_id','m.profile_subject_subject_id','m.profile_name','m.profile_year','m.level_old','m.level_cur','m.level_new',
                DB::raw('SUM(NhuCau2) as NhuCau'),
                DB::raw('SUM(DuToan2) as DuToan'))
            ->groupBy('HKII1','HKI2','HKII2','HKI3','m.DT1','m.DT2','m.profile_id','m.profile_subject_subject_id','m.profile_name','m.profile_year','m.level_old','m.level_cur','m.level_new');

            $data2 = DB::table('qlhs_profile')
            ->join('qlhs_profile_subject', 'profile_id', '=' , DB::raw('profile_subject_profile_id AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.$namhoc.' AND (qlhs_profile_subject.end_year is null OR qlhs_profile_subject.end_year > '.$namhoc.')'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$namhoc.'-'.($namhoc + 1).'"'))

            ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$truong.' AND kp.id_doituong = 92 AND '.($namhoc - 1).' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($namhoc + 1).''))
            ->leftJoin('qlhs_tonghopchedo as yearold', 'yearold.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearold.qlhs_thcd_nam = '.($namhoc - 1).' and (yearold.qlhs_thcd_trangthai_CPHT = 1 or yearold.qlhs_thcd_trangthai_CPHT_HK2 = 1)'))
            ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearcur.qlhs_thcd_nam = '.$namhoc))
            ->leftJoin('qlhs_tonghopchedo as yearnew', 'yearnew.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearnew.qlhs_thcd_nam = '.($namhoc + 1)))
            
            ->select('profile_id','profile_name','profile_year', 'level_old','level_cur','level_new', 
                DB::raw('MAX(profile_subject_subject_id) as profile_subject_subject_id'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 28 then 1 else 0 END) DT1'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 73 then 1 else 0 END) DT2'),

                DB::raw("MAX(CASE when level_old <> '' and qlhs_profile.profile_year < '".$namhoc."-06-01' and profile_subject_subject_id in (28,73) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".$namhoc."-01-01')) then 1 else 0 END) 'HKII1'"),
                DB::raw("MAX(CASE when level_cur <> '' and qlhs_profile.profile_year < '".$namhoc."-12-31' and profile_subject_subject_id in (28,73) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".$namhoc."-05-31')) then 1 else 0 END) 'HKI2'"),
                DB::raw("MAX(CASE when level_cur <> '' and qlhs_profile.profile_year < '".($namhoc + 1)."-06-01' and profile_subject_subject_id in (28,73) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".((int)$namhoc+1)."-01-01')) then 1 else 0 END) 'HKII2'"),
                DB::raw("MAX(CASE when level_new <> '' and qlhs_profile.profile_year < '".($namhoc +1)."-12-31' and profile_subject_subject_id in (28,73) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".((int)$namhoc+1)."-05-31')) then 1 else 0 END) 'HKI3'"),
                DB::raw("MAX(
                    CASE
                        WHEN (level_old <> ''
                        AND qlhs_profile.profile_year < '".$namhoc."-06-01'
                        AND profile_subject_subject_id IN (28, 73)
                        AND (
                            profile_leaveschool_date IS NULL
                            OR (
                                profile_leaveschool_date > '".$namhoc."-01-01'
                            )
                        ) AND kp.months in (1,2,3,4,5) AND kp.years = ".$namhoc." AND yearold.qlhs_thcd_trangthai_HK2 = 1 AND yearold.qlhs_thcd_trangthai_CPHT_HK2 = 1) 
                        OR (level_cur <> ''
                        AND qlhs_profile.profile_year < '".$namhoc."-12-31'
                        AND profile_subject_subject_id IN (28, 73)
                        AND (
                            profile_leaveschool_date IS NULL
                            OR (
                                profile_leaveschool_date > '".$namhoc."-05-31'
                            )
                        ) AND kp.months in (9,10,11,12) AND kp.years = ".$namhoc." AND yearcur.qlhs_thcd_trangthai = 1 AND yearcur.qlhs_thcd_trangthai_CPHT = 1)  THEN
                        kp.value_m
                        ELSE
                            0
                        END
                    ) 'NhuCau2'"),
                DB::raw("MAX(
                    CASE
                        WHEN (level_cur <> ''
                        AND qlhs_profile.profile_year < '".($namhoc + 1)."-06-01'
                        AND profile_subject_subject_id IN (28, 73)
                        AND (
                            profile_leaveschool_date IS NULL
                            OR (
                                profile_leaveschool_date > '".($namhoc + 1)."-01-01'
                            )
                        ) AND kp.months in (1,2,3,4,5) AND kp.years = ".($namhoc + 1)." AND yearcur.qlhs_thcd_trangthai_HK2 = 1 AND yearcur.qlhs_thcd_trangthai_CPHT_HK2 = 1)  
                        OR (level_new <> ''
                        AND qlhs_profile.profile_year < '".($namhoc + 1)."-12-31'
                        AND profile_subject_subject_id IN (28, 73)
                        AND (
                            profile_leaveschool_date IS NULL
                            OR (
                                profile_leaveschool_date > '".($namhoc + 1)."-05-31'
                            )
                        ) AND kp.months in (9,10,11,12) AND kp.years = ".($namhoc + 1).")  THEN
                        kp.value_m
                        ELSE
                            0
                        END
                    ) 'DuToan2'"))
            
            ->where('profile_year','>',$namhoc.'-06-01')
            ->where('profile_year','<',($namhoc + 1).'-06-01')
            ->where('profile_school_id','=',$truong)
            ->whereIn('profile_subject_subject_id',[28,73])
            
            // ->where('yearcur.qlhs_thcd_nam', '=', DB::raw($namhoc.' and (yearcur.qlhs_thcd_trangthai_CPHT = 1 or yearcur.qlhs_thcd_trangthai_CPHT_HK2 = 1)'))
            
            ->groupBy('profile_id','profile_name','profile_year','level_old','level_cur','level_new','kp.value_m','kp.months','kp.years');

            $data22 = DB::table(DB::raw("({$data2->toSql()}) as m"))->mergeBindings( $data2 )
            ->select('HKII1','HKI2','HKII2','HKI3','m.DT1','m.DT2','m.profile_id','m.profile_subject_subject_id','m.profile_name','m.profile_year','m.level_old','m.level_cur','m.level_new',
                DB::raw('SUM(NhuCau2) as NhuCau'),
                DB::raw('SUM(DuToan2) as DuToan'))
            ->groupBy('HKII1','HKI2','HKII2','HKI3','m.DT1','m.DT2','m.profile_id','m.profile_subject_subject_id','m.profile_name','m.profile_year','m.level_old','m.level_cur','m.level_new');

            $data3 = DB::table('qlhs_profile')
            ->join('qlhs_profile_subject', 'profile_id', '=' , DB::raw('profile_subject_profile_id AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.($namhoc + 1).' AND (qlhs_profile_subject.end_year is null OR qlhs_profile_subject.end_year > '.($namhoc + 1).')'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.($namhoc + 1).'-'.($namhoc + 2).'"'))

            ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$truong.' AND kp.id_doituong = 92 AND '.$namhoc.' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($namhoc + 2).''))
            ->leftJoin('qlhs_tonghopchedo as yearold', 'yearold.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearold.qlhs_thcd_nam = '.$namhoc.''))
            ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearcur.qlhs_thcd_nam = '.($namhoc + 1)))
            ->leftJoin('qlhs_tonghopchedo as yearnew', 'yearnew.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearnew.qlhs_thcd_nam = '.($namhoc + 2).''))
            
            ->select('profile_id','profile_name','profile_year', 'level_old','level_cur','level_new', 
                DB::raw('MAX(profile_subject_subject_id) as profile_subject_subject_id'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 28 then 1 else 0 END) DT1'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 73 then 1 else 0 END) DT2'),

                DB::raw("MAX(CASE when level_old <> '' and qlhs_profile.profile_year < '".$namhoc."-06-01' and profile_subject_subject_id in (28,73) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".$namhoc."-01-01')) then 1 else 0 END) 'HKII1'"),
                DB::raw("MAX(CASE when level_cur <> '' and qlhs_profile.profile_year < '".$namhoc."-12-31' and profile_subject_subject_id in (28,73) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".$namhoc."-05-31')) then 1 else 0 END) 'HKI2'"),
                DB::raw("MAX(CASE when level_cur <> '' and qlhs_profile.profile_year < '".($namhoc + 1)."-06-01' and profile_subject_subject_id in (28,73) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".((int)$namhoc+1)."-01-01')) then 1 else 0 END) 'HKII2'"),
                DB::raw("MAX(CASE when level_new <> '' and qlhs_profile.profile_year < '".($namhoc +1)."-12-31' and profile_subject_subject_id in (28,73) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".((int)$namhoc+1)."-05-31')) then 1 else 0 END) 'HKI3'"),
                // DB::raw("MAX(
                //     CASE
                //         WHEN (level_old <> ''
                //         AND qlhs_profile.profile_year < '".($namhoc + 1)."-06-01'
                //         AND profile_subject_subject_id IN (28, 73)
                //         AND (
                //             profile_leaveschool_date IS NULL
                //             OR (
                //                 profile_leaveschool_date > '".($namhoc + 1)."-01-01'
                //             )
                //         ) AND kp.months in (1,2,3,4,5) AND kp.years = ".($namhoc + 1).") 
                //         OR (level_cur <> ''
                //         AND qlhs_profile.profile_year < '".($namhoc + 1)."-12-31'
                //         AND profile_subject_subject_id IN (28, 73)
                //         AND (
                //             profile_leaveschool_date IS NULL
                //             OR (
                //                 profile_leaveschool_date > '".($namhoc + 1)."-05-31'
                //             )
                //         ) AND kp.months in (9,10,11,12) AND kp.years = ".($namhoc + 1).")  THEN
                //         kp.value_m
                //         ELSE
                //             0
                //         END
                //     ) 'NhuCau2'"),
                DB::raw("MAX(
                    CASE
                        WHEN (level_cur <> ''
                        AND qlhs_profile.profile_year < '".($namhoc + 2)."-12-31'
                        AND profile_subject_subject_id IN (28, 73)
                        AND (
                            profile_leaveschool_date IS NULL
                            OR (
                                profile_leaveschool_date > '".($namhoc + 2)."-05-31'
                            )
                        ) AND kp.months in (9,10,11,12) AND kp.years = ".($namhoc + 2).")  THEN
                        kp.value_m
                        ELSE
                            0
                        END
                    ) 'DuToan2'"))
            
            ->where('profile_year','>', ($namhoc + 1).'-06-01')
            ->where('profile_year','<', ($namhoc + 2).'-06-01')
            ->where('profile_school_id','=', $truong)
            ->whereIn('profile_subject_subject_id', [28,73])
            
            // ->where('yearcur.qlhs_thcd_nam', '=', ($namhoc + 1))
            
            ->groupBy('profile_id','profile_name','profile_year','level_old','level_cur','level_new','kp.value_m','kp.months','kp.years');

            $data33 = DB::table(DB::raw("({$data3->toSql()}) as m"))->mergeBindings( $data3 )
            ->select('HKII1','HKI2','HKII2','HKI3','m.DT1','m.DT2','m.profile_id','m.profile_subject_subject_id','m.profile_name','m.profile_year','m.level_old','m.level_cur','m.level_new',
                // DB::raw('SUM(NhuCau2) as NhuCau'),
                DB::raw('SUM(DuToan2) as DuToan'))
            ->groupBy('HKII1','HKI2','HKII2','HKI3','m.DT1','m.DT2','m.profile_id','m.profile_subject_subject_id','m.profile_name','m.profile_year','m.level_old','m.level_cur','m.level_new');

            if($data11->count()==0 && $data22->count()==0 && $data33->count()==0){
                
            }else{
                $import =  $this->insertReportCPHT($data11,$data22,$data33,$truong,$namhoc,$user,$now,$report_name,$report_user_sign,$user_name,$status,$note);
                
            }
        } catch (Exception $e) {
            return $e;
        }
        // return $json;
    }

    public function insertReportCPHT($data11,$data22,$data33,$truong,$namhoc,$user,$now,$report_name,$report_user_sign,$user_name,$status,$note){
        $json = [];
        $time = time();
        $dir = storage_path().'/files/CPHT';
        if($data11->count() > 0){
            foreach ($data11->get() as $key => $value) {
                if ($value->NhuCau > 0 || $value->DuToan > 0) {
                    $json['1'] = DB::table('qlhs_chiphihoctap')->insert([
                        'cpht_profile_id' => $value->profile_id,
                        'cpht_doituong1' => $value->DT1,
                        'cpht_doituong2' => $value->DT2,
                        'hocky2_old' => $value->HKII1,
                        'hocky1_cur' => $value->HKI2,
                        'hocky2_cur' => $value->HKII2,
                        'hocky1_new' => $value->HKI3,
                        // 'ho_tro' => $value->money1,
                        'nhu_cau' => $value->NhuCau,
                        'du_toan' => $value->DuToan,
                        'year_old' => (int)$namhoc,
                        'year_cur' => (int)$namhoc+1,
                        'type' => 1,
                        'type_code' => 'CPHT-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time,
                        'trangthai_pheduyet_CPHT' => 1,
                        'trangthai_thamdinh_CPHT' => 1
                        ]);
                }
                $json['1'] = 2;
            }
        }else{
            $json['1'] = 2;
        }
        if($data22->count()>0){
            foreach ($data22->get() as $key => $value) {
                if ($value->NhuCau > 0 || $value->DuToan > 0) {
                    $json['2'] = DB::table('qlhs_chiphihoctap')->insert([
                        'cpht_profile_id' => $value->profile_id,
                        'cpht_doituong1' => $value->DT1,
                        'cpht_doituong2' => $value->DT2,
                        'hocky2_old' => $value->HKII1,
                        'hocky1_cur' => $value->HKI2,
                        'hocky2_cur' => $value->HKII2,
                        'hocky1_new' => $value->HKI3,
                        // 'ho_tro' => $value->money1,
                        'nhu_cau' => $value->NhuCau,
                        'du_toan' => $value->DuToan,
                        'year_old' => (int)$namhoc,
                        'year_cur' => (int)$namhoc+1,
                        'type' => 2,
                        'type_code' => 'CPHT-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time,
                        'trangthai_pheduyet_CPHT' => 1,
                        'trangthai_thamdinh_CPHT' => 1
                        ]);
                }
                $json['2'] = 2;
            }
        }else{
            $json['2'] = 2;
        }
        if($data33->count()>0){
            foreach ($data33->get() as $key => $value) {
                if ($value->DuToan > 0) {
                    $json['3'] = DB::table('qlhs_chiphihoctap')->insert([
                        'cpht_profile_id' => $value->profile_id,
                        'cpht_doituong1' => $value->DT1,
                        'cpht_doituong2' => $value->DT2,
                        'hocky2_old' => $value->HKII1,
                        'hocky1_cur' => $value->HKI2,
                        'hocky2_cur' => $value->HKII2,
                        'hocky1_new' => $value->HKI3,
                        // 'ho_tro' => $value->money1,
                        'nhu_cau' => 0,//$value->NhuCau,
                        'du_toan' => $value->DuToan,
                        'year_old' => (int)$namhoc,
                        'year_cur' => (int)$namhoc+1,
                        'type' => 3,
                        'type_code' => 'CPHT-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time,
                        'trangthai_pheduyet_CPHT' => 1,
                        'trangthai_thamdinh_CPHT' => 1
                        ]);
                }
                $json['3'] = 2;
            }

        }else{
            $json['3'] = 2;
        }
        
        if((int)$json['1']> 0 && (int)$json['2']>0 && (int)$json['3']>0){
            
              
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
                    'report_attach_name' => '',
                    'report_nature' => $status,
                    'report_year' => $namhoc,
                    'report_id_truong' => $truong,
                    'report_note' => $note,
                    'report' => 'CPHT'
                ]);
                
                if(!is_null($insert_returnID) && $insert_returnID > 0 ){
                    $this->exportforSchoolsCPHT($insert_returnID);
                    if (file_exists(storage_path().'/exceldownload/CPHT/'.'CPHT-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time.'.xlsx')) {
                        return TRUE;
                    }
                    else {
                        $deleteHoso = DB::table('qlhs_hosobaocao')->where('report_type', 'LIKE', '%'.'CPHT-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time.'%')->delete();
                        $deleteCPHT = DB::table('qlhs_chiphihoctap')->where('type_code', 'LIKE', '%'.'CPHT-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time.'%')->delete();
                        return false;
                    }
                }else{
                    
                }
            return $insert_returnID;
        }
        else {
            return 0;
        }
    }

    public function exportforSchoolsCPHT($id, $type = true){
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
            $data_results['aCount'] = $this->countValueCPHT('1',$getSchoolName->report_type);
            $data_results['bCount'] = $this->countValueCPHT('2',$getSchoolName->report_type);
            $data_results['cCount'] = $this->countValueCPHT('3',$getSchoolName->report_type);
            $data_results['TotalCount'] = $this->countValueCPHT(null,$getSchoolName->report_type);
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
        $this->addCellExcelCPHT($data_results, $getSchoolName->report_type, $type);
    }

    private function addCellExcelCPHT($data_results, $filename, $type = true){
        $excel =    Excel::load(storage_path().'/exceltemplate/laphosoCPHT.xlsx', function($reader) use($data_results){
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
                    $col = 0;   $row++;

                    // $strYear = substr((string)$value->history_year, 0, 4);
                    // if ($strYear < $data_results['report_year']) {
                    //  $class_lv1 = $value->level_next_1;
                    //  $class_lv2 = $value->level_next_2;
                    //  $class_lv3 = $value->level_next_3;
                    // }

                    // if ($strYear == $data_results['report_year']) {
                    //  $class_lv1 = $value->level_next;
                    //  $class_lv2 = $value->level_next_2;
                    //  $class_lv3 = $value->level_next_3;
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
                    $col = 0;   $row++;

                    // $strYear = substr((string)$value->history_year, 0, 4);
                    // if ($strYear < $data_results['report_year']) {
                    //  $class_lv1 = $value->level_next;
                    //  $class_lv2 = $value->level_next_1;
                    //  $class_lv3 = $value->level_next_2;
                    // }

                    // if ($strYear == $data_results['report_year']) {
                    //  $class_lv1 = 0;
                    //  $class_lv2 = $value->level_next_1;
                    //  $class_lv3 = $value->level_next_2;
                    // }

                    // if ($strYear > $data_results['report_year']) {
                    //  $class_lv1 = 0;
                    //  $class_lv2 = 0;
                    //  $class_lv3 = $value->level_next_1;
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
                    $col = 0;   $row++;

                    // $strYear = substr((string)$value->history_year, 0, 4);
                    // if ($strYear < $data_results['report_year']) {
                    //  $class_lv1 = $value->level_next;
                    //  $class_lv2 = $value->level_next_1;
                    //  $class_lv3 = $value->level_next_2;
                    // }

                    // if ($strYear == $data_results['report_year']) {
                    //  $class_lv1 = 0;
                    //  $class_lv2 = $value->level_next_1;
                    //  $class_lv3 = $value->level_next_2;
                    // }

                    // if ($strYear > $data_results['report_year']) {
                    //  $class_lv1 = 0;
                    //  $class_lv2 = 0;
                    //  $class_lv3 = $value->level_next_1;
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
    
    public function countValueCPHT($type = null,$code){

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

//---------------------------------------------------------------------HTAT------------------------------------------------------------------------
    public function hoTroAnTruaTreEm($truong, $namhoc, $note, $report_name, $report_user_sign, $user_name, $status){
        $json = [];
        
        $user = Auth::user()->id;
        $now = Carbon::now('Asia/Ho_Chi_Minh');
        

        $data1 = DB::table('qlhs_profile')
        ->join('qlhs_profile_subject', 'profile_id', '=' , DB::raw('profile_subject_profile_id AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.$namhoc.' AND (qlhs_profile_subject.end_year is null OR qlhs_profile_subject.end_year > '.$namhoc.')'))
        ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$namhoc.'-'.($namhoc + 1).'"'))
        ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$truong.' AND kp.id_doituong = 93 AND '.($namhoc - 1).' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($namhoc + 1).''))
        ->leftJoin('qlhs_tonghopchedo as yearold', 'yearold.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearold.qlhs_thcd_nam = '.($namhoc - 1).' and (yearold.qlhs_thcd_trangthai_HTAT = 1 or yearold.qlhs_thcd_trangthai_HTAT_HK2 = 1)'))
        // ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', 'profile_id')
        ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearcur.qlhs_thcd_nam = '.$namhoc.' and (yearcur.qlhs_thcd_trangthai_HTAT = 1 or yearcur.qlhs_thcd_trangthai_HTAT_HK2 = 1)'))
        ->leftJoin('qlhs_tonghopchedo as yearnew', 'yearnew.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearnew.qlhs_thcd_nam = '.($namhoc + 1)))
        ->select('profile_id','profile_name','profile_year', 'level_old','level_cur','level_new', 
            DB::raw('MAX(profile_subject_subject_id) as profile_subject_subject_id'),
            DB::raw('MAX(CASE when profile_subject_subject_id = 26 or profile_subject_subject_id = 34 then 1 else 0 END) DT1'),
            DB::raw('MAX(CASE when profile_subject_subject_id = 41 then 1 else 0 END) DT2'),
            DB::raw('MAX(CASE when profile_subject_subject_id = 74 then 1 else 0 END) DT2_1'),
            DB::raw('MAX(CASE when profile_subject_subject_id = 28 then 1 else 0 END) DT3'),
            DB::raw('MAX(CASE when profile_subject_subject_id = 73 then 1 else 0 END) DT4'),

            DB::raw("MAX(CASE when level_old <> '' and qlhs_profile.profile_year < '".$namhoc."-06-01' and profile_subject_subject_id in (73,26,34,28,41,74) and (profile_leaveschool_date is null or (profile_leaveschool_date >= '".$namhoc."-01-01')) then 1 else 0 END) 'HKII1'"),
            DB::raw("MAX(CASE when level_cur <> '' and qlhs_profile.profile_year < '".$namhoc."-12-31' and profile_subject_subject_id in (73,26,34,28,41,74) and (profile_leaveschool_date is null or (profile_leaveschool_date >= '".$namhoc."-05-31')) then 1 else 0 END) 'HKI2'"),
            DB::raw("MAX(CASE when level_cur <> '' and qlhs_profile.profile_year < '".($namhoc +1)."-06-01' and profile_subject_subject_id in (73,26,34,28,41,74) and (profile_leaveschool_date is null or (profile_leaveschool_date >= '".((int)$namhoc+1)."-01-01')) then 1 else 0 END) 'HKII2'"),
            DB::raw("MAX(CASE when level_new <> '' and qlhs_profile.profile_year < '".($namhoc +1)."-12-31' and profile_subject_subject_id in (73,26,34,28,41,74) and (profile_leaveschool_date is null or (profile_leaveschool_date >= '".((int)$namhoc+1)."-05-31')) then 1 else 0 END) 'HKI3'"),
                DB::raw("MAX(
                    CASE
                        WHEN (level_old <> ''
                        AND qlhs_profile.profile_year < '".$namhoc."-06-01'
                        AND profile_subject_subject_id IN (73,26,34,28,41,74)
                        AND (
                            profile_leaveschool_date IS NULL
                            OR (
                                profile_leaveschool_date > '".$namhoc."-01-01'
                            )
                        ) AND kp.months in (1,2,3,4,5) AND kp.years = ".$namhoc." AND yearold.qlhs_thcd_trangthai_HK2 = 1 AND yearold.qlhs_thcd_trangthai_HTAT_HK2 = 1) 
                        OR (level_cur <> ''
                        AND qlhs_profile.profile_year < '".$namhoc."-12-31'
                        AND profile_subject_subject_id IN (73,26,34,28,41,74)
                        AND (
                            profile_leaveschool_date IS NULL
                            OR (
                                profile_leaveschool_date > '".$namhoc."-05-31'
                            )
                        ) AND kp.months in (9,10,11,12) AND kp.years = ".$namhoc." AND yearcur.qlhs_thcd_trangthai = 1 AND yearcur.qlhs_thcd_trangthai_HTAT = 1)  THEN
                        kp.value_m
                        ELSE
                            0
                        END
                    ) 'NhuCau2'"),
                DB::raw("MAX(
                    CASE
                        WHEN (level_cur <> ''
                        AND qlhs_profile.profile_year < '".($namhoc + 1)."-06-01'
                        AND profile_subject_subject_id IN (73,26,34,28,41,74)
                        AND (
                            profile_leaveschool_date IS NULL
                            OR (
                                profile_leaveschool_date > '".($namhoc + 1)."-01-01'
                            )
                        ) AND kp.months in (1,2,3,4,5) AND kp.years = ".($namhoc + 1)." AND yearcur.qlhs_thcd_trangthai_HK2 = 1 AND yearcur.qlhs_thcd_trangthai_HTAT_HK2 = 1) 
                        OR (level_new <> ''
                        AND qlhs_profile.profile_year < '".($namhoc + 1)."-12-31'
                        AND profile_subject_subject_id IN (73,26,34,28,41,74)
                        AND (
                            profile_leaveschool_date IS NULL
                            OR (
                                profile_leaveschool_date > '".($namhoc + 1)."-05-31'
                            )
                        ) AND kp.months in (9,10,11,12) AND kp.years = ".($namhoc + 1).") THEN
                        kp.value_m
                        ELSE
                            0
                        END
                    ) 'DuToan2'"))

        ->where('profile_year','<',$namhoc.'-06-01')
        ->where('profile_school_id','=',$truong)
        ->whereIn('profile_subject_subject_id',[73,26,34,28,41,74])
        // ->where('yearcur.qlhs_thcd_nam', '=', DB::raw($namhoc.' and (yearcur.qlhs_thcd_trangthai_HTAT = 1 or yearcur.qlhs_thcd_trangthai_HTAT_HK2 = 1 or yearold.qlhs_thcd_trangthai_HTAT = 1 or yearold.qlhs_thcd_trangthai_HTAT_HK2 = 1)'))
        ->groupBy('profile_id','profile_name','profile_year','level_old','level_cur','level_new','kp.value_m','kp.months','kp.years');

        $data11 = DB::table(DB::raw("({$data1->toSql()}) as m"))->mergeBindings( $data1 )
        ->select('HKII1','HKI2','HKII2','HKI3','m.DT1','m.DT2','m.DT2_1','m.DT3','m.DT4','m.profile_id','m.profile_subject_subject_id','m.profile_name','m.profile_year','m.level_old','m.level_cur','m.level_new',
            DB::raw('SUM(NhuCau2) as NhuCau'),
            DB::raw('SUM(DuToan2) as DuToan'))
        ->groupBy('HKII1','HKI2','HKII2','HKI3','m.DT1','m.DT2','m.DT2_1','m.DT3','m.DT4','m.profile_id','m.profile_subject_subject_id','m.profile_name','m.profile_year','m.level_old','m.level_cur','m.level_new');


        $data2 = DB::table('qlhs_profile')
        ->join('qlhs_profile_subject', 'profile_id', '=' , DB::raw('profile_subject_profile_id AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.$namhoc.' AND (qlhs_profile_subject.end_year is null OR qlhs_profile_subject.end_year > '.$namhoc.')'))
        ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$namhoc.'-'.($namhoc + 1).'"'))
        ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$truong.' AND kp.id_doituong = 93 AND '.($namhoc - 1).' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($namhoc + 1).''))
        ->leftJoin('qlhs_tonghopchedo as yearold', 'yearold.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearold.qlhs_thcd_nam = '.($namhoc - 1).' and (yearold.qlhs_thcd_trangthai_HTAT = 1 or yearold.qlhs_thcd_trangthai_HTAT_HK2 = 1)'))
        ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearcur.qlhs_thcd_nam = '.$namhoc))
        ->leftJoin('qlhs_tonghopchedo as yearnew', 'yearnew.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearnew.qlhs_thcd_nam = '.($namhoc + 1)))
        ->select('profile_id','profile_name','profile_year', 'level_old','level_cur','level_new', 
            DB::raw('MAX(profile_subject_subject_id) as profile_subject_subject_id'),
            DB::raw('MAX(CASE when profile_subject_subject_id = 26 or profile_subject_subject_id = 34 then 1 else 0 END) DT1'),
            DB::raw('MAX(CASE when profile_subject_subject_id = 41 then 1 else 0 END) DT2'),
            DB::raw('MAX(CASE when profile_subject_subject_id = 74 then 1 else 0 END) DT2_1'),
            DB::raw('MAX(CASE when profile_subject_subject_id = 28 then 1 else 0 END) DT3'),
            DB::raw('MAX(CASE when profile_subject_subject_id = 73 then 1 else 0 END) DT4'),

            DB::raw("MAX(CASE when level_old <> '' and qlhs_profile.profile_year < '".$namhoc."-06-01' and profile_subject_subject_id in (73,26,34,28,41,74) and (profile_leaveschool_date is null or (profile_leaveschool_date >= '".$namhoc."-01-01')) then 1 else 0 END) 'HKII1'"),
            DB::raw("MAX(CASE when level_cur <> '' and qlhs_profile.profile_year < '".$namhoc."-12-31' and profile_subject_subject_id in (73,26,34,28,41,74) and (profile_leaveschool_date is null or (profile_leaveschool_date >= '".$namhoc."-05-31')) then 1 else 0 END) 'HKI2'"),
            DB::raw("MAX(CASE when level_cur <> '' and qlhs_profile.profile_year < '".($namhoc +1)."-06-01' and profile_subject_subject_id in (73,26,34,28,41,74) and (profile_leaveschool_date is null or (profile_leaveschool_date >= '".((int)$namhoc+1)."-01-01')) then 1 else 0 END) 'HKII2'"),
            DB::raw("MAX(CASE when level_new <> '' and qlhs_profile.profile_year < '".($namhoc +1)."-12-31' and profile_subject_subject_id in (73,26,34,28,41,74) and (profile_leaveschool_date is null or (profile_leaveschool_date >= '".((int)$namhoc+1)."-05-31')) then 1 else 0 END) 'HKI3'"),
                DB::raw("MAX(
                    CASE
                        WHEN (level_old <> ''
                        AND qlhs_profile.profile_year < '".$namhoc."-06-01'
                        AND profile_subject_subject_id IN (73,26,34,28,41,74)
                        AND (
                            profile_leaveschool_date IS NULL
                            OR (
                                profile_leaveschool_date > '".$namhoc."-01-01'
                            )
                        ) AND kp.months in (1,2,3,4,5) AND kp.years = ".$namhoc." AND yearold.qlhs_thcd_trangthai_HK2 = 1 AND yearold.qlhs_thcd_trangthai_HTAT_HK2 = 1) 
                        OR (level_cur <> ''
                        AND qlhs_profile.profile_year < '".$namhoc."-12-31'
                        AND profile_subject_subject_id IN (73,26,34,28,41,74)
                        AND (
                            profile_leaveschool_date IS NULL
                            OR (
                                profile_leaveschool_date > '".$namhoc."-05-31'
                            )
                        ) AND kp.months in (9,10,11,12) AND kp.years = ".$namhoc." AND yearcur.qlhs_thcd_trangthai = 1 AND yearcur.qlhs_thcd_trangthai_HTAT = 1)  THEN
                        kp.value_m
                        ELSE
                            0
                        END
                    ) 'NhuCau2'"),
                DB::raw("MAX(
                    CASE
                        WHEN (level_cur <> ''
                        AND qlhs_profile.profile_year < '".($namhoc + 1)."-06-01'
                        AND profile_subject_subject_id IN (73,26,34,28,41,74)
                        AND (
                            profile_leaveschool_date IS NULL
                            OR (
                                profile_leaveschool_date > '".($namhoc + 1)."-01-01'
                            )
                        ) AND kp.months in (1,2,3,4,5) AND kp.years = ".($namhoc + 1)." AND yearcur.qlhs_thcd_trangthai_HK2 = 1 AND yearcur.qlhs_thcd_trangthai_HTAT_HK2 = 1) 
                        OR (level_new <> ''
                        AND qlhs_profile.profile_year < '".($namhoc + 1)."-12-31'
                        AND profile_subject_subject_id IN (73,26,34,28,41,74)
                        AND (
                            profile_leaveschool_date IS NULL
                            OR (
                                profile_leaveschool_date > '".($namhoc + 1)."-05-31'
                            )
                        ) AND kp.months in (9,10,11,12) AND kp.years = ".($namhoc + 1).") THEN
                        kp.value_m
                        ELSE
                            0
                        END
                    ) 'DuToan2'"))

        ->where('profile_year','>',$namhoc.'-05-31')
        ->where('profile_year','<',($namhoc + 1).'-06-01')
        ->where('profile_school_id','=',$truong)
        ->whereIn('profile_subject_subject_id',[73,26,34,28,41,74])
        // ->where('yearcur.qlhs_thcd_nam', '=', DB::raw($namhoc.' and (yearcur.qlhs_thcd_trangthai_HTAT = 1 or yearcur.qlhs_thcd_trangthai_HTAT_HK2 = 1)'))
        ->groupBy('profile_id','profile_name','profile_year','level_old','level_cur','level_new','kp.value_m','kp.months','kp.years');

        $data22 = DB::table(DB::raw("({$data2->toSql()}) as m"))->mergeBindings( $data2 )
        ->select('HKII1','HKI2','HKII2','HKI3','m.DT1','m.DT2','m.DT2_1','m.DT3','m.DT4','m.profile_id','m.profile_subject_subject_id','m.profile_name','m.profile_year','m.level_old','m.level_cur','m.level_new',
            DB::raw('SUM(NhuCau2) as NhuCau'),
            DB::raw('SUM(DuToan2) as DuToan'))
        ->groupBy('HKII1','HKI2','HKII2','HKI3','m.DT1','m.DT2','m.DT2_1','m.DT3','m.DT4','m.profile_id','m.profile_subject_subject_id','m.profile_name','m.profile_year','m.level_old','m.level_cur','m.level_new');

        $data3 = DB::table('qlhs_profile')
        ->join('qlhs_profile_subject', 'profile_id', '=' , DB::raw('profile_subject_profile_id AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.($namhoc + 1).' AND (qlhs_profile_subject.end_year is null OR qlhs_profile_subject.end_year > '.($namhoc + 1).')'))
        ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.($namhoc + 1).'-'.($namhoc + 2).'"'))
        ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$truong.' AND kp.id_doituong = 93 AND '.$namhoc.' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($namhoc + 2).''))
        ->leftJoin('qlhs_tonghopchedo as yearold', 'yearold.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearold.qlhs_thcd_nam = '.$namhoc.''))
        ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearcur.qlhs_thcd_nam = '.($namhoc + 1)))
        ->leftJoin('qlhs_tonghopchedo as yearnew', 'yearnew.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearnew.qlhs_thcd_nam = '.($namhoc + 2).''))
        ->select('profile_id','profile_name','profile_year', 'level_old','level_cur','level_new', 
            DB::raw('MAX(profile_subject_subject_id) as profile_subject_subject_id'),
            DB::raw('MAX(CASE when profile_subject_subject_id = 26 or profile_subject_subject_id = 34 then 1 else 0 END) DT1'),
            DB::raw('MAX(CASE when profile_subject_subject_id = 41 then 1 else 0 END) DT2'),
            DB::raw('MAX(CASE when profile_subject_subject_id = 74 then 1 else 0 END) DT2_1'),
            DB::raw('MAX(CASE when profile_subject_subject_id = 28 then 1 else 0 END) DT3'),
            DB::raw('MAX(CASE when profile_subject_subject_id = 73 then 1 else 0 END) DT4'),

            DB::raw("MAX(CASE when level_old <> '' and qlhs_profile.profile_year < '".$namhoc."-06-01' and profile_subject_subject_id in (73,26,34,28,41,74) and (profile_leaveschool_date is null or (profile_leaveschool_date >= '".$namhoc."-01-01')) then 1 else 0 END) 'HKII1'"),
            DB::raw("MAX(CASE when level_cur <> '' and qlhs_profile.profile_year < '".$namhoc."-12-31' and profile_subject_subject_id in (73,26,34,28,41,74) and (profile_leaveschool_date is null or (profile_leaveschool_date >= '".$namhoc."-05-31')) then 1 else 0 END) 'HKI2'"),
            DB::raw("MAX(CASE when level_cur <> '' and qlhs_profile.profile_year < '".($namhoc +1)."-06-01' and profile_subject_subject_id in (73,26,34,28,41,74) and (profile_leaveschool_date is null or (profile_leaveschool_date >= '".((int)$namhoc+1)."-01-01')) then 1 else 0 END) 'HKII2'"),
            DB::raw("MAX(CASE when level_new <> '' and qlhs_profile.profile_year < '".($namhoc +1)."-12-31' and profile_subject_subject_id in (73,26,34,28,41,74) and (profile_leaveschool_date is null or (profile_leaveschool_date >= '".((int)$namhoc+1)."-05-31')) then 1 else 0 END) 'HKI3'"),
                // DB::raw("MAX(
                //     CASE
                //         WHEN (level_old <> ''
                //         AND qlhs_profile.profile_year < '".($namhoc + 1)."-06-01'
                //         AND profile_subject_subject_id IN (73,26,34,28,41,74)
                //         AND (
                //             profile_leaveschool_date IS NULL
                //             OR (
                //                 profile_leaveschool_date > '".($namhoc + 1)."-01-01'
                //             )
                //         ) AND kp.months in (1,2,3,4,5) AND kp.years = ".($namhoc + 1).") 
                //         OR (level_cur <> ''
                //         AND qlhs_profile.profile_year < '".($namhoc + 1)."-12-31'
                //         AND profile_subject_subject_id IN (73,26,34,28,41,74)
                //         AND (
                //             profile_leaveschool_date IS NULL
                //             OR (
                //                 profile_leaveschool_date > '".($namhoc + 1)."-05-31'
                //             )
                //         ) AND kp.months in (9,10,11,12) AND kp.years = ".($namhoc + 1).")  THEN
                //         kp.value_m
                //         ELSE
                //             0
                //         END
                //     ) 'NhuCau2'"),
                DB::raw("MAX(
                    CASE
                        WHEN (level_cur <> ''
                        AND qlhs_profile.profile_year < '".($namhoc + 2)."-12-31'
                        AND profile_subject_subject_id IN (73,26,34,28,41,74)
                        AND (
                            profile_leaveschool_date IS NULL
                            OR (
                                profile_leaveschool_date > '".($namhoc + 2)."-05-31'
                            )
                        ) AND kp.months in (9,10,11,12) AND kp.years = ".($namhoc + 2).") THEN
                        kp.value_m
                        ELSE
                            0
                        END
                    ) 'DuToan2'"))

        ->where('profile_year','>',($namhoc + 1).'-05-31')
        ->where('profile_year','<',($namhoc + 2).'-06-01')
        ->where('profile_school_id','=',$truong)
        ->whereIn('profile_subject_subject_id',[73,26,34,28,41,74])
        // ->where('yearcur.qlhs_thcd_nam', '=', ($namhoc + 1))
        ->groupBy('profile_id','profile_name','profile_year','level_old','level_cur','level_new','kp.value_m','kp.months','kp.years');

        $data33 = DB::table(DB::raw("({$data3->toSql()}) as m"))->mergeBindings( $data3 )
        ->select('HKII1','HKI2','HKII2','HKI3','m.DT1','m.DT2','m.DT2_1','m.DT3','m.DT4','m.profile_id','m.profile_subject_subject_id','m.profile_name','m.profile_year','m.level_old','m.level_cur','m.level_new',
            // DB::raw('SUM(NhuCau2) as NhuCau'),
            DB::raw('SUM(DuToan2) as DuToan'))
        ->groupBy('HKII1','HKI2','HKII2','HKI3','m.DT1','m.DT2','m.DT2_1','m.DT3','m.DT4','m.profile_id','m.profile_subject_subject_id','m.profile_name','m.profile_year','m.level_old','m.level_cur','m.level_new');

        if($data11->count()==0 && $data22->count()==0 && $data33->count()==0){
            
        }else{
            $import =  $this->insertReportHTAT($data11,$data22,$data33,$truong,$namhoc,$user,$now,$report_name,$report_user_sign,$user_name,$status,$note);
        }

        // return $json;
    }

    public function insertReportHTAT($data11,$data22,$data33,$truong,$namhoc,$user,$now,$report_name,$report_user_sign,$user_name,$status,$note){
        $json = [];
        $time = time();
        $dir = storage_path().'/files/HTAT';
        if($data11->count() > 0){
            foreach ($data11->get() as $value) {
                if ($value->NhuCau > 0 || $value->DuToan) {
                    if (($value->DT2 == 0 || $value->DT2_1 == 0) && ($value->DT1 > 0 || $value->DT3 > 0 || $value->DT4 > 0)) {

                        $json['1'] =DB::table('qlhs_hotrotienan')->insert([
                            'htta_profile_id' => $value->profile_id,
                            'htta_doituong1' => $value->DT1,
                            'htta_doituong2' => 0,//$value->DT2,
                            'htta_doituong3' => $value->DT3,
                            'htta_doituong4' => $value->DT4,
                            'hocky2_old' => $value->HKII1,
                            'hocky1_cur' => $value->HKI2,
                            'hocky2_cur' => $value->HKII2,
                            'hocky1_new' => $value->HKI3,
                            // 'ho_tro' => $value->money1,
                            'nhu_cau' => $value->NhuCau,
                            'du_toan' => $value->DuToan,
                            'year_old' => (int)$namhoc,
                            'year_cur' => (int)$namhoc+1,
                            'type' => 1,
                            'type_code' => 'HTAT-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time,
                            'trangthai_pheduyet_HTAT' => 1,
                            'trangthai_thamdinh_HTAT' => 1
                            ]);
                    }
                    else if ($value->DT2 > 0 && $value->DT2_1 > 0) {
                        $json['1'] =DB::table('qlhs_hotrotienan')->insert([
                            'htta_profile_id' => $value->profile_id,
                            'htta_doituong1' => $value->DT1,
                            'htta_doituong2' => 1,//$value->DT2,
                            'htta_doituong3' => $value->DT3,
                            'htta_doituong4' => $value->DT4,
                            'hocky2_old' => $value->HKII1,
                            'hocky1_cur' => $value->HKI2,
                            'hocky2_cur' => $value->HKII2,
                            'hocky1_new' => $value->HKI3,
                            // 'ho_tro' => $value->money1,
                            'nhu_cau' => $value->NhuCau,
                            'du_toan' => $value->DuToan,
                            'year_old' => (int)$namhoc,
                            'year_cur' => (int)$namhoc+1,
                            'type' => 1,
                            'type_code' => 'HTAT-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time,
                            'trangthai_pheduyet_HTAT' => 1,
                            'trangthai_thamdinh_HTAT' => 1
                            ]);
                    }
                }
                $json['1'] = 2;
            }
        }else{
            $json['1'] = 2;
        }
        if($data22->count() > 0){
            foreach ($data22->get() as $key => $value) {
                if ($value->NhuCau > 0 || $value->DuToan) {
                    if (($value->DT2 == 0 || $value->DT2_1 == 0) && ($value->DT1 > 0 || $value->DT3 > 0 || $value->DT4 > 0)) {

                        $json['2'] =DB::table('qlhs_hotrotienan')->insert([
                            'htta_profile_id' => $value->profile_id,
                            'htta_doituong1' => $value->DT1,
                            'htta_doituong2' => 0,//$value->DT2,
                            'htta_doituong3' => $value->DT3,
                            'htta_doituong4' => $value->DT4,
                            'hocky2_old' => $value->HKII1,
                            'hocky1_cur' => $value->HKI2,
                            'hocky2_cur' => $value->HKII2,
                            'hocky1_new' => $value->HKI3,
                            // 'ho_tro' => $value->money1,
                            'nhu_cau' => $value->NhuCau,
                            'du_toan' => $value->DuToan,
                            'year_old' => (int)$namhoc,
                            'year_cur' => (int)$namhoc+1,
                            'type' => 2,
                            'type_code' => 'HTAT-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time,
                            'trangthai_pheduyet_HTAT' => 1,
                            'trangthai_thamdinh_HTAT' => 1
                            ]);
                    }
                    else if ($value->DT2 > 0 && $value->DT2_1 > 0) {
                        $json['2'] =DB::table('qlhs_hotrotienan')->insert([
                            'htta_profile_id' => $value->profile_id,
                            'htta_doituong1' => $value->DT1,
                            'htta_doituong2' => 1,//$value->DT2,
                            'htta_doituong3' => $value->DT3,
                            'htta_doituong4' => $value->DT4,
                            'hocky2_old' => $value->HKII1,
                            'hocky1_cur' => $value->HKI2,
                            'hocky2_cur' => $value->HKII2,
                            'hocky1_new' => $value->HKI3,
                            // 'ho_tro' => $value->money1,
                            'nhu_cau' => $value->NhuCau,
                            'du_toan' => $value->DuToan,
                            'year_old' => (int)$namhoc,
                            'year_cur' => (int)$namhoc+1,
                            'type' => 2,
                            'type_code' => 'HTAT-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time,
                            'trangthai_pheduyet_HTAT' => 1,
                            'trangthai_thamdinh_HTAT' => 1
                            ]);
                    }
                }
                $json['2'] = 2;
            }
        }else{
            $json['2'] = 2;
        }
        if($data33->count()>0){
            foreach ($data33->get() as $key => $value) {
                if (($value->DT2 == 0 || $value->DT2_1 == 0) && ($value->DT1 > 0 || $value->DT3 > 0 || $value->DT4 > 0)) {

                    $json['3'] =DB::table('qlhs_hotrotienan')->insert([
                        'htta_profile_id' => $value->profile_id,
                        'htta_doituong1' => $value->DT1,
                        'htta_doituong2' => 0,//$value->DT2,
                        'htta_doituong3' => $value->DT3,
                        'htta_doituong4' => $value->DT4,
                        'hocky2_old' => $value->HKII1,
                        'hocky1_cur' => $value->HKI2,
                        'hocky2_cur' => $value->HKII2,
                        'hocky1_new' => $value->HKI3,
                        // 'ho_tro' => $value->money1,
                        'nhu_cau' => 0,//$value->NhuCau,
                        'du_toan' => $value->DuToan,
                        'year_old' => (int)$namhoc,
                        'year_cur' => (int)$namhoc+1,
                        'type' => 3,
                        'type_code' => 'HTAT-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time,
                        'trangthai_pheduyet_HTAT' => 1,
                        'trangthai_thamdinh_HTAT' => 1
                        ]);
                }
                else if ($value->DT2 > 0 && $value->DT2_1 > 0) {
                    $json['3'] =DB::table('qlhs_hotrotienan')->insert([
                        'htta_profile_id' => $value->profile_id,
                        'htta_doituong1' => $value->DT1,
                        'htta_doituong2' => 1,//$value->DT2,
                        'htta_doituong3' => $value->DT3,
                        'htta_doituong4' => $value->DT4,
                        'hocky2_old' => $value->HKII1,
                        'hocky1_cur' => $value->HKI2,
                        'hocky2_cur' => $value->HKII2,
                        'hocky1_new' => $value->HKI3,
                        // 'ho_tro' => $value->money1,
                        'nhu_cau' => 0,//$value->NhuCau,
                        'du_toan' => $value->DuToan,
                        'year_old' => (int)$namhoc,
                        'year_cur' => (int)$namhoc+1,
                        'type' => 3,
                        'type_code' => 'HTAT-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time,
                        'trangthai_pheduyet_HTAT' => 1,
                        'trangthai_thamdinh_HTAT' => 1
                        ]);
                }
            }

        }else{
            $json['3'] = 2;
        }
        
        if((int)$json['1']> 0 && (int)$json['2']>0 && (int)$json['3']>0){
            
              
                $insert_returnID = DB::table('qlhs_hosobaocao')->insertGetId([
                    'report_name' => $report_name,
                    'report_type' => 'HTAT-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time,
                    'report_date' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                    'create_userid' => $user,
                    'update_userid' => $user,
                    'report_user' => $user_name,
                    'report_user_sign' => $report_user_sign,
                    'report_attach_name' => '',
                    'report_nature' => $status,
                    'report_year' => $namhoc,
                    'report_id_truong' => $truong,
                    'report_note' => $note,
                    'report' => 'HTAT'
                ]);
                
                if(!is_null($insert_returnID) && $insert_returnID > 0 ){
                    $this->exportforSchoolsHTAT($insert_returnID);
                    if (file_exists(storage_path().'/exceldownload/HTAT/'.'HTAT-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time.'.xlsx')) {
                        // return TRUE;
                    }
                    else {
                        $deleteHoso = DB::table('qlhs_hosobaocao')->where('report_type', 'LIKE', '%'.'HTAT-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time.'%')->delete();
                        $deleteHTAT = DB::table('qlhs_hotrotienan')->where('type_code', 'LIKE', '%'.'HTAT-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time.'%')->delete();
                        // return false;
                    }
                }else{
                    // return false;
                }
            return $insert_returnID;
        }
        else {
            return 0;
        }
    }

    public function exportforSchoolsHTAT($id, $type = true){
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
            $data_results['aCount'] = $this->countValueHTAT('1',$getSchoolName->report_type);
            $data_results['bCount'] = $this->countValueHTAT('2',$getSchoolName->report_type);
            $data_results['cCount'] = $this->countValueHTAT('3',$getSchoolName->report_type);
            $data_results['TotalCount'] = $this->countValueHTAT(null,$getSchoolName->report_type);
            //Get by type A
            $data_results['a'] = DB::table('qlhs_hotrotienan')
            ->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_hotrotienan.htta_profile_id')
            ->leftJoin(DB::raw('(select decided_profile_id, max(decided_id) decided_id, max(decided_confirmation) decided_confirmation, max(decided_confirmdate) decided_confirmdate, max(decided_number) decided_number, max(decided_type) decided_type from qlhs_decided GROUP BY decided_profile_id) as qlhs_decided'), 'qlhs_decided.decided_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_decided.decided_type = "HTAT"'))
            ->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_hotrotienan.htta_profile_id and qlhs_profile_history.history_year = "'.$getSchoolName->report_year.'-'.($getSchoolName->report_year + 1).'"'))
            ->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
            ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
            ->where('qlhs_hotrotienan.type_code', '=', $getSchoolName->report_type)
            ->where('qlhs_hotrotienan.type', '=', 1)
            ->select('qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile_history.history_year', 'qlhs_decided.decided_id', 'qlhs_decided.decided_confirmation', 'qlhs_decided.decided_number', 'qlhs_decided.decided_confirmdate', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'qlhs_hotrotienan.*')->DISTINCT()->get();

            $data_results['b'] = DB::table('qlhs_hotrotienan')
            ->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_hotrotienan.htta_profile_id')
            ->leftJoin(DB::raw('(select decided_profile_id, max(decided_id) decided_id, max(decided_confirmation) decided_confirmation, max(decided_confirmdate) decided_confirmdate, max(decided_number) decided_number, max(decided_type) decided_type from qlhs_decided GROUP BY decided_profile_id) as qlhs_decided'), 'qlhs_decided.decided_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_decided.decided_type = "HTAT"'))
            ->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_hotrotienan.htta_profile_id and qlhs_profile_history.history_year = "'.$getSchoolName->report_year.'-'.($getSchoolName->report_year + 1).'"'))
            ->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
            ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
            ->where('qlhs_hotrotienan.type_code', '=', $getSchoolName->report_type)
            ->where('qlhs_hotrotienan.type', '=', 2)
            ->select('qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile_history.history_year', 'qlhs_decided.decided_id', 'qlhs_decided.decided_confirmation', 'qlhs_decided.decided_number', 'qlhs_decided.decided_confirmdate', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'qlhs_hotrotienan.*')->DISTINCT()->get();

            $data_results['c'] = DB::table('qlhs_hotrotienan')
            ->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_hotrotienan.htta_profile_id')
            ->leftJoin(DB::raw('(select decided_profile_id, max(decided_id) decided_id, max(decided_confirmation) decided_confirmation, max(decided_confirmdate) decided_confirmdate, max(decided_number) decided_number, max(decided_type) decided_type from qlhs_decided GROUP BY decided_profile_id) as qlhs_decided'), 'qlhs_decided.decided_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_decided.decided_type = "HTAT"'))
            ->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_hotrotienan.htta_profile_id and qlhs_profile_history.history_year = "'.($getSchoolName->report_year + 1).'-'.($getSchoolName->report_year + 2).'"'))
            ->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
            ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
            ->where('qlhs_hotrotienan.type_code', '=', $getSchoolName->report_type)
            ->where('qlhs_hotrotienan.type', '=', 3)
            ->select('qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile_history.history_year', 'qlhs_decided.decided_id', 'qlhs_decided.decided_confirmation', 'qlhs_decided.decided_number', 'qlhs_decided.decided_confirmdate', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'qlhs_hotrotienan.*')->DISTINCT()->get();
        }
        $data_results['schools_name'] = $getSchoolName->schools_name;
        $data_results['report_year'] = $getSchoolName->report_year;
        $this->addCellExcelHTAT($data_results, $getSchoolName->report_type, $type);
    }

    private function addCellExcelHTAT($data_results, $filename, $type = true){
        $excel =    Excel::load(storage_path().'/exceltemplate/laphosoHTAT.xlsx', function($reader) use($data_results){
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

            $reader->getActiveSheet()->setCellValueByColumnAndRow(2, 5, 'Thông tin cơ bản (tính tại thời điểm tháng 5/'.$data_results['report_year'].')')->getStyle('C5')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

            $reader->getActiveSheet()->setCellValueByColumnAndRow(2, 7, 'Học kì II năm học '.($data_results['report_year'] - 1).'-'.$data_results['report_year'])->getStyle('C7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(3, 7, 'Năm học '.$data_results['report_year'].'-'.($data_results['report_year'] + 1))->getStyle('D7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(4, 7, 'Năm học '.($data_results['report_year'] + 1).'-'.($data_results['report_year'] + 2))->getStyle('E7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

            $reader->getActiveSheet()->setCellValueByColumnAndRow(18, 7, 'Học kỳ II năm học '.($data_results['report_year'] - 1).'-'.$data_results['report_year'])->getStyle('S7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(19, 7, 'Học kỳ I năm học '.$data_results['report_year'].'-'.($data_results['report_year'] + 1))->getStyle('T7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(20, 7, 'Học kỳ II năm học '.$data_results['report_year'].'-'.($data_results['report_year'] + 1))->getStyle('U7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(21, 7, 'Học kỳ I năm học '.($data_results['report_year'] + 1).'-'.($data_results['report_year'] + 2))->getStyle('V7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

            $reader->getActiveSheet()->setCellValueByColumnAndRow(22, 5, 'Nhu cầu kinh phí năm '.$data_results['report_year'])->getStyle('W5')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(23, 5, 'Dự toán kinh phí năm '.($data_results['report_year'] + 1))->getStyle('X5')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
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
            $reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row,$data_results['TotalCount']->tonghtta_doituong1)->getStyle('O'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row,$data_results['TotalCount']->tonghtta_doituong2)->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row,$data_results['TotalCount']->tonghtta_doituong3)->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row,$data_results['TotalCount']->tonghtta_doituong4)->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(18, $row,$data_results['TotalCount']->tonghocky2_old)->getStyle('S'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row,$data_results['TotalCount']->tonghocky1_cur)->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row,$data_results['TotalCount']->tonghocky2_cur)->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row,$data_results['TotalCount']->tonghocky1_new)->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row,$data_results['TotalCount']->tongnhu_cau)->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row,$data_results['TotalCount']->tongdu_toan)->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);

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
            $reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row,$data_results['aCount']->tonghtta_doituong1)->getStyle('O'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row,$data_results['aCount']->tonghtta_doituong2)->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row,$data_results['aCount']->tonghtta_doituong3)->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row,$data_results['aCount']->tonghtta_doituong4)->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(18, $row,$data_results['aCount']->tonghocky2_old)->getStyle('S'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row,$data_results['aCount']->tonghocky1_cur)->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row,$data_results['aCount']->tonghocky2_cur)->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row,$data_results['aCount']->tonghocky1_new)->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row,$data_results['aCount']->tongnhu_cau)->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row,$data_results['aCount']->tongdu_toan)->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);

            if($data_results['a']->count()>0){
                $indexa = 0;
                foreach($data_results['a'] as $key => $value){
                    $col = 0;   $row++;

                    // $strYear = substr((string)$value->history_year, 0, 4);
                    // if ($strYear < $data_results['report_year']) {
                    //  $class_lv1 = $value->level_next_1;
                    //  $class_lv2 = $value->level_next_2;
                    //  $class_lv3 = $value->level_next_3;
                    // }

                    // if ($strYear == $data_results['report_year']) {
                    //  $class_lv1 = $value->level_next;
                    //  $class_lv2 = $value->level_next_1;
                    //  $class_lv3 = $value->level_next_2;
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
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->nationals_name)->getStyle('G'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);  
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_parentname)->getStyle('H'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_household)->getStyle('I'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->tenxa)->getStyle('J'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 

                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->tenhuyen)->getStyle('K'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);         
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->decided_confirmation)->getStyle('L'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);        
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->decided_number)->getStyle('M'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$decided_date)->getStyle('N'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight); 

                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->htta_doituong1)->getStyle('O'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->htta_doituong2)->getStyle('P'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->htta_doituong3)->getStyle('Q'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->htta_doituong4)->getStyle('R'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_old)->getStyle('S'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_cur)->getStyle('T'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_cur)->getStyle('U'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_new)->getStyle('V'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->nhu_cau)->getStyle('W'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);         
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->du_toan)->getStyle('X'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
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
            $reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row,$data_results['bCount']->tonghtta_doituong1)->getStyle('O'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row,$data_results['bCount']->tonghtta_doituong2)->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row,$data_results['bCount']->tonghtta_doituong3)->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row,$data_results['bCount']->tonghtta_doituong4)->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(18, $row,$data_results['bCount']->tonghocky2_old)->getStyle('S'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row,$data_results['bCount']->tonghocky1_cur)->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row,$data_results['bCount']->tonghocky2_cur)->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row,$data_results['bCount']->tonghocky1_new)->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row,$data_results['bCount']->tongnhu_cau)->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row,$data_results['bCount']->tongdu_toan)->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);

            $indexb = 0;

            if($data_results['b']->count()>0){
                $indexa = 0;
                foreach($data_results['b'] as $key => $value){
                    $col = 0;   $row++;

                    // $strYear = substr((string)$value->history_year, 0, 4);
                    // if ($strYear < $data_results['report_year']) {
                    //  $class_lv1 = $value->level_next;
                    //  $class_lv2 = $value->level_next_1;
                    //  $class_lv3 = $value->level_next_2;
                    // }

                    // if ($strYear == $data_results['report_year']) {
                    //  $class_lv1 = 0;
                    //  $class_lv2 = $value->level_next_1;
                    //  $class_lv3 = $value->level_next_2;
                    // }

                    // if ($strYear > $data_results['report_year']) {
                    //  $class_lv1 = 0;
                    //  $class_lv2 = 0;
                    //  $class_lv3 = $value->level_next_1;
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
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->nationals_name)->getStyle('G'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);  
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_parentname)->getStyle('H'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_household)->getStyle('I'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->tenxa)->getStyle('J'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 

                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->tenhuyen)->getStyle('K'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);         
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->decided_confirmation)->getStyle('L'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);        
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->decided_number)->getStyle('M'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$decided_date)->getStyle('N'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight); 

                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->htta_doituong1)->getStyle('O'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->htta_doituong2)->getStyle('P'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->htta_doituong3)->getStyle('Q'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->htta_doituong4)->getStyle('R'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_old)->getStyle('S'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_cur)->getStyle('T'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_cur)->getStyle('U'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_new)->getStyle('V'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->nhu_cau)->getStyle('W'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);         
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->du_toan)->getStyle('X'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
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
            $reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row,$data_results['cCount']->tonghtta_doituong1)->getStyle('O'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row,$data_results['cCount']->tonghtta_doituong2)->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row,$data_results['cCount']->tonghtta_doituong3)->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row,$data_results['cCount']->tonghtta_doituong4)->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(18, $row,$data_results['cCount']->tonghocky2_old)->getStyle('S'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row,$data_results['cCount']->tonghocky1_cur)->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row,$data_results['cCount']->tonghocky2_cur)->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row,$data_results['cCount']->tonghocky1_new)->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row,$data_results['cCount']->tongnhu_cau)->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row,$data_results['cCount']->tongdu_toan)->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);

            $indexc = 0;
            if($data_results['c']->count()>0){
                $indexa = 0;
                foreach($data_results['c'] as $key => $value){
                    $col = 0;   $row++;

                    // $strYear = substr((string)$value->history_year, 0, 4);
                    // if ($strYear < $data_results['report_year']) {
                    //  $class_lv1 = $value->level_next;
                    //  $class_lv2 = $value->level_next_1;
                    //  $class_lv3 = $value->level_next_2;
                    // }

                    // if ($strYear == $data_results['report_year']) {
                    //  $class_lv1 = 0;
                    //  $class_lv2 = $value->level_next_1;
                    //  $class_lv3 = $value->level_next_2;
                    // }

                    // if ($strYear > $data_results['report_year']) {
                    //  $class_lv1 = 0;
                    //  $class_lv2 = 0;
                    //  $class_lv3 = $value->level_next_1;
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
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->nationals_name)->getStyle('G'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);  
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_parentname)->getStyle('H'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_household)->getStyle('I'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->tenxa)->getStyle('J'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 

                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->tenhuyen)->getStyle('K'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);         
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->decided_confirmation)->getStyle('L'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);        
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->decided_number)->getStyle('M'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$decided_date)->getStyle('N'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight); 

                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->htta_doituong1)->getStyle('O'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->htta_doituong2)->getStyle('P'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->htta_doituong3)->getStyle('Q'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->htta_doituong4)->getStyle('R'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_old)->getStyle('S'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_cur)->getStyle('T'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_cur)->getStyle('U'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_new)->getStyle('V'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->nhu_cau)->getStyle('W'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);         
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->du_toan)->getStyle('X'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                }
            }
        });
        if($type){
            return $excel->setFilename($filename)->store('xlsx', storage_path().'/exceldownload/HTAT');
        }else{
            return $excel->setFilename($filename)->download('xlsx');
        }
    }
    
    public function countValueHTAT($type = null,$code){

        if($type!=null){
            $count = DB::table('qlhs_hotrotienan')->where('type_code','=',$code)->where('type','=',$type)
            ->select(
                DB::raw('sum(htta_doituong1) as tonghtta_doituong1'),
                DB::raw('sum(htta_doituong2) as tonghtta_doituong2'),
                DB::raw('sum(htta_doituong3) as tonghtta_doituong3'),
                DB::raw('sum(htta_doituong4) as tonghtta_doituong4'),
                DB::raw('sum(hocky2_old) as tonghocky2_old'),
                DB::raw('sum(hocky1_cur) as tonghocky1_cur'),
                DB::raw('sum(hocky2_cur) as tonghocky2_cur'),
                DB::raw('sum(hocky1_new) as tonghocky1_new'),
                DB::raw('sum(nhu_cau) as tongnhu_cau'),
                DB::raw('sum(du_toan) as tongdu_toan'))->first();
            return $count;
        }else{
            $count = DB::table('qlhs_hotrotienan')->where('type_code','=',$code)
            ->select(
                DB::raw('sum(htta_doituong1) as tonghtta_doituong1'),
                DB::raw('sum(htta_doituong2) as tonghtta_doituong2'),
                DB::raw('sum(htta_doituong3) as tonghtta_doituong3'),
                DB::raw('sum(htta_doituong4) as tonghtta_doituong4'),
                DB::raw('sum(hocky2_old) as tonghocky2_old'),
                DB::raw('sum(hocky1_cur) as tonghocky1_cur'),
                DB::raw('sum(hocky2_cur) as tonghocky2_cur'),
                DB::raw('sum(hocky1_new) as tonghocky1_new'),
                DB::raw('sum(nhu_cau) as tongnhu_cau'),
                DB::raw('sum(du_toan) as tongdu_toan'))->first();
            return $count;
        }
    }

//---------------------------------------------------------------------HTBT------------------------------------------------------------------------
    public function getDataHTBT($school_id, $year, $report_name, $user_sign, $user_create, $note, $status){
        $result = [];
        try {
            
            $current_user_id = Auth::user()->id;
            $current_date = Carbon::now('Asia/Ho_Chi_Minh');
            

            $check = TRUE;

            $getDataTypeA = DB::table('qlhs_profile')
            ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id in (34, 46, 72) AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.$year.' AND (qlhs_profile_subject.end_year is null OR qlhs_profile_subject.end_year > '.$year.')'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))
            ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$school_id.' AND kp.id_doituong in (94, 98, 115) AND '.($year - 1).' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 1).''))
            ->leftJoin('qlhs_tonghopchedo as yearold', 'yearold.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearold.qlhs_thcd_nam = '.($year - 1).' and (yearold.qlhs_thcd_trangthai_HTBT_TA = 1 or yearold.qlhs_thcd_trangthai_HTBT_TO = 1 or yearold.qlhs_thcd_trangthai_HTBT_VHTT = 1 or yearold.qlhs_thcd_trangthai_HTBT_TA_HK2 = 1 or yearold.qlhs_thcd_trangthai_HTBT_TO_HK2 = 1 or yearold.qlhs_thcd_trangthai_HTBT_VHTT_HK2 = 1)'))
            // ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', 'profile_id')
            ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearcur.qlhs_thcd_nam = '.$year.' and (yearcur.qlhs_thcd_trangthai_HTBT_TA = 1 or yearcur.qlhs_thcd_trangthai_HTBT_TO = 1 or yearcur.qlhs_thcd_trangthai_HTBT_VHTT = 1 or yearcur.qlhs_thcd_trangthai_HTBT_TA_HK2 = 1 or yearcur.qlhs_thcd_trangthai_HTBT_TO_HK2 = 1 or yearcur.qlhs_thcd_trangthai_HTBT_VHTT_HK2 = 1)'))
            ->leftJoin('qlhs_tonghopchedo as yearnew', 'yearnew.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearnew.qlhs_thcd_nam = '.($year + 1)))
            ->where('qlhs_profile.profile_school_id', '=', $school_id)
            ->where('qlhs_profile.profile_year', '<', $year.'-06-01')
            // ->where('yearcur.qlhs_thcd_nam', '=', DB::raw($year.' and (yearcur.qlhs_thcd_trangthai = 1 or yearcur.qlhs_thcd_trangthai_HK2 = 1 or yearold.qlhs_thcd_trangthai = 1 or yearold.qlhs_thcd_trangthai_HK2 = 1)'))
            ->select('qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years', 
                DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id in (34, 46) then 1 else 0 END) as hotrotienan'), 
                DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id in (34, 46) then 1 else 0 END) as hotrotieno'), 
                DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 46 then 1 else 0 END) as hotroVHTT'), 
                // DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 72 then 1 else 0 END) as ongoaitruong'), 

                DB::raw('MAX(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile_subject.profile_subject_subject_id in (34, 46) and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hotrotienan_hocky2_old'), 
                DB::raw('MAX(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile_subject.profile_subject_subject_id in (34, 46) and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hotrotieno_hocky2_old'), 
                DB::raw('MAX(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hotroVHTT_hocky2_old'), 

                DB::raw('MAX(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile_subject.profile_subject_subject_id in (34, 46) and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hotrotienan_hocky1_cur'), 
                DB::raw('MAX(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile_subject.profile_subject_subject_id in (34, 46) and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hotrotieno_hocky1_cur'), 
                DB::raw('MAX(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hotroVHTT_hocky1_cur'), 

                DB::raw('MAX(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile_subject.profile_subject_subject_id in (34, 46) and qlhs_profile.profile_year < "'.($year +1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hotrotienan_hocky2_cur'), 
                DB::raw('MAX(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile_subject.profile_subject_subject_id in (34, 46) and qlhs_profile.profile_year < "'.($year +1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hotrotieno_hocky2_cur'), 
                DB::raw('MAX(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.($year +1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hotroVHTT_hocky2_cur'), 

                DB::raw('MAX(CASE when qlhs_profile_history.level_new <> "" and qlhs_profile_subject.profile_subject_subject_id in (34, 46) and qlhs_profile.profile_year < "'.($year +1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hotrotienan_hocky1_new'), 
                DB::raw('MAX(CASE when qlhs_profile_history.level_new <> "" and qlhs_profile_subject.profile_subject_subject_id in (34, 46) and qlhs_profile.profile_year < "'.($year +1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hotrotieno_hocky1_new'), 
                DB::raw('MAX(CASE when qlhs_profile_history.level_new <> "" and qlhs_profile.profile_year < "'.($year +1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hotroVHTT_hocky1_new'),
                    DB::raw('MAX(
                            CASE
                            WHEN ( qlhs_profile_history.level_old <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id in (34, 46)
                            AND qlhs_profile.profile_year < "'.$year.'-06-01"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01"
                                )
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.$year.' AND kp.id_doituong = 94 AND yearold.qlhs_thcd_trangthai_HK2 = 1 AND yearold.qlhs_thcd_trangthai_HTBT_TA_HK2 = 1) 
                            OR (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id in (34, 46)
                            AND qlhs_profile.profile_year < "'.$year.'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 94 AND yearcur.qlhs_thcd_trangthai = 1 AND yearcur.qlhs_thcd_trangthai_HTBT_TA = 1) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS NHUCAUAN'),
                    DB::raw('MAX(
                            CASE
                            WHEN (qlhs_profile_history.level_old <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id in (34, 46)
                            AND qlhs_profile.profile_year < "'.$year.'-06-01"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01"
                                )
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.$year.' AND kp.id_doituong = 98 AND yearold.qlhs_thcd_trangthai_HK2 = 1 AND yearold.qlhs_thcd_trangthai_HTBT_TO_HK2 = 1) 
                            OR (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id in (34, 46)
                            AND qlhs_profile.profile_year < "'.$year.'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 98 AND yearcur.qlhs_thcd_trangthai = 1 AND yearcur.qlhs_thcd_trangthai_HTBT_TO = 1) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS NHUCAUO'),
                    DB::raw('MAX(
                            CASE
                            WHEN (qlhs_profile_history.level_old <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 46
                            AND qlhs_profile.profile_year < "'.$year.'-06-01"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01"
                                )
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.$year.' AND kp.id_doituong = 115 AND yearold.qlhs_thcd_trangthai_HK2 = 1 AND yearold.qlhs_thcd_trangthai_HTBT_VHTT_HK2 = 1) 
                            OR (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 46
                            AND qlhs_profile.profile_year < "'.$year.'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 115 AND yearcur.qlhs_thcd_trangthai = 1 AND yearcur.qlhs_thcd_trangthai_HTBT_VHTT = 1) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS NHUCAUTT'),
                    DB::raw('MAX(
                            CASE
                            WHEN (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id in (34, 46)
                            AND qlhs_profile.profile_year < "'.($year +1).'-06-01"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year +1).'-01-01"
                                )
                            )
                            AND kp.months in (1,2,3,4,5) and kp.years = '.($year +1).' AND kp.id_doituong = 94 AND yearcur.qlhs_thcd_trangthai_HK2 = 1 AND yearcur.qlhs_thcd_trangthai_HTBT_TA_HK2 = 1) 
                            OR (qlhs_profile_history.level_new <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id in (34, 46)
                            AND qlhs_profile.profile_year < "'.($year +1).'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year +1).'-05-31"
                                )
                            ) AND kp.months in (9,10,11,12) and kp.years = '.($year +1).' AND kp.id_doituong = 94) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS DUTOANAN'),
                    DB::raw('MAX(
                            CASE
                            WHEN (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id in (34, 46)
                            AND qlhs_profile.profile_year < "'.($year +1).'-06-01"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year +1).'-01-01"
                                )
                            )
                            AND kp.months in (1,2,3,4,5) and kp.years = '.($year +1).' AND kp.id_doituong = 98 AND yearcur.qlhs_thcd_trangthai_HK2 = 1 AND yearcur.qlhs_thcd_trangthai_HTBT_TO_HK2 = 1) 
                            OR (qlhs_profile_history.level_new <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id in (34, 46)
                            AND qlhs_profile.profile_year < "'.($year +1).'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year +1).'-05-31"
                                )
                            ) AND kp.months in (9,10,11,12) and kp.years = '.($year +1).' AND kp.id_doituong = 98) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS DUTOANO'),
                    DB::raw('MAX(
                            CASE
                            WHEN (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 46
                            AND qlhs_profile.profile_year < "'.($year +1).'-06-01"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year +1).'-01-01"
                                )
                            )
                            AND kp.months in (1,2,3,4,5) and kp.years = '.($year +1).' AND kp.id_doituong = 115 AND yearcur.qlhs_thcd_trangthai_HK2 = 1 AND yearcur.qlhs_thcd_trangthai_HTBT_VHTT_HK2 = 1) 
                            OR (qlhs_profile_history.level_new <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 46
                            AND qlhs_profile.profile_year < "'.($year +1).'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year +1).'-05-31"
                                )
                            ) AND kp.months in (9,10,11,12) and kp.years = '.($year +1).' AND kp.id_doituong = 115) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS DUTOANTT'))
            ->groupBy('qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years');

            $getDataType1 = DB::table(DB::raw("({$getDataTypeA->toSql()}) as m"))
                ->mergeBindings( $getDataTypeA )
                ->select('m.profile_id',
                        'm.profile_year',
                        'm.level_old',
                        'm.level_cur',
                        'm.level_new',
                        'm.profile_leaveschool_date',
                        'm.hotrotienan', 'm.hotrotieno', 'm.hotroVHTT', //'m.ongoaitruong', 
                        'm.hotrotienan_hocky2_old', 'm.hotrotieno_hocky2_old', 'm.hotroVHTT_hocky2_old',
                        'm.hotrotienan_hocky1_cur', 'm.hotrotieno_hocky1_cur', 'm.hotroVHTT_hocky1_cur',
                        'm.hotrotienan_hocky2_cur', 'm.hotrotieno_hocky2_cur', 'm.hotroVHTT_hocky2_cur',
                        'm.hotrotienan_hocky1_new', 'm.hotrotieno_hocky1_new', 'm.hotroVHTT_hocky1_new',
                        DB::raw('SUM(m.NHUCAUAN) as NHUCAUAN'), DB::raw('SUM(m.NHUCAUO) as NHUCAUO'), DB::raw('SUM(m.NHUCAUTT) as NHUCAUTT'),
                        DB::raw('SUM(m.DUTOANAN) as DUTOANAN'), DB::raw('SUM(m.DUTOANO) as DUTOANO'), DB::raw('SUM(m.DUTOANTT) as DUTOANTT'))

                ->groupBy('m.profile_id',
                        'm.profile_year',
                        'm.level_old',
                        'm.level_cur',
                        'm.level_new',
                        'm.profile_leaveschool_date',
                        'm.hotrotienan', 'm.hotrotieno', 'm.hotroVHTT', //'m.ongoaitruong', 
                        'm.hotrotienan_hocky2_old', 'm.hotrotieno_hocky2_old', 'm.hotroVHTT_hocky2_old',
                        'm.hotrotienan_hocky1_cur', 'm.hotrotieno_hocky1_cur', 'm.hotroVHTT_hocky1_cur',
                        'm.hotrotienan_hocky2_cur', 'm.hotrotieno_hocky2_cur', 'm.hotroVHTT_hocky2_cur',
                        'm.hotrotienan_hocky1_new', 'm.hotrotieno_hocky1_new', 'm.hotroVHTT_hocky1_new')
                ->get();

            $getDataTypeB = DB::table('qlhs_profile')
            ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id in (34, 46, 72) AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.$year.' AND (qlhs_profile_subject.end_year is null OR qlhs_profile_subject.end_year > '.$year.')'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))
            ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$school_id.' AND kp.id_doituong in (94, 98, 115) AND '.($year - 1).' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 1).''))
            ->leftJoin('qlhs_tonghopchedo as yearold', 'yearold.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearold.qlhs_thcd_nam = '.($year - 1).' and (yearold.qlhs_thcd_trangthai_HTBT_TA = 1 or yearold.qlhs_thcd_trangthai_HTBT_TO = 1 or yearold.qlhs_thcd_trangthai_HTBT_VHTT = 1 or yearold.qlhs_thcd_trangthai_HTBT_TA_HK2 = 1 or yearold.qlhs_thcd_trangthai_HTBT_TO_HK2 = 1 or yearold.qlhs_thcd_trangthai_HTBT_VHTT_HK2 = 1)'))
            // ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', 'profile_id')
            ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearcur.qlhs_thcd_nam = '.$year))
            ->leftJoin('qlhs_tonghopchedo as yearnew', 'yearnew.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearnew.qlhs_thcd_nam = '.($year + 1)))
            ->where('qlhs_profile.profile_school_id', '=', $school_id)
            ->where('qlhs_profile.profile_year', '>', $year.'-05-31')
            ->where('qlhs_profile.profile_year', '<', ($year + 1).'-06-01')
            // ->where('yearcur.qlhs_thcd_nam', '=', DB::raw($year.' and (yearcur.qlhs_thcd_trangthai = 1 or yearcur.qlhs_thcd_trangthai_HK2 = 1)'))
            ->select('qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years', 
                DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id in (34, 46) then 1 else 0 END) as hotrotienan'), 
                DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id in (34, 46) then 1 else 0 END) as hotrotieno'), 
                DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 46 then 1 else 0 END) as hotroVHTT'), 
                // DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 72 then 1 else 0 END) as ongoaitruong'), 

                DB::raw('MAX(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile_subject.profile_subject_subject_id in (34, 46) and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hotrotienan_hocky2_old'), 
                DB::raw('MAX(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile_subject.profile_subject_subject_id in (34, 46) and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hotrotieno_hocky2_old'), 
                DB::raw('MAX(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hotroVHTT_hocky2_old'), 

                DB::raw('MAX(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile_subject.profile_subject_subject_id in (34, 46) and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hotrotienan_hocky1_cur'), 
                DB::raw('MAX(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile_subject.profile_subject_subject_id in (34, 46) and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hotrotieno_hocky1_cur'), 
                DB::raw('MAX(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hotroVHTT_hocky1_cur'), 

                DB::raw('MAX(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile_subject.profile_subject_subject_id in (34, 46) and qlhs_profile.profile_year < "'.($year +1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hotrotienan_hocky2_cur'), 
                DB::raw('MAX(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile_subject.profile_subject_subject_id in (34, 46) and qlhs_profile.profile_year < "'.($year +1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hotrotieno_hocky2_cur'), 
                DB::raw('MAX(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.($year +1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hotroVHTT_hocky2_cur'), 

                DB::raw('MAX(CASE when qlhs_profile_history.level_new <> "" and qlhs_profile_subject.profile_subject_subject_id in (34, 46) and qlhs_profile.profile_year < "'.($year +1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hotrotienan_hocky1_new'), 
                DB::raw('MAX(CASE when qlhs_profile_history.level_new <> "" and qlhs_profile_subject.profile_subject_subject_id in (34, 46) and qlhs_profile.profile_year < "'.($year +1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hotrotieno_hocky1_new'), 
                DB::raw('MAX(CASE when qlhs_profile_history.level_new <> "" and qlhs_profile.profile_year < "'.($year +1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hotroVHTT_hocky1_new'),
                    DB::raw('MAX(
                            CASE
                            WHEN ( qlhs_profile_history.level_old <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id in (34, 46)
                            AND qlhs_profile.profile_year < "'.$year.'-06-01"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01"
                                )
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.$year.' AND kp.id_doituong = 94 AND yearold.qlhs_thcd_trangthai_HK2 = 1 AND yearold.qlhs_thcd_trangthai_HTBT_TA_HK2 = 1) 
                            OR (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id in (34, 46)
                            AND qlhs_profile.profile_year < "'.$year.'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 94 AND yearcur.qlhs_thcd_trangthai = 1 AND yearcur.qlhs_thcd_trangthai_HTBT_TA = 1) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS NHUCAUAN'),
                    DB::raw('MAX(
                            CASE
                            WHEN (qlhs_profile_history.level_old <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id in (34, 46)
                            AND qlhs_profile.profile_year < "'.$year.'-06-01"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01"
                                )
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.$year.' AND kp.id_doituong = 98 AND yearold.qlhs_thcd_trangthai_HK2 = 1 AND yearold.qlhs_thcd_trangthai_HTBT_TO_HK2 = 1) 
                            OR (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id in (34, 46)
                            AND qlhs_profile.profile_year < "'.$year.'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 98 AND yearcur.qlhs_thcd_trangthai = 1 AND yearcur.qlhs_thcd_trangthai_HTBT_TO = 1) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS NHUCAUO'),
                    DB::raw('MAX(
                            CASE
                            WHEN (qlhs_profile_history.level_old <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 46
                            AND qlhs_profile.profile_year < "'.$year.'-06-01"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01"
                                )
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.$year.' AND kp.id_doituong = 115 AND yearold.qlhs_thcd_trangthai_HK2 = 1 AND yearold.qlhs_thcd_trangthai_HTBT_VHTT_HK2 = 1) 
                            OR (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 46
                            AND qlhs_profile.profile_year < "'.$year.'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 115 AND yearcur.qlhs_thcd_trangthai = 1 AND yearcur.qlhs_thcd_trangthai_HTBT_VHTT = 1) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS NHUCAUTT'),
                    DB::raw('MAX(
                            CASE
                            WHEN (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id in (34, 46)
                            AND qlhs_profile.profile_year < "'.($year +1).'-06-01"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year +1).'-01-01"
                                )
                            )
                            AND kp.months in (1,2,3,4,5) and kp.years = '.($year +1).' AND kp.id_doituong = 94 AND yearcur.qlhs_thcd_trangthai_HK2 = 1 AND yearcur.qlhs_thcd_trangthai_HTBT_TA_HK2 = 1) 
                            OR (qlhs_profile_history.level_new <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id in (34, 46)
                            AND qlhs_profile.profile_year < "'.($year +1).'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year +1).'-05-31"
                                )
                            ) AND kp.months in (9,10,11,12) and kp.years = '.($year +1).' AND kp.id_doituong = 94) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS DUTOANAN'),
                    DB::raw('MAX(
                            CASE
                            WHEN (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id in (34, 46)
                            AND qlhs_profile.profile_year < "'.($year +1).'-06-01"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year +1).'-01-01"
                                )
                            )
                            AND kp.months in (1,2,3,4,5) and kp.years = '.($year +1).' AND kp.id_doituong = 98 AND yearcur.qlhs_thcd_trangthai_HK2 = 1 AND yearcur.qlhs_thcd_trangthai_HTBT_TO_HK2 = 1) 
                            OR (qlhs_profile_history.level_new <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id in (34, 46)
                            AND qlhs_profile.profile_year < "'.($year +1).'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year +1).'-05-31"
                                )
                            ) AND kp.months in (9,10,11,12) and kp.years = '.($year +1).' AND kp.id_doituong = 98) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS DUTOANO'),
                    DB::raw('MAX(
                            CASE
                            WHEN (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 46
                            AND qlhs_profile.profile_year < "'.($year +1).'-06-01"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year +1).'-01-01"
                                )
                            )
                            AND kp.months in (1,2,3,4,5) and kp.years = '.($year +1).' AND kp.id_doituong = 115 AND yearcur.qlhs_thcd_trangthai_HK2 = 1 AND yearcur.qlhs_thcd_trangthai_HTBT_VHTT_HK2 = 1) 
                            OR (qlhs_profile_history.level_new <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 46
                            AND qlhs_profile.profile_year < "'.($year +1).'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year +1).'-05-31"
                                )
                            ) AND kp.months in (9,10,11,12) and kp.years = '.($year +1).' AND kp.id_doituong = 115) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS DUTOANTT'))
            ->groupBy('qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years');

            $getDataType2 = DB::table(DB::raw("({$getDataTypeB->toSql()}) as m"))
                ->mergeBindings( $getDataTypeB )
                ->select('m.profile_id',
                        'm.profile_year',
                        'm.level_old',
                        'm.level_cur',
                        'm.level_new',
                        'm.profile_leaveschool_date',
                        'm.hotrotienan', 'm.hotrotieno', 'm.hotroVHTT', //'m.ongoaitruong', 
                        'm.hotrotienan_hocky2_old', 'm.hotrotieno_hocky2_old', 'm.hotroVHTT_hocky2_old',
                        'm.hotrotienan_hocky1_cur', 'm.hotrotieno_hocky1_cur', 'm.hotroVHTT_hocky1_cur',
                        'm.hotrotienan_hocky2_cur', 'm.hotrotieno_hocky2_cur', 'm.hotroVHTT_hocky2_cur',
                        'm.hotrotienan_hocky1_new', 'm.hotrotieno_hocky1_new', 'm.hotroVHTT_hocky1_new',
                        DB::raw('SUM(m.NHUCAUAN) as NHUCAUAN'), DB::raw('SUM(m.NHUCAUO) as NHUCAUO'), DB::raw('SUM(m.NHUCAUTT) as NHUCAUTT'),
                        DB::raw('SUM(m.DUTOANAN) as DUTOANAN'), DB::raw('SUM(m.DUTOANO) as DUTOANO'), DB::raw('SUM(m.DUTOANTT) as DUTOANTT'))

                ->groupBy('m.profile_id',
                        'm.profile_year',
                        'm.level_old',
                        'm.level_cur',
                        'm.level_new',
                        'm.profile_leaveschool_date',
                        'm.hotrotienan', 'm.hotrotieno', 'm.hotroVHTT', //'m.ongoaitruong', 
                        'm.hotrotienan_hocky2_old', 'm.hotrotieno_hocky2_old', 'm.hotroVHTT_hocky2_old',
                        'm.hotrotienan_hocky1_cur', 'm.hotrotieno_hocky1_cur', 'm.hotroVHTT_hocky1_cur',
                        'm.hotrotienan_hocky2_cur', 'm.hotrotieno_hocky2_cur', 'm.hotroVHTT_hocky2_cur',
                        'm.hotrotienan_hocky1_new', 'm.hotrotieno_hocky1_new', 'm.hotroVHTT_hocky1_new')
                ->get();

            $getDataTypeC = DB::table('qlhs_profile')
            ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id in (34, 46, 72) AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.($year + 1).' AND (qlhs_profile_subject.end_year is null OR qlhs_profile_subject.end_year > '.($year + 1).')'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.($year + 1).'-'.($year + 2).'"'))
            ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$school_id.' AND kp.id_doituong in (94, 98, 115) AND '.$year.' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 2).''))
            ->leftJoin('qlhs_tonghopchedo as yearold', 'yearold.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearold.qlhs_thcd_nam = '.$year.''))
            ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', 'profile_id')
            ->leftJoin('qlhs_tonghopchedo as yearnew', 'yearnew.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearnew.qlhs_thcd_nam = '.($year + 2).''))
            ->where('qlhs_profile.profile_school_id', '=', $school_id)
            ->where('qlhs_profile.profile_year', '>', ($year + 1).'-05-31')
            ->where('qlhs_profile.profile_year', '<', ($year + 2).'-06-01')
            ->where('yearcur.qlhs_thcd_nam', '=', ($year + 1))
            ->select('qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years', 
                DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id in (34, 46) then 1 else 0 END) as hotrotienan'), 
                DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id in (34, 46) then 1 else 0 END) as hotrotieno'), 
                DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 46 then 1 else 0 END) as hotroVHTT'), 
                // DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 72 then 1 else 0 END) as ongoaitruong'), 

                DB::raw('MAX(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile_subject.profile_subject_subject_id in (34, 46) and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hotrotienan_hocky2_old'), 
                DB::raw('MAX(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile_subject.profile_subject_subject_id in (34, 46) and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hotrotieno_hocky2_old'), 
                DB::raw('MAX(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hotroVHTT_hocky2_old'), 

                DB::raw('MAX(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile_subject.profile_subject_subject_id in (34, 46) and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hotrotienan_hocky1_cur'), 
                DB::raw('MAX(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile_subject.profile_subject_subject_id in (34, 46) and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hotrotieno_hocky1_cur'), 
                DB::raw('MAX(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hotroVHTT_hocky1_cur'), 

                DB::raw('MAX(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile_subject.profile_subject_subject_id in (34, 46) and qlhs_profile.profile_year < "'.($year +1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hotrotienan_hocky2_cur'), 
                DB::raw('MAX(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile_subject.profile_subject_subject_id in (34, 46) and qlhs_profile.profile_year < "'.($year +1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hotrotieno_hocky2_cur'), 
                DB::raw('MAX(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.($year +1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hotroVHTT_hocky2_cur'), 

                DB::raw('MAX(CASE when qlhs_profile_history.level_new <> "" and qlhs_profile_subject.profile_subject_subject_id in (34, 46) and qlhs_profile.profile_year < "'.($year +1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hotrotienan_hocky1_new'), 
                DB::raw('MAX(CASE when qlhs_profile_history.level_new <> "" and qlhs_profile_subject.profile_subject_subject_id in (34, 46) and qlhs_profile.profile_year < "'.($year +1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hotrotieno_hocky1_new'), 
                DB::raw('MAX(CASE when qlhs_profile_history.level_new <> "" and qlhs_profile.profile_year < "'.($year +1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hotroVHTT_hocky1_new'),
                    // DB::raw('MAX(
                    //         CASE
                    //         WHEN ( qlhs_profile_history.level_old <> ""
                    //         AND qlhs_profile_subject.profile_subject_subject_id in (34, 46)
                    //         AND qlhs_profile.profile_year < "'.($year + 1).'-06-01"
                    //         AND (
                    //             qlhs_profile.profile_leaveschool_date IS NULL
                    //             OR (
                    //                 qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01"
                    //             )
                    //         ) AND kp.months in (1,2,3,4,5) and kp.years = '.($year + 1).' AND kp.id_doituong = 94) 
                    //         OR (qlhs_profile_history.level_cur <> ""
                    //         AND qlhs_profile_subject.profile_subject_subject_id in (34, 46)
                    //         AND qlhs_profile.profile_year < "'.($year + 1).'-12-31"
                    //         AND (
                    //             qlhs_profile.profile_leaveschool_date IS NULL
                    //             OR (
                    //                 qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31"
                    //             )
                    //         )
                    //         AND kp.months in (9,10,11,12) and kp.years = '.($year + 1).' AND kp.id_doituong = 94) THEN
                    //             kp.value_m
                    //         ELSE
                    //             0
                    //         END
                    //     ) AS NHUCAUAN'),
                    // DB::raw('MAX(
                    //         CASE
                    //         WHEN (qlhs_profile_history.level_old <> ""
                    //         AND qlhs_profile_subject.profile_subject_subject_id in (34, 46)
                    //         AND qlhs_profile.profile_year < "'.($year + 1).'-06-01"
                    //         AND (
                    //             qlhs_profile.profile_leaveschool_date IS NULL
                    //             OR (
                    //                 qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01"
                    //             )
                    //         ) AND kp.months in (1,2,3,4,5) and kp.years = '.($year + 1).' AND kp.id_doituong = 98) 
                    //         OR (qlhs_profile_history.level_cur <> ""
                    //         AND qlhs_profile_subject.profile_subject_subject_id in (34, 46)
                    //         AND qlhs_profile.profile_year < "'.($year + 1).'-12-31"
                    //         AND (
                    //             qlhs_profile.profile_leaveschool_date IS NULL
                    //             OR (
                    //                 qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31"
                    //             )
                    //         )
                    //         AND kp.months in (9,10,11,12) and kp.years = '.($year + 1).' AND kp.id_doituong = 98) THEN
                    //             kp.value_m
                    //         ELSE
                    //             0
                    //         END
                    //     ) AS NHUCAUO'),
                    // DB::raw('MAX(
                    //         CASE
                    //         WHEN (qlhs_profile_history.level_old <> ""
                    //         AND qlhs_profile_subject.profile_subject_subject_id = 46
                    //         AND qlhs_profile.profile_year < "'.($year + 1).'-06-01"
                    //         AND (
                    //             qlhs_profile.profile_leaveschool_date IS NULL
                    //             OR (
                    //                 qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01"
                    //             )
                    //         ) AND kp.months in (1,2,3,4,5) and kp.years = '.($year + 1).' AND kp.id_doituong = 115) 
                    //         OR (qlhs_profile_history.level_cur <> ""
                    //         AND qlhs_profile_subject.profile_subject_subject_id = 46
                    //         AND qlhs_profile.profile_year < "'.($year + 1).'-12-31"
                    //         AND (
                    //             qlhs_profile.profile_leaveschool_date IS NULL
                    //             OR (
                    //                 qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31"
                    //             )
                    //         )
                    //         AND kp.months in (9,10,11,12) and kp.years = '.($year + 1).' AND kp.id_doituong = 115) THEN
                    //             kp.value_m
                    //         ELSE
                    //             0
                    //         END
                    //     ) AS NHUCAUTT'),
                    DB::raw('MAX(
                            CASE
                            WHEN (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id in (34, 46)
                            AND qlhs_profile.profile_year < "'.($year + 2).'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year + 2).'-05-31"
                                )
                            ) AND kp.months in (9,10,11,12) and kp.years = '.($year + 2).' AND kp.id_doituong = 94) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS DUTOANAN'),
                    DB::raw('MAX(
                            CASE
                            WHEN (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id in (34, 46)
                            AND qlhs_profile.profile_year < "'.($year + 2).'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year + 2).'-05-31"
                                )
                            ) AND kp.months in (9,10,11,12) and kp.years = '.($year + 2).' AND kp.id_doituong = 98) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS DUTOANO'),
                    DB::raw('MAX(
                            CASE
                            WHEN (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 46
                            AND qlhs_profile.profile_year < "'.($year + 2).'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year + 2).'-05-31"
                                )
                            ) AND kp.months in (9,10,11,12) and kp.years = '.($year + 2).' AND kp.id_doituong = 115) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS DUTOANTT'))
            ->groupBy('qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years');

            $getDataType3 = DB::table(DB::raw("({$getDataTypeC->toSql()}) as m"))
                ->mergeBindings( $getDataTypeC )
                ->select('m.profile_id',
                        'm.profile_year',
                        'm.level_old',
                        'm.level_cur',
                        'm.level_new',
                        'm.profile_leaveschool_date',
                        'm.hotrotienan', 'm.hotrotieno', 'm.hotroVHTT', //'m.ongoaitruong', 
                        'm.hotrotienan_hocky2_old', 'm.hotrotieno_hocky2_old', 'm.hotroVHTT_hocky2_old',
                        'm.hotrotienan_hocky1_cur', 'm.hotrotieno_hocky1_cur', 'm.hotroVHTT_hocky1_cur',
                        'm.hotrotienan_hocky2_cur', 'm.hotrotieno_hocky2_cur', 'm.hotroVHTT_hocky2_cur',
                        'm.hotrotienan_hocky1_new', 'm.hotrotieno_hocky1_new', 'm.hotroVHTT_hocky1_new',
                        // DB::raw('SUM(m.NHUCAUAN) as NHUCAUAN'), DB::raw('SUM(m.NHUCAUO) as NHUCAUO'), DB::raw('SUM(m.NHUCAUTT) as NHUCAUTT'),
                        DB::raw('SUM(m.DUTOANAN) as DUTOANAN'), DB::raw('SUM(m.DUTOANO) as DUTOANO'), DB::raw('SUM(m.DUTOANTT) as DUTOANTT'))

                ->groupBy('m.profile_id',
                        'm.profile_year',
                        'm.level_old',
                        'm.level_cur',
                        'm.level_new',
                        'm.profile_leaveschool_date',
                        'm.hotrotienan', 'm.hotrotieno', 'm.hotroVHTT', //'m.ongoaitruong', 
                        'm.hotrotienan_hocky2_old', 'm.hotrotieno_hocky2_old', 'm.hotroVHTT_hocky2_old',
                        'm.hotrotienan_hocky1_cur', 'm.hotrotieno_hocky1_cur', 'm.hotroVHTT_hocky1_cur',
                        'm.hotrotienan_hocky2_cur', 'm.hotrotieno_hocky2_cur', 'm.hotroVHTT_hocky2_cur',
                        'm.hotrotienan_hocky1_new', 'm.hotrotieno_hocky1_new', 'm.hotroVHTT_hocky1_new')
                ->get();

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


                $insert_hosobaocao_id = DB::table('qlhs_hosobaocao')->insertGetId(['report_name' => $report_name, 'report_type' => $type_code, 'report_date' => $current_date, 'created_at' => $current_date, 'updated_at' => $current_date, 'create_userid' => $current_user_id, 'update_userid' => $current_user_id, 'report_user' => $user_create, 'report_user_sign' => $user_sign, 'report_attach_name' => '', 'report_nature' => $status, 'report_year' => $year, 'report_id_truong' => $school_id, 'report_note' => $note, 'report' => 'HTBT']);


                if (!is_null($insert_hosobaocao_id) && $insert_hosobaocao_id > 0) {
                    $this->exportforSchoolsHTBT($insert_hosobaocao_id);

                    if (file_exists(storage_path().'/exceldownload/HTBT/'.$type_code.'.xlsx')) {
                        
                    }
                    else {
                        $deleteHoso = DB::table('qlhs_hosobaocao')->where('report_type', 'LIKE', '%'.$type_code.'%')->delete();
                        $deleteHTBT = DB::table('qlhs_hotrohocsinhbantru')->where('type_code', 'LIKE', '%'.$type_code.'%')->delete();
                        
                    }
                }
                return $insert_hosobaocao_id;
            }
            else {
                return 0;
            }

            // return $result;
        } catch (Exception $e) {
            return $e;
        }       
    }

    public function insertHTBT($getDataType, $type, $current_user_id, $school_id, $year, $time){
        try {
            $bool = TRUE;
            $count = 1;
            
            $type_code = 'HTBT-' . $current_user_id . '-' . $school_id . '-' . $year . '' . ($year + 1) . '-' . $time;

            foreach ($getDataType as $value) {

                $nhucautienan = 0;
                $nhucautieno = 0;
                $nhucauVHTT = 0;
                $dutoantienan = 0;
                $dutoantieno = 0;
                $dutoanVHTT = 0;
                $tongnhucau = 0;
                $tongdutoan = 0;

                if ($value->{'hotroVHTT'} == 1 || $value->{'hotrotienan'} == 1 || $value->{'hotrotieno'} == 1) {

                    $dutoantienan = $value->{'DUTOANAN'};
                    $dutoantieno = $value->{'DUTOANO'};
                    $dutoanVHTT = $value->{'DUTOANTT'};

                    if ($type != 3) {
                        $nhucautienan = $value->{'NHUCAUAN'};
                        $nhucautieno = $value->{'NHUCAUO'};
                        $nhucauVHTT = $value->{'NHUCAUTT'};
                        $tongnhucau = ($nhucautienan + $nhucautieno + $nhucauVHTT);
                    }
                    
                    $tongdutoan = ($dutoantienan + $dutoantieno + $dutoanVHTT);

                    if ($tongnhucau > 0 || $tongdutoan > 0) {
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
                            'type' => $type,
                            'trangthai_pheduyet_HTBT' => 1,
                            'trangthai_thamdinh_HTBT' => 1
                            ]);

                        $count++;
                        if ($insert_type == 0) {
                            
                            $deleteHTBT = DB::table('qlhs_hotrohocsinhbantru')->where('type_code', 'LIKE', '%'.$type_code.'%')->where('type', '=', $type)->delete();
                            $count--;
                            break;
                        }
                    }
                }
            }
            
            if ($count > 1) {
                $bool = TRUE;
            }
            else {
                $bool = FALSE;
            }

            return TRUE;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function exportforSchoolsHTBT($id, $type = true){
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
            $data_results['aCount'] = $this->countValueHTBT('1',$getSchoolName->report_type);
            $data_results['bCount'] = $this->countValueHTBT('2',$getSchoolName->report_type);
            $data_results['cCount'] = $this->countValueHTBT('3',$getSchoolName->report_type);
            $data_results['TotalCount'] = $this->countValueHTBT(null,$getSchoolName->report_type);
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
        $this->addCellExcelHTBT($data_results, $getSchoolName->report_type, $type);
    }

    private function addCellExcelHTBT($data_results, $filename, $type = true){
        $excel =    Excel::load(storage_path().'/exceltemplate/laphosoHTBT.xlsx', function($reader) use($data_results){
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
            $reader->getActiveSheet()->setCellValueByColumnAndRow(0, 2, 'NHU CẦU KINH PHÍ NĂM '.$data_results['report_year'].', DỰ TOÁN NĂM '.($data_results['report_year'] + 1).' ĐỐI VỚI CHÍNH SÁCH HỖ TRỢ HỌC SINH BÁN TRÚ THEO QUYẾT ĐỊNH SỐ 85/2010/QĐ-TTG CỦA THỦ TƯỚNG CHÍNH PHỦ')->getStyle('A2')->applyFromArray($FontArray)->applyFromArray($style);
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
                    $col = 0;   $row++;

                    // $strYear = substr((string)$value->history_year, 0, 4);
                    // if ($strYear < $data_results['report_year']) {
                    //  $class_lv1 = $value->level_next_1;
                    //  $class_lv2 = $value->level_next_2;
                    //  $class_lv3 = $value->level_next_3;
                    // }

                    // if ($strYear == $data_results['report_year']) {
                    //  $class_lv1 = $value->level_next;
                    //  $class_lv2 = $value->level_next_1;
                    //  $class_lv3 = $value->level_next_2;
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
                    $col = 0;   $row++;

                    // $strYear = substr((string)$value->history_year, 0, 4);
                    // if ($strYear < $data_results['report_year']) {
                    //  $class_lv1 = $value->level_next;
                    //  $class_lv2 = $value->level_next_1;
                    //  $class_lv3 = $value->level_next_2;
                    // }

                    // if ($strYear == $data_results['report_year']) {
                    //  $class_lv1 = 0;
                    //  $class_lv2 = $value->level_next_1;
                    //  $class_lv3 = $value->level_next_2;
                    // }

                    // if ($strYear > $data_results['report_year']) {
                    //  $class_lv1 = 0;
                    //  $class_lv2 = 0;
                    //  $class_lv3 = $value->level_next_1;
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
                    $col = 0;   $row++;

                    // $strYear = substr((string)$value->history_year, 0, 4);
                    // if ($strYear < $data_results['report_year']) {
                    //  $class_lv1 = $value->level_next;
                    //  $class_lv2 = $value->level_next_1;
                    //  $class_lv3 = $value->level_next_2;
                    // }

                    // if ($strYear == $data_results['report_year']) {
                    //  $class_lv1 = 0;
                    //  $class_lv2 = $value->level_next_1;
                    //  $class_lv3 = $value->level_next_2;
                    // }

                    // if ($strYear > $data_results['report_year']) {
                    //  $class_lv1 = 0;
                    //  $class_lv2 = 0;
                    //  $class_lv3 = $value->level_next_1;
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
    
    public function countValueHTBT($type = null,$code){

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

//---------------------------------------------------------------------HSKT------------------------------------------------------------------------
    public function getDataHSKT($school_id, $year, $report_name, $user_sign, $user_create, $note, $status){
        $result = [];
        try {

            $current_user_id = Auth::user()->id;
            $current_date = Carbon::now('Asia/Ho_Chi_Minh');


            $check = TRUE;

            $getDataTypeA = DB::table('qlhs_profile')
            ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id = 74 AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.$year.' AND (qlhs_profile_subject.end_year is null OR qlhs_profile_subject.end_year > '.$year.')'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))
            ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$school_id.' AND kp.id_doituong in (95,100) AND '.($year - 1).' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 1).''))
            ->leftJoin('qlhs_tonghopchedo as yearold', 'yearold.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearold.qlhs_thcd_nam = '.($year - 1).' and (yearold.qlhs_thcd_trangthai_HSKT_HB = 1 or yearold.qlhs_thcd_trangthai_HSKT_DDHT = 1 or yearold.qlhs_thcd_trangthai_HSKT_HB_HK2 = 1 or yearold.qlhs_thcd_trangthai_HSKT_DDHT_HK2 = 1)'))
            // ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', 'profile_id')
            ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearcur.qlhs_thcd_nam = '.$year.' and (yearcur.qlhs_thcd_trangthai_HSKT_HB = 1 or yearcur.qlhs_thcd_trangthai_HSKT_DDHT = 1 or yearcur.qlhs_thcd_trangthai_HSKT_HB_HK2 = 1 or yearcur.qlhs_thcd_trangthai_HSKT_DDHT_HK2 = 1)'))
            ->leftJoin('qlhs_tonghopchedo as yearnew', 'yearnew.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearnew.qlhs_thcd_nam = '.($year + 1)))
            ->where('qlhs_profile.profile_school_id', '=', $school_id)
            ->where('qlhs_profile.profile_year', '<', $year.'-06-01')
            // ->where('yearcur.qlhs_thcd_nam', '=', DB::raw($year.' and (yearcur.qlhs_thcd_trangthai = 1 or yearcur.qlhs_thcd_trangthai_HK2 = 1 or yearold.qlhs_thcd_trangthai = 1 or yearold.qlhs_thcd_trangthai_HK2 = 1)'))
            ->select('qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years', 
                DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 74 then 1 else 0 END) as hotrohocbong'), 
                DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 74 then 1 else 0 END) as hotromuadodunght'),
                
                DB::raw('(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hotro_hocky2_old'), 
                DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hotro_hocky1_cur'), 
                DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.($year + 1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hotro_hocky2_cur'), 
                DB::raw('(CASE when qlhs_profile_history.level_new <> "" and qlhs_profile.profile_year < "'.($year + 1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hotro_hocky1_new'),
                    DB::raw('MAX(
                            CASE
                            WHEN ( qlhs_profile_history.level_old <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 74
                            AND qlhs_profile.profile_year < "'.$year.'-06-01"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01"
                                )
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.$year.' AND kp.id_doituong = 95 AND yearold.qlhs_thcd_trangthai_HK2 = 1 AND yearold.qlhs_thcd_trangthai_HSKT_HB_HK2 = 1) 
                            OR (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 74
                            AND qlhs_profile.profile_year < "'.$year.'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 95 AND yearcur.qlhs_thcd_trangthai = 1 AND yearcur.qlhs_thcd_trangthai_HSKT_HB = 1) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS NHUCAUHB'),
                    DB::raw('MAX(
                            CASE
                            WHEN (qlhs_profile_history.level_old <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 74
                            AND qlhs_profile.profile_year < "'.$year.'-06-01"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01"
                                )
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.$year.' AND kp.id_doituong = 100 AND yearold.qlhs_thcd_trangthai_HK2 = 1 AND yearold.qlhs_thcd_trangthai_HSKT_DDHT_HK2 = 1) 
                            OR (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 74
                            AND qlhs_profile.profile_year < "'.$year.'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 100 AND yearcur.qlhs_thcd_trangthai = 1 AND yearcur.qlhs_thcd_trangthai_HSKT_DDHT = 1) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS NHUCAUDDHT'),
                    DB::raw('MAX(
                            CASE
                            WHEN (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 74
                            AND qlhs_profile.profile_year < "'.($year +1).'-06-01"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year +1).'-01-01"
                                )
                            )
                            AND kp.months in (1,2,3,4,5) and kp.years = '.($year +1).' AND kp.id_doituong = 95 AND yearcur.qlhs_thcd_trangthai_HK2 = 1 AND yearcur.qlhs_thcd_trangthai_HSKT_HB_HK2 = 1) 
                            OR (qlhs_profile_history.level_new <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 74
                            AND qlhs_profile.profile_year < "'.($year +1).'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year +1).'-05-31"
                                )
                            ) AND kp.months in (9,10,11,12) and kp.years = '.($year +1).' AND kp.id_doituong = 95) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS DUTOANHB'),
                    DB::raw('MAX(
                            CASE
                            WHEN (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 74
                            AND qlhs_profile.profile_year < "'.($year +1).'-06-01"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year +1).'-01-01"
                                )
                            )
                            AND kp.months in (1,2,3,4,5) and kp.years = '.($year +1).' AND kp.id_doituong = 100 AND yearcur.qlhs_thcd_trangthai_HK2 = 1 AND yearcur.qlhs_thcd_trangthai_HSKT_DDHT_HK2 = 1) 
                            OR (qlhs_profile_history.level_new <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 74
                            AND qlhs_profile.profile_year < "'.($year +1).'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year +1).'-05-31"
                                )
                            ) AND kp.months in (9,10,11,12) and kp.years = '.($year +1).' AND kp.id_doituong = 100) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS DUTOANDDHT'))
            ->groupBy('qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years');

            $getDataType1 = DB::table(DB::raw("({$getDataTypeA->toSql()}) as m"))
                ->mergeBindings( $getDataTypeA )
                ->select('m.profile_id',
                        'm.profile_year',
                        'm.level_old',
                        'm.level_cur',
                        'm.level_new',
                        'm.profile_leaveschool_date',
                        'm.hotrohocbong', 'm.hotromuadodunght',
                        'm.hotro_hocky2_old', 
                        'm.hotro_hocky1_cur', 
                        'm.hotro_hocky2_cur', 
                        'm.hotro_hocky1_new', 
                        DB::raw('SUM(m.NHUCAUHB) as NHUCAUHB'), DB::raw('SUM(m.NHUCAUDDHT) as NHUCAUDDHT'), 
                        DB::raw('SUM(m.DUTOANHB) as DUTOANHB'), DB::raw('SUM(m.DUTOANDDHT) as DUTOANDDHT'))

                ->groupBy('m.profile_id',
                        'm.profile_year',
                        'm.level_old',
                        'm.level_cur',
                        'm.level_new',
                        'm.profile_leaveschool_date',
                        'm.hotrohocbong', 'm.hotromuadodunght',
                        'm.hotro_hocky2_old', 
                        'm.hotro_hocky1_cur', 
                        'm.hotro_hocky2_cur', 
                        'm.hotro_hocky1_new')
                ->get();


            $getDataTypeB = DB::table('qlhs_profile')
            ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id = 74 AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.$year.' AND (qlhs_profile_subject.end_year is null OR qlhs_profile_subject.end_year > '.$year.')'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))
            ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$school_id.' AND kp.id_doituong in (95,100) AND '.($year - 1).' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 1).''))
            ->leftJoin('qlhs_tonghopchedo as yearold', 'yearold.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearold.qlhs_thcd_nam = '.($year - 1).' and (yearold.qlhs_thcd_trangthai_HSKT_HB = 1 or yearold.qlhs_thcd_trangthai_HSKT_DDHT = 1 or yearold.qlhs_thcd_trangthai_HSKT_HB_HK2 = 1 or yearold.qlhs_thcd_trangthai_HSKT_DDHT_HK2 = 1)'))
            // ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', 'profile_id')
            ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearcur.qlhs_thcd_nam = '.$year))
            ->leftJoin('qlhs_tonghopchedo as yearnew', 'yearnew.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearnew.qlhs_thcd_nam = '.($year + 1)))
            ->where('qlhs_profile.profile_school_id', '=', $school_id)
            ->where('qlhs_profile.profile_year', '>', $year.'-05-31')
            ->where('qlhs_profile.profile_year', '<', ($year + 1).'-06-01')
            // ->where('yearcur.qlhs_thcd_nam', '=', DB::raw($year.' and (yearcur.qlhs_thcd_trangthai = 1 or yearcur.qlhs_thcd_trangthai_HK2 = 1)'))
            ->select('qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years', 
                DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 74 then 1 else 0 END) as hotrohocbong'), 
                DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 74 then 1 else 0 END) as hotromuadodunght'),
                
                DB::raw('(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hotro_hocky2_old'), 
                DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hotro_hocky1_cur'), 
                DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.($year + 1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hotro_hocky2_cur'), 
                DB::raw('(CASE when qlhs_profile_history.level_new <> "" and qlhs_profile.profile_year < "'.($year + 1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hotro_hocky1_new'),
                    DB::raw('MAX(
                            CASE
                            WHEN ( qlhs_profile_history.level_old <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 74
                            AND qlhs_profile.profile_year < "'.$year.'-06-01"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01"
                                )
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.$year.' AND kp.id_doituong = 95 AND yearold.qlhs_thcd_trangthai_HK2 = 1 AND yearold.qlhs_thcd_trangthai_HSKT_HB_HK2 = 1) 
                            OR (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 74
                            AND qlhs_profile.profile_year < "'.$year.'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 95 AND yearcur.qlhs_thcd_trangthai = 1 AND yearcur.qlhs_thcd_trangthai_HSKT_HB = 1) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS NHUCAUHB'),
                    DB::raw('MAX(
                            CASE
                            WHEN (qlhs_profile_history.level_old <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 74
                            AND qlhs_profile.profile_year < "'.$year.'-06-01"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01"
                                )
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.$year.' AND kp.id_doituong = 100 AND yearold.qlhs_thcd_trangthai_HK2 = 1 AND yearold.qlhs_thcd_trangthai_HSKT_DDHT_HK2 = 1) 
                            OR (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 74
                            AND qlhs_profile.profile_year < "'.$year.'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 100 AND yearcur.qlhs_thcd_trangthai = 1 AND yearcur.qlhs_thcd_trangthai_HSKT_DDHT = 1) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS NHUCAUDDHT'),
                    DB::raw('MAX(
                            CASE
                            WHEN (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 74
                            AND qlhs_profile.profile_year < "'.($year +1).'-06-01"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year +1).'-01-01"
                                )
                            )
                            AND kp.months in (1,2,3,4,5) and kp.years = '.($year +1).' AND kp.id_doituong = 95 AND yearcur.qlhs_thcd_trangthai_HK2 = 1 AND yearcur.qlhs_thcd_trangthai_HSKT_HB_HK2 = 1) 
                            OR (qlhs_profile_history.level_new <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 74
                            AND qlhs_profile.profile_year < "'.($year +1).'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year +1).'-05-31"
                                )
                            ) AND kp.months in (9,10,11,12) and kp.years = '.($year +1).' AND kp.id_doituong = 95) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS DUTOANHB'),
                    DB::raw('MAX(
                            CASE
                            WHEN (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 74
                            AND qlhs_profile.profile_year < "'.($year +1).'-06-01"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year +1).'-01-01"
                                )
                            )
                            AND kp.months in (1,2,3,4,5) and kp.years = '.($year +1).' AND kp.id_doituong = 100 AND yearcur.qlhs_thcd_trangthai_HK2 = 1 AND yearcur.qlhs_thcd_trangthai_HSKT_DDHT_HK2 = 1) 
                            OR (qlhs_profile_history.level_new <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 74
                            AND qlhs_profile.profile_year < "'.($year +1).'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year +1).'-05-31"
                                )
                            ) AND kp.months in (9,10,11,12) and kp.years = '.($year +1).' AND kp.id_doituong = 100) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS DUTOANDDHT'))
            ->groupBy('qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years');

            $getDataType2 = DB::table(DB::raw("({$getDataTypeB->toSql()}) as m"))
                ->mergeBindings( $getDataTypeB )
                ->select('m.profile_id',
                        'm.profile_year',
                        'm.level_old',
                        'm.level_cur',
                        'm.level_new',
                        'm.profile_leaveschool_date',
                        'm.hotrohocbong', 'm.hotromuadodunght',
                        'm.hotro_hocky2_old', 
                        'm.hotro_hocky1_cur', 
                        'm.hotro_hocky2_cur', 
                        'm.hotro_hocky1_new', 
                        DB::raw('SUM(m.NHUCAUHB) as NHUCAUHB'), DB::raw('SUM(m.NHUCAUDDHT) as NHUCAUDDHT'), 
                        DB::raw('SUM(m.DUTOANHB) as DUTOANHB'), DB::raw('SUM(m.DUTOANDDHT) as DUTOANDDHT'))

                ->groupBy('m.profile_id',
                        'm.profile_year',
                        'm.level_old',
                        'm.level_cur',
                        'm.level_new',
                        'm.profile_leaveschool_date',
                        'm.hotrohocbong', 'm.hotromuadodunght',
                        'm.hotro_hocky2_old', 
                        'm.hotro_hocky1_cur', 
                        'm.hotro_hocky2_cur', 
                        'm.hotro_hocky1_new')
                ->get();

            $getDataTypeC = DB::table('qlhs_profile')
            ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id = 74 AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.($year + 1).' AND (qlhs_profile_subject.end_year is null OR qlhs_profile_subject.end_year > '.($year + 1).')'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.($year + 1).'-'.($year + 2).'"'))
            ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$school_id.' AND kp.id_doituong in (95,100) AND '.$year.' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 2).''))
            ->leftJoin('qlhs_tonghopchedo as yearold', 'yearold.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearold.qlhs_thcd_nam = '.$year.''))
            ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearcur.qlhs_thcd_nam = '.($year + 1)))
            ->leftJoin('qlhs_tonghopchedo as yearnew', 'yearnew.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearnew.qlhs_thcd_nam = '.($year + 2).''))
            ->where('qlhs_profile.profile_school_id', '=', $school_id)
            ->where('qlhs_profile.profile_year', '>', ($year + 1).'-05-31')
            ->where('qlhs_profile.profile_year', '<', ($year + 2).'-06-01')
            // ->where('yearcur.qlhs_thcd_nam', '=', ($year + 1))
            ->select('qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years', 
                DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 74 then 1 else 0 END) as hotrohocbong'), 
                DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 74 then 1 else 0 END) as hotromuadodunght'),
                
                DB::raw('(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hotro_hocky2_old'), 
                DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hotro_hocky1_cur'), 
                DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.($year + 1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hotro_hocky2_cur'), 
                DB::raw('(CASE when qlhs_profile_history.level_new <> "" and qlhs_profile.profile_year < "'.($year + 1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hotro_hocky1_new'),
                    // DB::raw('MAX(
                    //         CASE
                    //         WHEN ( qlhs_profile_history.level_old <> ""
                    //         AND qlhs_profile_subject.profile_subject_subject_id = 74
                    //         AND qlhs_profile.profile_year < "'.($year + 1).'-06-01"
                    //         AND (
                    //             qlhs_profile.profile_leaveschool_date IS NULL
                    //             OR (
                    //                 qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01"
                    //             )
                    //         ) AND kp.months in (1,2,3,4,5) and kp.years = '.($year + 1).' AND kp.id_doituong = 95) 
                    //         OR (qlhs_profile_history.level_cur <> ""
                    //         AND qlhs_profile_subject.profile_subject_subject_id = 74
                    //         AND qlhs_profile.profile_year < "'.($year + 1).'-12-31"
                    //         AND (
                    //             qlhs_profile.profile_leaveschool_date IS NULL
                    //             OR (
                    //                 qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31"
                    //             )
                    //         )
                    //         AND kp.months in (9,10,11,12) and kp.years = '.($year + 1).' AND kp.id_doituong = 95) THEN
                    //             kp.value_m
                    //         ELSE
                    //             0
                    //         END
                    //     ) AS NHUCAUHB'),
                    // DB::raw('MAX(
                    //         CASE
                    //         WHEN (qlhs_profile_history.level_old <> ""
                    //         AND qlhs_profile_subject.profile_subject_subject_id = 74
                    //         AND qlhs_profile.profile_year < "'.($year + 1).'-06-01"
                    //         AND (
                    //             qlhs_profile.profile_leaveschool_date IS NULL
                    //             OR (
                    //                 qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01"
                    //             )
                    //         ) AND kp.months in (1,2,3,4,5) and kp.years = '.($year + 1).' AND kp.id_doituong = 100) 
                    //         OR (qlhs_profile_history.level_cur <> ""
                    //         AND qlhs_profile_subject.profile_subject_subject_id = 74
                    //         AND qlhs_profile.profile_year < "'.($year + 1).'-12-31"
                    //         AND (
                    //             qlhs_profile.profile_leaveschool_date IS NULL
                    //             OR (
                    //                 qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31"
                    //             )
                    //         )
                    //         AND kp.months in (9,10,11,12) and kp.years = '.($year + 1).' AND kp.id_doituong = 100) THEN
                    //             kp.value_m
                    //         ELSE
                    //             0
                    //         END
                    //     ) AS NHUCAUDDHT'),
                    DB::raw('MAX(
                            CASE
                            WHEN (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 74
                            AND qlhs_profile.profile_year < "'.($year + 2).'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year + 2).'-05-31"
                                )
                            ) AND kp.months in (9,10,11,12) and kp.years = '.($year + 2).' AND kp.id_doituong = 95) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS DUTOANHB'),
                    DB::raw('MAX(
                            CASE
                            WHEN (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 74
                            AND qlhs_profile.profile_year < "'.($year + 2).'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year + 2).'-05-31"
                                )
                            ) AND kp.months in (9,10,11,12) and kp.years = '.($year + 2).' AND kp.id_doituong = 100) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS DUTOANDDHT'))
            ->groupBy('qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years');

            $getDataType3 = DB::table(DB::raw("({$getDataTypeC->toSql()}) as m"))
                ->mergeBindings( $getDataTypeC )
                ->select('m.profile_id',
                        'm.profile_year',
                        'm.level_old',
                        'm.level_cur',
                        'm.level_new',
                        'm.profile_leaveschool_date',
                        'm.hotrohocbong', 'm.hotromuadodunght',
                        'm.hotro_hocky2_old', 
                        'm.hotro_hocky1_cur', 
                        'm.hotro_hocky2_cur', 
                        'm.hotro_hocky1_new', 
                        // DB::raw('SUM(m.NHUCAUHB) as NHUCAUHB'), DB::raw('SUM(m.NHUCAUDDHT) as NHUCAUDDHT'), 
                        DB::raw('SUM(m.DUTOANHB) as DUTOANHB'), DB::raw('SUM(m.DUTOANDDHT) as DUTOANDDHT'))

                ->groupBy('m.profile_id',
                        'm.profile_year',
                        'm.level_old',
                        'm.level_cur',
                        'm.level_new',
                        'm.profile_leaveschool_date',
                        'm.hotrohocbong', 'm.hotromuadodunght',
                        'm.hotro_hocky2_old', 
                        'm.hotro_hocky1_cur', 
                        'm.hotro_hocky2_cur', 
                        'm.hotro_hocky1_new')
                ->get();

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


                $insert_hosobaocao_id = DB::table('qlhs_hosobaocao')->insertGetId(['report_name' => $report_name, 'report_type' => $type_code, 'report_date' => $current_date, 'created_at' => $current_date, 'updated_at' => $current_date, 'create_userid' => $current_user_id, 'update_userid' => $current_user_id, 'report_user' => $user_create, 'report_user_sign' => $user_sign, 'report_attach_name' => '', 'report_nature' => $status, 'report_year' => $year, 'report_id_truong' => $school_id, 'report_note' => $note, 'report' => 'HSKT']);

                if (!is_null($insert_hosobaocao_id) && $insert_hosobaocao_id > 0) {
                    $this->exportforSchoolsHSKT($insert_hosobaocao_id);

                    if (file_exists(storage_path().'/exceldownload/HSKT/'.$type_code.'.xlsx')) {
                        // $result['success'] = "Thêm mới thành công!";
                        // return TRUE;
                    }
                    else {
                        $deleteHoso = DB::table('qlhs_hosobaocao')->where('report_type', 'LIKE', '%'.$type_code.'%')->delete();
                        $deleteHSKT = DB::table('qlhs_hotrohocsinhkhuyettat')->where('type_code', 'LIKE', '%'.$type_code.'%')->delete();
                        // $result['error'] = "Thêm mới thất bại!";
                        // return false;
                    }
                }
                // else {$result['error'] = "Thêm mới thất bại!"; return false;}
                return $insert_hosobaocao_id;
            }
            else {
                return 0;
            }

            // return $result;
        } catch (Exception $e) {
            return $e;
        }       
    }

    public function insertHSKT($getDataType, $type, $current_user_id, $school_id, $year, $time){
        try {
            
            $bool = TRUE;
            $count = 1;
            
            $type_code = 'HSKT-' . $current_user_id . '-' . $school_id . '-' . $year . '' . ($year + 1) . '-' . $time;

            foreach ($getDataType as $value) {

                $nhucau_hocbong = 0;
                $nhucau_muadodung = 0;
                $dutoan_hocbong = 0;
                $dutoan_muadodung = 0;
                $tong_nhucau = 0;
                $tong_dutoan = 0;

                if ($value->{'hotrohocbong'} == 1) {
                    if ($type != 3) {
                        $nhucau_hocbong = $value->{'NHUCAUHB'};
                    }
                    
                    $dutoan_hocbong = $value->{'DUTOANHB'};
                }
                if ($value->{'hotromuadodunght'} == 1) {
                    if ($type != 3) {
                        $nhucau_muadodung = $value->{'NHUCAUDDHT'};
                    }
                    
                    $dutoan_muadodung = $value->{'DUTOANDDHT'};
                }

                if ($type != 3) {
                    $tong_nhucau = ($nhucau_hocbong + $nhucau_muadodung);
                }
                
                $tong_dutoan = ($dutoan_hocbong + $dutoan_muadodung);

                if ($tong_nhucau > 0 || $tong_dutoan > 0) {
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
                        'type_code' => $type_code, 
                        'type' => $type,
                        'trangthai_pheduyet_HSKT' => 1,
                        'trangthai_thamdinh_HSKT' => 1
                        ]);
                    
                    $count++;
                    if ($insert_type == 0) {
                        
                        $deleteHSKT = DB::table('qlhs_hotrohocsinhkhuyettat')->where('type_code', 'LIKE', '%'.$type_code.'%')->where('type', '=', $type)->delete();
                        $count--;
                        break;
                    }
                }
            }
            
            if ($count > 1) {
                $bool = TRUE;
            }
            else {
                $bool = FALSE;
            }

            return TRUE;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function exportforSchoolsHSKT($id, $type = true){
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
            $data_results['aCount'] = $this->countValueHSKT('1',$getSchoolName->report_type);
            $data_results['bCount'] = $this->countValueHSKT('2',$getSchoolName->report_type);
            $data_results['cCount'] = $this->countValueHSKT('3',$getSchoolName->report_type);
            $data_results['TotalCount'] = $this->countValueHSKT(null,$getSchoolName->report_type);
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
        $this->addCellExcelHSKT($data_results, $getSchoolName->report_type, $type);
    }

    private function addCellExcelHSKT($data_results, $filename, $type = true){
        $excel =    Excel::load(storage_path().'/exceltemplate/laphosoHSKT.xlsx', function($reader) use($data_results){
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
                    $col = 0;   $row++;

                    // $strYear = substr((string)$value->history_year, 0, 4);
                    // if ($strYear < $data_results['report_year']) {
                    //  $class_lv1 = $value->level_next_1;
                    //  $class_lv2 = $value->level_next_2;
                    //  $class_lv3 = $value->level_next_3;
                    // }

                    // if ($strYear == $data_results['report_year']) {
                    //  $class_lv1 = $value->level_next;
                    //  $class_lv2 = $value->level_next_1;
                    //  $class_lv3 = $value->level_next_2;
                    // }

                    // if ($strYear > $data_results['report_year']) {
                    //  $class_lv1 = 0;
                    //  $class_lv2 = 0;
                    //  $class_lv3 = $value->level_next_1;
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
                    $col = 0;   $row++;

                    // $strYear = substr((string)$value->history_year, 0, 4);
                    // if ($strYear < $data_results['report_year']) {
                    //  $class_lv1 = $value->level_next;
                    //  $class_lv2 = $value->level_next_1;
                    //  $class_lv3 = $value->level_next_2;
                    // }

                    // if ($strYear == $data_results['report_year']) {
                    //  $class_lv1 = 0;
                    //  $class_lv2 = $value->level_next_1;
                    //  $class_lv3 = $value->level_next_2;
                    // }

                    // if ($strYear > $data_results['report_year']) {
                    //  $class_lv1 = 0;
                    //  $class_lv2 = 0;
                    //  $class_lv3 = $value->level_next_1;
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
                    $col = 0;   $row++;

                    // $strYear = substr((string)$value->history_year, 0, 4);
                    // if ($strYear < $data_results['report_year']) {
                    //  $class_lv1 = $value->level_next;
                    //  $class_lv2 = $value->level_next_1;
                    //  $class_lv3 = $value->level_next_2;
                    // }

                    // if ($strYear == $data_results['report_year']) {
                    //  $class_lv1 = 0;
                    //  $class_lv2 = $value->level_next_1;
                    //  $class_lv3 = $value->level_next_2;
                    // }

                    // if ($strYear > $data_results['report_year']) {
                    //  $class_lv1 = 0;
                    //  $class_lv2 = 0;
                    //  $class_lv3 = $value->level_next_1;
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
    
    public function countValueHSKT($type = null,$code){

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

//---------------------------------------------------------------------HSDTTS------------------------------------------------------------------------
    public function getDataHSDTTS($school_id, $year, $report_name, $user_sign, $user_create, $note, $status){
        $result = [];
        try {

            $current_user_id = Auth::user()->id;
            $current_date = Carbon::now('Asia/Ho_Chi_Minh');


            $check = TRUE;

            $getDataTypeA = DB::table('qlhs_profile')
            ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id = 49 AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.$year.' AND (qlhs_profile_subject.end_year is null OR qlhs_profile_subject.end_year > '.$year.')'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))
            ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$school_id.' AND kp.id_doituong = 99 AND '.($year - 1).' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 1).''))
            ->leftJoin('qlhs_tonghopchedo as yearold', 'yearold.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearold.qlhs_thcd_nam = '.($year - 1).' and (yearold.qlhs_thcd_trangthai_HSDTTS = 1 or yearold.qlhs_thcd_trangthai_HSDTTS_HK2 = 1)'))
            ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearcur.qlhs_thcd_nam = '.$year.' and (yearcur.qlhs_thcd_trangthai_HSDTTS = 1 or yearcur.qlhs_thcd_trangthai_HSDTTS_HK2 = 1)'))
            ->leftJoin('qlhs_tonghopchedo as yearnew', 'yearnew.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearnew.qlhs_thcd_nam = '.($year + 1)))
            ->where('qlhs_profile.profile_school_id', '=', $school_id)
            ->where('qlhs_profile.profile_year', '<', $year.'-06-01')
            ->select('qlhs_profile.profile_id', 'qlhs_profile.profile_school_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years', 
                // DB::raw('MAX(qlhs_profile_subject.profile_subject_subject_id) as profile_subject_subject_id'), 
                DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 49 then 1 else 0 END) as hotrokinhphi'), 

                DB::raw('(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hocky2_old'), 
                DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hocky1_cur'), 
                DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.($year + 1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hocky2_cur'), 
                DB::raw('(CASE when qlhs_profile_history.level_new <> "" and qlhs_profile.profile_year < "'.($year + 1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hocky1_new'),
                    DB::raw('MAX(
                            CASE
                            WHEN ( qlhs_profile_history.level_old <> ""
                            AND qlhs_profile.profile_year < "'.$year.'-06-01"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01"
                                )
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.$year.' AND kp.id_doituong = 99 AND yearold.qlhs_thcd_trangthai_HK2 = 1 AND yearold.qlhs_thcd_trangthai_HSDTTS_HK2 = 1) 
                            OR (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile.profile_year < "'.$year.'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 99 AND yearcur.qlhs_thcd_trangthai = 1 AND yearcur.qlhs_thcd_trangthai_HSDTTS = 1) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS NHUCAU'),
                    DB::raw('MAX(
                            CASE
                            WHEN (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile.profile_year < "'.($year + 1).'-06-01"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01"
                                )
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.($year + 1).' AND kp.id_doituong = 99 AND yearcur.qlhs_thcd_trangthai_HK2 = 1 AND yearcur.qlhs_thcd_trangthai_HSDTTS_HK2 = 1) 
                            OR (qlhs_profile_history.level_new <> ""
                            AND qlhs_profile.profile_year < "'.($year + 1).'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.($year + 1).' AND kp.id_doituong = 99) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS DUTOAN'))
            ->groupBy('qlhs_profile.profile_id', 'qlhs_profile.profile_school_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years');
            
            $getDataType1 = DB::table(DB::raw("({$getDataTypeA->toSql()}) as m"))
                ->mergeBindings( $getDataTypeA )
                ->select('m.profile_id',
                        'm.profile_year',
                        'm.level_old',
                        'm.level_cur',
                        'm.level_new',
                        'm.profile_leaveschool_date',
                        'm.hotrokinhphi',
                        'm.hocky2_old', 
                        'm.hocky1_cur', 
                        'm.hocky2_cur', 
                        'm.hocky1_new', 
                        DB::raw('SUM(m.NHUCAU) as NHUCAU'), 
                        DB::raw('SUM(m.DUTOAN) as DUTOAN'))

                ->groupBy('m.profile_id',
                        'm.profile_year',
                        'm.level_old',
                        'm.level_cur',
                        'm.level_new',
                        'm.profile_leaveschool_date',
                        'm.hotrokinhphi',
                        'm.hocky2_old', 
                        'm.hocky1_cur', 
                        'm.hocky2_cur', 
                        'm.hocky1_new')
                ->get();


            $getDataTypeB = DB::table('qlhs_profile')
            ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id = 49 AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.$year.' AND (qlhs_profile_subject.end_year is null OR qlhs_profile_subject.end_year > '.$year.')'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))
            ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$school_id.' AND kp.id_doituong = 99 AND '.($year - 1).' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 1).''))
            ->leftJoin('qlhs_tonghopchedo as yearold', 'yearold.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearold.qlhs_thcd_nam = '.($year - 1).' and (yearold.qlhs_thcd_trangthai_HSDTTS = 1 or yearold.qlhs_thcd_trangthai_HSDTTS_HK2 = 1)'))
            ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearcur.qlhs_thcd_nam = '.$year))
            ->leftJoin('qlhs_tonghopchedo as yearnew', 'yearnew.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearnew.qlhs_thcd_nam = '.($year + 1)))
            ->where('qlhs_profile.profile_school_id', '=', $school_id)
            ->where('qlhs_profile.profile_year', '>', $year.'-05-31')
            ->where('qlhs_profile.profile_year', '<', ($year + 1).'-06-01')
            // ->where('yearcur.qlhs_thcd_nam', '=', DB::raw($year.' and (yearcur.qlhs_thcd_trangthai_HSDTTS = 1 or yearcur.qlhs_thcd_trangthai_HSDTTS_HK2 = 1)'))
            ->select('qlhs_profile.profile_id', 'qlhs_profile.profile_school_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years', 
                DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 49 then 1 else 0 END) as hotrokinhphi'), 

                DB::raw('(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hocky2_old'), 
                DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hocky1_cur'), 
                DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.($year + 1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hocky2_cur'), 
                DB::raw('(CASE when qlhs_profile_history.level_new <> "" and qlhs_profile.profile_year < "'.($year + 1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hocky1_new'),
                    DB::raw('MAX(
                            CASE
                            WHEN ( qlhs_profile_history.level_old <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 49
                            AND qlhs_profile.profile_year < "'.$year.'-06-01"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01"
                                )
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.$year.' AND kp.id_doituong = 99 AND yearold.qlhs_thcd_trangthai_HK2 = 1 AND yearold.qlhs_thcd_trangthai_HSDTTS_HK2 = 1) 
                            OR (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 49
                            AND qlhs_profile.profile_year < "'.$year.'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 99 AND yearcur.qlhs_thcd_trangthai = 1 AND yearcur.qlhs_thcd_trangthai_HSDTTS = 1) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS NHUCAU'),
                    DB::raw('MAX(
                            CASE
                            WHEN (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 49
                            AND qlhs_profile.profile_year < "'.($year + 1).'-06-01"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01"
                                )
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.($year + 1).' AND kp.id_doituong = 99 AND yearcur.qlhs_thcd_trangthai_HK2 = 1 AND yearcur.qlhs_thcd_trangthai_HSDTTS_HK2 = 1) 
                            OR (qlhs_profile_history.level_new <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 49
                            AND qlhs_profile.profile_year < "'.($year + 1).'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.($year + 1).' AND kp.id_doituong = 99) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS DUTOAN'))
            ->groupBy('qlhs_profile.profile_id', 'qlhs_profile.profile_school_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years');

            $getDataType2 = DB::table(DB::raw("({$getDataTypeB->toSql()}) as m"))
                ->mergeBindings( $getDataTypeB )
                ->select('m.profile_id',
                        'm.profile_year',
                        'm.level_old',
                        'm.level_cur',
                        'm.level_new',
                        'm.profile_leaveschool_date',
                        'm.hotrokinhphi',
                        'm.hocky2_old', 
                        'm.hocky1_cur', 
                        'm.hocky2_cur', 
                        'm.hocky1_new', 
                        DB::raw('SUM(m.NHUCAU) as NHUCAU'), 
                        DB::raw('SUM(m.DUTOAN) as DUTOAN'))

                ->groupBy('m.profile_id',
                        'm.profile_year',
                        'm.level_old',
                        'm.level_cur',
                        'm.level_new',
                        'm.profile_leaveschool_date',
                        'm.hotrokinhphi',
                        'm.hocky2_old', 
                        'm.hocky1_cur', 
                        'm.hocky2_cur', 
                        'm.hocky1_new')
                ->get();


            $getDataTypeC = DB::table('qlhs_profile')
            ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id = 49 AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.($year + 1).' AND (qlhs_profile_subject.end_year is null OR qlhs_profile_subject.end_year > '.($year + 1).')'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.($year + 1).'-'.($year + 2).'"'))
            ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$school_id.' AND kp.id_doituong = 99 AND '.$year.' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 2).''))
            ->leftJoin('qlhs_tonghopchedo as yearold', 'yearold.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearold.qlhs_thcd_nam = '.$year))
            ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearcur.qlhs_thcd_nam = '.($year + 1)))
            ->leftJoin('qlhs_tonghopchedo as yearnew', 'yearnew.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearnew.qlhs_thcd_nam = '.($year + 2)))
            ->where('qlhs_profile.profile_school_id', '=', $school_id)
            ->where('qlhs_profile.profile_year', '>', ($year + 1).'-05-31')
            ->where('qlhs_profile.profile_year', '<', ($year + 2).'-06-01')
            // ->where('yearcur.qlhs_thcd_nam', '=', ($year + 1))
            ->select('qlhs_profile.profile_id', 'qlhs_profile.profile_school_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years', 
                DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 49 then 1 else 0 END) as hotrokinhphi'), 

                DB::raw('(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hocky2_old'), 
                DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hocky1_cur'), 
                DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.($year + 1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hocky2_cur'), 
                DB::raw('(CASE when qlhs_profile_history.level_new <> "" and qlhs_profile.profile_year < "'.($year + 1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hocky1_new'),
                    // DB::raw('MAX(
                    //         CASE
                    //         WHEN ( qlhs_profile_history.level_old <> ""
                    //         AND qlhs_profile_subject.profile_subject_subject_id = 49
                    //         AND qlhs_profile.profile_year < "'.($year + 1).'-06-01"
                    //         AND (
                    //             qlhs_profile.profile_leaveschool_date IS NULL
                    //             OR (
                    //                 qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01"
                    //             )
                    //         ) AND kp.months in (1,2,3,4,5) and kp.years = '.($year + 1).' AND kp.id_doituong = 99) 
                    //         OR (qlhs_profile_history.level_cur <> ""
                    //         AND qlhs_profile_subject.profile_subject_subject_id = 49
                    //         AND qlhs_profile.profile_year < "'.($year + 1).'-12-31"
                    //         AND (
                    //             qlhs_profile.profile_leaveschool_date IS NULL
                    //             OR (
                    //                 qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31"
                    //             )
                    //         )
                    //         AND kp.months in (9,10,11,12) and kp.years = '.($year + 1).' AND kp.id_doituong = 99) THEN
                    //             kp.value_m
                    //         ELSE
                    //             0
                    //         END
                    //     ) AS NHUCAU'),
                    DB::raw('MAX(
                            CASE
                            WHEN (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 49
                            AND qlhs_profile.profile_year < "'.($year + 2).'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year + 2).'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.($year + 2).' AND kp.id_doituong = 99) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS DUTOAN'))
            ->groupBy('qlhs_profile.profile_id', 'qlhs_profile.profile_school_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years');
            
            $getDataType3 = DB::table(DB::raw("({$getDataTypeC->toSql()}) as m"))
                ->mergeBindings( $getDataTypeC )
                ->select('m.profile_id',
                        'm.profile_year',
                        'm.level_old',
                        'm.level_cur',
                        'm.level_new',
                        'm.profile_leaveschool_date',
                        'm.hotrokinhphi',
                        'm.hocky2_old', 
                        'm.hocky1_cur', 
                        'm.hocky2_cur', 
                        'm.hocky1_new', 
                        // DB::raw('SUM(m.NHUCAU) as NHUCAU'), 
                        DB::raw('SUM(m.DUTOAN) as DUTOAN'))

                ->groupBy('m.profile_id',
                        'm.profile_year',
                        'm.level_old',
                        'm.level_cur',
                        'm.level_new',
                        'm.profile_leaveschool_date',
                        'm.hotrokinhphi',
                        'm.hocky2_old', 
                        'm.hocky1_cur', 
                        'm.hocky2_cur', 
                        'm.hocky1_new')
                ->get();

            $time = time();

            if ((is_null($getDataType1) || empty($getDataType1) || count($getDataType1) == 0) && (is_null($getDataType2) || empty($getDataType2) || count($getDataType2) == 0) && (is_null($getDataType3) || empty($getDataType3) || count($getDataType3) == 0)) {
                $result['success'] = "Trường không có học sinh thuộc đối tượng!";
                return $result;
            }
            
            if (!is_null($getDataType1) && !empty($getDataType1) && count($getDataType1) > 0) {
                $check = $this->insertHSDTTS($getDataType1, 1, $current_user_id, $school_id, $year, $time);
            }

            if (!is_null($getDataType2) && !empty($getDataType2) && count($getDataType2) > 0 && $check) {
                $check = $this->insertHSDTTS($getDataType2, 2, $current_user_id, $school_id, $year, $time);
            }

            if (!is_null($getDataType3) && !empty($getDataType3) && count($getDataType3) > 0 && $check) {
                $check = $this->insertHSDTTS($getDataType3, 3, $current_user_id, $school_id, $year, $time);
            }

            if ($check) {
                $type_code = 'HSDTTS-' . $current_user_id . '-' . $school_id . '-' . $year . '' . ($year + 1) . '-' . $time;

                $dir = storage_path().'/files/HSDTTS';
                

                $insert_hosobaocao_id = DB::table('qlhs_hosobaocao')->insertGetId(['report_name' => $report_name, 'report_type' => $type_code, 'report_date' => $current_date, 'created_at' => $current_date, 'updated_at' => $current_date, 'create_userid' => $current_user_id, 'update_userid' => $current_user_id, 'report_user' => $user_create, 'report_user_sign' => $user_sign, 'report_attach_name' => '', 'report_nature' => $status, 'report_year' => $year, 'report_id_truong' => $school_id, 'report_note' => $note, 'report' => 'HSDTTS']);

                if (!is_null($insert_hosobaocao_id) && $insert_hosobaocao_id > 0) {
                    $this->exportforSchoolsHSDTTS($insert_hosobaocao_id);

                    if (file_exists(storage_path().'/exceldownload/HSDTTS/'.$type_code.'.xlsx')) {
                        // $result['success'] = "Thêm mới thành công!";
                        // return TRUE;
                    }
                    else {
                        $deleteHoso = DB::table('qlhs_hosobaocao')->where('report_type', 'LIKE', '%'.$type_code.'%')->delete();
                        $deleteHSDTTS = DB::table('qlhs_hotrohocsinhdantocthieuso')->where('type_code', 'LIKE', '%'.$type_code.'%')->delete();
                        // $result['error'] = "Thêm mới thất bại!";
                        // return false;
                    }
                }
                // else {$result['error'] = "Thêm mới thất bại!"; return false;}
                return $insert_hosobaocao_id;
            }
            else {
                return 0;
            }

            // return $result;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function insertHSDTTS($getDataType, $type, $current_user_id, $school_id, $year, $time){
        try {
            $bool = TRUE;
            $count = 1;
            
            $type_code = 'HSDTTS-' . $current_user_id . '-' . $school_id . '-' . $year . '' . ($year + 1) . '-' . $time;

            foreach ($getDataType as $value) {
                $tongnhucau = 0;
                $tongdutoan = 0;

                if ($type != 3) {
                    $tongnhucau = $value->{'NHUCAU'};
                }

                $tongdutoan = $value->{'DUTOAN'};

                if ($tongnhucau > 0 || $tongdutoan > 0) {
                    $insert_type = DB::table('qlhs_hotrohocsinhdantocthieuso')
                    ->insert([
                        'profile_id' => $value->{'profile_id'}, 
                        'hotrokinhphi' => $value->{'hotrokinhphi'}, 
                        'hocky2_old' => $value->{'hocky2_old'}, 
                        'hocky1_cur' => $value->{'hocky1_cur'}, 
                        'hocky2_cur' => $value->{'hocky2_cur'}, 
                        'hocky1_new' => $value->{'hocky1_new'}, 
                        'nhucau' => $tongnhucau, 
                        'dutoan' => $tongdutoan, 
                        'type_code' => $type_code, 
                        'type' => $type,
                        'trangthai_pheduyet_HSDTTS' => 1,
                        'trangthai_thamdinh_HSDTTS' => 1
                        ]);

                    $count++;
                    if ($insert_type == 0) {
                        
                        $deleteHSDTTS = DB::table('qlhs_hotrohocsinhdantocthieuso')->where('type_code', 'LIKE', '%'.$type_code.'%')->where('type', '=', $type)->delete();
                        $count--;
                        break;
                    }
                }
                
            }
            
            if ($count > 1) {
                $bool = TRUE;
            }
            else {
                $bool = FALSE;
            }

            return TRUE;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function exportforSchoolsHSDTTS($id, $type = true){
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
            $data_results['aCount'] = $this->countValueHSDTTS('1',$getSchoolName->report_type);
            $data_results['bCount'] = $this->countValueHSDTTS('2',$getSchoolName->report_type);
            $data_results['cCount'] = $this->countValueHSDTTS('3',$getSchoolName->report_type);
            $data_results['TotalCount'] = $this->countValueHSDTTS(null,$getSchoolName->report_type);
            //Get by type A
            $year = $getSchoolName->report_year;
            $data_results['a'] = DB::table('qlhs_hotrohocsinhdantocthieuso')
            ->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_hotrohocsinhdantocthieuso.profile_id')
            ->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_hotrohocsinhdantocthieuso.profile_id and qlhs_profile_history.history_year = "'.$getSchoolName->report_year.'-'.($getSchoolName->report_year + 1).'"'))
            ->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
            ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
            ->where('qlhs_hotrohocsinhdantocthieuso.type_code', '=', $getSchoolName->report_type)
            ->where('qlhs_hotrohocsinhdantocthieuso.type', '=', 1)
            ->select('qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile_history.history_year', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'qlhs_hotrohocsinhdantocthieuso.*')->get();

            $data_results['b'] = DB::table('qlhs_hotrohocsinhdantocthieuso')
            ->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_hotrohocsinhdantocthieuso.profile_id')
            ->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_hotrohocsinhdantocthieuso.profile_id and qlhs_profile_history.history_year = "'.$getSchoolName->report_year.'-'.($getSchoolName->report_year + 1).'"'))
            ->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
            ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
            ->where('qlhs_hotrohocsinhdantocthieuso.type_code', '=', $getSchoolName->report_type)
            ->where('qlhs_hotrohocsinhdantocthieuso.type', '=', 2)
            ->select('qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile_history.history_year', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'qlhs_hotrohocsinhdantocthieuso.*')->get();

            $data_results['c'] = DB::table('qlhs_hotrohocsinhdantocthieuso')
            ->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_hotrohocsinhdantocthieuso.profile_id')
            ->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_hotrohocsinhdantocthieuso.profile_id and qlhs_profile_history.history_year = "'.($getSchoolName->report_year + 1).'-'.($getSchoolName->report_year + 2).'"'))
            ->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
            ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
            ->where('qlhs_hotrohocsinhdantocthieuso.type_code', '=', $getSchoolName->report_type)
            ->where('qlhs_hotrohocsinhdantocthieuso.type', '=', 3)
            ->select('qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile_history.history_year', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'qlhs_hotrohocsinhdantocthieuso.*')->get();
        }
        $data_results['schools_name'] = $getSchoolName->schools_name;
        $data_results['report_year'] = $getSchoolName->report_year;
        $this->addCellExcelHSDTTS($data_results, $getSchoolName->report_type, $type);
    }

    private function addCellExcelHSDTTS($data_results, $filename, $type = true){
        $excel =    Excel::load(storage_path().'/exceltemplate/laphosoHSDTTS.xlsx', function($reader) use($data_results){
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
            $reader->getActiveSheet()->setCellValueByColumnAndRow(0, 2, 'NHU CẦU KINH PHÍ NĂM '.$data_results['report_year'].', DỰ TOÁN NĂM '.($data_results['report_year'] + 1).' ĐỐI VỚI CHÍNH SÁCH HỖ TRỢ HỌC SINH DÂN TỘC THIỂU SỐ TẠI HUYỆN MÙ CANG CHẢI VÀ HUYỆN TRẠM TẤU THEO QUYẾT ĐỊNH 22/2016/QĐ-UBND CỦA UBND TỈNH')->getStyle('A2')->applyFromArray($FontArray)->applyFromArray($style);
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
                    $col = 0;   $row++;

                    // $strYear = substr((string)$value->history_year, 0, 4);
                    // if ($strYear < $data_results['report_year']) {
                    //     $class_lv1 = $value->level_next_1;
                    //     $class_lv2 = $value->level_next_2;
                    //     $class_lv3 = $value->level_next_3;
                    // }

                    // if ($strYear == $data_results['report_year']) {
                    //     $class_lv1 = $value->level_next;
                    //     $class_lv2 = $value->level_next_1;
                    //     $class_lv3 = $value->level_next_2;
                    // }

                    // if ($strYear > $data_results['report_year']) {
                    //     $class_lv1 = 0;
                    //     $class_lv2 = 0;
                    //     $class_lv3 = $value->level_next_1;
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
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hotrokinhphi)->getStyle('L'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);        
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_old)->getStyle('M'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_cur)->getStyle('N'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight); 

                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_cur)->getStyle('O'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_new)->getStyle('P'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->nhucau)->getStyle('Q'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->dutoan)->getStyle('R'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
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
                    $col = 0;   $row++;

                    // $strYear = substr((string)$value->history_year, 0, 4);
                    // if ($strYear < $data_results['report_year']) {
                    //     $class_lv1 = $value->level_next;
                    //     $class_lv2 = $value->level_next_1;
                    //     $class_lv3 = $value->level_next_2;
                    // }

                    // if ($strYear == $data_results['report_year']) {
                    //     $class_lv1 = 0;
                    //     $class_lv2 = $value->level_next_1;
                    //     $class_lv3 = $value->level_next_2;
                    // }

                    // if ($strYear > $data_results['report_year']) {
                    //     $class_lv1 = 0;
                    //     $class_lv2 = 0;
                    //     $class_lv3 = $value->level_next_1;
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
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hotrokinhphi)->getStyle('L'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);        
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_old)->getStyle('M'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_cur)->getStyle('N'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight); 

                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_cur)->getStyle('O'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_new)->getStyle('P'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->nhucau)->getStyle('Q'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->dutoan)->getStyle('R'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
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
                    $col = 0;   $row++;

                    // $strYear = substr((string)$value->history_year, 0, 4);
                    // if ($strYear < $data_results['report_year']) {
                    //     $class_lv1 = $value->level_next;
                    //     $class_lv2 = $value->level_next_1;
                    //     $class_lv3 = $value->level_next_2;
                    // }

                    // if ($strYear == $data_results['report_year']) {
                    //     $class_lv1 = 0;
                    //     $class_lv2 = $value->level_next_1;
                    //     $class_lv3 = $value->level_next_2;
                    // }

                    // if ($strYear > $data_results['report_year']) {
                    //     $class_lv1 = 0;
                    //     $class_lv2 = 0;
                    //     $class_lv3 = $value->level_next_1;
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
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hotrokinhphi)->getStyle('L'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);        
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_old)->getStyle('M'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_cur)->getStyle('N'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight); 

                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_cur)->getStyle('O'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_new)->getStyle('P'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->nhucau)->getStyle('Q'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->dutoan)->getStyle('R'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                }
            }
        });

        if($type){
            return $excel->setFilename($filename)->store('xlsx', storage_path().'/exceldownload/HSDTTS');
        }else{
            return $excel->setFilename($filename)->download('xlsx');
        }
    }
    
    public function countValueHSDTTS($type = null,$code){

        if($type!=null){
            $count = DB::table('qlhs_hotrohocsinhdantocthieuso')->where('type_code','=',$code)->where('type','=',$type)
            ->select(
                DB::raw('sum(hocky2_old) as tonghocky2_old'),
                DB::raw('sum(hocky1_cur) as tonghocky1_cur'),
                DB::raw('sum(hocky2_cur) as tonghocky2_cur'),
                DB::raw('sum(hocky1_new) as tonghocky1_new'),
                DB::raw('sum(nhucau) as tong_nhucau'),
                DB::raw('sum(dutoan) as tong_dutoan'))->first();
            return $count;
        }else{
            $count = DB::table('qlhs_hotrohocsinhdantocthieuso')->where('type_code','=',$code)
            ->select(
                DB::raw('sum(hocky2_old) as tonghocky2_old'),
                DB::raw('sum(hocky1_cur) as tonghocky1_cur'),
                DB::raw('sum(hocky2_cur) as tonghocky2_cur'),
                DB::raw('sum(hocky1_new) as tonghocky1_new'),
                DB::raw('sum(nhucau) as tong_nhucau'),
                DB::raw('sum(dutoan) as tong_dutoan'))->first();
            return $count;
        }
    }

//---------------------------------------------------------------------HTATHS------------------------------------------------------------------------
    public function getDataHTATHS($school_id, $year, $report_name, $user_sign, $user_create, $note, $status){
        $result = [];
        try {

            $current_user_id = Auth::user()->id;
            $current_date = Carbon::now('Asia/Ho_Chi_Minh');


            $check = TRUE;

            $getDataTypeA = DB::table('qlhs_profile')
            ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id = 69 AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.$year.' AND (qlhs_profile_subject.end_year is null OR qlhs_profile_subject.end_year > '.$year.')'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))
            ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$school_id.' AND kp.id_doituong = 118 AND '.($year - 1).' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 1).''))
            ->leftJoin('qlhs_tonghopchedo as yearold', 'yearold.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearold.qlhs_thcd_nam = '.($year - 1).' and (yearold.qlhs_thcd_trangthai_HTATHS = 1 or yearold.qlhs_thcd_trangthai_HTATHS_HK2 = 1)'))
            // ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', 'profile_id')
            ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearcur.qlhs_thcd_nam = '.$year.' and (yearcur.qlhs_thcd_trangthai_HTATHS = 1 or yearcur.qlhs_thcd_trangthai_HTATHS_HK2 = 1)'))
            ->leftJoin('qlhs_tonghopchedo as yearnew', 'yearnew.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearnew.qlhs_thcd_nam = '.($year + 1)))
            ->where('qlhs_profile.profile_school_id', '=', $school_id)
            ->where('qlhs_profile.profile_year', '<', $year.'-06-01')
            ->where('qlhs_profile.profile_statusNQ57', '=', 1)
            // ->where('yearcur.qlhs_thcd_nam', '=', DB::raw($year.' and (yearcur.qlhs_thcd_trangthai_HTATHS = 1 or yearcur.qlhs_thcd_trangthai_HTATHS_HK2 = 1 or yearold.qlhs_thcd_trangthai_HTATHS = 1 or yearold.qlhs_thcd_trangthai_HTATHS_HK2 = 1)'))
            ->select('qlhs_profile.profile_id', 'qlhs_profile.profile_school_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years', 
                DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 69 then 1 else 0 END) as hotrokinhphi'), 

                DB::raw('(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hocky2_old'), 
                DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hocky1_cur'), 
                DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.($year + 1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hocky2_cur'), 
                DB::raw('(CASE when qlhs_profile_history.level_new <> "" and qlhs_profile.profile_year < "'.($year + 1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hocky1_new'),
                    DB::raw('MAX(
                            CASE
                            WHEN ( qlhs_profile_history.level_old <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 69
                            AND qlhs_profile.profile_year < "'.$year.'-06-01"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01"
                                )
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.$year.' AND kp.id_doituong = 118 AND yearold.qlhs_thcd_trangthai_HK2 = 1 AND yearold.qlhs_thcd_trangthai_HTATHS_HK2 = 1) 
                            OR (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 69
                            AND qlhs_profile.profile_year < "'.$year.'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 118 AND yearcur.qlhs_thcd_trangthai = 1 AND yearcur.qlhs_thcd_trangthai_HTATHS = 1) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS NHUCAU'),
                    DB::raw('MAX(
                            CASE
                            WHEN (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 69
                            AND qlhs_profile.profile_year < "'.($year + 1).'-06-01"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01"
                                )
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.($year + 1).' AND kp.id_doituong = 118 AND yearcur.qlhs_thcd_trangthai_HK2 = 1 AND yearcur.qlhs_thcd_trangthai_HTATHS_HK2 = 1) 
                            OR (qlhs_profile_history.level_new <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 69
                            AND qlhs_profile.profile_year < "'.($year + 1).'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.($year + 1).' AND kp.id_doituong = 118) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS DUTOAN'))
            ->groupBy('qlhs_profile.profile_id', 'qlhs_profile.profile_school_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years');
            
            $getDataType1 = DB::table(DB::raw("({$getDataTypeA->toSql()}) as m"))
                ->mergeBindings( $getDataTypeA )
                ->select('m.profile_id',
                        'm.profile_year',
                        'm.level_old',
                        'm.level_cur',
                        'm.level_new',
                        'm.profile_leaveschool_date',
                        'm.hotrokinhphi',
                        'm.hocky2_old', 
                        'm.hocky1_cur', 
                        'm.hocky2_cur', 
                        'm.hocky1_new', 
                        DB::raw('SUM(m.NHUCAU) as NHUCAU'), 
                        DB::raw('SUM(m.DUTOAN) as DUTOAN'))

                ->groupBy('m.profile_id',
                        'm.profile_year',
                        'm.level_old',
                        'm.level_cur',
                        'm.level_new',
                        'm.profile_leaveschool_date',
                        'm.hotrokinhphi',
                        'm.hocky2_old', 
                        'm.hocky1_cur', 
                        'm.hocky2_cur', 
                        'm.hocky1_new')
                ->get();


            $getDataTypeB = DB::table('qlhs_profile')
            ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id = 69 AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.$year.' AND (qlhs_profile_subject.end_year is null OR qlhs_profile_subject.end_year > '.$year.')'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))
            ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$school_id.' AND kp.id_doituong = 118 AND '.($year - 1).' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 1).''))
            ->leftJoin('qlhs_tonghopchedo as yearold', 'yearold.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearold.qlhs_thcd_nam = '.($year - 1).' and (yearold.qlhs_thcd_trangthai_HTATHS = 1 or yearold.qlhs_thcd_trangthai_HTATHS_HK2 = 1)'))
            // ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', 'profile_id')
            ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearcur.qlhs_thcd_nam = '.$year))
            ->leftJoin('qlhs_tonghopchedo as yearnew', 'yearnew.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearnew.qlhs_thcd_nam = '.($year + 1)))
            ->where('qlhs_profile.profile_school_id', '=', $school_id)
            ->where('qlhs_profile.profile_year', '>', $year.'-05-31')
            ->where('qlhs_profile.profile_year', '<', ($year + 1).'-06-01')
            ->where('qlhs_profile.profile_statusNQ57', '=', 1)
            // ->where('yearcur.qlhs_thcd_nam', '=', DB::raw($year.' and (yearcur.qlhs_thcd_trangthai_HTATHS = 1 or yearcur.qlhs_thcd_trangthai_HTATHS_HK2 = 1)'))
            ->select('qlhs_profile.profile_id', 'qlhs_profile.profile_school_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years', 
                DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 69 then 1 else 0 END) as hotrokinhphi'), 

                DB::raw('(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hocky2_old'), 
                DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hocky1_cur'), 
                DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.($year + 1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hocky2_cur'), 
                DB::raw('(CASE when qlhs_profile_history.level_new <> "" and qlhs_profile.profile_year < "'.($year + 1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hocky1_new'),
                    DB::raw('MAX(
                            CASE
                            WHEN ( qlhs_profile_history.level_old <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 69
                            AND qlhs_profile.profile_year < "'.$year.'-06-01"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01"
                                )
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.$year.' AND kp.id_doituong = 118 AND yearold.qlhs_thcd_trangthai_HK2 = 1 AND yearold.qlhs_thcd_trangthai_HTATHS_HK2 = 1) 
                            OR (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 69
                            AND qlhs_profile.profile_year < "'.$year.'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 118 AND yearcur.qlhs_thcd_trangthai = 1 AND yearcur.qlhs_thcd_trangthai_HTATHS = 1) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS NHUCAU'),
                    DB::raw('MAX(
                            CASE
                            WHEN (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 69
                            AND qlhs_profile.profile_year < "'.($year + 1).'-06-01"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01"
                                )
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.($year + 1).' AND kp.id_doituong = 118 AND yearcur.qlhs_thcd_trangthai_HK2 = 1 AND yearcur.qlhs_thcd_trangthai_HTATHS_HK2 = 1) 
                            OR (qlhs_profile_history.level_new <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 69
                            AND qlhs_profile.profile_year < "'.($year + 1).'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.($year + 1).' AND kp.id_doituong = 118) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS DUTOAN'))
            ->groupBy('qlhs_profile.profile_id', 'qlhs_profile.profile_school_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years');
            
            $getDataType2 = DB::table(DB::raw("({$getDataTypeB->toSql()}) as m"))
                ->mergeBindings( $getDataTypeB )
                ->select('m.profile_id',
                        'm.profile_year',
                        'm.level_old',
                        'm.level_cur',
                        'm.level_new',
                        'm.profile_leaveschool_date',
                        'm.hotrokinhphi',
                        'm.hocky2_old', 
                        'm.hocky1_cur', 
                        'm.hocky2_cur', 
                        'm.hocky1_new', 
                        DB::raw('SUM(m.NHUCAU) as NHUCAU'), 
                        DB::raw('SUM(m.DUTOAN) as DUTOAN'))

                ->groupBy('m.profile_id',
                        'm.profile_year',
                        'm.level_old',
                        'm.level_cur',
                        'm.level_new',
                        'm.profile_leaveschool_date',
                        'm.hotrokinhphi',
                        'm.hocky2_old', 
                        'm.hocky1_cur', 
                        'm.hocky2_cur', 
                        'm.hocky1_new')
                ->get();


            $getDataTypeC = DB::table('qlhs_profile')
            ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id = 69 AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.($year + 1).' AND (qlhs_profile_subject.end_year is null OR qlhs_profile_subject.end_year > '.($year + 1).')'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.($year + 1).'-'.($year + 2).'"'))
            ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$school_id.' AND kp.id_doituong = 118 AND '.$year.' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 2).''))
            ->leftJoin('qlhs_tonghopchedo as yearold', 'yearold.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearold.qlhs_thcd_nam = '.$year))
            ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearcur.qlhs_thcd_nam = '.($year + 1)))
            ->leftJoin('qlhs_tonghopchedo as yearnew', 'yearnew.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearnew.qlhs_thcd_nam = '.($year + 2)))
            ->where('qlhs_profile.profile_school_id', '=', $school_id)
            ->where('qlhs_profile.profile_year', '>', ($year + 1).'-05-31')
            ->where('qlhs_profile.profile_year', '<', ($year + 2).'-06-01')
            ->where('qlhs_profile.profile_statusNQ57', '=', 1)
            // ->where('yearcur.qlhs_thcd_nam', '=', ($year + 1))
            ->select('qlhs_profile.profile_id', 'qlhs_profile.profile_school_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years', 
                DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 69 then 1 else 0 END) as hotrokinhphi'), 

                DB::raw('(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hocky2_old'), 
                DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hocky1_cur'), 
                DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.($year + 1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hocky2_cur'), 
                DB::raw('(CASE when qlhs_profile_history.level_new <> "" and qlhs_profile.profile_year < "'.($year + 1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hocky1_new'),
                    // DB::raw('MAX(
                    //         CASE
                    //         WHEN ( qlhs_profile_history.level_old <> ""
                    //         AND qlhs_profile_subject.profile_subject_subject_id = 69
                    //         AND qlhs_profile.profile_year < "'.($year + 1).'-06-01"
                    //         AND (
                    //             qlhs_profile.profile_leaveschool_date IS NULL
                    //             OR (
                    //                 qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01"
                    //             )
                    //         ) AND kp.months in (1,2,3,4,5) and kp.years = '.($year + 1).' AND kp.id_doituong = 118) 
                    //         OR (qlhs_profile_history.level_cur <> ""
                    //         AND qlhs_profile_subject.profile_subject_subject_id = 69
                    //         AND qlhs_profile.profile_year < "'.($year + 1).'-12-31"
                    //         AND (
                    //             qlhs_profile.profile_leaveschool_date IS NULL
                    //             OR (
                    //                 qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31"
                    //             )
                    //         )
                    //         AND kp.months in (9,10,11,12) and kp.years = '.($year + 1).' AND kp.id_doituong = 118) THEN
                    //             kp.value_m
                    //         ELSE
                    //             0
                    //         END
                    //     ) AS NHUCAU'),
                    DB::raw('MAX(
                            CASE
                            WHEN (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 69
                            AND qlhs_profile.profile_year < "'.($year + 2).'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year + 2).'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.($year + 2).' AND kp.id_doituong = 118) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS DUTOAN'))
            ->groupBy('qlhs_profile.profile_id', 'qlhs_profile.profile_school_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years');
            
            $getDataType3 = DB::table(DB::raw("({$getDataTypeC->toSql()}) as m"))
                ->mergeBindings( $getDataTypeC )
                ->select('m.profile_id',
                        'm.profile_year',
                        'm.level_old',
                        'm.level_cur',
                        'm.level_new',
                        'm.profile_leaveschool_date',
                        'm.hotrokinhphi',
                        'm.hocky2_old', 
                        'm.hocky1_cur', 
                        'm.hocky2_cur', 
                        'm.hocky1_new', 
                        // DB::raw('SUM(m.NHUCAU) as NHUCAU'), 
                        DB::raw('SUM(m.DUTOAN) as DUTOAN'))

                ->groupBy('m.profile_id',
                        'm.profile_year',
                        'm.level_old',
                        'm.level_cur',
                        'm.level_new',
                        'm.profile_leaveschool_date',
                        'm.hotrokinhphi',
                        'm.hocky2_old', 
                        'm.hocky1_cur', 
                        'm.hocky2_cur', 
                        'm.hocky1_new')
                ->get();

            $time = time();

            if ((is_null($getDataType1) || empty($getDataType1) || count($getDataType1) == 0) && (is_null($getDataType2) || empty($getDataType2) || count($getDataType2) == 0) && (is_null($getDataType3) || empty($getDataType3) || count($getDataType3) == 0)) {
                $result['success'] = "Trường không có học sinh thuộc đối tượng!";
                return $result;
            }
            
            if (!is_null($getDataType1) && !empty($getDataType1) && count($getDataType1) > 0) {
                $check = $this->insertHTATHS($getDataType1, 1, $current_user_id, $school_id, $year, $time);
            }

            if (!is_null($getDataType2) && !empty($getDataType2) && count($getDataType2) > 0 && $check) {
                $check = $this->insertHTATHS($getDataType2, 2, $current_user_id, $school_id, $year, $time);
            }

            if (!is_null($getDataType3) && !empty($getDataType3) && count($getDataType3) > 0 && $check) {
                $check = $this->insertHTATHS($getDataType3, 3, $current_user_id, $school_id, $year, $time);
            }

            if ($check) {
                $type_code = 'HTATHS-' . $current_user_id . '-' . $school_id . '-' . $year . '' . ($year + 1) . '-' . $time;

                $dir = storage_path().'/files/HTATHS';
                

                $insert_hosobaocao_id = DB::table('qlhs_hosobaocao')->insertGetId(['report_name' => $report_name, 'report_type' => $type_code, 'report_date' => $current_date, 'created_at' => $current_date, 'updated_at' => $current_date, 'create_userid' => $current_user_id, 'update_userid' => $current_user_id, 'report_user' => $user_create, 'report_user_sign' => $user_sign, 'report_attach_name' => '', 'report_nature' => $status, 'report_year' => $year, 'report_id_truong' => $school_id, 'report_note' => $note, 'report' => 'HTATHS']);

                if (!is_null($insert_hosobaocao_id) && $insert_hosobaocao_id > 0) {
                    $this->exportforSchoolsHTATHS($insert_hosobaocao_id);

                    if (file_exists(storage_path().'/exceldownload/HTATHS/'.$type_code.'.xlsx')) {
                        // $result['success'] = "Thêm mới thành công!";
                        // return TRUE;
                    }
                    else {
                        $deleteHoso = DB::table('qlhs_hosobaocao')->where('report_type', 'LIKE', '%'.$type_code.'%')->delete();
                        $deleteHTATHS = DB::table('qlhs_hotrohocsinhdantocthieuso')->where('type_code', 'LIKE', '%'.$type_code.'%')->delete();
                        // $result['error'] = "Thêm mới thất bại!";
                        // return false;
                    }
                }
                // else {$result['error'] = "Thêm mới thất bại!"; return false;}
                return $insert_hosobaocao_id;
            }
            else {
                return 0;
            }

            // return $result;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function insertHTATHS($getDataType, $type, $current_user_id, $school_id, $year, $time){
        try {
            $bool = TRUE;
            $count = 1;
            
            $type_code = 'HTATHS-' . $current_user_id . '-' . $school_id . '-' . $year . '' . ($year + 1) . '-' . $time;

            foreach ($getDataType as $value) {
                $tongnhucau = 0;
                $tongdutoan = 0;

                if ($type != 3) {
                    $tongnhucau = $value->{'NHUCAU'};
                }

                $tongdutoan = $value->{'DUTOAN'};

                if ($tongnhucau > 0 || $tongdutoan > 0) {
                    $insert_type = DB::table('qlhs_hotrohocsinhdantocthieuso')
                    ->insert([
                        'profile_id' => $value->{'profile_id'}, 
                        'hotrokinhphi' => $value->{'hotrokinhphi'}, 
                        'hocky2_old' => $value->{'hocky2_old'}, 
                        'hocky1_cur' => $value->{'hocky1_cur'}, 
                        'hocky2_cur' => $value->{'hocky2_cur'}, 
                        'hocky1_new' => $value->{'hocky1_new'}, 
                        'nhucau' => $tongnhucau, 
                        'dutoan' => $tongdutoan, 
                        'type_code' => $type_code, 
                        'type' => $type,
                        'trangthai_pheduyet_HSDTTS' => 1,
                        'trangthai_thamdinh_HSDTTS' => 1]);

                    $count++;
                    if ($insert_type == 0) {
                        
                        $deleteHTATHS = DB::table('qlhs_hotrohocsinhdantocthieuso')->where('type_code', 'LIKE', '%'.$type_code.'%')->where('type', '=', $type)->delete();
                        $count--;
                        break;
                    }
                }
            }
            
            if ($count > 1) {
                $bool = TRUE;
            }
            else {
                $bool = FALSE;
            }

            return TRUE;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function exportforSchoolsHTATHS($id, $type = true){
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
            $data_results['aCount'] = $this->countValueHTATHS('1',$getSchoolName->report_type);
            $data_results['bCount'] = $this->countValueHTATHS('2',$getSchoolName->report_type);
            $data_results['cCount'] = $this->countValueHTATHS('3',$getSchoolName->report_type);
            $data_results['TotalCount'] = $this->countValueHTATHS(null,$getSchoolName->report_type);
            //Get by type A
            $year = $getSchoolName->report_year;
            $data_results['a'] = DB::table('qlhs_hotrohocsinhdantocthieuso')
            ->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_hotrohocsinhdantocthieuso.profile_id')
            ->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_hotrohocsinhdantocthieuso.profile_id and qlhs_profile_history.history_year = "'.$getSchoolName->report_year.'-'.($getSchoolName->report_year + 1).'"'))
            ->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
            ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
            ->where('qlhs_hotrohocsinhdantocthieuso.type_code', '=', $getSchoolName->report_type)
            ->where('qlhs_hotrohocsinhdantocthieuso.type', '=', 1)
            ->select('qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile_history.history_year', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'qlhs_hotrohocsinhdantocthieuso.*')->get();

            $data_results['b'] = DB::table('qlhs_hotrohocsinhdantocthieuso')
            ->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_hotrohocsinhdantocthieuso.profile_id')
            ->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_hotrohocsinhdantocthieuso.profile_id and qlhs_profile_history.history_year = "'.$getSchoolName->report_year.'-'.($getSchoolName->report_year + 1).'"'))
            ->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
            ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
            ->where('qlhs_hotrohocsinhdantocthieuso.type_code', '=', $getSchoolName->report_type)
            ->where('qlhs_hotrohocsinhdantocthieuso.type', '=', 2)
            ->select('qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile_history.history_year', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'qlhs_hotrohocsinhdantocthieuso.*')->get();

            $data_results['c'] = DB::table('qlhs_hotrohocsinhdantocthieuso')
            ->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_hotrohocsinhdantocthieuso.profile_id')
            ->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_hotrohocsinhdantocthieuso.profile_id and qlhs_profile_history.history_year = "'.($getSchoolName->report_year + 1).'-'.($getSchoolName->report_year + 2).'"'))
            ->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
            ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
            ->where('qlhs_hotrohocsinhdantocthieuso.type_code', '=', $getSchoolName->report_type)
            ->where('qlhs_hotrohocsinhdantocthieuso.type', '=', 3)
            ->select('qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile_history.history_year', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'qlhs_hotrohocsinhdantocthieuso.*')->get();
        }
        $data_results['schools_name'] = $getSchoolName->schools_name;
        $data_results['report_year'] = $getSchoolName->report_year;
        $this->addCellExcelHTATHS($data_results, $getSchoolName->report_type, $type);
    }

    private function addCellExcelHTATHS($data_results, $filename, $type = true){
        $excel =    Excel::load(storage_path().'/exceltemplate/laphosoHTATHS.xlsx', function($reader) use($data_results){
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
            $reader->getActiveSheet()->setCellValueByColumnAndRow(0, 2, 'NHU CẦU KINH PHÍ NĂM '.$data_results['report_year'].', DỰ TOÁN NĂM '.($data_results['report_year'] + 1).' ĐỐI VỚI CHÍNH SÁCH HỖ TRỢ ĂN TRƯA DÀNH CHO HỌC SINH')->getStyle('A2')->applyFromArray($FontArray)->applyFromArray($style);
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
                    $col = 0;   $row++;

                    // $strYear = substr((string)$value->history_year, 0, 4);
                    // if ($strYear < $data_results['report_year']) {
                    //     $class_lv1 = $value->level_next_1;
                    //     $class_lv2 = $value->level_next_2;
                    //     $class_lv3 = $value->level_next_3;
                    // }

                    // if ($strYear == $data_results['report_year']) {
                    //     $class_lv1 = $value->level_next;
                    //     $class_lv2 = $value->level_next_1;
                    //     $class_lv3 = $value->level_next_2;
                    // }

                    // if ($strYear > $data_results['report_year']) {
                    //     $class_lv1 = 0;
                    //     $class_lv2 = 0;
                    //     $class_lv3 = $value->level_next_1;
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
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hotrokinhphi)->getStyle('L'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);        
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_old)->getStyle('M'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_cur)->getStyle('N'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight); 

                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_cur)->getStyle('O'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_new)->getStyle('P'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->nhucau)->getStyle('Q'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->dutoan)->getStyle('R'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
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
                    $col = 0;   $row++;

                    // $strYear = substr((string)$value->history_year, 0, 4);
                    // if ($strYear < $data_results['report_year']) {
                    //     $class_lv1 = $value->level_next;
                    //     $class_lv2 = $value->level_next_1;
                    //     $class_lv3 = $value->level_next_2;
                    // }

                    // if ($strYear == $data_results['report_year']) {
                    //     $class_lv1 = 0;
                    //     $class_lv2 = $value->level_next_1;
                    //     $class_lv3 = $value->level_next_2;
                    // }

                    // if ($strYear > $data_results['report_year']) {
                    //     $class_lv1 = 0;
                    //     $class_lv2 = 0;
                    //     $class_lv3 = $value->level_next_1;
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
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hotrokinhphi)->getStyle('L'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);        
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_old)->getStyle('M'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_cur)->getStyle('N'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight); 

                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_cur)->getStyle('O'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_new)->getStyle('P'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->nhucau)->getStyle('Q'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->dutoan)->getStyle('R'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
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
                    $col = 0;   $row++;

                    // $strYear = substr((string)$value->history_year, 0, 4);
                    // if ($strYear < $data_results['report_year']) {
                    //     $class_lv1 = $value->level_next;
                    //     $class_lv2 = $value->level_next_1;
                    //     $class_lv3 = $value->level_next_2;
                    // }

                    // if ($strYear == $data_results['report_year']) {
                    //     $class_lv1 = 0;
                    //     $class_lv2 = $value->level_next_1;
                    //     $class_lv3 = $value->level_next_2;
                    // }

                    // if ($strYear > $data_results['report_year']) {
                    //     $class_lv1 = 0;
                    //     $class_lv2 = 0;
                    //     $class_lv3 = $value->level_next_1;
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
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hotrokinhphi)->getStyle('L'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);        
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_old)->getStyle('M'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_cur)->getStyle('N'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight); 

                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_cur)->getStyle('O'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_new)->getStyle('P'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->nhucau)->getStyle('Q'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->dutoan)->getStyle('R'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                }
            }
        });

        if($type){
            return $excel->setFilename($filename)->store('xlsx', storage_path().'/exceldownload/HTATHS');
        }else{
            return $excel->setFilename($filename)->download('xlsx');
        }
    }
    
    public function countValueHTATHS($type = null,$code){

        if($type!=null){
            $count = DB::table('qlhs_hotrohocsinhdantocthieuso')->where('type_code','=',$code)->where('type','=',$type)
            ->select(
                DB::raw('sum(hocky2_old) as tonghocky2_old'),
                DB::raw('sum(hocky1_cur) as tonghocky1_cur'),
                DB::raw('sum(hocky2_cur) as tonghocky2_cur'),
                DB::raw('sum(hocky1_new) as tonghocky1_new'),
                DB::raw('sum(nhucau) as tong_nhucau'),
                DB::raw('sum(dutoan) as tong_dutoan'))->first();
            return $count;
        }else{
            $count = DB::table('qlhs_hotrohocsinhdantocthieuso')->where('type_code','=',$code)
            ->select(
                DB::raw('sum(hocky2_old) as tonghocky2_old'),
                DB::raw('sum(hocky1_cur) as tonghocky1_cur'),
                DB::raw('sum(hocky2_cur) as tonghocky2_cur'),
                DB::raw('sum(hocky1_new) as tonghocky1_new'),
                DB::raw('sum(nhucau) as tong_nhucau'),
                DB::raw('sum(dutoan) as tong_dutoan'))->first();
            return $count;
        }
    }

//---------------------------------------------------------------------HBHSDTNT------------------------------------------------------------------------
    public function getDataHBHSDTNT($school_id, $year, $report_name, $user_sign, $user_create, $note, $status){
        $result = [];
        try {

            $current_user_id = Auth::user()->id;
            $current_date = Carbon::now('Asia/Ho_Chi_Minh');


            $check = TRUE;

            $getDataTypeA = DB::table('qlhs_profile')
            ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id = 70 AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.$year.' AND (qlhs_profile_subject.end_year is null OR qlhs_profile_subject.end_year > '.$year.')'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))
            ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$school_id.' AND kp.id_doituong = 119 AND '.($year - 1).' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 1).''))
            ->leftJoin('qlhs_tonghopchedo as yearold', 'yearold.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearold.qlhs_thcd_nam = '.($year - 1).' and (yearold.qlhs_thcd_trangthai_HBHSDTNT = 1 or yearold.qlhs_thcd_trangthai_HBHSDTNT_HK2 = 1)'))
            ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearcur.qlhs_thcd_nam = '.$year.' and (yearcur.qlhs_thcd_trangthai_HBHSDTNT = 1 or yearcur.qlhs_thcd_trangthai_HBHSDTNT_HK2 = 1)'))
            ->leftJoin('qlhs_tonghopchedo as yearnew', 'yearnew.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearnew.qlhs_thcd_nam = '.($year + 1)))
            ->where('qlhs_profile.profile_school_id', '=', $school_id)
            ->where('qlhs_profile.profile_year', '<', $year.'-06-01')
            ->select('qlhs_profile.profile_id', 'qlhs_profile.profile_school_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years', 
                DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 70 then 1 else 0 END) as hotrokinhphi'), 

                DB::raw('(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hocky2_old'), 
                DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hocky1_cur'), 
                DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.($year + 1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hocky2_cur'), 
                DB::raw('(CASE when qlhs_profile_history.level_new <> "" and qlhs_profile.profile_year < "'.($year + 1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hocky1_new'),
                    DB::raw('MAX(
                            CASE
                            WHEN ( qlhs_profile_history.level_old <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 70
                            AND qlhs_profile.profile_year < "'.$year.'-06-01"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01"
                                )
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.$year.' AND kp.id_doituong = 119 AND yearold.qlhs_thcd_trangthai_HK2 = 1 AND yearold.qlhs_thcd_trangthai_HBHSDTNT_HK2 = 1) 
                            OR (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 70
                            AND qlhs_profile.profile_year < "'.$year.'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 119 AND yearcur.qlhs_thcd_trangthai = 1 AND yearcur.qlhs_thcd_trangthai_HBHSDTNT = 1) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS NHUCAU'),
                    DB::raw('MAX(
                            CASE
                            WHEN (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 70
                            AND qlhs_profile.profile_year < "'.($year + 1).'-06-01"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01"
                                )
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.($year + 1).' AND kp.id_doituong = 119 AND yearcur.qlhs_thcd_trangthai_HK2 = 1 AND yearcur.qlhs_thcd_trangthai_HBHSDTNT_HK2 = 1) 
                            OR (qlhs_profile_history.level_new <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 70
                            AND qlhs_profile.profile_year < "'.($year + 1).'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.($year + 1).' AND kp.id_doituong = 119) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS DUTOAN'))
            ->groupBy('qlhs_profile.profile_id', 'qlhs_profile.profile_school_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years');
            
            $getDataType1 = DB::table(DB::raw("({$getDataTypeA->toSql()}) as m"))
                ->mergeBindings( $getDataTypeA )
                ->select('m.profile_id',
                        'm.profile_year',
                        'm.level_old',
                        'm.level_cur',
                        'm.level_new',
                        'm.profile_leaveschool_date',
                        'm.hotrokinhphi',
                        'm.hocky2_old', 
                        'm.hocky1_cur', 
                        'm.hocky2_cur', 
                        'm.hocky1_new', 
                        DB::raw('SUM(m.NHUCAU) as NHUCAU'), 
                        DB::raw('SUM(m.DUTOAN) as DUTOAN'))

                ->groupBy('m.profile_id',
                        'm.profile_year',
                        'm.level_old',
                        'm.level_cur',
                        'm.level_new',
                        'm.profile_leaveschool_date',
                        'm.hotrokinhphi',
                        'm.hocky2_old', 
                        'm.hocky1_cur', 
                        'm.hocky2_cur', 
                        'm.hocky1_new')
                ->get();


            $getDataTypeB = DB::table('qlhs_profile')
            ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id = 70 AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.$year.' AND (qlhs_profile_subject.end_year is null OR qlhs_profile_subject.end_year > '.$year.')'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))
            ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$school_id.' AND kp.id_doituong = 119 AND '.($year - 1).' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 1).''))
            ->leftJoin('qlhs_tonghopchedo as yearold', 'yearold.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearold.qlhs_thcd_nam = '.($year - 1).' and (yearold.qlhs_thcd_trangthai_HBHSDTNT = 1 or yearold.qlhs_thcd_trangthai_HBHSDTNT_HK2 = 1)'))
            ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearcur.qlhs_thcd_nam = '.$year))
            ->leftJoin('qlhs_tonghopchedo as yearnew', 'yearnew.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearnew.qlhs_thcd_nam = '.($year + 1)))
            ->where('qlhs_profile.profile_school_id', '=', $school_id)
            ->where('qlhs_profile.profile_year', '>', $year.'-05-31')
            ->where('qlhs_profile.profile_year', '<', ($year + 1).'-06-01')
            // ->where('yearcur.qlhs_thcd_nam', '=', DB::raw($year.' and (yearcur.qlhs_thcd_trangthai_HBHSDTNT = 1 or yearcur.qlhs_thcd_trangthai_HBHSDTNT_HK2 = 1)'))
            ->select('qlhs_profile.profile_id', 'qlhs_profile.profile_school_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years', 
                DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 70 then 1 else 0 END) as hotrokinhphi'), 

                DB::raw('(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hocky2_old'), 
                DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hocky1_cur'), 
                DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.($year + 1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hocky2_cur'), 
                DB::raw('(CASE when qlhs_profile_history.level_new <> "" and qlhs_profile.profile_year < "'.($year + 1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hocky1_new'),
                    DB::raw('MAX(
                            CASE
                            WHEN ( qlhs_profile_history.level_old <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 70
                            AND qlhs_profile.profile_year < "'.$year.'-06-01"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01"
                                )
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.$year.' AND kp.id_doituong = 119 AND yearold.qlhs_thcd_trangthai_HK2 = 1 AND yearold.qlhs_thcd_trangthai_HBHSDTNT_HK2 = 1) 
                            OR (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 70
                            AND qlhs_profile.profile_year < "'.$year.'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 119 AND yearcur.qlhs_thcd_trangthai = 1 AND yearcur.qlhs_thcd_trangthai_HBHSDTNT = 1) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS NHUCAU'),
                    DB::raw('MAX(
                            CASE
                            WHEN (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 70
                            AND qlhs_profile.profile_year < "'.($year + 1).'-06-01"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01"
                                )
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.($year + 1).' AND kp.id_doituong = 119 AND yearcur.qlhs_thcd_trangthai_HK2 = 1 AND yearcur.qlhs_thcd_trangthai_HBHSDTNT_HK2 = 1) 
                            OR (qlhs_profile_history.level_new <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 70
                            AND qlhs_profile.profile_year < "'.($year + 1).'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.($year + 1).' AND kp.id_doituong = 119) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS DUTOAN'))
            ->groupBy('qlhs_profile.profile_id', 'qlhs_profile.profile_school_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years');
            
            $getDataType2 = DB::table(DB::raw("({$getDataTypeB->toSql()}) as m"))
                ->mergeBindings( $getDataTypeB )
                ->select('m.profile_id',
                        'm.profile_year',
                        'm.level_old',
                        'm.level_cur',
                        'm.level_new',
                        'm.profile_leaveschool_date',
                        'm.hotrokinhphi',
                        'm.hocky2_old', 
                        'm.hocky1_cur', 
                        'm.hocky2_cur', 
                        'm.hocky1_new', 
                        DB::raw('SUM(m.NHUCAU) as NHUCAU'), 
                        DB::raw('SUM(m.DUTOAN) as DUTOAN'))

                ->groupBy('m.profile_id',
                        'm.profile_year',
                        'm.level_old',
                        'm.level_cur',
                        'm.level_new',
                        'm.profile_leaveschool_date',
                        'm.hotrokinhphi',
                        'm.hocky2_old', 
                        'm.hocky1_cur', 
                        'm.hocky2_cur', 
                        'm.hocky1_new')
                ->get();


            $getDataTypeC = DB::table('qlhs_profile')
            ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id = 70 AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.($year + 1).' AND (qlhs_profile_subject.end_year is null OR qlhs_profile_subject.end_year > '.($year + 1).')'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.($year + 1).'-'.($year + 2).'"'))
            ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$school_id.' AND kp.id_doituong = 119 AND '.$year.' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 2).''))
            ->leftJoin('qlhs_tonghopchedo as yearold', 'yearold.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearold.qlhs_thcd_nam = '.$year))
            ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearcur.qlhs_thcd_nam = '.($year + 1)))
            ->leftJoin('qlhs_tonghopchedo as yearnew', 'yearnew.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearnew.qlhs_thcd_nam = '.($year + 2)))
            ->where('qlhs_profile.profile_school_id', '=', $school_id)
            ->where('qlhs_profile.profile_year', '>', ($year + 1).'-05-31')
            ->where('qlhs_profile.profile_year', '<', ($year + 2).'-06-01')
            // ->where('yearcur.qlhs_thcd_nam', '=', ($year + 1))
            ->select('qlhs_profile.profile_id', 'qlhs_profile.profile_school_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years', 
                DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 70 then 1 else 0 END) as hotrokinhphi'), 

                DB::raw('(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hocky2_old'), 
                DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hocky1_cur'), 
                DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.($year + 1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hocky2_cur'), 
                DB::raw('(CASE when qlhs_profile_history.level_new <> "" and qlhs_profile.profile_year < "'.($year + 1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hocky1_new'),
                    // DB::raw('MAX(
                    //         CASE
                    //         WHEN ( qlhs_profile_history.level_old <> ""
                    //         AND qlhs_profile_subject.profile_subject_subject_id = 70
                    //         AND qlhs_profile.profile_year < "'.($year + 1).'-06-01"
                    //         AND (
                    //             qlhs_profile.profile_leaveschool_date IS NULL
                    //             OR (
                    //                 qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01"
                    //             )
                    //         ) AND kp.months in (1,2,3,4,5) and kp.years = '.($year + 1).' AND kp.id_doituong = 119) 
                    //         OR (qlhs_profile_history.level_cur <> ""
                    //         AND qlhs_profile_subject.profile_subject_subject_id = 70
                    //         AND qlhs_profile.profile_year < "'.($year + 1).'-12-31"
                    //         AND (
                    //             qlhs_profile.profile_leaveschool_date IS NULL
                    //             OR (
                    //                 qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31"
                    //             )
                    //         )
                    //         AND kp.months in (9,10,11,12) and kp.years = '.($year + 1).' AND kp.id_doituong = 119) THEN
                    //             kp.value_m
                    //         ELSE
                    //             0
                    //         END
                    //     ) AS NHUCAU'),
                    DB::raw('MAX(
                            CASE
                            WHEN (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 70
                            AND qlhs_profile.profile_year < "'.($year + 2).'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.($year + 2).'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.($year + 2).' AND kp.id_doituong = 119) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS DUTOAN'))
            ->groupBy('qlhs_profile.profile_id', 'qlhs_profile.profile_school_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years');
            
            $getDataType3 = DB::table(DB::raw("({$getDataTypeC->toSql()}) as m"))
                ->mergeBindings( $getDataTypeC )
                ->select('m.profile_id',
                        'm.profile_year',
                        'm.level_old',
                        'm.level_cur',
                        'm.level_new',
                        'm.profile_leaveschool_date',
                        'm.hotrokinhphi',
                        'm.hocky2_old', 
                        'm.hocky1_cur', 
                        'm.hocky2_cur', 
                        'm.hocky1_new', 
                        // DB::raw('SUM(m.NHUCAU) as NHUCAU'), 
                        DB::raw('SUM(m.DUTOAN) as DUTOAN'))

                ->groupBy('m.profile_id',
                        'm.profile_year',
                        'm.level_old',
                        'm.level_cur',
                        'm.level_new',
                        'm.profile_leaveschool_date',
                        'm.hotrokinhphi',
                        'm.hocky2_old', 
                        'm.hocky1_cur', 
                        'm.hocky2_cur', 
                        'm.hocky1_new')
                ->get();

            $time = time();

            if ((is_null($getDataType1) || empty($getDataType1) || count($getDataType1) == 0) && (is_null($getDataType2) || empty($getDataType2) || count($getDataType2) == 0) && (is_null($getDataType3) || empty($getDataType3) || count($getDataType3) == 0)) {
                $result['success'] = "Trường không có học sinh thuộc đối tượng!";
                return $result;
            }
            
            if (!is_null($getDataType1) && !empty($getDataType1) && count($getDataType1) > 0) {
                $check = $this->insertHBHSDTNT($getDataType1, 1, $current_user_id, $school_id, $year, $time);
            }

            if (!is_null($getDataType2) && !empty($getDataType2) && count($getDataType2) > 0 && $check) {
                $check = $this->insertHBHSDTNT($getDataType2, 2, $current_user_id, $school_id, $year, $time);
            }

            if (!is_null($getDataType3) && !empty($getDataType3) && count($getDataType3) > 0 && $check) {
                $check = $this->insertHBHSDTNT($getDataType3, 3, $current_user_id, $school_id, $year, $time);
            }

            if ($check) {
                $type_code = 'HBHSDTNT-' . $current_user_id . '-' . $school_id . '-' . $year . '' . ($year + 1) . '-' . $time;

                $dir = storage_path().'/files/HBHSDTNT';
                

                $insert_hosobaocao_id = DB::table('qlhs_hosobaocao')->insertGetId(['report_name' => $report_name, 'report_type' => $type_code, 'report_date' => $current_date, 'created_at' => $current_date, 'updated_at' => $current_date, 'create_userid' => $current_user_id, 'update_userid' => $current_user_id, 'report_user' => $user_create, 'report_user_sign' => $user_sign, 'report_attach_name' => '', 'report_nature' => $status, 'report_year' => $year, 'report_id_truong' => $school_id, 'report_note' => $note, 'report' => 'HBHSDTNT']);

                if (!is_null($insert_hosobaocao_id) && $insert_hosobaocao_id > 0) {
                    $this->exportforSchoolsHBHSDTNT($insert_hosobaocao_id);

                    if (file_exists(storage_path().'/exceldownload/HBHSDTNT/'.$type_code.'.xlsx')) {
                        // $result['success'] = "Thêm mới thành công!";
                        // return TRUE;
                    }
                    else {
                        $deleteHoso = DB::table('qlhs_hosobaocao')->where('report_type', 'LIKE', '%'.$type_code.'%')->delete();
                        $deleteHBHSDTNT = DB::table('qlhs_hotrohocsinhdantocthieuso')->where('type_code', 'LIKE', '%'.$type_code.'%')->delete();
                        // $result['error'] = "Thêm mới thất bại!";
                        // return false;
                    }
                }
                // else {$result['error'] = "Thêm mới thất bại!"; return false;}
                return $insert_hosobaocao_id;
            }
            else {
                return 0;
            }

            // return $result;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function insertHBHSDTNT($getDataType, $type, $current_user_id, $school_id, $year, $time){
        try {
            $bool = TRUE;
            $count = 1;
            
            $type_code = 'HBHSDTNT-' . $current_user_id . '-' . $school_id . '-' . $year . '' . ($year + 1) . '-' . $time;

            foreach ($getDataType as $value) {
                $tongnhucau = 0;
                $tongdutoan = 0;

                if ($type != 3) {
                    $tongnhucau = $value->{'NHUCAU'};
                }
                
                $tongdutoan = $value->{'DUTOAN'};

                if ($tongnhucau > 0 || $tongdutoan > 0) {
                    $insert_type = DB::table('qlhs_hotrohocsinhdantocthieuso')
                    ->insert([
                        'profile_id' => $value->{'profile_id'}, 
                        'hotrokinhphi' => $value->{'hotrokinhphi'}, 
                        'hocky2_old' => $value->{'hocky2_old'}, 
                        'hocky1_cur' => $value->{'hocky1_cur'}, 
                        'hocky2_cur' => $value->{'hocky2_cur'}, 
                        'hocky1_new' => $value->{'hocky1_new'}, 
                        'nhucau' => $tongnhucau, 
                        'dutoan' => $tongdutoan, 
                        'type_code' => $type_code, 
                        'type' => $type,
                        'trangthai_pheduyet_HSDTTS' => 1,
                        'trangthai_thamdinh_HSDTTS' => 1]);

                    $count++;

                    if ($insert_type == 0) {
                        
                        $deleteHBHSDTNT = DB::table('qlhs_hotrohocsinhdantocthieuso')->where('type_code', 'LIKE', '%'.$type_code.'%')->where('type', '=', $type)->delete();

                        $count--;
                        break;
                    }
                }
            }

            if ($count > 1) {
                $bool = TRUE;
            }
            else {
                $bool = FALSE;
            }

            return TRUE;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function exportforSchoolsHBHSDTNT($id, $type = true){
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
            $data_results['aCount'] = $this->countValueHBHSDTNT('1',$getSchoolName->report_type);
            $data_results['bCount'] = $this->countValueHBHSDTNT('2',$getSchoolName->report_type);
            $data_results['cCount'] = $this->countValueHBHSDTNT('3',$getSchoolName->report_type);
            $data_results['TotalCount'] = $this->countValueHBHSDTNT(null,$getSchoolName->report_type);
            //Get by type A
            $year = $getSchoolName->report_year;
            $data_results['a'] = DB::table('qlhs_hotrohocsinhdantocthieuso')
            ->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_hotrohocsinhdantocthieuso.profile_id')
            ->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_hotrohocsinhdantocthieuso.profile_id and qlhs_profile_history.history_year = "'.$getSchoolName->report_year.'-'.($getSchoolName->report_year + 1).'"'))
            ->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
            ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
            ->where('qlhs_hotrohocsinhdantocthieuso.type_code', '=', $getSchoolName->report_type)
            ->where('qlhs_hotrohocsinhdantocthieuso.type', '=', 1)
            ->select('qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile_history.history_year', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'qlhs_hotrohocsinhdantocthieuso.*')->get();

            $data_results['b'] = DB::table('qlhs_hotrohocsinhdantocthieuso')
            ->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_hotrohocsinhdantocthieuso.profile_id')
            ->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_hotrohocsinhdantocthieuso.profile_id and qlhs_profile_history.history_year = "'.$getSchoolName->report_year.'-'.($getSchoolName->report_year + 1).'"'))
            ->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
            ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
            ->where('qlhs_hotrohocsinhdantocthieuso.type_code', '=', $getSchoolName->report_type)
            ->where('qlhs_hotrohocsinhdantocthieuso.type', '=', 2)
            ->select('qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile_history.history_year', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'qlhs_hotrohocsinhdantocthieuso.*')->get();

            $data_results['c'] = DB::table('qlhs_hotrohocsinhdantocthieuso')
            ->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_hotrohocsinhdantocthieuso.profile_id')
            ->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_hotrohocsinhdantocthieuso.profile_id and qlhs_profile_history.history_year = "'.($getSchoolName->report_year + 1).'-'.($getSchoolName->report_year + 2).'"'))
            ->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
            ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
            ->where('qlhs_hotrohocsinhdantocthieuso.type_code', '=', $getSchoolName->report_type)
            ->where('qlhs_hotrohocsinhdantocthieuso.type', '=', 3)
            ->select('qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile_history.history_year', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'qlhs_hotrohocsinhdantocthieuso.*')->get();
        }
        $data_results['schools_name'] = $getSchoolName->schools_name;
        $data_results['report_year'] = $getSchoolName->report_year;
        $this->addCellExcelHBHSDTNT($data_results, $getSchoolName->report_type, $type);
    }

    private function addCellExcelHBHSDTNT($data_results, $filename, $type = true){
        $excel =    Excel::load(storage_path().'/exceltemplate/laphosoHBHSDTNT.xlsx', function($reader) use($data_results){
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
            $reader->getActiveSheet()->setCellValueByColumnAndRow(0, 2, 'NHU CẦU KINH PHÍ NĂM '.$data_results['report_year'].', DỰ TOÁN NĂM '.($data_results['report_year'] + 1).' ĐỐI VỚI CHÍNH SÁCH HỖ TRỢ HỌC BỔNG DÀNH CHO HỌC SINH DÂN TỘC NỘI TRÚ')->getStyle('A2')->applyFromArray($FontArray)->applyFromArray($style);
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
                    $col = 0;   $row++;

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
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hotrokinhphi)->getStyle('L'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);        
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_old)->getStyle('M'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_cur)->getStyle('N'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight); 

                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_cur)->getStyle('O'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_new)->getStyle('P'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->nhucau)->getStyle('Q'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->dutoan)->getStyle('R'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
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
                    $col = 0;   $row++;

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
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hotrokinhphi)->getStyle('L'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);        
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_old)->getStyle('M'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_cur)->getStyle('N'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight); 

                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_cur)->getStyle('O'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_new)->getStyle('P'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->nhucau)->getStyle('Q'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->dutoan)->getStyle('R'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
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
                    $col = 0;   $row++;

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
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hotrokinhphi)->getStyle('L'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);        
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_old)->getStyle('M'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_cur)->getStyle('N'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight); 

                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky2_cur)->getStyle('O'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->hocky1_new)->getStyle('P'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->nhucau)->getStyle('Q'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);      
                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->dutoan)->getStyle('R'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                }
            }
        });

        if($type){
            return $excel->setFilename($filename)->store('xlsx', storage_path().'/exceldownload/HBHSDTNT');
        }else{
            return $excel->setFilename($filename)->download('xlsx');
        }
    }
    
    public function countValueHBHSDTNT($type = null,$code){

        if($type!=null){
            $count = DB::table('qlhs_hotrohocsinhdantocthieuso')->where('type_code','=',$code)->where('type','=',$type)
            ->select(
                DB::raw('sum(hocky2_old) as tonghocky2_old'),
                DB::raw('sum(hocky1_cur) as tonghocky1_cur'),
                DB::raw('sum(hocky2_cur) as tonghocky2_cur'),
                DB::raw('sum(hocky1_new) as tonghocky1_new'),
                DB::raw('sum(nhucau) as tong_nhucau'),
                DB::raw('sum(dutoan) as tong_dutoan'))->first();
            return $count;
        }else{
            $count = DB::table('qlhs_hotrohocsinhdantocthieuso')->where('type_code','=',$code)
            ->select(
                DB::raw('sum(hocky2_old) as tonghocky2_old'),
                DB::raw('sum(hocky1_cur) as tonghocky1_cur'),
                DB::raw('sum(hocky2_cur) as tonghocky2_cur'),
                DB::raw('sum(hocky1_new) as tonghocky1_new'),
                DB::raw('sum(nhucau) as tong_nhucau'),
                DB::raw('sum(dutoan) as tong_dutoan'))->first();
            return $count;
        }
    }

//---------------------------------------------------------------------NGNA-----------------------------------------------------------------------------
    public function getDataNGNA($school_id, $year, $report_name, $user_sign, $user_create, $note, $status){
        $result = [];
        try {
            $current_user_id = Auth::user()->id;
            $current_date = Carbon::now('Asia/Ho_Chi_Minh');
            
            
            $check = TRUE;

            $getMoney = DB::table('qlhs_profile')
            ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$school_id.' AND kp.id_doituong = 102 AND '.($year - 1).' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 1).''))
            
            ->where('qlhs_profile.profile_school_id', '=', $school_id)
            
            ->select(
                    DB::raw('MAX(
                            CASE
                            WHEN (kp.months in (1,2,3,4,5) and kp.years = '.$year.' AND kp.id_doituong = 102) 
                            THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS NHUCAU1'),
                    DB::raw('MAX(
                            CASE
                            WHEN (kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 102) 
                            THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS NHUCAU2'),
                    DB::raw('MAX(
                            CASE
                            WHEN (kp.months in (1,2,3,4,5) and kp.years = '.($year + 1).' AND kp.id_doituong = 102) 
                            THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS DUTOAN1'),
                    DB::raw('MAX(
                            CASE
                            WHEN (kp.months in (9,10,11,12) and kp.years = '.($year + 1).' AND kp.id_doituong = 102) 
                            THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS DUTOAN2'));
            
            $getData = DB::table(DB::raw("({$getMoney->toSql()}) as m"))
                ->mergeBindings( $getMoney )
                ->select(
                        DB::raw('SUM(m.NHUCAU1) as nhucau'), 
                        DB::raw('SUM(m.DUTOAN1) as duToan'),
                        DB::raw('SUM(m.NHUCAU2) as nhucau1'), 
                        DB::raw('SUM(m.DUTOAN2) as duToan1'))
                ->get();

            // return $getData[0]->{'nhucau'};

            // $getHS_old = DB::select("select * from qlhs_profile where profile_bantru = 1 and profile_school_id = ".$school_id." and profile_year < '".$year."-06-01' and (profile_leaveschool_date IS NULL or profile_leaveschool_date > '".$year."-01-01')", array());

            $getHS_old = DB::table('qlhs_profile')
            ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id in (46, 70) AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.$year.' AND (qlhs_profile_subject.end_year is null OR qlhs_profile_subject.end_year > '.$year.')'))
            ->where('qlhs_profile.profile_school_id', '=', $school_id)
            ->where('qlhs_profile.profile_year', '<', DB::raw('"'.$year.'-06-01" AND (profile_leaveschool_date IS NULL OR profile_leaveschool_date > "'.$year.'-01-01")'))
            ->select('profile_id')->groupBy('profile_id')->get();
            

            $getHS_cur1 = DB::table('qlhs_profile')
            ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id in (46, 70) AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.$year.' AND (qlhs_profile_subject.end_year is null OR qlhs_profile_subject.end_year > '.$year.')'))
            ->where('qlhs_profile.profile_school_id', '=', $school_id)
            ->where('qlhs_profile.profile_year', '<', DB::raw('"'.$year.'-12-31" AND (profile_leaveschool_date IS NULL OR profile_leaveschool_date > "'.$year.'-06-01")'))
            ->select('profile_id')->groupBy('profile_id')->get();


            $getHS_cur2 = DB::table('qlhs_profile')
            ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id in (46, 70) AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.$year.' AND (qlhs_profile_subject.end_year is null OR qlhs_profile_subject.end_year > '.$year.')'))
            ->where('qlhs_profile.profile_school_id', '=', $school_id)
            ->where('qlhs_profile.profile_year', '<', DB::raw('"'.($year + 1).'-06-01" AND (profile_leaveschool_date IS NULL OR profile_leaveschool_date > "'.($year + 1).'-01-01")'))
            ->select('profile_id')->groupBy('profile_id')->get();


            $getHS_new = DB::table('qlhs_profile')
            ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id in (46, 70) AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.($year + 1).' AND (qlhs_profile_subject.end_year is null OR qlhs_profile_subject.end_year > '.($year + 1).')'))
            ->where('qlhs_profile.profile_school_id', '=', $school_id)
            ->where('qlhs_profile.profile_year', '<', DB::raw('"'.($year + 1).'-12-31" AND (profile_leaveschool_date IS NULL OR profile_leaveschool_date > "'.($year + 1).'-06-01")'))
            ->select('profile_id')->groupBy('profile_id')->get();

            $time = time();

            $getNGNA_old = DB::table('qlhs_nguoinauan')->where('NGNA_school_id', $school_id)->where('NGNA_startdate', '<=', DB::raw('"'.$year.'-01-01" AND (NGNA_enddate >= "'.$year.'-06-01" OR NGNA_enddate is null)'))->select('NGNA_amount')->first();

            $getNGNA_cur1 = DB::table('qlhs_nguoinauan')->where('NGNA_school_id', $school_id)->where('NGNA_startdate', '<=', DB::raw('"'.$year.'-09-01" AND (NGNA_enddate >= "'.$year.'-12-31" OR NGNA_enddate is null)'))->select('NGNA_amount')->first();


            $getNGNA_cur2 = DB::table('qlhs_nguoinauan')->where('NGNA_school_id', $school_id)->where('NGNA_startdate', '<=', DB::raw('"'.($year + 1).'-01-01" AND (NGNA_enddate >= "'.($year + 1).'-06-01" OR NGNA_enddate is null)'))->select('NGNA_amount')->first();

            $getNGNA_new = DB::table('qlhs_nguoinauan')->where('NGNA_school_id', $school_id)->where('NGNA_startdate', '<=', DB::raw('"'.($year + 1).'-09-01" AND (NGNA_enddate >= "'.($year + 1).'-12-31" OR NGNA_enddate is null)'))->select('NGNA_amount')->first();

            if ((is_null($getHS_old) || empty($getHS_old) || count($getHS_old) == 0)
                && (is_null($getHS_cur1) || empty($getHS_cur1) || count($getHS_cur1) == 0)
                && (is_null($getHS_cur2) || empty($getHS_cur2) || count($getHS_cur2) == 0)
                && (is_null($getHS_new) || empty($getHS_new) || count($getHS_new) == 0)
                && (is_null($getNGNA_old) || empty($getNGNA_old) || $getNGNA_old->NGNA_amount == 0)
                && (is_null($getNGNA_cur1) || empty($getNGNA_cur1) || $getNGNA_cur1->NGNA_amount == 0)
                && (is_null($getNGNA_cur2) || empty($getNGNA_cur2) || $getNGNA_cur2->NGNA_amount == 0)
                && (is_null($getNGNA_new) || empty($getNGNA_new) || $getNGNA_new->NGNA_amount == 0)
                && $getData[0]->{'nhucau'} == 0 && $getData[0]->{'duToan'} == 0) {
                // $result['success'] = "Danh sách trống!";
                return $result;
            }

            $amount_old = 0;
            $amount_cur1 = 0;
            $amount_cur2 = 0;
            $amount_new = 0;

            if (!is_null($getNGNA_old->NGNA_amount) && !empty($getNGNA_old->NGNA_amount)) {
                $amount_old = $getNGNA_old->NGNA_amount;
            }
            if (!is_null($getNGNA_cur1->NGNA_amount) && !empty($getNGNA_cur1->NGNA_amount)) {
                $amount_cur1 = $getNGNA_cur1->NGNA_amount;
            }
            if (!is_null($getNGNA_cur2->NGNA_amount) && !empty($getNGNA_cur2->NGNA_amount)) {
                $amount_cur2 = $getNGNA_cur2->NGNA_amount;
            }
            if (!is_null($getNGNA_new->NGNA_amount) && !empty($getNGNA_new->NGNA_amount)) {
                $amount_new = $getNGNA_new->NGNA_amount;
            }
            
            // if (!is_null($getData) && !empty($getData) && count($getData) > 0) {
            $check = $this->insertNGNA($getData[0]->{'nhucau'}, $getData[0]->{'duToan'}, $getData[0]->{'nhucau1'}, $getData[0]->{'duToan1'}, count($getHS_old), count($getHS_cur1), count($getHS_cur2), count($getHS_new), $current_user_id, $school_id, $year, $time, $amount_old, $amount_cur1, $amount_cur2, $amount_new);
            // }

            if ($check) {
                $type_code = 'NGNA-' . $current_user_id . '-' . $school_id . '-' . $year . '' . ($year + 1) . '-' . $time;

                // $dir = storage_path().'/files/NGNA';
                // if(trim($files) != ""){
                //     if(file_exists($dir.'/'. $filename_attach)){
                //         $files->move($dir, $filename_attach.'-'.$time); 
                //         //File::delete($dir.'/'. $filename_attach); 
                //     }else{
                //         $files->move($dir, $filename_attach);   
                //     }
                // }

                $insert_hosobaocao_id = DB::table('qlhs_hosobaocao')->insertGetId(['report_name' => $report_name, 'report_type' => $type_code, 'report_date' => $current_date, 'created_at' => $current_date, 'updated_at' => $current_date, 'create_userid' => $current_user_id, 'update_userid' => $current_user_id, 'report_user' => $user_create, 'report_user_sign' => $user_sign, 'report_attach_name' => '', 'report_nature' => $status, 'report_year' => $year, 'report_id_truong' => $school_id, 'report_note' => $note, 'report' => 'NGNA']);

                if (!is_null($insert_hosobaocao_id) && $insert_hosobaocao_id > 0) {
                    $this->exportforSchoolsNGNA($insert_hosobaocao_id);

                    if (file_exists(storage_path().'/exceldownload/NGNA/'.$type_code.'.xlsx')) {
                        // $result['success'] = "Thêm mới thành công!";
                    }
                    else {
                        $deleteHoso = DB::table('qlhs_hosobaocao')->where('report_type', 'LIKE', '%'.$type_code.'%')->delete();
                        $deleteNGNA = DB::table('qlhs_hotronguoinauan')->where('type_code', 'LIKE', '%'.$type_code.'%')->delete();
                        // $result['error'] = "Thêm mới thất bại!";
                    }
                }

                return $insert_hosobaocao_id;
                // else {$result['error'] = "Thêm mới thất bại!";}
            }
            // else {$result['error'] = "Thêm mới thất bại!";}

            // return $result;
        } catch (Exception $e) {
            return $e;
        }       
    }

    public function insertNGNA($nhucau, $dutoan, $nhucau1, $dutoan1, $hs_old, $hs_cur1, $hs_cur2, $hs_new, $current_user_id, $school_id, $year, $time, $amount_old, $amount_cur1, $amount_cur2, $amount_new){
        try {
            $bool = TRUE;
            
            $type_code = 'NGNA-' . $current_user_id . '-' . $school_id . '-' . $year . '' . ($year + 1) . '-' . $time;

            // foreach ($getData as $value) {
                $sohocsinhhocky2_old = (int)$hs_old;//$value->{'sohocsinhhocky2_old'};
                $sohocsinhhocky1_cur = (int)$hs_cur1;//$value->{'sohocsinhhocky1_cur'};
                $sohocsinhhocky2_cur = (int)$hs_cur2;//$value->{'sohocsinhhocky2_cur'};
                $sohocsinhhocky1_new = (int)$hs_new;//$value->{'sohocsinhhocky1_new'};

                $tong_nhucau = 0;
                if ($amount_old > 0) {
                    $tong_nhucau += ($nhucau * 5 * $amount_old);
                }

                if ($amount_cur1 > 0) {
                    $tong_nhucau += ($nhucau1 * 4 * $amount_cur1);
                }

                $tong_dutoan = 0;
                if ($amount_cur2 > 0) {
                    $tong_dutoan += ($dutoan * 5 * $amount_cur2);
                }

                if ($amount_new > 0) {
                    $tong_dutoan += ($dutoan1 * 4 * $amount_new);
                }

                $insert_type = DB::table('qlhs_hotronguoinauan')->insert([
                    'school_id' => $school_id, 
                    'sohocsinhhocky2_old' => $sohocsinhhocky2_old, 
                    'sohocsinhhocky1_cur' => $sohocsinhhocky1_cur, 
                    'sohocsinhhocky2_cur' => $sohocsinhhocky2_cur, 
                    'sohocsinhhocky1_new' => $sohocsinhhocky1_new, 
                    'nguoinauanhocky2_old' => $amount_old, 
                    'nguoinauanhocky1_cur' => $amount_cur1, 
                    'nguoinauanhocky2_cur' => $amount_cur2, 
                    'nguoinauanhocky1_new' => $amount_new, 
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

    public function exportforSchoolsNGNA($id, $type = true){
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
        $this->addCellExcelNGNA($data_results, $getSchoolName->report_type, $type);
    }
    
    private function addCellExcelNGNA($data_results, $filename, $type = true){
        $excel =    Excel::load(storage_path().'/exceltemplate/laphosoNGNA.xlsx', function($reader) use($data_results){
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

//------------------------------------------------------------Thêm mới học sinh new------------------------------------------------------------------
    public function getViewChedo(){
        return view('admin.hoso.hosohocsinh.listingnew');
    }

    public function getViewDanhsachdalap(){
        return view('admin.hoso.hosohocsinh.listingview');
    }

    public function loadDataBaocao(Request $request)
    {
        try {
            $school_id = $request->input('SCHOOLID');
            $arrYear = $request->input('YEAR');
            $currentdate = Carbon::now('Asia/Ho_Chi_Minh')->toDateString();
            $year = [];
            $year = explode('-', $arrYear);

            $getData = DB::table('qlhs_hosobaocao')
                ->leftJoin('users', 'id', '=', 'create_userid')
                ->where('report_id_truong', '=', $school_id)
                ->where('report_year', '=', $year[1])
                ->where('report_date', 'LIKE', '%'.$currentdate.'%')
                ->select('report_id', 'report_name', 'report_type', 'report_date', 'report', 'report_status', 'first_name', 'last_name')
                ->orderBy('report_date', 'desc')->get();

            return $getData;
        } catch (Exception $e) {
            return $e;
        }
    }



//---------------------------------------------------------Danh sách học sinh chờ phê duyệt-------------------------------------------------------------
    public function getViewApproved(){
        return view('admin.hoso.hosohocsinh.listingnewapproved');
    }

    public function loadListApproved(Request $request)
    {
        $json = [];
        $start = $request->input('start');
        $limit = $request->input('limit');

        $schools_id = $request->input('SCHOOLID');
       // $year = $request->input('YEAR');
        $socongvan = $request->input('SOCONGVAN');
        $keySearch = $request->input('KEY');
        $status = $request->input('STATUS');

        // $arrYear = [];
        // $arrYear = explode("-", $year);
        // $schoolId = 37;//$request->input('SCHOOLID');
     //    $year = 2016;//$request->input('YEAR');
     //    $profileId = 834;

        // $user = Auth::user()->id;

        $datas = DB::table('qlhs_profile')
            ->join('qlhs_nationals', 'nationals_id', '=', 'profile_nationals_id')
            ->leftJoin('qlhs_miengiamhocphi', 'id_profile', '=', DB::raw('qlhs_profile.profile_id AND qlhs_miengiamhocphi.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "MGHP" and report_name = "'.$socongvan.'")'))
            ->leftJoin('qlhs_chiphihoctap', 'cpht_profile_id', '=', DB::raw('qlhs_profile.profile_id AND qlhs_chiphihoctap.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "CPHT" and report_name = "'.$socongvan.'")'))
            ->leftJoin('qlhs_hotrotienan', 'htta_profile_id', '=', DB::raw('qlhs_profile.profile_id AND qlhs_hotrotienan.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "HTAT" and report_name = "'.$socongvan.'")'))
            ->leftJoin('qlhs_hotrohocsinhbantru', 'qlhs_hotrohocsinhbantru.profile_id', '=', DB::raw('qlhs_profile.profile_id AND qlhs_hotrohocsinhbantru.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "HTBT" and report_name = "'.$socongvan.'")'))
            ->leftJoin('qlhs_hotrohocsinhkhuyettat', 'qlhs_hotrohocsinhkhuyettat.profile_id', '=', DB::raw('qlhs_profile.profile_id AND qlhs_hotrohocsinhkhuyettat.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "HSKT" and report_name = "'.$socongvan.'")'))
            ->leftJoin('qlhs_hotrohocsinhdantocthieuso AS hsdtts', 'hsdtts.profile_id', '=', DB::raw('qlhs_profile.profile_id AND hsdtts.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "HSDTTS" and report_name = "'.$socongvan.'")'))
            ->leftJoin('qlhs_hotrohocsinhdantocthieuso AS htaths', 'htaths.profile_id', '=', DB::raw('qlhs_profile.profile_id AND htaths.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "HTATHS" and report_name = "'.$socongvan.'")'))
            ->leftJoin('qlhs_hotrohocsinhdantocthieuso AS hbhsdtnt', 'hbhsdtnt.profile_id', '=', DB::raw('qlhs_profile.profile_id AND hbhsdtnt.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "HBHSDTNT" and report_name = "'.$socongvan.'")'))
            ->leftJoin('qlhs_schools', 'schools_id', '=', 'profile_school_id')
            ->leftJoin('qlhs_class', 'class_id', '=', 'profile_class_id')
            ->where('profile_school_id', '=', DB::raw($schools_id.' 
                AND (qlhs_miengiamhocphi.nhu_cau > 0 OR qlhs_miengiamhocphi.du_toan > 0 
                OR qlhs_chiphihoctap.nhu_cau > 0 OR qlhs_chiphihoctap.du_toan > 0
                OR qlhs_hotrotienan.nhu_cau > 0 OR qlhs_hotrotienan.du_toan > 0
                OR qlhs_hotrohocsinhbantru.tong_nhucau > 0 OR qlhs_hotrohocsinhbantru.tong_dutoan > 0
                OR qlhs_hotrohocsinhkhuyettat.tong_nhucau > 0 OR qlhs_hotrohocsinhkhuyettat.tong_dutoan > 0
                OR hsdtts.nhucau > 0 OR hsdtts.dutoan > 0
                OR htaths.nhucau > 0 OR htaths.dutoan > 0
                OR hbhsdtnt.nhucau > 0 OR hbhsdtnt.dutoan > 0)'))
            ->select('qlhs_profile.profile_id', 'profile_birthday', 'schools_name', 'profile_name', 'class_name', 
                DB::raw('CASE 
                    when qlhs_miengiamhocphi.nhu_cau is not null or qlhs_miengiamhocphi.du_toan is not null 
                    then (qlhs_miengiamhocphi.nhu_cau + qlhs_miengiamhocphi.du_toan) else 0 end MGHP'),
                DB::raw('CASE 
                    when qlhs_chiphihoctap.nhu_cau is not null or qlhs_chiphihoctap.du_toan is not null 
                    then (qlhs_chiphihoctap.nhu_cau + qlhs_chiphihoctap.du_toan) else 0 end CPHT'),
                DB::raw('CASE 
                    when qlhs_hotrotienan.nhu_cau is not null or qlhs_hotrotienan.du_toan is not null 
                    then (qlhs_hotrotienan.nhu_cau + qlhs_hotrotienan.du_toan) else 0 end HTAT'),
                DB::raw('CASE 
                    when qlhs_hotrohocsinhbantru.nhucau_hotrotienan is not null or qlhs_hotrohocsinhbantru.dutoan_hotrotienan is not null 
                    then (qlhs_hotrohocsinhbantru.nhucau_hotrotienan + qlhs_hotrohocsinhbantru.dutoan_hotrotienan) else 0 end HTBT_TA'),
                DB::raw('CASE 
                    when qlhs_hotrohocsinhbantru.nhucau_hotrotieno is not null or qlhs_hotrohocsinhbantru.dutoan_hotrotieno is not null 
                    then (qlhs_hotrohocsinhbantru.nhucau_hotrotieno + qlhs_hotrohocsinhbantru.dutoan_hotrotieno) else 0 end HTBT_TO'),
                DB::raw('CASE 
                    when qlhs_hotrohocsinhbantru.nhucau_VHTT is not null or qlhs_hotrohocsinhbantru.dutoan_VHTT is not null 
                    then (qlhs_hotrohocsinhbantru.nhucau_VHTT + qlhs_hotrohocsinhbantru.dutoan_VHTT) else 0 end HTBT_VHTT'),
                DB::raw('CASE 
                    when qlhs_hotrohocsinhkhuyettat.nhucau_hocbong is not null or qlhs_hotrohocsinhkhuyettat.dutoan_hocbong is not null 
                    then (qlhs_hotrohocsinhkhuyettat.nhucau_hocbong + qlhs_hotrohocsinhkhuyettat.dutoan_hocbong) else 0 end HSKT_HB'),
                DB::raw('CASE 
                    when qlhs_hotrohocsinhkhuyettat.nhucau_muadodung is not null or qlhs_hotrohocsinhkhuyettat.dutoan_muadodung is not null 
                    then (qlhs_hotrohocsinhkhuyettat.nhucau_muadodung + qlhs_hotrohocsinhkhuyettat.dutoan_muadodung) else 0 end HSKT_DDHT'),
                DB::raw('CASE 
                    when hsdtts.nhucau is not null or hsdtts.dutoan is not null 
                    then (hsdtts.nhucau + hsdtts.dutoan) else 0 end HSDTTS'),
                DB::raw('CASE 
                    when htaths.nhucau is not null or htaths.dutoan is not null 
                    then (htaths.nhucau + htaths.dutoan) else 0 end HTATHS'),
                DB::raw('CASE 
                    when hbhsdtnt.nhucau is not null or hbhsdtnt.dutoan is not null 
                    then (hbhsdtnt.nhucau + hbhsdtnt.dutoan) else 0 end HBHSDTNT'),
                // DB::raw('(qlhs_miengiamhocphi.nhu_cau + qlhs_miengiamhocphi.du_toan + qlhs_chiphihoctap.nhu_cau + qlhs_chiphihoctap.du_toan + qlhs_hotrotienan.nhu_cau + qlhs_hotrotienan.du_toan + qlhs_hotrohocsinhbantru.tong_nhucau + qlhs_hotrohocsinhbantru.tong_dutoan + qlhs_hotrohocsinhkhuyettat.tong_nhucau + qlhs_hotrohocsinhkhuyettat.tong_dutoan + hsdtts.nhucau + hsdtts.dutoan + htaths.nhucau + htaths.dutoan + hbhsdtnt.nhucau + hbhsdtnt.dutoan) as TONGTIEN'),
                DB::raw('(CASE when trangthai_pheduyet_MGHP = 1 or trangthai_pheduyet_CPHT = 1 or trangthai_pheduyet_HTAT = 1 or trangthai_pheduyet_HTBT = 1 or trangthai_pheduyet_HSKT = 1 or hsdtts.trangthai_pheduyet_HSDTTS = 1 or htaths.trangthai_pheduyet_HSDTTS = 1 or hbhsdtnt.trangthai_pheduyet_HSDTTS = 1 then 1 else 0 END) as TRANGTHAIPHEDUYET'),
                DB::raw('(CASE when trangthai_thamdinh_MGHP = 1 or trangthai_thamdinh_CPHT = 1 or trangthai_thamdinh_HTAT = 1 or trangthai_thamdinh_HTBT = 1 or trangthai_thamdinh_HSKT = 1 or hsdtts.trangthai_thamdinh_HSDTTS = 1 or htaths.trangthai_thamdinh_HSDTTS = 1 or hbhsdtnt.trangthai_thamdinh_HSDTTS = 1 then 1 else 0 END) as TRANGTHAITHAMDINH'));

        if (!is_null($status) && !empty($status)) {
            if ($status == "CHO") {
                $datas->where('trangthai_pheduyet_MGHP', '=', DB::raw('0 or trangthai_pheduyet_CPHT = 0 or trangthai_pheduyet_HTAT = 0 or trangthai_pheduyet_HTBT = 0 or trangthai_pheduyet_HSKT = 0 or hsdtts.trangthai_pheduyet_HSDTTS = 0 or htaths.trangthai_pheduyet_HSDTTS = 0 or hbhsdtnt.trangthai_pheduyet_HSDTTS = 0'));
            }
            if ($status == "DA") {
                $datas->where('trangthai_pheduyet_MGHP', '=', DB::raw('1 or trangthai_pheduyet_CPHT = 1 or trangthai_pheduyet_HTAT = 1 or trangthai_pheduyet_HTBT = 1 or trangthai_pheduyet_HSKT = 1 or hsdtts.trangthai_pheduyet_HSDTTS = 1 or htaths.trangthai_pheduyet_HSDTTS = 1 or hbhsdtnt.trangthai_pheduyet_HSDTTS = 1'));
            }
        }

        if (!is_null($keySearch) && !empty($keySearch)) {
            $datas->where('profile_name', 'LIKE', '%'.$keySearch.'%')
                    ->orWhere('class_name', 'LIKE', '%'.$keySearch.'%');
        }

        $json['totalRows'] = $datas->get()->count();
        
        $json['startRecord'] = ($start);
        $json['numRows'] = $limit;
            
        $json['data'] = $datas->orderBy('qlhs_profile.updated_at','desc')->skip($start*$limit)->take($limit)->get();

        return $json;

        // if ($arrYear[0] == "HK1") {
        //     $datas = DB::table('qlhs_tonghopchedo')
        //     ->join('qlhs_profile', 'profile_id', '=', DB::raw('qlhs_thcd_profile_id AND qlhs_thcd_school_id = '.$schools_id.' AND qlhs_thcd_nam = '.$arrYear[1].' AND qlhs_thcd_trangthai = 1'))

        //     ->leftJoin('qlhs_schools', 'schools_id', '=', 'profile_school_id')
        //     ->leftJoin('qlhs_class', 'class_id', '=', 'profile_class_id')
        //     ->select('qlhs_thcd_id', 
        //         'qlhs_thcd_tien_nhucau_MGHP as MGHP', 
        //         'qlhs_thcd_tien_nhucau_CPHT as CPHT', 
        //         'qlhs_thcd_tien_nhucau_HTAT as HTAT', 
        //         'qlhs_thcd_tien_nhucau_HTBT_TA as HTBT_TA', 
        //         'qlhs_thcd_tien_nhucau_HTBT_TO as HTBT_TO', 
        //         'qlhs_thcd_tien_nhucau_HTBT_VHTT as HTBT_VHTT', 
        //         'qlhs_thcd_tien_nhucau_HSDTTS as HSDTTS', 
        //         'qlhs_thcd_tien_nhucau_HSKT_HB as HSKT_HB', 
        //         'qlhs_thcd_tien_nhucau_HSKT_DDHT as HSKT_DDHT', 
        //         'qlhs_thcd_tien_nhucau_HTATHS as HTATHS', 
        //         'qlhs_thcd_tien_nhucau_HBHSDTNT as HBHSDTNT', 
        //         'qlhs_thcd_tongtien_nhucau as TONGTIEN', 
        //         'qlhs_thcd_trangthai as TRANGTHAI',  
        //         'qlhs_thcd_trangthai_PD as TRANGTHAIPHEDUYET', 
        //         'qlhs_thcd_trangthai_TD as TRANGTHAITHAMDINH',  
        //         'qlhs_profile.profile_id', 
        //         'profile_name', 
        //         'profile_birthday', 
        //         'schools_name', 
        //         'class_name', 
        //         'PheDuyet_ghichu as GHICHU');
        //     // ->where('qlhs_thcd_school_id', '=', $schools_id)->where('qlhs_thcd_nam', '=', $arrYear[1])->where('qlhs_thcd_trangthai', '=', 1);

        //     if (!is_null($status) && !empty($status)) {
        //         if ($status == "CHO") {
        //             $datas->where('qlhs_thcd_trangthai_PD', '=', 0);
        //         }
        //         if ($status == "DA") {
        //             $datas->where('qlhs_thcd_trangthai_PD', '=', 1);
        //         }
        //     }
        // }

        // if ($arrYear[0] == "HK2") {
        //     $datas = DB::table('qlhs_tonghopchedo')
        //     ->join('qlhs_profile', 'profile_id', '=', DB::raw('qlhs_thcd_profile_id AND qlhs_thcd_school_id = '.$schools_id.' AND qlhs_thcd_nam = '.$arrYear[1].' AND qlhs_thcd_trangthai_HK2 = 1'))

        //     ->leftJoin('qlhs_schools', 'schools_id', '=', 'profile_school_id')
        //     ->leftJoin('qlhs_class', 'class_id', '=', 'profile_class_id')
        //     ->select('qlhs_thcd_id', 
        //         'qlhs_thcd_tien_nhucau_MGHP_HK2 as MGHP', 
        //         'qlhs_thcd_tien_nhucau_CPHT_HK2 as CPHT', 
        //         'qlhs_thcd_tien_nhucau_HTAT_HK2 as HTAT', 
        //         'qlhs_thcd_tien_nhucau_HTBT_TA_HK2 as HTBT_TA', 
        //         'qlhs_thcd_tien_nhucau_HTBT_TO_HK2 as HTBT_TO', 
        //         'qlhs_thcd_tien_nhucau_HTBT_VHTT_HK2 as HTBT_VHTT', 
        //         'qlhs_thcd_tien_nhucau_HSDTTS_HK2 as HSDTTS', 
        //         'qlhs_thcd_tien_nhucau_HSKT_HB_HK2 as HSKT_HB', 
        //         'qlhs_thcd_tien_nhucau_HSKT_DDHT_HK2 as HSKT_DDHT', 
        //         'qlhs_thcd_tien_nhucau_HTATHS_HK2 as HTATHS', 
        //         'qlhs_thcd_tien_nhucau_HBHSDTNT_HK2 as HBHSDTNT', 
        //         'qlhs_thcd_tongtien_nhucau_HK2 as TONGTIEN', 
        //         'qlhs_thcd_trangthai_HK2 as TRANGTHAI', 
        //         'qlhs_thcd_trangthai_PD_HK2 as TRANGTHAIPHEDUYET', 
        //         'qlhs_thcd_trangthai_TD_HK2 as TRANGTHAITHAMDINH', 
        //         'qlhs_profile.profile_id', 
        //         'profile_name', 
        //         'profile_birthday', 
        //         'schools_name', 
        //         'class_name', 
        //         'PheDuyet_ghichu_HK2 as GHICHU');
        //     // ->where('qlhs_thcd_school_id', '=', $schools_id)->where('qlhs_thcd_nam', '=', $arrYear[1])->where('qlhs_thcd_trangthai_HK2', '=', 1);

        //     if (!is_null($status) && !empty($status)) {
        //         if ($status == "CHO") {
        //             $datas->where('qlhs_thcd_trangthai_PD_HK2', '=', 0);
        //         }
        //         if ($status == "DA") {
        //             $datas->where('qlhs_thcd_trangthai_PD_HK2', '=', 1);
        //         }
        //     }
        // }

        // if ($arrYear[0] == "CA") {
        //     $datas = DB::table('qlhs_tonghopchedo')
        //     ->join('qlhs_profile', 'profile_id', '=', DB::raw('qlhs_thcd_profile_id AND qlhs_thcd_school_id = '.$schools_id.' AND qlhs_thcd_nam = '.$arrYear[1].' AND qlhs_thcd_trangthai = 1 AND qlhs_thcd_trangthai_HK2 = 1'))

        //     ->leftJoin('qlhs_schools', 'schools_id', '=', 'profile_school_id')
        //     ->leftJoin('qlhs_class', 'class_id', '=', 'profile_class_id')
        //     ->select('qlhs_thcd_id', 
        //         DB::raw('CASE 
        //             when qlhs_thcd_tien_nhucau_MGHP is not null and qlhs_thcd_tien_nhucau_MGHP_HK2 is not null 
        //             then (qlhs_thcd_tien_nhucau_MGHP + qlhs_thcd_tien_nhucau_MGHP_HK2) else 0 end MGHP'),
        //         DB::raw('CASE 
        //             when qlhs_thcd_tien_nhucau_CPHT is not null and qlhs_thcd_tien_nhucau_CPHT_HK2 is not null 
        //             then (qlhs_thcd_tien_nhucau_CPHT + qlhs_thcd_tien_nhucau_CPHT_HK2) else 0 end CPHT'),
        //         DB::raw('CASE 
        //             when qlhs_thcd_tien_nhucau_HTAT is not null and qlhs_thcd_tien_nhucau_HTAT_HK2 is not null 
        //             then (qlhs_thcd_tien_nhucau_HTAT + qlhs_thcd_tien_nhucau_HTAT_HK2) else 0 end HTAT'),
        //         DB::raw('CASE 
        //             when qlhs_thcd_tien_nhucau_HTBT_TA is not null and qlhs_thcd_tien_nhucau_HTBT_TA_HK2 is not null 
        //             then (qlhs_thcd_tien_nhucau_HTBT_TA + qlhs_thcd_tien_nhucau_HTBT_TA_HK2) else 0 end HTBT_TA'),
        //         DB::raw('CASE 
        //             when qlhs_thcd_tien_nhucau_HTBT_TO is not null and qlhs_thcd_tien_nhucau_HTBT_TO_HK2 is not null 
        //             then (qlhs_thcd_tien_nhucau_HTBT_TO + qlhs_thcd_tien_nhucau_HTBT_TO_HK2) else 0 end HTBT_TO'),
        //         DB::raw('CASE 
        //             when qlhs_thcd_tien_nhucau_HTBT_VHTT is not null and qlhs_thcd_tien_nhucau_HTBT_VHTT_HK2 is not null 
        //             then (qlhs_thcd_tien_nhucau_HTBT_VHTT + qlhs_thcd_tien_nhucau_HTBT_VHTT_HK2) else 0 end HTBT_VHTT'),
        //         DB::raw('CASE 
        //             when qlhs_thcd_tien_nhucau_HSDTTS is not null and qlhs_thcd_tien_nhucau_HSDTTS_HK2 is not null 
        //             then (qlhs_thcd_tien_nhucau_HSDTTS + qlhs_thcd_tien_nhucau_HSDTTS_HK2) else 0 end HSDTTS'),
        //         DB::raw('CASE 
        //             when qlhs_thcd_tien_nhucau_HSKT_HB is not null and qlhs_thcd_tien_nhucau_HSKT_HB_HK2 is not null 
        //             then (qlhs_thcd_tien_nhucau_HSKT_HB + qlhs_thcd_tien_nhucau_HSKT_HB_HK2) else 0 end HSKT_HB'),
        //         DB::raw('CASE 
        //             when qlhs_thcd_tien_nhucau_HSKT_DDHT is not null and qlhs_thcd_tien_nhucau_HSKT_DDHT_HK2 is not null 
        //             then (qlhs_thcd_tien_nhucau_HSKT_DDHT + qlhs_thcd_tien_nhucau_HSKT_DDHT_HK2) else 0 end HSKT_DDHT'),
        //         DB::raw('CASE 
        //             when qlhs_thcd_tien_nhucau_HTATHS is not null and qlhs_thcd_tien_nhucau_HTATHS_HK2 is not null 
        //             then (qlhs_thcd_tien_nhucau_HTATHS + qlhs_thcd_tien_nhucau_HTATHS_HK2) else 0 end HTATHS'),
        //         DB::raw('CASE 
        //             when qlhs_thcd_tien_nhucau_HBHSDTNT is not null and qlhs_thcd_tien_nhucau_HBHSDTNT_HK2 is not null 
        //             then (qlhs_thcd_tien_nhucau_HBHSDTNT + qlhs_thcd_tien_nhucau_HBHSDTNT_HK2) else 0 end HBHSDTNT'),
        //         DB::raw('CASE 
        //             when qlhs_thcd_tongtien_nhucau is not null and qlhs_thcd_tongtien_nhucau_HK2 is not null 
        //             then (qlhs_thcd_tongtien_nhucau + qlhs_thcd_tongtien_nhucau_HK2) else 0 end TONGTIEN'),
        //         DB::raw('CASE 
        //             when (qlhs_thcd_trangthai is not null and qlhs_thcd_trangthai = 1) and (qlhs_thcd_trangthai_HK2 is not null and qlhs_thcd_trangthai_HK2 = 1)
        //             then 1 else 0 end TRANGTHAI'),
        //         DB::raw('CASE 
        //             when (qlhs_thcd_trangthai_PD is not null and qlhs_thcd_trangthai_PD = 1) and (qlhs_thcd_trangthai_PD_HK2 is not null and qlhs_thcd_trangthai_PD_HK2 = 1)
        //             then 1 else 0 end TRANGTHAIPHEDUYET'),
        //         DB::raw('CASE 
        //             when (qlhs_thcd_trangthai_TD is not null and qlhs_thcd_trangthai_TD = 1) and (qlhs_thcd_trangthai_TD_HK2 is not null and qlhs_thcd_trangthai_TD_HK2 = 1)
        //             then 1 else 0 end TRANGTHAITHAMDINH'),
        //         // '(qlhs_thcd_tien_nhucau_MGHP + qlhs_thcd_tien_nhucau_MGHP_HK2) as MGHP', 
        //         // '(qlhs_thcd_tien_nhucau_CPHT + qlhs_thcd_tien_nhucau_CPHT_HK2) as CPHT', 
        //         // '(qlhs_thcd_tien_nhucau_HTAT + qlhs_thcd_tien_nhucau_HTAT_HK2) as HTAT', 
        //         // '(qlhs_thcd_tien_nhucau_HTBT_TA + qlhs_thcd_tien_nhucau_HTBT_TA_HK2) as HTBT_TA', 
        //         // '(qlhs_thcd_tien_nhucau_HTBT_TO + qlhs_thcd_tien_nhucau_HTBT_TO_HK2) as HTBT_TO', 
        //         // '(qlhs_thcd_tien_nhucau_HTBT_VHTT + qlhs_thcd_tien_nhucau_HTBT_VHTT_HK2) as HTBT_VHTT', 
        //         // '(qlhs_thcd_tien_nhucau_HSDTTS + qlhs_thcd_tien_nhucau_HSDTTS_HK2) as HSDTTS', 
        //         // '(qlhs_thcd_tien_nhucau_HSKT_HB + qlhs_thcd_tien_nhucau_HSKT_HB_HK2) as HSKT_HB', 
        //         // '(qlhs_thcd_tien_nhucau_HSKT_DDHT + qlhs_thcd_tien_nhucau_HSKT_DDHT_HK2) as HSKT_DDHT', 
        //         // '(qlhs_thcd_tongtien_nhucau + qlhs_thcd_tongtien_nhucau_HK2) as TONGTIEN', 
        //         // 'qlhs_thcd_trangthai_HK2 as TRANGTHAI', 
        //         'qlhs_profile.profile_id', 
        //         'profile_name', 
        //         'profile_birthday', 
        //         'schools_name', 
        //         'class_name', 
        //         'PheDuyet_ghichu_CANAM as GHICHU');
        //     // ->where('qlhs_thcd_school_id', '=', $schools_id)
        //     // ->where('qlhs_thcd_nam', '=', $arrYear[1])
        //     // ->where('qlhs_thcd_trangthai', '=', 1)
        //     // ->where('qlhs_thcd_trangthai_HK2', '=', 1);

        //     if (!is_null($status) && !empty($status)) {
        //         if ($status == "CHO") {
        //             $datas->where('qlhs_thcd_trangthai_PD', '=', 0)->where('qlhs_thcd_trangthai_PD_HK2', '=', 0);
        //         }
        //         if ($status == "DA") {
        //             $datas->where('qlhs_thcd_trangthai_PD', '=', 1)->where('qlhs_thcd_trangthai_PD_HK2', '=', 1);
        //         }
        //     }
        // }

        // if (!is_null($datas)) {
        //     if (!is_null($keySearch) && !empty($keySearch)) {
        //         $datas->where('profile_name', 'LIKE', '%'.$keySearch.'%')
        //             ->orWhere('class_name', 'LIKE', '%'.$keySearch.'%');
        //     }

        //     $json['totalRows'] = $datas->count();
        
        //     $json['startRecord'] = ($start);
        //     $json['numRows'] = $limit;
            
        //     $json['data'] = $datas->orderBy('qlhs_profile.updated_at','desc')->skip($start*$limit)->take($limit)->get();
        // }
        
        
        // return $json;
    }

    public function loadListUnApproved(Request $request)
    {
        try {
            $json = [];
            $start = $request->input('start');
            $limit = $request->input('limit');

            $schools_id = $request->input('SCHOOLID');
           // $year = $request->input('YEAR');
            $socongvan = $request->input('SOCONGVAN');
            $keySearch = $request->input('KEY');
            $status = $request->input('STATUS');


            $datas = DB::table('qlhs_profile')
                ->join('qlhs_danhsachphongtralai', 'qlhs_danhsachphongtralai.Profile_id', '=', DB::raw('qlhs_profile.profile_id AND qlhs_danhsachphongtralai.Status = 2 AND qlhs_danhsachphongtralai.Report_name = "'.$socongvan.'" AND qlhs_danhsachphongtralai.School_id = '.$schools_id))
                ->join('qlhs_nationals', 'nationals_id', '=', 'profile_nationals_id')
                ->leftJoin('qlhs_miengiamhocphi', 'id_profile', '=', DB::raw('qlhs_profile.profile_id AND qlhs_miengiamhocphi.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "MGHP" and report_name = "'.$socongvan.'")'))
                ->leftJoin('qlhs_chiphihoctap', 'cpht_profile_id', '=', DB::raw('qlhs_profile.profile_id AND qlhs_chiphihoctap.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "CPHT" and report_name = "'.$socongvan.'")'))
                ->leftJoin('qlhs_hotrotienan', 'htta_profile_id', '=', DB::raw('qlhs_profile.profile_id AND qlhs_hotrotienan.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "HTAT" and report_name = "'.$socongvan.'")'))
                ->leftJoin('qlhs_hotrohocsinhbantru', 'qlhs_hotrohocsinhbantru.profile_id', '=', DB::raw('qlhs_profile.profile_id AND qlhs_hotrohocsinhbantru.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "HTBT" and report_name = "'.$socongvan.'")'))
                ->leftJoin('qlhs_hotrohocsinhkhuyettat', 'qlhs_hotrohocsinhkhuyettat.profile_id', '=', DB::raw('qlhs_profile.profile_id AND qlhs_hotrohocsinhkhuyettat.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "HSKT" and report_name = "'.$socongvan.'")'))
                ->leftJoin('qlhs_hotrohocsinhdantocthieuso AS hsdtts', 'hsdtts.profile_id', '=', DB::raw('qlhs_profile.profile_id AND hsdtts.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "HSDTTS" and report_name = "'.$socongvan.'")'))
                ->leftJoin('qlhs_hotrohocsinhdantocthieuso AS htaths', 'htaths.profile_id', '=', DB::raw('qlhs_profile.profile_id AND htaths.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "HTATHS" and report_name = "'.$socongvan.'")'))
                ->leftJoin('qlhs_hotrohocsinhdantocthieuso AS hbhsdtnt', 'hbhsdtnt.profile_id', '=', DB::raw('qlhs_profile.profile_id AND hbhsdtnt.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "HBHSDTNT" and report_name = "'.$socongvan.'")'))
                ->leftJoin('qlhs_schools', 'schools_id', '=', 'profile_school_id')
                ->leftJoin('qlhs_class', 'class_id', '=', 'profile_class_id')
                ->where('profile_school_id', '=', DB::raw($schools_id.' 
                    AND (qlhs_miengiamhocphi.nhu_cau > 0 OR qlhs_miengiamhocphi.du_toan > 0 
                    OR qlhs_chiphihoctap.nhu_cau > 0 OR qlhs_chiphihoctap.du_toan > 0
                    OR qlhs_hotrotienan.nhu_cau > 0 OR qlhs_hotrotienan.du_toan > 0
                    OR qlhs_hotrohocsinhbantru.tong_nhucau > 0 OR qlhs_hotrohocsinhbantru.tong_dutoan > 0
                    OR qlhs_hotrohocsinhkhuyettat.tong_nhucau > 0 OR qlhs_hotrohocsinhkhuyettat.tong_dutoan > 0
                    OR hsdtts.nhucau > 0 OR hsdtts.dutoan > 0
                    OR htaths.nhucau > 0 OR htaths.dutoan > 0
                    OR hbhsdtnt.nhucau > 0 OR hbhsdtnt.dutoan > 0)'))
                ->select('qlhs_profile.profile_id', 'profile_birthday', 'schools_name', 'profile_name', 'class_name', 'qlhs_danhsachphongtralai.Note', 
                    DB::raw('CASE 
                        when qlhs_miengiamhocphi.nhu_cau is not null or qlhs_miengiamhocphi.du_toan is not null 
                        then (qlhs_miengiamhocphi.nhu_cau + qlhs_miengiamhocphi.du_toan) else 0 end MGHP'),
                    DB::raw('CASE 
                        when qlhs_chiphihoctap.nhu_cau is not null or qlhs_chiphihoctap.du_toan is not null 
                        then (qlhs_chiphihoctap.nhu_cau + qlhs_chiphihoctap.du_toan) else 0 end CPHT'),
                    DB::raw('CASE 
                        when qlhs_hotrotienan.nhu_cau is not null or qlhs_hotrotienan.du_toan is not null 
                        then (qlhs_hotrotienan.nhu_cau + qlhs_hotrotienan.du_toan) else 0 end HTAT'),
                    DB::raw('CASE 
                        when qlhs_hotrohocsinhbantru.nhucau_hotrotienan is not null or qlhs_hotrohocsinhbantru.dutoan_hotrotienan is not null 
                        then (qlhs_hotrohocsinhbantru.nhucau_hotrotienan + qlhs_hotrohocsinhbantru.dutoan_hotrotienan) else 0 end HTBT_TA'),
                    DB::raw('CASE 
                        when qlhs_hotrohocsinhbantru.nhucau_hotrotieno is not null or qlhs_hotrohocsinhbantru.dutoan_hotrotieno is not null 
                        then (qlhs_hotrohocsinhbantru.nhucau_hotrotieno + qlhs_hotrohocsinhbantru.dutoan_hotrotieno) else 0 end HTBT_TO'),
                    DB::raw('CASE 
                        when qlhs_hotrohocsinhbantru.nhucau_VHTT is not null or qlhs_hotrohocsinhbantru.dutoan_VHTT is not null 
                        then (qlhs_hotrohocsinhbantru.nhucau_VHTT + qlhs_hotrohocsinhbantru.dutoan_VHTT) else 0 end HTBT_VHTT'),
                    DB::raw('CASE 
                        when qlhs_hotrohocsinhkhuyettat.nhucau_hocbong is not null or qlhs_hotrohocsinhkhuyettat.dutoan_hocbong is not null 
                        then (qlhs_hotrohocsinhkhuyettat.nhucau_hocbong + qlhs_hotrohocsinhkhuyettat.dutoan_hocbong) else 0 end HSKT_HB'),
                    DB::raw('CASE 
                        when qlhs_hotrohocsinhkhuyettat.nhucau_muadodung is not null or qlhs_hotrohocsinhkhuyettat.dutoan_muadodung is not null 
                        then (qlhs_hotrohocsinhkhuyettat.nhucau_muadodung + qlhs_hotrohocsinhkhuyettat.dutoan_muadodung) else 0 end HSKT_DDHT'),
                    DB::raw('CASE 
                        when hsdtts.nhucau is not null or hsdtts.dutoan is not null 
                        then (hsdtts.nhucau + hsdtts.dutoan) else 0 end HSDTTS'),
                    DB::raw('CASE 
                        when htaths.nhucau is not null or htaths.dutoan is not null 
                        then (htaths.nhucau + htaths.dutoan) else 0 end HTATHS'),
                    DB::raw('CASE 
                        when hbhsdtnt.nhucau is not null or hbhsdtnt.dutoan is not null 
                        then (hbhsdtnt.nhucau + hbhsdtnt.dutoan) else 0 end HBHSDTNT'),
                    // DB::raw('(qlhs_miengiamhocphi.nhu_cau + qlhs_miengiamhocphi.du_toan + qlhs_chiphihoctap.nhu_cau + qlhs_chiphihoctap.du_toan + qlhs_hotrotienan.nhu_cau + qlhs_hotrotienan.du_toan + qlhs_hotrohocsinhbantru.tong_nhucau + qlhs_hotrohocsinhbantru.tong_dutoan + qlhs_hotrohocsinhkhuyettat.tong_nhucau + qlhs_hotrohocsinhkhuyettat.tong_dutoan + hsdtts.nhucau + hsdtts.dutoan + htaths.nhucau + htaths.dutoan + hbhsdtnt.nhucau + hbhsdtnt.dutoan) as TONGTIEN'),
                    DB::raw('(CASE when trangthai_pheduyet_MGHP = 1 or trangthai_pheduyet_CPHT = 1 or trangthai_pheduyet_HTAT = 1 or trangthai_pheduyet_HTBT = 1 or trangthai_pheduyet_HSKT = 1 or hsdtts.trangthai_pheduyet_HSDTTS = 1 or htaths.trangthai_pheduyet_HSDTTS = 1 or hbhsdtnt.trangthai_pheduyet_HSDTTS = 1 then 1 else 0 END) as TRANGTHAIPHEDUYET'),
                    DB::raw('(CASE when trangthai_thamdinh_MGHP = 1 or trangthai_thamdinh_CPHT = 1 or trangthai_thamdinh_HTAT = 1 or trangthai_thamdinh_HTBT = 1 or trangthai_thamdinh_HSKT = 1 or hsdtts.trangthai_thamdinh_HSDTTS = 1 or htaths.trangthai_thamdinh_HSDTTS = 1 or hbhsdtnt.trangthai_thamdinh_HSDTTS = 1 then 1 else 0 END) as TRANGTHAITHAMDINH'));


            if (!is_null($keySearch) && !empty($keySearch)) {
                $datas->where('profile_name', 'LIKE', '%'.$keySearch.'%')
                        ->orWhere('class_name', 'LIKE', '%'.$keySearch.'%');
            }

            $json['totalRows'] = $datas->get()->count();
            
            $json['startRecord'] = ($start);
            $json['numRows'] = $limit;
                
            $json['data'] = $datas->orderBy('qlhs_profile.updated_at','desc')->skip($start*$limit)->take($limit)->get();

            return $json;
        } catch (Exception $e) {
            
        }
    }

    public function approvedchedoPD($objJson){
        $result = [];
        try {

            $obj = json_decode($objJson);
            $profile_id = $obj->{'PROFILEID'};
            $schools_id = $obj->{'SCHOOLID'};
            $socongvan = $obj->{'SOCONGVAN'};
            $arrSubId = $obj->{'ARRSUBJECTID'};
            $note = $obj->{'NOTE'};

            $arrSubjectId = [];
            $arrSubjectId = explode('-', $arrSubId);

            $update = 0;

            $statusMGHP = 0;
            $statusCPHT = 0;
            $statusHTAT = 0;
            $statusHTBT = 0;
            $statusHTBT_TA = 0;
            $statusHTBT_TO = 0;
            $statusHTBT_VHTT = 0;
            $statusHSKT = 0;
            $statusHSKT_HB = 0;
            $statusHSKT_DDHT = 0;
            $statusHSDTTS = 0;
            $statusHTATHS = 0;
            $statusHBHSDTNT = 0;

            $trangthai_PD = 0;

            if ($profile_id > 0 && !empty($socongvan)) {
                
                foreach ($arrSubjectId as $value) {
                    // return $value;
                    //MGHP
                    if ($value == 89 || $value == 90 || $value == 91) {

                        $statusMGHP = 1;
                    }

                    //CPHT
                    if ($value == 92) {

                        $statusCPHT = 1;
                    }

                    //HTAT
                    if ($value == 93) {

                        $statusHTAT = 1;
                    }

                    //HTBT
                    if ($value == 94) {
                        $statusHTBT = 1;
                        $statusHTBT_TA = 1;
                    }

                    if ($value == 98) {
                        $statusHTBT = 1;
                        $statusHTBT_TO = 1;
                    }

                    if ($value == 115) {
                        $statusHTBT = 1;
                        $statusHTBT_VHTT = 1;
                    }

                    //HSKT
                    if ($value == 95) {
                        $statusHSKT = 1;
                        $statusHSKT_HB = 1;
                    }

                    if ($value == 100) {
                        $statusHSKT = 1;
                        $statusHSKT_DDHT = 1;
                    }

                    //HSDTTS
                    if ($value == 99) {

                        $statusHSDTTS = 1;
                    }

                    //HTATHS
                    if ($value == 118) {

                        $statusHTATHS = 1;
                    }

                    //HBHSDTNT
                    if ($value == 119) {

                        $statusHBHSDTNT = 1;
                    }
                }

                if ($statusMGHP == 0 && $statusCPHT == 0 && $statusHTAT == 0 && $statusHTBT_TA == 0 &&  $statusHTBT_TO == 0 &&  $statusHTBT_VHTT == 0 && $statusHSKT_HB == 0 && $statusHSKT_DDHT == 0 && $statusHSDTTS == 0 && $statusHTATHS == 0 && $statusHBHSDTNT == 0) {

                    $result['success'] = 'Hủy phê duyệt thành công';
                }
                else {
                    $trangthai_PD = 1;
                    
                    $result['success'] = 'Phê duyệt thành công';
                }

                $getType = DB::table('qlhs_hosobaocao')->where('report_name', '=', $socongvan)->where('report_id_truong', '=', $schools_id)->select('report_name', 'report_type', 'report')->get();

                $getRevert = DB::table('qlhs_danhsachphongtralai')->where('Profile_id', '=', $profile_id)->where('Report_name', '=', $socongvan)->where('School_id', '=', $schools_id)->where('Status', '=', 2)->get();

                if (!is_null($getType) && !empty($getType) && count($getType) > 0) {
                    foreach ($getType as $valueType) {
                        if ($valueType->report == "MGHP") {
                            $update = DB::table('qlhs_miengiamhocphi')->where('type_code', '=', $valueType->report_type)->where('id_profile', '=', $profile_id)->update(['trangthai_pheduyet_MGHP' => $statusMGHP]);
                        }
                        if ($valueType->report == "CPHT") {
                            $update = DB::table('qlhs_chiphihoctap')->where('type_code', '=', $valueType->report_type)->where('cpht_profile_id', '=', $profile_id)->update(['trangthai_pheduyet_CPHT' => $statusCPHT]);
                        }
                        if ($valueType->report == "HTAT") {
                            $update = DB::table('qlhs_hotrotienan')->where('type_code', '=', $valueType->report_type)->where('htta_profile_id', '=', $profile_id)->update(['trangthai_pheduyet_HTAT' => $statusHTAT]);
                        }
                        if ($valueType->report == "HTBT") {
                            $update = DB::table('qlhs_hotrohocsinhbantru')->where('type_code', '=', $valueType->report_type)->where('profile_id', '=', $profile_id)->update(['trangthai_pheduyet_HTBT' => $statusHTBT]);
                        }
                        if ($valueType->report == "HSKT") {
                            $update = DB::table('qlhs_hotrohocsinhkhuyettat')->where('type_code', '=', $valueType->report_type)->where('profile_id', '=', $profile_id)->update(['trangthai_pheduyet_HSKT' => $statusHSKT]);
                        }
                        if ($valueType->report == "HSDTTS") {
                            $update = DB::table('qlhs_hotrohocsinhdantocthieuso')->where('type_code', '=', $valueType->report_type)->where('profile_id', '=', $profile_id)->update(['trangthai_pheduyet_HSDTTS' => $statusHSDTTS]);
                        }
                        if ($valueType->report == "HTATHS") {
                            $update = DB::table('qlhs_hotrohocsinhdantocthieuso')->where('type_code', '=', $valueType->report_type)->where('profile_id', '=', $profile_id)->update(['trangthai_pheduyet_HSDTTS' => $statusHTATHS]);
                        }
                        if ($valueType->report == "HBHSDTNT") {
                            $update = DB::table('qlhs_hotrohocsinhdantocthieuso')->where('type_code', '=', $valueType->report_type)->where('profile_id', '=', $profile_id)->update(['trangthai_pheduyet_HSDTTS' => $statusHBHSDTNT]);
                        }
                    }
                }

                if ((is_null($getRevert) || empty($getRevert) || count($getRevert) <= 0) && (is_null($arrSubId) || empty($arrSubId))) {
                    $insert = DB::table('qlhs_danhsachphongtralai')->insert([
                        'Profile_id' => $profile_id,
                        'Status' => 2,
                        'Note' => $note,
                        'Report_name' => $socongvan,
                        'School_id' => $schools_id
                        ]);
                    $count_mess = qlhs_message::where('report_name',$socongvan)->where('type',1)->where('status',0)->count();
                    $count = DB::table('qlhs_danhsachphongtralai')->where('Report_name',$socongvan)->where('School_id',$schools_id)->where('Status',2)->select(DB::raw('Count(Report_name) as num'))->groupBy('Report_name')->first();
                    if($count_mess == 0){
                        $mess = new qlhs_message();
                        $mess->type = 1;
                        $mess->message_text = "Công văn số ".$socongvan." có ".$count->num." học sinh trả lại.";
                        $mess->school_id = $schools_id;
                        $mess->report_name = $socongvan;
                        $mess->status = 0;
                        $mess->created_user = Auth::user()->id;
                        $mess->save();
                    }else{
                        $mess = qlhs_message::where('report_name',$socongvan)->where('type',1)->update(['message_text' => "Công văn số ".$socongvan." có ".$count->num." học sinh trả lại.",'status',0]);
                        
                    }
                }
                else if ((!is_null($getRevert) || !empty($getRevert) || count($getRevert) > 0) && (is_null($arrSubId) || empty($arrSubId))) {
                    $update = DB::table('qlhs_danhsachphongtralai')
                    ->where('Profile_id', '=', $profile_id)->where('Report_name', '=', $socongvan)->where('School_id', '=', $schools_id)->where('Status', '=', 2)
                    ->update([
                        'Status' => 2,
                        'Note' => $note
                        ]);
                }

                if (!is_null($arrSubId) && !empty($arrSubId)) {
                    $delete = DB::table('qlhs_danhsachphongtralai')
                    ->where('Profile_id', '=', $profile_id)->where('Report_name', '=', $socongvan)->where('School_id', '=', $schools_id)->where('Status', '=', 2)
                    ->delete();
                    $count_mess = qlhs_message::where('report_name',$socongvan)->where('type',1)->where('status',0)->count();
                    if($count_mess > 0){

                        $count = DB::table('qlhs_danhsachphongtralai')->where('Report_name',$socongvan)->where('School_id',$schools_id)->where('Status',2)->select(DB::raw('Count(Report_name) as num'))->groupBy('Report_name')->first();
                        if(count($count) > 0){
                             $mess = qlhs_message::where('report_name',$socongvan)->where('type',1)->update(['message_text' => "Công văn số ".$socongvan." có ".$count->num." học sinh trả lại.",'status',0]);
                        }else{
                            $mess = qlhs_message::where('report_name',$socongvan)->where('type',1)->update(['status' => 1,'updated_at' => new datetime]);
                        }
                    }
                }
            }

            return $result;
        } catch (Exception $e) {
            return $result['error'] = $e;
        }
    }

    public function approvedAllPheDuyet(Request $request){
        try {
            $result = [];

            $schools_id = $request->input('SCHOOLID');
            $socongvan = $request->input('SOCONGVAN');

            $update = 0;

            $getType = DB::table('qlhs_hosobaocao')->where('report_name', '=', $socongvan)->where('report_id_truong', '=', $schools_id)->select('report_name', 'report_type', 'report')->get();

            if (!is_null($getType) && !empty($getType) && count($getType) > 0) {
                foreach ($getType as $valueType) {
                    if ($valueType->report == "MGHP") {
                        $update = DB::table('qlhs_miengiamhocphi')->where('type_code', '=', $valueType->report_type)->update(['trangthai_pheduyet_MGHP' => 1]);
                    }
                    if ($valueType->report == "CPHT") {
                        $update = DB::table('qlhs_chiphihoctap')->where('type_code', '=', $valueType->report_type)->update(['trangthai_pheduyet_CPHT' => 1]);
                    }
                    if ($valueType->report == "HTAT") {
                        $update = DB::table('qlhs_hotrotienan')->where('type_code', '=', $valueType->report_type)->update(['trangthai_pheduyet_HTAT' => 1]);
                    }
                    if ($valueType->report == "HTBT") {
                        $update = DB::table('qlhs_hotrohocsinhbantru')->where('type_code', '=', $valueType->report_type)->update(['trangthai_pheduyet_HTBT' => 1]);
                    }
                    if ($valueType->report == "HSKT") {
                        $update = DB::table('qlhs_hotrohocsinhkhuyettat')->where('type_code', '=', $valueType->report_type)->update(['trangthai_pheduyet_HSKT' => 1]);
                    }
                    if ($valueType->report == "HSDTTS") {
                        $update = DB::table('qlhs_hotrohocsinhdantocthieuso')->where('type_code', '=', $valueType->report_type)->update(['trangthai_pheduyet_HSDTTS' => 1]);
                    }
                    if ($valueType->report == "HTATHS") {
                        $update = DB::table('qlhs_hotrohocsinhdantocthieuso')->where('type_code', '=', $valueType->report_type)->update(['trangthai_pheduyet_HSDTTS' => 1]);
                    }
                    if ($valueType->report == "HBHSDTNT") {
                        $update = DB::table('qlhs_hotrohocsinhdantocthieuso')->where('type_code', '=', $valueType->report_type)->update(['trangthai_pheduyet_HSDTTS' => 1]);
                    }
                }
            }

            if ($update == 0) {
                $result['error'] = "Toàn bộ học sinh đã được phê duyệt";
            }
            else {
                $result['success'] = "Phê duyệt toàn bộ học sinh thành công";
            }

            return $result;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function unApprovedAllPheDuyet(Request $request){
        try {
            $result = [];

            $schools_id = $request->input('SCHOOLID');
            $socongvan = $request->input('SOCONGVAN');

            $update = 0;

            $getType = DB::table('qlhs_hosobaocao')->where('report_name', '=', $socongvan)->where('report_id_truong', '=', $schools_id)->select('report_name', 'report_type', 'report')->get();

            if (!is_null($getType) && !empty($getType) && count($getType) > 0) {
                foreach ($getType as $valueType) {
                    if ($valueType->report == "MGHP") {
                        $update = DB::table('qlhs_miengiamhocphi')->where('type_code', '=', $valueType->report_type)->update(['trangthai_pheduyet_MGHP' => 0]);
                    }
                    if ($valueType->report == "CPHT") {
                        $update = DB::table('qlhs_chiphihoctap')->where('type_code', '=', $valueType->report_type)->update(['trangthai_pheduyet_CPHT' => 0]);
                    }
                    if ($valueType->report == "HTAT") {
                        $update = DB::table('qlhs_hotrotienan')->where('type_code', '=', $valueType->report_type)->update(['trangthai_pheduyet_HTAT' => 0]);
                    }
                    if ($valueType->report == "HTBT") {
                        $update = DB::table('qlhs_hotrohocsinhbantru')->where('type_code', '=', $valueType->report_type)->update(['trangthai_pheduyet_HTBT' => 0]);
                    }
                    if ($valueType->report == "HSKT") {
                        $update = DB::table('qlhs_hotrohocsinhkhuyettat')->where('type_code', '=', $valueType->report_type)->update(['trangthai_pheduyet_HSKT' => 0]);
                    }
                    if ($valueType->report == "HSDTTS") {
                        $update = DB::table('qlhs_hotrohocsinhdantocthieuso')->where('type_code', '=', $valueType->report_type)->update(['trangthai_pheduyet_HSDTTS' => 0]);
                    }
                    if ($valueType->report == "HTATHS") {
                        $update = DB::table('qlhs_hotrohocsinhdantocthieuso')->where('type_code', '=', $valueType->report_type)->update(['trangthai_pheduyet_HSDTTS' => 0]);
                    }
                    if ($valueType->report == "HBHSDTNT") {
                        $update = DB::table('qlhs_hotrohocsinhdantocthieuso')->where('type_code', '=', $valueType->report_type)->update(['trangthai_pheduyet_HSDTTS' => 0]);
                    }
                }
            }

            if ($update == 0) {
                $result['error'] = "Toàn bộ học sinh đã được hủy phê duyệt";
            }
            else {
                $result['success'] = "Hủy phê duyệt toàn bộ học sinh thành công";
            }

            return $result;
        } catch (Exception $e) {
            return $e;
        }
    }

//---------------------------------------------------------Danh sách học sinh chờ thẩm định-------------------------------------------------------------
    public function getViewApprovedPheDuyet(){
        return view('admin.hoso.duyetdanhsach.listingnew');
    }

    public function loadListApprovedPheduyet(Request $request)
    {
        $json = [];
        $start = $request->input('start');
        $limit = $request->input('limit');

        $schools_id = $request->input('SCHOOLID');
       // $year = $request->input('YEAR');
        $socongvan = $request->input('SOCONGVAN');
        $keySearch = $request->input('KEY');
        $status = $request->input('STATUS');

        // $arrYear = [];
        // $arrYear = explode("-", $year);
        // $schoolId = 37;//$request->input('SCHOOLID');
     //    $year = 2016;//$request->input('YEAR');
     //    $profileId = 834;

        // $user = Auth::user()->id;

        $datas = DB::table('qlhs_profile')
            ->join('qlhs_nationals', 'nationals_id', '=', 'profile_nationals_id')
            ->leftJoin('qlhs_miengiamhocphi', 'id_profile', '=', DB::raw('qlhs_profile.profile_id AND qlhs_miengiamhocphi.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "MGHP" and report_name = "'.$socongvan.'")'))
            ->leftJoin('qlhs_chiphihoctap', 'cpht_profile_id', '=', DB::raw('qlhs_profile.profile_id AND qlhs_chiphihoctap.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "CPHT" and report_name = "'.$socongvan.'")'))
            ->leftJoin('qlhs_hotrotienan', 'htta_profile_id', '=', DB::raw('qlhs_profile.profile_id AND qlhs_hotrotienan.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "HTAT" and report_name = "'.$socongvan.'")'))
            ->leftJoin('qlhs_hotrohocsinhbantru', 'qlhs_hotrohocsinhbantru.profile_id', '=', DB::raw('qlhs_profile.profile_id AND qlhs_hotrohocsinhbantru.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "HTBT" and report_name = "'.$socongvan.'")'))
            ->leftJoin('qlhs_hotrohocsinhkhuyettat', 'qlhs_hotrohocsinhkhuyettat.profile_id', '=', DB::raw('qlhs_profile.profile_id AND qlhs_hotrohocsinhkhuyettat.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "HSKT" and report_name = "'.$socongvan.'")'))
            ->leftJoin('qlhs_hotrohocsinhdantocthieuso AS hsdtts', 'hsdtts.profile_id', '=', DB::raw('qlhs_profile.profile_id AND hsdtts.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "HSDTTS" and report_name = "'.$socongvan.'")'))
            ->leftJoin('qlhs_hotrohocsinhdantocthieuso AS htaths', 'htaths.profile_id', '=', DB::raw('qlhs_profile.profile_id AND htaths.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "HTATHS" and report_name = "'.$socongvan.'")'))
            ->leftJoin('qlhs_hotrohocsinhdantocthieuso AS hbhsdtnt', 'hbhsdtnt.profile_id', '=', DB::raw('qlhs_profile.profile_id AND hbhsdtnt.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "HBHSDTNT" and report_name = "'.$socongvan.'")'))
            ->leftJoin('qlhs_schools', 'schools_id', '=', 'profile_school_id')
            ->leftJoin('qlhs_class', 'class_id', '=', 'profile_class_id')
            ->where('profile_school_id', '=', DB::raw($schools_id.' AND (hbhsdtnt.trangthai_pheduyet_HSDTTS = 1 OR htaths.trangthai_pheduyet_HSDTTS = 1 OR hsdtts.trangthai_pheduyet_HSDTTS = 1 OR trangthai_pheduyet_HSKT = 1 OR trangthai_pheduyet_HTBT = 1 OR trangthai_pheduyet_HTAT = 1 OR trangthai_pheduyet_CPHT = 1 OR trangthai_pheduyet_MGHP = 1)'))
            ->select('qlhs_profile.profile_id', 'profile_birthday', 'schools_name', 'profile_name', 'class_name', 
                DB::raw('CASE 
                    when qlhs_miengiamhocphi.nhu_cau is not null or qlhs_miengiamhocphi.du_toan is not null 
                    then (qlhs_miengiamhocphi.nhu_cau + qlhs_miengiamhocphi.du_toan) else 0 end MGHP'),
                DB::raw('CASE 
                    when qlhs_chiphihoctap.nhu_cau is not null or qlhs_chiphihoctap.du_toan is not null 
                    then (qlhs_chiphihoctap.nhu_cau + qlhs_chiphihoctap.du_toan) else 0 end CPHT'),
                DB::raw('CASE 
                    when qlhs_hotrotienan.nhu_cau is not null or qlhs_hotrotienan.du_toan is not null 
                    then (qlhs_hotrotienan.nhu_cau + qlhs_hotrotienan.du_toan) else 0 end HTAT'),
                DB::raw('CASE 
                    when qlhs_hotrohocsinhbantru.nhucau_hotrotienan is not null or qlhs_hotrohocsinhbantru.dutoan_hotrotienan is not null 
                    then (qlhs_hotrohocsinhbantru.nhucau_hotrotienan + qlhs_hotrohocsinhbantru.dutoan_hotrotienan) else 0 end HTBT_TA'),
                DB::raw('CASE 
                    when qlhs_hotrohocsinhbantru.nhucau_hotrotieno is not null or qlhs_hotrohocsinhbantru.dutoan_hotrotieno is not null 
                    then (qlhs_hotrohocsinhbantru.nhucau_hotrotieno + qlhs_hotrohocsinhbantru.dutoan_hotrotieno) else 0 end HTBT_TO'),
                DB::raw('CASE 
                    when qlhs_hotrohocsinhbantru.nhucau_VHTT is not null or qlhs_hotrohocsinhbantru.dutoan_VHTT is not null 
                    then (qlhs_hotrohocsinhbantru.nhucau_VHTT + qlhs_hotrohocsinhbantru.dutoan_VHTT) else 0 end HTBT_VHTT'),
                DB::raw('CASE 
                    when qlhs_hotrohocsinhkhuyettat.nhucau_hocbong is not null or qlhs_hotrohocsinhkhuyettat.dutoan_hocbong is not null 
                    then (qlhs_hotrohocsinhkhuyettat.nhucau_hocbong + qlhs_hotrohocsinhkhuyettat.dutoan_hocbong) else 0 end HSKT_HB'),
                DB::raw('CASE 
                    when qlhs_hotrohocsinhkhuyettat.nhucau_muadodung is not null or qlhs_hotrohocsinhkhuyettat.dutoan_muadodung is not null 
                    then (qlhs_hotrohocsinhkhuyettat.nhucau_muadodung + qlhs_hotrohocsinhkhuyettat.dutoan_muadodung) else 0 end HSKT_DDHT'),
                DB::raw('CASE 
                    when hsdtts.nhucau is not null or hsdtts.dutoan is not null 
                    then (hsdtts.nhucau + hsdtts.dutoan) else 0 end HSDTTS'),
                DB::raw('CASE 
                    when htaths.nhucau is not null or htaths.dutoan is not null 
                    then (htaths.nhucau + htaths.dutoan) else 0 end HTATHS'),
                DB::raw('CASE 
                    when hbhsdtnt.nhucau is not null or hbhsdtnt.dutoan is not null 
                    then (hbhsdtnt.nhucau + hbhsdtnt.dutoan) else 0 end HBHSDTNT'),
                // DB::raw('(qlhs_miengiamhocphi.nhu_cau + qlhs_miengiamhocphi.du_toan + qlhs_chiphihoctap.nhu_cau + qlhs_chiphihoctap.du_toan + qlhs_hotrotienan.nhu_cau + qlhs_hotrotienan.du_toan + qlhs_hotrohocsinhbantru.tong_nhucau + qlhs_hotrohocsinhbantru.tong_dutoan + qlhs_hotrohocsinhkhuyettat.tong_nhucau + qlhs_hotrohocsinhkhuyettat.tong_dutoan + hsdtts.nhucau + hsdtts.dutoan + htaths.nhucau + htaths.dutoan + hbhsdtnt.nhucau + hbhsdtnt.dutoan) as TONGTIEN'),
                DB::raw('(CASE when trangthai_pheduyet_MGHP = 1 or trangthai_pheduyet_CPHT = 1 or trangthai_pheduyet_HTAT = 1 or trangthai_pheduyet_HTBT = 1 or trangthai_pheduyet_HSKT = 1 or hsdtts.trangthai_pheduyet_HSDTTS = 1 or htaths.trangthai_pheduyet_HSDTTS = 1 or hbhsdtnt.trangthai_pheduyet_HSDTTS = 1 then 1 else 0 END) as TRANGTHAIPHEDUYET'),
                DB::raw('(CASE when trangthai_thamdinh_MGHP = 1 or trangthai_thamdinh_CPHT = 1 or trangthai_thamdinh_HTAT = 1 or trangthai_thamdinh_HTBT = 1 or trangthai_thamdinh_HSKT = 1 or hsdtts.trangthai_thamdinh_HSDTTS = 1 or htaths.trangthai_thamdinh_HSDTTS = 1 or hbhsdtnt.trangthai_thamdinh_HSDTTS = 1 then 1 else 0 END) as TRANGTHAITHAMDINH'));

        if (!is_null($status) && !empty($status)) {
            if ($status == "CHO") {
                $datas->where('trangthai_thamdinh_MGHP', '=', DB::raw('0 AND trangthai_thamdinh_CPHT = 0 AND trangthai_thamdinh_HTAT = 0 AND trangthai_thamdinh_HTBT = 0 AND trangthai_thamdinh_HSKT = 0 AND hsdtts.trangthai_thamdinh_HSDTTS = 0 AND htaths.trangthai_thamdinh_HSDTTS = 0 AND hbhsdtnt.trangthai_thamdinh_HSDTTS = 0'));
            }
            if ($status == "DA") {
                $datas->where('trangthai_thamdinh_MGHP', '=', DB::raw('1 or trangthai_thamdinh_CPHT = 1 or trangthai_thamdinh_HTAT = 1 or trangthai_thamdinh_HTBT = 1 or trangthai_thamdinh_HSKT = 1 or hsdtts.trangthai_thamdinh_HSDTTS = 1 or htaths.trangthai_thamdinh_HSDTTS = 1 or hbhsdtnt.trangthai_thamdinh_HSDTTS = 1'));
            }
        }
        
        if (!is_null($keySearch) && !empty($keySearch)) {
            $datas->where('profile_name', 'LIKE', '%'.$keySearch.'%')
                    ->orWhere('class_name', 'LIKE', '%'.$keySearch.'%');
        }

        $json['totalRows'] = $datas->get()->count();
        
        $json['startRecord'] = ($start);
        $json['numRows'] = $limit;
            
        $json['data'] = $datas->orderBy('qlhs_profile.updated_at','desc')->skip($start*$limit)->take($limit)->get();

        return $json;
    }

    public function approvedchedoTD($objJson){
        $result = [];
        try {

            $obj = json_decode($objJson);
            $profile_id = $obj->{'PROFILEID'};
            $schools_id = $obj->{'SCHOOLID'};
            $socongvan = $obj->{'SOCONGVAN'};
            $arrSubId = $obj->{'ARRSUBJECTID'};

            $note = $obj->{'NOTE'};

            $arrSubjectId = [];
            $arrSubjectId = explode('-', $arrSubId);

            $update = 0;

            $statusMGHP = 0;
            $statusCPHT = 0;
            $statusHTAT = 0;
            $statusHTBT = 0;
            $statusHTBT_TA = 0;
            $statusHTBT_TO = 0;
            $statusHTBT_VHTT = 0;
            $statusHSKT = 0;
            $statusHSKT_HB = 0;
            $statusHSKT_DDHT = 0;
            $statusHSDTTS = 0;
            $statusHTATHS = 0;
            $statusHBHSDTNT = 0;

            $trangthai_PD = 0;

            if ($profile_id > 0 && !empty($socongvan)) {
                
                foreach ($arrSubjectId as $value) {
                    // return $value;
                    //MGHP
                    if ($value == 89 || $value == 90 || $value == 91) {

                        $statusMGHP = 1;
                    }

                    //CPHT
                    if ($value == 92) {

                        $statusCPHT = 1;
                    }

                    //HTAT
                    if ($value == 93) {

                        $statusHTAT = 1;
                    }

                    //HTBT
                    if ($value == 94) {
                        $statusHTBT = 1;
                        $statusHTBT_TA = 1;
                    }

                    if ($value == 98) {
                        $statusHTBT = 1;
                        $statusHTBT_TO = 1;
                    }

                    if ($value == 115) {
                        $statusHTBT = 1;
                        $statusHTBT_VHTT = 1;
                    }

                    //HSKT
                    if ($value == 95) {
                        $statusHSKT = 1;
                        $statusHSKT_HB = 1;
                    }

                    if ($value == 100) {
                        $statusHSKT = 1;
                        $statusHSKT_DDHT = 1;
                    }

                    //HSDTTS
                    if ($value == 99) {

                        $statusHSDTTS = 1;
                    }

                    //HTATHS
                    if ($value == 118) {

                        $statusHTATHS = 1;
                    }

                    //HBHSDTNT
                    if ($value == 119) {

                        $statusHBHSDTNT = 1;
                    }
                }

                if ($statusMGHP == 0 && $statusCPHT == 0 && $statusHTAT == 0 && $statusHTBT_TA == 0 &&  $statusHTBT_TO == 0 &&  $statusHTBT_VHTT == 0 && $statusHSKT_HB == 0 && $statusHSKT_DDHT == 0 && $statusHSDTTS == 0 && $statusHTATHS == 0 && $statusHBHSDTNT == 0) {
                    
                    $result['success'] = 'Hủy thẩm định thành công';
                }
                else {
                    $trangthai_PD = 1;
                    
                    $result['success'] = 'Thẩm định thành công';
                }

                $getType = DB::table('qlhs_hosobaocao')->where('report_name', '=', $socongvan)->where('report_id_truong', '=', $schools_id)->select('report_name', 'report_type', 'report')->get();

                $getRevert = DB::table('qlhs_danhsachphongtralai')->where('Profile_id', '=', $profile_id)->where('Report_name', '=', $socongvan)->where('School_id', '=', $schools_id)->where('Status', '=', 3)->get();

                if (!is_null($getType) && !empty($getType) && count($getType) > 0) {
                    foreach ($getType as $valueType) {
                        if ($valueType->report == "MGHP") {
                            $update = DB::table('qlhs_miengiamhocphi')->where('type_code', '=', $valueType->report_type)->where('id_profile', '=', $profile_id)->update(['trangthai_thamdinh_MGHP' => $statusMGHP]);
                        }
                        if ($valueType->report == "CPHT") {
                            $update = DB::table('qlhs_chiphihoctap')->where('type_code', '=', $valueType->report_type)->where('cpht_profile_id', '=', $profile_id)->update(['trangthai_thamdinh_CPHT' => $statusCPHT]);
                        }
                        if ($valueType->report == "HTAT") {
                            $update = DB::table('qlhs_hotrotienan')->where('type_code', '=', $valueType->report_type)->where('htta_profile_id', '=', $profile_id)->update(['trangthai_thamdinh_HTAT' => $statusHTAT]);
                        }
                        if ($valueType->report == "HTBT") {
                            $update = DB::table('qlhs_hotrohocsinhbantru')->where('type_code', '=', $valueType->report_type)->where('profile_id', '=', $profile_id)->update(['trangthai_thamdinh_HTBT' => $statusHTBT]);
                        }
                        if ($valueType->report == "HSKT") {
                            $update = DB::table('qlhs_hotrohocsinhkhuyettat')->where('type_code', '=', $valueType->report_type)->where('profile_id', '=', $profile_id)->update(['trangthai_thamdinh_HSKT' => $statusHSKT]);
                        }
                        if ($valueType->report == "HSDTTS") {
                            $update = DB::table('qlhs_hotrohocsinhdantocthieuso')->where('type_code', '=', $valueType->report_type)->where('profile_id', '=', $profile_id)->update(['trangthai_thamdinh_HSDTTS' => $statusHSDTTS]);
                        }
                        if ($valueType->report == "HTATHS") {
                            $update = DB::table('qlhs_hotrohocsinhdantocthieuso')->where('type_code', '=', $valueType->report_type)->where('profile_id', '=', $profile_id)->update(['trangthai_thamdinh_HSDTTS' => $statusHTATHS]);
                        }
                        if ($valueType->report == "HBHSDTNT") {
                            $update = DB::table('qlhs_hotrohocsinhdantocthieuso')->where('type_code', '=', $valueType->report_type)->where('profile_id', '=', $profile_id)->update(['trangthai_thamdinh_HSDTTS' => $statusHBHSDTNT]);
                        }
                    }
                }


                if ((is_null($getRevert) || empty($getRevert) || count($getRevert) <= 0) && (is_null($arrSubId) || empty($arrSubId))) {
                    $insert = DB::table('qlhs_danhsachphongtralai')->insert([
                        'Profile_id' => $profile_id,
                        'Status' => 3,
                        'Note' => $note,
                        'Report_name' => $socongvan,
                        'School_id' => $schools_id
                        ]);
                }
                else if ((!is_null($getRevert) || !empty($getRevert) || count($getRevert) > 0) && (is_null($arrSubId) || empty($arrSubId))) {
                    $update = DB::table('qlhs_danhsachphongtralai')
                    ->where('Profile_id', '=', $profile_id)->where('Report_name', '=', $socongvan)->where('School_id', '=', $schools_id)->where('Status', '=', 3)
                    ->update([
                        'Status' => 3,
                        'Note' => $note
                        ]);
                }

                if (!is_null($arrSubId) && !empty($arrSubId)) {
                    $delete = DB::table('qlhs_danhsachphongtralai')
                    ->where('Profile_id', '=', $profile_id)->where('Report_name', '=', $socongvan)->where('School_id', '=', $schools_id)->where('Status', '=', 3)
                    ->delete();
                }
            }

            return $result;
        } catch (Exception $e) {
            return $result['error'] = $e;
        }
    }

    
    public function loadListUnApprovedThamDinh(Request $request)
    {
        try {
            $json = [];
            $start = $request->input('start');
            $limit = $request->input('limit');

            $schools_id = $request->input('SCHOOLID');
           // $year = $request->input('YEAR');
            $socongvan = $request->input('SOCONGVAN');
            $keySearch = $request->input('KEY');

            // $arrYear = [];
            // $arrYear = explode("-", $year);
            // $schoolId = 37;//$request->input('SCHOOLID');
         //    $year = 2016;//$request->input('YEAR');
         //    $profileId = 834;

            // $user = Auth::user()->id;

            $datas = DB::table('qlhs_profile')
                ->join('qlhs_danhsachphongtralai', 'qlhs_danhsachphongtralai.Profile_id', '=', DB::raw('qlhs_profile.profile_id AND qlhs_danhsachphongtralai.Status = 3 AND qlhs_danhsachphongtralai.Report_name = "'.$socongvan.'" AND qlhs_danhsachphongtralai.School_id = '.$schools_id))
                ->join('qlhs_nationals', 'nationals_id', '=', 'profile_nationals_id')
                ->leftJoin('qlhs_miengiamhocphi', 'id_profile', '=', DB::raw('qlhs_profile.profile_id AND qlhs_miengiamhocphi.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "MGHP" and report_name = "'.$socongvan.'")'))
                ->leftJoin('qlhs_chiphihoctap', 'cpht_profile_id', '=', DB::raw('qlhs_profile.profile_id AND qlhs_chiphihoctap.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "CPHT" and report_name = "'.$socongvan.'")'))
                ->leftJoin('qlhs_hotrotienan', 'htta_profile_id', '=', DB::raw('qlhs_profile.profile_id AND qlhs_hotrotienan.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "HTAT" and report_name = "'.$socongvan.'")'))
                ->leftJoin('qlhs_hotrohocsinhbantru', 'qlhs_hotrohocsinhbantru.profile_id', '=', DB::raw('qlhs_profile.profile_id AND qlhs_hotrohocsinhbantru.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "HTBT" and report_name = "'.$socongvan.'")'))
                ->leftJoin('qlhs_hotrohocsinhkhuyettat', 'qlhs_hotrohocsinhkhuyettat.profile_id', '=', DB::raw('qlhs_profile.profile_id AND qlhs_hotrohocsinhkhuyettat.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "HSKT" and report_name = "'.$socongvan.'")'))
                ->leftJoin('qlhs_hotrohocsinhdantocthieuso AS hsdtts', 'hsdtts.profile_id', '=', DB::raw('qlhs_profile.profile_id AND hsdtts.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "HSDTTS" and report_name = "'.$socongvan.'")'))
                ->leftJoin('qlhs_hotrohocsinhdantocthieuso AS htaths', 'htaths.profile_id', '=', DB::raw('qlhs_profile.profile_id AND htaths.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "HTATHS" and report_name = "'.$socongvan.'")'))
                ->leftJoin('qlhs_hotrohocsinhdantocthieuso AS hbhsdtnt', 'hbhsdtnt.profile_id', '=', DB::raw('qlhs_profile.profile_id AND hbhsdtnt.type_code = (SELECT report_type FROM qlhs_hosobaocao WHERE report = "HBHSDTNT" and report_name = "'.$socongvan.'")'))
                ->leftJoin('qlhs_schools', 'schools_id', '=', 'profile_school_id')
                ->leftJoin('qlhs_class', 'class_id', '=', 'profile_class_id')
                ->where('profile_school_id', '=', DB::raw($schools_id.' AND (hbhsdtnt.trangthai_pheduyet_HSDTTS = 1 OR htaths.trangthai_pheduyet_HSDTTS = 1 OR hsdtts.trangthai_pheduyet_HSDTTS = 1 OR trangthai_pheduyet_HSKT = 1 OR trangthai_pheduyet_HTBT = 1 OR trangthai_pheduyet_HTAT = 1 OR trangthai_pheduyet_CPHT = 1 OR trangthai_pheduyet_MGHP = 1)'))
                ->select('qlhs_profile.profile_id', 'profile_birthday', 'schools_name', 'profile_name', 'class_name', 'qlhs_danhsachphongtralai.Note', 
                    DB::raw('CASE 
                        when qlhs_miengiamhocphi.nhu_cau is not null or qlhs_miengiamhocphi.du_toan is not null 
                        then (qlhs_miengiamhocphi.nhu_cau + qlhs_miengiamhocphi.du_toan) else 0 end MGHP'),
                    DB::raw('CASE 
                        when qlhs_chiphihoctap.nhu_cau is not null or qlhs_chiphihoctap.du_toan is not null 
                        then (qlhs_chiphihoctap.nhu_cau + qlhs_chiphihoctap.du_toan) else 0 end CPHT'),
                    DB::raw('CASE 
                        when qlhs_hotrotienan.nhu_cau is not null or qlhs_hotrotienan.du_toan is not null 
                        then (qlhs_hotrotienan.nhu_cau + qlhs_hotrotienan.du_toan) else 0 end HTAT'),
                    DB::raw('CASE 
                        when qlhs_hotrohocsinhbantru.nhucau_hotrotienan is not null or qlhs_hotrohocsinhbantru.dutoan_hotrotienan is not null 
                        then (qlhs_hotrohocsinhbantru.nhucau_hotrotienan + qlhs_hotrohocsinhbantru.dutoan_hotrotienan) else 0 end HTBT_TA'),
                    DB::raw('CASE 
                        when qlhs_hotrohocsinhbantru.nhucau_hotrotieno is not null or qlhs_hotrohocsinhbantru.dutoan_hotrotieno is not null 
                        then (qlhs_hotrohocsinhbantru.nhucau_hotrotieno + qlhs_hotrohocsinhbantru.dutoan_hotrotieno) else 0 end HTBT_TO'),
                    DB::raw('CASE 
                        when qlhs_hotrohocsinhbantru.nhucau_VHTT is not null or qlhs_hotrohocsinhbantru.dutoan_VHTT is not null 
                        then (qlhs_hotrohocsinhbantru.nhucau_VHTT + qlhs_hotrohocsinhbantru.dutoan_VHTT) else 0 end HTBT_VHTT'),
                    DB::raw('CASE 
                        when qlhs_hotrohocsinhkhuyettat.nhucau_hocbong is not null or qlhs_hotrohocsinhkhuyettat.dutoan_hocbong is not null 
                        then (qlhs_hotrohocsinhkhuyettat.nhucau_hocbong + qlhs_hotrohocsinhkhuyettat.dutoan_hocbong) else 0 end HSKT_HB'),
                    DB::raw('CASE 
                        when qlhs_hotrohocsinhkhuyettat.nhucau_muadodung is not null or qlhs_hotrohocsinhkhuyettat.dutoan_muadodung is not null 
                        then (qlhs_hotrohocsinhkhuyettat.nhucau_muadodung + qlhs_hotrohocsinhkhuyettat.dutoan_muadodung) else 0 end HSKT_DDHT'),
                    DB::raw('CASE 
                        when hsdtts.nhucau is not null or hsdtts.dutoan is not null 
                        then (hsdtts.nhucau + hsdtts.dutoan) else 0 end HSDTTS'),
                    DB::raw('CASE 
                        when htaths.nhucau is not null or htaths.dutoan is not null 
                        then (htaths.nhucau + htaths.dutoan) else 0 end HTATHS'),
                    DB::raw('CASE 
                        when hbhsdtnt.nhucau is not null or hbhsdtnt.dutoan is not null 
                        then (hbhsdtnt.nhucau + hbhsdtnt.dutoan) else 0 end HBHSDTNT'),
                    // DB::raw('(qlhs_miengiamhocphi.nhu_cau + qlhs_miengiamhocphi.du_toan + qlhs_chiphihoctap.nhu_cau + qlhs_chiphihoctap.du_toan + qlhs_hotrotienan.nhu_cau + qlhs_hotrotienan.du_toan + qlhs_hotrohocsinhbantru.tong_nhucau + qlhs_hotrohocsinhbantru.tong_dutoan + qlhs_hotrohocsinhkhuyettat.tong_nhucau + qlhs_hotrohocsinhkhuyettat.tong_dutoan + hsdtts.nhucau + hsdtts.dutoan + htaths.nhucau + htaths.dutoan + hbhsdtnt.nhucau + hbhsdtnt.dutoan) as TONGTIEN'),
                    DB::raw('(CASE when trangthai_pheduyet_MGHP = 1 or trangthai_pheduyet_CPHT = 1 or trangthai_pheduyet_HTAT = 1 or trangthai_pheduyet_HTBT = 1 or trangthai_pheduyet_HSKT = 1 or hsdtts.trangthai_pheduyet_HSDTTS = 1 or htaths.trangthai_pheduyet_HSDTTS = 1 or hbhsdtnt.trangthai_pheduyet_HSDTTS = 1 then 1 else 0 END) as TRANGTHAIPHEDUYET'),
                    DB::raw('(CASE when trangthai_thamdinh_MGHP = 1 or trangthai_thamdinh_CPHT = 1 or trangthai_thamdinh_HTAT = 1 or trangthai_thamdinh_HTBT = 1 or trangthai_thamdinh_HSKT = 1 or hsdtts.trangthai_thamdinh_HSDTTS = 1 or htaths.trangthai_thamdinh_HSDTTS = 1 or hbhsdtnt.trangthai_thamdinh_HSDTTS = 1 then 1 else 0 END) as TRANGTHAITHAMDINH'));

            
            if (!is_null($keySearch) && !empty($keySearch)) {
                $datas->where('profile_name', 'LIKE', '%'.$keySearch.'%')
                        ->orWhere('class_name', 'LIKE', '%'.$keySearch.'%');
            }

            $json['totalRows'] = $datas->get()->count();
            
            $json['startRecord'] = ($start);
            $json['numRows'] = $limit;
                
            $json['data'] = $datas->orderBy('qlhs_profile.updated_at','desc')->skip($start*$limit)->take($limit)->get();

            return $json;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function approvedAllThamDinh(Request $request){
        try {
            $result = [];

            $schools_id = $request->input('SCHOOLID');
            $socongvan = $request->input('SOCONGVAN');

            $update = 0;

            $getType = DB::table('qlhs_hosobaocao')->where('report_name', '=', $socongvan)->where('report_id_truong', '=', $schools_id)->select('report_name', 'report_type', 'report')->get();

            if (!is_null($getType) && !empty($getType) && count($getType) > 0) {
                foreach ($getType as $valueType) {
                    if ($valueType->report == "MGHP") {
                        $update = DB::table('qlhs_miengiamhocphi')->where('type_code', '=', $valueType->report_type)->update(['trangthai_thamdinh_MGHP' => 1]);
                    }
                    if ($valueType->report == "CPHT") {
                        $update = DB::table('qlhs_chiphihoctap')->where('type_code', '=', $valueType->report_type)->update(['trangthai_thamdinh_CPHT' => 1]);
                    }
                    if ($valueType->report == "HTAT") {
                        $update = DB::table('qlhs_hotrotienan')->where('type_code', '=', $valueType->report_type)->update(['trangthai_thamdinh_HTAT' => 1]);
                    }
                    if ($valueType->report == "HTBT") {
                        $update = DB::table('qlhs_hotrohocsinhbantru')->where('type_code', '=', $valueType->report_type)->update(['trangthai_thamdinh_HTBT' => 1]);
                    }
                    if ($valueType->report == "HSKT") {
                        $update = DB::table('qlhs_hotrohocsinhkhuyettat')->where('type_code', '=', $valueType->report_type)->update(['trangthai_thamdinh_HSKT' => 1]);
                    }
                    if ($valueType->report == "HSDTTS") {
                        $update = DB::table('qlhs_hotrohocsinhdantocthieuso')->where('type_code', '=', $valueType->report_type)->update(['trangthai_thamdinh_HSDTTS' => 1]);
                    }
                    if ($valueType->report == "HTATHS") {
                        $update = DB::table('qlhs_hotrohocsinhdantocthieuso')->where('type_code', '=', $valueType->report_type)->update(['trangthai_thamdinh_HSDTTS' => 1]);
                    }
                    if ($valueType->report == "HBHSDTNT") {
                        $update = DB::table('qlhs_hotrohocsinhdantocthieuso')->where('type_code', '=', $valueType->report_type)->update(['trangthai_thamdinh_HSDTTS' => 1]);
                    }
                }
            }

            if ($update == 0) {
                $result['error'] = "Toàn bộ học sinh đã được thẩm định";
            }
            else {
                $result['success'] = "Thẩm định toàn bộ học sinh thành công";
            }

            return $result;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function unApprovedAllThamDinh(Request $request){
        try {
            $result = [];

            $schools_id = $request->input('SCHOOLID');
            $socongvan = $request->input('SOCONGVAN');

            $update = 0;

            $getType = DB::table('qlhs_hosobaocao')->where('report_name', '=', $socongvan)->where('report_id_truong', '=', $schools_id)->select('report_name', 'report_type', 'report')->get();

            if (!is_null($getType) && !empty($getType) && count($getType) > 0) {
                foreach ($getType as $valueType) {
                    if ($valueType->report == "MGHP") {
                        $update = DB::table('qlhs_miengiamhocphi')->where('type_code', '=', $valueType->report_type)->update(['trangthai_thamdinh_MGHP' => 0]);
                    }
                    if ($valueType->report == "CPHT") {
                        $update = DB::table('qlhs_chiphihoctap')->where('type_code', '=', $valueType->report_type)->update(['trangthai_thamdinh_CPHT' => 0]);
                    }
                    if ($valueType->report == "HTAT") {
                        $update = DB::table('qlhs_hotrotienan')->where('type_code', '=', $valueType->report_type)->update(['trangthai_thamdinh_HTAT' => 0]);
                    }
                    if ($valueType->report == "HTBT") {
                        $update = DB::table('qlhs_hotrohocsinhbantru')->where('type_code', '=', $valueType->report_type)->update(['trangthai_thamdinh_HTBT' => 0]);
                    }
                    if ($valueType->report == "HSKT") {
                        $update = DB::table('qlhs_hotrohocsinhkhuyettat')->where('type_code', '=', $valueType->report_type)->update(['trangthai_thamdinh_HSKT' => 0]);
                    }
                    if ($valueType->report == "HSDTTS") {
                        $update = DB::table('qlhs_hotrohocsinhdantocthieuso')->where('type_code', '=', $valueType->report_type)->update(['trangthai_thamdinh_HSDTTS' => 0]);
                    }
                    if ($valueType->report == "HTATHS") {
                        $update = DB::table('qlhs_hotrohocsinhdantocthieuso')->where('type_code', '=', $valueType->report_type)->update(['trangthai_thamdinh_HSDTTS' => 0]);
                    }
                    if ($valueType->report == "HBHSDTNT") {
                        $update = DB::table('qlhs_hotrohocsinhdantocthieuso')->where('type_code', '=', $valueType->report_type)->update(['trangthai_thamdinh_HSDTTS' => 0]);
                    }
                }
            }

            if ($update == 0) {
                $result['error'] = "Toàn bộ học sinh đã được hủy thẩm định";
            }
            else {
                $result['success'] = "Hủy thẩm định toàn bộ học sinh thành công";
            }

            return $result;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function downloadfile_ExportHTATHS($id){
        $data = DB::table('qlhs_hosobaocao')->where('report','=','HTATHS')->where('report_id','=',$id)->select('report_type')->first(); 
        $dir = storage_path().'/exceldownload/HTATHS/'.$data->report_type.'.xlsx';
        return response()->download($dir, $data->report_type.'.xlsx');
    }

    public function downloadfile_ExportHBHSDTNT($id){
        $data = DB::table('qlhs_hosobaocao')->where('report','=','HBHSDTNT')->where('report_id','=',$id)->select('report_type')->first(); 
        $dir = storage_path().'/exceldownload/HBHSDTNT/'.$data->report_type.'.xlsx';
        return response()->download($dir, $data->report_type.'.xlsx');
    }

//---------------------------------------------------------Danh sách đã lập đề nghị----------------------------------------------------------------------
    public function loadDataReport(){
        try {
            $result = [];

            $currentuser_id = Auth::user()->id;
            $currentdate = Carbon::now('Asia/Ho_Chi_Minh');

            $getIDTruong = DB::table('users')->select('truong_id')->where('id', '=', $currentuser_id)->first();

            $result['SCHOOL'] = DB::table('qlhs_schools')->select('schools_id', 'schools_name')->get();

            $result['REPORT'] = DB::table('qlhs_hosobaocao')->select('report_name', 'report_id_truong')->orderBy('report_date', 'desc')->groupBy('report_name', 'report_id_truong')->get();

            if ($getIDTruong->truong_id > 0) {
                $result['REPORT'] = DB::table('qlhs_hosobaocao')->where('report_id_truong', '=', $getIDTruong->truong_id)->select('report_name', 'report_id_truong')->orderBy('report_date', 'desc')->groupBy('report_name', 'report_id_truong')->get();

                $result['SCHOOL'] = DB::table('qlhs_schools')->where('schools_id', '=', $getIDTruong->truong_id)->select('schools_id', 'schools_name')->get();
            }
            // $getData->select('report_name')->groupBy('report_name')->toSql();

            return $result;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function loadDataReportBySchool($id){
        try {

            $getData = DB::table('qlhs_hosobaocao')->where('report_id_truong', '=', $id)->select('report_name')->orderBy('report_date', 'asc')->groupBy('report_name')->get();

            return $getData;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function loadDataReportType($objData){
        try {

            $getData = DB::table('qlhs_hosobaocao')->where('report_name', '=', $objData)->select('report')->orderBy('report', 'asc')->get();

            return $getData;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function loadDatabyGroupA(Request $request){
        try {
            $result = [];

            $reportName = $request->input('REPORTNAME');
            $reportType = $request->input('REPORTTYPE');

            $start = $request->input('start');
            $limit = $request->input('limit');
            $keySearch = $request->input('KEY');
            $groupHS = $request->input('GROUP');

            $getReport = DB::table('qlhs_hosobaocao')
                ->where('report_name', '=', $reportName)
                ->where('report', '=', $reportType)
                ->select('report_type', 'report_year')->first();

            if (!is_null($getReport) && !empty($getReport) && $getReport->report_year > 0) {
                
                if ($reportType == "MGHP") {
                    $getData = DB::table('qlhs_miengiamhocphi')
                        ->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_miengiamhocphi.id_profile')
                        ->leftJoin(DB::raw('(select decided_profile_id, max(decided_id) decided_id, max(decided_confirmation) decided_confirmation, max(decided_confirmdate) decided_confirmdate, max(decided_number) decided_number, max(decided_type) decided_type from qlhs_decided GROUP BY decided_profile_id) as qlhs_decided'), 'qlhs_decided.decided_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_decided.decided_type = "MGHP"'))
                        ->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
                        ->leftJoin('qlhs_profile_history as oldHis', 'oldHis.history_profile_id', '=', DB::raw('qlhs_miengiamhocphi.id_profile and oldHis.history_year = "'.$getReport->report_year.'-'.($getReport->report_year + 1).'"'))
                        ->leftJoin('qlhs_profile_history as newHis', 'newHis.history_profile_id', '=', DB::raw('qlhs_miengiamhocphi.id_profile and newHis.history_year = "'.($getReport->report_year + 1).'-'.($getReport->report_year + 2).'"'))
                        ->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
                        ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
                        ->leftJoin('qlhs_schools', 'schools_id', '=', 'qlhs_profile.profile_school_id')
                        ->where('qlhs_miengiamhocphi.type_code', '=', $getReport->report_type);

                    if (!is_null($groupHS) && !empty($groupHS)) {
                        if ($groupHS == "GROUPA") {
                            $getData->where('qlhs_miengiamhocphi.type', '=', 1);
                        }
                        if ($groupHS == "GROUPB") {
                            $getData->where('qlhs_miengiamhocphi.type', '=', 2);
                        }
                        if ($groupHS == "GROUPC") {
                            $getData->where('qlhs_miengiamhocphi.type', '=', 3);
                        }
                    }

                    if (!is_null($keySearch) && !empty($keySearch)) {
                        $getData->where(function($query) use ($keySearch){
                           $query->where("profile_name", "LIKE", "%".$keySearch."%")
                           ->orWhere("profile_birthday", "LIKE", "%".$keySearch."%")
                           ->orWhere("nationals_name", "LIKE", "%".$keySearch."%")
                           ->orWhere("profile_parentname", "LIKE", "%".$keySearch."%")
                           ->orWhere("profile_household", "LIKE", "%".$keySearch."%")
                           ->orWhere("xa.site_name", "LIKE", "%".$keySearch."%")
                           ->orWhere("huyen.site_name", "LIKE", "%".$keySearch."%")
                           ->orWhere("schools_name", "LIKE", "%".$keySearch."%")
                           ->orWhere("oldHis.level_cur", "LIKE", "%".$keySearch."%")
                           ->orWhere("newHis.level_cur", "LIKE", "%".$keySearch."%")
                           ->orWhere("profile_year", "=", "%".$keySearch."%");
                        });
                    }

                    $result['startRecord'] = ($start);
                    $result['numRows'] = $limit;
                    $result['totalRows'] = $getData->count();
                    $result['data'] = $getData->select('oldHis.history_year as old_history_year', 'oldHis.level_old as old_level_old', 'oldHis.level_cur as old_level_cur', 'oldHis.level_new as old_level_new','newHis.history_year as new_history_year', 'newHis.level_old as new_level_old', 'newHis.level_cur as new_level_cur', 'newHis.level_new as new_level_new', 'qlhs_decided.decided_id', 'qlhs_decided.decided_confirmation', 'qlhs_decided.decided_number', 'qlhs_decided.decided_confirmdate', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'schools_name', 'qlhs_miengiamhocphi.*')->orderBy('type', 'asc')->skip($start*$limit)->take($limit)->get();
                }
                if ($reportType == "CPHT") {
                    $getData = DB::table('qlhs_chiphihoctap')
                        ->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_chiphihoctap.cpht_profile_id')
                        ->leftJoin(DB::raw('(select decided_profile_id, max(decided_id) decided_id, max(decided_confirmation) decided_confirmation, max(decided_confirmdate) decided_confirmdate, max(decided_number) decided_number, max(decided_type) decided_type from qlhs_decided GROUP BY decided_profile_id) as qlhs_decided'), 'qlhs_decided.decided_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_decided.decided_type = "CPHT"'))
                        ->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
                        ->leftJoin('qlhs_profile_history as oldHis', 'oldHis.history_profile_id', '=', DB::raw('qlhs_chiphihoctap.cpht_profile_id and oldHis.history_year = "'.$getReport->report_year.'-'.($getReport->report_year + 1).'"'))
                        ->leftJoin('qlhs_profile_history as newHis', 'newHis.history_profile_id', '=', DB::raw('qlhs_chiphihoctap.cpht_profile_id and newHis.history_year = "'.($getReport->report_year + 1).'-'.($getReport->report_year + 2).'"'))
                        ->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
                        ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
                        ->leftJoin('qlhs_schools', 'schools_id', '=', 'qlhs_profile.profile_school_id')
                        ->where('qlhs_chiphihoctap.type_code', '=', $getReport->report_type);

                    if (!is_null($keySearch) && !empty($keySearch)) {
                        $getData->where(function($query) use ($keySearch){
                           $query->where("profile_name", "LIKE", "%".$keySearch."%")
                           ->orWhere("profile_birthday", "LIKE", "%".$keySearch."%")
                           ->orWhere("nationals_name", "LIKE", "%".$keySearch."%")
                           ->orWhere("profile_parentname", "LIKE", "%".$keySearch."%")
                           ->orWhere("profile_household", "LIKE", "%".$keySearch."%")
                           ->orWhere("xa.site_name", "LIKE", "%".$keySearch."%")
                           ->orWhere("huyen.site_name", "LIKE", "%".$keySearch."%")
                           ->orWhere("schools_name", "LIKE", "%".$keySearch."%")
                           ->orWhere("oldHis.level_cur", "LIKE", "%".$keySearch."%")
                           ->orWhere("newHis.level_cur", "LIKE", "%".$keySearch."%")
                           ->orWhere("profile_year", "=", "%".$keySearch."%");
                        });
                    }
                    
                    $result['startRecord'] = ($start);
                    $result['numRows'] = $limit;
                    $result['totalRows'] = $getData->count();
                    $result['data'] = $getData->select('oldHis.history_year as old_history_year', 'oldHis.level_old as old_level_old', 'oldHis.level_cur as old_level_cur', 'oldHis.level_new as old_level_new','newHis.history_year as new_history_year', 'newHis.level_old as new_level_old', 'newHis.level_cur as new_level_cur', 'newHis.level_new as new_level_new', 'qlhs_decided.decided_id', 'qlhs_decided.decided_confirmation', 'qlhs_decided.decided_number', 'qlhs_decided.decided_confirmdate', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'schools_name', 'qlhs_chiphihoctap.*')->orderBy('type', 'asc')->skip($start*$limit)->take($limit)->get();
                }
                if ($reportType == "HTAT") {
                    $getData = DB::table('qlhs_hotrotienan')
                        ->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_hotrotienan.htta_profile_id')
                        ->leftJoin(DB::raw('(select decided_profile_id, max(decided_id) decided_id, max(decided_confirmation) decided_confirmation, max(decided_confirmdate) decided_confirmdate, max(decided_number) decided_number, max(decided_type) decided_type from qlhs_decided GROUP BY decided_profile_id) as qlhs_decided'), 'qlhs_decided.decided_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_decided.decided_type = "HTAT"'))
                        ->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
                        ->leftJoin('qlhs_profile_history as oldHis', 'oldHis.history_profile_id', '=', DB::raw('qlhs_hotrotienan.htta_profile_id and oldHis.history_year = "'.$getReport->report_year.'-'.($getReport->report_year + 1).'"'))
                        ->leftJoin('qlhs_profile_history as newHis', 'newHis.history_profile_id', '=', DB::raw('qlhs_hotrotienan.htta_profile_id and newHis.history_year = "'.($getReport->report_year + 1).'-'.($getReport->report_year + 2).'"'))
                        ->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
                        ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
                        ->leftJoin('qlhs_schools', 'schools_id', '=', 'qlhs_profile.profile_school_id')
                        ->where('qlhs_hotrotienan.type_code', '=', $getReport->report_type);

                    if (!is_null($keySearch) && !empty($keySearch)) {
                        $getData->where(function($query) use ($keySearch){
                           $query->where("profile_name", "LIKE", "%".$keySearch."%")
                           ->orWhere("profile_birthday", "LIKE", "%".$keySearch."%")
                           ->orWhere("nationals_name", "LIKE", "%".$keySearch."%")
                           ->orWhere("profile_parentname", "LIKE", "%".$keySearch."%")
                           ->orWhere("profile_household", "LIKE", "%".$keySearch."%")
                           ->orWhere("xa.site_name", "LIKE", "%".$keySearch."%")
                           ->orWhere("huyen.site_name", "LIKE", "%".$keySearch."%")
                           ->orWhere("schools_name", "LIKE", "%".$keySearch."%")
                           ->orWhere("oldHis.level_cur", "LIKE", "%".$keySearch."%")
                           ->orWhere("newHis.level_cur", "LIKE", "%".$keySearch."%")
                           ->orWhere("profile_year", "=", "%".$keySearch."%");
                        });
                    }

                    $result['startRecord'] = ($start);
                    $result['numRows'] = $limit;
                    $result['totalRows'] = $getData->count();
                    $result['data'] = $getData->select('oldHis.history_year as old_history_year', 'oldHis.level_old as old_level_old', 'oldHis.level_cur as old_level_cur', 'oldHis.level_new as old_level_new','newHis.history_year as new_history_year', 'newHis.level_old as new_level_old', 'newHis.level_cur as new_level_cur', 'newHis.level_new as new_level_new', 'qlhs_decided.decided_id', 'qlhs_decided.decided_confirmation', 'qlhs_decided.decided_number', 'qlhs_decided.decided_confirmdate', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'schools_name', 'qlhs_hotrotienan.*')->orderBy('type', 'asc')->skip($start*$limit)->take($limit)->get();
                }
                if ($reportType == "HTBT") {
                    $getData = DB::table('qlhs_hotrohocsinhbantru')
                        ->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_hotrohocsinhbantru.profile_id')
                        ->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
                        ->leftJoin('qlhs_profile_history as oldHis', 'oldHis.history_profile_id', '=', DB::raw('qlhs_hotrohocsinhbantru.profile_id and oldHis.history_year = "'.$getReport->report_year.'-'.($getReport->report_year + 1).'"'))
                        ->leftJoin('qlhs_profile_history as newHis', 'newHis.history_profile_id', '=', DB::raw('qlhs_hotrohocsinhbantru.profile_id and newHis.history_year = "'.($getReport->report_year + 1).'-'.($getReport->report_year + 2).'"'))
                        ->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
                        ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
                        ->leftJoin('qlhs_schools', 'schools_id', '=', 'qlhs_profile.profile_school_id')
                        ->where('qlhs_hotrohocsinhbantru.type_code', '=', $getReport->report_type);

                    if (!is_null($keySearch) && !empty($keySearch)) {
                        $getData->where(function($query) use ($keySearch){
                           $query->where("profile_name", "LIKE", "%".$keySearch."%")
                           ->orWhere("profile_birthday", "LIKE", "%".$keySearch."%")
                           ->orWhere("nationals_name", "LIKE", "%".$keySearch."%")
                           ->orWhere("profile_parentname", "LIKE", "%".$keySearch."%")
                           ->orWhere("profile_household", "LIKE", "%".$keySearch."%")
                           ->orWhere("xa.site_name", "LIKE", "%".$keySearch."%")
                           ->orWhere("huyen.site_name", "LIKE", "%".$keySearch."%")
                           ->orWhere("schools_name", "LIKE", "%".$keySearch."%")
                           ->orWhere("oldHis.level_cur", "LIKE", "%".$keySearch."%")
                           ->orWhere("newHis.level_cur", "LIKE", "%".$keySearch."%")
                           ->orWhere("profile_year", "=", "%".$keySearch."%");
                        });
                    }

                    $result['startRecord'] = ($start);
                    $result['numRows'] = $limit;
                    $result['totalRows'] = $getData->count();
                    $result['data'] = $getData->select('oldHis.history_year as old_history_year', 'oldHis.level_old as old_level_old', 'oldHis.level_cur as old_level_cur', 'oldHis.level_new as old_level_new','newHis.history_year as new_history_year', 'newHis.level_old as new_level_old', 'newHis.level_cur as new_level_cur', 'newHis.level_new as new_level_new', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_km', 'qlhs_profile.profile_giaothong', 'qlhs_profile.profile_year', 'schools_name', 'qlhs_hotrohocsinhbantru.*')->orderBy('type', 'asc')->skip($start*$limit)->take($limit)->get();
                }
                if ($reportType == "HSKT") {
                    $getData = DB::table('qlhs_hotrohocsinhkhuyettat')
                        ->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_hotrohocsinhkhuyettat.profile_id')
                        ->leftJoin(DB::raw('(select decided_profile_id, max(decided_id) decided_id, max(decided_confirmation) decided_confirmation, max(decided_confirmdate) decided_confirmdate, max(decided_number) decided_number, max(decided_type) decided_type from qlhs_decided GROUP BY decided_profile_id) as qlhs_decided'), 'qlhs_decided.decided_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_decided.decided_type = "HSKT"'))
                        ->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
                        ->leftJoin('qlhs_profile_history as oldHis', 'oldHis.history_profile_id', '=', DB::raw('qlhs_hotrohocsinhkhuyettat.profile_id and oldHis.history_year = "'.$getReport->report_year.'-'.($getReport->report_year + 1).'"'))
                        ->leftJoin('qlhs_profile_history as newHis', 'newHis.history_profile_id', '=', DB::raw('qlhs_hotrohocsinhkhuyettat.profile_id and newHis.history_year = "'.($getReport->report_year + 1).'-'.($getReport->report_year + 2).'"'))
                        ->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
                        ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
                        ->leftJoin('qlhs_schools', 'schools_id', '=', 'qlhs_profile.profile_school_id')
                        ->where('qlhs_hotrohocsinhkhuyettat.type_code', '=', $getReport->report_type);

                    if (!is_null($keySearch) && !empty($keySearch)) {
                        $getData->where(function($query) use ($keySearch){
                           $query->where("profile_name", "LIKE", "%".$keySearch."%")
                           ->orWhere("profile_birthday", "LIKE", "%".$keySearch."%")
                           ->orWhere("nationals_name", "LIKE", "%".$keySearch."%")
                           ->orWhere("profile_parentname", "LIKE", "%".$keySearch."%")
                           ->orWhere("profile_household", "LIKE", "%".$keySearch."%")
                           ->orWhere("xa.site_name", "LIKE", "%".$keySearch."%")
                           ->orWhere("huyen.site_name", "LIKE", "%".$keySearch."%")
                           ->orWhere("schools_name", "LIKE", "%".$keySearch."%")
                           ->orWhere("oldHis.level_cur", "LIKE", "%".$keySearch."%")
                           ->orWhere("newHis.level_cur", "LIKE", "%".$keySearch."%")
                           ->orWhere("profile_year", "=", "%".$keySearch."%");
                        });
                    }

                    $result['startRecord'] = ($start);
                    $result['numRows'] = $limit;
                    $result['totalRows'] = $getData->count();
                    $result['data'] = $getData->select('oldHis.history_year as old_history_year', 'oldHis.level_old as old_level_old', 'oldHis.level_cur as old_level_cur', 'oldHis.level_new as old_level_new','newHis.history_year as new_history_year', 'newHis.level_old as new_level_old', 'newHis.level_cur as new_level_cur', 'newHis.level_new as new_level_new', 'qlhs_decided.decided_id', 'qlhs_decided.decided_confirmation', 'qlhs_decided.decided_number', 'qlhs_decided.decided_confirmdate', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'schools_name', 'qlhs_hotrohocsinhkhuyettat.*')->orderBy('type', 'asc')->skip($start*$limit)->take($limit)->get();
                }
                if ($reportType == "HTATHS" || $reportType == "HSDTTS" || $reportType == "HBHSDTNT") {
                    
                    $getData = DB::table('qlhs_hotrohocsinhdantocthieuso')
                        ->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_hotrohocsinhdantocthieuso.profile_id')
                        ->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
                        ->leftJoin('qlhs_profile_history as oldHis', 'oldHis.history_profile_id', '=', DB::raw('qlhs_hotrohocsinhdantocthieuso.profile_id and oldHis.history_year = "'.$getReport->report_year.'-'.($getReport->report_year + 1).'"'))
                        ->leftJoin('qlhs_profile_history as newHis', 'newHis.history_profile_id', '=', DB::raw('qlhs_hotrohocsinhdantocthieuso.profile_id and newHis.history_year = "'.($getReport->report_year + 1).'-'.($getReport->report_year + 2).'"'))
                        ->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
                        ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
                        ->leftJoin('qlhs_schools', 'schools_id', '=', 'qlhs_profile.profile_school_id')
                        ->where('qlhs_hotrohocsinhdantocthieuso.type_code', '=', $getReport->report_type);

                    if (!is_null($keySearch) && !empty($keySearch)) {
                        $getData->where(function($query) use ($keySearch){
                           $query->where("profile_name", "LIKE", "%".$keySearch."%")
                           ->orWhere("profile_birthday", "LIKE", "%".$keySearch."%")
                           ->orWhere("nationals_name", "LIKE", "%".$keySearch."%")
                           ->orWhere("profile_parentname", "LIKE", "%".$keySearch."%")
                           ->orWhere("profile_household", "LIKE", "%".$keySearch."%")
                           ->orWhere("xa.site_name", "LIKE", "%".$keySearch."%")
                           ->orWhere("huyen.site_name", "LIKE", "%".$keySearch."%")
                           ->orWhere("schools_name", "LIKE", "%".$keySearch."%")
                           ->orWhere("oldHis.level_cur", "LIKE", "%".$keySearch."%")
                           ->orWhere("newHis.level_cur", "LIKE", "%".$keySearch."%")
                           ->orWhere("profile_year", "=", "%".$keySearch."%");
                        });
                    }

                    $result['startRecord'] = ($start);
                    $result['numRows'] = $limit;
                    $result['totalRows'] = $getData->count();
                    $result['data'] = $getData->select('oldHis.history_year as old_history_year', 'oldHis.level_old as old_level_old', 'oldHis.level_cur as old_level_cur', 'oldHis.level_new as old_level_new','newHis.history_year as new_history_year', 'newHis.level_old as new_level_old', 'newHis.level_cur as new_level_cur', 'newHis.level_new as new_level_new', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'schools_name', 'qlhs_hotrohocsinhdantocthieuso.*')->orderBy('type', 'asc')->skip($start*$limit)->take($limit)->get();
                }
                if ($reportType == "NGNA") {
                    
                    $getData = DB::table('qlhs_hotronguoinauan')
                        ->join('qlhs_schools', 'qlhs_schools.schools_id', '=', 'qlhs_hotronguoinauan.school_id')
                        ->where('qlhs_hotronguoinauan.type_code', '=', $getReport->report_type);

                    $result['year'] = $getReport->report_year;
                    $result['startRecord'] = ($start);
                    $result['numRows'] = $limit;
                    $result['totalRows'] = $getData->count();
                    $result['data'] = $getData->select('qlhs_schools.schools_name', 'qlhs_hotronguoinauan.*')->skip($start*$limit)->take($limit)->get();
                }
            }

            // if (is_null($getReport) && empty($getReport)) {
            //     $getData = DB::table('qlhs_hosobaocao')
            //         ->leftJoin('qlhs_miengiamhocphi', 'qlhs_miengiamhocphi.type_code', '=', 'qlhs_hosobaocao.report_type')
            //         ->leftJoin('qlhs_chiphihoctap', 'qlhs_chiphihoctap.type_code', '=', 'qlhs_hosobaocao.report_type')
            //         ->leftJoin('qlhs_hotrotienan', 'qlhs_hotrotienan.type_code', '=', 'qlhs_hosobaocao.report_type')
            //         ->leftJoin('qlhs_hotrohocsinhbantru', 'qlhs_hotrohocsinhbantru.type_code', '=', 'qlhs_hosobaocao.report_type')
            //         ->leftJoin('qlhs_hotrohocsinhkhuyettat', 'qlhs_hotrohocsinhkhuyettat.type_code', '=', 'qlhs_hosobaocao.report_type')
            //         ->leftJoin('qlhs_hotrohocsinhdantocthieuso as hsdtts', 'hsdtts.type_code', '=', 'qlhs_hosobaocao.report_type')
            //         ->leftJoin('qlhs_hotrohocsinhdantocthieuso as htaths', 'htaths.type_code', '=', 'qlhs_hosobaocao.report_type')
            //         ->leftJoin('qlhs_hotrohocsinhdantocthieuso as hbhsdtnt', 'hbhsdtnt.type_code', '=', 'qlhs_hosobaocao.report_type')
            //         ->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_miengiamhocphi.id_profile')
            //         ->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
            //         ->leftJoin('qlhs_profile_history as oldHis', 'oldHis.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and oldHis.history_year = "qlhs_hosobaocao.report_year-(qlhs_hosobaocao.report_year + 1)"'))
            //         ->leftJoin('qlhs_profile_history as newHis', 'newHis.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and newHis.history_year = "(qlhs_hosobaocao.report_year + 1)-(qlhs_hosobaocao.report_year + 2)"'))
            //         ->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
            //         ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
            //         ->leftJoin('qlhs_schools', 'schools_id', '=', 'qlhs_profile.profile_school_id');

            //     if (!is_null($groupHS) && !empty($groupHS)) {
            //         if ($groupHS == "GROUPA") {
            //             $getData->where('qlhs_miengiamhocphi.type', '=', 1);
            //         }
            //         if ($groupHS == "GROUPB") {
            //             $getData->where('qlhs_miengiamhocphi.type', '=', 2);
            //         }
            //         if ($groupHS == "GROUPC") {
            //             $getData->where('qlhs_miengiamhocphi.type', '=', 3);
            //         }
            //     }

            //     if (!is_null($keySearch) && !empty($keySearch)) {
            //         $getData->where(function($query) use ($keySearch){
            //             $query->where("profile_name", "LIKE", "%".$keySearch."%")
            //                 ->orWhere("profile_birthday", "LIKE", "%".$keySearch."%")
            //                 ->orWhere("nationals_name", "LIKE", "%".$keySearch."%")
            //                 ->orWhere("profile_parentname", "LIKE", "%".$keySearch."%")
            //                 ->orWhere("profile_household", "LIKE", "%".$keySearch."%")
            //                 ->orWhere("xa.site_name", "LIKE", "%".$keySearch."%")
            //                 ->orWhere("huyen.site_name", "LIKE", "%".$keySearch."%")
            //                 ->orWhere("schools_name", "LIKE", "%".$keySearch."%")
            //                 ->orWhere("oldHis.level_cur", "LIKE", "%".$keySearch."%")
            //                 ->orWhere("newHis.level_cur", "LIKE", "%".$keySearch."%")
            //                 ->orWhere("profile_year", "=", "%".$keySearch."%");
            //         });
            //     }

            //     $result['startRecord'] = ($start);
            //     $result['numRows'] = $limit;
            //     $result['totalRows'] = $getData->count();
            //     $result['data'] = $getData->select('oldHis.history_year as old_history_year', 'oldHis.level_old as old_level_old', 'oldHis.level_cur as old_level_cur', 'oldHis.level_new as old_level_new','newHis.history_year as new_history_year', 'newHis.level_old as new_level_old', 'newHis.level_cur as new_level_cur', 'newHis.level_new as new_level_new', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'schools_name', 'qlhs_miengiamhocphi.*')->orderBy('type', 'asc')->skip($start*$limit)->take($limit)->toSql();
            // }

            return $result;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function loadDatabyGroupB(Request $request){
        try {
            $result = [];

            $reportName = $request->input('REPORTNAME');
            $reportType = $request->input('REPORTTYPE');

            $start = $request->input('start');
            $limit = $request->input('limit');
            $keySearch = $request->input('KEY');

            $getReport = DB::table('qlhs_hosobaocao')
                ->where('report_name', '=', $reportName)
                ->where('report', '=', $reportType)
                ->select('report_type', 'report_year')->first();

            if (!is_null($getReport) && !empty($getReport) && $getReport->report_year > 0) {
                
                if ($reportType == "MGHP") {
                    $getData = DB::table('qlhs_miengiamhocphi')
                        ->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_miengiamhocphi.id_profile')
                        ->leftJoin(DB::raw('(select decided_profile_id, max(decided_id) decided_id, max(decided_confirmation) decided_confirmation, max(decided_confirmdate) decided_confirmdate, max(decided_number) decided_number, max(decided_type) decided_type from qlhs_decided GROUP BY decided_profile_id) as qlhs_decided'), 'qlhs_decided.decided_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_decided.decided_type = "MGHP"'))
                        ->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
                        ->leftJoin('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_miengiamhocphi.id_profile and qlhs_profile_history.history_year = "'.$getReport->report_year.'-'.($getReport->report_year + 1).'"'))
                        ->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
                        ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
                        ->leftJoin('qlhs_schools', 'schools_id', '=', 'qlhs_profile.profile_school_id')
                        ->where('qlhs_miengiamhocphi.type_code', '=', $getReport->report_type) 
                        ->where('qlhs_miengiamhocphi.type', '=', 2);

                    $result['startRecord'] = ($start);
                    $result['numRows'] = $limit;
                    $result['totalRows'] = $getData->count();
                    $result['data'] = $getData->select('qlhs_profile_history.history_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_decided.decided_id', 'qlhs_decided.decided_confirmation', 'qlhs_decided.decided_number', 'qlhs_decided.decided_confirmdate', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'schools_name', 'qlhs_miengiamhocphi.*')->orderBy('qlhs_profile.updated_at', 'desc')->skip($start*$limit)->take($limit)->get();
                }
                if ($reportType == "CPHT") {
                    $getData = DB::table('qlhs_chiphihoctap')
                        ->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_chiphihoctap.cpht_profile_id')
                        ->leftJoin(DB::raw('(select decided_profile_id, max(decided_id) decided_id, max(decided_confirmation) decided_confirmation, max(decided_confirmdate) decided_confirmdate, max(decided_number) decided_number, max(decided_type) decided_type from qlhs_decided GROUP BY decided_profile_id) as qlhs_decided'), 'qlhs_decided.decided_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_decided.decided_type = "CPHT"'))
                        ->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
                        ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_chiphihoctap.cpht_profile_id and qlhs_profile_history.history_year = "'.$getReport->report_year.'-'.($getReport->report_year + 1).'"'))
                        ->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
                        ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
                        ->leftJoin('qlhs_schools', 'schools_id', '=', 'qlhs_profile.profile_school_id')
                        ->where('qlhs_chiphihoctap.type_code', '=', $getReport->report_type)
                        ->where('qlhs_chiphihoctap.type', '=', 2);

                    $result['startRecord'] = ($start);
                    $result['numRows'] = $limit;
                    $result['totalRows'] = $getData->count();
                    $result['data'] = $getData->select('qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile_history.history_year', 'qlhs_decided.decided_id', 'qlhs_decided.decided_confirmation', 'qlhs_decided.decided_number', 'qlhs_decided.decided_confirmdate', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'schools_name', 'qlhs_chiphihoctap.*')->orderBy('qlhs_profile.updated_at', 'desc')->skip($start*$limit)->take($limit)->get();
                }
                if ($reportType == "HTAT") {
                    $getData = DB::table('qlhs_hotrotienan')
                        ->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_hotrotienan.htta_profile_id')
                        ->leftJoin(DB::raw('(select decided_profile_id, max(decided_id) decided_id, max(decided_confirmation) decided_confirmation, max(decided_confirmdate) decided_confirmdate, max(decided_number) decided_number, max(decided_type) decided_type from qlhs_decided GROUP BY decided_profile_id) as qlhs_decided'), 'qlhs_decided.decided_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_decided.decided_type = "HTAT"'))
                        ->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
                        ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_hotrotienan.htta_profile_id and qlhs_profile_history.history_year = "'.$getReport->report_year.'-'.($getReport->report_year + 1).'"'))
                        ->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
                        ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
                        ->leftJoin('qlhs_schools', 'schools_id', '=', 'qlhs_profile.profile_school_id')
                        ->where('qlhs_hotrotienan.type_code', '=', $getReport->report_type)
                        ->where('qlhs_hotrotienan.type', '=', 2);

                    $result['startRecord'] = ($start);
                    $result['numRows'] = $limit;
                    $result['totalRows'] = $getData->count();
                    $result['data'] = $getData->select('qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile_history.history_year', 'qlhs_decided.decided_id', 'qlhs_decided.decided_confirmation', 'qlhs_decided.decided_number', 'qlhs_decided.decided_confirmdate', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'schools_name', 'qlhs_hotrotienan.*')->orderBy('qlhs_profile.updated_at', 'desc')->skip($start*$limit)->take($limit)->get();
                }
                if ($reportType == "HTBT") {
                    $getData = DB::table('qlhs_hotrohocsinhbantru')
                        ->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_hotrohocsinhbantru.profile_id')
                        ->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
                        ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_hotrohocsinhbantru.profile_id and qlhs_profile_history.history_year = "'.$getReport->report_year.'-'.($getReport->report_year + 1).'"'))
                        ->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
                        ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
                        ->leftJoin('qlhs_schools', 'schools_id', '=', 'qlhs_profile.profile_school_id')
                        ->where('qlhs_hotrohocsinhbantru.type_code', '=', $getReport->report_type)
                        ->where('qlhs_hotrohocsinhbantru.type', '=', 2);

                    $result['startRecord'] = ($start);
                    $result['numRows'] = $limit;
                    $result['totalRows'] = $getData->count();
                    $result['data'] = $getData->select('qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile_history.history_year', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_km', 'qlhs_profile.profile_giaothong', 'qlhs_profile.profile_year', 'schools_name', 'qlhs_hotrohocsinhbantru.*')->orderBy('qlhs_profile.updated_at', 'desc')->skip($start*$limit)->take($limit)->get();
                }
                if ($reportType == "HSKT") {
                    $getData = DB::table('qlhs_hotrohocsinhkhuyettat')
                        ->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_hotrohocsinhkhuyettat.profile_id')
                        ->leftJoin(DB::raw('(select decided_profile_id, max(decided_id) decided_id, max(decided_confirmation) decided_confirmation, max(decided_confirmdate) decided_confirmdate, max(decided_number) decided_number, max(decided_type) decided_type from qlhs_decided GROUP BY decided_profile_id) as qlhs_decided'), 'qlhs_decided.decided_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_decided.decided_type = "HSKT"'))
                        ->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
                        ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_hotrohocsinhkhuyettat.profile_id and qlhs_profile_history.history_year = "'.$getReport->report_year.'-'.($getReport->report_year + 1).'"'))
                        ->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
                        ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
                        ->leftJoin('qlhs_schools', 'schools_id', '=', 'qlhs_profile.profile_school_id')
                        ->where('qlhs_hotrohocsinhkhuyettat.type_code', '=', $getReport->report_type)
                        ->where('qlhs_hotrohocsinhkhuyettat.type', '=', 2);

                    $result['startRecord'] = ($start);
                    $result['numRows'] = $limit;
                    $result['totalRows'] = $getData->count();
                    $result['data'] = $getData->select('qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile_history.history_year', 'qlhs_decided.decided_id', 'qlhs_decided.decided_confirmation', 'qlhs_decided.decided_number', 'qlhs_decided.decided_confirmdate', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'schools_name', 'qlhs_hotrohocsinhkhuyettat.*')->orderBy('qlhs_profile.updated_at', 'desc')->skip($start*$limit)->take($limit)->get();
                }
                if ($reportType == "HTATHS" || $reportType == "HSDTTS" || $reportType == "HBHSDTNT") {
                    
                    $getData = DB::table('qlhs_hotrohocsinhdantocthieuso')
                        ->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_hotrohocsinhdantocthieuso.profile_id')
                        ->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
                        ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_hotrohocsinhdantocthieuso.profile_id and qlhs_profile_history.history_year = "'.$getReport->report_year.'-'.($getReport->report_year + 1).'"'))
                        ->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
                        ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
                        ->leftJoin('qlhs_schools', 'schools_id', '=', 'qlhs_profile.profile_school_id')
                        ->where('qlhs_hotrohocsinhdantocthieuso.type_code', '=', $getReport->report_type)
                        ->where('qlhs_hotrohocsinhdantocthieuso.type', '=', 2);

                    $result['startRecord'] = ($start);
                    $result['numRows'] = $limit;
                    $result['totalRows'] = $getData->count();
                    $result['data'] = $getData->select('qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile_history.history_year', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'schools_name', 'qlhs_hotrohocsinhdantocthieuso.*')->orderBy('qlhs_profile.updated_at', 'desc')->skip($start*$limit)->take($limit)->get();
                }
            }

            return $result;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function loadDatabyGroupC(Request $request){
        try {
            $result = [];

            $reportName = $request->input('REPORTNAME');
            $reportType = $request->input('REPORTTYPE');

            $start = $request->input('start');
            $limit = $request->input('limit');
            $keySearch = $request->input('KEY');

            $getReport = DB::table('qlhs_hosobaocao')
                ->where('report_name', '=', $reportName)
                ->where('report', '=', $reportType)
                ->select('report_type', 'report_year')->first();

            if (!is_null($getReport) && !empty($getReport) && $getReport->report_year > 0) {
                if ($reportType == "MGHP") {
                    $getData = DB::table('qlhs_miengiamhocphi')
                        ->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_miengiamhocphi.id_profile')
                        ->leftJoin(DB::raw('(select decided_profile_id, max(decided_id) decided_id, max(decided_confirmation) decided_confirmation, max(decided_confirmdate) decided_confirmdate, max(decided_number) decided_number, max(decided_type) decided_type from qlhs_decided GROUP BY decided_profile_id) as qlhs_decided'), 'qlhs_decided.decided_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_decided.decided_type = "MGHP"'))
                        ->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
                        ->leftJoin('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_miengiamhocphi.id_profile and qlhs_profile_history.history_year = "'.($getReport->report_year + 1).'-'.($getReport->report_year + 2).'"'))
                        ->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
                        ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
                        ->leftJoin('qlhs_schools', 'schools_id', '=', 'qlhs_profile.profile_school_id')
                        ->where('qlhs_miengiamhocphi.type_code', '=', $getReport->report_type) 
                        ->where('qlhs_miengiamhocphi.type', '=', 3);

                    $result['startRecord'] = ($start);
                    $result['numRows'] = $limit;
                    $result['totalRows'] = $getData->count();
                    $result['data'] = $getData->select('qlhs_profile_history.history_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_decided.decided_id', 'qlhs_decided.decided_confirmation', 'qlhs_decided.decided_number', 'qlhs_decided.decided_confirmdate', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'schools_name', 'qlhs_miengiamhocphi.*')->orderBy('qlhs_profile.updated_at', 'desc')->skip($start*$limit)->take($limit)->get();
                }
                if ($reportType == "CPHT") {
                    $getData = DB::table('qlhs_chiphihoctap')
                        ->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_chiphihoctap.cpht_profile_id')
                        ->leftJoin(DB::raw('(select decided_profile_id, max(decided_id) decided_id, max(decided_confirmation) decided_confirmation, max(decided_confirmdate) decided_confirmdate, max(decided_number) decided_number, max(decided_type) decided_type from qlhs_decided GROUP BY decided_profile_id) as qlhs_decided'), 'qlhs_decided.decided_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_decided.decided_type = "CPHT"'))
                        ->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
                        ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_chiphihoctap.cpht_profile_id and qlhs_profile_history.history_year = "'.($getReport->report_year + 1).'-'.($getReport->report_year + 2).'"'))
                        ->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
                        ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
                        ->leftJoin('qlhs_schools', 'schools_id', '=', 'qlhs_profile.profile_school_id')
                        ->where('qlhs_chiphihoctap.type_code', '=', $getReport->report_type)
                        ->where('qlhs_chiphihoctap.type', '=', 3);

                    $result['startRecord'] = ($start);
                    $result['numRows'] = $limit;
                    $result['totalRows'] = $getData->count();
                    $result['data'] = $getData->select('qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile_history.history_year', 'qlhs_decided.decided_id', 'qlhs_decided.decided_confirmation', 'qlhs_decided.decided_number', 'qlhs_decided.decided_confirmdate', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'schools_name', 'qlhs_chiphihoctap.*')->orderBy('qlhs_profile.updated_at', 'desc')->skip($start*$limit)->take($limit)->get();
                }
                if ($reportType == "HTAT") {
                    $getData = DB::table('qlhs_hotrotienan')
                        ->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_hotrotienan.htta_profile_id')
                        ->leftJoin(DB::raw('(select decided_profile_id, max(decided_id) decided_id, max(decided_confirmation) decided_confirmation, max(decided_confirmdate) decided_confirmdate, max(decided_number) decided_number, max(decided_type) decided_type from qlhs_decided GROUP BY decided_profile_id) as qlhs_decided'), 'qlhs_decided.decided_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_decided.decided_type = "HTAT"'))
                        ->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
                        ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_hotrotienan.htta_profile_id and qlhs_profile_history.history_year = "'.($getReport->report_year + 1).'-'.($getReport->report_year + 2).'"'))
                        ->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
                        ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
                        ->leftJoin('qlhs_schools', 'schools_id', '=', 'qlhs_profile.profile_school_id')
                        ->where('qlhs_hotrotienan.type_code', '=', $getReport->report_type)
                        ->where('qlhs_hotrotienan.type', '=', 3);

                    $result['startRecord'] = ($start);
                    $result['numRows'] = $limit;
                    $result['totalRows'] = $getData->count();
                    $result['data'] = $getData->select('qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile_history.history_year', 'qlhs_decided.decided_id', 'qlhs_decided.decided_confirmation', 'qlhs_decided.decided_number', 'qlhs_decided.decided_confirmdate', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'schools_name', 'qlhs_hotrotienan.*')->orderBy('qlhs_profile.updated_at', 'desc')->skip($start*$limit)->take($limit)->get();
                }
                if ($reportType == "HTBT") {
                    $getData = DB::table('qlhs_hotrohocsinhbantru')
                        ->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_hotrohocsinhbantru.profile_id')
                        ->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
                        ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_hotrohocsinhbantru.profile_id and qlhs_profile_history.history_year = "'.($getReport->report_year + 1).'-'.($getReport->report_year + 2).'"'))
                        ->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
                        ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
                        ->leftJoin('qlhs_schools', 'schools_id', '=', 'qlhs_profile.profile_school_id')
                        ->where('qlhs_hotrohocsinhbantru.type_code', '=', $getReport->report_type)
                        ->where('qlhs_hotrohocsinhbantru.type', '=', 3);

                    $result['startRecord'] = ($start);
                    $result['numRows'] = $limit;
                    $result['totalRows'] = $getData->count();
                    $result['data'] = $getData->select('qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile_history.history_year', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_km', 'qlhs_profile.profile_giaothong', 'qlhs_profile.profile_year', 'schools_name', 'qlhs_hotrohocsinhbantru.*')->orderBy('qlhs_profile.updated_at', 'desc')->skip($start*$limit)->take($limit)->get();
                }
                if ($reportType == "HSKT") {
                    $getData = DB::table('qlhs_hotrohocsinhkhuyettat')
                        ->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_hotrohocsinhkhuyettat.profile_id')
                        ->leftJoin(DB::raw('(select decided_profile_id, max(decided_id) decided_id, max(decided_confirmation) decided_confirmation, max(decided_confirmdate) decided_confirmdate, max(decided_number) decided_number, max(decided_type) decided_type from qlhs_decided GROUP BY decided_profile_id) as qlhs_decided'), 'qlhs_decided.decided_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_decided.decided_type = "HSKT"'))
                        ->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
                        ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_hotrohocsinhkhuyettat.profile_id and qlhs_profile_history.history_year = "'.($getReport->report_year + 1).'-'.($getReport->report_year + 2).'"'))
                        ->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
                        ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
                        ->leftJoin('qlhs_schools', 'schools_id', '=', 'qlhs_profile.profile_school_id')
                        ->where('qlhs_hotrohocsinhkhuyettat.type_code', '=', $getReport->report_type)
                        ->where('qlhs_hotrohocsinhkhuyettat.type', '=', 3);

                    $result['startRecord'] = ($start);
                    $result['numRows'] = $limit;
                    $result['totalRows'] = $getData->count();
                    $result['data'] = $getData->select('qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile_history.history_year', 'qlhs_decided.decided_id', 'qlhs_decided.decided_confirmation', 'qlhs_decided.decided_number', 'qlhs_decided.decided_confirmdate', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'schools_name', 'qlhs_hotrohocsinhkhuyettat.*')->orderBy('qlhs_profile.updated_at', 'desc')->skip($start*$limit)->take($limit)->get();
                }
                if ($reportType == "HTATHS" || $reportType == "HSDTTS" || $reportType == "HBHSDTNT") {
                    
                    $getData = DB::table('qlhs_hotrohocsinhdantocthieuso')
                        ->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_hotrohocsinhdantocthieuso.profile_id')
                        ->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
                        ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_hotrohocsinhdantocthieuso.profile_id and qlhs_profile_history.history_year = "'.($getReport->report_year + 1).'-'.($getReport->report_year + 2).'"'))
                        ->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
                        ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
                        ->leftJoin('qlhs_schools', 'schools_id', '=', 'qlhs_profile.profile_school_id')
                        ->where('qlhs_hotrohocsinhdantocthieuso.type_code', '=', $getReport->report_type)
                        ->where('qlhs_hotrohocsinhdantocthieuso.type', '=', 3);

                    $result['startRecord'] = ($start);
                    $result['numRows'] = $limit;
                    $result['totalRows'] = $getData->count();
                    $result['data'] = $getData->select('qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile_history.history_year', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'schools_name', 'qlhs_hotrohocsinhdantocthieuso.*')->orderBy('qlhs_profile.updated_at', 'desc')->skip($start*$limit)->take($limit)->get();
                }
            }

            return $result;
        } catch (Exception $e) {
            return $e;
        }
    }
}

?>