<?php 
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
/**
* 
*/

class DanhMucDantoc extends Model
{
	protected  $table="qlhs_nationals";
    protected $fillable = [
        'nationals_id', 'nationals_code', 'nationals_name', 'nationals_active', 'nationals_rewrite', 'nationals_md5', 'nationals_create_userid', 'nationals_update_userid'
    ];
}

?>