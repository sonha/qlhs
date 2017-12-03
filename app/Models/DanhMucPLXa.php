<?php 
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
/**
* 
*/
class DanhMucPLXa extends Model
{
	protected  $table="qlhs_wards";
    protected $fillable = [
        'wards_id', 'wards_code', 'wards_name', 'wards_parent_id', 'wards_active', 'wards_rewrite', 'wards_md5', 'wards_level', 'wards_create_userid', 'wards_createdate', 'wards_update_userid', 'wards_updatedate'
    ];
}

?>