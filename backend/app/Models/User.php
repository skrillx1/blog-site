<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // protected function password(): Attribute
    // {
    //     return Attribute::make(
    //         set: fn ($value) => bcrypt($value),
    //     );
    // }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isEditor()
    {
        return $this->role === 'editor';
    }
}