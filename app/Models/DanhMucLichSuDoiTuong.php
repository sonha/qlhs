<?php 
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
/**
* 
*/

class DanhMucLichSuDoiTuong extends Model
{
    protected  $table="qlhs_subject_history";
    protected $fillable = [
        'subject_history_id', 'subject_history_subject_id', 'subject_history_group_id', 'subject_history_rewrite', 'subject_history_md5'
    ];
}

?>