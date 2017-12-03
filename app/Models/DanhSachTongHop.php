<?php 
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
/**
* 
*/

class DanhSachTongHop extends Model
{
	protected  $table="qlhs_pheduyettonghop";
	protected $primaryKey  = 'pheduyettonghop_id';
    protected $fillable = [
         'pheduyettonghop_name', 'pheduyettonghop_year', 'pheduyettonghop_type', 'pheduyettonghop_nguoilap', 'pheduyettonghop_ngaylap', 'pheduyettonghop_dinhkem', 'pheduyettonghop_code', 'pheduyettonghop_note', 'pheduyettonghop_status', 'pheduyettonghop_nguoiky', 'created_user','pheduyettonghop_danhsach','pheduyettonghop_nguoithamdinh','pheduyettonghop_nguoitralai','pheduyettonghop_ngaytralai'
    ];
}

?>