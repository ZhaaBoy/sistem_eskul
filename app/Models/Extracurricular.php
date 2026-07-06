<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Extracurricular extends Model
{
    protected $fillable = [
        'coach_id',
        'name',
        'description',
        'quota',
        'status',
    ];

    public function coach()
    {
        return $this->belongsTo(Coach::class);
    }

    public function schedules()
    {
        return $this->hasMany(ExtracurricularSchedule::class);
    }

    public function registrations()
    {
        return $this->hasMany(ExtracurricularRegistration::class);
    }

    public function members()
    {
        return $this->hasMany(ExtracurricularMember::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'aktif');
    }
}
