<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Models\DanhMucHoSoHocSinh;
use App\Models\qlhs_tonghopchedo;
use Excel;
use File,datetime;

class HosohocsinhController extends Controller
{
    public function view(){
          $list = DB::table('users')->paginate(5);
        return view('admin.hoso.hosohocsinh.listing',['list'=>$list]);
    }

    //Get permission
    public function getPermission($id){
      $json = [];
      $val = [];
      $data = DB::select('SELECT pu.module_id,pu.permission_id FROM permission_users pu WHERE pu.role_user_id = '.Auth::user()->id.' and pu.module_id = '.$id.'');
      foreach ($data as $key => $value) {
        $val[] = $value->permission_id.'';
      }
      $json['permission'] = $val;
      return $json;
    }
    public function changeSubject(){
         return view('admin.hoso.hosohocsinh.changeSubject');
    }
    //
    public function updateSubject(){
         return view('admin.hoso.hosohocsinh.updateSubject');
    }
    public function changeSubjectLoad(Request $rq){
        $json = [];
            $start = $rq->input('start');
            $limit = $rq->input('limit');
            //$id_truong = $rq->input('id_truong');class_id
            //$id_lop = $rq->input('id_lop');
        $keysearch = $rq->input('key');
        if($keysearch != null && $keysearch != ''){
            if($rq->schools_id == 0){
                $data = DB::table('qlhs_profile_subject')->leftJoin('qlhs_profile','profile_id','profile_subject_profile_id')->leftJoin('qlhs_subject','profile_subject_subject_id','subject_id')->leftJoin('qlhs_class','class_id','profile_class_id')->where('active',1)->where(function($q) use ($keysearch){
                    $q->where('profile_name', 'LIKE','%'.$keysearch.'%')->orWhere('subject_name','LIKE','%'.$keysearch.'%')->orWhere('start_year','LIKE','%'.$keysearch.'%')->orWhere('end_year','LIKE','%'.$keysearch.'%')->orWhere('class_name','LIKE','%'.$keysearch.'%');
                })->select(DB::raw('profile_name,profile_status ,GROUP_CONCAT(subject_name) as subject_name,profile_id,profile_birthday,class_name,profile_start_time,profile_end_time,start_year,end_year'))->groupBy('profile_name','profile_status','profile_id','class_name','profile_start_time','profile_end_time','profile_birthday','start_year','end_year');
           
                $count = DB::table('qlhs_profile_subject')->leftJoin('qlhs_profile','profile_id','profile_subject_profile_id')->leftJoin('qlhs_subject','profile_subject_subject_id','subject_id')->leftJoin('qlhs_class','class_id','profile_class_id')->where('active',1)->where(function($q) use ($keysearch){
                    $q->where('profile_name', 'LIKE','%'.$keysearch.'%')->orWhere('subject_name','LIKE','%'.$keysearch.'%')->orWhere('start_year','LIKE','%'.$keysearch.'%')->orWhere('end_year','LIKE','%'.$keysearch.'%')->orWhere('class_name','LIKE','%'.$keysearch.'%');
                })->select(DB::raw('profile_name ,GROUP_CONCAT(subject_name) as subject_name,profile_id,profile_birthday,class_name,profile_start_time,profile_end_time'))->groupBy('profile_name','profile_id','class_name','profile_start_time','profile_end_time','profile_birthday','start_year','end_year');
            }else{
                $data = DB::table('qlhs_profile_subject')->leftJoin('qlhs_profile','profile_id','profile_subject_profile_id')->leftJoin('qlhs_subject','profile_subject_subject_id','subject_id')->leftJoin('qlhs_class','class_id','profile_class_id')->where('profile_school_id',$rq->schools_id)->where('active',1)->where(function($q) use ($keysearch){
                    $q->where('profile_name', 'LIKE','%'.$keysearch.'%')->orWhere('subject_name','LIKE','%'.$keysearch.'%')->orWhere('start_year','LIKE','%'.$keysearch.'%')->orWhere('end_year','LIKE','%'.$keysearch.'%')->orWhere('class_name','LIKE','%'.$keysearch.'%');
                })->select(DB::raw('profile_name,profile_status ,GROUP_CONCAT(subject_name) as subject_name,profile_id,profile_birthday,class_name,profile_start_time,profile_end_time,start_year,end_year'))->groupBy('profile_name','profile_status','profile_id','class_name','profile_start_time','profile_end_time','profile_birthday','start_year','end_year');
           
                $count = DB::table('qlhs_profile_subject')->leftJoin('qlhs_profile','profile_id','profile_subject_profile_id')->leftJoin('qlhs_subject','profile_subject_subject_id','subject_id')->leftJoin('qlhs_class','class_id','profile_class_id')->where('active',1)->where('profile_school_id',$rq->schools_id)->where(function($q) use ($keysearch){
                    $q->where('profile_name', 'LIKE','%'.$keysearch.'%')->orWhere('subject_name','LIKE','%'.$keysearch.'%')->orWhere('start_year','LIKE','%'.$keysearch.'%')->orWhere('end_year','LIKE','%'.$keysearch.'%')->orWhere('class_name','LIKE','%'.$keysearch.'%');
                })->select(DB::raw('profile_name ,GROUP_CONCAT(subject_name) as subject_name,profile_id,profile_birthday,class_name,profile_start_time,profile_end_time'))->groupBy('profile_name','profile_id','class_name','profile_start_time','profile_end_time','profile_birthday','start_year','end_year');
            }
            if($rq->class_id != null){
                $count = $count->where('profile_class_id',$rq->class_id);
            }
            $json['totalRows'] = DB::table(DB::raw("({$count->toSql()}) as m"))->mergeBindings($count)->select(DB::raw('count(*) as total'))->first()->total;
            $json['startRecord'] = ($start);
            $json['numRows'] = $limit;
            if($rq->class_id != null){
                $json['data'] = $data->where('profile_class_id',$rq->class_id)->orderBy('profile_start_time','desc')->orderBy('qlhs_profile_subject.profile_subject_updatedate', 'desc')->orderBy('profile_name')->skip($start*$limit)->take($limit)->get();
            }else{
                $json['data'] = $data->orderBy('profile_start_time','desc')->orderBy('qlhs_profile_subject.profile_subject_updatedate', 'desc')->orderBy('profile_name')->skip($start*$limit)->take($limit)->get();
            }
        }else {

            if($rq->schools_id == 0){
                $data = DB::table('qlhs_profile_subject')->leftJoin('qlhs_profile','profile_id','profile_subject_profile_id')->leftJoin('qlhs_subject','profile_subject_subject_id','subject_id')->leftJoin('qlhs_class','class_id','profile_class_id')->where('active',1)->select(DB::raw('profile_name,profile_status ,GROUP_CONCAT(subject_name) as subject_name,profile_id,profile_birthday,class_name,profile_start_time,profile_end_time,start_year,end_year'))->groupBy('profile_status','profile_name','profile_id','class_name','profile_start_time','profile_end_time','profile_birthday','start_year','end_year');
                $count = DB::table('qlhs_profile_subject')->leftJoin('qlhs_profile','profile_id','profile_subject_profile_id')->leftJoin('qlhs_subject','profile_subject_subject_id','subject_id')->leftJoin('qlhs_class','class_id','profile_class_id')->where('active',1)->select(DB::raw('profile_name ,GROUP_CONCAT(subject_name) as subject_name,profile_id,profile_birthday,class_name,profile_start_time,profile_end_time'))->groupBy('profile_name','profile_id','class_name','profile_start_time','profile_end_time','profile_birthday','start_year','end_year');
            }else{
                $data = DB::table('qlhs_profile_subject')->leftJoin('qlhs_profile','profile_id','profile_subject_profile_id')->leftJoin('qlhs_subject','profile_subject_subject_id','subject_id')->leftJoin('qlhs_class','class_id','profile_class_id')->where('profile_school_id',$rq->schools_id)->where('active',1)->select(DB::raw('profile_name,profile_status ,GROUP_CONCAT(subject_name) as subject_name,profile_id,profile_birthday,class_name,profile_start_time,profile_end_time,start_year,end_year'))->groupBy('profile_status','profile_name','profile_id','class_name','profile_start_time','profile_end_time','profile_birthday','start_year','end_year');
                $count = DB::table('qlhs_profile_subject')->leftJoin('qlhs_profile','profile_id','profile_subject_profile_id')->leftJoin('qlhs_subject','profile_subject_subject_id','subject_id')->leftJoin('qlhs_class','class_id','profile_class_id')->where('active',1)->where('profile_school_id',$rq->schools_id)->select(DB::raw('profile_name ,GROUP_CONCAT(subject_name) as subject_name,profile_id,profile_birthday,class_name,profile_start_time,profile_end_time'))->groupBy('profile_name','profile_id','class_name','profile_start_time','profile_end_time','profile_birthday','start_year','end_year');
            }   
       
            
            if($rq->class_id != null){
                $count = $count->where('profile_class_id',$rq->class_id);
            }
            $json['totalRows'] = DB::table(DB::raw("({$count->toSql()}) as m"))->mergeBindings($count)->select(DB::raw('count(*) as total'))->first()->total;
            $json['startRecord'] = ($start);
            $json['numRows'] = $limit;
            if($rq->class_id != null){
                $json['data'] = $data->where('profile_class_id',$rq->class_id)->orderBy('profile_start_time','desc')->orderBy('qlhs_profile_subject.profile_subject_updatedate', 'desc')->orderBy('profile_name')->skip($start*$limit)->take($limit)->get();
            }else{
                $json['data'] = $data->orderBy('profile_start_time','desc')->orderBy('qlhs_profile_subject.profile_subject_updatedate', 'desc')->orderBy('profile_name')->skip($start*$limit)->take($limit)->get();
            }
        }
        
        
        
        return $json;
    }
    public function updateSubjectLoad(Request $rq){
        $json = [];
            $start = $rq->input('start');
            $limit = $rq->input('limit');
        $data = DB::table('qlhs_profile_subject')->leftJoin('qlhs_profile','profile_id','profile_subject_profile_id')->leftJoin('qlhs_subject','profile_subject_subject_id','subject_id')->leftJoin('qlhs_class','class_id','profile_class_id')->where('profile_school_id',$rq->schools_id)->where('profile_end_time',null)->select(DB::raw('profile_name ,GROUP_CONCAT(subject_name) as subject_name,profile_id,profile_birthday,class_name,profile_start_time'))->groupBy('profile_name','profile_id','class_name','profile_start_time','profile_birthday');
        //return $data->toSql();
        $count = DB::table('qlhs_profile_subject')->leftJoin('qlhs_profile','profile_id','profile_subject_profile_id')->leftJoin('qlhs_subject','profile_subject_subject_id','subject_id')->leftJoin('qlhs_class','class_id','profile_class_id')->where('profile_school_id',$rq->schools_id)->where('profile_end_time',null)->select('profile_id')->groupBy('profile_name','profile_id','class_name');
        if($rq->class_id != null){
            $count = $count->where('profile_class_id',$rq->class_id);
        }
        $json['totalRows'] = DB::table(DB::raw("({$count->toSql()}) as m"))->mergeBindings($count)->select(DB::raw('count(*) as total'))->first()->total;
        $json['startRecord'] = ($start);
        $json['numRows'] = $limit;
        if($rq->class_id != null){
            $json['data'] = $data->where('profile_class_id',$rq->class_id)->orderBy('qlhs_profile_subject.profile_subject_updatedate', 'desc')->skip($start*$limit)->take($limit)->get();
        }else{
            $json['data'] = $data->orderBy('qlhs_profile_subject.profile_subject_updatedate', 'desc')->skip($start*$limit)->take($limit)->get();
        }
        
        
        return $json;
    }
    public function getByProfile($time,$id){
        $data = DB::table('qlhs_subject')->leftJoin('qlhs_profile_subject',function($q) use ($id,$time)
        {
            $q->on('profile_subject_subject_id', '=', 'subject_id')
                ->where('profile_subject_profile_id', '=', $id)->where('profile_start_time', '=', $time);
        })->leftJoin('users','id','profile_subject_update_userid')->where('subject_active',1)->get();
        return $data;
    }
    public function load(Request $req){
        try {
            //->leftJoin('qlhs_profile_history','qlhs_profile_history.history_class_id' ,'=', DB::raw('qlhs_profile.profile_class_id and qlhs_profile_history.history_profile_id = qlhs_profile.profile_id'))
            $json = [];
            $start = $req->input('start');
            $limit = $req->input('limit');
            $id_truong = $req->input('id_truong');
            $id_lop = $req->input('id_lop');
            $keysearch = $req->input('key');

            $qlhs_profile = DB::table('qlhs_profile')
            ->leftJoin('qlhs_nationals','qlhs_profile.profile_nationals_id' ,'=', 'qlhs_nationals.nationals_id')
            ->leftJoin('qlhs_site','qlhs_site.site_id' ,'=', 'qlhs_profile.profile_site_id2')
            ->leftJoin('qlhs_class','qlhs_profile.profile_class_id' ,'=', 'qlhs_class.class_id')
            ->leftJoin(DB::raw('(SELECT history_profile_id,max(history_year) history_year,max(history_upto_level) history_upto_level from qlhs_profile_history 
                GROUP BY history_profile_id) as qlhs_profile_history '),'qlhs_profile_history.history_profile_id', '=', 'qlhs_profile.profile_id')
            ->where('qlhs_nationals.nationals_active', 1)
            ->where('qlhs_site.site_active', 1)         
            ->where('qlhs_class.class_active', 1);
            
            if (Auth::user()->truong_id != null && Auth::user()->truong_id > 0) {
                $qlhs_profile->where('qlhs_profile.profile_school_id', '=', Auth::user()->truong_id);
            }
            if($id_truong!=0){
                if($id_lop!=0){
                    $qlhs_profile->where('qlhs_profile.profile_class_id','=',$id_lop);
                }else{
                     $qlhs_profile->where('qlhs_profile.profile_school_id','=',$id_truong);
                }
            }
            if ($keysearch != null && $keysearch != "") {
                $qlhs_profile->where(function($query) use ($keysearch){
                   $query->where("profile_code", "LIKE","%".$keysearch."%")
                   ->orWhere("profile_name", "LIKE", "%".$keysearch."%")
                   ->orWhere("profile_birthday", "LIKE", "%".$keysearch."%")
                   ->orWhere("nationals_name", "LIKE", "%".$keysearch."%")
                   ->orWhere("site_name", "LIKE", "%".$keysearch."%")
                   ->orWhere("profile_parentname", "LIKE", "%".$keysearch."%")
                   ->orWhere("class_name", "LIKE", "%".$keysearch."%")
                   ->orWhere("history_year", "=", "%".$keysearch."%")
                   ->orWhere("profile_year", "=", "%".$keysearch."%");
                });
            }
            $json['startRecord'] = ($start);
            $json['numRows'] = $limit;
            $json['totalRows'] = $qlhs_profile->count();
            $json['data'] = $qlhs_profile->select('qlhs_profile.profile_id','qlhs_profile.profile_code','qlhs_profile.profile_name','qlhs_profile.profile_birthday','qlhs_profile.profile_household','qlhs_profile.profile_parentname','qlhs_profile.profile_status','qlhs_profile.profile_year','qlhs_profile.profile_leaveschool_date','qlhs_nationals.nationals_name','qlhs_site.site_name','qlhs_class.class_name', 'qlhs_profile_history.history_year')->orderBy('qlhs_profile.updated_at', 'desc')->skip($start*$limit)->take($limit)->get();

            return $json;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function getView1(){
        return view('admin.hoso.lapdanhsach.miengiamhocphi');
        // /return view('category/wards')->with('wards', $wards);
    }

    public function exportExcel($jsonData)
    {
        $schools_id = 0;//$request->input('SCHOOLID');
        $class_id = 0;//$request->input('CLASSID');
        $key = "";//$request->input('KEY');

        $my_array_data = json_decode($jsonData, TRUE);

        foreach ($my_array_data as $key => $value) {
            switch ($key) {
                case 'SCHOOLID':
                    $schools_id = $value;
                    break;
                case 'CLASSID':
                    $class_id = $value;
                    break;
                case 'KEY':
                    $key = $value;
                    break;
                
                default:
                    # code...
                    break;
            }
        }

        $data_results = [];

        $schools = DB::table('qlhs_schools');

        $profiles = DB::table('qlhs_profile')
        ->join('qlhs_nationals', 'qlhs_nationals.nationals_id', '=', 'qlhs_profile.profile_nationals_id')
        ->join('qlhs_schools', 'qlhs_schools.schools_id', '=', 'qlhs_profile.profile_school_id')
        ->join('qlhs_class', 'qlhs_class.class_id', '=', 'qlhs_profile.profile_class_id')
        ->join('qlhs_site as tinh', 'tinh.site_id', '=', 'qlhs_profile.profile_site_id1')
        ->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
        ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3');        

        if (Auth::user()->truong_id != null && Auth::user()->truong_id > 0) {
            $profiles->where('qlhs_profile.profile_school_id', '=', Auth::user()->truong_id);
            $schools->where('qlhs_schools.schools_id', '=', Auth::user()->truong_id);
        }

        if (!is_null($schools_id) && !empty($schools_id) && $schools_id > 0) {
            $profiles->where('qlhs_profile.profile_school_id', '=', $schools_id);
            $schools->where('qlhs_schools.schools_id', '=', $schools_id);
        }

        if (!is_null($class_id) && !empty($class_id) && $class_id > 0) {
            $profiles->where('qlhs_profile.profile_class_id', '=', $class_id);
        }

        if (!is_null($key) && !empty($key)) {
            $profiles->where('qlhs_profile.profile_name', 'LIKE', '%'.$key.'%');
        }

        $data_results['schools'] = $schools->select('qlhs_schools.schools_id', 'qlhs_schools.schools_name')->get();

        $data_results['profiles'] = $profiles->select('qlhs_profile.profile_code', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_parentname', 'tinh.site_name as tentinh', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_household', 'qlhs_profile.profile_school_id', 'qlhs_schools.schools_name', 'qlhs_class.class_name', 'qlhs_profile.profile_year', 'qlhs_profile.profile_bantru', 'qlhs_profile.profile_status', 'qlhs_profile.profile_leaveschool_date')->get();

        $this->addCellExcel($data_results, 'Hồ sơ học sinh'.'_'.Auth::user()->username, FALSE);
    }

    private function addCellExcel($data_results, $filename, $type = true){
        $excel =    Excel::load(storage_path().'/exceltemplate/hosohocsinh.xlsx', function($reader) use($data_results){
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

            $row = 1;

            $indexa = 0;
            foreach($data_results['schools'] as $school){
                $row++;
                $reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row, $school->schools_name)->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($styleLeft);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row, '')->getStyle('B'.$row)->applyFromArray($borderArray);                                
                $reader->getActiveSheet()->setCellValueByColumnAndRow(2, $row, '')->getStyle('C'.$row)->applyFromArray($borderArray);                                
                $reader->getActiveSheet()->setCellValueByColumnAndRow(3, $row, '')->getStyle('D'.$row)->applyFromArray($borderArray);                                
                $reader->getActiveSheet()->setCellValueByColumnAndRow(4, $row, '')->getStyle('E'.$row)->applyFromArray($borderArray);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(5, $row, '')->getStyle('F'.$row)->applyFromArray($borderArray);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(6, $row, '')->getStyle('G'.$row)->applyFromArray($borderArray);  
                $reader->getActiveSheet()->setCellValueByColumnAndRow(7, $row, '')->getStyle('H'.$row)->applyFromArray($borderArray); 
                $reader->getActiveSheet()->setCellValueByColumnAndRow(8, $row, '')->getStyle('I'.$row)->applyFromArray($borderArray); 
                $reader->getActiveSheet()->setCellValueByColumnAndRow(9, $row, '')->getStyle('J'.$row)->applyFromArray($borderArray);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(10, $row, '')->getStyle('K'.$row)->applyFromArray($borderArray);         
                $reader->getActiveSheet()->setCellValueByColumnAndRow(11, $row, '')->getStyle('L'.$row)->applyFromArray($borderArray);        
                // $reader->getActiveSheet()->setCellValueByColumnAndRow(12, $row, '')->getStyle('M'.$row)->applyFromArray($borderArray);
                foreach($data_results['profiles'] as $value){
                    if ($school->schools_id == $value->profile_school_id) {
                        $col = 0;
                        $row++;

                        $ngaynghihoc = '-';
                        if (!is_null($value->profile_leaveschool_date)) {
                            $ngaynghihoc = Carbon::parse($value->profile_leaveschool_date)->format('d-m-Y');
                        }                        

                        $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->profile_name)->getStyle('B'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);                                
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, Carbon::parse($value->profile_birthday)->format('d-m-Y'))->getStyle('C'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);                                
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->nationals_name)->getStyle('D'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);                                
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->profile_parentname)->getStyle('E'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->profile_household)->getStyle('F'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->tenxa)->getStyle('G'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);  
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->tenhuyen)->getStyle('H'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->tentinh)->getStyle('I'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->class_name)->getStyle('J'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, Carbon::parse($value->profile_year)->format('d-m-Y'))->getStyle('K'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);         
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $ngaynghihoc)->getStyle('L'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);        
                        // $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $ngaynghihoc)->getStyle('M'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                    }
                }
            }
        });
        if($type){
            return $excel->setFilename($filename)->store('xlsx', storage_path().'/excel/MGHP');
        }else{
            return $excel->setFilename($filename)->download('xlsx');
        }
    }
    
    public function insertHoSoHocSinh(Request $request) {
        $results = [];
        try {
            // $profile_code = $request->input("PROFILECODE") ? $request->input("PROFILECODE") : "";
            // $profile_code = trim($profile_code);
            $profile_name = $request->input("PROFILENAME");
            $profile_name = trim($profile_name);
            $profile_birthday = $request->input("PROFILEBIRTHDAY");
            $profile_birthday = Carbon::parse($profile_birthday);//date('Y-m-d', strtotime(str_replace('-', '/', $profile_birthday)));//date("Y-m-d", strtotime($profile_birthday));//
            $profile_nationals_id = $request->input("PROFILENATIONALID");
            $profile_site_id1 = $request->input("PROFILESITE1");
            $profile_site_id2 = $request->input("PROFILESITE2");
            $profile_site_id3 = $request->input("PROFILESITE3") ? $request->input("PROFILESITE3") : null;
            $profile_household = $request->input("PROFILEHOUSEHOLD") ? $request->input("PROFILEHOUSEHOLD") : "";
            $profile_parentname = $request->input("PROFILEPARENTNAME");
            $profile_parentname = trim($profile_parentname);
            $profile_year = $request->input("PROFILEYEAR");
            $strProfile_year = "01" . "-" . $profile_year;
            $profile_year = Carbon::parse($strProfile_year);//date('Y-m-d', strtotime(str_replace('-', '/', $strProfile_year)));//date("Y-m-d", strtotime($strProfile_year));//

            $profile_school_id = $request->input("PROFILESCHOOLID");
            $profile_class_id = $request->input("PROFILECLASSID");
            $profile_status = $request->input("PROFILESTATUS");
            $profile_statusNQ57  = $request->input("PROFILESTATUSNQ57");

            $profile_leaveschool_date = null;
            if ($profile_status == 1) {
                $profile_leaveschool_date = $request->input("PROFILELEAVESCHOOLDATE");
                $profile_leaveschool_date = Carbon::parse($profile_leaveschool_date);//date('Y-m-d', strtotime(str_replace('-', '/', $profile_leaveschool_date)));
            }            

            $arrSubjectID = array();

            $arrSubjectID = explode('-',$request->input("ARRSUBJECTID"));
            $arrDecided = array();
            $arrDecided = $request->input("ARRDECIDED");
            //$arrSubjectID = $request->input("ARRSUBJECTID") ? $request->input("ARRSUBJECTID") : [];
            
            //$arrDecided = $request->input("ARRDECIDED") ?  $request->input("ARRDECIDED") : [];

            $profile_bantru = (int)$request->input("PROFILEBANTRU");            
            $profile_KM = $request->input("PROFILEKM") ? $request->input("PROFILEKM") : 0;
            $profile_giaothong = $request->input("PROFILEGIAOTHONG");

            $bool = TRUE;
            //$currentdate = Carbon::now('Asia/Ho_Chi_Minh');
            $currentuser_id = Auth::user()->id;
            $currentdate = Carbon::now('Asia/Ho_Chi_Minh');

            $strYear = substr((string)$profile_year, 0, 4);
            $year_his = (int)$strYear;
            $strYear = (string)$year_his . "-" . (string)($year_his + 1);

            $getbyProfile_Code = DB::table('qlhs_profile')
                ->where('profile_name', '=', $profile_name)
                ->where('profile_birthday', '=', $profile_birthday)
                ->where('profile_nationals_id', '=', $profile_nationals_id)
                ->where('profile_site_id1', '=', $profile_site_id1)
                ->where('profile_site_id2', '=', $profile_site_id2)
                ->where('profile_site_id3', '=', $profile_site_id3)
                ->where('profile_household', '=', $profile_household)
                ->where('profile_parentname', '=', $profile_parentname)
                ->where('profile_school_id', '=', $profile_school_id)
                ->where('profile_class_id', '=', $profile_class_id)->count();

            if ($getbyProfile_Code > 0) {
                $results['error'] = 'Học sinh đã tồn tại, vui lòng nhập lại!';
            }
            else{
                //Insert Hồ sơ học sinh

                $hosohocsinh = new DanhMucHoSoHocSinh();
                // $hosohocsinh->profile_code = $profile_code;
                $hosohocsinh->profile_name = $profile_name;
                $hosohocsinh->profile_birthday = $profile_birthday;
                $hosohocsinh->profile_nationals_id = $profile_nationals_id;
                $hosohocsinh->profile_site_id1 = $profile_site_id1;
                $hosohocsinh->profile_site_id2 = $profile_site_id2;
                $hosohocsinh->profile_site_id3 = $profile_site_id3;
                $hosohocsinh->profile_household = $profile_household;
                $hosohocsinh->profile_parentname = $profile_parentname;
                $hosohocsinh->profile_year = $profile_year;
                $hosohocsinh->profile_school_id = $profile_school_id;
                $hosohocsinh->profile_class_id = $profile_class_id;
                $hosohocsinh->profile_status = $profile_status;
                $hosohocsinh->profile_statusNQ57 = $profile_statusNQ57;
                $hosohocsinh->profile_leaveschool_date = $profile_leaveschool_date;
                $hosohocsinh->profile_bantru = $profile_bantru;
                $hosohocsinh->profile_create_userid = $currentuser_id;
                $hosohocsinh->created_at = $currentdate;
                $hosohocsinh->profile_update_userid = $currentuser_id;
                $hosohocsinh->updated_at = $currentdate;
                $hosohocsinh->profile_bantru = $profile_bantru;
                $hosohocsinh->profile_km = $profile_KM;
                $hosohocsinh->profile_giaothong = $profile_giaothong;
                $hosohocsinh->save();

                $insertGetIdProfile = $hosohocsinh->profile_id;

                // $insertGetIdProfile = DB::table('qlhs_profile')->insertGetId(
                // [ 'profile_code' => $profile_code, 'profile_name' => $profile_name, 'profile_birthday' => $profile_birthday, 'profile_nationals_id' => $profile_nationals_id, 'profile_site_id1' => $profile_site_id1, 'profile_site_id2' => $profile_site_id2, 'profile_site_id3' => $profile_site_id3, 'profile_household' => $profile_household, 'profile_parentname' => $profile_parentname, 'profile_year' => $profile_year, 'profile_class_id' => $profile_class_id, 'profile_school_id' => $profile_school_id, 'profile_status' => $profile_status, 'profile_leaveschool_date' => $profile_leaveschool_date, 'profile_create_userid' => $currentuser_id, 'profile_update_userid' => $currentuser_id, 'created_at' => $currentdate, 'updated_at' => $currentdate, 'profile_bantru' => $profile_bantru, 'profile_km' => $profile_KM, 'profile_giaothong' => $profile_giaothong ]
                //  );

                if ($insertGetIdProfile > 0) {
                    // Lấy thông tin lớp của học sinh
                      $getLevelClass = DB::table('qlhs_level')->join('qlhs_class', 'qlhs_class.class_level_id', '=', 'qlhs_level.level_id')->where('qlhs_class.class_id', '=', $profile_class_id)->select('level_level', 'level_next', 'level_next_1', 'level_next_2','level_name','class_old','class_new')->first();
                    //Insert Hoso_History
                    $insert_history = DB::table('qlhs_profile_history')->insert(['history_class_id' => $profile_class_id, 'history_profile_id' => $insertGetIdProfile, 'history_year' => $strYear, 'history_upto_level' => 0,'level_old' => '','level_new' => $getLevelClass->class_new,'level_cur' => $getLevelClass->level_name,'history_update_user_id' => Auth::user()->id,'history_update_date' => $currentdate]);

                    //Insert Hosohs_Subject
                    
                    // if (count($arrSubjectID) > 0) {
                    //     foreach ($arrSubjectID as $value) {
                    //         $subject_id = (int)$value['value'];
                    //         $insert_hosohs_subject = DB::table('qlhs_profile_subject')->insert(['profile_subject_profile_id' => $insertGetIdProfile, 'profile_subject_subject_id' => $subject_id, 'profile_subject_create_userid' => $currentuser_id, 'profile_subject_update_userid' => $currentuser_id, 'profile_subject_createdate' => $currentdate, 'profile_subject_updatedate' => $currentdate]);
                    //         if ($insert_hosohs_subject == 0) {
                    //             $bool = FALSE;
                    //             break;
                    //         }
                    //     }
                    // }
                    $date  = Carbon::now()->timestamp;
                    if (count($arrSubjectID) > 0) {                                        
                        foreach ($arrSubjectID as $value) {
                            $subject_id = (int)$value;
                            $insert_hosohs_subject = DB::table('qlhs_profile_subject')->insert(['profile_subject_profile_id' => $insertGetIdProfile, 'profile_subject_subject_id' => $subject_id, 'profile_subject_create_userid' => $currentuser_id, 'profile_subject_update_userid' => $currentuser_id, 'profile_subject_createdate' => $currentdate, 'profile_subject_updatedate' => $currentdate,
                                'profile_start_time' => $date,
                                'start_year' => substr((string)$profile_year, 0, 4)]);
                            if ($insert_hosohs_subject <= 0) {
                                $bool = FALSE;
                                break;
                            }
                        }
                    }
                    if (count($arrDecided) > 0) {
                        $num = (int)$request->input("decided_number");
                        $dir = storage_path().'/HOSO/QUYETDINH';
                        $filename_attach  = "";
                        $getTime = time();
                        for ($i=0; $i < $num; $i++) { 
                            // format date
                            $files = $request->file('file_'.$i);
                            $filename_attach  = $request->input('fileold_'.$i);
                            $decided_confirmdate = $request->input('confirmdate_'.$i)!="" ? Carbon::parse($request->input('confirmdate_'.$i)) : null;//date('Y-m-d', strtotime(str_replace('-', '/', $files)));
                            if(trim($files) != ""){
                                 $filename_attach = 'QD_'.$insertGetIdProfile.'_'.Auth::user()->id.'_'.$getTime.'_'.$files->getClientOriginalName();
                                 if(file_exists($dir.'/'. $filename_attach)){
                                        $files->move($dir, $filename_attach.'-'.$getTime); 
                                        //File::delete($dir.'/'. $filename_attach); 
                                 }else{
                                        $files->move($dir, $filename_attach);   
                                 }
                            }
                            $insert_diceded = DB::table('qlhs_decided')->insert(['decided_type' => $request->input('decided_type_'.$i), 'decided_profile_id' => $insertGetIdProfile, 'decided_code' => $request->input('code_'.$i), 'decided_name' => $request->input('name_'.$i), 'decided_number' => $request->input('number_'.$i), 'decided_confirmation' =>  $request->input('confirmation_'.$i), 'decided_confirmdate' => $decided_confirmdate, 'decided_filename' => $filename_attach, 'decided_user_id' => $currentuser_id, 'decided_createdate' => $currentdate, 'decided_updatedate' => $currentdate]);
                    }
                    //Insert Decided
                    // if (count($arrDecided) > 0) {

                    //     foreach ($arrDecided as $value) {

                    //         $getDecided = DB::table('qlhs_decided')->where('decided_code', '=', $value['code'])->get();

                    //         if (count($getDecided) > 0) {
                    //             $results['error'] = 'Mã quyết định đã tồn tại, mời bạn nhập mã khác!';
                    //             return $results;
                    //         }
                    //         else {

                    //             // $files =  $value;

                    //             // $filename_attach = "";
                    //             // if(trim($files) != "") {
                    //             //     $filenames = 'File-'.$currentuser_id.'-'.$files->getClientOriginalName();
                    //             //     $filename_attach = $filenames;
                    //             // }

                    //             // $time = time();

                    //             // $dir = storage_path().'/files/Profiles';
                    //             // if(trim($files) != "") {
                    //             //     if(file_exists($dir.'/'. $filename_attach)) {
                    //             //         $files->move($dir, $filename_attach.'-'.$time);
                    //             //     }else {
                    //             //         $files->move($dir, $filename_attach);   
                    //             //     }
                    //             // }

                    //             $decided_confirmdate = Carbon::parse($value['confirmdate']);//date('Y-m-d', strtotime(str_replace('-', '/', $value['confirmdate'])));

                    //             $insert_diceded = DB::table('qlhs_decided')->insert(['decided_type' => $value['decided_type'], 'decided_profile_id' => $insertGetIdProfile, 'decided_code' => $value['code'], 'decided_name' => $value['name'], 'decided_number' => $value['number'], 'decided_confirmation' => $value['confirmation'], 'decided_confirmdate' => $decided_confirmdate, 'decided_filename' => $value['filename'], 'decided_user_id' => $currentuser_id, 'decided_createdate' => $currentdate, 'decided_updatedate' => $currentdate]);
                    //             if ($insert_diceded == 0) {
                    //                 $bool = FALSE;
                    //                 break;
                    //             }
                    //         }
                    //     }
                     }

                    if ($insert_history > 0 && $bool == TRUE) {
                        $results['success'] = 'Thêm mới thành công!';

                        $this->tonghop($year_his, $insertGetIdProfile, $profile_school_id, 0);
                    }
                    else {
                        $deleteProfile = DB::table('qlhs_profile')->where('profile_id', '=', $insertGetIdProfile)->delete();
                        $deleteProfileHis = DB::table('qlhs_profile_history')->where('history_profile_id', '=', $insertGetIdProfile)->delete();
                        $deleteProfileSub = DB::table('qlhs_profile_subject')->where('profile_subject_profile_id', '=', $insertGetIdProfile)->delete();
                        $deleteProfileDec = DB::table('qlhs_decided')->where('decided_profile_id', '=', $insertGetIdProfile)->delete();
                        $results['error'] = 'Thêm mới thất bại!';
                    }
                }
            }
            
            return $results;

            // $arrDecided = $request->input("ARRDECIDED");
            // return $arrDecided;
        } catch (Exception $e) {
            return $e;
        }
    }
    public function download_quyetdinh($id){
        $data = DB::table('qlhs_decided')->where('decided_id','=',$id)->select('decided_filename')->first(); 
        $dir = storage_path().'/HOSO/QUYETDINH/'.$data->decided_filename;
        return response()->download($dir,$data->decided_filename);
    }
    public function updateHoSoHocSinh(Request $request)
    {
        $results = [];
        try {
           //  $file = [];
           // // return count($file).'==';
           //  $num = (int)$request->input("decided_number");
           //  for ($i=0; $i < $num; $i++) { 
           //      $file['files'][] = $request->file('file_'.$i);
           //      $file['code'][] = $request->input('decided_type_'.$i);
           //      $file['name'][] = $request->input('name_'.$i);
           //      $file['confirmation'][] = $request->input('confirmation_'.$i);
           //      $file['confirmdate'][] = $request->input('confirmdate_'.$i);
           //  }
           //  return $file['files'][0]->getClientOriginalName();
            $profile_id = $request->input("PROFILEID");
            $profile_name = $request->input("PROFILENAME");
            $profile_name = trim($profile_name);
            $profile_birthday = $request->input("PROFILEBIRTHDAY");
            $profile_birthday = Carbon::parse($profile_birthday);//date('Y-m-d', strtotime(str_replace('-', '/', $profile_birthday)));//date("Y-m-d", strtotime($profile_birthday));//
            $profile_nationals_id = $request->input("PROFILENATIONALID");
            $profile_site_id1 = $request->input("PROFILESITE1");
            $profile_site_id2 = $request->input("PROFILESITE2");
            $profile_site_id3 = $request->input("PROFILESITE3") ? $request->input("PROFILESITE3") : null;
            $profile_household = $request->input("PROFILEHOUSEHOLD") ? $request->input("PROFILEHOUSEHOLD") : "";
            $profile_parentname = $request->input("PROFILEPARENTNAME");
            $profile_parentname = trim($profile_parentname);
            $profile_year = $request->input("PROFILEYEAR");
            $strProfile_year = "01" . "-" . (string)$profile_year;
            $profile_year = Carbon::parse($strProfile_year);//date('Y-m-d', strtotime(str_replace('-', '/', $strProfile_year)));//date("Y-m-d", strtotime($strProfile_year));//
            $profile_statusNQ57  = $request->input("PROFILESTATUSNQ57");

            $profile_school_id = $request->input("PROFILESCHOOLID");
            $profile_class_id = $request->input("PROFILECLASSID");
            $profile_status = $request->input("PROFILESTATUS");
            if((int)$profile_status == 1){
                $profile_leaveschool_date = $request->input("PROFILELEAVESCHOOLDATE");
                if (!is_null($profile_leaveschool_date) && !empty($profile_leaveschool_date)) {
                    $profile_leaveschool_date = Carbon::parse($profile_leaveschool_date);
                }
                else { $profile_leaveschool_date = null; }
            }else {
                $profile_leaveschool_date = null;
            }
            //$profile_leaveschool_date = Carbon::parse(null);            

            $arrSubjectID = array();

            $arrSubjectID = explode('-',$request->input("ARRSUBJECTID"));
            //return $arrSubjectID;
            $arrDecided = array();
            $arrDecided = $request->input("ARRDECIDED");
            //return $arrDecided;
            $profile_bantru = (int)$request->input("PROFILEBANTRU");
            $profile_KM = $request->input("PROFILEKM") ? $request->input("PROFILEKM") : 0;
            $profile_giaothong = $request->input("PROFILEGIAOTHONG");
            $profile_currentYear = (int)$request->input("CURRENTYEAR");

            $bool = TRUE;

            $currentuser_id = Auth::user()->id;
            $currentdate = Carbon::now('Asia/Ho_Chi_Minh');

            $strYear = substr((string)$profile_year, 0, 4);
            $year_his = (int)$strYear;
            $strYear = (string)$year_his . "-" . (string)($year_his + 1);


            if ($profile_id > 0) {
                $hosohocsinh =  DanhMucHoSoHocSinh::find($profile_id);
                $hosohocsinh->profile_name = $profile_name;
                $hosohocsinh->profile_birthday = $profile_birthday;
                $hosohocsinh->profile_nationals_id = $profile_nationals_id;
                $hosohocsinh->profile_site_id1 = $profile_site_id1;
                $hosohocsinh->profile_site_id2 = $profile_site_id2;
                $hosohocsinh->profile_site_id3 = $profile_site_id3;
                $hosohocsinh->profile_household = $profile_household;
                $hosohocsinh->profile_parentname = $profile_parentname;
                $hosohocsinh->profile_year = $profile_year;
                $hosohocsinh->profile_school_id = $profile_school_id;
                $hosohocsinh->profile_class_id = $profile_class_id;
                $hosohocsinh->profile_status = $profile_status;
                $hosohocsinh->profile_statusNQ57 = $profile_statusNQ57;
                $hosohocsinh->profile_leaveschool_date = $profile_leaveschool_date;
                $hosohocsinh->profile_bantru = $profile_bantru;
                //$hosohocsinh->profile_create_userid = $currentuser_id;
                //$hosohocsinh->created_at = $currentdate;
                $hosohocsinh->profile_update_userid = $currentuser_id;
                $hosohocsinh->updated_at = $currentdate;
                $hosohocsinh->profile_bantru = $profile_bantru;
                $hosohocsinh->profile_km = $profile_KM;
                $hosohocsinh->profile_giaothong = $profile_giaothong;
                $hosohocsinh->save();


                // if ($profile_status == 1) {
                //     $profile_leaveschool_date = $request->input("PROFILELEAVESCHOOLDATE");
                //     $profile_leaveschool_date = Carbon::parse($profile_leaveschool_date);//date('Y-m-d', strtotime(str_replace('-', '/', $profile_leaveschool_date)));
                //     $updateProfile = DB::update("update qlhs_profile set profile_name = '$profile_name', profile_birthday = '$profile_birthday', profile_nationals_id = '$profile_nationals_id', profile_site_id1 = '$profile_site_id1', profile_site_id2 = '$profile_site_id2', profile_site_id3 = $profile_site_id3, profile_household = '$profile_household', profile_parentname = '$profile_parentname', profile_year = '$profile_year', profile_class_id = '$profile_class_id', profile_school_id = '$profile_school_id', profile_status = '$profile_status', profile_leaveschool_date = '$profile_leaveschool_date', profile_update_userid = '$currentuser_id', updated_at = '$currentdate', profile_bantru = '$profile_bantru', profile_km = '$profile_KM', profile_giaothong = '$profile_giaothong' where profile_id = '$profile_id'");
                // }
                // else {
                //     $updateProfile = DB::update("update qlhs_profile set profile_name = '$profile_name', profile_birthday = '$profile_birthday', profile_nationals_id = '$profile_nationals_id', profile_site_id1 = '$profile_site_id1', profile_site_id2 = '$profile_site_id2', profile_site_id3 = $profile_site_id3, profile_household = '$profile_household', profile_parentname = '$profile_parentname', profile_year = '$profile_year', profile_class_id = '$profile_class_id', profile_school_id = '$profile_school_id', profile_status = '$profile_status', profile_leaveschool_date = null, profile_update_userid = '$currentuser_id', updated_at = '$currentdate', profile_bantru = '$profile_bantru', profile_km = '$profile_KM', profile_giaothong = '$profile_giaothong' where profile_id = '$profile_id'");
                // }
                if($profile_status === 1){
                     $getLevelClass = DB::table('qlhs_level')->join('qlhs_class', 'qlhs_class.class_level_id', '=', 'qlhs_level.level_id')->where('qlhs_class.class_id', '=', $profile_class_id)->select('level_level', 'level_next', 'level_next_1', 'level_next_2','level_name','class_old','class_new')->first();
                    $insert = DB::table('qlhs_profile_history')->insert(
                        ['history_profile_id' => $profile_id,'history_year' => $strYear,'history_update_date' => $currentdate, 'history_update_user_id' => $currentuser_id,'level_old' => $getLevelClass->level_name,'history_class_id' => $profile_class_id,'level_cur' => '','level_new' => '','history_upto_level' => 4]
                                                                    );
                }
                   // $getLevelHis = DB::table('qlhs_profile_history')->where('history_profile_id', '=', $profile_id)->select(DB::raw('MAX(history_upto_level) as history_upto_level'))->first();
                    $selectCountHis = DB::table('qlhs_profile_history')->where('history_profile_id','=',$profile_id)->count();
                if($selectCountHis == 1){
                     // Lấy thông tin lớp của học sinh
                      $getLevelClass = DB::table('qlhs_level')->join('qlhs_class', 'qlhs_class.class_level_id', '=', 'qlhs_level.level_id')->where('qlhs_class.class_id', '=', $profile_class_id)->select('level_level', 'level_next', 'level_next_1', 'level_next_2','level_name','class_old','class_new')->first();
                    //Insert Hoso_History
                   // $insert_history = DB::table('qlhs_profile_history')->insert(['history_class_id' => $profile_class_id, 'history_profile_id' => $insertGetIdProfile, 'history_year' => $strYear, 'history_upto_level' => 0,'level_old' => null,'level_new' => '$getLevelClass->class_new','level_cur' => '$getLevelClass->class_new','history_update_user_id' => Auth::user()->id,'history_update_date' => '$currentdate']);

                    $updateProfileHis = DB::update("update qlhs_profile_history set history_year = '$strYear', history_class_id = '$profile_class_id', history_update_user_id = '$currentuser_id',history_update_date = '$currentdate',level_old = '' ,level_new = '$getLevelClass->class_new',level_cur = '$getLevelClass->level_name'  where history_profile_id = '$profile_id' ");
                }
                    // if (count($arrSubjectID) > 0) {
                    //     //Xóa hosohs Subject cũ
                    //     $deleteProfileSub = DB::table('qlhs_profile_subject')->where('profile_subject_profile_id', '=', $profile_id)->delete();//DB::delete('delete from qlhs_profile_subject where profile_subject_profile_id = ?', array($this->profileID));
                                        
                    //     foreach ($arrSubjectID as $value) {
                    //         $subject_id = (int)$value;
                    //         $insert_hosohs_subject = DB::table('qlhs_profile_subject')->insert(['profile_subject_profile_id' => $profile_id, 'profile_subject_subject_id' => $subject_id, 'profile_subject_create_userid' => $currentuser_id, 'profile_subject_update_userid' => $currentuser_id, 'profile_subject_createdate' => $currentdate, 'profile_subject_updatedate' => $currentdate]);
                    //         if ($insert_hosohs_subject <= 0) {
                    //             $bool = FALSE;
                    //             break;
                    //         }
                    //     }
                    // }

                    if (count($arrDecided) > 0) {
                        //Xóa quyết định cũ
                        $deleteProfileDec = DB::table('qlhs_decided')->where('decided_profile_id', '=', $profile_id)->delete();//DB::delete('delete from qlhs_decided where decided_profile_id = ?', array($this->profileID));
                    // /$file = [];
                   // return count($file).'==';
                    $num = (int)$request->input("decided_number");
                    $dir = storage_path().'/HOSO/QUYETDINH';
                    $filename_attach  = "";
                    $getTime = time();
                    for ($i=0; $i < $num; $i++) { 
                        // format date
                        $files = $request->file('file_'.$i);
                        $filename_attach  = $request->input('fileold_'.$i);
                        $decided_confirmdate = $request->input('confirmdate_'.$i)!="" ? Carbon::parse($request->input('confirmdate_'.$i)) : null;//date('Y-m-d', strtotime(str_replace('-', '/', $files)));
                        if(trim($files) != ""){
                             $filename_attach = 'QD_'.$profile_id.'_'.Auth::user()->id.'_'.$getTime.'_'.$files->getClientOriginalName();
                             if(file_exists($dir.'/'. $filename_attach)){
                                    $files->move($dir, $filename_attach.'-'.$getTime); 
                                    //File::delete($dir.'/'. $filename_attach); 
                             }else{
                                    $files->move($dir, $filename_attach);   
                             }
                        }
                        $insert_diceded = DB::table('qlhs_decided')->insert(['decided_type' => $request->input('decided_type_'.$i), 'decided_profile_id' => $profile_id, 'decided_code' => $request->input('code_'.$i), 'decided_name' => $request->input('name_'.$i), 'decided_number' => $request->input('number_'.$i), 'decided_confirmation' =>  $request->input('confirmation_'.$i), 'decided_confirmdate' => $decided_confirmdate, 'decided_filename' => $filename_attach, 'decided_user_id' => $currentuser_id, 'decided_createdate' => $currentdate, 'decided_updatedate' => $currentdate,'decided_update_id' => $currentuser_id]);

                       // $file['files'][] = $request->file('file_'.$i);
                        //$file['code'][] = $request->input('code_'.$i);
                        //$file['name'][] = $request->input('v_number'.$i);
                       // $file['confirmation'][] = $request->input('confirmation_'.$i);
                        //$file['confirmdate'][] = $request->input('confirmdate_'.$i);
                    }
                        // foreach ($arrDecided as $value) {

                        //     $getDecided = DB::table('qlhs_decided')->where('decided_code', '=', $value['code'])->get();

                        //     if (count($getDecided) > 0) {
                        //         $results['error'] = 'Mã quyết định đã tồn tại, mời bạn nhập mã khác!';
                        //         $bool = FALSE;
                        //         break;
                        //     }

                        //     $decided_confirmdate = date('Y-m-d', strtotime(str_replace('-', '/', $value['confirmdate'])));

                             // $insert_diceded = DB::table('qlhs_decided')->insert(['decided_type' => $value['decided_type'], 'decided_profile_id' => $profile_id, 'decided_code' => $value['code'], 'decided_name' => $value['name'], 'decided_number' => $value['number'], 'decided_confirmation' => $value['confirmation'], 'decided_confirmdate' => $decided_confirmdate, 'decided_filename' => $value['filename'], 'decided_user_id' => $currentuser_id, 'decided_createdate' => $currentdate, 'decided_updatedate' => $currentdate]);
                        //     if ($insert_diceded = 0) {
                        //         $bool = FALSE;
                        //         break;
                        //     }
                        // }
                    }
                    //return $updateProfileHis.'==';
                    if ( $bool == TRUE) {
                        $results['success'] = "Sửa hồ sơ học sinh thành công!";

                        
                    }
                    else { $results['error'] = 'Sửa hồ sơ học sinh thất bại!'; }
                }

            return $results;
        } catch (Exception $e) {
            return $e;
        }
    }
    public function viewHistory(Request $request){
         $results = [];
            $profile_id = $request->input("PROFILEID");
            $results['data'] = DB::table('qlhs_profile_history')->join('qlhs_class','class_id','=','history_class_id')->leftJoin('users','history_update_user_id','=','id')->where('history_profile_id','=',$profile_id)->select('class_name','history_year','history_update_date','username','history_upto_level')->get();
            return $results;

    }
    public function deleteHoSoHocSinh(Request $request)
    {
        $results = [];
        try {
            $profile_id = $request->input("PROFILEID");

            if ($profile_id > 0) {

            $deleteProfile = DB::table('qlhs_profile')->where('profile_id', '=', $profile_id)->delete();

                $deleteProfileSub = DB::table('qlhs_profile_subject')->where('profile_subject_profile_id', '=', $profile_id)->delete();

                $deleteProfileHis = DB::table('qlhs_profile_history')->where('history_profile_id', '=', $profile_id)->delete();

                $deleteTongHopCheDo = DB::table('qlhs_tonghopchedo')->where('qlhs_thcd_profile_id', '=', $profile_id)->delete();

                $selectFileName = DB::table('qlhs_decided')->where('decided_profile_id', '=', $profile_id)->get();
                $dir = storage_path().'/HOSO/QUYETDINH';
                foreach ($selectFileName as $key => $value) {
                    
                    if(file_exists($dir.'/'. $value->decided_filename)){
                        //return $dir.'/'. $value->decided_filename;
                        File::delete($dir.'/'. $value->decided_filename); 
                    }
                }

                $deleteProfileDec = DB::table('qlhs_decided')->where('decided_profile_id', '=', $profile_id)->delete();//DB::delete('delete from qlhs_decided where decided_profile_id = ?', array($this->profileID));

                // ($deleteProfile > 0) {
                    $results['success'] = 'Xóa hồ sơ học sinh thành công!';
               // }
               // else {
                   
              //  }
            }

            return $results;
        } catch (Exception $e) {
             $results['error'] = 'Xóa hồ sơ học sinh thất bại!'.$e;
        }
    }
    
    public function getHoSoHocSinhbyID(Request $request)
    {
        try {
            $profile_id = $request->input("PROFILEID");
            $arrProfile = array();
            if ($profile_id > 0) {
                $arrProfile['objProfile'] = DB::table('qlhs_profile')
                ->join('qlhs_profile_history','profile_id','=','history_profile_id')
                ->where('profile_id','=',$profile_id)
                ->groupBy('profile_id','profile_code','profile_name','profile_birthday','profile_nationals_id','profile_site_id1','profile_site_id2','profile_site_id3','profile_household','profile_parentname','profile_guardian','profile_year','profile_school_id','profile_class_id','profile_status','profile_statusNQ57','profile_leaveschool_date','profile_rewrite','profile_md5','profile_create_userid','created_at','profile_update_userid','updated_at','profile_bantru','profile_km','profile_giaothong','profile_revert')
                ->select('profile_id','profile_code','profile_name','profile_birthday','profile_nationals_id','profile_site_id1','profile_site_id2','profile_site_id3','profile_household','profile_parentname','profile_guardian','profile_year','profile_school_id','profile_class_id','profile_status','profile_statusNQ57','profile_leaveschool_date','profile_rewrite','profile_md5','profile_create_userid','created_at','profile_update_userid','updated_at','profile_bantru','profile_km','profile_giaothong','profile_revert',DB::raw('count(profile_id) as history'))->get();
                $arrProfile['arrProfileSub'] = DB::select('select profile_subject_subject_id from qlhs_profile_subject where profile_subject_profile_id = ?', array($profile_id));
                $arrProfile['arrProfileDec'] = DB::select('select * from qlhs_decided where decided_profile_id = ?', array($profile_id));
                //$arrProfile['History'] = DB::table('qlhs_profile_history')->where('history_profile_id','=',$profile_id)->count();
            }
            return $arrProfile;
        } catch (Exception $e) {
            
        }
    }

    public function getProfilePopupUpto(Request $request)
    {
        $results = [];
        try {
            $start = $request->input('start');
            $limit = $request->input('limit');
            $schools_id = $request->input('PROFILESCHOOL');
            $class_id = $request->input('PROFILECLASS');
            $year = $request->input('PROFILEYEAR');
            $data = null;

            if ($schools_id > 0 && $class_id > 0 && (!is_null($year) && !empty($year))) {
                $data = DB::table('qlhs_profile')
                ->join('qlhs_nationals', 'qlhs_nationals.nationals_id', '=', 'qlhs_profile.profile_nationals_id')
                ->join('qlhs_schools', 'qlhs_schools.schools_id', '=', 'qlhs_profile.profile_school_id')
                ->join('qlhs_class', 'qlhs_class.class_id', '=', 'qlhs_profile.profile_class_id')
                ->join('qlhs_level', 'qlhs_level.level_id', '=', 'qlhs_class.class_level_id')
                ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', 'qlhs_profile.profile_id')
                ->where('qlhs_profile.profile_school_id', '=', $schools_id)
                ->where('qlhs_profile_history.history_class_id', '=', $class_id)
                ->where('qlhs_profile_history.history_year', '=', $year);
            }

           // $results['startRecord'] = ($start);
           // $results['numRows'] = $limit;
           // $results['totalRows'] = $data->count();

            // 'qlhs_profile.profile_code', 
            $results['data'] = $data->select('qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_profile.profile_household', 'qlhs_profile.profile_parentname', 'qlhs_nationals.nationals_id', 'qlhs_nationals.nationals_name', 'qlhs_class.class_id', 'qlhs_class.class_name', 'qlhs_schools.schools_id', 'qlhs_schools.schools_name', 'qlhs_level.level_id', 'qlhs_level.level_name', 'qlhs_profile_history.history_year')->orderBy('qlhs_profile.profile_id', 'desc')->get();

            //->skip($start * $limit)->take($limit)
            return $results;
        } catch (Exception $e) {
            return $e;
        }   
    }



    public function getYearHistory(Request $request){
        try {
            $class_id = $request->input('PROFILECLASS');
            $class = [];
            $class['levelClass'] = DB::table('qlhs_class')->leftJoin('qlhs_level','level_id','=','class_level_id')->where('class_id','=',$class_id)->select('level_level','class_schools_id')->get();
            $class['year_his'] = DB::table('qlhs_profile_history')->where('history_class_id', '=', $class_id)->select('history_year')->DISTINCT()->get();
    
            return $class;
        } catch (Exception $e) {
            
        }
    }

    public function uptoProfile(Request $request){
        $results = [];
        try {
            $currentdate = Carbon::now('Asia/Ho_Chi_Minh');
            $dateOutProfile =  $request->input('DATEOUTPROFIEL') != "" ? Carbon::parse($request->input('DATEOUTPROFIEL')) : $currentdate;
            $classBack = $request->input('CLASSBACK');
            $ClassNext = $request->input('CLASSNEXT');
            $arrProfileID = $request->input('ARRPROFILEID');
            $classID = $request->input('CLASSID');
            $strYear = $request->input('YEAR');
            $classID_next = $request->input('CLASSIDNEXT');

            $currentuser_id = Auth::user()->id;
            
            $strYear = substr($strYear, 5);
            $year = (int)$strYear;
            $strYear = (string)$year . "-" . (string)($year + 1);
            $bool = TRUE;
            $countLevelHis;
            // Có học sinh và có lớp hiện tại
        
            if(count($arrProfileID) > 0 && $classID > 0){

                //Lấy trường id
                    $getSchoolId = DB::table('qlhs_class')->where('qlhs_class.class_id', '=', $classID)->select('class_schools_id')->first();

                //Lấy level lớp cũ
                    $getLevelClass = DB::table('qlhs_level')->join('qlhs_class', 'qlhs_class.class_level_id', '=', 'qlhs_level.level_id')->where('qlhs_class.class_id', '=', $classID)->select('level_level', 'level_next', 'level_next_1', 'level_next_2','level_name','class_old','class_new')->first();
                // Lấy level lớp mới
                    $getLevelClassNew = DB::table('qlhs_level')->join('qlhs_class', 'qlhs_class.class_level_id', '=', 'qlhs_level.level_id')->where('qlhs_class.class_id', '=', $ClassNext)->select('level_level', 'level_next', 'level_next_1', 'level_next_2','level_name','class_old','class_new')->first();
                // Lấy level lớp học lại hoặc lớp chuyển
                    $getLevelClassBack = DB::table('qlhs_level')->join('qlhs_class', 'qlhs_class.class_level_id', '=', 'qlhs_level.level_id')->where('qlhs_class.class_id', '=', $classBack)->select('level_level', 'level_next', 'level_next_1', 'level_next_2','level_name','class_old','class_new')->first();
                if((int)$classID_next === 1){// Lên lớp   
                    //Trường hợp lớp tiếp theo của học sinh đó là lớp cuối thì cho ra trường
                   // return $getLevelClass->level_next_2."---";

                    if ($getLevelClass->level_next_2 == 0) {
                        $leaveSchoolYear = substr($strYear, 5);
                        $leaveSchoolYear = $leaveSchoolYear . '-09-01';
                        //cập nhật từng học sinh
                        foreach ($arrProfileID as $profileID) {
                            $checkNghiHoc = DB::table('qlhs_profile')->where('profile_id','=',$profileID)->select('profile_status')->first();
                            if((int)$checkNghiHoc->profile_status != 1){//Chỉ xử lý khi học sinh đấy chưa nghỉ học
                                //Kết thúc nghỉ học trong hồ sơ - revert = 1 len lop
                            
                                $updateProfile = DB::update("update qlhs_profile set profile_status = 1,profile_revert = 1, profile_leaveschool_date = '$leaveSchoolYear',profile_update_userid = '$currentuser_id',updated_at = '$currentdate' where profile_id = '$profileID'");
                                    //Chuyển trạng thái thành công 
                                if($updateProfile > 0){
                                        //Kiểm tra học sinh đã có lịch sử năm học mới chưa?
                                    $countLevelHis = DB::table('qlhs_profile_history')->where('history_profile_id', '=', $profileID)->where('history_year','=',$strYear);
                                    if($countLevelHis->count()==0){ //Chưa có lịch sử năm học mới cho học sinh cuối cấp
                                        // khóa trạng thái cũ của lịch sử
                                       // $lock = DB::update("update qlhs_profile_history set history_upto_level = 0 where  history_profile_id = '$profileID'");  
                                            //Cập nhật lịch sử nếu ra trường thên bản ghi năm học mới level cũ - không có level hiện tại và năm mới

                                        $insert = DB::table('qlhs_profile_history')->insert(
                                                            ['history_profile_id' => $profileID,'history_year' => $strYear,'history_update_date' => $currentdate, 'history_update_user_id' => $currentuser_id,'level_old' => $getLevelClass->level_name,'history_class_id' => $classID,'level_cur' => '','level_new' => '','history_upto_level' => 1]
                                                        );
                                        

                                        $getMaxYear = DB::table('qlhs_tonghopchedo')->where('qlhs_thcd_profile_id', '=', $profileID)
                                            ->select('qlhs_thcd_id', DB::raw('MAX(qlhs_thcd_nam) as nam'))->groupBy('qlhs_thcd_id')->first();

                                        // return $getMaxYear->nam;

                                        $this->tonghop($getMaxYear->nam, $profileID, $getSchoolId->class_schools_id, $getMaxYear->qlhs_thcd_id);
                                    }else{// Có lịch sử cho học sinh cuối cấp
                                        // Lấy ID của his tồn tại update lại lớp
                                        $check = $countLevelHis->select('history_id')->first();
                                        // Cập nhật kết thúc nghỉ học cho năm học mới
                                        $insert = DB::update("update qlhs_profile_history set history_class_id = '$classID',history_update_user_id = '$currentuser_id',history_upto_level = 1,history_update_date = '$currentdate',level_old = '$getLevelClass->level_name',level_cur = '',level_new = '' where history_id = '$check->history_id' and history_profile_id = '$profileID'");  
                                    }    
                                }else{ // insert không thành công thì để nguyên
                                    $updateProfile = DB::update("update qlhs_profile set profile_status = 0,profile_revert = 0,history_update_user_id = '$currentuser_id',history_update_date = '$currentdate', profile_leaveschool_date = null,profile_update_userid = '$currentuser_id',updated_at = '$currentdate' where profile_id = '$profileID'");
                                }
                            }
                        }

                        if ($bool) {
                            $results['success'] = "Danh sách học sinh đang học là lớp cuối cấp. Sửa trạng thái và lớp sẽ nghỉ học vào ngày ".$leaveSchoolYear."!";
                        }
                        else { $results['error'] = "Thực hiện danh sách học sinh cuối cấp lỗi đường truyền!Xin mời thử lại"; }

                       // return $results;
                    }else{ // Trường hợp không phải là lớp cuối cấp

                        foreach ($arrProfileID as $value) {
                                   $checkNghiHoc = DB::table('qlhs_profile')->where('profile_id','=',$value)->select('profile_status')->first();
                            if((int)$checkNghiHoc->profile_status != 1){//Chỉ xử lý khi học sinh đấy chưa nghỉ học
                                        // Đổi lớp cho học sinh và revert = 1 lên lớp
                                $updateProfile = DB::update("update qlhs_profile set profile_revert = 1, profile_class_id = '$ClassNext',profile_update_userid = '$currentuser_id',updated_at = '$currentdate' where profile_id = '$value'");
                                               
                                if ($updateProfile > 0) {
                                                 //Kiểm tra học sinh đã có lịch sử năm học mới chưa?
                                    $countLevelHis = DB::table('qlhs_profile_history')->where('history_profile_id', '=', $value)->where('history_year','=',$strYear);
                                            // Nếu chưa tồn tại thì thêm bản ghi lịch sử mới
                                    if($countLevelHis->count()==0){
                                        // khóa trạng thái cũ của lịch sử
                                     //   $lock = DB::update("update qlhs_profile_history set history_upto_level = 0 where  history_profile_id = '$value'");  

                                        $insert = DB::table('qlhs_profile_history')->insert(
                                                            ['history_profile_id' => $value,'history_year' => $strYear,'history_update_date' => $currentdate, 'history_update_user_id' => $currentuser_id,'level_old' => $getLevelClassNew->class_old,'history_class_id' => $ClassNext,'level_cur' => $getLevelClassNew->level_name,'level_new' => $getLevelClassNew->class_new,'history_upto_level' => 3]
                                                        );

                                        $this->tonghop($year, $value, $getSchoolId->class_schools_id, 0);
                                    }else{ // Nếu tồn tại 1 bản ghi của năm học mới 
                                                // Lấy ID của his tồn tại update lại lớp
                                        $check = $countLevelHis->select('history_id')->first();
                                                // Đổi lớp trong hồ sơ
                                        $updateProfile = DB::update("update qlhs_profile set profile_status = 0,profile_revert = 0, profile_leaveschool_date = null,profile_update_userid = '$currentuser_id',updated_at = '$currentdate', profile_class_id = '$ClassNext' where profile_id = '$value'");
                                                //Đổi lớp trong lịch sử
                                        $updateHisProfile = DB::update("update qlhs_profile_history set history_upto_level = 3, history_class_id = '$ClassNext',level_old = '".$getLevelClassNew->class_old."',level_cur = '".$getLevelClassNew->level_name."',level_new = '".$getLevelClassNew->class_new."',history_update_user_id = '$currentuser_id',history_update_date = '$currentdate' where history_id = '$check->history_id' and history_profile_id = '$value'");                       
                                    }
                                }else{
                                    $updateProfile = DB::update("update qlhs_profile set profile_revert = 0, profile_class_id = '$classID',profile_update_userid = '$currentuser_id',updated_at = '$currentdate' where profile_id = '$value'");
                                }
                            }
                        }

                        if ($bool) {
                            $results['success'] = "Thực hiện lên lớp thành công";
                        }
                        else { $results['error'] = "Thực hiện lên lớp có lỗi đường truyền!Xin mời thử lại."; }
                    }
                    return $results;
                }else if((int)$classID_next == 2){ // Trường hợp nghỉ học
                            //cập nhật từng học sinh
                    foreach ($arrProfileID as $profileID) {
                        $checkNghiHoc = DB::table('qlhs_profile')->where('profile_id','=',$profileID)->select('profile_status')->first();
                        if((int)$checkNghiHoc->profile_status != 1){//Chỉ xử lý khi học sinh đấy chưa nghỉ học
                                //Kết thúc nghỉ học trong hồ sơ - revert = 2 nghỉ học
                            $updateProfile = DB::update("update qlhs_profile set profile_status = 1,profile_revert = 2, profile_leaveschool_date = '$dateOutProfile',profile_update_userid = '$currentuser_id',updated_at = '$currentdate' where profile_id = '$profileID'");
                                    //Chuyển trạng thái thành công 
                            if($updateProfile > 0){
                                          //Kiểm tra học sinh đã có lịch sử năm học mới chưa?
                                $countLevelHis = DB::table('qlhs_profile_history')->where('history_profile_id', '=', $profileID)->where('history_year','=',$strYear);
                                        // Nếu chưa tồn tại thì thêm bản ghi lịch sử mới
                                if($countLevelHis->count()==0){
                                    // khóa trạng thái cũ của lịch sử
                                      //  $lock = DB::update("update qlhs_profile_history set history_upto_level = 0 where  history_profile_id = '$profileID'");  
                                        //Cập nhật lịch sử nghỉ học
                                    $insert = DB::table('qlhs_profile_history')->insert(
                                                            ['history_profile_id' => $profileID,'history_year' => $strYear,'history_update_date' => $currentdate, 'history_update_user_id' => $currentuser_id,'level_old' => $getLevelClass->level_name,'history_class_id' => $classID,'level_cur' => '','level_new' => '','history_upto_level' => 4]
                                                        );


                                    $getMaxYear = DB::table('qlhs_tonghopchedo')->where('qlhs_thcd_profile_id', '=', $profileID)
                                        ->select('qlhs_thcd_id', DB::raw('MAX(qlhs_thcd_nam) as nam'))->groupBy('qlhs_thcd_id')->first();

                                    // return $getMaxYear->nam;

                                    $this->tonghop($getMaxYear->nam, $profileID, $getSchoolId->class_schools_id, $getMaxYear->qlhs_thcd_id);

                                }else{//Đã tòn tại lịch sử năm học mới
                                            // Lấy ID của his tồn tại update lại lớp
                                            $check = $countLevelHis->select('history_id')->first();
                                            // Cập nhật kết thúc nghỉ học cho năm học mới
                                            $insert = DB::update("update qlhs_profile_history set history_class_id = '$classID',level_old = '$getLevelClass->level_name',level_cur = '',level_new = '',history_update_user_id = '$currentuser_id',history_upto_level = 4,history_update_date = '$currentdate'  where history_id = '$check->history_id' and history_profile_id = '$profileID'"); 
                                }

                            }else{ // insert không thành công thì để nguyên
                                $updateProfile = DB::update("update qlhs_profile set profile_status = 0,profile_revert = 0, profile_leaveschool_date = null,profile_update_userid = '$currentuser_id',updated_at = '$currentdate' where profile_id = '$profileID'");
                            }
                        }
                    }

                    if ($bool) {
                        $results['success'] = "Thực hiện danh sách nghỉ học thành công.";
                    }
                    else { $results['error'] = "Thực hiện danh sách nghỉ học có lỗi đường truyền!Xin mời thử lại"; }
                    return $results;
                }else if((int)$classID_next == 3 ){ // Trường hợp học lại
                    if($classID > 0){
                                //cập nhật từng học sinh học lại - revert = 3
                        foreach ($arrProfileID as $profileID) {
                            $checkNghiHoc = DB::table('qlhs_profile')->where('profile_id','=',$profileID)->select('profile_status')->first();
                            if((int)$checkNghiHoc->profile_status != 1){//Chỉ xử lý khi học sinh đấy chưa nghỉ học
                                    //Kết thúc nghỉ học trong hồ sơ - revert = 2 nghỉ học
                                $updateProfile = DB::update("update qlhs_profile set profile_status = 0,profile_revert = 3, profile_leaveschool_date = null,profile_class_id = '$classBack',profile_update_userid = '$currentuser_id',updated_at = '$currentdate' where profile_id = '$profileID'");
                                        //Chuyển trạng thái thành công 
                                if($updateProfile > 0){
                                              //Kiểm tra học sinh đã có lịch sử năm học mới chưa?
                                    $countLevelHis = DB::table('qlhs_profile_history')->where('history_profile_id', '=', $profileID)->where('history_year','=',$strYear);
                                            // Nếu chưa tồn tại thì thêm bản ghi lịch sử mới
                                    if($countLevelHis->count()==0){
                                        // khóa trạng thái cũ của lịch sử
                                      //  $lock = DB::update("update qlhs_profile_history set history_upto_level = 0 where  history_profile_id = '$profileID'");  
                                                //Cập nhật lịch sử nghỉ học
                                        $insert = DB::table('qlhs_profile_history')->insert(
                                                                ['history_profile_id' => $profileID,'history_year' => $strYear,'history_update_date' => $currentdate, 'history_update_user_id' => $currentuser_id,'level_old' => $getLevelClassBack->class_old,'history_class_id' => $classBack,'level_cur' => $getLevelClassBack->level_name,'level_new' => $getLevelClassBack->class_new,'history_upto_level' => 2]
                                                            );
                                    }else{//Đã tòn tại lịch sử năm học mới
                                                // Lấy ID của his tồn tại update lại lớp
                                        $check = $countLevelHis->select('history_id')->first();
                                                // Cập nhật kết thúc nghỉ học cho năm học mới
                                        $insert = DB::update("update qlhs_profile_history set history_upto_level = 2, history_class_id = '$classBack','level_old' = $getLevelClassBack->class_old,'level_cur' = $getLevelClassBack->level_name,'level_new' = $getLevelClassBack->class_new,history_update_user_id = '$currentuser_id',history_update_date = '$currentdate' where history_id = '$check->history_id' and history_profile_id = '$profileID'"); 
                                    }    

                                }else{ // insert không thành công thì để nguyên 
                                    $updateProfile = DB::update("update qlhs_profile set profile_status = 0,profile_revert = 0, profile_leaveschool_date = null,profile_update_userid = '$currentuser_id',updated_at = '$currentdate',profile_class_id = '$classID' where profile_id = '$profileID'");
                                }
                            }

                            $this->tonghop($year, $profileID, $getSchoolId->class_schools_id, 0);
                        }

                        if ($bool) {
                            $results['success'] = "Thực hiện danh sách học lại thành công.";
                        }
                        else { $results['error'] = "Thực hiện danh sách học lại có lỗi đường truyền!Xin mời thử lại"; }
                    }
                    return $results;
                }else{
                    $results['error'] = "Xin mời chọn chức năng!";
                    return $results; 
                }
            }else{
                $results['error'] = "Xin mời chọn học sinh!";
                return $results; 
            }
        

            
        } catch (Exception $e) {
            return $e;
        }
    }

    public function revertProfile(Request $request){
        $results = [];
        try {
            $arrProfileID = $request->input('ARRPROFILEID');
            $classID = $request->input('CLASSID');
            $strYear = $request->input('YEAR');
            $classID_next = $request->input('CLASSIDNEXT');
            $currentuser_id = Auth::user()->id;
            $currentdate = Carbon::now('Asia/Ho_Chi_Minh');
            $bool = TRUE;

            if (count($arrProfileID) > 0 ) {

                foreach ($arrProfileID as $value) {
                    // Lấy thao tác gần nhất
                    $revert = DB::table('qlhs_profile')->where('profile_id','=',$value)->select('profile_revert')->first();
                    // nếu là lên lớp hoặc học lại thì xóa lịch sử mới nhất
                    if((int)$revert->profile_revert == 1 || (int)$revert->profile_revert == 3){
                        // lấy thao tác mới nhất trong lịch sử 
                        $getLevelHis = DB::table('qlhs_profile_history')->where('history_profile_id', '=', $value)->select(DB::raw('MAX(history_upto_level) as history_upto_level'))->first();
                        if((int)$getLevelHis->history_upto_level != 0){
                            // Xóa bản ghi đấy
                            $delete_history = DB::table("qlhs_profile_history")->where('history_profile_id', '=', $value)->where('history_upto_level', '=', $getLevelHis->history_upto_level)->delete();
                            if (!is_null($delete_history) && !empty($delete_history)) {
                                // Lấy lớp gần nhất để update lại hồ sơ
                                $getLevelHis2 = DB::table('qlhs_profile_history')->where('history_profile_id', '=', $value)->select(DB::raw('MAX(history_upto_level) as history_upto_level'))->first();
                                $getIdHis = DB::table('qlhs_profile_history')->where('history_profile_id', '=', $value)->where('history_upto_level','=', $getLevelHis2->history_upto_level)->select('history_class_id')->first();
                                    $updateProfile = DB::update("update qlhs_profile set profile_revert = 0, profile_class_id = '$getIdHis->history_class_id',profile_update_userid = '$currentuser_id',updated_at = '$currentdate' where profile_id = '$value'");

                                    if ($updateProfile <= 0) {
                                         $bool = false;
                                            break;
                                    }
                            }
                        }
                    // Trường hợp nghỉ học trả về chưa nghỉ học
                    }else if((int)$revert->profile_revert == 2){

                        $updateProfile = DB::update("update qlhs_profile set profile_revert = 0, profile_leaveschool_date = null,profile_status = 0,profile_update_userid = '$currentuser_id',updated_at = '$currentdate' where profile_id = '$value'");
                    }

                    // $this->tonghop($year, $profileID, $getSchoolId->class_schools_id, 1);
                }

                if ($bool) {
                    $results['success'] = "Hoàn tác thành công!";
                }
                else { $results['error'] = "Hoàn tác thất bại!"; }
            }
            else { $results['error'] = "Vui lòng chọn học sinh!"; }

            return $results;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function tonghop($year, $profileId, $schoolid, $thcd_ID,$end_year=0){
        try {
            // $schoolId = 37;//$request->input('SCHOOLID');
            // $year = 2016;//$request->input('YEAR');
            //$schoolid = DB::table('qlhs_profile')->where('profile_id',$profileId)
            $user = Auth::user()->id;

            $PROFILEID = $profileId;

            $MONEYMGHP = 0;
            $MONEYCPHT = 0;
            $MONEYHTAT = 0;
            $MONEYHTBTTIENAN = 0;
            $MONEYHTBTTIENO = 0;
            $MONEYHTBTVHTT = 0;
            $MONEYHSKTHOCBONG = 0;
            $MONEYHSKTDDHT = 0;
            $MONEYHSDTTS = 0;
            $MONEYHTATHS = 0;
            $MONEYHBHSDTNT = 0;

            $MONEYMGHPHK2 = 0;
            $MONEYCPHTHK2 = 0;
            $MONEYHTATHK2 = 0;
            $MONEYHTBTTIENANHK2 = 0;
            $MONEYHTBTTIENOHK2 = 0;
            $MONEYHTBTVHTTHK2 = 0;
            $MONEYHSKTHOCBONGHK2 = 0;
            $MONEYHSKTDDHTHK2 = 0;
            $MONEYHSDTTSHK2 = 0;
            $MONEYHTATHSHK2 = 0;
            $MONEYHBHSDTNTHK2 = 0;

            $getUnit = DB::table('qlhs_schools')->where('schools_id', '=', $schoolid)->select('schools_unit_id')->first();

//------------------------------------------------------------MGHP--------------------------------------------------------------


            $dataMGHP1 = DB::table('qlhs_profile')
            ->leftJoin('qlhs_profile_subject','profile_id', '=', DB::raw('profile_subject_profile_id AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.$year.' AND (qlhs_profile_subject.end_year > '.$year.' OR qlhs_profile_subject.end_year is null)'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))
            ->leftJoin('qlhs_kinhphinamhoc as kp1','kp1.idTruong','=',DB::raw('profile_school_id and kp1.codeYear = '.($year - 1).''))
            ->leftJoin('qlhs_kinhphinamhoc as kp2','kp2.idTruong','=',DB::raw('profile_school_id and kp2.codeYear = '.$year.''))
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

                DB::raw("MAX(CASE when level_old <> '' and qlhs_profile.profile_year < '".$year."-06-01' and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".$year."-01-01')) then 1 else 0 END) 'HKII1'"),
                DB::raw("MAX(CASE when level_cur <> '' and qlhs_profile.profile_year < '".$year."-12-31' and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".$year."-05-31')) then 1 else 0 END) 'HKI2'"),
                DB::raw("MAX(CASE when level_cur <> '' and qlhs_profile.profile_year < '".($year + 1)."-06-01' and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".((int)$year+1)."-01-01')) then 1 else 0 END) 'HKII2'"),
                DB::raw("MAX(CASE when level_new <> '' and qlhs_profile.profile_year < '".($year +1)."-12-31' and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".((int)$year+1)."-05-31')) then 1 else 0 END) 'HKI3'"))       
            ->where('profile_year','<',$year.'-06-01')
            ->where('profile_id','=',$profileId)
            ->whereIn('profile_subject_subject_id',[28,35,36,73,38,39,34,40,41,74,49])       
            ->groupBy('profile_id','profile_name','profile_year','level_old','level_cur','level_new','kp1.money','kp2.money');
            
            $dataMGHP11 = DB::table(DB::raw("({$dataMGHP1->toSql()}) as m"))->mergeBindings( $dataMGHP1 )
            ->select('HKII1','HKI2','HKII2','HKI3','m.money1','m.money2','m.Mien','m.Mien1','m.Mien2','m.Mien3','m.Mien4','m.Mien5','m.Mien6','m.Giam70','m.Giam70_1','m.Giam501','m.Giam502','m.profile_id','m.profile_subject_subject_id','m.profile_name','m.profile_year','m.level_old','m.level_cur','m.level_new',
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

            $dataMGHP2 = DB::table('qlhs_profile')
            ->leftJoin('qlhs_profile_subject','profile_id', '=', DB::raw('profile_subject_profile_id AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.$year.' AND (qlhs_profile_subject.end_year > '.$year.' OR qlhs_profile_subject.end_year is null)'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))
            ->leftJoin('qlhs_kinhphinamhoc as kp1','kp1.idTruong','=',DB::raw('profile_school_id and kp1.codeYear = '.($year - 1).''))
            ->leftJoin('qlhs_kinhphinamhoc as kp2','kp2.idTruong','=',DB::raw('profile_school_id and kp2.codeYear = '.$year.''))
            ->select('kp1.money as money1','kp2.money as money2','profile_id', 'profile_name','profile_year', 'level_old', 'level_cur', 'level_new', 
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

                    DB::raw("MAX(CASE when level_old <> '' and qlhs_profile.profile_year < '".$year."-06-01' and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".($year)."-01-01')) then 1 else 0 END) 'HKII1'"),
                    DB::raw("MAX(CASE when level_cur <> '' and qlhs_profile.profile_year < '".$year."-12-31' and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".($year)."-05-31')) then 1 else 0 END) 'HKI2'"),
                    DB::raw("MAX(CASE when level_cur <> '' and qlhs_profile.profile_year < '".($year + 1)."-06-01' and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".((int)$year+1)."-01-01')) then 1 else 0 END) 'HKII2'"),
                    DB::raw("MAX(CASE when level_new <> '' and qlhs_profile.profile_year < '".($year +1)."-12-31' and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".((int)$year+1)."-05-31')) then 1 else 0 END) 'HKI3'"))
                ->where('profile_year','>',$year.'-05-31')
                ->where('profile_year','<',((int)$year+1).'-06-01')
                ->where('profile_id','=',$profileId)
                ->whereIn('profile_subject_subject_id',[28,35,36,73,38,39,34,40,41,74,49])
                ->groupBy('profile_id','profile_name','profile_year','level_old','level_cur','level_new','kp1.money','kp2.money');

            $dataMGHP22 = DB::table(DB::raw("({$dataMGHP2->toSql()}) as m"))->mergeBindings( $dataMGHP2 )
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

            if (count($dataMGHP11) > 0) {                                    
                foreach ($dataMGHP11->get() as $value) {
                    if ($value->Mien > 0 && $value->Giam502 > 0) {
                        
                        $MONEYMGHP = ($value->HKI2 * 4 * $value->money2);
                        $MONEYMGHPHK2 = ($value->HKII2 * 5 * $value->money2);
                    }
                    else {
                        if ($value->Mien1 > 0 || $value->Mien2 > 0 || $value->Mien3 > 0 || $value->Mien4 > 0 || $value->Mien5 > 0 || $value->Mien6 > 0) {
                            $MONEYMGHP = ($value->HKI2 * 4 * $value->money2);
                            $MONEYMGHPHK2 = ($value->HKII2 * 5 * $value->money2);
                        }
                        else {
                            if ($value->Giam70 > 0 && $value->Giam70_1 > 0) {
                                $MONEYMGHP = ((($value->HKI2 * 4 * $value->money2)) * 7) / 10;
                                $MONEYMGHPHK2 = ((($value->HKII2 * 5 * $value->money2)) * 7) / 10;
                            }
                            else if ($value->Giam501 > 0 || $value->Giam502 > 0) {
                                $MONEYMGHP = ((($value->HKI2 * 4 * $value->money2)) * 5) / 10;
                                $MONEYMGHPHK2 = ((($value->HKII2 * 5 * $value->money2)) * 5) / 10;
                            }
                        }
                    }
                }
            }

            if (count($dataMGHP22) > 0) {                                    
                foreach ($dataMGHP22->get() as $value) {
                    if ($value->Mien > 0 && $value->Giam502 > 0) {
                        
                        $MONEYMGHP = ($value->HKI2 * 4 * $value->money2);
                        $MONEYMGHPHK2 = ($value->HKII2 * 5 * $value->money2);
                    }
                    else {
                        if ($value->Mien1 > 0 || $value->Mien2 > 0 || $value->Mien3 > 0 || $value->Mien4 > 0 || $value->Mien5 > 0 || $value->Mien6 > 0) {
                            $MONEYMGHP = ($value->HKI2 * 4 * $value->money2);
                            $MONEYMGHPHK2 = ($value->HKII2 * 5 * $value->money2);
                        }
                        else {
                            if ($value->Giam70 > 0 && $value->Giam70_1 > 0) {
                                $MONEYMGHP = ((($value->HKI2 * 4 * $value->money2)) * 7) / 10;
                                $MONEYMGHPHK2 = ((($value->HKII2 * 5 * $value->money2)) * 7) / 10;
                            }
                            else if ($value->Giam501 > 0 || $value->Giam502 > 0) {
                                $MONEYMGHP = ((($value->HKI2 * 4 * $value->money2)) * 5) / 10;
                                $MONEYMGHPHK2 = ((($value->HKII2 * 5 * $value->money2)) * 5) / 10;
                            }
                        }
                    }
                }
            }

            // return $MONEYMGHP;

//----------------------------------------------------------------------CPHT------------------------------------------------------------
            $dataCPHT1 = DB::table('qlhs_profile')
            ->leftJoin('qlhs_profile_subject','profile_id', '=', DB::raw('profile_subject_profile_id AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.$year.' AND (qlhs_profile_subject.end_year > '.$year.' OR qlhs_profile_subject.end_year is null)'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))

            ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$schoolid.' AND kp.id_doituong = 92 AND '.$year.' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 1).''))
            
            ->select('profile_id','profile_name','profile_year', 'level_old','level_cur','level_new',

                DB::raw('MAX(profile_subject_subject_id) as profile_subject_subject_id'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 28 then 1 else 0 END) DT1'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 73 then 1 else 0 END) DT2'),

                DB::raw("MAX(CASE when level_old <> '' and qlhs_profile.profile_year < '".$year."-06-01' and profile_subject_subject_id in (28,73) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".$year."-01-01')) then 1 else 0 END) 'HKII1'"),
                DB::raw("MAX(CASE when level_cur <> '' and qlhs_profile.profile_year < '".$year."-12-31' and profile_subject_subject_id in (28,73) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".$year."-05-31')) then 1 else 0 END) 'HKI2'"),
                DB::raw("MAX(CASE when level_cur <> '' and qlhs_profile.profile_year < '".($year + 1)."-06-01' and profile_subject_subject_id in (28,73) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".((int)$year+1)."-01-01')) then 1 else 0 END) 'HKII2'"),
                DB::raw("MAX(CASE when level_new <> '' and qlhs_profile.profile_year < '".($year +1)."-12-31' and profile_subject_subject_id in (28,73) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".((int)$year+1)."-05-31')) then 1 else 0 END) 'HKI3'"),
                DB::raw("MAX(
                    CASE
                        WHEN (level_cur <> ''
                        AND qlhs_profile.profile_year < '".$year."-12-31'
                        AND profile_subject_subject_id IN (28, 73)
                        AND (
                            profile_leaveschool_date IS NULL
                            OR (
                                profile_leaveschool_date > '".$year."-05-31'
                            )
                        ) AND kp.months in (9,10,11,12) AND kp.years = ".$year.")  THEN
                        kp.value_m
                        ELSE
                            0
                        END
                    ) 'NhuCau2'"),
                DB::raw("MAX(
                    CASE
                        WHEN (level_cur <> ''
                        AND qlhs_profile.profile_year < '".($year + 1)."-06-01'
                        AND profile_subject_subject_id IN (28, 73)
                        AND (
                            profile_leaveschool_date IS NULL
                            OR (
                                profile_leaveschool_date > '".($year + 1)."-01-01'
                            )
                        ) AND kp.months in (1,2,3,4,5) AND kp.years = ".($year + 1).") THEN
                        kp.value_m
                        ELSE
                            0
                        END
                    ) 'DuToan2'"))

            ->where('profile_year','<',$year.'-06-01')
            ->where('profile_id','=',$profileId)
            ->whereIn('profile_subject_subject_id',[28,73])
            ->groupBy('profile_id','profile_name','profile_year','level_old','level_cur','level_new','kp.value_m','kp.months','kp.years');

            $dataCPHT11 = DB::table(DB::raw("({$dataCPHT1->toSql()}) as m"))
                ->mergeBindings( $dataCPHT1 )
                ->select('HKII1',
                        'HKI2',
                        'HKII2',
                        'HKI3',
                        'm.DT1',
                        'm.DT2',
                        'm.profile_id',
                        'm.profile_subject_subject_id',
                        'm.profile_name',
                        'm.profile_year',
                        'm.level_old',
                        'm.level_cur',
                        'm.level_new',
                        DB::raw("SUM(NhuCau2) as NhuCau"),
                        DB::raw("SUM(DuToan2) as DuToan"))
                ->groupBy('HKII1',
                        'HKI2',
                        'HKII2',
                        'HKI3',
                        'm.DT1',
                        'm.DT2',
                        'm.profile_id',
                        'm.profile_subject_subject_id',
                        'm.profile_name',
                        'm.profile_year',
                        'm.level_old',
                        'm.level_cur',
                        'm.level_new');

            $dataCPHT2 = DB::table('qlhs_profile')
            ->leftJoin('qlhs_profile_subject','profile_id', '=', DB::raw('profile_subject_profile_id AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.$year.' AND (qlhs_profile_subject.end_year > '.$year.' OR qlhs_profile_subject.end_year is null)'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))
            
            ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$schoolid.' AND kp.id_doituong = 92 AND '.$year.' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 1).''))

            ->select('profile_id','profile_name','profile_year', 'level_old','level_cur','level_new',

                DB::raw('MAX(profile_subject_subject_id) as profile_subject_subject_id'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 28 then 1 else 0 END) DT1'),
                DB::raw('MAX(CASE when profile_subject_subject_id = 73 then 1 else 0 END) DT2'),

                DB::raw("MAX(CASE when level_old <> '' and qlhs_profile.profile_year < '".$year."-06-01' and profile_subject_subject_id in (28,73) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".$year."-01-01')) then 1 else 0 END) 'HKII1'"),
                DB::raw("MAX(CASE when level_cur <> '' and qlhs_profile.profile_year < '".$year."-12-31' and profile_subject_subject_id in (28,73) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".$year."-05-31')) then 1 else 0 END) 'HKI2'"),
                DB::raw("MAX(CASE when level_cur <> '' and qlhs_profile.profile_year < '".($year + 1)."-06-01' and profile_subject_subject_id in (28,73) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".((int)$year+1)."-01-01')) then 1 else 0 END) 'HKII2'"),
                DB::raw("MAX(CASE when level_new <> '' and qlhs_profile.profile_year < '".($year +1)."-12-31' and profile_subject_subject_id in (28,73) and (profile_leaveschool_date is null or ( profile_leaveschool_date > '".((int)$year+1)."-05-31')) then 1 else 0 END) 'HKI3'"),
                DB::raw("MAX(
                    CASE
                        WHEN (level_cur <> ''
                        AND qlhs_profile.profile_year < '".$year."-12-31'
                        AND profile_subject_subject_id IN (28, 73)
                        AND (
                            profile_leaveschool_date IS NULL
                            OR (
                                profile_leaveschool_date > '".$year."-05-31'
                            )
                        ) AND kp.months in (9,10,11,12) AND kp.years = ".$year.")  THEN
                        kp.value_m
                        ELSE
                            0
                        END
                    ) 'NhuCau2'"),
                DB::raw("MAX(
                    CASE
                        WHEN (level_cur <> ''
                        AND qlhs_profile.profile_year < '".($year + 1)."-06-01'
                        AND profile_subject_subject_id IN (28, 73)
                        AND (
                            profile_leaveschool_date IS NULL
                            OR (
                                profile_leaveschool_date > '".($year + 1)."-01-01'
                            )
                        ) AND kp.months in (1,2,3,4,5) AND kp.years = ".($year + 1).") THEN
                        kp.value_m
                        ELSE
                            0
                        END
                    ) 'DuToan2'"))

            ->where('profile_year','>',$year.'-05-31')
            ->where('profile_year','<',((int)$year+1).'-06-01')
            ->where('profile_id','=',$profileId)
            ->whereIn('profile_subject_subject_id',[28,73])
            ->groupBy('profile_id','profile_name','profile_year','level_old','level_cur','level_new','kp.value_m','kp.months','kp.years');

            $dataCPHT22 = DB::table(DB::raw("({$dataCPHT2->toSql()}) as m"))
                ->mergeBindings( $dataCPHT2 )
                ->select('HKII1',
                        'HKI2',
                        'HKII2',
                        'HKI3',
                        'm.DT1',
                        'm.DT2',
                        'm.profile_id',
                        'm.profile_subject_subject_id',
                        'm.profile_name',
                        'm.profile_year',
                        'm.level_old',
                        'm.level_cur',
                        'm.level_new',
                        DB::raw("SUM(NhuCau2) as NhuCau"),
                        DB::raw("SUM(DuToan2) as DuToan"))
                ->groupBy('HKII1',
                        'HKI2',
                        'HKII2',
                        'HKI3',
                        'm.DT1',
                        'm.DT2',
                        'm.profile_id',
                        'm.profile_subject_subject_id',
                        'm.profile_name',
                        'm.profile_year',
                        'm.level_old',
                        'm.level_cur',
                        'm.level_new');

            if (count($dataCPHT11) > 0) {                                    
                foreach ($dataCPHT11->get() as $value) {

                    if (!is_null($value->NhuCau) && !empty($value->NhuCau) && $value->NhuCau > 0) {
                        $MONEYCPHT = $value->NhuCau;
                        $MONEYCPHTHK2 = $value->DuToan;
                    }
                }
            }

            if (count($dataCPHT22) > 0) {                                    
                foreach ($dataCPHT22->get() as $value) {

                    if (!is_null($value->NhuCau) && !empty($value->NhuCau) && $value->NhuCau > 0) {
                        $MONEYCPHT = $value->NhuCau;
                        $MONEYCPHTHK2 = $value->DuToan;
                    }
                }
            }

//-------------------------------------------------------------------HTAT---------------------------------------------------------------
            if ($getUnit->schools_unit_id == 1) {
                $dataHTAT1 = DB::table('qlhs_profile')
                ->leftJoin('qlhs_profile_subject','profile_id', '=', DB::raw('profile_subject_profile_id AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.$year.' AND (qlhs_profile_subject.end_year > '.$year.' OR qlhs_profile_subject.end_year is null)'))
                ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))
                // ->join('qlhs_kinhphidoituong as kp1','kp1.idTruong','=',DB::raw('profile_school_id AND kp1.doituong_id = 93'))
                // ->join('qlhs_kinhphidoituong as kp2','kp2.idTruong','=',DB::raw('profile_school_id AND kp2.doituong_id = 93'))

                ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$schoolid.' AND kp.id_doituong = 93 AND '.$year.' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 1).''))

                ->select('profile_id','profile_name','profile_year', 'level_old','level_cur','level_new',
                    DB::raw('MAX(profile_subject_subject_id) as profile_subject_subject_id'),
                    DB::raw('MAX(CASE when profile_subject_subject_id = 26 or profile_subject_subject_id = 34 then 1 else 0 END) DT1'),
                    DB::raw('MAX(CASE when profile_subject_subject_id = 41 then 1 else 0 END) DT2'),
                    DB::raw('MAX(CASE when profile_subject_subject_id = 74 then 1 else 0 END) DT2_1'),
                    DB::raw('MAX(CASE when profile_subject_subject_id = 28 then 1 else 0 END) DT3'),
                    DB::raw('MAX(CASE when profile_subject_subject_id = 73 then 1 else 0 END) DT4'),

                    DB::raw("MAX(CASE when level_old <> '' and qlhs_profile.profile_year < '".$year."-06-01' and profile_subject_subject_id in (73,26,34,28,41,74) and (profile_leaveschool_date is null or (profile_leaveschool_date >= '".$year."-01-01')) then 1 else 0 END) 'HKII1'"),
                    DB::raw("MAX(CASE when level_cur <> '' and qlhs_profile.profile_year < '".$year."-12-31' and profile_subject_subject_id in (73,26,34,28,41,74) and (profile_leaveschool_date is null or (profile_leaveschool_date >= '".$year."-05-31')) then 1 else 0 END) 'HKI2'"),
                    DB::raw("MAX(CASE when level_cur <> '' and qlhs_profile.profile_year < '".($year +1)."-06-01' and profile_subject_subject_id in (73,26,34,28,41,74) and (profile_leaveschool_date is null or (profile_leaveschool_date >= '".((int)$year+1)."-01-01')) then 1 else 0 END) 'HKII2'"),
                    DB::raw("MAX(CASE when level_new <> '' and qlhs_profile.profile_year < '".($year +1)."-12-31' and profile_subject_subject_id in (73,26,34,28,41,74) and (profile_leaveschool_date is null or (profile_leaveschool_date >= '".((int)$year+1)."-05-31')) then 1 else 0 END) 'HKI3'"),
                    DB::raw("MAX(
                        CASE
                            WHEN (level_cur <> ''
                            AND qlhs_profile.profile_year < '".$year."-12-31'
                            AND profile_subject_subject_id IN (73,26,34,28,41,74)
                            AND (
                                profile_leaveschool_date IS NULL
                                OR (
                                    profile_leaveschool_date > '".$year."-05-31'
                                )
                            ) AND kp.months in (9,10,11,12) AND kp.years = ".$year.")  THEN
                            kp.value_m
                            ELSE
                                0
                            END
                        ) 'NhuCau2'"),
                    DB::raw("MAX(
                        CASE
                            WHEN (level_cur <> ''
                            AND qlhs_profile.profile_year < '".($year + 1)."-06-01'
                            AND profile_subject_subject_id IN (73,26,34,28,41,74)
                            AND (
                                profile_leaveschool_date IS NULL
                                OR (
                                    profile_leaveschool_date > '".($year + 1)."-01-01'
                                )
                            ) AND kp.months in (1,2,3,4,5) AND kp.years = ".($year + 1).")  THEN
                            kp.value_m
                            ELSE
                                0
                            END
                        ) 'DuToan2'"))
                
                ->where('profile_year','<',$year.'-06-01')
                ->where('profile_id','=',$profileId)
                ->whereIn('profile_subject_subject_id',[73,26,34,28,41,74])
                ->groupBy('profile_id','profile_name','profile_year','level_old','level_cur','level_new','kp.value_m','kp.months','kp.years');

                $dataHTAT11 = DB::table(DB::raw("({$dataHTAT1->toSql()}) as m"))
                    ->mergeBindings( $dataHTAT1 )
                    ->select('HKII1','HKI2','HKII2','HKI3','m.DT1','m.DT2','m.DT2_1','m.DT3','m.DT4','m.profile_id','m.profile_subject_subject_id','m.profile_name','m.profile_year','m.level_old','m.level_cur','m.level_new',
                        DB::raw("SUM(NhuCau2) as NhuCau"),
                        DB::raw("SUM(DuToan2) as DuToan"))
                    ->groupBy('HKII1','HKI2','HKII2','HKI3','m.DT1','m.DT2','m.DT2_1','m.DT3','m.DT4','m.profile_id','m.profile_subject_subject_id','m.profile_name','m.profile_year','m.level_old','m.level_cur','m.level_new');

                $dataHTAT2 = DB::table('qlhs_profile')
                ->leftJoin('qlhs_profile_subject','profile_id', '=', DB::raw('profile_subject_profile_id AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.$year.' AND (qlhs_profile_subject.end_year > '.$year.' OR qlhs_profile_subject.end_year is null)'))
                ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))        

                ->join('years_months_kp as kp','kp.id_school','=',DB::raw('profile_school_id AND kp.id_school = '.$schoolid.' AND kp.id_doituong = 93 AND '.$year.' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 1).''))

                ->select('profile_id','profile_name','profile_year', 'level_old','level_cur','level_new',
                    DB::raw('MAX(profile_subject_subject_id) as profile_subject_subject_id'),
                    DB::raw('MAX(CASE when profile_subject_subject_id = 26 or profile_subject_subject_id = 34 then 1 else 0 END) DT1'),
                    DB::raw('MAX(CASE when profile_subject_subject_id = 41 then 1 else 0 END) DT2'),
                    DB::raw('MAX(CASE when profile_subject_subject_id = 74 then 1 else 0 END) DT2_1'),
                    DB::raw('MAX(CASE when profile_subject_subject_id = 28 then 1 else 0 END) DT3'),
                    DB::raw('MAX(CASE when profile_subject_subject_id = 73 then 1 else 0 END) DT4'),

                    DB::raw("MAX(CASE when level_old <> '' and qlhs_profile.profile_year < '".$year."-06-01' and (profile_leaveschool_date is null or ( profile_leaveschool_date >= '".$year."-01-01')) then 1 else 0 END) 'HKII1'"),
                    DB::raw("MAX(CASE when level_cur <> '' and qlhs_profile.profile_year < '".$year."-12-31' and (profile_leaveschool_date is null or ( profile_leaveschool_date >= '".$year."-05-31')) then 1 else 0 END) 'HKI2'"),
                    DB::raw("MAX(CASE when level_cur <> '' and qlhs_profile.profile_year < '".($year + 1)."-06-01' and (profile_leaveschool_date is null or ( profile_leaveschool_date >= '".((int)$year+1)."-01-01')) then 1 else 0 END) 'HKII2'"),
                    DB::raw("MAX(CASE when level_new <> '' and qlhs_profile.profile_year < '".($year +1)."-12-31' and (profile_leaveschool_date is null or ( profile_leaveschool_date >= '".((int)$year+1)."-05-31')) then 1 else 0 END) 'HKI3'"),
                    DB::raw("MAX(
                        CASE
                            WHEN (level_cur <> ''
                            AND qlhs_profile.profile_year < '".$year."-12-31'
                            AND profile_subject_subject_id IN (73,26,34,28,41,74)
                            AND (
                                profile_leaveschool_date IS NULL
                                OR (
                                    profile_leaveschool_date > '".$year."-05-31'
                                )
                            ) AND kp.months in (9,10,11,12) AND kp.years = ".$year.")  THEN
                            kp.value_m
                            ELSE
                                0
                            END
                        ) 'NhuCau2'"),
                    DB::raw("MAX(
                        CASE
                            WHEN (level_cur <> ''
                            AND qlhs_profile.profile_year < '".($year + 1)."-06-01'
                            AND profile_subject_subject_id IN (73,26,34,28,41,74)
                            AND (
                                profile_leaveschool_date IS NULL
                                OR (
                                    profile_leaveschool_date > '".($year + 1)."-01-01'
                                )
                            ) AND kp.months in (1,2,3,4,5) AND kp.years = ".($year + 1).") THEN
                            kp.value_m
                            ELSE
                                0
                            END
                        ) 'DuToan2'"))
                
                ->where('profile_year','>',$year.'-05-31')
                ->where('profile_year','<',((int)$year+1).'-06-01')
                ->where('profile_id','=',$profileId)
                ->whereIn('profile_subject_subject_id',[73,26,34,28,41,74])
                ->groupBy('profile_id','profile_name','profile_year','level_old','level_cur','level_new','kp.value_m','kp.months','kp.years');

                $dataHTAT22 = DB::table(DB::raw("({$dataHTAT2->toSql()}) as m"))
                    ->mergeBindings( $dataHTAT2 )
                    ->select('HKII1','HKI2','HKII2','HKI3','m.DT1','m.DT2','m.DT2_1','m.DT3','m.DT4','m.profile_id','m.profile_subject_subject_id','m.profile_name','m.profile_year','m.level_old','m.level_cur','m.level_new',
                        DB::raw("SUM(NhuCau2) as NhuCau"),
                        DB::raw("SUM(DuToan2) as DuToan"))
                    ->groupBy('HKII1','HKI2','HKII2','HKI3','m.DT1','m.DT2','m.DT2_1','m.DT3','m.DT4','m.profile_id','m.profile_subject_subject_id','m.profile_name','m.profile_year','m.level_old','m.level_cur','m.level_new');
                 // return  $dataHTAT11->toSql();      
                if (count($dataHTAT11) > 0) {                                    
                    foreach ($dataHTAT11->get() as $value) {

                        if (($value->DT2_1 > 0 && $value->DT2 > 0) 
                            || ($value->DT1 > 0 || $value->DT3 > 0 || $value->DT4 > 0) 
                            && (!is_null($value->NhuCau) && !empty($value->NhuCau) && $value->NhuCau > 0)) {

                            $MONEYHTAT = $value->NhuCau;
                            $MONEYHTATHK2 = $value->DuToan;
                        }
                    }
                }

                if (count($dataHTAT22) > 0) {                                    
                    foreach ($dataHTAT22->get() as $value) {

                        if (($value->DT2_1 > 0 && $value->DT2 > 0) 
                            || ($value->DT1 > 0 || $value->DT3 > 0 || $value->DT4 > 0) 
                            && (!is_null($value->NhuCau) && !empty($value->NhuCau) && $value->NhuCau > 0)) {

                            $MONEYHTAT = $value->NhuCau;
                            $MONEYHTATHK2 = $value->DuToan;
                        }
                    }
                }
            }
            
//----------------------------------------------------------------HTBT-----------------------------------------------------------
            if ($getUnit->schools_unit_id != 1) {
                $getDataTypeHTBT1 = DB::table('qlhs_profile')
                    ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id in (34, 46, 72) AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.$year.' AND (qlhs_profile_subject.end_year is null OR qlhs_profile_subject.end_year > '.$year.')'))
                    ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))

                    ->join('years_months_kp as kp', 'kp.id_school', '=', DB::raw('qlhs_profile.profile_school_id and kp.id_school = '.$schoolid.' and kp.id_doituong in (94, 98, 115) and '.$year.' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 1).''))
                    
                    ->where('qlhs_profile.profile_id', '=', $profileId)
                    ->where('qlhs_profile.profile_year', '<', $year.'-06-01')
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
                                WHEN (qlhs_profile_history.level_cur <> ""
                                AND qlhs_profile_subject.profile_subject_subject_id in (34, 46)
                                AND qlhs_profile.profile_year < "'.$year.'-12-31"
                                AND (
                                    qlhs_profile.profile_leaveschool_date IS NULL
                                    OR (
                                        qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                    )
                                )
                                AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 94 ) THEN
                                    kp.value_m
                                ELSE
                                    0
                                END
                            ) AS NHUCAUAN'),
                        DB::raw('MAX(
                                CASE
                                WHEN (qlhs_profile_history.level_cur <> ""
                                AND qlhs_profile_subject.profile_subject_subject_id in (34, 46)
                                AND qlhs_profile.profile_year < "'.$year.'-12-31"
                                AND (
                                    qlhs_profile.profile_leaveschool_date IS NULL
                                    OR (
                                        qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                    )
                                )
                                AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 98) THEN
                                    kp.value_m
                                ELSE
                                    0
                                END
                            ) AS NHUCAUO'),
                        DB::raw('MAX(
                                CASE
                                WHEN (qlhs_profile_history.level_cur <> ""
                                AND qlhs_profile_subject.profile_subject_subject_id = 46
                                AND qlhs_profile.profile_year < "'.$year.'-12-31"
                                AND (
                                    qlhs_profile.profile_leaveschool_date IS NULL
                                    OR (
                                        qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                    )
                                )
                                AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 115 ) THEN
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
                                AND kp.months in (1,2,3,4,5) and kp.years = '.($year +1).' AND kp.id_doituong = 94 ) 
                                THEN
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
                                AND kp.months in (1,2,3,4,5) and kp.years = '.($year +1).' AND kp.id_doituong = 98 ) 
                                THEN
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
                                AND kp.months in (1,2,3,4,5) and kp.years = '.($year +1).' AND kp.id_doituong = 115 ) 
                                THEN
                                    kp.value_m
                                ELSE
                                    0
                                END
                            ) AS DUTOANTT'))
                    ->groupBy('qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years');

                $getDataTypeHTBT11 = DB::table(DB::raw("({$getDataTypeHTBT1->toSql()}) as m"))
                    ->mergeBindings( $getDataTypeHTBT1 )
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

                $getDataTypeHTBT2 = DB::table('qlhs_profile')
                    ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id in (34, 46, 72) AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.$year.' AND (qlhs_profile_subject.end_year is null OR qlhs_profile_subject.end_year > '.$year.')'))
                    ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))

                    ->join('years_months_kp as kp', 'kp.id_school', '=', DB::raw('qlhs_profile.profile_school_id and kp.id_school = '.$schoolid.' and kp.id_doituong in (94, 98, 115) and '.$year.' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 1).''))
                    
                    ->where('qlhs_profile.profile_id', '=', $profileId)
                    
                    ->where('qlhs_profile.profile_year', '>', $year.'-05-31')
                    ->where('qlhs_profile.profile_year', '<', ($year + 1).'-06-01')
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
                                WHEN (qlhs_profile_history.level_cur <> ""
                                AND qlhs_profile_subject.profile_subject_subject_id in (34, 46)
                                AND qlhs_profile.profile_year < "'.$year.'-12-31"
                                AND (
                                    qlhs_profile.profile_leaveschool_date IS NULL
                                    OR (
                                        qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                    )
                                )
                                AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 94 ) THEN
                                    kp.value_m
                                ELSE
                                    0
                                END
                            ) AS NHUCAUAN'),
                        DB::raw('MAX(
                                CASE
                                WHEN (qlhs_profile_history.level_cur <> ""
                                AND qlhs_profile_subject.profile_subject_subject_id in (34, 46)
                                AND qlhs_profile.profile_year < "'.$year.'-12-31"
                                AND (
                                    qlhs_profile.profile_leaveschool_date IS NULL
                                    OR (
                                        qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                    )
                                )
                                AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 98) THEN
                                    kp.value_m
                                ELSE
                                    0
                                END
                            ) AS NHUCAUO'),
                        DB::raw('MAX(
                                CASE
                                WHEN (qlhs_profile_history.level_cur <> ""
                                AND qlhs_profile_subject.profile_subject_subject_id = 46
                                AND qlhs_profile.profile_year < "'.$year.'-12-31"
                                AND (
                                    qlhs_profile.profile_leaveschool_date IS NULL
                                    OR (
                                        qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                    )
                                )
                                AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 115 ) THEN
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
                                AND kp.months in (1,2,3,4,5) and kp.years = '.($year +1).' AND kp.id_doituong = 94 ) 
                                THEN
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
                                AND kp.months in (1,2,3,4,5) and kp.years = '.($year +1).' AND kp.id_doituong = 98 ) 
                                THEN
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
                                AND kp.months in (1,2,3,4,5) and kp.years = '.($year +1).' AND kp.id_doituong = 115 ) 
                                THEN
                                    kp.value_m
                                ELSE
                                    0
                                END
                            ) AS DUTOANTT'))
                ->groupBy('qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years');

                $getDataTypeHTBT22 = DB::table(DB::raw("({$getDataTypeHTBT2->toSql()}) as m"))
                    ->mergeBindings( $getDataTypeHTBT2 )
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
                // return $getDataTypeHTBT22;

                if (!is_null($getDataTypeHTBT11) && !empty($getDataTypeHTBT11) && count($getDataTypeHTBT11) > 0) {
                    foreach ($getDataTypeHTBT11 as $value) {

                        if ($value->{'hotroVHTT'} == 1) {
                            $MONEYHTBTVHTT = $value->{'NHUCAUTT'};
                            $MONEYHTBTVHTTHK2 = $value->{'DUTOANTT'};
                        }

                        if ($value->{'hotrotienan'} == 1) {
                            $MONEYHTBTTIENAN = $value->{'NHUCAUAN'};
                            $MONEYHTBTTIENANHK2 = $value->{'DUTOANAN'};
                        }
                        if ($value->{'hotrotieno'} == 1) {
                            $MONEYHTBTTIENO = $value->{'NHUCAUO'};
                            $MONEYHTBTTIENOHK2 = $value->{'DUTOANO'};
                        }
                    }
                }

                if (!is_null($getDataTypeHTBT22) && !empty($getDataTypeHTBT22) && count($getDataTypeHTBT22) > 0) {
                    foreach ($getDataTypeHTBT22 as $value) {

                        if ($value->{'hotroVHTT'} == 1) {
                            $MONEYHTBTVHTT = $value->{'NHUCAUTT'};
                            $MONEYHTBTVHTTHK2 = $value->{'DUTOANTT'};
                        }

                        if ($value->{'hotrotienan'} == 1) {
                            $MONEYHTBTTIENAN = $value->{'NHUCAUAN'};
                            $MONEYHTBTTIENANHK2 = $value->{'DUTOANAN'};
                        }
                        if ($value->{'hotrotieno'} == 1) {
                            $MONEYHTBTTIENO = $value->{'NHUCAUO'};
                            $MONEYHTBTTIENOHK2 = $value->{'DUTOANO'};
                        }
                    }
                }
            }

//-------------------------------------------------------------------HSKT------------------------------------------------------------------
            $getDataTypeHSKT1 = DB::table('qlhs_profile')
            ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id = 74 AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.$year.' AND (qlhs_profile_subject.end_year is null OR qlhs_profile_subject.end_year > '.$year.')'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))

            ->join('years_months_kp as kp', 'kp.id_school', '=', DB::raw('qlhs_profile.profile_school_id and kp.id_school = '.$schoolid.' and kp.id_doituong in (95,100) and '.$year.' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 1).''))
                
            ->where('qlhs_profile.profile_id', '=', $profileId)
            ->where('qlhs_profile.profile_year', '<', $year.'-06-01')
            ->select('qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years', 
                DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 74 then 1 else 0 END) as hotrohocbong'), 
                DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 74 then 1 else 0 END) as hotromuadodunght'), 
                    
                DB::raw('(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hotro_hocky2_old'), 
                DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hotro_hocky1_cur'), 
                DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.($year + 1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hotro_hocky2_cur'), 
                DB::raw('(CASE when qlhs_profile_history.level_new <> "" and qlhs_profile.profile_year < "'.($year + 1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hotro_hocky1_new'),
                    DB::raw('MAX(
                            CASE
                            WHEN (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 74
                            AND qlhs_profile.profile_year < "'.$year.'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 95 ) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS NHUCAUHB'),
                    DB::raw('MAX(
                            CASE
                            WHEN (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 74
                            AND qlhs_profile.profile_year < "'.$year.'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 100 ) THEN
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
                            AND kp.months in (1,2,3,4,5) and kp.years = '.($year +1).' AND kp.id_doituong = 95 ) 
                            THEN
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
                            AND kp.months in (1,2,3,4,5) and kp.years = '.($year +1).' AND kp.id_doituong = 100 ) 
                            THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS DUTOANDDHT'))
            ->groupBy('qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years');

            $getDataTypeHSKT11 = DB::table(DB::raw("({$getDataTypeHSKT1->toSql()}) as m"))
                ->mergeBindings( $getDataTypeHSKT1 )
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

            $getDataTypeHSKT2 = DB::table('qlhs_profile')
            ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id = 74 AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.$year.' AND (qlhs_profile_subject.end_year is null OR qlhs_profile_subject.end_year > '.$year.')'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))

            ->join('years_months_kp as kp', 'kp.id_school', '=', DB::raw('qlhs_profile.profile_school_id and kp.id_school = '.$schoolid.' and kp.id_doituong in (95,100) and '.$year.' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 1).''))

            ->where('qlhs_profile.profile_id', '=', $profileId)
            ->where('qlhs_profile.profile_year', '>', $year.'-05-31')
            ->where('qlhs_profile.profile_year', '<', ($year + 1).'-06-01')
            ->select('qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years', 
                DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 74 then 1 else 0 END) as hotrohocbong'), 
                DB::raw('MAX(CASE when qlhs_profile_subject.profile_subject_subject_id = 74 then 1 else 0 END) as hotromuadodunght'), 
                    
                DB::raw('(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hotro_hocky2_old'), 
                DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hotro_hocky1_cur'), 
                DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.($year + 1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hotro_hocky2_cur'), 
                DB::raw('(CASE when qlhs_profile_history.level_new <> "" and qlhs_profile.profile_year < "'.($year + 1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hotro_hocky1_new'),
                    DB::raw('MAX(
                            CASE
                            WHEN (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 74
                            AND qlhs_profile.profile_year < "'.$year.'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 95 ) THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS NHUCAUHB'),
                    DB::raw('MAX(
                            CASE
                            WHEN (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 74
                            AND qlhs_profile.profile_year < "'.$year.'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 100 ) THEN
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
                            AND kp.months in (1,2,3,4,5) and kp.years = '.($year +1).' AND kp.id_doituong = 95 ) 
                            THEN
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
                            AND kp.months in (1,2,3,4,5) and kp.years = '.($year +1).' AND kp.id_doituong = 100 ) 
                            THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS DUTOANDDHT'))
            ->groupBy('qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years');

            $getDataTypeHSKT22 = DB::table(DB::raw("({$getDataTypeHSKT2->toSql()}) as m"))
                ->mergeBindings( $getDataTypeHSKT2 )
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
               // return $getDataTypeHSKT11->t
            if (!is_null($getDataTypeHSKT11) && !empty($getDataTypeHSKT11) && count($getDataTypeHSKT11) > 0) {
                foreach ($getDataTypeHSKT11 as $value) {

                    if ($value->{'hotrohocbong'} == 1) {
                            $MONEYHSKTHOCBONG = $value->{'NHUCAUHB'};
                            $MONEYHSKTHOCBONGHK2 = $value->{'DUTOANHB'};
                    }
                    if ($value->{'hotromuadodunght'} == 1) {                    
                            $MONEYHSKTDDHT = $value->{'NHUCAUDDHT'};
                            $MONEYHSKTDDHTHK2 = $value->{'DUTOANDDHT'};
                    }
                }
            }

            if (!is_null($getDataTypeHSKT22) && !empty($getDataTypeHSKT22) && count($getDataTypeHSKT22) > 0) {
                foreach ($getDataTypeHSKT22 as $value) {

                    if ($value->{'hotrohocbong'} == 1) {
                            $MONEYHSKTHOCBONG = $value->{'NHUCAUHB'};
                            $MONEYHSKTHOCBONGHK2 = $value->{'DUTOANHB'};
                    }
                    if ($value->{'hotromuadodunght'} == 1) {                    
                            $MONEYHSKTDDHT = $value->{'NHUCAUDDHT'};
                            $MONEYHSKTDDHTHK2 = $value->{'DUTOANDDHT'};
                    }
                }
            }

//-----------------------------------------------------------------HSDTTS----------------------------------------------------------------
            $getDataTypeHSDTTS1 = DB::table('qlhs_profile')
            ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id = 49 AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.$year.' AND (qlhs_profile_subject.end_year is null OR qlhs_profile_subject.end_year > '.$year.')'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))

            ->join('years_months_kp as kp', 'kp.id_school', '=', DB::raw('qlhs_profile.profile_school_id and kp.id_school = '.$schoolid.' and kp.id_doituong = 99 and '.$year.' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 1).''))

            ->where('qlhs_profile.profile_id', '=', $profileId)

            ->where('qlhs_profile.profile_year', '<', $year.'-06-01')
            ->select('qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years', 
                DB::raw('(CASE when qlhs_profile_subject.profile_subject_subject_id = 49 then 1 else 0 END) as hotrokinhphi'), 
                DB::raw('(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hocky2_old'), 
                DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hocky1_cur'), 
                DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.($year + 1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hocky2_cur'), 
                DB::raw('(CASE when qlhs_profile_history.level_new <> "" and qlhs_profile.profile_year < "'.($year +1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hocky1_new'),
                    DB::raw('MAX(
                            CASE
                            WHEN (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 49
                            AND qlhs_profile.profile_year < "'.$year.'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 99 ) THEN
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
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.($year + 1).' AND kp.id_doituong = 99 ) 
                            THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS DUTOAN'))
            ->groupBy('qlhs_profile_subject.profile_subject_subject_id', 'qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years');

            $getDataTypeHSDTTS11 = DB::table(DB::raw("({$getDataTypeHSDTTS1->toSql()}) as m"))
                ->mergeBindings( $getDataTypeHSDTTS1 )
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

            $getDataTypeHSDTTS2 = DB::table('qlhs_profile')
            ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id = 49 AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.$year.' AND (qlhs_profile_subject.end_year is null OR qlhs_profile_subject.end_year > '.$year.')'))
            ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))

            ->join('years_months_kp as kp', 'kp.id_school', '=', DB::raw('qlhs_profile.profile_school_id and kp.id_school = '.$schoolid.' and kp.id_doituong = 99 and '.$year.' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 1).''))

            ->where('qlhs_profile.profile_id', '=', $profileId)

            ->where('qlhs_profile.profile_year', '>', $year.'-05-31')
            ->where('qlhs_profile.profile_year', '<', ($year + 1).'-06-01')
            ->select('qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years', 
                DB::raw('(CASE when qlhs_profile_subject.profile_subject_subject_id = 49 then 1 else 0 END) as hotrokinhphi'), 
                DB::raw('(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hocky2_old'), 
                DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hocky1_cur'), 
                DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.($year + 1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hocky2_cur'), 
                DB::raw('(CASE when qlhs_profile_history.level_new <> "" and qlhs_profile.profile_year < "'.($year + 1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hocky1_new'),
                    DB::raw('MAX(
                            CASE
                            WHEN (qlhs_profile_history.level_cur <> ""
                            AND qlhs_profile_subject.profile_subject_subject_id = 49
                            AND qlhs_profile.profile_year < "'.$year.'-12-31"
                            AND (
                                qlhs_profile.profile_leaveschool_date IS NULL
                                OR (
                                    qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                )
                            )
                            AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 99 ) THEN
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
                            ) AND kp.months in (1,2,3,4,5) and kp.years = '.($year + 1).' AND kp.id_doituong = 99 ) 
                            THEN
                                kp.value_m
                            ELSE
                                0
                            END
                        ) AS DUTOAN'))
            ->groupBy('qlhs_profile_subject.profile_subject_subject_id', 'qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years');

            $getDataTypeHSDTTS22 = DB::table(DB::raw("({$getDataTypeHSDTTS2->toSql()}) as m"))
                ->mergeBindings( $getDataTypeHSDTTS2 )
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

            if (!is_null($getDataTypeHSDTTS11) && !empty($getDataTypeHSDTTS11) && count($getDataTypeHSDTTS11) > 0) {
                foreach ($getDataTypeHSDTTS11 as $value) {

                    $MONEYHSDTTS = $value->{'NHUCAU'};
                    $MONEYHSDTTSHK2 = $value->{'DUTOAN'};
                }
            }

            if (!is_null($getDataTypeHSDTTS22) && !empty($getDataTypeHSDTTS22) && count($getDataTypeHSDTTS22) > 0) {
                foreach ($getDataTypeHSDTTS22 as $value) {

                    $MONEYHSDTTS = $value->{'NHUCAU'};
                    $MONEYHSDTTSHK2 = $value->{'DUTOAN'};
                }
            }

//---------------------------------------------------------------Hỗ trợ ăn trưa cho HS--------------------------------------------------------------
            if ($getUnit->schools_unit_id != 1) {
                $getDataTypeHTATHS1 = DB::table('qlhs_profile')
                ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id = 69 AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.$year.' AND (qlhs_profile_subject.end_year is null OR qlhs_profile_subject.end_year > '.$year.')'))
                ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))

                ->join('years_months_kp as kp', 'kp.id_school', '=', DB::raw('qlhs_profile.profile_school_id and kp.id_school = '.$schoolid.' and kp.id_doituong = 118 and '.$year.' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 1).''))

                ->where('qlhs_profile.profile_id', '=', $profileId)
                ->where('qlhs_profile.profile_year', '<', $year.'-06-01')
                ->where('qlhs_profile.profile_statusNQ57', '=', 1)
                ->select('qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years', 
                    
                    DB::raw('(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hocky2_old'), 
                    DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hocky1_cur'), 
                    DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.($year + 1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hocky2_cur'), 
                    DB::raw('(CASE when qlhs_profile_history.level_new <> "" and qlhs_profile.profile_year < "'.($year +1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hocky1_new'),
                        DB::raw('MAX(
                                CASE
                                WHEN (qlhs_profile_history.level_cur <> ""
                                AND qlhs_profile_subject.profile_subject_subject_id = 69
                                AND qlhs_profile.profile_year < "'.$year.'-12-31"
                                AND (
                                    qlhs_profile.profile_leaveschool_date IS NULL
                                    OR (
                                        qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                    )
                                )
                                AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 118 ) THEN
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
                                ) AND kp.months in (1,2,3,4,5) and kp.years = '.($year + 1).' AND kp.id_doituong = 118 ) 
                                THEN
                                    kp.value_m
                                ELSE
                                    0
                                END
                            ) AS DUTOAN'))
                ->groupBy('qlhs_profile_subject.profile_subject_subject_id', 'qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years');

                $getDataTypeHTATHS11 = DB::table(DB::raw("({$getDataTypeHTATHS1->toSql()}) as m"))
                    ->mergeBindings( $getDataTypeHTATHS1 )
                    ->select('m.profile_id',
                            'm.profile_year',
                            'm.level_old',
                            'm.level_cur',
                            'm.level_new',
                            'm.profile_leaveschool_date',
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
                            'm.hocky2_old', 
                            'm.hocky1_cur', 
                            'm.hocky2_cur', 
                            'm.hocky1_new')
                    ->get();

                $getDataTypeHTATHS2 = DB::table('qlhs_profile')
                ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id = 69 AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.$year.' AND (qlhs_profile_subject.end_year is null OR qlhs_profile_subject.end_year > '.$year.')'))
                ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))

                ->join('years_months_kp as kp', 'kp.id_school', '=', DB::raw('qlhs_profile.profile_school_id and kp.id_school = '.$schoolid.' and kp.id_doituong = 118 and '.$year.' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 1).''))

                ->where('qlhs_profile.profile_id', '=', $profileId)
                ->where('qlhs_profile.profile_year', '>', $year.'-05-31')
                ->where('qlhs_profile.profile_year', '<', ($year + 1).'-06-01')
                ->where('qlhs_profile.profile_statusNQ57', '=', 1)
                ->select('qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years', 
                     
                    DB::raw('(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hocky2_old'), 
                    DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hocky1_cur'), 
                    DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.($year + 1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hocky2_cur'), 
                    DB::raw('(CASE when qlhs_profile_history.level_new <> "" and qlhs_profile.profile_year < "'.($year + 1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hocky1_new'),
                        DB::raw('MAX(
                                CASE
                                WHEN (qlhs_profile_history.level_cur <> ""
                                AND qlhs_profile_subject.profile_subject_subject_id = 69
                                AND qlhs_profile.profile_year < "'.$year.'-12-31"
                                AND (
                                    qlhs_profile.profile_leaveschool_date IS NULL
                                    OR (
                                        qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                    )
                                )
                                AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 118 ) THEN
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
                                ) AND kp.months in (1,2,3,4,5) and kp.years = '.($year + 1).' AND kp.id_doituong = 118 ) 
                                THEN
                                    kp.value_m
                                ELSE
                                    0
                                END
                            ) AS DUTOAN'))
                ->groupBy('qlhs_profile_subject.profile_subject_subject_id', 'qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years');

                $getDataTypeHTATHS22 = DB::table(DB::raw("({$getDataTypeHTATHS2->toSql()}) as m"))
                    ->mergeBindings( $getDataTypeHTATHS2 )
                    ->select('m.profile_id',
                            'm.profile_year',
                            'm.level_old',
                            'm.level_cur',
                            'm.level_new',
                            'm.profile_leaveschool_date',
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
                            'm.hocky2_old', 
                            'm.hocky1_cur', 
                            'm.hocky2_cur', 
                            'm.hocky1_new')
                    ->get();

                // return $getDataTypeHTATHS22;
                if (!is_null($getDataTypeHTATHS11) && !empty($getDataTypeHTATHS11) && count($getDataTypeHTATHS11) > 0) {
                    foreach ($getDataTypeHTATHS11 as $value) {

                        $MONEYHTATHS = $value->{'NHUCAU'};
                        $MONEYHTATHSHK2 = $value->{'DUTOAN'};
                    }
                }

                if (!is_null($getDataTypeHTATHS22) && !empty($getDataTypeHTATHS22) && count($getDataTypeHTATHS22) > 0) {
                    foreach ($getDataTypeHTATHS22 as $value) {

                        $MONEYHTATHS = $value->{'NHUCAU'};
                        $MONEYHTATHSHK2 = $value->{'DUTOAN'};
                    }
                }
            }

//--------------------------------------------------------Học bổng hs dân tộc nội trú----------------------------------------------------
            if ($getUnit->schools_unit_id != 1) {
                $getDataTypeHBHSDTNT1 = DB::table('qlhs_profile')
                ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id = 70 AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.$year.' AND (qlhs_profile_subject.end_year is null OR qlhs_profile_subject.end_year > '.$year.')'))
                ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))

                ->join('years_months_kp as kp', 'kp.id_school', '=', DB::raw('qlhs_profile.profile_school_id and kp.id_school = '.$schoolid.' and kp.id_doituong = 119 and '.$year.' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 1).''))

                ->where('qlhs_profile.profile_id', '=', $profileId)

                ->where('qlhs_profile.profile_year', '<', $year.'-06-01')
                ->select('qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years', 
                    
                    DB::raw('(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hocky2_old'), 
                    DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hocky1_cur'), 
                    DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.($year + 1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hocky2_cur'), 
                    DB::raw('(CASE when qlhs_profile_history.level_new <> "" and qlhs_profile.profile_year < "'.($year +1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hocky1_new'),
                        DB::raw('MAX(
                                CASE
                                WHEN (qlhs_profile_history.level_cur <> ""
                                AND qlhs_profile_subject.profile_subject_subject_id = 70
                                AND qlhs_profile.profile_year < "'.$year.'-12-31"
                                AND (
                                    qlhs_profile.profile_leaveschool_date IS NULL
                                    OR (
                                        qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                    )
                                )
                                AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 119 ) THEN
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
                                ) AND kp.months in (1,2,3,4,5) and kp.years = '.($year + 1).' AND kp.id_doituong = 119 ) 
                                THEN
                                    kp.value_m
                                ELSE
                                    0
                                END
                            ) AS DUTOAN'))
                ->groupBy('qlhs_profile_subject.profile_subject_subject_id', 'qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years');

                $getDataTypeHBHSDTNT11 = DB::table(DB::raw("({$getDataTypeHBHSDTNT1->toSql()}) as m"))
                    ->mergeBindings( $getDataTypeHBHSDTNT1 )
                    ->select('m.profile_id',
                            'm.profile_year',
                            'm.level_old',
                            'm.level_cur',
                            'm.level_new',
                            'm.profile_leaveschool_date',
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
                            'm.hocky2_old', 
                            'm.hocky1_cur', 
                            'm.hocky2_cur', 
                            'm.hocky1_new')
                    ->get();

                $getDataTypeHBHSDTNT2 = DB::table('qlhs_profile')
                ->join('qlhs_profile_subject', 'qlhs_profile_subject.profile_subject_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_subject.profile_subject_subject_id = 70 AND qlhs_profile_subject.active = 1 AND qlhs_profile_subject.start_year <= '.$year.' AND (qlhs_profile_subject.end_year is null OR qlhs_profile_subject.end_year > '.$year.')'))
                ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_profile_history.history_year = "'.$year.'-'.($year + 1).'"'))

                ->join('years_months_kp as kp', 'kp.id_school', '=', DB::raw('qlhs_profile.profile_school_id and kp.id_school = '.$schoolid.' and kp.id_doituong = 119 and '.$year.' <= YEAR(kp.date_con) and YEAR(kp.date_con) <= '.($year + 1).''))

                ->where('qlhs_profile.profile_id', '=', $profileId)

                ->where('qlhs_profile.profile_year', '>', $year.'-05-31')
                ->where('qlhs_profile.profile_year', '<', ($year + 1).'-06-01')
                ->select('qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years', 
                     
                    DB::raw('(CASE when qlhs_profile_history.level_old <> "" and qlhs_profile.profile_year < "'.$year.'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-01-01")) then 1 else 0 END) as hocky2_old'), 
                    DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.$year.'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31")) then 1 else 0 END) as hocky1_cur'), 
                    DB::raw('(CASE when qlhs_profile_history.level_cur <> "" and qlhs_profile.profile_year < "'.($year + 1).'-06-01" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-01-01")) then 1 else 0 END) as hocky2_cur'), 
                    DB::raw('(CASE when qlhs_profile_history.level_new <> "" and qlhs_profile.profile_year < "'.($year + 1).'-12-31" and (qlhs_profile.profile_leaveschool_date is null or ( qlhs_profile.profile_leaveschool_date > "'.($year + 1).'-05-31")) then 1 else 0 END) as hocky1_new'),
                        DB::raw('MAX(
                                CASE
                                WHEN (qlhs_profile_history.level_cur <> ""
                                AND qlhs_profile_subject.profile_subject_subject_id = 70
                                AND qlhs_profile.profile_year < "'.$year.'-12-31"
                                AND (
                                    qlhs_profile.profile_leaveschool_date IS NULL
                                    OR (
                                        qlhs_profile.profile_leaveschool_date > "'.$year.'-05-31"
                                    )
                                )
                                AND kp.months in (9,10,11,12) and kp.years = '.$year.' AND kp.id_doituong = 119 ) THEN
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
                                ) AND kp.months in (1,2,3,4,5) and kp.years = '.($year + 1).' AND kp.id_doituong = 119 ) 
                                THEN
                                    kp.value_m
                                ELSE
                                    0
                                END
                            ) AS DUTOAN'))
                ->groupBy('qlhs_profile_subject.profile_subject_subject_id', 'qlhs_profile.profile_id', 'qlhs_profile.profile_year', 'qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_leaveschool_date', 'kp.value_m', 'kp.id_doituong', 'kp.months', 'kp.years');

                $getDataTypeHBHSDTNT22 = DB::table(DB::raw("({$getDataTypeHBHSDTNT2->toSql()}) as m"))
                    ->mergeBindings( $getDataTypeHBHSDTNT2 )
                    ->select('m.profile_id',
                            'm.profile_year',
                            'm.level_old',
                            'm.level_cur',
                            'm.level_new',
                            'm.profile_leaveschool_date',
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
                            'm.hocky2_old', 
                            'm.hocky1_cur', 
                            'm.hocky2_cur', 
                            'm.hocky1_new')
                    ->get();

                // return $getDataTypeHTATHS22->toSql();

                if (!is_null($getDataTypeHBHSDTNT11) && !empty($getDataTypeHBHSDTNT11) && count($getDataTypeHBHSDTNT11) > 0) {
                    foreach ($getDataTypeHBHSDTNT11 as $value) {

                        $MONEYHBHSDTNT = $value->{'NHUCAU'};
                        $MONEYHBHSDTNTHK2 = $value->{'DUTOAN'};
                    }
                }

                if (!is_null($getDataTypeHBHSDTNT22) && !empty($getDataTypeHBHSDTNT22) && count($getDataTypeHBHSDTNT22) > 0) {
                    foreach ($getDataTypeHBHSDTNT22 as $value) {

                        $MONEYHBHSDTNT = $value->{'NHUCAU'};
                        $MONEYHBHSDTNTHK2 = $value->{'DUTOAN'};
                    }
                }
            }
            
//--------------------------------------------------------End Học bổng hs dân tộc nội trú----------------------------------------------------
            //array_push($results, $data);

            $status = 0;
            $TOTALMONEY = $MONEYMGHP + $MONEYCPHT + $MONEYHTAT + $MONEYHTBTTIENAN + $MONEYHTBTTIENO + $MONEYHTBTVHTT + $MONEYHSKTHOCBONG + $MONEYHSKTDDHT + $MONEYHSDTTS + $MONEYHTATHS + $MONEYHBHSDTNT;

            $TOTALMONEYHK2 = $MONEYMGHPHK2 + $MONEYCPHTHK2 + $MONEYHTATHK2 + $MONEYHTBTTIENANHK2 + $MONEYHTBTTIENOHK2 + $MONEYHTBTVHTTHK2 + $MONEYHSKTHOCBONGHK2 + $MONEYHSKTDDHTHK2 + $MONEYHSDTTSHK2 + $MONEYHTATHSHK2 + $MONEYHBHSDTNTHK2;

            $hocky1 = 'HK1'.$year;
            $hocky2 = 'HK2'.$year;

            

            if($end_year == 0 ){
                $getProfile = DB::table('qlhs_tonghopchedo')
                ->where('qlhs_thcd_profile_id', '=', $PROFILEID)
                ->where('qlhs_thcd_school_id', '=', $schoolid)
                ->where('qlhs_thcd_nam', '=', $year)
                ->get();
                if ($getProfile != null && count($getProfile) > 0 && $thcd_ID != null && $thcd_ID > 0) {
                    $status = DB::table('qlhs_tonghopchedo')
                    ->where('qlhs_thcd_profile_id', $PROFILEID)->where('qlhs_thcd_id', $thcd_ID)
                    ->update([
                        'qlhs_thcd_tien_nhucau_MGHP' => $MONEYMGHP, 
                        'qlhs_thcd_tien_nhucau_CPHT' => $MONEYCPHT, 
                        'qlhs_thcd_tien_nhucau_HTAT' => $MONEYHTAT, 
                        'qlhs_thcd_tien_nhucau_HTBT_TA' => $MONEYHTBTTIENAN, 
                        'qlhs_thcd_tien_nhucau_HTBT_TO' => $MONEYHTBTTIENO, 
                        'qlhs_thcd_tien_nhucau_HTBT_VHTT' => $MONEYHTBTVHTT, 
                        'qlhs_thcd_tien_nhucau_HSDTTS' => $MONEYHSDTTS, 
                        'qlhs_thcd_tien_nhucau_HSKT_HB' => $MONEYHSKTHOCBONG, 
                        'qlhs_thcd_tien_nhucau_HSKT_DDHT' => $MONEYHSKTDDHT,
                        'qlhs_thcd_tien_nhucau_HTATHS' => $MONEYHTATHS,
                        'qlhs_thcd_tien_nhucau_HBHSDTNT' => $MONEYHBHSDTNT,

                        'qlhs_thcd_tien_nhucau_MGHP_HK2' => $MONEYMGHPHK2,
                        'qlhs_thcd_tien_nhucau_CPHT_HK2' => $MONEYCPHTHK2,
                        'qlhs_thcd_tien_nhucau_HTAT_HK2' => $MONEYHTATHK2,
                        'qlhs_thcd_tien_nhucau_HTBT_TA_HK2' => $MONEYHTBTTIENANHK2,
                        'qlhs_thcd_tien_nhucau_HTBT_TO_HK2' => $MONEYHTBTTIENOHK2,
                        'qlhs_thcd_tien_nhucau_HTBT_VHTT_HK2' => $MONEYHTBTVHTTHK2,
                        'qlhs_thcd_tien_nhucau_HSDTTS_HK2' => $MONEYHSDTTSHK2,
                        'qlhs_thcd_tien_nhucau_HSKT_HB_HK2' => $MONEYHSKTHOCBONGHK2,
                        'qlhs_thcd_tien_nhucau_HSKT_DDHT_HK2' => $MONEYHSKTDDHTHK2,
                        'qlhs_thcd_tien_nhucau_HTATHS_HK2' => $MONEYHTATHSHK2,
                        'qlhs_thcd_tien_nhucau_HBHSDTNT_HK2' => $MONEYHBHSDTNTHK2,

                        'qlhs_thcd_tongtien_nhucau' => $TOTALMONEY,
                        'qlhs_thcd_tongtien_nhucau_HK2' => $TOTALMONEYHK2,
                        
                        'qlhs_thcd_school_id' => $schoolid,
                        'qlhs_thcd_nam' => $year,
                        'qlhs_thcd_usercreate_id' => $user]);

                        // qlhs_thcd_trangthai = 0,
                        // qlhs_thcd_trangthai_HK2 = 0,

                        // qlhs_thcd_trangthai_MGHP = 0,
                        // qlhs_thcd_trangthai_CPHT = 0,
                        // qlhs_thcd_trangthai_HTAT = 0,
                        // qlhs_thcd_trangthai_HTBT_TA = 0,
                        // qlhs_thcd_trangthai_HTBT_TO = 0,
                        // qlhs_thcd_trangthai_HTBT_VHTT = 0,
                        // qlhs_thcd_trangthai_HSKT_HB = 0,
                        // qlhs_thcd_trangthai_HSKT_DDHT = 0,
                        // qlhs_thcd_trangthai_HSDTTS = 0,
                        // qlhs_thcd_trangthai_HTATHS = 0,
                        // qlhs_thcd_trangthai_HBHSDTNT = 0,

                        // qlhs_thcd_trangthai_MGHP_HK2 = 0,
                        // qlhs_thcd_trangthai_CPHT_HK2 = 0,
                        // qlhs_thcd_trangthai_HTAT_HK2 = 0,
                        // qlhs_thcd_trangthai_HTBT_TA_HK2 = 0,
                        // qlhs_thcd_trangthai_HTBT_TO_HK2 = 0,
                        // qlhs_thcd_trangthai_HTBT_VHTT_HK2 = 0,
                        // qlhs_thcd_trangthai_HSKT_HB_HK2 = 0,
                        // qlhs_thcd_trangthai_HSKT_DDHT_HK2 = 0,
                        // qlhs_thcd_trangthai_HSDTTS_HK2 = 0,
                        // qlhs_thcd_trangthai_HTATHS_HK2 = 0,
                        // qlhs_thcd_trangthai_HBHSDTNT_HK2 = 0
                    return $status;
                }
                else {
                    $status = DB::table('qlhs_tonghopchedo')->insert([
                        'qlhs_thcd_tien_nhucau_MGHP' => $MONEYMGHP, 
                        'qlhs_thcd_tien_nhucau_CPHT' => $MONEYCPHT, 
                        'qlhs_thcd_tien_nhucau_HTAT' => $MONEYHTAT, 
                        'qlhs_thcd_tien_nhucau_HTBT_TA' => $MONEYHTBTTIENAN, 
                        'qlhs_thcd_tien_nhucau_HTBT_TO' => $MONEYHTBTTIENO, 
                        'qlhs_thcd_tien_nhucau_HTBT_VHTT' => $MONEYHTBTVHTT, 
                        'qlhs_thcd_tien_nhucau_HSDTTS' => $MONEYHSDTTS, 
                        'qlhs_thcd_tien_nhucau_HSKT_HB' => $MONEYHSKTHOCBONG, 
                        'qlhs_thcd_tien_nhucau_HSKT_DDHT' => $MONEYHSKTDDHT,
                        'qlhs_thcd_tien_nhucau_HTATHS' => $MONEYHTATHS,
                        'qlhs_thcd_tien_nhucau_HBHSDTNT' => $MONEYHBHSDTNT,

                        'qlhs_thcd_tien_nhucau_MGHP_HK2' => $MONEYMGHPHK2,
                        'qlhs_thcd_tien_nhucau_CPHT_HK2' => $MONEYCPHTHK2,
                        'qlhs_thcd_tien_nhucau_HTAT_HK2' => $MONEYHTATHK2,
                        'qlhs_thcd_tien_nhucau_HTBT_TA_HK2' => $MONEYHTBTTIENANHK2,
                        'qlhs_thcd_tien_nhucau_HTBT_TO_HK2' => $MONEYHTBTTIENOHK2,
                        'qlhs_thcd_tien_nhucau_HTBT_VHTT_HK2' => $MONEYHTBTVHTTHK2,
                        'qlhs_thcd_tien_nhucau_HSDTTS_HK2' => $MONEYHSDTTSHK2,
                        'qlhs_thcd_tien_nhucau_HSKT_HB_HK2' => $MONEYHSKTHOCBONGHK2,
                        'qlhs_thcd_tien_nhucau_HSKT_DDHT_HK2' => $MONEYHSKTDDHTHK2,
                        'qlhs_thcd_tien_nhucau_HTATHS_HK2' => $MONEYHTATHSHK2,
                        'qlhs_thcd_tien_nhucau_HBHSDTNT_HK2' => $MONEYHBHSDTNTHK2,

                        'qlhs_thcd_tongtien_nhucau' => $TOTALMONEY,
                        'qlhs_thcd_tongtien_nhucau_HK2' => $TOTALMONEYHK2,

                        'qlhs_thcd_trangthai' => 1,
                        'qlhs_thcd_trangthai_HK2' => 1,
                        'qlhs_thcd_trangthai_PD' => 0,
                        'qlhs_thcd_trangthai_PD_HK2' => 0,
                        'qlhs_thcd_trangthai_TD' => 0,
                        'qlhs_thcd_trangthai_TD_HK2' => 0,

                        'qlhs_thcd_profile_id' => $PROFILEID,
                        'qlhs_thcd_school_id' => $schoolid,
                        'qlhs_thcd_nam' => $year,

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

                        'qlhs_thcd_usercreate_id' => $user,

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
                        'qlhs_thcd_trangthai_HBHSDTNT_HK2' => 1,

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
                        'PheDuyet_trangthai_HBHSDTNT_HK2' => 0,

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
                        'ThamDinh_trangthai_HBHSDTNT_HK2' => 0
                    ]);

                    // $status = DB::table('qlhs_tonghopchedo')->insert([
                    //     'qlhs_tonghopchedo_tien_nhucau_MGHP' => $MONEYMGHPHK2, 
                    //     'qlhs_tonghopchedo_tien_nhucau_CPHT' => $MONEYCPHTHK2, 
                    //     'qlhs_tonghopchedo_tien_nhucau_HTAT' => $MONEYHTATHK2, 
                    //     'qlhs_tonghopchedo_tien_nhucau_HTBT_TA' => $MONEYHTBTTIENANHK2, 
                    //     'qlhs_tonghopchedo_tien_nhucau_HTBT_TO' => $MONEYHTBTTIENOHK2, 
                    //     'qlhs_tonghopchedo_tien_nhucau_HTBT_VHTT' => $MONEYHTBTVHTTHK2, 
                    //     'qlhs_tonghopchedo_tien_nhucau_HSKT_HB' => $MONEYHSKTHOCBONGHK2, 
                    //     'qlhs_tonghopchedo_tien_nhucau_HSKT_DDHT' => $MONEYHSKTDDHTHK2, 
                    //     'qlhs_tonghopchedo_tien_nhucau_HSDTTS' => $MONEYHSDTTSHK2,
                    //     'qlhs_tonghopchedo_tongtien' => $TOTALMONEYHK2,
                    //     'qlhs_tonghopchedo_profile_id' => $PROFILEID,
                    //     'qlhs_tonghopchedo_school_id' => $schoolid,
                    //     'qlhs_tonghopchedo_trangthai' => 0,
                    //     'qlhs_tonghopchedo_type' => $hocky2,
                    //     'qlhs_tonghopchedo_usercreate_id' => $user
                    // ]);

                    // $status = DB::table('qlhs_tonghopchedo')->insert([
                    //     'qlhs_tonghopchedo_tien_nhucau_MGHP' => ($MONEYMGHPHK2 + $MONEYMGHP), 
                    //     'qlhs_tonghopchedo_tien_nhucau_CPHT' => ($MONEYCPHTHK2 + $MONEYCPHT), 
                    //     'qlhs_tonghopchedo_tien_nhucau_HTAT' => ($MONEYHTATHK2 + $MONEYHTAT), 
                    //     'qlhs_tonghopchedo_tien_nhucau_HTBT_TA' => ($MONEYHTBTTIENANHK2 + $MONEYHTBTTIENAN), 
                    //     'qlhs_tonghopchedo_tien_nhucau_HTBT_TO' => ($MONEYHTBTTIENOHK2 + $MONEYHTBTTIENO), 
                    //     'qlhs_tonghopchedo_tien_nhucau_HTBT_VHTT' => ($MONEYHTBTVHTTHK2 + $MONEYHTBTVHTT), 
                    //     'qlhs_tonghopchedo_tien_nhucau_HSKT_HB' => ($MONEYHSKTHOCBONGHK2 + $MONEYHSKTHOCBONG), 
                    //     'qlhs_tonghopchedo_tien_nhucau_HSKT_DDHT' => ($MONEYHSKTDDHTHK2 + $MONEYHSKTDDHT), 
                    //     'qlhs_tonghopchedo_tien_nhucau_HSDTTS' => ($MONEYHSDTTSHK2 + $MONEYHSDTTS),
                    //     'qlhs_tonghopchedo_tongtien' => ($TOTALMONEYHK2 + $TOTALMONEY),
                    //     'qlhs_tonghopchedo_profile_id' => $PROFILEID,
                    //     'qlhs_tonghopchedo_school_id' => $schoolid,
                    //     'qlhs_tonghopchedo_trangthai' => 0,
                    //     'qlhs_tonghopchedo_type' => $year,
                    //     'qlhs_tonghopchedo_usercreate_id' => $user
                    // ]);
                }
            }else{
               // $y = array();
               //  $getMaxYears = DB::table('qlhs_tonghopchedo')->where('qlhs_thcd_profile_id',$PROFILEID)->select('qlhs_thcd_id', DB::raw('MAX(qlhs_thcd_nam) as nam'))->groupBy('qlhs_thcd_id')->first();
                // return $getMaxYears;
               // for ($i=(int)$year; $i < (int)$getMaxYears->nam + 1 ; $i++) { 
                    $check = DB::table('qlhs_profile_history')->where('history_profile_id',$PROFILEID)->where('history_year',$year.'-'.($year+1))->count();
                    //array_push($y,$i);   
                    if($check > 0){
                        //array_push($y,$i);   
                        $statuss = qlhs_tonghopchedo::where('qlhs_thcd_profile_id',$PROFILEID)->where('qlhs_thcd_nam', $year)->first();
                      //  return count($statuss);
                        if(count($statuss)==0){
                            $statuss = new qlhs_tonghopchedo();
                            $statuss->qlhs_thcd_nam = $year;
                            $statuss->qlhs_thcd_profile_id = $PROFILEID;
                            $statuss->qlhs_thcd_school_id = $schoolid;

                            $statuss->qlhs_thcd_trangthai = 1;
                            $statuss->qlhs_thcd_trangthai_HK2 = 1;
                            $statuss->qlhs_thcd_trangthai_PD = 0;
                            $statuss->qlhs_thcd_trangthai_PD_HK2 = 0;
                            $statuss->qlhs_thcd_trangthai_TD = 0;
                            $statuss->qlhs_thcd_trangthai_TD_HK2 = 0;
                            $statuss->qlhs_thcd_trangthai_MGHP = 1;
                            $statuss->qlhs_thcd_trangthai_CPHT = 1;
                            $statuss->qlhs_thcd_trangthai_HTAT = 1;
                            $statuss->qlhs_thcd_trangthai_HTBT_TA = 1;
                            $statuss->qlhs_thcd_trangthai_HTBT_TO = 1;
                            $statuss->qlhs_thcd_trangthai_HTBT_VHTT = 1;
                            $statuss->qlhs_thcd_trangthai_HSKT_HB = 1;
                            $statuss->qlhs_thcd_trangthai_HSKT_DDHT = 1;
                            $statuss->qlhs_thcd_trangthai_HSDTTS = 1;
                            $statuss->qlhs_thcd_trangthai_HTATHS = 1;
                            $statuss->qlhs_thcd_trangthai_HBHSDTNT = 1;
                            $statuss->qlhs_thcd_trangthai_MGHP_HK2 = 1;
                            $statuss->qlhs_thcd_trangthai_CPHT_HK2 = 1;
                            $statuss->qlhs_thcd_trangthai_HTAT_HK2 = 1;
                            $statuss->qlhs_thcd_trangthai_HTBT_TA_HK2 = 1;
                            $statuss->qlhs_thcd_trangthai_HTBT_TO_HK2 = 1;
                            $statuss->qlhs_thcd_trangthai_HTBT_VHTT_HK2 = 1;
                            $statuss->qlhs_thcd_trangthai_HSKT_HB_HK2 = 1;
                            $statuss->qlhs_thcd_trangthai_HSKT_DDHT_HK2 = 1;
                            $statuss->qlhs_thcd_trangthai_HSDTTS_HK2 = 1;
                            $statuss->qlhs_thcd_trangthai_HTATHS_HK2 = 1;
                            $statuss->qlhs_thcd_trangthai_HBHSDTNT_HK2 = 1;
                            $statuss->qlhs_thcd_trangthai_HTBT_TA_HK2 = 1;
                            $statuss->qlhs_thcd_trangthai_HTBT_TA_HK2 = 1;
                            $statuss->qlhs_thcd_trangthai_HTBT_TA_HK2 = 1;
                            $statuss->qlhs_thcd_trangthai_HTBT_TA_HK2 = 1;

                            $statuss->PheDuyet_trangthai_MGHP = 0;
                            $statuss->PheDuyet_trangthai_CPHT = 0;
                            $statuss->PheDuyet_trangthai_HTAT = 0;
                            $statuss->PheDuyet_trangthai_HTBT_TA = 0;
                            $statuss->PheDuyet_trangthai_HTBT_TO = 0;
                            $statuss->PheDuyet_trangthai_HTBT_VHTT = 0;
                            $statuss->PheDuyet_trangthai_HSKT_HB = 0;
                            $statuss->PheDuyet_trangthai_HSKT_DDHT = 0;
                            $statuss->PheDuyet_trangthai_HSDTTS = 0;
                            $statuss->PheDuyet_trangthai_HTATHS = 0;
                            $statuss->PheDuyet_trangthai_HBHSDTNT = 0;
                            $statuss->ThamDinh_trangthai_MGHP = 0;
                            $statuss->ThamDinh_trangthai_CPHT = 0;
                            $statuss->ThamDinh_trangthai_HTAT = 0;
                            $statuss->ThamDinh_trangthai_HTBT_TA = 0;
                            $statuss->ThamDinh_trangthai_HTBT_TO = 0;
                            $statuss->ThamDinh_trangthai_HTBT_VHTT = 0;
                            $statuss->ThamDinh_trangthai_HSKT_HB = 0;
                            $statuss->ThamDinh_trangthai_HSKT_DDHT = 0;
                            $statuss->ThamDinh_trangthai_HSDTTS = 0;
                            $statuss->ThamDinh_trangthai_HTATHS = 0;
                            $statuss->ThamDinh_trangthai_HBHSDTNT = 0;
                            $statuss->PheDuyet_trangthai_MGHP_HK2 = 0;
                            $statuss->PheDuyet_trangthai_CPHT_HK2 = 0;
                            $statuss->PheDuyet_trangthai_HTAT_HK2 = 0;
                            $statuss->PheDuyet_trangthai_HTBT_TA_HK2 = 0;
                            $statuss->PheDuyet_trangthai_HTBT_TO_HK2 = 0;
                            $statuss->PheDuyet_trangthai_HTBT_VHTT_HK2 = 0;
                            $statuss->PheDuyet_trangthai_HSKT_HB_HK2 = 0;
                            $statuss->PheDuyet_trangthai_HSKT_DDHT_HK2 = 0;
                            $statuss->PheDuyet_trangthai_HSDTTS_HK2 = 0;
                            $statuss->PheDuyet_trangthai_HTATHS_HK2 = 0;
                            $statuss->PheDuyet_trangthai_HBHSDTNT_HK2 = 0;
                            $statuss->ThamDinh_trangthai_MGHP_HK2 = 0;
                            $statuss->ThamDinh_trangthai_CPHT_HK2 = 0;
                            $statuss->ThamDinh_trangthai_HTAT_HK2 = 0;
                            $statuss->ThamDinh_trangthai_HTBT_TA_HK2 = 0;
                            $statuss->ThamDinh_trangthai_HTBT_TO_HK2 = 0;
                            $statuss->ThamDinh_trangthai_HTBT_VHTT_HK2 = 0;
                            $statuss->ThamDinh_trangthai_HSKT_HB_HK2 = 0;
                            $statuss->ThamDinh_trangthai_HSKT_DDHT_HK2 = 0;
                            $statuss->ThamDinh_trangthai_HSDTTS_HK2 = 0;
                            $statuss->ThamDinh_trangthai_HTATHS_HK2 = 0;
                            $statuss->ThamDinh_trangthai_HBHSDTNT_HK2 = 0;                            
                        }
                        $statuss->qlhs_thcd_tien_nhucau_MGHP = $MONEYMGHP;
                        $statuss->qlhs_thcd_tien_nhucau_CPHT = $MONEYCPHT; 
                        $statuss->qlhs_thcd_tien_nhucau_HTAT = $MONEYHTAT; 
                        $statuss->qlhs_thcd_tien_nhucau_HTBT_TA = $MONEYHTBTTIENAN; 
                        $statuss->qlhs_thcd_tien_nhucau_HTBT_TO =$MONEYHTBTTIENO; 
                        $statuss->qlhs_thcd_tien_nhucau_HTBT_VHTT = $MONEYHTBTVHTT; 
                        $statuss->qlhs_thcd_tien_nhucau_HSDTTS = $MONEYHSDTTS; 
                        $statuss->qlhs_thcd_tien_nhucau_HSKT_HB = $MONEYHSKTHOCBONG; 
                        $statuss->qlhs_thcd_tien_nhucau_HSKT_DDHT = $MONEYHSKTDDHT;
                        $statuss->qlhs_thcd_tien_nhucau_HTATHS = $MONEYHTATHS;
                        $statuss->qlhs_thcd_tien_nhucau_HBHSDTNT = $MONEYHBHSDTNT;

                        $statuss->qlhs_thcd_tien_nhucau_MGHP_HK2 = $MONEYMGHPHK2;
                        $statuss->qlhs_thcd_tien_nhucau_CPHT_HK2 = $MONEYCPHTHK2;
                        $statuss->qlhs_thcd_tien_nhucau_HTAT_HK2 = $MONEYHTATHK2;
                        $statuss->qlhs_thcd_tien_nhucau_HTBT_TA_HK2 = $MONEYHTBTTIENANHK2;
                        $statuss->qlhs_thcd_tien_nhucau_HTBT_TO_HK2 = $MONEYHTBTTIENOHK2;
                        $statuss->qlhs_thcd_tien_nhucau_HTBT_VHTT_HK2 = $MONEYHTBTVHTTHK2;
                        $statuss->qlhs_thcd_tien_nhucau_HSDTTS_HK2 = $MONEYHSDTTSHK2;
                        $statuss->qlhs_thcd_tien_nhucau_HSKT_HB_HK2 = $MONEYHSKTHOCBONGHK2;
                        $statuss->qlhs_thcd_tien_nhucau_HSKT_DDHT_HK2 = $MONEYHSKTDDHTHK2;
                        $statuss->qlhs_thcd_tien_nhucau_HTATHS_HK2 = $MONEYHTATHSHK2;
                        $statuss->qlhs_thcd_tien_nhucau_HBHSDTNT_HK2 = $MONEYHBHSDTNTHK2;
                        $statuss->qlhs_thcd_tongtien_nhucau = $TOTALMONEY;
                        $statuss->qlhs_thcd_tongtien_nhucau_HK2 = $TOTALMONEYHK2;
                        $statuss->qlhs_thcd_usercreate_id = $user;
                        $statuss->save();
                    }
               // }
               
                return 1;
            }
            //return $status.'=';//[0]['PROFILEID'];
        } catch (Exception $e) {
            return $e;
        }
    }


    public function loadMoneyBySubject($objData){
        try {
            $results = [];
            $resultsData = null;
            $data = [];
            $arrSub = [];
            $arrID = [];
            $arrID = explode("-", $objData);

            $date = '';
            $month = 0;

            if ($arrID[0] == 'HK1') {
                $date = '-12-31';
                $month = 4;
            }
            else if ($arrID[0] == 'HK2') {
                $date = '-06-01';
                $month = 5;
            }
            else if ($arrID[0] == 'CA') {
                $date = '-06-01';
                $month = 9;
            }

            // $arrData = $arrID[3];

            // return $arrID[3];

            if ($month > 0) {
                if (count($arrID) > 3) {
                    $arrSub = explode(",", $arrID[3]);

                    foreach ($arrSub as $value) {
                        if ($value == 35 || $value == 36 || $value == 73 || $value == 38 || $value == 39 || $value == 34 || $value == 40 || $value == 41) {

                            $result = DB::table('qlhs_kinhphinamhoc')
                                ->where('codeYear', '=', $arrID[1])
                                ->where('idTruong', '=', $arrID[2])
                                ->select('money')->groupBy('money')->first();

                            if (!is_null($result) && !empty($result) && count($result) > 0) {
                                $data['group_name'] = "Cấp bù học phí";
                                $data['money'] = $result->money;// * $month;
                                array_push($results, $data);

                                break;
                            }                        
                        }
                    }

                    if ($month > 0 && !is_null($arrID[3]) && !empty($arrID[3])) {
                        $resultsData = DB::table('qlhs_group')
                            ->leftJoin('qlhs_subject_history', 'subject_history_group_id', '=', 'group_id')
                            ->leftJoin('qlhs_kinhphidoituong', 'doituong_id', '=', 'group_id')
                            ->whereIn('subject_history_subject_id', $arrSub)
                            ->where('start_date', '<=', DB::raw('"'.$arrID[1].$date.'" AND (end_date >= "'.$arrID[1].$date.'" OR end_date is null)'))
                            // ->whereNull('end_date')
                            ->where('idTruong', '=', $arrID[2])
                            ->select('group_id', 'group_name', 'money')->get();
                        // return $resultsData;
                    }
                }

                if (!is_null($resultsData) && count($resultsData) > 0) {
                    foreach ($resultsData as $value) {
                        $data['group_id'] = $value->{'group_id'};
                        $data['group_name'] = $value->{'group_name'};
                        $data['money'] = $value->{'money'};// * $month;
                        array_push($results, $data);
                    }
                }
            }

            return $results;
        } catch (Exception $e) {
            return $e;
        }
    }
    public function updateByProfile(Request $rq){
        $result = [];
        try{
            $sub = explode('-', $rq->un_subject);
            $del = DB::table('qlhs_profile_subject')->where('profile_subject_profile_id',$rq->profile_id)->where('profile_start_time',$rq->start_time)->whereIn('profile_subject_subject_id',$sub)->delete();
            $data = explode('-', $rq->subject);
            foreach ($data as $key => $value) {
                $check = DB::table('qlhs_profile_subject')->where('profile_subject_subject_id',$value)->where('profile_subject_profile_id',$rq->profile_id)->where('profile_start_time',$rq->start_time);
                if($check->count() == 0){
                    if($rq->end_time != null && $rq->end_time != '' ){
                        $update = DB::table('qlhs_profile_subject')->insert(['profile_subject_profile_id' => $rq->profile_id,'profile_start_time' => $rq->start_time ,'profile_end_time' => (int)$rq->end_time,'profile_subject_subject_id' => (int)$value,'profile_subject_updatedate' => new datetime,'profile_subject_createdate' => new datetime,'profile_subject_create_userid' => Auth::user()->id,'profile_subject_update_userid' =>  Auth::user()->id,
                            'start_year' => $rq->start_year,'end_year' => $rq->end_year]); 
                    }else{
                        $update = DB::table('qlhs_profile_subject')->insert(['profile_subject_profile_id' => $rq->profile_id,'profile_start_time' => $rq->start_time ,'profile_subject_subject_id' => (int)$value,'profile_subject_updatedate' => new datetime,'profile_subject_createdate' => new datetime,'profile_subject_create_userid' => Auth::user()->id,'profile_subject_update_userid' =>  Auth::user()->id,
                            'start_year' => $rq->start_year]);
                    }
                }else{
                    $check->update(['profile_subject_updatedate' => new datetime]);
                }
            }
            $getMaxYear = DB::table('qlhs_tonghopchedo')->where('qlhs_thcd_profile_id', '=',$rq->profile_id)->select(DB::raw('MAX(qlhs_thcd_nam) as nam'))->first();
              //  return $getMaxYear->nam;
            if ($rq->start_year != null && $rq->start_year != '') {
                $end = (int)$getMaxYear->nam + 1;
                
                if($rq->end_year != null && $rq->end_year != '' ){
                    $end = $rq->end_year;
                }
                $schoolid = DB::table('qlhs_profile')->where('profile_id',$rq->profile_id)->select('profile_school_id')->first()->profile_school_id;
                for ($i=(int)$rq->start_year; $i < (int)$end  ; $i++) { 
                    //array_push($y, $i);
                     $this->tonghop($i,$rq->profile_id,$schoolid,null,$end);
                }
                //return $y;
            }
            $result['success'] = "Cập nhật thành công đối tượng";
        }catch(Exception $e){
            return $e;
            $result['error'] = "Cập nhật đối tượng có lỗi.Xin mời thử lại!";
        }
        return $result;
        
    }

    public function insertByProfile(Request $rq){
        $result = [];
        try{
            $date  = Carbon::now()->timestamp;
            $year = Carbon::now()->format('Y');
            if($rq->start_year != '' && $rq->start_year != null){
                $year = $rq->start_year;
            }else{
                $year = (int)$rq->start_year_cur+1;
            }
            
            $check = DB::table('qlhs_profile_subject')->where('profile_subject_profile_id',$rq->profile_id)->whereNull('profile_end_time')->where('start_year',(int)$year)->where('active',1)->select('profile_subject_profile_id','start_year')->groupBy('profile_subject_profile_id','start_year')->count();
            //return $rq->profile_id.'-'.$year;
            if($check==0){
                $del = DB::table('qlhs_profile_subject')->where('profile_subject_profile_id',$rq->profile_id)->whereNull('profile_end_time')->where('active',1)->update(['profile_end_time' => $date,'end_year' => $year,'profile_subject_updatedate' => new datetime]);
            }
            $data = explode('-', $rq->subject);
            foreach ($data as $key => $value) {
                if($check>0){
                    $update = DB::table('qlhs_profile_subject')->insert(['profile_subject_profile_id' => $rq->profile_id,'profile_start_time' =>  $date,'profile_subject_subject_id' => (int)$value,'profile_subject_updatedate' => new datetime,'profile_subject_createdate' => new datetime,'profile_subject_create_userid' => Auth::user()->id,'profile_subject_update_userid' =>  Auth::user()->id,
                    'start_year' => (int)$year+1]); 
                }else{
                    $update = DB::table('qlhs_profile_subject')->insert(['profile_subject_profile_id' => $rq->profile_id,'profile_start_time' =>  $date,'profile_subject_subject_id' => (int)$value,'profile_subject_updatedate' => new datetime,'profile_subject_createdate' => new datetime,'profile_subject_create_userid' => Auth::user()->id,'profile_subject_update_userid' =>  Auth::user()->id,
                    'start_year' => $year]); 
                }
            }
            // $getMaxYear = DB::table('qlhs_tonghopchedo')->where('qlhs_thcd_profile_id', '=',$rq->profile_id)
            //                                 ->select('qlhs_thcd_id', DB::raw('MAX(qlhs_thcd_nam) as nam'))->groupBy('qlhs_thcd_id')->first();
            // if ($rq->start_year != null && $rq->start_year != '') {
            //     $end = (int)Carbon::now()->format('Y') + 1;
            //     if((int)$rq->start_year > (int)Carbon::now()->format('Y')){
            //         $end =  (int)$rq->start_year + 1;    
            //     }else{
            //          $end = (int)$getMaxYear->nam + 1;    
            //     }
                
            //     $schoolid = DB::table('qlhs_profile')->where('profile_id',$rq->profile_id)->select('profile_school_id')->first()->profile_school_id;
            //     $this->tonghop($year,$rq->profile_id,$schoolid,null,$end);
            // }
            $getMaxYear = DB::table('qlhs_tonghopchedo')->where('qlhs_thcd_profile_id', '=',$rq->profile_id)->select(DB::raw('MAX(qlhs_thcd_nam) as nam'))->first();
              //  return $getMaxYear->nam;
            //if ($rq->start_year != null && $rq->start_year != '') {
                $end = (int)$getMaxYear->nam + 1;

                $schoolid = DB::table('qlhs_profile')->where('profile_id',$rq->profile_id)->select('profile_school_id')->first()->profile_school_id;
                for ($i=(int)$year; $i < (int)$end  ; $i++) { 
                    //array_push($y, $i);
                     $this->tonghop($i,$rq->profile_id,$schoolid,null,$end);
                }
                //return $y;
            //}
            $result['success'] = "Thay đổi thành công đối tượng";
        }catch(Exception $e){
            return $e;
            $result['error'] = "Thay đổi đối tượng có lỗi.Xin mời thử lại!";
        }
        return $result;
        
    }
    public function delSubject($time,$p_id){
        $rs = [];
        $check = DB::table('qlhs_profile_subject')->where('active',1)->where('profile_subject_profile_id',$p_id)->select('profile_subject_profile_id','start_year')->groupBy('profile_subject_profile_id','start_year')->get();
        //return count($check).'=';
        if(count($check) <= 1){
            $rs['fall'] = "Bản ghi cuối không thể xóa.";
        }else{
            try{
                $record_cur = DB::table('qlhs_profile_subject')->whereNull('end_year')->where('active',1)->where('profile_subject_profile_id',$p_id)->select('profile_subject_profile_id','start_year')->groupBy('profile_subject_profile_id','start_year')->first();
                //return count($record_old).'=';
                if(count($record_cur) > 0){
                    $del = DB::table('qlhs_profile_subject')->whereNull('end_year')->where('active',1)->where('profile_subject_profile_id',$p_id)->update(['active' => 0,'profile_subject_updatedate' => new datetime,'profile_subject_update_userid' => Auth::user()->id]);
                    $record_old  = DB::table('qlhs_profile_subject')->where('end_year',$record_cur->start_year)->where('active',1)->where('profile_subject_profile_id',$p_id);
                    if($record_old->count() > 0){
                        $record_old->update(['end_year' => null,'profile_end_time' => null]);
                    }else{
                        $record_old  = DB::table('qlhs_profile_subject')->where('end_year',(int)$record_cur->start_year-1)->where('active',1)->where('profile_subject_profile_id',$p_id)->update(['end_year' => null,'profile_end_time' => null]);
                    }
                    
                    $getMaxYear = DB::table('qlhs_tonghopchedo')->where('qlhs_thcd_profile_id', '=',$p_id)->select(DB::raw('MAX(qlhs_thcd_nam) as nam'))->first();

                        $end = (int)$getMaxYear->nam + 1;
                        $schoolid = DB::table('qlhs_profile')->where('profile_id',$p_id)->select('profile_school_id')->first()->profile_school_id;
                        $y = DB::table('qlhs_profile_subject')->whereNull('end_year')->where('active',1)->where('profile_subject_profile_id',$p_id)->select('profile_subject_profile_id','start_year')->groupBy('profile_subject_profile_id','start_year')->first();
                        for ($i=(int)$y->start_year; $i < (int)$end  ; $i++) { 
                             $this->tonghop($i,$p_id,$schoolid,null,$end);
                        }
                    
                }
                


                $rs['success'] = "Đã xóa bản ghi thành công.";
            }catch(Exception $e){
                $rs['error'] = "Đã xóa bản ghi có lỗi!Xin mời thử lại.".$e;
            }
        }
        return $rs;
    }

}
