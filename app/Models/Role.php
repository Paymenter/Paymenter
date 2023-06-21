<?php

namespace App\Models;

use App\Utils\Permissions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    protected $fillable = [
        'permissions',
        'name',
    ];

    public function has($permission)
    {
        return (new Permissions($this->permissions))->has($permission);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
