<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtracurricularRegistration extends Model
{
    public const WAITING_PARENT = 'menunggu_validasi_orang_tua';
    public const REJECTED_PARENT = 'ditolak_orang_tua';
    public const WAITING_COACH = 'menunggu_validasi_pembina';
    public const REJECTED_COACH = 'ditolak_pembina';
    public const ACCEPTED = 'diterima';

    protected $fillable = [
        'student_id',
        'extracurricular_id',
        'status',
        'parent_approved_at',
        'parent_rejected_at',
        'parent_rejection_reason',
        'coach_approved_at',
        'coach_rejected_at',
        'coach_rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'parent_approved_at' => 'datetime',
            'parent_rejected_at' => 'datetime',
            'coach_approved_at' => 'datetime',
            'coach_rejected_at' => 'datetime',
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

    public function member()
    {
        return $this->hasOne(ExtracurricularMember::class, 'registration_id');
    }
}
