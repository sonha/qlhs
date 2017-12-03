<?php 
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
/**
* 
*/

class DanhMucPhongBan extends Model
{
	protected  $table="qlhs_department";
    protected $fillable = [
        'department_id', 'department_code', 'department_name', 'department_function', 'department_manager', 'department_deputy', 'department_parent_id', 'department_active', 'department_rewrite', 'department_md5', 'department_level', 'department_create_userid', 'department_createdate', 'department_update_userid', 'department_updatedate'
    ];
}


?>