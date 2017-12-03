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

class LapHoSoPhongController extends Controller
{

    public function insertPhongtralai(Request $request){
        try {
            $result = [];
            $arrProfileId = $request->input('ARRPROFILEID');
            $note = $request->input('NOTE');
            $report_name = $request->input('REPORTNAME');
            $report_type = $request->input('REPORTTYPE');

            $arrId = explode('-', $arrProfileId);

            $exe = 0;

            if (!is_null($arrId) && !empty($arrId) && count($arrId) > 0) {
                foreach ($arrId as $value) {
                    $getData = DB::table('qlhs_danhsachphongtralai')->where('Profile_id', '=', $value)->get();
                    if (is_null($getData) || empty($getData) || count($getData) <= 0) {
                        $exe = DB::table('qlhs_danhsachphongtralai')->insert([
                            'Profile_id' => $value,
                            'Status' => 2,
                            'Note' => $note,
                            'Report_name' => $report_name,
                            'Report_type' => $report_type
                            ]);
                    }
                }
            }

            if ($exe == 0) {
                $result['error'] = "Học sinh đã được trả lại";
            }
            else {
                $result['success'] = "Danh sách trả lại thành công";
            }

            return $result;
        } catch (Exception $e) {
            return $result['error'] = $e;
        }
    }

//------------------------------------------------------Lập danh sách--------------------------------------------------------------------
    public function lapdanhsachTHCD_pheduyet(Request $request){
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
                    # code...
                }
            }
            
            

            if ($number == 1) {
                $result['success'] = "Lập danh sách thành công";
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


//---------------------------------------------------------------------MGHP------------------------------------------------------------------------
    public function miengiamhocphi($truong, $namhoc, $note, $report_name, $report_user_sign, $user_name, $status){
        $json = [];
        
        $user = Auth::user()->id;
        $now = Carbon::now('Asia/Ho_Chi_Minh');
        
        
        $data1 = DB::table('qlhs_profile')
            ->leftJoin('qlhs_profile_subject','profile_id', '=' ,'profile_subject_profile_id')
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$namhoc.'-'.($namhoc + 1).'"'))
            ->leftJoin('qlhs_kinhphinamhoc as kp1','kp1.idTruong','=',DB::raw('profile_school_id and kp1.codeYear = '.($namhoc - 1).''))
            ->leftJoin('qlhs_kinhphinamhoc as kp2','kp2.idTruong','=',DB::raw('profile_school_id and kp2.codeYear = '.$namhoc.''))
            ->leftJoin('qlhs_tonghopchedo as yearold', 'yearold.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearold.qlhs_thcd_nam = '.($namhoc - 1)))
            ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearcur.qlhs_thcd_nam = '.$namhoc))
            ->leftJoin('qlhs_tonghopchedo as yearnew', 'yearnew.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearnew.qlhs_thcd_nam = '.($namhoc + 1)))
            ->select('kp1.money as money1','kp2.money as money2','profile_id','profile_name','profile_year', 'level_old', 'level_cur', 'level_new', 'yearold.PheDuyet_trangthai_MGHP as old_MGHP', 'yearold.PheDuyet_trangthai_MGHP_HK2 as old_MGHP_HK2', 'yearcur.PheDuyet_trangthai_MGHP as cur_MGHP', 'yearcur.PheDuyet_trangthai_MGHP_HK2 as cur_MGHP_HK2',
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
            // ->where('qlhs_thcd_nam', '=', DB::raw(($namhoc - 1).' and (PheDuyet_trangthai_MGHP = 1 or PheDuyet_trangthai_MGHP_HK2 = 1)'))
            
            ->groupBy('profile_id','profile_name','profile_year','level_old','level_cur','level_new','kp1.money','kp2.money', 'yearold.PheDuyet_trangthai_MGHP', 'yearold.PheDuyet_trangthai_MGHP_HK2', 'yearcur.PheDuyet_trangthai_MGHP', 'yearcur.PheDuyet_trangthai_MGHP_HK2');
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
            ->leftJoin('qlhs_profile_subject','profile_id', '=' ,'profile_subject_profile_id')
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$namhoc.'-'.($namhoc + 1).'"'))
            ->leftJoin('qlhs_kinhphinamhoc as kp1','kp1.idTruong','=',DB::raw('profile_school_id and kp1.codeYear = '.($namhoc - 1).''))
            ->leftJoin('qlhs_kinhphinamhoc as kp2','kp2.idTruong','=',DB::raw('profile_school_id and kp2.codeYear = '.$namhoc.''))
            ->leftJoin('qlhs_tonghopchedo as yearold', 'yearold.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearold.qlhs_thcd_nam = '.($namhoc - 1)))
            ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearcur.qlhs_thcd_nam = '.$namhoc))
            ->leftJoin('qlhs_tonghopchedo as yearnew', 'yearnew.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearnew.qlhs_thcd_nam = '.($namhoc + 1)))
            ->select('kp1.money as money1','kp2.money as money2','profile_id','profile_name','profile_year', 'level_old', 'level_cur', 'level_new', 'yearold.PheDuyet_trangthai_MGHP as old_MGHP', 'yearold.PheDuyet_trangthai_MGHP_HK2 as old_MGHP_HK2', 'yearcur.PheDuyet_trangthai_MGHP as cur_MGHP', 'yearcur.PheDuyet_trangthai_MGHP_HK2 as cur_MGHP_HK2',
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
            // ->where('yearcur.qlhs_thcd_nam', '=', DB::raw($namhoc.' and (yearcur.PheDuyet_trangthai_MGHP = 1 or yearcur.PheDuyet_trangthai_MGHP_HK2 = 1)'))
            ->groupBy('profile_id','profile_name','profile_year','level_old','level_cur','level_new','kp1.money','kp2.money', 'yearold.PheDuyet_trangthai_MGHP', 'yearold.PheDuyet_trangthai_MGHP_HK2', 'yearcur.PheDuyet_trangthai_MGHP', 'yearcur.PheDuyet_trangthai_MGHP_HK2');
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
            ->leftJoin('qlhs_profile_subject','profile_id', '=' ,'profile_subject_profile_id')
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
            // ->where('qlhs_thcd_nam', '=', DB::raw(($namhoc + 1).' and (PheDuyet_trangthai_MGHP = 1 or PheDuyet_trangthai_MGHP_HK2 = 1)'))
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
                            'type_code' => 'MGHP-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time
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
                            'type_code' => 'MGHP-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time
                            ]);

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
                            'type_code' => 'MGHP-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time
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
                            'type_code' => 'MGHP-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time
                            ]);

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
                            'type_code' => 'MGHP-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time
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
                            'type_code' => 'MGHP-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time
                            ]);

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
        }
    }

    public function exportforSchoolsMGHP($id){
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
            ->leftJoin('qlhs_profile_subject','profile_id', '=' ,'profile_subject_profile_id')
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$namhoc.'-'.($namhoc + 1).'"'))

            ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$truong.' AND kp.id_doituong = 92 AND kp.u_id = '.$user.' AND '.($namhoc - 1).' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($namhoc + 1).''))
            ->leftJoin('qlhs_tonghopchedo as yearold', 'yearold.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearold.qlhs_thcd_nam = '.($namhoc - 1).' and (yearold.PheDuyet_trangthai_CPHT = 1 or yearold.PheDuyet_trangthai_CPHT_HK2 = 1)'))
            ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearcur.qlhs_thcd_nam = '.$namhoc.' and (yearcur.PheDuyet_trangthai_CPHT = 1 or yearcur.PheDuyet_trangthai_CPHT_HK2 = 1)'))
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
                        ) AND kp.months in (1,2,3,4,5) AND kp.years = ".$namhoc." AND yearold.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearold.PheDuyet_trangthai_CPHT_HK2 = 1) 
                        OR (level_cur <> ''
                        AND qlhs_profile.profile_year < '".$namhoc."-12-31'
                        AND profile_subject_subject_id IN (28, 73)
                        AND (
                            profile_leaveschool_date IS NULL
                            OR (
                                profile_leaveschool_date > '".$namhoc."-05-31'
                            )
                        ) AND kp.months in (9,10,11,12) AND kp.years = ".$namhoc." AND yearcur.qlhs_thcd_trangthai_PD = 1 AND yearcur.PheDuyet_trangthai_CPHT = 1)  THEN
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
                        ) AND kp.months in (1,2,3,4,5) AND kp.years = ".($namhoc + 1)." AND yearcur.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearcur.PheDuyet_trangthai_CPHT_HK2 = 1)  
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
            
            // ->where('yearcur.qlhs_thcd_nam', '=', DB::raw($namhoc.' and (yearcur.PheDuyet_trangthai_CPHT = 1 or yearcur.PheDuyet_trangthai_CPHT_HK2 = 1 or yearold.PheDuyet_trangthai_CPHT = 1 or yearold.PheDuyet_trangthai_CPHT_HK2 = 1)'))
            
            ->groupBy('profile_id','profile_name','profile_year','level_old','level_cur','level_new','kp.value_m','kp.months','kp.years');

            $data11 = DB::table(DB::raw("({$data1->toSql()}) as m"))->mergeBindings( $data1 )
            ->select('HKII1','HKI2','HKII2','HKI3','m.DT1','m.DT2','m.profile_id','m.profile_subject_subject_id','m.profile_name','m.profile_year','m.level_old','m.level_cur','m.level_new',
                DB::raw('SUM(NhuCau2) as NhuCau'),
                DB::raw('SUM(DuToan2) as DuToan'))
            ->groupBy('HKII1','HKI2','HKII2','HKI3','m.DT1','m.DT2','m.profile_id','m.profile_subject_subject_id','m.profile_name','m.profile_year','m.level_old','m.level_cur','m.level_new');

            $data2 = DB::table('qlhs_profile')
            ->leftJoin('qlhs_profile_subject','profile_id', '=' ,'profile_subject_profile_id')
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$namhoc.'-'.($namhoc + 1).'"'))

            ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$truong.' AND kp.id_doituong = 92 AND kp.u_id = '.$user.' AND '.($namhoc - 1).' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($namhoc + 1).''))
            ->leftJoin('qlhs_tonghopchedo as yearold', 'yearold.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearold.qlhs_thcd_nam = '.($namhoc - 1).' and (yearold.PheDuyet_trangthai_CPHT = 1 or yearold.PheDuyet_trangthai_CPHT_HK2 = 1)'))
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
                        ) AND kp.months in (1,2,3,4,5) AND kp.years = ".$namhoc." AND yearold.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearold.PheDuyet_trangthai_CPHT_HK2 = 1) 
                        OR (level_cur <> ''
                        AND qlhs_profile.profile_year < '".$namhoc."-12-31'
                        AND profile_subject_subject_id IN (28, 73)
                        AND (
                            profile_leaveschool_date IS NULL
                            OR (
                                profile_leaveschool_date > '".$namhoc."-05-31'
                            )
                        ) AND kp.months in (9,10,11,12) AND kp.years = ".$namhoc." AND yearcur.qlhs_thcd_trangthai_PD = 1 AND yearcur.PheDuyet_trangthai_CPHT = 1)  THEN
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
                        ) AND kp.months in (1,2,3,4,5) AND kp.years = ".($namhoc + 1)." AND yearcur.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearcur.PheDuyet_trangthai_CPHT_HK2 = 1)  
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
            
            // ->where('yearcur.qlhs_thcd_nam', '=', DB::raw($namhoc.' and (yearcur.PheDuyet_trangthai_CPHT = 1 or yearcur.PheDuyet_trangthai_CPHT_HK2 = 1)'))
            
            ->groupBy('profile_id','profile_name','profile_year','level_old','level_cur','level_new','kp.value_m','kp.months','kp.years');

            $data22 = DB::table(DB::raw("({$data2->toSql()}) as m"))->mergeBindings( $data2 )
            ->select('HKII1','HKI2','HKII2','HKI3','m.DT1','m.DT2','m.profile_id','m.profile_subject_subject_id','m.profile_name','m.profile_year','m.level_old','m.level_cur','m.level_new',
                DB::raw('SUM(NhuCau2) as NhuCau'),
                DB::raw('SUM(DuToan2) as DuToan'))
            ->groupBy('HKII1','HKI2','HKII2','HKI3','m.DT1','m.DT2','m.profile_id','m.profile_subject_subject_id','m.profile_name','m.profile_year','m.level_old','m.level_cur','m.level_new');

            $data3 = DB::table('qlhs_profile')
            ->leftJoin('qlhs_profile_subject','profile_id', '=' ,'profile_subject_profile_id')
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.($namhoc + 1).'-'.($namhoc + 2).'"'))

            ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$truong.' AND kp.id_doituong = 92 AND kp.u_id = '.$user.' AND '.$namhoc.' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($namhoc + 2).''))
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
                    // 'ho_tro' => $value->money1,
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
                    // 'ho_tro' => $value->money1,
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
                    // 'ho_tro' => $value->money1,
                    'nhu_cau' => 0,//$value->NhuCau,
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
                    return false;
                }               
        }else{
            return false;
        }
    }

    public function exportforSchoolsCPHT($id){
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
        $this->addCellExcelCPHT($data_results, $getSchoolName->report_type, TRUE);
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
        ->leftJoin('qlhs_profile_subject','profile_id', '=' ,'profile_subject_profile_id')
        ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$namhoc.'-'.($namhoc + 1).'"'))
        ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$truong.' AND kp.id_doituong = 93 AND kp.u_id = '.$user.' AND '.($namhoc - 1).' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($namhoc + 1).''))
        ->leftJoin('qlhs_tonghopchedo as yearold', 'yearold.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearold.qlhs_thcd_nam = '.($namhoc - 1).' and (yearold.PheDuyet_trangthai_HTAT = 1 or yearold.PheDuyet_trangthai_HTAT_HK2 = 1)'))
        // ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', 'profile_id')
        ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearcur.qlhs_thcd_nam = '.$namhoc.' and (yearcur.PheDuyet_trangthai_HTAT = 1 or yearcur.PheDuyet_trangthai_HTAT_HK2 = 1)'))
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
                        ) AND kp.months in (1,2,3,4,5) AND kp.years = ".$namhoc." AND yearold.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearold.PheDuyet_trangthai_HTAT_HK2 = 1) 
                        OR (level_cur <> ''
                        AND qlhs_profile.profile_year < '".$namhoc."-12-31'
                        AND profile_subject_subject_id IN (73,26,34,28,41,74)
                        AND (
                            profile_leaveschool_date IS NULL
                            OR (
                                profile_leaveschool_date > '".$namhoc."-05-31'
                            )
                        ) AND kp.months in (9,10,11,12) AND kp.years = ".$namhoc." AND yearcur.qlhs_thcd_trangthai_PD = 1 AND yearcur.PheDuyet_trangthai_HTAT = 1)  THEN
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
                        ) AND kp.months in (1,2,3,4,5) AND kp.years = ".($namhoc + 1)." AND yearcur.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearcur.PheDuyet_trangthai_HTAT_HK2 = 1) 
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
        // ->where('yearcur.qlhs_thcd_nam', '=', DB::raw($namhoc.' and (yearcur.PheDuyet_trangthai_HTAT = 1 or yearcur.PheDuyet_trangthai_HTAT_HK2 = 1 or yearold.PheDuyet_trangthai_HTAT = 1 or yearold.PheDuyet_trangthai_HTAT_HK2 = 1)'))
        ->groupBy('profile_id','profile_name','profile_year','level_old','level_cur','level_new','kp.value_m','kp.months','kp.years');

        $data11 = DB::table(DB::raw("({$data1->toSql()}) as m"))->mergeBindings( $data1 )
        ->select('HKII1','HKI2','HKII2','HKI3','m.DT1','m.DT2','m.DT2_1','m.DT3','m.DT4','m.profile_id','m.profile_subject_subject_id','m.profile_name','m.profile_year','m.level_old','m.level_cur','m.level_new',
            DB::raw('SUM(NhuCau2) as NhuCau'),
            DB::raw('SUM(DuToan2) as DuToan'))
        ->groupBy('HKII1','HKI2','HKII2','HKI3','m.DT1','m.DT2','m.DT2_1','m.DT3','m.DT4','m.profile_id','m.profile_subject_subject_id','m.profile_name','m.profile_year','m.level_old','m.level_cur','m.level_new');


        $data2 = DB::table('qlhs_profile')
        ->leftJoin('qlhs_profile_subject','profile_id', '=' ,'profile_subject_profile_id')
        ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$namhoc.'-'.($namhoc + 1).'"'))
        ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$truong.' AND kp.id_doituong = 93 AND kp.u_id = '.$user.' AND '.($namhoc - 1).' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($namhoc + 1).''))
        ->leftJoin('qlhs_tonghopchedo as yearold', 'yearold.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearold.qlhs_thcd_nam = '.($namhoc - 1).' and (yearold.PheDuyet_trangthai_HTAT = 1 or yearold.PheDuyet_trangthai_HTAT_HK2 = 1)'))
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
                        ) AND kp.months in (1,2,3,4,5) AND kp.years = ".$namhoc." AND yearold.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearold.PheDuyet_trangthai_HTAT_HK2 = 1) 
                        OR (level_cur <> ''
                        AND qlhs_profile.profile_year < '".$namhoc."-12-31'
                        AND profile_subject_subject_id IN (73,26,34,28,41,74)
                        AND (
                            profile_leaveschool_date IS NULL
                            OR (
                                profile_leaveschool_date > '".$namhoc."-05-31'
                            )
                        ) AND kp.months in (9,10,11,12) AND kp.years = ".$namhoc." AND yearcur.qlhs_thcd_trangthai_PD = 1 AND yearcur.PheDuyet_trangthai_HTAT = 1)  THEN
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
                        ) AND kp.months in (1,2,3,4,5) AND kp.years = ".($namhoc + 1)." AND yearcur.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearcur.PheDuyet_trangthai_HTAT_HK2 = 1) 
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
        // ->where('yearcur.qlhs_thcd_nam', '=', DB::raw($namhoc.' and (yearcur.PheDuyet_trangthai_HTAT = 1 or yearcur.PheDuyet_trangthai_HTAT_HK2 = 1)'))
        ->groupBy('profile_id','profile_name','profile_year','level_old','level_cur','level_new','kp.value_m','kp.months','kp.years');

        $data22 = DB::table(DB::raw("({$data2->toSql()}) as m"))->mergeBindings( $data2 )
        ->select('HKII1','HKI2','HKII2','HKI3','m.DT1','m.DT2','m.DT2_1','m.DT3','m.DT4','m.profile_id','m.profile_subject_subject_id','m.profile_name','m.profile_year','m.level_old','m.level_cur','m.level_new',
            DB::raw('SUM(NhuCau2) as NhuCau'),
            DB::raw('SUM(DuToan2) as DuToan'))
        ->groupBy('HKII1','HKI2','HKII2','HKI3','m.DT1','m.DT2','m.DT2_1','m.DT3','m.DT4','m.profile_id','m.profile_subject_subject_id','m.profile_name','m.profile_year','m.level_old','m.level_cur','m.level_new');

        $data3 = DB::table('qlhs_profile')
        ->leftJoin('qlhs_profile_subject','profile_id', '=' ,'profile_subject_profile_id')
        ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.($namhoc + 1).'-'.($namhoc + 2).'"'))
        ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$truong.' AND kp.id_doituong = 93 AND kp.u_id = '.$user.' AND '.$namhoc.' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($namhoc + 2).''))
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
                        'type_code' => 'HTAT-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time
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
                        'type_code' => 'HTAT-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time
                        ]);
                }
            }
        }else{
            $json['1'] = 2;
        }
        if($data22->count()>0){
            foreach ($data22->get() as $key => $value) {
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
                        'type_code' => 'HTAT-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time
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
                        'type_code' => 'HTAT-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time
                        ]);
                }
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
                        'type_code' => 'HTAT-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time
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
                        'type_code' => 'HTAT-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time
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
                        return TRUE;
                    }
                    else {
                        $deleteHoso = DB::table('qlhs_hosobaocao')->where('report_type', 'LIKE', '%'.'HTAT-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time.'%')->delete();
                        $deleteHTAT = DB::table('qlhs_hotrotienan')->where('type_code', 'LIKE', '%'.'HTAT-'.$user.'-'.$truong.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time.'%')->delete();
                        return false;
                    }
                }else{
                    return false;
                }
        }else{
            return false;
        }
        //}
    }

    public function exportforSchoolsHTAT($id){
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
        $this->addCellExcelHTAT($data_results, $getSchoolName->report_type, TRUE);
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
            ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id in (34, 46, 72)'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))
            ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$school_id.' AND kp.id_doituong in (94, 98, 115) AND kp.u_id = '.$current_user_id.' AND '.($year - 1).' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 1).''))
            ->leftJoin('qlhs_tonghopchedo as yearold', 'yearold.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearold.qlhs_thcd_nam = '.($year - 1).' and (yearold.PheDuyet_trangthai_HTBT_TA = 1 or yearold.PheDuyet_trangthai_HTBT_TO = 1 or yearold.PheDuyet_trangthai_HTBT_VHTT = 1 or yearold.PheDuyet_trangthai_HTBT_TA_HK2 = 1 or yearold.PheDuyet_trangthai_HTBT_TO_HK2 = 1 or yearold.PheDuyet_trangthai_HTBT_VHTT_HK2 = 1)'))
            // ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', 'profile_id')
            ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearcur.qlhs_thcd_nam = '.$year.' and (yearcur.PheDuyet_trangthai_HTBT_TA = 1 or yearcur.PheDuyet_trangthai_HTBT_TO = 1 or yearcur.PheDuyet_trangthai_HTBT_VHTT = 1 or yearcur.PheDuyet_trangthai_HTBT_TA_HK2 = 1 or yearcur.PheDuyet_trangthai_HTBT_TO_HK2 = 1 or yearcur.PheDuyet_trangthai_HTBT_VHTT_HK2 = 1)'))
            ->leftJoin('qlhs_tonghopchedo as yearnew', 'yearnew.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearnew.qlhs_thcd_nam = '.($year + 1)))
            ->where('qlhs_profile.profile_school_id', '=', $school_id)
            ->where('qlhs_profile.profile_year', '<', $year.'-06-01')
            // ->where('yearcur.qlhs_thcd_nam', '=', DB::raw($year.' and (yearcur.PheDuyet_trangthai = 1 or yearcur.PheDuyet_trangthai_HK2 = 1 or yearold.PheDuyet_trangthai = 1 or yearold.PheDuyet_trangthai_HK2 = 1)'))
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
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.$year.' AND kp.id_doituong = 94 AND yearold.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearold.PheDuyet_trangthai_HTBT_TA_HK2 = 1) 
                            OR (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id in (34, 46)
                            AND qlhs_profile.profile_year < "'.$year.'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 94 AND yearcur.qlhs_thcd_trangthai_PD = 1 AND yearcur.PheDuyet_trangthai_HTBT_TA = 1) THEN
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
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.$year.' AND kp.id_doituong = 98 AND yearold.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearold.PheDuyet_trangthai_HTBT_TO_HK2 = 1) 
                            OR (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id in (34, 46)
                            AND qlhs_profile.profile_year < "'.$year.'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 98 AND yearcur.qlhs_thcd_trangthai_PD = 1 AND yearcur.PheDuyet_trangthai_HTBT_TO = 1) THEN
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
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.$year.' AND kp.id_doituong = 115 AND yearold.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearold.PheDuyet_trangthai_HTBT_VHTT_HK2 = 1) 
                            OR (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 46
                            AND qlhs_profile.profile_year < "'.$year.'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 115 AND yearcur.qlhs_thcd_trangthai_PD = 1 AND yearcur.PheDuyet_trangthai_HTBT_VHTT = 1) THEN
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
                            AND kp.months in (1,2,3,4,5) and kp.years = '.($year +1).' AND kp.id_doituong = 94 AND yearcur.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearcur.PheDuyet_trangthai_HTBT_TA_HK2 = 1) 
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
                            AND kp.months in (1,2,3,4,5) and kp.years = '.($year +1).' AND kp.id_doituong = 98 AND yearcur.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearcur.PheDuyet_trangthai_HTBT_TO_HK2 = 1) 
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
                            AND kp.months in (1,2,3,4,5) and kp.years = '.($year +1).' AND kp.id_doituong = 115 AND yearcur.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearcur.PheDuyet_trangthai_HTBT_VHTT_HK2 = 1) 
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
            ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id in (34, 46, 72)'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))
            ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$school_id.' AND kp.id_doituong in (94, 98, 115) AND kp.u_id = '.$current_user_id.' AND '.($year - 1).' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 1).''))
            ->leftJoin('qlhs_tonghopchedo as yearold', 'yearold.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearold.qlhs_thcd_nam = '.($year - 1).' and (yearold.PheDuyet_trangthai_HTBT_TA = 1 or yearold.PheDuyet_trangthai_HTBT_TO = 1 or yearold.PheDuyet_trangthai_HTBT_VHTT = 1 or yearold.PheDuyet_trangthai_HTBT_TA_HK2 = 1 or yearold.PheDuyet_trangthai_HTBT_TO_HK2 = 1 or yearold.PheDuyet_trangthai_HTBT_VHTT_HK2 = 1)'))
            // ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', 'profile_id')
            ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearcur.qlhs_thcd_nam = '.$year))
            ->leftJoin('qlhs_tonghopchedo as yearnew', 'yearnew.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearnew.qlhs_thcd_nam = '.($year + 1)))
            ->where('qlhs_profile.profile_school_id', '=', $school_id)
            ->where('qlhs_profile.profile_year', '>', $year.'-05-31')
            ->where('qlhs_profile.profile_year', '<', ($year + 1).'-06-01')
            // ->where('yearcur.qlhs_thcd_nam', '=', DB::raw($year.' and (yearcur.PheDuyet_trangthai = 1 or yearcur.PheDuyet_trangthai_HK2 = 1)'))
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
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.$year.' AND kp.id_doituong = 94 AND yearold.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearold.PheDuyet_trangthai_HTBT_TA_HK2 = 1) 
                            OR (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id in (34, 46)
                            AND qlhs_profile.profile_year < "'.$year.'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 94 AND yearcur.qlhs_thcd_trangthai_PD = 1 AND yearcur.PheDuyet_trangthai_HTBT_TA = 1) THEN
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
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.$year.' AND kp.id_doituong = 98 AND yearold.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearold.PheDuyet_trangthai_HTBT_TO_HK2 = 1) 
                            OR (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id in (34, 46)
                            AND qlhs_profile.profile_year < "'.$year.'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 98 AND yearcur.qlhs_thcd_trangthai_PD = 1 AND yearcur.PheDuyet_trangthai_HTBT_TO = 1) THEN
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
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.$year.' AND kp.id_doituong = 115 AND yearold.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearold.PheDuyet_trangthai_HTBT_VHTT_HK2 = 1) 
                            OR (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 46
                            AND qlhs_profile.profile_year < "'.$year.'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 115 AND yearcur.qlhs_thcd_trangthai_PD = 1 AND yearcur.PheDuyet_trangthai_HTBT_VHTT = 1) THEN
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
                            AND kp.months in (1,2,3,4,5) and kp.years = '.($year +1).' AND kp.id_doituong = 94 AND yearcur.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearcur.PheDuyet_trangthai_HTBT_TA_HK2 = 1) 
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
                            AND kp.months in (1,2,3,4,5) and kp.years = '.($year +1).' AND kp.id_doituong = 98 AND yearcur.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearcur.PheDuyet_trangthai_HTBT_TO_HK2 = 1) 
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
                            AND kp.months in (1,2,3,4,5) and kp.years = '.($year +1).' AND kp.id_doituong = 115 AND yearcur.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearcur.PheDuyet_trangthai_HTBT_VHTT_HK2 = 1) 
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
            ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id in (34, 46, 72)'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.($year + 1).'-'.($year + 2).'"'))
            ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$school_id.' AND kp.id_doituong in (94, 98, 115) AND kp.u_id = '.$current_user_id.' AND '.$year.' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 2).''))
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
            }

            // return $result;
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
            }

            return $bool;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function exportforSchoolsHTBT($id){
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
        $this->addCellExcelHTBT($data_results, $getSchoolName->report_type, TRUE);
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
            ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id = 74'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))
            ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$school_id.' AND kp.id_doituong in (95,100) AND kp.u_id = '.$current_user_id.' AND '.($year - 1).' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 1).''))
            ->leftJoin('qlhs_tonghopchedo as yearold', 'yearold.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearold.qlhs_thcd_nam = '.($year - 1).' and (yearold.PheDuyet_trangthai_HSKT_HB = 1 or yearold.PheDuyet_trangthai_HSKT_DDHT = 1 or yearold.PheDuyet_trangthai_HSKT_HB_HK2 = 1 or yearold.PheDuyet_trangthai_HSKT_DDHT_HK2 = 1)'))
            // ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', 'profile_id')
            ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearcur.qlhs_thcd_nam = '.$year.' and (yearcur.PheDuyet_trangthai_HSKT_HB = 1 or yearcur.PheDuyet_trangthai_HSKT_DDHT = 1 or yearcur.PheDuyet_trangthai_HSKT_HB_HK2 = 1 or yearcur.PheDuyet_trangthai_HSKT_DDHT_HK2 = 1)'))
            ->leftJoin('qlhs_tonghopchedo as yearnew', 'yearnew.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearnew.qlhs_thcd_nam = '.($year + 1)))
            ->where('qlhs_profile.profile_school_id', '=', $school_id)
            ->where('qlhs_profile.profile_year', '<', $year.'-06-01')
            // ->where('yearcur.qlhs_thcd_nam', '=', DB::raw($year.' and (yearcur.PheDuyet_trangthai = 1 or yearcur.PheDuyet_trangthai_HK2 = 1 or yearold.PheDuyet_trangthai = 1 or yearold.PheDuyet_trangthai_HK2 = 1)'))
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
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.$year.' AND kp.id_doituong = 95 AND yearold.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearold.PheDuyet_trangthai_HSKT_HB_HK2 = 1) 
                            OR (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 74
                            AND qlhs_profile.profile_year < "'.$year.'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 95 AND yearcur.qlhs_thcd_trangthai_PD = 1 AND yearcur.PheDuyet_trangthai_HSKT_HB = 1) THEN
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
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.$year.' AND kp.id_doituong = 100 AND yearold.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearold.PheDuyet_trangthai_HSKT_DDHT_HK2 = 1) 
                            OR (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 74
                            AND qlhs_profile.profile_year < "'.$year.'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 100 AND yearcur.qlhs_thcd_trangthai_PD = 1 AND yearcur.PheDuyet_trangthai_HSKT_DDHT = 1) THEN
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
                            AND kp.months in (1,2,3,4,5) and kp.years = '.($year +1).' AND kp.id_doituong = 95 AND yearcur.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearcur.PheDuyet_trangthai_HSKT_HB_HK2 = 1) 
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
                            AND kp.months in (1,2,3,4,5) and kp.years = '.($year +1).' AND kp.id_doituong = 100 AND yearcur.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearcur.PheDuyet_trangthai_HSKT_DDHT_HK2 = 1) 
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
            ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id = 74'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))
            ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$school_id.' AND kp.id_doituong in (95,100) AND kp.u_id = '.$current_user_id.' AND '.($year - 1).' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 1).''))
            ->leftJoin('qlhs_tonghopchedo as yearold', 'yearold.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearold.qlhs_thcd_nam = '.($year - 1).' and (yearold.PheDuyet_trangthai_HSKT_HB = 1 or yearold.PheDuyet_trangthai_HSKT_DDHT = 1 or yearold.PheDuyet_trangthai_HSKT_HB_HK2 = 1 or yearold.PheDuyet_trangthai_HSKT_DDHT_HK2 = 1)'))
            // ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', 'profile_id')
            ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearcur.qlhs_thcd_nam = '.$year))
            ->leftJoin('qlhs_tonghopchedo as yearnew', 'yearnew.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearnew.qlhs_thcd_nam = '.($year + 1)))
            ->where('qlhs_profile.profile_school_id', '=', $school_id)
            ->where('qlhs_profile.profile_year', '>', $year.'-05-31')
            ->where('qlhs_profile.profile_year', '<', ($year + 1).'-06-01')
            // ->where('yearcur.qlhs_thcd_nam', '=', DB::raw($year.' and (yearcur.PheDuyet_trangthai = 1 or yearcur.PheDuyet_trangthai_HK2 = 1)'))
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
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.$year.' AND kp.id_doituong = 95 AND yearold.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearold.PheDuyet_trangthai_HSKT_HB_HK2 = 1) 
                            OR (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 74
                            AND qlhs_profile.profile_year < "'.$year.'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 95 AND yearcur.qlhs_thcd_trangthai_PD = 1 AND yearcur.PheDuyet_trangthai_HSKT_HB = 1) THEN
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
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.$year.' AND kp.id_doituong = 100 AND yearold.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearold.PheDuyet_trangthai_HSKT_DDHT_HK2 = 1) 
                            OR (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 74
                            AND qlhs_profile.profile_year < "'.$year.'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 100 AND yearcur.qlhs_thcd_trangthai_PD = 1 AND yearcur.PheDuyet_trangthai_HSKT_DDHT = 1) THEN
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
                            AND kp.months in (1,2,3,4,5) and kp.years = '.($year +1).' AND kp.id_doituong = 95 AND yearcur.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearcur.PheDuyet_trangthai_HSKT_HB_HK2 = 1) 
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
                            AND kp.months in (1,2,3,4,5) and kp.years = '.($year +1).' AND kp.id_doituong = 100 AND yearcur.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearcur.PheDuyet_trangthai_HSKT_DDHT_HK2 = 1) 
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
            ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id = 74'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.($year + 1).'-'.($year + 2).'"'))
            ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$school_id.' AND kp.id_doituong in (95,100) AND kp.u_id = '.$current_user_id.' AND '.$year.' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 2).''))
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
                        $result['success'] = "Thêm mới thành công!";
                        return TRUE;
                    }
                    else {
                        $deleteHoso = DB::table('qlhs_hosobaocao')->where('report_type', 'LIKE', '%'.$type_code.'%')->delete();
                        $deleteHSKT = DB::table('qlhs_hotrohocsinhkhuyettat')->where('type_code', 'LIKE', '%'.$type_code.'%')->delete();
                        $result['error'] = "Thêm mới thất bại!";
                        return false;
                    }
                }
                else {$result['error'] = "Thêm mới thất bại!"; return false;}
            }
            else {$result['error'] = "Thêm mới thất bại!"; return false;}

            // return $result;
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
                    'type' => $type
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

    public function exportforSchoolsHSKT($id){
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
        $this->addCellExcelHSKT($data_results, $getSchoolName->report_type, TRUE);
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
            ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id = 49'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))
            ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$school_id.' AND kp.id_doituong = 99 AND kp.u_id = '.$current_user_id.' AND '.($year - 1).' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 1).''))
            ->leftJoin('qlhs_tonghopchedo as yearold', 'yearold.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearold.qlhs_thcd_nam = '.($year - 1).' and (yearold.PheDuyet_trangthai_HSDTTS = 1 or yearold.PheDuyet_trangthai_HSDTTS_HK2 = 1)'))
            ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearcur.qlhs_thcd_nam = '.$year.' and (yearcur.PheDuyet_trangthai_HSDTTS = 1 or yearcur.PheDuyet_trangthai_HSDTTS_HK2 = 1)'))
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
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.$year.' AND kp.id_doituong = 99 AND yearold.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearold.PheDuyet_trangthai_HSDTTS_HK2 = 1) 
                            OR (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile.profile_year < "'.$year.'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 99 AND yearcur.qlhs_thcd_trangthai_PD = 1 AND yearcur.PheDuyet_trangthai_HSDTTS = 1) THEN
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
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.($year + 1).' AND kp.id_doituong = 99 AND yearcur.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearcur.PheDuyet_trangthai_HSDTTS_HK2 = 1) 
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
            ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id = 49'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))
            ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$school_id.' AND kp.id_doituong = 99 AND kp.u_id = '.$current_user_id.' AND '.($year - 1).' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 1).''))
            ->leftJoin('qlhs_tonghopchedo as yearold', 'yearold.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearold.qlhs_thcd_nam = '.($year - 1).' and (yearold.PheDuyet_trangthai_HSDTTS = 1 or yearold.PheDuyet_trangthai_HSDTTS_HK2 = 1)'))
            ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearcur.qlhs_thcd_nam = '.$year))
            ->leftJoin('qlhs_tonghopchedo as yearnew', 'yearnew.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearnew.qlhs_thcd_nam = '.($year + 1)))
            ->where('qlhs_profile.profile_school_id', '=', $school_id)
            ->where('qlhs_profile.profile_year', '>', $year.'-05-31')
            ->where('qlhs_profile.profile_year', '<', ($year + 1).'-06-01')
            // ->where('yearcur.qlhs_thcd_nam', '=', DB::raw($year.' and (yearcur.PheDuyet_trangthai_HSDTTS = 1 or yearcur.PheDuyet_trangthai_HSDTTS_HK2 = 1)'))
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
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.$year.' AND kp.id_doituong = 99 AND yearold.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearold.PheDuyet_trangthai_HSDTTS_HK2 = 1) 
                            OR (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 49
                            AND qlhs_profile.profile_year < "'.$year.'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 99 AND yearcur.qlhs_thcd_trangthai_PD = 1 AND yearcur.PheDuyet_trangthai_HSDTTS = 1) THEN
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
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.($year + 1).' AND kp.id_doituong = 99 AND yearcur.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearcur.PheDuyet_trangthai_HSDTTS_HK2 = 1) 
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
            ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id = 49'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.($year + 1).'-'.($year + 2).'"'))
            ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$school_id.' AND kp.id_doituong = 99 AND kp.u_id = '.$current_user_id.' AND '.$year.' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 2).''))
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
                        $result['success'] = "Thêm mới thành công!";
                        return TRUE;
                    }
                    else {
                        $deleteHoso = DB::table('qlhs_hosobaocao')->where('report_type', 'LIKE', '%'.$type_code.'%')->delete();
                        $deleteHSDTTS = DB::table('qlhs_hotrohocsinhdantocthieuso')->where('type_code', 'LIKE', '%'.$type_code.'%')->delete();
                        $result['error'] = "Thêm mới thất bại!";
                        return false;
                    }
                }
                else {$result['error'] = "Thêm mới thất bại!"; return false;}
            }
            else {$result['error'] = "Thêm mới thất bại!"; return false;}

            // return $result;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function insertHSDTTS($getDataType, $type, $current_user_id, $school_id, $year, $time){
        try {
            $bool = TRUE;
            
            $type_code = 'HSDTTS-' . $current_user_id . '-' . $school_id . '-' . $year . '' . ($year + 1) . '-' . $time;

            foreach ($getDataType as $value) {
                $tongnhucau = 0;
                $tongdutoan = 0;

                if ($type != 3) {
                    $tongnhucau = $value->{'NHUCAU'};
                }

                $tongdutoan = $value->{'DUTOAN'};

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
                    'type' => $type]);

                if ($insert_type == 0) {
                    $bool = FALSE;
                    $deleteHSDTTS = DB::table('qlhs_hotrohocsinhdantocthieuso')->where('type_code', 'LIKE', '%'.$type_code.'%')->where('type', '=', $type)->delete();
                    break;
                }
            }

            return $bool;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function exportforSchoolsHSDTTS($id){
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
        $this->addCellExcelHSDTTS($data_results, $getSchoolName->report_type, TRUE);
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
            ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id = 69'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))
            ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$school_id.' AND kp.id_doituong = 118 AND kp.u_id = '.$current_user_id.' AND '.($year - 1).' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 1).''))
            ->leftJoin('qlhs_tonghopchedo as yearold', 'yearold.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearold.qlhs_thcd_nam = '.($year - 1).' and (yearold.PheDuyet_trangthai_HTATHS = 1 or yearold.PheDuyet_trangthai_HTATHS_HK2 = 1)'))
            // ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', 'profile_id')
            ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearcur.qlhs_thcd_nam = '.$year.' and (yearcur.PheDuyet_trangthai_HTATHS = 1 or yearcur.PheDuyet_trangthai_HTATHS_HK2 = 1)'))
            ->leftJoin('qlhs_tonghopchedo as yearnew', 'yearnew.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearnew.qlhs_thcd_nam = '.($year + 1)))
            ->where('qlhs_profile.profile_school_id', '=', $school_id)
            ->where('qlhs_profile.profile_year', '<', $year.'-06-01')
            ->where('qlhs_profile.profile_statusNQ57', '=', 1)
            // ->where('yearcur.qlhs_thcd_nam', '=', DB::raw($year.' and (yearcur.PheDuyet_trangthai_HTATHS = 1 or yearcur.PheDuyet_trangthai_HTATHS_HK2 = 1 or yearold.PheDuyet_trangthai_HTATHS = 1 or yearold.PheDuyet_trangthai_HTATHS_HK2 = 1)'))
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
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.$year.' AND kp.id_doituong = 118 AND yearold.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearold.PheDuyet_trangthai_HTATHS_HK2 = 1) 
                            OR (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 69
                            AND qlhs_profile.profile_year < "'.$year.'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 118 AND yearcur.qlhs_thcd_trangthai_PD = 1 AND yearcur.PheDuyet_trangthai_HTATHS = 1) THEN
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
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.($year + 1).' AND kp.id_doituong = 118 AND yearcur.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearcur.PheDuyet_trangthai_HTATHS_HK2 = 1) 
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
            ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id = 69'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))
            ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$school_id.' AND kp.id_doituong = 118 AND kp.u_id = '.$current_user_id.' AND '.($year - 1).' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 1).''))
            ->leftJoin('qlhs_tonghopchedo as yearold', 'yearold.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearold.qlhs_thcd_nam = '.($year - 1).' and (yearold.PheDuyet_trangthai_HTATHS = 1 or yearold.PheDuyet_trangthai_HTATHS_HK2 = 1)'))
            // ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', 'profile_id')
            ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearcur.qlhs_thcd_nam = '.$year))
            ->leftJoin('qlhs_tonghopchedo as yearnew', 'yearnew.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearnew.qlhs_thcd_nam = '.($year + 1)))
            ->where('qlhs_profile.profile_school_id', '=', $school_id)
            ->where('qlhs_profile.profile_year', '>', $year.'-05-31')
            ->where('qlhs_profile.profile_year', '<', ($year + 1).'-06-01')
            ->where('qlhs_profile.profile_statusNQ57', '=', 1)
            // ->where('yearcur.qlhs_thcd_nam', '=', DB::raw($year.' and (yearcur.PheDuyet_trangthai_HTATHS = 1 or yearcur.PheDuyet_trangthai_HTATHS_HK2 = 1)'))
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
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.$year.' AND kp.id_doituong = 118 AND yearold.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearold.PheDuyet_trangthai_HTATHS_HK2 = 1) 
                            OR (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 69
                            AND qlhs_profile.profile_year < "'.$year.'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 118 AND yearcur.qlhs_thcd_trangthai_PD = 1 AND yearcur.PheDuyet_trangthai_HTATHS = 1) THEN
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
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.($year + 1).' AND kp.id_doituong = 118 AND yearcur.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearcur.PheDuyet_trangthai_HTATHS_HK2 = 1) 
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
            ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id = 69'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.($year + 1).'-'.($year + 2).'"'))
            ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$school_id.' AND kp.id_doituong = 118 AND kp.u_id = '.$current_user_id.' AND '.$year.' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 2).''))
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
                        $result['success'] = "Thêm mới thành công!";
                        return TRUE;
                    }
                    else {
                        $deleteHoso = DB::table('qlhs_hosobaocao')->where('report_type', 'LIKE', '%'.$type_code.'%')->delete();
                        $deleteHTATHS = DB::table('qlhs_hotrohocsinhdantocthieuso')->where('type_code', 'LIKE', '%'.$type_code.'%')->delete();
                        $result['error'] = "Thêm mới thất bại!";
                        return false;
                    }
                }
                else {$result['error'] = "Thêm mới thất bại!"; return false;}
            }
            else {$result['error'] = "Thêm mới thất bại!"; return false;}

            // return $result;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function insertHTATHS($getDataType, $type, $current_user_id, $school_id, $year, $time){
        try {
            $bool = TRUE;
            
            $type_code = 'HTATHS-' . $current_user_id . '-' . $school_id . '-' . $year . '' . ($year + 1) . '-' . $time;

            foreach ($getDataType as $value) {
                $tongnhucau = 0;
                $tongdutoan = 0;

                if ($type != 3) {
                    $tongnhucau = $value->{'NHUCAU'};
                }

                $tongdutoan = $value->{'DUTOAN'};

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
                    'type' => $type]);

                if ($insert_type == 0) {
                    $bool = FALSE;
                    $deleteHTATHS = DB::table('qlhs_hotrohocsinhdantocthieuso')->where('type_code', 'LIKE', '%'.$type_code.'%')->where('type', '=', $type)->delete();
                    break;
                }
            }

            return $bool;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function exportforSchoolsHTATHS($id){
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
        $this->addCellExcelHTATHS($data_results, $getSchoolName->report_type, TRUE);
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
            ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id = 70'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))
            ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$school_id.' AND kp.id_doituong = 119 AND kp.u_id = '.$current_user_id.' AND '.($year - 1).' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 1).''))
            ->leftJoin('qlhs_tonghopchedo as yearold', 'yearold.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearold.qlhs_thcd_nam = '.($year - 1).' and (yearold.PheDuyet_trangthai_HBHSDTNT = 1 or yearold.PheDuyet_trangthai_HBHSDTNT_HK2 = 1)'))
            ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearcur.qlhs_thcd_nam = '.$year.' and (yearcur.PheDuyet_trangthai_HBHSDTNT = 1 or yearcur.PheDuyet_trangthai_HBHSDTNT_HK2 = 1)'))
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
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.$year.' AND kp.id_doituong = 119 AND yearold.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearold.PheDuyet_trangthai_HBHSDTNT_HK2 = 1) 
                            OR (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 70
                            AND qlhs_profile.profile_year < "'.$year.'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 119 AND yearcur.qlhs_thcd_trangthai_PD = 1 AND yearcur.PheDuyet_trangthai_HBHSDTNT = 1) THEN
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
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.($year + 1).' AND kp.id_doituong = 119 AND yearcur.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearcur.PheDuyet_trangthai_HBHSDTNT_HK2 = 1) 
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
            ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id = 70'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))
            ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$school_id.' AND kp.id_doituong = 119 AND kp.u_id = '.$current_user_id.' AND '.($year - 1).' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 1).''))
            ->leftJoin('qlhs_tonghopchedo as yearold', 'yearold.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearold.qlhs_thcd_nam = '.($year - 1).' and (yearold.PheDuyet_trangthai_HBHSDTNT = 1 or yearold.PheDuyet_trangthai_HBHSDTNT_HK2 = 1)'))
            ->leftJoin('qlhs_tonghopchedo as yearcur', 'yearcur.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearcur.qlhs_thcd_nam = '.$year))
            ->leftJoin('qlhs_tonghopchedo as yearnew', 'yearnew.qlhs_thcd_profile_id', '=', DB::raw('profile_id AND yearnew.qlhs_thcd_nam = '.($year + 1)))
            ->where('qlhs_profile.profile_school_id', '=', $school_id)
            ->where('qlhs_profile.profile_year', '>', $year.'-05-31')
            ->where('qlhs_profile.profile_year', '<', ($year + 1).'-06-01')
            // ->where('yearcur.qlhs_thcd_nam', '=', DB::raw($year.' and (yearcur.PheDuyet_trangthai_HBHSDTNT = 1 or yearcur.PheDuyet_trangthai_HBHSDTNT_HK2 = 1)'))
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
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.$year.' AND kp.id_doituong = 119 AND yearold.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearold.PheDuyet_trangthai_HBHSDTNT_HK2 = 1) 
                            OR (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 70
                            AND qlhs_profile.profile_year < "'.$year.'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 119 AND yearcur.qlhs_thcd_trangthai_PD = 1 AND yearcur.PheDuyet_trangthai_HBHSDTNT = 1) THEN
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
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.($year + 1).' AND kp.id_doituong = 119 AND yearcur.qlhs_thcd_trangthai_PD_HK2 = 1 AND yearcur.PheDuyet_trangthai_HBHSDTNT_HK2 = 1) 
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
            ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id = 70'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.($year + 1).'-'.($year + 2).'"'))
            ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$school_id.' AND kp.id_doituong = 119 AND kp.u_id = '.$current_user_id.' AND '.$year.' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 2).''))
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
                        $result['success'] = "Thêm mới thành công!";
                        return TRUE;
                    }
                    else {
                        $deleteHoso = DB::table('qlhs_hosobaocao')->where('report_type', 'LIKE', '%'.$type_code.'%')->delete();
                        $deleteHBHSDTNT = DB::table('qlhs_hotrohocsinhdantocthieuso')->where('type_code', 'LIKE', '%'.$type_code.'%')->delete();
                        $result['error'] = "Thêm mới thất bại!";
                        return false;
                    }
                }
                else {$result['error'] = "Thêm mới thất bại!"; return false;}
            }
            else {$result['error'] = "Thêm mới thất bại!"; return false;}

            // return $result;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function insertHBHSDTNT($getDataType, $type, $current_user_id, $school_id, $year, $time){
        try {
            $bool = TRUE;
            
            $type_code = 'HBHSDTNT-' . $current_user_id . '-' . $school_id . '-' . $year . '' . ($year + 1) . '-' . $time;

            foreach ($getDataType as $value) {
                $tongnhucau = 0;
                $tongdutoan = 0;

                if ($type != 3) {
                    $tongnhucau = $value->{'NHUCAU'};
                }
                
                $tongdutoan = $value->{'DUTOAN'};

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
                    'type' => $type]);

                if ($insert_type == 0) {
                    $bool = FALSE;
                    $deleteHBHSDTNT = DB::table('qlhs_hotrohocsinhdantocthieuso')->where('type_code', 'LIKE', '%'.$type_code.'%')->where('type', '=', $type)->delete();
                    break;
                }
            }

            return $bool;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function exportforSchoolsHBHSDTNT($id){
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
        $this->addCellExcelHBHSDTNT($data_results, $getSchoolName->report_type, TRUE);
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
}
?>