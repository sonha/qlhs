<?php 
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
/**
* 
*/

class DanhMucDoiTuong extends Model
{
	protected  $table="qlhs_subject";
    protected $fillable = [
        'subject_id', 'subject_code', 'subject_name', 'subject_active', 'subject_rewrite', 'subject_md5', 'subject_create_userid', 'subject_update_userid'
    ];
}

?>