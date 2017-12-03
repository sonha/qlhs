<?php 
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class LichSuHoSoHocSinh extends Model
{
    protected  $table="qlhs_profile_history";
    protected $fillable = [
        'history_id', 'history_profile_id', 'history_class_id', 'history_year'
    ];
}

?>