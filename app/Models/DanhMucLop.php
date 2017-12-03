<?php 
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
/**
* 
*/

class DanhMucLop extends Model
{
	protected  $table="qlhs_class";
    protected $fillable = [
        'class_id', 'class_schools_id', 'class_level_id', 'class_code', 'class_name', 'class_active', 'class_rewrite', 'class_md5', 'class_create_userid', 'class_createdate', 'class_update_userid', 'class_updatedate'
    ];
}

?>