<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtracurricularMember extends Model
{
    protected $fillable = [
        'student_id',
        'extracurricular_id',
        'registration_id',
        'joined_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'joined_at' => 'datetime',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function extracurricular()
    {
        return $this->belongsTo(Extracurricular::class);
    }

    public function registration()
    {
        return $this->belongsTo(ExtracurricularRegistration::class);
    }
}
