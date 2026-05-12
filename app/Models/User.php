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
        'phone',
        'avatar',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Role checks
    public function isPetugas(): bool
    {
        return $this->role === 'petugas';
    }

    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }

    public function isOrangTua(): bool
    {
        return $this->role === 'orangtua';
    }

    // Relationships
    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'courier_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'parent_id');
    }

    public function school()
    {
        return $this->hasOne(School::class, 'teacher_id');
    }
}
