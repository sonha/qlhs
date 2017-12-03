<?php 
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DanhMucHoSoHocSinh extends Model
{
    protected  $table="qlhs_profile";
    protected $primaryKey  = 'profile_id';
    protected $fillable = [
         'profile_code', 'profile_name', 'profile_birthday', 'profile_nationals_id', 'profile_site_id1', 'profile_site_id2', 'profile_site_id3', 'profile_household', 'profile_parentname', 'profile_guardian', 'profile_year', 'profile_school_id', 'profile_class_id', 'profile_status', 'profile_leaveschool_date', 'profile_create_userid', 'profile_update_userid', 'profile_bantru', 'profile_statusNQ57'
    ];
}

?>