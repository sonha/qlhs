<?php 
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DanhMucQuyetDinh extends Model
{
    protected  $table="qlhs_decided";
    protected $fillable = [
        'decided_id', 'decided_code', 'decided_name', 'decided_number', 'decided_confirmation', 'decided_confirmdate', 'decided_filename', 'decided_profile_id', 'decided_create_userid', 'decided_createdate', 'decided_update_userid', 'decided_updatedate'
    ];
}

?>