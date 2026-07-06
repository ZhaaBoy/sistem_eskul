<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    public const WAITING = 'menunggu_approval';
    public const APPROVED = 'disetujui';
    public const REJECTED = 'ditolak';

    protected $fillable = [
        'student_id',
        'extracurricular_id',
        'schedule_id',
        'attendance_date',
        'status',
        'submitted_at',
        'approved_at',
        'rejected_at',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
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

    public function schedule()
    {
        return $this->belongsTo(ExtracurricularSchedule::class, 'schedule_id');
    }
}
