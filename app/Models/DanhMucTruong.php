<?php 
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
/**
* 
*/

class DanhMucTruong extends Model
{
	protected  $table="qlhs_schools";
    protected $fillable = [
        'schools_id', 'schools_code', 'schools_name', 'schools_active', 'schools_rewrite', 'schools_md5', 'schools_unit_id', 'schools_create_userid', 'schools_createdate', 'schools_update_userid', 'schools_updatedate'
    ];
}

?>