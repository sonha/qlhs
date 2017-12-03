<?php 
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
/**
* 
*/

class HoSoBaoCao extends Model
{
	protected  $table="qlhs_hosobaocao";
	protected $primaryKey  = 'report_id';
    protected $fillable = [
         'report_name', 'report_type', 'report_date', 'created_at', 'updated_at', 'create_userid', 'update_userid', 'report_user', 'report_user_sign', 'report_user_verify', 'report_user_approved','report_attach_name','report_nature','report','report_status','report_verify','report_approved','report_user_send'
    ];
}

?>