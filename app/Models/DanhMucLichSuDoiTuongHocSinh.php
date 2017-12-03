<?php 
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DanhMucLichSuDoiTuongHocSinh extends Model
{
    protected  $table="qlhs_profile_subject";
    protected $fillable = [
        'profile_subject_id', 'profile_subject_profile_id', 'profile_subject_subject_id', 'profile_subject_create_userid', 'profile_subject_createdate', 'profile_subject_update_userid', 'profile_subject_updatedate'
    ];
}

?>