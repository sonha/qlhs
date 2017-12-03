<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PagesController extends Controller
{

    public function getHome()
    {
		//if(Auth::guest()){
		        return view('auth.login');
		//}else{
		//	 return view('layouts.auth', ['category' => 'James']);
		//}
    }
}
