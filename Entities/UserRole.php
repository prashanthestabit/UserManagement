<?php

namespace Modules\UserManagement\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserRole extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','role_id'
    ];

    public function Roles()
    {
        return $this->hasMany(Roles::class,'id','role_id');
    }

    public function userData()
    {
        return $this->hasOne(User::class,'id','user_id');
    }

    protected static function newFactory()
    {
        return \Modules\UserManagement\Database\factories\UserRoleFactory::new();
    }
}
