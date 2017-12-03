<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\DanhSachTongHop;
use Carbon\Carbon;
use Excel;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

class HosoController extends Controller
{
    public function getList(){
       return view('admin.hoso.duyetdanhsach.listing');
       // / return view('layouts.intro');
    }
     public function thamdinh(){
        //return view('admin.hoso.thamdinhdanhsach.listing');
        return view('admin.hoso.thamdinhdanhsach.menu');
    }
    public function vanbanthamdinh(){
        //return view('admin.hoso.thamdinhdanhsach.listing');
        return view('admin.hoso.thamdinhdanhsach.inbox');
    }
    public function tonghopthamdinh(){
        //return view('admin.hoso.thamdinhdanhsach.listing');
        return view('admin.hoso.thamdinhdanhsach.listing');
    }
    public function getViewLapDS(){
        return view('admin.hoso.lapdanhsach.listing');
        // /return view('category/wards')->with('wards', $wards);
    }
    public function getView1(){
        return view('admin.hoso.lapdanhsach.miengiamhocphi');
    }
    public function getView2(){
        return view('admin.hoso.lapdanhsach.chiphihoctap');
    }
    public function getView3(){
        return view('admin.hoso.lapdanhsach.chinhsachuudai');
    }
    public function getView4(){
        return view('admin.hoso.lapdanhsach.hocsinhbantru');
    }
    public function getView5(){
        return view('admin.hoso.lapdanhsach.hocsinhdantocthieuso');
    }
    public function getView6(){
        return view('admin.hoso.lapdanhsach.hocsinhkhuyettat');
    }
    public function getView7(){
        return view('admin.hoso.lapdanhsach.hotroantruatreem');
    }
    public function getView8(){
        return view('admin.hoso.lapdanhsach.nguoinauan');
    }
    public function getView9(){
        return view('admin.hoso.lapdanhsach.tonghopchedo');
    }
    public function getViewPheDuyetNew(){
        return view('admin.hoso.thamdinhdanhsach.listingnew');
    }
    public function getViewThamDinhNew(){
        return view('admin.hoso.thamdinhdanhsach.listingunapproved');
    }
    public function download_attach($id){
        $data = DB::table('qlhs_thamdinh')->where('thamdinh_id','=',$id)->select('thamdinh_file_dinhkem', 'thamdinh_type')->first(); 
        $dir = storage_path().'/exceldownload/'.$data->thamdinh_type.'/'.$data->thamdinh_file_dinhkem;
        return response()->download($dir, $data->thamdinh_file_dinhkem);
    }
    public function download_file($id){
        $data = DB::table('qlhs_thamdinh')->where('thamdinh_id','=',$id)->select('thamdinh_file_dikem', 'thamdinh_type')->first(); 
        $dir = storage_path().'/files/'.$data->thamdinh_type.'/'.$data->thamdinh_file_dikem;
        return response()->download($dir, $data->thamdinh_file_dikem);
    }
    public function download_file_pheduyettonghop($id){
        $data = DB::table('qlhs_pheduyettonghop')->where('pheduyettonghop_id','=',$id)->select('pheduyettonghop_dinhkem')->first(); 
        $dir = storage_path().'/files/'.$data->pheduyettonghop_dinhkem;
        return response()->download($dir, $data->pheduyettonghop_dinhkem);
    }
    public function getPermissionThamdinh(){
        $json = [];
        $val = [];
        $data = DB::select('SELECT pu.module_id,pu.permission_id FROM permission_users pu WHERE pu.role_user_id = '.Auth::user()->id.' and pu.module_id = 11');
        foreach ($data as $key => $value) {
            $val[]= $value->permission_id.'';
        }
        $json['permission'] = $val;
        return $json;
    }  
    public function getViewThamdinh($id){
        $now = Carbon::now('Asia/Ho_Chi_Minh');
        $checkview = DB::table('qlhs_thamdinh')->where('thamdinh_id','=',$id)->update(['thamdinh_view' => 1,'thamdinh_user_view' => Auth::user()->id,'thamdinh_view_date' => $now]);
        $datas = DB::table('qlhs_thamdinh as  td')->join('qlhs_hosobaocao as hs','td.thamdinh_hoso_id' ,'=', 'hs.report_id')->join('users as u1','td.thamdinh_nguoigui','=','u1.id')->leftJoin('users as u2','td.thamdinh_nguoiduyet','=','u2.id')->join('qlhs_schools','hs.report_id_truong','=','qlhs_schools.schools_id')->select('td.thamdinh_id','hs.report_name','td.thamdinh_type','td.thamdinh_name','hs.report_attach_name','td.thamdinh_nguoigui','td.thamdinh_ngaygui','hs.report_nature','u1.first_name as first_name1','u1.last_name as last_name1','u2.first_name as first_name2','u2.last_name as last_name2','thamdinh_trangthai','qlhs_schools.schools_name','td.thamdinh_view_date','td.thamdinh_ngayduyet','thamdinh_file_dinhkem','thamdinh_file_dikem','thamdinh_content')->where('td.thamdinh_id','=',$id)->get();
        $js = [];
        $items = array();
      foreach ($datas as $key => $value) {
        $item['thamdinh_ngaygui'] = $this->rebuild_date('D, j  M  ,Y g:i A',$value->thamdinh_ngaygui);
        $item['report_name'] = $value->report_name;
        $item['thamdinh_name'] = $value->thamdinh_name;
        $item['thamdinh_content'] = $value->thamdinh_content != null ? $value->thamdinh_content : '';
        $item['report_nature'] = $value->report_nature;
        $item['thamdinh_type'] = $value->thamdinh_type;
        $item['thamdinh_view_date'] = $this->rebuild_date('D, j  M  ,Y g:i A',$value->thamdinh_view_date);
        if($value->thamdinh_ngayduyet!=null){
            $item['thamdinh_ngayduyet'] = $this->rebuild_date('D, j  M  ,Y g:i A',$value->thamdinh_ngayduyet);
        }else{
            $item['thamdinh_ngayduyet'] = '-';
        }
        $item['thamdinh_id'] = $value->thamdinh_id;
        $item['thamdinh_file_dikem'] = $value->thamdinh_file_dikem;
        $item['thamdinh_file_dinhkem'] = $value->thamdinh_file_dinhkem;

        $item['report_attach_name'] = $value->report_attach_name;
        $item['thamdinh_trangthai'] = $value->thamdinh_trangthai;
        $item['thamdinh_nguoigui'] = $value->last_name1.' '.$value->first_name1;
        $item['thamdinh_nguoiduyet'] = $value->last_name2.' '.$value->first_name2;
        $item['schools_name'] = $value->schools_name;
        $js[] = $item;
      }
        return $js;
    }

    public function getViewPheDuyet($id){
        $now = Carbon::now('Asia/Ho_Chi_Minh');
        $checkview = DB::table('qlhs_pheduyet')->where('pheduyet_id','=',$id)->update(['pheduyet_view' => 1,'pheduyet_user_view' => Auth::user()->id,'pheduyet_view_date' => $now]);
        $datas = DB::table('qlhs_pheduyet as pd')
        ->join('qlhs_pheduyettonghop as pdth','pd.pheduyet_hoso_thamdinh' ,'=', 'pdth.pheduyettonghop_id')
        ->join('users as u1','pd.pheduyet_nguoigui','=','u1.id')
        ->leftJoin('users as u2','pd.pheduyet_nguoiduyet','=','u2.id')
        ->select('pd.pheduyet_id', 'pdth.pheduyettonghop_id','pdth.pheduyettonghop_name','pd.pheduyet_type', 'pdth.type','pd.pheduyet_name','pdth.pheduyettonghop_dinhkem','pd.pheduyet_nguoigui','pd.pheduyet_ngaygui','pdth.pheduyettonghop_nature','pdth.pheduyettonghop_note','pdth.pheduyettonghop_note_approved', 'pdth.pheduyettonghop_file_approved','u1.first_name as first_name1','u1.last_name as last_name1','u2.first_name as first_name2','u2.last_name as last_name2','pheduyet_trangthai','pd.pheduyet_view_date','pd.pheduyet_ngayduyet','pheduyet_file_dinhkem','pheduyet_file_dikem','pheduyet_ghichu')
        ->where('pd.pheduyet_id','=',$id)->get();
         $js = [];
        $items = array();
        foreach ($datas as $key => $value) {
            $item['pheduyet_ngaygui'] = $this->rebuild_date('D, j  M  ,Y g:i A',$value->pheduyet_ngaygui);
            $item['pheduyettonghop_name'] = $value->pheduyettonghop_name;
            $item['pheduyet_name'] = $value->pheduyet_name;
            $item['pheduyet_ghichu'] = $value->pheduyet_ghichu != null ? $value->pheduyet_ghichu : '';
            $item['pheduyettonghop_nature'] = $value->pheduyettonghop_nature;
            $item['pheduyet_type'] = $value->pheduyet_type;
            $item['type'] = $value->type;
            $item['pheduyet_view_date'] = $this->rebuild_date('D, j  M  ,Y g:i A',$value->pheduyet_view_date);
            if($value->pheduyet_ngayduyet!=null){
                $item['pheduyet_ngayduyet'] = $this->rebuild_date('D, j  M  ,Y g:i A',$value->pheduyet_ngayduyet);
            }else{
                $item['pheduyet_ngayduyet'] = '-';
            }
            $item['pheduyet_id'] = $value->pheduyet_id;
            $item['pheduyet_file_dikem'] = $value->pheduyet_file_dikem;
            $item['pheduyet_file_dinhkem'] = $value->pheduyet_file_dinhkem;

            $item['pheduyettonghop_dinhkem'] = $value->pheduyettonghop_dinhkem;
            $item['pheduyet_trangthai'] = $value->pheduyet_trangthai;
            $item['pheduyet_nguoigui'] = $value->last_name1.' '.$value->first_name1;
            $item['pheduyet_nguoiduyet'] = $value->last_name2.' '.$value->first_name2;
            // $item['name_send'] = $value->schools_name;

            $item['pheduyet_note'] = $value->pheduyettonghop_note;
            $item['pheduyet_note_approved'] = $value->pheduyettonghop_note_approved;

            $item['pheduyettonghop_id'] = $value->pheduyettonghop_id;
            $item['pheduyettonghop_file_approved'] = $value->pheduyettonghop_file_approved;

            $js[] = $item;
        }
        return $js;
    }

    public function loadThamDinh(Request $req){
        Carbon::setLocale('vi');
        $json = [];
        $start = $req->input('start');
        $limit = $req->input('limit');
        $keySearch = $req->input('key');
        $current_user_id = Auth::user()->id;
      //$loadkinhphidoituong = [];
      
        $datas = DB::table('qlhs_thamdinh as  td')
        ->join('qlhs_hosobaocao as hs','td.thamdinh_hoso_id' ,'=', 'hs.report_id')
        ->leftJoin('qlhs_schools as school','school.schools_id','=','hs.report_id_truong')
        ->leftJoin('users as u1','td.thamdinh_nguoiduyet','=','u1.id')
        ->leftJoin('users as u2','td.thamdinh_user_view','=','u2.id')
        ->select('td.thamdinh_id','hs.report_name','td.thamdinh_view','td.thamdinh_type','hs.report_attach_name','td.thamdinh_nguoigui','td.thamdinh_ngaygui','hs.report_nature','u1.first_name as first_name1','u1.last_name as last_name1','u2.first_name as first_name2','u2.last_name as last_name2','thamdinh_nguoiduyet','thamdinh_ngayduyet','thamdinh_trangthai', 'school.schools_name');

        $datas->where(function($query) use($current_user_id){
            $query->orWhere('thamdinh_nguoi_nhan', 'LIKE', '%'.$current_user_id.'%')->orWhere('thamdinh_nguoi_cc', 'LIKE', '%'.$current_user_id.'%');
        });

        if (!is_null($keySearch) && !empty($keySearch)) {
            $datas = $datas->where('hs.report_name', 'LIKE', '%'.$keySearch.'%');
        }

        $json['totalRows'] = $datas->count();
        $json['startRecord'] = ($start);
        $json['numRows'] = $limit;
        $MM = $datas->orderBy('td.thamdinh_ngaygui', 'desc')->skip($start*$limit)->take($limit)->get();
        $js = [];
        $items = array();

        foreach ($MM as $key => $value) {
            $item['thamdinh_ngaygui'] =  $value->thamdinh_ngaygui;
            //Carbon::parse($value->thamdinh_ngaygui)->diffForHumans();
            $item['thamdinh_ngayduyet'] = $value->thamdinh_ngayduyet != null ? $value->thamdinh_ngayduyet : '-';

            $item['report_nature'] = $value->report_nature;
            $item['thamdinh_trangthai'] = $value->thamdinh_trangthai;
            $item['thamdinh_view'] = $value->thamdinh_view;
            $item['thamdinh_nguoiduyet'] = $value->last_name1.' '.$value->first_name1;
            $item['thamdinh_user_view'] = $value->last_name2.' '.$value->first_name2;
            $item['report_name'] = $value->report_name;
            $item['thamdinh_type'] = $value->thamdinh_type;
            $item['thamdinh_id'] = $value->thamdinh_id;
            $item['report_attach_name'] = $value->report_attach_name;
            $item['thamdinh_nguoigui'] = $value->thamdinh_nguoigui;
            $item['schools_name'] = $value->schools_name;
            $js[] = $item;
        }
        $json['data'] = $js;
        return $json;
    }

    public function loadVerifyThamDinh(Request $req){
        Carbon::setLocale('vi');
        $json = [];
        $start = $req->input('start');
        $limit = $req->input('limit');
        $current_date = Carbon::now('Asia/Ho_Chi_Minh');
        $current_user_id = Auth::user()->id;
        $keySearch = $req->input('key');

      //$ho_so = $req->input('ho_so');
      
        $datas = DB::table('qlhs_pheduyet as  td')
        ->join('qlhs_pheduyettonghop as hs','td.pheduyet_hoso_thamdinh' ,'=', 'hs.pheduyettonghop_id')
        ->leftJoin('users as u1','td.pheduyet_nguoiduyet','=','u1.id')
        ->leftJoin('users as u2','td.pheduyet_user_view','=','u2.id')
        ->select('td.pheduyet_id','hs.pheduyettonghop_name', 'td.pheduyet_view_date','td.pheduyet_type', 'hs.type','hs.pheduyettonghop_dinhkem','td.pheduyet_nguoigui','td.pheduyet_ngaygui','u1.first_name as first_name1','u1.last_name as last_name1','u2.first_name as first_name2','u2.last_name as last_name2','pheduyet_nguoiduyet','pheduyet_ngayduyet','pheduyet_trangthai','pheduyet_nature');

        $datas->where(function($query) use($current_user_id){
            $query->orWhere('pheduyet_nguoi_nhan', 'LIKE', '%'.$current_user_id.'%')->orWhere('pheduyet_nguoi_cc', 'LIKE', '%'.$current_user_id.'%');
        });

        if ($keySearch != null && $keySearch != "") {
            $datas->where('hs.pheduyettonghop_name', 'LIKE', '%'.$keySearch.'%');
        }

        $updatePheDuyet = DB::table('qlhs_pheduyet')->update(['pheduyet_user_view' => $current_user_id,'pheduyet_view_date' => $current_date]);
        $json['totalRows'] = $datas->count();
        $json['startRecord'] = ($start);
        $json['numRows'] = $limit;
        $MM = $datas->orderBy('td.pheduyet_ngaygui', 'desc')->skip($start*$limit)->take($limit)->get();
        
        $js = [];
        $items = array();

        foreach ($MM as $key => $value) {
            $item['pheduyet_ngaygui'] =  Carbon::parse($value->pheduyet_ngaygui )->diffForHumans();

            $item['pheduyet_ngayduyet'] = $value->pheduyet_ngayduyet != null ? $value->pheduyet_ngayduyet : '-';

            //$item['report_nature'] = $value->report_nature;
            $item['pheduyet_trangthai'] = $value->pheduyet_trangthai;
            $item['pheduyet_view_date'] = $value->pheduyet_view_date;
            $item['pheduyet_nguoiduyet'] = $value->last_name1.' '.$value->first_name1;
            $item['pheduyet_user_view'] = $value->last_name2.' '.$value->first_name2;
            $item['pheduyettonghop_name'] = $value->pheduyettonghop_name;
            $item['pheduyet_type'] = $value->pheduyet_type;
            $item['type'] = $value->type;
            $item['pheduyet_id'] = $value->pheduyet_id;
            $item['pheduyettonghop_dinhkem'] = $value->pheduyettonghop_dinhkem;
            $item['pheduyet_nguoigui'] = $value->pheduyet_nguoigui;
            $js[] = $item;
        }
        $json['data'] = $js;
        return $json;
    }

    public function loadDaPheDuyet(Request $req){
        Carbon::setLocale('vi');
        $json = [];
        $start = $req->input('start');
        $limit = $req->input('limit');
        $nam_hoc = $req->input('nam_hoc');
        $ho_so = $req->input('ho_so');
      
        $datas = DB::table('qlhs_thamdinh as td')
        ->join('qlhs_hosobaocao as hs','td.thamdinh_hoso_id' ,'=', 'hs.report_id')
        ->join('qlhs_schools','hs.report_id_truong','=','qlhs_schools.schools_id')
        ->leftJoin('users as u1','td.thamdinh_nguoiduyet','=','u1.id')
        ->leftJoin('users as u2','td.thamdinh_user_view','=','u2.id')
        ->where('report_year','LIKE', '%'.$nam_hoc.'%')
        ->where('report','LIKE', '%'.$ho_so.'%')
        ->where('thamdinh_trangthai','=',1)
        ->select('report_id','report_nature','report_name','schools_name','thamdinh_file_dikem','u1.last_name as last_name1','u1.first_name as first_name1','thamdinh_id','report_name');

        $json['totalRows'] = $datas->count();
        $json['startRecord'] = ($start);
        $json['numRows'] = $limit;
        $MM = $datas->orderBy('td.thamdinh_ngayduyet', 'desc')->skip($start*$limit)->take($limit)->get();
        $js = [];
        $items = array();

        foreach ($MM as $key => $value) {
            $item['thamdinh_id'] = $value->thamdinh_id;
            $item['report_nature'] = $value->report_nature;
            $item['report_name'] = $value->report_name;
            $item['schools_name'] = $value->schools_name;
            $item['thamdinh_file_dikem'] = $value->thamdinh_file_dikem;
            $item['thamdinh_nguoiduyet'] = $value->first_name1.' '.$value->last_name1 ;
            $js[] = $item;
        }

        $json['data'] = $js;
        return $json;
    }

    public function rebuild_date( $format, $time = 0 )
    {
        if ( ! $time ) $time = time();

        $lang = array();
        $lang['sun'] = 'Chủ nhật';
        $lang['mon'] = 'Thứ hai';
        $lang['tue'] = 'Thứ ba';
        $lang['wed'] = 'Thứ tư';
        $lang['thu'] = 'Thứ năm';
        $lang['fri'] = 'Thứ sáu';
        $lang['sat'] = 'Thứ bảy';
        $lang['sunday'] = 'Chủ nhật';
        $lang['monday'] = 'Thứ hai';
        $lang['tuesday'] = 'Thứ ba';
        $lang['wednesday'] = 'Thứ tư';
        $lang['thursday'] = 'Thứ năm';
        $lang['friday'] = 'Thứ sáu';
        $lang['saturday'] = 'Thứ bảy';
        $lang['january'] = 'tháng 01';
        $lang['february'] = 'tháng 02';
        $lang['march'] = 'tháng 03';
        $lang['april'] = 'tháng 04';
        $lang['may'] = 'tháng 05';
        $lang['june'] = 'tháng 06';
        $lang['july'] = 'tháng 07';
        $lang['august'] = 'tháng 08';
        $lang['september'] = 'tháng 09';
        $lang['october'] = 'tháng 10';
        $lang['november'] = 'tháng 11';
        $lang['december'] = 'tháng 12';
        $lang['jan'] = 'tháng 01';
        $lang['feb'] = 'tháng 02';
        $lang['mar'] = 'tháng 03';
        $lang['apr'] = 'tháng 04';
        $lang['may2'] = 'tháng 05';
        $lang['jun'] = 'tháng 06';
        $lang['jul'] = 'tháng 07';
        $lang['aug'] = 'tháng 08';
        $lang['sep'] = 'tháng 09';
        $lang['oct'] = 'tháng 10';
        $lang['nov'] = 'tháng 11';
        $lang['dec'] = 'tháng 12';

        $format = str_replace( "r", "D, d M Y H:i:s O", $format );
        $format = str_replace( array( "D", "M" ), array( "[D]", "[M]" ), $format );
        $return = Carbon::parse($time)->format($format);

        $replaces = array(
            '/\[Sun\](\W|$)/' => $lang['sun'] . "$1",
            '/\[Mon\](\W|$)/' => $lang['mon'] . "$1",
            '/\[Tue\](\W|$)/' => $lang['tue'] . "$1",
            '/\[Wed\](\W|$)/' => $lang['wed'] . "$1",
            '/\[Thu\](\W|$)/' => $lang['thu'] . "$1",
            '/\[Fri\](\W|$)/' => $lang['fri'] . "$1",
            '/\[Sat\](\W|$)/' => $lang['sat'] . "$1",
            '/\[Jan\](\W|$)/' => $lang['jan'] . "$1",
            '/\[Feb\](\W|$)/' => $lang['feb'] . "$1",
            '/\[Mar\](\W|$)/' => $lang['mar'] . "$1",
            '/\[Apr\](\W|$)/' => $lang['apr'] . "$1",
            '/\[May\](\W|$)/' => $lang['may2'] . "$1",
            '/\[Jun\](\W|$)/' => $lang['jun'] . "$1",
            '/\[Jul\](\W|$)/' => $lang['jul'] . "$1",
            '/\[Aug\](\W|$)/' => $lang['aug'] . "$1",
            '/\[Sep\](\W|$)/' => $lang['sep'] . "$1",
            '/\[Oct\](\W|$)/' => $lang['oct'] . "$1",
            '/\[Nov\](\W|$)/' => $lang['nov'] . "$1",
            '/\[Dec\](\W|$)/' => $lang['dec'] . "$1",
            '/Sunday(\W|$)/' => $lang['sunday'] . "$1",
            '/Monday(\W|$)/' => $lang['monday'] . "$1",
            '/Tuesday(\W|$)/' => $lang['tuesday'] . "$1",
            '/Wednesday(\W|$)/' => $lang['wednesday'] . "$1",
            '/Thursday(\W|$)/' => $lang['thursday'] . "$1",
            '/Friday(\W|$)/' => $lang['friday'] . "$1",
            '/Saturday(\W|$)/' => $lang['saturday'] . "$1",
            '/January(\W|$)/' => $lang['january'] . "$1",
            '/February(\W|$)/' => $lang['february'] . "$1",
            '/March(\W|$)/' => $lang['march'] . "$1",
            '/April(\W|$)/' => $lang['april'] . "$1",
            '/May(\W|$)/' => $lang['may'] . "$1",
            '/June(\W|$)/' => $lang['june'] . "$1",
            '/July(\W|$)/' => $lang['july'] . "$1",
            '/August(\W|$)/' => $lang['august'] . "$1",
            '/September(\W|$)/' => $lang['september'] . "$1",
            '/October(\W|$)/' => $lang['october'] . "$1",
            '/November(\W|$)/' => $lang['november'] . "$1",
            '/December(\W|$)/' => $lang['december'] . "$1" );

        return preg_replace( array_keys( $replaces ), array_values( $replaces ), $return );
    }

    public function sendThamDinh(Request $req){
        $note = $req->input('NOTE') ? $req->input('NOTE') : "";
        //$fileRevert = $req->input('FILEREVERT');
        $time = time();

        $files =  $req->file('file');
        $dir = storage_path().'/files';
        $filename_attach = "";

        if(trim($files) != ""){
            $filenames = 'ThamDinh'.'-'.$files->getClientOriginalName();
            $filename_attach = $filenames;
        }

        if(trim($files) != ""){
            if(file_exists($dir.'/'. $filename_attach)){
                $files->move($dir, $filename_attach.'-'.$time); 
            }else{
                $files->move($dir, $filename_attach);   
            }
        }

        $now = Carbon::now('Asia/Ho_Chi_Minh');
        $checkview = DB::table('qlhs_thamdinh')->where('thamdinh_id','=',$req->input('id'))->update(['thamdinh_trangthai' => 1,'thamdinh_nguoiduyet' => Auth::user()->id,'thamdinh_ngayduyet' => $now]);
        $data = DB::table('qlhs_thamdinh')->where('thamdinh_id','=',$req->input('id'))->select('thamdinh_hoso_id')->first();
        $update = DB::table('qlhs_hosobaocao')->where('report_id','=',$data->thamdinh_hoso_id)->update(['report_status' => 3,'report_ngaytra' => $now,'report_nguoitra' => Auth::user()->id, 'report_note' => $note, 'report_file_revert' => $filename_attach]);
        $json['success'] = "Phê duyệt thành công.";
        return $json;
    }

    public function resendThamDinh(Request $req){
        $note = $req->input('NOTE') ? $req->input('NOTE') : "";
        //$fileRevert = $req->input('FILEREVERT');
        $time = time();

        $files =  $req->file('file');
        $dir = storage_path().'/files';
        $filename_attach = "";

        if(trim($files) != ""){
            $filenames = 'ThamDinh'.'-'.$files->getClientOriginalName();
            $filename_attach = $filenames;
        }

        if(trim($files) != ""){
            if(file_exists($dir.'/'. $filename_attach)){
                $files->move($dir, $filename_attach.'-'.$time); 
            }else{
                $files->move($dir, $filename_attach);   
            }
        }

        $now = Carbon::now('Asia/Ho_Chi_Minh');
        $checkview = DB::table('qlhs_thamdinh')->where('thamdinh_id','=',$req->input('id'))->update(['thamdinh_trangthai' => 2,'thamdinh_nguoichuyenlai' => Auth::user()->id,'thamdinh_ngaychuyenlai' => $now,'thamdinh_ngayduyet' => null,'thamdinh_nguoiduyet' => null]);
        $data = DB::table('qlhs_thamdinh')->where('thamdinh_id','=',$req->input('id'))->select('thamdinh_hoso_id')->first();
        $update = DB::table('qlhs_hosobaocao')->where('report_id','=',$data->thamdinh_hoso_id)->update(['report_status' => 2,'report_ngaytra' => $now,'report_nguoitra' => Auth::user()->id, 'report_note' => $note, 'report_file_revert' => $filename_attach]);
        $json['success'] = "Danh sách đã chuyển lại";
        return $json;
    }
    /// Phê duyệt

    public function getPermissionPheDuyet(){
        $json = [];
        $val = [];
        $data = DB::select('SELECT pu.module_id,pu.permission_id FROM permission_users pu WHERE pu.role_user_id = '.Auth::user()->id.' and pu.module_id = 10');
        foreach ($data as $key => $value) {
            $val[]= $value->permission_id.'';
        }
        $json['permission'] = $val;
        return $json;
    }

//------------------------------------------------------------------PHÊ DUYỆT-----------------------------------------------------------------
    public function loadPheDuyet(Request $req){
        Carbon::setLocale('vi');
        $json = [];
      $start = $req->input('start');
      $limit = $req->input('limit');
      //$loadkinhphidoituong = [];
      
      $datas = DB::table('qlhs_thamdinh as  td')->join('qlhs_hosobaocao as hs','td.thamdinh_hoso_id' ,'=', 'hs.report_id')->leftJoin('users as u1','td.thamdinh_nguoiduyet','=','u1.id')->leftJoin('users as u2','td.thamdinh_user_view','=','u2.id')->select('td.thamdinh_id','hs.report_name','td.thamdinh_view','td.thamdinh_type','hs.report_attach_name','td.thamdinh_nguoigui','td.thamdinh_ngaygui','hs.report_nature','u1.first_name as first_name1','u1.last_name as last_name1','u2.first_name as first_name2','u2.last_name as last_name2','thamdinh_nguoiduyet','thamdinh_ngayduyet','thamdinh_trangthai');
      $json['totalRows'] = $datas->count();
      $json['startRecord'] = ($start);
      $json['numRows'] = $limit;
      $MM= $datas->orderBy('td.thamdinh_ngaygui', 'desc')->skip($start*$limit)->take($limit)->get();
        $js = [];
        $items = array();
      foreach ($MM as $key => $value) {
        $item['thamdinh_ngaygui'] =  Carbon::parse($value->thamdinh_ngaygui)->diffForHumans();

        $item['thamdinh_ngayduyet'] = $value->thamdinh_ngayduyet != null ? $value->thamdinh_ngayduyet : '-';

        $item['report_nature'] = $value->report_nature;
        $item['thamdinh_trangthai'] = $value->thamdinh_trangthai;
        $item['thamdinh_view'] = $value->thamdinh_view;
        $item['thamdinh_nguoiduyet'] = $value->last_name1.' '.$value->first_name1;
        $item['thamdinh_user_view'] = $value->last_name2.' '.$value->first_name2;
        $item['report_name'] = $value->report_name;
        $item['thamdinh_type'] = $value->thamdinh_type;
        $item['thamdinh_id'] = $value->thamdinh_id;
        $item['report_attach_name'] = $value->report_attach_name;
        $item['thamdinh_nguoigui'] = $value->thamdinh_nguoigui;
        $js[] = $item;
      }
       $json['data'] = $js;
        return $json;
    }

    public function insertDataTotal(Request $req){
        $json = [];
        $files =  $req->file('file');
        $type = $req->input('type');
        $list = $req->input('list');
        $namhoc = $req->input('nam_hoc');
        $note = $req->input('note');
        $user = Auth::user()->id;
        $now = Carbon::now('Asia/Ho_Chi_Minh');
        $thamdinh_name = $req->input('name');
        $thamdinh_user_sign = $req->input('create_sign');
        $user_name = $req->input('create_name');
        $time = time();
        $dir = storage_path().'/files';
        $status = $req->input('status');
        $filename_attach = "";

        if(trim($files) != ""){
            $filenames = 'File-'.$user.'-'.$files->getClientOriginalName();
            $filename_attach = $filenames;
        }

        if(trim($files) != ""){
            if(file_exists($dir.'/'. $filename_attach)){
                $files->move($dir, $filename_attach.'-'.$time); 
            }else{
                $files->move($dir, $filename_attach);   
            }
        }

        $getName = DB::table('qlhs_pheduyettonghop')->where('pheduyettonghop_name', 'LIKE', '%'.$thamdinh_name.'%')->get();
        if (!is_null($getName) && !empty($getName) && count($getName) > 0) {
            $json['error'] = "Tên danh sách đã tồn tại, vui lòng nhập tên khác!";
            return $json;
        }

        $insert_pheduyettonghop_id = DB::table('qlhs_pheduyettonghop')->insertGetId([
                    'pheduyettonghop_name' => $thamdinh_name,
                    'pheduyettonghop_year'=>$namhoc,
                    'type'=>$type,
                    'pheduyettonghop_code' => $type.'-'.$user.'-'.$namhoc.''.((int)$namhoc+1).'-'.$time,
                    'pheduyettonghop_ngaylap' => $now,
                    'pheduyettonghop_nguoilap' => $user_name,
                    'created_user' => $user,
                    'pheduyettonghop_nguoiky' => $thamdinh_user_sign,
                    'pheduyettonghop_dinhkem' => $filename_attach,
                    'pheduyettonghop_ghichu' => $note,
                    'pheduyettonghop_type' => $status,
                    'pheduyettonghop_danhsach' => $list
                ]);

        if (!is_null($insert_pheduyettonghop_id) && $insert_pheduyettonghop_id > 0) {
            $this->exportExcelPheDuyetTongHop($insert_pheduyettonghop_id);

            $get_type_code = DB::table('qlhs_pheduyettonghop')->where('pheduyettonghop_id', '=', $insert_pheduyettonghop_id)->select('pheduyettonghop_code')->first();

            if (file_exists(storage_path().'/exceldownload/THAMDINH/'.$get_type_code->pheduyettonghop_code.'.xlsx')) {
                $json['success'] = "Tổng hợp danh sách thành công!";
            }
            else {
                $deletePheDuyetTongHop = DB::table('qlhs_pheduyettonghop')->where('pheduyettonghop_id', '=', $insert_pheduyettonghop_id)->delete();
                $json['error'] = "Tổng hợp danh sách lỗi";
            }
        }
        else {$json['error'] = "Tổng hợp danh sách lỗi";}

        return $json;       
    }

    public function loadData(Request $req){
        $json = [];
        $start = $req->input('start');
        $limit = $req->input('limit');
        $datas = DB::table('qlhs_pheduyettonghop');
        $json['totalRows'] = $datas->count();
        
        // foreach ($datas->$key as $data) {
            $json['startRecord'] = ($start);
            $json['numRows'] = $limit;
        //  $json['datatable'] = $data->data;
        // }
        $json['data'] = $datas->orderBy('pheduyettonghop_id','desc')->skip($start*$limit)->take($limit)->get();;
        return $json;
    }

    public function sendPheDuyet(Request $request){
        try{

            $thamdinh_id = $request->input('id');
            $list_id_nguoinhan = $request->input('list_id_nguoinhan');
            $list_id_cc = $request->input('list_id_cc');
            
            $time = time();
            $hosopheduyet = DanhSachTongHop::find($thamdinh_id);
            //$hosopheduyet = DB::table('qlhs_pheduyettonghop')->where('pheduyettonghop_id','=',$id)->first();
            $hosopheduyet->pheduyettonghop_status = 1;
            $hosopheduyet->pheduyettonghop_verify = 0;
            $hosopheduyet->pheduyettonghop_nguoigui = Auth::user()->id;
            $hosopheduyet->save();
           // $this->exportforSchool($id,true);
            $now = Carbon::now('Asia/Ho_Chi_Minh');
            $mghp = DB::table('qlhs_pheduyet')->insert([
                    'pheduyet_name' => $hosopheduyet->type.'-'.$time,
                    'pheduyet_type' => $hosopheduyet->pheduyettonghop_code,
                    'pheduyet_hoso_thamdinh' => $thamdinh_id,
                    'pheduyet_trangthai' => 0,
                    'pheduyet_ghichu' => $hosopheduyet->pheduyettonghop_note,
                    'pheduyet_ngaygui' => $now,
                    'pheduyet_file_dinhkem' => $hosopheduyet->pheduyettonghop_code.'.xlsx',
                    'pheduyet_file_dikem' => $hosopheduyet->pheduyettonghop_dinhkem, 
                    'pheduyet_nguoigui' => Auth::user()->id,
                    'pheduyet_nguoi_nhan' => $list_id_nguoinhan,
                    'pheduyet_nguoi_cc' => $list_id_cc
                    ]);
            $json['success'] = "Gửi thành công.";
        }catch(\Exception $e){
            $json['error'] = "Gửi lỗi.".$e;
        }
        return $json;
    }

    public function updatePheDuyet(Request $req){
        $result = [];
        try{
            $pheduyet_id = $req->input('id');
            $pheduyet_note = $req->input('note');
            $files = $req->file('file');
            $pheduyet_note_approved = $req->input('noteapproved');
            $files_approved = $req->file('fileapproved');

            $current_date = Carbon::now('Asia/Ho_Chi_Minh');
            $current_user_id = Auth::user()->id;
            $time = time();
            $dir = storage_path().'/files';
            $status = $req->input('status');
            $filename_attach = "";

            if(trim($files) != ""){
                $filenames = 'ThamDinh'.'-'.$files->getClientOriginalName();
                $filename_attach = $filenames;
            }

            if(trim($files) != ""){
                if(file_exists($dir.'/'. $filename_attach)){
                    $files->move($dir, $filename_attach.'-'.$time); 
                }else{
                    $files->move($dir, $filename_attach);   
                }
            }

            if(trim($files_approved) != ""){
                $filenames = 'QuyetDinhThamDinh'.'-'.$files_approved->getClientOriginalName();
                $filename_attach = $filenames;
            }

            if(trim($files_approved) != ""){
                if(file_exists($dir.'/'. $filename_attach)){
                    $files_approved->move($dir, $filename_attach.'-'.$time); 
                }else{
                    $files_approved->move($dir, $filename_attach);   
                }
            }

            $getPheduyet = DB::table('qlhs_pheduyet')->where('pheduyet_id', '=', $pheduyet_id)->select('pheduyet_type')->first();
            
            $updatePheDuyet = DB::table('qlhs_pheduyet')->where('pheduyet_id', '=', $pheduyet_id)->update(['pheduyet_nguoiduyet' => $current_user_id,'pheduyet_ngayduyet' => $current_date, 'pheduyet_trangthai' => 1]);

            $updatePheDuyetTongHop = DB::table('qlhs_pheduyettonghop')->where('pheduyettonghop_code', 'LIKE', '%'.$getPheduyet->pheduyet_type.'%')->update(['pheduyettonghop_status' => 3, 'pheduyettonghop_note' => $pheduyet_note, 'pheduyettonghop_file_revert' => $filename_attach, 'pheduyettonghop_note_approved' => $pheduyet_note_approved, 'pheduyettonghop_file_approved' => $files_approved]);

            if ($updatePheDuyet > 0 && $updatePheDuyetTongHop > 0) {
                $result['success'] = "Thẩm định thành công!";
            }
            else { $result['error'] = "Thẩm định thất bại!"; }

            return $result;
        }catch(\Exception $e){
            return $e;
        }
    }

    public function revertPheDuyet(Request $req){
        $result = [];
        try{
            $pheduyet_id = $req->input('id');
            $pheduyet_note = $req->input('note');
            $files = $req->file('file');
            $current_date = Carbon::now('Asia/Ho_Chi_Minh');
            $current_user_id = Auth::user()->id;
            $time = time();
            $dir = storage_path().'/files';
            $status = $req->input('status');
            $filename_attach = "";

            if(trim($files) != ""){
                $filenames = 'PheDuyet'.'-'.$files->getClientOriginalName();
                $filename_attach = $filenames;
            }

            if(trim($files) != ""){
                if(file_exists($dir.'/'. $filename_attach)){
                    $files->move($dir, $filename_attach.'-'.$time); 
                }else{
                    $files->move($dir, $filename_attach);   
                }
            }

            $getPheduyet = DB::table('qlhs_pheduyet')->where('pheduyet_id', '=', $pheduyet_id)->select('pheduyet_type')->first();
            
            $updatePheDuyet = DB::table('qlhs_pheduyet')->where('pheduyet_id', '=', $pheduyet_id)->update(['pheduyet_nguoitralai' => $current_user_id, 'pheduyet_ngaytralai' => $current_date, 'pheduyet_trangthai' => 3]);

            $updatePheDuyetTongHop = DB::table('qlhs_pheduyettonghop')->where('pheduyettonghop_code', 'LIKE', '%'.$getPheduyet->pheduyet_type.'%')->update(['pheduyettonghop_nguoitralai' => $current_user_id, 'pheduyettonghop_ngaytralai' => $current_date, 'pheduyettonghop_status' => 2, 'pheduyettonghop_note' => $pheduyet_note, 'pheduyettonghop_file_revert' => $filename_attach]);

            if ($updatePheDuyet > 0 && $updatePheDuyetTongHop > 0) {
                $result['success'] = "Danh sách đã trả lại!";
            }
            else { $result['error'] = "Trả lại danh sách thất bại!"; }

            return $result;
        }catch(\Exception $e){
            return $e;
        }
    }
//-------------------------------------------------------Download File Revert-----------------------------------------------------------------
    public function loadNoteAndFile_Pheduyet($id){
        try {
            $getPheDuyetTongHop = DB::table('qlhs_pheduyettonghop')->where('pheduyettonghop_id', '=', $id)
            ->select('pheduyettonghop_id', 'pheduyettonghop_note', 'pheduyettonghop_file_revert', 'pheduyettonghop_note_approved', 'pheduyettonghop_file_approved')->get();

            return $getPheDuyetTongHop;
        } catch (Exception $e) {
            
        }
    }

    public function download_file_Revert($id){
        $data = DB::table('qlhs_pheduyettonghop')->where('pheduyettonghop_id','=',$id)->select('pheduyettonghop_file_revert')->first(); 
        $dir = storage_path().'/files/'.$data->pheduyettonghop_file_revert;
        return response()->download($dir, $data->pheduyettonghop_file_revert);
    }

    public function download_file_Approved($id){
        $data = DB::table('qlhs_pheduyettonghop')->where('pheduyettonghop_id','=',$id)->select('pheduyettonghop_file_approved')->first(); 
        $dir = storage_path().'/files/'.$data->pheduyettonghop_file_approved;
        return response()->download($dir, $data->pheduyettonghop_file_approved);
    }
//-------------------------------------------------------Download File and Delete Report------------------------------------------------------
    public function download_file_pheduyet($id){
        $data = DB::table('qlhs_pheduyet')->where('pheduyet_id','=',$id)->select('pheduyet_file_dinhkem', 'pheduyet_type')->first(); 
        $dir = storage_path().'/exceldownload/THAMDINH/'.$data->pheduyet_type.'.xlsx';
        return response()->download($dir, $data->pheduyet_type.'.xlsx');
    }

    public function download_ExcelExport($id){
        $data = DB::table('qlhs_pheduyettonghop')->where('pheduyettonghop_id','=',$id)->select('pheduyettonghop_code')->first();
        $dir = storage_path().'/exceldownload/THAMDINH/'.$data->pheduyettonghop_code.'.xlsx';
        return response()->download($dir, $data->pheduyettonghop_code.'.xlsx');
    }

    public function delete_Report($id){
        
        $results = [];

        //$dir = storage_path().'/files/'.$data->report;
        if(!is_null($id) && $id > 0){
            $data = DB::table('qlhs_pheduyettonghop')->where('pheduyettonghop_id','=',$id)->select('pheduyettonghop_code', 'pheduyettonghop_dinhkem')->first();

            // if(file_exists($dir.'/'. $data->report_attach_name)){ 
            //     File::delete($dir.'/'. $data->report_attach_name);  
            // }

            if (file_exists(storage_path().'/exceldownload/THAMDINH/'.$data->pheduyettonghop_code.'.xlsx')) {
                File::delete(storage_path().'/exceldownload/THAMDINH/'.$data->pheduyettonghop_code.'.xlsx');
            }
            $deleteReport = DB::table('qlhs_pheduyettonghop')->where('pheduyettonghop_id','=',$id)->delete();

            if($deleteReport > 0){
                $results['success'] = 'Xóa danh sách thành công!';
            }else{
                $results['error'] = 'Xóa danh sách bị lỗi!';
            }
        }else{
            $results['error'] = 'Danh sách không tồn tại!';
        }

        return $results;
    }

//Get Data Export Excel--------------------------------------------------------------------------------------------------------------------------------------
    public function exportExcelPheDuyetTongHop($id)
    {
        try {
            $arrHosoID = [];
            $reporttype = "";
            $reportname = "";
            $reportyear = "";
            $resultsData = [];

            if (!is_null($id) && !empty($id) && $id > 0) {
                $getPheDuyetTongHop = DB::table('qlhs_pheduyettonghop')->where('pheduyettonghop_id', '=', $id)->select('pheduyettonghop_year', 'pheduyettonghop_danhsach', 'pheduyettonghop_code', 'type')->first();

                $arrIDThamDinh = [];
                $arrIDThamDinh = explode('-', $getPheDuyetTongHop->pheduyettonghop_danhsach);
                $reporttype = $getPheDuyetTongHop->type;
                $reportname = $getPheDuyetTongHop->pheduyettonghop_code;
                $reportyear = $getPheDuyetTongHop->pheduyettonghop_year;
                
                if (!is_null($arrIDThamDinh) && count($arrIDThamDinh) > 0) {
                    foreach ($arrIDThamDinh as $thamdinhid) {
                        $getThamDinh = DB::table('qlhs_thamdinh')->where('thamdinh_id', '=', $thamdinhid)->select('thamdinh_hoso_id')->first();

                        if (!is_null($getThamDinh) && !empty($getThamDinh) && count($getThamDinh) > 0) {
                            $arrHosoID[] = $getThamDinh->thamdinh_hoso_id;
                        }
                    }
                    // for ($i = 0; $i < count($arrIDThamDinh); $i++) { 
                    //     $getThamDinh = DB::table('qlhs_thamdinh')->where('thamdinh_id', '=', $arrIDThamDinh[$i])->select('thamdinh_hoso_id')->first();
                    //     if (!is_null($getThamDinh) && !empty($getThamDinh) && count($getThamDinh) > 0 && $i != count($arrIDThamDinh) - 1) {
                    //         $strHosoID .= $getThamDinh->thamdinh_hoso_id . ",";
                    //     }
                    //     else { $strHosoID .= $getThamDinh->thamdinh_hoso_id; }
                    // }
                }
            }

            if (!is_null($arrHosoID) && !empty($arrHosoID) && count($arrHosoID) > 0) {
                $getHosoBaocao = DB::table('qlhs_hosobaocao')
                ->join('qlhs_schools', 'qlhs_schools.schools_id', '=', 'qlhs_hosobaocao.report_id_truong')
                ->whereIn('qlhs_hosobaocao.report_id', $arrHosoID)
                ->select('qlhs_schools.schools_unit_id', 'qlhs_schools.schools_name', 'qlhs_hosobaocao.report_type', 'qlhs_hosobaocao.report_year')->get();

                $getUnit = DB::table('qlhs_unit')
                ->leftJoin('qlhs_schools', 'qlhs_schools.schools_unit_id', '=', 'qlhs_unit.unit_id')
                ->leftJoin('qlhs_hosobaocao', 'qlhs_hosobaocao.report_id_truong', '=', 'qlhs_schools.schools_id')
                ->whereIn('qlhs_hosobaocao.report_id', $arrHosoID)
                ->select('unit_id', 'unit_name')->groupBy('unit_id', 'unit_name')->get();

                $resultsData['unit'] = $getUnit;
                $resultsData['hosobaocao'] = $getHosoBaocao;
                $resultsData['reportyear'] = $reportyear;
                //return $getHosoBaocao;

                if (!is_null($getHosoBaocao) && count($getHosoBaocao) > 0) {
                    
                    if (!is_null($reporttype) && !empty($reporttype) && ($reporttype == "NGNA" || $reporttype == "TONGHOP")) {
                        $resultsData['DataNGNAorTONGHOP'] = $this->getData($getHosoBaocao, $reporttype);
                    }
                    else {
                        $resultsData['a'] = $this->getData($getHosoBaocao, $reporttype, 1);

                        $resultsData['b'] = $this->getData($getHosoBaocao, $reporttype, 2);

                        $resultsData['c'] = $this->getData($getHosoBaocao, $reporttype, 3);
                    }
                }
            }

            // return $resultsData;
            // foreach ($resultsData['a'] as $value) {
            //     return $value->type_code;
            // }

            if (!is_null($resultsData) && count($resultsData) > 0 && !is_null($reporttype) && !empty($reporttype) && !is_null($reportname) && !empty($reportname)) {
                if ($reporttype == "MGHP") {
                    $this->setCellExcelMGHP( $resultsData, $reportname, TRUE);
                }
                
                if ($reporttype == "CPHT") {
                    $this->setCellExcelCPHT( $resultsData, $reportname, TRUE);
                }

                if ($reporttype == "HTAT") {
                    $this->setCellExcelHTAT( $resultsData, $reportname, TRUE);
                }

                if ($reporttype == "HTBT") {
                    $this->setCellExcelHTBT( $resultsData, $reportname, TRUE);
                }

                if ($reporttype == "HSDTTS") {
                    $this->setCellExcelHSDTTS( $resultsData, $reportname, TRUE);
                }

                if ($reporttype == "HSKT") {
                    $this->setCellExcelHSKT( $resultsData, $reportname, TRUE);
                }

                if ($reporttype == "TONGHOP") {
                    $this->setCellExcelTongHop( $resultsData, $reportname, TRUE);
                }

                if ($reporttype == "NGNA") {
                    $this->setCellExcelNGNA( $resultsData, $reportname, TRUE);
                }
            }

        } catch (Exception $e) {
            return $e;
        }
    }

//Create file Export----------------------------------------------------------------------------------------------------------------------------------------

    private function setCellExcelMGHP($resultsData, $filename, $type = true){
        // $listData = [];
        // foreach ($resultsData['unit'] as $unit) {
        //     $listData = $this->countUnitMGHP($unit->unit_id, $resultsData['hosobaocao']);
        // }
        // return $listData;
        $excel =    Excel::load(storage_path().'/exceltemplate/laphosoMGHP.xlsx', function($reader) use($resultsData){
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
            $row = 4;

            $col = 0;
            $colA = 0;

            $class_lv1 = 0;
            $class_lv2 = 0;
            $class_lv3 = 0;

            //-----------------------------------------Title------------------------------------------------------------------------------------------------
            $reader->getActiveSheet()->setCellValueByColumnAndRow(2, 2, 'Thông tin cơ bản (tính tại thời điểm tháng 5/'.$resultsData['reportyear'].')')->getStyle('C2')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

            $reader->getActiveSheet()->setCellValueByColumnAndRow(2, 4, 'Học kì II năm học '.($resultsData['reportyear'] - 1).'-'.$resultsData['reportyear'])->getStyle('C4')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(3, 4, 'Năm học '.$resultsData['reportyear'].'-'.($resultsData['reportyear'] + 1))->getStyle('D4')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(4, 4, 'Năm học '.($resultsData['reportyear'] + 1).'-'.($resultsData['reportyear'] + 2))->getStyle('E4')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

            $reader->getActiveSheet()->setCellValueByColumnAndRow(22, 4, 'Học kỳ II năm học '.($resultsData['reportyear'] - 1).'-'.$resultsData['reportyear'])->getStyle('W4')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(23, 4, 'Học kỳ I năm học '.$resultsData['reportyear'].'-'.($resultsData['reportyear'] + 1))->getStyle('X4')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(24, 4, 'Học kỳ II năm học '.$resultsData['reportyear'].'-'.($resultsData['reportyear'] + 1))->getStyle('Y4')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(25, 4, 'Học kỳ I năm học '.($resultsData['reportyear'] + 1).'-'.($resultsData['reportyear'] + 2))->getStyle('Z4')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

            $reader->getActiveSheet()->setCellValueByColumnAndRow(26, 3, 'Năm học '.($resultsData['reportyear'] - 1).'-'.$resultsData['reportyear'])->getStyle('AA3')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(27, 3, 'Năm học '.$resultsData['reportyear'].'-'.($resultsData['reportyear'] + 1).', Năm học '.($resultsData['reportyear'] + 1).'-'.($resultsData['reportyear'] + 2))->getStyle('AB3')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

            $reader->getActiveSheet()->setCellValueByColumnAndRow(28, 2, 'Nhu cầu kinh phí năm '.$resultsData['reportyear'])->getStyle('AC2')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(29, 2, 'Dự toán kinh phí năm '.($resultsData['reportyear'] + 1))->getStyle('AD2')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            //-----------------------------------------End Title----------------------------------------------------------------------------------------------------


            //$resultsData['aCount'][0]
            foreach ($resultsData['unit'] as $unit) {
                $row++;
                $row++;
                $listData = [];
                $listData = $this->countUnitMGHP($unit->unit_id, $resultsData['hosobaocao']);

                $reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row, '')->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $unit->unit_name)->getStyle('B'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($FontArrayitalic);
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
                $reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row, $listData['tongmien_1'])->getStyle('O'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row, $listData['tongmien_2'])->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row, $listData['tongmien_3'])->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row, $listData['tongmien_4'])->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(18, $row, $listData['tongmien_5'])->getStyle('S'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row, $listData['tonggiam_70'])->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row, $listData['tonggiam_501'])->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row, $listData['tonggiam_502'])->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row, $listData['tonghk_old'])->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row, $listData['tonghk1_cur'])->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(24, $row, $listData['tonghk2_cur'])->getStyle('Y'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(25, $row, $listData['tonghk1_new'])->getStyle('Z'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(26, $row, '')->getStyle('AA'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(27, $row, '')->getStyle('AB'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(28, $row, $listData['tong_nhucau'])->getStyle('AC'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight)->applyFromArray($FontArrayitalic)->applyFromArray($FormatCurrency);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(29, $row, $listData['tong_dutoan'])->getStyle('AD'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight)->applyFromArray($FontArrayitalic);

                $sttSchool = 0;

                foreach ($resultsData['hosobaocao'] as $report) {
                    if ($report->schools_unit_id == $unit->unit_id) {
                        # code...
                        $row++;
                        $data_count['aT'] = 'Học sinh có mặt tại trường tháng 5/' . $report->report_year;
                        $data_count['bT'] = 'Học sinh dự kiến tuyển mới năm học ' . $report->report_year . '-' . ((int)$report->report_year + 1);
                        $data_count['cT'] = 'Học sinh dự kiến tuyển mới năm học ' . ((int)$report->report_year + 1) . '-' . ((int)$report->report_year + 2);
                
                        $data_count['aCount'] = $this->countValueMGHP('1',$report->report_type);
                        $data_count['bCount'] = $this->countValueMGHP('2',$report->report_type);
                        $data_count['cCount'] = $this->countValueMGHP('3',$report->report_type);
                        $data_count['TotalCount'] = $this->countValueMGHP(null,$report->report_type);
                        
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row, ++$sttSchool)->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $report->schools_name)->getStyle('B'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($FontArrayitalic);
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
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row,$data_count['TotalCount']->tongmien1)->getStyle('O'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row,$data_count['TotalCount']->tongmien2)->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row,$data_count['TotalCount']->tongmien3)->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row,$data_count['TotalCount']->tongmien4)->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(18, $row,$data_count['TotalCount']->tongmien5)->getStyle('S'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row,$data_count['TotalCount']->tonggiam70)->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row,$data_count['TotalCount']->tonggiam501)->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row,$data_count['TotalCount']->tonggiam502)->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row,$data_count['TotalCount']->tonghk12)->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row,$data_count['TotalCount']->tonghk21)->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(24, $row,$data_count['TotalCount']->tonghk22)->getStyle('Y'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(25, $row,$data_count['TotalCount']->tonghk31)->getStyle('Z'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(26, $row, '')->getStyle('AA'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(27, $row, '')->getStyle('AB'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(28, $row,$data_count['TotalCount']->tongnc)->getStyle('AC'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight)->applyFromArray($FontArrayitalic)->applyFromArray($FormatCurrency);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(29, $row,$data_count['TotalCount']->tongdt)->getStyle('AD'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight)->applyFromArray($FontArrayitalic);

                        $row++;
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row,'a')->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

                        $reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row,$data_count['aT'])->getStyle('B'.$row)->applyFromArray($FontArray);
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
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row,$data_count['aCount']->tongmien1)->getStyle('O'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row,$data_count['aCount']->tongmien2)->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row,$data_count['aCount']->tongmien3)->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row,$data_count['aCount']->tongmien4)->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(18, $row,$data_count['aCount']->tongmien5)->getStyle('S'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row,$data_count['aCount']->tonggiam70)->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row,$data_count['aCount']->tonggiam501)->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row,$data_count['aCount']->tonggiam502)->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row,$data_count['aCount']->tonghk12)->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row,$data_count['aCount']->tonghk21)->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(24, $row,$data_count['aCount']->tonghk22)->getStyle('Y'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(25, $row,$data_count['aCount']->tonghk31)->getStyle('Z'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(26, $row, '')->getStyle('AA'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(27, $row, '')->getStyle('AB'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(28, $row,$data_count['aCount']->tongnc)->getStyle('AC'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(29, $row,$data_count['aCount']->tongdt)->getStyle('AD'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $indexa = 0;
                        if(count($resultsData['a']) > 0){
                            foreach($resultsData['a'] as $valueA){
                                if ($report->report_type == $valueA->{'type_code'}) {
                                    
                                    $col = 0;   $row++;

                                    $decided_date = "";
                                    if (!is_null($valueA->decided_confirmdate) && !empty($valueA->decided_confirmdate)) {
                                        $decided_date = Carbon::parse($valueA->decided_confirmdate)->format('d-m-Y');
                                    }

                                    $reader->getActiveSheet()->setCellValueByColumnAndRow($col, $row,++$indexa)->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($style);

                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$valueA->profile_name)->getStyle('B'.$row)->applyFromArray($borderArray);
                                            
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$valueA->level_old)->getStyle('C'.$row)->applyFromArray($borderArray);
                                            
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$valueA->level_cur)->getStyle('D'.$row)->applyFromArray($borderArray);
                                            
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$valueA->level_new)->getStyle('E'.$row)->applyFromArray($borderArray);
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,Carbon::parse($valueA->profile_birthday)->format('d-m-Y'))->getStyle('F'.$row)->applyFromArray($borderArray); 
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$valueA->nationals_name)->getStyle('G'.$row)->applyFromArray($borderArray);  
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$valueA->profile_household)->getStyle('H'.$row)->applyFromArray($borderArray); 
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$valueA->tenxa)->getStyle('I'.$row)->applyFromArray($borderArray); 
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$valueA->tenhuyen)->getStyle('J'.$row)->applyFromArray($borderArray); 

                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$valueA->profile_parentname)->getStyle('K'.$row)->applyFromArray($borderArray);       
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$valueA->decided_confirmation)->getStyle('L'.$row)->applyFromArray($borderArray);      
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$valueA->decided_number)->getStyle('M'.$row)->applyFromArray($borderArray);      
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$decided_date)->getStyle('N'.$row)->applyFromArray($borderArray);

                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$valueA->mienphi_1)->getStyle('O'.$row)->applyFromArray($borderArray)->applyFromArray($style);
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$valueA->mienphi_2)->getStyle('P'.$row)->applyFromArray($borderArray)->applyFromArray($style);        
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$valueA->mienphi_3)->getStyle('Q'.$row)->applyFromArray($borderArray)->applyFromArray($style);        
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$valueA->mienphi_4)->getStyle('R'.$row)->applyFromArray($borderArray)->applyFromArray($style);        
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$valueA->mienphi_5)->getStyle('S'.$row)->applyFromArray($borderArray)->applyFromArray($style);        
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$valueA->giam_70)->getStyle('T'.$row)->applyFromArray($borderArray)->applyFromArray($style);      
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$valueA->giam_50_1)->getStyle('U'.$row)->applyFromArray($borderArray)->applyFromArray($style);        
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$valueA->giam_50_2)->getStyle('V'.$row)->applyFromArray($borderArray)->applyFromArray($style);        
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$valueA->hocky2_old)->getStyle('W'.$row)->applyFromArray($borderArray)->applyFromArray($style);       
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$valueA->hocky1_cur)->getStyle('X'.$row)->applyFromArray($borderArray)->applyFromArray($style);       
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$valueA->hocky2_cur)->getStyle('Y'.$row)->applyFromArray($borderArray)->applyFromArray($style);       
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$valueA->hocky1_new)->getStyle('Z'.$row)->applyFromArray($borderArray)->applyFromArray($style);       
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$valueA->hocphi_old)->getStyle('AA'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);         
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$valueA->hocphi_new)->getStyle('AB'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);         
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$valueA->nhu_cau)->getStyle('AC'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);        
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$valueA->du_toan)->getStyle('AD'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                                }
                            }
                        }
                        //b

                        $row++;
                            $reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row,'b')->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

                        $reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row,$data_count['bT'])->getStyle('B'.$row)->applyFromArray($FontArray);
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
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row,$data_count['bCount']->tongmien1)->getStyle('O'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row,$data_count['bCount']->tongmien2)->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row,$data_count['bCount']->tongmien3)->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row,$data_count['bCount']->tongmien4)->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(18, $row,$data_count['bCount']->tongmien5)->getStyle('S'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row,$data_count['bCount']->tonggiam70)->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row,$data_count['bCount']->tonggiam501)->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row,$data_count['bCount']->tonggiam502)->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row,$data_count['bCount']->tonghk12)->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row,$data_count['bCount']->tonghk21)->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(24, $row,$data_count['bCount']->tonghk22)->getStyle('Y'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(25, $row,$data_count['bCount']->tonghk31)->getStyle('Z'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(26, $row, '')->getStyle('AA'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(27, $row, '')->getStyle('AB'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(28, $row,$data_count['bCount']->tongnc)->getStyle('AC'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(29, $row,$data_count['bCount']->tongdt)->getStyle('AD'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $indexb = 0;

                        if(count($resultsData['b'])>0){
                            foreach($resultsData['b'] as $value){
                                if ($report->report_type == $value->type_code) {
                                    $col = 0;   $row++;

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
                        }

                        //c

                        $row++;
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row,'c')->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

                        $reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row,$data_count['cT'])->getStyle('B'.$row)->applyFromArray($FontArray);
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
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row,$data_count['cCount']->tongmien1)->getStyle('O'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row,$data_count['cCount']->tongmien2)->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row,$data_count['cCount']->tongmien3)->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row,$data_count['cCount']->tongmien4)->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(18, $row,$data_count['cCount']->tongmien5)->getStyle('S'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row,$data_count['cCount']->tonggiam70)->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row,$data_count['cCount']->tonggiam501)->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row,$data_count['cCount']->tonggiam502)->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row,$data_count['cCount']->tonghk12)->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row,$data_count['cCount']->tonghk21)->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(24, $row,$data_count['cCount']->tonghk22)->getStyle('Y'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(25, $row,$data_count['cCount']->tonghk31)->getStyle('Z'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(26, $row, '')->getStyle('AA'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(27, $row, '')->getStyle('AB'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(28, $row,$data_count['cCount']->tongnc)->getStyle('AC'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(29, $row,$data_count['cCount']->tongdt)->getStyle('AD'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $indexc = 0;
                        if(count($resultsData['c'])>0){
                            foreach($resultsData['c'] as $value){
                                if ($report->report_type == $value->type_code) {
                                    $col = 0;   $row++;

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
                        }
                    }
                }
            }
        });

        if($type){
            return $excel->setFilename($filename)->store('xlsx', storage_path().'/exceldownload/THAMDINH');
        }else{
            return $excel->setFilename($filename)->download('xlsx');
        }
    }

    private function setCellExcelCPHT($resultsData, $filename, $type = true){
        $excel =    Excel::load(storage_path().'/exceltemplate/laphosoCPHT.xlsx', function($reader) use($resultsData){
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
            $row = 7;

            $col = 0;
            $colA = 0;

            $class_lv1 = 0;
            $class_lv2 = 0;
            $class_lv3 = 0;

            //-----------------------------------------Title------------------------------------------------------------------------------------------------
            $reader->getActiveSheet()->setCellValueByColumnAndRow(0, 2, 'NHU CẦU KINH PHÍ NĂM '.$resultsData['reportyear'].', DỰ TOÁN NĂM '.($resultsData['reportyear'] + 1).' ĐỐI VỚI CHÍNH SÁCH HỖ TRỢ CHI PHÍ HỌC TẬP THEO NGHỊ ĐỊNH SỐ 86/2015/NĐ-CP CỦA CHÍNH PHỦ')->getStyle('A2')->applyFromArray($FontArray)->applyFromArray($style);
            // $reader->getActiveSheet()->setCellValueByColumnAndRow(8, 3, '(Kèm theo Công văn số        /STC-KHNS ngày     /8/'.$resultsData['reportyear'].' của Sở Tài chính Yên Bái)')->getStyle('I3')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(2, 5, 'Thông tin cơ bản (tính tại thời điểm tháng 5/'.$resultsData['reportyear'].')')->getStyle('C5')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

            $reader->getActiveSheet()->setCellValueByColumnAndRow(2, 7, 'Học kì II năm học '.($resultsData['reportyear'] - 1).'-'.$resultsData['reportyear'])->getStyle('C7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(3, 7, 'Năm học '.$resultsData['reportyear'].'-'.($resultsData['reportyear'] + 1))->getStyle('D7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(4, 7, 'Năm học '.($resultsData['reportyear'] + 1).'-'.($resultsData['reportyear'] + 2))->getStyle('E7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

            $reader->getActiveSheet()->setCellValueByColumnAndRow(12, 7, 'Học kỳ II năm học '.($resultsData['reportyear'] - 1).'-'.$resultsData['reportyear'])->getStyle('M7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(13, 7, 'Học kỳ I năm học '.$resultsData['reportyear'].'-'.($resultsData['reportyear'] + 1))->getStyle('N7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(14, 7, 'Học kỳ II năm học '.$resultsData['reportyear'].'-'.($resultsData['reportyear'] + 1))->getStyle('O7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(15, 7, 'Học kỳ I năm học '.($resultsData['reportyear'] + 1).'-'.($resultsData['reportyear'] + 2))->getStyle('P7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

            $reader->getActiveSheet()->setCellValueByColumnAndRow(16, 5, 'Nhu cầu kinh phí năm '.$resultsData['reportyear'])->getStyle('Q5')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(17, 5, 'Dự toán kinh phí năm '.($resultsData['reportyear'] + 1))->getStyle('R5')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            //-----------------------------------------End Title----------------------------------------------------------------------------------------------------

            //$data_results['aCount'][0]
            foreach ($resultsData['unit'] as $unit) {
                $row++;
                $row++;

                $listData = [];
                $listData = $this->countUnitCPHT($unit->unit_id, $resultsData['hosobaocao']);

                $reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row,'')->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $unit->unit_name)->getStyle('B'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($FontArrayitalic)->applyFromArray($styleLeft);
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
                $reader->getActiveSheet()->setCellValueByColumnAndRow(12, $row, $listData['tonghk2_old'])->getStyle('M'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(13, $row, $listData['tonghk1_cur'])->getStyle('N'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row, $listData['tonghk2_cur'])->getStyle('O'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row, $listData['tonghk1_new'])->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row, $listData['tong_nhucau'])->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row, $listData['tong_dutoan'])->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);

                $sttSchool = 0;

                foreach ($resultsData['hosobaocao'] as $report) {
                    if ($report->schools_unit_id == $unit->unit_id) {
                        # code...
                        $row++;
                        $data_count['aT'] = 'Học sinh có mặt tại trường tháng 5/' . $report->report_year;
                        $data_count['bT'] = 'Học sinh dự kiến tuyển mới năm học ' . $report->report_year . '-' . ((int)$report->report_year + 1);
                        $data_count['cT'] = 'Học sinh dự kiến tuyển mới năm học ' . ((int)$report->report_year + 1) . '-' . ((int)$report->report_year + 2);
                
                        $data_count['aCount'] = $this->countValueCPHT('1', $report->report_type);
                        $data_count['bCount'] = $this->countValueCPHT('2', $report->report_type);
                        $data_count['cCount'] = $this->countValueCPHT('3', $report->report_type);
                        $data_count['TotalCount'] = $this->countValueCPHT(null, $report->report_type);

                        $reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row, ++$sttSchool)->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $report->schools_name)->getStyle('B'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($FontArrayitalic)->applyFromArray($styleLeft);
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
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(12, $row, $data_count['TotalCount']->tonghocky2_old)->getStyle('M'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(13, $row, $data_count['TotalCount']->tonghocky1_cur)->getStyle('N'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row, $data_count['TotalCount']->tonghocky2_cur)->getStyle('O'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row, $data_count['TotalCount']->tonghocky1_new)->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row, $data_count['TotalCount']->tong_nhucau)->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row, $data_count['TotalCount']->tong_dutoan)->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);

                        $row++;
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row,'a')->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

                        //____________________________________________________________________________________________________________________________________________________
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data_count['aT'])->getStyle('B'.$row)->applyFromArray($FontArray)->applyFromArray($styleLeft);
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
                        
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(12, $row, $data_count['aCount']->tonghocky2_old)->getStyle('M'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(13, $row, $data_count['aCount']->tonghocky1_cur)->getStyle('N'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row, $data_count['aCount']->tonghocky2_cur)->getStyle('O'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row, $data_count['aCount']->tonghocky1_new)->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row, $data_count['aCount']->tong_nhucau)->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row, $data_count['aCount']->tong_dutoan)->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);

                        $indexa = 0;
                        if(count($resultsData['a']) > 0){
                            foreach($resultsData['a'] as $key => $value){
                                if ($report->report_type == $value->{'type_code'}) {
                                    $col = 0;   $row++;

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
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->decided_number)->getStyle('H'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->decided_confirmation)->getStyle('I'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $decided_date)->getStyle('J'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 

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
                        }
                        //b

                        $row++;
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row, 'b')->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

                        $reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data_count['bT'])->getStyle('B'.$row)->applyFromArray($FontArray);
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
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(12, $row, $data_count['bCount']->tonghocky2_old)->getStyle('M'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(13, $row, $data_count['bCount']->tonghocky1_cur)->getStyle('N'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row, $data_count['bCount']->tonghocky2_cur)->getStyle('O'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row, $data_count['bCount']->tonghocky1_new)->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row, $data_count['bCount']->tong_nhucau)->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row, $data_count['bCount']->tong_dutoan)->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);

                        $indexb = 0;

                        if(count($resultsData['b']) > 0){
                            foreach($resultsData['b'] as $key => $value){
                                if ($report->report_type == $value->{'type_code'}) {
                                    $col = 0;   $row++;

                                    $decided_date = "";
                                    if (!is_null($value->decided_confirmdate) && !empty($value->decided_confirmdate)) {                 
                                        $decided_date = Carbon::parse($value->decided_confirmdate)->format('d-m-Y');
                                    }

                                    $reader->getActiveSheet()->setCellValueByColumnAndRow($col, $row,++$indexb)->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($style);

                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_name)->getStyle('B'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
                                            
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->level_old)->getStyle('C'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
                                            
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->level_cur)->getStyle('D'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
                                            
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->level_new)->getStyle('E'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,Carbon::parse($value->profile_birthday)->format('d-m-Y'))->getStyle('F'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight); 
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_parentname)->getStyle('G'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);  
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->decided_number)->getStyle('H'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->decided_confirmation)->getStyle('I'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $decided_date)->getStyle('J'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 

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
                        }

                            //c

                        $row++;
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row, 'c')->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

                        $reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data_count['cT'])->getStyle('B'.$row)->applyFromArray($FontArray);
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
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(12, $row, $data_count['cCount']->tonghocky2_old)->getStyle('M'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(13, $row, $data_count['cCount']->tonghocky1_cur)->getStyle('N'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row, $data_count['cCount']->tonghocky2_cur)->getStyle('O'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row, $data_count['cCount']->tonghocky1_new)->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row, $data_count['cCount']->tong_nhucau)->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row, $data_count['cCount']->tong_dutoan)->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);

                        $indexc = 0;
                        if(count($resultsData['c']) > 0){
                            foreach($resultsData['c'] as $key => $value){
                                if ($report->report_type == $value->{'type_code'}) {
                                    $col = 0;   $row++;

                                    $decided_date = "";
                                    if (!is_null($value->decided_confirmdate) && !empty($value->decided_confirmdate)) {                 
                                        $decided_date = Carbon::parse($value->decided_confirmdate)->format('d-m-Y');
                                    }

                                    $reader->getActiveSheet()->setCellValueByColumnAndRow($col, $row,++$indexc)->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($style);

                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_name)->getStyle('B'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
                                            
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,'')->getStyle('C'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
                                            
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,'')->getStyle('D'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
                                            
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->level_cur)->getStyle('E'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,Carbon::parse($value->profile_birthday)->format('d-m-Y'))->getStyle('F'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight); 
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row,$value->profile_parentname)->getStyle('G'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);  
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->decided_number)->getStyle('H'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $value->decided_confirmation)->getStyle('I'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 
                                    $reader->getActiveSheet()->setCellValueByColumnAndRow(++$col, $row, $decided_date)->getStyle('J'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft); 

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
                        }
                    }
                }
            }
        });

        if($type){
            return $excel->setFilename($filename)->store('xlsx', storage_path().'/exceldownload/THAMDINH');
        }else{
            return $excel->setFilename($filename)->download('xlsx');
        }
    }

    private function setCellExcelHTAT($resultsData, $filename, $type = true){
        $excel =    Excel::load(storage_path().'/exceltemplate/laphosoHTAT.xlsx', function($reader) use($resultsData){
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
            $row = 7;

            $col = 0;
            $colA = 0;

            $class_lv1 = 0;
            $class_lv2 = 0;
            $class_lv3 = 0;

            //-----------------------------------------Title------------------------------------------------------------------------------------------------
            $reader->getActiveSheet()->setCellValueByColumnAndRow(0, 2, 'NHU CẦU KINH PHÍ NĂM '.$resultsData['reportyear'].', DỰ TOÁN NĂM '.($resultsData['reportyear'] + 1).' ĐỐI VỚI CHÍNH SÁCH HỖ TRỢ ĂN TRƯA CHO TRẺ EM MẪU GIÁO THEO QUYẾT ĐỊNH SỐ 60/QĐ-TTG CỦA THỦ TƯỚNG CHÍNH PHỦ')->getStyle('A2')->applyFromArray($FontArray)->applyFromArray($style);

            $reader->getActiveSheet()->setCellValueByColumnAndRow(2, 5, 'Thông tin cơ bản (tính tại thời điểm tháng 5/'.$resultsData['reportyear'].')')->getStyle('C5')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

            $reader->getActiveSheet()->setCellValueByColumnAndRow(2, 7, 'Học kì II năm học '.($resultsData['reportyear'] - 1).'-'.$resultsData['reportyear'])->getStyle('C7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(3, 7, 'Năm học '.$resultsData['reportyear'].'-'.($resultsData['reportyear'] + 1))->getStyle('D7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(4, 7, 'Năm học '.($resultsData['reportyear'] + 1).'-'.($resultsData['reportyear'] + 2))->getStyle('E7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

            $reader->getActiveSheet()->setCellValueByColumnAndRow(18, 7, 'Học kỳ II năm học '.($resultsData['reportyear'] - 1).'-'.$resultsData['reportyear'])->getStyle('S7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(19, 7, 'Học kỳ I năm học '.$resultsData['reportyear'].'-'.($resultsData['reportyear'] + 1))->getStyle('T7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(20, 7, 'Học kỳ II năm học '.$resultsData['reportyear'].'-'.($resultsData['reportyear'] + 1))->getStyle('U7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(21, 7, 'Học kỳ I năm học '.($resultsData['reportyear'] + 1).'-'.($resultsData['reportyear'] + 2))->getStyle('V7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

            $reader->getActiveSheet()->setCellValueByColumnAndRow(22, 5, 'Nhu cầu kinh phí năm '.$resultsData['reportyear'])->getStyle('W5')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(23, 5, 'Dự toán kinh phí năm '.($resultsData['reportyear'] + 1))->getStyle('X5')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            //-----------------------------------------End Title----------------------------------------------------------------------------------------------------


            //$data_results['aCount'][0]
            foreach ($resultsData['unit'] as $unit) {
                $row++;
                $row++;

                $listData = [];
                $listData = $this->countUnitHTAT($unit->unit_id, $resultsData['hosobaocao']);

                $reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row, '')->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $unit->unit_name)->getStyle('B'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($FontArrayitalic)->applyFromArray($styleLeft);
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
                $reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row, $listData['tong_doituong1'])->getStyle('O'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row, $listData['tong_doituong2'])->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row, $listData['tong_doituong3'])->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row, $listData['tong_doituong4'])->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(18, $row, $listData['tonghk2_old'])->getStyle('S'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row, $listData['tonghk1_cur'])->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row, $listData['tonghk2_cur'])->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row, $listData['tonghk1_new'])->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row, $listData['tong_nhucau'])->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row, $listData['tong_dutoan'])->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);

                $sttSchool = 0;
                foreach ($resultsData['hosobaocao'] as $report) {
                    if ($report->schools_unit_id == $unit->unit_id) {
                        # code...
                        $row++;
                        $data_count['aT'] = 'Học sinh có mặt tại trường tháng 5/' . $report->report_year;
                        $data_count['bT'] = 'Học sinh dự kiến tuyển mới năm học ' . $report->report_year . '-' . ((int)$report->report_year + 1);
                        $data_count['cT'] = 'Học sinh dự kiến tuyển mới năm học ' . ((int)$report->report_year + 1) . '-' . ((int)$report->report_year + 2);
                
                        $data_count['aCount'] = $this->countValueHTAT('1', $report->report_type);
                        $data_count['bCount'] = $this->countValueHTAT('2', $report->report_type);
                        $data_count['cCount'] = $this->countValueHTAT('3', $report->report_type);
                        $data_count['TotalCount'] = $this->countValueHTAT(null, $report->report_type);

                        $reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row, ++$sttSchool)->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $report->schools_name)->getStyle('B'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($FontArrayitalic)->applyFromArray($styleLeft);
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
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row,$data_count['TotalCount']->tonghtta_doituong1)->getStyle('O'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row, $data_count['TotalCount']->tonghtta_doituong2)->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row, $data_count['TotalCount']->tonghtta_doituong3)->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row, $data_count['TotalCount']->tonghtta_doituong4)->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(18, $row, $data_count['TotalCount']->tonghocky2_old)->getStyle('S'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row, $data_count['TotalCount']->tonghocky1_cur)->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row, $data_count['TotalCount']->tonghocky2_cur)->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row, $data_count['TotalCount']->tonghocky1_new)->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row, $data_count['TotalCount']->tongnhu_cau)->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row, $data_count['TotalCount']->tongdu_toan)->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);

                        $row++;
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row,'a')->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

                        //____________________________________________________________________________________________________________________________________________________
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data_count['aT'])->getStyle('B'.$row)->applyFromArray($FontArray)->applyFromArray($styleLeft);
                        
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
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row, $data_count['aCount']->tonghtta_doituong1)->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row, $data_count['aCount']->tonghtta_doituong2)->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row, $data_count['aCount']->tonghtta_doituong3)->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row, $data_count['aCount']->tonghtta_doituong4)->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(18, $row, $data_count['aCount']->tonghocky2_old)->getStyle('S'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row, $data_count['aCount']->tonghocky1_cur)->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row, $data_count['aCount']->tonghocky2_cur)->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row, $data_count['aCount']->tonghocky1_new)->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row, $data_count['aCount']->tongnhu_cau)->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row, $data_count['aCount']->tongdu_toan)->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);

                        $indexa = 0;
                        if(count($resultsData['a']) > 0){
                            foreach($resultsData['a'] as $key => $value){
                                if ($report->report_type == $value->{'type_code'}) {
                                    $col = 0;   $row++;

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
                        }

                        $row++;
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row,'b')->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

                        $reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data_count['bT'])->getStyle('B'.$row)->applyFromArray($FontArray);
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
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row, $data_count['bCount']->tonghtta_doituong1)->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row, $data_count['bCount']->tonghtta_doituong2)->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row, $data_count['bCount']->tonghtta_doituong3)->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row, $data_count['bCount']->tonghtta_doituong4)->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(18, $row, $data_count['bCount']->tonghocky2_old)->getStyle('S'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row, $data_count['bCount']->tonghocky1_cur)->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row, $data_count['bCount']->tonghocky2_cur)->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row, $data_count['bCount']->tonghocky1_new)->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row, $data_count['bCount']->tongnhu_cau)->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row, $data_count['bCount']->tongdu_toan)->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);

                        $indexb = 0;

                        if(count($resultsData['b']) > 0){
                            foreach($resultsData['b'] as $key => $value){
                                if ($report->report_type == $value->{'type_code'}) {
                                    $col = 0;   $row++;

                                    $decided_date = "";
                                    if (!is_null($value->decided_confirmdate) && !empty($value->decided_confirmdate)) {                 
                                        $decided_date = Carbon::parse($value->decided_confirmdate)->format('d-m-Y');
                                    }

                                    $reader->getActiveSheet()->setCellValueByColumnAndRow($col, $row,++$indexb)->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($style);

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
                        }
                            //c

                        $row++;
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row,'c')->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

                        $reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data_count['cT'])->getStyle('B'.$row)->applyFromArray($FontArray);
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
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row, $data_count['cCount']->tonghtta_doituong1)->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row, $data_count['cCount']->tonghtta_doituong2)->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row, $data_count['cCount']->tonghtta_doituong3)->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row, $data_count['cCount']->tonghtta_doituong4)->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(18, $row, $data_count['cCount']->tonghocky2_old)->getStyle('S'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row, $data_count['cCount']->tonghocky1_cur)->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row, $data_count['cCount']->tonghocky2_cur)->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row, $data_count['cCount']->tonghocky1_new)->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row, $data_count['cCount']->tongnhu_cau)->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row, $data_count['cCount']->tongdu_toan)->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);

                        $indexc = 0;
                        if(count($resultsData['c']) > 0){
                            foreach($resultsData['c'] as $key => $value){
                                if ($report->report_type == $value->{'type_code'}) {
                                    $col = 0;   $row++;

                                    $decided_date = "";
                                    if (!is_null($value->decided_confirmdate) && !empty($value->decided_confirmdate)) {                 
                                        $decided_date = Carbon::parse($value->decided_confirmdate)->format('d-m-Y');
                                    }

                                    $reader->getActiveSheet()->setCellValueByColumnAndRow($col, $row,++$indexc)->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($style);

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
                        }
                    }
                }
            }
        });

        if($type){
            return $excel->setFilename($filename)->store('xlsx', storage_path().'/exceldownload/THAMDINH');
        }else{
            return $excel->setFilename($filename)->download('xlsx');
        }
    }

    private function setCellExcelHTBT($resultsData, $filename, $type = true){
        $excel = Excel::load(storage_path().'/exceltemplate/laphosoHTBT.xlsx', function($reader) use($resultsData){
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
            $row = 7;

            $col = 0;
            $colA = 0;

            $class_lv1 = 0;
            $class_lv2 = 0;
            $class_lv3 = 0;

            //-----------------------------------------Title------------------------------------------------------------------------------------------------
            $reader->getActiveSheet()->setCellValueByColumnAndRow(0, 2, 'NHU CẦU KINH PHÍ NĂM '.$resultsData['reportyear'].', DỰ TOÁN NĂM '.($resultsData['reportyear'] + 1).' ĐỐI VỚI CHÍNH SÁCH HỖ TRỢ ĂN TRƯA CHO TRẺ EM MẪU GIÁO THEO QUYẾT ĐỊNH SỐ 60/QĐ-TTG CỦA THỦ TƯỚNG CHÍNH PHỦ')->getStyle('A2')->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(2, 4, 'Thông tin cơ bản (tính tại thời điểm tháng 5/'.$resultsData['reportyear'].')')->getStyle('C4')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(23, 4, 'Nhu cầu kinh phí năm '.$resultsData['reportyear'])->getStyle('X4')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(27, 4, 'Dự toán kinh phí năm '.($resultsData['reportyear'] + 1))->getStyle('AB4')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

            $reader->getActiveSheet()->setCellValueByColumnAndRow(2, 7, 'Học kì II năm học '.($resultsData['reportyear'] - 1).'-'.$resultsData['reportyear'])->getStyle('C7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(3, 7, 'Năm học '.$resultsData['reportyear'].'-'.($resultsData['reportyear'] + 1))->getStyle('D7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(4, 7, 'Năm học '.($resultsData['reportyear'] + 1).'-'.($resultsData['reportyear'] + 2))->getStyle('E7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

            $reader->getActiveSheet()->setCellValueByColumnAndRow(15, 6, 'Học kỳ II năm học '.($resultsData['reportyear'] - 1).'-'.$resultsData['reportyear'])->getStyle('P6')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(17, 6, 'Học kỳ I năm học '.$resultsData['reportyear'].'-'.($resultsData['reportyear'] + 1))->getStyle('R6')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(19, 6, 'Học kỳ II năm học '.$resultsData['reportyear'].'-'.($resultsData['reportyear'] + 1))->getStyle('T6')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(21, 6, 'Học kỳ I năm học '.($resultsData['reportyear'] + 1).'-'.($resultsData['reportyear'] + 2))->getStyle('V6')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            //-----------------------------------------End Title----------------------------------------------------------------------------------------------------


            //$data_results['aCount'][0]
            foreach ($resultsData['unit'] as $unit) {
                $row++;
                $row++;

                $listData = [];
                $listData = $this->countUnitHTBT($unit->unit_id, $resultsData['hosobaocao']);

                $reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row, '')->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $unit->unit_name)->getStyle('B'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($FontArrayitalic)->applyFromArray($styleLeft);
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
                $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row, $listData['tong_hotrotienan_hk2_old'])->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row, $listData['tong_hotrotieno_hk2_old'])->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row, $listData['tong_hotrotienan_hk1_cur'])->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(18, $row, $listData['tong_hotrotieno_hk1_cur'])->getStyle('S'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row, $listData['tong_hotrotienan_hk2_cur'])->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row, $listData['tong_hotrotieno_hk2_cur'])->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row, $listData['tong_hotrotienan_hk1_new'])->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row, $listData['tong_hotrotieno_hk1_new'])->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row, $listData['tong_nhucau'])->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(24, $row, $listData['tong_nhucau_hotrotienan'])->getStyle('Y'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(25, $row, $listData['tong_nhucau_hotrotieno'])->getStyle('Z'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(26, $row, $listData['tong_nhucau_hotroVHTT'])->getStyle('AA'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(27, $row, $listData['tong_dutoan'])->getStyle('AB'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(28, $row, $listData['tong_dutoan_hotrotienan'])->getStyle('AC'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(29, $row, $listData['tong_dutoan_hotrotieno'])->getStyle('AD'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(30, $row, $listData['tong_dutoan_hotroVHTT'])->getStyle('AE'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);

                $sttSchool = 0;
                foreach ($resultsData['hosobaocao'] as $report) {
                    if ($report->schools_unit_id == $unit->unit_id) {
                        # code...
                        $row++;
                        $data_count['aT'] = 'Học sinh có mặt tại trường tháng 5/' . $report->report_year;
                        $data_count['bT'] = 'Học sinh dự kiến tuyển mới năm học ' . $report->report_year . '-' . ((int)$report->report_year + 1);
                        $data_count['cT'] = 'Học sinh dự kiến tuyển mới năm học ' . ((int)$report->report_year + 1) . '-' . ((int)$report->report_year + 2);
                
                        $data_count['aCount'] = $this->countValueHTBT('1', $report->report_type);
                        $data_count['bCount'] = $this->countValueHTBT('2', $report->report_type);
                        $data_count['cCount'] = $this->countValueHTBT('3', $report->report_type);
                        $data_count['TotalCount'] = $this->countValueHTBT(null, $report->report_type);

                        $reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row, ++$sttSchool)->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $report->schools_name)->getStyle('B'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($FontArrayitalic)->applyFromArray($styleLeft);
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
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row, $data_count['TotalCount']->tongtienan_hocky2_old)->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row, $data_count['TotalCount']->tongtieno_hocky2_old)->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row, $data_count['TotalCount']->tongtienan_hocky1_cur)->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(18, $row, $data_count['TotalCount']->tongtieno_hocky1_cur)->getStyle('S'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row, $data_count['TotalCount']->tongtienan_hocky2_cur)->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row, $data_count['TotalCount']->tongtieno_hocky2_cur)->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row, $data_count['TotalCount']->tongtienan_hocky1_new)->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row, $data_count['TotalCount']->tongtieno_hocky1_new)->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row, $data_count['TotalCount']->tong_tongnhucau)->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(24, $row, $data_count['TotalCount']->tongnhucau_hotrotienan)->getStyle('Y'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(25, $row, $data_count['TotalCount']->tongnhucau_hotrotieno)->getStyle('Z'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(26, $row, $data_count['TotalCount']->tongnhucau_hotroVHTT)->getStyle('AA'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(27, $row, $data_count['TotalCount']->tong_tongdutoan)->getStyle('AB'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(28, $row, $data_count['TotalCount']->tongdutoan_hotrotienan)->getStyle('AC'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(29, $row, $data_count['TotalCount']->tongdutoan_hotrotieno)->getStyle('AD'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(30, $row, $data_count['TotalCount']->tongdutoan_hotroVHTT)->getStyle('AE'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);

                        $row++;
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row,'a')->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

                        //____________________________________________________________________________________________________________________________________________________
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data_count['aT'])->getStyle('B'.$row)->applyFromArray($FontArray)->applyFromArray($styleLeft);
                        
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
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row, $data_count['aCount']->tongtienan_hocky2_old)->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row, $data_count['aCount']->tongtieno_hocky2_old)->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row, $data_count['aCount']->tongtienan_hocky1_cur)->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(18, $row, $data_count['aCount']->tongtieno_hocky1_cur)->getStyle('S'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row, $data_count['aCount']->tongtienan_hocky2_cur)->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row, $data_count['aCount']->tongtieno_hocky2_cur)->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row, $data_count['aCount']->tongtienan_hocky1_new)->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row, $data_count['aCount']->tongtieno_hocky1_new)->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row, $data_count['aCount']->tong_tongnhucau)->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(24, $row, $data_count['aCount']->tongnhucau_hotrotienan)->getStyle('Y'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(25, $row, $data_count['aCount']->tongnhucau_hotrotieno)->getStyle('Z'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(26, $row, $data_count['aCount']->tongnhucau_hotroVHTT)->getStyle('AA'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(27, $row, $data_count['aCount']->tong_tongdutoan)->getStyle('AB'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(28, $row, $data_count['aCount']->tongdutoan_hotrotienan)->getStyle('AC'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(29, $row, $data_count['aCount']->tongdutoan_hotrotieno)->getStyle('AD'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(30, $row, $data_count['aCount']->tongdutoan_hotroVHTT)->getStyle('AE'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);

                        $indexa = 0;
                        if(count($resultsData['a'])>0){
                            foreach($resultsData['a'] as $key => $value){
                                if ($report->report_type == $value->{'type_code'}) {
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
                        }

                        $row++;
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row, 'b')->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

                        $reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data_count['bT'])->getStyle('B'.$row)->applyFromArray($FontArray);
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
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row, $data_count['bCount']->tongtienan_hocky2_old)->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row, $data_count['bCount']->tongtieno_hocky2_old)->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row, $data_count['bCount']->tongtienan_hocky1_cur)->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(18, $row, $data_count['bCount']->tongtieno_hocky1_cur)->getStyle('S'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row, $data_count['bCount']->tongtienan_hocky2_cur)->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row, $data_count['bCount']->tongtieno_hocky2_cur)->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row, $data_count['bCount']->tongtienan_hocky1_new)->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row, $data_count['bCount']->tongtieno_hocky1_new)->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row, $data_count['bCount']->tong_tongnhucau)->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(24, $row, $data_count['bCount']->tongnhucau_hotrotienan)->getStyle('Y'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(25, $row, $data_count['bCount']->tongnhucau_hotrotieno)->getStyle('Z'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(26, $row, $data_count['bCount']->tongnhucau_hotroVHTT)->getStyle('AA'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(27, $row, $data_count['bCount']->tong_tongdutoan)->getStyle('AB'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(28, $row, $data_count['bCount']->tongdutoan_hotrotienan)->getStyle('AC'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(29, $row, $data_count['bCount']->tongdutoan_hotrotieno)->getStyle('AD'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(30, $row, $data_count['bCount']->tongdutoan_hotroVHTT)->getStyle('AE'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $indexb = 0;

                        if(count($resultsData['b']) > 0){
                            foreach($resultsData['b'] as $key => $value){
                                if ($report->report_type == $value->{'type_code'}) {
                                    $col = 0;   $row++;

                                    $reader->getActiveSheet()->setCellValueByColumnAndRow($col, $row,++$indexb)->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($style);

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
                        }

                        $row++;
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row, 'c')->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

                        $reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data_count['cT'])->getStyle('B'.$row)->applyFromArray($FontArray);
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
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row, $data_count['cCount']->tongtienan_hocky2_old)->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row, $data_count['cCount']->tongtieno_hocky2_old)->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row, $data_count['cCount']->tongtienan_hocky1_cur)->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(18, $row, $data_count['cCount']->tongtieno_hocky1_cur)->getStyle('S'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row, $data_count['cCount']->tongtienan_hocky2_cur)->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row, $data_count['cCount']->tongtieno_hocky2_cur)->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row, $data_count['cCount']->tongtienan_hocky1_new)->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row, $data_count['cCount']->tongtieno_hocky1_new)->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row, $data_count['cCount']->tong_tongnhucau)->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(24, $row, $data_count['cCount']->tongnhucau_hotrotienan)->getStyle('Y'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(25, $row, $data_count['cCount']->tongnhucau_hotrotieno)->getStyle('Z'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(26, $row, $data_count['cCount']->tongnhucau_hotroVHTT)->getStyle('AA'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(27, $row, $data_count['cCount']->tong_tongdutoan)->getStyle('AB'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(28, $row, $data_count['cCount']->tongdutoan_hotrotienan)->getStyle('AC'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(29, $row, $data_count['cCount']->tongdutoan_hotrotieno)->getStyle('AD'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(30, $row, $data_count['cCount']->tongdutoan_hotroVHTT)->getStyle('AE'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $indexc = 0;
                        if(count($resultsData['c']) > 0){
                            foreach($resultsData['c'] as $key => $value){
                                if ($report->report_type == $value->{'type_code'}) {
                                    $col = 0;   $row++;

                                    $reader->getActiveSheet()->setCellValueByColumnAndRow($col, $row,++$indexc)->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($style);

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
                        }
                    }
                }
            }
        });
        if($type){
            return $excel->setFilename($filename)->store('xlsx', storage_path().'/exceldownload/THAMDINH');
        }else{
            return $excel->setFilename($filename)->download('xlsx');
        }
    }

    private function setCellExcelHSDTTS($resultsData, $filename, $type = true){

        $excel = Excel::load(storage_path().'/exceltemplate/laphosoHSDTTS.xlsx', function($reader) use($resultsData){
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
            $row = 7;

            $col = 0;
            $colA = 0;

            $class_lv1 = 0;
            $class_lv2 = 0;
            $class_lv3 = 0;

            //-----------------------------------------Title------------------------------------------------------------------------------------------------
            $reader->getActiveSheet()->setCellValueByColumnAndRow(0, 2, 'NHU CẦU KINH PHÍ NĂM '.$resultsData['reportyear'].', DỰ TOÁN NĂM '.($resultsData['reportyear'] + 1).' ĐỐI VỚI CHÍNH SÁCH HỖ TRỢ HỌC SINH DÂN TỘC THIỂU SỐ TẠI HUYỆN MÙ CANG CHẢI VÀ HUYỆN TRẠM TẤU THEO QUYẾT ĐỊNH 22/2016/QĐ-UBND CỦA UBND TỈNH')->getStyle('A2')->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(2, 5, 'Thông tin cơ bản (tính tại thời điểm tháng 5/'.$resultsData['reportyear'].')')->getStyle('C5')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

            $reader->getActiveSheet()->setCellValueByColumnAndRow(2, 7, 'Học kì II năm học '.($resultsData['reportyear'] - 1).'-'.$resultsData['reportyear'])->getStyle('C7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(3, 7, 'Năm học '.$resultsData['reportyear'].'-'.($resultsData['reportyear'] + 1))->getStyle('D7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(4, 7, 'Năm học '.($resultsData['reportyear'] + 1).'-'.($resultsData['reportyear'] + 2))->getStyle('E7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

            $reader->getActiveSheet()->setCellValueByColumnAndRow(12, 7, 'Học kỳ II năm học '.($resultsData['reportyear'] - 1).'-'.$resultsData['reportyear'])->getStyle('M7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(13, 7, 'Học kỳ I năm học '.$resultsData['reportyear'].'-'.($resultsData['reportyear'] + 1))->getStyle('N7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(14, 7, 'Học kỳ II năm học '.$resultsData['reportyear'].'-'.($resultsData['reportyear'] + 1))->getStyle('O7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(15, 7, 'Học kỳ I năm học '.($resultsData['reportyear'] + 1).'-'.($resultsData['reportyear'] + 2))->getStyle('P7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

            $reader->getActiveSheet()->setCellValueByColumnAndRow(16, 5, 'Nhu cầu kinh phí năm '.$resultsData['reportyear'])->getStyle('Q5')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(17, 5, 'Dự toán kinh phí năm '.($resultsData['reportyear'] + 1))->getStyle('R5')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            //-----------------------------------------End Title----------------------------------------------------------------------------------------------------


            //$data_results['aCount'][0]
            foreach ($resultsData['unit'] as $unit) {
                $row++;
                $row++;

                $listData = [];
                $listData = $this->countUnitHSDTTS($unit->unit_id, $resultsData['hosobaocao']);

                $reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row, '')->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $unit->unit_name)->getStyle('B'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($FontArrayitalic)->applyFromArray($styleLeft);
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
                $reader->getActiveSheet()->setCellValueByColumnAndRow(12, $row, $listData['tonghk2_old'])->getStyle('M'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(13, $row, $listData['tonghk1_cur'])->getStyle('N'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row, $listData['tonghk2_cur'])->getStyle('O'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row, $listData['tonghk1_new'])->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row, $listData['tong_nhucau'])->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row, $listData['tong_dutoan'])->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);

                $sttSchool = 0;
                foreach ($resultsData['hosobaocao'] as $report) {
                    if ($report->schools_unit_id == $unit->unit_id) {
                        # code...
                        $row++;
                        $data_count['aT'] = 'Học sinh có mặt tại trường tháng 5/' . $report->report_year;
                        $data_count['bT'] = 'Học sinh dự kiến tuyển mới năm học ' . $report->report_year . '-' . ((int)$report->report_year + 1);
                        $data_count['cT'] = 'Học sinh dự kiến tuyển mới năm học ' . ((int)$report->report_year + 1) . '-' . ((int)$report->report_year + 2);
                
                        $data_count['aCount'] = $this->countValueHSDTTS('1', $report->report_type);
                        $data_count['bCount'] = $this->countValueHSDTTS('2', $report->report_type);
                        $data_count['cCount'] = $this->countValueHSDTTS('3', $report->report_type);
                        $data_count['TotalCount'] = $this->countValueHSDTTS(null, $report->report_type);

                        $reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row, ++$sttSchool)->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $report->schools_name)->getStyle('B'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($FontArrayitalic)->applyFromArray($styleLeft);
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
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(12, $row, $data_count['TotalCount']->tonghocky2_old)->getStyle('M'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(13, $row, $data_count['TotalCount']->tonghocky1_cur)->getStyle('N'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row, $data_count['TotalCount']->tonghocky2_cur)->getStyle('O'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row, $data_count['TotalCount']->tonghocky1_new)->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row, $data_count['TotalCount']->tong_nhucau)->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row, $data_count['TotalCount']->tong_dutoan)->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($style)->applyFromArray($FontArrayitalic);

                        $row++;
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row, 'a')->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

                        //____________________________________________________________________________________________________________________________________________________
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data_count['aT'])->getStyle('B'.$row)->applyFromArray($FontArray)->applyFromArray($styleLeft);
                        
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
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(12, $row, $data_count['aCount']->tonghocky2_old)->getStyle('M'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(13, $row, $data_count['aCount']->tonghocky1_cur)->getStyle('N'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row, $data_count['aCount']->tonghocky2_cur)->getStyle('O'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row, $data_count['aCount']->tonghocky1_new)->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row, $data_count['aCount']->tong_nhucau)->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row, $data_count['aCount']->tong_dutoan)->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);

                        $indexa = 0;
                        if(count($resultsData['a']) > 0){
                            foreach($resultsData['a'] as $key => $value){
                                if ($report->report_type == $value->{'type_code'}) {
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
                        }

                        $row++;
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row, 'b')->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

                        $reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data_count['bT'])->getStyle('B'.$row)->applyFromArray($FontArray);
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
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(12, $row, $data_count['bCount']->tonghocky2_old)->getStyle('M'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(13, $row, $data_count['bCount']->tonghocky1_cur)->getStyle('N'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row, $data_count['bCount']->tonghocky2_cur)->getStyle('O'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row, $data_count['bCount']->tonghocky1_new)->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row, $data_count['bCount']->tong_nhucau)->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row, $data_count['bCount']->tong_dutoan)->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);

                        $indexb = 0;

                        if(count($resultsData['b']) > 0){
                            foreach($resultsData['b'] as $key => $value){
                                if ($report->report_type == $value->{'type_code'}) {
                                    $col = 0;   $row++;

                                    $reader->getActiveSheet()->setCellValueByColumnAndRow($col, $row,++$indexb)->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($style);

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
                        }

                        $row++;
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row, 'c')->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

                        $reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data_count['cT'])->getStyle('B'.$row)->applyFromArray($FontArray);
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
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(12, $row, $data_count['cCount']->tonghocky2_old)->getStyle('M'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(13, $row, $data_count['cCount']->tonghocky1_cur)->getStyle('N'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row, $data_count['cCount']->tonghocky2_cur)->getStyle('O'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row, $data_count['cCount']->tonghocky1_new)->getStyle('P'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row, $data_count['cCount']->tong_nhucau)->getStyle('Q'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(17, $row, $data_count['cCount']->tong_dutoan)->getStyle('R'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);

                        $indexc = 0;
                        if(count($resultsData['c']) > 0){
                            foreach($resultsData['c'] as $key => $value){
                                if ($report->report_type == $value->{'type_code'}) {
                                    $col = 0;   $row++;

                                    $reader->getActiveSheet()->setCellValueByColumnAndRow($col, $row,++$indexc)->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($style);

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
                        }
                    }
                }
            }
        });
        if($type){
            return $excel->setFilename($filename)->store('xlsx', storage_path().'/exceldownload/THAMDINH');
        }else{
            return $excel->setFilename($filename)->download('xlsx');
        }
    }

    private function setCellExcelHSKT($resultsData, $filename, $type = true){
        $excel =    Excel::load(storage_path().'/exceltemplate/laphosoHSKT.xlsx', function($reader) use($resultsData){
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
            $row = 7;

            $col = 0;
            $colA = 0;

            $class_lv1 = 0;
            $class_lv2 = 0;
            $class_lv3 = 0;

            //-----------------------------------------Title------------------------------------------------------------------------------------------------
            $reader->getActiveSheet()->setCellValueByColumnAndRow(0, 2, 'NHU CẦU KINH PHÍ NĂM '.$resultsData['reportyear'].', DỰ TOÁN NĂM '.($resultsData['reportyear'] + 1).' ĐỐI VỚI CHÍNH SÁCH HỖ TRỢ HỌC SINH KHUYẾT TẬT THEO THÔNG TƯ LIÊN TỊCH SỐ 42/2013/TTLT-BGDĐT-BLĐTBXH-BTC (KHỐI MẦM NON VÀ TRUNG HỌC PHỔ THÔNG)')->getStyle('A2')->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(2, 5, 'Thông tin cơ bản (tính tại thời điểm tháng 5/'.$resultsData['reportyear'].')')->getStyle('C5')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(22, 5, 'Nhu cầu kinh phí năm '.$resultsData['reportyear'])->getStyle('W5')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(25, 5, 'Dự toán kinh phí năm '.($resultsData['reportyear'] + 1))->getStyle('Z5')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

            $reader->getActiveSheet()->setCellValueByColumnAndRow(2, 7, 'Học kì II năm học '.($resultsData['reportyear'] - 1).'-'.$resultsData['reportyear'])->getStyle('C7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(3, 7, 'Năm học '.$resultsData['reportyear'].'-'.($resultsData['reportyear'] + 1))->getStyle('D7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(4, 7, 'Năm học '.($resultsData['reportyear'] + 1).'-'.($resultsData['reportyear'] + 2))->getStyle('E7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

            $reader->getActiveSheet()->setCellValueByColumnAndRow(18, 7, 'Học kỳ II năm học '.($resultsData['reportyear'] - 1).'-'.$resultsData['reportyear'])->getStyle('S7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(19, 7, 'Học kỳ I năm học '.$resultsData['reportyear'].'-'.($resultsData['reportyear'] + 1))->getStyle('T7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(20, 7, 'Học kỳ II năm học '.$resultsData['reportyear'].'-'.($resultsData['reportyear'] + 1))->getStyle('U7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(21, 7, 'Học kỳ I năm học '.($resultsData['reportyear'] + 1).'-'.($resultsData['reportyear'] + 2))->getStyle('V7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            //-----------------------------------------End Title----------------------------------------------------------------------------------------------------


            //$data_results['aCount'][0]
            foreach ($resultsData['unit'] as $unit) {
                $row++;
                $row++;

                $listData = [];
                $listData = $this->countUnitHSKT($unit->unit_id, $resultsData['hosobaocao']);

                $reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row, '')->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $unit->unit_name)->getStyle('B'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($FontArrayitalic)->applyFromArray($styleLeft);
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
                $reader->getActiveSheet()->setCellValueByColumnAndRow(18, $row, $listData['tonghk2_old'])->getStyle('S'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row, $listData['tonghk1_cur'])->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row, $listData['tonghk2_cur'])->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row, $listData['tonghk1_new'])->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row, $listData['tong_nhucau'])->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row, $listData['tong_nhucauhocbong'])->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(24, $row, $listData['tong_nhucaumuadodung'])->getStyle('Y'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(25, $row, $listData['tong_dutoan'])->getStyle('Z'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(26, $row, $listData['tong_dutoanhocbong'])->getStyle('AA'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight)->applyFromArray($FontArrayitalic);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(27, $row, $listData['tong_dutoanmuadodung'])->getStyle('AB'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight)->applyFromArray($FontArrayitalic);

                $sttSchool = 0;
                foreach ($resultsData['hosobaocao'] as $report) {
                    if ($report->schools_unit_id == $unit->unit_name) {
                        # code...
                        $row++;
                        $data_count['aT'] = 'Học sinh có mặt tại trường tháng 5/' . $report->report_year;
                        $data_count['bT'] = 'Học sinh dự kiến tuyển mới năm học ' . $report->report_year . '-' . ((int)$report->report_year + 1);
                        $data_count['cT'] = 'Học sinh dự kiến tuyển mới năm học ' . ((int)$report->report_year + 1) . '-' . ((int)$report->report_year + 2);
                
                        $data_count['aCount'] = $this->countValueHSKT('1', $report->report_type);
                        $data_count['bCount'] = $this->countValueHSKT('2', $report->report_type);
                        $data_count['cCount'] = $this->countValueHSKT('3', $report->report_type);
                        $data_count['TotalCount'] = $this->countValueHSKT(null, $report->report_type);

                        $reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row, ++$sttSchool)->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row,$report->schools_name)->getStyle('B'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($FontArrayitalic)->applyFromArray($styleLeft);
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
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(18, $row, $data_count['TotalCount']->tonghocky2_old)->getStyle('S'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row, $data_count['TotalCount']->tonghocky1_cur)->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row, $data_count['TotalCount']->tonghocky2_cur)->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row, $data_count['TotalCount']->tonghocky1_new)->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row, $data_count['TotalCount']->tong_tongnhucau)->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row, $data_count['TotalCount']->tongnhucau_hocbong)->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(24, $row, $data_count['TotalCount']->tongnhucau_muadodung)->getStyle('Y'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(25, $row, $data_count['TotalCount']->tong_tongdutoan)->getStyle('Z'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(26, $row, $data_count['TotalCount']->tongdutoan_hocbong)->getStyle('AA'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight)->applyFromArray($FontArrayitalic);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(27, $row, $data_count['TotalCount']->tongdutoan_muadodung)->getStyle('AB'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight)->applyFromArray($FontArrayitalic);


                        $row++;
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row, 'a')->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

                        //____________________________________________________________________________________________________________________________________________________
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data_count['aT'])->getStyle('B'.$row)->applyFromArray($FontArray)->applyFromArray($styleLeft);
                        
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
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(18, $row, $data_count['aCount']->tonghocky2_old)->getStyle('S'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row, $data_count['aCount']->tonghocky1_cur)->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row, $data_count['aCount']->tonghocky2_cur)->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row, $data_count['aCount']->tonghocky1_new)->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row, $data_count['aCount']->tong_tongnhucau)->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row, $data_count['aCount']->tongnhucau_hocbong)->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(24, $row, $data_count['aCount']->tongnhucau_muadodung)->getStyle('Y'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(25, $row, $data_count['aCount']->tong_tongdutoan)->getStyle('Z'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(26, $row, $data_count['aCount']->tongdutoan_hocbong)->getStyle('AA'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(27, $row, $data_count['aCount']->tongdutoan_muadodung)->getStyle('AB'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);

                        $indexa = 0;
                        if(count($resultsData['a']) > 0){
                            foreach($resultsData['a'] as $key => $value){
                                if ($report->report_type == $value->{'type_code'}) {
                                    $col = 0;   $row++;

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
                        }

                        $row++;
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row, 'b')->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

                        $reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data_count['bT'])->getStyle('B'.$row)->applyFromArray($FontArray)->applyFromArray($styleLeft);
                        
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
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(18, $row, $data_count['bCount']->tonghocky2_old)->getStyle('S'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row, $data_count['bCount']->tonghocky1_cur)->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row, $data_count['bCount']->tonghocky2_cur)->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row, $data_count['bCount']->tonghocky1_new)->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row, $data_count['bCount']->tong_tongnhucau)->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row, $data_count['bCount']->tongnhucau_hocbong)->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(24, $row, $data_count['bCount']->tongnhucau_muadodung)->getStyle('Y'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(25, $row, $data_count['bCount']->tong_tongdutoan)->getStyle('Z'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(26, $row, $data_count['bCount']->tongdutoan_hocbong)->getStyle('AA'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(27, $row, $data_count['bCount']->tongdutoan_muadodung)->getStyle('AB'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);

                        $indexb = 0;

                        if(count($resultsData['b']) > 0){
                            foreach($resultsData['b'] as $key => $value){
                                if ($report->report_type == $value->{'type_code'}) {
                                    $col = 0;   $row++;

                                    $decided_date = "";
                                    if (!is_null($value->decided_confirmdate) && !empty($value->decided_confirmdate)) {                 
                                        $decided_date = Carbon::parse($value->decided_confirmdate)->format('d-m-Y');
                                    }

                                    $reader->getActiveSheet()->setCellValueByColumnAndRow($col, $row,++$indexb)->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($style);

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
                        }

                        $row++;
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row, 'c')->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

                        $reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $data_count['cT'])->getStyle('B'.$row)->applyFromArray($FontArray)->applyFromArray($styleLeft);
                        
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
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(18, $row, $data_count['cCount']->tonghocky2_old)->getStyle('S'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(19, $row, $data_count['cCount']->tonghocky1_cur)->getStyle('T'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(20, $row, $data_count['cCount']->tonghocky2_cur)->getStyle('U'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(21, $row, $data_count['cCount']->tonghocky1_new)->getStyle('V'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(22, $row, $data_count['cCount']->tong_tongnhucau)->getStyle('W'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(23, $row, $data_count['cCount']->tongnhucau_hocbong)->getStyle('X'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(24, $row, $data_count['cCount']->tongnhucau_muadodung)->getStyle('Y'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(25, $row, $data_count['cCount']->tong_tongdutoan)->getStyle('Z'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(26, $row, $data_count['cCount']->tongdutoan_hocbong)->getStyle('AA'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(27, $row, $data_count['cCount']->tongdutoan_muadodung)->getStyle('AB'.$row)->applyFromArray($FontArray)->applyFromArray($borderArray)->applyFromArray($styleRight);

                        $indexc = 0;
                        if(count($resultsData['c']) > 0){
                            foreach($resultsData['c'] as $key => $value){
                                if ($report->report_type == $value->{'type_code'}) {
                                    $col = 0;   $row++;

                                    $decided_date = "";
                                    if (!is_null($value->decided_confirmdate) && !empty($value->decided_confirmdate)) {                 
                                        $decided_date = Carbon::parse($value->decided_confirmdate)->format('d-m-Y');
                                    }

                                    $reader->getActiveSheet()->setCellValueByColumnAndRow($col, $row,++$indexc)->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($style);

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
                        }
                    }
                }
            }
        });

        if($type){
            return $excel->setFilename($filename)->store('xlsx', storage_path().'/exceldownload/THAMDINH');
        }else{
            return $excel->setFilename($filename)->download('xlsx');
        }
    }
    
    private function setCellExcelTongHop($resultsData, $filename, $type = true){
        $excel =    Excel::load(storage_path().'/exceltemplate/laphosoTONGHOP.xlsx', function($reader) use($resultsData){
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
            $row = 7;

            $col = 0;

            //-----------------------------------------Title------------------------------------------------------------------------------------------------
            $reader->getActiveSheet()->setCellValueByColumnAndRow(0, 2, 'NHU CẦU KINH PHÍ NĂM '.$resultsData['reportyear'].', DỰ TOÁN NĂM '.($resultsData['reportyear'] + 1).' ĐỐI VỚI CÁC CHẾ ĐỘ, CHÍNH SÁCH ƯU ĐÃI CHO TRẺ EM MẪU GIÁO, HỌC SINH, SINH VIÊN')->getStyle('A2')->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(2, 5, 'NHU CẦU KINH PHÍ NĂM '.$resultsData['reportyear'])->getStyle('C5')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(16, 6, 'Tổng nhu cầu kinh phí năm '.$resultsData['reportyear'])->getStyle('Q6')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

            $reader->getActiveSheet()->setCellValueByColumnAndRow(17, 5, 'NHU CẦU KINH PHÍ NĂM '.$resultsData['reportyear'])->getStyle('R5')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(31, 6, 'Tổng nhu cầu kinh phí năm '.$resultsData['reportyear'])->getStyle('AF6')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

            $reader->getActiveSheet()->setCellValueByColumnAndRow(32, 5, 'DỰ TOÁN KINH PHÍ NĂM '.($resultsData['reportyear'] + 1))->getStyle('AG5')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(46, 6, 'Tổng dự toán kinh phí năm '.($resultsData['reportyear'] + 1))->getStyle('AU6')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

            $reader->getActiveSheet()->setCellValueByColumnAndRow(47, 5, 'DỰ TOÁN KINH PHÍ NĂM '.($resultsData['reportyear'] + 1))->getStyle('AV5')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(61, 6, 'Tổng dự toán kinh phí năm '.($resultsData['reportyear'] + 1))->getStyle('BJ6')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);


            foreach ($resultsData['unit'] as $unit) {
                $row++;
                $row++;
                //Nhu cầu
                $reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row, '')->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($style);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $unit->unit_name)->getStyle('B'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(2, $row, '')->getStyle('C'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(3, $row, '')->getStyle('D'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(4, $row, '')->getStyle('E'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(5, $row, '')->getStyle('F'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(6, $row, '')->getStyle('G'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(7, $row, '')->getStyle('H'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(8, $row, '')->getStyle('I'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(9, $row, '')->getStyle('J'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(10, $row, '')->getStyle('K'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(11, $row, '')->getStyle('L'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(12, $row, '')->getStyle('M'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(13, $row, '')->getStyle('N'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row, '')->getStyle('O'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row, '')->getStyle('P'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row, '')->getStyle('Q'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);

                        //Dự toán
                $reader->getActiveSheet()->setCellValueByColumnAndRow(32, $row, '')->getStyle('AG'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(33, $row, '')->getStyle('AH'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(34, $row, '')->getStyle('AI'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(35, $row, '')->getStyle('AJ'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(36, $row, '')->getStyle('AK'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(37, $row, '')->getStyle('AL'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(38, $row, '')->getStyle('AM'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(39, $row, '')->getStyle('AN'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(40, $row, '')->getStyle('AO'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(41, $row, '')->getStyle('AP'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(42, $row, '')->getStyle('AQ'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(43, $row, '')->getStyle('AR'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(44, $row, '')->getStyle('AS'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(45, $row, '')->getStyle('AT'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(46, $row, '')->getStyle('AU'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);

                $stt = 0;
                foreach ($resultsData['DataNGNAorTONGHOP'] as $report) {
                    if ($report->schools_unit_id == $unit->unit_id) {
                        # code...
                        $row++;
                        //Nhu cầu
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row, ++$stt)->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $report->schools_name)->getStyle('B'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $report->nhucau_capbuhocphi)->getStyle('C'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $report->nhucau_hotrochiphihoctap)->getStyle('D'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $report->nhucau_hotroantrua)->getStyle('E'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $report->nhucau_tong_hotrobantru)->getStyle('F'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $report->nhucau_hotrotienan)->getStyle('G'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(7, $row, $report->nhucau_hotrotieno)->getStyle('H'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(8, $row, $report->nhucau_VHTT_tuthuoc)->getStyle('I'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(9, $row, $report->nhucau_tong_hotroHSKT)->getStyle('J'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(10, $row, $report->nhucau_hocbong)->getStyle('K'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(11, $row, $report->nhucau_muadodung)->getStyle('L'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(12, $row, $report->nhucau_hocbonghsdantocnoitru)->getStyle('M'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(13, $row, $report->nhucau_hotroHSDTTS)->getStyle('N'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(14, $row, $report->nhucau_hotroCPHTSVDTTS)->getStyle('O'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(15, $row, $report->nhucau_hotroNGNA)->getStyle('P'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(16, $row, $report->tong_nhucaukinhphi)->getStyle('Q'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);

                        //Dự toán
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(32, $row, $report->dutoan_capbuhocphi)->getStyle('AG'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(33, $row, $report->dutoan_hotrochiphihoctap)->getStyle('AH'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(34, $row, $report->dutoan_hotroantrua)->getStyle('AI'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(35, $row, $report->dutoan_tong_hotrobantru)->getStyle('AJ'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(36, $row, $report->dutoan_hotrotienan)->getStyle('AK'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(37, $row, $report->dutoan_hotrotieno)->getStyle('AL'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(38, $row, $report->dutoan_VHTT_tuthuoc)->getStyle('AM'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(39, $row, $report->dutoan_tong_hotroHSKT)->getStyle('AN'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(40, $row, $report->dutoan_hocbong)->getStyle('AO'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(41, $row, $report->dutoan_muadodung)->getStyle('AP'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(42, $row, $report->dutoan_hocbonghsdantocnoitru)->getStyle('AQ'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(43, $row, $report->dutoan_hotroHSDTTS)->getStyle('AR'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(44, $row, $report->dutoan_hotroCPHTSVDTTS)->getStyle('AS'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(45, $row, $report->dutoan_hotroNGNA)->getStyle('AT'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(46, $row, $report->tong_dutoankinhphi)->getStyle('AU'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                    }
                }
            }
        });

        if($type){
            return $excel->setFilename($filename)->store('xlsx', storage_path().'/exceldownload/THAMDINH');
        }else{
            return $excel->setFilename($filename)->download('xlsx');
        }
    }
    
    private function setCellExcelNGNA($resultsData, $filename, $type = true){
        $excel =    Excel::load(storage_path().'/exceltemplate/laphosoNGNA.xlsx', function($reader) use($resultsData){
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
            $row = 7;

            $col = 0;

            //-----------------------------------------Title------------------------------------------------------------------------------------------------
            $reader->getActiveSheet()->setCellValueByColumnAndRow(0, 3, 'NHU CẦU KINH PHÍ NĂM '.$resultsData['reportyear'].', DỰ TOÁN NĂM '.($resultsData['reportyear'] + 1).' ĐỐI VỚI CHÍNH SÁCH HỖ TRỢ NGƯỜI NẤU ĂN THEO NGHỊ QUYẾT SỐ 23/2015/NQ-HĐND CỦA HỘI ĐỒNG NHÂN DÂN')->getStyle('A3')->applyFromArray($FontArray)->applyFromArray($style);

            $reader->getActiveSheet()->setCellValueByColumnAndRow(2, 7, 'Học kỳ II năm học '.($resultsData['reportyear'] - 1).'-'.$resultsData['reportyear'])->getStyle('C7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(3, 7, 'Học kỳ I năm học '.$resultsData['reportyear'].'-'.($resultsData['reportyear'] + 1))->getStyle('D7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(4, 7, 'Học kỳ II năm học '.$resultsData['reportyear'].'-'.($resultsData['reportyear'] + 1))->getStyle('E7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(5, 7, 'Học kỳ I năm học '.($resultsData['reportyear'] + 1).'-'.($resultsData['reportyear'] + 2))->getStyle('F7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

            $reader->getActiveSheet()->setCellValueByColumnAndRow(6, 7, 'Học kỳ II năm học '.($resultsData['reportyear'] - 1).'-'.$resultsData['reportyear'])->getStyle('G7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(7, 7, 'Học kỳ I năm học '.$resultsData['reportyear'].'-'.($resultsData['reportyear'] + 1))->getStyle('H7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(8, 7, 'Học kỳ II năm học '.$resultsData['reportyear'].'-'.($resultsData['reportyear'] + 1))->getStyle('I7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(9, 7, 'Học kỳ I năm học '.($resultsData['reportyear'] + 1).'-'.($resultsData['reportyear'] + 2))->getStyle('J7')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);

            $reader->getActiveSheet()->setCellValueByColumnAndRow(10, 6, 'Nhu cầu kinh phí năm '.$resultsData['reportyear'])->getStyle('K6')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            $reader->getActiveSheet()->setCellValueByColumnAndRow(11, 6, 'Dự toán kinh phí năm '.($resultsData['reportyear'] + 1))->getStyle('L6')->applyFromArray($borderArray)->applyFromArray($FontArray)->applyFromArray($style);
            //-----------------------------------------End Title----------------------------------------------------------------------------------------------------

            foreach ($resultsData['unit'] as $unit) {
                $row++;
                $row++;
                $reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row, '')->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($style);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $unit->unit_name)->getStyle('B'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(2, $row, '')->getStyle('C'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(3, $row, '')->getStyle('D'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(4, $row, '')->getStyle('E'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(5, $row, '')->getStyle('F'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(6, $row, '')->getStyle('G'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(7, $row, '')->getStyle('H'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(8, $row, '')->getStyle('I'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(9, $row, '')->getStyle('J'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(10, $row, '')->getStyle('K'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(11, $row, '')->getStyle('L'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                $reader->getActiveSheet()->setCellValueByColumnAndRow(12, $row, '')->getStyle('M'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);

                $stt = 0;
                foreach ($resultsData['DataNGNAorTONGHOP'] as $report) {
                    if ($report->schools_unit_id == $unit->unit_id) {
                        # code...
                        $row++;                        
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(0, $row, ++$stt)->getStyle('A'.$row)->applyFromArray($borderArray)->applyFromArray($style);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(1, $row, $report->schools_name)->getStyle('B'.$row)->applyFromArray($borderArray)->applyFromArray($styleLeft);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(2, $row, $report->sohocsinhhocky2_old)->getStyle('C'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(3, $row, $report->sohocsinhhocky1_cur)->getStyle('D'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(4, $row, $report->sohocsinhhocky2_cur)->getStyle('E'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(5, $row, $report->sohocsinhhocky1_new)->getStyle('F'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(6, $row, $report->nguoinauanhocky2_old)->getStyle('G'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(7, $row, $report->nguoinauanhocky1_cur)->getStyle('H'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(8, $row, $report->nguoinauanhocky2_cur)->getStyle('I'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(9, $row, $report->nguoinauanhocky1_new)->getStyle('J'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(10, $row, $report->nhucau)->getStyle('K'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(11, $row, $report->dutoan)->getStyle('L'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                        $reader->getActiveSheet()->setCellValueByColumnAndRow(12, $row, '')->getStyle('M'.$row)->applyFromArray($borderArray)->applyFromArray($styleRight);
                    }
                }
            }
        });

        if($type){
            return $excel->setFilename($filename)->store('xlsx', storage_path().'/exceldownload/THAMDINH');
        }else{
            return $excel->setFilename($filename)->download('xlsx');
        }
    }

//Helper get data Export Excel---------------------------------------------------------------------------------------------------------------------------------------
    private function getData($data, $reporttype, $type = null)
    {
        $dataResults = [];
        foreach ($data as $value) {
            $getDataResults = null;
            $historyYear = "";

            if ($type == 1 || $type == 2) {
                $historyYear = $value->report_year.'-'.($value->report_year + 1);
            }

            if ($type == 3) {
                $historyYear = ($value->report_year + 1).'-'.($value->report_year + 2);
            }

            if (!is_null($reporttype) && !empty($reporttype) && $reporttype == "MGHP") {
                $getDataResults = DB::table('qlhs_miengiamhocphi')
                ->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_miengiamhocphi.id_profile')
                ->leftJoin(DB::raw('(select decided_profile_id, max(decided_id) decided_id, max(decided_confirmation) decided_confirmation, max(decided_confirmdate) decided_confirmdate, max(decided_number) decided_number, max(decided_type) decided_type from qlhs_decided GROUP BY decided_profile_id) as qlhs_decided'), 'qlhs_decided.decided_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_decided.decided_type = "MGHP"'))
                ->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
                ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw("qlhs_miengiamhocphi.id_profile and qlhs_profile_history.history_year LIKE '%".$historyYear."%'"))
                ->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
                ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
                ->where('qlhs_miengiamhocphi.type_code', 'LIKE', '%'.$value->report_type.'%')
                ->where('qlhs_miengiamhocphi.type', '=', $type)
                ->select('qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_decided.decided_id', 'qlhs_decided.decided_confirmation', 'qlhs_decided.decided_number', 'qlhs_decided.decided_confirmdate', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'qlhs_miengiamhocphi.*')->get();
            }

            if (!is_null($reporttype) && !empty($reporttype) && $reporttype == "CPHT") {
                $getDataResults = DB::table('qlhs_chiphihoctap')
                ->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_chiphihoctap.cpht_profile_id')
                ->leftJoin(DB::raw('(select decided_profile_id, max(decided_id) decided_id, max(decided_confirmation) decided_confirmation, max(decided_confirmdate) decided_confirmdate, max(decided_number) decided_number, max(decided_type) decided_type from qlhs_decided GROUP BY decided_profile_id) as qlhs_decided'), 'qlhs_decided.decided_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_decided.decided_type = "CPHT"'))
                ->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
                ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw("qlhs_chiphihoctap.cpht_profile_id and qlhs_profile_history.history_year LIKE '%".$historyYear."%'"))
                ->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
                ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
                ->where('qlhs_chiphihoctap.type_code', 'LIKE', '%'.$value->report_type.'%')
                ->where('qlhs_chiphihoctap.type', '=', $type)
                ->select('qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_decided.decided_id', 'qlhs_decided.decided_confirmation', 'qlhs_decided.decided_number', 'qlhs_decided.decided_confirmdate', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'qlhs_chiphihoctap.*')->get();
            }

            if (!is_null($reporttype) && !empty($reporttype) && $reporttype == "HTAT") {
                $getDataResults = DB::table('qlhs_hotrotienan')
                ->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_hotrotienan.htta_profile_id')
                ->leftJoin(DB::raw('(select decided_profile_id, max(decided_id) decided_id, max(decided_confirmation) decided_confirmation, max(decided_confirmdate) decided_confirmdate, max(decided_number) decided_number, max(decided_type) decided_type from qlhs_decided GROUP BY decided_profile_id) as qlhs_decided'), 'qlhs_decided.decided_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_decided.decided_type = "HTAT"'))
                ->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
                ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw("qlhs_hotrotienan.htta_profile_id and qlhs_profile_history.history_year LIKE '%".$historyYear."%'"))
                ->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
                ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
                ->where('qlhs_hotrotienan.type_code', 'LIKE', '%'.$value->report_type.'%')
                ->where('qlhs_hotrotienan.type', '=', $type)
                ->select('qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_decided.decided_id', 'qlhs_decided.decided_confirmation', 'qlhs_decided.decided_number', 'qlhs_decided.decided_confirmdate', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'qlhs_hotrotienan.*')->get();
            }

            if (!is_null($reporttype) && !empty($reporttype) && $reporttype == "HTBT") {
                $getDataResults = DB::table('qlhs_hotrohocsinhbantru')
                ->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_hotrohocsinhbantru.profile_id')
                ->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
                ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw("qlhs_hotrohocsinhbantru.profile_id and qlhs_profile_history.history_year LIKE '%".$historyYear."%'"))
                ->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
                ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
                ->where('qlhs_hotrohocsinhbantru.type_code', 'LIKE', '%'.$value->report_type.'%')
                ->where('qlhs_hotrohocsinhbantru.type', '=', $type)
                ->select('qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_km', 'qlhs_profile.profile_giaothong', 'qlhs_profile.profile_year', 'qlhs_hotrohocsinhbantru.*')->get();
            }

            if (!is_null($reporttype) && !empty($reporttype) && $reporttype == "NGNA") {
                $getDataResults = DB::table('qlhs_hotronguoinauan')
                ->join('qlhs_schools', 'qlhs_schools.schools_id', '=', 'qlhs_hotronguoinauan.school_id')
                ->where('qlhs_hotronguoinauan.type_code', 'LIKE', '%'.$value->report_type.'%')
                ->select('qlhs_schools.schools_name', 'qlhs_schools.schools_unit_id', 'qlhs_hotronguoinauan.*')->get();
            }

            if (!is_null($reporttype) && !empty($reporttype) && $reporttype == "HSKT") {
                $getDataResults = DB::table('qlhs_hotrohocsinhkhuyettat')
                ->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_hotrohocsinhkhuyettat.profile_id')
                ->leftJoin(DB::raw('(select decided_profile_id, max(decided_id) decided_id, max(decided_confirmation) decided_confirmation, max(decided_confirmdate) decided_confirmdate, max(decided_number) decided_number, max(decided_type) decided_type from qlhs_decided GROUP BY decided_profile_id) as qlhs_decided'), 'qlhs_decided.decided_profile_id', '=', DB::raw('qlhs_profile.profile_id and qlhs_decided.decided_type = "HSKT"'))
                ->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
                ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw("qlhs_hotrohocsinhkhuyettat.profile_id and qlhs_profile_history.history_year LIKE '%".$historyYear."%'"))
                ->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
                ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
                ->where('qlhs_hotrohocsinhkhuyettat.type_code', 'LIKE', '%'.$value->report_type.'%')
                ->where('qlhs_hotrohocsinhkhuyettat.type', '=', $type)
                ->select('qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_decided.decided_id', 'qlhs_decided.decided_confirmation', 'qlhs_decided.decided_number', 'qlhs_decided.decided_confirmdate', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'qlhs_hotrohocsinhkhuyettat.*')->get();
            }

            if (!is_null($reporttype) && !empty($reporttype) && $reporttype == "HSDTTS") {
                $getDataResults = DB::table('qlhs_hotrohocsinhdantocthieuso')
                ->join('qlhs_profile', 'qlhs_profile.profile_id', '=', 'qlhs_hotrohocsinhdantocthieuso.profile_id')
                ->join('qlhs_nationals', 'nationals_id', '=', 'qlhs_profile.profile_nationals_id')
                ->join('qlhs_profile_history', 'qlhs_profile_history.history_profile_id', '=', DB::raw("qlhs_hotrohocsinhdantocthieuso.profile_id and qlhs_profile_history.history_year LIKE '%".$historyYear."%'"))
                ->join('qlhs_site as huyen', 'huyen.site_id', '=', 'qlhs_profile.profile_site_id2')
                ->leftJoin('qlhs_site as xa', 'xa.site_id', '=', 'qlhs_profile.profile_site_id3')
                ->where('qlhs_hotrohocsinhdantocthieuso.type_code', 'LIKE', '%'.$value->report_type.'%')
                ->where('qlhs_hotrohocsinhdantocthieuso.type', '=', $type)
                ->select('qlhs_profile_history.level_old', 'qlhs_profile_history.level_cur', 'qlhs_profile_history.level_new', 'qlhs_profile.profile_id', 'qlhs_profile.profile_name', 'qlhs_profile.profile_birthday', 'qlhs_nationals.nationals_name', 'qlhs_profile.profile_household', 'qlhs_profile.profile_site_id3', 'huyen.site_name as tenhuyen', 'xa.site_name as tenxa', 'qlhs_profile.profile_parentname', 'qlhs_profile.profile_year', 'qlhs_hotrohocsinhdantocthieuso.*')->get();

                // $getDataResults = DB::select("select qlhs_profile_history.level_old, qlhs_profile_history.level_cur, qlhs_profile_history.level_new, qlhs_profile.profile_id, qlhs_profile.profile_name,  qlhs_profile.profile_birthday,  qlhs_nationals.nationals_name,  qlhs_profile.profile_household, qlhs_profile.profile_site_id3,  huyen.site_name AS tenhuyen, xa.site_name AS tenxa,  qlhs_profile.profile_parentname, qlhs_profile.profile_year,  qlhs_hotrohocsinhdantocthieuso.* FROM qlhs_hotrohocsinhdantocthieuso INNER JOIN qlhs_profile ON qlhs_profile.profile_id = qlhs_hotrohocsinhdantocthieuso.profile_id INNER JOIN qlhs_nationals ON nationals_id = qlhs_profile.profile_nationals_id INNER JOIN qlhs_profile_history ON qlhs_profile_history.history_profile_id = qlhs_hotrohocsinhdantocthieuso.profile_id AND qlhs_profile_history.history_year LIKE '%".$historyYear."%' INNER JOIN qlhs_site AS huyen ON huyen.site_id = qlhs_profile.profile_site_id2 LEFT JOIN qlhs_site AS xa ON xa.site_id = qlhs_profile.profile_site_id3 WHERE qlhs_hotrohocsinhdantocthieuso.type_code LIKE '%".$value->report_type."%' AND qlhs_hotrohocsinhdantocthieuso.type = ".$type."", array());

                // return $getDataResults;
            }

            if (!is_null($reporttype) && !empty($reporttype) && $reporttype == "TONGHOP") {
                $getDataResults = DB::table('qlhs_tonghopbaocao')
                ->join('qlhs_schools', 'qlhs_schools.schools_id', '=', 'qlhs_tonghopbaocao.school_id')
                ->where('qlhs_tonghopbaocao.type_code', 'LIKE', '%'.$value->report_type.'%')
                ->select('qlhs_schools.schools_name', 'qlhs_schools.schools_unit_id', 'qlhs_tonghopbaocao.*')->get();
            }

            if (!is_null($getDataResults) && count($getDataResults) > 0) {
                foreach ($getDataResults as $data) {
                    array_push($dataResults, $data);
                }
            }
        }

        return $dataResults;
    }

//Helper get count data school Export Excel--------------------------------------------------------------------------------------------------------
    public function countValueMGHP($type = null, $code){
        if($type!=null){
            $count = DB::table('qlhs_miengiamhocphi')
            ->where('type_code','=',$code)
            ->where('type','=',$type)
            ->select(
                DB::raw('sum(mienphi_1) as tongmien1'),
                DB::raw('sum(mienphi_2) as tongmien2'),
                DB::raw('sum(mienphi_3) as tongmien3'),
                DB::raw('sum(mienphi_4) as tongmien4'),
                DB::raw('sum(mienphi_5) as tongmien5'),
                DB::raw('sum(giam_70) as tonggiam70'),
                DB::raw('sum(giam_50_1) as tonggiam501'),
                DB::raw('sum(giam_50_2) as tonggiam502'),
                DB::raw('sum(hocky2_old) as tonghk12'),
                DB::raw('sum(hocky1_cur) as tonghk21'),
                DB::raw('sum(hocky2_cur) as tonghk22'),
                DB::raw('sum(hocky1_new) as tonghk31'),
                DB::raw('sum(nhu_cau) as tongnc'),
                DB::raw('sum(du_toan) as tongdt')
                )->first();
            return $count;
        }else{
            $count = DB::table('qlhs_miengiamhocphi')
            ->where('type_code','=',$code)
            ->select(
                DB::raw('sum(mienphi_1) as tongmien1'),
                DB::raw('sum(mienphi_2) as tongmien2'),
                DB::raw('sum(mienphi_3) as tongmien3'),
                DB::raw('sum(mienphi_4) as tongmien4'),
                DB::raw('sum(mienphi_5) as tongmien5'),
                DB::raw('sum(giam_70) as tonggiam70'),
                DB::raw('sum(giam_50_1) as tonggiam501'),
                DB::raw('sum(giam_50_2) as tonggiam502'),
                DB::raw('sum(hocky2_old) as tonghk12'),
                DB::raw('sum(hocky1_cur) as tonghk21'),
                DB::raw('sum(hocky2_cur) as tonghk22'),
                DB::raw('sum(hocky1_new) as tonghk31'),
                DB::raw('sum(nhu_cau) as tongnc'),
                DB::raw('sum(du_toan) as tongdt')
                )->first();
            return $count;
        }
    }
    
    public function countValueCPHT($type = null, $code){

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
    
    public function countValueHTAT($type = null, $code){

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
    
    public function countValueHTBT($type = null, $code){

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
    
    public function countValueHSDTTS($type = null, $code){

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
    
    public function countValueHSKT($type = null, $code){

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

//Helper get count data unit Export Excel-----------------------------------------------------------------------------------------------------------
    private function countUnitMGHP($unitID, $listSchools){
        $listData = null;
        $listDataResult = [];
        $tong_mien_1 = 0;
        $tong_mien_2 = 0;
        $tong_mien_3 = 0;
        $tong_mien_4 = 0;
        $tong_mien_5 = 0;
        $tong_giam_70 = 0;
        $tong_giam_501 = 0;
        $tong_giam_502 = 0;
        $tong_hk_old = 0;
        $tong_hk1_cur = 0;
        $tong_hk2_cur = 0;
        $tong_hk1_new = 0;
        $tong_nhu_cau = 0;
        $tong_du_toan = 0;
        foreach ($listSchools as $school) {
            if ($school->schools_unit_id == $unitID) {
                $listData = $this->countValueMGHP(null, $school->report_type);

                $tong_mien_1 += $listData->tongmien1;
                $tong_mien_2 += $listData->tongmien2;
                $tong_mien_3 += $listData->tongmien3;
                $tong_mien_4 += $listData->tongmien4;
                $tong_mien_5 += $listData->tongmien5;
                $tong_giam_70 += $listData->tonggiam70;
                $tong_giam_501 += $listData->tonggiam501;
                $tong_giam_502 += $listData->tonggiam502;
                $tong_hk_old += $listData->tonghk12;
                $tong_hk1_cur += $listData->tonghk21;
                $tong_hk2_cur += $listData->tonghk22;
                $tong_hk1_new += $listData->tonghk31;
                $tong_nhu_cau += $listData->tongnc;
                $tong_du_toan += $listData->tongdt;
            }                
        }

        $listDataResult['tongmien_1'] = $tong_mien_1;
        $listDataResult['tongmien_2'] = $tong_mien_2;
        $listDataResult['tongmien_3'] = $tong_mien_3;
        $listDataResult['tongmien_4'] = $tong_mien_4;
        $listDataResult['tongmien_5'] = $tong_mien_5;
        $listDataResult['tonggiam_70'] = $tong_giam_70;
        $listDataResult['tonggiam_501'] = $tong_giam_501;
        $listDataResult['tonggiam_502'] = $tong_giam_502;
        $listDataResult['tonghk_old'] = $tong_hk_old;
        $listDataResult['tonghk1_cur'] = $tong_hk1_cur;
        $listDataResult['tonghk2_cur'] = $tong_hk2_cur;
        $listDataResult['tonghk1_new'] = $tong_hk1_new;
        $listDataResult['tong_nhucau'] = $tong_nhu_cau;
        $listDataResult['tong_dutoan'] = $tong_du_toan;

        return $listDataResult;
    }

    private function countUnitCPHT($unitID, $listSchools){
        $listData = null;
        $listDataResult = [];
        
        $tong_hk2_old = 0;
        $tong_hk1_cur = 0;
        $tong_hk2_cur = 0;
        $tong_hk1_new = 0;
        $tong_nhu_cau = 0;
        $tong_du_toan = 0;

        foreach ($listSchools as $school) {
            if ($school->schools_unit_id == $unitID) {
                $listData = $this->countValueCPHT(null, $school->report_type);

                $tong_hk2_old += $listData->tonghocky2_old;
                $tong_hk1_cur += $listData->tonghocky1_cur;
                $tong_hk2_cur += $listData->tonghocky2_cur;
                $tong_hk1_new += $listData->tonghocky1_new;
                $tong_nhu_cau += $listData->tong_nhucau;
                $tong_du_toan += $listData->tong_dutoan;
            }                
        }

        $listDataResult['tonghk2_old'] = $tong_hk2_old;
        $listDataResult['tonghk1_cur'] = $tong_hk1_cur;
        $listDataResult['tonghk2_cur'] = $tong_hk2_cur;
        $listDataResult['tonghk1_new'] = $tong_hk1_new;
        $listDataResult['tong_nhucau'] = $tong_nhu_cau;
        $listDataResult['tong_dutoan'] = $tong_du_toan;

        return $listDataResult;
    }

    private function countUnitHTAT($unitID, $listSchools){
        $listData = null;
        $listDataResult = [];
        $tong_doituong_1 = 0;
        $tong_doituong_2 = 0;
        $tong_doituong_3 = 0;
        $tong_doituong_4 = 0;
        $tong_hk2_old = 0;
        $tong_hk1_cur = 0;
        $tong_hk2_cur = 0;
        $tong_hk1_new = 0;
        $tong_nhu_cau = 0;
        $tong_du_toan = 0;

        foreach ($listSchools as $school) {
            if ($school->schools_unit_id == $unitID) {
                $listData = $this->countValueHTAT(null, $school->report_type);

                $tong_doituong_1 += $listData->tonghtta_doituong1;
                $tong_doituong_2 += $listData->tonghtta_doituong2;
                $tong_doituong_3 += $listData->tonghtta_doituong3;
                $tong_doituong_4 += $listData->tonghtta_doituong4;
                $tong_hk2_old += $listData->tonghocky2_old;
                $tong_hk1_cur += $listData->tonghocky1_cur;
                $tong_hk2_cur += $listData->tonghocky2_cur;
                $tong_hk1_new += $listData->tonghocky1_new;
                $tong_nhu_cau += $listData->tongnhu_cau;
                $tong_du_toan += $listData->tongdu_toan;
            }                
        }

        $listDataResult['tong_doituong1'] = $tong_doituong_1;
        $listDataResult['tong_doituong2'] = $tong_doituong_2;
        $listDataResult['tong_doituong3'] = $tong_doituong_3;
        $listDataResult['tong_doituong4'] = $tong_doituong_4;
        $listDataResult['tonghk2_old'] = $tong_hk2_old;
        $listDataResult['tonghk1_cur'] = $tong_hk1_cur;
        $listDataResult['tonghk2_cur'] = $tong_hk2_cur;
        $listDataResult['tonghk1_new'] = $tong_hk1_new;
        $listDataResult['tong_nhucau'] = $tong_nhu_cau;
        $listDataResult['tong_dutoan'] = $tong_du_toan;

        return $listDataResult;
    }

    private function countUnitHTBT($unitID, $listSchools){
        $listData = null;
        $listDataResult = [];
        $tong_hotro_tienan = 0;
        $tong_hotro_tieno = 0;
        $tong_hotro_tienan_hk2_old = 0;
        $tong_hotro_tieno_hk2_old = 0;
        $tong_hotro_tienan_hk1_cur = 0;
        $tong_hotro_tieno_hk1_cur = 0;
        $tong_hotro_tienan_hk2_cur = 0;
        $tong_hotro_tieno_hk2_cur = 0;
        $tong_hotro_tienan_hk1_new = 0;
        $tong_hotro_tieno_hk1_new = 0;
        $tong_nhucau_hotro_tienan = 0;
        $tong_nhucau_hotro_tieno = 0;
        $tong_nhucau_hotro_VHTT = 0;
        $tong_nhu_cau = 0;
        $tong_dutoan_hotro_tienan = 0;
        $tong_dutoan_hotro_tieno = 0;
        $tong_dutoan_hotro_VHTT = 0;
        $tong_du_toan = 0;
        foreach ($listSchools as $school) {
            if ($school->schools_unit_id == $unitID) {
                $listData = $this->countValueHTBT(null, $school->report_type);

                $tong_hotro_tienan += $listData->tonghotrotienan;
                $tong_hotro_tieno += $listData->tonghotrotieno;
                $tong_hotro_tienan_hk2_old += $listData->tongtienan_hocky2_old;
                $tong_hotro_tieno_hk2_old += $listData->tongtieno_hocky2_old;
                $tong_hotro_tienan_hk1_cur += $listData->tongtienan_hocky1_cur;
                $tong_hotro_tieno_hk1_cur += $listData->tongtieno_hocky1_cur;
                $tong_hotro_tienan_hk2_cur += $listData->tongtienan_hocky2_cur;
                $tong_hotro_tieno_hk2_cur += $listData->tongtieno_hocky2_cur;
                $tong_hotro_tienan_hk1_new += $listData->tongtienan_hocky1_new;
                $tong_hotro_tieno_hk1_new += $listData->tongtieno_hocky1_new;
                $tong_nhucau_hotro_tienan += $listData->tongnhucau_hotrotienan;
                $tong_nhucau_hotro_tieno += $listData->tongnhucau_hotrotieno;
                $tong_nhucau_hotro_VHTT += $listData->tongnhucau_hotroVHTT;
                $tong_nhu_cau += $listData->tong_tongnhucau;
                $tong_dutoan_hotro_tienan += $listData->tongdutoan_hotrotienan;
                $tong_dutoan_hotro_tieno += $listData->tongdutoan_hotrotieno;
                $tong_dutoan_hotro_VHTT += $listData->tongdutoan_hotroVHTT;
                $tong_du_toan += $listData->tong_tongdutoan;
            }                
        }

        $listDataResult['tong_hotrotienan'] = $tong_hotro_tienan;
        $listDataResult['tong_hotrotieno'] = $tong_hotro_tieno;
        $listDataResult['tong_hotrotienan_hk2_old'] = $tong_hotro_tienan_hk2_old;
        $listDataResult['tong_hotrotieno_hk2_old'] = $tong_hotro_tieno_hk2_old;
        $listDataResult['tong_hotrotienan_hk1_cur'] = $tong_hotro_tienan_hk1_cur;
        $listDataResult['tong_hotrotieno_hk1_cur'] = $tong_hotro_tieno_hk1_cur;
        $listDataResult['tong_hotrotienan_hk2_cur'] = $tong_hotro_tienan_hk2_cur;
        $listDataResult['tong_hotrotieno_hk2_cur'] = $tong_hotro_tieno_hk2_cur;
        $listDataResult['tong_hotrotienan_hk1_new'] = $tong_hotro_tienan_hk1_new;
        $listDataResult['tong_hotrotieno_hk1_new'] = $tong_hotro_tieno_hk1_new;
        $listDataResult['tong_nhucau_hotrotienan'] = $tong_nhucau_hotro_tienan;
        $listDataResult['tong_nhucau_hotrotieno'] = $tong_nhucau_hotro_tieno;
        $listDataResult['tong_nhucau_hotroVHTT'] = $tong_nhucau_hotro_VHTT;
        $listDataResult['tong_nhucau'] = $tong_nhu_cau;
        $listDataResult['tong_dutoan_hotrotienan'] = $tong_dutoan_hotro_tienan;
        $listDataResult['tong_dutoan_hotrotieno'] = $tong_dutoan_hotro_tieno;
        $listDataResult['tong_dutoan_hotroVHTT'] = $tong_dutoan_hotro_VHTT;
        $listDataResult['tong_dutoan'] = $tong_du_toan;

        return $listDataResult;
    }

    private function countUnitHSDTTS($unitID, $listSchools){
        $listData = null;
        $listDataResult = [];
        
        $tong_hk2_old = 0;
        $tong_hk1_cur = 0;
        $tong_hk2_cur = 0;
        $tong_hk1_new = 0;
        $tong_nhu_cau = 0;
        $tong_du_toan = 0;

        foreach ($listSchools as $school) {
            if ($school->schools_unit_id == $unitID) {
                $listData = $this->countValueHSDTTS(null, $school->report_type);

                $tong_hk2_old += $listData->tonghocky2_old;
                $tong_hk1_cur += $listData->tonghocky1_cur;
                $tong_hk2_cur += $listData->tonghocky2_cur;
                $tong_hk1_new += $listData->tonghocky1_new;
                $tong_nhu_cau += $listData->tong_nhucau;
                $tong_du_toan += $listData->tong_dutoan;
            }                
        }

        $listDataResult['tonghk2_old'] = $tong_hk2_old;
        $listDataResult['tonghk1_cur'] = $tong_hk1_cur;
        $listDataResult['tonghk2_cur'] = $tong_hk2_cur;
        $listDataResult['tonghk1_new'] = $tong_hk1_new;
        $listDataResult['tong_nhucau'] = $tong_nhu_cau;
        $listDataResult['tong_dutoan'] = $tong_du_toan;

        return $listDataResult;
    }

    private function countUnitHSKT($unitID, $listSchools){
        $listData = null;
        $listDataResult = [];
        $tong_hk2_old = 0;
        $tong_hk1_cur = 0;
        $tong_hk2_cur = 0;
        $tong_hk1_new = 0;
        $tong_nhucau_hocbong = 0;
        $tong_nhucau_muadodung = 0;
        $tong_nhu_cau = 0;
        $tong_dutoan_hocbong = 0;
        $tong_dutoan_muadodung = 0;
        $tong_du_toan = 0;

        foreach ($listSchools as $school) {
            if ($school->schools_unit_id == $unitID) {
                $listData = $this->countValueHSKT(null, $school->report_type);

                $tong_hk2_old += $listData->tonghocky2_old;
                $tong_hk1_cur += $listData->tonghocky1_cur;
                $tong_hk2_cur += $listData->tonghocky2_cur;
                $tong_hk1_new += $listData->tonghocky1_new;
                $tong_nhucau_hocbong += $listData->tongnhucau_hocbong;
                $tong_nhucau_muadodung += $listData->tongnhucau_muadodung;
                $tong_nhu_cau += $listData->tong_tongnhucau;
                $tong_dutoan_hocbong += $listData->tongdutoan_hocbong;
                $tong_dutoan_muadodung += $listData->tongdutoan_muadodung;
                $tong_du_toan += $listData->tong_tongdutoan;
            }                
        }

        $listDataResult['tonghk2_old'] = $tong_hk2_old;
        $listDataResult['tonghk1_cur'] = $tong_hk1_cur;
        $listDataResult['tonghk2_cur'] = $tong_hk2_cur;
        $listDataResult['tonghk1_new'] = $tong_hk1_new;
        $listDataResult['tong_nhucauhocbong'] = $tong_nhucau_hocbong;
        $listDataResult['tong_nhucaumuadodung'] = $tong_nhucau_muadodung;
        $listDataResult['tong_nhucau'] = $tong_nhu_cau;
        $listDataResult['tong_dutoanhocbong'] = $tong_dutoan_hocbong;
        $listDataResult['tong_dutoanmuadodung'] = $tong_dutoan_muadodung;
        $listDataResult['tong_dutoan'] = $tong_du_toan;

        return $listDataResult;
    }



    public function loadAllUserTotal(){
        try {
            $getAllUser = DB::table('users')->leftJoin('qlhs_department', 'qlhs_department.department_id', '=', 'users.phongban_id')
            ->where('users.level', '=', 2)
            ->select('users.id', 'users.first_name', 'users.last_name', 'qlhs_department.department_name')->get();

            return $getAllUser;
        } catch (Exception $e) {
            
        }
    }

    //------------------------------------------------Form Phê duyệt mới---------------------------------------------------------
    public function approvedPheDuyet($objData){
        $result = [];
        try {

            $arrData = [];
            $update = 0;

            $arrData = explode("-", $objData);

            $id = substr($arrData[0], 2);

            $note = $arrData[2];

            return $arrData[3];

            if ($arrData[1] == "HK1") {
                $update = DB::update("update qlhs_tonghopchedo set
                    qlhs_thcd_trangthai_PD = 1
                    where qlhs_thcd_id = $id");
            }

            if ($arrData[1] == "HK2") {
                $update = DB::update("update qlhs_tonghopchedo set
                    qlhs_thcd_trangthai_PD_HK2 = 1
                    where qlhs_thcd_id = $id");
            }

            if ($arrData[1] == "CA") {
                $update = DB::update("update qlhs_tonghopchedo set
                    qlhs_thcd_trangthai_PD = 1, 
                    qlhs_thcd_trangthai_PD_HK2 = 1
                    where qlhs_thcd_id = $id");
            }
            

            if ($update > 0) {
                $result['success'] = 'Phê duyệt thành công';
            }
            else {
                $result['error'] = 'Phê duyệt thất bại';
            }

            return $result;
        } catch (Exception $e) {
            return $result['error'] = $e;
        }
    }
}