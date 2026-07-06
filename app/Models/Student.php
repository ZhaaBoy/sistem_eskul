<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'parent_id',
        'nis',
        'nisn',
        'name',
        'class',
        'major',
        'gender',
        'birth_date',
        'phone',
        'email',
        'address',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(StudentParent::class, 'parent_id');
    }

    public function registrations()
    {
        return $this->hasMany(ExtracurricularRegistration::class);
    }

    public function members()
    {
        return $this->hasMany(ExtracurricularMember::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }
}
