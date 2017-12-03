<?php 
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
	protected  $table="permission_users";
    protected $fillable = [
        'permission_id', 'role_id', 'module_id','role_user_id'
    ];
}