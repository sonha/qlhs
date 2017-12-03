<?php 
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
/**
* 
*/

class DanhMucKhoi extends Model
{
	protected  $table="qlhs_unit";
    protected $fillable = [
        'unit_id', 'unit_code', 'unit_name', 'unit_active', 'unit_rewrite', 'unit_md5', 'unit_create_userid', 'unit_update_userid'
    ];
}

?>