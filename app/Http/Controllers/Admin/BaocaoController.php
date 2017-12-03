<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class BaocaoController extends Controller
{
    public function getReport(){
        return view('admin.baocao.listing');
        // /return view('category/wards')->with('wards', $wards);
    }
    
      
}
