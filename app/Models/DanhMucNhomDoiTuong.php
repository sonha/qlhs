<?php 
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
/**
* 
*/
class DanhMucNhomDoiTuong extends Model
{
	protected  $table="qlhs_group";
    protected $fillable = [
        'group_id', 'group_code', 'group_name', 'group_active', 'group_rewrite', 'group_md5', 'group_create_userid', 'group_update_userid'
    ];
}

?>