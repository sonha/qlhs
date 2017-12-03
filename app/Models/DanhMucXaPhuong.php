<?php 
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
/**
* 
*/

class DanhMucXaPhuong extends Model
{
    protected  $table="qlhs_site";
    protected $fillable = [
        'site_id', 'site_code', 'site_name', 'site_parent_id', 'site_level', 'site_active', 'site_rewrite', 'site_md5', 'site_create_userid', 'site_createdate', 'site_update_userid', 'site_updatedate'
    ];
}

?>