<?php

namespace Modules\UserManagement\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Roles extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_name'
    ];

    protected static function newFactory()
    {
        return \Modules\UserManagement\Database\factories\RolesFactory::new();
    }
}
