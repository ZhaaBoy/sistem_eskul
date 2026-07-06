<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ExtracurricularSchedule extends Model
{
    protected $fillable = [
        'extracurricular_id',
        'day',
        'start_time',
        'end_time',
        'location',
        'description',
        'status',
    ];

    public function extracurricular()
    {
        return $this->belongsTo(Extracurricular::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'schedule_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'aktif');
    }
}
