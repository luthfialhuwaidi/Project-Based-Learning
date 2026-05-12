<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Confirmation extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_id', 'student_id', 'teacher_confirmed', 'teacher_confirmed_at',
        'teacher_id', 'parent_confirmed', 'parent_confirmed_at',
        'eaten_status', 'eaten_at', 'notes'
    ];

    protected $casts = [
        'teacher_confirmed' => 'boolean',
        'parent_confirmed' => 'boolean',
        'eaten_status' => 'boolean',
        'teacher_confirmed_at' => 'datetime',
        'parent_confirmed_at' => 'datetime',
        'eaten_at' => 'datetime',
    ];

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
