<?php 
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class KinhPhiNamHoc extends Model
{
	protected  $table="qlhs_kinhphinamhoc";
    protected $fillable = [
        'code', 'codeYear', 'money','start_date','end_date','create_userid','update_userid','created_at','updated_at','idTruong'
    ];
}