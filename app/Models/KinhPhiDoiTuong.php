<?php 
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class KinhPhiDoiTuong extends Model
{
	protected  $table="qlhs_kinhphidoituong";
    protected $fillable = [
        'code', 'doituong_id', 'money','start_date','end_date','create_userid','update_userid','created_at','updated_at','idTruong','status'
    ];
}